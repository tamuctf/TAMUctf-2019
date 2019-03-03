FROM ubuntu:trusty

MAINTAINER WPScan Team <team@wpscan.org>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update
RUN apt-get -qy dist-upgrade

RUN { \
        echo mysql-community-server mysql-community-server/data-dir select ''; \
        echo mysql-community-server mysql-community-server/root-pass password ''; \
        echo mysql-community-server mysql-community-server/re-root-pass password ''; \
        echo mysql-community-server mysql-community-server/remove-test-db select false; \
    } | debconf-set-selections
RUN apt-get -qy install wget ed sed curl apache2 mysql-server php5-mysql php5 libapache2-mod-php5 php5-mcrypt php5-gd unzip openssh-server

# setup mysql
RUN sed -Ei 's/^(bind-address|log)/#&/' /etc/mysql/my.cnf

# extract wordpress
ADD https://wordpress.org/latest.tar.gz /wordpress.tar.gz
#COPY latest.tar.gz /wordpress.tar.gz
RUN rm -rf /var/www/
RUN tar xvzf /wordpress.tar.gz
RUN mv /wordpress /var/www/
ADD ./revslider /var/www/wp-content/plugins/revslider
# configure wordpress
RUN mv /var/www/wp-config-sample.php /var/www/wp-config.php
RUN sed -i -r "s/define\( 'DB_NAME', '[^']+' \);/define\('DB_NAME', 'wordpress'\);/g" /var/www/wp-config.php
RUN sed -i -r "s/define\( 'DB_USER', '[^']+' \);/define\('DB_USER', 'wordpress'\);/g" /var/www/wp-config.php
RUN sed -i -r "s/define\( 'DB_PASSWORD', '[^']+' \);/define\('DB_PASSWORD', '0NYa6PBH52y86C'\);/g" /var/www/wp-config.php
RUN sed -i -r "s/define\( 'DB_HOST', '[^']+' \);/define\('DB_HOST', '172.30.0.2'\);/g" /var/www/wp-config.php
RUN printf '%s\n' "g/put your unique phrase here/d" a "$(curl -sL https://api.wordpress.org/secret-key/1.1/salt/)" . w | ed -s /var/www/wp-config.php
ADD ./vhost.conf /etc/apache2/sites-available/000-default.conf
ADD note.txt /var/www
RUN chown -R www-data:www-data /var/www
ADD flag.txt /root/flag.txt
RUN chmod 600 /root/flag.txt
ADD id_rsa.pub /root/.ssh/authorized_keys
RUN sed -i "s/#AuthorizedKeysFile/AuthorizedKeysFile/g" /etc/ssh/sshd_config
ADD wp-cli.phar /usr/local/bin/wp

EXPOSE 80
EXPOSE 22

ADD start.sh /start.sh
RUN chmod 700 /start.sh
CMD ["/bin/bash", "/start.sh"]
