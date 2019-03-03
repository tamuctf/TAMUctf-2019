#!/bin/sh
# Start CTFd
python serve.py &
sleep 1s
python tests/queue.py
