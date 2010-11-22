<?php
require('www/Group-Office.php');

$tpl = '{package} ({version}) three; urgency=low

  * Changes can be found in /usr/share/groupoffice/CHANGELOG.TXT

 -- Merijn Schering <mschering@intermesh.nl>  {date}';

//Mon, 26 May 2010 12:30:00 +0200
$date = date('D, j M Y H:i:s O');

$packages = array('groupoffice-com', 'groupoffice-pro','groupoffice-mailserver', 'groupoffice-servermanager');

foreach($packages as $package){
	file_put_contents('debian-'.$package.'/debian/changelog', str_replace(
					array('{package}', '{version}', '{date}'),
					array($package, $GO_CONFIG->version, $date),
					$tpl
					));
}
?>
