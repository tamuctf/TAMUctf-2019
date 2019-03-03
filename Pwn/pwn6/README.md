## Description: 
Banking on Vulnerabilities

A new startup has set up a banking application, and has recieved many
complaints from users about unathorized transactions. IT has claimed it
is mostly users faults, but did patch a vulnerability they noticed.

A server and client binary has been provided to find and test vulnerability.

nc IP PORT

## Documentation:

1. Run initDB.py before packaging in docker
    This deletes all current entries in Banking.db and reinitializes with preset values
    Contains 4 tables:
        users - account number, password encrypted, priveledge level, email account, account holder name, which question, answer.
            Only number, password, and priveledge are relevant and implemented.
        accounts - (irrelevant to challenge) each account has checking, savings account with balance
        transactions - (irrelevant to challenge) history of transactions, pending entries moved here
        pending - (irrelevant to challenge) where new transactions are added

2. Run ```make``` which builds client, and server.

3. Package into docker image using Dockerfile, this moves a db, and server and flag file into docker image.

4. Docker runs server, binary is provided to find gadgets

## Solution:
1. Challenger runs client to connect to server, binary is provided to understand messages.
        Challenger can use wireshark or reverse engineering to understand messages

2. Challenger prints recent logins on client, these are hardcoded, gives access to 2 accounts
        1337, and 23646, with permissions of 2 and 10 respectively.
        

3. Challenge bruteforces password with login requests using common passwords or password lists.
        Passwords for both are from top 100 passwords. 
        There is 2 possible passwords based on 2 usernames provided to brute force
        These are pulled from 20 most common passwords in 2016, password is also set to all lowercase
        User 1: 1337 (qwertyuiop), and User 2: 23646 (3rjs1la7qe)
        (challenger could guess other login usernames)

4. Challenger must login to 23646 with permissions 10 and create another user 
        Permissions 10 gives access to creating another user.
            state->actions[97] = create_login;

5. Challenger must use priv 10 login, to create a new user with priv 3 based on populate actions code
        if(priv >= 4 || priv < 3)
        {
            client->client_actions[3] = deposit;
        }

6. Challenger should use a ropgadget tool to get from server
    1. A data section to place string in safely like(6d0240)
    2. pop rdi; ret
    3. pop rax; ret
    4. pop rdx ; ret
    5. pop rsi ; ret
    6. mov qword ptr [rdi], rax ; ret
    7. and the address of system from main

7. Challenger must then send malicious payload to faulty deposit to trigger buffer overflow and create reverse shell
        Code for full exploit is found in exploit.py
