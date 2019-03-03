from pwn import *

p = remote('172.17.0.2', 3334)

payload =  "p3Asujmn9CEeCB3A"

p.send(payload + '\n')
p.interactive()

