<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * 
 * The Category model
 */

 class GO_Notes_Model_Category extends GO_Base_Db_ActiveRecord{
		 
	public $aclField='acl_id';
	
	public $tableName='no_categories';
	
	protected $relations=array(
				'notes' => array(self::HAS_MANY, 'GO_Notes_Model_Note', 'category_id'),
				'user' => array(self::BELONGS_TO, 'GO_Base_Model_User', 'user_id')
		);

	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_INT),
		'user_id'=>array('type'=>PDO::PARAM_INT),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'acl_id'=>array('type'=>PDO::PARAM_INT)
	);
	
	public function getFilesFolder(){
		$path = 'notes/'.File::strip_invalid_chars($this->name);
		
		require_once(GO::modules()->modules['files']['class_path'].'files.class.inc.php');
		$files = new files();			

		if($folder=$files->create_unique_folder($path))
		{
			return $folder['id'];
		}
	}
}