#!/bin/bash

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

rm -Rf groupoffice-mailserver

mkdir groupoffice-mailserver

cd groupoffice-mailserver

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-mailserver

mv debian-groupoffice-mailserver groupoffice-mailserver-$VERSION

tar czf groupoffice-mailserver-$VERSION.orig.tar.gz groupoffice-mailserver-$VERSION

cd groupoffice-mailserver-$VERSION

debuild -rfakeroot

#mv ../groupoffice-mailserver_$VERSION-1_all.deb $FULLPATH/
