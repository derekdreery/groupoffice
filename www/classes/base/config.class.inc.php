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
 * @version $Id: config.class.inc.php 2939 2008-09-02 09:30:37Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This class holds the main configuration options of Group-Office
 * Don't modify this file. The values defined here are just default values.
 * They are overwritten by the configuration options in /config.inc.php or
 * /etc/Group-Office/{HOSTNAME}/config.inc.php
 *
 * To edit these options use install.php.
 *
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 2939 2008-09-02 09:30:37Z mschering $
 * @copyright Copyright Intermesh
 * @package go.basic
 *
 * @uses db
 */

class GO_CONFIG
{
	#FRAMEWORK VARIABLES

	/**
	 * Enable this Group-Office installation?
	 *
	 * @var     string
	 * @access  public
	 */
	var $enabled = true;

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
	var $default_country = "NL";

	/**
	 * Default timezone
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_timezone = 'Europe/Amsterdam';

	/**
	 * Default language
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_currency='€';

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
	 * Allow e-mail address more then once
	 *
	 * @var     bool
	 * @access  public
	 */
	var $allow_duplicate_email = false;

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
	var $register_modules_read = 'notes,email,summary,mailings,addressbook,calendar,files,tasks,sync,gota';

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



	#FILE BROWSER VARIABLES

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
	 * The maximum file size the filebrowser attempts to upload. Be aware that
	 * the php.ini file must be set accordingly (http://www.php.net).
	 *
	 * @var     string
	 * @access  public
	 */
	var $max_file_size = '10000000';


	#email variables
	/**
	* The E-mail mailer type to use. Valid options are: smtp, qmail, sendmail, mail
	*
	* @var     int
	* @access  public
	*/
	//var $mailer = 'smtp';
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
	 * A comma separated list of smtp server IP addresses that you
	 * want to restrict.
	 *
	 * eg. '213.207.103.219:10,127.0.0.1:10';
	 *
	 * Will restrict those IP's to 10 e-mails per day.
	 *
	 * @var unknown_type
	 */

	var $restrict_smtp_hosts = '';

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
	 * Command to convert xml to wbxml
	 *
	 * @var     string
	 * @access  public
	 */
	var $cmd_xml2wbxml = '/usr/bin/xml2wbxml';

	/**
	 * Command to convert wbxml to xml
	 *
	 * @var     string
	 * @access  public
	 */
	var $cmd_wbxml2xml = '/usr/bin/wbxml2xml';

	/**
	 * Command to unpack winmail.dat files
	 *
	 * @var     string
	 * @access  public
	 */
	var $cmd_tnef = '/usr/bin/tnef';

	/**
	 * If this URL is set and PhpMyAdmin is configured to allow authentication
	 * with signon. You can edit the database in the admin tools module.
	 *
	 * Example phpmyadmin configuration:
	 *
	 * $cfg['Servers'][$i]['auth_type'] = 'signon';
	 * $cfg['Servers'][$i]['SignonSession'] = 'groupoffice';
	 * $cfg['Servers'][$i]['SignonURL']='http://localhost/phpmyadmin/';
	 *
	 * @var unknown_type
	 */
	var $phpMyAdminUrl='';

	/*//////////////////////////////////////////////////////////////////////////////
	//////////      Variables that are not configured by config.php   //////////////
	//////////////////////////////////////////////////////////////////////////////*/


	/**
	 * The Group-Office version number
	 *
	 * @var     string
	 * @access  public
	 */
	var $version = '3.00';
	
	
	/**
	 * Modification date
	 * 
	 * @var     string
	 * @access  public
	 */
	
	var $mtime = '20080910';
	
	/* The permissions mode to use when creating files and folders
	 *
	 * @var     hexadecimal
	 * @access  public
	 */
	var $create_mode = '0755';

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
	 * The internal user group ID
	 *
	 * @var     string
	 * @access  public
	 */
	var $group_internal = '3';

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
      		//$this->root_path = str_replace('classes/base/config.class.inc','',__FILE__);
      		$this->root_path = str_replace('\\','/',dirname(dirname(dirname(__FILE__)))).'/';
      		$this->host = dirname(dirname($_SERVER['PHP_SELF'])).'/';
      		$this->full_url = 'http://'.$_SERVER['SERVER_NAME'].dirname(dirname($_SERVER['PHP_SELF'])).'/';

      		$this->local_path = $this->root_path.'local/';
      		$this->local_url = $this->host.'local/';

      		$this->db_host='localhost';
      	}

      	$config = array();

      	@include($this->get_config_file());

      	foreach($config as $key=>$value)
      	{
      		$this->$key=$value;
      	}

      	// path to classes
      	$this->class_path = $this->root_path.$this->class_path.'/';

      	// path to themes
      	$this->theme_path = $this->root_path.$this->theme_path.'/';

      	// URL to themes
      	$this->theme_url = $this->host.$this->theme_url.'/';

      	// path to controls
      	$this->control_path = $this->root_path.$this->control_path.'/';

      	// url to controls
      	$this->control_url = $this->host.$this->control_url.'/';

      	// path to modules
      	$this->module_path = $this->root_path.$this->module_path.'/';

      	// url to user configuration apps
      	$this->configuration_url = $this->host.$this->configuration_url.'/';

      	// database class library
      	require_once($this->root_path.'database/'.$this->db_type.".class.inc");

      	$this->db = new db();
      	$this->db->Host = $this->db_host;
      	$this->db->Database = $this->db_name;
      	$this->db->User = $this->db_user;
      	$this->db->Password = $this->db_pass;


      	if(is_string($this->create_mode))
      	{
      		$this->create_mode=octdec($this->create_mode);
      	}

      	if($this->debug)
      	{
      		$this->log=true;
      	}
      	 
      	$this->set_full_url();
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
      	if(defined('CONFIG_FILE'))
      	return CONFIG_FILE;

      	$config_file = '/etc/groupoffice/'.$_SERVER['SERVER_NAME'].'/config.php';
      	 
      	if(@file_exists($config_file))
      	{
      		return $config_file;
      	}else
      	{
      		return $this->root_path.'config.php';
      	}
      }

      function set_full_url() {
				if(isset($_SERVER["SERVER_NAME"]))
				{
	      	$https = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on";
	      	$url = 'http';
	      	if ($https)
	      	{
	      		$url .= "s";
	      	}
	      	$url .= "://";
	      	if ((!$https && $_SERVER["SERVER_PORT"] != "80") || ($https && $_SERVER["SERVER_PORT"] != "443")) {
	      		$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$this->host;
	      	} else {
	      		$url .= $_SERVER["SERVER_NAME"].$this->host;
	      	}
	      	$this->full_url=$url;
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
      	$this->db->query("SELECT * FROM go_settings WHERE name='$name' AND user_id=$user_id");
      	if ( $this->db->next_record() ) {
      		return $this->db->f('value');
      	}
      	return false;
      }

      /*
       * Gets all custom saved user settings from the database
       *
       * @param  user_id The user ID to get the settings for.
       * @access public
       * @return array Configurations with key and value
       */
      function get_settings($user_id)
      {
      	$settings=array();
      	$this->db->query("SELECT * FROM go_settings WHERE user_id=$user_id");
      	while($this->db->next_record())
      	{
      		$settings[$this->db->f('name')]=$this->db->f('value');
      	}
      	return $settings;
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
      		return $this->db->query("INSERT INTO go_settings (name, value, user_id) VALUES ('$name', '$value', '$user_id')");
      	} else {
      		return $this->db->query("UPDATE go_settings SET value='$value' WHERE name='$name' AND user_id='$user_id'");
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
      	return $this->db->query("DELETE FROM go_settings WHERE name='$name'");
      }

      function save_state($user_id, $index, $name, $value)
      {
      	$state['user_id']=$user_id;
      	$state['index']=$index;
      	$state['name']=$name;
      	$state['value']=$value;

      	return $this->db->replace_row('go_state',$state);
      }

      function get_state($user_id, $index)
      {
      	$state = array();
      	$sql = "SELECT * FROM go_state WHERE user_id=$user_id AND `index`='$index'";
      	$this->db->query($sql);

      	while($this->db->next_record(MYSQL_ASSOC))
      	{
      		$state[$this->db->f('name')]=$this->db->f('value');
      	}
      	return $state;
      }



      function get_client_settings()
      {
      	global $GO_SECURITY, $GO_MODULES, $GO_THEME, $GO_LANGUAGE;

      	$response['state_index'] = basename(dirname($_SERVER['PHP_SELF']));


      	$response['state']=array();
      	if($GO_SECURITY->logged_in())
      	{
      		$response['user_id']=$GO_SECURITY->user_id;
      		$response['username']=$_SESSION['GO_SESSION']['username'];
      		$response['name']=$_SESSION['GO_SESSION']['name'];
      		$response['email']=$_SESSION['GO_SESSION']['email'];

      		$response['theme']=$_SESSION['GO_SESSION']['theme'];

      		$response['thousands_seperator']=$_SESSION['GO_SESSION']['thousands_seperator'];
      		$response['decimal_seperator']=$_SESSION['GO_SESSION']['decimal_seperator'];
      		$response['date_format']=$_SESSION['GO_SESSION']['date_format'];
      		$response['date_seperator']=$_SESSION['GO_SESSION']['date_seperator'];
      		$response['time_format']=$_SESSION['GO_SESSION']['time_format'];
      		$response['timezone']=$_SESSION['GO_SESSION']['timezone'];
      		$response['start_module']=$_SESSION['GO_SESSION']['start_module'];
      		$response['first_weekday']=$_SESSION['GO_SESSION']['first_weekday'];
      		$response['sort_name']=$_SESSION['GO_SESSION']['sort_name'];
      		$response['max_rows_list']=$_SESSION['GO_SESSION']['max_rows_list'];
      		$response['currency']=$_SESSION['GO_SESSION']['currency'];

      		$response['language']=$GO_LANGUAGE->language;
      		//state for Ext components
      		$response['state'] = $this->get_state($GO_SECURITY->user_id, $response['state_index']);
      	}else
      	{
      		$response['user_id']=0;
      		$response['username']='';
      		$response['name']='';
      		$response['email']='';

      		$response['thousands_seperator']=$this->default_thousands_seperator;
      		$response['decimal_seperator']=$this->default_decimal_seperator;
      		$response['date_format']=$this->default_date_format;
      		$response['date_seperator']=$this->default_date_seperator;
      		$response['time_format']=$this->default_time_format;
      		$response['timezone']=$this->default_timezone;
      		$response['start_module']='';
      		$response['first_weekday']=$this->default_first_weekday;
      		$response['sort_name']='first_name';
      		$response['max_rows_list']=30;
      		$response['language']=$GO_LANGUAGE->language;
      	}
      	$response['modules']=$GO_MODULES->modules;
      	$response['config']['theme_url']=$GO_THEME->theme_url;
      	$response['config']['theme']=$GO_THEME->theme;
      	$response['config']['host']=$this->host;
      	$response['config']['local_url']=$this->local_url;
      	 
      	$response['config']['max_users']=$this->max_users;
      	 

      	return $response;

      }
}
