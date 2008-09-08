<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 2826 2008-08-26 08:01:58Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('files');

require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc");
$fs = new files();

require($GO_LANGUAGE->get_language_file('files'));


$task=isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';
$response=array();

define('SERVER_PATH', empty($_POST['local_path']) ? $GO_CONFIG->file_storage_path : $GO_CONFIG->local_path);

function strip_server_path($path)
{
	global $GO_CONFIG;
	return substr($path, strlen(SERVER_PATH));
}

try{

	switch($task)
	{
		case 'tree':

			$home_path = SERVER_PATH.'users/'.$_SESSION['GO_SESSION']['username'];
			$node = isset($_POST['node']) ? smart_stripslashes($_POST['node']) : 'root';

			switch($node)
			{
				case 'root':
						
					/*Home folder with children */
					$folders = $fs->get_folders_sorted($home_path);

					$children = array();
					foreach($folders as $folder)
					{
						$node= array(
						'text'=>$folder['name'],
						'id'=>strip_server_path($folder['path']),
						'notreloadable'=>true			
						);

						$db_folder = $fs->get_folder(addslashes($folder['path']));
						if($db_folder['acl_read']>0)
						{
							$node['iconCls']='folder-shared';
						}else
						{
							$node['iconCls']='folder-default';
						}

						if(!$fs->get_folders($folder['path']))
						{
							$node['children']=array();
							$node['expanded']=true;
						}

						$children[]=$node;
					}
						
						

					$node= array(
					'text'=>$lang['files']['personal'],
					'id'=>strip_server_path($home_path),
					'iconCls'=>'folder-home',
					'expanded'=>true,
					'children'=>$children,
					'notreloadable'=>true			
					);
					$response[]=$node;
						
						
						
						
					/* All shares */
						
						
						
					$share_count = $fs->get_authorized_shares($GO_SECURITY->user_id);
					$children = array();
					while ($fs->next_record())
					{
						$share_path = $fs->f('path');
						if (file_exists($share_path))
						{
							if (is_dir($share_path))
							{
								$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_path, $last_folder) : false;

								if (!$is_sub_dir)
								{
									$last_folder = $fs->f('path');

									$node = array(
											'text'=>basename($fs->f('path')),
											'id'=>strip_server_path($fs->f('path')),
											'iconCls'=>'folder-default',
											'reloadable'=>false
											);
											if(!$fs->get_folders($fs->f('path')))
											{
												$node['children']=array();
												$node['expanded']=true;
											}
											$children[]=$node;
								}
							}
						}
					}
						
						
					$node= array(
					'text'=>$lang['files']['shared'],
					'id'=>'shared',
					'iconCls'=>'folder-shares',
					'expanded'=>true,
					'children'=>$children,
					'notreloadable'=>true					
					);
					$response[]=$node;

					break;


				case 'shared':

					$share_count = $fs->get_authorized_shares($GO_SECURITY->user_id);

					while ($fs->next_record())
					{
						$share_path = $fs->f('path');
						if (file_exists($share_path))
						{
							if (is_dir($share_path))
							{
								$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_path, $last_folder) : false;

								if (!$is_sub_dir)
								{
									$last_folder = $fs->f('path');

									$node = array(
											'text'=>basename($fs->f('path')),
											'id'=>strip_server_path($fs->f('path')),
											'iconCls'=>'folder-default',
											'notreloadable'=>true
											);
											if(!$fs->get_folders($fs->f('path')))
											{
												$node['children']=array();
												$node['expanded']=true;
											}
											$response[]=$node;
								}
							}
						}
					}





					break;

				default:
					$path = SERVER_PATH.smart_stripslashes($_POST['node']);
					if(!$fs->has_read_permission($GO_SECURITY->user_id, $path))
					{
						throw new AccessDeniedException();
												//$path = $home_path;
					}


					$folders = $fs->get_folders_sorted($path);

					foreach($folders as $folder)
					{
						$node= array(
						'text'=>$folder['name'],
						'id'=>strip_server_path($folder['path'])
						);

						$db_folder = $fs->get_folder(addslashes($folder['path']));
						if($db_folder['acl_read']>0)
						{
							$node['iconCls']='folder-shared';
						}else
						{
							$node['iconCls']='folder-default';
						}

						if(!$fs->get_folders($folder['path']))
						{
							$node['children']=array();
							$node['expanded']=true;
						}

						$response[]=$node;
					}

					break;
			}

			break;

				case 'grid':

					$response['results']=array();

					if($_POST['path'] == 'shared')
					{
						$response['write_permission']=false;
						$share_count = $fs->get_authorized_shares($GO_SECURITY->user_id);

						while ($fs->next_record())
						{
							$share_path = $fs->f('path');
							if (file_exists($share_path))
							{
								if (is_dir($share_path))
								{
									$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_path, $last_folder) : false;

									if (!$is_sub_dir)
									{
										$last_folder = $fs->f('path');
											
										$folder['path']=strip_server_path($share_path);
										$folder['grid_display']='<div class="go-grid-icon filetype-folder">'.basename($share_path).'</div>';
										$folder['type']='Shared folder';
										$folder['mtime']=Date::get_timestamp(filemtime($share_path));
										$folder['size']='-';
										$folder['extension']='folder';
										$response['results'][]=$folder;
									}
								}
							}
						}
					}else
					{
						$path = SERVER_PATH.smart_stripslashes($_POST['path']);

						if(!$fs->has_read_permission($GO_SECURITY->user_id, $path))
						{
							throw new AccessDeniedException();
						}

						$response['write_permission']=$fs->has_write_permission($GO_SECURITY->user_id, $path);

						if(isset($_POST['delete_keys']))
						{
							try{
								$response['deleteSuccess']=true;
								$delete_paths = json_decode(smart_stripslashes($_POST['delete_keys']));

								foreach($delete_paths as $delete_path)
								{
									if(!$fs->has_write_permission($GO_SECURITY->user_id, SERVER_PATH.$delete_path))
									{
										throw new AccessDeniedException();
									}								
									
									$fs->delete(SERVER_PATH.$delete_path);
								}
							}catch(Exception $e)
							{
								$response['deleteSuccess']=false;
								$response['deleteFeedback']=$e->getMessage();
							}
						}

						if($response['write_permission'])
						{
							if(!empty($_POST['template_id']) && !empty($_POST['template_name']))
							{

								$template = $fs->get_template(smart_addslashes($_POST['template_id']), true);

								$new_path = $path.'/'.smart_stripslashes($_POST['template_name']).'.'.$template['extension'];
								file_put_contents($new_path, $template['content']);

								$response['new_path']=strip_server_path($new_path);
							}

							try{
								if(isset($_POST['compress_sources']) && isset($_POST['archive_name']))
								{
									$compress_sources = json_decode(smart_stripslashes($_POST['compress_sources']),true);
									$archive_name = smart_stripslashes($_POST['archive_name']).'.zip';
										
									function strip_path($client_path)
									{
										global $path, $GO_CONFIG;
										return '.'.substr(SERVER_PATH.$client_path, strlen($path));
									}
										
									$compress_sources=array_map('strip_path', $compress_sources);

									//var_dump($compress_sources);
									if(file_exists($path.'/'.$archive_name))
									{
										throw new Exception($lang['files']['filenameExists']);
									}
										
									chdir($path);
									//echo $GO_CONFIG->cmd_zip.' -r "'.$archive_name.'" "'.implode('" "',$compress_sources).'"';
									exec($GO_CONFIG->cmd_zip.' -r "'.$archive_name.'" "'.implode('" "',$compress_sources).'"');

									$response['compress_success']=true;
								}
							}catch(Exception $e)
							{
								$response['compress_success']=false;
								$response['compress_feedback']=$e->getMessage();
							}

							try{
								if(isset($_POST['decompress_sources']))
								{
									chdir($path);
									$decompress_sources = json_decode(smart_stripslashes($_POST['decompress_sources']));
									while ($file = array_shift($decompress_sources)) {
										switch(File::get_extension($file))
										{
											case 'zip':
												exec($GO_CONFIG->cmd_unzip.' "'.SERVER_PATH.$file.'"');
												break;

											case 'gz':
											case 'tgz':
												exec($GO_CONFIG->cmd_tar.' zxf "'.SERVER_PATH.$file.'"');
												break;

											case 'tar':
												exec($GO_CONFIG->cmd_tar.' xf "'.SERVER_PATH.$file.'"');
												break;
										}

									}
										
									//TODO error handling
									$response['decompress_success']=true;
								}
							}catch(Exception $e)
							{
								$response['decompress_success']=false;
								$response['decompress_feedback']=$e->getMessage();
							}
						}

						$sort = isset($_POST['sort']) ? $_POST['sort'] : '';
						$dir = isset($_POST['dir']) ? $_POST['dir'] : 'ASC';

						switch($sort)
						{
							case 'mtime':
								$sort = 'filemtime';
								break;
							case 'type':
								$sort = 'File::get_extension';
								break;
							case 'size':
								$sort = 'filesize';
								break;
							default:
								$sort = 'basename';
								break;

						}



						$folders = $fs->get_folders_sorted($path, 'basename', $dir);
						foreach($folders as $folder)
						{
							$db_folder = $fs->get_folder(addslashes($folder['path']));
							if($db_folder['acl_read']>0)
							{
								$class='folder-shared';
							}else
							{
								$class='filetype-folder';
							}

							$folder['path']=strip_server_path($folder['path']);
							$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
							$folder['type']='Folder';
							$folder['mtime']=Date::get_timestamp($folder['mtime']);
							$folder['size']='-';
							$folder['extension']='folder';
							$response['results'][]=$folder;
						}
						
						
						if(isset($_POST['files_filter']))
						{
							$extensions = explode(',',$_POST['files_filter']);
						}
						

						$files = $fs->get_files_sorted($path, $sort, $dir);
						foreach($files as $file)
						{
							$extension = File::get_extension($file['name']);
							
							if(!isset($extensions) || in_array($extension, $extensions))
							{							
								$file['extension']=$extension;
								$file['path']=strip_server_path($file['path']);
								$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
								$file['type']=File::get_filetype_description($extension);
								$file['mtime']=Date::get_timestamp($file['mtime']);
								$file['size']=Number::format_size($file['size']);
								$response['results'][]=$file;
							}
						}
							
					}

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

								$response['success']=true;
									
								$response['data'] = $fs->get_folder(addslashes($path));
								$response['data']['name']=basename($path);
								$response['data']['path']=smart_stripslashes($_POST['path']);
								$response['data']['mtime']=Date::get_timestamp(filemtime($path));
								$response['data']['ctime']=Date::get_timestamp(filectime($path));
								$response['data']['atime']=Date::get_timestamp(fileatime($path));
								$response['data']['type']='<div class="go-grid-icon filetype-folder">Folder</div>';
								$response['data']['size']=Number::format_size(filesize($path));
								$response['data']['write_permission']=$fs->is_owner($GO_SECURITY->user_id, $path);
								$response['data']['notify']=$fs->is_notified(addslashes($path), $GO_SECURITY->user_id);

								break;


							case 'file_properties':

								$path = SERVER_PATH.smart_stripslashes($_POST['path']);

								if(!file_exists($path))
								{
									throw new Exception('File not found');
								}elseif(!$fs->has_read_permission($GO_SECURITY->user_id, $path))
								{
									throw new AccessDeniedException();
								}

								$extension=File::get_extension($path);

								$response['success']=true;
								$response['data'] = $fs->get_file(addslashes($path));
								$response['data']['name']=File::strip_extension(basename($path));
								$response['data']['path']=smart_stripslashes($_POST['path']);
								$response['data']['mtime']=Date::get_timestamp(filemtime($path));
								$response['data']['ctime']=Date::get_timestamp(filectime($path));
								$response['data']['atime']=Date::get_timestamp(fileatime($path));
								$response['data']['type']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.File::get_filetype_description($extension).'</div>';
								$response['data']['size']=Number::format_size(filesize($path));
								$response['data']['write_permission']=$fs->has_write_permission($GO_SECURITY->user_id, dirname($path));
								break;

							case 'templates':
								if(isset($_POST['delete_keys']))
								{
									try{
										$response['deleteSuccess']=true;
										$templates = json_decode(smart_stripslashes($_POST['delete_keys']));

										foreach($templates as $template_id)
										{
											$fs->delete_template(addslashes($template_id));
										}
									}catch(Exception $e)
									{
										$response['deleteSuccess']=false;
										$response['deleteFeedback']=$e->getMessage();
									}
								}

								if(isset($_POST['writable_only']))
								{
									$response['total'] = $fs->get_writable_templates($GO_SECURITY->user_id);
								}else
								{
									$response['total'] = $fs->get_authorized_templates($GO_SECURITY->user_id);
								}
								$response['results']=array();
								while($fs->next_record(MYSQL_ASSOC))
								{
									$user = $GO_USERS->get_user($fs->f('user_id'));


									$fs->Record['user_name'] = String::format_name($user);
									$fs->Record['type'] = File::get_filetype_description($fs->f('extension'));
									$fs->Record['grid_display']='<div class="go-grid-icon filetype filetype-'.$fs->f('extension').'">'.$fs->f('name').'</div>';
									$response['results'][] = $fs->Record;
								}

								break;

							case 'template':
								$response['data']=$fs->get_template(smart_addslashes($_POST['template_id']));
								$user = $GO_USERS->get_user($response['data']['user_id']);
								$response['data']['user_name']=String::format_name($user);
								$response['success']=true;
								break;

	}

}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);