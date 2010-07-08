<?php
class wordpress extends db{

	public function __on_load_listeners($events){
		$events->add_listener('load_contact', __FILE__, 'wordpress', 'load_contact');
		$events->add_listener('save_contact', __FILE__, 'wordpress', 'save_contact');

		$events->add_listener('load_project', __FILE__, 'wordpress', 'load_project');
		$events->add_listener('save_project', __FILE__, 'wordpress', 'save_project');
		
	}

	public function load(&$response, $id, $link_type){
		$this->query("SELECT publish AS wp_publish, title AS wp_title, content AS wp_content ".
						"FROM wp_posts WHERE id=? AND link_type=?", 'ii', array($id, $link_type));
		$record = $this->next_record();

		if($record)
			$response['data']=array_merge($response['data'], $record);
	}

	public function save($id, $link_type){
		$w['id']=$id;
		$w['link_type']=$link_type;
		$w['publish']=isset($_POST['wp_publish']) ? '1' : '0';
		$w['title']=$_POST['wp_title'];
		$w['content']=$_POST['wp_content'];
		$w['updated']=1;


		if($this->get_post($w['id'], $w['link_type']))
			$this->update_row('wp_posts',array('id','link_type'), $w);
		else
			$this->insert_row('wp_posts', $w);
	}

	public static function load_contact(&$response, $task){
		if($task=='load_contact'){
			$wp = new wordpress();
			$wp->load($response, $_POST['contact_id'], 2);
		}
	}

	public static function save_contact($credentials){

		$wp = new wordpress();
		$wp->save($credentials['id'], 2);
	}

	public static function load_project(&$response, $task){
		if($task=='project'){
			$wp = new wordpress();
			$wp->load($response, $_POST['project_id'], 5);
		}
	}

	public static function save_project($credentials){

		$wp = new wordpress();
		$wp->save($credentials['id'], 5);
	}

	public function get_post($id, $link_type){
		$sql = "SELECT * FROM wp_posts WHERE id=? AND link_type=?";
		$this->query($sql, 'ii', array($id, $link_type));
		return $this->next_record();
	}

	public function set_contact_wp_user($contact_id, $wp_user_id){
		$r['contact_id']=$contact_id;
		$r['wp_user_id']=$wp_user_id;

		return $this->replace_row('gw_contacts_wp_users', $r);
	}

	public function get_contact_id_by_wp_user_id($wp_user_id){
		$sql = "SELECT contact_id FROM gw_contacts_wp_users WHERE wp_user_id=".intval($wp_user_id);
		$this->query($sql);

		$record = $this->next_record();
		if(!$record)
			return false;

		return $record['contact_id'];
	}

}