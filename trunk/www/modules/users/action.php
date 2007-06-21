<?php
/**
* @copyright Intermesh 2007
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');


$result =array();
switch($_REQUEST['task'])
{
	case 'delete':

		$selectedRows = json_decode(smart_stripslashes($_POST['selectedRows']));
		foreach($selectedRows as $note_id)
		{
			$notes->delete_note($note_id);
		}
		$result['success']=true;
		$result['errors']='Notes deleted successfully';

		break;

	case 'save':

		$user['birthday'] = date_to_db_date(smart_addslashes($_POST['birthday']));
		$user['first_name'] = smart_addslashes(trim($_POST['first_name']));
		$user['middle_name'] = smart_addslashes(trim($_POST['middle_name']));
		$user['last_name'] = smart_addslashes(trim($_POST['last_name']));
		$user['initials'] = smart_addslashes($_POST["initials"]);
		$user['title'] = smart_addslashes($_POST["title"]);
		$user['email'] = smart_addslashes($_POST["email"]);
		//$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
		$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
		$user['fax'] = smart_addslashes($_POST["fax"]);
		$user['cellular'] = smart_addslashes($_POST["cellular"]);
		$user['country_id'] = smart_addslashes($_POST["country_id"]);
		$user['state'] = smart_addslashes($_POST["state"]);
		$user['city'] = smart_addslashes($_POST["city"]);
		$user['zip'] = smart_addslashes($_POST["zip"]);
		$user['address'] = smart_addslashes($_POST["address"]);
		$user['address_no'] = smart_addslashes($_POST["address_no"]);
		//$user['department'] = smart_addslashes($_POST["department"]);
		//$user['function'] = smart_addslashes($_POST["function"]);
		$user['company'] = smart_addslashes($_POST["company"]);
		/*$user['work_country_id'] = smart_addslashes($_POST["work_country_id"]);
		$user['work_state'] = smart_addslashes($_POST["work_state"]);
		$user['work_city'] = smart_addslashes($_POST["work_city"]);
		$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
		$user['work_address'] = smart_addslashes($_POST["work_address"]);
		$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
		$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
		$user['homepage'] = smart_addslashes($_POST["homepage"]);
		$user['enabled'] = isset($_POST["enabled"]) ? '1' : '0';*/
		//$user['language'] = isset($_POST['language']) ? smart_stripslashes($_POST['language']) : $GO_CONFIG->language;
		//$user['theme'] = isset($_POST['theme']) ? smart_stripslashes($_POST['theme']) : $GO_CONFIG->theme;
		$user['sex'] = $_POST['sex'];

		$user['id'] = $_POST['user_id'];


		$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);


		if
		(
		empty($user['first_name']) ||
		empty($user['last_name'])
		)
		{
			$result['errors'] = $error_missing_field;
		}elseif(!validate_email($user['email']))
		{
			$result['errors'] = $error_email;
		}elseif($existing_email_user && ($existing_email_user['id'] != $user['id']))
		{
			$result['errors'] =  $error_email_exists;
		}else
		{
			if (!$GO_USERS->update_user($user))
			{
				$result['errors'] = $strSaveError;
			}else
			{
				$result['success']=true;
			}
		}
		break;

	case 'save_permissions':

		$user= $GO_USERS->get_user($_POST['user_id']);

		$user_groups = isset($_POST['user_groups']) ? $_POST['user_groups'] : array();
		$modules_read =  isset($_POST['modules_read']) ? $_POST['modules_read'] : array();
		$modules_write =  isset($_POST['modules_write']) ? $_POST['modules_write'] : array();
		$visible_user_groups = isset($_POST['visible_user_groups']) ? $_POST['visible_user_groups'] : array();

		$GO_MODULES->get_modules();
		while ($GO_MODULES->next_record())
		{
			$could_read = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_read'));
			$can_read =  in_array($GO_MODULES->f('id'), $modules_read);

			if ($could_read && !$can_read)
			{
				$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_read'));
			}

			if ($can_read && !$could_read)
			{
				$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_read'));
			}

			$could_write = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_write'));
			$can_write =  in_array($GO_MODULES->f('id'), $modules_write);

			if ($could_write && !$can_write)
			{
				$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_write'));
			}

			if ($can_write && !$could_write)
			{
				$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_write'));
			}
		}



		$GO_GROUPS->get_groups();
		$groups2 = new $go_groups_class();
		while($GO_GROUPS->next_record())
		{
			$is_in_group = $groups2->is_in_group($user['id'], $GO_GROUPS->f('id'));
			$should_be_in_group = in_array($GO_GROUPS->f('id'), $user_groups);

			if ($is_in_group && !$should_be_in_group)
			{
				$groups2->delete_user_from_group($user['id'], $GO_GROUPS->f('id'));
			}

			if (!$is_in_group && $should_be_in_group)
			{
				$groups2->add_user_to_group($user['id'], $GO_GROUPS->f('id'));
			}



			$group_is_visible = $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']);
			$group_should_be_visible = in_array($GO_GROUPS->f('id'), $visible_user_groups);

			if ($group_is_visible && !$group_should_be_visible)
			{
				$GO_SECURITY->delete_group_from_acl($GO_GROUPS->f('id'), $user['acl_id']);
			}

			if (!$group_is_visible  && $group_should_be_visible)
			{
				$GO_SECURITY->add_group_to_acl($GO_GROUPS->f('id'), $user['acl_id']);
			}
		}
		$result['success']=true;

		break;

	case 'save_lookandfeel':
		$user['id'] = $_REQUEST['user_id'];
		$user['max_rows_list'] = smart_addslashes($_POST['max_rows_list']);
		$user['start_module'] = smart_addslashes($_POST['start_module']);
		$user['sort_name'] =	smart_addslashes($_POST['sort_name']);
		$user['theme'] = smart_addslashes($_POST['theme']);
		$user['use_checkbox_select'] = isset($_POST['use_checkbox_select']) ? '1' : '0';

		if($GO_USERS->update_profile($user))
		{		
			$result['success']=true;
		}else {
			$result['success']=false;
			$result['errors'] = $strSaveError;
		}
		break;
}

echo json_encode($result);