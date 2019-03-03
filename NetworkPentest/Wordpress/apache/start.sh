#!/bin/bash

service ssh restart
#curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
#chmod +x wp-cli.phar
#mv wp-cli.phar /usr/local/bin/wp
sleep 30s

wp core install --path=/var/www --url="172.30.0.3" --title="TAMUctf" --admin_name=admin --admin_password=txL6wG39IdNt1x --admin_email=you@example.com --allow-root
wp plugin activate revslider --path=/var/www --allow-root

source /etc/apache2/envvars
a2enmod autoindex
apache2 -D FOREGROUND
