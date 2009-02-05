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

require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('files');

require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc");
$fs = new files();


require($GO_LANGUAGE->get_language_file('files'));

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

$response=array();


try{

	switch($task)
	{
		case 'set_view':
			
			$up_folder['path']=$_POST['path'];
			$up_folder['thumbs']=$_POST['thumbs'];
			
			$fs->update_folder($up_folder);
			
			$response['success']=true;
			
			break;
		case 'delete':
			
			$delete_path = $GO_CONFIG->file_storage_path.$_POST['path'];

			if(!$fs->has_write_permission($GO_SECURITY->user_id, $delete_path))
			{
				throw new AccessDeniedException();
			}
			
			$fs->notify_users($_POST['path'], $GO_SECURITY->user_id, array(), array(), array(utf8_basename($delete_path)));
			
			$response['success']=$fs->delete($delete_path);
			break;
		case 'file_properties':
			$path = $GO_CONFIG->file_storage_path.$_POST['path'];

			if(!file_exists($path))
			{
				throw new Exception($lang['files']['fileNotFound']);
			}elseif(!$fs->has_write_permission($GO_SECURITY->user_id, $path))
			{
				throw new AccessDeniedException();
			}
			
			$up_file['path']=$_POST['path'];
			$up_file['comments']=$_POST['comments'];
			$fs->update_file($up_file);


			$new_name = $_POST['name'];

			if(empty($new_name))
			{
				throw new MissingFieldException();
			}
			$extension = File::get_extension($path);
			if(!empty($extension))
			{
				$new_name .= '.'.$extension;
			}

			if($new_name != utf8_basename($path))
			{
				$new_path = dirname($path).'/'.$new_name;

				$fs->move($path, $new_path);
				$response['path']=str_replace($GO_CONFIG->file_storage_path,'',$new_path);
			}

			$response['success']=true;

			break;

		case 'folder_properties':
			if(!file_exists($GO_CONFIG->file_storage_path.$_POST['path']))
			{
				throw new Exception($lang['files']['fileNotFound']);
			}elseif(!$fs->has_write_permission($GO_SECURITY->user_id, $GO_CONFIG->file_storage_path.$_POST['path']))
			{
				throw new AccessDeniedException();
			}

			$new_notify = isset($_POST['notify']);
			$old_notify = $fs->is_notified($_POST['path'], $GO_SECURITY->user_id);

			if($new_notify && !$old_notify)
			{
				$fs->add_notification($_POST['path'], $GO_SECURITY->user_id);
			}
			if(!$new_notify && $old_notify)
			{
				$fs->remove_notification($_POST['path'], $GO_SECURITY->user_id);
			}

			if($fs->is_owner($GO_SECURITY->user_id, $_POST['path']))
			{
				$folder = $fs->get_folder($_POST['path']);
				
				$up_folder['path']=$_POST['path'];
				$up_folder['comments']=$_POST['comments'];
				
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
					
				$new_name = $_POST['name'];
					
				if(empty($new_name))
				{
					throw new MissingFieldException();
				}
					
				if($new_name != utf8_basename($_POST['path']))
				{
					$new_path = dirname($_POST['path']).'/'.$new_name;

					$fs->move($GO_CONFIG->file_storage_path.$_POST['path'], $GO_CONFIG->file_storage_path.$new_path);
					$response['path']=$new_path;
				}
			}
			$response['success']=true;

			break;

		case 'new_folder':

			$full_path = $GO_CONFIG->file_storage_path.$_POST['path'];
			if(!file_exists($full_path))
			{
				throw new Exception($lang['files']['fileNotFound']);
			}elseif(!$fs->has_write_permission($GO_SECURITY->user_id, $full_path))
			{
				throw new AccessDeniedException();
			}

			$response['success']=true;


			$name = $_POST['name'];
			if ($name == '') {
				throw new Exception($lang['common']['missingField']);
			}

			if (file_exists($full_path.'/'.$name)) {
				throw new Exception($lang['files']['folderExists']);
			}
			if (!@ mkdir($full_path.'/'.$name, $GO_CONFIG->folder_create_mode)) {
				throw new Exception($lang['common']['saveError']);
			} else {
				//$GO_LOGGER->log('filesystem', 'NEW FOLDER '.$fs->strip_file_storage_path($fv->path.'/'.$name));
				
				$folder['path']=$_POST['path'].'/'.$name;
				$folder['visible']='1';
				$folder['user_id']=$GO_SECURITY->user_id;
				
				$fs->add_folder($folder);
			}

			break;

		case 'upload':
			//var_dump($_FILES);
			$response['success']=true;
			$full_path = $GO_CONFIG->file_storage_path.$_POST['path'];

			if(!file_exists($GO_CONFIG->tmpdir.'files_upload'))
			{
				$fs->mkdir_recursive($GO_CONFIG->tmpdir.'files_upload');
			}

			$_SESSION['GO_SESSION']['files']['uploaded_files']=array();

			for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n ++)
			{
				if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n]))
				{
					$tmp_file = $GO_CONFIG->tmpdir.'files_upload/'.($_FILES['attachments']['name'][$n]);
					move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);
					chmod($tmp_file, $GO_CONFIG->file_create_mode);
	
					$_SESSION['GO_SESSION']['files']['uploaded_files'][]=$tmp_file;
				}
			}
			$response['success']=true;
			break;

		case 'overwrite':
			
			require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
			$quota = new quota();
			
			$new = array();
			$modified=array();

			$command = isset($_POST['command']) ? $_POST['command'] : 'ask';
			$full_path = $GO_CONFIG->file_storage_path.$_POST['path'];

			while($tmp_file = array_shift($_SESSION['GO_SESSION']['files']['uploaded_files']))
			{
				$new_path = $full_path.'/'.utf8_basename($tmp_file);
				if(file_exists($new_path) && $command!='yes' && $command!='yestoall')
				{
					if($command!='no' && $command != 'notoall')
					{
						array_unshift($_SESSION['GO_SESSION']['files']['uploaded_files'], $tmp_file);
						$response['file_exists']=utf8_basename($tmp_file);
						throw new Exception('File exists');
					}
				}else
				{
					$size = filesize($tmp_file)/1024;
					if(!$quota->check($size))
					{
						throw new Exception($lang['common']['quotaExceeded']);
					}
					
					if(!$fs->move($tmp_file, $new_path))
					{
						throw new Exception($lang['common']['saveError']);
					}
					
					$quota->add($size);
					
					if(file_exists($new_path))
					{										
						$modified[]=utf8_basename($new_path);
					}else
					{
						$new[]=utf8_basename($new_path);
					}					
				}
				if($command != 'yestoall' && $command != 'notoall')
				{
					$command='ask';
				}			
			}
			
			$fs->notify_users($_POST['path'], $GO_SECURITY->user_id, $modified, $new);

			$response['success']=true;

			break;

		case 'paste':
			
			$command = isset($_POST['command']) ? $_POST['command'] : 'ask';
			
			if($command=='ask' && isset($_POST['paste_sources']) && isset($_POST['paste_destination']))
			{
				$_SESSION['GO_SESSION']['files']['paste_sources']= json_decode($_POST['paste_sources']);
				$_SESSION['GO_SESSION']['files']['paste_destination']= $_POST['paste_destination'];
			}

			if(isset($_SESSION['GO_SESSION']['files']['paste_sources']) && count($_SESSION['GO_SESSION']['files']['paste_sources']))
			{
				$response['success']=true;

				if(!$fs->has_write_permission($GO_SECURITY->user_id, $GO_CONFIG->file_storage_path.$_SESSION['GO_SESSION']['files']['paste_destination']))
				{
					throw new AccessDeniedException();
				}

				

				while($paste_source = array_shift($_SESSION['GO_SESSION']['files']['paste_sources']))
				{
					$destination = $GO_CONFIG->file_storage_path.$_SESSION['GO_SESSION']['files']['paste_destination'].'/'.utf8_basename($paste_source);
					
					if($_POST['paste_mode']=='copy')
					{
						if($GO_CONFIG->file_storage_path.$paste_source==$destination)
						{
							$name = $destination;
							$x=0;						
							while(file_exists($destination))
							{
								$x++;
								$destination=File::strip_extension($name).'_'.$x.'.'.File::get_extension($name);								
							}
						}
					}
					
					if(file_exists($destination) && $command!='yes' && $command!='yestoall')
					{
						if($command!='no' && $command != 'notoall')
						{
							array_unshift($_SESSION['GO_SESSION']['files']['paste_sources'], $paste_source);
							$response['file_exists']=utf8_basename($destination);
							throw new Exception('File exists');
						}
					}else
					{
						if($_POST['paste_mode']=='cut')
						{
							if(!$fs->has_write_permission($GO_SECURITY->user_id, $GO_CONFIG->file_storage_path.$paste_source))
							{
								throw new AccessDeniedException();
							}
							$fs->move($GO_CONFIG->file_storage_path.$paste_source,
							$destination);
						}else
						{
							$fs->copy($GO_CONFIG->file_storage_path.$paste_source,
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
				
			$template['id']=isset($_POST['template_id']) ? ($_POST['template_id']) : 0;
			$template['name']=$_POST['name'];
			
			$types = 'is';
			
			if (is_uploaded_file($_FILES['file']['tmp_name'][0]))
			{
				$fp = fopen($_FILES['file']['tmp_name'][0], "rb");
				$template['content'] = fread($fp, $_FILES['file']['size'][0]);
				fclose($fp);
				
				$template['extension']=File::get_extension($_FILES['file']['name'][0]);
				$types .= 'bs';
			}		

			if(isset($_POST['user_id']))
			{
				$template['user_id']=$_POST['user_id'];
				$types .= 'i';
			}

			if($template['id']>0)
			{
				$fs->update_template($template, $types);
				$response['success']=true;
			}else
			{
				if(empty($template['user_id']))
				{
						$template['user_id']=$GO_SECURITY->user_id;
				}
				$response['acl_read']=$template['acl_read']=$GO_SECURITY->get_new_acl();
				$response['acl_write']=$template['acl_write']=$GO_SECURITY->get_new_acl();
				$types .= 'ii';
				$response['template_id']=$fs->add_template($template, $types);				
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