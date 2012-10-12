<?php
/**
 * Group-Office
 *
 * Copyright Intermesh BV.
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 */

/**
 * Main configuration
 *
 * This class holds the main configuration options of Group-Office
 * Don't modify this file. The values defined here are just default values.
 * They are overwritten by the configuration options in /config.php or
 * /etc/groupoffice/{HOSTNAME}/config.php
 *
 * To edit these options use install.php.
 *
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package GO.base
 */

class GO_Base_Config {
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
	 * Just enable the debug log.
	 * @var bool
	 */
	var $debug_log = false;

	/**
	 * Enable FirePhp
	 *
	 * @var bool
	 * @access  public
	 */
	var $firephp = false;

	/**
	 * Info log location. If empty it will be in <file_storage_path>/log/info.log
	 * @var bool
	 */
	var $info_log = "";

	/**
	 * Output errors in debug mode
	 *
	 * @var     bool
	 * @access  public
	 */
	var $debug_display_errors=true;

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
	 * Default VAT percentage
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_vat = 19;

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
	var $default_currency='â‚¬';

	/**
	 * Default date format
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_date_format='dmY';

	/**
	 * Default date separator
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_date_separator='-';

	/**
	 * Default time format
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_time_format='G:i';

	/**
	 * Default name formatting and sorting. Can be last_name or first_name
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_sort_name = "last_name";


	/**
	 * Default first day of the week 0=sunday 1=monday
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_first_weekday=1;

	/**
	 * Default decimal separator for numbers
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_decimal_separator=',';

	/**
	 * Default thousands separator for numbers
	 *
	 * @var     string
	 * @access  public
	 */
	var $default_thousands_separator='.';

	/**
	 * Default theme
	 *
	 * @var     string
	 * @access  public
	 */
	var $theme = 'Default';

	/**
	 * Default theme
	 *
	 * @var     string
	 * @access  public
	 */
	var $defaultView = 'Extjs3';

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
	 * Enable profile editing by every user through the settings dialog
	 *
	 * @var     bool
	 * @access  public
	 */
	var $allow_profile_edit = true;

	/**
	 * Enable user registration by everyone
	 *
	 * @var     bool
	 * @access  public
	 */
	var $allow_registration = false;



	/**
	 * Allow e-mail address more then once
	 *
	 * @var     bool
	 * @access  public
	 */
	var $allow_duplicate_email = false;
	
	/**
	 * The font used in all HTML editor including the E-mail editor
	 * 
	 * @var string 
	 */
	public $html_editor_font = "font-size:13px; font-family:Arial, Helvetica, sans-serif;";


	/**
	 * Grant read permissions for these modules to new self-registered users.
	 * Module names are separated by a comma.
	 *
	 * @var     string
	 * @access  public
	 */
	var $register_modules_read = '';

	/**
	 * Grant write permissions for these modules to new self-registered users.
	 * Module names are separated by a comma.
	 *
	 * @var     string
	 * @access  public
	 */
	var $register_modules_write = '';

	/**
	 * Comma separated list of allowed modules. Leave empty to allow all modules.
	 *
	 * @var     string
	 * @access  public
	 */
	var $allowed_modules = '';


	/**
	 * Add self-registered users to these user groups
	 * Group names are separated by a comma.
	 *
	 * @var     string
	 * @access  public
	 */
	var $register_user_groups = '';

	/**
	 * Self-registered users will be visible to these user groups
	 * Group names are separated by a comma.
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

//	/**
//	 * Useful to force https://your.host:433 or something like that
//	 *
//	 * @var bool
//	 * @access  public
//	 */
//
//	var $force_login_url = false;

	/**
	 * Full URL to reach Group-Office with slash on end. This value is determined
	 * automatically if not set in config.php
	 *
	 * @var     string
	 * @access  public
	 */
	var $full_url = '';

	/**
	 * Title of Group-Office
	 *
	 * @var     string
	 * @access  public
	 */
	var $title = '';

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
	 * If set, user queries will only return this maximum number of users.
	 * Useful in large environments where you don't want users to scroll through all,
	 * 
	 * @var int 
	 */
	var $limit_usersearch=0;

	/**
	 * The maximum number KB this Group-Office installation may use. 0 will allow unlimited usage of disk space.
	 *
	 * @var     int
	 * @access  public
	 */
	var $quota = 0;


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

	/**
	 * Specifies the port number to attempt to connect to the MySQL server.
	 *
	 * @var     string
	 * @access  public
	 */
	var $db_port = 3306;

	/**
	 * Specifies the socket or named pipe that should be used.
	 *
	 * @var     string
	 * @access  public
	 */
	var $db_socket = '';

	/**
	 *
	 * Useful in clustering mode. Defaults to "1". Set to the number of clustered
	 * nodes.
	 *
	 * @var string
	 * @access public
	 */

	var $db_auto_increment_increment=1;

	/**
	 *
	 * Give each node an incremented number.
	 *
	 * @var string
	 * @access public
	 */

	var $db_auto_increment_offset=1;



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
	 * Convert non ASCII characters to ASCII codes when uploaded to Group-Office.
	 * Useful for Windows servers that don't support UTF8.
	 * 
	 * @var boolean 
	 */
	public $convert_utf8_filenames_to_ascii=false;

	/**
	 * The maximum file size the filebrowser attempts to upload. Be aware that
	 * the php.ini file must be set accordingly (http://www.php.net).
	 *
	 * @var     string
	 * @access  public
	 */
	var $max_file_size = '10000000';
	
	
	/**
	 * Maximum number of old file versions to keep
	 * -1 will disable versioning. 0 will keep an infinite number of versions (Be careful!).
	 * 
	 * @var int 
	 */
	public $max_file_versions = 3;


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
	 * Leave blank or set to tls or ssl
	 *
	 * @var     string
	 * @access  public
	 */
	var $smtp_encryption = '';


	/**
	 * The Swift mailer component auto detects the domain you are connecting from.
	 * In some cases it fails and uses an invalid IPv6 IP like ::1. You can
	 * override it here.
	 *
	 * @var     string
	 * @access  public
	 */
	var $smtp_local_domain = '';
	
	
	/**
	 * A special Swift preference to escape dots. For some buggy SMTP servers this
	 * is necessary.
	 * 
	 * @var boolean 
	 */
	var $swift_qp_dot_escape=false;

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
	 * Command to execute the php command line interface
	 *
	 * @var     string
	 * @access  public
	 */
	var $cmd_php = 'php';


	/**
	 * Length of the password generated when a user uses the lost password option
	 *
	 * @var int
	 */
	var $default_password_length=6;

	/**
	 * Automatically log a user out after n seconds of inactivity
	 *
	 * @var int
	 */

	var $session_inactivity_timeout = 0;

	/**
	 * Callto: link template
	 */

	var $callto_template='callto:{phone}';


	/**
	 * Don't use flash to upload. In some cases it doesn't work like when
	 * using a self-signed certificate.
	 */
	var $disable_flash_upload=false;

	/**
	 * Disable security check for cross domain forgeries
	 *
	 * @var <type>
	 */

	var $disable_security_token_check=false;

	/**
	 * The number of items displayed in the navigation panels (Calendars, addressbooks etc.)
	 * Don't set this number too high because it may slow the browser and server down.
	 *
	 * @var type
	 */

	var $nav_page_size=50;
	
//	/**
//	 * Enable logging of slow requests
//	 * 
//	 * @var boolean 
//	 */
//	public $log_slow_requests=false;
//	
//	/**
//	 * Slow request time in seconds
//	 * 
//	 * @var float 
//	 */
//	public $log_slow_requests_trigger=1;
//	
//	/**
//	 * Path of the log file
//	 * 
//	 * @var string 
//	 */
//	public $log_slow_requests_file="/home/groupoffice/slow-requests.log";

	/*//////////////////////////////////////////////////////////////////////////////
	 //////////      Variables that are not touched by the installer   /////////////
	 //////////////////////////////////////////////////////////////////////////////*/

	/**
	 * The Group-Office version number
	 *
	 * @var     string
	 * @access  public
	 */
	var $version = '4.0.112';


	/* The permissions mode to use when creating files
	 *
	 * @var     string
	 * @access  public
	 */
	var $file_create_mode = '0644';

	/* The permissions mode to use when creating folders
	 *
	 * @var     string
	 * @access  public
	 */
	var $folder_create_mode = '0755';

	/* The permissions mode to use when creating folders
	 *
	 * @var     string
	 * @access  public
	 */
	var $file_change_group = '';

	/**
	 * Modification date
	 *
	 * @var     string
	 * @access  public
	 */
	var $mtime = '20121012';

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
	 * Date separators to be used.
	 *
	 * @var     string
	 * @access  public
	 */

	var $date_separators = array(
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
	 * The link in menu help -> contents
	 *
	 * @var     string
	 * @access  public
	 */
	var $help_link = 'http://wiki4.group-office.com/wiki/';
	
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
	 * Original tmpdir. The user_id is appended (/tmp/1/) to the normal tmpdir.
	 * In some cases you don't want that.
	 *
	 * @var     string
	 * @access  public
	 */
	var $orig_tmpdir = '';

	/**
	 * Database object
	 *
	 * @var     object
	 * @access  private
	 */
	var $db;

	/**
	 * Enable zlib compression for faster downloading of scripts and css
	 *
	 * @var     string
	 * @access  public
	 */
	var $zlib_compress = true;

	
	var $default_max_rows_list = 30;

	var $product_name='Group-Office';

	/**
	 * Full original URL to reach Group-Office with slash on end
	 *
	 * @var     string
	 * @access  public
	 */
	var $orig_full_url = '';

	/**
	 * Constructor. Initialises all public variables.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$config = array();

		$this->root_path = str_replace('\\','/',dirname(dirname(dirname(__FILE__)))).'/';

		//suppress error for open_basedir warnings etc
		if(@file_exists('/etc/groupoffice/globalconfig.inc.php')) {
			require('/etc/groupoffice/globalconfig.inc.php');
		}

		$config_file = $this->get_config_file();

		if($config_file)
			include($config_file);

		foreach($config as $key=>$value) {
			$this->$key=$value;
		}

		if($this->info_log=="")
			$this->info_log =$this->file_storage_path.'log/info.log';

		//this can be used in some cases where you don't want the dynamically
		//determined full URL. This is done in set_full_url below.
		$this->orig_full_url = $this->full_url;

		$this->orig_tmpdir=$this->tmpdir;

		if(empty($this->db_user)) {
		//Detect some default values for installation if root_path is not set yet
			$this->host = dirname($_SERVER['PHP_SELF']);
			if(basename($this->host)=='install')
				$this->host = dirname($this->host);

			//if(substr($this->host,-1) != '/') {
				$this->host .= '/';
			//}

			$this->db_host='localhost';

			if(GO_Base_Util_Common::isWindows()) {
				$this->file_storage_path = substr($this->root_path,0,3).'groupoffice/';
				$this->tmpdir=substr($this->root_path,0,3).'temp';

				$this->cmd_zip=$this->root_path.'controls/win32/zip.exe';
				$this->cmd_unzip=$this->root_path.'controls/win32/unzip.exe';
				$this->cmd_xml2wbxml=$this->root_path.'controls/win32/libwbxml/xml2wbxml.exe';
				$this->cmd_wbxml2xml=$this->root_path.'controls/win32/libwbxml/wbxml2xml.exe';
				
				$this->convert_utf8_filenames_to_ascii=true;
			}

			if(empty($config['tmpdir']) && function_exists('sys_get_temp_dir')) {
				$this->tmpdir = str_replace('\\','/', sys_get_temp_dir());
			}

			$this->default_timezone=@date_default_timezone_get(); //suppress warning if using system tz

			$lc = localeconv();

			$this->default_currency=empty($lc['currency_symbol']) ? 'â‚¬' : $lc['currency_symbol'];
			$this->default_decimal_separator=empty($lc['decimal_point']) ? '.' : $lc['decimal_point'];
			$this->default_thousands_separator=$this->default_decimal_separator == '.' ? ',' : '.';//$lc['thousands_sep'];
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


		if($this->debug)
			$this->debug_log=true;

		if($this->debug_log){// || $this->log_slow_requests) {			

			list ($usec, $sec) = explode(" ", microtime());
			$this->loadstart = ((float) $usec + (float) $sec);
			
//			$dat = getrusage();
//			define('PHP_TUSAGE', microtime(true));
//			define('PHP_RUSAGE', $dat["ru_utime.tv_sec"]*1e6+$dat["ru_utime.tv_usec"]);
		}

		if(is_string($this->file_create_mode)) {
			$this->file_create_mode=octdec($this->file_create_mode);
		}

		if(is_string($this->folder_create_mode)) {
			$this->folder_create_mode=octdec($this->folder_create_mode);
		}

		if($this->debug_log) {
			$this->log=true;
		}

		$this->set_full_url();



	}

	/**
	 * Get the temporary files folder.
	 *
	 * @return GO_Base_Fs_Folder
	 */
	public function getTempFolder(){
		$user_id = GO::user() ? GO::user()->id : 0;
		$folder = new GO_Base_Fs_Folder($this->orig_tmpdir.$user_id);
		$folder->create();
		return $folder;
	}


	function __destruct() {
		if($this->debug_log) {
			//GO::debug('Performed '.$GLOBALS['query_count'].' database queries', $this);

			GO::debug('Page load took: '.(GO_Base_Util_Date::getmicrotime()-$this->loadstart).'ms', $this);
			GO::debug('Peak memory usage:'.round(memory_get_peak_usage()/1048576,2).'MB', $this);
			GO::debug("--------------------\n", $this);
		}
		
		GO::endRequest();
		
//		$this->_logSlowRequest();
	}
	
//	private function _logSlowRequest(){
//		if($this->log_slow_requests){
//			$time = GO_Base_Util_Date::getmicrotime()-$this->loadstart;
//			if($time>$this->log_slow_requests_trigger){
//
//				$logStr = "URI: ";
//
//				if(isset($_SERVER['HTTP_HOST']))
//					$logStr .= $_SERVER['HTTP_HOST'];
//
//				if(isset($_SERVER['REQUEST_URI']))
//					$logStr .= $_SERVER['REQUEST_URI'];
//
//				$logStr .= '; ';
//
//				$logStr .= 'r: '.GO::router()->getControllerRoute().';';
//
//				$logStr .= 'time: '.$time.';'."\n";
//
//
//				file_put_contents($this->log_slow_requests_file, $logStr,FILE_APPEND);			
//			}		
//		}
//	}

	function use_zlib_compression(){

		if(!isset($this->zlib_support_tested)){
			$this->zlib_support_tested=true;
			$this->zlib_compress=$this->zlib_compress && extension_loaded('zlib') && !ini_get('zlib.output_compression');
		}
		return $this->zlib_compress;
	}


	public function getCompleteDateFormat(){
		return $this->default_date_format[0].
						$this->default_date_separator.
						$this->default_date_format[1].
						$this->default_date_separator.
						$this->default_date_format[2];
	}

	/**
	 * Get's the location of a configuration file.
	 * Group-Office searches two locations:
	 *	1. /etc/Group-Office/APACHE SERVER NAME/subdir/to/groupoffice/config.php
	 *	2. /path/to/groupoffice/config.php
	 *
	 * The first location is more secure because the sensitive information is kept
	 * outside the document root.
	 *
	 * @access public
	 * @return string Path to configuration file
	 */

	function get_config_file() {
		if(defined('GO_CONFIG_FILE'))
			return GO_CONFIG_FILE;

		//on start page always search for config
		if(empty($_REQUEST['r'])){
			unset($_SESSION['GO_SESSION']['config_file']);
		}

		if(isset($_SESSION['GO_SESSION']['config_file'])) {
			$this->session_config_file=true;
			return $_SESSION['GO_SESSION']['config_file'];
		}else {
			$config_dir = $this->root_path;
			$config_file = $config_dir.'config.php';
			if(@file_exists($config_file)) {
				$_SESSION['GO_SESSION']['config_file']=$config_file;
				return $config_file;
			}

			$count = 0;

			//use SCRIPT_FILENAME in apache mode because it will use a symlinked
			//directory
			$script = php_sapi_name()=='cli' ? __FILE__ : $_SERVER['SCRIPT_FILENAME'];

			$config_dir = dirname($script).'/';

			/*
			 * z-push also has a config.php. Don't detect that.
			 */
			$pos = strpos($config_dir, 'modules/z-push');
			if($pos){
				$config_dir = substr($config_dir, 0, $pos);
			}

			//openlog('[Group-Office]['.date('Ymd G:i').']', LOG_PERROR, LOG_USER);

			while(!isset($_SESSION['GO_SESSION']['config_file'])){
				$count++;
				$config_file = $config_dir.'config.php';
				//syslog(LOG_NOTICE,$config_file);

				if(@file_exists($config_file)) {
					$_SESSION['GO_SESSION']['config_file']=$config_file;
					return $config_file;
				}
				$config_dir=dirname($config_dir);

				if($count==10 || dirname($config_dir) == $config_dir){
					break;
				}
				$config_dir .= '/';
			}

			/*if(isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['PHP_SELF'])) {
				$config_file = dirname(substr($_SERVER['SCRIPT_FILENAME'], 0 ,-strlen($_SERVER['PHP_SELF']))).'/config.php';
				if(@file_exists($config_file)) {
					$_SESSION['GO_SESSION']['config_file']=$config_file;
					return $config_file;
				}
			}*/
			if(!empty($_SERVER['SERVER_NAME'])){
				$config_file = '/etc/groupoffice/'.$_SERVER['SERVER_NAME'].'/config.php';
				if(@file_exists($config_file)) {
					$_SESSION['GO_SESSION']['config_file']=$config_file;
					return $config_file;
				}
			}
			$config_file = '/etc/groupoffice/config.php';
			if(@file_exists($config_file)) {
				$_SESSION['GO_SESSION']['config_file']=$config_file;
				return $config_file;
			}else
			{
				return false;
			}
		}
	}

	/**
	 * Sets Full URL to reach Group-Office with slash on end
	 *
	 * This function checks wether or not Group-Office runs on a
	 * default http or https port and stores the full url in a variable
	 *
	 * @access public
	 */
	function set_full_url() {
		//full_url may be configured permanent in config.php. If not then
		//autodetect it and put it in the session. It can be used by wordpress for
		//example.

		//this used to use HTTP_HOST but that is a client controlled value which can be manipulated and is unsafe.

		if(isset($_SERVER["SERVER_NAME"])) {
			if(!isset($_SESSION['GO_SESSION']['full_url']) && isset($_SERVER["SERVER_NAME"])) {
				$https = (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == "1")) || !empty($_SERVER["HTTP_X_SSL_REQUEST"]);
				$_SESSION['GO_SESSION']['full_url'] = 'http';
				if ($https) {
					$_SESSION['GO_SESSION']['full_url'] .= "s";
				}
				$_SESSION['GO_SESSION']['full_url'] .= "://".$_SERVER["SERVER_NAME"];
				if ((!$https && $_SERVER["SERVER_PORT"] != "80") || ($https && $_SERVER["SERVER_PORT"] != "443")) 
					$_SESSION['GO_SESSION']['full_url'] .= ":".$_SERVER["SERVER_PORT"];
								
				$_SESSION['GO_SESSION']['full_url'] .= $this->host;
			}
			$this->full_url=$_SESSION['GO_SESSION']['full_url'];
		}else
		{
			$_SESSION['GO_SESSION']['full_url']=$this->full_url;
		}
		if(empty($this->orig_full_url))
			$this->orig_full_url=$this->full_url;
	}


	/**
	 * Gets a custom saved setting from the database
	 *
	 * @param  string $name Configuration key name
	 * @access public
	 * @return string Configuration key value
	 */
	function get_setting($name, $user_id=0) {
		$attributes['name']=$name;
		$attributes['user_id']=$user_id;

		$setting = GO_Base_Model_Setting::model()->findSingleByAttributes($attributes);
		if ($setting) {
			return $setting->value;
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
	function save_setting( $name, $value, $user_id=0) {

		$attributes['name']=$name;
		$attributes['user_id']=$user_id;

		$setting = GO_Base_Model_Setting::model()->findSingleByAttributes($attributes);
		if(!$setting){
			$setting = new GO_Base_Model_Setting();
			$setting->setAttributes($attributes);
		}

		$setting->value=$value;
		return $setting->save();


//		if ( $this->get_setting($name, $user_id) === false ) {
//			return $this->db->query("INSERT INTO go_settings (name, value, user_id) VALUES ('".$this->db->escape($name)."', '".$this->db->escape($value)."', ".intval($user_id).")");
//		} else {
//			return $this->db->query("UPDATE go_settings SET value='".$this->db->escape($value)."' WHERE name='".$this->db->escape($name)."' AND user_id='".$this->db->escape($user_id)."'");
//		}
	}

	/**
	 * Deletes a custom setting from the database
	 *
	 * @param 	string $name Configuration key name
	 * @access public
	 * @return bool Returns true on succes
	 */
	function delete_setting( $name , $user_id=0) {
		$attributes['name']=$name;
		$attributes['user_id']=$user_id;

		$setting = GO_Base_Model_Setting::model()->findSingleByAttributes($attributes);
		return $setting->delete();
	}



	/**
	 * Save the current configuraton to the config.php file.
	 *
	 * @return boolean
	 */
	public function save() {

		$values = get_object_vars(GO::config());
		$config=array();

		require($this->get_config_file());

		foreach($values as $key=>$value)
		{
			if($key == 'version')
			break;

			if(!is_object($value))
			{
				$config[$key]=$value;
			}
		}

		return GO_Base_Util_ConfigEditor::save(new GO_Base_Fs_File(GO::config()->get_config_file()), $config);
	}
}
