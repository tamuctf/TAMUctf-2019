FROM 32bit/ubuntu:16.04

RUN apt update
RUN apt install -y socat gdb

RUN groupadd ctf

RUN mkdir /pwn

COPY pwn1 /pwn/pwn1
COPY flag.txt /pwn/flag.txt
COPY entry.sh /pwn/entry.sh

RUN useradd -G ctf --home=/pwn pwnuser
RUN useradd -G ctf --home=/pwn pwnflag

RUN chown pwnflag:pwnflag /pwn/flag.txt
RUN chown pwnflag:pwnflag /pwn/pwn1
RUN chown pwnflag:pwnflag /pwn

RUN chmod 4755 /pwn/pwn1
RUN chmod 444 /pwn/flag.txt
RUN chmod 4755 /pwn/entry.sh

EXPOSE 4321
ENTRYPOINT ["/pwn/entry.sh"]
