#!/bin/bash

while :
do
    su -c "socat TCP4-listen:4323,reuseaddr,fork EXEC:/pwn/pwn3" - pwnuser
done
