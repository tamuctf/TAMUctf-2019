FROM python:3.5-alpine
RUN apk update && apk add build-base
RUN pip3 install pycrypto
COPY ./alice.py ./alice.py
COPY ./AESCipher.py ./AESCipher.py
COPY ./DiffieHellman.py ./DiffieHellman.py
COPY ./script.txt ./script.txt
CMD ["python", "./alice.py"]
