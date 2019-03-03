# Weakness of Knapsack Cryptosystems
Easy / Medium depending on programming proficiency.

## Description: 
For the most part, this challenge shows that someone fully understands how the MerkleHellman
cryptosystem works, as well as the fundamentals of knapsack based crypto systems. The
challenge demonstrates the fundamental weakness of knapsack cryptosystems i.e. large keys are
needed in order to make them secure. This is impractical which is why they are not really used in
practice. https://en.wikipedia.org/wiki/Merkle–Hellman_knapsack_cryptosystem  
  
In this challenge, the Merkle-Hellman cryptosystem is used to encrypt a flag using the public key. The
players will be given the public key and will use it to decrypt the flag.

## Challenge:
```
11b90d6311b90ff90ce610c4123b10c40ce60dfa123610610ce60d450d000ce61061106110c4098515340d4512361534098509270e5d09850e58123610c9
```  
Public key: `{99, 1235, 865, 990, 5, 1443, 895, 1477}`

## Solution:
`gigem{merkle-hellman-knapsack}`
Key Points:
- The public key is short (only 8 integers); This can be brute-forced
- The sum of the integers in the public key is 4,096 < 7,009 < 65,536 → 16<sup>3</sup> < 7,009 < 16<sup>4</sup>, which means that 4 hexadecimal digits are needed for each letter that is encrypted. (The size of the encryption is 4 times larger than the size of the flag.)
- The public key is 8 integers long meaning that 8 bits are used… So the player could assume that ASCII was being used.

## Possible Future Hard challenges that build off of this one
- Implement a Merkle-Hellman Cryptosystem that uses a public key of size 52 (give or take). This
would make brute-forcing ineffective in the traditional sense (2<sup>52</sup>)… Modern techniques can reduce this search space to 2<sup>n/2</sup> = 2<sup>26</sup> (which could be brute-forced)
