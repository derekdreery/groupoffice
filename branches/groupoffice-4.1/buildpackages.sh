#!/bin/bash

eval "$(gpg-agent --daemon)"

svn up
php ./createchangelogs.php
svn commit -m 'Updated changelogs'

#send to repos
./debian-groupoffice-servermanager/builddeb.sh send
./debian-groupoffice-mailserver/builddeb.sh send
./debian-groupoffice-pro/builddeb.sh send
./debian-groupoffice-billing/builddeb.sh send
./debian-groupoffice-documents/builddeb.sh send
./debian-groupoffice-com/builddeb.sh real send


#testing
#./debian-groupoffice-servermanager/builddeb.sh 
#./debian-groupoffice-mailserver/builddeb.sh 
#./debian-groupoffice-pro/builddeb.sh 
#./debian-groupoffice-billing/builddeb.sh 
#./debian-groupoffice-documents/builddeb.sh
#./debian-groupoffice-com/builddeb.sh real 
