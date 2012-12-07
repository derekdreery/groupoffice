#!/bin/bash
VERSION=4.0
cd `dirname "$0"`/www/modules

svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/scanbox
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/sync
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/billing
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/professional
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/projects
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/webshop
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/gota
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/customfields
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/licenses
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/hoursapproval
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/timeregistration
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/documenttemplates
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/savemailas
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/tickets
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/z-push
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/filesearch
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/caldav
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/syncml
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/workflow
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/scanbox
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/carddav
svn co svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/branches/modules-$VERSION/googledrive
