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

define('EMAIL_TEMPLATE', 0);
define('OO_TEMPLATE', 1);

class templates extends db {
	var $custom_field_types=array(2,3,8);

	function get_template_by_name($user_id, $name) {
		$sql = "SELECT * FROM ml_templates WHERE ml_templates.name='".$this->escape($name)."' AND  user_id='$user_id'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function download_OO_template($id) {
		$sql = "SELECT * FROM ml_templates WHERE id='$id'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function get_writable_templates($user_id, $offset, $start, $name, $dir, $type=-1) {
		$sql = "SELECT DISTINCT ml_templates.* ".
						"FROM ml_templates ".
						"	INNER JOIN go_acl ON (ml_templates.acl_id = go_acl.acl_id AND go_acl.level>1) ".
						"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
						"WHERE (go_acl.user_id=".intval($user_id)." ".
						"OR go_users_groups.user_id=".intval($user_id).") ";

		if ($type != -1) {
			$sql .= " AND type='$type'";
		}

		$sql .= " ORDER BY ml_templates.".$name." ".$dir;
		
		$this->query($sql);
		$count = $this->num_rows();

		if($offset > 0 ) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		
		return $count;
	}

	function get_templates_json(&$response){

		global $lang, $GO_SECURITY, $GO_LANGUAGE;

		$GO_LANGUAGE->require_language_file('mailings');

		if(isset($_POST['default_template_id']))
		{
			$this->save_default_template($GO_SECURITY->user_id, $_POST['default_template_id']);
		}

		$default_template = $this->get_default_template($GO_SECURITY->user_id);

		$count = $this->get_authorized_templates($GO_SECURITY->user_id, 0,0, 'name','ASC', 0);
		$response['total'] = $count+1;
		$response['results'] = array(array(
			'group' => 'templates',
			'checked'=>$default_template && $default_template['template_id']==0,
			'text' => $lang['common']['none'],
			'template_id'=>0
		));

		while($this->next_record())
		{
			$record = array(
				'group' => 'templates',
				'checked'=>!$default_template || $default_template['template_id']==$this->f('id'),
				'text' => $this->f('name'),
				'template_id'=>$this->f('id')
			);

			if(!$default_template)
				$default_template['template_id']=$this->f('id');

			$response['results'][] = $record;
		}
		if($response['total']>1){

			$response['results'][] = '-';

			$record = array(
				'text' => $lang['mailings']['setCurrentTemplateAsDefault'],
				'template_id'=>'default'
			);

			$response['results'][] = $record;
		}
	}

	function get_authorized_templates($user_id, $offset, $start, $name, $dir, $type=-1, $query='') {
		$sql = "SELECT DISTINCT ml_templates.id, ml_templates.type, ml_templates.extension, ml_templates.name, ml_templates.user_id ".
						"FROM ml_templates ".						
						"INNER JOIN go_acl ON ml_templates.acl_id = go_acl.acl_id ".
						"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
						"WHERE (go_acl.user_id=".intval($user_id)." ".
						"OR go_users_groups.user_id=".intval($user_id).") ";

		if ($type != -1) {
			$sql .= " AND type='$type'";
		}

		if(!empty($query))
			$sql .= "AND ml_templates.name LIKE '".$this->escape($query)."'";

		$sql .= " ORDER BY ml_templates.".$name." ".$dir;

		if($offset > 0 ) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
			return $this->num_rows();
		}else {
			$this->query($sql);
			return $this->num_rows();
		}
	}

	function add_template($template, $types='') {
		$template['id']=$this->nextid('ml_templates');
		$this->insert_row("ml_templates", $template,$types,false);
		return $template['id'];
	}

	function update_template($template,$types='') {
		$this->update_row("ml_templates", 'id', $template, $types,false);
	}


	function get_template($template_id) {
		$sql = "SELECT * FROM ml_templates WHERE id='$template_id'";

		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function delete_template($template_id) {
		if($template = $this->get_template($template_id)) {
			global $GO_SECURITY;

			$GO_SECURITY->delete_acl($template['acl_id']);

			$sql = "DELETE FROM ml_templates WHERE id='$template_id'";
			return $this->query($sql);
		}
		return false;
	}



	private function build_unsubscribe_href($mailing_group_id, $recipient_type, $recipient_id, $ctime) {
		global $GO_MODULES;

		return $GO_MODULES->modules['mailings']['full_url'].'extern/unsubscribe.php?mailing_group_id='.$mailing_group_id
						.'&recipient_type='.$recipient_type.'&recipient_id='.$recipient_id.'&hash='.$this->get_unsubscribe_hash($ctime,$mailing_group_id,$recipient_type,$recipient_id);
	}

	public function get_unsubscribe_hash($ctime,$mailing_group_id,$recipient_type,$recipient_id) {
		return md5($ctime.$mailing_group_id.$recipient_type.$recipient_id);
	}

	function replace_contact_data_fields($input, $contact_id=0, $htmlspecialchars=false, $mailing_group_id=0, $skip_empty=false) {
		$ab = new addressbook();

		if(is_array($contact_id))
			$contact=$contact_id;

		if (isset($contact) || ($contact_id > 0 && $contact = $ab->get_contact($contact_id))) {
			$contact['hash']=md5($contact['id'].$contact['ctime']);
			$contact['company']=$contact['company_name'];
			$contact['company2']=$contact['company_name2'];

			global $GO_MODULES;
			if(isset($GO_MODULES->modules['customfields'])) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				$cf_values = $cf->get_values(1, 2, $contact['id']);
				$contact = array_merge($contact, $cf_values);
			}else {
				$cf=false;
			}

			/**
			 * this value must be passed so that an unsubscribe link can be generated
			 */
			if(!empty($mailing_group_id)) {

				global $GO_LANGUAGE, $lang;
				if(!isset($lang['mailings']))
					require($GO_LANGUAGE->get_language_file('mailings'));

				$contact['unsubscribe_href']=$this->build_unsubscribe_href($mailing_group_id, 'contact', $contact_id, $contact['ctime']);
				$contact['unsubscribe_link'] = '<a href="'.$contact['unsubscribe_href'].'">'.$lang['mailings']['unsubscription']."</a>";
			}
		}else {
			$contact=array();
		}

		$ab->format_contact_record($contact, $cf);

		if($htmlspecialchars)
			$contact = array_map('htmlspecialchars', $contact);

		$contact['comment']=String::text_to_html($contact['comment']);

		$this->replace_fields($input, $contact,$skip_empty,'contact');
		return $input;
	}

	function replace_company_data_fields($input, $company_id=0, $htmlspecialchars=false, $mailing_group_id=0, $skip_empty=false) {

		//$this->replace_unsubscription_field($input, $mailing['mailing_group_id'], 'company', $company_id, $htmlspecialchars);

		$ab = new addressbook();

		if(is_array($company_id))
			$company=$company_id;

		if (isset($company)  || ($company_id > 0 && $company = $ab->get_company($company_id))) {
			$company['hash']=md5($company['id'].$company['ctime']);
			$company['company']=$company['name'];
			$company['work_phone']=$company['phone'];
			$company['work_fax']=$company['fax'];
			$company['work_address']=$company['address'];
			$company['work_address_no']=$company['address_no'];
			$company['work_zip']=$company['zip'];
			$company['work_city']=$company['city'];
			$company['work_country']=$company['country'];
			$company['work_state']=$company['state'];

			$company['work_post_address']=$company['post_address'];
			$company['work_post_address_no']=$company['post_address_no'];
			$company['work_post_zip']=$company['post_zip'];
			$company['work_post_city']=$company['post_city'];
			$company['work_post_state']=$company['post_state'];
			$company['work_post_country']=$company['post_country'];

			global $GO_MODULES;
			if(isset($GO_MODULES->modules['customfields'])) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				$cf_values = $cf->get_values(1, 3, $company['id']);
				$company = array_merge($company, $cf_values);
			}else {
				$cf=false;
			}

			if(!empty($mailing_group_id)) {

				global $GO_LANGUAGE, $lang;
				if(!isset($lang['mailings']))
					require($GO_LANGUAGE->get_language_file('mailings'));

				$company['unsubscribe_href']=$this->build_unsubscribe_href($mailing_group_id, 'company', $company_id, $company['ctime']);
				$company['unsubscribe_link'] = '<a href="'.$company['unsubscribe_href'].'">'.$lang['mailings']['unsubscription']."</a>";
			}

			$ab->format_company_record($company, $cf);

			if($htmlspecialchars)
				$company = array_map('htmlspecialchars', $company);

		}else {
			$company=array();
		}

		$this->replace_fields($input, $company, $skip_empty, 'company');
		return $input;
	}

	function replace_user_data_fields($input, $user_id=0, $mailing_group_id=0) {
		global $GO_CONFIG, $GO_MODULES, $GO_SECURITY, $sir_madam;

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		if ($user_id > 0 && $user = $GO_USERS->get_user($user_id))
		{
			if(!empty($mailing_group_id)){

				global $GO_LANGUAGE, $lang;
				if(!isset($lang['mailings']))
					require($GO_LANGUAGE->get_language_file('mailings'));

				$user['unsubscribe_href']=$this->build_unsubscribe_href($mailing_group_id, 'user', $user_id, $user['registration_time']);
				$user['unsubscribe_link'] = '<a href="'.$user['unsubscribe_href'].'">'.$lang['mailings']['unsubscription']."</a>";
			}
			global $GO_MODULES;
			if(isset($GO_MODULES->modules['customfields'])) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				$cf_values = $cf->get_values(1, 8, $user['id']);
				$user = array_merge($user, $cf_values);
			}

		}else {
			$user = array();
		}

		$this->replace_fields($input, $user);
		return $input;
	}

	function get_replacements(&$values, $skip_empty=false) {
		global $GO_SECURITY, $GO_CONFIG, $lang, $GO_LANGUAGE, $GO_MODULES;

		require($GO_LANGUAGE->get_base_language_file('countries'));

		$values['date']=Date::get_timestamp(time(),false);
		if(isset($_SESSION['GO_SESSION']['name']))
			$values['my_name']=$_SESSION['GO_SESSION']['name'];

		if(!$skip_empty && empty($values['salutation'])) {
			$values['salutation']=$lang['common']['default_salutation']['unknown'];
		}

		if (empty($values['sex'])) {
			$values['beginning'] = $lang['common']['sirMadam']['M'].'/'.$lang['common']['sirMadam']['F'];
		}else {
			$values['beginning'] = isset($lang['common']['sirMadam'][$values['sex']]) ? $lang['common']['sirMadam'][$values['sex']] : $lang['common']['sirMadam']['M'];
		}

		$fields = array(
			'unsubscribe_link',
			'unsubscribe_href',
			'my_name',
			'date',
			'sex',
			'salutation',
			'birthday',
			'beginning',
			'name',
			'name2',
			'first_name',
			'middle_name',
			'last_name',
			'initials',
			'title',
			'email',
			'email2',
			'email3',
			'home_phone',
			'fax',
			'cellular',
			'address',
			'address_no',
			'zip',
			'city',
			'state',
			'country',
			'company',
			'company2',
			'department',
			'function',
			'work_phone',
			'work_fax',
			'work_address',
			'work_address_no',
			'work_zip',
			'work_city',
			'work_state',
			'work_country',
			'work_post_address',
			'work_post_address_no',
			'work_post_zip',
			'work_post_city',
			'work_post_state',
			'work_post_country',
			'homepage',
			'hash',//for security checking in newsletters
			'type',
			'id',
			'comment',
			'crn',
			'vat_no',
			'iban');
		
		

		$user_id = $GO_SECURITY->user_id>0 ? $GO_SECURITY->user_id : 1;

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();
		

		$user = $GO_USERS->get_user($user_id);

		foreach($user as $field=>$value) {
			$fields[]='my_'.$field;
			$values['my_'.$field]=$value;
		}

		$values['country']=!empty($values['country']) && isset($countries[$values['country']]) ? $countries[$values['country']] : '';
		$values['work_country']=!empty($values['work_country']) && isset($countries[$values['work_country']]) ? $countries[$values['work_country']] : '';
		$values['work_post_country']=!empty($values['work_post_country']) && isset($countries[$values['work_post_country']]) ? $countries[$values['work_post_country']] : '';
		$values['my_country']=!empty($values['my_country']) && isset($countries[$values['my_country']]) ? $countries[$values['my_country']] : '';
		$values['my_work_country']=!empty($values['my_work_country']) && isset($countries[$values['my_work_country']]) ? $countries[$values['my_work_country']] : '';

		if(isset($GO_MODULES->modules['customfields'])) {
			require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
			$cf = new customfields();

			$cf->get_all_fields(2);
			while($cf->next_record()) {
				$fields[]='col_'.$cf->f('id');
			}
			$cf->get_all_fields(3);
			while($cf->next_record()) {
				$fields[]='col_'.$cf->f('id');
			}
			$cf->get_all_fields(8);
			while($cf->next_record()) {
				$fields[]='col_'.$cf->f('id');
			}
			
			$cf_values=$cf->get_values($user_id, 8, $user_id);
			foreach($cf_values as $field=>$value) {
				$fields[]='my_'.$field;
				$values['my_'.$field]=$value;
			}
		}

		return $fields;
	}

	function replace_customfields($record, $tag, $content) {
		for($i=0; $i<count($record); $i++) {
			$record[$i]['value']=preg_replace('/^[0-9]+:/','',$record[$i]['value']);
			$content = str_replace('{'.$tag.':'.$record[$i]['category_name'].':'.$record[$i]['name'].'}', htmlspecialchars($record[$i]['value'], ENT_COMPAT, 'UTF-8'), $content);
		}
		return $content;
	}

	function replace_fields(&$content, $values, $skip_empty=false, $prefixtag='') {
		global  $GO_CONFIG;

		$fields = $this->get_replacements($values, $skip_empty);

		if(!empty($prefixtag)){
			foreach($fields as $value){
				$fields[]=$prefixtag.':'.$value;
				if(isset($values[$value]))
					$values[$prefixtag.':'.$value]=$values[$value];
					
			}
		}

		require_once($GO_CONFIG->class_path.'go_template_parser.class.inc.php');
		$tpl = new go_template_parser($fields,$values,$skip_empty);
		$tpl->parse($content);
	}

	function save_default_template($user_id, $template_id) {
		
		if ($this->get_default_template($user_id)) {
			$this->update_row("ml_default_templates", 'user_id', array('user_id'=>$user_id, 'template_id'=>$template_id));
		}else {
			$this->insert_row("ml_default_templates", array('user_id'=>$user_id, 'template_id'=>$template_id));
		}
		return true;
	}

	function get_default_template($user_id){
		$this->query("SELECT * FROM ml_default_templates WHERE user_id=?", 'i', $user_id);
		return $this->next_record();
	}

}
?>
