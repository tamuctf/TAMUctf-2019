#!/bin/sh

cp login.php /var/www/html/login.php
cp index.html /var/www/html/index.html

./db_gen.sh
service apache2 stop;
service apache2 start;
python tests/queue.py

