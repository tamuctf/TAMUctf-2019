FROM python:3.6.6-stretch

RUN apt-get update && apt-get install -y \
    apt-utils \
    build-essential
RUN pip install pyAesCrypt
COPY ./cert ./cert
COPY ./server.py ./server.py
COPY ./elliptic.py ./elliptic.py
COPY ./flag.txt ./flag.txt
COPY ./finitefield ./finitefield
COPY ./__pycache__ ./__pycache__
EXPOSE 5005
ENTRYPOINT ["python","./server.py"]
