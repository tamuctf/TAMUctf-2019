FROM python:3.5-alpine
RUN apk update && apk add build-base
RUN pip3 install pycrypto
COPY ./bob.py ./bob.py
COPY ./AESCipher.py ./AESCipher.py
COPY ./DiffieHellman.py ./DiffieHellman.py
COPY ./script.txt ./script.txt
CMD ["python", "./bob.py"]
