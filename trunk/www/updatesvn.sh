#!/bin/bash
cd `dirname "$0"`
cd modules
svn update sync/ billing/ professional/ projects/ webshop/ gota/ customfields/ licenses hoursapproval timeregistration documenttemplates savemailas tickets z-push filesearch caldav syncml
cd ..
svn update