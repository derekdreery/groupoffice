<?php
require('../www/Group-Office.php');

$conn = GO::getDbConnection();

$table = $argv[1];

$sql = "SHOW FIELDS FROM `".$table."`;";

$stmt = $conn->query($sql);

$cols='';

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
	
	$gotype='textfield';
	
	$pdoType = "PDO::PARAM_STR";
	switch($type){
		case 'int':
		case 'tinyint':
		case 'bigint':
			$pdoType = "PDO::PARAM_INT";
			$length=0;
			$gotype='';
		break;	
	
		case 'text':
			$gotype='textarea';
			break;
	}	
	
	$cols .= "id'=>array('type'=>$pdoType, 'required'=>false";
	
	if($length)
		$cols .= ",'length'=>$length";
	
	if(!empty($gotype))
		$cols .= ", 'gotype'=>'$gotype'";
	
	$cols .= "),\n\t\t";
}

rtrim($cols,',');


echo '<?php
class ReplaceMe extends GO_Base_Db_ActiveRecord{

	protected $aclField=false;

	public $tableName="'.$table.'";

	protected $_columns=array(
		'.$cols.'
	);	
}';
				



