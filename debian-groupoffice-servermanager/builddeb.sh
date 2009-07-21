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

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-servermanager

mv debian-groupoffice-servermanager groupoffice-servermanager-$VERSION
cd groupoffice-servermanager-$VERSION

dpkg-buildpackage -rfakeroot

mv ../groupoffice-servermanager_$VERSION-1_all.deb $FULLPATH/
