#!/bin/bash

# useful: DEBCONF_DEBUG="developer"

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

VERSION=`cat ../www/classes/base/config.class.inc.php | grep '$version' | sed -e 's/[^0-9\.]*//g'`

echo "Group-Office version: $VERSION"

cd /tmp

rm -Rf groupoffice-servermanager

mkdir groupoffice-servermanager

cd groupoffice-servermanager

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/branches/groupoffice-$VERSION/debian-groupoffice-servermanager

mv debian-groupoffice-servermanager groupoffice-servermanager-$VERSION

#tar czf groupoffice-servermanager_$VERSION.orig.tar.gz groupoffice-mailserver-$VERSION

cd groupoffice-servermanager-$VERSION


if [ "$1" == "send" ]; then
	debuild -rfakeroot
	cd ..
	scp *.deb mschering@imfoss.nl:/var/www/groupoffice/repos.groupoffice.eu/groupoffice/pool/main/

	#ssh mschering@imfoss.nl "dpkg-scanpackages /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary /dev/null | gzip -9c > /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary/Packages.gz"
else
	debuild -S -rfakeroot
fi
