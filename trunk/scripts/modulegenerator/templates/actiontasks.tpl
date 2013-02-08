		
		case 'save_{friendly_single}':		
			<gotpl if="$module_admin_authenticate">if(!$GLOBALS['GO_MODULES']->modules['{module}']['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			</gotpl>
			${friendly_single}_id=${friendly_single}['id']=isset($_POST['{friendly_single}_id']) ? $_POST['{friendly_single}_id'] : 0;
			
			<gotpl if="$authenticate_relation && $relation">
			${related_friendly_single} = ${module}->get_{related_friendly_single}($_POST['{related_field_id}']);
			
			if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, ${related_friendly_single}['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
			{
				throw new AccessDeniedException();
			}
			</gotpl>
			
			{ACTIONFIELDS}

			if(${friendly_single}['id']>0)
			{
				${module}->update_{friendly_single}(${friendly_single});
				$response['success']=true;
				$insert=false;
			}else
			{
				${friendly_single}['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				<gotpl if="$authenticate">
				$response['acl_id']=${friendly_single}['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('{friendly_single}');
				</gotpl>
				
				${friendly_single}_id= ${module}->add_{friendly_single}(${friendly_single});

				<gotpl if="$files">
				if($GLOBALS['GO_MODULES']->modules['files'])
				{
					require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
					$fs = new files();

					$response['files_path']='{module}/'.${friendly_single}_id;						
					$full_path = $GLOBALS['GO_CONFIG']->file_storage_path.$response['files_path'];
					$fs->check_share($full_path, $GLOBALS['GO_SECURITY']->user_id, <gotpl if="$authenticate">${friendly_single}['acl_id']</gotpl><gotpl if="$authenticate_relation">${related_friendly_single}['acl_id']</gotpl>);
				}
				</gotpl>				

				$response['{friendly_single}_id']=${friendly_single}_id;
				$response['success']=true;
				
				$insert=true;
			}
			
			<gotpl if="$link_type&gt;0">
			if($GLOBALS['GO_MODULES']->has_module('customfields'))
			{
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, ${friendly_single}_id, {link_type}, $_POST, $insert);
			}			
				
			if(!empty($_POST['link']))
			{
				require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
				$GO_LINKS = new GO_LINKS();

				$link_props = explode(':', $_POST['link']);
				$GO_LINKS->add_link(
				$link_props[1],
				$link_props[0],
				${friendly_single}_id,
				{link_type});
			}
			</gotpl>
		
			break;
