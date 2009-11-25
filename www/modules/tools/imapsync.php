<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: dbcheck.php 3669 2009-11-24 16:39:24Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * @package Tools
 * @subpackage DB check
 */

//otherwise log module will log all items as added.
define('NOLOG', true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");

$local_host = 'localhost';
$remote_host = 'mail.example.com';
$imapsync = '/usr/bin/imapsync';

$db = new db();

$sql = "SELECT DISTINCT username, email, password FROM em_accounts e INNER JOIN em_aliases a ON (a.account_id=e.id AND a.default='1')  WHERE host='$local_host' AND username='HWROME@houtwerf.nl'";
$db->query($sql);

while($account = $db->next_record()){
	echo "Syncing ".$account['username']."\n\n";

	$cmd = $imapsync.' --subscribe  --authmech1 LOGIN --authmech2 LOGIN '.
		'--host1="'.$remote_host.'" --user1="'.$account['email'].'" --password1="'.$account['password'].'" '.
		'--user2="'.$account['username'].'" --host2="'.$local_host.'" --password2="'.$account['password'].'"';

	//echo $cmd."\n\n";

	system($cmd);
}

