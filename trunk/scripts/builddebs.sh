#!/bin/sh

cd /tmp

rm -Rf godebs

mkdir godebs

cd godebs

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice
svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-mailserver

dpkg --build debian-groupoffice
dpkg --build debian-groupoffice-mailserver

mv debian-groupoffice.deb /var/www/trunk/scripts/groupoffice.deb
mv debian-groupoffice-mailserver.deb /var/www/trunk/scripts/groupoffice-mailserver.deb
