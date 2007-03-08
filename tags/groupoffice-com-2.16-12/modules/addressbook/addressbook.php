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
$post_action = isset($post_action) ? $post_action : '';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));

load_basic_controls();


//load contact management class
require_once($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();

$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$addressbook_id = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : 0;

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$user_id = (isset($_POST['user_id']) && $_POST['user_id'] > 0) ? $_POST['user_id'] : $GO_SECURITY->user_id;


switch($task)
{
	case 'save':
		$name = smart_addslashes(trim($_POST['name']));
		if ($name == '')
		{
			$feedback = $error_missing_field;
		}else
		{
			if ($addressbook_id > 0)
			{
				$existing_addressbook = $ab->get_addressbook_by_name($name);
				if($existing_addressbook && $existing_addressbook['id'] != $addressbook_id)
				{
					$feedback = $ab_addressbook_exists;
				}else
				{
					$addressbook = $ab->get_addressbook($addressbook_id);
					if($addressbook['user_id'] != $user_id)
					{
						$GO_SECURITY->chown_acl($addressbook['acl_read'], $user_id);
						$GO_SECURITY->chown_acl($addressbook['acl_write'], $user_id);
					}

					$ab->update_addressbook($_POST['addressbook_id'], $user_id, $name);
					if($_POST['close'] == 'true')
					{
						header('Location: '.$return_to);
						exit();
					}
				}
			}else
			{
				if($ab->get_addressbook_by_name($name))
				{
					$feedback = $ab_addressbook_exists;
				}elseif(!$addressbook_id = $ab->add_addressbook($user_id, $name))
				{
					$feedback = $strSaveError;
				}elseif($_POST['close'] == 'true')
				{
					header('Location: '.$return_to);
					exit();
				}
			}
		}
		break;

	case 'export':
		$file_type = isset($_REQUEST['file_type']) ? $_REQUEST['file_type'] : 'csv';
		$addressbook = $ab->get_addressbook($addressbook_id);

		$browser = detect_browser();
		header("Content-type: text/x-csv;charset=".$charset);
		header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
		$export_type = isset($_POST['export_type']) ? " - ".$_POST['export_type'] : '';
		if ($browser['name'] == 'MSIE')
		{
			header('Content-Disposition: inline; filename="'.$addressbook['name'].$export_type.'.'.$file_type.'"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}else
		{
			header('Pragma: no-cache');
			header('Content-Disposition: attachment; filename="'.$addressbook['name'].$export_type.'.'.$file_type.'"');
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
				$headings = array($strTitle, $strFirstName, $strMiddleName, $strLastName, $strInitials, $strSex, $strBirthday, $strEmail, $strCountry, $strState, $strCity, $strZip, $strAddress, $strAddressNo, $strPhone, $strWorkphone, $strFax, $strWorkFax, $strCellular, $strCompany, $strDepartment, $strFunction, $ab_comment, $contacts_group);
				$headings = $quote.implode($quote.$seperator.$quote, $headings).$quote;
				echo $headings;
				echo $crlf;

				$ab->get_contacts_for_export($_POST['addressbook_id']);
				while ($ab->next_record())
				{
					$record = array($ab->f("title"), $ab->f("first_name"),$ab->f("middle_name"), $ab->f("last_name"), $ab->f("initials"), $ab->f("sex"), $ab->f('birthday'), $ab->f("email"), $ab->f("country"), $ab->f("state"), $ab->f("city"), $ab->f("zip"), $ab->f("address"), $ab->f("address_no"), $ab->f("home_phone"), $ab->f("work_phone"), $ab->f("fax"), $ab->f("work_fax"), $ab->f("cellular"), $ab->f("company"), $ab->f("department"), $ab->f("function"), $ab->f("comment"), $ab->f("group_name"));
					$record = $quote.implode($quote.$seperator.$quote, $record).$quote;
					echo $record;
					echo $crlf;
				}
			}else
			{
				$headings = array($strName, $strCountry, $strState, $strCity, $strZip, $strAddress, $strAddressNo, $strPostCountry, $strPostState, $strPostCity, $strPostZip, $strPostAddress, $strPostAddressNo,  $strEmail, $strPhone, $strFax, $strHomepage, $ab_bank_no, $ab_vat_no);
				$headings = $quote.implode($quote.$seperator.$quote, $headings).$quote;
				echo $headings;
				echo $crlf;

				$ab->get_companies($_POST['addressbook_id']);

				while($ab->next_record())
				{
					$record = array($ab->f("name"), $ab->f("country"), $ab->f("state"), $ab->f("city"), $ab->f("zip"), $ab->f("address"), $ab->f("address_no"), $ab->f("post_country"), $ab->f("post_state"), $ab->f("post_city"), $ab->f("post_zip"), $ab->f("post_address"), $ab->f("post_address_no"),$ab->f("email"), $ab->f("phone"), $ab->f("fax"), $ab->f("homepage"), $ab->f("bank_no"), $ab->f('vat_no'));
					$record = $quote.implode($quote.$seperator.$quote, $record).$quote;
					echo $record;
					echo $crlf;
				}
			}
		}
		exit();
		break;
}

if ($addressbook_id > 0 && $addressbook = $ab->get_addressbook($addressbook_id))
{
	if (!$write_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_write']))
	{
		$read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $addressbook['acl_read']);
	}
	$name = isset($name) ? $name : $addressbook['name'];
	$user_id = $addressbook['user_id'];

	$tabstrip = new tabstrip('addressbook', $name);
	$tabstrip->set_attribute('style','width:100%');
	$tabstrip->set_return_to(htmlspecialchars($return_to));

	$tabstrip->add_tab('name', $strProperties);
	if ($write_permission)
	{
		$tabstrip->add_tab('import', $contacts_import);
	}
	$tabstrip->add_tab('export', $contacts_export);
	if($GO_MODULES->write_permission)
	{
		$tabstrip->add_tab('read_permissions', $strReadRights);
		$tabstrip->add_tab('write_permissions', $strWriteRights);
	}
}else
{
	$tabstrip = new tabstrip('addressbook', $ab_new_ab);
	$tabstrip->set_attribute('style','width:100%');
	$tabstrip->set_return_to(htmlspecialchars($return_to));
	$write_permission = true;

	$user_id = $GO_SECURITY->user_id;
}

if (!$write_permission && !$read_permission)
{
	header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit();
}





$form = new form('addressbook_form');
$form->add_html_element(new input('hidden','task', '', false));
$form->add_html_element(new input('hidden','close', 'false'));
$form->add_html_element(new input('hidden','addressbook_id', $addressbook_id, false));
$form->add_html_element(new input('hidden','return_to', $return_to));
$form->add_html_element(new input('hidden','link_back', $link_back));

if(isset($feedback))
{
	$p = new html_element('p',$feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

switch($tabstrip->get_active_tab_id())
{
	case 'read_permissions':
		$tabstrip->innerHTML .= get_acl($addressbook["acl_read"]);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	case 'write_permissions':
		$tabstrip->innerHTML .= get_acl($addressbook["acl_write"]);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;


	case 'import':
		require_once('import.inc');
		break;

	case 'export':
		require_once('export.inc');
		break;

	default:
		$GO_HEADER['body_arguments'] = 'onload="javascript:document.addressbook_form.name.focus();"';

		$name = isset($name) ? htmlspecialchars(stripslashes($name)) : '';

		$table = new table();

		if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
		{
			load_control('user_autocomplete');
			$user_autocomplete=new user_autocomplete('user_id',$addressbook['user_id'],'0',$link_back);			
			
			$row = new table_row();
			$row->add_cell(new table_cell($strOwner.':'));
			$row->add_cell(new table_cell($user_autocomplete->get_html()));
			$table->add_row($row);
		}

		$row = new table_row();
		$row->add_cell(new table_cell($strName.'*: '));
		$input = new input('text', 'name',$name);
		$input->set_attribute('maxlength','100');
		$input->set_attribute('style', 'width:300px');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);

		$tabstrip->add_html_element($table);

		if ($write_permission)
		{
			$tabstrip->add_html_element(new button($cmdOk, "javascript:ok_addressbook()"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:apply_addressbook()"));
		}
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;
}
$form->add_html_element($tabstrip);

require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
?>
<script type="text/javascript" language="javascript">

function ok_addressbook()
{
	document.forms[0].close.value = 'true';
	document.forms[0].task.value = 'save';
	document.forms[0].submit();
}
function apply_addressbook()
{
	document.forms[0].task.value = 'save';
	document.forms[0].submit();
}

function copy_acl(task)
{
	document.forms[0].task.value = task;
	document.forms[0].submit();
}

function upload()
{
	document.forms[0].task.value="upload";
	var status = null;
	if (status = get_object("status"))
	{
		status.innerHTML = "<?php echo $fbPleaseWait; ?>";
	}
	document.forms[0].action='import.php';
	document.forms[0].submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
