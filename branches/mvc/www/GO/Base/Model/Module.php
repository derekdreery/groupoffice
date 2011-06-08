<?php
 class GO_Base_Model_Module extends GO_Base_Db_ActiveRecord{
	 
	protected $primaryKey='name';
	
	protected $aclField='acl_id';
	
	protected $tableName='go_modules';

	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_STR),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'version'=>array('type'=>PDO::PARAM_INT),
		'sort_order'=>array('type'=>PDO::PARAM_INT),
		'acl_id'=>array('type'=>PDO::PARAM_INT)
	);	
}