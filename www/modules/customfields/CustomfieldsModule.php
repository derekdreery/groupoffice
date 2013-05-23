<?php

class GO_Customfields_CustomfieldsModule extends GO_Base_Module {

	public function autoInstall() {
		return true;
	}

	public static function getCustomfieldTypes($extendModel=false) {

		$types = array();

		$modules = GO::modules()->getAllModules();

		while ($module = array_shift($modules)) {
			if ($module->moduleManager) {
				$classes = $module->moduleManager->findClasses('customfieldtype');

				foreach ($classes as $class) {

					if ($class->isSubclassOf('GO_Customfields_Customfieldtype_Abstract') && $class->getName() != 'GO_Customfields_Customfieldtype_TreeselectSlave') {

						$className = $class->getName();
						$t = new $className;
						
						if(!empty($extendModel)){
							
							$supportedModels = $t->supportedModels();
							
							if(empty($supportedModels) || in_array($extendModel, $supportedModels))					
								$types[] = array('className' => $className, 'type' => $t->name());
						
						} else {
							$types[] = array('className' => $className, 'type' => $t->name());
						}
					}
				}
			}
		}
		return $types;
	}

	/**
	 * 
	 * @return GO_Base_Util_ReflectionClass[]
	 */
	public static function getCustomfieldModels() {
		
		$cfModels=array();
		$moduleObjects = GO::modules()->getAllModules();
		foreach ($moduleObjects as $moduleObject) {
			$file = $moduleObject->path . ucfirst($moduleObject->id) . 'Module.php';
			//todo load listeners
			if (file_exists($file)) {
//		require_once($file);
				$class = 'GO_' . ucfirst($moduleObject->id) . '_' . ucfirst($moduleObject->id) . 'Module';

				$object = new $class;
				$models = $object->findClasses("customfields/model");

				foreach ($models as $customFieldModel) {

					if ($customFieldModel->isSubclassOf('GO_Customfields_Model_AbstractCustomFieldsRecord')) {
						$cfModels[]=$customFieldModel;
					}
				}
			}
		}
		
		return $cfModels;
	}

}