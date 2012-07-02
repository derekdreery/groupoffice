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

// check is needed for SWFUpload
if(isset($_REQUEST['groupoffice']))
{
	session_id($_REQUEST['groupoffice']);
}

require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('files');

require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc.php");
$files = new files();

require($GO_LANGUAGE->get_language_file('files'));

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

$response=array();

try {
	if(empty($_REQUEST['task'])){
		//probably too large upload
		throw new Exception(sprintf($lang['common']['upload_file_to_big'], ini_get('upload_max_filesize')));
	}

	switch($task) {

		case 'set_view':
			$up_folder['id']=$_POST['id'];
			$up_folder['thumbs']=$_POST['thumbs'];

			$files->update_folder($up_folder);

			$response['success']=true;

			break;
		case 'delete':

		//delete called from tree

			$folder =$files->get_folder($_POST['id']);

			if(!$files->has_delete_permission($GO_SECURITY->user_id, $folder)) {
				throw new AccessDeniedException();
			}

			$files->notify_users($folder, $GO_SECURITY->user_id, array(), array(), array($folder['name']));

			$response['success']=$files->delete_folder($folder);
			break;
		case 'file_properties':
			$file = $files->get_file($_POST['file_id']);
			$folder = $files->get_folder($file['folder_id']);
			if(!$folder) {
				throw new FileNotFoundException();
			}elseif(!$files->has_write_permission($GO_SECURITY->user_id, $folder)) {
				throw new AccessDeniedException();
			}

			$up_file['id']=$file['id'];
			$up_file['comments']=$_POST['comments'];

			$name = trim($_POST['name']);



			if(empty($name)) {
				throw new MissingFieldException();
			}
			$up_file['extension'] = File::get_extension($file['name']);
			if(!empty($up_file['extension'])) {
				$name .= '.'.$up_file['extension'];
			}

			if($name != $file['name']) {
				$path=$files->build_path($folder);
				$oldpath = $path.'/'.$file['name'];
				$newpath = $path.'/'.$name;

				$fs = new filesystem();
				$fs->move($GO_CONFIG->file_storage_path.$oldpath, $GO_CONFIG->file_storage_path.$newpath);
				$response['path']=$newpath;
			}
			$up_file['name']=$name;
			$up_file['folder_id'] = $folder['id'];
			$files->update_file($up_file);


			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission']) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->insert_cf_row(6,$up_file['id']);
				$cf->update_fields($GO_SECURITY->user_id, $up_file['id'], 6, $_POST, false, true);
			}

			$GO_EVENTS->fire_event('save_file_properties', array(&$response,$up_file));

			$response['success']=true;

			break;

		case 'folder_properties':
			$folder = $files->get_folder($_POST['folder_id']);
			if(!$folder) {
				throw new FileNotFoundException();
			}

			if(!$files->has_read_permission($GO_SECURITY->user_id, $folder)) {
				throw new AccessDeniedException();
			}

			$new_notify = isset($_POST['notify']);
			$old_notify = $files->is_notified($_POST['folder_id'], $GO_SECURITY->user_id);

			if($new_notify && !$old_notify) {
				$files->add_notification($_POST['folder_id'], $GO_SECURITY->user_id);
			}
			if(!$new_notify && $old_notify) {
				$files->remove_notification($_POST['folder_id'], $GO_SECURITY->user_id);
			}
       $permission_level=$files->get_permission_level($GO_SECURITY->user_id, $folder);
			if($permission_level>GO_SECURITY::READ_PERMISSION) {
				if(isset($_POST['name']) && empty($_POST['name'])) {
					throw new MissingFieldException();
				}

				$up_folder['id']=$_POST['folder_id'];
				$up_folder['comments']=$_POST['comments'];

				$usersfolder = $files->resolve_path('users');
				
				//throw new Exception($permission_level);

				if(empty($folder['readonly']) && $folder['parent_id']!=$usersfolder['id'] && $permission_level==GO_SECURITY::MANAGE_PERMISSION) {
					
					if (isset($_POST['share']) && $folder['acl_id']==0) {

						$up_folder['acl_id']=$GO_SECURITY->get_new_acl();
						$up_folder['visible']='1';

						$response['acl_id']=$up_folder['acl_id'];
					}
					if (!isset ($_POST['share']) && $folder['acl_id']) {
						$up_folder['acl_id']=0;
						
						require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
						$search = new search();
						$search->log($up_folder['id'],17, "Sharing removed for folder ".$folder['name'].' '.$folder['id']);
							
					

						$GO_SECURITY->delete_acl($folder['acl_id']);
					}
				}

				$up_folder['apply_state'] = !empty($_POST['apply_state']) ? 1 : 0;

				//$files->update_folder($up_folder);

				if(isset($_POST['name'])) {
					$name = trim($_POST['name']);
					if($name != $folder['name']) {
						$path=$files->build_path($folder);
						$newpath = dirname($path).'/'.$name;

						$fs = new filesystem();
						$fs->move($GO_CONFIG->file_storage_path.$path, $GO_CONFIG->file_storage_path.$newpath);

						$up_folder['name']=$name;
						$up_folder['mtime']=filemtime($GO_CONFIG->file_storage_path.$newpath);

						$response['path']=$newpath;
					}
				}

				$files->update_folder($up_folder);

				$GO_EVENTS->fire_event('save_folder_properties', array(&$response,$up_folder));
			}

			$response['success']=true;

			break;

		case 'new_folder':
			$folder=$files->mkdir($_POST['folder_id'], $_POST['name']);
			$response['folder_id']=$folder['id'];
			$response['success']=true;
			break;


		case 'upload_file':

			$response['success']=true;

			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			filesystem::mkdir_recursive($GO_CONFIG->tmpdir.'files_upload/');

			if(!isset($_SESSION['GO_SESSION']['files']['uploaded_files']))
				$_SESSION['GO_SESSION']['files']['uploaded_files']=array();
			if(!isset($_SESSION['GO_SESSION']['files']['uploaded_files_props']))
				$_SESSION['GO_SESSION']['files']['uploaded_files_props']=array();
			
			if(isset($_FILES['Filedata']))
			{
				$file = $_FILES['Filedata'];
			}else
			{
				$file['name'] = $_FILES['attachments']['name'][0];
				$file['tmp_name'] = $_FILES['attachments']['tmp_name'][0];
				$file['size'] = $_FILES['attachments']['size'][0];
			}

			if(is_uploaded_file($file['tmp_name']))
			{
				$tmp_file = $GO_CONFIG->tmpdir.'files_upload/'.$file['name'];
				move_uploaded_file($file['tmp_name'], $tmp_file);
				chmod($tmp_file, $GO_CONFIG->file_create_mode);
				if(!empty($GO_CONFIG->file_change_group))
					chgrp($tmp_file, $GO_CONFIG->file_change_group);				

				$_SESSION['GO_SESSION']['files']['uploaded_files'][]=$tmp_file;
				$_SESSION['GO_SESSION']['files']['uploaded_files_props'][]=$_POST;
			}

			echo json_encode($response);
			exit();

			break;
			
		case 'upload':
			$response['success']=true;
			$fs = new filesystem();
			$fs->mkdir_recursive($GO_CONFIG->tmpdir.'files_upload');

			$_SESSION['GO_SESSION']['files']['uploaded_files']=array();
			$_SESSION['GO_SESSION']['files']['uploaded_files_props']=array();

			for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n ++) {
				if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n])) {
					$tmp_file = $GO_CONFIG->tmpdir.'files_upload/'.$_FILES['attachments']['name'][$n];
					move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);
					chmod($tmp_file, $GO_CONFIG->file_create_mode);
					if(!empty($GO_CONFIG->file_change_group))
						chgrp($tmp_file, $GO_CONFIG->file_change_group);

					$_SESSION['GO_SESSION']['files']['uploaded_files'][]=$tmp_file;
					$_SESSION['GO_SESSION']['files']['uploaded_files_props'][]=$_POST;
				}
			}
			$response['success']=true;
			break;

		case 'overwrite':

			require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
			$quota = new quota();

			$fs = new filesystem();

			$new = array();
			$modified=array();

			$command = isset($_POST['command']) ? $_POST['command'] : 'ask';

			$folder = $files->get_folder($_POST['folder_id']);
			if(!$folder) {
				throw new FileNotFoundException();
			}
			if(!$files->has_write_permission($GO_SECURITY->user_id, $folder)) {
				throw new AccessDeniedException();
			}

			$rel_path = $files->build_path($folder);
			$full_path = $GO_CONFIG->file_storage_path.$rel_path;

			while($tmp_file = array_shift($_SESSION['GO_SESSION']['files']['uploaded_files'])) {
				
				$filename = utf8_basename($tmp_file);
				$new_path = $full_path.'/'.$filename;

				if(file_exists($new_path) && $command!='yes' && $command!='yestoall') {
					if($command!='no' && $command != 'notoall') {
						array_unshift($_SESSION['GO_SESSION']['files']['uploaded_files'], $tmp_file);
						$response['file_exists']=utf8_basename($tmp_file);
						throw new Exception('File exists');
					}
				}else {
					if(!$quota->add_file($tmp_file)) {
						throw new Exception($lang['common']['quotaExceeded']);
					}

					$existing_file = $files->file_exists($folder['id'], $filename);
					if($existing_file) {
						$modified[]=$filename;
					}else {
						$new[]=$filename;
					}

					if($existing_file) {
						$files->move_version($existing_file, $new_path);
					}

					if(!$fs->move($tmp_file, $new_path)) {
						throw new Exception($lang['common']['saveError']);
					}

					$allprops = isset($_SESSION['GO_SESSION']['files']['uploaded_files_props']) ? array_shift($_SESSION['GO_SESSION']['files']['uploaded_files_props']) : false;
					$comments = isset($allprops['comments']) ? $allprops['comments'] : '';

					unset($allprops['comments']);
					
					$props = array();
					foreach($allprops as $key=>$value) {
						if(!empty($value))
							$props[$key]=$value;
					}

					$file_id = $files->import_file($new_path, $folder['id'], $comments);

					if($props && $GO_MODULES->has_module('customfields')) {
						require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
						$cf = new customfields();

						$cf->insert_cf_row(6, $file_id);
						$cf->update_fields($GO_SECURITY->user_id, $file_id, 6, $props, false);
					}


					if(!$existing_file && $GO_MODULES->has_module('workflow')) {
						require_once($GO_MODULES->modules['workflow']['class_path'].'workflow.class.inc.php');
						$wf = new workflow();

						$wf_folder = $wf->get_folder($folder['id']);
						if(!empty($wf_folder['default_process_id'])) {
							$wf->enable_workflow_process($file_id, $wf_folder['default_process_id']);
						}
					}
				}
				if($command != 'yestoall' && $command != 'notoall') {
					$command='ask';
				}
			}

			$files->touch_folder($folder['id']);

			$files->notify_users($folder, $GO_SECURITY->user_id, $modified, $new);

			$response['success']=true;

			break;

		case 'paste':

			$command = isset($_POST['command']) ? $_POST['command'] : 'ask';

			if($command=='ask' && isset($_POST['paste_sources']) && isset($_POST['paste_destination'])) {
				$_SESSION['GO_SESSION']['files']['paste_sources']= json_decode($_POST['paste_sources']);
				$_SESSION['GO_SESSION']['files']['paste_destination']= $_POST['paste_destination'];
			}

			if(isset($_SESSION['GO_SESSION']['files']['paste_sources']) && count($_SESSION['GO_SESSION']['files']['paste_sources'])) {
				$response['success']=true;

				if(!$files->has_write_permission($GO_SECURITY->user_id, $_SESSION['GO_SESSION']['files']['paste_destination'])) {
					throw new AccessDeniedException();
				}

				while($paste_source = array_shift($_SESSION['GO_SESSION']['files']['paste_sources'])) {
					$destfolder = $files->get_folder($_SESSION['GO_SESSION']['files']['paste_destination']);
					$destpath = $GO_CONFIG->file_storage_path.$files->build_path($destfolder);

					$type_id = explode(':',$paste_source);

					if($type_id[0]=='d') {
						$sourcefolder = $files->get_folder($type_id[1]);
						$sourcepath = $GO_CONFIG->file_storage_path.$files->build_path($sourcefolder);
						$destpath .= '/'.$sourcefolder['name'];
					}else {
						$sourcefile = $files->get_file($type_id[1]);
						$sourcepath = $GO_CONFIG->file_storage_path.$files->build_path($sourcefile['folder_id']).'/'.$sourcefile['name'];
						$destpath .= '/'.$sourcefile['name'];
					}


					$fs = new filesystem();

					if($_POST['paste_mode']=='copy') {
						if($sourcepath==$destpath) {
							$name = $destpath;
							$x=0;
							while(file_exists($destpath)) {
								$x++;
								if($type_id[0]=='d') {
									$destpath=$name.' ('.$x.')';
								}else {
									$destpath=File::strip_extension($name).' ('.$x.').'.File::get_extension($name);
								}
							}
						}
					}else {
						if($destpath==$sourcepath) {
							continue;
						}
					}

					$exists = file_exists($destpath);
					if($exists && $command!='yes' && $command!='yestoall') {
						if($command!='no' && $command != 'notoall') {
							array_unshift($_SESSION['GO_SESSION']['files']['paste_sources'], $paste_source);
							$response['file_exists']=utf8_basename($destpath);
							throw new Exception('File exists');
						}
					}else {
						if($_POST['paste_mode']=='cut') {
							if($type_id[0]=='d') {
								if(!$files->has_write_permission($GO_SECURITY->user_id, $sourcefolder)) {
									throw new AccessDeniedException();
								}

								$fs->move($sourcepath, $destpath);
								$files->move_folder($sourcefolder, $destfolder);
							}else {
								if(!$files->has_write_permission($GO_SECURITY->user_id, $sourcefile['folder_id'])) {
									throw new AccessDeniedException();
								}

								$fs->move($sourcepath, $destpath);
								$files->move_file($sourcefile, $destfolder);
							}
						}else {
							//todo check if exists on import
							$fs->copy($sourcepath, $destpath);
							if($type_id[0]=='d') {
								$files->import_folder($destpath,$destfolder['id']);
							}else {
								$files->import_file($destpath,$destfolder['id']);
							}
						}
					}

					if($command != 'yestoall' && $command != 'notoall') {
						$command='ask';
					}
				}

			}
			break;


		case 'save_template':

			$template['id']=isset($_POST['template_id']) ? ($_POST['template_id']) : 0;
			$template['name']=$_POST['name'];

			$types = 'is';

			if (is_uploaded_file($_FILES['file']['tmp_name'][0])) {
				$template['content'] = file_get_contents($_FILES['file']['tmp_name'][0]);

				$template['extension']=File::get_extension($_FILES['file']['name'][0]);
				$types .= 'bs';
			}

			/*if(isset($_POST['user_id'])) {
				$template['user_id']=$_POST['user_id'];
				$types .= 'i';
			}*/

			if($template['id']>0) {
				$files->update_template($template, $types);
				$response['success']=true;
			}else {
				//if(empty($template['user_id'])) {
				$template['user_id']=$GO_SECURITY->user_id;
				//}
				$response['acl_id']=$template['acl_id']=$GO_SECURITY->get_new_acl();

				$types .= 'ii';
				$response['template_id']=$files->add_template($template, $types);
			}
			$response['success']=true;

			break;

		case 'save_state':
			$folder['id'] = $_POST['folder_id'];
			$folder['cm_state'] = $_POST['state'];
			$files->update_folder($folder);
			$response['success'] = true;
	}
}
catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);