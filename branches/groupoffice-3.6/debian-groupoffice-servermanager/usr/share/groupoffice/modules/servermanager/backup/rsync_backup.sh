#!/bin/sh
# Author: Merijn Schering info@intermesh.nl

# Directories to backup. Separate with a space. Exclude trailing slash!
SOURCES="/etc /home /var/www /root /vmail"
#SOURCES="/home /root/mysql_backup"
# IP or FQDN of Remote Machine
RMACHINE=backup2.imfoss.nl

# Remote username
RUSER=imfoss

# Location of passphraseless ssh keyfile
RKEY=/root/.ssh/id_rsa
# Directory to backup to on the remote machine. This is where your backup(s) will be stored
# :: NOTICE :: -> Make sure this directory is empty or contains ONLY backups created by
#	                        this script and NOTHING else. Exclude trailing slash!
RTARGET="/var/backup/imfoss"

# Set the number of backups to keep (greater than 1). Ensure you have adaquate space.
ROTATIONS=14

QUIET=1

EMAILADDRESS=info@intermesh.nl,info@foss-it.nl
# Email Subject
EMAILSUBJECT="$HOSTNAME Backup"
    

# Your EXCLUDE_FILE tells rsync what NOT to backup. Leave it unchanged, missing or
# empty if you want to backup all files in your SOURCES. If performing a
# FULL SYSTEM BACKUP, ie. Your SOURCES is set to "/", you will need to make
# use of EXCLUDE_FILE. The file should contain directories and filenames, one per line.
# An example of a EXCLUDE_FILE would be:
# /proc/
# /tmp/
# /mnt/
# *.SOME_KIND_OF_FILE
EXCLUDE_FILE="/root/scripts/exclude"

# Comment out the following line to disable verbose output
#VERBOSE="-vv"

#######################################
########DO_NOT_EDIT_BELOW_THIS_POINT#########
#######################################
LOGFILE=/var/log/backup/`date +"%m%d%Y_%s"`.log
####### Redirect Output to a logfile and screen - Couldnt get tee to work
exec 3>&1                         # create pipe (copy of stdout)
exec 1>$LOGFILE                   # direct stdout to file
exec 2>&1                         # uncomment if you want stderr too
if [ $QUIET -eq 0 ]
  then tail -f $LOGFILE >&3 &     # run tail in bg
fi

# backup mysql databases
#mysqldump --all-databases -u root --password=mks14785 > /root/mysql_backup/mysql.sql
/root/scripts/mysql_backup.pl

if [ ! -f $RKEY ]; then
echo "Couldn't find ssh keyfile!"
echo "Exiting..."
exit 2
fi

if ! ssh -i $RKEY $RUSER@$RMACHINE "test -x $RTARGET"; then
echo "Target directory on remote machine doesn't exist or bad permissions."
echo "Exiting..."
exit 2
fi

# Set name (date) of backup.
BACKUP_DATE="`date +%F_%H-%M`"

if [ ! $ROTATIONS -gt 1 ]; then
echo "You must set ROTATIONS to a number greater than 1!"
echo "Exiting..."
exit 2
fi

#### BEGIN ROTATION SECTION ####

BACKUP_NUMBER=1
# incrementor used to determine current number of backups

# list all backups in reverse (newest first) order, set name of oldest backup to $backup
# if the retention number has been reached.
for backup in `ssh -i $RKEY $RUSER@$RMACHINE "ls -dXr $RTARGET/*/"`; do
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
	ssh -i $RKEY $RUSER@$RMACHINE "find $OLDEST_BACKUP -type d -exec chmod +xw {} \;"
	ssh -i $RKEY $RUSER@$RMACHINE "rm -Rf $OLDEST_BACKUP"
fi

ssh -i $RKEY $RUSER@$RMACHINE "mkdir $RTARGET/$BACKUP_DATE"

# Update current backup using hard links from the most recent backup
if [ $NEWEST_BACKUP ]; then
	echo Copying all from $NEWEST_BACKUP to $RTARGET/$BACKUP_DATE
	ssh -i $RKEY $RUSER@$RMACHINE "cp -al $NEWEST_BACKUP. $RTARGET/$BACKUP_DATE"
fi

#### END ROTATION SECTION ####

# Check to see if rotation section created backup destination directory
if ! ssh -i $RKEY $RUSER@$RMACHINE "test -d $RTARGET/$BACKUP_DATE"; then
	echo "Backup destination not available."
	echo "Make sure you have write permission in RTARGET on Remote Machin  e."
	echo "Exiting..."
	exit 2
fi

echo "Verifying Sources..."
for source in $SOURCES; do
	echo "Checking $source..."
	if [ ! -x $source ]; then
		echo "Error with $source!"
		echo "Directory either does not exist, or you do not have proper permissions."
		exit 2
	fi
done

if [ -f $EXCLUDE_FILE ]; then
	EXCLUDE="--exclude-from=$EXCLUDE_FILE"
fi
ssh -i $RKEY $RUSER@$RMACHINE "umask 000";

echo "Sources verified. Running rsync..."
for source in $SOURCES; do

	# Create directories in $RTARGET to mimick source directory hiearchy
	if ! ssh -i $RKEY $RUSER@$RMACHINE "test -d $RTARGET/$BACKUP_DATE/$source"; then
		ssh -i $RKEY $RUSER@$RMACHINE "mkdir -p $RTARGET/$BACKUP_DATE/$source"
	fi
	#-rl was -a
	rsync $VERBOSE $EXCLUDE -a --delete -e "ssh -i $RKEY" $source/ $RUSER@$RMACHINE:$RTARGET/$BACKUP_DATE/$source/
done

#ssh -i $RKEY $RUSER@$RMACHINE "chmod -R 777 $RTARGET/$BACKUP_DATE"
#ssh -i $RKEY $RUSER@$RMACHINE "du -sh $RTARGET";

cat $LOGFILE | mail -s "$EMAILSUBJECT" "$EMAILADDRESS"

exit 0
