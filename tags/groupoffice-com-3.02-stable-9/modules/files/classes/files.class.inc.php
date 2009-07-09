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
	}

	function get_thumb_url($path) {
		global $GO_THEME, $GO_CONFIG;

		$extension = File::get_extension($path);

		switch($extension) {
			case 'jpg':
			case 'jpeg';
			case 'png';
			case 'gif';
				return phpThumbURL('src='.$GO_CONFIG->file_storage_path.$path.'&w=100&h=100&zc=1');
				break;

			case 'pdf':
				return $GO_THEME->image_url.'128x128/filetypes/pdf.png';
				break;

			case 'tar':
			case 'tgz':
			case 'gz':
			case 'bz2':
			case 'zip':
				return $GO_THEME->image_url.'128x128/filetypes/zip.png';
				break;
			case 'odt':
			case 'docx':
			case 'doc':
				return $GO_THEME->image_url.'128x128/filetypes/doc.png';
				break;

			case 'odc':
			case 'xls':
			case 'xlsx':
				return $GO_THEME->image_url.'128x128/filetypes/spreadsheet.png';
				break;

			case 'odp':
			case 'pps':
			case 'pptx':
				return $GO_THEME->image_url.'128x128/filetypes/pps.png';
				break;

			case 'htm':
				return $GO_THEME->image_url.'128x128/filetypes/doc.png';
				break;

			default:
				if(file_exists($GO_THEME->theme_path.'images/128x128/filetypes/'.$extension.'.png')) {
					return $GO_THEME->image_url.'128x128/filetypes/'.$extension.'.png';
				}else {
					return $GO_THEME->image_url.'128x128/filetypes/unknown.png';
				}
				break;

		}

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
			if(!is_dir($GO_CONFIG->file_storage_path.$current_path))
			{
				mkdir($GO_CONFIG->file_storage_path.$current_path, $GO_CONFIG->folder_create_mode, true);
			}
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

	function set_readonly($folder_id) {
		$up_folder['readonly']='1';
		$up_folder['id']=$folder_id;
		$this->update_folder($up_folder);
	}

	function check_share($path, $user_id, $acl_read, $acl_write, $quiet=true) {
		global $GO_LANGUAGE, $lang, $GO_CONFIG;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		$full_path = $GO_CONFIG->file_storage_path.$path;

		require($GO_LANGUAGE->get_language_file('files'));
		$fs = new filesystem();
		$fs->mkdir_recursive($full_path);

		$folder = $this->resolve_path($path,true,1,'1');

		if($folder['acl_read']!=$acl_read || $folder['acl_write']!=$acl_write || $folder['readonly']!='1') {
			$up_folder['id']=$folder['id'];
			$up_folder['user_id']=$user_id;
			$up_folder['acl_read']=$acl_read;
			$up_folder['acl_write']=$acl_write;
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
		$sql = "SELECT * FROM fs_notifications WHERE folder_id=".$this->escape($folder_id)." AND user_id=".$this->escape($user_id);
		$this->query($sql);
		if($this->next_record()) {
			return true;
		}else {
			return false;
		}
	}


	function get_users_to_notify($folder_id) {
		$sql = "SELECT user_id FROM fs_notifications WHERE folder_id=".$this->escape($folder_id);
		$this->query($sql);
		return $this->num_rows();
	}

	function notify_users($folder, $modified_by_user_id, $modified=array(), $new=array(), $deleted=array()) {
		global $GO_USERS, $GO_LANGUAGE, $GO_CONFIG, $GO_SECURITY;

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		require($GO_LANGUAGE->get_language_file('files'));

		$user = $GO_USERS->get_user($modified_by_user_id);
		$modified_by_user_name = String::format_name($user);


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

		if($this->insert_row('fs_templates', $template)) {
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
		return $this->update_row('fs_templates', 'id', $template);
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
		return $this->query("DELETE FROM fs_templates WHERE id=".$this->escape($template_id));
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
			$fields = 'id, name, user_id, extension, acl_read, acl_write';
		}
		$this->query("SELECT $fields FROM fs_templates WHERE id=".$this->escape($template_id));
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
		$user_id = $this->escape($user_id);
		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
				"INNER JOIN go_acl a ON (a.acl_id=t.acl_read OR a.acl_id=t.acl_write) ".
				"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
				"WHERE (a.user_id=".$this->escape($user_id)." OR ug.user_id=".$this->escape($user_id).") ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
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
		$user_id = $this->escape($user_id);

		$sql = "SELECT DISTINCT t.id, t.user_id, t.name, t.extension FROM fs_templates t ".
				"INNER JOIN go_acl a ON a.acl_id=t.acl_write ".
				"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
				"WHERE (a.user_id=".$this->escape($user_id)." OR ug.user_id=".$this->escape($user_id).") ORDER BY ".$this->escape($sortfield." ".$sortorder);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
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
		$sql = "SELECT name FROM fs_statuses WHERE id=".$this->escape($status_id);
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
			$users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_read']);
			$write_users = $GO_SECURITY->get_authorized_users_in_acl($share['acl_write']);
			while($user_id = array_shift($write_users)) {
				if(!in_array($user_id, $users)) {
					$users[]=$user_id;
				}
			}
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
		if(!is_dir($path))
			mkdir($path, $GO_CONFIG->folder_create_mode,true);

		return $path;

	}

	function move_version($file) {

		if($this->enable_versioning) {
		//no db functions apply to this move
			$fs = new filesystem();

			$versions_dir = $this->get_versions_dir($file['id']);

			if($source_versions_dir!=$versions_dir && is_dir($source_versions_dir)) {
				$fs->move($source_versions_dir, $versions_dir);
			}

			if(file_exists($destination_path)) {
				if(!is_dir($versions_dir)) {
					global $GO_CONFIG;
					mkdir($versions_dir, $GO_CONFIG->folder_create_mode);
				}

				$filename = utf8_basename($destination_path);
				$version_filepath = $versions_dir.'/'.date('YmdGi').'_'.$_SESSION['GO_SESSION']['username'].'_'.$filename;

				$fs->move($destination_path, $version_filepath);
			}
		}
	}



	function move_file($sourcefile, $destfolder) {
		$existing_file = $this->file_exists($destfolder['id'], $sourcefile['name']);
		if($existing_file) {
			$this->delete_file($existing_file);
		}


		$up_file['id']=$sourcefile['id'];
		$up_file['folder_id']=$destfolder['id'];
		$this->update_file($up_file);
	}

	function move_folder($sourcefolder, $destfolder) {
		if($sourcefolder['parent_id']==$destfolder['id']) {
			return $sourcefolder['id'];
		}
		$existing_folder = $this->folder_exists($destfolder['id'], $sourcefolder['name']);

		$sourcefolder['parent_id']=$destfolder['id'];
		$this->update_folder($sourcefolder);

		if($existing_folder) {
			$files = new files();

			$this->get_files($existing_folder['id']);
			while($file = $this->next_record()) {
				$files->move_file($file, $sourcefolder);
			}

			$this->get_folders($existing_folder['id']);
			while($folder = $this->next_record()) {
				$files->move_folder($folder, $sourcefolder);
			}

			$sql = "DELETE FROM fs_folders WHERE id=?";
			$this->query($sql, 'i', $existing_folder['id']);
		}

		return $sourcefolder['id'];
	}



	function add_file($file) {
		global $GO_CONFIG;

		$file['id']=$this->nextid('fs_files');
		$file['user_id']=$GLOBALS['GO_SECURITY']->user_id;
		$file['extension']=File::get_extension($file['name']);
		$this->insert_row('fs_files', $file);

		$this->cache_file($file);

		$this->add_new_filelink($file);

		return $file['id'];
	}

	function sync_file($file, $full_path) {
		$file['ctime']=filectime($full_path);
		$file['mtime']=filemtime($full_path);
		$file['size']=filesize($full_path);
		$file['extension']=File::get_extension($full_path);

		return $this->update_file($file);
	}

	function import_file($full_path, $parent_id=false) {
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

		if($existing_file) {
			$file['id']=$existing_file['id'];
			$this->update_file($file);
			return $file['id'];
		}else {
			return $this->add_file($file);
		}
	}

	function import_folder($full_path, $parent_id) {
		global $GO_SECURITY, $GO_CONFIG;

		$fs = new filesystem();

		$folder['name']=utf8_basename($full_path);
		$folder['visible']='0';
		$folder['user_id']=$GO_SECURITY->user_id;
		$folder['parent_id']=$parent_id;
		$folder['ctime']=filectime($full_path);

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

		$files = $fs->get_files($full_path);
		while($fs_file = array_shift($files)) {
			$this->import_file($fs_file['path'], $folder['id']);
		}

		$folders = $fs->get_folders($full_path);
		while($fs_folder=array_shift($folders)) {
			$this->import_folder($fs_folder['path'], $folder['id']);
		}
		return $folder['id'];
	}

	function sync_folder($folder_id, $recursive=false){
		global $GO_CONFIG;
		$fs = new filesystem();
		$folder = $this->get_folder($folder_id);

		if(!$folder)
		{
			throw new FileNotFoundException();
		}

		$full_path = $GO_CONFIG->file_storage_path.$this->build_path($folder);
		if(!is_dir($full_path))
		{
			throw new FileNotFoundException();
		}

		$dbfolders=array();
		$dbfolders_names=array();
		$this->get_folders($folder['id']);
		while($dbfolder = $this->next_record())
		{
			$dbfolders_names[]=$dbfolder['name'];
			$dbfolders[]=$dbfolder;
		}
		$fsfolders = $fs->get_folders($full_path);

		foreach($fsfolders as $fsfolder)
		{
			$key = array_search($fsfolder['name'], $dbfolders_names);
			if($key===false)
			{
				$this->import_folder($fsfolder['path'], $folder_id);
			}elseif($recursive){
				$this->sync_folder($dbfolders[$key]['id'], true);
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
		$fsfiles = $fs->get_files($full_path);

		foreach($fsfiles as $fsfile)
		{
			if(!in_array($fsfile['name'], $dbfiles_names))
			{
				$this->import_file($fsfile['path'], $folder_id);
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

		
		//$file['mtime']=time();
		$this->update_row('fs_files', 'id', $file);

		$this->cache_file($file['id']);
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

		return $folder['id'];
	}

	function update_folder($folder) {
		return $this->update_row('fs_folders', 'id', $folder);
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
		$user_id=$this->escape($user_id);
		//ORDER BY PATH important so higher order shares come first
		$sql = "SELECT DISTINCT f.* FROM fs_folders f ".
				"INNER JOIN go_acl a ON (f.acl_read=a.acl_id OR f.acl_write=a.acl_id) ".
				"LEFT JOIN go_users_groups ug ON (a.group_id=ug.group_id) ".
				"WHERE (ug.user_id=".$this->escape($user_id)." OR a.user_id=".$this->escape($user_id).") AND f.user_id!=$user_id ";
		if($visible_only) {
			$sql .= "AND f.visible='1' ";
		}
		$sql .= "ORDER BY name ASC";

		$this->query($sql);
		return $this->num_rows();

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
		$sql = "SELECT * FROM fs_shares WHERE user_id='".$this->escape($user_id)."' ORDER BY path ASC";
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

		if(empty($folder['acl_write'])) {
			if(empty($folder['parent_id'])) {
				return $GO_SECURITY->has_admin_permission($user_id);
			}
			$parent = $this->get_folder($folder['parent_id']);
			return $this->has_write_permission($user_id, $parent);
		}else {
			return $GO_SECURITY->has_permission($user_id, $folder['acl_write']);
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
		if(empty($folder['acl_write'])) {
			if(empty($folder['parent_id'])) {
				return $GO_SECURITY->has_admin_permission($user_id);
			}
			$parent = $this->get_folder($folder['parent_id']);
			return $this->has_read_permission($user_id, $parent);
		}else {

			return $GO_SECURITY->has_permission($user_id, $folder['acl_read']) || $GO_SECURITY->has_permission($user_id, $folder['acl_write']);
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

		$this->query("SELECT ff.* FROM fs_new_files AS fn, fs_files AS ff WHERE fn.file_id = ff.id AND fn.user_id = ?", 'i', $user_id);

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

	public static function login($username, $password, $user) {
	// Default timeout: 30 days
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
		$sql = "SELECT ";
		if($offset>0) {
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		$sql .= "* FROM fs_files ";
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
		return $this->query($sql, $types, $params);
	}

	function has_children($folder_id){
		$sql = "SELECT * FROM fs_folders WHERE parent_id=".$this->escape($folder_id).' LIMIT 0,1';
		$this->query($sql);
		return $this->next_record();
	}

	function get_folders($folder_id, $sortfield='name', $sortorder='ASC', $start=0, $offset=0, $authenticate=false) {
		global $GO_SECURITY;

		$sql = "SELECT ";
		if($authenticate) {
			$sql .= "DISTINCT ";
		}
		if($offset>0) {
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}

		$sql .= "f.* FROM fs_folders f ";

		if($authenticate) {
			$sql .= "LEFT JOIN go_acl a ON (a.acl_id=f.acl_read OR a.acl_id=f.acl_write) ".
					"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id ".
					"WHERE (a.user_id=".$this->escape($GO_SECURITY->user_id)." OR ug.user_id=".$this->escape($GO_SECURITY->user_id)." OR ISNULL(a.acl_id)) AND ";
		}else {
			$sql .= " WHERE ";
		}


		$types='';
		$params=array();

		$sql .= "parent_id=? ";
		$types .= 'i';
		$params[]=$folder_id;

		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);
		if($offset>0) {
		 	$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		return $this->query($sql, $types, $params);


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
			$url=substr($path,0,-1);
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
					$folder = $this->mkdir($folder_id, $first_part,false, $user_id, true,'1');
				}
				return $folder;
			}else {
				return $file;
			}
		}
	}

	function find_share($folder_id) {
		$folder = $this->get_folder($folder_id);

		if ($folder && $folder['acl_read']>0) {
			return $folder;
		}elseif($folder['parent_id']>0) {
			return $this->find_share($folder['parent_id']);
		}else {
			return false;
		}
	}




	function mkdir($parent, $name, $share_user_id=0, $user_id=0, $ignore_existing_filesystem_folder=false, $readonly='0') {

		global $GO_SECURITY, $GO_CONFIG, $lang;

		if($user_id==0) {
			$user_id=$GO_SECURITY->user_id;
		}


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

		$rel_path=$this->build_path($parent);
		$full_path = $GO_CONFIG->file_storage_path.$rel_path;

		if (!$ignore_existing_filesystem_folder && file_exists($full_path.'/'.$name)) {
			throw new Exception($lang['files']['folderExists']);
		}

		if (!file_exists($full_path.'/'.$name) && !mkdir($full_path.'/'.$name, $GO_CONFIG->folder_create_mode,true)) {
			throw new Exception($lang['common']['saveError'].$full_path.'/'.$name);
		} else {
			$folder['readonly']=$readonly;
			$folder['visible']='0';
			$folder['user_id']=$user_id;
			$folder['parent_id']=$parent['id'];
			$folder['name']=$name;
			$folder['ctime']=time();
			if($share_user_id) {
				$folder['acl_read']=$GO_SECURITY->get_new_acl('files', $share_user_id);
				$folder['acl_write']=$GO_SECURITY->get_new_acl('files', $share_user_id);
			}else {
				$folder['acl_read']=0;
				$folder['acl_write']=0;
			}
			$folder['id']=$this->add_folder($folder);
			return $folder;
		}
	}


	/**
	 *
	 * @param $folder_id folder id or record
	 * @param $path
	 * @return unknown_type
	 */

	function build_path($folder_id, $path='') {
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

		//array_unshift($pathinfo, $folder);

		$path = empty($path) ? $folder['name'] : $folder['name'].'/'.$path;
		return $this->build_path($folder['parent_id'], $path);
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
		global $GO_SECURITY;

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
		return $fs->delete($path);
	}

	function delete_file($file) {
		global $GO_CONFIG, $GO_MODULES;

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
		return @unlink($path);
	}

	function get_content_json($folder_id, $sort='name', $dir='ASC', $filter=null) {
		$results = array();
		if($folder_id>0) {

			$folders = $this->get_folders($folder_id, 'name', $dir);
			while($folder=$this->next_record()) {
				if($folder['acl_read']>0) {
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


	public static function check_database() {
		global $GO_USERS, $GO_CONFIG, $GO_SECURITY;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		$fs = new files();

		$fs2 = new files();
		/*echo 'Deleting invalid folders in database'.$line_break;
		 $sql = "SELECT * FROM fs_folders";
		 $fs->query($sql);
		 while($folder = $fs->next_record())
		 {
			$full_path = $GO_CONFIG->file_storage_path.$folder['path'];
			if(!is_dir($full_path))
			{
			$fs2->delete_folder($full_path);
			}
			}

			echo 'Deleting invalid files in database'.$line_break;

			$sql = "SELECT * FROM fs_files";
			$fs->query($sql);
			while($file = $fs->next_record())
			{
			$full_path = $GO_CONFIG->file_storage_path.$file['path'];
			if(!file_exists($full_path))
			{
			$fs2->delete_file($full_path);
			}
			}*/

		echo "Checking user home directories$line_break";



		$GO_USERS->get_users();

		while($GO_USERS->next_record()) {
			$home_dir = 'users/'.$GO_USERS->f('username');

			$folder = $fs->resolve_path($home_dir);

			if(empty($folder['acl_read'])) {
				echo "Sharing users/".$GO_USERS->f('username').$line_break;

				$up_folder['id']=$folder['id'];
				$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
				$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));

				$fs->update_folder($up_folder);
			}
		}

		/*echo 'Correcting id=0'.$line_break;

		$sql = "SELECT path FROM fs_folders WHERE id=0";
		$fs->query($sql);
		while($r = $fs->next_record())
		{
		$r['id']=$fs2->nextid('fs_folders');
		$fs2->update_row('fs_folders', 'path', $r);
		}*/
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

	function cache_file($file) {
		global $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();

		require($GO_LANGUAGE->get_language_file('files'));

		$fs = new files();

		if(is_numeric($file)) {
			$file = $this->get_file($file);
		}

		

		if($file) {
			$share = $fs->find_share($file['folder_id']);

			if(!isset($file['comments']))
				$file['comments']='';

			if($share) {
				$cache['id']=$file['id'];
				$cache['user_id']=$file['user_id'];
				$cache['name'] = htmlspecialchars($file['name'], ENT_QUOTES, 'utf-8');
				$cache['link_type']=6;
				$cache['description']='';
				$cache['type']=$lang['files']['file'];
				$cache['module']='files';
				$cache['keywords']=$file['comments'].','.$cache['name'].','.$cache['type'];
				$cache['mtime']=$file['mtime'];
				$cache['acl_read']=$share['acl_read'];
				$cache['acl_write']=$share['acl_read'];

				//var_dump($cache);

				$search->cache_search_result($cache);
			}
		}
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
	}
}
