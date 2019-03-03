from .telnetbot import TelnetBot
from os import environ
import logging
import random
import time

HOST = environ.get('SHELL_HOST', 'shell')
USER = environ.get('SHELL_USER', 'noob')
PASSWD = environ.get('SHELL_PASS', 'noob')
NAUMOTP_SECRET = environ.get('NAUMOTP_SECRET', None)
HUMANISH = environ.get("HUMANISH", "true").lower() == "true"

DELAY_MEAN = 3
DELAY_VAR = 0.6
DELAY_INITIAL = 10

logger = logging.getLogger("client")
logging.basicConfig(level=environ.get('LOG_LEVEL', 'info').upper())

def delay(mu=DELAY_MEAN, sigma=DELAY_VAR):
    length = abs(random.gauss(mu, sigma))
    time.sleep(length)

def session():
    with TelnetBot(HOST, USER, PASSWD, naumotp_secret=NAUMOTP_SECRET, humanish=HUMANISH) as shell:
        shell.login()
        logger.info(f"Logged in to {HOST} as {USER} with {PASSWD}")
        delay()

        date = shell.date()
        logger.info(f"Got date as {date}")
        delay()

def main():
    logger.info("Telnet shell host is '{0}'".format(HOST))

    while True:
        try:
            if DELAY_INITIAL > 0:
                logger.info(f"Waiting {DELAY_INITIAL} seconds to start")
                time.sleep(DELAY_INITIAL)
            session()
        except ConnectionErrror:
            logger.exception("Connection error in interaction loop")

if __name__ == "__main__":
    main()
