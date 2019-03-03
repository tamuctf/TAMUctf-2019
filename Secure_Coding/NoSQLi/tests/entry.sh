#!/bin/bash

mongod --quiet 1>/dev/null &
sleep 5s;
node server.js &
sleep 2s;
python tests/queue.py
