<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('addressbook');
require_once($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
require_once($GLOBALS['GO_MODULES']->modules['addressbook']['class_path'].'addressbook.class.inc.php');
$ab = new addressbook;

$feedback = null;

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : null;

try {
    switch($task) {
	 case 'move_employees':

			$to_company = $ab->get_company($_POST['to_company_id']);

			$ab2 = new addressbook();
			$ab->get_company_contacts($_POST['from_company_id']);
			while ($contact = $ab->next_record()) {
				$up = array(
					'id' => $contact['id'],
					'addressbook_id' => $to_company['addressbook_id'],
					'company_id' => $to_company['id']
				);
				$ab2->update_contact($up, false, $contact);
			}

			$response['success'] = true;

			echo json_encode($response);

			break;

		case 'save_contact':
			$contact_id = isset($_REQUEST['contact_id']) ? ($_REQUEST['contact_id']) : 0;

			if (isset($_POST['delete_photo']) && strcmp($_POST['delete_photo'], 'true') == 0 && $contact_id > 0) {
				@unlink($GLOBALS['GO_CONFIG']->file_storage_path . 'contacts/contact_photos/' . $contact_id . '.jpg');
				$response['image'] = '';
			}

			$credentials = array(
				'first_name', 'middle_name', 'last_name', 'title', 'initials', 'sex', 'email',
				'email2', 'email3', 'home_phone', 'fax', 'cellular', 'comment', 'address', 'address_no',
				'zip', 'city', 'state', 'country', 'company', 'department', 'function', 'work_phone',
				'work_fax', 'addressbook_id', 'salutation', 'iso_address_format'
			);

			$contact_credentials['email_allowed'] = isset($_POST['email_allowed']) ? '1' : '0';
			foreach ($credentials as $key) {
				$contact_credentials[$key] = isset($_REQUEST[$key]) ? $_REQUEST[$key] : '';
			}

			//added is_nummeric becuase extjs sends the text as hiddenName now when no record is found
			$contact_credentials['company_id'] = !empty($_REQUEST['company_id']) && is_numeric($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;


			$addressbook = $ab->get_addressbook($contact_credentials['addressbook_id']);
			if ($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $addressbook['acl_id']) < GO_SECURITY::WRITE_PERMISSION) {
				throw new AccessDeniedException();
			}

			$result['success'] = true;
			$result['feedback'] = $feedback;

			if (!empty($contact_credentials['company']) && empty($contact_credentials['company_id'])) {
				if (!$contact_credentials['company_id'] = $ab->get_company_id_by_name($contact_credentials['company'], $contact_credentials['addressbook_id'])) {
					$company['addressbook_id'] = $contact_credentials['addressbook_id'];
					$company['name'] = $contact_credentials['company']; // bedrijfsnaam
					$company['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
					$contact_credentials['company_id'] = $ab->add_company($company);
				}
			}

			$contact_credentials['birthday'] = Date::to_db_date($_POST['birthday'], false);


			unset($contact_credentials['company']);
			if ($contact_id < 1) {
				$contact_id = $ab->add_contact($contact_credentials, $addressbook);

				if (!$contact_id) {
					$result['feedback'] = $lang['common']['saveError'];
					$result['success'] = false;
				} else {
					$result['contact_id'] = $contact_id;
				}


				$insert = true;
			} else {
				$old_contact = $ab->get_contact($contact_id);
				if (($old_contact['addressbook_id'] != $contact_credentials['addressbook_id']) && $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_contact['acl_id']) < GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}

				$contact_credentials['id'] = $contact_id;

				if (!$ab->update_contact($contact_credentials, $addressbook, $old_contact)) {
					$result['feedback'] = $lang['common']['saveError'];
					$result['success'] = false;
				}

				$insert = false;
			}



			if ($GLOBALS['GO_MODULES']->has_module('customfields')) {
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'] . 'customfields.class.inc.php');
				$cf = new customfields();

				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $contact_id, 2, $_POST, $insert);
			}


			if ($GLOBALS['GO_MODULES']->has_module('mailings')) {
				require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'] . 'mailings.class.inc.php');
				$ml = new mailings();
				$ml2 = new mailings();

				$ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0, 0);
				while ($ml->next_record()) {
					$is_in_group = $ml2->contact_is_in_group($contact_id, $ml->f('id'));
					$should_be_in_group = isset($_POST['mailing_' . $ml->f('id')]);

					if ($is_in_group && !$should_be_in_group) {
						$ml2->remove_contact_from_group($contact_id, $ml->f('id'));
					}
					if (!$is_in_group && $should_be_in_group) {
						$ml2->add_contact_to_mailing_group($contact_id, $ml->f('id'));
					}
				}
			}

			if ($contact_id > 0) {
				if (isset($_FILES['image']['tmp_name'][0]) && is_uploaded_file($_FILES['image']['tmp_name'][0])) {
					move_uploaded_file($_FILES['image']['tmp_name'][0], $GLOBALS['GO_CONFIG']->tmpdir . $_FILES['image']['name'][0]);
					$tmp_file = $GLOBALS['GO_CONFIG']->tmpdir . $_FILES['image']['name'][0];

					$result['image'] = $ab->save_contact_photo($tmp_file, $contact_id);
				}
			}

			$GLOBALS['GO_EVENTS']->fire_event('save_contact', array($contact_credentials));


			echo json_encode($result);
			break;
		case 'save_company':
			$company_id = isset($_REQUEST['company_id']) ? ($_REQUEST['company_id']) : 0;

			$credentials = array(
				'addressbook_id', 'name', 'name2', 'address', 'address_no', 'zip', 'city', 'state', 'country', 'iso_address_format',
				'post_address', 'post_address_no', 'post_city', 'post_state', 'post_country', 'post_zip', 'post_iso_address_format', 'phone',
				'fax', 'email', 'homepage', 'bank_no', 'vat_no', 'comment','iban','crn'
			);

			$company_credentials['email_allowed'] = isset($_POST['email_allowed']) ? '1' : '0';
			foreach ($credentials as $key) {
				$company_credentials[$key] = isset($_REQUEST[$key]) ? ($_REQUEST[$key]) : null;
			}

			if (!empty($company_credentials['homepage']) && !strpos($company_credentials['homepage'], '://')) {
				$company_credentials['homepage'] = 'http://' . $company_credentials['homepage'];
			}

			$addressbook = $ab->get_addressbook($company_credentials['addressbook_id']);

			if ($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $addressbook['acl_id']) < GO_SECURITY::WRITE_PERMISSION) {
				throw new AccessDeniedException();
			}

			if ($company_id > 0) {
				$old_company = $ab->get_company($company_id);

				if (($old_company['addressbook_id'] != $company_credentials['addressbook_id']) && $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_company['acl_id']) < GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}
			}


			$result['success'] = true;
			$result['feedback'] = $feedback;

			if ($company_id < 1) {
				# insert
				$result['company_id'] = $company_id = $ab->add_company($company_credentials, $addressbook);
				$insert = true;
			} else {
				# update
				$company_credentials['id'] = $company_id;

				$ab->update_company($company_credentials, $addressbook, $old_company);
				$insert = false;
			}

			if (isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'] . 'customfields.class.inc.php');
				$cf = new customfields();

				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $company_id, 3, $_POST, $insert);
			}


			if ($GLOBALS['GO_MODULES']->has_module('mailings')) {
				require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'] . 'mailings.class.inc.php');
				$ml = new mailings();
				$ml2 = new mailings();

				$ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0, 0);
				while ($ml->next_record()) {
					$is_in_group = $ml2->company_is_in_group($company_id, $ml->f('id'));
					$should_be_in_group = isset($_POST['mailing_' . $ml->f('id')]);

					if ($is_in_group && !$should_be_in_group) {
						$ml2->remove_company_from_group($company_id, $ml->f('id'));
					}
					if (!$is_in_group && $should_be_in_group) {
						$ml2->add_company_to_mailing_group($company_id, $ml->f('id'));
					}
				}
			}

			$GLOBALS['GO_EVENTS']->fire_event('save_company', array($company_credentials));


			echo json_encode($result);
			break;

		case 'save_addressbook':
			$addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;

			$name = isset($_REQUEST['name']) ? ($_REQUEST['name']) : null;

			$result['success'] = true;
			$result['feedback'] = $feedback;

			if (empty($name)) {
				throw new Exception($lang['common']['missingField']);
			} else {
				//$existing_ab = $ab->get_addressbook_by_name($name);


				if ($addressbook_id < 1) {

					if (!$GLOBALS['GO_MODULES']->modules['addressbook']['write_permission']) {
						throw new AccessDeniedException();
					}

					$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : $GLOBALS['GO_SECURITY']->user_id;

					$addressbook = $ab->add_addressbook($user_id, $name, $_REQUEST['default_iso_address_format'], $_REQUEST['default_salutation']);
					$result['addressbook_id'] = $addressbook['addressbook_id'];
					$result['acl_id'] = $addressbook['acl_id'];
				} else {


					$addressbook['id'] = $addressbook_id;

					if (isset($_REQUEST['user_id']) && $GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id))
						$addressbook['user_id'] = $_REQUEST['user_id'];

					$addressbook['default_salutation'] = $_REQUEST['default_salutation'];
					$addressbook['default_iso_address_format'] = $_REQUEST['default_iso_address_format'];

					$addressbook['name'] = $name;
					$ab->update_addressbook($addressbook);
				}
			}

			echo json_encode($result);
			break;
	case 'upload':
	    $addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;
	    $import_filetype = isset($_REQUEST['import_filetype']) ? ($_REQUEST['import_filetype']) : null;
	    $import_file = isset($_FILES['import_file']['tmp_name']) ? ($_FILES['import_file']['tmp_name']) : null;
	    $separator	= isset($_REQUEST['separator']) ? ($_REQUEST['separator']) : ',';
	    $quote	= isset($_REQUEST['quote']) ? ($_REQUEST['quote']) : '"';

	    $result['success'] = true;

	    $_SESSION['GO_SESSION']['addressbook']['import_file'] = $GLOBALS['GO_CONFIG']->tmpdir.uniqid(time());
	    go_debug($import_file);

	    if(!move_uploaded_file($import_file, $_SESSION['GO_SESSION']['addressbook']['import_file'])) {
		throw new Exception('Could not move '.$import_file);
	    }
	    File::convert_to_utf8($_SESSION['GO_SESSION']['addressbook']['import_file']);

	    switch($import_filetype) {
		case 'vcf':

			ini_set('max_execution_time', 360);
			ini_set('memory_limit', '256M');

		    require_once ($GLOBALS['GO_MODULES']->path."classes/vcard.class.inc.php");
		    $vcard = new vcard();
		    $result['success'] = $vcard->import($_SESSION['GO_SESSION']['addressbook']['import_file'], $GLOBALS['GO_SECURITY']->user_id, ($_POST['addressbook_id']));
		    break;
		case 'csv':

		    $fp = fopen($_SESSION['GO_SESSION']['addressbook']['import_file'], 'r');

		    if (!$fp || !$addressbook = $ab->get_addressbook($addressbook_id)) {
				unlink($_SESSION['GO_SESSION']['addressbook']['import_file']);
				throw new Exception($lang['comon']['selectError']);
		    } else {
				//fgets($fp, 4096);

				if (!$record = fgetcsv($fp, 4096, $separator, $quote)) {
					throw new Exception('Could not read import file');
				}

				fclose($fp);

				$result['list_keys'] = array();
				$result['list_keys'][]=array('id' => -1, 'name' => $lang['addressbook']['notIncluded']);
				for ($i = 0; $i < sizeof($record); $i++) {
					$result['list_keys'][]=array('id' => $i, 'name' => $record[$i]);
				}

		    }
		    break;
	    }



	    echo json_encode($result);
	    break;
	case'import':

	    ini_set('max_execution_time', 360);

	    $addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;
	    $separator	= isset($_REQUEST['separator']) ? ($_REQUEST['separator']) : ',';
	    $quote	= isset($_REQUEST['quote']) ? ($_REQUEST['quote']) : '"';
	    $import_type = isset($_REQUEST['import_type']) ? ($_REQUEST['import_type']) : '';
	    $import_filetype = isset($_REQUEST['import_filetype']) ? ($_REQUEST['import_filetype']) : '';

	    $addressbook = $ab->get_addressbook($addressbook_id);
					ini_set('memory_limit', '256M');

	    $result['success'] = true;
	    $result['feedback'] = $feedback;

	    switch($import_filetype) {
		case 'vcf':

		    break;
		case 'csv':

		    if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
			require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
			$cf = new customfields();
			$company_customfields = $cf->get_authorized_fields($GLOBALS['GO_SECURITY']->user_id, 3);
			$contact_customfields = $cf->get_authorized_fields($GLOBALS['GO_SECURITY']->user_id, 2);
		    }

		    $fp = fopen($_SESSION['GO_SESSION']['addressbook']['import_file'], "r");

		    if (!$fp || !$addressbook = $ab->get_addressbook($addressbook_id)) {
			unlink($_SESSION['GO_SESSION']['addressbook']['import_file']);
			throw new Exception($lang['comon']['selectError']);
		    }

		    fgets($fp, 4096);
		    while (!feof($fp)) {
			$record = fgetcsv($fp, 4096, $separator, $quote);

			$new_id=0;

			if ($import_type == 'contacts') {
			    if ((isset ($record[$_POST['first_name']]) && $record[$_POST['first_name']] != "") || (isset ($record[$_POST['last_name']]) && $record[$_POST['last_name']] != '')) {
				$contact=array();
				$contact['email_allowed']='1';
				$contact['title'] = isset ($record[$_POST['title']]) ? trim($record[$_POST['title']]) : '';
				$contact['first_name'] = isset ($record[$_POST['first_name']]) ? trim($record[$_POST['first_name']]) : '';
				$contact['middle_name'] = isset ($record[$_POST['middle_name']]) ? trim($record[$_POST['middle_name']]) : '';
				$contact['last_name'] = isset ($record[$_POST['last_name']]) ? trim($record[$_POST['last_name']]) : '';
				$contact['initials'] = isset ($record[$_POST['initials']]) ? trim($record[$_POST['initials']]) : '';
				$contact['sex'] = isset ($record[$_POST['sex']]) ? trim($record[$_POST['sex']]) : 'M';
				$contact['birthday'] = isset ($record[$_POST['birthday']]) ? trim($record[$_POST['birthday']]) : '';
				$contact['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
				$contact['email2'] = isset ($record[$_POST['email2']]) ? String::get_email_from_string($record[$_POST['email2']]) : '';
				$contact['email3'] = isset ($record[$_POST['email3']]) ? String::get_email_from_string($record[$_POST['email3']]) : '';
				$contact['work_phone'] = isset ($record[$_POST['work_phone']]) ? trim($record[$_POST['work_phone']]) : '';
				$contact['home_phone'] = isset ($record[$_POST['home_phone']]) ? trim($record[$_POST['home_phone']]) : '';
				$contact['fax'] = isset ($record[$_POST['fax']]) ? trim($record[$_POST['fax']]) : '';
				$contact['work_fax'] = isset ($record[$_POST['work_fax']]) ? trim($record[$_POST['work_fax']]) : '';
				$contact['cellular'] = isset ($record[$_POST['cellular']]) ? trim($record[$_POST['cellular']]) : '';
				$contact['country'] = isset ($record[$_POST['country']]) ? trim($record[$_POST['country']]) : '';
				$contact['state'] =  isset($record[$_POST['state']]) ? trim($record[$_POST['state']]) : '';
				$contact['city'] = isset ($record[$_POST['city']]) ? trim($record[$_POST['city']]) : '';
				$contact['zip'] = isset ($record[$_POST['zip']]) ? trim($record[$_POST['zip']]) : '';
				$contact['address'] = isset ($record[$_POST['address']]) ? trim($record[$_POST['address']]) : '';
				$contact['address_no'] = isset ($record[$_POST['address_no']]) ? trim($record[$_POST['address_no']]) : '';
				$company_name = isset ($record[$_POST['company_name']]) ? trim($record[$_POST['company_name']]) : '';
				$company_name2 = isset ($record[$_POST['company_name2']]) ? trim($record[$_POST['company_name2']]) : '';
				$contact['department'] = isset ($record[$_POST['department']]) ? trim($record[$_POST['department']]) : '';
				$contact['function'] = isset ($record[$_POST['function']]) ? trim($record[$_POST['function']]) : '';
				$contact['salutation'] = isset ($record[$_POST['salutation']]) ? trim($record[$_POST['salutation']]) : '';
				$contact['comment'] = isset ($record[$_POST['comment']]) ? trim($record[$_POST['comment']]) : '';

				if ($company_name != '') {
				    $contact['company_id'] = $ab->get_company_id_by_name($company_name, $addressbook_id);

				    if(!$contact['company_id']) {
					$company=array();
					$company['addressbook_id']=$addressbook_id;
					$company['name']=$company_name;
					$company['name2']=$company_name2;

					$contact['company_id']=$ab->add_company($company);
				    }
				}else {
				    $contact['company_id']=0;
				}

				$contact['addressbook_id'] = $addressbook_id;
				$new_id=$ab->add_contact($contact, $addressbook);
				$new_type=2;
			    }
			} else {
			    if (isset ($record[$_POST['name']]) && $record[$_POST['name']] != '') {
				$company=array();
				$company['name'] = trim($record[$_POST['name']]);
				$company['name2'] = trim($record[$_POST['name2']]);

				//if (!$ab->get_company_by_name($_POST['addressbook_id'], $company['name']))
				{

				    $company['email_allowed']='1';
				    $company['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
				    $company['phone'] = isset ($record[$_POST['phone']]) ? trim($record[$_POST['phone']]) : '';
				    $company['fax'] = isset ($record[$_POST['fax']]) ? trim($record[$_POST['fax']]) : '';
				    $company['country'] = isset ($record[$_POST['country']]) ? trim($record[$_POST['country']]) : '';
				    $company['state'] = isset ($record[$_POST['state']]) ? trim($record[$_POST['state']]) : '';
				    $company['city'] = isset ($record[$_POST['city']]) ? trim($record[$_POST['city']]) : '';
				    $company['zip'] = isset ($record[$_POST['zip']]) ? trim($record[$_POST['zip']]) : '';
				    $company['address'] = isset ($record[$_POST['address']]) ? trim($record[$_POST['address']]) : '';
				    $company['address_no'] = isset ($record[$_POST['address_no']]) ? trim($record[$_POST['address_no']]) : '';
				    $company['post_country'] = isset ($record[$_POST['post_country']]) ? trim($record[$_POST['post_country']]) : '';
				    $company['post_state'] = isset ($record[$_POST['post_state']]) ? trim($record[$_POST['post_state']]) : '';
				    $company['post_city'] = isset ($record[$_POST['post_city']]) ? trim($record[$_POST['post_city']]) : '';
				    $company['post_zip'] = isset ($record[$_POST['post_zip']]) ? trim($record[$_POST['post_zip']]) : '';
				    $company['post_address'] = isset ($record[$_POST['post_address']]) ? trim($record[$_POST['post_address']]) : '';
				    $company['post_address_no'] = isset ($record[$_POST['post_address_no']]) ? trim($record[$_POST['post_address_no']]) : '';
				    $company['homepage'] = isset ($record[$_POST['homepage']]) ? trim($record[$_POST['homepage']]) : '';
				    $company['bank_no'] = isset ($record[$_POST['bank_no']]) ? trim($record[$_POST['bank_no']]) : '';
				    $company['vat_no'] = isset ($record[$_POST['vat_no']]) ? trim($record[$_POST['vat_no']]) : '';
				    $company['addressbook_id']  = $_POST['addressbook_id'];

				    $new_id=$ab->add_company($company, $addressbook);
				    $new_type=3;
				}
			    }
			}

			if($new_id>0) {
			    if(isset($cf)) {
				$customfields = $new_type==2 ? $contact_customfields : $company_customfields;
				$cf_record=array('link_id'=>$new_id);
				foreach($customfields as $field) {
				    if(isset($_POST[$field['dataname']]) && isset($record[$_POST[$field['dataname']]]))
					$cf_record[$field['dataname']]=$record[$_POST[$field['dataname']]];
				}
				$cf->insert_row('cf_'.$new_type,$cf_record);
			    }
			}
		    }
		    break;
	    }
	    echo json_encode($result);
	    break;


	   case 'drop_contact':

			$contacts = json_decode(($_POST['items']));
			$abook_id = isset($_REQUEST['book_id']) ? ($_REQUEST['book_id']) : 0;

			$addressbook = $ab->get_addressbook($abook_id);
			if ($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $addressbook['acl_id']) < 3) {
				throw new AccessDeniedException();
			}

			$result['success'] = true;
			$result['feedback'] = $feedback;

			for ($i = 0; $i < count($contacts); $i++) {
				$contact['id'] = $contacts[$i];
				if ($contact['id'] > 0) {
					$old_contact = $ab->get_contact($contact['id']);
					if (($old_contact['addressbook_id'] != $abook_id) && $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_contact['acl_id']) < 2) {
						throw new AccessDeniedException();
					}
					$contact['addressbook_id'] = $abook_id;
					$contact['company_id'] = $old_contact['company_id'];
					$contact['last_name'] = $old_contact['last_name'];

					if (!$ab->update_contact($contact, $addressbook)) {
						$result['feedback'] = $lang['common']['saveError'];
						$result['success'] = false;
					}
				}
			}
			echo json_encode($result);
			break;

	case 'drop_company':

			$companies = json_decode(($_POST['items']));
			$abook_id = isset($_REQUEST['book_id']) ? ($_REQUEST['book_id']) : 0;

			$addressbook = $ab->get_addressbook($abook_id);
			if ($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $addressbook['acl_id']) < 3) {
				throw new AccessDeniedException();
			}

			$result['success'] = true;
			$result['feedback'] = $feedback;

			for ($i = 0; $i < count($companies); $i++) {
				$company['id'] = $companies[$i];
				if ($company['id'] > 0) {
					$old_company = $ab->get_company($company['id']);
					if (($old_company['addressbook_id'] != $abook_id) && $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_company['acl_id']) < 3) {
						throw new AccessDeniedException();
					}

					$company['addressbook_id'] = $abook_id;
					$company['name'] = $old_company['name'];
					if (!$ab->update_company($company, $addressbook)) {
						$result['feedback'] = $lang['common']['saveError'];
						$result['success'] = false;
					}
				}
			}
			echo json_encode($result);
			break;

	case 'save_sql':

	    $ab->save_sql(array('user_id'=>$GLOBALS['GO_SECURITY']->user_id, 'sql'=>$_POST['sql'],'name'=>$_POST['name'], 'companies'=>$_POST['companies']));
	    $response['success']=true;
	    echo json_encode($response);

	    break;

	case 'merge_email':

	    $email = (isset($_REQUEST['email']) && $_REQUEST['email']) ? $_REQUEST['email'] : '';
	    $replace_email = (isset($_REQUEST['replace_email']) && $_REQUEST['replace_email']) ? $_REQUEST['replace_email'] : '';
	    $contact_id = (isset($_REQUEST['contact_id']) && $_REQUEST['contact_id']) ? $_REQUEST['contact_id'] : 0;

	    $response['success'] = false;
	    if($email && $contact_id)
	    {
		$contact = $ab->get_contact($contact_id);
		$email_addresses = array($contact['email'], $contact['email2'], $contact['email3']);
		
		if(!$replace_email)
		{		    		    		    
		    if(!in_array($email, $email_addresses))
		    {
			$index = array_search('', $email_addresses);
			if($index === false)
			{
			    $response['addresses'] = array(array('name' => $contact['email']), array('name' => $contact['email2']), array('name' => $contact['email3']));
			    $response['contact_name'] = String::format_name($contact);
			}else
			{
			    $field = ($index == 0) ? 'email' : 'email'.($index+1);
			    
			    $ab->update_contact(array('id' => $contact_id, $field => $email));
			}
			$response['success'] = true;
		    }else
		    {
			$response['feedback'] = $lang['addressbook']['emailAlreadyExists'];
		    }
		}else
		{
		    $index = array_search($replace_email, $email_addresses);
		    if($index === false)
		    {
			$response['feedback'] = $lang['addressbook']['emailDoesntExists'];			
		    }else
		    {
			$field = ($index == 0) ? 'email' : 'email'.($index+1);

			$ab->update_contact(array('id' => $contact_id, $field => $email));
			$response['success']=true;
		    }		        
		}		
	    }

	    echo json_encode($response);	    
	    break;

		case 'save_addressbook_limits':

			if (!$GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id))
				throw AccessDeniedException();

			if (empty($_REQUEST['addressbook_id'])) {
				require_once($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
				throw new Exception($lang['addressbook']['no_addressbook_id']);
			}

			$addressbook_id = $_REQUEST['addressbook_id'];

			$ab->toggle_addressbook_limit($addressbook_id, !empty($_POST['limit_contacts']), 2);
			$ab->toggle_addressbook_limit($addressbook_id, !empty($_POST['limit_companies']), 3);

			$ab->clear_addressbook_cf_categories($addressbook_id,2);
			$ab->clear_addressbook_cf_categories($addressbook_id,3);

			foreach($_POST as $k=>$v)
				if (substr($k,0,4)=='cat_')
					$ab->add_addressbook_cf_category($addressbook_id,substr($k,6),substr($k,4,1));

			$response['success']=true;

			break;
    }
}
catch(Exception $e) {
    $response['feedback']=$e->getMessage();
    $response['success']= false;

    echo json_encode($response);

}
?>