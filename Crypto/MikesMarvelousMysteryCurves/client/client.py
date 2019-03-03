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
    #TCP_IP = 'localhost'
    TCP_IP = '192.168.11.4'
    TCP_PORT = 5005
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.connect((TCP_IP,TCP_PORT))

    with open('cert','r') as cert:
        data = cert.read()
    encodeddata = "-----BEGIN CERTIFICATE-----\n" + str(base64.b64encode(data.encode('utf-8')).decode('utf-8')) + "\n-----END CERTIFICATE-----"
    #Curve Exchange info
    F = FiniteField(412220184797, 1)
    curve = EllipticCurve(a=F(10717230661382162362098424417014722231813), b=F(22043581253918959176184702399480186312))
    G = Point(curve, F(56797798272), F(349018778637))
    s.recv(1024)
    s.close()
    s1 = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s1.connect((TCP_IP,TCP_PORT))
    s1.sendall(encodeddata.encode('utf-8'))
    s1.close()

    s2 = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s2.connect((TCP_IP,TCP_PORT))

    #Private key:
    private_key = 6895697291

    #Public key: (196393473219,35161195210) order 137406875064
    public_key = generate_public_key(private_key, G, lambda x:x)

    #Key exchange
    peer_public_key = Point(curve,F(61801292647),F(228288385004))

    #Generate shared key
    shared_secret = generate_shared_secret(private_key, lambda: peer_public_key)
    print(str(shared_secret))
    secret_password = str(shared_secret.x.n) + str(shared_secret.y.n)
    print(secret_password)

    #Receive encrypted file and decrypt it
    with open('flag.txt.aes', 'wb') as f:
        while True:
            data = s2.recv(1024)
            if not data:
                break
            # write data to a file
            f.write(data)
    f.close()
    s2.close()
    print('connection closed')
    with open('flag.txt.aes','rb') as encrypted_file:
        encrypted_flag = encrypted_file.read()
    decryption_suite = AES.new(secret_password,AES.MODE_CBC,'0000000000000000')
    flag = decryption_suite.decrypt(encrypted_flag)
    with open('flag.txt','wb') as flag_file:
        flag_file.write(flag)
