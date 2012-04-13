<?php
require('../../GO.php');

$site_id=isset($_REQUEST['site_id']) ? $_REQUEST['site_id'] : '1';

$site = GO_Sites_Model_Site::model()->findByPk($site_id);

$path = isset($_REQUEST['path']) ? $_REQUEST['path'] : '';

$page = GO_Sites_Model_Page::model()->findSingleByAttributes(array('site_id'=>$site_id, 'path'=>$path));

if(!$page){
	header("HTTP/1.0 404 Not Found");
  header("Status: 404 Not Found");
	echo '<h1>Page not found</h1>';
	echo '<a href="/">Visit '.$_SERVER['SERVER_NAME'].'</a>';
	exit();
}

$controller = new $page->controller($site, $page);
$controller->run($page->controller_action, $_REQUEST);

//$action = 'action'.$page->controller_action;
//
//$controller->$action($_REQUEST);

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