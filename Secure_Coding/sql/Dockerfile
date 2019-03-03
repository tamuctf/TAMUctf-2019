#pull latest ubuntu image
FROM ubuntu:latest

#sets cmdline interface as noninteractive for installing packages below
env DEBIAN_FRONTEND="noninteractive"

#set up php/mysql/apache and dependencies
RUN apt-get -y update && apt-get install -y apache2 apache2-doc apache2-utils mysql-server php libapache2-mod-php php-mysql python-pip

RUN pip install pika

#set up files
COPY index.html /var/www/html/
COPY login.php /var/www/html/web/
COPY logo.png /var/www/html/images/

#set up environment for apache serv
env APACHE_RUN_USER    www-data
env APACHE_RUN_GROUP   www-data
env APACHE_PID_FILE    /var/run/apache2.pid
env APACHE_RUN_DIR     /var/run/apache2
env APACHE_LOCK_DIR    /var/lock/apache2
env APACHE_LOG_DIR     /var/log/apache2

#allows running of file via changing permissions


#gives access to port 80
EXPOSE 80

#runs on boot of container
