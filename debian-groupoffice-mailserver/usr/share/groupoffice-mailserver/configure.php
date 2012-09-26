#!/usr/bin/php
<?php
require('/etc/groupoffice/config-mailserver.inc.php');

chdir(dirname(__FILE__));

$replacements['db_name']=$dbname;
$replacements['db_user']=$dbuser;
$replacements['db_pass']=$dbpass;
$replacements['domain']=$domain;

function create_file($file, $tpl, $replacements) {
	$data = file_get_contents($tpl);

	foreach($replacements as $key=>$value) {
		$data = str_replace('{'.$key.'}', $value, $data);
	}

	file_put_contents($file, $data);
}

function set_value($file, $str) {
	$data = file_get_contents($file);

	if(!strpos($data, $str)) {
		$data .= "\n".$str;
	}
	file_put_contents($file, $data);
}




echo "Configuring Postfix\n";


$DBCONN="user = $dbuser
password = $dbpass
hosts = localhost
dbname = $dbname";

if(!file_exists('/etc/postfix/mysql_virtual_alias_maps.cf')) {
	$content="$DBCONN
table = pa_aliases
select_field = goto
where_field = address
additional_conditions = and active = '1'";
	file_put_contents('/etc/postfix/mysql_virtual_alias_maps.cf', $content);
}

if(!file_exists('/etc/postfix/mysql_virtual_domains_maps.cf')) {
	$content="$DBCONN
table = pa_domains
select_field = domain
where_field = domain
additional_conditions = and backupmx = '0' and active = '1'";
	file_put_contents('/etc/postfix/mysql_virtual_domains_maps.cf', $content);
}

if(!file_exists('/etc/postfix/mysql_virtual_mailbox_limit_maps.cf')) {
	$content="$DBCONN
table = pa_mailboxes
select_field = quota
where_field = username
additional_conditions = and active = '1'";
	file_put_contents('/etc/postfix/mysql_virtual_mailbox_limit_maps.cf', $content);
}

if(!file_exists('/etc/postfix/mysql_virtual_mailbox_maps.cf')) {
	$content="$DBCONN
table = pa_mailboxes
select_field = maildir
where_field = username
additional_conditions = and active = '1'";
	file_put_contents('/etc/postfix/mysql_virtual_mailbox_maps.cf', $content);
}

if(!file_exists('/etc/postfix/mysql_virtual_transports.cf')) {
	$content="$DBCONN
table = pa_domains
select_field = transport
where_field = domain
additional_conditions = and active = '1'";
	file_put_contents('/etc/postfix/mysql_virtual_transports.cf', $content);
}

if(!file_exists('/etc/postfix/mysql_relay_domains_maps.cf')) {
	$content="$DBCONN
table = pa_domains
select_field = domain
where_field = domain
additional_conditions = and backupmx = '1' and active = '1'";
	file_put_contents('/etc/postfix/mysql_relay_domains_maps.cf', $content);
}

$transport = file_exists('/etc/postfix/transport') ? file_get_contents('/etc/postfix/transport') : '';
if(strpos($transport, "autoreply.$domain vacation:")===false) {
	file_put_contents('/etc/postfix/transport', "autoreply.$domain vacation:", FILE_APPEND);
	system('postmap /etc/postfix/transport');
}


/*$version=0;
$replacements['sieve']='cmusieve';
exec("lsb_release -a", $output);
foreach($output as $line) {
	$parts = explode(':', $line);
	$name = trim($parts[0]);
	$value = trim($parts[1]);
	if($name=='Release') {
		$version = floatval($value);
		break;
	}
}

echo "Linux version: ".$version."\n\n";

if($version > 9.10) {
	$replacements['sieve']='sieve';
}*/

//works with debian 6 now too
$replacements['sieve']='sieve';

function file_contains($filename, $str){
	if(!file_exists($filename))
		return false;

	return strpos(file_get_contents($filename),$str)!==false;
}

echo "Configuring Dovecot\n";

exec('dovecot --version', $output);

$version = trim($output[0]);

echo "Dovecot version ".$version." detected\n";

if(version_compare(2, $version)>0){

	$filename = file_contains('/etc/dovecot/dovecot-sql.conf', 'pa_mailboxes') ? '/etc/dovecot/dovecot-sql.conf.'.date('Ymd') : '/etc/dovecot/dovecot-sql.conf';
	create_file($filename,'tpl/etc/dovecot/dovecot-sql.conf', $replacements);

	$filename = file_contains('/etc/dovecot/dovecot.conf', 'Group-Office') ? '/etc/dovecot/dovecot.conf.'.date('Ymd') : '/etc/dovecot/dovecot.conf';
	create_file($filename,'tpl/etc/dovecot/dovecot.conf', $replacements);
	
	if(!file_contains('/etc/groupoffice/config.php', 'sieve_port'))
		set_value('/etc/groupoffice/config.php', '$config[\'sieve_port\']="2000";');
}else
{
	$filename = '/etc/dovecot/dovecot-sql.conf.ext';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/dovecot-sql.conf.ext', $replacements);
	
	$filename = '/etc/dovecot/conf.d/auth-sql.conf.ext';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/auth-sql.conf.ext', $replacements);
	
	$filename = '/etc/dovecot/conf.d/10-auth.conf';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/10-auth.conf', $replacements);
	
	$filename = '/etc/dovecot/conf.d/10-mail.conf';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/10-mail.conf', $replacements);
	
	$filename = '/etc/dovecot/conf.d/10-master.conf';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/10-master.conf', $replacements);
	
	$filename = '/etc/dovecot/conf.d/15-lda.conf';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/15-lda.conf', $replacements);
	
	$filename = '/etc/dovecot/conf.d/20-imap.conf';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/20-imap.conf', $replacements);
	
	$filename = '/etc/dovecot/conf.d/90-quota.conf';
	if(!file_contains($filename, 'Group-Office'))
		create_file($filename,'tpl/etc/dovecot/conf.d/90-quota.conf', $replacements);
	
	if(!file_contains('/etc/groupoffice/config.php', 'sieve_port'))
		set_value('/etc/groupoffice/config.php', '$config[\'sieve_port\']="4190";');
}

echo "Configuring amavis\n";
$filename =  '/etc/amavis/conf.d/60-groupoffice_defaults';
if(!file_contains($filename, 'Group-Office'))
	create_file($filename,'tpl/etc/amavis/conf.d/60-groupoffice_defaults', $replacements);


echo "Configuring groupoffice\n";
if(!file_exists('/etc/groupoffice/globalconfig.inc.php'))
	create_file('/etc/groupoffice/globalconfig.inc.php','tpl/etc/groupoffice/globalconfig.inc.php', $replacements);

if(!file_contains('/etc/groupoffice/config.php', 'serverclient_domains'))
	set_value('/etc/groupoffice/config.php', '$config[\'serverclient_domains\']="'.$domain.'";');



if(file_exists($config['file_storage_path'].'key.txt'))
	system('chown www-data:www-data '.$config['file_storage_path'].'key.txt');
?>