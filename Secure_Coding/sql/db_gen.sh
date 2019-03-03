#start, set up mysql database
#create user 'sqli-user' with permissions on
#DATABASE: SqliDB;
#one TABLE: login, two parameters in login: (user, password)
#randomize password for root
service mysql start && mysql -uroot -e "CREATE DATABASE SqliDB; CREATE USER 'sqli-user'@'localhost' IDENTIFIED BY 'AxU3a9w-azMC7LKzxrVJ^tu5qnM_98Eb'; GRANT ALL PRIVILEGES ON SqliDB.* TO 'sqli-user'@'localhost'; USE SqliDB; CREATE TABLE login (User varchar(20), Password varchar(100)); INSERT INTO login (User,Password) VALUES ('admin', 'tS&LjHue6Z&m*&JeTU#U%btyA8gmJXh'); INSERT INTO login (User,Password) VALUES ('bobsagget', 'password'); SET PASSWORD FOR root@'localhost' = PASSWORD('^wn=GBr^92@&wf+Ebq3w!CsTP4%Mr6+_')";
#run in background