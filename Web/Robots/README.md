# Robots.txt

Objective is for the player to access the robots.txt file, but they must have their user-agent changed to be a variant for a Googlebot to see the version with the flag.

## Setup

Install docker and while in the challenge directory, run
```
sudo docker build -t robots .
sudo docker run robots
```

## Solution

The solution uses the 'Requests' module for Python to send a crafted HTTP GET request that will have a different user-agent to the webserver.

The PHP script for robots.txt is looking for a user-agent that contains "googlebot" (case-insensitive), and upon finding that will print the hidden message with the flag.
