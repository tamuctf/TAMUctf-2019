#!/bin/bash

export DEBIAN_FRONTEND="noninteractive"

sudo apt-get -y update && apt-get install -y apache2 apache2-doc apache2-utils mysql-server php libapache2-mod-php php-mcrypt php-mysql python

sudo sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sudo sed -i 's/StrictModes yes/#StrictModes yes/g' /etc/ssh/sshd_config
sudo service ssh restart
sudo echo "root:0A0YlBjrlBXSr14MPz" | chpasswd

export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_PID_FILE=/var/run/apache2.pid
export APACHE_RUN_DIR=/var/run/apache2
export APACHE_LOCK_DIR=/var/lock/apache2
export APACHE_LOG_DIR=/var/log/apache2

sudo service apache2 restart;

