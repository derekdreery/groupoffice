#!/bin/bash

#dependencies:
#sudo apt-get install gnupg-agent pinentry-curses pbuilder php5-cli php5-curl

eval "$(gpg-agent --daemon)"

svn up
php ./createchangelogs.php
svn commit -m 'Updated changelogs'

#send to repos
./debian-groupoffice-servermanager/builddeb.sh send
./debian-groupoffice-mailserver/builddeb.sh send
./debian-groupoffice-com/builddeb.sh real send


#testing
#./debian-groupoffice-servermanager/builddeb.sh 
#./debian-groupoffice-mailserver/builddeb.sh 
#./debian-groupoffice-pro/builddeb.sh 
#./debian-groupoffice-billing/builddeb.sh 
#./debian-groupoffice-documents/builddeb.sh
#./debian-groupoffice-com/builddeb.sh real 
