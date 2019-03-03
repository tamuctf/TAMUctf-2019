#!/bin/bash

for f in /backup/scripts/*.sh; do
  chmod +x "$f"
  bash -c "$f"  || break
done
