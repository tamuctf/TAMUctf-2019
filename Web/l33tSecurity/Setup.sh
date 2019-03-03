#!/bin/bash
service mysql start;

mysql -u root -e "CREATE DATABASE 1337_Secur1ty; USE 1337_Secur1ty; CREATE TABLE Users (UserID int(9) NOT NULL auto_increment, Username VARCHAR(20) NOT NULL, Password VARCHAR(50) NOT NULL, FirstName VARCHAR(10) NOT NULL, LastName VARCHAR(15) NOT NULL, Phone VARCHAR(10), Email VARCHAR(37) NOT NULL, Description VARCHAR(200), CreateDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, Secret VARCHAR(50) NOT NULL, PRIMARY KEY(UserID)); CREATE TABLE Messages (MessageID int(9) NOT NULL auto_increment, Username VARCHAR(20) NOT NULL, FirstName VARCHAR(15) NOT NULL, CreateDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, MessageTo VARCHAR(20) NOT NULL, Message VARCHAR(10000), PRIMARY KEY (MessageID)); GRANT SELECT ON 1337_Secur1ty.Users TO 'vulnadmin'@'localhost' IDENTIFIED BY 'ayKOMD13&o8@?!D0FkUB'; GRANT SELECT ON 1337_Secur1ty.Messages TO 'vulnadmin'@'localhost';GRANT SELECT, INSERT ON 1337_Secur1ty.Users TO 'ctfadmin'@'localhost' IDENTIFIED BY 'a7u&09Tq&xLY60lbvPbJ'; GRANT SELECT, INSERT ON 1337_Secur1ty.Messages TO 'ctfadmin'@'localhost'; GRANT SELECT, UPDATE ON 1337_Secur1ty.Users TO 'updateadmin'@'localhost' IDENTIFIED BY 'c%L68TPZ!n!JOxezuKvR'; GRANT SELECT, UPDATE ON 1337_Secur1ty.Messages TO 'updateadmin'@'localhost'; INSERT INTO Users(Username, Password, FirstName, LastName, Phone, Email, Description, Secret) VALUES ('1337-admin', '02ca0b0603222a090fe2fbf3ba97d90c', 'Joe', 'Joeson', '', '1337-admin@l337secur1ty.hak', 'Most secure admin to ever grace existence.', 'WIFHXDZ3BOHJMJSC'); INSERT INTO Users(Username, Password, FirstName, LastName, Phone, Email, Description, Secret) VALUES ('ScrubLord', 'fc8b8be2abe4a79bf6f36eee484c1f08', 'Bob', 'Bobson', '', 'ScrubLord@l337secur1ty.hak', 'That random intern.', '4VCLO52ALSUUO5OM'); INSERT INTO Messages(Username, Firstname, Message, MessageTo) VALUES ('ScrubLord', 'Bob', 'Please don\'t blow off the meeting today, we need to talk about the cookies.', '1337-admin'); SET PASSWORD FOR 'root'@'localhost' = PASSWORD('JW1gArlX3OqcH1NTE3o');";

apache2 -D FOREGROUND;

#CREATE DATABASE 1337_Secur1ty;

#USE 1337_Secur1ty;

#CREATE TABLE Users (UserID int(9) NOT NULL auto_increment, Username VARCHAR(20) NOT NULL, Password VARCHAR(50) NOT NULL, FirstName VARCHAR(10) NOT NULL, LastName VARCHAR(15) NOT NULL, Phone VARCHAR(10), Email VARCHAR(37) NOT NULL, Description VARCHAR(200), CreateDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, Secret VARCHAR(50) NOT NULL, PRIMARY KEY(UserID));
#CREATE TABLE Messages (MessageID int(9) NOT NULL auto_increment, Username VARCHAR(20) NOT NULL, FirstName VARCHAR(15) NOT NULL, CreateDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, MessageTo VARCHAR(20) NOT NULL, Message VARCHAR(10000), PRIMARY KEY (MessageID));

#GRANT SELECT ON 1337_Secur1ty.Users TO 'vulnadmin'@'localhost' IDENTIFIED BY 'ayKOMD13&o8@?!D0FkUB';
#GRANT SELECT ON 1337_Secur1ty.Messages TO 'vulnadmin'@'localhost';

#GRANT SELECT, INSERT ON 1337_Secur1ty.Users TO 'ctfadmin'@'localhost' IDENTIFIED BY 'a7u&09Tq&xLY60lbvPbJ';
#GRANT SELECT, INSERT ON 1337_Secur1ty.Messages TO 'ctfadmin'@'localhost';

#GRANT SELECT, UPDATE ON 1337_Secur1ty.Users TO 'updateadmin'@'localhost' IDENTIFIED BY 'c%L68TPZ!n!JOxezuKvR';
#GRANT SELECT, UPDATE ON 1337_Secur1ty.Messages TO 'updateadmin'@'localhost';

#INSERT INTO Users(Username, Password, FirstName, LastName, Phone, Email, Description, Secret) VALUES ('1337-admin', '02ca0b0603222a090fe2fbf3ba97d90c', 'Joe', 'Joeson', '', '1337-admin@l337secur1ty.hak', 'Most secure admin to ever grace existence.', 'WIFHXDZ3BOHJMJSC');
#INSERT INTO Users(Username, Password, FirstName, LastName, Phone, Email, Description, Secret) VALUES ('ScrubLord', 'fc8b8be2abe4a79bf6f36eee484c1f08', 'Bob', 'Bobson', '', 'ScrubLord@l337secur1ty.hak', 'That random intern.', '4VCLO52ALSUUO5OM');

#INSERT INTO Messages(Username, Firstname, Message, MessageTo) VALUES ('ScrubLord', 'Bob', 'Please don\'t blow off the meeting today, we need to talk about the cookies.', '1337-admin');

#SET PASSWORD FOR 'root'@'localhost' = PASSWORD('JW1gArlX3OqcH1NTE3o');
