FROM 32bit/ubuntu:16.04

RUN apt update
RUN apt install -y socat

RUN groupadd ctf

RUN mkdir /pwn

COPY entry.sh /entry.sh
COPY pwn2 /pwn/pwn2
COPY flag.txt /pwn/flag.txt

RUN useradd -G ctf --home=/pwn pwnuser
RUN useradd -G ctf --home=/pwn pwnflag

RUN chown pwnflag:pwnflag /pwn/flag.txt
RUN chown pwnflag:pwnflag /pwn/pwn2

RUN chmod 4755 /pwn/pwn2
RUN chmod 444 /pwn/flag.txt

EXPOSE 4322
CMD ["./entry.sh"]
