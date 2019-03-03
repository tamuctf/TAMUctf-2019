# KeyGenMe

## Challenge

`nc pwn.tamuctf.com 7223`

## Setup
Build the container with `docker build -t advkeygen .` and run it with `docker run -it --rm advkeygen`

## Solution
Send the string `jfZxShcfa7hcX9cn` to stdin, this string is encrypted using multiplication addition and modulus and compared to static string. This would be computed one byte at a time in reverse order.    
```python
from pwn import *

p = remote('172.17.0.2', 7223)

payload = "jfZxShcfa7hcX9cn"

p.send(payload + '\n')
p.interactive()
```
