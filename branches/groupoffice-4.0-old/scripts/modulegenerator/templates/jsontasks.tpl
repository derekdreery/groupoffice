
		<gotpl if="$link_type &gt; 0">case '{friendly_single}_with_items':</gotpl>
		case '{friendly_single}':
		
			<gotpl if="$module_admin_authenticate">if(!$GLOBALS['GO_MODULES']->modules['{module}']['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			</gotpl>

			${friendly_single} = ${module}->get_{friendly_single}($_REQUEST['{friendly_single}_id']);
			<gotpl if="$authenticate_relation && $relation">
			${related_friendly_single} = ${module}->get_{related_friendly_single}(${friendly_single}['{related_field_id}']);
			${friendly_single}['{related_friendly_single}_name']=${related_friendly_single}['name'];
			</gotpl>
			<gotpl if="$user_id">
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			
			${friendly_single}['user_name']= $GO_USERS->get_user_realname(${friendly_single}['user_id']);
			</gotpl>
			<gotpl if="$mtime">
			${friendly_single}['mtime']=Date::get_timestamp(${friendly_single}['mtime']);
			</gotpl>
			<gotpl if="$ctime">
			${friendly_single}['ctime']=Date::get_timestamp(${friendly_single}['ctime']);
			</gotpl>
			
			$response['data']=${friendly_single};
			<gotpl if="$authenticate_relation && $relation">
			$response['data']['permission_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, ${related_friendly_single}['acl_id']);
			$response['data']['write_permission']=$response['permission_level']>GO_SECURITY::READ_PERMISSION;
			if(!$response['data']['permission_level'])
			{
				throw new AccessDeniedException();
			}
			</gotpl>
			
			<gotpl if="$files">
			if(isset($GLOBALS['GO_MODULES']->modules['files']))
			{
				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();

				$response['data']['files_path']='{module}/'.$response['data']['id'];

				$full_path = $GLOBALS['GO_CONFIG']->file_storage_path.$response['data']['files_path'];
				$fs->check_share($full_path, $response['data']['user_id'], <gotpl if="$authenticate">${friendly_single}['acl_id']</gotpl><gotpl if="$authenticate_relation">${related_friendly_single}['acl_id']</gotpl>);
			}
			</gotpl>			
			$response['success']=true;
			
			<gotpl if="$link_type &gt; 0">
			if($task=='{friendly_single}')
			{
				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, {link_type}, $response['data']['id']);				
					$response['data']=array_merge($response['data'], $values);			
				}
				break;
			}else
			{
					
				if($GLOBALS['GO_MODULES']->has_module('customfields'))
				{
					require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$response['data']['customfields']=
						$cf->get_all_fields_with_values(
							$GLOBALS['GO_SECURITY']->user_id, {link_type}, $response['data']['id']);			
				}

				
				require_once($GLOBALS['GO_CONFIG']->class_path.'/base/search.class.inc.php');
				$search = new search();
			
				$links_json = $search->get_latest_links_json($GLOBALS['GO_SECURITY']->user_id, $response['data']['id'], {link_type});				
				$response['data']['links']=$links_json['results'];				

				<gotpl if="$files">
				if(isset($GLOBALS['GO_MODULES']->modules['files']))
				{
					$response['data']['files']=$fs->get_content_json($full_path);
				}else
				{
					$response['data']['files']=array();				
				}
				</gotpl>
				
				if(isset($GLOBALS['GO_MODULES']->modules['comments']))
				{
					require_once ($GLOBALS['GO_MODULES']->modules['comments']['class_path'].'comments.class.inc.php');
					$comments = new comments();
					
					$response['data']['comments']=$comments->get_comments_json($response['data']['id'], {link_type});
				}
				break;
			}			
			</gotpl>
			<gotpl if="!$link_type">
			break;
			</gotpl>
			

				
		case '{friendly_multiple}':
		
			<gotpl if="$authenticate">
			$auth_type = isset($_POST['auth_type']) ? $_POST['auth_type'] : 'write';
			
			</gotpl>
			<gotpl if="$relation">
			${related_field_id}=$_POST['{related_field_id}'];
			${related_friendly_single} = ${module}->get_{related_friendly_single}(${related_field_id});
			$response['permission_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, ${related_friendly_single}['acl_id']);
			$response['data']['write_permission']=$response['permission_level']>GO_SECURITY::READ_PERMISSION;
			if(!$response['data']['permission_level'])
			{
				throw new AccessDeniedException();
			}
			</gotpl>

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_{friendly_multiple} = json_decode($_POST['delete_keys']);

					foreach($delete_{friendly_multiple} as ${friendly_single}_id)
					{
						${module}->delete_{friendly_single}(${friendly_single}_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';

			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';
			
			$response['total'] = ${module}->get_<gotpl if="$authenticate">authorized_</gotpl>{friendly_multiple}(<gotpl if="$authenticate">$auth_type, $GLOBALS['GO_SECURITY']->user_id, </gotpl><gotpl if="$relation">${related_field_id}, </gotpl> $query, $sort, $dir, $start, $limit);
			$response['results']=array();

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			while(${friendly_single} = ${module}->next_record())
			{
				<gotpl if="$user_id">				
				${friendly_single}['user_name']=$GO_USERS->get_user_realname(${friendly_single}['user_id']);
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
			