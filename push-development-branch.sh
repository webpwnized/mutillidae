#!/bin/bash

if (( $# != 2 ))
then
    printf "%b" "Usage: git.sh <version> <annotation>\n" >&2;
    exit 1;
fi;

VERSION=$1;
ANNOTATION=$2;

echo "Calling git.sh with tag $VERSION with annotation \"$ANNOTATION\"";
./git.sh "$VERSION" "$ANNOTATION";

echo "Checking out main branch";
git checkout main;

echo "Merging development branch";
git merge development;

echo "Calling git.sh with tag $VERSION with annotation \"$ANNOTATION\"";
./git.sh "$VERSION" "$ANNOTATION";

echo "Checking out development branch";
git checkout development;

git status;