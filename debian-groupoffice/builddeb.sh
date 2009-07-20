#!/bin/bash

VERSION=3.2.14

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

rm -Rf godebs

mkdir godebs

cd godebs


#svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-mailserver

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice

if [ $1 == "real" ]; then
	svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/www
	mv www debian-groupoffice/usr/share/groupoffice
fi

mv debian-groupoffice groupoffice-$VERSION
cd groupoffice-$VERSION

dpkg-buildpackage -rfakeroot

mv ../groupoffice-com_$VERSION-1_all.deb $FULLPATH/groupoffice.deb
