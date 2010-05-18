#!/bin/bash

#echo "--PREPARING GROUP-OFFICE--"
#mv ../../javascript/tiny_mce ./
#echo "  --Moved tiny_mce"
#find ../../ -name "*-min.js" -exec rm {} \;
#echo "  --Removed *-min.js files"

echo "--GENERATING PHP DOCUMENTATION--"
phpdoc/phpdoc -t ./go_doc/php -ti Group-Office PHP-Documentation -dn GO -ric CHANGELOG, DEVELOPERS, README, RELEASE, TODO, TRANSLATORS, FAQ, LICENSE, Release-1.4.0 -d ../../www/classes -f ../../www/functions.inc.php -i mail/phpmailer/,mail/swift/,smarty/,tcpdf/,mysqlold.class.inc.php -o HTML:Smarty:Group-Office -s on

#echo "--GENERATING JS DOCUMENTATION--"
#cd jsdoc
#java -jar jsrun.jar app/main.js -t=templates/ext ../../../javascript ../../../modules -d=../go_doc/js -r -a
#cd ..

#echo "--RECOVERING FILES--"
#mv tiny_mce ../../javascript
#echo "  --Recovered tiny_mce"
#cd ../../
#svn update
#echo "  --Recovered *-min.js files"

echo "--FINISHED--"
