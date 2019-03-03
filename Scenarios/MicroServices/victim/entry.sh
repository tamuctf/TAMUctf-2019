#! /bin/bash

sudo apt-get update

sudo apt-get -y install \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg-agent \
    software-properties-common \
    openssh-server \
    python-pip

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"

sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io
sudo curl -L "https://github.com/docker/compose/releases/download/1.23.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

sudo ssh-keygen -t rsa -N "" -f id_rsa
mkdir /root/.ssh
mv id_rsa /root/.ssh/
cat id_rsa.pub >> /root/.ssh/authorized_keys

sudo docker-compose  -f /home/ubuntu/docker-compose.yml build
sudo docker-compose  -f /home/ubuntu/docker-compose.yml up -d
