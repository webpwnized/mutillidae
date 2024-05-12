#!/bin/bash

if (( $# != 2 ))
then
    printf "%b" "Usage: git.sh <version> <message>\n" >&2
    exit 1
fi

sudo git tag -a "$1" -m "$2"
sudo git commit -a -m "$1: $2"
sudo git push --tag
sudo git push
