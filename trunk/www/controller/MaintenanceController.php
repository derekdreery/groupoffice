<?php
class GO_Core_Controller_Maintenance extends GO_Base_Controller_AbstractController {

	/**
	 * Calls buildSearchIndex on each Module class.
	 * @return array 
	 */
	public function actionBuildSearchCache() {
		$response = array();
		
		GO::$ignoreAclPerissions=true; //allow this script access to all
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '300');
		
		echo '<pre>';
		GO::modules()->callModuleMethod('buildSearchCache', array(&$response));
		return $response;
	}

	/**
	 * Calls checkDatabase on each Module class.
	 * @return array 
	 */
	public function actionCheckDatabase() {
		$response = array();
		
		GO::$ignoreAclPerissions=true; //allow this script access to all
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '300');
		
		echo '<pre>';		
		GO::modules()->callModuleMethod('checkDatabase', array(&$response));
		return $response;
	}


	

	public function actionUpgrade($params) {

		GO::$ignoreAclPerissions=true; //allow this script access to all
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '300');
		
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
			
		
		GO::$ignoreAclPerissions = true;

		
		//build an array of all update files. The queries are indexed by timestamp
		//so they will all be executed in the right order.
		$u = array();

		require(GO::config()->root_path . 'install/updates.php');

		//put the updates in an extra array dimension so we know to which module
		//they belong too.
		foreach ($updates as $timestamp => $updatequeries) {
			$u[$timestamp]['core'] = $updatequeries;
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
					$u[$timestamp][$module->id] = $updatequeries;
				}
			}
		}
		//sort the array by timestamp
		ksort($u);

		$currentCoreVersion = GO::config()->get_setting('version');
		if (!$currentCoreVersion)
			$currentCoreVersion = 0;
		
		$counts=array();
		
		foreach ($u as $timestamp => $updateQuerySet) {
			
			foreach ($updateQuerySet as $module => $queries) {
				
				if($module=='core')
					$currentVersion=$currentCoreVersion;
				else
					$currentVersion = GO::modules()->$module->version;
				
				if(!isset($counts[$module]))
					$counts[$module]=1000;				//start at 100 for 3.x update system.	
				
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
								GO::modules()->$module->version=$counts[$module];
								GO::modules()->$module->save();
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

	/**
	 * Todo replace compress.php with this action
	 */
	protected function actionCompress() {
		
	}

}