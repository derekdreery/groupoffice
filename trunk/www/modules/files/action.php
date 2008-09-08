<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.php 2826 2008-08-26 08:01:58Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('files');

require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc");
$fs = new files();


require($GO_LANGUAGE->get_language_file('files'));

$task=isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';
define('SERVER_PATH', empty($_POST['local_path']) ? $GO_CONFIG->file_storage_path : $GO_CONFIG->local_path);

$response=array();

try{

	switch($task)
	{
		case 'delete':
			
			$delete_path = SERVER_PATH.smart_addslashes($_POST['path']);

			if(!$fs->has_write_permission($GO_SECURITY->user_id, $delete_path))
			{
				throw new AccessDeniedException();
			}
			
			$response['success']=$fs->delete($delete_path);
			break;
		case 'file_properties':
			$path = SERVER_PATH.smart_stripslashes($_POST['path']);

			if(!file_exists($path))
			{
				throw new Exception($lang['files']['fileNotFound']);
			}elseif(!$fs->has_write_permission($GO_SECURITY->user_id, $path))
			{
				throw new AccessDeniedException();
			}
			
			$up_file['path']=addslashes($path);
			$up_file['comments']=smart_addslashes($_POST['comments']);
			$fs->update_file($up_file);


			$new_name = smart_stripslashes($_POST['name']);

			if(empty($new_name))
			{
				throw new MissingFieldException();
			}
			$extension = File::get_extension($path);
			if(!empty($extension))
			{
				$new_name .= '.'.$extension;
			}

			if($new_name != basename($path))
			{
				$new_path = dirname($path).'/'.$new_name;

				$fs->move($path, $new_path);
				$response['path']=str_replace(SERVER_PATH,'',$new_path);
			}

			$response['success']=true;

			break;

		case 'folder_properties':
			$path = SERVER_PATH.smart_stripslashes($_POST['path']);

			if(!file_exists($path))
			{
				throw new Exception($lang['files']['fileNotFound']);
			}elseif(!$fs->has_read_permission($GO_SECURITY->user_id, $path))
			{
				throw new AccessDeniedException();
			}

			$new_notify = isset($_POST['notify']);
			$old_notify = $fs->is_notified(addslashes($path), $GO_SECURITY->user_id);

			if($new_notify && !$old_notify)
			{
				$fs->add_notification(addslashes($path), $GO_SECURITY->user_id);
			}
			if(!$new_notify && $old_notify)
			{
				$fs->remove_notification(addslashes($path), $GO_SECURITY->user_id);
			}


			if($fs->is_owner($GO_SECURITY->user_id, $path))
			{

				$folder = $fs->get_folder($path);
					
				
				$up_folder['path']=addslashes($path);
				$up_folder['comments']=smart_addslashes($_POST['comments']);
				
				if (isset ($_POST['share']) && $folder['acl_read']==0) {
					
					$up_folder['acl_read']=$GO_SECURITY->get_new_acl();
					$up_folder['acl_write']=$GO_SECURITY->get_new_acl();
					$up_folder['visible']='1';
					

					$response['acl_read']=$up_folder['acl_read'];
					$response['acl_write']=$up_folder['acl_write'];
				}
				if (!isset ($_POST['share']) && $folder['acl_read']) {
					$up_folder['acl_read']=0;
					$up_folder['acl_write']=0;					

					$GO_SECURITY->delete_acl($folder['acl_read']);
					$GO_SECURITY->delete_acl($folder['acl_write']);					
				}
				
				
				
				$fs->update_folder($up_folder);
					
				$new_name = smart_stripslashes($_POST['name']);
					
				if(empty($new_name))
				{
					throw new MissingFieldException();
				}
					
				if($new_name != basename($path))
				{
					$new_path = dirname($path).'/'.$new_name;

					$fs->move($path, $new_path);
					$response['path']=str_replace(SERVER_PATH,'',$new_path);
				}
			}
			$response['success']=true;

			break;

		case 'new_folder':

			$path = SERVER_PATH.smart_stripslashes($_POST['path']);

			if(!file_exists($path))
			{
				throw new Exception($lang['files']['fileNotFound']);
			}elseif(!$fs->has_write_permission($GO_SECURITY->user_id, $path))
			{
				throw new AccessDeniedException();
			}

			$response['success']=true;


			$name = smart_stripslashes($_POST['name']);
			if ($name == '') {
				throw new Exception($lang['common']['missingField']);
			}

			if (file_exists($path.'/'.$name)) {
				throw new Exception($lang['files']['folderExists']);
			}
			if (!@ mkdir($path.'/'.$name, $GO_CONFIG->create_mode)) {
				throw new Exception($strSaveError);
			} else {
				//$GO_LOGGER->log('filesystem', 'NEW FOLDER '.$fs->strip_file_storage_path($fv->path.'/'.$name));
				
				$folder['path']=$path.'/'.$name;
				$folder['visible']='1';
				$folder['user_id']=$GO_SECURITY->user_id;
				
				$fs->add_folder($folder);
			}

			break;

		case 'upload':
			//var_dump($_FILES);
			$response['success']=true;
			$path = SERVER_PATH.smart_stripslashes($_POST['path']);

			if(!file_exists($GO_CONFIG->tmpdir.'files_upload'))
			{
				$fs->mkdir_recursive($GO_CONFIG->tmpdir.'files_upload');
			}

			$_SESSION['GO_SESSION']['files']['uploaded_files']=array();

			for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n ++)
			{
				if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n]))
				{
					$tmp_file = $GO_CONFIG->tmpdir.'files_upload/'.smart_stripslashes($_FILES['attachments']['name'][$n]);
					move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);
					$_SESSION['GO_SESSION']['files']['uploaded_files'][]=$tmp_file;
				}
			}
			$response['success']=true;
			break;

		case 'overwrite':

			$command = isset($_POST['command']) ? $_POST['command'] : 'ask';
			$path = SERVER_PATH.smart_stripslashes($_POST['path']);

			while($tmp_file = array_shift($_SESSION['GO_SESSION']['files']['uploaded_files']))
			{
				$new_path = $path.'/'.basename($tmp_file);
				if(file_exists($new_path) && $command!='yes' && $command!='yestoall')
				{
					if($command!='no' && $command != 'notoall')
					{
						array_unshift($_SESSION['GO_SESSION']['files']['uploaded_files'], $tmp_file);
						$response['file_exists']=basename($tmp_file);
						throw new Exception('File exists');
					}
				}else
				{
					$fs->move($tmp_file, $new_path);
				}
				if($command != 'yestoall' && $command != 'notoall')
				{
					$command='ask';
				}
			}

			$response['success']=true;

			break;

		case 'paste':
			if(isset($_POST['paste_sources']) && isset($_POST['paste_destination']))
			{
				$_SESSION['GO_SESSION']['files']['paste_sources']= json_decode(smart_stripslashes($_POST['paste_sources']));
				$_SESSION['GO_SESSION']['files']['paste_destination']= smart_stripslashes($_POST['paste_destination']);
			}

			if(isset($_SESSION['GO_SESSION']['files']['paste_sources']) && count($_SESSION['GO_SESSION']['files']['paste_sources']))
			{

				$response['success']=true;

				if(!$fs->has_write_permission($GO_SECURITY->user_id, SERVER_PATH.$_SESSION['GO_SESSION']['files']['paste_destination']))
				{
					throw new AccessDeniedException();
				}

				$command = isset($_POST['command']) ? $_POST['command'] : 'ask';

				while($paste_source = array_shift($_SESSION['GO_SESSION']['files']['paste_sources']))
				{
					$destination = SERVER_PATH.$_SESSION['GO_SESSION']['files']['paste_destination'].'/'.basename($paste_source);

					if(file_exists($destination) && $command!='yes' && $command!='yestoall')
					{
						if($command!='no' && $command != 'notoall')
						{
							array_unshift($_SESSION['GO_SESSION']['files']['paste_sources'], $paste_source);
							$response['file_exists']=basename($destination);
							throw new Exception('File exists');
						}
					}else
					{
						if($_POST['paste_mode']=='cut')
						{
							if(!$fs->has_write_permission($GO_SECURITY->user_id, SERVER_PATH.$paste_source))
							{
								throw new AccessDeniedException();
							}
							$fs->move(SERVER_PATH.$paste_source,
							$destination);
						}else
						{
							$fs->copy(SERVER_PATH.$paste_source,
							$destination);
						}
					}

					if($command != 'yestoall' && $command != 'notoall')
					{
						$command='ask';
					}

				}

			}
			break;
				
				
		case 'save_template':
				
			$template['id']=isset($_POST['template_id']) ? smart_addslashes($_POST['template_id']) : 0;
			$template['name']=smart_addslashes($_POST['name']);
			
			if (is_uploaded_file($_FILES['file']['tmp_name'][0]))
			{
				$fp = fopen($_FILES['file']['tmp_name'][0], "rb");
				$template['content'] = addslashes(fread($fp, $_FILES['file']['size'][0]));
				fclose($fp);
				
				$template['extension']=File::get_extension($_FILES['file']['name'][0]);
			}		

			if(isset($_POST['user_id']))
			{
				$template['user_id']=smart_addslashes($_POST['user_id']);
			}
			
			debug(var_export($template, true));

			if($template['id']>0)
			{
				$fs->update_template($template);
				$response['success']=true;
			}else
			{
				if(empty($template['user_id']))
				{
						$template['user_id']=$GO_SECURITY->user_id;
				}
				$response['acl_read']=$template['acl_read']=$GO_SECURITY->get_new_acl();
				$response['acl_write']=$template['acl_write']=$GO_SECURITY->get_new_acl();
				$response['template_id']=$fs->add_template($template);				
			}
			$response['success']=true;
				
			break;
	}
}
catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);