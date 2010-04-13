<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * @package Tools
 * @subpackage DB check
 */

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");

$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
//$GO_SECURITY->html_authenticate('tools');

ini_set('max_execution_time', 360);

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$fs = new files();

echo "Crawling all user files and store them in the database".$line_break.$line_break;
$fs->crawl($GO_CONFIG->file_storage_path.'users');

echo $line_break.$line_break.'Done!'.$line_break;
?>