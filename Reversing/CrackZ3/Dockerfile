FROM ubuntu:16.04

RUN apt update --fix-missing
RUN apt install -y socat

RUN groupadd ctf

RUN mkdir /rev

COPY prodkey /rev/prodkey
COPY flag.txt /rev/flag.txt

RUN useradd -G ctf --home=/rev revuser
RUN useradd -G ctf --home=/rev revflag

RUN chown revflag:revflag /rev/flag.txt
RUN chown revflag:revflag /rev/prodkey

RUN chmod 4755 /rev/prodkey
RUN chmod 444 /rev/flag.txt

EXPOSE 8189

CMD ["su", "-c", "exec socat TCP-LISTEN:8189,reuseaddr,fork EXEC:/rev/prodkey,stderr", "-", "revuser"]
