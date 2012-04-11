#!/bin/bash
php ./createchangelogs.php

svn commit -m 'Updated changelogs'

./debian-groupoffice-servermanager/builddeb.sh send
./debian-groupoffice-mailserver/builddeb.sh send
./debian-groupoffice-pro/builddeb.sh send
./debian-groupoffice-com/builddeb.sh real send

#./debian-groupoffice-servermanager/builddeb.sh 
#./debian-groupoffice-mailserver/builddeb.sh 
#./debian-groupoffice-pro/builddeb.sh 
#./debian-groupoffice-com/builddeb.sh real 
