
		<gotpl if="$link_type &gt; 0">case '{friendly_single}_with_items':</gotpl>
		case '{friendly_single}':
		
			<gotpl if="$module_admin_authenticate">if(!$GO_MODULES->modules['{module}']['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			</gotpl>

			${friendly_single} = ${module}->get_{friendly_single}(smart_addslashes($_REQUEST['{friendly_single}_id']));
			<gotpl if="$authenticate_relation && $relation">
			${related_friendly_single} = ${module}->get_{related_friendly_single}(${friendly_single}['{related_field_id}']);
			${friendly_single}[{related_friendly_single}_name]=${related_friendly_single}['name'];
			</gotpl>
			<gotpl if="$user_id">
			$user = $GO_USERS->get_user(${friendly_single}['user_id']);
			${friendly_single}['user_name']=String::format_name($user);
			</gotpl>
			<gotpl if="$mtime">
			${friendly_single}['mtime']=Date::get_timestamp(${friendly_single}['mtime']);
			</gotpl>
			<gotpl if="$ctime">
			${friendly_single}['ctime']=Date::get_timestamp(${friendly_single}['ctime']);
			</gotpl>
			
			$response['data']=${friendly_single};
			<gotpl if="$authenticate_relation && $relation">
			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, ${related_friendly_single}['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, ${related_friendly_single}['acl_read']))
			{
				throw new AccessDeniedException();
			}
			</gotpl>
			
			<gotpl if="$files">
			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
				$fs = new files();

				$response['data']['files_path']='{module}/'.$response['data']['id'];

				$full_path = $GO_CONFIG->file_storage_path.$response['data']['files_path'];
				if(!file_exists($full_path))
				{
					$fs->mkdir_recursive($full_path);

					if(!$fs->get_folder(addslashes($full_path)))
					{
						$folder['user_id']=$response['data']['user_id'];
						$folder['path']=addslashes($full_path);
						$folder['visible']='0';
						<gotpl if="$authenticate">
						$folder['acl_read']=${friendly_single}['acl_read'];
						$folder['acl_write']=${friendly_single}['acl_write'];
						</gotpl>
						<gotpl if="$authenticate_relation">
						$folder['acl_read']=${related_friendly_single}['acl_read'];
						$folder['acl_write']=${related_friendly_single}['acl_write'];
						</gotpl>

						$fs->add_folder($folder);
					}
				}
			}
			</gotpl>			
			$response['success']=true;
			
			<gotpl if="$link_type &gt; 0">
			if($task=='{friendly_single}')
			{
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, {link_type}, $response['data']['id']);				
					$response['data']=array_merge($response['data'], $values);			
				}
				break;
			}else
			{
					
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$response['data']['customfields']=
						$cf->get_all_fields_with_values(
							$GO_SECURITY->user_id, {link_type}, $response['data']['id']);			
				}

				
				require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
				$search = new search();
			
				$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], {link_type});				
				$response['data']['links']=$links_json['results'];				

				<gotpl if="$files">
				if(isset($GO_MODULES->modules['files']))
				{
					$response['data']['files']=$fs->get_content_json($full_path);
				}else
				{
					$response['data']['files']=array();				
				}
				</gotpl>

				break;
			}			
			</gotpl>
			<gotpl if="!$link_type">
			break;
			</gotpl>
			

				
		case '{friendly_multiple}':
		
			<gotpl if="$authenticate">
			$auth_type = isset($_POST['auth_type']) ? smart_addslashes($_POST['auth_type']) : 'write';
			
			</gotpl>
			<gotpl if="$relation">
			${related_field_id}=smart_addslashes($_POST['{related_field_id}']);
			${related_friendly_single} = ${module}->get_{related_friendly_single}(${related_field_id});
			$response['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, ${related_friendly_single}['acl_write']);
			if(!$response['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, ${related_friendly_single}['acl_read']))
			{
				throw new AccessDeniedException();
			}
			</gotpl>

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_{friendly_multiple} = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_{friendly_multiple} as ${friendly_single}_id)
					{
						${module}->delete_{friendly_single}(addslashes(${friendly_single}_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			$query = isset($_REQUEST['query']) ? '%'.smart_addslashes($_REQUEST['query']).'%' : '';
			
			$response['total'] = ${module}->get_<gotpl if="$authenticate">authorized_</gotpl>{friendly_multiple}(<gotpl if="$authenticate">$auth_type, $GO_SECURITY->user_id, </gotpl><gotpl if="$relation">${related_field_id}, </gotpl> $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while(${module}->next_record())
			{
				${friendly_single} = ${module}->Record;
				
				<gotpl if="$user_id">
				$user = $GO_USERS->get_user(${friendly_single}['user_id']);
				${friendly_single}['user_name']=String::format_name($user);
				</gotpl>
				<gotpl if="$mtime">
				${friendly_single}['mtime']=Date::get_timestamp(${friendly_single}['mtime']);
				</gotpl>
				<gotpl if="$ctime">
				${friendly_single}['ctime']=Date::get_timestamp(${friendly_single}['ctime']);
				</gotpl>
								
				$response['results'][] = ${friendly_single};
			}

			break;
			