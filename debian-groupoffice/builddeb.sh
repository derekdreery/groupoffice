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

rm -Rf groupoffice-com

mkdir groupoffice-com

cd groupoffice-com

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice

if [ "$1" == "real" ]; then
	#svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/tags/groupoffice-com-$VERSION
	#mv groupoffice-com-$VERSION debian-groupoffice/usr/share/groupoffice

	svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/www
	mv www debian-groupoffice/usr/share/groupoffice

	rm debian-groupoffice/usr/share/groupoffice/LICENSE.TXT
fi

mv debian-groupoffice groupoffice-com-$VERSION

tar --exclude=debian czf groupoffice-com_$VERSION.orig.tar.gz groupoffice-com-$VERSION

cd groupoffice-com-$VERSION

debuild -S -rfakeroot
#debuild -rfakeroot
