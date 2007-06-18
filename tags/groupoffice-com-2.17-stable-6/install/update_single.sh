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

GROUPOFFICE_PATH=/var/www/html/groupoffice
GROUPOFFICE_URL=http://localhost/groupoffice

HOST=https://www.group-office.com
USERNAME=$1
PASSWORD=$2

function quit {
	echo Cleaning up...	
	if [ -r VERSION ]; then
		mv VERSION OLDVERSION
	fi	
	rm -Rf groupoffice*
	echo Exiting
  exit
}

if [ -r links ]; then
	echo 'Required package elinks is not installed';
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
wget $HOST/releases/VERSION  --http-user=$USERNAME --http-passwd=$PASSWORD

if [ -r VERSION ]; then
	VERSION=`cat VERSION`
	
	echo Remote version: $VERSION
	if [ -r OLDVERSION ]; then
		OLDVERSION=`cat OLDVERSION`
		
		echo Installed version: $OLDVERSION
		if [ "$OLDVERSION" == "$VERSION" ]; then
			echo You are already runnning an updated version			
			quit
		fi
	fi
	
	if [ -e groupoffice-pro-$VERSION.tar.gz ]; then	
		echo Version $VERSION is already downloaded
	else
	
		echo Downloading new version $VERSION
		wget $HOST/releases/groupoffice-pro-$VERSION.tar.gz --http-user=$USERNAME --http-passwd=$PASSWORD

		if [ ! -r groupoffice-pro-$VERSION.tar.gz ]; then
			echo Fatal error. Downloading of version $VERSION failed!
		fi
	fi
	echo Unpacking archive...
	tar -zxf groupoffice-pro-$VERSION.tar.gz

	echo Updating Group-Office files...
	cp -R groupoffice-pro-$VERSION/* $GROUPOFFICE_PATH

	echo Updating database...
	chmod 666 $GROUPOFFICE_PATH/config.php
	links -dump $GROUPOFFICE_URL/install/upgrade.php 
	chmod 644 $GROUPOFFICE_PATH/config.php
	rm -Rf $GROUPOFFICE_PATH/install/
	quit
	
else
	echo Fatal error, Failed getting version information
	quit
fi          
