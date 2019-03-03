#!/bin/sh

set -ex

echo "Building ircd-hybrid from $IRCD_REPO_URL"

mkdir build
cd build

git clone "$IRCD_REPO_URL" ircd
if [ -z "$(ls ircd)" ]; then
    echo "BUILD FAILED: Could not clone $IRCD_REP_URL"
    exit 1
fi

cd ircd

if [ -n "$IRCD_REPO_CHECKOUT" ]; then
    git checkout "$IRCD_REPO_CHECKOUT"
fi

./configure $IRCD_OPTIONS && make && make install

cd ../..

rm -rf build
