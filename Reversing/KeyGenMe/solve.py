from pwn import *

p = remote('172.17.0.2', 7223)

payload = "jfZxShcfa7hcX9cn"

p.send(payload + '\n')
p.interactive()

