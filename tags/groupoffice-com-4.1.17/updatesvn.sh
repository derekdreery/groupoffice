#!/bin/bash
cd `dirname "$0"`
cd modules
svn update scanbox/ sync/ billing/ professional/ projects/ webshop/ gota/ customfields/ licenses hoursapproval timeregistration documenttemplates savemailas tickets z-push filesearch caldav syncml workflow scanbox carddav
cd ..
svn update
