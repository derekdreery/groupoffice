<?php
class GO_Core_Controller_Maintenance extends GO_Base_Controller_AbstractController {

	protected function init() {
		
		GO::$ignoreAclPermissions=true; //allow this script access to all
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '300');
		session_write_close();		
	}
	/**
	 * Calls buildSearchIndex on each Module class.
	 * @return array 
	 */
	public function actionBuildSearchCache() {
		$response = array();
		
		GO::getDbConnection()->query('TRUNCATE TABLE go_search_cache');
		
		echo '<pre>';
		GO::modules()->callModuleMethod('buildSearchCache', array(&$response));
		return $response;
	}

	/**
	 * Calls checkDatabase on each Module class.
	 * @return array 
	 */
	public function actionCheckDatabase($params) {
		$response = array();
				
		echo '<pre>';		
		
		if(!empty($params['module'])){
			if($params['module']=='base'){
				$this->_checkCoreModels();
			}else
			{
				$class='GO_'.ucfirst($params['module']).'_'.ucfirst($params['module']).'Module';
				$module = new $class;
				$module->checkDatabase($response);
			}
		}else
		{
			$this->_checkCoreModels();
			GO::modules()->callModuleMethod('checkDatabase', array(&$response));
		}
		
		
		return $response;
	}
	
	private function _checkCoreModels(){
		
		//fix for invalid acl rows.
		$sql = "insert ignore into go_acl (acl_id,group_id) SELECT acl_id,group_id FROM `go_acl` WHERE user_id>0 && group_id>0;";
		GO::getDbConnection()->query($sql);
		
		$sql = "insert ignore into go_acl (acl_id,user_id) SELECT acl_id,user_id FROM `go_acl` WHERE user_id>0 && group_id>0;";
		GO::getDbConnection()->query($sql);		
		
		$sql = "delete from go_acl where user_id>0 and group_id>0;";
		GO::getDbConnection()->query($sql);

		
		
		$classes=GO::findClasses('model');
		foreach($classes as $model){
			if($model->isSubclassOf('GO_Base_Db_ActiveRecord')){
		
				echo "Processing ".$model->getName()."\n";
				flush();

				$m = GO::getModel($model->getName());

				$stmt = $m->find(array(
						'ignoreAcl'=>true
				));
				$stmt->callOnEach('checkDatabase');
			}
		}
	}
	
	public function actionUpgrade($params) {
		
		//don't be strict in upgrade process
		GO::getDbConnection()->query("SET sql_mode=''");
		
		$logDir = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'log/upgrade/');
		$logDir->create();
		global $logFile;
		
		$logFile = $logDir->path().'/'.date('Ymd_Gi').'.log';
		touch ($logFile);

		if(!is_writable($logFile)){
			die('Fatal error: Could not write to log file');
		}

		function ob_upgrade_log($buffer)
		{
			global $logFile;

			file_put_contents($logFile, $buffer, FILE_APPEND);
			return $buffer;
		}
		
		if(php_sapi_name() != 'cli'){
			echo '<pre>';
		}
		ob_start("ob_upgrade_log");
		
		echo "Removing cached javascripts...\n";
		
		GO::clearCache();
		
		
		echo "Updating Group-Office database\n";
		
		//build an array of all update files. The queries are indexed by timestamp
		//so they will all be executed in the right order.
		$u = array();

		require(GO::config()->root_path . 'install/updates.php');
		
		//put the updates in an extra array dimension so we know to which module
		//they belong too.
		foreach ($updates as $timestamp => $updatequeries) {
			$u["$timestamp"]['core'] = $updatequeries;
		}


		$stmt = GO::modules()->getAll();
		while ($module = $stmt->fetch()) {
			$updatesFile = $module->path . 'install/updates.php';
			if (!file_exists($updatesFile))
				$updatesFile = $module->path . 'install/updates.inc.php';

			if (file_exists($updatesFile)) {
				$updates = array();
				require($updatesFile);

				//put the updates in an extra array dimension so we know to which module
				//they belong too.
				foreach ($updates as $timestamp => $updatequeries) {
					$u["$timestamp"][$module->id] = $updatequeries;
				}
			}
		}
		//sort the array by timestamp
		ksort($u);
//		
//		var_dump($u);
//		exit();

		$currentCoreVersion = GO::config()->get_setting('version');
		if (!$currentCoreVersion)
			$currentCoreVersion = 0;
		
		$counts=array();
		
		foreach ($u as $timestamp => $updateQuerySet) {
			
			foreach ($updateQuerySet as $module => $queries) {
				
				echo "Getting updates for ".$module."\n";
				
				if(!is_array($queries)){
					exit("Invalid queries in module: ".$module);
				}
				
				if($module=='core')
					$currentVersion=$currentCoreVersion;
				else
					$currentVersion = GO::modules()->$module->version;
				
				if(!isset($counts[$module]))
					$counts[$module]=0;			
				
				foreach ($queries as $query) {
					$counts[$module]++;
					if ($counts[$module] > $currentVersion) {
						if (substr($query, 0, 7) == 'script:') {
							if ($module == 'core')
								$updateScript = GO::config()->root_path . 'install/updatescripts/' . substr($query, 7);
							else
								$updateScript = GO::modules()->$module->path . 'install/updatescripts/' . substr($query, 7);

							if (!file_exists($updateScript)) {
								die($updateScript . ' not found!');
							}
							//if(!$quiet)
							echo 'Running ' . $updateScript . "\n";
							if (empty($params['test']))
								require_once($updateScript);
						}else {
							echo 'Excuting query: ' . $query . "\n";
							if (empty($params['test'])) {
								try {
									GO::getDbConnection()->query($query);
								} catch (PDOException $e) {
									//var_dump($e);
									echo $e->getMessage() . "\n";
//									if ($e->getCode() == 1091 || $e->getCode() == 1060) {
//										//duplicate and drop errors. Ignore those on updates
//									} else {
//										die();
//									}
								}
							}
						}

						if (empty($params['test'])) {
							if($module=='core')
								GO::config()->save_setting('version', $counts[$module]);
							else{
								
								//echo $module.' updated to '.$counts[$module]."\n";
								
								$moduleModel = GO::modules()->$module;
								
								$moduleModel->version=$counts[$module];
								$moduleModel->save();
							}
							flush();
						}
					}
				}
			}
		}

		if (empty($params['test'])) {
			echo "Database updated to version " . GO::config()->mtime, "\n";
			
			GO::config()->save_setting('upgrade_mtime', GO::config()->mtime);
		} else {
			echo "Ran in test mode\n";
		}

		//return $response;
	}
}