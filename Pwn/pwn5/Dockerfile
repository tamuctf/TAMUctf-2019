FROM 32bit/ubuntu:16.04

RUN apt update
RUN apt install -y socat

RUN groupadd ctf

RUN mkdir /pwn

COPY entry.sh /entry.sh
COPY pwn5 /pwn/pwn5
COPY flag.txt /pwn/flag.txt

RUN useradd -G ctf --home=/pwn pwnuser
RUN useradd -G ctf --home=/pwn pwnflag

RUN chown pwnflag:pwnflag /pwn/flag.txt
RUN chown pwnflag:pwnflag /pwn/pwn5

RUN chmod 4755 /pwn/pwn5
RUN chmod 444 /pwn/flag.txt

EXPOSE 4325
CMD ["./entry.sh"]
