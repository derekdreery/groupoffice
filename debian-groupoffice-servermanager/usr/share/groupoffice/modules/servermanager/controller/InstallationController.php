<?php

class GO_Servermanager_Controller_Installation extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Servermanager_Model_Installation';
	
	protected function allowGuests() {
		return array('create','destroy', 'report','upgradeall','rename','fixquota');
	}
	
	protected function ignoreAclPermissions() {
		return array('create','destroy', 'report');
	}
	
	/**
	 * Loop through all config directories in /etc/groupoffice
	 * If it is not found in the database create a new one and call report() function
	 * @param array $params $_REQUEST object
	 */
	protected function actionImport($params){
		$folder = new GO_Base_Fs_Folder('/etc/groupoffice');
		$items = $folder->ls();
		
		foreach($items as $item){
			if($item->isFolder() && $item->child('config.php')){
				
				if(is_dir('/home/govhosts/'.$item->name())){
					$installation = GO_ServerManager_Model_Installation::model()->findSingleByAttribute('name', $item->name());
					if(!$installation){
						echo "Importing ".$item->name()."\n";
						$installation = new GO_ServerManager_Model_Installation();
						$installation->ignoreExistingForImport=true;
						$installation->name=$item->name();
						$installation->save();
						$installation->loadUsageData();			

					}
				}
			}
		}
		echo "Done\n\n";
	}
	
	
	public function actionUndestroy($params){
		
		$this->checkRequiredParameters(array('name'), $params);
		
		$trashFolderGovhosts = new GO_Base_Fs_Folder('/home/gotrash/govhosts/'.$params['name']);
		if(!$trashFolderGovhosts->exists())
			throw new Exception($trashFolderGovhosts->path().' does not exist');
		
		$trashFolderConfig = new GO_Base_Fs_Folder('/home/gotrash/etc/groupoffice/'.$params['name']);
		if(!$trashFolderConfig->exists())
			throw new Exception($trashFolderConfig->path().' does not exist');
		
		echo "Retoring files...\n";
		$trashFolderGovhosts->move(new GO_Base_Fs_Folder('/home/govhosts'));	
		$trashFolderConfig->move(new GO_Base_Fs_Folder('/etc/groupoffice'));
		
		exec('chown www-data:www-data -R '.$trashFolderGovhosts->path());
		
		

		require_once('/etc/groupoffice/'.$params['name'].'/config.php');
		
		
		GO::getDbConnection()->query("CREATE DATABASE IF NOT EXISTS `".$config['db_name']."`");	
		
		$this->_createDbUser($config);
		
		echo "Retoring database...\n";
		$cmd = 'mysql --user='.$config['db_user'].' --password='.$config['db_pass'].' '.$config['db_name'].' < /home/gotrash/mysqldump/'.$config['db_name'].'.sql';
		system($cmd);
		
		echo "Creating installation in servermanager...\n";
		$installation = new GO_ServerManager_Model_Installation();
		$installation->ignoreExistingForImport=true;
		$installation->name=$params['name'];
		$installation->save();
		$installation->loadUsageData();					
		
		echo "Restore done!\n";
		
	}
	
	/**
	 * Command line action that will delete the database and remove symlinks
	 * This will be executed by Installation->beforeDelete()
	 * @param array $params the $_REQUEST[]
	 * @throws Exception 
	 */
	public function actionDestroy($params){
		if(!$this->isCli())
			throw new Exception("Action servermanager/installation/delete may only be run by root on the command line");
		
		$installation = GO_ServerManager_Model_Installation::model()->findByPk($params['id']);
		
		if($installation->name=='servermanager'){
			throw new Exception("You can't delete the servermanager installation");
		}
		
		if(!$installation)
			throw new Exception("Installation ".$params['name']." not found!");
		
		if(!$installation->validate())
			throw new Exception("Installation ".$params['name']." is invalid");
		
		$trashFolderGovhosts = new GO_Base_Fs_Folder('/home/gotrash/govhosts');
		$trashFolderGovhosts->create();
		
		$trashFolderConfig = new GO_Base_Fs_Folder('/home/gotrash/etc/groupoffice');
		$trashFolderConfig->create();
		
		$trashFolderMysql = new GO_Base_Fs_Folder('/home/gotrash/mysqldump');
		$trashFolderMysql->create();
		
		try{
			$installation->mysqldump('/home/gotrash/mysqldump');
		}catch(Exception $e){
			trigger_error("Failed to backup MySQL. Skipped drop of database ".$installation->dbName,E_USER_WARNING);
		}
		
		
		try{
			GO::getDbConnection()->query("DROP USER '".$installation->dbUser."'@'".GO::config()->db_host."'");		
		}catch(Exception $e){
			trigger_error("Could not remove mysql user ".$installation->dbUser,E_USER_WARNING);
		}
		try{
			GO::getDbConnection()->query("DROP DATABASE `".$installation->dbName."`");
		}catch(Exception $e){
			trigger_error("Could not remove mysql database ".$installation->dbName,E_USER_WARNING);
		}		
		
		$installationFolder = new GO_Base_Fs_Folder($installation->installPath);
		$installationFolder->move($trashFolderGovhosts);
		
		$configFolder = new GO_Base_Fs_Folder('/etc/groupoffice/'.$installation->name);
		$configFolder->move($trashFolderConfig);
		
	}	
	
	private function _getConfigFromFile($path){
		require($path);
		return $config;
	}
	
	
	public function actionRename($params){
	
		if(!$this->isCli())
			throw new Exception("Action servermanager/installation/create may only be run by root on the command line");
		
		$this->checkRequiredParameters(array("oldname","newname"), $params);		
		
		$installation = GO_ServerManager_Model_Installation::model()->findSingleByAttribute('name', $params['oldname']);
		
		if(!$installation)
			throw new GO_Base_Exception_NotFound();
		
		$configFolder = new GO_Base_Fs_Folder(dirname($installation->configPath));
		$installationFolder = new GO_Base_Fs_Folder($installation->installPath);
		
		$oldDbName = $installation->dbName;
		$oldDbUser = $installation->dbUser;
		
		require($installation->configPath);
		
		
		$installation->name = $params["newname"];
		$installation->save();		
		
		$newInstallPath = $installationFolder->parent()->path()."/".$installation->name."/";
		
		$config['id']=$installation->name;	
		$config['file_storage_path']=$newInstallPath."data/";
		$config['root_path']=$newInstallPath."groupoffice/";
		system('mv "'.$configFolder->path().'" "'.$configFolder->parent()->path()."/".$installation->name.'"');
		
		//$configFolder->move(new GO_Base_Fs_Folder("/etc/groupoffice"), $installation->name);
		
		GO_Base_Util_ConfigEditor::save(new GO_Base_Fs_File($installation->configPath), $config);
		
		//$installationFolder->move(new GO_Base_Fs_Folder("/home/govhosts"), $installation->name);
		
		system('mv "'.$installationFolder->path().'" "'.$newInstallPath.'"');
		
		echo "Installation ".$params['oldname']." was renamed to ".$params['newname']."\n";
	}
	
	/**
	 * Create database, dbuser, symlinks and configfile
	 * Only run this action as root on the commandline
	 * 
	 * @param string $params[name] name is installation to create
	 * @param string $params[tmp_config] path to temp config file
	 * @param string $params[adminpassword] ??
	 * @throws Exception if not called from CLI
	 * @throws Exception if installation is not found
	 */
	public function actionCreate($params){
		if(!$this->isCli())
			throw new Exception("Action servermanager/installation/create may only be run by root on the command line");
		
		//todo check if we are root
		
		$installation = GO_ServerManager_Model_Installation::model()->findSingleByAttribute('name', $params['name']);
		
		if(!$installation)
			throw new Exception("Installation ".$params['name']." not found!");
		
		$configFile = new GO_Base_Fs_File($installation->configPath);
		
		//if config file already exists then include it so we will keep the manually added config values.
		if($configFile->exists())
			$existingConfig = $installation->config;
		else
			$existingConfig = array();
		
		//create config file
		require($params['tmp_config']);
		$newConfig = $config;
		unlink($params['tmp_config']);
		$existingConfig=array_merge($existingConfig, $newConfig);		

		$this->_createFolderStructure($existingConfig, $installation);
		
		GO_Base_Util_ConfigEditor::save($configFile, $existingConfig);
		$configFile->chown('root');
		$configFile->chgrp('www-data');
		$configFile->chmod(0640);		
		
		$this->_createDatabase($params,$installation, $existingConfig);		
		
	}
	
	private function _createDatabaseContent($params, $installation, $config){
		$cmd = 'sudo -u www-data php '.GO::config()->root_path.'install/autoinstall.php'.
						' -c='.$installation->configPath.
						' --adminusername=admin'.
						' --adminpassword="'.$params['adminpassword'].'"'.
						' --adminemail="'.$config['webmaster_email'].'" 2>&1';
		
		GO::debug($cmd);
		
		exec($cmd, $output, $return_var);

		if($return_var!=0)
			throw new Exception(implode("\n", $output));
	}
	
	private function _createFolderStructure($config, $installation){
		
		$dataFolder = new GO_Base_Fs_Folder($installation->installPath.'data');
		$dataFolder->create(0755);
		$dataFolder->chown('www-data');
		$dataFolder->chgrp('www-data');
		
		$log = new GO_Base_Fs_Folder($installation->installPath.'data/log');
		$log->create(0755);
		$log->chown('www-data');
		$log->chgrp('www-data');
				
		$tmpFolder = new GO_Base_Fs_Folder('/tmp/'.$installation->name);
		$tmpFolder->create(0777);
		
		$configFolder = new GO_Base_Fs_Folder('/etc/groupoffice/'.$installation->name);
		$configFolder->create(0755);
		
		if(!file_exists($installation->installPath.'groupoffice'))
			symlink(GO::config()->root_path, $installation->installPath.'groupoffice');
	}
	
	private function _createDatabase($params, $installation, $config){
		
		try{			
			if(!GO_Base_Db_Utils::databaseExists($config['db_name'])){
			
				GO::getDbConnection()->query("CREATE DATABASE IF NOT EXISTS `".$config['db_name']."`");				
				
				$this->_createDbUser($config);

				$this->_createDatabaseContent($params, $installation, $config);
			}else
			{
				if(!empty($params['adminpassword'])){
					GO::setDbConnection($config["db_name"], $config["db_user"], $config["db_pass"]);					
					
					$admin = GO_Base_Model_User::model()->findByPk(1, false,true,true);
					$admin->password=$params['adminpassword'];
					$admin->save();	
					
					GO::setDbConnection();
				}
			}
		}catch(Exception $e){
			
			//$installation->delete();
			
			throw new Exception("Could not create database. Did you grant permissions to create databases to the main database user by running: \n\n".
							"REVOKE ALL PRIVILEGES ON * . * FROM 'groupoffice-com'@'localhost';\n".
							"GRANT ALL PRIVILEGES ON * . * TO 'groupoffice-com'@'localhost' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;\n\n". $e->getMessage());
		}
	}
	
	private function _createDbUser($config){
		$sql = "GRANT ALL PRIVILEGES ON `".$config['db_name']."`.*	TO ".
								"'".$config['db_user']."'@'".$config['db_host']."' ".
								"IDENTIFIED BY '".$config['db_pass']."' WITH GRANT OPTION";			

		GO::getDbConnection()->query($sql);
		GO::getDbConnection()->query('FLUSH PRIVILEGES');		
	}
	
	/**
	 * Load data from config file to response array to fill form fields
	 * @param type $response
	 * @param type $model
	 * @param type $params
	 * @return type 
	 */
	protected function afterLoad(&$response, &$model, &$params) {
		
		if(file_exists($model->configPath))
		{
			$c = $model->getConfigWithGlobals();

			$response['data']['enabled']=empty($c['id']) || !empty($c['enabled']);
			$response['data']['max_users'] = GO_Base_Util_Number::unlocalize($c['max_users']);

			$response['data']['webmaster_email'] = $c['webmaster_email'];
			$response['data']['title'] = $c['title'];
			$response['data']['default_country'] = $c['default_country'];
			$response['data']['language'] = $c['language'];
			$response['data']['default_timezone'] = $c['default_timezone'];
			$response['data']['default_currency'] = $c['default_currency'];
			$response['data']['default_time_format'] = $c['default_time_format'];
			$response['data']['default_date_format'] = $c['default_date_format'];
			$response['data']['default_date_separator'] = $c['default_date_separator'];
			$response['data']['default_thousands_separator'] = $c['default_thousands_separator'];
			$response['data']['theme'] = $c['theme'];

			$response['data']['default_decimal_separator'] = $c['default_decimal_separator'];
			$response['data']['first_weekday'] = $c['first_weekday'];


			$response['data']['allow_themes'] = !empty($c['allow_themes']);
			$response['data']['allow_password_change'] = !empty($c['allow_password_change']);

			$response['data']['quota'] = GO_Base_Util_Number::localize($c['quota']/1024/1024/1024); //in gigabytes
			$response['data']['restrict_smtp_hosts'] = $c['restrict_smtp_hosts'];
			$response['data']['serverclient_domains'] = $c['serverclient_domains'];
		}
		
		if($model->automaticInvoice == null)
			$model->automaticInvoice = new GO_ServerManager_Model_AutomaticInvoice();
		
		$response['data'] = array_merge($response['data'], $model->automaticInvoice->getAttributes());
		
		return parent::afterLoad($response, $model, $params);
	}

	/**
	 * Create a temparory config file and call the create action as root
	 */
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		$tmpConfigFile = $this->_createConfig($params, $model);
				
		$cmd = 'sudo TERM=dumb '.GO::config()->root_path.
						'groupofficecli.php -r=servermanager/installation/create'.
						' -c='.GO::config()->get_config_file().
						' --tmp_config='.$tmpConfigFile->path().
						' --name='.$model->name.	
						' --adminpassword='.$params['admin_password1'].' 2>&1';
		//throw new Exception($cmd);
		exec($cmd, $output, $return_var);		

		if($return_var!=0){
			throw new Exception(implode('<br />', $output));
		}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		if(!empty($params['admin_password1']))
		{
			if($params['admin_password1']!=$params['admin_password2'])
			{
				throw new Exception('The passwords didn\'t match. Please try again');
			}
		}
		
		if(isset($params['enable_invoicing']) && $params['enable_invoicing']=='on')
		{
			if($model->automaticInvoice != null)
				$autoInvoice = $model->automaticInvoice;
			else
				$autoInvoice = new GO_ServerManager_Model_AutomaticInvoice();
			
			$autoInvoice->setAttributes($params);
			
			$autoInvoice->enable_invoicing = true;
			$model->setAutoInvoice($autoInvoice);
		}
		elseif($model->automaticInvoice != null) //turn off if exists
		{
			$autoInvoice = $model->automaticInvoice;
			$autoInvoice->enable_invoicing = false;
			$model->setAutoInvoice($autoInvoice);
		}
		
		if(isset($params['modules']))
			$model->setModules( json_decode($params['modules'], true) );
		
		return parent::beforeSubmit($response, $model, $params);
	}

	private function _createConfig($params, $model) {
		if (isset($params['modules'])) {
			$modules = json_decode($params['modules']);
			$modules[]='serverclient';
			$modules[]='users';
			$modules[]='groups';
			$modules[]='modules';
			
			$config['allowed_modules'] = implode(',', $modules);
		}
		
		
		if(!file_exists($model->configPath)){
			//only create these values on new config files.
			
			//for testing		
			$config['debug']=GO::config()->debug;

			$config['id']=$model->dbName;
			$config['db_name']=$model->dbName;
			$config['db_user']=$model->dbUser;
			$config['db_host']=GO::config()->db_host;
			$config['db_pass']= GO_Base_Util_String::randomPassword(8,'a-z,A-Z,1-9');
			$config['host']='/';
			$config['root_path']=$model->installPath.'groupoffice/';
			$config['tmpdir']='/tmp/'.$model->name.'/';
			$config['file_storage_path']=$model->installPath.'data/';
		}
				
		$config['enabled']=empty($params['id']) || !empty($params['enabled']);
		$config['max_users'] = GO_Base_Util_Number::unlocalize($params['max_users']);

		$config['webmaster_email'] = $params['webmaster_email'];
		$config['title'] = $params['title'];
		$config['default_country'] = $params['default_country'];
		$config['language'] = $params['language'];
		$config['default_timezone'] = $params['default_timezone'];
		$config['default_currency'] = $params['default_currency'];
		$config['default_time_format'] = $params['default_time_format'];
		$config['default_date_format'] = $params['default_date_format'];
		$config['default_date_separator'] = $params['default_date_separator'];
		$config['default_thousands_separator'] = $params['default_thousands_separator'];
		$config['theme'] = $params['theme'];

		$config['default_decimal_separator'] = $params['default_decimal_separator'];
		$config['first_weekday'] = $params['first_weekday'];


		$config['allow_themes'] = !empty($params['allow_themes']);
		$config['allow_password_change'] = !empty($params['allow_password_change']);

		$config['quota'] = GO_Base_Util_Number::unlocalize($params['quota'])*1024*1024*1024;
		$config['restrict_smtp_hosts'] = $params['restrict_smtp_hosts'];
		$config['serverclient_domains'] = $params['serverclient_domains'];
		
		//throw new Exception(var_export($config, true));
				

		if (intval($config['max_users']) < 1)
			throw new Exception('You must set a maximum number of users');

		if (!GO_Base_Util_String::validate_email($config['webmaster_email']))
			throw new Exception(GO::t('invalidEmail','servermanager'));
		
		$tmpFile = GO_Base_Fs_File::tempFile('', 'php');
		
		if(!GO_Base_Util_ConfigEditor::save($tmpFile, $config)){
			throw new Exception("Failed to save config file!");
		}
		
		return $tmpFile;
	}


	public function formatStoreRecord($record, $model, $store) {
		
		$record['total_usage']= $model->totalUsageText;
		$record['file_storage_usage']= $model->fileStorageUsageText;
		
		$record['database_usage']= $model->databaseUsageText;
		$record['mailbox_usage']= $model->mailboxUsageText;
		$record['count_users'] = $model->countUsers;
		$record['total_logins'] = $model->totalLogins;
		//$record['quota']=GO_Base_Util_Number::formatSize($model->quota*1024);
		
		if(file_exists($model->configPath))
		{
			$c = $model->getConfigWithGlobals();
			$record['quota']=GO_Base_Util_Number::formatSize($c['quota']);
			$record['enabled']=isset($c['enabled']) ? $c['enabled'] : true;
			$record['title']=$c['title'];
			$record['webmaster_email']=$c['webmaster_email'];
			$record['max_users']=isset($c['max_users']) ? $c['max_users'] : 0;
			$record['serverclient_domains']=isset($c['serverclient_domains']) ? $c['serverclient_domains'] : '';
		}
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	/*
	private function _countModuleUsers($installation_id, $module_id){
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('count(*) AS usercount')
						->joinModel(array('model'=>'GO_ServerManager_Model_InstallationUser', 'localField'=>'user_id','tableAlias'=>'u'))
						->single()
						->debugSql()
						->criteria(
									GO_Base_Db_FindCriteria::newInstance()
									->addCondition('installation_id', $installation_id,'=','u')
									->addCondition('module_id', $module_id)
								);
		
		$record = GO_ServerManager_Model_InstallationUserModule::model()->find($findParams);
		return $record->usercount;
	}
	*/
	
	/**
	 * Returns a list with all modules availible for installation
	 * Get executed when clicking Modules tab in installationdialog
	 * @param array $params the $_REQUEST
	 * @return string JSON encode array for extjs datagrid
	 */
	protected function actionModules($params){

		$installation=null;
		if(isset($params['installation_id']))
			$installation = GO_ServerManager_Model_Installation::model()->findByPk($params['installation_id']);
		if($installation == null)
			$installation = new GO_ServerManager_Model_Installation();

		$moduleList = $installation->getModulesList();

		$results=array();
		foreach($moduleList as $module)
			$results[$module->name] = $module->toArray();
		
		ksort($results); //Sort modules by name
		
		$response['results']=array_values($results);
		
		$response['total']=count($response['results']);
		
		return $response;		
	}
	
	/**
	 * Run maintenance/upgrade and clear cache for every installation
	 * @throws Exception when not run from commandline
	 */
	protected function actionUpgradeAll($params){
		
		if(!$this->isCli())
			throw new Exception("This action may only be ran on the command line.");
		
		$stmt = GO_Servermanager_Model_Installation::model()->find();
		while($installation = $stmt->fetch()){
			
			echo "Upgrading ".$installation->name."\n";
			
			if(!$installation->config){
				echo "\nERROR: Config file ".$installation->configPath." not found\n\n";
				continue;
			}
			
			$cmd = GO::config()->root_path.'groupofficecli.php -r=maintenance/upgrade -c="'.$installation->configPath.'"';
			
			system($cmd);		
			
			exec('chown -R www-data:www-data '.$installation->config['file_storage_path'].'cache');

//			if($return_var!=0){
//				echo "ERROR: ".implode("\n", $output);
//			}
			
			echo "Done\n\n";
			
		}
	}	
	
	/**
	 * Run maintenance/upgrade and clear cache for every installation
	 * @throws Exception when not run from commandline
	 */
	protected function actionFixQuota($params){
		
		if(!$this->isCli())
			throw new Exception("This action may only be ran on the command line.");
		
		$stmt = GO_Servermanager_Model_Installation::model()->find();
		while($installation = $stmt->fetch()){
			
			echo "Processing ".$installation->name."\n";
			
			if(!$installation->config){
				echo "\nERROR: Config file ".$installation->configPath." not found\n\n";
				continue;
			}
			
			if(isset($installation->config['quota']))
				$installation->setConfigVariable('quota', $installation->config['quota']*1024);
			
			echo "Done\n\n";
			
		}
	}	
	
	protected function actionRunOnAll($params){
		
		if(!$this->isCli())
			throw new Exception("This action may only be ran on the command line.");
		
		$stmt = GO_Servermanager_Model_Installation::model()->find();
		while($installation = $stmt->fetch()){
			
			echo "Upgrading ".$installation->name."\n";
			
			if(!file_exists($installation->configPath)){
				echo "\nERROR: Config file ".$installation->configPath." not found\n\n";
				continue;
			}
			
			require($installation->configPath);
			
			$cmd = GO::config()->root_path.'groupofficecli.php -r="'.$params["route"].'" -c="'.$installation->configPath.'"';
			
			system($cmd);
						
			echo "Done\n\n";
			
		}
	}	
	
	protected function actionSetAllowed($params){
		
		if(!$this->isCli())
			throw new Exception("This action may only be ran on the command line.");
		
		$this->checkRequiredParameters(array('module'), $params);
		
		if(!isset($params['allow'])){
			exit("--allow is required");
		}
		
//		if(!empty($allow)){
//			exit("--allow is required");
//		}
		
		$allow = !empty($params['allow']);	
		
		$stmt = GO_Servermanager_Model_Installation::model()->find();
		while($installation = $stmt->fetch()){
			echo "Setting ".$installation->name."\n";
			$c = $installation->getConfigWithGlobals();
			if($c){
				$allowed = explode(',',$c['allowed_modules']);
				$newAllowed = array();
				
				if(!$allow){					
					foreach($allowed as $module){
						if($module!=$params['module'])
							$newAllowed[]=$module;
					}
				}else
				{
					$allowed[]=$params['module'];
					$newAllowed = array_unique($allowed);
				}
				
				$installation->setConfigVariable('allowed_modules',implode(',',$newAllowed));
			}		
		}
	}	
	
	
	protected function actionRemoveSuspendedAndUnused($params){
		
		if(!$this->isCli())
			throw new Exception("This action may only be ran on the command line.");
		
		//unused for two months
		$lastlogin = GO_Base_Util_Date::date_add(time(), 0, -2);
		
		
		$fp = GO_Base_Db_FindParams::newInstance();
		$fp->getCriteria()->addCondition('lastlogin', $lastlogin,'<')->addCondition('lastlogin', null, 'IS','t',false);
		
		$count=0;
		
		$stmt = GO_Servermanager_Model_Installation::model()->find($fp);
		while($installation = $stmt->fetch()){
			echo "Deleting ".$installation->name."\n";
			
			if(!empty($params['really']))
				$installation->delete();
			
			$count++;
		}
		
		echo "Deleted ".$count." trials\n\n";
		
		echo "Done\n\n";
	}
	
	
	/**
	 * This will test the connection with the billing module
	 * Will return succes when connection succeeds
	 * @param array $parmas the $_REQUEST object 
	 */
	protected function actionTestBilling($parmas){
		
		$response = array(
				'success'=>GO_ServerManager_Model_AutomaticInvoice::canConnect()
		);
		return $response;
	}
	
	/**
	 * This action will be called by a cronjob that runs daily
	 * Loop through all installations see if config file excists and is enabled
	 * Check if (trail) installation is expired if not execute maintenance/servermanagerReport
	 * Send automatic email for every installation
	 * TODO: Send reports for automatic invoicing
	 * @param array $params content of $_REQUEST (empty)
	 * @throws Exception When this action is not called from Commandline
	 */
	protected function actionReport($params){
		
		if(!$this->isCli())
			throw new Exception("You may only run this command on the command line");
		
		$report = array(
				'installations'=>array(),
				'id'=>GO::config()->id,
				'hostname'=>getHostName(),
				'ip'=>  gethostbyname(getHostName()),
				'name'=>GO::config()->title,
				'version'=>GO::config()->version,
				'uname'=>  php_uname(),
				'moduleCounts'=>array()
		);

		
		$installations = empty($params['installation']) ? GO_ServerManager_Model_Installation::model()->find()->fetchAll() : GO_ServerManager_Model_Installation::model()->findByAttribute('name',$params['installation'])->fetchAll();
		foreach($installations as $installation)
		{			
			
			if(!$installation->config){
				echo "Config file does not exist for ".$installation->name."\n";
				continue;
			}
			if(isset($installation->config['enabled']) && $installation->config['enabled']==false) {
				echo "Installation ".$installation->name." is suspended\n";
				continue;
			}
			
			echo "Creating report for ".$installation->name."\n";
			try{
				if($installation->loadUsageData()){
					
					$report['installations'][]=array_merge($installation->getAttributes(), $installation->getHistoryAttributes());
				}else{
					echo "Unable to fetch data for ".$installation->name."\n";
				}

				
				
				if($installation->save())
					echo "Installation was updated\n";
				else
					echo "ERROR: failed to save new installation information\n";
				
				
				//check if installation is expired and suspend if so
				if($installation->isExpired){
					if($installation->delete())
						echo "Installation ".$installation->name." was deleted\n";
				}
				
			}catch(Exception $e){
				echo "ERROR:\n";
				echo $e->getMessage()."\n";
				$report['errors']=(string) $e;
			}
			
			if(!$installation->isSuspended)
			{
				//run tasks for installation like log rotation and filesearch index update.
				echo "Running daily tasks for installation\n";
				$cmd ='/usr/share/groupoffice/groupofficecli.php -r=maintenance/servermanagerReport -c="'.$installation->configPath.'"  2>&1';				
				system($cmd);
			}

			$installation->sendAutomaticEmails();
			
			//send automatic invoices if enabled
//			if(!empty($installation->automaticInvoice) && $installation->automaticInvoice->enable_invoicing && $installation->automaticInvoice->shouldCreateOrder())
//			{	
//				if($installation->automaticInvoice->sendOrder())
//					echo "Order was posted to billing successfull\n";
//				else
//					echo "ERROR: Failed sending order to billing\n";
//			}
		}
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('name, count(name) AS count')
						->group('name');
		
		$stmt = GO_ServerManager_Model_InstallationModule::model()->find($findParams);
		foreach($stmt as $module){
			$report['moduleCounts'][$module->name]=intval($module->count);
		}
		
//		var_dump($report);
		
		//$report['moduleCounts']=
		

//		if(class_exists('GO_Professional_LicenseCheck')){
//			
//			if(!isset(GO::config()->license_name)){
//				throw new Exception('$config["license_name"] is not set. Please contact Intermesh to get your key.');
//			}
//			
//			$report['license_name']=GO::config()->license_name;
//		}

		//Post the report to intermesh
		if(class_exists('GO_Professional_LicenseCheck')){
			$c = new GO_Base_Util_HttpClient();
			$url = 'https://intermesh.group-office.com/index.php?r=licenses/server/report';
//			$url = 'http://intermesh.intermesh.dev/index.php?r=licenses/server/report';
			$response = $c->request($url, array(
					'report'=>json_encode($report)
			));

			$response = json_decode($response, true);

			if($response['success'])
				echo "Report was sent to Intermesh\n";
			else{
				echo "ERROR: sending report to Intermesh\n";
				var_dump($response);
			}
		}

//		$message = GO_Base_Mail_Message::newInstance();
//		$message->setSubject("Servermanager report for ". $report['hostname']);
//
//		$message->setBody(json_encode($report),'text/plain');
//		$message->setFrom(GO::config()->webmaster_email,"Servermanager");
//		$message->addTo('admin@intermesh.dev');
//
//		GO_Base_Mail_Mailer::newGoInstance()->send($message);
				
		
		echo "Done\n\n";
	}
	
	/**
	 * Save the remote invoice connection parameters with database and try to connection
	 * @param array $params the $_REQUEST object
	 * @return boolean true if the connection was established with the remote host
	 */
	public function actionRemoteInvoiceConnection($params)
	{
		//Set the settings from params
		/*
		GO::config()->save_setting('servermanager_invoice_host', $params['remote_invoice_host']);
		GO::config()->save_setting('servermanager_invoice_username', $params['remote_invoice_username']);
		GO::config()->save_setting('servermanager_invoice_password', $params['remote_invoice_password']);
		*/
		
		$response['success'] = GO_ServerManager_Model_AutomaticInvoice::canConnect();
		return $response;
	}
	
	/**
	 * Returns a list of InstallationUsers's to display on the usage tab of an installation
	 * @param type $params 
	 */
	public function actionUsersStore($params)
	{
		$cm =  new GO_Base_Data_ColumnModel();
		$cm->setColumnsFromModel(GO_ServerManager_Model_InstallationUser::model());
		$cm->formatColumn('trialDaysLeft','$model->trialDaysLeft');
		
		$store = new GO_Base_Data_Store($cm);
		$storeParams = $store->getDefaultParams($params);
		$storeParams = $storeParams->select('t.*'); //makes sure field of type TEXT get loaded
		$criteria = GO_Base_Db_FindCriteria::newInstance()->addCondition('installation_id', $params['installation_id']);
		$storeParams->mergeWith(GO_Base_Db_FindParams::newInstance()->criteria($criteria));
		$store->setStatement(GO_ServerManager_Model_InstallationUser::model()->find($storeParams));
		
		$response=array("success"=>true,"results"=>array());
		$response = array_merge($response, $store->getData());
		
		return $response;
	}
	
	public function actionHistoryStore($params)
	{
		$cm =  new GO_Base_Data_ColumnModel();
		$cm->setColumnsFromModel(GO_ServerManager_Model_UsageHistory::model());
		$cm->formatColumn('total_usage', '$model->totalUsageText');
		$cm->formatColumn('mailbox_usage', '$model->mailboxUsageText');
		$cm->formatColumn('database_usage', '$model->databaseUsageText');
		$cm->formatColumn('file_storage_usage', '$model->fileStorageUsageText');

		$store = new GO_Base_Data_Store($cm);
		$storeParams = $store->getDefaultParams($params);
		$criteria = GO_Base_Db_FindCriteria::newInstance()->addCondition('installation_id', $params['installation_id']);
		$storeParams->mergeWith(GO_Base_Db_FindParams::newInstance()->criteria($criteria));
		$store->setStatement(GO_ServerManager_Model_UsageHistory::model()->find($storeParams));
		
		$response=array("success"=>true,"results"=>array());
		$response = array_merge($response, $store->getData());
		
		return $response;
	}
	
	
	
}