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

#where are the Group-Office installations?
INSTALL_PATH=/var/www
#remote server where new Group-Office versions are located
HOST=https://group-office.com


USERNAME=$1
PASSWORD=$2

HOSTS=`cat installations`

function quit {
	echo Cleaning up...	
	if [ -r VERSION ]; then
		mv VERSION OLDVERSION
	fi	
	echo Exiting
  exit
}

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

		if [ -e groupoffice-pro-$VERSION.tar.gz ]; then
		
			echo Unpacking archive...
			tar -zxf groupoffice-pro-$VERSION.tar.gz
		else
			echo Fatal error. Downloading of version $VERSION failed!
		fi
	fi
else
	echo Fatal error, Failed getting version information
	quit
fi

#Ok we now have downloaded the new version so let's start the update

for host in $HOSTS
do
         if [  -e "$INSTALL_PATH/$host" ]       # Check if file exists.
          then
                echo Processing installation: $host
                echo Updating Group-Office files...
				cp -R groupoffice-pro-$VERSION/* $INSTALL_PATH/$host/html/groupoffice/
	
				echo Updating database...
				touch $INSTALL_PATH/$host/html/groupoffice/config.php
				chmod 666 $INSTALL_PATH/$host/html/groupoffice/config.php
				lynx -dump http://$host/groupoffice/install/upgrade.php
				chmod 644 $INSTALL_PATH/$host/html/groupoffice/config.php
				rm -Rf $INSTALL_PATH/$host/html/groupoffice/install/
				echo Finnished with $host
				echo ---------------------------------------------------
		 else
		 	echo $host is in installations file but does not exist in $INSTALL_PATH
		 	echo --------------------------------------------------
         fi
done

quit 
