FROM ubuntu:16.04

RUN apt-get update && apt-get install -y curl python-pip
RUN pip install requests

COPY bot.py /bot.py
COPY entry.sh /entry.sh

RUN chmod 700 /entry.sh

ENTRYPOINT ["/bin/bash", "/entry.sh"]
