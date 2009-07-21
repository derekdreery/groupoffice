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

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-mailserver

mv debian-groupoffice-mailserver groupoffice-mailserver-$VERSION
cd groupoffice-mailserver-$VERSION

dpkg-buildpackage -rfakeroot

mv ../groupoffice-mailserver_$VERSION-1_all.deb $FULLPATH/
