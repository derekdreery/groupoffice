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
}
