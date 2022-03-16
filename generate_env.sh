#!/bin/bash

ENVS="$(< /var/www/.env.dist)"
for ENVI in $ENVS; do
  IFS='=' read -r key val <<< "$ENVI"
  echo "$key=`printenv "$key"`"
done