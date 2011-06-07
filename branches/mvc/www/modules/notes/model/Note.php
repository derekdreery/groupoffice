<?php
class GO_Notes_Model_Note extends GO_ActiveRecord{
	
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