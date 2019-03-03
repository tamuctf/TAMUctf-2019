# 04_privilege_escalation

We will have to get on to the devs for leaving that account on the website and machine.
Some good news is that we finally obtained a disk image of the machine. 
If the attacker modified the web files on the server they must have had higher privileges than the account you found.
See if you can find some information about how they could have done so.

Link to disk image: https://drive.google.com/open?id=1RYUjGXK94m-I7N1YJuypHudr2nCPRUJm (10 GB unzipped)

## Questions
1. What is the md5sum of the file that was most likely used or found by the attackers to get higher privileges?
2. What account were the attackers able to escalate to?
3. What is the password for that account?

## Answers
1. 93b74abb459cdd93bd254302fba4dfdf
2. root
3. 0A0YlBjrlBXSr14MPz
