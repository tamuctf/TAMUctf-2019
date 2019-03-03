FROM ubuntu:latest

RUN apt update && apt install -y \
	apt-utils \
	gcc \
	gcc-multilib \
	build-essential \
	python \
	python-pip \
	socat
COPY requirements.txt .
RUN pip install -r requirements.txt
EXPOSE 3456
