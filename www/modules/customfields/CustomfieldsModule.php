<?php
class GO_Customfields_CustomfieldsModule extends GO_Base_Module{	
	public function autoInstall() {
		return true;
	}
	
	
	public static function getCustomfieldTypes() {

		$types = array();

		$modules = GO::modules()->getAllModules();
			
		while ($module=array_shift($modules)) {
			if ($module->moduleManager) {
				$classes = $module->moduleManager->findClasses('customfieldtype');

				foreach ($classes as $class) {

					if ($class->isSubclassOf('GO_Customfields_Customfieldtype_Abstract') && $class->getName()!='GO_Customfields_Customfieldtype_TreeselectSlave') {

						$className = $class->getName();
						$t = new $className;
						$types[] = array('className' => $className, 'type' => $t->name());
					}
				}
			}
		}
		return $types;
	}

}