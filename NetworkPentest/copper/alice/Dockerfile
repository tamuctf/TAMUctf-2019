FROM ubuntu:18.04

RUN apt update && apt install -y python3-pip 
RUN pip3 install telnetlib3

COPY ./client.py .
COPY ./monitor.sh .

ENTRYPOINT ["python3", "./client.py"]
