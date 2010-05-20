#!/bin/bash
regex='^[0-9]\.[0-9]\.[0-9]$'
shift
echo "regex: $regex"
echo

while [[ $1 ]]
do
    if [[ $1 =~ $regex ]]; then
        echo "$1 matches"
        i=1
        n=${#BASH_REMATCH[*]}
        while [[ $i -lt $n ]]
        do
            echo "  capture[$i]: ${BASH_REMATCH[$i]}"
            let i++
        done
    else
        echo "$1 does not match"
    fi
    shift
done

