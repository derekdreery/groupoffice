<?php

require('../../GO.php');

require('./components/GOS.php');

GOS::launch()->run();

/**
 * EXAMPLE MOD_REWRITE RULE FOR A SITE


<VirtualHost *:80>
ServerName group-office.com
ServerAlias testshop.group-office.com
DocumentRoot /var/www/testshop.group-office.com/html
RewriteLog "/var/log/apache2/rewrite.log"
RewriteLogLevel 3
<Directory /var/www/testshop.group-office.com/html>
RewriteEngine On
RewriteBase /

RewriteRule ^index.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*) /groupoffice/modules/sites/index.php?site_id=1&path=$1&%{QUERY_STRING} [L]

</Directory>
</VirtualHost>


*/