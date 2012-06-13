<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model
 */

/**
 * Models that extends this must have a 'user_id'. If it has a name attribute it
 * will be copied from the user They will be automatically created and deleted 
 * along with a user.
 * 
 * @package GO.base.model
 */
abstract class GO_Base_Model_AbstractUserDefaultModel extends GO_Base_Db_ActiveRecord {

	private static $_allUserDefaultModels;

	/**
	 * Get all models that should exist by default for a user.
	 * 
	 * @param int $user_id
	 * @return GO_Base_Db_ActiveRecord 
	 */
	public static function getAllUserDefaultModels($user_id=0) {
		
		$oldIgnoreAcl = GO::setIgnoreAclPermissions(true);

		if (!isset(self::$_allUserDefaultModels)) {
			self::$_allUserDefaultModels = array();
			$modules = GO::modules()->getAllModules();
			
			while ($module=array_shift($modules)) {
			  $permissionLevel=$user_id ? GO_Base_Model_Acl::getUserPermissionLevel($module->acl_id, $user_id) : 1;
				if($permissionLevel){
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
		}
		GO::setIgnoreAclPermissions($oldIgnoreAcl);
		return self::$_allUserDefaultModels;
	}
	
	/**
	 * Return a model to store the default in. Eg. The default tasklist created by
	 * getDefault should be stored in GO_Tasks_Model_Settings->default_taskkist_id
	 * 
	 * @return string 
	 */
	public function settingsModelName() {
		return false;
	}

	/**
	 * Return a settings attribute name. Eg. The default tasklist created by
	 * getDefault should be stored in GO_Tasks_Model_Settings->default_taskkist_id
	 * 
	 * @return string 
	 */
	public function settingsPkAttribute() {
		return false;
	}

	/**
	 * Creates a default model for the user. 
	 * 
	 * This function is automaticall called in afterSave of the user model and
	 * after a module is installed.
	 * 
	 * @param GO_Base_Model_User $user
	 * @return GO_Base_Model_AbstractUserDefaultModel 
	 */
	public function getDefault(GO_Base_Model_User $user) {	
		
			
		if(!$user)
			return false;
		
		$settingsModelName = $this->settingsModelName();
		if ($settingsModelName) {
			
			$settingsModel = GO::getModel($settingsModelName)->findByPk($user->id);
			if(!$settingsModel){
				$settingsModel = new $settingsModelName;
				$settingsModel->user_id=$user->id;
			}else
			{
				$pk = $settingsModel->{$this->settingsPkAttribute()};
				$defaultModel = $this->findByPk($pk);
				if($defaultModel)
					return $defaultModel;
			}
		}
		
		
		$defaultModel = $this->findSingleByAttribute('user_id', $user->id);
		if (!$defaultModel) {
			$className =$this->className();
			$defaultModel = new $className;
			$defaultModel->user_id = $user->id;
			
			if(isset($this->columns['name'])){
//				$defaultModel->name = $user->name;
//				$defaultModel->makeAttributeUnique('name');
				$this->setDefaultAttributes($defaultModel, $user);
			}
			
			//any user may do this.
			$oldIgnore = GO::setIgnoreAclPermissions(true);		
			$defaultModel->save();			
			GO::setIgnoreAclPermissions($oldIgnore);
		}

		if ($settingsModelName) {
			$settingsModel->{$this->settingsPkAttribute()} = $defaultModel->id;
			$settingsModel->save();
		}

		return $defaultModel;
	}
	
	public static function getNameTemplate($className){
		$template = GO::config()->get_setting("name_template_".$className);
		if(!$template)
			$template = "{first_name} {middle_name} {last_name}";
		
		return $template;
	}
	
	public static function setNameTemplate($className,$templateString,$findModelsToChangeParams=false,$remoteForeignKey=false){
		GO::config()->save_setting("name_template_".$className,$templateString);
		if ($findModelsToChangeParams instanceof GO_Base_Db_FindParams && $remoteForeignKey!==false) {
			$model = GO_Base_Db_ActiveRecord::model($className);
			$stmt = $model->find($findModelsToChangeParams->ignoreAcl());
			while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$updateModel = new $className();
				$updateModel = $updateModel->findByPk($record[$remoteForeignKey]);
				$updateModel->id = $record[$remoteForeignKey];
				$updateModel->name = GO_Base_Util_String::reformat_name_template($templateString,$record);
				$updateModel->save();
			}
		}

	}
	
	protected function setDefaultAttributes(GO_Base_Db_ActiveRecord $defaultModel, GO_Base_Model_User $user){
		
		//TODO Get templates for this name		
		//$template = "{first_name} {middle_name} {last_name}";
		
		$template = self::getNameTemplate($this->className());
		
		$defaultModel->name = $this->parseUserTemplate($template, $user->getAttributes());
		$defaultModel->makeAttributeUnique('name');
	}
	
	private function parseUserTemplate($template, $attributes)
	{
		foreach($attributes as $key=>$value){
			if(is_string($value))
				$template = str_replace("{".$key."}", $value, $template);
		}

		$template = trim(preg_replace('/\s+/', ' ',$template));
		$template = str_replace(array('()','[]'),'', $template);

		return $template;
	}
}