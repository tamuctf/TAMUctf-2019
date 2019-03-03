#!/bin/bash
while :
do
    su -c "socat TCP4-listen:4325,reuseaddr,fork EXEC:/pwn/pwn5" - pwnuser
done
