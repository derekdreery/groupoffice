#!/bin/bash
cd `dirname "$0"`
cd ../www/modules

svn update sync/ billing/ legalhorizons/ professional/ projects/ webshop/ gota/ customfields/ dealermanager/ licenses hoursapproval timeregistration mailings tickets z-push filesearch caldav
