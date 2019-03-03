
# Blind SQL Injection

Objective is for the player to use blind SQL injections to get the user account that is using the SQL table, which is the flag.

## Setup

Install docker and  while in the challenge directory, run
```
sudo docker build -t blind-sql .
sudo docker run blind-sql
```

## Solution

The solution requires using blind boolean sql injections to bruteforce each character of the user account name. 
Here is a general guide on using these: http://www.danieledonzelli.com/ethical-hacking/blind-sql-injection-boolean-based/

A key injection would be
```
' OR substr(user(), 1, 1) = 'w';-- -
```
and adjusting the injection parameters as needed to brute force each character.
A successful injection will return a gif called Nice_Going!.gif, so looking for that in the HTTP response will be key.
I used the requests module in Python, as shown in the solution script. 
