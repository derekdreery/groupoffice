<?php

function generateModel($className, $table, $moduleName){
	require('../../www/Group-Office.php');
	$conn = GO::getDbConnection();
	
	$sql = "SHOW FIELDS FROM `".$table."`;";

	$stmt = $conn->query($sql);

	$props='';

	while($field = $stmt->fetch()){
		preg_match('/([a-zA-Z].*)\(([1-9].*)\)/',$field['Type'], $matches);
		if($matches){
			$length = $matches[2];
			$type = $matches[1];
		}else
		{
			$type = $field['Type'];
			$length=0;
		}

		$pdoType = $type;

		switch($type){
			case 'int':
			case 'tinyint':
			case 'bigint':
				$pdoType = "int";
			break;	

			case 'varchar':
			case 'char':
			case 'text':
				$pdoType='String';
			break;

			case 'enum(\'0\',\'1\')';
				$pdoType='boolean';
			break;
		}	

		$props .= " * @property ".$pdoType." $".$field['Field'];
		$props .= "\n";
	}
	$props .= " */";
	rtrim($props,',');

$date = date('Y-m-d H:i:s');
$className = 'GO_'.$moduleName.'_Model_'.$className;

	
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
'.$props.'

class '.$className.' extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return '.$className.'
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}


	/**
	 * Enable this function if you want this model to check the acl\'s automatically.
	 */
	// public function aclField(){
	//	 return \'acl_id\';	
	// }

	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return \''.$table.'\';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
}
').'</pre>';
}
