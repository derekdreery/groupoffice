<?php

class GO_ServerManager_Model_Installation extends GO_Base_Db_ActiveRecord {

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
	
	public function validate() {
		if(empty($this->dbName))
			$this->setValidationError('name','Name is invalid');
		
		if($this->isNew){
			if(file_exists('/var/lib/mysql/'.$this->dbName) || file_exists('/etc/apache2/sites-enabled/'.$this->name) || is_dir($this->installPath))
				$this->setValidationError ('name', GO::t('duplicateHost','servermanager'));
		}
							
		return parent::validate();
	}

	protected function beforeDelete() {
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
	
	protected function beforeSave() {
		
		$this->calculateStatistics();
		
		return parent::beforeSave();
	}
	
	
	public function calculateStatistics(){
		require($this->configPath);
		$folder = new GO_Base_Fs_Folder($config['file_storage_path']);
		$this->file_storage_usage=$folder->calculateSize();
		
		$this->_calculateDatabaseSize($config['db_name']);
		$this->_calculateMailboxUsage($config);
		
		GO::$disableModelCache=true;
		
		GO::setDbConnection(
						$config['db_name'], 
						$config['db_user'], 
						$config['db_pass'], 
						$config['db_host']
						);
		
		$adminUser = GO_Base_Model_User::model()->findByPk(1);
		$this->admin_email=$adminUser->email;
		
		GO::debug($this->admin_email);
		
		GO::setDbConnection();
		
//		$this->decimal_separator=$config['default_decimal_separator'];
//		$this->thousands_separator=$config['default_thousands_separator'];
//		$this->date_format=Date::get_dateformat($config['default_date_format'], $config['default_date_separator']);

		
		
		//$this->save();
	}
	
	private function _calculateMailboxUsage($config){
		$this->mailbox_usage=0;
		$this->mail_domains=isset($config['serverclient_domains']) ? $config['serverclient_domains'] : '';
		
		if(!empty(GO::config()->serverclient_server_url) && !empty($config['serverclient_domains'])) {
			$c = new GO_Serverclient_HttpClient();
			$response = $c->postfixRequest(array(
					'task'=>'serverclient_get_usage',
					'domains'=>$config['serverclient_domains']
			));
			
			$response = json_decode($response);

			foreach($response->domains as $domain) {
				$this->mailbox_usage+=$domain->usage;
			}
		}
	}
	
	private function _calculateDatabaseSize($dbName){
		$stmt =GO::getDbConnection()->query("SHOW TABLE STATUS FROM `".$dbName."`;");

		$this->database_usage=0;
		while($r=$stmt->fetch()){
			$this->database_usage+=$r['Data_length'];
			$this->database_usage+=$r['Index_length'];
		}
	}
	
	
}
