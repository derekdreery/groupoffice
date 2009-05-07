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
$fs = new files();

require($GO_LANGUAGE->get_language_file('files'));

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';
$response=array();

try{

	switch($task)
	{
		case 'tree':

			$fs2= new files();
			
			$home_id = 'users/'.$_SESSION['GO_SESSION']['username'];
			$home_folder=$fs->resolve_path($home_id);
			$node = isset($_POST['node']) ? ($_POST['node']) : 'root';

			switch($node)
			{
				case 'root':
						
					/*Home folder with children */
					$folders = $fs->get_folders($home_folder['id']);

					$children = array();
					while($folder=$fs->next_record())
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
						
						
						
				/*	$share_count = $fs->get_authorized_shares($GO_SECURITY->user_id);
					$children = array();
					while ($fs->next_record())
					{
						$share_id = $fs->f('id');
						if (file_exists($share_id))
						{
							if (is_dir($share_id))
							{
								$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_id, $last_folder) : false;

								if (!$is_sub_dir)
								{
									$last_folder = $fs->f('id');

									$node = array(
											'text'=>utf8_basename($fs->f('id')),
											'id'=>$fs->strip_server_id($fs->f('id')),
											'iconCls'=>'folder-default',
											'reloadable'=>false
											);
											if(!$fs->get_folders($fs->f('id')))
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
					
					
					$num_new_files = $fs->get_num_new_files($GO_SECURITY->user_id);
					
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

					$share_count = $fs->get_authorized_shares($GO_SECURITY->user_id);

					$count = 0;
					while ($fs->next_record())
					{
						$share_id = $GO_CONFIG->file_storage_id.$fs->f('id');

						if (is_dir($share_id))
						{
							$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_id, $last_folder) : false;

							if (!$is_sub_dir)
							{									
								$last_folder = $share_id;

								$node = array(
										'text'=>utf8_basename($fs->f('id')),
										'id'=>$fs->f('id'),
										'iconCls'=>'folder-default',
										'notreloadable'=>true
										);
										if(!$fs->get_folders($share_id))
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

					$folder = $fs->get_folder($_POST['node']);
					if(!$fs->has_read_permission($GO_SECURITY->user_id, $folder))
					{
						throw new AccessDeniedException();
					}
					
					$folders = $fs->get_folders($_POST['node']);

					$children = array();
					while($folder=$fs->next_record())
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
						$fs->notify_users($_POST['id'],$GO_SECURITY->user_id, array(), $_SESSION['GO_SESSION']['files']['jupload_new_files']);
						
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
						$share_count = $fs->get_authorized_shares($GO_SECURITY->user_id);

						while ($fs->next_record())
						{
							$share_id = $GO_CONFIG->file_storage_id.$fs->f('id');
							if (file_exists($share_id))
							{
								if (is_dir($share_id))
								{
									$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_id, $last_folder) : false;

									if (!$is_sub_dir)
									{
										$last_folder = $share_id;
										$folder['name']=utf8_basename($share_id);
										$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder.png';										
										$folder['id']=$fs->f('id');
										$folder['grid_display']='<div class="go-grid-icon filetype-folder">'.$folder['name'].'</div>';
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

						$files = $fs->get_new_files($GO_SECURITY->user_id, $sort, $dir);
						foreach($files as $file)
						{
							$extension = File::get_extension($file['name']);
						
							$file['thumb_url']=$fs->get_thumb_url($file['id']);						
							$file['extension']=$extension;
							$file['id']=$fs->strip_server_id($file['id']);
							$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
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
												
						
						$curfolder = $fs->get_folder($_POST['id']);						
						$response['thumbs']=$curfolder['thumbs'];
						$response['parent_id']=$curfolder['parent_id'];
						
						/*if($db_folder['thumbs']=='0' && !empty($_POST['thumbs']))
						{
							$up_folder['id']=$db_folder['id'];
							$up_folder['thumbs']='1';
							
							$fs->update_folder($up_folder);
							$response['thumbs']='1';
							
						}*/
						
						/*if(!empty($_POST['create_id']) && !is_dir($folder['id']))
						{
							mkdir($id, 0755, true);
						}*/

						$response['write_permission']=$fs->has_write_permission($GO_SECURITY->user_id, $curfolder);
						if(!$response['write_permission'] && !$fs->has_read_permission($GO_SECURITY->user_id, $curfolder))
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
								foreach($delete_ids as $delete_id)
								{
									if(!$fs->has_write_permission($GO_SECURITY->user_id, $delete_id))
									{
										throw new AccessDeniedException();
									}
									
									//$quota->delete($id);
									
									if(!$fs->delete($GO_CONFIG->file_storage_id.$delete_id))
									{
										throw new AccessDeniedException();
									}
									
									$deleted[]=utf8_basename($delete_id);
								}
								
								$fs->notify_users($_POST['id'], $GO_SECURITY->user_id, array(), array(), $deleted);
			
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
								$template = $fs->get_template($_POST['template_id'], true);

								$new_id = $id.'/'.$_POST['template_name'].'.'.$template['extension'];
								file_put_contents($new_id, $template['content']);

								$response['new_id']=$fs->strip_server_id($new_id);
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

						$sort = isset($_POST['sort']) ? $_POST['sort'] : 'name';
						$dir = isset($_POST['dir']) ? $_POST['dir'] : 'ASC';

						

						require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

						$fs->get_folders($curfolder['id'],$sort,$dir);
						while($folder = $fs->next_record())
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
						

						$files = $fs->get_files($curfolder['id'], $sort, $dir);
						while($file = $fs->next_record())
						{
							$extension = File::get_extension($file['name']);
							
							if(!isset($extensions) || in_array($extension, $extensions))
							{		
								$file['thumb_url']=$fs->get_thumb_url($file['id']);								
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

								$id = $GO_CONFIG->file_storage_id.$_POST['id'];

								if(!file_exists($id))
								{
									throw new Exception($lang['files']['fileNotFound']);
								}elseif(!$fs->has_read_permission($GO_SECURITY->user_id, $id))
								{
									throw new AccessDeniedException();
								}

								$response['success']=true;
								
								$admin = $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id);
									
								$response['data'] = $fs->get_folder($_POST['id']);
								$response['data']['name']=utf8_basename($_POST['id']);
								$response['data']['id']=$_POST['id'];
								$response['data']['mtime']=Date::get_timestamp(filemtime($id));
								$response['data']['ctime']=Date::get_timestamp(filectime($id));
								$response['data']['atime']=Date::get_timestamp(fileatime($id));
								$response['data']['type']='<div class="go-grid-icon filetype-folder">'.$lang['files']['folder'].'</div>';
								$response['data']['size']=Number::format_size(filesize($id));
								//$response['data']['write_permission']=$admin || $fs->is_owner($GO_SECURITY->user_id, $_POST['id']);
								$response['data']['write_permission']=$fs->has_write_permission($GO_SECURITY->user_id, $id);
								$response['data']['is_owner']=$admin || $fs->is_owner($GO_SECURITY->user_id, $_POST['id']);
								$response['data']['is_home_dir']=utf8_basename(dirname($id)) == 'users';
								$response['data']['notify']=$fs->is_notified($_POST['id'], $GO_SECURITY->user_id);

								break;


							case 'file_properties':
								
								if(is_numeric($_POST['id']))
								{
									$response['data'] = $fs->get_file_by_id($_POST['id']);
									if(!$response['data'])
									{
										throw new DatabaseSelectException();
									}
									$_POST['id']=$response['data']['id'];
								}
								$id = $GO_CONFIG->file_storage_id.$_POST['id'];

								if(!file_exists($id))
								{
									throw new Exception('File not found: '.$_POST['id']);
								}elseif(!$fs->has_read_permission($GO_SECURITY->user_id, $id))
								{
									throw new AccessDeniedException();
								}

								$extension=File::get_extension($id);

								$response['success']=true;
								if(!isset($response['data']))
								{
									$response['data'] = $fs->get_file($_POST['id']);
								}
								$response['data']['name']=File::strip_extension(utf8_basename($_POST['id']));
								//$response['data']['id']=$_POST['id'];
								$response['data']['mtime']=Date::get_timestamp(filemtime($id));
								$response['data']['ctime']=Date::get_timestamp(filectime($id));
								$response['data']['atime']=Date::get_timestamp(fileatime($id));
								$response['data']['type']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.File::get_filetype_description($extension).'</div>';
								$response['data']['size']=Number::format_size(filesize($id));
								$response['data']['write_permission']=$fs->has_write_permission($GO_SECURITY->user_id, dirname($id));
								
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
											$fs->delete_template($template_id);
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
								while($fs->next_record(DB_ASSOC))
								{
									$user = $GO_USERS->get_user($fs->f('user_id'));


									$fs->record['user_name'] = String::format_name($user);
									$fs->record['type'] = File::get_filetype_description($fs->f('extension'));
									$fs->record['grid_display']='<div class="go-grid-icon filetype filetype-'.$fs->f('extension').'">'.$fs->f('name').'</div>';
									$response['results'][] = $fs->record;
								}

								break;

							case 'template':
								$response['data']=$fs->get_template(($_POST['template_id']));
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