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
 */

/**
 * Usage : php export_domain.php --domain=example.com"
 */
chdir(dirname(__FILE__));
require('/usr/share/groupoffice/cli-functions.inc.php');

$args = parse_cli_args($argv);

$vmail = is_dir('/vmail') ? '/vmail' : '/home/vmail';

if(!isset($args['go_root']))
	$args['go_root']='/usr/share/groupoffice';

if(isset($args['go_config']))
	define('CONFIG_FILE', $args['go_config']);

require($args['go_root'].'/Group-Office.php');


echo 'Using config: '.$GO_CONFIG->get_config_file()."\n\n";

require_once($GO_MODULES->modules['postfixadmin']['class_path'].'postfixadmin.class.inc.php');
$pa = new postfixadmin();



echo "Importing domain...\n\n";

$sql_file = $vmail.'/'.$args['domain'].'/export.sql';

$sql = file_get_contents($sql_file);

$queries = String::get_sql_queries($sql_file);
//var_dump($queries);

foreach($queries as $query){
	//echo "QUERY: $query\n";
	$pa->query($query);
}

$db = new db();
$pa->query("SELECT id FROM pa_domains WHERE acl_id=0");
while($r = $pa->next_record()){
	$r['acl_id']=$GO_SECURITY->get_new_acl();
	$db->update_row('pa_domains', 'id', $r);
}

echo "Setting file permissions...\n\n";
system('chown -R vmail:mail '.$vmail.'/'.$args['domain']);

echo "Done!\n\n";