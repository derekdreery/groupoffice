<?php
require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');


require_once ($GO_MODULES->class_path."addressbook.class.inc");
require_once ($GO_CONFIG->class_path."mail/RFC822.class.inc");

$RFC822 = new RFC822();

header('Content-Type: text/xml; charset: UTF-8');

echo "<?xml version=\"1.0\" ?>\r\n";
echo '<addressses>';
$ab = new addressbook();
$ab->search_email($GO_SECURITY->user_id, '%'.smart_addslashes($_REQUEST['query']).'%');

$addresses=array();

while($ab->next_record(MYSQL_ASSOC))
{
	$name = format_name($ab->f('last_name'),$ab->f('first_name'),$ab->f('middle_name'),'first_name');
	$rcf_email =$RFC822->write_address($name, $ab->f('email'));
	if($ab->f('email')!='')
	{
		$rcf_email =$RFC822->write_address($name, $ab->f('email'));
		if( !in_array($rcf_email, $addresses))
		{
			$addresses[]=$rcf_email;
			echo '<contact><full_email>'.htmlspecialchars($rcf_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($ab->f('email')).'</email></contact>';
		}
	}	
	if($ab->f('email2')!='')
	{
		$rcf_email =$RFC822->write_address($name, $ab->f('email2'));
		if( !in_array($rcf_email, $addresses))
		{
			$addresses[]=$rcf_email;
			echo '<contact><full_email>'.htmlspecialchars($rcf_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($ab->f('email2')).'</email></contact>';
		}
	}	
	if($ab->f('email3')!='')
	{
		$rcf_email =$RFC822->write_address($name, $ab->f('email3'));
		if( !in_array($rcf_email, $addresses))
		{
			$addresses[]=$rcf_email;
			echo '<contact><full_email>'.htmlspecialchars($rcf_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($ab->f('email3')).'</email></contact>';
		}
	}	
}

if(count($addresses)<10)
{
	$GO_USERS->search('%'.smart_addslashes($_REQUEST['query']).'%',array('name','email'),$GO_SECURITY->user_id, 0,10);
	
	while($GO_USERS->next_record(MYSQL_ASSOC))
	{
		$name = format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'),'first_name');
		$rfc_email = $RFC822->write_address($name, $GO_USERS->f('email'));
		if(!in_array($rfc_email,$addresses))
		{			
			echo '<contact><full_email>'.htmlspecialchars($rcf_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($GO_USERS->f('email')).'</email></contact>';
		}
	}
	echo '</addressses>';
}
