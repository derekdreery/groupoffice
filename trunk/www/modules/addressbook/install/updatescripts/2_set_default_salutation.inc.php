<?php
	$module = $GO_MODULES->get_module('addressbook');
	global $GO_LANGUAGE, $lang;
	require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));

	require_once($module['class_path'].'addressbook.class.inc.php');
	$ab = new addressbook();

	$default_salutation = $lang['common']['dear'].' ['.$lang['common']['sirMadam']['M'].'/'.$lang['common']['sirMadam']['F'].'] {middle_name} {last_name}';
	$default_language = $GO_CONFIG->default_country;
	if($GO_LANGUAGE->get_address_format_by_iso($default_language) == 0)
		$default_language = 'US';

	$ab->update_all_addressbooks($default_language, $default_salutation);
?>