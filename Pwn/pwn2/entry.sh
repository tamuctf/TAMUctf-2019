#!/bin/bash

while :
do
    su -c "socat TCP4-listen:4322,reuseaddr,fork EXEC:/pwn/pwn2" - pwnuser
done
