FROM ubuntu:latest

COPY db.js .
COPY server.js .
COPY index.html .

RUN mkdir -p /data/db
RUN apt update && apt install -y nodejs npm python-pip
RUN pip install pika
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 9DA31620334BD75D9DCB49F368818C72E52529D4
RUN echo "deb [ arch=amd64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.0 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-4.0.list
RUN apt update
RUN export DEBIAN_FRONTEND=noninteractive && apt install -y mongodb-org
RUN ln -fs /usr/share/zoneinfo/America/Chicago /etc/localtime
RUN dpkg-reconfigure --frontend noninteractive tzdata
RUN npm install express mongodb

