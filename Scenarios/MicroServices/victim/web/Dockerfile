FROM ubuntu:18.04

RUN apt update && DEBIAN_FRONTEND=noninteractive apt install curl mysql-client apache2 apache2-utils php libapache2-mod-php php-mysql -y

COPY *.html /var/www/html/

COPY ./entry.sh /entry.sh

env APACHE_RUN_USER    www-data
env APACHE_RUN_GROUP   www-data
env APACHE_PID_FILE    /var/run/apache2.pid
env APACHE_RUN_DIR     /var/run/apache2
env APACHE_LOCK_DIR    /var/lock/apache2
env APACHE_LOG_DIR     /var/log/apache2

#EXPOSE 80

ENTRYPOINT ["./entry.sh"]
