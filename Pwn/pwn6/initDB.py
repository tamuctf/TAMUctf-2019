import sys, os
import sqlite3
import hashlib

def main():
  # Delete Old Database
  os.system("rm Banking.db")
  
  users = [
    (    '1337', '$6$$XwKlyKsHMyn8BlKnwq7tyEtQalo2hd0zHWZBEl5stXug57iQHDaQe7.4j21ux9ceIUzHs.QN2mMPWIjozhP4x/', 2, "joe@hotmail.com", "Joe Shmoe", 1, "cat"),                      # qwertyuiop
    (    '2667', '$6$$9Us.X/4EGH5pLAAuHe9YQZDZ514IB/t34t1QDq8/RDpttoySnmWSRughm2iILAbxgE83shOqAwu5wxwa6qzbl1', 3, "steve@aol.com", "Steve Aoki", 1, "blue"),                      # mynoob
    (   '23646', '$6$$VvmJf5rbK4qdwB.KNWMIQ1V3JUSshUl6nPKHpZzzOWBa0PsV5KXCHWXYivwbZnyfpbVVNn67VtGY.6XbRochY.', 10, "mike@magic.com", "Matt Mcconaughey", 1, "green"),             # 3rjs1la7qe                                                                                            # nimda                                                                                              # nimda
    (   '77041', '$6$$pwiot03wo0pXZcqngbuuieCRjXkJncAMvfuKxXJJLWXJFCwPMx1nIeqgw9HAdO6UEV1j3D1GDhXKgj8V1CVZ3.', 3, "robert@yahoo.com", "Rob Ford", 0, "Fish"),                     # nimda
    (   '77840', '$6$$O7dPNUJfCclYB1nV4Rlz0iZDhEztXjWYpx3lhXRnw30/.cAQnC0D01PIkQBlY3/FkNc8j5pHF55FAO59zKF6z1', 3, "kevin@gmail.com", "Kevin Daugherty", 2, "Spot"),               # 18atcskd2w
    ('10101010', '$6$$shvW9EeHPIVTtjWh9PGpGKz9ZcCXQfJcGY52SdG0GZM6pTZOwOEIOjUFah9KEYhxyEWCx3ZqaocTExGraYBQt.', 2, "Hugh+subscription@mansion.com", "Hugh Hefner", 3, "West Wing"), # 123qwe
    ('95127000', '$6$$9/cAEZ/prSr1mr4/cRBc7scFR4p9lJLf5Xpqs98G5uSFTsamztTc5HhSS4KqlNXtdZFWdzH0HjXtaM6gnPp1i.', 4, "peter@bakery.com", "Peter Bakery", 4, "Catcher in the Rye"),   # 1q2w3e4r
    ('EXTERN0', '', 0, '', '', 0, '')
    ]
	
  accounts = [
    ('1337', 0, 333.33),
    ('1337', 1, 666.66),
    ('1337', 2, 999.99),
    ('2667', 0, 111.11),
    ('2667', 1, 222.22),
    ('2667', 2, 444.44),
    ('23646', 0, 1.01),
    ('23646', 1, 2.02),
    ('23646', 2, 3.03),
    ('77041', 0, 99.99),
    ('77041', 1, 9.99),
    ('77041', 2, 0.99),
    ('77840', 0, 123.12),
    ('77840', 1, 231.23),
    ('77840', 2, 312.31),
    ('10101010', 0, 1000.01),
    ('10101010', 1, 100.01),
    ('10101010', 2, 10.01),
    ('95127000', 0, 55.55),
    ('95127000', 1, 5.55),
    ('95127000', 2, 500.50),
    ('EXTERN0', 0, 0),
    
    
  ]
  transactions = [
    (-74.36,"1337","EXTERN0","McAlisters","3/11/18"),
    (-14.84,"1337","EXTERN0","McDonalds","3/10/18"),
    (-6.71,"1337","EXTERN0","Shipleys","3/10/18"),
    (-11.24,"1337","EXTERN0","Arbies","3/09/18"),
    (-52.89,"1337","EXTERN0","McAlisters","3/07/18"),
    (-16.36,"2667","EXTERN0","Carinos","3/08/18"),
    (-5.89,"2667","EXTERN0","OliveGarden","3/07/18"),
    (-21.84,"2667","EXTERN0","Subway","3/05/18"),
    (-0.24,"2667","EXTERN0","StopAndGo","3/05/18"),
    (-18.71,"2667","EXTERN0","SevenEleven","3/04/18"),
    (-100.03,"77041","EXTERN0","Steam","3/10/18"),
    (-22.74,"77041","EXTERN0","Steam","3/09/18"),
    (-7.84,"77041","EXTERN0","Subway","3/07/18"),
    (-13.44,"77041","EXTERN0","GameStop","3/07/18"),
    (-31,"77041","EXTERN0","HumbleBundle","3/07/18"),
    (-3.33,"77840","EXTERN0","ProbePurchase","03/01/18"),
    (-3.33,"77840","EXTERN0","ProbePurchase","02/01/18"),
    (-3.33,"77840","EXTERN0","ProbePurchase","01/01/18"),
    (-3.33,"77840","EXTERN0","ProbePurchase","12/01/17"),
    (-3.33,"77840","EXTERN0","ProbePurchase","11/01/17"),
    (200,"10101010","EXTERN0","Paypal","03/11/18"),
    (200,"10101010","EXTERN0","Paypal","03/08/18"),
    (200,"10101010","EXTERN0","Paypal","03/06/18"),
    (200,"10101010","EXTERN0","Paypal","03/05/18"),
    (200,"10101010","EXTERN0","Paypal","03/03/18"),
    (200,"10101010","EXTERN0","Paypal","03/01/18"),
    (-951.83,"95127000","EXTERN0","Rent","06/01/18"),
    (-951.83,"95127000","EXTERN0","Rent","05/01/18"),
    (-951.83,"95127000","EXTERN0","Rent","04/01/18"),
    (-951.83,"95127000","EXTERN0","Rent","03/01/18"),
    (-951.83,"95127000","EXTERN0","Rent","02/01/18"),
    (-951.83,"95127000","EXTERN0","Rent","01/01/18"),
    ]

  pending = [
    ("95127000", "EXTERN0", 0, 0, 1934873, 3.50, "03/14/18"),
  ]
  conn = sqlite3.connect('Banking.db')

  # Create table
  conn.execute("create table if not exists user(userID PRIMARY KEY, pass, priv, email, name, security_question, answer)")
  conn.execute("create table if not exists account(userID, account_no, balance, CONSTRAINT unq UNIQUE (userID, account_no)) ")
  conn.execute("create table if not exists transact(value, fro, toward, note, date)")
  conn.execute("create table if not exists pend(userID, fro, to_acc, fro_acc, check_no PRIMARY KEY, value, date)")

  """
    userID - Number Identifier for user
    pass - string of sha512 hash of password
    balance - current signed integer value of their balance
    recent_purchase - json encoding of transactions
    priv - client version they last logged in with
    value - dollar amount double
    check_no - number identifer for check
    date - string identifier
    fro - userID account where transaction originated
    toward - userID account where transaction is going
  """

  # fill tables
  conn.executemany("insert into user(userID, pass, priv, email, name, security_question, answer) values (?, ?, ?, ?, ?, ?, ?)", users)
  conn.executemany("insert into account(userID, account_no, balance) values (?, ?, ?)", accounts)
  conn.executemany("insert into transact(value, fro, toward, note, date) values (?, ?, ?, ?, ?)", transactions)
  conn.executemany("insert into pend(userID, fro, to_acc, fro_acc, check_no, value, date) values (?, ?, ?, ?, ?, ?, ?)", pending)

  # Save (commit) the changes
  conn.commit()

  # We can also close the connection if we are done with it.
  # Just be sure any changes have been committed or they will be lost.
  conn.close()

if __name__ == "__main__":
  main()