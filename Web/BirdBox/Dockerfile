# Pulling latest Ubuntu image.
FROM ubuntu:latest

# Making it so that the CLI will not be interactive during the building of the Docker container.
ARG DEBIAN_FRONTEND="noninteractive"

# Installing apach2, mysql, php, and everything they need to interact.
RUN apt-get -y update && apt-get install -y apache2 apache2-doc apache2-utils mysql-server php libapache2-mod-php php-mysql

# Copying over the needed web files.
COPY /Website/index.html /var/www/html
COPY /Website/Search.php /var/www/html
COPY /Website/Best_Aggie.png /var/www/html
COPY /Website/Best_Truck.png /var/www/html
COPY /Website/Ehhh.png /var/www/html
COPY /Website/Happy_Eggs.png /var/www/html
COPY /Website/Nice_Going!.gif /var/www/html
COPY /Website/Nope.gif /var/www/html
COPY /Website/TAMU_CTF.png /var/www/html

#Adding the Database Generation Script.
COPY DB-Gen.sh /

# Setting the environment variables for apache2.
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2

# Opening up port 80 for connections.
EXPOSE 80

# Giving the generation script executable permissions.
RUN chmod 700 /DB-Gen.sh

# Upon start of the container it will launch the generation script.
ENTRYPOINT ["./DB-Gen.sh"]
