<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

$test = false;

$root = dirname(dirname(dirname(__FILE__)));
require_once($root.'/go/GO.php');
GO::init();

$mailing_id = $argv[2];

$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($mailing_id);
if(!$mailing)
	die("Mailing not found!\n");

GO::session()->setCurrentUser($mailing->user_id);

$addresslist = GO_Addressbook_Model_Addresslist::model()->findByPk($mailing->addresslist_id);
$mimeData = file_get_contents($mailing->message_path);
$message = GO_Base_Mail_Message::newInstance()
				->loadMimeMessage($mimeData);

if($test)
	echo "Running in test mode\n";

if(isset($argv[3]) && $argv[3]=='restart') {
  //clear recipients status and set mailing status to 1 (in progress)
	// TODO: replace this
	// $ml->start_mailing($mailing);
	echo "Restarting mailing\n";
}


$joinCriteria = GO_Base_Db_FindCriteria::newInstance()->addRawCondition('t.id', 'a.account_id');		
$findParams = GO_Base_Db_FindParams::newInstance()
	->single()
	->debugSql()
	->join(GO_Email_Model_Alias::model()->tableName(), $joinCriteria,'a')
	->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('id', $mailing->alias_id,'=','a')
);
$account = GO_Email_Model_Account::model()->find($findParams);

$mailer = GO_Base_Mail_Mailer::newGoInstance(GO_Email_Transport::newGoInstance($account));

echo "Will send emails from ".$account->username.".\n";

//$message->setTo(GO::config()->webmaster_email,"Wilmar van Beusekom");
//
//$mailer = GO_Base_Mail_Mailer::newGoInstance();

if(!$test){
	//Rate limit to 100 emails per-minute
	$mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(GO::config()->mailing_messages_per_minute, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));
}

echo 'Sending a maximum of '.GO::config()->mailing_messages_per_minute.' messages per minute'."\n";

$failedRecipients = array();

$bodyWithTags = $message->getBody();

function sendmail($typestring,$message,$model,$mailer,$addresslist,$mailing) {
	echo "Sending to ".$typestring." id: ".$model->id." email: ".$model->email."\n";
	
	try {
		$mailer->send($message);
	} catch(Exception $e) {
		$status=$e->getMessage();
	}
	if(!empty($status)) {
		echo "---------\n";
		echo "Failed!\n";
		echo $status."\n";
		echo "---------\n";
		
		$error = 1;
		$sent = 0;
		unset($status);
	} else {
		$error = 0;
		$sent = 1;
	}
	
	if ($typestring=='contact') {
		$stmt = GO_Addressbook_Model_SendmailingContact::model()->findByAttributes(
			array(
				"addresslist_id" => $addresslist->id,
				"contact_id" => $model->id
			)
		);
	} else {
		$stmt = GO_Addressbook_Model_SendmailingCompany::model()->findByAttributes(
			array(
				"addresslist_id" => $addresslist->id,
				"company_id" => $model->id
			)
		);
	}
	if ($coupling = $stmt->fetch())
		$coupling->delete();

	$mailing->setAttributes(array(
			"sent" => $sent+$mailing->sent,
			"errors" => $error+$mailing->errors
		));
	$mailing->save();
}

foreach ($addresslist->contacts as $contact) {
	$message->setTo($contact->email,$contact->name);	
	$message->setBody(GO_Addressbook_Model_Template::model()->replaceModelTags($bodyWithTags,$contact));
	sendmail('contact',$message,$contact,$mailer,$addresslist,$mailing);
}

foreach ($addresslist->companies as $company) {
	$message->setTo($company->email,$company->name);
	$message->setBody(GO_Addressbook_Model_Template::model()->replaceModelTags($bodyWithTags,$company));
	sendmail('company',$message,$company,$mailer,$addresslist,$mailing);
}

echo "Mailing finished\n";
?>