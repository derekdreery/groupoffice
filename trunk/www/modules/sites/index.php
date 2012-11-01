<?php
//Copy this file to the root of your website
$path_to_gos = '/var/www/trunk/www/modules/sites/components/GOS.php';
require($path_to_gos);
GOS::launch()->run();

/**
 * EXAMPLE MOD_REWRITE RULE FOR A SITE

<VirtualHost *:80>
	ServerName group-office.com
	ServerAlias website.group-office.com
	DocumentRoot /var/www/website.group-office.com/html

	<Directory /var/www/website.group-office.com/html>
		RewriteEngine On
		RewriteBase /

		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^(.*)\?*$ index.php/$1 [L,QSA]

	</Directory>
</VirtualHost>

*/
