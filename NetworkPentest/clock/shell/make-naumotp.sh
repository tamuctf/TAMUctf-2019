#!/bin/sh

set -e

echo "Building Naum OTP from $NAUMOTP_REPO_URL"

OWD=$PWD

mkdir build-tmp
cd build-tmp

git clone "$NAUMOTP_REPO_URL" naumotp
if [ -z "$(ls naumotp)" ]; then
    echo "BUILD FAILED: Could not clone $NAUMOTP_REPO_URL"
    exit 1
fi

cd naumotp

if [ -n "$NAUMOTP_REPO_BRANCH" ]; then
    git checkout "$NAUMOTP_REPO_BRANCH"
fi

make && make install

cd $OWD

rm -rf build-tmp
