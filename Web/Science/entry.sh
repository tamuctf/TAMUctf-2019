#!/bin/sh
# Start CTFd
touch /etc/authbind/byport/80
chmod 777 /etc/authbind/byport/80
while true; do
    su -c "authbind --deep /usr/bin/python serve.py" - webuser
done
