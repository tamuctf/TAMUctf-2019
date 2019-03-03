# Onboarding Checklist
## Challenge
From: importantperson@somebigcorp.com<br/>
Date: Feb 22, 2019 9:00 AM<br/>
To: someguy@somebigcorp.com<br/>
Subject: New Employee Access

Hello Some Guy,

We need to begin sending requests for the new employee to get access to our security appliances. I believe they already know that you are authorized to make a new account request. Would you mind sending the new employee's email address to tamuctf@gmail.com so they can process the account request?

Thank you,<br/>
Important Person

## Configuration
```docker build -t spoofers .```<br/>
```docker run spoofers```

```credentials.json``` and ```gmail-python-email-send.json``` are both in the tamuctf team drive under TAMUctf 2019 \> Keys \> spoofers email bot credentials

## Solution
Send a spoofed email address to tamuctf@gmail.com using the sender address someguy@somebigcorp.com<br/>
Make sure an email that you have access to is included in the email body.
