FROM python:3-alpine

RUN apk add --no-cache gcc python3-dev musl-dev

COPY ./app /usr/local/web
RUN pip install -r /usr/local/web/requirements.txt

COPY ./ca.naum.crt /etc/ssl/ca.naum.crt
ENV CA_CERT=/etc/ssl/ca.naum.crt

WORKDIR /usr/local/
CMD ["gunicorn", "-w", "1", "--worker-class", "eventlet", "-b", "0.0.0.0:80", "web:app"]
