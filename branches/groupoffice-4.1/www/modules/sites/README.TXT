QUICK AND DIRTY DEPLOYMENT

1. chmod 777 modules/sites/assets
2. Create a site in the module. Just fill in the name and leave the defaults.
3. Right click the site and click view.


DEPLOY WITH NICE URLS:

1. Create apache virtual host for example.com
2. Copy modules/sites/index.php in document root of example.com's document root. eg. /var/www/example.com/html/index.php.
3. Edit the new index.php file and set the $path_to_gos variable to your path.
4. Create a symlink to the template assets folder. For example if you have "Example" for the site's template property it should be: 

cd /var/www/example.com/html
ln -s /usr/share/groupoffice/modules/sites/Example/assets Example

5. If you have multiple Group-Office instances or for whatever reason the site is not able to find the config.php file. You can create a symlink to the real config file in:

/etc/groupoffice/example.com/config.php

6. Now the site should be functional but the URL's aren't pretty. You can enable mod rewrite in the Sites GUI of Group-Office. Right click on the site to edit the properties.
Mod rewrite must be configured for apache too. Below is an example virtual host file:

<VirtualHost *:80>
	ServerName example.com
	ServerAlias www.example.com
	DocumentRoot /var/www/example.com/html

	<Directory /var/www/example.com/html>
		RewriteEngine On
		RewriteBase /

		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^(.*)\?*$ index.php/$1 [L,QSA]

	</Directory>
</VirtualHost>