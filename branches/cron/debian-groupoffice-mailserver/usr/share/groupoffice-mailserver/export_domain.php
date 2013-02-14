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
 * Usage : php export_domain.php --domain=example.com --targethost="newmailserver.com"
 */
chdir(dirname(__FILE__));
require('/usr/share/groupoffice/cli-functions.inc.php');

$args = parse_cli_args($argv);

if(empty($args['domain'])){
	
	echo 'Usage : php export_domain.php --domain=example.com --targethost="newmailserver.com
		
	Optional: 
	--go_config=/etc/groupoffice/config.php
	--go_root=/usr/share/groupoffice	

';
	
	exit();
}

$vmail = is_dir('/vmail') ? '/vmail' : '/home/vmail';

if(!isset($args['go_root']))
	$args['go_root']='/usr/share/groupoffice';

if(isset($args['go_config']))
	define('CONFIG_FILE', $args['go_config']);

require($args['go_root'].'/Group-Office.php');


echo 'Using config: '.$GLOBALS['GO_CONFIG']->get_config_file()."\n\n";

require_once($GLOBALS['GO_MODULES']->modules['postfixadmin']['class_path'].'postfixadmin.class.inc.php');
$pa = new postfixadmin();

require_once($GLOBALS['GO_CONFIG']->class_path.'database/sql_export.class.inc.php');
$sql_export = new sql_export();


$domain = $pa->get_domain_by_domain($args['domain']);
if(!$domain)
	die('Fatal error: Domain not found in database');


echo "Exporting SQL...\n\n";

$sql_file = $vmail.'/'.$args['domain'].'/export.sql';

if(file_exists($sql_file))
	unlink($sql_file);

touch($sql_file);

$domain['acl_id']=0;
$domain_id = $domain['id'];
$domain['id']='{domain_id}';

$sql = $sql_export->array_to_insert('pa_domains', $domain, 'INSERT IGNORE').";\n\n---\n\n";
file_put_contents($sql_file, $sql, FILE_APPEND);

$pa->get_mailboxes($domain_id);
while($record = $pa->next_record()){
	unset($record['id']);
	$record['domain_id']='{domain_id}';

	$sql = $sql_export->array_to_insert('pa_mailboxes', $record, 'INSERT IGNORE').";\n";
	file_put_contents($sql_file, $sql, FILE_APPEND);
}

file_put_contents($sql_file, "\n---\n\n", FILE_APPEND);


$pa->get_aliases($domain_id);
while($record = $pa->next_record()){
	unset($record['id']);
	$record['domain_id']='{domain_id}';
	
	$sql = $sql_export->array_to_insert('pa_aliases', $record, 'INSERT IGNORE').";\n";
	file_put_contents($sql_file, $sql, FILE_APPEND);
}

file_put_contents($sql_file, "\n---\n\n", FILE_APPEND);


if(isset($args['targethost']))
{
	echo "Running rsync to sync mail\n\n";

	$rsync_cmd = 'rsync -v -rltD --delete -e "ssh" '.$vmail.'/'.$args['domain'].' root@'.$args['targethost'].':/home/vmail';
	system($rsync_cmd);

	$chown_cmd='ssh root@'.$args['targethost'].' "chown vmail:mail -R /home/vmail/'.$args['domain'].'"';
	system($chown_cmd);
}
else
{
	echo "No rsync command executed!";
}

echo "Done! now run /usr/share/groupoffice-mailserver/import_domain.php to import it.\n\n";