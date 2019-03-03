#!/bin/bash

while :
do
    su -c "socat TCP4-listen:8188,reuseaddr,fork EXEC:/pwn/a.out" - pwnuser
done
