<?php
$module = $this->get_module('addressbook');
global $GO_LANGUAGE, $lang;
require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));

require_once($module['class_path'].'addressbook.class.inc.php');
$ab = new addressbook();

require_once($GLOBALS['GO_CONFIG']->class_path.'mail/Go2Mime.class.inc.php');


//$default_salutation = $lang['common']['dear'].' ['.$lang['common']['sirMadam']['M'].'/'.$lang['common']['sirMadam']['F'].'] {middle_name} {last_name}';

$default_language = $GLOBALS['GO_CONFIG']->default_country;
if(!$GLOBALS['GO_LANGUAGE']->get_address_format_by_iso($default_language))
	$default_language = 'US';

$addressbook = $ab->add_addressbook(1, $lang['addressbook']['prospects'], $default_language, $lang['addressbook']['defaultSalutation']);
$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $addressbook['acl_id'], GO_SECURITY::WRITE_PERMISSION);

$addressbook = $ab->add_addressbook(1, $lang['addressbook']['suppliers'], $default_language, $lang['addressbook']['defaultSalutation']);
$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $addressbook['acl_id'], GO_SECURITY::WRITE_PERMISSION);


$company['addressbook_id']=$addressbook['addressbook_id'];
$company['name']='Intermesh';
$company['address']='Reitscheweg';
$company['address_no']='37';
$company['zip']='5232 BX';
$company['city']='\'s-Hertogenbosch';
$company['state']='Noord-Brabant';
$company['country']='NL';
$company['iso_address_format']=$default_language;
$company['post_address']='Reitscheweg';
$company['post_address_no']='37';
$company['post_zip']='5232 BX';
$company['post_city']='\'s-Hertogenbosch';
$company['post_state']='Intermesh';
$company['post_country']='NL';
$company['post_iso_address_format']=$default_language;
$company['phone']='+31 (0) 73 - 644 55 08';
$company['fax']='+31 (0) 84 738 03 70';
$company['email']='info@intermesh.nl';
$company['homepage']='http://www.intermesh.nl';
$company['bank_no']='';
$company['vat_no']='NL 1502.03.871.B01';
$company['user_id']=1;
$company['comment']='';

$contact['user_id']=1;
//$contact['company_id']=$ab->add_company($company);
$contact['addressbook_id']=$addressbook['addressbook_id'];
$contact['first_name']='Merijn';
$contact['middle_name']='';
$contact['last_name']='Schering';
$contact['title']='Ing.';
$contact['initials']='M.K.';
$contact['sex']='M';
$contact['email']='mschering@intermesh.nl';
$contact['salutation']=$lang['common']['dear'].' Merijn';
$contact['comment']='';
$contact['iso_address_format']=$default_language;

if (!is_dir($GLOBALS['GO_CONFIG']->file_storage_path.'contacts/contact_photos'))
	mkdir($GLOBALS['GO_CONFIG']->file_storage_path.'contacts/contact_photos',0755, true);

//copy($module['path'].'install/noperson.jpg',$GLOBALS['GO_CONFIG']->file_storage_path.'contacts/contact_photos/noperson.jpg');

//$ab->add_contact($contact);


$addressbook = $ab->add_addressbook(1, $lang['addressbook']['customers'], $default_language, $lang['addressbook']['defaultSalutation']);
$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $addressbook['acl_id'], GO_SECURITY::WRITE_PERMISSION);

/*
 * Problem if files module is not intalled yet.
 * 
 * require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
$files = new files();
$files->check_share('contacts', 1, $GLOBALS['GO_MODULES']->modules['addressbook']['acl_id'], false);
$files->check_share('companies', 1, $GLOBALS['GO_MODULES']->modules['addressbook']['acl_id'], false);*/