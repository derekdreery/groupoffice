<?php
$module = $this->get_module('addressbook');
global $GO_LANGUAGE, $lang;
require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));

require_once($module['class_path'].'addressbook.class.inc.php');
$ab = new addressbook();

require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');

$addressbook = $ab->add_addressbook(1, $lang['addressbook']['prospects']);
$GLOBALS['GO_SECURITY']->add_group_to_acl($GO_CONFIG->group_internal, $addressbook['acl_write']);

$addressbook = $ab->add_addressbook(1, $lang['addressbook']['suppliers']);
$GLOBALS['GO_SECURITY']->add_group_to_acl($GO_CONFIG->group_internal, $addressbook['acl_write']);


$company['addressbook_id']=$addressbook['addressbook_id'];
$company['name']='Intermesh';
$company['address']='Reitscheweg';
$company['address_no']='37';
$company['zip']='5232 BX';
$company['city']='\'s-Hertogenbosch';
$company['state']='Noord-Brabant';
$company['country']='NL';
$company['post_address']='Reitscheweg';
$company['post_address_no']='37';
$company['post_zip']='5232 BX';
$company['post_city']='\'s-Hertogenbosch';
$company['post_state']='Intermesh';
$company['post_country']='NL';
$company['phone']='+31 (0) 73 - 644 55 08';
$company['fax']='+31 (0) 84 738 03 70';
$company['email']='info@intermesh.nl';
$company['homepage']='http://www.intermesh.nl';
$company['bank_no']='';
$company['vat_no']='NL 1502.03.871.B01';
$company['user_id']=1;
$company['comment']='';

$contact['user_id']=1;
$contact['company_id']=$ab->add_company($company);
$contact['addressbook_id']=$addressbook['addressbook_id'];
$contact['first_name']='Merijn';
$contact['middle_name']='';
$contact['last_name']='Schering';
$contact['title']='Ing.';
$contact['initials']='M.K.';
$contact['sex']='M';
$contact['email']='mschering@intermesh.nl';
$contact['salutation']='Dear Merijn';
$contact['comment']='';

$ab->add_contact($contact);


$addressbook = $ab->add_addressbook(1, $lang['addressbook']['customers']);
$GLOBALS['GO_SECURITY']->add_group_to_acl($GO_CONFIG->group_internal, $addressbook['acl_write']);
