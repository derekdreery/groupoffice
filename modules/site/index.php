<?php
/*
 * 1. Create a symlink or alias to $config['file_storage_path'].'public/' in
 *    the document root
 * 
 * 2. Copy this file to the document root * 
 * 
 * 
 * 3. Change $go = dirname(__FILE__).'/../../GO.php'; to point to the correct 
 *    location.
 * 
 * 4. Add this to a .htaccess file or to the VirtualHost file to enable pretty 
 *    URL's:
 * 
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)\?*$ index.php/$1 [L,QSA]

 */


//If the config.php file can't be found add this to the Apache configuration:
//SetEnv GO_CONFIG /etc/groupoffice/config.php

//Or you can use:
//define('GO_CONFIG_FILE', '/path/to/config.php');

$go = dirname(__FILE__).'/../../GO.php';
if(file_exists($go))
	require($go);
elseif(file_exists('/usr/share/groupoffice/GO.php'))
	require('/usr/share/groupoffice/GO.php');
else
	die("Please change the \$go variable to the correct location of GO.php");

require(GO::config()->root_path.'modules/site/components/Site.php');
Site::launch();
?>