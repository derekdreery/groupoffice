<?php
/**
 * @copyright Copyright &copy; Intermesh 2004
 * @version $Revision: 1.10 $ $Date: 2006/11/24 14:55:32 $
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Configuration
 */

/**
 * This class holds the main configuration options of Group-Office
 * Don't modify this file. The values defined here are just default values.
 * They are overwritten by the configuration options in local/config.php. 
 * To edit these options use install.php.
 *
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 1.0
 * 
 * @package Framework
 * @subpackage Configuration
 * 
 * @access public
 */
class GO_CONFIG
{
	#FRAMEWORK VARIABLES

	/**
   * The Group-Office version number
   *
   * @var     string
   * @access  public
   */
  var $version = '2.16';
  
  /**
   * The Group-Office database version number
   *
   * @var     string
   * @access  public
   */
  var $db_version = '219';

	/**
   * The Group-Office release number
   *
   * @var     string
   * @access  public
   */
  var $release = '1';

  	/**
   * The Group-Office server ID
   *
   * @var     string
   * @access  public
   */
  var $id = 'groupoffice';
  
  /**
   * Enable debugging mode
   *
   * @var     bool
   * @access  public
   */
  var $debug = false;

  /**
   * Enable syslog
   *
   * @var     bool
   * @access  public
   */
   
  var $log = false;

  /**
   * Slash to use '/' for linux and '\\' for windows
   *
   * @var     string
   * @access  public
   */
  var $slash = '/';

  /**
   * Default language
   *
   * @var     string
   * @access  public
   */
  var $language = 'en';
  
  /**
   * Default country
   *
   * @var     string
   * @access  public
   */
  var $default_country_id = 150;
  
  /**
   * Default language
   *
   * @var     string
   * @access  public
   */
  var $default_timezone = '1';
  
  /**
   * Default language
   *
   * @var     string
   * @access  public
   */
  var $default_dst = '1';

  /**
   * Default language
   *
   * @var     string
   * @access  public
   */ 
  var $default_currency='EUR';
  
  /**
   * Default date format
   *
   * @var     string
   * @access  public
   */
  var $default_date_format='dmY';
  
   /**
   * Default date seperator
   *
   * @var     string
   * @access  public
   */
  var $default_date_seperator='-';
  
  /**
   * Default time format
   *
   * @var     string
   * @access  public
   */
  var $default_time_format='G:i';
  
  /**
   * Default first day of the week 0=sunday 1=monday
   *
   * @var     string
   * @access  public
   */
  var $default_first_weekday=1;
  
  /**
   * Default decimal seperator for numbers
   *
   * @var     string
   * @access  public
   */
  var $default_decimal_seperator=',';
  
  /**
   * Default thousands seperator for numbers
   *
   * @var     string
   * @access  public
   */
  var $default_thousands_seperator='.';
  
  /**
   * Default theme
   *
   * @var     string
   * @access  public
   */
  var $theme = 'Default';

  /**
   * Enable theme switching by users
   *
   * @var     bool
   * @access  public
   */
  var $allow_themes = true;

  /**
   * Enable password changing by users
   *
   * @var     bool
   * @access  public
   */
  var $allow_password_change = true;
  
  /**
   * Enable user registration by everyone
   *
   * @var     bool
   * @access  public
   */
  var $allow_registration = false;

 /**
   * Enabled fields for the user registration form
   *
   * @var     bool
   * @access  public
   */
  var $registration_fields = 'title_initials,sex,birthday,address,home_phone,fax,cellular,company,department,function,work_address,work_phone,work_fax,homepage';
  
  
  /**
   * Enabled fields for the user registration form
   *
   * @var     bool
   * @access  public
   */
  var $required_registration_fields = 'company,address';
  
  
  /**
   * Activate self regstered accounts?
   *
   * @var     bool
   * @access  public
   */
  var $auto_activate_accounts = false;
  
  /**
   * Notify webmaster of user signup?
   *
   * @var     bool
   * @access  public
   */
  var $notify_admin_of_registration = true;
  
  /**
   * Grant read permissions for these modules to new self-registered users.
   * Module names are seperated by a comma.
   *
   * @var     string
   * @access  public
   */
  var $register_modules_read = 'calendar,addressbook,email,todos,summary,filesystem,notes';
  
  /**
   * Grant write permissions for these modules to new self-registered users.
   * Module names are seperated by a comma.
   *
   * @var     string
   * @access  public
   */
  var $register_modules_write = '';
  

  /**
   * Add self-registered users to these user groups
   * Group names are seperated by a comma.
   *
   * @var     string
   * @access  public
   */
  var $register_user_groups = '';
  
   /**
   * Self-registered users will be visible to these user groups
   * Group names are seperated by a comma.
   *
   * @var     string
   * @access  public
   */
  var $register_visible_user_groups = 'Everyone';
   
  /**
   * Relative hostname with slash on both start and end
   *
   * @var     string
   * @access  public
   */
  var $host = '/groupoffice/';

  /**
   * Full URL to reach Group-Office with slash on end
   *
   * @var     string
   * @access  public
   */
  var $full_url = 'http://localhost/groupoffice/';

  /**
   * Title of Group-Office
   *
   * @var     string
   * @access  public
   */
  var $title = 'Group-Office';

  /**
   * The e-mail of the webmaster
   *
   * @var     string
   * @access  public
   */
  var $webmaster_email = 'webmaster@example.com';

  /**
   * The path to the root of Group-Office with slash on end
   * 
   * @var     string
   * @access  public
   */
  var $root_path = '';

  /**
   * The path to store temporary files with a slash on end
   * Leave to ../ for installation
   *
   * @var     string
   * @access  public
   */
  var $tmpdir = '/tmp/';

  /**
   * The maximum number of users
   *
   * @var     int
   * @access  public
   */
  var $max_users = 0;

  /**
   * Refresh interval in seconds for the mail & event checker
   *
   * @var     string
   * @access  public
   */
  var $refresh_rate = '60';

  /**
   * The login screen to display
   *
   * @var     string
   * @access  public
   */
  var $login_screen = 'Default';

	#database configuration
  /**
   * The database type to use. Currently only MySQL is supported
   *
   * @var     string
   * @access  public
   */
  var $db_type = 'mysql';
  /**
   * The host of the database
   *
   * @var     string
   * @access  public
   */
  var $db_host = '';
  /**
   * The name of the database
   *
   * @var     string
   * @access  public
   */
  var $db_name = '';
  /**
   * The username to connect to the database
   *
   * @var     string
   * @access  public
   */
  var $db_user = '';
  /**
   * The password to connect to the database
   *
   * @var     string
   * @access  public
   */
  var $db_pass = '';

 	/** Path to local installation specific files
   * 
   * @var     string
   * @access  public
   */
  var $local_path = '';
  
  /** URL to local installation specific files
   * 
   * @var     string
   * @access  public
   */
  var $local_url = '';

  /**
   * The path to external authentication sources file
   * 
   * @var     string
   * @access  public
   */
  var $auth_sources = '';
  
   /**
   * Auth source to use for sync
   * @var     Int
   * @access  public
   */
  var $sync_auth_source_key = 0;

#FILE BROWSER VARIABLES
  /**
   * The path to the mime.types file in Linux
   *
   * @var     string
   * @access  public
   */
  var $mime_types_file = '/etc/mime.types';
  /**
   * The path to the location where the files of the file browser module are stored
   *
   * This path should NEVER be inside the document root of the webserver
   * this directory should be writable by apache. Also choose a partition that
   * has enough diskspace.
   *
   * @var     string
   * @access  public
   */
  var $file_storage_path = '/home/groupoffice/';
  /**
   * The permissions mode to use when creating files and folders
   *
   * @var     hexadecimal
   * @access  public
   */
  var $create_mode = '0755';
  /**
   * The maximum file size the filebrowser attempts to upload. Be aware that 
   * the php.ini file must be set accordingly (http://www.php.net).
   *
   * @var     string
   * @access  public
   */
  var $max_file_size = '10000000';
  /**
   * The maximum amount of diskspace that a user may use in Kb 
   *
   * @var     int
   * @access  public
   */
  var $user_quota = 0;	# kb


  #WebDAV VARIABLES
  /**
   * Enable WebDAV?
   *
   * @var     bool
   * @access  public
   */
  var $dav_switch = false;
  /**
   * Path to Apache DAV library with slash on end
   *
   * @var     string
   * @access  public
   */
  var $dav_apachedir = '';
  /**
   * Path to Apache drafts with slash on end
   *
   * @var     string
   * @access  public
   */
  var $dav_drafts = '';
  /**
   * DAV authentication source
   *
   * When it is empty, use the MySQL-Authentication
   * For the test: ldap://localhost/dc=tgm,dc=ac,dc=at?uid
   *
   * @var     string
   * @access  public
   */
  var $dav_auth = '%ldap_dav_auth%';
  /**
   * The alias as defined in Apache configuration to access the DAV files
   * with slash at start and end.
   *
   * @var     string
   * @access  public
   */
  var $dav_alias = '/dav/';
  /**
   * The name of the access file (usually .htaccess)
   *
   * @var     string
   * @access  public
   */
  var $dav_accessfilename = '.htaccess';
  /**
   * The name of the folder to put the symblic links to the Group-Office shares
   *
   * @var     string
   * @access  public
   */
  var $name_of_sharedir = 'Shares';
  
#email variables
  /**
   * The E-mail mailer type to use. Valid options are: smtp, qmail, sendmail, mail 
   *
   * @var     int
   * @access  public
   */
  var $mailer = 'smtp';
  /**
   * The SMTP host to use when using the SMTP mailer 
   *
   * @var     string
   * @access  public
   */
  var $smtp_server = 'localhost';
  /**
   * The SMTP port to use when using the SMTP mailer 
   *
   * @var     string
   * @access  public
   */
  var $smtp_port = '25';
  
   /**
   * The SMTP username for authentication (Empty for no authentication) 
   *
   * @var     string
   * @access  public
   */
  var $smtp_username = '';
  
  /**
   * The SMTP password for authentication
   *
   * @var     string
   * @access  public
   */
  var $smtp_password = '';
  
  /**
   * Connection string options to append to the hostname when connecting to IMAP
   * servers using the PHP imap extension. Some distributions require /notls here.
   *
   * @var     string
   * @access  public
   */
  var $email_connectstring_options = '';
  /**
   * The maximum size of e-mail attachments the browser attempts to upload.
   * Be aware that the php.ini file must be set accordingly (http://www.php.net).
   *
   * @var     string
   * @access  public
   */
  var $max_attachment_size = '10000000';
  /**
   * The width of the E-mail composer's popup window
   *
   * @var     string
   * @access  public
   */
  var $composer_width = '780';
  /**
   * The height of the E-mail composer's popup window
   *
   * @var     string
   * @access  public
   */
  var $composer_height = '580';
  
  
  //External programs
  
   /**
   * Command to create ZIP archive
   * @var     string
   * @access  public
   */
  var $cmd_zip = '/usr/bin/zip';

   /**
   * Command to unpack ZIP archive
   * @var     string
   * @access  public
   */
  var $cmd_unzip = '/usr/bin/unzip';

   /**
   * Command to control TAR archives
   * @var     string
   * @access  public
   */
  var $cmd_tar = '/bin/tar';

   /**
   * Command to set system passwords. Used by passwd.users.class.inc.
   * SUDO must be set up!
   *
   * @var     string
   * @access  public
   */
  var $cmd_chpasswd = '/usr/sbin/chpasswd';
  
    /**
   * Command to SUDO
   * @var     string
   * @access  public
   */
  var $cmd_sudo = '/usr/bin/sudo';
  
  /**
   * Command to get user quota's. Used by passwd.users.class.inc.
   * Leave empty to disable. SUDO must be set up!
   *
   * @var     string
   * @access  public
   */
  var $cmd_quota = '';

  /**
   * Command to set user quota's. Used by passwd.users.class.inc.
   * Leave empty to disable. SUDO must be set up!
   *
   * @var     string
   * @access  public
   */
  var $cmd_edquota = '';

  /**
   * Command to script to set a postfix alias
   *
   * @var     string
   * @access  public
   */
  var $cmd_alias = '/usr/local/bin/alias.sh';
    
  /**
   * If $quota_protouser is set, a default quota will be set from that user with
   * `edquota -p QUOTAUSER newuser
   *
   * @var     string
   * @access  public
   */
  var $quota_protouser = '';
  
 /**
	* Automatically popup Group-Office in a screen without the brwoser
	* toolbars
	*
	* @var     bool
	* @access  public
	*/ 
  
  var $force_fullscreen=false;

 /**
	* Go to this URL after logout
	*
	* @var     string
	* @access  public
	*/ 
  
  var $logout_url='';
  
  /**
	* Use the java upload dialog to upload files.
	*
	* @var     bool
	* @access  public
	*/ 
  
  var $use_jupload=false;
  
  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////      Do not change underneath this      ///////////////////
  ////////////////////////////////////////////////////////////////////////////////
	#group configuration
  /**
   * The administrator user group ID
   *
   * @var     string
   * @access  public
   */
  var $group_root = '1';
  /**
   * The everyone user group ID
   *  
   * @var     string
   * @access  public
   */
  var $group_everyone = '2';

  /**
   * Date formats to be used. Only Y, m and d are supported.
   *
   * @var     string
   * @access  public
   */
  var $date_formats = array(
      'dmY',
      'mdY',
      'Ymd'
      );
      
  /**
   * Date seperators to be used.
   *
   * @var     string
   * @access  public
   */
   
  var $date_seperators = array(
  	'-', 
  	'.',
  	'/'
  	);
  /**
   * Time formats to be used.
   *
   * @var     string
   * @access  public
   */
  var $time_formats = array(
      'G:i',
      'g:i a'
      );

  /**
   * Relative path to the modules directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $module_path = 'modules';
  /**
   * Relative URL to the administrator directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */

  var $configuration_url = 'configuration';
  /**
   * Relative path to the classes directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $class_path = 'classes';
  /**
   * Relative path to the controls directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $control_path = 'controls';
  /**
   * Relative URL to the controls directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $control_url = 'controls';
  /**
   * Relative path to the themes directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $theme_path = 'themes';
  
  /**
   * Relative URL to the themes directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $theme_url = 'themes';
  
  /**
   * Relative path to the language directory with no slash at start and end
   *
   * @var     string
   * @access  private
   */
  var $language_path = 'language';

  /**
   * Database object
   *
   * @var     object
   * @access  private
   */
  var $db;

  /**
   * The window mode of Group-Office
   *
   * @var     string
   * @access  public
   */
  var $window_mode = 'normal';
  

  
  /**
   * Set this value for PHP5 only.
   *
   * @var     string
   * @access  public
   */
  var $default_timezone_string = '';



  /**
   * Constructor. Initialises all public variables.
   *
   * @access public
   * @return void
   */
  function GO_CONFIG()
  {
  	
  	if($this->root_path == '')
  	{
  		//Detect some default values for installation if root_path is not set yet
  		$this->root_path = str_replace('classes/base/config.class.inc','',__FILE__);
  		$this->host = str_replace('install/install.php', '', $_SERVER['PHP_SELF']);  		
  		$this->full_url = 'http://'.$_SERVER['SERVER_NAME'].$this->host;
  		
  		$this->local_path = $this->root_path.'local/';
  		$this->local_url = $this->host.'local/';
  		
  		if(ini_get('safe_mode') == '0' && file_exists('/etc/mime.types'))
  		{
  			$this->mime_types_file = '/etc/mime.types';
  		}else
  		{
  			$this->mime_types_file = $this->root_path.'/lib/mime.types';
  		}
  	}
  	
  	$config = array();
  	
 		@include($this->get_config_file());
  	
  	foreach($config as $key=>$value)
  	{
  		$this->$key=$value;
  	} 	
  	  	
    // path to classes
    $this->class_path = $this->root_path.$this->class_path.$this->slash;
    
    // path to themes
    $this->theme_path = $this->root_path.$this->theme_path.$this->slash;
    
    // URL to themes
    $this->theme_url = $this->host.$this->theme_url.'/';

    // path to controls
    $this->control_path = $this->root_path.$this->control_path.'/';

    // url to controls
    $this->control_url = $this->host.$this->control_url.'/';

    // path to modules
    $this->module_path = $this->root_path.$this->module_path.$this->slash;

    // url to user configuration apps
    $this->configuration_url = $this->host.$this->configuration_url.'/';

    // database class library
    require_once($this->root_path.'database/'.$this->db_type.".class.inc");
    if(isset($config['db_user']))
    {   
	    $this->db = new db();
	    $this->db->Host = $this->db_host;
	    $this->db->Database = $this->db_name;
	    $this->db->User = $this->db_user;
	    $this->db->Password = $this->db_pass;
    }
    
    if(is_string($this->create_mode))
    {
		$this->create_mode=octdec($this->create_mode);
	}
	
	if($this->debug)
	{
		$this->log=true;
	}
  }
  
  /**
   * Get's the location of a configuration file.
   * Group-Office searches two locations:
   *  1. /etc/Group-Office/APACHE SERVER NAME/subdir/to/groupoffice/config.php
   *	2. /path/to/groupoffice/config.php
   *
   * The first location is more secure because the sensitive information is kept
   * outside the document root.
   *
   * @access public
   * @return string Path to configuration file
   */
  
  function get_config_file()
  {
  	$subdir = str_replace($_SERVER['DOCUMENT_ROOT'],$_SERVER['SERVER_NAME'],__FILE__);
  	$config_file = '/etc/Group-Office/'.str_replace('classes/base/config.class.inc', 'config.php',$subdir);
  	if(@file_exists($config_file))
  	{
  		return $config_file;
  	}else
  	{
  		return $this->root_path.'config.php';
  	}
  }

  
  /**
   * Gets a custom saved setting from the database
   *
   * @param 	string $name Configuration key name
   * @access public
   * @return string Configuration key value
   */
  function get_setting($name, $user_id=0)
  {
    $this->db->query("SELECT * FROM settings WHERE name='$name' AND user_id=$user_id");
    if ( $this->db->next_record() ) {
      return $this->db->f('value');
    }
    return false;
  }

  /**
   * Saves a custom setting to the database
   *
   * @param 	string $name Configuration key name
   * @param 	string $value Configuration key value
   * @access public
   * @return bool Returns true on succes
   */
  function save_setting( $name, $value, $user_id=0)
  {
    if ( $this->get_setting($name, $user_id) === false ) {
      return $this->db->query("INSERT INTO settings (name, value, user_id) VALUES ('$name', '$value', '$user_id')");
    } else {
      return $this->db->query("UPDATE settings SET value='$value' WHERE name='$name' AND user_id='$user_id'");
    }
  }

  /**
   * Deletes a custom setting from the database
   *
   * @param 	string $name Configuration key name
   * @access public
   * @return bool Returns true on succes
   */
  function delete_setting( $name )
  {
    return $this->db->query("DELETE FROM settings WHERE name='$name'");
  }
}
