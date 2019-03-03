#Pulling apache/php image.
#FROM php:7.0-apache
FROM ubuntu:latest

# Making it so that the CLI will not be interactive during the building of the Docker container.
ARG DEBIAN_FRONTEND="noninteractive"

#Setup command.
#RUN apt-get -y update && apt-get install -y mysql-server
RUN apt-get -y update && apt-get install -y apache2 apache2-doc apache2-utils mysql-server php libapache2-mod-php php-mysql

#Copying over the needed web files.
COPY /Website/admin_check.php /var/www/html
COPY /Website/apply_edit.php /var/www/html
COPY /Website/auth.php /var/www/html
COPY /Website/cookie_check.php /var/www/html
COPY /Website/edit.php /var/www/html
COPY /Website/index.php /var/www/html
COPY /Website/login.php /var/www/html
COPY /Website/logout.php /var/www/html
COPY /Website/logo.png /var/www/html
COPY /Website/message.php /var/www/html
COPY /Website/register_user.php /var/www/html
COPY /Website/send.php /var/www/html
COPY /Website/sql_connect.php /var/www/html
COPY /Website/style.css /var/www/html
COPY /Website/.htaccess /var/www/html

#Adding database setup script.
COPY Setup.sh /Setup.sh

#Open up port 80 for connections.
EXPOSE 80

#Running Setup Commands
RUN service apache2 start
RUN echo "<Directory /var/www/html>" >> /etc/apache2/sites-available/000-default.conf
RUN echo "Options Indexes FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf
RUN echo "AllowOverride All" >> /etc/apache2/sites-available/000-default.conf
RUN echo "Require all granted" >> /etc/apache2/sites-available/000-default.conf
RUN echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN service apache2 restart

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2

#Giving the setup script executable permissions.
RUN chmod 700 /Setup.sh
RUN rm /var/www/html/index.html

#Upon start of the container it will launch the setup script.
ENTRYPOINT ["sh","/Setup.sh"]
