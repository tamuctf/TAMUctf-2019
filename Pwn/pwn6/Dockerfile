FROM ubuntu:16.04

RUN apt update
RUN apt install -y socat gdb netcat

RUN groupadd ctf

RUN mkdir /pwn

COPY server /pwn/pwn4
COPY flag.txt /pwn/flag.txt
COPY Banking.db /pwn/Banking.db

RUN useradd -G ctf --home=/pwn pwnuser
RUN useradd -G ctf --home=/pwn pwnflag

RUN chown pwnflag:pwnflag /pwn/flag.txt
RUN chown pwnflag:pwnflag /pwn/pwn4
RUN chown pwnflag:pwnflag /pwn

RUN chmod 4755 /pwn/pwn4
RUN chmod 444 /pwn/flag.txt
RUN chmod 444 /pwn/Banking.db

EXPOSE 6210
CMD ["su", "-c", "./pwn4", "-", "pwnuser"]
