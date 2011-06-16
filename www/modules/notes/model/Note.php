<?php


class GO_Notes_Model_Note extends GO_Base_Db_ActiveRecord{
	
	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_INT),
		'category_id'=>array('type'=>PDO::PARAM_INT),
		'user_id'=>array('type'=>PDO::PARAM_INT),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'content'=>array('type'=>PDO::PARAM_STR,'gotype'=>'textarea'),
		'ctime'=>array('type'=>PDO::PARAM_INT,'gotype'=>'unixtimestamp'),
		'mtime'=>array('type'=>PDO::PARAM_INT,'gotype'=>'unixtimestamp'),
	);
	
	protected $link_type=4;
	
	/**
	 * 
	 * @var string The database table name
	 */
	
	public $tableName='no_notes';
	
	/*
	 * Points to a relation here
	 */
	public $aclField='category.acl_id';	
	
	protected $relations=array(
				'category' => array(self::BELONGS_TO, 'GO_Notes_Model_Category', 'category_id')
		);

}