FROM ubuntu:16.04

RUN apt update --fix-missing
RUN apt install -y socat

RUN groupadd ctf

RUN mkdir /rev

COPY gadsby /rev/gadsby
COPY flag.txt /rev/flag.txt

RUN useradd -G ctf --home=/rev revuser
RUN useradd -G ctf --home=/rev revflag

RUN chown revflag:revflag /rev/flag.txt
RUN chown revflag:revflag /rev/gadsby

RUN chmod 4755 /rev/gadsby
RUN chmod 444 /rev/flag.txt

EXPOSE 3334

CMD ["su", "-c", "exec socat TCP-LISTEN:7224,reuseaddr,fork EXEC:/rev/gadsby,stderr", "-", "revuser"]
