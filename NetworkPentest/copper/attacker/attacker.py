import asyncio, telnetlib3
import sys
import time

SERVER = "172.25.0.2"
PORT = 6023

enc_cmds = open('cmds.txt').read().split('==')
cmds = open('../alice/monitor.sh').read()
cmds = list(cmds)
#print(cmds)
cmd_mapping = {}
for ec, c in zip(enc_cmds, cmds):
    if c not in cmd_mapping:
        cmd_mapping[c] = ec + '=='
cmds = [
cmd_mapping['c'],
cmd_mapping['p'],
cmd_mapping[' '],
cmd_mapping['.'],
cmd_mapping['/'],
cmd_mapping['f'],
cmd_mapping['l'],
cmd_mapping['a'],
cmd_mapping['g'],
cmd_mapping['.'],
cmd_mapping['t'],
cmd_mapping['x'],
cmd_mapping['t'],
cmd_mapping[' '],
cmd_mapping['/'],
cmd_mapping['l'],
cmd_mapping['o'],
cmd_mapping['g'],
cmd_mapping['s'],
cmd_mapping['\n']
]
print(cmds)
@asyncio.coroutine
def shell(reader, writer):

    for cmd in cmds:
        print(cmd)
        writer.write(cmd)
        outp = yield from reader.read(24)
        sys.stdout.write(outp)

    outp = yield from reader.read(1024)
    print(outp)
    print('done')
    writer.close()
    print()

while True:
    #key = b'Sixteen byte key'
    loop = asyncio.get_event_loop()
    coro = telnetlib3.open_connection(SERVER, PORT, shell=shell)
    r, w = loop.run_until_complete(coro)
    loop.run_until_complete(w.protocol.waiter_closed)

    time.sleep(1)



