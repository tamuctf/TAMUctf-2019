from binascii import hexlify, unhexlify
from datetime import datetime
from hashlib import sha256
from os import path, environ, getenv
from telnetlib import Telnet
import hmac
import itertools
import logging
import random
import re
import string
import sys
import time

logger = logging.getLogger(__name__)
logging.basicConfig(level=environ.get('LOG_LEVEL', 'info').upper())

def timepoints(mu, sigma):
    acc = time.time()
    while True:
        yield acc
        acc += random.gauss(mu, sigma)

def humantype(text, mu=0.3, sigma=0.05):
    for (ch, schedule) in zip(text, timepoints(mu, sigma)):
        yield ch
        time.sleep(max(schedule - time.time(), 0))
        
class TelnetTimeoutError(ConnectionError):
    def __init__(self, msg="telnet connection took too long to respond"):
        self.msg = msg

    def __str__(self):
        return "TelnetTimoutException: {msg}".format(msg=self.msg)

class TelnetBot:
    """
    An interface for issuing and interpretting commands over telnet
    """
    port = 23
    retry_period = 5
    retry_count = 5

    def __init__(self, host, user=None, passwd=None, timeout=10, naumotp_secret=None, humanish=True):
        self.connected = False
        self.loggedin = False
        self.host = host
        self.user = user
        self.passwd = passwd
        self.timeout = timeout
        self.naumotp_secret = naumotp_secret
        self.humanish = humanish

        if isinstance(self.naumotp_secret, str):
            self.naumotp_secret = self.naumotp_secret.encode('utf-8')

        self.connect()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.write("exit\n")
        self.conn.close()

    def connect(self):
        for i in range(self.retry_count + 1):
            start_time = time.time()
            try:
                self.conn = Telnet(self.host, self.port, self.timeout)
            except Exception as e: # Catch all errors: not very precise, but it shouldn't be an issue
                logger.info("Connect to shell server failed due to '{0}'".format(e))
                elapsed = time.time() - start_time
                remaining = self.retry_period - elapsed
                logger.debug("Retrying in {0:.2f}s".format(remaining))
                if remaining > 0: time.sleep(remaining)
                if i == self.retry_count: raise
            else:
                self.connected = True


    def login(self):
        self.read_until("login: ")
        self.write("{0}\n".format(self.user))

        self.read_until("Password: ")
        self.write("{0}\n".format(self.passwd))

        index, match, _ = self.expect([r'\$ ', r'challenge \[(?P<chal>[0-9a-f]+)\]'])
        if index == 0:
            self.loggedin = True

        elif index == 1:
            if self.naumotp_secret is None:
                raise ValueError("Encountered naumotp challenge without a secret to compute the HMAC")

            resp = hexlify(hmac.new(self.naumotp_secret, unhexlify(match["chal"]), digestmod=sha256).digest())

            self.read_until("response: ")
            self.write("{0}\n".format(resp.decode('utf-8')), humanish=False)

            index, _, _ = self.expect([r'\$ '])
            if index == 0:
                self.loggedin = True

    def expect(self, patterns):
        for i, pattern in enumerate(patterns):
            if isinstance(pattern, str):
                patterns[i] = pattern.encode('utf-8')

        index, match, text = self.conn.expect(patterns, self.timeout)

        try:
            logger.debug(text.decode('utf-8'))
        except UnicodeDecodeError:
            pass

        if index == -1:
            raise TelnetTimeoutError()
        else:
            return index, match, text.decode('utf-8')

    def read_until(self, expected):
        if isinstance(expected, str):
            expected = expected.encode('utf-8')

        text = self.conn.read_until(expected, self.timeout)

        try:
            result = text.decode('utf-8')
            logger.debug(result)
        except UnicodeDecodeError:
            result = None

        if not text.endswith(expected):
            raise TelnetTimeoutError()
        else:
            return result

    def write(self, text, humanish=None):
        if isinstance(text, str):
            text = text.encode('utf-8')

        try:
            logger.debug(text.decode('utf-8'))
        except UnicodeDecodeError:
           pass

        if humanish is None:
            humanish = self.humanish

        if humanish:
            for ch in humantype(text):
                self.conn.write(bytes((ch,)))
        else:
            self.conn.write(text)

    def bc(self, expr):
        self.write(f"bc <<< '{expr}'\n")
        return int(self.read_until("$ ").splitlines()[-2])

    def date(self):
        self.write(f"date\n")
        return self.read_until("$ ").splitlines()[-2]
