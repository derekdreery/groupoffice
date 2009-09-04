#!/bin/bash
cd `dirname "$0"`
cd ../www/modules
svn update sync/ billing/ legalhorizons/ professional/ cms/ projects/ webshop/ gota/ customfields/ dealermanager/ licenses hoursapproval timeregistration mailings 
