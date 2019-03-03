FROM python:alpine

ENV HOME /

COPY flag_bot.py /
COPY requirements.txt /
COPY credentials.json /

RUN python -m pip install --upgrade pip
RUN python -m pip install --upgrade -r requirements.txt

RUN mkdir /root/.credentials
COPY credentials.json /root/.credentials/
COPY gmail-python-email-send.json /root/.credentials/
ADD crontab.txt /crontab.txt
RUN /usr/bin/crontab /crontab.txt

CMD ["crond","-f","-l","8"]
