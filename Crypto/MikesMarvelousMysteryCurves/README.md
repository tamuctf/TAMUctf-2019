# Elliptic Curve Diffie Hellman key exchange exploit
## Challenge
Mike, the System Administrator, thought it would be a good idea to implement his own Elliptic Curve Diffie Hellman key exchange using unnamed curves to use across the network. 
We managed to capture TCP traffic of the unencrypted key exchange along with an encrypted file exchange. See if you can read the contents of that file.

Hint: The password to the AES256-CBC encrypted file is the shared key in the ECDH key exchange. It is the string representation of the x coordinate concatenated with the y coordinate of the shared key.
	Example: 
		shared key: (12345,67890)
		password: 1234567890
Mike forgot to include the order of the points on the curve in his homemade certificates.

## Setup
Players are given ecdhKeyExchange.pcap


## Solution
In the "solution" directory, there are 2 files, aesDecrypter.py and sharedKeySolver.py
1. Open up the pcap in whichever app you prefer (I like wireshark)
2. There are 3 TCP streams. The first two have certificates in them and the third one has a file transfer.
3. The 2 certificate are just fake x509 certs encoded in base64. Not actually an ssl certificate. Decode those base64.
4. Get the information for the elliptic curve in the cert: 
	y^2 = x^3 + 10717230661382162362098424417014722231813x + 22043581253918959176184702399480186312 mod 412220184797
5. And the generator point(they're the same for both): 
	G = (56797798272,349018778637)
6. Gather the public key from both: 
	PubA = (61801292647,228288385004) 
	PubB = (196393473219,35161195210)
7. Calculate the order of either point. They should be the same. The library I used utilizes Shank's algorithm to complete this task: 
	In "sharedKeySolver.py", this is done with the function computeOrder()
	Order = 137406875064
8. Figure out either one of private keys using the Pohlig-Hellman algorithm with the Chinese Remainder theorem to solve this discrete logarithm problem:
	PrivA = 54628069049 
	PrivB = 6895697291
9. Use that private key to generate the shared secret by finishing the last step in the elliptic curve diffie hellman key exchange: 
	shared secret = (PrivA * PubB) or (PrivB * PubA) = (130222573707,242246159397)
		- (where * is elliptic curve point multiplication)
10. Form the password by concatenating the x and y values from the shared secret:
	password = 130222573707242246159397
11. Save the raw data from TCP stream 2 (the third one) as a file.
12. Decrypt the AES256-CBC encrypted file with the password.
13. Cat the decrypted file and grep gigem for the flag:
	gigem{Forty-two_said_Deep_Thought}
