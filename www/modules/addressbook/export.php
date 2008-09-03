<?PHP
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
$post_task = isset($post_task) ? $post_task : '';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));

//load contact management class
require_once($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();

$addressbook_id = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : 0;
$export_type = isset($_POST['export_type']) ? " - ".$_POST['export_type'] : '';
$file_type = isset($_REQUEST['export_filetype']) ? $_REQUEST['export_filetype'] : 'csv';

#go_log(LOG_DEBUG, $_REQUEST);
#echo '<pre>';
#print_r($_REQUEST);
#echo '</pre>';

$addressbook = $ab->get_addressbook($addressbook_id);
$browser = detect_browser();

$filename = $addressbook['name'].$export_type.'.'.$file_type;

if($file_type == 'csv')
{
	header("Content-type: text/x-csv;charset=UTF-8");
} else {
	header("Content-Type: text/x-vCard; name=".$filename);
}

if ($browser['name'] == 'MSIE')
{
	header('Content-Disposition: inline; filename="'.$filename.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
} else {
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}

if($file_type == 'vcf') {
	require_once($GO_MODULES->path."classes/vcard.class.inc");
	$vcard = new vcard();
	if($vcard->export_addressbook($addressbook_id)) {
		echo $vcard->vcf;
	}
} else {
	$quote = smart_stripslashes($_POST['quote']);
	$crlf = smart_stripslashes($_POST['crlf']);
	$crlf = str_replace('\\r', "\015", $crlf);
	$crlf = str_replace('\\n', "\012", $crlf);
	$crlf = str_replace('\\t', "\011", $crlf);
	$seperator = smart_stripslashes($_POST['seperator']);

	if ($_POST['export_type'] == 'contacts')
	{
		$headings = array($lang['common']['title'], $lang['common']['firstName'], $lang['common']['middleName'], $lang['common']['lastName'], $lang['common']['initials'], $lang['common']['sex'], $lang['common']['birthday'], $lang['common']['email'], $lang['common']['email'].' 2', $lang['common']['email'].' 3', $lang['common']['country'], $lang['common']['state'], $lang['common']['city'], $lang['common']['zip'], $lang['common']['address'], $lang['common']['addressNo'], $lang['common']['phone'], $lang['common']['workphone'], $lang['common']['fax'], $lang['common']['workFax'], $lang['common']['cellular'], $lang['common']['company'], $lang['common']['department'], $lang['common']['function'], $lang['addressbook']['comment'], $lang['addressbook']['contactsGroup'], $lang['common']['salutation']);
		$headings = $quote.implode($quote.$seperator.$quote, $headings).$quote;
		echo $headings;
		echo $crlf;

		$ab->get_contacts_for_export($addressbook_id);
		while ($ab->next_record())
		{	
			$record = array($ab->f("title"), $ab->f("first_name"),$ab->f("middle_name"), $ab->f("last_name"), $ab->f("initials"), $ab->f("sex"), $ab->f('birthday'), $ab->f("email"), $ab->f("email2"), $ab->f("email3"), $ab->f("country")/* $country['name']*/, $ab->f("state"), $ab->f("city"), $ab->f("zip"), $ab->f("address"), $ab->f("address_no"), $ab->f("home_phone"), $ab->f("work_phone"), $ab->f("fax"), $ab->f("work_fax"), $ab->f("cellular"), $ab->f("company"), $ab->f("department"), $ab->f("function"), $ab->f("comment"), $ab->f("group_name"), $ab->f("salutation"));
			$record = $quote.implode($quote.$seperator.$quote, $record).$quote;
			echo $record;
			echo $crlf;
		}
	} else {
		$headings = array($lang['common']['name'], $lang['common']['country'], $lang['common']['state'], $lang['common']['city'], $lang['common']['zip'], $lang['common']['address'], $lang['common']['addressNo'], $lang['common']['postCountry'], $lang['common']['postState'], $lang['common']['postCity'], $lang['common']['postZip'], $lang['common']['postAddress'], $lang['common']['postAddressNo'],  $lang['common']['email'], $lang['common']['phone'], $lang['common']['fax'], $lang['common']['homepage'], $lang['addressbook']['bankNo'], $lang['addressbook']['vatNo']);
		$headings = $quote.implode($quote.$seperator.$quote, $headings).$quote;
		echo $headings;
		echo $crlf;

		$ab->get_companies($addressbook_id);

		while($ab->next_record())
		{
			#$country = $GO_USERS->get_country($ab->f('country'));
			#$post_country = $GO_USERS->get_country($ab->f('post_country'));
			
			require($GO_LANGUAGE->get_base_language_file('countries'));
			$country=isset($countries[$ab->f('country')]) ? $countries[$ab->f('country')] : $ab->f('country');
			$post_country=isset($countries[$ab->f('post_country')]) ? $countries[$ab->f('post_country')] : $ab->f('post_country');
			
			
			
			$record = array($ab->f("name"), $country,$ab->f("state"), $ab->f("city"), $ab->f("zip"), $ab->f("address"), $ab->f("address_no"), $post_country, $ab->f("post_state"), $ab->f("post_city"), $ab->f("post_zip"), $ab->f("post_address"), $ab->f("post_address_no"),$ab->f("email"), $ab->f("phone"), $ab->f("fax"), $ab->f("homepage"), $ab->f("bank_no"), $ab->f('vat_no'));
			$record = $quote.implode($quote.$seperator.$quote, $record).$quote;
			echo $record;
			echo $crlf;
		}
	}
}
exit();
?>