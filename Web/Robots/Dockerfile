# Pulling latest Ubuntu image.
FROM php:7.0-apache

# Copying over the needed web files.
COPY /Website/index.html /var/www/html
COPY /Website/robots.php /var/www/html
COPY /Website/Background.jpg /var/www/html
COPY /Website/Down_With_Robots.gif /var/www/html
COPY /Website/Not_A_Robot.png /var/www/html
COPY /Website/.htaccess /var/www/html

# Opening up port 80 for connections.
EXPOSE 80

# Running setup commands.
RUN service apache2 start
RUN echo "<Directory /var/www/html>" >> /etc/apache2/sites-available/000-default.conf
RUN echo "Options Indexes FollowSymLinks" >> /etc/apache2/sites-available/000-default.conf
RUN echo "AllowOverride All" >> /etc/apache2/sites-available/000-default.conf
RUN echo "Require all granted" >> /etc/apache2/sites-available/000-default.conf
RUN echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN service apache2 restart


