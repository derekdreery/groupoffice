<?php

class GO_Servermanager_Controller_Installation extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Servermanager_Model_Installation';
	
	protected function allowGuests() {
		return array('create','destroy', 'report','upgradeall','rename');
	}
	
	protected function ignoreAclPermissions() {
		return array('create','destroy', 'report');
	}
	
	protected function actionImport($params){
		$folder = new GO_Base_Fs_Folder('/etc/groupoffice');
		$items = $folder->ls();
		
		foreach($items as $item){
			if($item->isFolder() && $item->child('config.php')){
				$installation = GO_ServerManager_Model_Installation::model()->findSingleByAttribute('name', $item->name());
				if(!$installation){
					echo "Importing ".$item->name()."\n";
					$installation = new GO_ServerManager_Model_Installation();
					$installation->ignoreExistingForImport=true;
					$installation->name=$item->name();					
					$installation->report();					
				}
			}
		}
		echo "Done\n\n";
	}
	
	public function actionDestroy($params){
		if(PHP_SAPI!='cli')
			throw new Exception("Action servermanager/installation/delete may only be run by root on the command line");
		
		$installation = GO_ServerManager_Model_Installation::model()->findSingleByAttribute('name', $params['name']);
		
		if(!$installation)
			throw new Exception("Installation ".$params['name']." not found!");
		
		if(!$installation->validate())
			throw new Exception("Installation ".$params['name']." is invalid");
		
		$trashFolderGovhosts = new GO_Base_Fs_Folder('/home/gotrash/govhosts');
		$trashFolderGovhosts->create();
		
		$installationFolder = new GO_Base_Fs_Folder($installation->installPath);
		$installationFolder->move($trashFolderGovhosts);
		
		$trashFolderConfig = new GO_Base_Fs_Folder('/home/gotrash/etc/groupoffice');
		$trashFolderConfig->create();
		
		$configFolder = new GO_Base_Fs_Folder('/etc/groupoffice/'.$installation->name);
		$configFolder->move($trashFolderConfig);
		
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
			$existingConfig = $this->_getConfigFromFile($configFile->path());
		else
			$existingConfig = array();
		
		//create config file
		$newConfig = $this->_getConfigFromFile($params['tmp_config']);
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

				$this->_createDatabaseContent($params, $installation, $existingConfig);
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
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		if(file_exists($model->configPath))
		{
			require($model->configPath);
			if(isset($config))
			{
				$response['data']['enabled']=empty($config['id']) || !empty($config['enabled']);
				$response['data']['max_users'] = GO_Base_Util_Number::unlocalize($config['max_users']);

				$response['data']['webmaster_email'] = $config['webmaster_email'];
				$response['data']['title'] = $config['title'];
				$response['data']['default_country'] = $config['default_country'];
				$response['data']['language'] = $config['language'];
				$response['data']['default_timezone'] = $config['default_timezone'];
				$response['data']['default_currency'] = $config['default_currency'];
				$response['data']['default_time_format'] = $config['default_time_format'];
				$response['data']['default_date_format'] = $config['default_date_format'];
				$response['data']['default_date_separator'] = $config['default_date_separator'];
				$response['data']['default_thousands_separator'] = $config['default_thousands_separator'];
				$response['data']['theme'] = $config['theme'];

				$response['data']['default_decimal_separator'] = $config['default_decimal_separator'];
				$response['data']['first_weekday'] = $config['first_weekday'];


				$response['data']['allow_themes'] = isset($config['allow_themes']) ? true : false;
				$response['data']['allow_password_change'] = isset($config['allow_password_change']) ? true : false;

				$response['data']['quota'] = GO_Base_Util_Number::localize($config['quota']/1024/1024);
				$response['data']['restrict_smtp_hosts'] = $config['restrict_smtp_hosts'];
				$response['data']['serverclient_domains'] = $config['serverclient_domains'];
			}
		}
		
		return parent::afterLoad($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		$tmpConfigFile = $this->_createConfig($params, $model);
		
		if(!empty($params['admin_password1']))
		{
			if($params['admin_password1']!=$params['admin_password2'])
			{
				throw new Exception('The passwords didn\'t match. Please try again');
			}
		}	
				
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
		
		$modules = isset($params['modules']) ? json_decode($params['modules'], true) : false;
		if (empty($params['id']) && empty($modules)) {
			throw new Exception("Please select the allowed modules");
		}
		
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


		$config['allow_themes'] = isset($params['allow_themes']) ? true : false;
		$config['allow_password_change'] = isset($params['allow_password_change']) ? true : false;

		$config['quota'] = GO_Base_Util_Number::unlocalize($params['quota'])*1024*1024*1024;
		$config['restrict_smtp_hosts'] = $params['restrict_smtp_hosts'];
		$config['serverclient_domains'] = $params['serverclient_domains'];
		
		
				

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
	
	protected function afterStore(&$response, &$params, &$store, $storeParams) {
		
		$response['max_users']=0;
		$response['total_users']=0;
		
		return parent::afterStore($response, $params, $store, $storeParams);
	}
	
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['total_usage']= GO_Base_Util_Number::formatSize(($record['file_storage_usage']+$record['database_usage']+$record['mailbox_usage'])*1024);
		$record['file_storage_usage']= GO_Base_Util_Number::formatSize($record['file_storage_usage']*1024);
		$record['database_usage']= GO_Base_Util_Number::formatSize($record['database_usage']*1024);
		$record['mailbox_usage']= GO_Base_Util_Number::formatSize($record['mailbox_usage']*1024);
		
		
		if(file_exists($model->configPath))
		{
			require($model->configPath);
			if(isset($config))
			{
				$record['enabled']=isset($config['enabled']) ? $config['enabled'] : true;
				$record['title']=$config['title'];
				$record['webmaster_email']=$config['webmaster_email'];
				$record['max_users']=isset($config['max_users']) ? $config['max_users'] : 0;
			}
		}
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	private function _countModuleUsers($installation_id, $module_id){
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('count(*) AS usercount')
						->joinModel(array('model'=>'GO_ServerManager_Model_InstallationUser',  'localField'=>'user_id','tableAlias'=>'u'))
						->single()
						->debugSql()
						->criteria(
										GO_Base_Db_FindCriteria::newInstance()
										->addCondition('installation_id', $installation_id,'=','u')
										->addCondition('module_id', $module_id)
										);
		
		$record = GO_ServerManager_Model_InstallationUserModule::model()->find($findParams);
		return $record['usercount'];
	}
	
	
	protected function actionModules($params){

		$modules = GO::modules()->getAvailableModules(true);
		
		$allowedModules = array();
		if(!empty($params['installation_id']) && ($installation=  GO_ServerManager_Model_Installation::model()->findByPk($params['installation_id']))){
			require($installation->configPath);
			
			if(!isset($config['allowed_modules']))
				$config['allowed_modules']="";
			
			$allowedModules = explode(',', $config['allowed_modules']);
		}
		
		$hideModules = array('servermanager','serverclient','users','groups','modules','postfixadmin');
		$availableModules=array();
		foreach($modules as $moduleClass){
			
			$module = new $moduleClass;//call_user_func($moduleClase();
			if(!in_array($module->id(), $hideModules)){
				$availableModules[$module->name()] = array(
						'id'=>$module->id(),
						'name'=>$module->name(),
						'description'=>$module->description(),
						'checked'=>in_array($module->id(), $allowedModules),
						'usercount'=>!empty($params['installation_id']) ? $this->_countModuleUsers($params['installation_id'], $module->id()) : '-'
				);
			}
		}
		
		ksort($availableModules);		
		
		$response['results']=array_values($availableModules);
		
		$response['total']=count($response['results']);
		
		return $response;		
	}
	
	protected function actionUpgradeAll($params){
		
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
			
			$cmd = GO::config()->root_path.'groupofficecli.php -r=maintenance/upgrade -c="'.$installation->configPath.'"';
			
			system($cmd);		
			
			exec('chown -R www-data:www-data '.$config['file_storage_path'].'cache');

//			if($return_var!=0){
//				echo "ERROR: ".implode("\n", $output);
//			}
			
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
	
	
	protected function actionReport($params){
		$now = time();
		$stmt = GO_ServerManager_Model_Installation::model()->find();
		
		if(!$this->isCli())
			throw new Exception("You may only run this command on the command line");
		
		$report = array(
				'installations'=>array(),
				'id'=>GO::config()->id,
				'hostname'=>getHostName(),
				'ip'=>  gethostbyname(getHostName()),
				'uname'=>  php_uname()
		);
		
		while($installation = $stmt->fetch()){
			echo "Creating report for ".$installation->name."\n";
			$report['installations'][]=$installation->report();
			
			//run tasks for installation like log rotation and filesearch index update.
			
			echo "Running daily tasks for installation\n";
			$cmd ='/usr/share/groupoffice/groupofficecli.php -r=maintenance/servermanagerReport -c="'.$installation->configPath.'"  2>&1';				
			system($cmd);
			
			
			$this->_sendAutomaticEmails($installation,$now);
		
		}
		
		
		
//		if(class_exists('GO_Professional_LicenseCheck')){
//			
//			if(!isset(GO::config()->license_name)){
//				throw new Exception('$config["license_name"] is not set. Please contact Intermesh to get your key.');
//			}
//			
//			$report['license_name']=GO::config()->license_name;
//			
//			$c = new GO_Base_Util_HttpClient();
//			$response = $c->request('http://localhost/groupoffice/?r=licenses/license/report', array(
//					'report'=>json_encode($report)
//			));
//			
//			var_dump($response);
//		}
		
	
	
		
				
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

	private function _sendAutomaticEmails(GO_Servermanager_Model_Installation $installationModel, $nowUnixTime=false) {
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
		
		while ($autoEmailModel = $autoEmailsStmt->fetch()) {
			
			//Send the mail only if the creation time of the installation + the number of days is today.
			$dayStart = GO_Base_Util_Date::date_add($nowUnixTime,-$autoEmailModel->days);
			$dayStart = GO_Base_Util_Date::clear_time($dayStart);
			$dayEnd = GO_Base_Util_Date::date_add($dayStart,1);			
			
			if (!empty($autoEmailModel->active) && $installationModel->ctime>$dayStart && $installationModel->ctime<$dayEnd) {
				$message = GO_Base_Mail_Message::newInstance()
					->loadMimeMessage($autoEmailModel->mime)
					->addTo($installationModel->admin_email, $installationModel->admin_name)
					->setFrom(GO::config()->webmaster_email, 'Servermanager Administrator');

				$body = $this->_parseTags(
					$message->getBody(),
					array('installation'=>$installationModel,'automaticemail'=>$autoEmailModel)
				);
				
				$message->setBody($body);

				GO_Base_Mail_Mailer::newGoInstance()->send($message);
			}
		}
		
	}
	
	/**
	 * Parses string using tag combinations of the form:
	 * 'modelname:attributename' replaced by the value of $model->attribute
	 * @param String $string String to be parsed
	 * @param array $models Array of ActiveRecords. Keys will be the prefixes (the
	 * modelname part mentioned above).
	 * @return String Parsed string.
	 */
	private function _parseTags($string,array $models) {
		$attributes = array();
		foreach ($models as $tagPrefix => $model) {
			$attributes = array_merge($attributes,$this->_addPrefixToKeys($model->getAttributes(),$tagPrefix.':'));
		}
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($string, $attributes);
	}
	
	/**
	 * Puts the prefix $tagPrefix before each key in the $array.
	 * @param array $array
	 * @param string $tagPrefix
	 * @return array
	 */
	private function _addPrefixToKeys(array $array,$tagPrefix) {
		$outputArray = array();
		foreach ($array as $k => $v) {
			$outputArray[$tagPrefix.$k] = $v;
		}
		return $outputArray;
	}
	
}

