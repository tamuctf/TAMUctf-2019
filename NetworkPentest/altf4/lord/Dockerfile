FROM python:alpine

RUN apk add --no-cache netcat-openbsd

COPY ./requirements.txt ./requirements.txt
RUN pip install -r ./requirements.txt && rm ./requirements.txt

ARG DIR=/usr/local/lord
COPY ./lord.py $DIR/lord.py
COPY ./bin $DIR/bin

ARG OWNER
RUN adduser -D $OWNER && \
    chown -R $OWNER:$OWNER $DIR && \
    chmod +x $DIR/bin/*

USER $OWNER
CMD ["python", "/usr/local/lord/lord.py"]
