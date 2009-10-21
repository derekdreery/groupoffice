#!/bin/bash

PROMODULES="sync customfields gota mailings projects professional timeregistration";

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

if [ ! -e /var/www/release/groupoffice-pro-$VERSION ]; then
	echo /var/www/release/groupoffice-pro-$VERSION bestaat niet. eerst createtag.sh draaien.
	exit
fi

cd /tmp

rm -Rf groupoffice-pro

mkdir groupoffice-pro

cd groupoffice-pro

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice-pro

mv debian-groupoffice-pro groupoffice-pro-$VERSION

for m in $PROMODULES; do
	cp -R /var/www/release/groupoffice-pro-$VERSION/modules/$m groupoffice-pro-$VERSION/usr/share/groupoffice/modules/
done

cd groupoffice-pro-$VERSION

if [ "$1" == "send" ]; then
	debuild -rfakeroot
	cd ..
	scp *.deb mschering@imfoss.nl:/var/www/groupoffice/repos.groupoffice.eu/groupoffice/pool/main/

	#ssh mschering@imfoss.nl "dpkg-scanpackages /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary /dev/null | gzip -9c > /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary/Packages.gz"
else
	debuild -S -rfakeroot
fi
