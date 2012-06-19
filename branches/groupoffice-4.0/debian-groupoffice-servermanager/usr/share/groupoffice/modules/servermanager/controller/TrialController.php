<?php

class GO_Servermanager_Controller_Trial extends GO_Base_Controller_AbstractController {

	protected $newTrial;
	
	protected function allowGuests() {
		return array('create');
	}

	public function actionCreate($params) {
		
		if(empty(GO::config()->servermanager_trials_enabled))
			throw new Exception("Trials are not enabled. Set \$config['servermanager_trials_enabled']=true;");

		$this->newTrial = GO_ServerManager_Model_NewTrial::model()->findSingleByAttribute('key', $params['key']);

		if (GO_Base_Util_Http::isPostRequest()) {
			
			
			$installation = new GO_ServerManager_Model_Installation();
			$installation->name = $this->newTrial->name.'.'.GO::config()->servermanager_wildcard_domain;
			
			if(GO_Base_Html_Error::validateModel($installation)){

				$installation->save();

				$tmpConfigFile = $this->_createConfig($params, $installation, $this->newTrial);

				$cmd = 'sudo TERM=dumb ' . GO::config()->root_path .
								'groupofficecli.php -r=servermanager/installation/create' .
								' -c=' . GO::config()->get_config_file() .
								' --tmp_config=' . $tmpConfigFile->path() .
								' --name=' . $installation->name.
								' --adminpassword=' . $this->newTrial->password . ' 2>&1';

				exec($cmd, $output, $return_var);

				if ($return_var != 0) {
					throw new Exception(implode('<br />', $output));
				}
				
				
				$this->render('trialcreated', array('installation'=>$installation));
				
				$this->newTrial->delete();
				exit();
			}
		}

		$this->render('createtrial');
	}
	
	
	private function _createConfig($params, GO_ServerManager_Model_Installation $model, GO_ServerManager_Model_NewTrial $newTrial) {
		
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
				
		$config['enabled']=true;
		$config['max_users'] = $model->max_users;

		$config['webmaster_email'] = $newTrial->email;
		$config['title'] = $newTrial->title;
		$config['default_country'] = $params['default_country'];
		$config['language'] = $params['language'];
		$config['default_timezone'] = $params['default_timezone'];
		$config['default_currency'] = $params['default_currency'];
		$config['default_time_format'] = $params['default_time_format'];
		$config['default_date_format'] = $params['default_date_format'];
		$config['default_date_separator'] = $params['default_date_separator'];
		$config['default_thousands_separator'] = $params['default_thousands_separator'];
		$config['default_decimal_separator'] = $params['default_decimal_separator'];
		//$config['first_weekday'] = $params['first_weekday'];
		

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

}

