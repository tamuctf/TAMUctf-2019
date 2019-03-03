#!/bin/bash
service mysql start;
mysql -u root -e "CREATE DATABASE SqliDB; USE SqliDB; CREATE TABLE Search (items VARCHAR(100)); GRANT SELECT ON SqliDB.Search TO 'gigem{w3_4r3_th3_4ggi3s}'@'localhost' IDENTIFIED BY '1VmHrwxT1iuVag^@PtuDC@KEd421v9'; INSERT INTO Search (items) VALUES ('Eggs'); INSERT INTO Search (items) VALUES ('Trucks'); INSERT INTO Search (items) VALUES ('Aggies'); SET PASSWORD FOR 'root'@'localhost' = PASSWORD('4P0m8B39P8mUMOt7bxl*H41%EhEWxR');";
# service apache2 restart;
apache2 -D FOREGROUND;
# service mysql start
# Starts up the 'mysql' service on the host.

# mysql -u root -e '...'
# Starts up the mysql service as the user 'root', then executes the command
# following the -e option.

# CREATE DATABASE SqliDB;
# Creates a database called 'SqliDB'.

# USE SqliDB;
# Set the current database being used to 'SqliDB'.

# CREATE TABLE Search (items VARCHAR(100));
# Creates a table in the database called 'Search'. It has one parameter called
# 'items', which can take in 100 characters string.

# GRANT SELECT ON SqliDB.Search TO 'w3_4r3_th3_4ggi3s'@'localhost' IDENTIFIED BY '1VmHrwxT1iuVag^@PtuDC@KEd421v9';
# Creates the user 'w3_4r3_th3_4ggi3s' and assigns it a randomized password.
# The user is then granted permissions to search the 'SqliDB' database.

# INSERT INTO Search (items) VALUES ('Eggs');
# Inserts the value 'Eggs' into the 'Search' table under the 'items' parameter.

# INSERT INTO Search (items) VALUES ('Trucks');
# Inserts the value 'Trucks' into the 'Search' table under the 'items' parameter.

# INSERT INTO Search (items) VALUES ('Aggies');
# Inserts the value 'Aggies' into the 'Search' table under the 'items' parameter.

# SET PASSWORD FOR 'root'@'localhost' = PASSWORD('4P0m8B39P8mUMOt7bxl*H41%EhEWxR');
# Changes the password for 'root' to a randomized one that is hashed.
