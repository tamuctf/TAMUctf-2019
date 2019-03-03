all: pwn

pwn: simple_rop.c
	gcc -fno-stack-protector -no-pie simple_rop.c -m32 -static -o pwn5
