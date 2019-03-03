# VeggieTales
## Challenge
This challenge requires players to investigate this server application for vulnerabilities.
nc \<IP\> \<port\> 
Hint: I've seen episode 5 at least 63 times.

## Setup
Note - Player is **NOT** given server side script
Build the docker file: `sudo docker build -t pickle .`
Run the docker: `sudo docker run -p 9000:8448 pickle`


## Solution
solver.py is an example of a working solution. Run using `python3 solver.py`
1. Have the script create a backup and copy the string
2. Do a rot13 on that string
3. Decode the string using base64
4. Figure out python is using pickle to save the state of the watchlist
5. Write and pickle a malicious python object that runs '/bin/sh' when unpickled
6. Encode the pickled object string in base64
7. Do a rot13 on the string
8. Enter the string in the area to load your watchlist.
8. cat flag.txt
