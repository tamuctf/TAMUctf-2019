from .telnetbot import TelnetBot
from os import environ
import itertools
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
ACTION_MAX = 5

logger = logging.getLogger("client")
logging.basicConfig(level=environ.get('LOG_LEVEL', 'info').upper())

def randnumber(decay):
    number = 0
    for i in itertools.count():
        if random.random() > decay ** i:
            break
        number += random.randrange(10) * (10**i)
    return number
    
def randterms(alpha, beta):
    for i in itertools.count():
        if random.random() > alpha ** i:
            return
        yield randnumber(beta)

def randexpr(alpha, beta, seed=None):
    """Generate simple random expression of plus, minus, and multiply
    
    Args:
        alpha (float): Decay for the number of terms
        beta (float): Decay for the number of digits in each term
        seed (int): Seed term to start the expression
    """
    terms = tuple(randterms(alpha, beta))
    if seed is not None:
        terms = (seed,) + terms
    
    for term in terms[:-1]:
        yield str(term)
        yield random.choice('-+*')
    yield str(terms[-1])

def randexprstr(alpha=0.8, beta=0.8, seed=None):
    return ' '.join(randexpr(alpha, beta, seed=seed))

def delay(mu=DELAY_MEAN, sigma=DELAY_VAR):
    length = abs(random.gauss(mu, sigma))
    time.sleep(length)

def session():
    with TelnetBot(HOST, USER, PASSWD, naumotp_secret=NAUMOTP_SECRET, humanish=HUMANISH) as shell:
        shell.login()
        logger.info(f"Logged in to {HOST} as {USER} with {PASSWD}")
        delay()

        ret = None
        for _ in range(random.randrange(1, ACTION_MAX)):
            expr = randexprstr(seed=ret)
            ret = shell.bc(expr)
            logger.info(f"Calculated {expr} = {ret}")
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
