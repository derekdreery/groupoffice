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
 * @package GO.modules.servermanager.model
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering mschering@intermesh.nl
 */
 
/**
 * The GO_ServerManager_Model_Installation model
 * TODO: build in cost that post to billing, build modules and user usage statistics
 *
 * @package GO.modules.servermanager.model
 * @property int $id
 * @property string $name Usually the domain name 
 * @property int $ctime
 * @property int $mtime
 * @property int $max_users
 * @property int $trial_days
 * @property int $lastlogin
 * @property string $comment
 * @property string $features
 * @property string $mail_domains
 * @property string $admin_email
 * @property string $admin_name
 * @property int $status_change_time
 * @property string $configPath
 * @property string $installPath
 * @property string $token
 * 
 * @property string $url
 * 
 * @property GO_ServerManager_Model_AutomaticInvoice automaticInvoice the automatic invoice object if exists
 * @property GO_ServerManager_Model_UsageHistory currentusage the latest created usagehistory object
 */

class GO_ServerManager_Model_Installation extends GO_Base_Db_ActiveRecord {

	private $_config; //the config array of this installation
	
	private $_total_logins;
	private $_count_users;
	private $_modules; //an array of InstallationModule objects
	private $_currentHistory; //GO_ServerManager_Model_UsageHistory object with latest usagedata
	private $_installationUsers; //Saves installation users loaded from external database will be saved in afterSave()
	
	const STATUS_TRIAL ='trial';
	const STATUS_ACTIVE ='ignore';
	/**
	 * Ignore existing database and folder structure when importing.
	 * 
	 * @var boolean 
	 */
	public $ignoreExistingForImport=false;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	protected function init() {
		
		$this->columns['name']['required']=true;
		$this->columns['name']['unique']=true;
		$this->columns['name']['regex']='/^[a-z0-9-_\.]*$/';
		$this->columns['max_users']['required']=true;
		
		$this->columns['lastlogin']['gotype']='unixtimestamp';
		$this->columns['ctime']['gotype']='unixtimestamp';
		
		return parent::init();
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_installations';
	}
	
	public function getDatabaseUsageText()
	{
		if($this->currentusage != null)
			return $this->currentusage->databaseUsageText;
		else
			return '-';
	}
	public function getFileStorageUsageText()
	{
		if($this->currentusage != null)
			return $this->currentusage->getFileStorageUsageText();
		else
			return '-';
	}
	public function getMailboxUsageText()
	{
		if($this->currentusage != null)
			return $this->currentusage->getMailboxUsageText();
		else
			return '-';
	}
	public function getTotalUsageText()
	{
		if($this->currentusage != null)
			return $this->currentusage->getTotalUsageText();
		else
			return '-';
	}
	public function getLastUsageCheckDate()
	{
		if($this->currentusage != null)
			return $this->currentusage->ctime;
		else
			return 'Never';
	}
	public function getTotalLogins()
	{
		if($this->currentusage != null)
			return $this->currentusage->total_logins;
		else
			return '-';
	}
	public function getCountUsers()
	{
		if($this->currentusage != null)
			return $this->currentusage->count_users;
		else
			return '-';
	}
	
	public function relations() {
		return array(
			'histories' => array('type' => self::HAS_MANY, 'model' => 'GO_ServerManager_Model_UsageHistory', 'field' => 'installation_id','delete'=>true),
			'currentusage'=> array('type' => self::HAS_ONE, 'model' => 'GO_ServerManager_Model_UsageHistory', 'field' => 'installation_id', 'findParams'=>array('order'=>'id','orderDirection'=>'DESC','limit'=>1)),
			'users' => array('type'=>self::HAS_MANY, 'model'=>'GO_ServerManager_Model_InstallationUser', 'field'=>'installation_id','delete'=>true, 'findParams'=>array('fields'=>'t.*')),
			'modules' => array('type'=>self::HAS_MANY, 'model'=>'GO_ServerManager_Model_InstallationModule', 'field'=>'installation_id','delete'=>true),
			'automaticInvoice'=>array('type'=>self::HAS_ONE, 'model'=>'GO_ServerManager_Model_AutomaticInvoice', 'field'=>'installation_id','delete'=>true),
		);
	}
	
	/**
	 * Get the DB name for this installation
	 * @return String the Db name based in the installation name
	 */
	protected function getDbName(){
		$name=strtolower(trim($this->name));
		$name=str_replace(array('.','-'),'_',$name);
		return $name;
	}
	
	/**
	 * Get the db username from db name cut of at 16 character if needed
	 * @return String the DB username based in the dbName
	 */
	protected function getDbUser(){
		return substr($this->dbName,0,16);
	}
	
	protected function getInstallPath(){
		if(empty($this->name))
			throw new Exception("Name empty in installation!");
		
		return '/home/govhosts/'.$this->name.'/';
	}
	
	/**
	 * read the list with allows modules from this config file of the installation
	 * and explore is to an array
	 * @return array $allowedModules an array with allowed module keys
	 */
	public function getAllowedModules()
	{
		$allowedModules = array();
		if(!isset($this->config['allowed_modules']))
			$this->_config['allowed_modules']="";

		$allowedModules = explode(',', $this->config['allowed_modules']);
		return $allowedModules;
	}
	
	/**
	 * Returns all available modules that can be activated
	 * Find InstallationModules that are saved in the database and merge them with the rest
	 * @return array(GO_ServerManager_Model_InstallationModule) 
	 */
	public function getModulesList()
	{
		$result = array();
		
		$allModules = GO::modules()->getAvailableModules(true);
		foreach($allModules as $moduleClass)
		{
			$module = new $moduleClass;

			$installationModule = new GO_ServerManager_Model_InstallationModule();
			$installationModule->installation_id = $this->id;
			$installationModule->installation = $this;
			$installationModule->name = $module->id();
			
			if(!$installationModule->isHidden()){
				$result[$installationModule->name] = $installationModule;
			}
		}
		
		$databaseModules = $this->modules->fetchAll();
		foreach($databaseModules as $dbmodule)
		{
			$result[$dbmodule->name] = $dbmodule;
		}
		
		return $result;
	}
	
	/**
	 * Set all database action for Installation modules
	 * All excisting modules should be changed
	 * All none excisting module that are set should be added
	 * 
	 * @param array $modules array of module name strings
	 */
	public function setModules($modules)
	{
		if(!isset($this->_modules)) //load modules from database if not done yet
		{
			$this->_modules = array();
			$stmt = $this->modules;
			while($module = $stmt->fetch())
				$this->_modules[$module->name] = $module;
		}
		
		//set posted modules to true
		foreach($this->_modules as &$module)
		{
			if(in_array($module->name, $modules))
				$module->enabled = true;
			else
				$module->enabled = false;
		}
		
		//add all new modules that are not yet in db
		foreach($modules as $modulename)
		{
			if(!isset($this->_modules[$modulename]))
			{
				$module = new GO_ServerManager_Model_InstallationModule();
				$module->enabled = true;
				$module->name = $modulename;
				$this->_modules[$modulename] = $module;
			}
		}
	}
	
	/**
	 * Get the path to the config file of an installation
	 * 
	 * @return string path to config file
	 */
	protected function getConfigPath(){		
		return '/etc/groupoffice/'.$this->name.'/config.php';
	}
	
	/**
	 * The url of the installation
	 * @return string URL
	 */
	protected function getUrl(){
		$protocol = empty(GO::config()->servermanager_ssl) ? 'http' : 'https';
		return $protocol.'://'.$this->name;
	}
	
	/**
	 * Get the content of config file of this installation
	 * If file not exisits return false;
	 * @return mixed $config array in config.php or false if not exists 
	 */
	public function getConfig(){
		if($this->_config!==null)
			return $this->_config; 
		else
		{
			if(!file_exists($this->configPath)){
				return false;
			} else {
				$config=array();
				require($this->configPath);
				$this->_config = $config;
				return $this->_config;
			}
		}
	}
	
	public function validate() {
		if(empty($this->dbName))
			$this->setValidationError('name','Name is invalid');
		
		if($this->isNew && !$this->ignoreExistingForImport){
			if(file_exists('/var/lib/mysql/'.$this->dbName) || file_exists('/etc/apache2/sites-enabled/'.$this->name) || is_dir($this->installPath))
				$this->setValidationError ('name', GO::t('duplicateHost','servermanager'));
		}
		
		if (!$this->isNew && empty($this->modules)) {
			$this->setValidationError ('modules',"Please select the allowed modules");
		}
							
		return parent::validate();
	}
	
	public function defaultAttributes() {		
		$attr = parent::defaultAttributes();
		
		$attr['max_users'] = isset(GO::config()->servermanager_max_users) ? GO::config()->servermanager_max_users : 3;
		
		return $attr;
	}

	/**
	 * Before the installation gets deleted from the database we'll do some cleanup
	 * cleanup is done on the commandprompt with root access.
	 * It will delete the database and remove the symlinks to the installation
	 * @return boolean true
	 * @throws Exception if the executed command fails we'll throw an exeption
	 */
	protected function beforeDelete() {
		
		if(file_exists($this->configPath)){
			//throw new Exception("Error: Could not find installation configuration.");
		
			$cmd = 'sudo TERM=dumb '.GO::config()->root_path.
							'groupofficecli.php -r=servermanager/installation/destroy'.
							' -c='.GO::config()->get_config_file().
							' --id='.$this->id;

			exec($cmd, $output, $return_var);	

			if($return_var!=0){
				throw new Exception(implode("\n", $output));
			}
		}
		
		return parent::beforeDelete();
	}
	
	
	protected function afterDbInsert() {
		if(class_exists("GO_Professional_LicenseCheck"))
		{
			$lc = new GO_Professional_LicenseCheck();
			$this->token = $lc->generateToken($this);
			
			return true;
		}
	}
	
	/**
	 * check if the installation is expired
	 * @return boolean true of installation is more then 30 old and in trial status. 
	 */
	public function getIsExpired()
	{
		return ($this->status == GO_ServerManager_Model_Installation::STATUS_TRIAL && 
						$this->ctime<GO_Base_Util_Date::date_add(time(),-30));

	}
	
	/**
	 * check is installation is suspended
	 * @return boolean true if the installation enabled value in config is set tot false
	 */
	public function getIsSuspended()
	{
		return !$this->config['enabled'];
	}
	
	/**
	 * will write enabled=false to the config file
	 * @return boolean true is installation was suspended
	 */
	public function suspend()
	{
		if(!$this->isSuspended) //if not already suspended
		{
			$this->_config['enabled']=false;
			//saves config in before/afterSave()
			return true;
		} 
		else
			return false;
	}
	
	/**
	 * Will create and save a new UsageHistory object and load current usage data 
	 * This should be called once a day by actionReport()
	 */
	public function loadUsageData()
	{
		if($this->isNew)
			throw new Exception('Can not load usage data for a new installation');
		if(!$this->config)
			return false;
		
		//if(isset($this->config['max_users']))
			//$this->max_users=$this->config['max_users'];
		
		$history = new GO_ServerManager_Model_UsageHistory();
		$history->installation_id = $this->id;
		//recalculated the size of the file folder
		$folder = new GO_Base_Fs_Folder($this->config['file_storage_path']);
		$history->file_storage_usage = $folder->calculateSize();
		//Recalculate the size of the database and mailbox
		$history->database_usage = $this->_calculateDatabaseSize();
		$history->mailbox_usage = $this->_calculateMailboxUsage();
		
		$this->_loadFromInstallationDatabase();
		
		$history->count_users = $this->_count_users;
		$history->total_logins = $this->_total_logins;
		
		$this->_currentHistory = $history;

		return true;
	}
	
	public function getHistoryAttributes()
	{
		if(!isset($this->_currentHistory))
			throw new Exception('no new usage data loaded');
		return $this->_currentHistory->getAttributes();
	}
	
	private function _loadModuleData()
	{
		// conect to installation database
		// reconnect to servermanager database
		// set data from db

		//load modules from installation database with ctime
		$modules = GO_Base_Model_Module::model()->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl());
		foreach($modules as $module)
		{
			if(empty($this->first_installation_time))
				$this->first_installation_time = $module->ctime;
		}
	}
	
	/**
	 * Returns an array with latest usage data
	 * @return array usage data
	 */
	public function report(){
		
		$report = $this->getAttributes();
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('module_id, count(*) AS usercount')
						->joinModel(array('model'=>'GO_ServerManager_Model_InstallationUser',  'localField'=>'user_id','tableAlias'=>'u'))
						->group(array('module_id'))
						->criteria(
										GO_Base_Db_FindCriteria::newInstance()
										->addCondition('installation_id', $this->id,'=','u')										
										);
		
		$stmt = GO_ServerManager_Model_InstallationUserModule::model()->find($findParams);
		
		$report['modules']=$stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $report;
	}
	
	/**
	 * Load data from the installations database
	 * this will load the users used and the modules they have access to
	 * this will load the installed module with there ctime
	 * this will load last login, total users and total logins
	 * @return $installationUsers Array(GO_ServerManager_Model_InstallationUser)
	 * @throws Exception 
	 */
	private function _loadFromInstallationDatabase()
	{
		if($this->isNew)
			throw new Exception('Can not load userdata for a new installation');
		
		//dummy object for SHOW COLUMN from servermanager database
		$installationUser = new GO_ServerManager_Model_InstallationUser();
		
		//prevent model caching and switch to installation database.
		GO::$disableModelCache=true;
		try{
			GO::setDbConnection(
					$this->config['db_name'], 
					$this->config['db_user'], 
					$this->config['db_pass'], 
					$this->config['db_host']
				);

			$adminUser = GO_Base_Model_User::model()->findByPk(1); //find admin user
			$this->admin_email=$adminUser->email;
			$this->admin_name=$adminUser->name;

			$findParams = GO_Base_Db_FindParams::newInstance()
							->select('count(*) as count, max(lastlogin) AS lastlogin, sum(logins) as total_logins');
			$record = GO_Base_Model_User::model()->findSingle($findParams);	//find lastlogin, usercount and total login					
			$this->lastlogin = intval($record->lastlogin);
			$this->_count_users = intval($record->count);		
			$this->_total_logins = intval($record->total_logins);
			
			$allowedModules = empty($this->config['allowed_modules']) ? array() : explode(',', $this->config['allowed_modules']);
			$this->_installationUsers=array();
			$stmt = GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl());
			
			while($user = $stmt->fetch()){
				$installationUser = new GO_ServerManager_Model_InstallationUser();
				$installationUser->installation_id=$this->id;
				$installationUser->setAttributesFromUser($user);
				
				$oldIgnore = GO::setIgnoreAclPermissions(false);
				
				$modStmt = GO_Base_Model_Module::model()->find(GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::READ_PERMISSION, $user->id));
				while($module = $modStmt->fetch()){			
					if(empty($allowedModules) || in_array($module->id, $allowedModules))
						$installationUser->addModule($module->id);				
				}
				$modStmt=null;
				
				GO::setIgnoreAclPermissions($oldIgnore);

				$this->_installationUsers[]=$installationUser;
			}
			//unset stmt to clean up connections
			$stmt=null;
			//GO::config()->save_setting('mailbox_usage', $this->mailbox_usage);
			//GO::config()->save_setting('file_storage_usage', $this->file_storage_usage);
			//GO::config()->save_setting('database_usage', $this->database_usage);
		}catch(Exception $e){
			GO::setDbConnection();
			$stmt=null;
			$modStmt=null;
			if(isset($oldIgnore))
				GO::setIgnoreAclPermissions($oldIgnore);
			throw new Exception($e->getMessage());
		}
		
		//reconnect to servermanager database
		GO::setDbConnection();

	}
	
	/**
	 * calculate the size of the mailboxes if they are used.
	 * @return double the mailbox size in bytes?
	 */
	private function _calculateMailboxUsage(){
		$mailbox_usage=0;
		$this->mail_domains=isset($this->config['serverclient_domains']) ? $this->config['serverclient_domains'] : '';
		
		if(!empty(GO::config()->serverclient_server_url) && !empty($this->config['serverclient_domains'])) {
			$c = new GO_Serverclient_HttpClient();
			$c->postfixLogin();
			
			$response = $c->request(
					GO::config()->serverclient_server_url."?r=postfixadmin/domain/getUsage", 
					array('domains'=>json_encode(explode(",",$this->config['serverclient_domains'])))
			);
			
			$result = json_decode($response);
			$mailbox_usage=$result->usage;			
		}
		return $mailbox_usage;
	}

	/**
	 * Calculate the database size of the database name in config file of installation
	 * @return double Database size in bytes
	 */
	private function _calculateDatabaseSize(){
		$stmt =GO::getDbConnection()->query("SHOW TABLE STATUS FROM `".$this->config["db_name"]."`;");

		$database_usage=0;
		while($r=$stmt->fetch()){
			$database_usage+=$r['Data_length'];
			$database_usage+=$r['Index_length'];
		}
		
		return $database_usage;
	}
	
	
	private function _sendTrialtimeMails()
	{
		$module_stmt = $this->modules;
		foreach($module_stmt as $module)
		{
			if ($module->trialDaysLeft == 30 || $module->trialDaysLeft == 7)
				$module->sendTrialTimeLeftMail();
		}

		foreach($this->getTrialUsers() as $user)
		{
			if ($user->trialDaysLeft == 30 || $user->trialDaysLeft == 7)
				$user->sendTrialTimeLeftMail();
		}
	}
	
	
	/**
	 * Find all automatic email that should be send and send the ones that should be send today
	 * This function should be called once a day by a cronjob for every installation
	 * @param int $nowUnixTime time()?
	 * @return boolean $success true if all mails successfull send
	 */
	public function sendAutomaticEmails($nowUnixTime=false) {
		
		$this->_sendTrialtimeMails();
		
		if (!is_int($nowUnixTime))
			$nowUnixTime = time();
		
		$autoEmailsStmt = GO_ServerManager_Model_AutomaticEmail::model()
			->find(
				GO_Base_Db_FindParams::newInstance()
					->select('t.*')
					->criteria(
						GO_Base_Db_FindCriteria::newInstance()
							->addCondition('active','1')
					)
			);
		
		$success = true;
		
		while ($autoEmailModel = $autoEmailsStmt->fetch()) {
			
			//Send the mail only if the creation time of the installation + the number of days is today.
			$dayStart = GO_Base_Util_Date::date_add($nowUnixTime,-$autoEmailModel->days);
			$dayStart = GO_Base_Util_Date::clear_time($dayStart);
			$dayEnd = GO_Base_Util_Date::date_add($dayStart,1);			
			
//			echo $autoEmailModel->name.' '.date('c', $dayStart).' - '.date('c', $dayEnd)."\n";
			
//			echo "Installation time: ".date('c', $installationModel->ctime)."\n";
			
			if (!empty($autoEmailModel->active) && $this->ctime>=$dayStart && $this->ctime<$dayEnd) {
				
				echo "Sending message ".$autoEmailModel->name." to ".$this->admin_email."\n";
				
				$message = GO_Base_Mail_Message::newInstance()
					->loadMimeMessage($autoEmailModel->mime)
					->addTo($this->admin_email, $this->admin_name)
					->setFrom(GO::config()->webmaster_email, 'Servermanager Administrator');

				$body = $this->_parseTags(
					$message->getBody(),
					array('installation'=>$this,'automaticemail'=>$autoEmailModel)
				);
				
				$message->setBody($body);

				$success = $success && GO_Base_Mail_Mailer::newGoInstance()->send($message);
			}
		}
		return $success;
	}
	
	public function getTrialUsers()
	{
		$trialUsers = array();
		$stmt = $this->users;
		foreach($stmt->fetchAll() as $user)
		{
			if($user->isTrial())
				$trialUsers[] = $user;
		}
		return $trialUsers;
	}
	public function getPayedUsers()
	{
		$payedUsers = array();
		$stmt = $this->users;
		foreach($stmt->fetchAll() as $user)
		{
			if(!$user->isTrial())
				$payedUsers[] = $user;
		}
		return $payedUsers;
	}
	
	/**
	 * Returns the amount that should be payed for the user account 
	 */
	public function getUserPrice()
	{
		$userprices = GO_ServerManager_Model_UserPrice::findAll();
		$highest_count = 0;
		$price = 0;
		foreach($userprices as $userprice)
		{
			if($userprice->max_users <= $this->getPayedUsers() && $userprice->max_users > $highest_count)
			{
				$highest_count = $userprice->max_users;
				$price = $userprice->price_per_month;
			}
		}
		return $price;
	}
	
	/**
	 * Save the config file of in the installation if it has been modified 
	 * Save module information is it has been set
	 * Save history object if it has been build
	 * @return boolean true if all got saved
	 */
	protected function afterSave($wasnew)
	{
		$success= true;
		
		//NOTE: write the config is done in afterSubmit() calling an controller action as root
		
		//save module information
		if(is_array($this->_modules))
		{
			foreach($this->_modules as $module)
			{
				$module->installation_id = $this->id;
				$success = $success && $module->save();
			}
		}
		
		if(!$wasnew)
		{
			//save new user data of an installation
			if(is_array($this->_installationUsers))
			{
				//Drop all installation user for this installation and insert the new ones base on loaded data
				GO_ServerManager_Model_InstallationUser::model()->deleteByAttribute('installation_id', $this->id);
				foreach($this->_installationUsers as $user){
					$user->installation_id = $this->id;
					$success = $success && $user->save();
				}
			}
			
			//save latest usage history if exists
			if($this->_currentHistory != null)
				$success=$success && $this->_currentHistory->save();
		}
		
		//save automatic invoicing setting
		if(isset($this->_autoInvoice))
		{
			$this->_autoInvoice->installation_id = $this->id;
			$success=$success && $this->_autoInvoice->save();
		}
		
		return $success;
	}
	
	private $_autoInvoice;
	public function setAutoInvoice(GO_ServerManager_Model_AutomaticInvoice $value)
	{
		$this->_autoInvoice = $value;
	}
	
}