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

require_once ("../../Group-Office.php");
$post_action = isset ($post_action) ? $post_action : '';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once ($GO_LANGUAGE->get_language_file('addressbook'));

load_basic_controls();
load_control('tabtable');
load_control('dropbox');

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$addressbook_id = isset ($_REQUEST['addressbook_id']) ? smart_addslashes($_REQUEST['addressbook_id']) : '0';
$return_to = (isset ($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
;

//load contact management class
require_once ($GO_MODULES->path."classes/addressbook.class.inc");
$ab = new addressbook();

require_once ($GO_THEME->theme_path."header.inc");
?>
<script type="text/javascript" language="javascript">
<!--

function import_data()
{
	document.forms[0].task.value='import';
	document.forms[0].submit();
}

-->
</script>

<form name="import" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="task" value="import" />
<input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to); ?>" />

<?php

$tabtable = new tabtable('export_tab', $contacts_import, '100%', '400', '120', '', true);
$tabtable->print_head();
echo '<table border="0" cellpadding="5"><tr><td>';
if ($task == 'import') {


	switch ($_POST['file_type']) {
		case 'vcf' :
			require_once ($GO_MODULES->path."classes/vcard.class.inc");
			$vcard = new vcard();
			$success = $vcard->import($_POST['import_file'], $GO_SECURITY->user_id, $_POST['addressbook_id']);

			unlink($_POST['import_file']);
			if ($success) {
				echo $contacts_import_success;
			} else {
				echo $ab_import_failed;
			}
			echo '<br /><br />';
			$button = new button($cmdOk, "javascript:document.location='".htmlspecialchars($return_to)."'");
			echo $button->get_html();
			break;
		case 'csv' :
			$seperator = isset ($_POST['seperator']) ? $_POST['seperator'] : ';';

			$fp = fopen($_POST['import_file'], "r");

			if (!$fp || !$addressbook = $ab->get_addressbook($_POST['addressbook_id'])) {
				unlink($_POST['import_file']);
				$feedback = "<p class=\"Error\">".$strDataError."</p>";

			} else {
				fgets($fp, 4096);
				while (!feof($fp)) {
					$record = fgetcsv($fp, 4096, ',', '"');

					if ($_POST['import_type'] == 'contacts') {
						if ((isset ($record[$_POST['first_name']]) && $record[$_POST['first_name']] != "") || (isset ($record[$_POST['last_name']]) && $record[$_POST['last_name']] != '')) {

							$contact['title'] = isset ($record[$_POST['title']]) ? addslashes(trim($record[$_POST['title']])) : '';
							$contact['first_name'] = isset ($record[$_POST['first_name']]) ? addslashes(trim($record[$_POST['first_name']])) : '';
							$contact['middle_name'] = isset ($record[$_POST['middle_name']]) ? addslashes(trim($record[$_POST['middle_name']])) : '';
							$contact['last_name'] = isset ($record[$_POST['last_name']]) ? addslashes(trim($record[$_POST['last_name']])) : '';
							$contact['initials'] = isset ($record[$_POST['initials']]) ? addslashes(trim($record[$_POST['initials']])) : '';
							$contact['sex'] = isset ($record[$_POST['sex']]) ? addslashes(trim($record[$_POST['sex']])) : 'M';
							$contact['birthday'] = isset ($record[$_POST['birthday']]) ? addslashes(trim($record[$_POST['birthday']])) : '';
							$contact['email'] = isset ($record[$_POST['email']]) ? get_email_from_string($record[$_POST['email']]) : '';
							$contact['work_phone'] = isset ($record[$_POST['work_phone']]) ? addslashes(trim($record[$_POST['work_phone']])) : '';
							$contact['home_phone'] = isset ($record[$_POST['home_phone']]) ? addslashes(trim($record[$_POST['home_phone']])) : '';
							$contact['fax'] = isset ($record[$_POST['fax']]) ? addslashes(trim($record[$_POST['fax']])) : '';
							$contact['work_fax'] = isset ($record[$_POST['work_fax']]) ? addslashes(trim($record[$_POST['work_fax']])) : '';
							$contact['cellular'] = isset ($record[$_POST['cellular']]) ? addslashes(trim($record[$_POST['cellular']])) : '';
							$contact['country'] = isset ($record[$_POST['country']]) ? addslashes(trim($record[$_POST['country']])) : '';
							$contact['state'] = isset ($record[$_POST['state']]) ? addslashes(trim($record[$_POST['state']])) : '';
							$contact['city'] = isset ($record[$_POST['city']]) ? addslashes(trim($record[$_POST['city']])) : '';
							$contact['zip'] = isset ($record[$_POST['zip']]) ? addslashes(trim($record[$_POST['zip']])) : '';
							$contact['address'] = isset ($record[$_POST['address']]) ? addslashes(trim($record[$_POST['address']])) : '';
							$contact['address_no'] = isset ($record[$_POST['address_no']]) ? addslashes(trim($record[$_POST['address_no']])) : '';
							$company_name = isset ($record[$_POST['company_name']]) ? addslashes(trim($record[$_POST['company_name']])) : '';
							$contact['department'] = isset ($record[$_POST['department']]) ? addslashes(trim($record[$_POST['department']])) : '';
							$contact['function'] = isset ($record[$_POST['function']]) ? addslashes(trim($record[$_POST['function']])) : '';

							if ($company_name != '') {
								$contact['company_id'] = $ab->get_company_id_by_name($company_name, $_POST['addressbook_id']);
							}
							$contact['addressbook_id'] = $_POST['addressbook_id'];
							$ab->add_contact($contact);

						}
					} else {
						if (isset ($record[$_POST['name']]) && $record[$_POST['name']] != '') {
							$company['name'] = addslashes(trim($record[$_POST['name']]));

							if (!$ab->get_company_by_name($_POST['addressbook_id'], $company['name'])) {
								$company['email'] = isset ($record[$_POST['email']]) ? get_email_from_string($record[$_POST['email']]) : '';
								$company['phone'] = isset ($record[$_POST['phone']]) ? addslashes(trim($record[$_POST['phone']])) : '';
								$company['fax'] = isset ($record[$_POST['fax']]) ? addslashes(trim($record[$_POST['fax']])) : '';
								$company['country'] = isset ($record[$_POST['country']]) ? addslashes(trim($record[$_POST['country']])) : '';
								$company['state'] = isset ($record[$_POST['state']]) ? addslashes(trim($record[$_POST['state']])) : '';
								$company['city'] = isset ($record[$_POST['city']]) ? addslashes(trim($record[$_POST['city']])) : '';
								$company['zip'] = isset ($record[$_POST['zip']]) ? addslashes(trim($record[$_POST['zip']])) : '';
								$company['address'] = isset ($record[$_POST['address']]) ? addslashes(trim($record[$_POST['address']])) : '';
								$company['address_no'] = isset ($record[$_POST['address_no']]) ? addslashes(trim($record[$_POST['address_no']])) : '';
								$company['post_country'] = isset ($record[$_POST['post_country']]) ? addslashes(trim($record[$_POST['post_country']])) : '';
								$company['post_state'] = isset ($record[$_POST['post_state']]) ? addslashes(trim($record[$_POST['post_state']])) : '';
								$company['post_city'] = isset ($record[$_POST['post_city']]) ? addslashes(trim($record[$_POST['post_city']])) : '';
								$company['post_zip'] = isset ($record[$_POST['post_zip']]) ? addslashes(trim($record[$_POST['post_zip']])) : '';
								$company['post_address'] = isset ($record[$_POST['post_address']]) ? addslashes(trim($record[$_POST['post_address']])) : '';
								$company['post_address_no'] = isset ($record[$_POST['post_address_no']]) ? addslashes(trim($record[$_POST['post_address_no']])) : '';
								$company['homepage'] = isset ($record[$_POST['homepage']]) ? addslashes(trim($record[$_POST['homepage']])) : '';
								$company['bank_no'] = isset ($record[$_POST['bank_no']]) ? addslashes(trim($record[$_POST['bank_no']])) : '';
								$company['vat_no'] = isset ($record[$_POST['vat_no']]) ? addslashes(trim($record[$_POST['vat_no']])) : '';
								$company['addressbook_id']  = $_POST['addressbook_id'];

								$ab->add_company($company);

							}
						}
					}
				}
				fclose($fp);
				unlink($_POST['import_file']);
				echo $contacts_import_success;
				echo '<br /><br />';
				$button = new button($cmdOk, "javascript:document.location='".htmlspecialchars($return_to)."'");
				echo $button->get_html();
			}
			break;
	}
}

if ($task == 'upload') {

	$import_file = $GO_CONFIG->tmpdir.'contacts_import_'.$GO_SECURITY->user_id.'.'.$_POST['file_type'];

	if (isset ($_FILES['import_file']) && is_uploaded_file($_FILES['import_file']['tmp_name'])) {
		if (!copy($_FILES['import_file']['tmp_name'], $import_file)) {
			unset ($import_file);
			echo '<p class="Error">'.$fbNoFile.'</p>';
			echo '<br /><br />';
			$button = new button($cmdOk, "javascript:document.location='".htmlspecialchars($return_to)."'");
			echo $button->get_html();

		}
	}
	elseif (!file_exists($import_file)) {
		unset ($import_file);
		echo '<p class="Error">'.$fbNoFile.'</p>';
		echo '<br /><br />';
		$button = new button($cmdOk, "javascript:document.location='".htmlspecialchars($return_to)."'");
		echo $button->get_html();
	}

	if (isset ($import_file)) {
		echo '<input type="hidden" name="addressbook_id" value="'.$addressbook_id.'">';
		echo '<input type="hidden" value="'.$import_file.'" name="import_file" />';
		echo '<input type="hidden" value="'.$_POST['file_type'].'" name="file_type" />';

		switch ($_POST['file_type']) {
			case 'vcf' :
				echo '<table border="0" cellpadding="4" cellspacing="0">';
				echo '<tr><td><h3>Group-Office</h3></td>';
				echo '<td><h3>VCF</h3></td></tr>';
				echo '</table>';
				echo $ab_import_vcf_file_ok.'<br /><br />';
				echo '<table border="0" cellpadding="4" cellspacing="0">';
				echo '<tr><td colspan="2"><br />';
				$button = new button($cmdOk, 'javascript:import_data()');
				echo $button->get_html();
				$button = new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."'");
				echo $button->get_html();
				echo '</td></tr>';
				echo '</table>';
				$tabtable->print_foot();
				require_once ($GO_THEME->theme_path.'footer.inc');
				exit ();
				break;
			case 'csv' :
				$fp = fopen($import_file, 'r');

				if ($fp) {
					//when it's still not exploded then the file is not compatible.
					if (!$record = fgetcsv($fp, 4096, ',', '"')) {
						echo '<p class="Error">'.$contacts_import_incompatible.'</p>';
						$button = new button($cmdOk, "javascript:document.location='".htmlspecialchars($return_to)."'");
						echo $button->get_html();
					} else {
						fclose($fp);
						//echo '<input type="hidden" name="seperator" value="'.$seperator.'">';
						echo '<input type="hidden" name="import_type" value="'.$_POST['import_type'].'">';
						if (isset ($feedback)) {
							echo $feedback;
						}
						echo $contacts_import_feedback.'<br /><br />';

						if ($_POST['import_type'] == 'contacts') {

							$dropbox = new dropbox();
							$required_dropbox = new dropbox();
							$dropbox->add_value('-1', $strNotIncluded);
							for ($n = 0; $n < sizeof($record); $n ++) {
								$required_dropbox->add_value($n, $record[$n]);
								$dropbox->add_value($n, $record[$n]);
							}

							echo '<table border="0" cellpadding="4" cellspacing="0">';
							echo '<tr><td><h3>Group-Office</h3></td>';
							echo '<td><h3>CSV</h3></td></tr>';

							$default = ($key = array_search($strTitle, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$title = isset ($_POST['title']) ? $_POST['title'] : $default;
							echo '<tr><td>'.$strTitle.':</td><td>';
							$dropbox->print_dropbox('title', $title);
							echo '</td></tr>';

							$default = ($key = array_search($strFirstName, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$first_name = isset ($_POST['first_name']) ? $_POST['first_name'] : $default;
							echo '<tr><td>'.$strFirstName.':</td><td>';
							$dropbox->print_dropbox('first_name', $first_name);
							echo '</td></tr>';
							
							$default = ($key = array_search($strMiddleName, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$middle_name = isset ($_POST['middle_name']) ? $_POST['middle_name'] : $default;
							echo '<tr><td>'.$strMiddleName.':</td><td>';
							$dropbox->print_dropbox('middle_name', $middle_name);
							echo '</td></tr>';
							
							$default = ($key = array_search($strLastName, $dropbox->text)) ? $dropbox->value[$key] : -1;		
							$last_name = isset ($_POST['last_name']) ? $_POST['last_name'] : $default;
							echo '<tr><td>'.$strLastName.':</td><td>';
							$required_dropbox->print_dropbox('last_name', $last_name);
							echo '</td></tr>';

							$default = ($key = array_search($strInitials, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$initials = isset ($_POST['initials']) ? $_POST['initials'] : $default;
							echo '<tr><td>'.$strInitials.':</td><td>';
							$dropbox->print_dropbox('initials', $initials);
							echo '</td></tr>';

							$default = ($key = array_search($strSex, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$sex = isset ($_POST['sex']) ? $_POST['sex'] : $default;
							echo '<tr><td>'.$strSex.':</td><td>';
							$dropbox->print_dropbox('sex', $sex);
							echo '</td></tr>';

							$default = ($key = array_search($strBirthday, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$birthday = isset ($_POST['birthday']) ? $_POST['birthday'] : $default;
							echo '<tr><td>'.$strBirthday.':</td><td>';
							$dropbox->print_dropbox('birthday', $birthday);
							echo '</td></tr>';

							$default = ($key = array_search($strAddress, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$address = isset ($_POST['address']) ? $_POST['address'] : $default;
							echo '<tr><td>'.$strAddress.':</td><td>';
							$dropbox->print_dropbox('address', $address);
							echo '</td></tr>';

							$default = ($key = array_search($strAddressNo, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$address_no = isset ($_POST['address_no']) ? $_POST['address_no'] : $default;
							echo '<tr><td>'.$strAddressNo.':</td><td>';
							$dropbox->print_dropbox('address_no', $address_no);
							echo '</td></tr>';

							$default = ($key = array_search($strZip, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$zip = isset ($_POST['zip']) ? $_POST['zip'] : $default;
							echo '<tr><td>'.$strZip.':</td><td>';
							$dropbox->print_dropbox('zip', $zip);
							echo '</td></tr>';

							$default = ($key = array_search($strCity, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$city = isset ($_POST['city']) ? $_POST['city'] : $default;
							echo '<tr><td>'.$strCity.':</td><td>';
							$dropbox->print_dropbox('city', $city);
							echo '</td></tr>';

							$default = ($key = array_search($strState, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$state = isset ($_POST['state']) ? $_POST['state'] : $default;
							echo '<tr><td>'.$strState.':</td><td>';
							$dropbox->print_dropbox('state', $state);
							echo '</td></tr>';

							$default = ($key = array_search($strCountry, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$country = isset ($_POST['country']) ? $_POST['country'] : $default;
							echo '<tr><td>'.$strCountry.':</td><td>';
							$dropbox->print_dropbox('country', $country);
							echo '</td></tr>';

							$default = ($key = array_search($strEmail, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$email = isset ($_POST['email']) ? $_POST['email'] : $default;
							echo '<tr><td>'.$strEmail.':</td><td>';
							$dropbox->print_dropbox('email', $email);
							echo '</td></tr>';

							$default = ($key = array_search($strPhone, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$home_phone = isset ($_POST['home_phone']) ? $_POST['home_phone'] : $default;
							echo '<tr><td>'.$strPhone.':</td><td>';
							$dropbox->print_dropbox('home_phone', $home_phone);
							echo '</td></tr>';

							$default = ($key = array_search($strFax, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$fax = isset ($_POST['fax']) ? $_POST['fax'] : $default;
							echo '<tr><td>'.$strFax.':</td><td>';
							$dropbox->print_dropbox('fax', $fax);
							echo '</td></tr>';

							$default = ($key = array_search($strWorkphone, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$work_phone = isset ($_POST['work_phone']) ? $_POST['work_phone'] : $default;
							echo '<tr><td>'.$strWorkphone.':</td><td>';
							$dropbox->print_dropbox('work_phone', $work_phone);
							echo '</td></tr>';

							$default = ($key = array_search($strWorkFax, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$work_fax = isset ($_POST['work_fax']) ? $_POST['work_fax'] : $default;
							echo '<tr><td>'.$strWorkFax.':</td><td>';
							$dropbox->print_dropbox('work_fax', $work_fax);
							echo '</td></tr>';

							$default = ($key = array_search($strCellular, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$cellular = isset ($_POST['cellular']) ? $_POST['cellular'] : $default;
							echo '<tr><td>'.$strCellular.':</td><td>';
							$dropbox->print_dropbox('cellular', $cellular);
							echo '</td></tr>';
							
							$default = ($key = array_search($strCompany, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$company_name = isset ($_POST['company_name']) ? $_POST['company_name'] : $default;
							echo '<tr><td>'.$strCompany.':</td><td>';
							$dropbox->print_dropbox('company_name', $company_name);
							echo '</td></tr>';
							
							$default = ($key = array_search($strDepartment, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$department = isset ($_POST['department']) ? $_POST['department'] : $default;
							echo '<tr><td>'.$strDepartment.':</td><td>';
							$dropbox->print_dropbox('department', $department);
							echo '</td></tr>';

							$default = ($key = array_search($strFunction, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$function = isset ($_POST['function']) ? $_POST['function'] : $default;
							echo '<tr><td>'.$strFunction.':</td><td>';
							$dropbox->print_dropbox('function', $function);
							echo '</td></tr>';
							echo '</table>';
						} else {
							$dropbox = new dropbox();
							$required_dropbox = new dropbox();
							$dropbox->add_value('', $strNotIncluded);
							for ($n = 0; $n < sizeof($record); $n ++) {
								$dropbox->add_value($n, $record[$n]);
								$required_dropbox->add_value($n, $record[$n]);
							}
							echo '<table border="0" cellpadding="4" cellspacing="0">';
							echo '<tr><td><h3>Group-Office</h3></td>';
							echo '<td><h3>CSV</h3></td></tr>';
						
							$default = ($key = array_search($strName, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$name = isset ($_POST['name']) ? $_POST['name'] : $default;
							echo '<tr><td>'.$strName.':</td><td>';
							$required_dropbox->print_dropbox('name', $name);
							echo '</td></tr>';

							$default = ($key = array_search($strAddress, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$address = isset ($_POST['address']) ? $_POST['address'] : $default;
							echo '<tr><td>'.$strAddress.':</td><td>';
							$dropbox->print_dropbox('address', $address);
							echo '</td></tr>';

							$default = ($key = array_search($strAddressNo, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$address_no = isset ($_POST['address_no']) ? $_POST['address_no'] : $default;
							echo '<tr><td>'.$strAddressNo.':</td><td>';
							$dropbox->print_dropbox('address_no', $address_no);
							echo '</td></tr>';

							$default = ($key = array_search($strZip, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$zip = isset ($_POST['zip']) ? $_POST['zip'] : $default;
							echo '<tr><td>'.$strZip.':</td><td>';
							$dropbox->print_dropbox('zip', $zip);
							echo '</td></tr>';

							$default = ($key = array_search($strCity, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$city = isset ($_POST['city']) ? $_POST['city'] : $default;
							echo '<tr><td>'.$strCity.':</td><td>';
							$dropbox->print_dropbox('city', $city);
							echo '</td></tr>';

							$default = ($key = array_search($strState, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$state = isset ($_POST['state']) ? $_POST['state'] : $default;
							echo '<tr><td>'.$strState.':</td><td>';
							$dropbox->print_dropbox('state', $state);
							echo '</td></tr>';

							$default = ($key = array_search($strCountry, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$country = isset ($_POST['country']) ? $_POST['country'] : $default;
							echo '<tr><td>'.$strCountry.':</td><td>';
							$dropbox->print_dropbox('country', $country);
							echo '</td></tr>';

							$default = ($key = array_search($strPostAddress, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$post_address = isset ($_POST['post_address']) ? $_POST['post_address'] : $default;
							echo '<tr><td>'.$strPostAddress.':</td><td>';
							$dropbox->print_dropbox('post_address', $post_address);
							echo '</td></tr>';

							$default = ($key = array_search($strPostAddressNo, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$post_address_no = isset ($_POST['post_address_no']) ? $_POST['post_address_no'] : $default;
							echo '<tr><td>'.$strPostAddressNo.':</td><td>';
							$dropbox->print_dropbox('post_address_no', $post_address_no);
							echo '</td></tr>';

							$default = ($key = array_search($strPostZip, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$post_zip = isset ($_POST['post_zip']) ? $_POST['post_zip'] : $default;
							echo '<tr><td>'.$strPostZip.':</td><td>';
							$dropbox->print_dropbox('post_zip', $post_zip);
							echo '</td></tr>';

							$default = ($key = array_search($strPostCity, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$post_city = isset ($_POST['post_city']) ? $_POST['post_city'] : $default;
							echo '<tr><td>'.$strPostCity.':</td><td>';
							$dropbox->print_dropbox('post_city', $post_city);
							echo '</td></tr>';

							$default = ($key = array_search($strPostState, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$post_state = isset ($_POST['post_state']) ? $_POST['post_state'] : $default;
							echo '<tr><td>'.$strPostState.':</td><td>';
							$dropbox->print_dropbox('post_state', $post_state);
							echo '</td></tr>';

							$$default = ($key = array_search($strPostCountry, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$post_country = isset ($_POST['post_country']) ? $_POST['post_country'] : $default;
							echo '<tr><td>'.$strPostCountry.':</td><td>';
							$dropbox->print_dropbox('post_country', $post_country);
							echo '</td></tr>';

							$default = ($key = array_search($strEmail, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$email = isset ($_POST['email']) ? $_POST['email'] : $default;
							echo '<tr><td>'.$strEmail.':</td><td>';
							$dropbox->print_dropbox('email', $email);
							echo '</td></tr>';

							$default = ($key = array_search($strPhone, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$phone = isset ($_POST['phone']) ? $_POST['phone'] : $default;
							echo '<tr><td>'.$strPhone.':</td><td>';
							$dropbox->print_dropbox('phone', $phone);
							echo '</td></tr>';

							$default = ($key = array_search($strFax, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$fax = isset ($_POST['fax']) ? $_POST['fax'] : $default;
							echo '<tr><td>'.$strFax.':</td><td>';
							$dropbox->print_dropbox('fax', $fax);
							echo '</td></tr>';

							$default = ($key = array_search($strHomepage, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$homepage = isset ($_POST['homepage']) ? $_POST['homepage'] : $default;
							echo '<tr><td>'.$strHomepage.':</td><td>';
							$dropbox->print_dropbox('homepage', $homepage);
							echo '</td></tr>';

							$default = ($key = array_search($ab_bank_no, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$bank_no = isset ($_POST['bank_no']) ? $_POST['bank_no'] : $default;
							echo '<tr><td>'.$ab_bank_no.':</td><td>';
							$dropbox->print_dropbox('bank_no', $bank_no);
							echo '</td></tr>';

							$default = ($key = array_search($ab_vat_no, $dropbox->text)) ? $dropbox->value[$key] : -1;
							$vat_no = isset ($_POST['vat_no']) ? $_POST['vat_no'] : $default;
							echo '<tr><td>'.$ab_vat_no.':</td><td>';
							$dropbox->print_dropbox('vat_no', $vat_no);
							echo '</td></tr>';
							echo '</table>';
						}

						echo "<tr><td colspan=\"2\"><br />";
						$button = new button($cmdOk, 'javascript:import_data()');
						echo $button->get_html();
						$button = new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."'");
						echo $button->get_html();
						echo "</td></tr>";
						echo "</table>";
						echo "</td></tr>";
						echo "</table>";
						$tabtable->print_foot();
						echo "</td></tr>";
						echo "</table>";
						require_once ($GO_THEME->theme_path.'footer.inc');
						exit ();
					}
				}
				break;
		}
	}
}
echo '</td></tr></table>';
$tabtable->print_foot();
?>
</form>
<?php
require_once ($GO_THEME->theme_path.'footer.inc');
