#!/bin/bash
OLD_FILE=$1
NEW_FILE=$2

mv $OLD_FILE $OLD_FILE.bak
mv $NEW_FILE $OLD_FILE
chown root $OLD_FILE
postmap $OLD_FILE
