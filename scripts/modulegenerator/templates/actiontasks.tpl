		
		case 'save_{friendly_single}':		
			<gotpl if="$module_admin_authenticate">if(!$GO_MODULES->modules['{module}']['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			</gotpl>
			${friendly_single}_id=${friendly_single}['id']=isset($_POST['{friendly_single}_id']) ? $_POST['{friendly_single}_id'] : 0;
			
			<gotpl if="$authenticate_relation && $relation">
			${related_friendly_single} = ${module}->get_{related_friendly_single}($_POST['{related_field_id}']);
			
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, ${related_friendly_single}['acl_write']))
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
				${friendly_single}['user_id']=$GO_SECURITY->user_id;
				<gotpl if="$authenticate">
				$response['acl_read']=${friendly_single}['acl_read']=$GO_SECURITY->get_new_acl('{friendly_single}');
				$response['acl_write']=${friendly_single}['acl_write']=$GO_SECURITY->get_new_acl('{friendly_single}');
				</gotpl>
				
				${friendly_single}_id= ${module}->add_{friendly_single}(${friendly_single});

				<gotpl if="$files">
				if($GO_MODULES->modules['files'])
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
					$fs = new files();

					$response['files_path']='{module}/'.${friendly_single}_id;						
					$full_path = $GO_CONFIG->file_storage_path.$response['files_path'];
					$fs->check_share($full_path, $GO_SECURITY->user_id, <gotpl if="$authenticate">${friendly_single}['acl_read'],${friendly_single}['acl_write']</gotpl><gotpl if="$authenticate_relation">${related_friendly_single}['acl_read'],${related_friendly_single}['acl_write']</gotpl>);
				}
				</gotpl>				

				$response['{friendly_single}_id']=${friendly_single}_id;
				$response['success']=true;
				
				$insert=true;
			}
			
			<gotpl if="$link_type&gt;0">
			if($GO_MODULES->has_module('customfields'))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GO_SECURITY->user_id, ${friendly_single}_id, {link_type}, $_POST, $insert);
			}			
				
			if(!empty($_POST['link']))
			{
				$link_props = explode(':', $_POST['link']);
				$GO_LINKS->add_link(
				$link_props[1],
				$link_props[0],
				${friendly_single}_id,
				{link_type});
			}
			</gotpl>
		
			break;
