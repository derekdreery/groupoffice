<?php

/**
 * Models that extends this must have a 'user_id'. If it has a name attribute it will be copied from the user They will be automatically created and deleted along with a user.
 */
abstract class GO_Base_Model_AbstractUserDefaultModel extends GO_Base_Db_ActiveRecord {

	private static $_allUserDefaultModels;

	public static function getAllUserDefaultModels() {

		if (!isset(self::$_allUserDefaultModels)) {
			self::$_allUserDefaultModels = array();
			$stmt = GO::modules()->getAll();
		
			while ($module=$stmt->fetch()) {
				if($module->moduleManager){
					$classes = $module->moduleManager->findClasses('model');
					foreach($classes as $class){
						if($class->isSubclassOf('GO_Base_Model_AbstractUserDefaultModel')){
							self::$_allUserDefaultModels[] = GO::getModel($class->getName());
						}					
					}
				}
			}
		}
		return self::$_allUserDefaultModels;
	}

	public function settingsModelName() {
		return false;
	}

	public function settingsPkAttribute() {
		return false;
	}

	public function getDefault(GO_Base_Model_User $user) {
		$defaultModel = $this->findSingleByAttribute('user_id', $user->id);
		if (!$defaultModel) {
			$className =$this->className();
			$defaultModel = new $className;
			$defaultModel->user_id = $user->id;
			
			if(isset($this->columns['name'])){
				$defaultModel->name = $user->name;
				$defaultModel->makeAttributeUnique('name');
			}
			
			$defaultModel->save();
		}

		$settingsModelName = $this->settingsModelName();
		if ($settingsModelName) {
			
			$settingsModel = GO::getModel($settingsModelName)->findByPk($user->id);
			if(!$settingsModel){
				$settingsModel = new $settingsModelName;
				$settingsModel->user_id=$user->id;
			}
			
			$settingsModel->{$this->settingsPkAttribute()} = $defaultModel->id;
			$settingsModel->save();
		}

		return $defaultModel;
	}
}