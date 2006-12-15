<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');

require_once($GO_MODULES->class_path.'addressbook.class.inc');
require_once($GO_MODULES->class_path.'vcard.class.inc');
$vcard = new vcard();

if (isset($_REQUEST['contact_id']) && $contact = $vcard->get_contact($_REQUEST['contact_id']))
{
	$filename = $contact['first_name'].'_'.$contact['last_name'].'.vcf';
}

if (!isset($filename))
{
	die($strDataError);
}else
{
	$browser = detect_browser();

	header('Content-Type: text/x-vcard;charset='.$charset);
	//header('Content-Length: '.filesize($path));
	header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
	if ($browser['name'] == 'MSIE')
	{
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}else
	{
		header('Pragma: no-cache');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	}
	header('Content-Transfer-Encoding: binary');

	if($vcard->export_contact($_REQUEST['contact_id'])) {
		echo $vcard->vcf;
	}
}
?>
