#!/bin/bash
while :
do
    su -c "socat TCP4-listen:4324,reuseaddr,fork EXEC:/pwn/pwn4" - pwnuser
done
