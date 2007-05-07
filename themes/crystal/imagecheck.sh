#!/bin/bash
grep "\$images" images.inc | sed "s/.*= '//g" | sed "s/';//g" | sort | uniq > images.file
find images -type f -name "*" | fgrep -v "CVS" | sed "s/images\///g" | sort > images.dir

echo "files only in directory:"
diff images.file images.dir | grep "^>" | sed "s/^> //g"
echo
echo "files only in images.inc:"
diff images.file images.dir | grep "^<" | sed "s/^< //g"

rm images.file images.dir
