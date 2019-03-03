#!/bin/bash
/usr/bin/mysqld_safe &
sleep 10s
# allow remote logins
mysql -u root -e "GRANT ALL ON *.* to root@'%' IDENTIFIED BY 'r1WX4PPpMm4770';"
mysql -u root -e "CREATE DATABASE wordpress;"
mysql -u root -e "CREATE USER wordpress@localhost;"
mysql -u root -e "SET PASSWORD FOR wordpress@localhost= PASSWORD('0NYa6PBH52y86C');"
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO wordpress@'%' IDENTIFIED BY '0NYa6PBH52y86C';"
mysql -u root -e "FLUSH PRIVILEGES;"

mysqladmin -u root shutdown 
sleep 5s
/usr/bin/mysqld_safe
sleep 10s
(crontab -l 2>/dev/null; echo "* * * * * /backup/backup.sh") | crontab -
cron
