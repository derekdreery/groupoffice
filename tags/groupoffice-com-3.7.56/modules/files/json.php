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

//task grids may write to session
if($task != 'grid')
	session_write_close();

try {

	switch($task) {
		case 'sendfile':
			

			$file = $files->get_file($_POST['file_id']);
			$up_file['id'] = $file['id'];
			$expire_time = isset($_POST['expire_time']) ? $_POST['expire_time'] : $file['expire_time'];
			$random_code = $up_file['random_code'] = !empty($file['random_code']) ? $file['random_code'] : String::random_password('a-z,1-9','i,o',11);

			$up_file['expire_time']=Date::date_add($expire_time, 1);
			$files->update_file($up_file);



			if ($_POST['content_type']!='plain') {
				$downloadLink = $lang['files']['clickHereToDownload'].':'." ".
					'<a href="'.$GO_MODULES->modules['files']['full_url']. 'download.php?id='.$_POST['file_id'].'&random_code='.$random_code.'">'.$file['name'].'</a> ';
				$lb = "<br />";
			} else {
				$downloadLink = $lang['files']['copyPasteToDownload'].':'."\n\n" .
					$GO_MODULES->modules['files']['full_url']. 'download.php?id='.$_POST['file_id'].'&random_code='.$random_code."\n\n";
				$lb = "\n";
			}

			$_POST['body'] = $downloadLink.'('.$lang['files']['possibleUntil'].' '.date($_SESSION['GO_SESSION']['date_format'],$expire_time).')'.$lb.$lb;

			if(!empty($_POST['template_id'])) {
				require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
				$response = load_template($_POST['template_id']);

				require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
				$go2mime = new Go2Mime();
				$response['data']['attachments']=$go2mime->remove_inline_images($response['data']['attachments']);
			}else
			{
				$response['data']['body']=$_POST['body'];
			}

			if (empty($response['data']['subject']))
				$response['data']['subject'] = $lang['files']['downloadLink'].' '.$file['name'];

			if($_POST['content_type']=='plain') {
				$response['data']['textbody']=$response['data']['body'];
				unset($response['data']['body']);
			}

			$response['success']=true;

			break;


		case 'tree':
			if(!empty($_POST['sync_folder_id']) && is_numeric($_POST['sync_folder_id'])) {
				$folder = $files->get_folder($_POST['sync_folder_id']);
				$files->sync_folder($folder);
			}

			if(!empty($_POST['sync_folder_id'])){
				//this will rebuild the cached shares folder
				$GO_CONFIG->save_setting('fs_shared_cache', 0, $GO_SECURITY->user_id);
			}

			$fs2= new files();

			function get_node_children($folder_id, $authenticate=false, $expand_folder_ids=array(), $inherit_parent_permissions=true) {
				global $fs2;

				go_debug("get_node_children($folder_id, $authenticate, $expand_folder_ids, $inherit_parent_permissions)");

				$files = new files();

				$children = array();
				$files->get_folders($folder_id,'name','ASC', 0,200, $authenticate, $inherit_parent_permissions);
				while($folder=$files->next_record()) {
					$node= array(
						'text'=>$folder['name'],
						'id'=>$folder['id'],
						'notreloadable'=>true
					);

					if($folder['readonly']=='1') {
						$node['draggable']=false;
					}

					if($folder['acl_id']>0) {
						$node['iconCls']='folder-shared';
					}else {
						$node['iconCls']='folder-default';
					}
					if(in_array($folder['id'], $expand_folder_ids)) {

						//this is an addressbook or projects folder and then we should not inherit permissions
						$special_shared_folder = $folder['parent_id']==0;

						$node['children']=get_node_children($folder['id'], true, $expand_folder_ids, !$special_shared_folder);
						$node['expanded']=true;
					}else	if(!$fs2->has_children($folder['id'])) {
						$node['children']=array();
						$node['expanded']=true;
					}
					$children[]=$node;
				}
				return $children;
			}

			$node = isset($_POST['node']) ? $_POST['node'] : 'root';

			switch($node) {
				case 'root':
					if(!empty($_POST['root_folder_id'])) {
						$folder = $files->get_folder($_POST['root_folder_id']);
						$files->check_folder_sync($folder);
					}else {
						/*Home folder with children */
						$home_path = 'users/'.$_SESSION['GO_SESSION']['username'];
						$folder=$files->resolve_path($home_path);

						$files->check_folder_sync($folder, $home_path);
					}

					$expand_folder_ids=array();
					if(!empty($_POST['expand_folder_id'])) {
						//This is the active folder. We need to make sure this folder is
						//provided
						$pathinfo=array();
						$path = $files->build_path($_POST['expand_folder_id'], $pathinfo);

						$files->check_folder_sync($folder, $path);

						$expand_folder_ids=array();
						foreach($pathinfo as $expandfolder) {
							$expand_folder_ids[] = $expandfolder['id'];
						}
					}

					if(!empty($_POST['root_folder_id'])) {
						$node= array(
							'text'=>$folder['name'],
							'id'=>$folder['id'],
							'expanded'=>true,
							'draggable'=>false,
							'iconCls'=>'folder-default',
							'children'=>get_node_children($folder['id'], true, $expand_folder_ids, true),
							'notreloadable'=>true
						);
						$response[]=$node;

					}else {
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
							'iconCls'=>'folder-shares',
							//'expanded'=>true,
							//'children'=>$children
						);
						$response[]=$node;


						if($GO_MODULES->has_module('projects')) {
							$GO_LANGUAGE->require_language_file('projects');

							$projects_folder = $files->resolve_path('projects');
							$node= array(
								'text'=>$lang['projects']['projects'],
								'id'=>$projects_folder['id'],
								'iconCls'=>'folder-default',
								'draggable'=>false,
								'allowDrop'=>false,
								'notreloadable'=>true
							);

							if(in_array($projects_folder['id'], $expand_folder_ids)) {
								$node['children']=get_node_children($projects_folder['id'], true, $expand_folder_ids, false);
								$node['expanded']=true;
							}
							$response[]=$node;
						}


						if($GO_MODULES->has_module('addressbook')) {
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
							if(in_array($contacts_folder['id'], $expand_folder_ids)) {
								$node['children']=get_node_children($contacts_folder['id'], true, $expand_folder_ids, false);
								$node['expanded']=true;
							}
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
							if(in_array($companies_folder['id'], $expand_folder_ids)) {
								$node['children']=get_node_children($companies_folder['id'], true, $expand_folder_ids, false);
								$node['expanded']=true;
							}
							$response[]=$node;
						}


						if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id)) {

							$log_folder = $files->resolve_path('log',true);
							if($log_folder) {
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
						}


						if(isset($GO_MODULES->modules['newfiles']) && !empty($GO_MODULES->modules['newfiles']['read_permission'])){
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
					}

					break;


				case 'shared':

					$share_count = $files->get_cached_shares($GO_SECURITY->user_id, false, 0,100);

					$nodes=array();

					$count = 0;
					while ($r = $files->next_record()) {
						//$is_sub_dir = isset($last_folder) ? $files->is_sub_dir($share_id, $last_folder) : false;

						$node = array(
							'text'=>$r['name'],
							'id'=>$r['folder_id'],
							'iconCls'=>'folder-default',
							'notreloadable'=>true
						);

						if(!$fs2->has_children($node['id'])) {
							$node['children']=array();
							$node['expanded']=true;
						}						
						$response[]=$node;
					}
					
					break;

				case 'new' :

					$response['success'] = true;
					break;

				default:

					$folder = $files->get_folder($_POST['node']);

					if(!$folder) {
						throw new FileNotFoundException();
					}

					$authenticate=!$files->is_owner($folder);

					//this is an addressbook or projects folder and then we should not inherit permissions
					$special_shared_folder = $folder['parent_id']==0;

					$response = get_node_children($_POST['node'], $authenticate, array(), !$special_shared_folder);
					break;
			}

			break;

		case 'grid':

			require_once($GO_CONFIG->class_path.'base/theme.class.inc.php');
			$GO_THEME = new GO_THEME();


			if(empty($_POST['id'])) {
				throw new Exception('No location given');
			}

			if(!empty($_POST['empty_new_files'])) {
				$files->delete_all_new_filelinks($GO_SECURITY->user_id);
			}

			$response['results']=array();

			if(isset($_SESSION['GO_SESSION']['files']['jupload_new_files']) && count($_SESSION['GO_SESSION']['files']['jupload_new_files'])) {
				$files->notify_users($_POST['id'],$GO_SECURITY->user_id, array(), $_SESSION['GO_SESSION']['files']['jupload_new_files']);

				$_SESSION['GO_SESSION']['files']['jupload_new_files']=array();
			}

			session_write_close();

			if($_POST['id'] == 'shared') {
				$response['parent_id']=0;
				if(isset($_POST['delete_keys'])) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$lang['common']['accessDenied'];
				}
				$response['write_permission']=false;

				$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
				$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';


				$response['total']= $share_count = $files->get_cached_shares($GO_SECURITY->user_id, true, $start, $limit);

				while($folder = $files->next_record()){
					$folder['thumb_url']=$GO_THEME->image_url.'128x128/filetypes/folder.png';
					$class='filetype-folder';
					
					$folder['type_id']='d:'.$folder['id'];
					$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
					$folder['type']=$lang['files']['folder'];
					$folder['timestamp']=$folder['ctime'];
					$folder['mtime']=Date::get_timestamp($folder['mtime']);
					$folder['size']='-';
					$folder['extension']='folder';
					if($folder['readonly']=='1') {
						$folder['draggable']=false;
					}
					$response['results'][]=$folder;
				}

			}elseif($_POST['id'] == 'new') {
				$response['parent_id']=0;
				
				//require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

				$sort = isset($_POST['sort']) ? $_POST['sort'] : 'mtime';
				$dir = isset($_POST['dir']) ? $_POST['dir'] : 'DESC';


				//if($sort == 'grid_display') $sort = 'name';

				$response['num_files'] = $files->get_new_files($GO_SECURITY->user_id, $sort, $dir);
				while($file = $files->next_record()) {
					$extension = File::get_extension($file['name']);

					if(!isset($extensions) || in_array($extension, $extensions)) {
						$file['type_id']='f:'.$file['id'];
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

			}else {
				$curfolder = $files->get_folder($_POST['id']);

				if(!$curfolder)
					throw new FileNotFoundException();

				$response['thumbs']=$curfolder['thumbs'];
				$response['parent_id']=$curfolder['parent_id'];
				$response['current_id']=$_POST['id'];
				/*if($db_folder['thumbs']=='0' && !empty($_POST['thumbs']))
						 {
							$up_folder['id']=$db_folder['id'];
							$up_folder['thumbs']='1';

							$files->update_folder($up_folder);
							$response['thumbs']='1';

							}*/

				$response['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $curfolder);				

				$_usersfolder = $files->resolve_path('users');
				$response['may_apply_state'] = $curfolder['apply_state']==1 && (
						$response['write_permission'] ||
						($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) || $files->is_owner($curfolder)) || // is_owner;
						$curfolder['parent_id']==$_usersfolder['id'] || // is_home_dir
						$curfolder['user_id']==$GO_SECURITY->user_id // user_id_match
					);

				$response['lock_state'] = $curfolder['apply_state']==1;
				$response['cm_state'] = $curfolder['cm_state'];

				if(!$response['write_permission'] && !$files->has_read_permission($GO_SECURITY->user_id, $curfolder)) {
					throw new AccessDeniedException();
				}

				$authenticate=!$files->is_owner($curfolder);

				$path = $response['path']=$files->build_path($curfolder);


				$response['refreshed']=$files->check_folder_sync($curfolder, $path);



				if(isset($_POST['delete_keys'])) {
					try {

						$response['deleteSuccess']=true;
						$delete_ids = json_decode($_POST['delete_keys']);						

						$folder_delete_permission = $files->has_delete_permission($GO_SECURITY->user_id, $curfolder);

						$deleted = array();
						foreach($delete_ids as $delete_type_id) {
							$ti = explode(':',$delete_type_id);

							if($ti[0]=='f') {
								if(!$folder_delete_permission) {
									throw new AccessDeniedException();
								}
								$file = $files->get_file($ti[1]);
								$deleted[]=$file['name'];
								$files->delete_file($file);
							}else {

								$folder = $files->get_folder($ti[1]);
								if(!$folder_delete_permission) {
									throw new AccessDeniedException();
								}
								$files->delete_folder($folder);
								$deleted[]=$folder['name'];
							}
						}



						$files->notify_users($curfolder, $GO_SECURITY->user_id, array(), array(), $deleted);

					}catch(Exception $e) {
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}

				if($response['write_permission']) {
					if(!empty($_POST['template_id']) && !empty($_POST['template_name'])) {
						$template = $files->get_template($_POST['template_id'], true);

						$new_path = $GO_CONFIG->file_storage_path.$files->build_path($curfolder).'/'.$_POST['template_name'];
						if(!empty($template['extension']))
							$new_path .= '.'.$template['extension'];

						file_put_contents($new_path, $template['content']);
						/*$fp = fopen($new_path, "w+");
								 fputs($fp, $template['content']);
								 fclose($fp);*/

						$response['new_id'] = $files->import_file($new_path,$curfolder['id']);
					}

					try {
						if(isset($_POST['compress_sources']) && isset($_POST['archive_name'])) {

							if(!is_windows())
								putenv('LANG=en_US.UTF-8');

							$compress_sources = json_decode($_POST['compress_sources'],true);
							$archive_name = $_POST['archive_name'].'.zip';

							if(!empty($_POST['compress_id'])) {
								$compress_rel_path = $files->build_path($_POST['compress_id']);
								$full_path = $GO_CONFIG->file_storage_path.dirname($compress_rel_path);
								$compress_sources=array($compress_rel_path);
							}else {
								$full_path = $GO_CONFIG->file_storage_path.$path;

								if(file_exists($full_path.'/'.$archive_name)) {
									throw new Exception($lang['files']['filenameExists']);
								}
							}

							$compress_sources = array_map('utf8_basename', $compress_sources);

							chdir($full_path);

							$cmd = $GO_CONFIG->cmd_zip.' -r "'.$archive_name.'" "'.implode('" "',$compress_sources).'"';

							exec($cmd, $output);

							$full_file_path = $full_path.'/'.$archive_name;
							if(!file_exists($full_file_path)) {
								throw new Exception('Command failed: '.$cmd."<br /><br />".implode("<br />", $output));
							}

							if($full_path!=$GO_CONFIG->file_storage_path.$path) {
								$new_full_file_path =$GO_CONFIG->file_storage_path.$path.'/'.utf8_basename($full_file_path);
								rename($full_file_path, $new_full_file_path);
								$full_file_path=$new_full_file_path;
							}

							$response['compress_success']=true;
							;
							$files->import_file($full_file_path,$curfolder['id']);

						}
					}catch(Exception $e) {
						$response['compress_success']=false;
						$response['compress_feedback']=$e->getMessage();
					}

					try {
						if(isset($_POST['decompress_sources'])) {
							if(!is_windows())
								putenv('LANG=en_US.UTF-8');

							$full_path=$GO_CONFIG->file_storage_path.$path;

							chdir($full_path);
							$decompress_sources = json_decode($_POST['decompress_sources']);
							while ($file = array_shift($decompress_sources)) {
								switch(File::get_extension($file)) {
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
							$files->import_folder($full_path, $curfolder['parent_id'], true);

							//TODO error handling
							$response['decompress_success']=true;
						}
					}catch(Exception $e) {
						$response['decompress_success']=false;
						$response['decompress_feedback']=$e->getMessage();
					}
				}

				$fsort = isset($_POST['sort']) ? $_POST['sort'] : 'name';

				if($fsort=='type') {
					$fsort='extension';
				}

				$dsort = isset($_POST['sort']) ? $_POST['sort'] : 'name';
				if($dsort!='name' || $dsort !='mtime' || $dsort=='type') {
					$dsort='name';
				}
				$dir = isset($_POST['dir']) ? $_POST['dir'] : 'ASC';

				$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
				$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';

				//require_once($GO_CONFIG->control_path.'phpthumb/phpThumb.config.php');

				//$response['path']=$path;

				//this is an addressbook or projects folder and then we should not inherit permissions
				$special_shared_folder = $curfolder['parent_id']==0;
				$authenticate=strpos('users/'.$_SESSION['GO_SESSION']['username'], $path)===false;

				$response['total']=$files->get_folders($curfolder['id'],$dsort,$dir,$start,$limit,$authenticate, !$special_shared_folder);

				$thumb_location = get_thumb_dir();

				while($folder = $files->next_record()) {
					if($folder['acl_id']>0) {
						$folder['thumb_url']=$thumb_location['url'].'folder_public.png';
						//$class='folder-shared';
					}else {
						$folder['thumb_url']=$thumb_location['url'].'folder.png';
						//$class='filetype-folder';
					}

					$folder['path']=$path.'/'.$folder['name'];
					$folder['type_id']='d:'.$folder['id'];
					//$folder['grid_display']='<div class="go-grid-icon '.$class.'">'.$folder['name'].'</div>';
					$folder['type']=$lang['files']['folder'];
					$folder['timestamp']=$folder['ctime'];
					$folder['mtime']=Date::get_timestamp(filemtime($GO_CONFIG->file_storage_path.$folder['path']));
					$folder['size']='-';
					$folder['extension']='folder';
					$response['results'][]=$folder;
				}
				$count = count($response['results']);


				$folder_pages = floor($response['total']/$limit);
				$folders_on_last_page = $response['total']-($folder_pages*$limit);

				if($count) {
					$file_start = $start - ($folder_pages*$limit);
					$file_limit = $limit-$folders_on_last_page;
				}else {
					$file_start = $start - $response['total'];
					$file_limit = $limit;
				}
				if(!empty($_POST['files_filter'])) {
					$extensions = explode(',',$_POST['files_filter']);
				}

				if(!isset($extensions) || $extensions!='foldersonly'){
					if($file_start>=0) {

						if($GO_MODULES->has_module('customfields')) {
							require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
							$cf = new customfields();
						}else {
							$cf=false;
						}

						$response['total']+=$files->get_files($curfolder['id'], $fsort, $dir, $file_start, $file_limit);


						while($file = $files->next_record()) {

							if(!isset($extensions) || in_array(File::get_extension($file['name']), $extensions)) {
								$file['path']=$path.'/'.$file['name'];
								$file['type_id']='f:'.$file['id'];
								$file['thumb_url']=get_thumb_url($file['path']);
								//$file['extension']=$extension;
								$file['grid_display']='<div class="go-grid-icon filetype filetype-'.$file['extension'].'">'.$file['name'].'</div>';
								$file['type']=File::get_filetype_description($file['extension']);
								$file['timestamp']=$file['mtime'];
								$file['mtime']=Date::get_timestamp($file['mtime']);
								//$file['size']=Number::format_size($file['size']);

								if($cf)
									$cf->format_record($file, 6);

								$response['results'][]=$file;
							}
						}
					}else {
						//$files->get_files($curfolder['id'], $fsort, $dir, 0, 1);
						$response['total']+=$files->count_files($curfolder['id']);
					}
				}

			}

			break;


		case 'versions':

			$path = $files->get_versions_dir($_POST['file_id']);

			$fs = new filesystem();
			$fs_files = $fs->get_files($path);

			$response['results']=array();
			$response['total']=count($fs_files);
			while($file=array_shift($fs_files)) {
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

			$sortBy = $_POST['sort'];
			$dir = $_POST['dir']=='ASC' ? 1 : -1;
			global $sortBy, $dir;
			
			function sortResults($a,$b) {
				global $sortBy, $dir;					
				if ($sortBy!='grid_display')
					return $dir*($b[$sortBy]-$a[$sortBy]);
				else
					return $dir*(strcmp($a['name'],$b['name']));
			}
			
			usort($response['results'],'sortResults');
			$response['total'] = 3;

			break;

		case 'folder_properties':


			$folder = $files->get_folder($_POST['folder_id']);
			if(!$folder) {
				throw new FileNotFoundException();
			}elseif(!$files->has_read_permission($GO_SECURITY->user_id, $folder)) {
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

      $permission_level = $files->get_permission_level($GO_SECURITY->user_id, $folder);

      $response['data']['manage_permission']=$permission_level==  GO_SECURITY::MANAGE_PERMISSION;
      $response['data']['write_permission']=empty($response['data']['readonly']) && $permission_level>GO_SECURITY::READ_PERMISSION;
			//$response['data']['is_owner']=$admin || $files->is_owner($folder);

			$usersfolder = $files->resolve_path('users');
			$response['data']['is_home_dir']=$folder['parent_id']==$usersfolder['id'];
			//$response['data']['user_id_match']=$folder['user_id']==$GO_SECURITY->user_id;
			$response['data']['notify']=$files->is_notified($folder['id'], $GO_SECURITY->user_id);

			$params['response']=&$response;
			$GO_EVENTS->fire_event('load_folder_properties', $params);
			break;


		case 'file_with_items':
		case 'file_properties':

			$file = $files->get_file($_POST['file_id']);
			$folder = $files->get_folder($file['folder_id']);
			if(!$folder) {
				throw new FileNotFoundException();
			}elseif(!$files->has_read_permission($GO_SECURITY->user_id, $folder)) {
				throw new AccessDeniedException();
			}

			$extension=File::get_extension($file['name']);

			$response['success']=true;

			$response['data'] = $file;
			$relpath = $files->build_path($folder).'/';
			$pdf_path=$path=$relpath.$file['name'];

			$GO_EVENTS->fire_event('file_extension_and_path_known',array(&$files,$extension,$path,&$pdf_path,$file,$folder));


      if(!empty($file['random_code']) && time()<$file['expire_time'])
      {
        $response['data']['expire_time']=Date::get_timestamp($file['expire_time'],false);
        $response['data']['download_link']='<a href="'.$GO_MODULES->modules['files']['full_url'].'download.php?id='.$file['id'].'&random_code='.$file['random_code'].'">'.$GO_MODULES->modules['files']['full_url'].'download.php?id='.$file['id'].'&random_code='.$file['random_code'].'</a>';

      }
      else
      {
        $response['data']['expire_time']=null;
        $response['data']['download_link']=null;
      }

      
			$response['data']['path']=$pdf_path;
			$response['data']['name']=File::strip_extension($file['name']);
			$response['data']['ctime']=Date::get_timestamp(filectime($GO_CONFIG->file_storage_path.$path));
			$response['data']['mtime']=Date::get_timestamp(fileatime($GO_CONFIG->file_storage_path.$path));
			$response['data']['atime']=Date::get_timestamp(filemtime($GO_CONFIG->file_storage_path.$path));
			$response['data']['type']='<div class="go-grid-icon filetype filetype-'.$extension.'">'.File::get_filetype_description($extension).'</div>';
			$response['data']['size']=Number::format_size($file['size']);
			$response['data']['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $folder);

			switch($extension) {
				case 'jpg':
				case 'jpeg':
				case 'gif':
				case 'bmp':
				case 'tif':
				case 'png':
				case 'xmind':
					$response['data']['image_src'] = get_thumb_url($path,350,0,false);
					$response['data']['image_path'] = urlencode($path);
					$response['data']['image_name'] = basename($path);
					$response['data']['image_extension'] = $extension;
					if ($extension=='xmind')
						$response['data']['download_path'] = $GO_MODULES->modules['files']['url'].'download.php?path='.urlencode($path);
					else
						$response['data']['download_path'] = '';
					break;
				/*
				case 'xmind':
					$filename_no_ext = substr(basename($path),0,-5);
					$response['data']['image_src'] = get_thumb_url($path,350,0,false);
					$response['data']['image_path'] = 'thumbcache/'.basename($filename_no_ext).'jpeg';
					$response['data']['image_name'] = basename($filename_no_ext).'jpeg';
					$response['data']['image_extension'] = 'jpeg';
					break;
				 *
				 */
				default:
					$response['data']['image_src'] = '';
				  break;
			}

			if($task == 'file_properties') {
				if(isset($GO_MODULES->modules['customfields'])) {
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 6, $response['data']['id']);
					$response['data']=array_merge($response['data'], $values);
				}
			}else {

				$response['data']['location']=$files->build_path($file['folder_id']);

				$response['data']['comment']=String::text_to_html($response['data']['comments']);


				//set files in array so they won't be loaded by load_standard_info_panel_items
				$response['data']['files']=array();
				load_standard_info_panel_items($response, 6);
			}

			$params['response']=&$response;
			$params['task']=$task;
			$params['file']=$file;
			$params['folder']=$folder;
			$GO_EVENTS->fire_event('load_file_properties', $params);

			break;


		case 'folder_with_items':

			$folder = $files->get_folder($_POST['folder_id']);
			if(!$folder) {
				throw new FileNotFoundException();
			}elseif(!$files->has_read_permission($GO_SECURITY->user_id, $folder)) {
				throw new AccessDeniedException();
			}		

			$response['success']=true;

			$response['data'] = $folder;
			$path=$files->build_path($folder);
			$response['data']['path']=$path;
			$response['data']['ctime']=Date::get_timestamp(filectime($GO_CONFIG->file_storage_path.$path));
			$response['data']['mtime']=Date::get_timestamp(fileatime($GO_CONFIG->file_storage_path.$path));
			$response['data']['atime']=Date::get_timestamp(filemtime($GO_CONFIG->file_storage_path.$path));
			$response['data']['type']='<div class="go-grid-icon filetype filetype-folder">Folder</div>';
			$response['data']['write_permission']=$files->has_write_permission($GO_SECURITY->user_id, $folder);			

			$response['data']['location']=$path;
			$response['data']['comment']=String::text_to_html($response['data']['comments']);			
	
			//set files in array so they won't be loaded by load_standard_info_panel_items
			$response['data']['files']=array();
			load_standard_info_panel_items($response, 17);

			break;

		case 'templates':

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			if(isset($_POST['delete_keys'])) {
				try {
					$response['deleteSuccess']=true;
					$templates = json_decode(($_POST['delete_keys']));

					foreach($templates as $template_id) {
						$files->delete_template($template_id);
					}
				}catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			if(isset($_POST['writable_only'])) {
				$response['total'] = $files->get_writable_templates($GO_SECURITY->user_id);
			}else {
				$response['total'] = $files->get_authorized_templates($GO_SECURITY->user_id);
			}
			$response['results']=array();
			while($files->next_record(DB_ASSOC)) {
				$files->record['user_name'] = $GO_USERS->get_user_realname($files->f('user_id'));
				$files->record['type'] = File::get_filetype_description($files->f('extension'));
				$files->record['grid_display']='<div class="go-grid-icon filetype filetype-'.$files->f('extension').'">'.$files->f('name').'</div>';
				$response['results'][] = $files->record;
			}

			break;

		case 'template':
			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			
			$response['data']=$files->get_template($_POST['template_id']);			
			$response['data']['user_name']=$GO_USERS->get_user_realname($response['data']['user_id']);
			$response['success']=true;
			break;

	}

}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);