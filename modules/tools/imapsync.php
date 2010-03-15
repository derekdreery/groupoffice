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
$local_host='localhost';
$remote_host = 'mail.houtwerf.nl';
//$imapsync = '/usr/share/groupoffice/modules/tools/imapsync-1.286/imapsync';
$imapsync = '/usr/bin/imapsync';


$fp = fopen('/root/remotepasswords2.csv', "r");
if(!$fp)
{
	die('COuld not read remotepasswords.csv');
}

while($record = fgetcsv($fp, 4096, ',', '"'))
{
//	if($record[0]=='g.dekker@houtwerf.nl'){
	echo "Syncing ".$record[0]."\n\n";

$cmd = $imapsync.' --skipsize --syncinternaldates --fast --useheader Message-Id --authmech1 LOGIN --authmech2 LOGIN '.
			'--host1="'.$remote_host.'" --user1="'.$record[0].'" --password1="'.$record[1].'" '.
			'--user2="'.$record[2].'" --host2="'.$local_host.'" --password2="'.$record[3].'"';

	echo $cmd."\n\n";

	system($cmd);
//	}
}

