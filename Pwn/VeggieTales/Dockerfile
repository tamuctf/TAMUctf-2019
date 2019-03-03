FROM python:3.6.6-stretch

RUN apt-get update && apt-get install -y \
    apt-utils \
    build-essential \
    socat

RUN groupadd ctf

RUN mkdir /pwn

COPY flag.txt /pwn/flag.txt
COPY server.py /pwn/server.py

RUN useradd -G ctf --home=/pwn pwnuser
RUN useradd -G ctf --home=/pwn pwnflag

RUN chown pwnflag:pwnflag /pwn/flag.txt
RUN chown pwnflag:pwnflag /pwn/server.py
RUN chown pwnflag:pwnflag /pwn

RUN chmod 4755 /pwn/server.py
RUN chmod 444 /pwn/flag.txt

EXPOSE 8448
ENTRYPOINT ["su","-c","exec socat TCP-LISTEN:8448,reuseaddr,fork EXEC:/pwn/server.py,stderr","-","pwnuser"]
