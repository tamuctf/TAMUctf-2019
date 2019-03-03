all: pwn

pwn: simple_shell.c
	gcc -fno-stack-protector simple_shell.c -m32 -z execstack -o pwn3
