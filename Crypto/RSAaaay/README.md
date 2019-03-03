# Easy_RSA

This is a pretty simple challenge that just tests you know how RSA works.

### Solution
1.) Factorize n to find p and q, two primes (509/4973)
2.) Calculate ```phi=(p-1)(q-1)``` (phi=252776)
3.) Now find private key, d, where ```d=(e^-1) mod phi``` (d=58739)
4.) Use d to decrypt message segments ```m=(c^d) mod n```

The ciphertext segments should turn out ascii. Convert to text to find the flag:

```
gigem{Savage_Six_Flying_Tigers}
```

