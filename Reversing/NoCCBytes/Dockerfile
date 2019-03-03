FROM ubuntu:16.04

RUN apt update
RUN apt install -y socat

RUN groupadd ctf

RUN mkdir /pwn

COPY entry.sh /entry.sh
COPY a.out /pwn/a.out
COPY flag.txt /pwn/flag.txt

RUN useradd -G ctf --home=/pwn pwnuser
RUN useradd -G ctf --home=/pwn pwnflag

RUN chown pwnflag:pwnflag /pwn/flag.txt
RUN chown pwnflag:pwnflag /pwn/a.out

RUN chmod 4755 /pwn/a.out
RUN chmod 444 /pwn/flag.txt

EXPOSE 8188
CMD ["./entry.sh"]
