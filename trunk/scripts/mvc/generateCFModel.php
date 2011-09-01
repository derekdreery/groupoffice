<?php

function generateCFModel($extendclassName, $moduleName){

$date = date('Y-m-d H:i:s');
$className = 'GO_'.$moduleName.'_Model_'.$extendclassName.'CustomFieldsRecord';
$extendclassName = 'GO_'.$moduleName.'_Model_'.$extendclassName;
	
echo '<pre>'.htmlspecialchars('<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: '.$className.'.php 7607 '.$date.'Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */  

/**
 * The '.$className.' model
 *
 */

class '.$className.' extends GO_Customfields_Model_AbstractCustomFieldsRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Notes_Model_CustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function extendsModel(){
		return "'.$extendclassName.'";
	}
}
').'</pre>';
}
