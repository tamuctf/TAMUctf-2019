#!/bin/bash

pushd /logs
    python3 -m http.server 8080 &
popd

pushd /chal
    python3 ./server.py
popd
