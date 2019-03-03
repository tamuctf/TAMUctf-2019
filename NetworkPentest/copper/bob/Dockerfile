FROM ubuntu:18.04

RUN apt update && apt install -y python3-pip 
RUN pip3 install telnetlib3

RUN mkdir logs
RUN mkdir chal

COPY ./server.py /chal/
COPY ./flag.txt /chal/
COPY ./entry.sh .

ENTRYPOINT ["./entry.sh"]
