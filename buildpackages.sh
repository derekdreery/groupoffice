#!/bin/bash

eval "$(gpg-agent --daemon)"

php ./createchangelogs.php

svn commit -m 'Updated changelogs'

./debian-groupoffice-servermanager/builddeb.sh send
./debian-groupoffice-mailserver/builddeb.sh send
./debian-groupoffice-pro/builddeb.sh send
./debian-groupoffice-com/builddeb.sh real send
