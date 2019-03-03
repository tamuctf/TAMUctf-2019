#!/bin/bash

while : 
do
    su -c "exec socat TCP-LISTEN:4321,reuseaddr,fork EXEC:/pwn/pwn1,stderr" - pwnuser;
done
