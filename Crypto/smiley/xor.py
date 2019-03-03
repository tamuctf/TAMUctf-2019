sxor = ':)'
sflag = 'this is the flag'
flag = bytearray(sflag)
xor = bytearray(sxor)
encrypted = ''
for i in range(0,len(flag),2):
    encrypted += str(hex(flag[i] ^ xor[0]))[2:]
    encrypted += str(hex(flag[i+1] ^ xor[1]))[2:]
print encrypted
