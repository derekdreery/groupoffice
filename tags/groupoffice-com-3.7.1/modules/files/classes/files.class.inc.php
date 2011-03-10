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

require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');


class files extends db {
	var $enable_versioning=true;

	var $reabable_paths = array();
	var $writable_paths = array();

	function __construct() {
		global $GO_CONFIG;

		parent::__construct();

		$this->readable_paths = array(
				$GO_CONFIG->tmpdir,
				$GO_CONFIG->file_storage_path.'public/'
		);

		$this->writeable_paths = array(
				$GO_CONFIG->tmpdir
		);


		if(!empty($_SESSION['GO_SESSION']['username'])) {
			$this->readable_paths[] = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/';
			$this->writeable_paths[] = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/';
		}
	}

	public function __on_load_listeners($events) {
		$events->add_listener('check_database', __FILE__, 'files', 'check_database');
		$events->add_listener('user_delete', __FILE__, 'files', 'user_delete');
		//$events->add_listener('add_user', __FILE__, 'files', 'add_user');
		$events->add_listener('build_search_index', __FILE__, 'files', 'build_search_index');
		$events->add_listener('login', __FILE__, 'files', 'login');
		$events->add_listener('init_customfields_types', __FILE__, 'files', 'init_customfields_types');
	}

	function init_customfields_types(){
		global $GO_MODULES, $customfield_types;
		require_once($GO_MODULES->modules['files']['class_path'].'file_customfield_type.class.inc.php');
		$customfield_types['file']=new file_customfield_type(array());
	}
	
	/**
	 * Check if a path is the user's home path
	 *
	 * @param int $user_id Group-Office user ID
	 * @param string $path The path to check
	 *
	 * @access public
	 * @return bool
	 */
	function is_owner($folder) {
		$home = 'users/'.$_SESSION['GO_SESSION']['username'];
		$homefolder = $this->resolve_path($home);
		if(!$homefolder)
			return false;

		if($folder['id']==$homefolder['id']){
			return true;
		}else
		{
			return $this->is_sub_dir($folder, $homefolder);
		}
	}


	function is_sub_dir($sub, $parent) {
		if($sub['parent_id']==0) {
			return false;
		}
		if($sub['parent_id']==$parent['id']) {
			return true;
		}
		$next = $this->get_folder($sub['parent_id']);
		return $this->is_sub_dir($next, $parent);
	}

	function check_folder_location($folder_id, $path, $use_existing=false) {

		global $GO_CONFIG;

		$new_folder_id=$folder_id;

		$current_path = $this->build_path($folder_id);

		//strip the (n) part at the end of the path that is added when a duplicate
		//is found.
		$check_current_path=preg_replace('/ \([0-9]+\)$/', '', $current_path);

		//echo $current_path.' -> '.$path.'<br />';

		if(!$current_path) {
			$new_folder = $this->resolve_path($path,true,1,'1');

			

			return $new_folder['id'];
		}elseif($check_current_path != $path) {

			$fs = new filesystem();

			$destfolder_path = dirname($path);
			$destfolder = $this->resolve_path($destfolder_path,true);
			$base = $folder_name = utf8_basename($path);
			$count=1;
			while($existing_folder = $this->folder_exists($destfolder['id'], $folder_name))
			{
				if($use_existing)
				{
					return $existing_folder['id'];
				}
				$folder_name = $base.' ('.$count.')';
				$count++;
			}

			$full_source_path = $GO_CONFIG->file_storage_path.$current_path;
			$full_dest_path = $GO_CONFIG->file_storage_path.$destfolder_path.'/'.$folder_name;

			if(is_dir($full_source_path))
			{
				if($fs->is_sub_dir($full_dest_path, $full_source_path))
				{
					//moving into it's own sub path? Strange we must create a new folder
					$folder = $this->create_unique_folder($this->strip_server_path($full_dest_path));
					return $folder['id'];
				}else
				{
					$fs->move($full_source_path,$full_dest_path);
				}
			}else
			{
				$fs->mkdir_recursive($GO_CONFIG->file_storage_path.$destfolder_path.'/'.$folder_name);
			}

			$sourcefolder = $this->get_folder($folder_id);

			$up_folder['id']=$new_folder_id;
			$up_folder['parent_id']=$destfolder['id'];
			$up_folder['name']=$folder_name;
			$up_folder['readonly']='1';

			$this->update_folder($up_folder);
		}else
		{
			File::mkdir($GO_CONFIG->file_storage_path.$current_path);
		}
		return $new_folder_id;
	}

	function create_unique_folder($new_path){
		if(empty($new_path))
		{
			return false;
		}
		$parent = dirname($new_path);
		$base = $folder_name = utf8_basename($new_path);
		$parent_folder = $this->resolve_path($parent,true,1,'1');

		$count=1;
		while($existing_folder = $this->folder_exists($parent_folder['id'], $folder_name))
		{
			$folder_name = $base.' ('.$count.')';
			$count++;
		}
		return $this->mkdir($parent_folder, $folder_name, false,1,true,'1');
	}

	/*
	 * Read only is a little misleading. It means the sharing settings may not
	 * be adjusted.
	 */

	function set_readonly($folder_id) {
		$up_folder['readonly']='1';
		$up_folder['id']=$folder_id;
		$this->update_folder($up_folder);
	}

	function check_share($path, $user_id, $acl_id, $quiet=true) {
		global $GO_LANGUAGE, $lang, $GO_CONFIG;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		$full_path = $GO_CONFIG->file_storage_path.$path;

		require($GO_LANGUAGE->get_language_file('files'));
		$fs = new filesystem();
		$fs->mkdir_recursive($full_path);

		$folder = $this->resolve_path($path,true,1,'1');

		if($folder['acl_id']!=$acl_id || $folder['readonly']!='1') {
			$up_folder['id']=$folder['id'];
			$up_folder['user_id']=$user_id;
			$up_folder['acl_id']=$acl_id;
			$up_folder['readonly']='1';

			$this->update_folder($up_folder);
			if(!$quiet)
				echo 'Updating '.$path.$line_break;
		}

		return $folder;

	}

	function add_notification($folder_id, $user_id) {
		$notification['folder_id']=$folder_id;
		$notification['user_id']=$user_id;

		$this->insert_row('fs_notifications', $notification);
	}

	function remove_notification($folder_id, $user_id) {
		$sql = "DELETE FROM fs_notifications WHERE folder_id=? AND user_id=?";
		return $this->query($sql, 'ii', array($folder_id, $user_id));
	}

	function remove_notifications($folder_id) {
		$sql = "DELETE FROM fs_notifications WHERE folder_id='".$this->escape($folder_id)."'";
		return $this->query($sql);
	}

	function is_notified($folder_id, $user_id) {
		$sql = "SELECT * FROM fs_notifications WHERE folder_id=".intval($folder_id)." AND user_id=".intval($user_id);
		$this->query($sql);
		if($this->next_record()) {
			return true;
		}else {
			return false;
		}
	}


	function get_users_to_notify($folder_id) {
		$sql = "SELECT user_id FROM fs_notifications WHERE folder_id=".intval($folder_id);
		$this->query($sql);
		return $this->num_rows();
	}

	function notify_users($folder, $modified_by_user_id, $modified=array(), $new=array(), $deleted=array()) {
		global $GO_LANGUAGE, $GO_CONFIG, $GO_SECURITY;

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		require($GO_LANGUAGE->get_language_file('files'));

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$modified_by_user_name = $GO_USERS->get_user_realname($modified_by_user_id);


		$changes = '';
		if(count($new)) {
			$changes .= $lang['files']['new'].":\n".implode("\n", $new)."\n\n";
		}
		if(count($modified)) {
			$changes .= $lang['files']['modified'].":\n".implode("\n", $modified)."\n\n";
		}

		if(count($deleted)) {
			$changes .= $lang['files']['deleted'].":\n".implode("\n", $deleted)."\n\n";
		}

		$body = sprintf($lang['files']['folder_modified_body'],
				$this->build_path($folder),
				$modified_by_user_name,
				$changes);

		$users=array();
		$this->get_users_to_notify($folder['id']);
		while($this->next_record()) {
			if($this->f('user_id')!=$GO_SECURITY->user_id) {
				$user = $GO_USERS->get_user($this->f('user_id'));

				$swift = new GoSwift($user['email'], $lang['files']['folder_modified_subject'],0,0,'3',$body);
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->sendmail();
			}
		}
	}



	/**
	 * Add a template
	 *
	 * @param Array $template Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_template($template, $types='') {
		if(!empty($types) && !isset($template['id'])) {
			$types.='i';
		}

		$template['id']=$this->nextid('fs_templates');

		if($this->insert_row('fs_templates', $template, '', false)) {
			return $template['id'];
		}
		return false;
	}

	/**
	 * Update a template
	 *
	 * @param Array $template Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_template($template, $types='') {
		return $this->update_row('fs_templates', 'id', $template,'',false);
	}


	/**
	 * Delete a template
	 *
	 * @param Int $template_id ID of the template
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_template($template_id) {
		return $this->query("DELETE FROM fs_templates WHERE id=".intval($template_id));
	}


	/**
	 * Gets a template record
	 *
	 * @param Int $template_id ID of the template
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_template($template_id, $with_content=false) {
		if($with_content) {
			$fields = '*';
		}else {
			$fields = 'id, name, user_id, extension, acl_id';
		}
		$this->query("SELECT $fields FROM fs_templates WHERE id=".intval($template_id));
		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets a template record by the name field
	 *
	 * @param String $name Name of the template
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_template_by_name($name) {
		$this->query("SELECT * FROM fs_templates WHERE template_name='".$this->escape($name)."'");
		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}


	/**
	 * Gets authorized templates
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_authorized_templates($user_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC') {
		$user_id = intval($user_id);
		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
				"INNER JOIN go_acl a ON a.acl_id=t.acl_id ".
				"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
				"WHERE (a.user_id=".intval($user_id)." OR ug.user_id=".intval($user_id).") ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}


	/**
	 * Gets writable templates
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_writable_templates($user_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC') {
		$user_id = intval($user_id);

		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
				"INNER JOIN go_acl a ON a.acl_id=t.acl_id AND a.level>".GO_SECURITY::READ_PERMISSION.' '.
				"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
				"WHERE (a.user_id=".intval($user_id)." OR ug.user_id=".intval($user_id).") ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}



	function get_status_history($link_id) {
		$sql = "SELECT fs_status_history.*, fs_statuses.name AS status_name FROM ".
				"fs_status_history  ".
				"INNER JOIN fs_statuses ON fs_statuses.id=fs_status_history.status_id".
				" WHERE link_id='".$this->escape($link_id)."' ORDER BY ctime ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_status_name($status_id) {
		$sql = "SELECT name FROM fs_statuses WHERE id=".intval($status_id);
		$this->query($sql);
		if($this->next_record()) {
			return $this->f('name');
		}
		return false;
	}

	function get_statuses() {
		$sql = "SELECT * FROM fs_statuses";
		$this->query($sql);
		return $this->num_rows();
	}

	function change_status($link_id, $status_id, $comments) {
		global $GO_SECURITY;
		$link['link_id']=$link_id;
		$link['status_id']=$status_id;

		$this->update_row('fs_links','link_id',$link);

		$status['id']=$this->nextid('fs_status_history');
		$status['link_id']=$link_id;
		$status['status_id']=$status_id;
		$status['ctime']=time();
		$status['user_id']=$GO_SECURITY->user_id;
		$status['comments']=$comments;

		$this->insert_row('fs_status_history',$status);
	}

	function get_users_in_share($folder_id) {
		global $GO_SECURITY;

		$users=array();
		$share= $this->find_share($folder_id);

		if($share) {
			$users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_id']);
		}

		return $users;
	}




	function get_latest_files() {
		$sql = "SELECT * FROM fs_files ORDER BY mtime DESC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_file($file_id) {
		$sql = "SELECT * FROM fs_files WHERE id='".$this->escape($file_id)."';";
		$this->query($sql);
		return $this->next_record();
	}

	function get_versions_dir($file_id) {
		global $GO_CONFIG;

		$path = $GO_CONFIG->file_storage_path.'versioning/'.$file_id;
		File::mkdir($path);

		return $path;

	}

	function move_version($file, $path) {

		if(!isset($GO_CONFIG->max_file_versions)){
			$GO_CONFIG->max_file_versions=3;
		}

		if(empty($GO_CONFIG->max_file_versions) || $GO_CONFIG->max_file_versions!=1){
			//no db functions apply to this move
			$fs = new filesystem();

			$versions_dir = $this->get_versions_dir($file['id']);
			$filename = utf8_basename($path);

			$version_filepath = $versions_dir.'/'.date('YmdGis',filectime($path)).'_'.$_SESSION['GO_SESSION']['username'].'_'.$filename;
			if(!file_exists($version_filepath))
				$fs->move($path, $version_filepath);

			global $GO_CONFIG;

			if(!empty($GO_CONFIG->max_file_versions)){
				$max = $GO_CONFIG->max_file_versions-1;

				$files = $fs->get_files_sorted($versions_dir, 'filemtime', 'ASC');
				$count=count($files);
				if($count>$max){
					$delete_count = $count - $max;
					for($i=0;$i<$delete_count;$i++){
						$file = array_shift($files);
						unlink($file['path']);
					}
				}
			}
		}
	}



	function move_file($sourcefile, $destfolder, $log=true) {
		$existing_file = $this->file_exists($destfolder['id'], $sourcefile['name']);
		if($existing_file) {
			//$this->delete_file($existing_file);
			$sql = "DELETE FROM fs_files WHERE id=?";
			$this->query($sql, 'i', $existing_file['id']);
		}


		$up_file['id']=$sourcefile['id'];
		$up_file['folder_id']=$destfolder['id'];
		$this->update_file($up_file);

		if($log){
			global $GO_CONFIG;
			require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
			$search = new search();

			$search->log(6,$up_file['id'], 'Moved file '.$sourcefile['name'].' to '.$destfolder['name']);
		}

	}

	function move_folder($sourcefolder, $destfolder, $log=true) {
		if($sourcefolder['parent_id']==$destfolder['id']) {

			$this->update_folder($sourcefolder);

			return $sourcefolder['id'];
		}
		$existing_folder = $this->folder_exists($destfolder['id'], $sourcefolder['name']);

		$sourcefolder['parent_id']=$destfolder['id'];
		$this->update_folder($sourcefolder);

		if($existing_folder) {
			$files = new files();

			$this->get_files($existing_folder['id']);
			while($file = $this->next_record()) {
				$files->move_file($file, $sourcefolder, false);
			}

			$this->get_folders($existing_folder['id']);
			while($folder = $this->next_record()) {
				$files->move_folder($folder, $sourcefolder, false);
			}

			$sql = "DELETE FROM fs_folders WHERE id=?";
			$this->query($sql, 'i', $existing_folder['id']);
		}


		if($log){
			global $GO_CONFIG;
			require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
			$search = new search();

			$search->log(6,$sourcefolder['id'], 'Moved folder '.$sourcefolder['name'].' to '.$destfolder['name']);
		}

		return $sourcefolder['id'];
	}



	function add_file($file) {
		global $GO_CONFIG, $GO_EVENTS;

		$file['id']=$this->nextid('fs_files');
		$file['user_id']=$GLOBALS['GO_SECURITY']->user_id;
		$file['extension']=File::get_extension($file['name']);
		$this->insert_row('fs_files', $file);

		$this->cache_file($file);

		$this->add_new_filelink($file);

		$GO_EVENTS->fire_event('add_file', $params=array($file));

		return $file['id'];
	}

	function sync_file($file, $full_path) {
		$file['ctime']=filectime($full_path);
		$file['mtime']=filemtime($full_path);
		$file['size']=filesize($full_path);
		$file['extension']=File::get_extension($full_path);

		return $this->update_file($file);
	}

	function import_file($full_path, &$parent_id=false, $comments='') {
		global $GO_CONFIG;
		
		@chmod($full_path, $GO_CONFIG->file_create_mode);
		if(!empty($GO_CONFIG->file_change_group))
			@chgrp($full_path, $GO_CONFIG->file_change_group);

		if(!$parent_id) {
			$parent = $this->resolve_path(dirname($this->strip_server_path($full_path)),true);
			$parent_id=$parent['id'];
		}

		$file['name']=utf8_basename($full_path);

		$existing_file = $this->file_exists($parent_id, $file['name']);

		$file['name']=utf8_basename($full_path);
		$file['ctime']=filectime($full_path);
		$file['mtime']=filemtime($full_path);
		$file['folder_id']=$parent_id;
		$file['size']=filesize($full_path);
		$file['extension']=File::get_extension($full_path);

		if(!empty($comments))
			$file['comments']=empty($existing_file['comments']) ? $comments : $existing_file['comments']."\n\n---\n\n".$comments;

		if($existing_file) {
			$file['id']=$existing_file['id'];
			$this->update_file($file);
			return $file['id'];
		}else {
			return $this->add_file($file);
		}
	}

	function import_folder($full_path, $parent_id, $recurse_only_one_level=false, $levels_recursed=0) {
		global $GO_SECURITY, $GO_CONFIG;

		$fs = new filesystem();

		$folder['name']=utf8_basename($full_path);
		$folder['visible']='0';
		$folder['user_id']=$GO_SECURITY->user_id;
		$folder['parent_id']=$parent_id;
		$folder['ctime']=filectime($full_path);
		$folder['mtime']=$recurse_only_one_level ? 1 : filemtime($full_path);

		$existing_folder = $this->folder_exists($parent_id, $folder['name']);
		if($existing_folder) {
			$folder['id']=$existing_folder['id'];
			$folder['parent_id']=$existing_folder['parent_id'];
			$this->update_folder($folder);
		}else {
			$folder['id']=$this->add_folder($folder);
		}

		if(!$folder['id']) {
			throw new Exception('Could not create folder: '.$full_path);
		}

		if($levels_recursed<1){
			$files = $fs->get_files($full_path);
			while($fs_file = array_shift($files)) {
				$this->import_file($fs_file['path'], $folder['id']);
			}

			$folders = $fs->get_folders($full_path);
			while($fs_folder=array_shift($folders)) {
				$this->import_folder($fs_folder['path'], $folder['id'], $recurse_only_one_level, $levels_recursed+1);
			}
		}
		return $folder['id'];
	}

	/**
	 * Compares the folder mtime in the database with the filesystem.
	 * If it's different it will sync the filesystem with the database
	 * 
	 * @global config class $GO_CONFIG
	 * @param array $folder
	 * @param string $path
	 * @return true if folder was updated
	 */

	function check_folder_sync($folder, &$path=false){
		global $GO_CONFIG;

		$path = $path ? $path : $this->build_path($folder);

		$filemtime=filemtime($GO_CONFIG->file_storage_path.$path);
		if($folder['mtime']<$filemtime)
		{
			go_debug('Timestamp on disk didn\'t match the database so starting sync for: '.$path);

			ini_set('memory_limit','1000M');
			
			$this->sync_folder($folder);

			$this->touch_folder($folder['id'], $filemtime);

			return true;
		}else
		{
			return false;
		}
	}


	function sync_folder($folder, $recursive=false){
		global $GO_CONFIG;
		$fs = new filesystem();



		if(!$folder)
		{
			throw new FileNotFoundException();
		}

		$full_path = $GO_CONFIG->file_storage_path.$this->build_path($folder);
		if(!is_dir($full_path))
		{
			echo 'Not found: '.$full_path;
			throw new FileNotFoundException();
		}

		go_debug("files::sync_folder ".$full_path);

		$dbfolders=array();
		$dbfolders_names=array();
		$this->get_folders($folder['id']);
		while($dbfolder = $this->next_record())
		{
			$dbfolders_names[]=$dbfolder['name'];
			$dbfolders[]=$dbfolder;
		}
		
		if($dir = @opendir($full_path))
		{
			while($filename=readdir($dir))
			{
				$file_path = $full_path.'/'.$filename;
				if (is_dir($file_path) && strpos($filename,".") !== 0){
					$key = array_search($filename, $dbfolders_names);
					if($key===false)
					{
						$this->import_folder($file_path, $folder['id'], !$recursive);
					}elseif($recursive){
						$this->sync_folder($dbfolders[$key], true);
					}
				}
			}
		}

		
		foreach($dbfolders as $dbfolder)
		{
			if(!is_dir($full_path.'/'.$dbfolder['name']))
			{
				$this->delete_folder($dbfolder);
			}
		}

		$dbfiles=array();
		$dbfiles_names=array();
		$this->get_files($folder['id']);
		while($dbfile = $this->next_record())
		{
			$dbfiles_names[]=$dbfile['name'];
			$dbfiles[]=$dbfile;
		}
		
		if($dir = @opendir($full_path))
		{
			while($filename=readdir($dir))
			{
				$file_path = $full_path.'/'.$filename;
				if (!is_dir($file_path) && strpos($filename,".") !== 0 && !in_array($filename, $dbfiles_names)){
					$this->import_file($file_path, $folder['id']);
				}
			}
		}
		
		foreach($dbfiles as $dbfile)
		{
			if(!file_exists($full_path.'/'.$dbfile['name']))
			{
				$this->delete_file($dbfile);
			}
		}
	}


	function update_file($file) {
		global $GO_EVENTS;
		
		//$file['mtime']=time();
		$this->update_row('fs_files', 'id', $file);

		$this->cache_file($file['id']);

		$GO_EVENTS->fire_event('update_file', $params=array($file));
	}

	function get_folder($id) {
		$sql = "SELECT * FROM fs_folders WHERE id=?;";
		$this->query($sql,'i',$id);
		return $this->next_record();
	}

	function add_folder($folder) {
		$folder['id']=$this->nextid('fs_folders');
		if(!isset($folder['user_id'])) {
			global $GO_SECURITY;
			$folder['user_id']=$GO_SECURITY->user_id;
		}

		$this->insert_row('fs_folders', $folder);

		$this->cache_folder($folder);

		return $folder['id'];			
	}

	function update_folder($folder) {
		
		$result = $this->update_row('fs_folders', 'id', $folder);
		
		if(!isset($folder['user_id']) || !isset($folder['name']))
		{
			$folder = $this->get_folder($folder['id']);

		}
		$this->cache_folder($folder);

		return $result;
	}


	/**
	 * Get the shares owned by a user.
	 *
	 * @param int $user_id Group-Office user ID
	 *
	 * @access public
	 * @return int Number of shares found,
	 */
	function get_authorized_shares($user_id, $visible_only=true) {
		$user_id=intval($user_id);
		//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT DISTINCT f.* FROM fs_folders f ".
				"INNER JOIN go_acl a ON f.acl_id=a.acl_id ".
				"LEFT JOIN go_users_groups ug ON (a.group_id=ug.group_id) ".
				"WHERE (ug.user_id=".intval($user_id)." OR a.user_id=".intval($user_id).") AND f.user_id!=$user_id ";
		if($visible_only) {
			$sql .= "AND f.visible='1' ";
		}
		$sql .= "ORDER BY name ASC";

		$this->query($sql);
		return $this->num_rows();

	}

	function get_cached_share($user_id, $name){
		$sql = "SELECT * FROM fs_shared_cache WHERE user_id=".intval($user_id)." AND name=?";

		$this->query($sql,'s', $name);
		return $this->next_record();
	}

	function get_cached_shares($user_id, $join=false, $start=0, $offset=0){

		global $GO_CONFIG;

		$last_build_time = $GO_CONFIG->get_setting('fs_shared_cache', $user_id);

		if($last_build_time<(time()-86400)){

			go_debug('Rebuilding fs_shared_cache');

			$fs2 = new files();

			$share_count = $this->get_authorized_shares($user_id);

			$folders=array();

			while ($folder = $this->next_record()) {

				$folder['path'] = $fs2->build_path($folder);
				$folders[$folder['path']]=$folder;
			}
			ksort($folders);


			$fs = new filesystem();

			$sql = "DELETE FROM fs_shared_cache WHERE user_id=".intval($user_id);
			$this->query($sql);

			foreach ($folders as $path=>$folder) {
				$is_sub_dir = isset($last_path) ? $fs->is_sub_dir($path, $last_path) : false;
				if (!$is_sub_dir) {

					$c['folder_id']=$folder['id'];
					$c['user_id']=$user_id;
					$c['name']=$folder['name'];
					$c['path']=$path;

					$this->insert_row('fs_shared_cache', $c);

					$last_path = $path;
				}
			}
			$GO_CONFIG->save_setting('fs_shared_cache', time(), $user_id);
		}


		$sql = "SELECT * FROM fs_shared_cache c ";
		
		if($join)
			$sql .= "INNER JOIN fs_folders f ON f.id=c.folder_id ";
		
		$sql .= "WHERE c.user_id=".intval($user_id)." ORDER BY c.name ASC";

		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			
			$sql = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $sql);
		}



		$this->query($sql);

		return $offset>0 ? $this->found_rows() : $this->num_rows();

	}

	/**
	 * Get the shares owned by a user.
	 *
	 * @param int $user_id Group-Office user ID
	 *
	 * @access public
	 * @return int Number of shares found,
	 */
	function get_user_shares($user_id) {
	//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT * FROM fs_shares WHERE user_id='".intval($user_id)."' ORDER BY path ASC";
		$this->query($sql);
		return $this->num_rows();
	}


	function has_write_permission($user_id, $folder) {
		global $GO_SECURITY;

		if(is_numeric($folder)) {
			$folder = $this->get_folder($folder);
		}
		if(!$folder) {
			return $GO_SECURITY->has_admin_permission($user_id);
		}

		if(empty($folder['acl_id'])) {
			if(empty($folder['parent_id'])) {
				return $GO_SECURITY->has_admin_permission($user_id);
			}
			$parent = $this->get_folder($folder['parent_id']);
			return $this->has_write_permission($user_id, $parent);
		}else {
			return $GO_SECURITY->has_permission($user_id, $folder['acl_id'])>GO_SECURITY::READ_PERMISSION;
		}
	}

	function has_delete_permission($user_id, $folder){

		global $GO_SECURITY;

		if($folder['parent_id']==0){
			return false;
		}
		$path = $this->build_path($folder);
		/*if(strpos(dirname($path),'/')===false){
			return false;
		}*/


		if(is_numeric($folder)) {
			$folder = $this->get_folder($folder);
		}

		//var_dump($folder);

		if(empty($folder['acl_id'])) {
			if(empty($folder['parent_id'])) {
				return $GO_SECURITY->has_admin_permission($user_id);
			}
			$parent = $this->get_folder($folder['parent_id']);
			return $this->has_delete_permission($user_id, $parent);
		}else {
			$level = $GO_SECURITY->has_permission($user_id, $folder['acl_id']);
			//var_dump($level);
			return $level>GO_SECURITY::WRITE_PERMISSION;
		}
	}

	function has_read_permission($user_id, $folder) {
		global $GO_SECURITY;

		if(is_numeric($folder)) {
			$folder = $this->get_folder($folder);
		}
		if(!$folder) {
			return $GO_SECURITY->has_admin_permission($user_id);
		}
		if(empty($folder['acl_id'])) {
			if(empty($folder['parent_id'])) {
				return $GO_SECURITY->has_admin_permission($user_id);
			}
			$parent = $this->get_folder($folder['parent_id']);
			return $this->has_read_permission($user_id, $parent);
		}else {

			return $GO_SECURITY->has_permission($user_id, $folder['acl_id']);
		}
	}


	function add_new_filelink($file) {
		$users = $this->get_users_in_share($file['folder_id']);

		for($i=0; $i<count($users); $i++) {
			if($users[$i] != $file['user_id']) {
				$this->insert_row('fs_new_files', array('file_id' => $file['id'], 'user_id' => $users[$i]));
			}
		}
	}

	function check_existing_filelink($file_id, $user_id) {
		$this->query("SELECT * FROM fs_new_files WHERE user_id = ? AND file_id = ?", 'ii', array($user_id, $file_id));
		return $this->num_rows();
	}

	function delete_all_new_filelinks($user_id) {
		$this->query("DELETE FROM fs_new_files WHERE user_id = ?", 'i', $user_id);
		return $this->num_rows();
	}
	function delete_new_filelink($file_id, $user_id=0) {
		if($user_id > 0) {
			$this->query("DELETE FROM fs_new_files WHERE file_id = ? AND user_id = ?", 'ii', array($file_id, $user_id));
		} else {
			$this->query("DELETE FROM fs_new_files WHERE file_id = ?", 'i', $file_id);
		}
	}

	function get_num_new_files($user_id) {
		$this->query("SELECT id FROM fs_new_files AS fn, fs_files AS ff WHERE fn.file_id = ff.id AND fn.user_id = ?", 'i', $user_id);
		return $this->num_rows();
	}

	function get_new_files($user_id, $sort='name', $dir='DESC') {
	//global $GO_CONFIG;

		$this->query("SELECT ff.* FROM fs_new_files AS fn, fs_files AS ff WHERE fn.file_id = ff.id AND fn.user_id = ? ORDER BY ".$this->escape($sort.' '.$dir), 'i', $user_id);

		/*$files = array();
		while($item = $this->next_record())
		{
			$file = array();
			$file['path'] = $GO_CONFIG->file_storage_path.$item['path'];
			$file['name'] = utf8_basename($file['path']);
			$file['mtime'] = filemtime($file['path']);
			$file['size'] = filesize($file['path']);
			$file['type'] = File::get_mime($file['path']);

			$files[] = $file;
		}

		if(count($files) > 1)
		{
			// only sort when there is something to sort
			return $this->sksort($files, $sort, $dir);
		} else
		{
			return $files;
		}*/
		return $this->num_rows();
	}

	public static function login($username, $password, $user, $count_login) {
	// Default timeout: 30 days

		if($count_login){
			global $GO_CONFIG;

			//this will rebuild the cached shares folder
			//disabled this because of slowdown with lots of users
			//$GO_CONFIG->save_setting('fs_shared_cache', 0, $user['id']);

			$timeout = 60*60*24*30;
			$deltime = time() - $timeout;

			$fs = new files();

			$fs->query("SELECT ff.id FROM fs_new_files AS fn, fs_files AS ff
				WHERE fn.file_id = ff.id AND ctime < ? AND fn.user_id = ?", 'ii', array($deltime, $user['id']));

			$files = array();
			if($fs->num_rows() > 0) {
				while($file = $fs->next_record()) {
					$files[] = $file['id'];
				}
				$fs->query("DELETE FROM fs_new_files WHERE file_id IN (".implode(',', $files).") ");
			}
		}
	}

	function sksort($array, $sort='name', $dir='DESC') {
		if (count($array)) {
			$temp_array[key($array)] = array_shift($array);
		}

		foreach($array as $key => $val) {
			$offset = 0;
			$found = false;
			foreach($temp_array as $tmp_key => $tmp_val) {
				if(!$found and strtolower($val[$sort]) > strtolower($tmp_val[$sort])) {
					$temp_array = array_merge((array)array_slice($temp_array,0,$offset), array($key => $val), array_slice($temp_array,$offset));
					$found = true;
				}
				$offset++;
			}
			if(!$found) {
				$temp_array = array_merge($temp_array, array($key => $val));
			}
		}

		$array = ($dir == 'DESC') ? $temp_array : array_reverse($temp_array);

		return $array;
	}
	// END NEW FUNCTIONS



	function strip_server_path($path) {
		global $GO_CONFIG;
		return substr($path, strlen($GO_CONFIG->file_storage_path));
	}

	function get_files($folder_id, $sortfield='name', $sortorder='ASC', $start=0, $offset=0, $extensions=array()) {
		global $GO_MODULES;

		$sql = "SELECT ";
		if($offset>0) {
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		$sql .= "f.*";

		if($GO_MODULES->has_module('customfields')) {
			$sql .= ",cf_6.*";
		}
		$sql .= " FROM fs_files f ";

		if($GO_MODULES->has_module('customfields')) {
			$sql .= "LEFT JOIN cf_6 ON cf_6.link_id=f.id ";
		}

		$types='';
		$params=array();

		$sql .= " WHERE folder_id=?";
		$types .= 'i';
		$params[]=$folder_id;

		if(count($extensions))
		{
			$sql .= " AND extension IN (".implode(',', $extensions).")";
		}

		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);
		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		$this->query($sql, $types, $params);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}

	function has_children($folder_id){
		$sql = "SELECT * FROM fs_folders WHERE parent_id=".intval($folder_id).' LIMIT 0,1';
		$this->query($sql);
		return $this->next_record();
	}
	/**
	 *
	 * @global <type> $GO_SECURITY
	 * @param <type> $folder_id
	 * @param <type> $sortfield
	 * @param <type> $sortorder
	 * @param <type> $start
	 * @param <type> $offset
	 * @param <type> $authenticate
	 * @param <type> $inherit_parent_permission When this is set to true it automatically authenticates when there's no acl id set for this folder.
	 * @return <type>
	 */

	function get_folders($folder_id, $sortfield='name', $sortorder='ASC', $start=0, $offset=0, $authenticate=false, $inherit_parent_permission=true) {
		global $GO_SECURITY;

		$sql = "SELECT ";

		if($offset>0) {
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}

		$sql .= "f.* FROM fs_folders f ";

		if($authenticate) {
			$sql .= "LEFT JOIN go_acl a ON a.acl_id=f.acl_id ".
					"WHERE (a.user_id=".$GO_SECURITY->user_id." OR a.group_id IN (".implode(',',$GLOBALS['GO_SECURITY']->get_user_group_ids($GO_SECURITY->user_id)).") ";

			if($inherit_parent_permission){
				$sql .= "OR ISNULL(a.acl_id) OR a.acl_id=0";
			}

			$sql .= ") AND ";
		}else {
			$sql .= " WHERE ";
		}


		$types='';
		$params=array();

		$sql .= "parent_id=? ";
		$types .= 'i';
		$params[]=$folder_id;

		if($authenticate) {
			$sql .= "GROUP BY f.id ";
		}

		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);
		if($offset>0) {
		 	$sql .= " LIMIT ".intval($start).",".intval($offset);
		}

		$this->query($sql, $types, $params);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}

	function move_by_paths($sourcepath, $destpath) {
		global $GO_CONFIG;

		$destination = dirname($destpath);
		if(file_exists($GO_CONFIG->file_storage_path.$sourcepath) && file_exists($GO_CONFIG->file_storage_path.$destination)) {
			$fs = new filesystem();
			$fs->move($GO_CONFIG->file_storage_path.$sourcepath, $GO_CONFIG->file_storage_path.$destpath);

			$source = $this->resolve_path($sourcepath);
			$dest = $this->resolve_path($destination);

			$new_filename = utf8_basename($destpath);

			if(is_dir($GO_CONFIG->file_storage_path.$destpath)) {
				$this->move_folder($source, $dest);
				if($new_filename!=$source['name']) {
					$up_folder['name']=$new_filename;
					$up_folder['id']=$source['id'];
					$this->update_folder($up_folder);
				}
			}else {
				$this->move_file($source, $dest);
				if($new_filename!=$source['name']) {
					$up_file['name']=$new_filename;
					$up_file['id']=$source['id'];
					$this->update_file($up_file);
				}
			}
		}
	}

	function resolve_path($path,$create_folders=false, $user_id=0, $readonly='0', $folder_id=0) {
		if(substr($path,-1)=='/') {
			$path=substr($path,0,-1);
		}
		$parts = explode('/', $path);		
		$first_part = array_shift($parts);
		

		if(count($parts)) {
			$folder = $this->folder_exists($folder_id, $first_part);

			if(!$folder && $create_folders) {
				$folder = $this->mkdir($folder_id, $first_part,false, $user_id, true,$readonly);
			}

			if($folder) {
				return $this->resolve_path(implode('/', $parts),$create_folders,$user_id,$readonly,$folder['id']);
			}else {
				return false;
			}
		}else {
			$file = $this->file_exists($folder_id, $first_part);
			if(!$file) {
				$folder = $this->folder_exists($folder_id, $first_part);
				if(!$folder && $create_folders) {
					$folder = $this->mkdir($folder_id, $first_part,false, $user_id, true,'0');
				}
				return $folder;
			}else {
				return $file;
			}
		}
	}

	function find_share($folder_id) {
		$folder = $this->get_folder($folder_id);

		if ($folder && $folder['acl_id']>0) {
			return $folder;
		}elseif($folder['parent_id']>0) {
			return $this->find_share($folder['parent_id']);
		}else {
			return false;
		}
	}




	function mkdir($parent, $name, $share_user_id=0, $user_id=0, $ignore_existing_filesystem_folder=false, $readonly='0', $visible='0') {

		global $GO_SECURITY, $GO_CONFIG, $lang;

		$name = trim($name);

		if($user_id==0) {
			$user_id=$GO_SECURITY->user_id;
		}

		/*if($share_user_id==0) {
			$share_user_id=$GO_SECURITY->user_id;
		}*/


		if($parent==0) {
			/*if(!$GO_SECURITY->has_admin_permission($user_id)) {
				throw new AccessDeniedException();
			}*/
		}else {
			if(is_numeric($parent)) {
				$parent = $this->get_folder($parent);
			}
			if(!$parent) {
				throw new FileNotFoundException();
			}
			/*if(!$this->has_write_permission($user_id, $parent)) {
				throw new AccessDeniedException();
			}*/
		}


		if (empty($name)) {
			throw new Exception($lang['common']['missingField']);
		}

		if(strlen($name)>240){
			throw new Exception('Filename too long: '.$name);
		}

		$rel_path=$this->build_path($parent);
		$full_path = $GO_CONFIG->file_storage_path.$rel_path;

		if (!$ignore_existing_filesystem_folder && file_exists($full_path.'/'.$name)) {
			throw new Exception($lang['files']['folderExists']);
		}

		if (!File::mkdir($full_path.'/'.$name)) {
			throw new Exception($lang['common']['saveError'].$full_path.'/'.$name);
		} else {
			$folder['readonly']=$readonly;
			$folder['visible']=$visible;
			//used to be share_user_id
			$folder['user_id']=$user_id;
			$folder['parent_id']=$parent['id'];
			$folder['name']=$name;
			$folder['ctime']=filectime($full_path.'/'.$name);
			$folder['mtime']=filemtime($full_path.'/'.$name);
			if($share_user_id) {
				$folder['acl_id']=$GO_SECURITY->get_new_acl('files', $share_user_id);
			}else {
				$folder['acl_id']=0;
			}
			$folder['id']=$this->add_folder($folder);


			$this->touch_folder($parent['id'], $folder['mtime']);

			return $folder;
		}
	}

	function touch_folder($folder_id, $time=false){
		if(!$time)
		{
			$time=time();
		}
		$up_folder['id']=$folder_id;
		$up_folder['mtime']=$time;
		return $this->update_folder($up_folder);
	}


	/**
	 *
	 * @param $folder_id folder id or record
	 * @param $path
	 * @return unknown_type
	 */

	function build_path($folder_id, &$pathinfo=array(), $path='') {
		if($folder_id==0) {
			return $path;
		}

		if(is_array($folder_id)) {
			$folder=$folder_id;
		}else {
			$folder=$this->get_folder($folder_id);
		}

		if(!$folder)
			return $path;
		
		array_unshift($pathinfo, $folder);

		$path = empty($path) ? $folder['name'] : $folder['name'].'/'.$path;
		return $this->build_path($folder['parent_id'], $pathinfo, $path);
	}

	function folder_exists($parent_id, $name) {
		$sql = "SELECT * FROM fs_folders WHERE parent_id='".$this->escape($parent_id)."' AND name COLLATE utf8_bin LIKE '".$this->escape($name)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function file_exists($parent_id, $name) {
		$sql = "SELECT * FROM fs_files WHERE folder_id='".$this->escape($parent_id)."' AND name COLLATE utf8_bin LIKE '".$this->escape($name)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function delete_folder($folder) {
		global $GO_SECURITY, $GO_CONFIG;

		if(is_numeric($folder)) {
			$folder = $this->get_folder($folder);
			if(!$folder) {
				throw new FileNotFoundException();
			}
		}
		$files = new files();
		$this->get_folders($folder['id']);
		while($subfolder = $this->next_record()) {
			$files->delete_folder($subfolder);
		}

		$this->get_files($folder['id']);
		while($file=$this->next_record()) {
			$files->delete_file($file);
		}

		$subpath = $this->build_path($folder);

		if(!$subpath) {
			throw new FileNotFoundException();
		}

		$this->remove_notifications($folder['id']);
		$sql = "DELETE FROM fs_folders WHERE id=?";
		$this->query($sql, 'i', $folder['id']);

		$path = $GLOBALS['GO_CONFIG']->file_storage_path.$subpath;
		$fs = new filesystem();

		if($GO_CONFIG->quota>0){
			require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
			$quota = new quota();
			$quota->add(-File::get_directory_size($path));
		}

		return $fs->delete($path);
	}

	function delete_file($file) {
		global $GO_CONFIG, $GO_MODULES, $GO_EVENTS;

		if(is_numeric($file)) {
			$file = $this->get_file($file);
			if(!$file) {
				throw new FileNotFoundException();
			}
		}

		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();

		$search->delete_search_result($file['id'], 6);

		$sql = "DELETE FROM fs_files WHERE id=?";
		$this->query($sql, 'i', $file['id']);

		$this->delete_new_filelink($file['id']);


		if($this->enable_versioning) {
			$versions_dir = $this->get_versions_dir($file['id']);
			if(is_dir($versions_dir)) {
			//no db functions apply to this move
				$fs = new filesystem();
				$fs->delete($versions_dir);

				if($GO_CONFIG->quota>0){
					require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
					$quota = new quota();
					$quota->add(-File::get_directory_size($versions_dir));
				}
			}
		}

		if(isset($GO_MODULES->modules['workflow']))
		{
			require_once ($GO_MODULES->modules['workflow']['class_path'].'workflow.class.inc.php');
			$workflow = new workflow();
			$workflow2 = new workflow();

			$workflow->get_linked_process_files($file['id'], 6);
			while($r = $workflow->next_record())
			{
				$workflow2->delete_process_file($r['id']);
			}
		}


		$path = $GO_CONFIG->file_storage_path.$this->build_path($file['folder_id']).'/'.$file['name'];

		require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
		$quota = new quota();
		$quota->add(-@filesize($path)/1024);


		$GO_EVENTS->fire_event('delete_file', array($file, $path));

		return @unlink($path);
	}

	function get_content_json($folder_id, $sort='name', $dir='ASC', $filter=null) {
		$results = array();
		if($folder_id>0) {

			$folders = $this->get_folders($folder_id, 'name', $dir,0,0,true);
			while($folder=$this->next_record()) {
				if($folder['acl_id']>0) {
					$class='folder-shared';
				}else {
					$class='filetype-folder';
				}

				$folder['type_id']='d:'.$folder['id'];
				$folder['type']='Folder';
				$folder['mtime']='-';
				$folder['size']='-';
				$folder['extension']='folder';
				$results[]=$folder;
			}


			if(isset($filter)) {
				$extensions = explode(',',$filter);
			}


			$files = $this->get_files($folder_id, $sort, $dir);
			while($file=$this->next_record()) {
				$extension = File::get_extension($file['name']);

				if(!isset($extensions) || in_array($extension, $extensions)) {
					$file['extension']=$extension;
					$file['type_id']='f:'.$file['id'];
					$file['type']=File::get_filetype_description($extension);
					$file['mtime']=Date::get_timestamp($file['mtime']);
					$file['size']=Number::format_size($file['size']);
					$results[]=$file;
				}
			}
		}
		return $results;
	}

	public function update_child_folders_recursively($folder_id, $update){

		$files = new files();
		$files->get_folders($folder_id);
		while($record = $files->next_record()){

			$this->update_child_folders_recursively($record['id'], $update);

			$update['id']=$record['id'];
			$this->update_row('fs_folders', 'id', $update);
		}
	}


	public static function check_database() {
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		$fs = new files();

		echo 'Correcting id=0'.$line_break;

		$sql = "SELECT path FROM fs_folders WHERE id=0";
		$fs->query($sql);
		$fs2 = new files();
		while($r = $fs->next_record())
		{
			$r['id']=$fs2->nextid('fs_folders');
			$fs2->update_row('fs_folders', 'path', $r);
		}

		if(isset($GO_MODULES->modules['customfields'])){
			$db = new db();
			echo "Deleting non existing custom field records".$line_break.$line_break;
			$db->query("delete from cf_6 where link_id not in (select id from fs_files);");
		}
	}

	function crawl($path) {
		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'Crawling folder '.$path.$line_break;
		$files = $this->get_files($path);
		while($file = array_shift($files)) {
			$this->get_file($this->strip_server_path($file['path']));
		}

		$this->get_folder($this->strip_server_path($path));

		$folders = $this->get_folders($path);
		while($folder = array_shift($folders)) {
			$this->crawl($folder['path']);
		}
	}



	function search($path, $keyword, $modified_later_then=0, $modified_earlier_then=0) {
		global $GO_SECURITY;

		if ($modified_earlier_then == 0) {
			$modified_earlier_then = time();
		}

		if($this->has_read_permission($GO_SECURITY->user_id, $path)) {
			$folders = $this->get_folders($path);
			while ($folder = array_shift($folders)) {
				$this->search($folder['path'], $keyword, $modified_later_then, $modified_earlier_then);
			}

			$folder['path'] = $path;
			$folder['name'] = utf8_basename($path);
			$folder['mtime'] = filemtime($path);
			$folder['size'] = filesize($path);
			$folder['type'] = mime_content_type($path);

			if (stristr(utf8_basename($path), $keyword) && $modified_later_then < $folder['mtime'] && $modified_earlier_then > $folder['mtime']) {
				$this->search_results[] = $folder;
			}

			$files = $this->get_files($path);
			while ($file = array_shift($files)) {
				if (stristr($file['name'], $keyword) && $modified_later_then < $file['mtime'] && $modified_earlier_then > $file['mtime']) {
					$this->search_results[] = $file;
				}
			}
		}
		return $this->search_results;
	}


	function user_delete($user) {
		global $GO_CONFIG;

		$files = new files();

		if(!empty($user['username'])) {
			$folder = $files->resolve_path('users/'.$user['username']);
			if($folder) {
				$files->delete_folder($folder);
			}
			$folder = $files->resolve_path('adminusers/'.$user['username']);
			if($folder) {
				$files->delete_folder($folder);
			}
		}
	}

	function cache_file($file, $is_folder=false) {
		global $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();

		require($GO_LANGUAGE->get_language_file('files'));

		$fs = new files();
		
		if(is_numeric($file) && !$is_folder) {
			$file = $this->get_file($file);
		}else
		if(is_numeric($file) && $is_folder){		
			$file = $this->get_folder($file);
		}

		if($file) {

			$share_id = ($is_folder) ? $file['id'] : $file['folder_id'];
			$share = $fs->find_share($share_id);

			if(!isset($file['comments']))
				$file['comments']='';

			if($file['comments'])
				$file['comments'].=',';

			if($share) {
				$cache['id']=$file['id'];
				$cache['user_id']=$file['user_id'];
				$cache['name'] = htmlspecialchars($file['name'], ENT_QUOTES, 'utf-8');
				$cache['link_type']=($is_folder) ? 17 : 6;
				$cache['description']='';
				$cache['type']=($is_folder) ? $lang['files']['folder'] : $lang['files']['file'];
				$cache['module']='files';
				$cache['keywords']=$file['comments'].$cache['name'].','.$cache['type'];
				$cache['mtime']=$file['mtime'];
				$cache['acl_id']=$share['acl_id'];

				$search->cache_search_result($cache);
			}
		}
	}

	function cache_folder($folder)
	{
		$this->cache_file($folder, true);
	}

	/**
	 * When a global search action is performed this function will be called for each module
	 */
	public static function build_search_index() {
		global $GO_CONFIG;

		$fs = new files();

		$sql = "SELECT * FROM fs_files";
		$fs->query($sql);
		$fs1 = new files();
		while($record = $fs->next_record()) {
			$fs1->cache_file($record);
		}

		$sql = "SELECT * FROM fs_folders";
		$fs->query($sql);
		while($record = $fs->next_record()) {			
			$fs1->cache_folder($record);
		}
	}
}
