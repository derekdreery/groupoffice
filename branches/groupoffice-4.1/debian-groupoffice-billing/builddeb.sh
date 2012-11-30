#!/bin/bash

PROMODULES="billing";

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

VERSION=`cat ../www/go/base/Config.php | grep '$version' | sed -e 's/[^0-9\.]*//g'`

if [[ $VERSION =~ ^([0-9]\.[0-9])\.[0-9]{1,3}$ ]]; then
	MAJORVERSION=${BASH_REMATCH[1]}
fi

echo "Group-Office version: $VERSION"
echo "Major version: $MAJORVERSION"

if [ ! -e /root/packages/billing-$VERSION ]; then
	echo /root/packages/billing-$VERSION bestaat niet. eerst createtag.sh draaien.
	exit
fi

cd /tmp

rm -Rf groupoffice-billing

mkdir groupoffice-billing

cd groupoffice-billing

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/branches/groupoffice-$MAJORVERSION/debian-groupoffice-billing

mv debian-groupoffice-billing groupoffice-billing-$VERSION

for m in $PROMODULES; do
	cp -R /root/packages/billing-$VERSION/$m groupoffice-billing-$VERSION/usr/share/groupoffice/modules/
done


cd groupoffice-billing-$VERSION

if [ "$1" == "send" ]; then
	debuild -rfakeroot
	cd ..
	scp *.deb mschering@imfoss.nl:/var/www/groupoffice/repos.groupoffice.eu/groupoffice/poolfourone/main/

	#ssh mschering@imfoss.nl "dpkg-scanpackages /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary /dev/null | gzip -9c > /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary/Packages.gz"
else
	debuild -rfakeroot
	cd ..
	mv *.deb ../
fi
