# SQL

### How to setup:
Create docker container locally using Dockerfile called web_sql using "sudo docker build -t web_sql ."
Copy 'SQLi.py' and 'SQLiSimple.py' where they need to be


### Solution
One way would be to use prepared statements, one way being similar to something like this:
```
$params=array($_POST['Username'],$_POST['Password']);
$stmt=sqlsrv_query($conn,$sql,$params);
```

There are other options, like using the "real_escape_strings" function:
```
$user=$conn->real_escape_string($_POST['username']);
$pass=$conn->real_escape_string($_POST['password']);
```
