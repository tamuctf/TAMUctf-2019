FROM ubuntu:xenial

RUN apt-get -y update \
    && apt-get -y install xinetd telnetd bc

ARG USERNAME
ARG PASSWORD
ARG CTF_FLAG

RUN useradd -Um $USERNAME && \
    printf "${PASSWORD}\n${PASSWORD}" | passwd $USERNAME

RUN cd /home/$USERNAME && \
    echo "$CTF_FLAG" > .ctf_flag

COPY ./etc/* /etc/

EXPOSE 23
CMD ["xinetd", "-dontfork", "-inetd_compat"]
