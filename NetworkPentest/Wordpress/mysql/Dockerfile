FROM ubuntu:trusty
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update
RUN apt-get -qy dist-upgrade

RUN { \
        echo mysql-community-server mysql-community-server/data-dir select ''; \
        echo mysql-community-server mysql-community-server/root-pass password ''; \
        echo mysql-community-server mysql-community-server/re-root-pass password ''; \
        echo mysql-community-server mysql-community-server/remove-test-db select false; \
    } | debconf-set-selections
RUN apt-get -qy install mysql-server
# setup mysql
#RUN sed -Ei 's/^(bind-address|log)/#&/' /etc/mysql/my.cnf
RUN sed -i "s/127.0.0.1/0.0.0.0/g" /etc/mysql/my.cnf
RUN sed -i "s/\[mysqld\]/\[mysqld\]\nsecure-file-priv = \"\"/g" /etc/mysql/my.cnf
RUN sed -i "s/\[mysqld\]/\[mysqld\]\ninnodb_use_native_aio=0/g" /etc/mysql/my.cnf
ADD ./start.sh .
RUN chmod 700 ./start.sh
RUN mkdir /backup
ADD id_rsa /backup/id_rsa
RUN chmod 777 /backup
RUN chmod 777 /backup/id_rsa
EXPOSE 3306
ENTRYPOINT ["./start.sh"]
