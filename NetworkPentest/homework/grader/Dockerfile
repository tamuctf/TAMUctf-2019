FROM python:3-alpine

COPY ./app /usr/local/grader
RUN pip install -r /usr/local/grader/requirements.txt

COPY ./ca.naum.crt /etc/ssl/ca.naum.crt
ENV CA_CERT=/etc/ssl/ca.naum.crt

COPY ./flag.txt /home/root/flag.txt
RUN chmod -R 600 /home/root

WORKDIR /usr/local/
CMD ["python", "-m", "grader"]
