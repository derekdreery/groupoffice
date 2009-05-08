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
require_once($GO_CONFIG->class_path.'File.class.inc.php');
require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc.php");
$files = new files();

require($GO_LANGUAGE->get_language_file('files'));

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';
$response=array();

try{

	switch($task)
	{
		case 'tree':

			$fs2= new files();
			
			$home_id = 'users/'.$_SESSION['GO_SESSION']['username'];
			$home_folder=$files->resolve_path($home_id);
			$node = isset($_POST['node']) ? ($_POST['node']) : 'root';

			switch($node)
			{
				case 'root':
						
					/*Home folder with children */
					$folders = $files->get_folders($home_folder['id']);

					$children = array();
					while($folder=$files->next_record())
					{
						$node= array(
						'text'=>$folder['name'],
						'id'=>$folder['id'],
						'notreloadable'=>true			
						);

						if($folder['acl_read']>0)
						{
							$node['iconCls']='folder-shared';
						}else
						{
							$node['iconCls']='folder-default';
						}

						$fs2->get_folders($folder['id']);
						if(!$fs2->found_rows())
						{
							$node['children']=array();
							$node['expanded']=true;
						}

						$children[]=$node;
					}
						
						

					$node= array(
					'text'=>$lang['files']['personal'],
					'id'=>$home_folder['id'],
					'iconCls'=>'folder-home',
					'expanded'=>true,
					'children'=>$children,
					'notreloadable'=>true			
					);
					$response[]=$node;
						
						
						
						
					/* All shares */
						
						
						
				/*	$share_count = $files->get_authorized_shares($GO_SECURITY->user_id);
					$children = array();
					while ($files->next_record())
					{
						$share_id = $files->f('id');
						if (file_exists($share_id))
						{
							if (is_dir($share_id))
							{
								$is_sub_dir = isset($last_folder) ? $files->is_sub_dir($share_id, $last_folder) : false;

								if (!$is_sub_dir)
								{
									$last_folder = $files->f('id');

									$node = array(
											'text'=>utf8_basename($files->f('id')),
											'id'=>$files->strip_server_id($files->f('id')),
											'iconCls'=>'folder-default',
											'reloadable'=>false
											);
											if(!$files->get_folders($files->f('id')))
											{
												$node['children']=array();
												$node['expanded']=true;
											}
											$children[]=$node;
								}
							}
						}
					}*/
						
						
					$node= array(
					'text'=>$lang['files']['shared'],
					'id'=>'shared',
					'readonly'=>true,
					'iconCls'=>'folder-shares'/*,
					'expanded'=>true,
					'children'=>$children,
					'notreloadable'=>true				*/	
					);
					$response[]=$node;
					
					if(isset($GO_MODULES->modules['projects']) && $GO_MODULES->modules['projects']['read_permission'])
					{
						require($GO_LANGUAGE->get_language_file('projects'));
						$node= array(
							'text'=>$lang['projects']['projects'],
							'id'=>'projects',
							'readonly'=>true,
							'iconCls'=>'go-link-icon-5'
							);
							$response[]=$node;
					}
					
					
					$num_new_files = $files->get_num_new_files($GO_SECURITY->user_id);
					
					$node= array(
					'text'=>$lang['files']['new'].' ('.$num_new_files.')',
					'id'=>'new',
					'readonly'=>true,
					'children'=>array(),
					'expanded'=>true,
					'iconCls'=>'folder-new'
					);
					$response[]=$node;

					break;


				case 'shared':

					$share_count = $files->get_authorized_shares($GO_SECURITY->user_id);

					$count = 0;
					while ($files->next_record())
					{
						$share_id = $GO_CONFIG->file_storage_id.$files->f('id');

						if (is_dir($share_id))
						{
							$is_sub_dir = isset($last_folder) ? $files->is_sub_dir($share_id, $last_folder) : false;

							if (!$is_sub_dir)
							{									
								$last_folder = $share_id;

								$node = array(
										'text'=>utf8_basename($files->f('id')),
										'id'=>$files->f('id'),
										'iconCls'=>'folder-default',
										'notreloadable'=>true
										);
										if(!$files->get_folders($share_id))
										{
											$node['children']=array();
											$node['expanded']=true;
										}
										$response[]=$node;
							}
						}
										
					}
					break;
					
				case 'new' :
					
					$response['success'] = true;
					break;

				default:

					$folder = $files->get_folder($_POST['node']);
					if(!$files->has_read_permission($GO_SECURITY->user_id, $folder))
					{
						throw new AccessDeniedException();
					}
					
					$folders = $files->get_folders($_POST['node']);

					$children = array();
					while($folder=$files->next_record())
					{
						$node= array(
						'text'=>$folder['name'],
						'id'=>$folder['id'],
						'notreloadable'=>true			
						);

						if($folder['acl_read']>0)
						{
							$node['iconCls']='folder-shared';
						}else
						{
							$node['iconCls']='folder-default';
						}

						$fs2->get_folders($folder['id']);
						if(!$fs2->found_rows())
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
					
					if(isset($_SESSION['GO_SESSION']['files']['jupload_new_files']) && count($_SESSION['GO_SESSION']['files']['jupload_new_files']))
					{						
						$files->notify_users($_POST['id'],$GO_SECURITY->user_id, array(), $_SESSION['GO_SESSION']['files']['jupload_new_files']);
						
						$_SESSION['GO_SESSION']['files']['jupload_new_files']=array();
					}
					
					if($_POST['id'] == 'shared')
					{
						if(isset($_POST['delete_keys']))
						{
							$response['deleteSuccess']=false;
							$response['deleteFeedback']=$lang['common']['accessDenied'];
						}
						$response['write_permission']=false;
						$share_count = $files->get_authorized_shares($GO_SECURITY->user_id);

						while ($files->next_record())
						{
							$share_id = $GO_CONFIG->file_storage_id.$files->f('id');
							if (file_exists($share_id))
							{
								if (is_dir($share_id))
								{
									$is_sub_dir = isset($last_folder) ? $files->is_sub_dir($share_id, $last_folder) : false;

									if (!$is_sub_dir)
									{
										$last_folder = $share_id;
										$folder['type_id']='d:'.$folder['id'];
										$folder['name']=utf8_basename($share_id);
										$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder.png';										
										$folder['id']=$files->f('id');
										//$folder['grid_display']='<div class="go-grid-icon filetype-folder">'.$folder['name'].'</div>';
										$folder['type']=$lang['files']['folder'];
										$folder['mtime']=Date::get_timestamp(filemtime($share_id));
										$folder['size']='-';
										$folder['extension']='folder';
										$response['results'][]=$folder;
									}
								}
							}
						}
					}elseif($_POST['id'] == 'new')
					{
						require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

						$sort = isset($_POST['sort']) ? $_POST['sort'] : 'mtime';
						$dir = isset($_POST['dir']) ? $_POST['dir'] : 'DESC';
						
						if($sort == 'grid_display') $sort = 'name';

						$files = $files->get_new_files($GO_SECURITY->user_id, $sort, $dir);
						foreach($files as $file)
						{
							$extension = File::get_extension($file['name']);
						
							$file['type_id']='f:'.$file['id'];
							$file['thumb_url']=$files->get_thumb_url($file['id']);						
							$file['extension']=$extension;
							//$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
							$file['type']=File::get_filetype_description($extension);
							$file['timestamp']=$file['mtime'];
							$file['mtime']=Date::get_timestamp($file['mtime']);
							$file['size']=Number::format_size($file['size']);
							$response['results'][]=$file;
						}
						
						$response['write_permission'] = false;
						$response['thumbs']=0;
						$response['num_files'] = count($files);
						
					}else
					{					
						$curfolder = $files->get_folder($_POST['id']);						
						$response['thumbs']=$curfolder['thumbs'];
						$response['parent_id']=$curfolder['parent_id'];
						
						/*if($db_folder['thumbs']=='0' && !empty($_POST['thumbs']))
						{
							$up_folder['id']=$db_folder['id'];
							$up_folder['thumbs']='1';
							
							$files->update_folder($up_folder);
							$response['thumbs']='1';
							
						}*/
						
						/*if(!empty($_POST['create_id']) && !is_dir($folder['id']))
						{
							mkdir($id, 0755, true);
						}*/

						$response['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $curfolder);
						if(!$response['write_permission'] && !$files->has_read_permission($GO_SECURITY->user_id, $curfolder))
						{
							throw new AccessDeniedException();
						}						

						if(isset($_POST['delete_keys']))
						{
							try{
								
								require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
								$quota = new quota();
								
								$response['deleteSuccess']=true;
								$delete_ids = json_decode($_POST['delete_keys']);

								$deleted = array();
								foreach($delete_ids as $delete_type_id)
								{
									$ti = explode(':',$delete_type_id);
									
									if($ti[0]=='f')
									{
										if(!$response['write_permission'])
										{
											throw new AccessDeniedException();
										}
										$file = $files->get_file($ti[1]);
										$deleted[]=$file['name'];
										$files->delete_file($file);
									}else
									{
										$folder = $files->get_folder($ti[1]);
										$files->delete_folder($folder);
										$deleted[]=$file['name'];
									}									
									
								}
								
								$files->notify_users($_POST['id'], $GO_SECURITY->user_id, array(), array(), $deleted);
			
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
								$template = $files->get_template($_POST['template_id'], true);
								
								$path = $files->build_path($curfolder);

								$new_path = $GO_CONFIG->file_storage_path.$files->build_path($curfolder).'/'.$_POST['template_name'].'.'.$template['extension'];
								file_put_contents($new_path, $template['content']);
								/*$fp = fopen($new_path, "w+");
								fputs($fp, $template['content']);
								fclose($fp);*/
								
								$file = $files->import_file($new_path,$curfolder['id']);

								$response['new_id']=$file['id'];
							}

							try{
								if(isset($_POST['compress_sources']) && isset($_POST['archive_name']))
								{
									$compress_sources = json_decode($_POST['compress_sources'],true);
									$archive_name = $_POST['archive_name'].'.zip';
										
									function strip_id($client_id)
									{
										global $id, $GO_CONFIG;
										return '.'.substr($GO_CONFIG->file_storage_id.$client_id, strlen($id));
									}
										
									$compress_sources=array_map('strip_id', $compress_sources);

									//var_dump($compress_sources);
									if(file_exists($id.'/'.$archive_name))
									{
										throw new Exception($lang['files']['filenameExists']);
									}
										
									chdir($id);
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
									chdir($id);
									$decompress_sources = json_decode(($_POST['decompress_sources']));
									while ($file = array_shift($decompress_sources)) {
										switch(File::get_extension($file))
										{
											case 'zip':
												exec($GO_CONFIG->cmd_unzip.' "'.$GO_CONFIG->file_storage_id.$file.'"');
												break;

											case 'gz':
											case 'tgz':
												exec($GO_CONFIG->cmd_tar.' zxf "'.$GO_CONFIG->file_storage_id.$file.'"');
												break;

											case 'tar':
												exec($GO_CONFIG->cmd_tar.' xf "'.$GO_CONFIG->file_storage_id.$file.'"');
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

						$fsort = isset($_POST['sort']) ? $_POST['sort'] : 'name';
						$dsort = isset($_POST['sort']) ? $_POST['sort'] : 'name';
						if($dsort=='size')
						{
							$dsort='name';
						}
						$dir = isset($_POST['dir']) ? $_POST['dir'] : 'ASC';

						

						require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

						$files->get_folders($curfolder['id'],$dsort,$dir);
						while($folder = $files->next_record())
						{							
							if($folder['acl_read']>0)
							{
								$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder_public.png';
								$class='folder-shared';
							}else
							{
								$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder.png';
								$class='filetype-folder';
							}							
							
							$folder['type_id']='d:'.$folder['id'];
							$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
							$folder['type']=$lang['files']['folder'];
							$folder['timestamp']=$folder['ctime'];
							$folder['mtime']=Date::get_timestamp($folder['ctime']);
							$folder['size']='-';
							$folder['extension']='folder';
							$response['results'][]=$folder;
						}
						
						
						if(!empty($_POST['files_filter']))
						{
							$extensions = explode(',',$_POST['files_filter']);
						}
						

						$files->get_files($curfolder['id'], $fsort, $dir);
						while($file = $files->next_record())
						{
							$extension = File::get_extension($file['name']);
							
							if(!isset($extensions) || in_array($extension, $extensions))
							{		
								$file['type_id']='f:'.$file['id'];
								$file['thumb_url']=$files->get_thumb_url($file['id']);								
								$file['extension']=$extension;								
								$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
								$file['type']=File::get_filetype_description($extension);
								$file['timestamp']=$file['mtime'];
								$file['mtime']=Date::get_timestamp($file['mtime']);
								//$file['size']=Number::format_size($file['size']);
								$response['results'][]=$file;
							}
						}
							
					}

					break;

							case 'folder_properties':

		
								$folder = $files->get_folder($_POST['folder_id']);
								if(!$folder)
								{
									throw new FileNotFoundException();
								}elseif(!$files->has_read_permission($GO_SECURITY->user_id, $folder))
								{
									throw new AccessDeniedException();
								}

								$response['success']=true;
								
								$admin = $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id);
									
								$response['data'] = $folder;
								$path=$files->build_path($folder);
								$response['data']['path']=$path;
								$response['data']['ctime']=Date::get_timestamp(filectime($GO_CONFIG->file_storage_path.$path));
								$response['data']['mtime']=Date::get_timestamp(fileatime($GO_CONFIG->file_storage_path.$path));
								$response['data']['atime']=Date::get_timestamp(filemtime($GO_CONFIG->file_storage_path.$path));

								$response['data']['type']='<div class="go-grid-icon filetype-folder">'.$lang['files']['folder'].'</div>';
								$response['data']['size']='-';
		
								$response['data']['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $folder);
								$response['data']['is_owner']=$admin || $files->is_owner($folder);
								
								$usersfolder = $files->resolve_path('users');
								$response['data']['is_home_dir']=$folder['parent_id']==$usersfolder['id'];
								$response['data']['notify']=$files->is_notified($folder['id'], $GO_SECURITY->user_id);

								break;


							case 'file_properties':
								
								$file = $files->get_file($_POST['file_id']);
								$folder = $files->get_folder($file['folder_id']);
								if(!$folder)
								{
									throw new FileNotFoundException();
								}elseif(!$files->has_read_permission($GO_SECURITY->user_id, $folder))
								{
									throw new AccessDeniedException();
								}

								$extension=File::get_extension($file['name']);

								$response['success']=true;
					
								$response['data'] = $file;
								$path=$files->build_path($folder).'/'.$file['name'];
								$response['data']['path']=$path;
								$response['data']['name']=File::strip_extension($file['name']);
								$response['data']['ctime']=Date::get_timestamp(filectime($GO_CONFIG->file_storage_path.$path));
								$response['data']['mtime']=Date::get_timestamp(fileatime($GO_CONFIG->file_storage_path.$path));
								$response['data']['atime']=Date::get_timestamp(filemtime($GO_CONFIG->file_storage_path.$path));
								$response['data']['type']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.File::get_filetype_description($extension).'</div>';
								$response['data']['size']=Number::format_size($file['size']);
								$response['data']['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $folder);
								
								$params['response']=&$response;								
								$GO_EVENTS->fire_event('load_file_properties', $params);
								
								break;

							case 'templates':
								if(isset($_POST['delete_keys']))
								{
									try{
										$response['deleteSuccess']=true;
										$templates = json_decode(($_POST['delete_keys']));

										foreach($templates as $template_id)
										{
											$files->delete_template($template_id);
										}
									}catch(Exception $e)
									{
										$response['deleteSuccess']=false;
										$response['deleteFeedback']=$e->getMessage();
									}
								}

								if(isset($_POST['writable_only']))
								{
									$response['total'] = $files->get_writable_templates($GO_SECURITY->user_id);
								}else
								{
									$response['total'] = $files->get_authorized_templates($GO_SECURITY->user_id);
								}
								$response['results']=array();
								while($files->next_record(DB_ASSOC))
								{
									$user = $GO_USERS->get_user($files->f('user_id'));


									$files->record['user_name'] = String::format_name($user);
									$files->record['type'] = File::get_filetype_description($files->f('extension'));
									$files->record['grid_display']='<div class="go-grid-icon filetype filetype-'.$files->f('extension').'">'.$files->f('name').'</div>';
									$response['results'][] = $files->record;
								}

								break;

							case 'template':
								$response['data']=$files->get_template(($_POST['template_id']));
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