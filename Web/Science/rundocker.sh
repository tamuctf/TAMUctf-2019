#!/bin/sh
hash=$(sudo docker build . | tail -1 | cut -b 19-31)
echo "the hash is $hash"
sudo docker run -p 8000:8000 -it $hash
