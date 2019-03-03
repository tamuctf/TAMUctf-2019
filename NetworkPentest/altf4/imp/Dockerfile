FROM python:stretch

RUN apt-get update && \
    apt-get install -y nmap libpcap0.8 bash netcat

COPY ./requirements.txt ./requirements.txt
RUN pip install -r ./requirements.txt && rm ./requirements.txt

COPY ./imp.py /usr/local/bin/adminutil
RUN chmod +x /usr/local/bin/adminutil

ARG OWNER
RUN useradd -ms /bin/bash $OWNER

CMD ["python", "/usr/local/bin/adminutil"]
