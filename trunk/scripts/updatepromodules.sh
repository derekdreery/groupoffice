#!/bin/bash
cd `dirname "$0"`
cd ../
svn update

cd www/modules

svn update sync/ billing/ legalhorizons/ professional/ projects/ webshop/ gota/ customfields/ dealermanager/ licenses hoursapproval timeregistration documenttemplates savemailas tickets z-push filesearch caldav syncml workflow
