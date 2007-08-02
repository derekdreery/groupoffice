<?php
require('../../Group-Office.php');
require_once ($GO_MODULES->modules['addressbook']['path']."classes/addressbook.class.inc");
require_once ($GO_MODULES->modules['addressbook']['path']."classes/vcard.class.inc");
$vcard = new vcard();
/*
if ($content = $vcard->_get_file_content('vcard.vcf')) {
		if ($vcard->_set_vcard($content, "file")) {
		foreach ($vcard->instance as $_vcard) {
		$record = $vcard->_get_vcard_contact($_vcard);
		//$record = $vcard->_get_vcard_contact($_vcard);
		var_dump($record);
		}
	}
}
*/
$vcard->export_contact(2);
echo nl2br($vcard->vcf);
