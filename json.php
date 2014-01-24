<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("Group-Office.php");

$GLOBALS['GO_SECURITY']->json_authenticate();

$GLOBALS['GO_SECURITY']->check_token();

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';


try {
	switch($_REQUEST['task']) {

//		case 'saved_advanced_queries':
//
//		require_once($GLOBALS['GO_CONFIG']->class_path.'advanced_query.class.inc.php');
//		$aq = new advanced_query();
//
//		if (isset($_POST['delete_keys'])) {
//			try {
//				$delete_sqls = json_decode($_POST['delete_keys']);
//
//				foreach ($delete_sqls as $id) {
//					$aq->delete_search_query($id);
//				}
//				$response['deleteSuccess'] = true;
//			} catch (Exception $e) {
//				$response['deleteFeedback'] = $e->getMessage();
//				$response['deleteSuccess'] = false;
//			}
//		}
//
//		$response['total'] = $aq->get_search_queries($GLOBALS['GO_SECURITY']->user_id);
//		$response['results'] = array();
//
//		while ($r = $aq->next_record())
//			$response['results'][] = $r;
//
//		$response['success'] = true;
//		break;

		case 'get_weeks':

			$start_time = mktime(0,0,0,1,1,$_POST['year']);
			$end_time = Date::date_add($start_time,0,0,1);

			$first_day = date('N', $start_time);
			$first_monday = ($first_day != 1) ? Date::date_add($start_time, 8-$first_day) : $start_time;
			if(date('W', $first_monday) == 2) {
				$first_monday = Date::date_add($first_monday, -7);
			}

			$weeks = array();
			for($i=$first_monday; $i<$end_time; $i=(Date::date_add($i, 7))) {
				if(date("o",$i) == $_POST['year']) {
					$week_nr = date("W",$i);
					$week['value'] = Date::get_last_sunday($i);
					$first_weekday = Date::date_add($first_monday, ($week_nr-1)*7);
					$last_weekday = Date::date_add($first_weekday, 6);

					$date_format = str_replace($_SESSION['GO_SESSION']['date_separator'].'Y', '', $_SESSION['GO_SESSION']['date_format']);

					$first_weekday = date($date_format,$first_weekday);
					$last_weekday = date($date_format,$last_weekday);
					$week['text'] = $week_nr.' ('.$first_weekday.')';// / '.$last_weekday.')';

					$weeks[] = $week;
				}
			}

			$response['results'] = $weeks;
			$response['success'] = true;
			break;

		case 'email_export_query':

			if(!empty($_POST['template_id'])) {
				require_once($GLOBALS['GO_MODULES']->modules['email']['class_path'].'email.class.inc.php');
				$response = load_template($_POST['template_id']);
			}



			require_once($GLOBALS['GO_CONFIG']->class_path.'/export/export_query.class.inc.php');
			$eq = new export_query();

			$type = $_REQUEST['type'];
			$filename = $type.'.class.inc.php';

//			$file = $GLOBALS['GO_CONFIG']->class_path.'export/'.$filename;
//			if(!file_exists($file)){
//				$file = $GLOBALS['GO_CONFIG']->file_storage_path.'customexports/'.$filename;
//			}

			if(isset($_REQUEST['export_directory']) && file_exists($GLOBALS['GO_CONFIG']->root_path.$_REQUEST['export_directory'].$filename)){
				$file = $GLOBALS['GO_CONFIG']->root_path.$_REQUEST['export_directory'].$filename;
			}else
			{
				$file = $GLOBALS['GO_CONFIG']->class_path.'export/'.$filename;
				if(!file_exists($file)){
					$file = $GLOBALS['GO_CONFIG']->file_storage_path.'customexports/'.$filename;
				}
				if(!file_exists($file)){
					die('Custom export class not found.');
				}
			}

			if(!file_exists($file)){
				die('Custom export class not found.');
			}
			require_once($file);
			$eq = new $type();

			$tmp_file = $GLOBALS['GO_CONFIG']->tmpdir.File::strip_invalid_chars($_POST['title']).'.'.$eq->extension;

			$fp = fopen($tmp_file, 'w+');
			$eq->export($fp);
			fclose($fp);

			$response['data']['attachments'][] = array(
							'tmp_name'=>$tmp_file,
							'name'=>utf8_basename($tmp_file),
							'size'=>filesize($tmp_file),
							'type'=>File::get_filetype_description(strtolower($_POST['type'])),
							'human_size'=>Number::format_size(filesize($tmp_file)),
							'extension'=>$eq->extension
			);

			require_once($GLOBALS['GO_CONFIG']->class_path.'mail/Go2Mime.class.inc.php');
			$go2mime = new Go2Mime();
			$response['data']['attachments']=$go2mime->remove_inline_images($response['data']['attachments']);

			$response['success']=true;
			break;

		case 'link_descriptions':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			if(isset($_POST['delete_keys'])) {
				try {
					$response['deleteSuccess']=true;
					$delete_link_descriptions = json_decode($_POST['delete_keys']);
					foreach($delete_link_descriptions as $link_description_id) {
						$GO_LINKS->delete_link_description(addslashes($link_description_id));
					}
				}catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';
			$response['total'] = $GO_LINKS->get_link_descriptions( $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($link_description = $GO_LINKS->next_record()) {
				$response['results'][] = $link_description;
			}

			break;

//		case 'settings':
//			$response['data']=array();
//
//			$params['response']=&$response;
//			$GLOBALS['GO_EVENTS']->fire_event('load_settings', $params);
//
//			$response['success']=true;
//			break;


//		case 'checker':
//
//			//close writing to session or this checker process will lock out other
//			//requests until it's complete. With flaky IMAP connections this can
//			//be a big problem.
//			session_write_close();
//
//			$response=array();
//
//			foreach($GLOBALS['GO_MODULES']->modules as $module) {
//				$lang_file = $GLOBALS['GO_LANGUAGE']->get_language_file($module['id']);
//
//				if(!empty($lang_file))
//					require($lang_file);
//			}
//
//			$response['notification_area']='';
//
//			require($GLOBALS['GO_CONFIG']->class_path.'base/reminder.class.inc.php');
//			$rm = new reminder();
//
//			$rm->get_reminders($GLOBALS['GO_SECURITY']->user_id);
//
//			while($reminder=$rm->next_record()) {
//				
//				$reminder['iconCls']=empty($reminder['link_type']) ? 'go-icon-reminders' : 'go-link-icon-'.$reminder['link_type'];
//				//$reminder['link_type_name']=isset($lang['link_type'][$reminder['link_type']]) ? $lang['link_type'][$reminder['link_type']] : $lang['common']['other'];
//
//				$now = getdate(time());
//				$today = mktime(0,0,0,$now['mon'],$now['mday'], $now['year']);
//
//				$time = ($reminder['vtime']) ? $reminder['vtime'] : $reminder['time'];
//				if($time == $today) {
//					$reminder['local_time']=date($_SESSION['GO_SESSION']['time_format'], $time);
//				}else {
//					$reminder['local_time']=date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $time);
//				}
//
//
//				$response['reminders'][]=$reminder;
//			}
//			$params = array(&$response);
//			$GLOBALS['GO_EVENTS']->fire_event('checker', $params);
//
//			break;

		

//		//used by /javascript/dialog/SelectGroups.js
//		case 'groups':
//			require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
//			$GO_GROUPS = new GO_GROUPS();
//
//			$user_id = $GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id) ? 0 : $GLOBALS['GO_SECURITY']->user_id;
//			$response['total']=$GO_GROUPS->get_groups($user_id, $start, $limit, $sort, $dir);
//
//			$response['results']=array();
//			while($GO_GROUPS->next_record()) {
//
//				$record = array(
//								'id' => $GO_GROUPS->f('id'),
//								'name' => $GO_GROUPS->f('name'),
//								'user_id' => $GO_GROUPS->f('user_id'),
//								'user_name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name'))
//				);
//				$response['results'][] = $record;
//
//			}
//
//			break;

		case 'groups_in_acl':

			$acl_id = ($_REQUEST['acl_id']);

			$response['manage_permission']=$GLOBALS['GO_SECURITY']->has_permission_to_manage_acl($GLOBALS['GO_SECURITY']->user_id, $acl_id);

			if(isset($_REQUEST['delete_keys'])) {
				try {

					if(!$response['manage_permission']) {
						throw new AccessDeniedException();
					}

					$response['deleteSuccess']=true;
					$groups = json_decode(($_REQUEST['delete_keys']));

					foreach($groups as $group_id) {
						if($group_id == $GLOBALS['GO_CONFIG']->group_root) {
							throw new Exception($lang['common']['dontChangeAdminsPermissions']);
						}
						$GLOBALS['GO_SECURITY']->delete_group_from_acl($group_id, $acl_id);
					}
				}catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			if(isset($_REQUEST['add_groups'])) {
				try {

					if(!$response['manage_permission']) {
						throw new AccessDeniedException();
					}

					$response['addSuccess']=true;
					$groups = json_decode(($_REQUEST['add_groups']));

					foreach($groups as $group_id) {
						if(!$GLOBALS['GO_SECURITY']->group_in_acl($group_id, $acl_id)) {
							$GLOBALS['GO_SECURITY']->add_group_to_acl(addslashes($group_id), $acl_id);
						}
					}
				}catch(Exception $e) {
					$response['addSuccess']=false;
					$response['addFeedback']=$e->getMessage();
				}
			}

			$response['total'] = $GLOBALS['GO_SECURITY']->get_groups_in_acl($acl_id);
			$response['results']=array();
			while($r=$GLOBALS['GO_SECURITY']->next_record(DB_ASSOC)) {
				$response['results'][]=$r;
			}
			break;


		case 'users_in_acl':
			$acl_id = ($_REQUEST['acl_id']);

			$response['manage_permission']=$GLOBALS['GO_SECURITY']->has_permission_to_manage_acl($GLOBALS['GO_SECURITY']->user_id, $acl_id);

			if(isset($_REQUEST['delete_keys'])) {
				try {
					if(!$response['manage_permission']) {
						throw new AccessDeniedException();
					}

					$response['deleteSuccess']=true;
					$users = json_decode($_REQUEST['delete_keys']);

					foreach($users as $user_id) {
						if($GLOBALS['GO_SECURITY']->user_owns_acl($user_id, $acl_id)) {
							if($GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id)){
								$GLOBALS['GO_SECURITY']->chown_acl($GLOBALS['GO_SECURITY']->user_id, $GLOBALS['GO_SECURITY']->user_id);
							}else
							{
								throw new Exception($lang['common']['dontChangeOwnersPermissions']);
							}
						}
						$GLOBALS['GO_SECURITY']->delete_user_from_acl($user_id, $acl_id);

					}
				}catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			if(isset($_REQUEST['add_users'])) {
				try {

					if(!$response['manage_permission']) {
						throw new AccessDeniedException();
					}

					$response['addSuccess']=true;
					$users = json_decode(($_REQUEST['add_users']));

					foreach($users as $user_id) {
						if(!$GLOBALS['GO_SECURITY']->user_in_acl($user_id, $acl_id)) {
							$GLOBALS['GO_SECURITY']->add_user_to_acl($user_id, $acl_id);
						}
					}
				}catch(Exception $e) {
					$response['addSuccess']=false;
					$response['addFeedback']=$e->getMessage();
				}
			}

			$response['total'] = $GLOBALS['GO_SECURITY']->get_users_in_acl($acl_id);
			$response['results']=array();
			while($GLOBALS['GO_SECURITY']->next_record(DB_ASSOC)) {
				$result['id']=$GLOBALS['GO_SECURITY']->f('id');
				$result['name']=String::format_name($GLOBALS['GO_SECURITY']->record);
				$result['level']=$GLOBALS['GO_SECURITY']->f('level');
				$response['results'][]=$result;
			}


			break;

		case 'email':
			require_once ($GLOBALS['GO_CONFIG']->class_path."mail/RFC822.class.inc");
			$RFC822 = new RFC822();

			$addresses=array();

			$results=array();

			$query = !empty($_REQUEST['query']) ? '%'.trim($_REQUEST['query']).'%' : '%';

			if(isset($GLOBALS['GO_MODULES']->modules['addressbook']) && $GLOBALS['GO_MODULES']->modules['addressbook']['read_permission']) {
				$GLOBALS['GO_LANGUAGE']->require_language_file('addressbook');

				require_once ($GLOBALS['GO_MODULES']->modules['addressbook']['class_path']."addressbook.class.inc.php");
				$ab = new addressbook();
				$ab->search_email($GLOBALS['GO_SECURITY']->user_id, $query);

				while($ab->next_record()) {
					$name = String::format_name($ab->f('last_name'),$ab->f('first_name'),$ab->f('middle_name'),'first_name');
					if($ab->f('email')!='') {
						$rfc_email =$RFC822->write_address($name, $ab->f('email'));
						if( !in_array($rfc_email, $addresses)) {
							$addresses[]=$rfc_email;

							$results[]=array(
											'info'=>htmlspecialchars($rfc_email.' ('.sprintf($lang['addressbook']['contactFromAddressbook'], $ab->f('addressbook_name')).')', ENT_COMPAT, 'UTF-8'),
											'full_email'=>htmlspecialchars($rfc_email, ENT_COMPAT, 'UTF-8'),
											'name'=>$name,
											'email'=>$ab->f('email')
							);
							//echo '<contact><full_email>'.htmlspecialchars($rfc_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($ab->f('email')).'</email></contact>';
						}
					}
					if($ab->f('email2')!='') {
						$rfc_email =$RFC822->write_address($name, $ab->f('email2'));
						if( !in_array($rfc_email, $addresses)) {
							$addresses[]=$rfc_email;
							$results[]=array(
											'info'=>htmlspecialchars($rfc_email.' ('.sprintf($lang['addressbook']['contactFromAddressbook'], $ab->f('addressbook_name')).')', ENT_COMPAT, 'UTF-8'),
											'full_email'=>htmlspecialchars($rfc_email, ENT_COMPAT, 'UTF-8'),
											'name'=>$name,
											'email'=>$ab->f('email2')
							);
							//echo '<contact><full_email>'.htmlspecialchars($rfc_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($ab->f('email2')).'</email></contact>';
						}
					}
					if($ab->f('email3')!='') {
						$rfc_email =$RFC822->write_address($name, $ab->f('email3'));
						if( !in_array($rfc_email, $addresses)) {
							$addresses[]=htmlspecialchars($rfc_email, ENT_COMPAT, 'UTF-8');
							$results[]=array(
											'info'=>htmlspecialchars($rfc_email.' ('.sprintf($lang['addressbook']['contactFromAddressbook'], $ab->f('addressbook_name')).')', ENT_COMPAT, 'UTF-8'),
											'full_email'=>htmlspecialchars($rfc_email, ENT_COMPAT, 'UTF-8'),
											'name'=>$name,
											'email'=>$ab->f('email3')
							);
							//echo '<contact><full_email>'.htmlspecialchars($rfc_email).'</full_email><name>'.htmlspecialchars($name).'</name><email>'.htmlspecialchars($ab->f('email3')).'</email></contact>';
						}
					}
				}

				if(count($addresses)<10) {
					$ab->search_company_email($GLOBALS['GO_SECURITY']->user_id, $query);

					while($ab->next_record()) {
						$rfc_email =$RFC822->write_address($ab->f('name'), $ab->f('email'));
						if( !in_array($rfc_email, $addresses)) {
							$addresses[]=$rfc_email;
							$results[]=array(
											'info'=>htmlspecialchars($rfc_email.' ('.sprintf($lang['addressbook']['companyFromAddressbook'], $ab->f('addressbook_name')).')', ENT_COMPAT, 'UTF-8'),
											'full_email'=>htmlspecialchars($rfc_email, ENT_COMPAT, 'UTF-8'),
											'name'=>$ab->f('name'),
											'email'=>$ab->f('email')
							);
						}
					}
				}
			}

			if(count($addresses)<10) {

				require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$GO_USERS->search($query,array('name','email'),$GLOBALS['GO_SECURITY']->user_id, 0,10);

				while($GO_USERS->next_record(DB_ASSOC)) {
					$name = String::format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'),'first_name');
					$rfc_email = $RFC822->write_address($name, $GO_USERS->f('email'));
					if(!in_array($rfc_email,$addresses)) {
						$addresses[]=$rfc_email;
						$results[]=array(
										'info'=>htmlspecialchars($rfc_email.' ('.$lang['common']['user'].')', ENT_COMPAT, 'UTF-8'),
										'full_email'=>htmlspecialchars($rfc_email, ENT_COMPAT, 'UTF-8'),
										'name'=>$name,
										'email'=>$GO_USERS->f('email')
						);
					}
				}
			}

			echo json_encode(array('persons'=> $results));
			exit();

			break;

		case 'modules':


			$response['success']=true;
			$response['modules']=array();
			foreach($GLOBALS['GO_MODULES']->modules as $module) {
				if($module['read_permission']) {
					$response['modules'][]=$module;
				}
			}
			break;

		/*case 'link_types':

			foreach($GLOBALS['GO_MODULES']->modules as $module) {
				if($lang_file = $GLOBALS['GO_LANGUAGE']->get_language_file($module['id'])) {
					$GLOBALS['GO_LANGUAGE']->require_language_file($module['id']);
				}
			}

			$response['total'] = count($lang['link_type']);
			$response['results']=array();

			$types = $GLOBALS['GO_CONFIG']->get_setting('link_type_filter', $GLOBALS['GO_SECURITY']->user_id);
			$types = empty($types) ? array() : explode(',', $types);

			asort($lang['link_type']);
			foreach($lang['link_type'] as $id=>$name) {
				$type['id']=$id;
				$type['name']=$name;
				$type['checked']=in_array($id, $types);
				$response['results'][] = $type;
			}
			break;

			break;*/

//		case 'links':
//
//		//ini_set('max_execution_time', 120);
//
//			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
//			$GO_LINKS = new GO_LINKS();
//
//			require_once($GLOBALS['GO_CONFIG']->class_path.'/base/search.class.inc.php');
//			$search = new search();
//
//			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
//			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;
//
//			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
//			$dir= isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
//
//
//			if(isset($_REQUEST['delete_keys'])) {
//				try {
//					$delete_links = json_decode($_REQUEST['delete_keys'], true);
//
//					foreach($delete_links as $delete_link) {
//						$link = explode(':',$delete_link);
//
//						if($link[0]=='folder') {
//							$GO_LINKS->delete_folder($link[1]);
//						}else {
//
//							$record = $search->get_search_result($link[1], $link[0]);
//
//							if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $record['acl_id'])<GO_SECURITY::DELETE_PERMISSION) {
//								throw new AccessDeniedException();
//							}
//
//							if(!isset($GLOBALS['GO_MODULES']->modules[$record['module']])) {
//								throw new Exception('No module found for this link type');
//							}
//							$module=$GLOBALS['GO_MODULES']->modules[$record['module']];
//
//							$file = $module['class_path'].$module['id'].'.class.inc';
//							if(!file_exists($file)) {
//								$file = $module['class_path'].$module['id'].'.class.inc.php';
//							}
//							if(!file_exists($file)) {
//								throw new Exception('No main module class found for this link type');
//							}
//							require_once($file);
//							if(!class_exists($module['id'])) {
//								throw new Exception('No main module class found for this link type');
//							}
//							$class = new $module['id'];
//							if(!method_exists($class, '__on_delete_link')) {
//								throw new Exception('Delete method is not implented for this link type');
//
//							}
//							$class->__on_delete_link($link[1], $link[0]);
//						}
//					}
//
//					$response['deleteSuccess']=true;
//				}catch(Exception $e) {
//					$response['deleteSuccess']=false;
//					$response['deleteFeedback']=$e->getMessage();
//				}
//			}
//
//
//			$link_id = isset($_REQUEST['link_id']) ?  ($_REQUEST['link_id']) : 0;
//			$link_type = isset($_REQUEST['link_type']) ? ($_REQUEST['link_type']) : 0;
//			$folder_id = isset($_REQUEST['folder_id']) ? ($_REQUEST['folder_id']) : 0;
//			$query = isset($_POST['query']) ? ($_REQUEST['query']) : '';
//
//
//			$types=array();
//			if(!empty($_POST['type_filter'])) {
//				if(isset($_POST['types'])) {
//					$types= json_decode($_POST['types'], true);
//					if(!isset($_POST['no_filter_save']))
//						$GLOBALS['GO_CONFIG']->save_setting('link_type_filter', implode(',',$types), $GLOBALS['GO_SECURITY']->user_id);
//				}else {
//					$types = $GLOBALS['GO_CONFIG']->get_setting('link_type_filter', $GLOBALS['GO_SECURITY']->user_id);
//					$types = empty($types) ? array() : explode(',', $types);
//				}
//			}
//
//
//
//			if(isset($_REQUEST['unlinks'])) {
//				$unlinks = json_decode(($_REQUEST['unlinks']), true);
//				foreach($unlinks as $unlink) {
//					$link = explode(':', $unlink);
//
//					$unlink_type = $link[0];
//					$unlink_id = $link[1];
//
//					//echo $link_id.':'.$link_type.' '.$unlink_id.':'.$unlink_type;
//					if($unlink_type!='folder')
//						$GO_LINKS->delete_link($link_id, $link_type, $unlink_id, $unlink_type);
//				}
//			}
//
//			$links_response = $search->get_links_json($GLOBALS['GO_SECURITY']->user_id, $query, $start, $limit, $sort,$dir, $types, $link_id, $link_type,$folder_id);
//
//			/*
//			 * Do this after search otherwise the new search result might not be present
//			*/
//			if($link_id>0) {
//				$record = $search->get_search_result($link_id, $link_type);
//
//				//go_debug($record);
//
//				$response['permission_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $record['acl_id']);
//				$response['write_permission']=$response['permission_level']>1;
//				if(!$response['permission_level']) {
//					throw new AccessDeniedException();
//				}
//
//			}
//
//			$response = isset($response) ? array_merge($response, $links_response) : $links_response;
//
//
//			break;


		case 'link_folder':
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			$response['data']= $GO_LINKS->get_folder($_REQUEST['folder_id']);
			$response['success']=true;
			break;

		case 'link_folders':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			$folder_id=isset($_POST['folder_id']) ? ($_POST['folder_id']) : 0;
			$link_id=isset($_POST['link_id']) ? ($_POST['link_id']) : 0;
			$link_type=isset($_POST['link_type']) ? ($_POST['link_type']) : 0;

			$response['total']=$GO_LINKS->get_link_folders($link_id, $link_type, $parent_id);
			$response['results']=array();
			while($GO_LINKS->next_record()) {
				$response['results'][] = $GO_LINKS->record;
			}
			break;

		case 'link_folders_tree':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			$folder_id=isset($_POST['node']) && substr($_POST['node'],0,10)=='lt-folder-' ? (substr($_POST['node'],10)) : 0;
			$link_id=isset($_POST['link_id']) ? ($_POST['link_id']) : 0;
			$link_type=isset($_POST['link_type']) ? ($_POST['link_type']) : 0;

			$GO_LINKS->get_folders($link_id, $link_type, $folder_id);
			$response=array();
			$links = new GO_LINKS();
			while($GO_LINKS->next_record()) {
				$node= array(
								'text'=>$GO_LINKS->f('name'),
								'id'=>'lt-folder-'.$GO_LINKS->f('id'),
								'iconCls'=>'folder-default'
				);

				$childCount = $links->get_folders($link_id,$link_type,$GO_LINKS->f('id'));

				if(!$childCount) {
					$node['expanded']=true;
					$node['children']=array();
				}

				$response[] = $node;
			}
			break;
//		case 'select_address_format':
//
//			require($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));
//			require_once ($GLOBALS['GO_MODULES']->modules['addressbook']['class_path']."addressbook.class.inc.php");
//			$ab = new addressbook();
//
//
//			$response['total'] = $GLOBALS['GO_LANGUAGE']->get_address_formats();
//
//			$formats = array();
//
//			while($record = $GLOBALS['GO_LANGUAGE']->next_record()) {
//				if(!empty($countries[$GLOBALS['GO_LANGUAGE']->f('iso')])) {
//					$formats[$countries[$GLOBALS['GO_LANGUAGE']->f('iso')]]=$record;
//				}
//			}
//
//			ksort($formats);
//
//			foreach($formats as $name => $record){
//
//				$response['results'][] = array( 'iso'=>$record['iso'],
//									'address_format_id'=>$record['address_format_id'],
//									'country_name'=>$name);
//			}
//			break;
	}

}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);

