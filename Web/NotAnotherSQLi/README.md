### BASIC SQL CHALLENGE ###

This challenge sets up a Dockerized ubuntu image, which is then set up as a LAMP server,
and runs a simple login php/mysql system which tests the challenger's knowledge of basic
SQL injection.


### INSTALLATION ###
1.) sudo docker build -t websql .
2.) sudo docker run websql

NOTE: ServerName is not suppressed, so container will spit out what IP the site is at (for me it was 172.17.0.2)
