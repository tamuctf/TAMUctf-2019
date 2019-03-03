# NoSQL Injection Secure Coding

Fix the NoSQL challenge

## Setup
Make sure in the file `/etc/gitlab-runner/config.toml` the line `pull_policy = "if-not-present"` is added under the `[runners.docker]` section.   
Build the docker image locally with `docker build -t messy/nosql .`  
Copy `nosql_exploit.py` and `nosql.py` to their respective locations.
Add `'nosql_server': ('nosql_server', ['nosql_exploit'], ['nosql'], 4000)` to the config file.

## Solution
The simplest solution is to modify `server.js` to change:
```
25 -            username: req.body.username,
26 -            password: req.body.password 
25 +            username: req.body.username.toString(),
26 +            password: req.body.password.toString()
```
