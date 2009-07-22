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

rm -Rf groupoffice-com

mkdir groupoffice-com

cd groupoffice-com

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice

if [ $1 == "real" ]; then
	svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/tags/groupoffice-com-$VERSION
	mv groupoffice-com-$VERSION debian-groupoffice/usr/share/groupoffice
fi

mv debian-groupoffice groupoffice-com-$VERSION
cd groupoffice-com-$VERSION

debuild -rfakeroot

#mv ../groupoffice-com_$VERSION-1_all.deb $FULLPATH/
