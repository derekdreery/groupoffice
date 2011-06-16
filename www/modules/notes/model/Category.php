<?php
 class GO_Notes_Model_Category extends GO_Base_Db_ActiveRecord{
		 
	public $aclField='acl_id';
	
	public $tableName='no_categories';

	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_STR),
		'user_id'=>array('type'=>PDO::PARAM_INT),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'acl_id'=>array('type'=>PDO::PARAM_INT)
	);
}