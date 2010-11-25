<?php
/*
 * Create a file called wp_config.inc.php and put it in the same directory where
 * your Group-Office config.php is. Then add something like this:
 *
 * var $mapping = array(
		'5' => array( //projects
				'post_title' => 'name',	//name is page title
				'categories_from_column'=>false, //categories may also come from a posted value
				'categories' => 'Vacatures', //categories
				'custom' => array( //wordpress custom fields
					'Korte beschrijving'=>'description',
					'Functie' => 'col_19',
					'Sectoren' => 'col_20',
					'Opleiding' => 'col_21',
					'Werkervaring' => 'col_22',
					'Locatie' => 'col_23',
					'Dienstverband' => 'col_24',
					)
			),
		'2' => array(
				'post_title' => 'first_name',
				* 'categories_from_column'=>false,
				'categories' => 'Keystaffers',
				'custom' => array()
			)
		);
 */
class wordpress extends db{

	var $mapping;

	public function get_mapping(){
		if(!isset($this->mapping)){
			global $GO_CONFIG;

			$config_dir = dirname($GO_CONFIG->get_config_file());
			$wp_config = $config_dir.'/wp_config.inc.php';

			$this->mapping=array();
			if(file_exists($wp_config)){
				require($wp_config);
				if(isset($mapping))
					$this->mapping = $mapping;
			}
		}
		return $this->mapping;
	}

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

	public function save($values, $link_type){

		$this->get_mapping();

		if(isset($this->mapping[$link_type])){

			global $GO_MODULES;

			if(isset($GO_MODULES->modules['customfields'])){
				$cf = new customfields();
				$custom = $cf->get_values(1, 5, $values['id']);


				$values = array_merge($values, $custom);
				//go_debug($values);
			}



			$w['id']=$values['id'];
			$w['link_type']=$link_type;
			$w['publish']=isset($_POST['wp_publish']) ? '1' : '0';
			$w['title']=$values[$this->mapping[$link_type]['post_title']];
			//$w['content']=$_POST['wp_content'];//$values[$this->mapping[$link_type]['post_content']];
			$w['updated']=1;
			$w['categories']=$this->mapping[$link_type]['categories_from_column'] ? $values[$this->mapping[$link_type]['categories']] : $this->mapping[$link_type]['categories'];


			if($this->get_post($w['id'], $w['link_type'])){
				$this->update_row('wp_posts',array('id','link_type'), $w);
				$this->query('DELETE FROM wp_posts_custom WHERE id=? AND link_type=?', 'ii', array($values['id'], $link_type));
			}else{
				$this->insert_row('wp_posts', $w);
			}

			

			foreach($this->mapping[$link_type]['custom'] as $wp_key=>$go_col){
				if(isset($values[$go_col])){
					$c['id']=$values['id'];
					$c['link_type']=$link_type;
					$c['key']=$wp_key;
					$c['value']=$values[$go_col];

					$this->insert_row('wp_posts_custom', $c);
				}
			}
		}else
		{
			//throw new Exception('No mapping found for link type '.$link_type);
		}

	}

	public static function load_contact(&$response, $task){
		if($task=='load_contact'){
			$wp = new wordpress();
			$wp->load($response, $_POST['contact_id'], 2);
		}
	}

	public static function save_contact($values){

		$wp = new wordpress();
		$wp->save($values, 2);
	}

	public static function load_project(&$response, $task){
		if($task=='project'){
			$wp = new wordpress();
			$wp->load($response, $_POST['project_id'], 5);
		}
	}

	public static function save_project($values){

		$wp = new wordpress();
		$wp->save($values, 5);
	}

	public function get_post($id, $link_type){
		$sql = "SELECT * FROM wp_posts WHERE id=? AND link_type=?";
		$this->query($sql, 'ii', array($id, $link_type));
		return $this->next_record();
	}

	public function get_post_by_wp_id($id){
		$sql = "SELECT * FROM wp_posts WHERE post_id=?";
		$this->query($sql, 'i', array($id));
		return $this->next_record();
	}

	public function set_contact_wp_user($r){
		
		return $this->replace_row('wp_contacts_wp_users', $r);
	}

	public function get_contact_by_wp_user_id($wp_user_id){
		$sql = "SELECT contact_id FROM wp_contacts_wp_users WHERE wp_user_id=".intval($wp_user_id);
		$this->query($sql);

		$record = $this->next_record();
		if(!$record)
			return false;

		return $record['contact_id'];
	}

}