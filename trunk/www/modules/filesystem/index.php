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

$popup_feedback = '';
$mode = isset ($mode) ? $mode : 'normal';

function access_denied_box($file) {
	global $strAccessDenied;
	$string = "<script type=\"text/javascript\" language=\"javascript\">\n";
	$string .= "alert('".$strAccessDenied.": ".basename($file)."');\n";
	$string .= "</script>\n";
	return $string;
}

function feedback($text) {
	$string = "<script type=\"text/javascript\" language=\"javascript\">\n";
	$string .= 'alert("'.$text.'");';
	$string .= "</script>\n";
	return $string;
}
//set umask to 000 and remember the old umaks to reset it below
//umask must be 000 to create 777 files and folders
$old_umask = umask(000);

//basic group-office authentication
if (!defined('GO_LOADED')) {
	require_once ("../../Group-Office.php");
}
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');
require_once ($GO_LANGUAGE->get_language_file('filesystem'));



$GO_HANDLER = isset ($GO_HANDLER) ? $GO_HANDLER : '';
$GO_MULTI_SELECT = isset ($GO_MULTI_SELECT) ? $GO_MULTI_SELECT : true;

$target_frame = isset ($target_frame) ? $target_frame : '_self';

//set path to browse
$home_path = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'];
if (!isset ($_SESSION['GO_FILESYSTEM_PATH'])) {
	if (file_exists($home_path) || mkdir_recursive($home_path, $GO_CONFIG->create_mode)) {
		$_SESSION['GO_FILESYSTEM_PATH'] = $home_path;
	} else {
		die('Failed creating home directory. Check server configuration. See if "'.$GO_CONFIG->file_storage_path.'" exists and is writable for the webserver.');
	}
}

$treeview = isset($treeview) ? $treeview : true;

require($GO_MODULES->modules['filesystem']['class_path'].'filesystem_view.class.inc');
$fv = new filesystem_view('fs_list',  $_SESSION['GO_FILESYSTEM_PATH'], $GO_HANDLER, true,'0',$treeview);

if(isset($_REQUEST['path']) && file_exists(smart_stripslashes($_REQUEST['path'])))
{
	$fv->set_path(smart_stripslashes($_REQUEST['path']));
}

$urlencoded_path = urlencode($fv->path);
$return_to_path = isset ($_REQUEST['return_to_path']) ? smart_stripslashes($_REQUEST['return_to_path']) : $fv->path;
$return_to_path = is_dir($return_to_path) ? $return_to_path : dirname($return_to_path);

//create filesystem  object
require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();


//define task to peform
$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$_SESSION['cut_files'] = isset ($_SESSION['cut_files']) ? $_SESSION['cut_files'] : array ();
$_SESSION['copy_files'] = isset ($_SESSION['copy_files']) ? $_SESSION['copy_files'] : array ();

//vars used to remember files that are to be overwritten or not
$overwrite_destination_path = isset ($_POST['overwrite_destination_path']) ? smart_stripslashes($_POST['overwrite_destination_path']) : '';
$overwrite_source_path = isset ($_POST['overwrite_source_path']) ? smart_stripslashes($_POST['overwrite_source_path']) : '';
$overwrite_all = (isset ($_POST['overwrite_all']) && $_POST['overwrite_all'] == 'true') ? 'true' : 'false';
$overwrite = isset ($_POST['overwrite']) ? $_POST['overwrite'] : $overwrite_all;

//check read permissions and remember last browsed path
$read_permission = $fs->has_read_permission($GO_SECURITY->user_id, $fv->path);
$write_permission = $fs->has_write_permission($GO_SECURITY->user_id, $fv->path);

if (!$read_permission && !$write_permission) {
	$_SESSION['GO_FILESYSTEM_PATH'] = $home_path;
	$task = 'access_denied';
} else {
	$_SESSION['GO_FILESYSTEM_PATH'] = is_dir($fv->path) ? $fv->path : dirname($fv->path);
}



//cut paste or copy before output has started
switch ($task) {

	case 'cut' :
		$_SESSION['cut_files'] = isset ($fv->fsl->selected) ? $fv->fsl->selected : array ();
		$_SESSION['copy_files'] = array ();
		break;

	case 'copy' :
		$_SESSION['copy_files'] = isset ($fv->fsl->selected) ? $fv->fsl->selected : array ();
		$_SESSION['cut_files'] = array ();
		break;

	case 'paste' :

		while ($file = smart_stripslashes(array_shift($_SESSION['cut_files']))) {

			if ($file != $fv->path.'/'.basename($file)) {
				if (!$fs->has_write_permission($GO_SECURITY->user_id, $file)) {
					$popup_feedback .= access_denied_box($file);
					break;
				}
				elseif (!$fs->has_write_permission($GO_SECURITY->user_id, $fv->path)) {
					$popup_feedback .= access_denied_box($fv->path);
					break;
				}
				elseif (file_exists($fv->path.'/'.basename($file))) {
					if ($overwrite_destination_path == $fv->path.'/'.basename($file) || $overwrite_all == 'true') {
						if ($overwrite == "true") {
							$fs->move($file, $fv->path.'/'.basename($file));
						}
					} else {
						array_unshift($_SESSION['cut_files'], $file);
						$overwrite_source_path = $file;
						$overwrite_destination_path = $fv->path.'/'.basename($file);
						$task = 'overwrite';
						break;
					}
				} else {

					if(!$fs->move($file, $fv->path.'/'.basename($file)))
					{
						$feedback = $fs_inssufficient_diskspace;
					}

				}
			}
		}
		while ($file = smart_stripslashes(array_shift($_SESSION['copy_files']))) {
			if ($file != $fv->path.'/'.basename($file)) {
				if (!$fs->has_read_permission($GO_SECURITY->user_id, $file)) {
					$popup_feedback .= access_denied_box($file);
					break;
				}
				elseif (!$fs->has_write_permission($GO_SECURITY->user_id, $fv->path)) {
					$popup_feedback .= access_denied_box($fv->path);
					break;
				}
				elseif (file_exists($fv->path.'/'.basename($file))) {
					if ($overwrite_destination_path == $fv->path.'/'.basename($file) || $overwrite_all == 'true') {
						if ($overwrite == "true") {
							if(!$fs->copy($file, $fv->path.'/'.basename($file)))
							{
								$feedback = $fs_inssufficient_diskspace;
							}
						}
					} else {
						array_unshift($_SESSION['copy_files'], $file);
						$overwrite_source_path = $file;
						$overwrite_destination_path = $fv->path.'/'.basename($file);
						$task = 'overwrite';
						break;
					}
				} else {
					if(!$fs->copy($file, $fv->path.'/'.basename($file)))
					{
						$feedback = $fs_inssufficient_diskspace;
					}
				}
			}
		}

		if(!isset($feedback)  && isset($_REQUEST['return_to']) && $task != 'overwrite')
		{
			header('Location: '.$_REQUEST['return_to']);
		}
		break;

	case 'post_upload' :
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$task = 'list';
			if (isset ($_FILES) && count($_FILES)) {
				$_SESSION['cut_files'] = array ();
				$_SESSION['copy_files'] = array ();

				//get share users for email notify
				$users=$fs->get_users_in_share($fv->path);



				for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i ++) {
					if (is_uploaded_file($_FILES['file']['tmp_name'][$i])) {
						$destination =$GO_CONFIG->tmpdir.$_FILES['file']['name'][$i];
						if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $destination)) {
							$_SESSION['cut_files'][] = $destination;

							foreach($users as $user_id)
							{
								if($user_id != $GO_SECURITY->user_id)
								{
									$fs_settings = $fs->get_settings($user_id);
									if($fs_settings['notify']=='1')
									{
										$user = $GO_USERS->get_user($user_id);
										$subject = sprintf($fs_new_file_uploaded, $_FILES['file']['name'][$i]);

										$link = new hyperlink($GO_CONFIG->full_url.'index.php?return_to='.
										urlencode($GO_MODULES->url.'index.php?path='.
										urlencode($fv->path)),$fs_open_containing_folder);
										$link->set_attribute('target','_blank');
										$link->set_attribute('class','blue');

										$body = sprintf($fs_file_put_in, $_FILES['file']['name'][$i], str_replace($GO_CONFIG->file_storage_path.'users','', $fv->path)).'<br>'.$link->get_html();

										sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
									}
								}
							}

						}
					}
				}
		
				while ($file = smart_stripslashes(array_shift($_SESSION['cut_files']))) {
					$new_path = $fv->path.'/'.basename($file);
					if (!$fs->has_write_permission($GO_SECURITY->user_id, $fv->path)) {
						$popup_feedback .= access_denied_box($fv->path);
						break;
					}
					elseif (file_exists($new_path)) {
						if ($overwrite_destination_path == $new_path && $overwrite_all != 'true') {
							if ($overwrite == "true") {
								if(!$file_uploaded = $fs->move(addslashes($file), $new_path))
								{
									$task = 'upload';
									$feedback = $fs_inssufficient_diskspace;
								}else
								{									
									$file_link_id = $fs->get_link_id($new_path);								
								}
							}
						}else{
							array_unshift($_SESSION['cut_files'], $file);
							$overwrite_source_path = $file;
							$overwrite_destination_path = $new_path;
							$task = 'overwrite';
							break;
						}
					} else {
						if(!$file_uploaded = $fs->move(addslashes($file), $fv->path.'/'.basename($file)))
						{
							$task = 'upload';
							$feedback = $fs_inssufficient_diskspace;
						}else
						{
							$file_link_id = $fs->get_link_id($new_path);
						}
					}
				}
				if(!isset($feedback)  && isset($_REQUEST['return_to']) && $task != 'overwrite')
				{
					header('Location: '.$_REQUEST['return_to']);
				
					exit();
				}


			} else {
				$task = 'upload';
				$feedback = $fbNoFile.' '.format_size($GO_CONFIG->max_file_size);
			}
		}



		break;



	case 'properties' :

		$properties_task = isset($_POST['properties_task']) ? $_POST['properties_task'] : '';
		switch ($properties_task)
		{

			case 'save_properties':



				if (isset ($_POST['name'])) {

					$file = $fs->get_file(addslashes($fv->path));

					$name = trim(smart_stripslashes($_POST['name']));

					if(isset($_POST['status_id']) && ($_POST['status_id']!=$file['status_id'] || !empty($_POST['comments'])))
					{
						$fs->change_status($file['link_id'],smart_addslashes($_POST['status_id']), smart_addslashes($_POST['comments']));

						$users=$fs->get_users_in_share($fv->path);
						foreach($users as $user_id)
						{
							if($user_id != $GO_SECURITY->user_id)
							{
								$user = $GO_USERS->get_user($user_id);
								$subject = sprintf($fs_file_updated, $name);

								$link = new hyperlink($GO_CONFIG->full_url.'index.php?return_to='.
								urlencode($GO_MODULES->url.'index.php?path='.
								urlencode($fv->path)),$fs_open_containing_folder);
								$link->set_attribute('target','_blank');
								$link->set_attribute('class','blue');

								$body = smart_stripslashes($_POST['comments']).'<br /><br />'.$link->get_html();

								sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
							}
						}
					}




					if (validate_input($name)) {
						if (!$fs->has_write_permission($GO_SECURITY->user_id, $fv->path)) {
							$feedback = $strAccessDenied;
						}
						elseif ($name == '') {
							$feedback = $error_missing_field;
						} else {
							if (isset($_POST['extension']) && $_POST['extension'] != '') {
								$extension = '.'.smart_stripslashes($_POST['extension']);
							}else
							{
								$extension = '';
							}
							$location = dirname($fv->path);
							$name = smart_stripslashes($name);
							$new_path = $location.'/'.$name.$extension;
							if ($name.$extension != basename($fv->path)) {
								if (file_exists($new_path)) {
									$feedback = $fbNameExists;
								} else {

									if ($fs->move($fv->path, $new_path)) {
										if ($return_to_path == $fv->path) {
											$return_to_path = $new_path;
										}
										$fv->set_path($new_path);
										$urlencoded_path = urlencode($fv->path);
									}
								}
							}
						}
					} else {
						$feedback = $invalid_chars.': " & ? / \\';
					}
				}

				if (isset ($_POST['share_folder']) && !$fs->get_share($fv->path)) {
					$fs->add_share($GO_SECURITY->user_id, $fv->path, 'filesystem');
				}
				if (!isset ($_POST['share_folder'])) {
					//echo $fv->path;
					$fs->delete_share($fv->path);
				}

				break;

			case 'share':



				break;
		}

		$link_id = $fs->get_link_id(addslashes($fv->path));
		$prop_task = isset($_REQUEST['prop_task']) ? $_REQUEST['prop_task'] : '';

		switch ($prop_task) {
			case 'activate_linking':
				$GO_LINKS->activate_linking($link_id, 6, basename($fv->path), $GO_MODULES->modules['filesystem']['url'].'?task=properties&path='.$urlencoded_path);

				header('Location: '.$GO_CONFIG->host.'link.php');
				exit();
				break;

			case 'create_link':
				if($link = $GO_LINKS->get_active_link())
				{
					$GO_LINKS->add_link($link['id'], $link['type'], $link_id, 6);
					$GO_LINKS->deactivate_linking();
					//header('Location: '.$link['return_to']);
					//exit();
				}
				break;
		}

		if (isset($_POST['close']) && $_POST['close'] == 'true' && !isset ($feedback)) {
			$fv->set_path($return_to_path);
			$urlencoded_path = urlencode($fv->path);
			$_SESSION['GO_FILESYSTEM_PATH'] = $fv->path;
			$task = '';
		}
		break;


	case 'save_archive' :
		if (isset ($_POST['archive_files'])) {
			$name = trim($_POST['name']);
			if ($name == '') {
				$feedback = '<p class="Error">'.$error_missing_field.'</p>';
				$task = 'create_archive';
			} else {
				while ($file = array_shift($_POST['archive_files'])) {
					$archive_files[] = str_replace($fv->path.'/', '', smart_stripslashes($file));
				}

				chdir($fv->path);
				switch ($_POST['compression_type']) {
					case 'zip' :
						if (get_extension($name) != $_POST['compression_type']) {
							$name .= '.'.$_POST['compression_type'];
						}

						exec($GO_CONFIG->cmd_zip.' -r "'.$name.'" "'.implode('" "', $archive_files).'"');
						break;

					case 'gz' :
						if (get_extension($name) != $_POST['compression_type']) {
							$name .= '.tar.'.$_POST['compression_type'];
						}
						exec($GO_CONFIG->cmd_tar.' -czf "'.$name.'" "'.implode('" "', $archive_files).'"');
						break;
				}
			}
		}
		break;

	case 'extract' :
		if (isset ($_POST['fs_list']['selected'])) {
			chdir($fv->path);
			while ($file = array_shift($_POST['fs_list']['selected'])) {
				switch (strtolower(get_extension($file))) {
					case 'zip' :
						exec($GO_CONFIG->cmd_unzip.' "'.$file.'"');
						break;

					case 'gz' :
						exec($GO_CONFIG->cmd_tar.' -zxf "'.$file.'"');
						break;

					case 'tgz' :
						exec($GO_CONFIG->cmd_tar.' -zxf "'.$file.'"');
						break;

					default :
						$popup_feedback .= feedback($fb_unkown_compression.": '$file'");
						break;
				}
			}
		}
		break;
}

$GO_HEADER['head'] = datatable::get_header();
$GO_HEADER['head'] .= '<script type="text/javascript" language="javascript" src="'.$GO_MODULES->url.'filesystem.js"></script>';
switch($task)
{
	case 'create_archive':

	case 'new_folder':
		if($_SERVER['REQUEST_METHOD'] != 'POST')
		{
			$GO_HEADER['body_arguments'] = 'onload="javascript:document.forms[0].name.focus();" onkeypress="javascript:executeOnEnter(event, \'save()\');"';
		}
		break;

	case 'search':
		load_control('date_picker');
		$GO_HEADER['head'] .= date_picker::get_header();
		$GO_HEADER['body_arguments'] = 'onload="javascript:document.forms[0].keyword.focus();" onkeypress="javascript:executeOnEnter(event, \'search()\');"';
		break;

	case 'upload':
		$GO_HEADER['head'] .= '<script type="text/javascript" language="javascript" src="'.$GO_CONFIG->host.'javascript/multifile.js"></script>';
		$GO_HEADER['head'] .= '<style>.deleteButton{background-image:url(\''.$GO_THEME->images['delete'].'\');width:16px;height:16px;cursor:default;display:inline;background-repeat:no-repeat;margin-left:5px;</style>';
		break;
}

if($mode=='save')
{
	$GO_HEADER['body_arguments'] = 'onload="document.forms[0].filename.focus();"';
}

require_once ($GO_THEME->theme_path.'header.inc');

echo $popup_feedback;

$form = new form('filesystem_form');
if($task == 'upload')
{
	$form->set_attribute('enctype','multipart/form-data');
}
if($task == 'properties')
{
	$form->add_html_element(new input('hidden', 'task', 'properties',false));
}else
{
	$form->add_html_element(new input('hidden', 'task', '', false));
}
$form->add_html_element(new input('hidden', 'return_to_path', $return_to_path, false));
$form->add_html_element(new input('hidden', 'share_path', '', false));


switch ($task) {
	case 'mail_files' :

		$_SESSION['attach_array'] = array ();
		require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc");
		$email = new email();
		if (isset ($_POST['fs_list']['selected'])) {
			while ($file = smart_stripslashes(array_shift($_POST['fs_list']['selected']))) {
				if ($fs->has_read_permission($GO_SECURITY->user_id, $file)) {
					$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
					if (copy($file, $tmp_file)) {
						$filename = basename($file);
						$email->register_attachment($tmp_file, $filename, filesize($file), mime_content_type($file));
					}
				} else {
					$popup_feedback .= access_denied_box(basename($file));
				}
			}
			$form->innerHTML .= '<script type="text/javascript" language="javascript">';
			$form->innerHTML .= 'popup("'.$GO_MODULES->modules['email']['url'].'send.php?email_file=true","'.$GO_CONFIG->composer_width.'","'.$GO_CONFIG->composer_height.'");';
			$form->innerHTML .= '</script>';
		}
		require_once ($GO_MODULES->modules['filesystem']['path'].'listview.inc');
		break;


	case 'access_denied' :
		require_once ($GO_CONFIG->root_path.'error_docs/403.inc');
		break;

	case 'new_folder' :
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$name = smart_stripslashes($_POST['name']);
			if ($name == '') {
				$feedback = $error_missing_field;
				require_once ('new_folder.inc');
			}
			elseif (!validate_input($name)) {
				$feedback = $invalid_chars.': " & ? / \\';
				require_once ('new_folder.inc');
			}
			elseif (file_exists($fv->path.'/'.$name)) {
				$feedback = $fbFolderExists;
				require_once ('new_folder.inc');
			}
			elseif (!@ mkdir($fv->path.'/'.$name, $GO_CONFIG->create_mode)) {
				$feedback = $strSaveError;
				require_once ('new_folder.inc');
			} else {
				require_once ($GO_MODULES->modules['filesystem']['path'].'listview.inc');
			}
		} else {
			if ($fs->has_write_permission($GO_SECURITY->user_id, $fv->path)) {
				require_once ('new_folder.inc');
			} else {
				require_once ($GO_CONFIG->root_path.'error_docs/401.inc');
			}
		}
		break;

	case 'upload' :
		if ($fs->has_write_permission($GO_SECURITY->user_id, $fv->path)) {
			require_once ($GO_MODULES->modules['filesystem']['path'].'upload.inc');
		} else {
			require_once ($GO_CONFIG->root_path.'error_docs/401.inc');
		}
		break;

	case 'overwrite' :
		require_once ('overwrite.inc');
		break;

	case 'properties' :
		require_once ('properties.inc');
		break;

	case 'read_permissions' :
		require_once ('read_permissions.inc');
		break;

	case 'write_permissions' :
		require_once ('write_permissions.inc');
		break;

	case 'shares' :
		require_once ('shares.inc');
		break;

	case 'search' :
		require_once ('search.inc');
		break;

	case 'create_archive' :
		require_once ('compress.inc');
		break;

	default :
		require_once ($GO_MODULES->modules['filesystem']['path'].'listview.inc');
		break;

}

echo $form->get_html();

umask($old_umask);
require_once ($GO_THEME->theme_path.'footer.inc');
