#!/bin/bash

PROMODULES="sync customfields gota caldav documenttemplates savemailas projects professional timeregistration hoursapproval billing tickets";

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

if [[ $VERSION =~ ^([0-9]\.[0-9])\.[0-9]{1,2}$ ]]; then
	MAJORVERSION=${BASH_REMATCH[1]}
fi

echo "Group-Office version: $VERSION"
echo "Major version: $MAJORVERSION"

if [ ! -e /root/packages/groupoffice-pro-$VERSION ]; then
	echo /root/release/packages/groupoffice-pro-$VERSION bestaat niet. eerst createtag.sh draaien.
	exit
fi

cd /tmp

rm -Rf groupoffice-pro

mkdir groupoffice-pro

cd groupoffice-pro

svn export https://mschering@svn.code.sf.net/p/group-office/code/branches/groupoffice-$MAJORVERSION/debian-groupoffice-pro

mv debian-groupoffice-pro groupoffice-pro-$VERSION

for m in $PROMODULES; do
	cp -R /root/packages/groupoffice-pro-$VERSION/modules/$m groupoffice-pro-$VERSION/usr/share/groupoffice/modules/
done

cp -R /root/packages/billing-$VERSION/billing groupoffice-pro-$VERSION/usr/share/groupoffice/modules

cd groupoffice-pro-$VERSION

if [ "$1" == "send" ]; then
	debuild -rfakeroot
	cd ..
	scp *.deb mschering@imfoss.nl:/var/www/groupoffice/repos.groupoffice.eu/groupoffice/poolthreeseven/main/

	#ssh mschering@imfoss.nl "dpkg-scanpackages /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary /dev/null | gzip -9c > /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary/Packages.gz"
else
	debuild -S -rfakeroot
fi
