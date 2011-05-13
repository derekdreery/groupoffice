#!/usr/bin/perl

# $Id: mysql-backup.pl,v 1.1.1.1 2002/12/31 17:33:45 smerkel Exp $

# Author:       Steve Merkel
# Email:        s...@venturesonline.com
# Date:         12.29.2002

# Notes:  This is a new implementation of the mysql_backup script.
# This version uses mysqlhotcopy and works with all control panels
# and no control panels.  It also doesn't add the full path to the
# compressed database.  Since it doesn't use mysqldump, the
# resulting backup simply needs to be copied into /var/lib/mysql
# under whatever name you want to give the database and restart
# mysql.  No import of data is needed.

# There are several global variables that need to be set below.

# TODO:
#
#  (12.29.2002)
#
#  - Add the ability to define everything via a command-line
#    option.
#
#  - Make it work with Ensim.  (Test with ensim anyway.)

use strict;

# Configuration Data
# ---------------------------------------------------------------

# Location where the live databases live.
my $LIVEDBDIR = '/var/lib/mysql';

# Directory where to store the backup data. This directory
# and the above directory can't be the same.
my $DATADIR = '/home/mysqlbackup/';
# This defines which control panel is installed on the server.
# Current choices are:  cpanel,psa,none
my $CP = 'none';

# Admin password.  Set these if the control panel isn't a supported
# control panel and you need a username and password to connect to the
# database.
my $sqlpass = '';
my $sqluser = '';
if(@ARGV>0) {
  $sqluser = $ARGV[0];
  $sqlpass = $ARGV[1];
}else
{
  $sqluser = 'root';
  $sqlpass = '';
}

# Control Panel Specific Values
# ---------------------------------------------------------------
# The location of the PSA shadow file.
my $psa_shadow = '';

# Subroutines
# ---------------------------------------------------------------
# Function: getPSAPass
# This function grabs the admin password out of the PSA shadow file.
sub getPSAPass {

  # Open the shadow file
  open(PSASHADOW,$psa_shadow) || die "Can't open $psa_shadow\n";
  # Get the PW.
  my @psapw=<PSASHADOW>;
  # Close the file.
  close(PSASHADOW);
  # Return the password.
  return $psapw[0];

}

# Function genDBList
# This function uses the mysql client to generate a file with a list of
# databases.  Cheap hack.  No depend.
sub genDBList {
# Get the user and password passed into the function.
# Get the user and password passed into the function.
  my $sqluser = shift;
  my $sqlpass = shift;

  # This array will contain the list of mysql databases.
  my (@dblist);

  # Bust out some mad system calls to get the db names.
  if (($CP =~ /psa/) or (($sqluser ne '') and ($sqlpass ne ''))){
    @dblist = `/usr/bin/mysql --user=$sqluser --password=$sqlpass -e "show databases;"`;
  }
  else {
    @dblist = `/usr/bin/mysql -e "show databases;"`;
  }
  # Return our list.
  return(@dblist);

}

# Function: copyDB
# This function will use the mysqlhotcopy command to copy the databases
# listed in @dblist to $DATADIR.
sub copyDB {

  # Get the user,password and database name info.
  my ($sqluser,$sqlpass,@dblist) = @_;

  # Check to make sure $DATADIR is defined.
  if ($DATADIR eq '') {
    die "You must define \$DATADIR\n";
  }

  # Check to see if the datadir exists, and create it if not.
  if (! -d $DATADIR) {
    mkdir($DATADIR) || die "Can't create " . $DATADIR . ".  Error: " . $!.. "\n";
  }

  # Just in case someone desides to put the location of the existing databases in.
  # Assumes default location.
  if ($DATADIR eq $LIVEDBDIR) {
    die "You want to overwrite your databases?\n";

}

  # Run mysqlhotcopy against all databases.
  foreach my $line (@dblist) {
    # Drop the eol char.
    $line =~ /^(.*)$/;
    my $db = $1;

    # Make sure the db is there.  Won't die if it isn't, it just looks
    # like I'm not error checking if I omit this.
    if ( ! -d $LIVEDBDIR . '/' . $db ){

      # Check to see if the database name is 'Database'.  This is crapthat mysql show databases gives
      # and shouldn't exist.   However, someone could have a database named "Database" so we want
      # to provide the information.
      if ($db =~ /^Database$/) {
        print "Skipping \'$db\'.  Doesn't seem to exist.  This is probably normal in this case.\n";
      }
      else {
        print "Skipping \'$db\'.  Doesn't seem to exist.\n";
      }
      next;
    }

    print "Copying $db...\n";
    # If we got user and password data.  Use it.
    if (($sqluser ne '') and ($sqlpass ne '')){
      # System call to copy the database.  Overwrite existing directories.
      system('/usr/bin/mysqlhotcopy --allowold --user=' . $sqluser . ' --password=' . $sqlpass . ' ' . $db . ' ' . $DATADIR . ' > /dev/null 2>&1');
    }
    else {
      # System call to copy the database.  Overwrite existing directories.
      system('/usr/bin/mysqlhotcopy --allowold ' . $db . ' ' . $DATADIR .' > /dev/null 2>&1');
    }
    compressDB($db);
  }
}

# Function:  compressDB
# This function will tgz all of the copied database directories.
sub compressDB {

  # Grab the dblist.
  my @dblist = @_;

  # Hump the db list.
  foreach my $line (@dblist){
    # Strip the EOL char.
    $line =~ /^(.*)$/;
    my $db = $1;
    # If the $DATADIR directory doesn't exist, create it.
    if ( -d $DATADIR . '/' . $db) {
      print "Compressing $db...\n";
      # System calls to create the the tarballs and delete the dirs after doing so.
      system('cd ' . $DATADIR . ';/bin/tar cvzf ' . $db . '.tgz ' . $db .' > /dev/null 2>&1');
      print "Cleaning up after $db...\n";
      system('rm -rf ' . $DATADIR . '/' . $db . ' > /dev/null 2>&1');
    }
  }

}

#
# Main
# -------------------------------------------------------------------

# Global Variable
my @dblist;

# If this is a psa install, get the admin password and generate the DB list.
# Otherwise just generate the DB list using the variables above.
if ($CP =~ /psa/) {
  $sqlpass = getPSAPass;
  @dblist = genDBList('admin',$sqlpass);
  copyDB('admin',$sqlpass,@dblist);
}else {
  @dblist = genDBList($sqluser,$sqlpass);
  copyDB($sqluser,$sqlpass,@dblist);

}

# Exit -- Ah DUH!
exit 0; 
