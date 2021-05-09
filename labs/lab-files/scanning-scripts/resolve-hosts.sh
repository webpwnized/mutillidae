#!/bin/bash
	
PROGNAME=${0##*/}
PROGVERSION="0.1"
USAGE="USAGE: ./$PROGNAME [options] input_file"
SHORTOPTS="hv"
LONGOPTS="help,version"

TITLE="\n$PROGNAME by Jeremy Druin\n
Options:
\t-h | --help\t\tDisplay this help and exit
\t-v | --version\t\tDisplay version and exit"

if [ $# != 1 ]
then
	echo "$USAGE"
	exit 1
fi

ARGS=$(getopt -s bash --options $SHORTOPTS --longoptions $LONGOPTS --name $PROGNAME -- "$@" )

eval set -- "$ARGS"

while true; do
case $1 in
-h | --help) printf "${TITLE}\n\n"; printf "${USAGE}\n\n"; exit 0;;
-v | --version) printf "${PROGVERSION}\n"; exit 0;;
--) shift; break;;
*) break;;
esac
shift
done

#Final argument is required to be the input file
shift $(($OPTIND - 1))
INPUT_FILE=$1

input_file="$1"
while IFS='' read -r line || [[ -n "$line" ]]; do
    host "$line" | grep "has address" | sed 's/ has address /:/' &
done < $INPUT_FILE

