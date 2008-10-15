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


/**
 * This class is used to install/remove modules and to access module information
 * in other PHP scripts. This class is always available in $GO_MODULES. 
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * @since Group-Office 1.0
 *
 * @uses db
 */

class GO_MODULES extends db {

	/**
	 * The active user has write permission for the currently active module
	 *
	 * @var     bool
	 * @access  public
	 */
	var $read_permission = false;

	/**
	 * The active user has write permission for the currently active module
	 *
	 * @var     bool
	 * @access  public
	 */
	var $write_permission = false;

	/**
	 * The full path to the currently active module
	 *
	 * @var     string
	 * @access  public
	 */
	var $path;

	/**
	 * The id of the currently active module
	 *
	 * @var     int
	 * @access  public
	 */
	var $id;

	/**
	 * The relative URL to the currently active module
	 *
	 * @var     string
	 * @access  public
	 */
	var $url;

	/**
	 * The full URL to the currently active module
	 *
	 * @var     string
	 * @access  public
	 */
	var $full_url;

	/**
	 * The full path to the classes of the currently active module
	 *
	 * @var     string
	 * @access  public
	 */
	var $class_path;

	/**
	 * Array of all installed modules with thier properties:
	 *
	 * humanName 					The localized name
	 * id									The module name
	 * description
	 * acl_write
	 * acl_read
	 * read_permission 		bool
	 * write_permission 	bool
	 *
	 *
	 * @var     Array
	 * @access  public
	 */
	var $modules = array();

	/**
	 * Constructor. Loads all installed modules into the modules array
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$this->db();		
		if(isset($_SESSION['GO_SESSION']['modules']))
		{
			$this->modules = $_SESSION['GO_SESSION']['modules'];
		}elseif(!defined('NOTINSTALLED') && !empty($this->User))
		{
			$this->load_modules();
		}	
	}
	
	/**
	 * Will call a function in all main module classes
	 * For example fire_event('delete_user', $arguments) will
	 * call the function __on_delete_user in all modules.
	 *
	 * @param string $name
	 * @param array $arguments Passed by reference so the functions can alter the value and return something in this way
	 */
	
	public function fire_event($name, &$arguments=array())
	{
		foreach($this->modules as $module)
		{
			
			$file = $module['class_path'].$module['id'].'.class.inc';

			if(!file_exists($file))
			{
				$file = $module['class_path'].$module['id'].'.class.inc.php';
			}
			if(file_exists($file))
			{
				require_once($file);
				if(class_exists($module['id'], false))
				{				
					$class = new $module['id'];
					$method = '__on_'.$name;
					if(method_exists($class, $method))
					{
						$class->$method($arguments);						
					}
				}
			}
		}
	}

	/**
	 * Load the modules into the modules array
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 *
	 * @return void
	 */
	function load_modules()
	{
		global $GO_SECURITY, $GO_LANGUAGE, $lang_modules;
		$this->modules=array();
		$_SESSION['GO_SESSION']['modules']=array();

		$modules_props = $this->get_modules_with_locations();

		for ( $i = 0; $i < count($modules_props); $i ++ ) {
			$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']] = $modules_props[$i];
			if (isset($GO_SECURITY) &&  $GO_SECURITY->logged_in() ) {
				$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['write_permission'] =
				$GO_SECURITY->has_permission(
				$_SESSION['GO_SESSION']['user_id'], $modules_props[$i]['acl_write']);
				$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['read_permission'] =
				$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['write_permission'] ? true :
				$GO_SECURITY->has_permission(
				$_SESSION['GO_SESSION']['user_id'], $modules_props[$i]['acl_read']);
					
					
					
			}else
			{
				$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['write_permission'] = $_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['read_permission'] = false;
			}

			$language_file = $GO_LANGUAGE->get_language_file($modules_props[$i]['id']);
			if(file_exists($language_file))
			{
				require($language_file);
			}
				
			$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['humanName'] = isset($lang[$modules_props[$i]['id']]['name']) ? $lang[$modules_props[$i]['id']]['name'] : $modules_props[$i]['id'];
			$_SESSION['GO_SESSION']['modules'][$modules_props[$i]['id']]['description'] = isset($lang[$modules_props[$i]['id']]['description']) ? $lang[$modules_props[$i]['id']]['description'] : '';
				
		}
		$this->modules=$_SESSION['GO_SESSION']['modules'];

	}


	/**
	 * Checks if the currently active user is permissioned for a module
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 * @param string $module_id The name of the module
	 * @param bool $admin Admin permissions required
	 *
	 * @return bool
	 */
	function authenticate( $module_id, $admin = false ) {
		global $GO_CONFIG, $GO_SECURITY;
		if ( isset( $this->modules[$module_id] ) ) {
			$module = $this->modules[$module_id];
			$_SESSION['GO_SESSION']['active_module'] = $module_id;
			$this->path = $GO_CONFIG->root_path.'modules/'.$module_id.'/';
			$this->class_path = $this->path.'classes/';
			$this->read_permission = $module['read_permission'];
			$this->write_permission = $module['write_permission'];
			$this->id = $module_id;
			$this->full_url = $GO_CONFIG->full_url.'modules/'.$module_id.'/';
			$this->url = $GO_CONFIG->host.'modules/'.$module_id.'/';

			if ( $this->read_permission || $this->write_permission ) {
				if ( $admin ) {
					if ( $this->write_permission ) {
						return true;
					}
				} else {
					return true;
				}
			}
			header( 'Location: '.$GO_CONFIG->host.'error_docs/403.php' );
			exit();
		} else {
			exit( 'Invalid module specified' );
		}
	}


	/**
	 * Get information of a module in an Array
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 * @param string $module_id The name of the module
	 *
	 * @return mixed array with module information or false on failure
	 */
	function get_module( $module_id ) {
		global $GO_CONFIG;

		$sql = "SELECT * FROM go_modules WHERE id='".addslashes($module_id)."'";
		$this->query($sql);
		if ( $this->next_record(MYSQL_ASSOC) ) {
			$this->Record['full_url'] =
			$GO_CONFIG->full_url.'modules/'.$module_id.'/';
			$this->Record['url'] =
			$GO_CONFIG->host.'modules/'.$module_id.'/';
			$this->Record['path'] =
			$GO_CONFIG->root_path.'modules/'.$module_id.'/';
			$this->Record['class_path'] =
			$this->Record['path'].'classes/';
			return $this->Record;
		} else {
			return false;
		}
	}

	/**
	 * Installs a module
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 * @param string $module_id The name of the module
	 * @param float $version The version of the module
	 * @param int $acl_read The ACL id used to control read permissions
	 * @param int $acl_write The ACL id used to control write permissions. This means a user has admin permission for a module
	 * @param int $sort_order The sort index used to control the position in the module
	 *
	 * @return mixed array with module information or false on failure
	 */
	function add_module($module_id) {
		global $GO_CONFIG, $GO_SECURITY;

		$module['id']=$module_id;
		$module['sort_order'] = count($this->modules)+1;
		$module['acl_read']=$GO_SECURITY->get_new_acl();
		$module['acl_write']=$GO_SECURITY->get_new_acl();
		
		$updates_file = $GO_CONFIG->root_path.'modules/'.$module_id.'/install/updates.inc.php';		
		if(file_exists($updates_file))
		{
			require($updates_file);			
			$module['version']=isset($updates) ? count($updates) : 0;
		}

		$this->insert_row('go_modules', array_map('addslashes', $module));


		$install_sql_file = $GO_CONFIG->root_path.'modules/'.$module_id.'/install/install.sql';

		if ( file_exists( $install_sql_file ) ) {
			if ( $queries = String::get_sql_queries($install_sql_file)) {
				while ( $query = array_shift( $queries ) ) {
					$this->query($query);
				}
			}
		}

		$install_script = $GO_CONFIG->root_path.'modules/'.$module_id.'/install/install.inc.php';
		if(file_exists($install_script))
		{
			require($install_script);
		}
		$params = array('module'=>$module);
		$this->fire_event('install_module', $arams);
		
		$this->load_modules();

		return true;
	}

	/**
	 * Set's the order a module appears in the menu bar
	 *
	 * TODO long description
	 *
	 * @access public
	 * @param string $module_id The name of the module
	 * @param string $admin_menu If the module should be in the admin menu
	 * @param int $sort_order The sort index
	 *
	 * @return bool True on success
	 */
	function update_module($module_id, $sort_order, $admin_menu ) {
		$admin_menu = $admin_menu ? '1' : '0';
		$sql = "UPDATE go_modules SET sort_order='$sort_order', admin_menu='$admin_menu' WHERE id='$module_id'";
		return $this->query($sql);
	}

	/**
	 * Installs a module
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 * @param string $module_id The name of the module
	 *
	 * @return mixed array with module information or false on failure
	 */
	function delete_module( $module_id ) {
		global $GO_SECURITY, $GO_CONFIG;
		if ( $module = $this->get_module($module_id)) {
			
			$this->fire_event('uninstall_module', $module);
			
			$uninstall_script = $GO_CONFIG->root_path.'modules/'.$module_id.'/install/uninstall.inc.php';
			if(file_exists($uninstall_script))
			{
				require($uninstall_script);
			}			
			
			
			$GO_SECURITY->delete_acl($module['acl_read']);
			$GO_SECURITY->delete_acl($module['acl_write']);
			$sql = "DELETE FROM go_modules WHERE id='".$module_id."'";
			if ( $this->query( $sql ) ) {
				$uninstall_sql_file = $GO_CONFIG->root_path.'modules/'.$module_id.'/install/uninstall.sql';

				if (file_exists($uninstall_sql_file)) {
					if ( $queries = String::get_sql_queries( $uninstall_sql_file ) ) {
						while ( $query = array_shift( $queries ) ) {
							$this->query( $query );
						}
					}
				}
			}
			
			
			return true;
		}
		return false;
	}

	/**
	 * Get's all modules from the database
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 * @return int Number of installed modules
	 */
	function get_modules($admin_menu=null) {

		$sql = "SELECT * FROM go_modules";

		if(isset($admin_menu))
		{
			$admin_menu = $admin_menu ? '1' : '0';
			$sql .= " WHERE admin_menu='$admin_menu'";
		}
		$sql .= " ORDER BY sort_order ASC";
		$this->query( $sql );
		return $this->num_rows();
	}

	/**
	 * Get's all modules in an array with detailed information
	 *
	 * TODO long description
	 *
	 * @access public
	 *
	 * @return Array All modules with detailed information
	 */
	function get_modules_with_locations($admin_menu=null) {
		global $GO_CONFIG;

		$modules = array();
		$this->get_modules($admin_menu);
		while ( $this->next_record(MYSQL_ASSOC) ) {
				
			$this->Record['path'] = $GO_CONFIG->root_path.'modules/'.$this->f('id').'/';
			$this->Record['full_url'] =	$GO_CONFIG->full_url.'modules/'.$this->f('id').'/';
			$this->Record['url'] = $GO_CONFIG->host.'modules/'.$this->f('id').'/';
			$this->Record['legacy']=false;
			if(!file_exists($this->Record['path']))
			{
				$this->Record['path'] = $GO_CONFIG->root_path.'legacy/modules/'.$this->f('id').'/';
				$this->Record['full_url'] =	$GO_CONFIG->full_url.'legacy/modules/'.$this->f('id').'/';
				$this->Record['url'] = $GO_CONFIG->host.'legacy/modules/'.$this->f('id').'/';
				$this->Record['legacy']=true;
			}
			if(file_exists($this->Record['path']))
			{
				$this->Record['class_path'] =	$this->Record['path'].'classes/';
					
				$modules[] = $this->Record;
			}
		}
		return $modules;
	}
}
