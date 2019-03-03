# Obfuscaxor

## Challenge

`nc rev.tamuctf.com 3334`

## Setup
Build the container with `docker build -t obfsxor .` and run it with `docker run -it --rm obfsxor`

## Solution
Send the string `p3Asujmn9CEeCB3A` to stdin. This will be xored with key length 4 bytes and compared to fixed value. The binary is compiled with preprocessor definitions to make harder to reverse engineer.