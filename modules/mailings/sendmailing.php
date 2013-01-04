#!/usr/bin/php
<?php
if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

//event firing will cause problems with Ioncube
define('NO_EVENTS',true);

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$test=false;

$mailing_id = $argv[2];

$root_path = dirname(dirname(dirname(__FILE__)));

require($root_path.'/Group-Office.php');


require($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
require_once ($GO_CONFIG->class_path.'mail/mimeDecode.class.inc');

$RFC822 = new RFC822();

require_once($GO_MODULES->modules['addressbook']['path'].'classes/addressbook.class.inc.php');
require_once($GO_MODULES->modules['email']['path'].'classes/email.class.inc.php');
require_once($GO_MODULES->modules['mailings']['path'].'classes/templates.class.inc.php');
require_once($GO_MODULES->modules['mailings']['path'].'classes/mailings.class.inc.php');
$tp = new templates();
$ml = new mailings();
$ml2 = new mailings();
$email = new email();
$ab = new addressbook();

$mailing = $ml->get_mailing($mailing_id);
$mailing_group = $ml->get_mailing_group($mailing['mailing_group_id']);

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GO_USERS->update_session($mailing['user_id']);

if($test) {
	echo "Running in test mode\n";
}

if(isset($argv[3]) && $argv[3]=='restart') {
  //clear recipients status and set mailing status to 1 (in progress)
	$ml->start_mailing($mailing);
	echo "Restarting mailing\n";
}

if(!$mailing) {
	die("Mailing not found!\n");
}


$transport=null;
if(isset($GO_CONFIG->mailing_smtp_server)) {

	$GO_CONFIG->mailing_smtp_port = isset($GO_CONFIG->mailing_smtp_port) ? $GO_CONFIG->mailing_smtp_port : 25;

	$encryption = empty($GO_CONFIG->mailing_smtp_encryption) ? null : $GO_CONFIG->mailing_smtp_encryption;
	$transport=new Swift_SmtpTransport($GO_CONFIG->mailing_smtp_server, $GO_CONFIG->mailing_smtp_port, $encryption);
	if(!empty($GO_CONFIG->mailing_smtp_username)) {
		$transport->setUsername($GO_CONFIG->mailing_smtp_username)
				->setPassword($GO_CONFIG->mailing_smtp_password);
	}
}


$data = file_get_contents($mailing['message_path']);
$swift = new GoSwiftImport($data,false,$mailing['alias_id'],$transport);

$GO_CONFIG->mailing_messages_per_minute = isset($GO_CONFIG->mailing_messages_per_minute) ? $GO_CONFIG->mailing_messages_per_minute : 30;

echo 'Sending a maximum of '.$GO_CONFIG->mailing_messages_per_minute.' messages per minute'."\n";

if(!$test){
	//Rate limit to 100 emails per-minute
	$swift->registerPlugin(new Swift_Plugins_ThrottlerPlugin($GO_CONFIG->mailing_messages_per_minute, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));
}


$failed=array();

function send($type, $id, $body, $to) {

	global $swift, $ml2, $RFC822, $log, $mailing, $test;

	//check if mailing was paused
	$mailing = $ml2->get_mailing($mailing['id']);
	if($mailing['status']==3){
		echo "Mailing was paused\n";
		exit();
	}

	echo "Sending to ".$type." id: ".$id." email: ".$to."\n";

	$success=false;
	$status='';
	try {
		$swift->set_body($body,'html');
		if($test) {
			$swift->set_to('admin@intermeshdev.nl');
		}
		else {
			$swift->set_to($to);
		}

		$success = $swift->sendmail(false, true);
	}
	catch(Exception $e ) {
		$status=$e->getMessage();
	}
	

	if(!$success) {

		echo "---------\n";

		echo "Failed!\n";
		echo $status."\n";

		echo "---------\n";
	}
	
	$func = $type.'_sent';
	$ml2->$func($mailing['id'], $id);

	$ml2->update_status($mailing['id'], $success ? 0 : 1, $success ? 1 : 0);

//echo memory_get_usage()."\n";
}

if($ml->get_contacts_for_send($mailing['id'])) {

	while($record = $ml->next_record()) {
		send('contact',
				$record['id'],
				$tp->replace_contact_data_fields($swift->body, $record['id'], false,$mailing['mailing_group_id']),
				$RFC822->write_address(String::format_name($record),$record['email'])
		);
	}
}
if($ml->get_companies_for_send($mailing['id'])) {

	while($record = $ml->next_record()) {

		$record['mailing_group_id']=$mailing['mailing_group_id'];

		send('company', 
				$record['id'],
				$tp->replace_company_data_fields($swift->body, $record['id'], false,$mailing['mailing_group_id']),
				$RFC822->write_address($record['name'],$record['email']));
	}
}

if($ml->get_users_for_send($mailing['id'])) {
	$type='user';

	while($record = $ml->next_record()) {

		$record['mailing_group_id']=$mailing['mailing_group_id'];

		send('user', 
				$record['id'],
				$tp->replace_user_data_fields($swift->body, $record['id'], $mailing['mailing_group_id']),
				$RFC822->write_address(String::format_name($record),$record['email']));
	}
}

$ml->end_mailing($mailing);
echo "Mailing finished\n";
