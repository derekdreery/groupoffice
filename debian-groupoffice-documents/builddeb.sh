#!/bin/bash

PROMODULES="workflow filesearch";

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

if [ ! -e /root/packages/documents-$VERSION ]; then
	echo /root/packages/documents-$VERSION bestaat niet. eerst createtag.sh draaien.
	exit
fi

cd /tmp

rm -Rf groupoffice-documents

mkdir groupoffice-documents

cd groupoffice-documents

svn export https://mschering@svn.code.sf.net/p/group-office/code/branches/groupoffice-$MAJORVERSION/debian-groupoffice-documents

mv debian-groupoffice-documents groupoffice-documents-$VERSION

for m in $PROMODULES; do
	cp -R /root/packages/documents-$VERSION/$m groupoffice-documents-$VERSION/usr/share/groupoffice/modules/
done


cd groupoffice-documents-$VERSION

if [ "$1" == "send" ]; then
	debuild -rfakeroot
	cd ..
	scp *.deb mschering@imfoss.nl:/var/www/groupoffice/repos.groupoffice.eu/groupoffice/poolfiveone/main/

	#ssh mschering@imfoss.nl "dpkg-scanpackages /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary /dev/null | gzip -9c > /var/www/groupoffice/repos.groupoffice.eu/groupoffice/binary/Packages.gz"
else
	debuild -rfakeroot
	cd ..
	mv *.deb ../
fi
