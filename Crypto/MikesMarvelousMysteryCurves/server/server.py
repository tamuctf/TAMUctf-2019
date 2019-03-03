#!/usr/bin/env python3
import socket,base64
from Crypto.Cipher import AES
from elliptic import *
from finitefield.finitefield import FiniteField

import os


def generate_private_key(numBits):
    return int.from_bytes(os.urandom(numBits // 8), byteorder='big')


def generate_public_key(privateKey, generator, pubFunction):
    return pubFunction(privateKey * generator)


def generate_shared_secret(privateKey, sharedFunction):
    return privateKey * sharedFunction()

if __name__ == "__main__":

    TCP_IP = '0.0.0.0'
    TCP_PORT = 5005
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind((TCP_IP,TCP_PORT))
    s.listen(1)

    c, addr = s.accept()
    with open('cert','r') as cert:
        data = cert.read()
    encodeddata = "-----BEGIN CERTIFICATE-----\n" + str(base64.b64encode(data.encode('utf-8')).decode('utf-8')) + "\n-----END CERTIFICATE-----"

    #Curve Exchange info
    F = FiniteField(412220184797, 1)
    curve = EllipticCurve(a=F(10717230661382162362098424417014722231813), b=F(22043581253918959176184702399480186312))
    G = Point(curve, F(56797798272), F(349018778637))
    c.sendall(encodeddata.encode('utf-8'))
    c.close()
    c1, addr = s.accept()
    c1.recv(1024)
    c1.close()
    c2, addr = s.accept()

    #Private key:
    private_key = 54628069049

    #Public key: (61801292647, 228288385004) order 137406875064
    public_key = generate_public_key(private_key, G, lambda x:x)

    #Key exchange
    peer_public_key = Point(curve,F(196393473219),F(35161195210))

    #Generate shared key
    shared_secret = generate_shared_secret(private_key, lambda: peer_public_key)
    secret_password = str(shared_secret.x.n) + str(shared_secret.y.n)
    print(secret_password)

    #Encrypt file with shared key and send it over
    encryption_suite = AES.new(secret_password,AES.MODE_CBC, 'yoyoyoyoyoyoyoyo')
    filename='flag.txt.aes'
    try:
        with open('flag.txt','rb') as flag_file:
            flag = flag_file.read()
        print(flag)
        flag_file.close()
        f = open(filename,'wb')
        f.write(encryption_suite.encrypt(flag))
        f.close()


        f = open(filename,'rb')
        l = f.read(1024)
        while (l):
           c2.send(l)
           l = f.read(1024)
        f.close()
        print('Done sending')
    finally:
        c2.close()
    os.remove('flag.txt.aes')
