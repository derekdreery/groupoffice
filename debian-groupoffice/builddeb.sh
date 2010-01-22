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

echo "Group-Office version: $VERSION"

cd /tmp

rm -Rf groupoffice-com

mkdir groupoffice-com

cd groupoffice-com

svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/debian-groupoffice

if [ "$1" == "real" ]; then
	#svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/tags/groupoffice-com-$VERSION
	#mv groupoffice-com-$VERSION debian-groupoffice/usr/share/groupoffice

	#svn export https://mschering@group-office.svn.sourceforge.net/svnroot/group-office/trunk/www
	#mv www debian-groupoffice/usr/share/groupoffice

	cp -R /var/www/release/packages/groupoffice-com-$VERSION debian-groupoffice/usr/share/groupoffice
	mv debian-groupoffice/usr/share/groupoffice/LICENSE.TXT debian-groupoffice
fi

mv debian-groupoffice groupoffice-com-$VERSION

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
