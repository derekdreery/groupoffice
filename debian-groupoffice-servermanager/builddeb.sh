#!/bin/bash

# useful: DEBCONF_DEBUG="developer"

VERSION=3.2.15

PRG="$0"
OLDPWD=`pwd`
P=`dirname $PRG`
cd $P
if [ `pwd` != "/" ]
then
FULLPATH=`pwd`
else
FULLPATH=''
fi

cd /tmp

rm -Rf groupoffice-servermanager

mkdir groupoffice-servermanager

cd groupoffice-servermanager

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-servermanager

mv debian-groupoffice-servermanager groupoffice-servermanager-$VERSION

#tar czf groupoffice-servermanager_$VERSION.orig.tar.gz groupoffice-mailserver-$VERSION

cd groupoffice-servermanager-$VERSION

debuild -S -rfakeroot
debuild -rfakeroot

#mv ../groupoffice-servermanager_$VERSION-1_all.deb $FULLPATH/
