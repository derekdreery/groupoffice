<?php
require('../../Group-Office.php');

$GO_SECURITY->authenticate();

$GO_MODULES->authenticate('email');

if(count($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']) > 0)
{
	
	require($GO_LANGUAGE->get_language_file('email'));
	//var_dump($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']);
	$return_to=$_SERVER['PHP_SELF'];
	//$contact = array_shift($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']);
	$contact=$_SESSION['GO_SESSION']['email_module']['unknown_reciepents'][0];

	$url = $GO_MODULES->modules['addressbook']['url'].'contact.php';
	$url = add_params_to_url($url, 'feedback='.urlencode($ml_unknown_reciepent));
	
	foreach($contact as $field=>$value)
	{
		$url = add_params_to_url($url, urlencode($field).'='.urlencode($value));
	}
	$url = add_params_to_url($url, 'return_to='.urlencode($return_to));
	
	
	require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
	
	$count = $ab->search_contacts($GO_SECURITY->user_id, $contact['first_name'].'%'.$contact['last_name'],'name',0,0,0,0,false,'name','ASC',true);
	if(!$count)
	{	
		header('Location: '.$url);
	}else {
		require($GO_CONFIG->class_path.'mail/RFC822.class.inc');
		$RFC822 = new RFC822();
		
		load_basic_controls();
		require($GO_LANGUAGE->get_language_file('addressbook'));
		//require the header file
		require_once($GO_THEME->theme_path.'header.inc');
		
		$tabstrip = new tabstrip('select_mailing', $ab_mailings);
		$tabstrip->set_attribute('style','width:100%');
		
		$email = $RFC822->write_address(format_name($contact['last_name'],$contact['first_name'], $contact['middle_name'], 'first_name'), $contact['email']);
		
		$tabstrip->add_html_element(new html_element('p', sprintf($ab_select_unknown_recipient, htmlspecialchars($email))));		

		while($ab->next_record())
		{			
			$contact_url = $GO_MODULES->modules['addressbook']['url'].'contact.php';			
			
			
			if($ab->f('email')=='')
			{
				$contact_url = add_params_to_url($contact_url,'email='.urlencode($contact['email']));
			}elseif($ab->f('email2')=='')
			{
				$contact_url = add_params_to_url($contact_url,'email2='.urlencode($contact['email']));
			}elseif($ab->f('email3')=='')
			{
				$contact_url = add_params_to_url($contact_url,'email3='.urlencode($contact['email']));
			}else{
				continue;
			}
			$contact_url = add_params_to_url($contact_url, 'return_to='.urlencode($return_to));
			$contact_url = add_params_to_url($contact_url, 'feedback='.urlencode($ab_check_info));
	
			$link = new hyperlink(add_params_to_url($contact_url,'contact_id='.$ab->f('id')), format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'), 'first_name'));
			$link->set_attribute('class','selectableItem');
			$tabstrip->add_html_element($link);
		}
		
		$tabstrip->add_html_element(new button($contacts_add, "javascript:document.location='".$url."';"));

		if(count($_SESSION['GO_SESSION']['email_module']['unknown_reciepents']) > 0)
		{
			$tabstrip->add_html_element(new button($cmdClose, "javascript:window.close()"));
		}else {
			$tabstrip->add_html_element(new button($ab_ignore, "javascript:document.location='".$_SERVER['PHP_SELF']."';"));
		}
		echo $tabstrip->get_html();
		require_once($GO_THEME->theme_path.'footer.inc');
		
	}
}else {
	require_once($GO_THEME->theme_path.'header.inc');
	echo "<script type=\"text/javascript\">\r\nwindow.close();\r\n</script>\r\n";
	require_once($GO_THEME->theme_path.'footer.inc');
}