# 03_Forensics

Unfortunately it looks like the attackers used pretty standard tools to hack into our website.
It looks like they didn't modify the web page from the admin interface on the website though. 
They probably logged into the webserver somehow. Can you see if you can find out how they got credentials to log in?

## Questions
1. List the compromised usernames in comma separated alphabetical order
2. What username and password combo were the attackers most likely able to get ahold of? (format as username:password)

## Answers
1. admin,alice,bob,devtest,suzy
2. devtest:driveby
