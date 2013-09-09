#!/bin/bash
# Author: Merijn Schering info@intermesh.nl

# Directories to backup. Separate with a space. Exclude trailing slash!
SOURCES="$8"
#SOURCES="/home /root/mysql_backup"
# IP or FQDN of Remote Machine
RMACHINE=$1
RPORT=$2
#echo $RMACHINE

# Remote username
RUSER=$3

# Location of passphraseless ssh keyfile
RKEY="$9"
# Directory to backup to on the remote machine. This is where your backup(s) will be stored
# :: NOTICE :: -> Make sure this directory is empty or contains ONLY backups created by
#	                        this script and NOTHING else. Exclude trailing slash!
RTARGET="$4"

# Set the number of backups to keep (greater than 1). Ensure you have adaquate space.
ROTATIONS=$5
QUIET=1

EMAILADDRESS=$6
# Email Subject
EMAILSUBJECT=$7

MYSQL_USER=${10}
MYSQL_PASS=${11}

# First Run variable
FIRST_RUN=${12}

# function for checking exit
function exitBackup {
	cat $LOGFILE | mail -s "$EMAILSUBJECT" "$EMAILADDRESS"
	exit $1
}

# Your EXCLUDE_FILE tells rsync what NOT to backup. Leave it unchanged, missing or
# empty if you want to backup all files in your SOURCES. If performing a
# FULL SYSTEM BACKUP, ie. Your SOURCES is set to "/", you will need to make
# use of EXCLUDE_FILE. The file should contain directories and filenames, one per line.
# An example of a EXCLUDE_FILE would be:
# /proc/
# /tmp/
# /mnt/
# *.SOME_KIND_OF_FILE
#EXCLUDE_FILE="/root/scripts/exclude"

# Comment out the following line to disable verbose output
VERBOSE="-v"

#######################################
########DO_NOT_EDIT_BELOW_THIS_POINT#########
#######################################
mkdir -p /var/log/gobackup
LOGFILE=/var/log/gobackup/`date +"%m%d%Y_%s"`.log
####### Redirect Output to a logfile and screen - Couldnt get tee to work
exec 3>&1                         # create pipe (copy of stdout)
exec 1>$LOGFILE                   # direct stdout to file
exec 2>&1                         # uncomment if you want stderr too
if [ $QUIET -eq 0 ]
  then tail -f $LOGFILE >&3 &     # run tail in bg
fi

echo Backing up MySQL databases
#perl mysql_backup.pl $MYSQL_USER $MYSQL_PASS


OUTPUTDIR="/home/mysql_backup"

mkdir -p $OUTPUTDIR

MYSQLDUMP="/usr/bin/mysqldump"
MYSQL="/usr/bin/mysql"

# clean up any old backups - save space
rm "$OUTPUTDIR/*bak" > /dev/null 2>&1

# get a list of databases
databases=`$MYSQL --user=$MYSQL_USER --password=$MYSQL_PASS \
 -e "SHOW DATABASES;" | tr -d "| " | grep -v Database`

cd $OUTPUTDIR

# dump each database in turn
for db in $databases; do

	if [ "$db" != "information_schema" ]; then
    echo "Dumping $db"
    $MYSQLDUMP --force --opt --user=$MYSQL_USER --password=$MYSQL_PASS \
    --databases $db > "$db.sql"

		echo "Compressing $db"

		tar czf $db.tar.gz $db.sql
		rm $db.sql
	fi
done

echo "Done with MySQL backup"



if [ ! -f $RKEY ]; then
echo "Couldn't find ssh keyfile!"
echo "Exiting..."
exitBackup 2
fi

#check for hostKey
if [ "$FIRST_RUN" == "1" ]; then
ssh -o "StrictHostKeyChecking=no" -i $RKEY $RUSER@$RMACHINE -p $RPORT "test -x $RTARGET"
echo "First Run Done!"
fi

if ! ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "test -x $RTARGET"; then
echo "Target directory on remote machine doesn't exist or bad permissions."
echo "Exiting..."
exitBackup 2
fi

# Set name (date) of backup.
BACKUP_DATE="`date +%F_%H-%M`"

if [ ! $ROTATIONS -gt 1 ]; then
echo "You must set ROTATIONS to a number greater than 1!"
echo "Exiting..."
exitBackup 2
fi

#### BEGIN ROTATION SECTION ####

BACKUP_NUMBER=1
# incrementor used to determine current number of backups

# list all backups in reverse (newest first) order, set name of oldest backup to $backup
# if the retention number has been reached.
for backup in `ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "ls -dXr $RTARGET/*/"`; do
if [ $BACKUP_NUMBER -eq 1 ]; then
NEWEST_BACKUP="$backup"
fi

if [ $BACKUP_NUMBER -eq $ROTATIONS ]; then
OLDEST_BACKUP="$backup"
break
fi

let "BACKUP_NUMBER=$BACKUP_NUMBER+1"
done

# Check if $OLDEST_BACKUP has been found. If so, rotate. If not, create new directory for new backup.
if [ $OLDEST_BACKUP ]; then
	# Set oldest backup to current one
	echo Deleting $OLDEST_BACKUP
	#ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "find $OLDEST_BACKUP -type d -exec chmod +xw {} \;"
	ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "rm -Rf $OLDEST_BACKUP"
fi

ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "mkdir $RTARGET/$BACKUP_DATE"

# Update current backup using hard links from the most recent backup
if [ $NEWEST_BACKUP ]; then
	echo Copying all from $NEWEST_BACKUP to $RTARGET/$BACKUP_DATE
	ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "cp -al $NEWEST_BACKUP. $RTARGET/$BACKUP_DATE"
fi

#### END ROTATION SECTION ####

# Check to see if rotation section created backup destination directory
if ! ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "test -d $RTARGET/$BACKUP_DATE"; then
	echo "Backup destination not available."
	echo "Make sure you have write permission in RTARGET on Remote Machin  e."
	echo "Exiting..."
	exitBackup 2
fi

echo "Verifying Sources..."
for source in $SOURCES; do
	echo "Checking $source..."
	if [ ! -x $source ]; then
		echo "Error with $source!"
		echo "Directory either does not exist, or you do not have proper permissions."
		exitBackup 2
	fi
done

if [ -f $EXCLUDE_FILE ]; then
	EXCLUDE="--exclude-from=$EXCLUDE_FILE"
fi
ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "umask 000";

echo "Sources verified. Running rsync..."
for source in $SOURCES; do

	# Create directories in $RTARGET to mimick source directory hiearchy
	if ! ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "test -d $RTARGET/$BACKUP_DATE/$source"; then
		ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "mkdir -p $RTARGET/$BACKUP_DATE/$source"
	fi
	#-rl was -a
	rsync $VERBOSE $EXCLUDE -a --delete -e "ssh -i $RKEY -p$RPORT" $source/ $RUSER@$RMACHINE:$RTARGET/$BACKUP_DATE/$source/
done

#ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "chmod -R 777 $RTARGET/$BACKUP_DATE"
#ssh -i $RKEY $RUSER@$RMACHINE -p $RPORT "du -sh $RTARGET";

exitBackup 0
