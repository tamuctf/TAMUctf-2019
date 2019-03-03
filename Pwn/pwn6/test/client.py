#!/usr/bin/env python
import socket
import struct
from time import sleep

def wait_secs():
  sleep(1)

def build_header(size, cmd):
  return struct.pack('II', size, cmd)

def build_login(username, password):
  body = struct.pack("BB", len(username), len(password)) + username + password
  return build_header(len(body), 0) + body 

def build_check_balance(account_no):
  return build_header(4, 1) + struct.pack("i", account_no)

def build_deposit(account_no, check, value, date):
  body = struct.pack("B", len(date)) + struct.pack("i", account_no) + struct.pack("i", check) + struct.pack("d", value) + date
  return build_header(len(body), 3) + body

def build_create_account(account_no):
  body = struct.pack("i", account_no)
  return build_header(len(body), 2) + body 

def build_create_login(username, password, priv, email, name, question, answer):
  body = struct.pack("BBBBBBB", len(username), len(password), priv, len(email), len(name), len(answer), question) + username + password + email + name + answer
  return build_header(len(body), 97) + body 

def build_update(swtch, field):
  body = struct.pack("BB", swtch, len(field)) + field
  return build_header(len(body), 9) + body 

def build_proc_pending():
  return build_header(0, 94)

def build_transfer(user, from_acc, to_acc, check_no, value, date):
  body = struct.pack("BBiii", len(user), len(date), check_no, from_acc, to_acc) + struct.pack("d", value) + user + date
  return build_header(len(body), 6) + body

def build_arb_transfer(user, second_user, from_acc, to_acc, check_no, value, date):
  body = struct.pack("BBBiii", len(user), len(second_user), len(date), check_no, from_acc, to_acc) + struct.pack("d", value) + user + second_user + date
  return build_header(len(body), 96) + body

def send_login(s, username, password):
  s.send(build_login(username, password))
  print "The login using credentials %s:%s was %s" % (username, password, "Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()

def send_check_balance(s, account_no):
  s.send(build_check_balance(account_no))
  print "Your Balance for account %i was %.2f" % (account_no, struct.unpack("d",s.recv(8))[0])
  wait_secs()

def send_deposit(s, account_no, check, value, date):
  s.send(build_deposit(account_no, check, value, date))
  print "Adding check %i, to account %i, of value %.2f on %s was %s" % (check, account_no, value, date, "Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()

def send_create_login(s, username, password, priv, email, name, question, answer):
  s.send(build_create_login(username, password, priv, email, name, question, answer))
  print "Creating Login with credentials %s:%s, with priveldge %i was %s" % (username, password, priv, "Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()

def send_create_account(s, new_account):
  s.send(build_create_account(new_account))
  print "Creating Account %i, Request was %s" % (new_account, "Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()

def send_update(s, swtch, field):
  s.send(build_update(swtch, field))
  print "Updating field %i, with value %s, Request was %s" % (swtch, field, "Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()

def send_transfer(s, user, from_acc, to_acc, check_no, value, date):
  s.send(build_transfer(user, from_acc, to_acc, check_no, value, date))
  print "Request was %s" % ("Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()
  
def send_arb_transfer(s, user, second_user, from_acc, to_acc, check_no, value, date):
  s.send(build_arb_transfer(user, second_user, from_acc, to_acc, check_no, value, date))
  print "Request was %s" % ("Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()
  
def send_proc_pending(s):
  s.send(build_proc_pending())
  print "Request was %s" % ("Sucessful" if struct.unpack("B",s.recv(1))[0] == 0x1 else "Failure")
  wait_secs()

def main():
  # Build connection to server
  s = socket.socket()
  host = "172.17.0.2"  #socket.gethostname()
  port = 6210
  s.connect((host, port))

  # Define test constants
  username = "77840"
  account_no = 1
  new_account = 9
  check = 19570
  value = 954.50
  priv = 3
  password = "18atcskd2w"
  date = "10/23/85"
  a_user = "23646"
  a_pass = "nimda"
  b_user = "1337"
  b_pass = "qwertyuiop"
  c_user = "nadmin"
  c_pass = "nimdan"
  d_user = "10101010"
  d_pass = "123qwe"
  email = "newacc@dodgeit.com"
  name = "Bobby"
  question = 2
  answer = "Robert'); Drop Table Students;--"
  swtch = 2
  field = "Super Smash Bros"
  from_acc_1 = 0
  to_acc_2 = 1
  transfer_check = 37641
  transfer_value = 30.48
  
  #send login, and check balance request
  #send_login(s, username, password)
  #send_check_balance(s, account_no)
  #send_deposit(s, account_no, check, value, date)
  #send_create_account(s, new_account)
  #send_login(s, a_user, a_pass)
  #send_create_login(s, c_user, c_pass, priv, email, name, question, answer)
  #send_login(s, c_user, c_pass)
  #send_update(s, swtch, field)
  send_login(s, "2667", "mynoob")
  send_check_balance(s, 1)
  #send_transfer(s, c_user, from_acc_1, to_acc_2, transfer_check, transfer_value, date)
  #send_login(s, b_user, b_pass) # causes segfault
  #send_login(s, username, password)
  #for i in range(0, 3):
  #  send_transfer(s, d_user, from_acc_1, to_acc_2, transfer_check+i, transfer_value, date)
  #  send_deposit(s, account_no, check+i, value+(i*2.0), date)
  send_login(s, a_user, a_pass)
  send_proc_pending(s)
  #send_create_account(s, 1)
  #send_create_account(s, 6)
  send_arb_transfer(s, d_user, c_user, from_acc_1, to_acc_2, 12345, 33.33, date)
  #send_deposit(s, account_no, check, value, "a"*255) Old client VULN

if __name__ == "__main__":
  main()

#s.send(build_header(4, 1) + 'a'*2)
#sleep(2)
#s.send('b'*2)
#sleep(2)

#s.send(build_header(4, 1) + 'c'*4 + build_header(6, 1) + 'd'*6 + build_header(54, 1) + 'e'*54)
#s.send(build_header(20, 1) + 'f'*20)
#sleep(2)
#s.send(build_header(7, 1) + 'g'*7)
#s.send(build_header(8, 1) + 'h'*8)
#print(s.recv(1024))
