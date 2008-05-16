#!/bin/sh
#
# Copyright Intermesh 2004
# Author: Merijn Schering <mschering@intermesh.nl>
# Version: 1.0 Release date: 08 July 2003
# Version: 1.1 Release date: 09 April 2004
#
# This program is free software; you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the
# Free Software Foundation; either version 2 of the License, or (at your
# option) any later version.

#IP address for virtual host
IP_ADDRESS=*
APACHE_CONF=/etc/apache/httpd.conf
#where are the Group-Office installations?
INSTALL_PATH=/var/www
#remote server where new Group-Office versions are located
HOST=https://www.group-office.com
USERNAME=$1
PASSWORD=$2
NAME=$3
APACHE_USER=www-data
CONFIG_FILE=/etc/Group-Office/$NAME/groupoffice/config.php
#CONFIG_FILE=/var/www/$NAME/html/groupoffice/config.php


function quit {
	echo Cleaning up...	
	if [ -r VERSION ]; then
		mv VERSION OLDVERSION
	fi	
	echo Exiting
  exit
}

if [ "$NAME" == "" ]; then
        echo Please enter a name
        quit
fi

if [ -r linx ]; then
	echo 'Required package lynx is not installed';
	quit
fi

if [ -r wget ]; then
	echo 'Required package wget is not installed';
	quit
fi

if [ -r tar ]; then
	echo 'Required package tar is not installed';
	quit
fi


if [ -r VERSION ]; then
	rm VERSION
fi

echo Getting version information...
if [ -r VERSION ]; then
	rm VERSION
fi
wget $HOST/releases/VERSION  --http-user=$USERNAME --http-passwd=$PASSWORD

if [ -e VERSION ]; then
	VERSION=`cat VERSION`
	
	echo Remote version: $VERSION
	if [ -e OLDVERSION ]; then
		OLDVERSION=`cat OLDVERSION`
		
		#echo Installed version: $OLDVERSION
		#if [ "$OLDVERSION" == "$VERSION" ]; then
		#	echo You are already runnning an updated version			
		#	quit
		#fi
	fi
	if [ -e groupoffice-pro-$VERSION.tar.gz ]; then	
		echo Version $VERSION is already downloaded
	else
		echo Downloading version $VERSION
		wget $HOST/releases/groupoffice-pro-$VERSION.tar.gz --http-user=$USERNAME --http-passwd=$PASSWORD

		if [ ! -e groupoffice-pro-$VERSION.tar.gz ]; then
			echo Fatal error. Downloading of version $VERSION failed!
		fi
	fi
else
	echo Fatal error, Failed getting version information
	quit
fi

echo Unpacking archive...
tar -zxf groupoffice-pro-$VERSION.tar.gz

#Create directory structure
mkdir $INSTALL_PATH/$NAME
mkdir $INSTALL_PATH/$NAME/data
mkdir $INSTALL_PATH/$NAME/logs
mkdir $INSTALL_PATH/$NAME/html

chown $APACHE_USER:$APACHE_USER $INSTALL_PATH/$NAME/data

#Add installation
echo Registering installation $NAME
echo "$NAME" >> installations

#Add virtual host
echo Adding virtual host to $APACHE_CONF
cp $APACHE_CONF $APACHE_CONF.GOB
echo "<VirtualHost $IP_ADDRESS>" >> $APACHE_CONF
echo "DocumentRoot $INSTALL_PATH/$NAME/html" >> $APACHE_CONF
echo "ServerName $NAME" >> $APACHE_CONF
echo "ServerAlias www.$NAME" >> $APACHE_CONF
echo "ErrorLog /var/www/$NAME/logs/$NAME-error.log" >> $APACHE_CONF
echo "CustomLog /var/www/$NAME/logs/$NAME-access.log common" >> $APACHE_CONF
echo "</VirtualHost>" >> $APACHE_CONF

/etc/init.d/apache restart

echo Copying Group-Office files...
mv groupoffice-pro-$VERSION $INSTALL_PATH/$NAME/html/groupoffice

echo Running install script http://www.$NAME/groupoffice/install.ph

touch $CONFIG_FILE
chmod 777 $CONFIG_FILE
#todo make a lynx compatible install.php
#lynx http://www.$NAME/groupoffice/install/install.php
chmod 755 $CONFIG_FILE
quit
