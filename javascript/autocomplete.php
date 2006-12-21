<?php
require('../Group-Office.php');

$GO_SECURITY->authenticate();


if (!isset($_SESSION['GO_SESSION']['autocomplete_cache_file']) || !file_exists($_SESSION['GO_SESSION']['autocomplete_cache_file']))
{
	require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
	$RFC822 = new RFC822();
	if(!is_dir($GO_CONFIG->tmpdir.'cache/'))
	{
		mkdir_recursive($GO_CONFIG->tmpdir.'cache/');
	}
	$_SESSION['GO_SESSION']['autocomplete_cache_file'] = $GO_CONFIG->tmpdir.'cache/'.uniqid(time()).'.js';

	if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
	{ 	
		require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
		$ab = new addressbook();
		
		$ab->search_contacts($GO_SECURITY->user_id, '%');
		while($ab->next_record())
		{
			if(validate_email($ab->f('email')))
			{
				$middle_name = $ab->f('middle_name') == '' ? ' ' : ' '.$ab->f('middle_name').' ';  		
				$autocomplete_contacts[] = $RFC822->write_address(addslashes($ab->f('first_name').$middle_name.$ab->f('last_name')),$ab->f('email'));
			}
		}
		$ab->search_companies($GO_SECURITY->user_id, '%');
		while($ab->next_record())
		{
			if(validate_email($ab->f('email')))
			{
				$autocomplete_contacts[] = $RFC822->write_address(addslashes($ab->f('name')),$ab->f('email'));
			}
		}
	}
	$GO_USERS->get_authorized_users($GO_SECURITY->user_id);
	while($GO_USERS->next_record())
	{
		$middle_name = $GO_USERS->f('middle_name') == '' ? ' ' : ' '.$GO_USERS->f('middle_name').' ';
		$autocomplete_contacts[] = '"'.addslashes($GO_USERS->f('first_name').$middle_name.$GO_USERS->f('last_name')).'" <'.$GO_USERS->f('email').'>';
	}

	$fp = fopen($_SESSION['GO_SESSION']['autocomplete_cache_file'], 'w+');
	fwrite($fp, 'var autocomplete_contacts = new Array(\''.implode("','", $autocomplete_contacts).'\');');
	fclose($fp);
	go_log(LOG_DEBUG, 'Generated cache file');
}
echo file_get_contents($_SESSION['GO_SESSION']['autocomplete_cache_file']);
