#!/bin/bash

export DEBIAN_FRONTEND="noninteractive"

sudo apt-get -y update && apt-get install -y apache2 apache2-doc apache2-utils mysql-server php libapache2-mod-php php-mcrypt php-mysql python

sudo sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sudo sed -i 's/StrictModes yes/#StrictModes yes/g' /etc/ssh/sshd_config
sudo sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/g' /etc/ssh/sshd_config
sudo service ssh restart
sudo mkdir /home/devtest
sudo useradd --home=/home/devtest -s /bin/bash devtest
sudo echo "devtest:driveby" | chpasswd
sudo chown devtest:devtest /home/devtest

sudo echo "root:0A0YlBjrlBXSr14MPz" | chpasswd
sudo mv /home/vagrant/setup.sh /home/ubuntu/setup.sh

sudo cp -r /home/vagrant/html/* /var/www/html

export APACHE_RUN_USER=www-data
export APACHE_RUN_GROUP=www-data
export APACHE_PID_FILE=/var/run/apache2.pid
export APACHE_RUN_DIR=/var/run/apache2
export APACHE_LOCK_DIR=/var/lock/apache2
export APACHE_LOG_DIR=/var/log/apache2

sudo service mysql start && mysql -uroot -e "CREATE DATABASE SqliDB; CREATE USER 'sqli-server'@'localhost' IDENTIFIED BY 'Bx117@\$YaML**\!'; \
GRANT ALL PRIVILEGES ON SqliDB.* TO 'sqli-server'@'localhost'; \
USE SqliDB; CREATE TABLE Users (ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, User varchar(20), Password varchar(100)); \
INSERT INTO Users (User,Password) VALUES ('admin','7a4434d48772fee914a99590376ee438'); \
INSERT INTO Users (User,Password) VALUES ('devtest','2e107f8e7aaf178bf00e58c09abfba08'); \
INSERT INTO Users (User,Password) VALUES ('suzy','5f836ac3e2ea2b22227c940754283fde'); \
INSERT INTO Users (User,Password) VALUES ('bob','442f0577be5c6e59a77047eaa37b15c6'); \
INSERT INTO Users (User,Password) VALUES ('alice','5efb309c9b1dc4e90fa136a64e3902e0'); \
SET PASSWORD FOR root@'localhost' = PASSWORD('Tl6@$0lxyaA@#--Jl3NMA@1-9283D')";

sudo service apache2 restart;

