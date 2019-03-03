all: pwn

pwn: simple_ret2libc.c
	gcc -fno-stack-protector -no-pie simple_ret2libc.c -m32  -o pwn4
