<?php


class GO_Notes_Model_Note extends GO_Base_Db_ActiveRecord{
	
	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_INT),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'content'=>array('type'=>PDO::PARAM_STR),
		'ctime'=>array('type'=>PDO::PARAM_INT),
		'mtime'=>array('type'=>PDO::PARAM_INT),
	);
	
	protected $link_type=4;
	
	/**
	 * 
	 * @var string The database table name
	 */
	
	protected $tableName='no_notes';
	
	
	
	
	public function relations()
	{
//		return array(
//				'category' => array(self::BELONGS_TO, 'Category', 'category_id')
//		);
		
		return array();
	}

}