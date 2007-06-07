<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.39 $ $Date: 2006/11/24 14:55:32 $

  This program is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published by the
  Free Software Foundation; either version 2 of the License, or (at your
  option) any later version.

 * @package Framework
 */

/**
 * This class is used to manage the modules
 * 
 * TODO long description
 * 
 * @since Group-Office 1.0
 * @package Framework
 * @access public
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
   * Array of all installed modules with thier properties
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
	function GO_MODULES() {
		$this->db();

		if(basename($_SERVER['PHP_SELF']) != 'install.php' && !empty($this->User))
		{
			if(isset($_SESSION['GO_SESSION']['modules']))
			{
				$this->modules = $_SESSION['GO_SESSION']['modules'];
			}else {
				$this->load_modules();
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
		global $GO_SECURITY;
		$this->modules=array();
				
		$modules = $this->get_modules_with_locations();
		for ( $i = 0; $i < count($modules); $i ++ ) {
			$_SESSION['GO_SESSION']['modules'][$modules[$i]['id']] = $modules[$i];
			if ( $GO_SECURITY->logged_in() ) {
				$_SESSION['GO_SESSION']['modules'][$modules[$i]['id']]['write_permission'] =
					$GO_SECURITY->has_permission(
						$_SESSION['GO_SESSION']['user_id'], $modules[$i]['acl_write']);
				$_SESSION['GO_SESSION']['modules'][$modules[$i]['id']]['read_permission'] =
					$_SESSION['GO_SESSION']['modules'][$modules[$i]['id']]['write_permission'] ? true :
						$GO_SECURITY->has_permission(
							$_SESSION['GO_SESSION']['user_id'], $modules[$i]['acl_read']);
			}else
			{
				$_SESSION['GO_SESSION']['modules'][$modules[$i]['id']]['write_permission'] = $_SESSION['GO_SESSION']['modules'][$modules[$i]['id']]['read_permission'] = false;
			}
		}
		$this->modules=$_SESSION['GO_SESSION']['modules'];

	}

	/**
   * Get's information about the module that comes from the module.info file.
   * 
   * TODO long description
   * 
   * @access public
   * 
   * @param string $module_id The name of the module
   * 
   * @return mixed Array with module information or false on failure
   */
	function get_module_info( $module_id ) {
		global $GO_CONFIG;

		$info_file = $GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
		$module_id.$GO_CONFIG->slash.'module.info';

		if ( file_exists( $info_file ) ) {
			require($info_file);
			if ( isset( $module[$module_id] ) ) {
				return $module[$module_id];
			}
		}
		return false;
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
			$this->path = $GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
			$module_id.$GO_CONFIG->slash;
			$this->class_path = $this->path.'classes'.$GO_CONFIG->slash;
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
   * Checks if a user has read premission for a module
   * 
   * TODO long description
   * 
   * @access public
   * 
   * @param Int $user_id The user ID
   * @param string $module_id The name of the module
   * 
   * @return bool
   */
	function has_read_permission( $user_id, $module_id ) {
		global $GO_SECURITY;
		$module = $this->get_module( $module_id );
		if ( $GO_SECURITY->has_permission( $user_id, $module['acl_read'] ) ||
		$GO_SECURITY->has_permission( $user_id, $module['acl_write'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
   * Checks if a user has write premission for a module
   * 
   * TODO long description
   * 
   * @access public
   * 
   * @param Int $user_id The user ID
   * @param string $module_id The name of the module
   * 
   * @return bool
   */
	function has_write_permission( $user_id, $module_id ) {
		global $GO_SECURITY;
		$module = $this->get_module( $module_id );
		return $GO_SECURITY->has_permission( $user_id, $module['acl_write'] );
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

		$sql = "SELECT * FROM modules WHERE id='".addslashes($module_id)."'";
		$this->query($sql);
		if ( $this->next_record(MYSQL_ASSOC) ) {
			$this->Record['full_url'] =
			$GO_CONFIG->full_url.'modules/'.$module_id.'/';
			$this->Record['url'] =
			$GO_CONFIG->host.'modules/'.$module_id.'/';
			$this->Record['path'] =
			$GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
			$module_id.$GO_CONFIG->slash;
			$this->Record['class_path'] =
			$this->Record['path'].'classes'.$GO_CONFIG->slash;
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
	function add_module( $module_id, $version, $acl_read, $acl_write, $sort_order=0, $admin_menu='0') {
		global $GO_CONFIG;

		$sql = "INSERT INTO modules (id, version, acl_read, acl_write, ".
		"sort_order, admin_menu) VALUES ( '".addslashes($module_id)."', '$version',".
		"'$acl_read', '$acl_write', '$sort_order', '$admin_menu' )";

		if ( $this->query( $sql ) ) {
			$install_sql_file = $GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
			$module_id.$GO_CONFIG->slash.'sql'.$GO_CONFIG->slash.
			$module_id.'.install.sql';

			if ( !file_exists( $install_sql_file ) ) {
				$install_sql_file = $GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
				$module_id.$GO_CONFIG->slash.'sql'.$GO_CONFIG->slash.'.install.sql';
				if ( !file_exists( $install_sql_file ) ) {
					return true;
				}
			}
			if ( $queries = get_sql_queries( $install_sql_file ) ) {
				while ( $query = array_shift( $queries ) ) {
					$this->query( $query );
				}
			}
			return true;
		}
		return false;
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
		$sql = "UPDATE modules SET sort_order='$sort_order', admin_menu='$admin_menu' WHERE id='$module_id'";
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
		if ( $module = $this->get_module( $module_id ) ) {
			$GO_SECURITY->delete_acl( $module['acl_read'] );
			$GO_SECURITY->delete_acl( $module['acl_write'] );
			$sql = "DELETE FROM modules WHERE id='".$module_id."'";
			if ( $this->query( $sql ) ) {
				$uninstall_sql_file = $GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
				$module_id.$GO_CONFIG->slash.'sql'.$GO_CONFIG->slash.
				$module_id.'.uninstall.sql';

				if ( !file_exists( $uninstall_sql_file ) ) {
					$uninstall_sql_file = $GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.
					$module_id.$GO_CONFIG->slash.'sql'.$GO_CONFIG->slash.'uninstall.sql';
					if ( !file_exists( $uninstall_sql_file ) ) {
						return true;
					}
				}
				if ( $queries = get_sql_queries( $uninstall_sql_file ) ) {
					while ( $query = array_shift( $queries ) ) {
						$this->query( $query );
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

		$sql = "SELECT * FROM modules";

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
			$this->Record['full_url'] =
			$GO_CONFIG->full_url.'modules/'.$this->f('id').'/';
			$this->Record['url'] =
			$GO_CONFIG->host.'modules/'.$this->f('id').'/';
			$this->Record['path'] =
			$GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.$this->f('id').$GO_CONFIG->slash;
			$this->Record['class_path'] =
			$this->Record['path'].'classes'.$GO_CONFIG->slash;

			if(file_exists($this->Record['path']))
			{
				$modules[] = $this->Record;
			}
		}
		return $modules;
	}

	/**
   * Get's a plugin that is installed for a module.
   * 
   * TODO long description
   * 
   * @access public
   * 
   * @param string $plugin_id The name of the plugin
   * @param string $module_id The name of the module
   * 
   * @return mixed Array with with detailed plugin information or false on failure
   */
	function get_plugin( $plugin_id, $module_id = '' ) {
		global $GO_CONFIG;

		if ( $module_id == '' ) {
			$module_id = $this->id;
			$module_path = $this->path;
		} else {
			if ( !$module = $this->get_module( $module_id ) ) {
				return false;
			} else {
				$module_path = $module['path'];
			}
		}

		$plugin['id'] = $plugin_id;
		$plugin['path'] = $module_path.$plugin_id.$GO_CONFIG->slash;
		$plugin['class_path'] = $plugin['path'].'classes'.$GO_CONFIG->slash;

		if ( file_exists( $plugin['path'] ) ) {
			$plugin['full_url'] =
			$GO_CONFIG->full_url.'modules/'.$module_id.'/'.$plugin_id.'/';
			$plugin['url'] =
			$GO_CONFIG->host.'modules/'.$module_id.'/'.$plugin_id.'/';
			return $plugin;
		} else {
			return false;
		}
	}
}
