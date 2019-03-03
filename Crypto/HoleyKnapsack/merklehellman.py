#Python file for Merkle-Hellman Implementation

#Function used to encrypt message
def encrypt(message):
    serverPublicKey = [99, 1235, 865, 990, 5, 1443, 895, 1477]
    cipherText = ""
    for c in message:
        temp = ord(c)
        s = '{0:08b}'.format(temp)
        i = 7
        num = 0
        for ch in s:
            if ch == '1':
                num += serverPublicKey[i]

            i-=1
                
        cipherText += format(num, '04x')
        
    return cipherText


#Function used to get a powerset of a set
def powerSet(s):
    x = len(s)
    ps = []
    for i in range(1 << x):
        ps.append([s[j] for j in range(x) if (i & (1 << j))])

    return ps



#BruteForce Knapsack Encryption
def bruteForceKnapsack(cipherText, pk):
    i = 1
    sets = powerSet(pk)

    cipherList = [''.join(t) for t in zip(*[iter(cipherText)]*4)]
    message = ""
    for c in cipherList:
        num = int(c,16)
        p = 0
        found = False
        while not found:
            guess = sum(sets[p])
            if guess == num:
                asciiVal = ""
                curr = 7;
                while curr >= 0:
                    if pk[curr] in sets[p]:
                        asciiVal += "1"
                        curr -= 1
                    else:
                        asciiVal += "0"
                        curr -= 1

                
                message += chr(int(asciiVal,2))
                found = True
            else:
                p += 1

    return message



#Decrypt Knapsack Algorithm using Private Key
def decrypt(cipherText):
    
    cipherList = [''.join(t) for t in zip(*[iter(cipherText)]*4)]
    message = ""
    privateKey = [3, 7, 11, 30, 61, 135, 377, 851]
    n = 1037
    m = 1506
    inverse_n = 1217

    for c in cipherList:

        num = int(c,16)
        temp = (num*inverse_n)%m
        asciiVal = ""
        curr = 7
        while temp > 0:
            if temp >= privateKey[curr]:
                asciiVal += "1"
                temp -= privateKey[curr]
            else:
                asciiVal += "0"

            curr -= 1

        while len(asciiVal) < 8:
            asciiVal += "0"
            
        message += chr(int(asciiVal,2))
            
        
    return message


publicKey = [99, 1235, 865, 990, 5, 1443, 895, 1477]
superIncreasing = [3, 7, 11, 30, 61, 135, 377, 851]

flag = encrypt("gigem{merkle-hellman-knapsack}")
print(flag)


message = bruteForceKnapsack(flag, publicKey)
print(message)
