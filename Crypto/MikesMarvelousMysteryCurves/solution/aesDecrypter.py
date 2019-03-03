#!/usr/bin/env python3
from Crypto.Cipher import AES
secret_password = "130222573707242246159397" 

with open('flag.txt.aes','rb') as encrypted_file:
        encrypted_flag = encrypted_file.read()
decryption_suite = AES.new(secret_password,AES.MODE_CBC,'0000000000000000')
flag = decryption_suite.decrypt(encrypted_flag)
with open('flag.txt','wb') as flag_file:
    flag_file.write(flag)
