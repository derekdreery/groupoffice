<?php
class GO_Base_Model_SearchCacheRecord extends GO_Base_Db_ActiveRecord{
	
	public $aclField='acl_id';

	public $tableName="go_search_cache";
	
	public $primaryKey=array('id','link_type');

	protected $_columns=array(
		'user_id'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'id'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'module'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'name'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>100, 'gotype'=>'textfield'),
		'description'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>255, 'gotype'=>'textfield'),
		//'url'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>255, 'gotype'=>'textfield'),
		'link_type'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'type'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>20, 'gotype'=>'textfield'),
		'keywords'=>array('type'=>PDO::PARAM_STR, 'required'=>false, 'gotype'=>'textarea'),
		'mtime'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'acl_id'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		//'link_count'=>array('type'=>PDO::PARAM_INT, 'required'=>false),		
	);	
}
