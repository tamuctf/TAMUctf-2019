import asyncio, telnetlib3
import sys
from Crypto.Cipher import AES
import hashlib
from base64 import b64encode, b64decode
import os
import time
import random
import logging

SERVER = 'bob'
PORT = 6023
SEED = 1234

random.seed(SEED)

def pad(s):
    return s + ('\x00'*(16-(len(s)%16)))


@asyncio.coroutine
def shell(reader, writer):

    cmds = open('monitor.sh').read().split('\n')
    for cmd in cmds:
        print(cmd)
        cmd += '\n'
        for c in cmd:
            print(hex(ord(c)))
            sys.stdout.flush()
            c = b64encode(cipher.encrypt(pad(c))).decode()
            writer.write(c)
            outp = yield from reader.read(24)
            print(outp)
            #print(len(b64decode(outp)))
            sys.stdout.flush()
            outp = cipher.decrypt(b64decode(outp)).decode().replace('\x00', '')
            #sys.stdout.write(outp)

        outp = yield from reader.read(4096)
        print(outp)
        sys.stdout.flush()
        try:
            print(len(b64decode(outp)))
            outp = cipher.decrypt(b64decode(outp)).decode().replace('\x00', '')
            print(outp)
        except:
            print("Failure in shell")
            print(outp)
            sys.stdout.flush()
            writer.close()
            return

    writer.close()
    print()

while True:
    try:
        m = hashlib.sha256()
        m.update(str(random.random()).encode())
        key = m.digest()[:16]
        print(key)
        key = b'Sixteen byte key'
        cipher = AES.new(key, AES.MODE_ECB)

        loop = asyncio.get_event_loop()
        coro = telnetlib3.open_connection(SERVER, PORT, shell=shell)
        r, w = loop.run_until_complete(coro)
        loop.run_until_complete(w.protocol.waiter_closed)
    except Exception as err:
        logging.exception("Failure in main loop")

    time.sleep(30)
