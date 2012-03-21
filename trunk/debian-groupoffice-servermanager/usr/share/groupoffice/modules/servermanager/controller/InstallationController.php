<?php

class GO_Servermanager_Controller_Installation extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Servermanager_Model_Installation';
	
	protected function allowGuests() {
		return array('create','destroy');
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
			GO::getDbConnection()->query("DROP USER `".$installation->dbUser."`");		
		}catch(Exception $e){
			trigger_error("Could not remove msyql user ".$installation->dbUser,E_USER_WARNING);
		}
		try{
			GO::getDbConnection()->query("DROP DATABASE `".$installation->dbName."`");
		}catch(Exception $e){
			trigger_error("Could not remove msyql database ".$installation->dbName,E_USER_WARNING);
		}
	}	
	
	public function actionCreate($params){
		if(PHP_SAPI!='cli')
			throw new Exception("Action servermanager/installation/create may only be run by root on the command line");
		
		//todo check if we are root
		
		
		
		$installation = GO_ServerManager_Model_Installation::model()->findSingleByAttribute('name', $params['name']);
		
		if(!$installation)
			throw new Exception("Installation ".$params['name']." not found!");
		
		//create config file
		require($params['tmp_config']);		
		unlink($params['tmp_config']);
		
		$config['db_pass']= GO_Base_Util_String::randomPassword(8,'a-zA-Z1-9');
		$configFile = new GO_Base_Fs_File($installation->configPath);
		
		$this->_createFolderStructure($config, $installation);
		
		GO_Base_Util_ConfigEditor::save($configFile, $config);
		$configFile->chown('root');
		$configFile->chgrp('www-data');
		$configFile->chmod(0640);		
		
		$this->_createDatabase($config);
		
		$this->_createDatabaseContent($params, $installation, $config);
	}
	
	private function _createDatabaseContent($params, $installation, $config){
		$cmd = 'php '.GO::config()->root_path.'install/autoinstall.php'.
						' -c='.$installation->configPath.
						' --adminusername=admin'.
						' --adminpassword="'.$params['adminpassword'].'"'.
						' --adminemail="'.$config['webmaster_email'].'"';
		
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
	
	private function _createDatabase($config){
		try{
			GO::getDbConnection()->query("CREATE DATABASE IF NOT EXISTS `".$config['db_name']."`");

			$sql = "GRANT ALL PRIVILEGES ON `".$config['db_name']."`.*	TO ".
							"'".$config['db_user']."'@'".$config['db_host']."' ".
							"IDENTIFIED BY '".$config['db_pass']."' WITH GRANT OPTION";

			GO::getDbConnection()->query($sql);

			GO::getDbConnection()->query('FLUSH PRIVILEGES');		
		}catch(Exception $e){
			throw new Exception("Could not create database. Did you grant permissions to create databases to the main database user by running: \n\n".
							"REVOKE ALL PRIVILEGES ON * . * FROM 'groupoffice-com'@'localhost';\n".
							"GRANT ALL PRIVILEGES ON * . * TO 'groupoffice-com'@'localhost' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;\n\n". $e->getMessage());
		}
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
						' --adminpassword='.$params['admin_password1'];	
		
		exec($cmd, $output, $return_var);		

		if($return_var!=0){
			throw new Exception(implode('<br />', $output));
		}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	private function _createConfig($params, $model) {
		if (isset($params['modules'])) {
			$modules = json_decode($params['modules'], true);

			$allowed = array();

			foreach ($modules as $module) {
				if ($module['allowed'])
					$allowed[] = $module['id'];
			}

			$config['allowed_modules'] = implode(',', $allowed);
		}elseif (empty($params['id'])) {
			$config['allowed_modules'] = isset($default_config['allowed_modules']) ? $default_config['allowed_modules'] : '';
		}
		$config['id']=$model->dbName;
		$config['db_name']=$model->dbName;
		$config['db_user']=$model->dbUser;
		$config['db_host']=GO::config()->db_host;
		
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

		$config['quota'] = GO_Base_Util_Number::unlocalize($params['quota']) * 1024;
		$config['restrict_smtp_hosts'] = $params['restrict_smtp_hosts'];
		$config['serverclient_domains'] = $params['serverclient_domains'];
		
		$config['host']='/';
		$config['root_path']=$model->installPath.'groupoffice/';
		$config['tmpdir']='/tmp/'.$model->name.'/';
		$config['file_storage_path']=$model->installPath.'data/';
				

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

}

