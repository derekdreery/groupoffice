#!/bin/bash

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

if [[ $VERSION =~ ^([0-9]\.[0-9])\.[0-9]$ ]]; then
	MAJORVERSION=${BASH_REMATCH[1]}
fi

echo "Group-Office version: $VERSION"
echo "Major version: $MAJORVERSION"

cd /tmp

rm -Rf groupoffice-com
mkdir groupoffice-com
cd groupoffice-com

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/branches/groupoffice-$MAJORVERSION/debian-groupoffice-com

if [ "$1" == "real" ]; then
	cp -R /var/www/release/packages/groupoffice-com-$VERSION debian-groupoffice-com/usr/share/groupoffice
	mv debian-groupoffice-com/usr/share/groupoffice/LICENSE.TXT debian-groupoffice-com
fi

mv debian-groupoffice-com groupoffice-com-$VERSION

tar --exclude=debian -czf groupoffice-com_$VERSION.orig.tar.gz groupoffice-com-$VERSION

cd groupoffice-com-$VERSION

if [ "$2" == "send" ]; then
	debuild -rfakeroot
	cd ..
	scp *.deb mschering@imfoss.nl:/var/www/groupoffice/repos.groupoffice.eu/groupoffice/pool/main/

	#ssh mschering@imfoss.nl "dpkg-scanpackages /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary /dev/null | gzip -9c > /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary/Packages.gz"
else
	debuild -S -rfakeroot
fi
