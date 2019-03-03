#!/bin/bash

mongod --quiet 1>/dev/null &
sleep 5s;
node server.js
