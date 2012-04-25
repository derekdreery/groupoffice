<?php
/**
 * WARNING: This controller does not check authentication!
 * 
 * Controller with some maintenance functions
 */
class GO_Core_Controller_Maintenance extends GO_Base_Controller_AbstractController {
	
	protected function allowGuests() {
		return array('upgrade','checkdatabase');
	}

	protected function init() {
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '0'); //allow long runs		
		ini_set('memory_limit','512M');
		ini_set('display_errors','on');
		error_reporting(E_ALL);
		
		GO::session()->closeWriting(); //close writing otherwise concurrent requests are blocked.
	}
	
	protected function ignoreAclPermissions() {
		return array('*');
	}
	
	protected function actionGetNewAcl($params){
		$acl = new GO_Base_Model_Acl();
		$acl->user_id=isset($params['user_id']) ? $params['user_id'] : GO::user()->id;
		$acl->description=$params['description'];
		$acl->save();
		
		echo $acl->id;
	}
	
	protected function actionRemoveDuplicates($params){
		$checkModels = array(
				"GO_Calendar_Model_Event"=>array('name', 'start_time', 'end_time', 'calendar_id', 'rrule', 'user_id'),
				"GO_Tasks_Model_Task"=>array('name', 'start_time', 'due_time', 'tasklist_id', 'rrule', 'user_id'),
				"GO_Addressbook_Model_Contact"=>array('first_name', 'middle_name', 'last_name', 'addressbook_id', 'company_id', 'email'),
				//"GO_Billing_Model_Order"=>array('order_id','book_id','btime')
			);
		
		foreach($checkModels as $modelName=>$checkFields){
			
			echo '<h1>'.$modelName.'</h1>';
			
			$checkFieldsStr = 't.'.implode(', t.',$checkFields);
			$findParams = GO_Base_Db_FindParams::newInstance()
							->ignoreAcl()
							->select('t.id, count(*) AS n, '.$checkFieldsStr)
							->group($checkFields)
							->having('n>1');

			$stmt1 = GO::getModel($modelName)->find($findParams);

			echo '<table border="1">';
			echo '<tr><td>ID</th><th>'.implode('</th><th>',$checkFields).'</th></tr>';

			$count = 0;

			while($dupModel = $stmt1->fetch()){

				$findParams = GO_Base_Db_FindParams::newInstance()
							->ignoreAcl()
							->select('t.id, '.$checkFieldsStr)
							->order('id','ASC');

				$criteria=$findParams->getCriteria();

				foreach($checkFields as $field){
					$criteria->addCondition($field, $dupModel->getAttribute($field));
				}							

				$stmt = GO::getModel($modelName)->find($findParams);

				$first = true;

				while($model = $stmt->fetch()){
					echo '<tr><td>';
					if(!$first)
						echo '<span style="color:red">';
					echo $model->id;
					if(!$first)
						echo '</span>';
					echo '</th>';				

					foreach($checkFields as $field)
					{
						echo '<td>'.$model->getAttribute($field,'html').'</td>';
					}

					echo '</tr>';

					if(!$first){
						if(!empty($params['delete']))
							$model->delete();

						$count++;
					}

					$first=false;
				}
			}

			echo '</table>';

			echo '<p>Found '.$count.' duplicates</p>';
		}
		
		echo '<br /><br /><a href="'.GO::url('maintenance/removeDuplicates', array('delete'=>true)).'">Click here to delete the newest duplicates marked in red.</a>';

	}
	
	/**
	 * Calls buildSearchIndex on each Module class.
	 * @return array 
	 */
	protected function actionBuildSearchCache($params) {
		$response = array();
		
		if(empty($params['keepexisting']))
			GO::getDbConnection()->query('TRUNCATE TABLE go_search_cache');
		if(!headers_sent())
			header('Content-Type: text/plain; charset=UTF-8');
		
		$models=GO::findClasses('model');
		foreach($models as $model){
			if($model->isSubclassOf("GO_Base_Db_ActiveRecord") && !$model->isAbstract()){
				echo "Processing ".$model->getName()."\n";
				flush();
				$stmt = GO::getModel($model->getName())->rebuildSearchCache();			
			}
		}
		
		GO::modules()->callModuleMethod('buildSearchCache', array(&$response));
		
		
		echo "\n\nAll done!\n\n";
	}

	/**
	 * Calls checkDatabase on each Module class.
	 * @return array 
	 */
	protected function actionCheckDatabase($params) {
		$response = array();
		
		GO_Base_Fs_File::$allowDeletes=false;
				
		if(!headers_sent())
			header('Content-Type: text/plain; charset=UTF-8');
		
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
		
		echo "All Done!\n";
		
		GO_Base_Fs_File::$allowDeletes=true;
		
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
			if($model->isSubclassOf('GO_Base_Db_ActiveRecord') && !$model->isAbstract()){
		
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
	
	private function _checkV3(){
		
		if(!GO_Base_Db_Utils::tableExists('go_model_types')){
			
			echo "Older version of Group-Office detected. Preparing database for 4.0 upgrade\n";
		
			$queries[]="TRUNCATE TABLE `go_state`";
			$queries[]="delete from go_settings where name='version'";

			$queries[]="ALTER TABLE `go_users` ADD `mute_reminder_sound` ENUM( '0', '1' ) NOT NULL AFTER `mute_sound` ,
			ADD `mute_new_mail_sound` ENUM( '0', '1' ) NOT NULL AFTER `mute_reminder_sound`";

			$queries[]="ALTER TABLE `go_users` ADD `show_smilies` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `mute_new_mail_sound`";
			$queries[]="ALTER TABLE `go_users` CHANGE `password` `password` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

			foreach($queries as $query){
				try {
					GO::getDbConnection()->query($query);
				} catch (PDOException $e) {
					echo $e->getMessage() . "\n";
				}
			}
			
			echo "Done.\n";
			return true;
		}else
		{
			return false;
		}
		
	}
	
	protected function actionUpgrade($params) {
				
		//don't be strict in upgrade process
		GO::getDbConnection()->query("SET sql_mode=''");
		
		if(php_sapi_name() != 'cli'){
			echo '<pre>';
		}
		
		$v3 = $this->_checkV3();
		
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
		
		
		ob_start("ob_upgrade_log");
		
		
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


		$modules = GO::modules()->getAllModules();
			
		while ($module=array_shift($modules)) {
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
				
				//echo "Getting updates for ".$module."\n";
				
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
							ob_flush();
						}
					}
				}
			}
		}

		if (empty($params['test'])) {
			echo "Database updated to version " . GO::config()->mtime, "\n";
			ob_flush();
			
			GO::config()->save_setting('upgrade_mtime', GO::config()->mtime);
		} else {
			echo "Ran in test mode\n";
		}
		
		echo "Removing cached javascripts...\n\n";		
		GO::clearCache();
		
		if($v3){
			
			if(GO::modules()->isInstalled('projects') && GO::modules()->isInstalled('files')){
				echo "Renaming projects folder temporarily for new project paths\n";
				$folder = GO_Files_Model_Folder::model()->findByPath('projects');
				if($folder){
					$folder->name='oldprojects';
					$folder->systemSave=true;
					$folder->save();
				}
			}
			
			
//			echo "Checking database after version 3.7 upgrade.\n";
//			$this->actionCheckDatabase($params);
//			echo "Done\n\n";
//			ob_flush();
			
			echo "Building search cache after version 3.7 upgrade.\n";
			$this->actionBuildSearchCache($params);
			echo "Done\n\n";
			ob_flush();
		}		
		
		echo "All Done!\n";		
		
		if(php_sapi_name() != 'cli'){
			echo '</pre><br /><br />';
			echo '<a href="'.GO::config()->host.'">'.GO::t('cmdContinue').'</a>';
		}
		//return $response;
	}
}