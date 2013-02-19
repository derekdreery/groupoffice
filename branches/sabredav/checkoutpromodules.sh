#!/bin/bash
REPOROOT=svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/sabredav

cd `dirname "$0"`/www/modules

svn co $REPOROOT/scanbox
svn co $REPOROOT/sync
svn co $REPOROOT/billing
svn co $REPOROOT/professional
svn co $REPOROOT/projects
svn co $REPOROOT/webshop
svn co $REPOROOT/gota
svn co $REPOROOT/customfields
svn co $REPOROOT/licenses
svn co $REPOROOT/hoursapproval
svn co $REPOROOT/timeregistration
svn co $REPOROOT/documenttemplates
svn co $REPOROOT/savemailas
svn co $REPOROOT/tickets
svn co $REPOROOT/z-push
svn co $REPOROOT/filesearch
svn co $REPOROOT/caldav
svn co $REPOROOT/syncml
svn co $REPOROOT/workflow
svn co $REPOROOT/scanbox
svn co $REPOROOT/carddav
svn co $REPOROOT/z-push2
svn co $REPOROOT/googledrive

svn co svn+ssh://mschering@svn.code.sf.net/p/group-office/code/branches/sabredav/debian-groupoffice-servermanager/usr/share/groupoffice/modules/servermanager
