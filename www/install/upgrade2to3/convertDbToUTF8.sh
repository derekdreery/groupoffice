#!/bin/bash
echo Script to convert MySQL latin1 charsets to utf8.
echo Usage: $0 dbname mysqloptions

echo Dumping out $1 database
mysqldump --default-character-set=latin1 --add-drop-table $2 $1 > db.sql

mydate=`date +%y%m%d`
echo Making a backup
mkdir bak &> /dev/null
cp db.sql bak/$1-$mydate.sql

echo String replacing latin1 with utf8
#cat db.sql | replace CHARSET=latin1 CHARSET=utf8 > db2.sql
cat db.sql | sed 's/CHARSET=latin1/CHARSET=utf8/g' > db2.sql

echo String replace COLLATE
#cat db2.sql | replace COLLATE=latin1_german1_ci "" > db.sql
sed -e 's/\(ENGINE=.*\) COLLATE=[a-z1-9_]*;/\1;/g' db2.sql > db.sql

iconv -c -f utf8 -t iso-8859-15 db.sql > utf8.sql

echo Pumping back $1 into database
mysql --default-character-set=utf8 $1 $2 < utf8.sql

echo Changing db charset to utf8
mysql $1 $2 -e "alter database $1 charset=utf8;"

echo $1 Done!

#ALTER TABLE tbl_name CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
