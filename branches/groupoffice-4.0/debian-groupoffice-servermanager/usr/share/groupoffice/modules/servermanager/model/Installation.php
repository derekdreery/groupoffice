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
 * @author WilmarVB wilmar@intermesh.nl
 */
 
/**
 * The GO_ServerManager_Model_Installation model
 *
 * @package GO.modules.servermanager.model
 * @property int $id
 * @property int $mtime
 * @property int $max_users
 * @property int $count_users
 * @property int $install_time
 * @property int $lastlogin
 * @property int $total_logins
 * @property int $database_usage
 * @property int $file_storage_usage
 * @property int $mailbox_usage
 * @property int $report_ctime
 * @property string $comment
 * @property string $features
 * @property string $mail_domains
 * @property string $admin_email
 * @property string $admin_name
 * @property string $admin_salutation
 * @property string $admin_country
 * @property string $date_format
 * @property string $thousands_separator
 * @property string $decimal_separator
 * @property boolean $billing
 * @property boolean $professional
 * @property int $status_change_time
 * @property string $configPath
 * @property string $installPath
 * 
 * @property string $url
 */

class GO_ServerManager_Model_Installation extends GO_Base_Db_ActiveRecord {

	/**
	 * Ignore existing database and folder structure when importing.
	 * 
	 * @var boolean 
	 */
	public $ignoreExistingForImport=false;
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_ServerManager_Model_Installation
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_installations';
	}
	
	protected function init() {
		
		$this->columns['name']['required']=true;
		$this->columns['name']['unique']=true;
		$this->columns['name']['regex']='/^[a-z0-9-_\.]*$/';
		$this->columns['max_users']['required']=true;
		
		$this->columns['lastlogin']['gotype']='unixtimestamp';
		$this->columns['install_time']['gotype']='unixtimestamp';
		
		return parent::init();
	}
	
	protected function getDbName(){
		$name=strtolower(trim($this->name));
		$name=str_replace(array('.','-'),'_',$name);
		return $name;
	}
	
	protected function getDbUser(){
		return substr($this->dbName,0,16);
	}
	
	protected function getInstallPath(){
		if(empty($this->name))
			throw new Exception("Name empty in installation!");
		
		return '/home/govhosts/'.$this->name.'/';
	}
	
	protected function getConfigPath(){
		return '/etc/groupoffice/'.$this->name.'/config.php';
	}
	
	protected function getUrl(){
		$protocol = empty(GO::config()->servermanager_ssl) ? 'http' : 'https';
		return $protocol.'://'.$this->name;
	}
	
	public function validate() {
		if(empty($this->dbName))
			$this->setValidationError('name','Name is invalid');
		
		if($this->isNew && !$this->ignoreExistingForImport){
			if(file_exists('/var/lib/mysql/'.$this->dbName) || file_exists('/etc/apache2/sites-enabled/'.$this->name) || is_dir($this->installPath))
				$this->setValidationError ('name', GO::t('duplicateHost','servermanager'));
		}
							
		return parent::validate();
	}
	
	public function defaultAttributes() {		
		$attr = parent::defaultAttributes();
		
		$attr['max_users'] = isset(GO::config()->servermanager_max_users) ? GO::config()->servermanager_max_users : 3;
		
		return $attr;
	}

	protected function beforeDelete() {
		
		if(!file_exists($this->configPath))
			throw new Exception("Error: Could not find installation configuration.");
		
		$cmd = 'sudo TERM=dumb '.GO::config()->root_path.
						'groupofficecli.php -r=servermanager/installation/destroy'.
						' -c='.GO::config()->get_config_file().
						' --name='.$this->name;
		
//		GO::debug($cmd);
//		throw new Exception($cmd);
						
		exec($cmd, $output, $return_var);		

		if($return_var!=0){
			throw new Exception(implode("\n", $output));
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
	
	public function report(){
		if(!file_exists($this->configPath))
			return false;
		
		require($this->configPath);
		
		if(isset($config['max_users']))
			$this->max_users=$config['max_users'];
		
		$folder = new GO_Base_Fs_Folder($config['file_storage_path']);
		$this->file_storage_usage=$folder->calculateSize()/1024;
		
		$this->_calculateDatabaseSize($config['db_name']);
		$this->_calculateMailboxUsage($config);
		$this->_calculateInstallationUsage($config);
		
		//$this->save();
		
		$report = $this->getAttributes();
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('module_id, count(*) AS usercount')
						->joinModel(array('model'=>'GO_ServerManager_Model_InstallationUser',  'localField'=>'user_id','tableAlias'=>'u'))
						->group(array('module_id'))
						->debugSql()
						->criteria(
										GO_Base_Db_FindCriteria::newInstance()
										->addCondition('installation_id', $this->id,'=','u')										
										);
		
		$stmt = GO_ServerManager_Model_InstallationUserModule::model()->find($findParams);
		
		$report['modules']=$stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $report;
	}
	
	private function _calculateInstallationUsage($config){
		//prevent model caching and switch to installation database.
		GO::$disableModelCache=true;		
		GO::setDbConnection(
						$config['db_name'], 
						$config['db_user'], 
						$config['db_pass'], 
						$config['db_host']
						);
		
		$adminUser = GO_Base_Model_User::model()->findByPk(1);
		$this->admin_email=$adminUser->email;
		$this->admin_name=$adminUser->name;
		$this->install_time = $adminUser->ctime;
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('count(*) as count, max(lastlogin) AS lastlogin');
		$record = GO_Base_Model_User::model()->findSingle($findParams);						
		
		$this->lastlogin = intval($record['lastlogin']);
		$this->count_users = intval($record['count']);		
		
		$allowedModules = empty($config['allowed_modules']) ? array() : explode(',', $config['allowed_modules']);
		
		$stmt = GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl());
		$iUsers=array();
		while($user = $stmt->fetch()){
			$iUser = $user->getAttributes('raw');
			$iUser['modules']=array();
			
			$modStmt = GO_Base_Model_Module::model()->find(GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::READ_PERMISSION, $user->id));
			while($module = $modStmt->fetch()){			
				if(empty($allowedModules) || in_array($module->id, $allowedModules))
					$iUser['modules'][]=$module->id;				
			}
			
			$iUsers[]=$iUser;
		}
		GO::config()->save_setting('mailbox_usage', $this->mailbox_usage);
		GO::config()->save_setting('file_storage_usage', $this->file_storage_usage);
		GO::config()->save_setting('database_usage', $this->database_usage);
		
		//var_dump($iUsers);
		
		//reconnect to servermanager database
		GO::setDbConnection();
		
		$this->save();
		
		GO_ServerManager_Model_InstallationUser::model()->deleteByAttribute('installation_id', $this->id);
		
		while($attributes = array_shift($iUsers)){
			$iUser = new GO_ServerManager_Model_InstallationUser();
			
			$modules = $attributes['modules'];
			unset($attributes['id'],$attributes['modules']);
			
			$iUser->setAttributes($attributes, false);
			$iUser->installation_id=$this->id;
			$iUser->save();
			while($module = array_shift($modules)){
				$iModule = new GO_ServerManager_Model_InstallationUserModule();
				$iModule->user_id=$iUser->id;
				$iModule->module_id=$module;
				$iModule->save();
			}
		}
		
		
	}
	
	private function _calculateMailboxUsage($config){
		$this->mailbox_usage=0;
		$this->mail_domains=isset($config['serverclient_domains']) ? $config['serverclient_domains'] : '';
		
		if(!empty(GO::config()->serverclient_server_url) && !empty($config['serverclient_domains'])) {
			$c = new GO_Serverclient_HttpClient();
			$c->postfixLogin();
			
			$response = $c->request(
					GO::config()->serverclient_server_url."?r=postfixadmin/domain/getUsage", 
					array('domains'=>json_encode(explode(",",$config['serverclient_domains'])))
			);
			
			$result = json_decode($response);
			$this->mailbox_usage=$result->usage;			
		}
		
	}
	
	private function _calculateDatabaseSize($dbName){
		$stmt =GO::getDbConnection()->query("SHOW TABLE STATUS FROM `".$dbName."`;");

		$this->database_usage=0;
		while($r=$stmt->fetch()){
			$this->database_usage+=$r['Data_length'];
			$this->database_usage+=$r['Index_length'];
		}
		$this->database_usage/=1024;
	}
	
	
}
