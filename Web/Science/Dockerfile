FROM ubuntu:latest 
RUN apt-get update
RUN apt-get install python-pip authbind -y

RUN mkdir -p /opt/tamuctf
COPY ./config.py /opt/tamuctf
COPY ./serve.py /opt/tamuctf
COPY ./requirements.txt /opt/tamuctf
COPY ./tamuctf /opt/tamuctf/tamuctf
COPY ./entry.sh /opt/tamuctf
COPY ./flag.txt /opt/tamuctf

WORKDIR /opt/tamuctf
VOLUME ["/opt/tamuctf"]

RUN pip install -r requirements.txt

EXPOSE 80

RUN groupadd ctf
RUN useradd -G ctf --home=/opt/tamuctf webuser

RUN chown -R webuser:webuser /opt/tamuctf

RUN chmod +x /opt/tamuctf/entry.sh
WORKDIR /opt/tamuctf
ENTRYPOINT ["./entry.sh"]
