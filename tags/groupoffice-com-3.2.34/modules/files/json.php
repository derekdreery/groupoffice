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
			/*if(!empty($_POST['refresh_folder_id']) && is_numeric($_POST['refresh_folder_id']))
			{
				$folder = $this->get_folder($_POST['refresh_folder_id']);
				$files->sync_folder($folder);
			}*/

			$fs2= new files();

			function get_node_children($folder_id, $authenticate=false, $expand_folder_ids=array())
			{
				global $fs2;

				$files = new files();

				$children = array();
				$files->get_folders($folder_id,'name','ASC', 0,200, $authenticate);
				while($folder=$files->next_record())
				{
					$node= array(
						'text'=>$folder['name'],
						'id'=>$folder['id'],
						'notreloadable'=>true
					);

					if($folder['readonly']=='1')
					{
							$node['draggable']=false;
					}

					if($folder['acl_read']>0)
					{
						$node['iconCls']='folder-shared';
					}else
					{
						$node['iconCls']='folder-default';
					}
					if(in_array($folder['id'], $expand_folder_ids))
					{
						$node['children']=get_node_children($folder['id'], $authenticate, $expand_folder_ids);
						$node['expanded']=true;
					}else	if(!$fs2->has_children($folder['id']))
					{
						$node['children']=array();
						$node['expanded']=true;
					}
					$children[]=$node;
				}
				return $children;
			}

			$node = isset($_POST['node']) ? $_POST['node'] : 'root';

			switch($node)
			{
				case 'root':
					if(!empty($_POST['root_folder_id']))
					{
						$folder = $files->get_folder($_POST['root_folder_id']);
						$files->check_folder_sync($folder);
					}else{
						/*Home folder with children */
						$home_path = 'users/'.$_SESSION['GO_SESSION']['username'];
						$folder=$files->resolve_path($home_path);

						$files->check_folder_sync($folder, $home_path);
					}

					$expand_folder_ids=array();
					if(!empty($_POST['expand_folder_id'])){
						//This is the active folder. We need to make sure this folder is
						//provided
						$pathinfo=array();
						$path = $files->build_path($_POST['expand_folder_id'], $pathinfo);

						$files->check_folder_sync($folder, $path);

						$expand_folder_ids=array();
						foreach($pathinfo as $expandfolder){
							$expand_folder_ids[] = $expandfolder['id'];
						}
					}

					if(!empty($_POST['root_folder_id']))
					{
						$node= array(
							'text'=>$folder['name'],
							'id'=>$folder['id'],
							'expanded'=>true,
               'draggable'=>false,
							'iconCls'=>'folder-default',
							'children'=>get_node_children($folder['id'], true, $expand_folder_ids),
							'notreloadable'=>true
						);
						$response[]=$node;

					}else
					{
						$files->get_folders($folder['id'],'name', 'ASC',0,200,false);
						
						$node= array(
						'text'=>$lang['files']['personal'],
						'id'=>$folder['id'],
						'iconCls'=>'folder-home',
						'expanded'=>true,
            'draggable'=>false,
						'children'=>get_node_children($folder['id'], false, $expand_folder_ids),
						'notreloadable'=>true
						);
						$response[]=$node;


						$node= array(
						'text'=>$lang['files']['shared'],
						'id'=>'shared',
						'readonly'=>true,
						'draggable'=>false,
						'allowDrop'=>false,
						'iconCls'=>'folder-shares'/*,
						'expanded'=>true,
						'children'=>$children,
						'notreloadable'=>true				*/
						);
						$response[]=$node;

						if($GO_MODULES->has_module('projects'))
						{
								require($GO_LANGUAGE->get_language_file('projects'));

								$projects_folder = $files->resolve_path('projects');
								$node= array(
								'text'=>$lang['projects']['projects'],
								'id'=>$projects_folder['id'],
								'iconCls'=>'folder-default',
								'draggable'=>false,
								'allowDrop'=>false,
								'notreloadable'=>true
								);
								$response[]=$node;
						}


						if($GO_MODULES->has_module('addressbook'))
						{
								require($GO_LANGUAGE->get_language_file('addressbook'));
								$contacts_folder = $files->resolve_path('contacts');
								$node= array(
								'text'=>$lang['addressbook']['contacts'],
								'id'=>$contacts_folder['id'],
								'iconCls'=>'folder-default',
								'draggable'=>false,
								'allowDrop'=>false,
								'notreloadable'=>true
								);
								$response[]=$node;

								$companies_folder = $files->resolve_path('companies');
								$node= array(
								'text'=>$lang['addressbook']['companies'],
								'id'=>$companies_folder['id'],
								'iconCls'=>'folder-default',
								'draggable'=>false,
								'allowDrop'=>false,
								'notreloadable'=>true
								);
								$response[]=$node;
						}


						if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
						{

								$log_folder = $files->resolve_path('log');
								$node= array(
								'text'=>$lang['commmon']['logFiles'],
								'id'=>$log_folder['id'],
								'iconCls'=>'folder-default',
								'draggable'=>false,
								'allowDrop'=>false,
								'notreloadable'=>true
								);
								$response[]=$node;
						}



						$num_new_files = $files->get_num_new_files($GO_SECURITY->user_id);

						$node= array(
						'text'=>$lang['files']['new'].' ('.$num_new_files.')',
						'id'=>'new',
						'allowDrop'=>false,
						'children'=>array(),
						'expanded'=>true,
                        'draggable'=>false,
						'iconCls'=>'folder-new'
						);
						$response[]=$node;
					}

					break;


				case 'shared':

					$share_count = $files->get_authorized_shares($GO_SECURITY->user_id);

          $nodes=array();

					$count = 0;
					while ($folder = $files->next_record())
					{
							//$is_sub_dir = isset($last_folder) ? $files->is_sub_dir($share_id, $last_folder) : false;

							$node = array(
											'text'=>$folder['name'],
											'id'=>$folder['id'],
											'iconCls'=>'folder-default',
											'notreloadable'=>true
							);

							$path = $fs2->build_path($folder);
							$nodes[$path]=$node;
					}
					ksort($nodes);

					$fs = new filesystem();


					foreach($nodes as $path=>$node)
					{
							$is_sub_dir = isset($last_path) ? $fs->is_sub_dir($path, $last_path) : false;
							if(!$is_sub_dir)
							{
								//var_dump($node);
									if(!$fs2->has_children($node['id']))
									{										
											$node['children']=array();
											$node['expanded']=true;
									}
									$response[]=$node;
									$last_path=$path;
							}
					}

					break;

				case 'new' :

					$response['success'] = true;
					break;

				default:

					$folder = $files->get_folder($_POST['node']);
					$authenticate=!$files->is_owner($folder);

					//$files->check_folder_sync($folder);

					$response = get_node_children($_POST['node'], $authenticate);

					break;
			}

			break;

				case 'grid':

					if(empty($_POST['id']))
					{
							throw new Exception('No location given');
					}

					if(!empty($_POST['empty_new_files']))
					{
						$files->delete_all_new_filelinks($GO_SECURITY->user_id);
					}

					$response['results']=array();

					if(isset($_SESSION['GO_SESSION']['files']['jupload_new_files']) && count($_SESSION['GO_SESSION']['files']['jupload_new_files']))
					{
						$files->notify_users($_POST['id'],$GO_SECURITY->user_id, array(), $_SESSION['GO_SESSION']['files']['jupload_new_files']);

						$_SESSION['GO_SESSION']['files']['jupload_new_files']=array();
					}

					if($_POST['id'] == 'shared')
					{
						$response['parent_id']=0;
						if(isset($_POST['delete_keys']))
						{
							$response['deleteSuccess']=false;
							$response['deleteFeedback']=$lang['common']['accessDenied'];
						}
						$response['write_permission']=false;

						$fs2 = new files();


						$share_count = $files->get_authorized_shares($GO_SECURITY->user_id);

						$folders=array();

						$count = 0;
						while ($folder = $files->next_record())
						{
								$path = $fs2->build_path($folder);
								$folders[$path]=$folder;
						}
						ksort($folders);

						$fs = new filesystem();


						foreach($folders as $path=>$folder)
						{
								$is_sub_dir = isset($last_path) ? $fs->is_sub_dir($path, $last_path) : false;
								if(!$is_sub_dir)
								{
										 $folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder.png';
										$class='filetype-folder';

										$folder['type_id']='d:'.$folder['id'];
										$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
										$folder['type']=$lang['files']['folder'];
										$folder['timestamp']=$folder['ctime'];
										$folder['mtime']=Date::get_timestamp($folder['ctime']);
										$folder['size']='-';
										$folder['extension']='folder';
										if($folder['readonly']=='1')
										{
												$folder['draggable']=false;
										}
										$response['results'][]=$folder;

										$last_path=$path;
								}
						}

					}elseif($_POST['id'] == 'new')
					{
						$response['parent_id']=0;

						require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

						$sort = isset($_POST['sort']) ? $_POST['sort'] : 'mtime';
						$dir = isset($_POST['dir']) ? $_POST['dir'] : 'DESC';
						

						//if($sort == 'grid_display') $sort = 'name';

						$response['num_files'] = $files->get_new_files($GO_SECURITY->user_id, $sort, $dir);
                        while($file = $files->next_record())
						{
							$extension = File::get_extension($file['name']);

							if(!isset($extensions) || in_array($extension, $extensions))
							{
								$file['type_id']='f:'.$file['id'];
								//$file['thumb_url']=$files->get_thumb_url($file['id']);
								$file['extension']=$extension;
								$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
								$file['type']=File::get_filetype_description($extension);
								$file['timestamp']=$file['mtime'];
								$file['mtime']=Date::get_timestamp($file['mtime']);
								//$file['size']=Number::format_size($file['size']);
								$response['results'][]=$file;
							}
						}

						$response['write_permission'] = false;
						$response['thumbs']=0;

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


						$response['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $curfolder);

						if(!$response['write_permission'] && !$files->has_read_permission($GO_SECURITY->user_id, $curfolder))
						{
							throw new AccessDeniedException();
						}

						$authenticate=!$files->is_owner($curfolder);

            $path = $files->build_path($curfolder);


						$response['refreshed']=$files->check_folder_sync($curfolder, $path);



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
										if(!$files->has_delete_permission($GO_SECURITY->user_id, $folder)){
											throw new AccessDeniedException();
										}
										$files->delete_folder($folder);
										$deleted[]=$folder['name'];
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

								$new_path = $GO_CONFIG->file_storage_path.$files->build_path($curfolder).'/'.$_POST['template_name'].'.'.$template['extension'];
								file_put_contents($new_path, $template['content']);
								/*$fp = fopen($new_path, "w+");
								 fputs($fp, $template['content']);
								 fclose($fp);*/

								$response['new_id'] = $files->import_file($new_path,$curfolder['id']);
							}

							try{
								if(isset($_POST['compress_sources']) && isset($_POST['archive_name']))
								{
									$compress_sources = json_decode($_POST['compress_sources'],true);
									$archive_name = $_POST['archive_name'].'.zip';

                                    $full_path = $GO_CONFIG->file_storage_path.$path;

									if(file_exists($full_path.'/'.$archive_name))
									{
										throw new Exception($lang['files']['filenameExists']);
									}

                  $compress_sources = array_map('utf8_basename', $compress_sources);

									chdir($full_path);

                  $cmd = $GO_CONFIG->cmd_zip.' -r "'.$archive_name.'" "'.implode('" "',$compress_sources).'"';

									exec($cmd, $output);

									if(!file_exists($full_path.'/'.$archive_name))
									{
											throw new Exception('Command failed: '.$cmd."<br /><br />".implode("<br />", $output));
									}

									$response['compress_success']=true;;
                  $files->import_file($full_path.'/'.$archive_name,$curfolder['id']);

								}
							}catch(Exception $e)
							{
								$response['compress_success']=false;
								$response['compress_feedback']=$e->getMessage();
							}

							try{
								if(isset($_POST['decompress_sources']))
								{
                  $full_path=$GO_CONFIG->file_storage_path.$path;

									chdir($full_path);
									$decompress_sources = json_decode($_POST['decompress_sources']);
									while ($file = array_shift($decompress_sources)) {
										switch(File::get_extension($file))
										{
											case 'zip':
												exec($GO_CONFIG->cmd_unzip.' "'.$GO_CONFIG->file_storage_path.$file.'"');
												break;

											case 'gz':
											case 'tgz':
												exec($GO_CONFIG->cmd_tar.' zxf "'.$GO_CONFIG->file_storage_path.$file.'"');
												break;

											case 'tar':
												exec($GO_CONFIG->cmd_tar.' xf "'.$GO_CONFIG->file_storage_path.$file.'"');
												break;
										}

									}

									//TODO sync only missing files
									$files->import_folder($full_path, $curfolder['parent_id']);

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

						$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
						$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';

						require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

						//$response['path']=$path;

						$files->get_folders($curfolder['id'],$dsort,$dir,$start,$limit,true);
						
						while($folder = $files->next_record())
						{
							if($folder['acl_read']>0)
							{
								$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder_public.png';
								//$class='folder-shared';
							}else
							{
								$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder.png';
								//$class='filetype-folder';
							}

              $folder['path']=$path.'/'.$folder['name'];
							$folder['type_id']='d:'.$folder['id'];
							//$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
							$folder['type']=$lang['files']['folder'];
							$folder['timestamp']=$folder['ctime'];
							$folder['mtime']=Date::get_timestamp($folder['ctime']);
							$folder['size']='-';
							$folder['extension']='folder';
							$response['results'][]=$folder;
						}
						$count = count($response['results']);
						$response['total']=$files->found_rows();

						$folder_pages = floor($response['total']/$limit);
						$folders_on_last_page = $response['total']-($folder_pages*$limit);

						if($count)
						{
							$file_start = $start - ($folder_pages*$limit);
							$file_limit = $limit-$folders_on_last_page;
						}else
						{
							$file_start = $start - $response['total'];
							$file_limit = $limit;
						}
						if(!empty($_POST['files_filter']))
						{
							$extensions = explode(',',$_POST['files_filter']);
						}

						if($file_start>=0)
						{
							$files->get_files($curfolder['id'], $fsort, $dir, $file_start, $file_limit);


							while($file = $files->next_record())
							{
								//$extension = File::get_extension($file['name']);

								//if(!isset($extensions) || in_array($extension, $extensions))
								//{
									$file['path']=$path.'/'.$file['name'];
									$file['type_id']='f:'.$file['id'];
									$file['thumb_url']=$files->get_thumb_url($file['path']);
									//$file['extension']=$extension;
									$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$file['extension'].'">'.$file['name'].'</div>';
									$file['type']=File::get_filetype_description($file['extension']);
									$file['timestamp']=$file['mtime'];
									$file['mtime']=Date::get_timestamp($file['mtime']);
									//$file['size']=Number::format_size($file['size']);
									$response['results'][]=$file;
								//}
							}
						}else
						{
							$files->get_files($curfolder['id'], $fsort, $dir, 0, 1);
						}
						$response['total']+=$files->found_rows();
					}

					break;


                case 'versions':

                    $path = $files->get_versions_dir($_POST['file_id']);

                    $fs = new filesystem();
                    $fs_files = $fs->get_files($path);

                    $response['results']=array();
                    $response['total']=count($fs_files);
                    while($file=array_shift($fs_files))
                    {
                        $extension = File::get_extension($file['name']);
                        $file['path']=$files->strip_server_path($file['path']);
                        $file['extension']=$extension;
                        $file['grid_display']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.$file['name'].'</div>';
                        $file['type']=File::get_filetype_description($extension);
                        $file['timestamp']=$file['mtime'];
                        $file['mtime']=Date::get_timestamp($file['mtime']);
                        $file['size']=Number::format_size($file['size']);
                        $response['results'][]=$file;
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

												$response['data']['write_permission']=empty($response['data']['readonly']) && $files->has_write_permission($GO_SECURITY->user_id, $folder);
												$response['data']['is_owner']=$admin || $files->is_owner($folder);

												$usersfolder = $files->resolve_path('users');
												$response['data']['is_home_dir']=$folder['parent_id']==$usersfolder['id'];
												$response['data']['notify']=$files->is_notified($folder['id'], $GO_SECURITY->user_id);

												$params['response']=&$response;
												$GO_EVENTS->fire_event('load_folder_properties', $params);
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