<?php
require('../../www/GO.php');

GO::session()->setCurrentUser(1);

function insertString($string, $insert, $pos){
	
	$firstPart= substr($string,0, $pos);
	$lastPart= substr($string,$pos);
	
	return $firstPart.$insert.$lastPart;
}

function classNameToPackage($className){
	$parts = explode('_',$className);
	
	if($parts[1]!='Base'){
		$package = 'GO.modules.';
	}else
	{
		$package = 'GO.';
	}	
	array_shift($parts);
	array_pop($parts);
	$parts = array_map('strtolower',$parts);
	
	$package .= implode('.', $parts);
	
	return $package;
}


$classes=GO::findClasses('model');
$stmt = GO::modules()->getAll();
while($module = $stmt->fetch()){
	$models = $module->moduleManager->getModels();
	
	$classes = array_merge($classes, $models);
}


foreach($classes as $model){
	if($model->isSubclassOf('GO_Base_Db_ActiveRecord') && !$model->isAbstract()){

		echo "Processing ".$model->getName()."\n";
		
		$instance = GO::getModel($model->getName());
		$columns = $instance->getColumns();
		
		$contents = file_get_contents($model->getFileName());
		
		$lastProperty = strrpos($contents, '@property');
		
		if($lastProperty===false){
			//no property in this model yet. Find comment block			
			preg_match('/class[ ]+'.$model->getName().'/', $contents, $matches, PREG_OFFSET_CAPTURE);
			
			if(!count($matches))
				die('No class definition found in '.$model->getName()."\n");
			
			$offset = $matches[0][1];		
			
			
			
			$commentBlockPos = strrpos(substr($contents,0,$offset), '*/');			
			if($commentBlockPos){
				$insertPos = strrpos(substr($contents,0,$commentBlockPos), "\n");
			}else
			{
				$insertPos = strrpos(substr($contents,0,$offset), "\n");
				
				$insertString = "
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
 * @package ".classNameToPackage($model->getName())."
 * @version \$Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The ".$model->getName()." model
 *
 * @package ".classNameToPackage($model->getName())."
 */
";
								
				$contents = insertString($contents,$insertString,$insertPos);
				
				$insertPos +=strlen($insertString)-5;
			}
			
		}else
		{
			$insertPos = strpos(substr($contents,0,$lastProperty), "\n");
		}
		
		echo "Insert pos: ".$insertPos."\n";
		
		foreach ($columns as $name => $attr) {
			if (preg_match('/@property .*' . $name . '/', $contents)) {
				echo "Property $name found in file\n";
			} else {
				echo "Property $name NOT found in file\n";

				switch ($attr['dbtype']) {
					case 'int':
					case 'tinyint':
					case 'bigint':
						$string = $attr['gotype']=='boolean' ? 'boolean' : 'int';
						break;
					case 'enum(\'0\',\'1\')';
						$string = 'boolean';
						break;
					default:
						$string = 'string';
						break;
				}
				
				$prop = "\n * @property ".$string." $".$name;
				
				$contents = insertString($contents, $prop, $insertPos);				
			}			
		}
		
		echo "\n\n----\n\n";
		echo $contents;
		echo "\n\n----\n\n";		
		
	}
}