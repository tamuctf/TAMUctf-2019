#!/bin/sh
gcc -m32 -w -fno-stack-protector -z execstack -o vuln vuln.c
socat TCP-LISTEN:3456,reuseaddr,fork EXEC:./vuln &
python tests/queue.py

