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
 * 
 * @property String $name The name of the category
 * @property int $files_folder_id
 */

 class GO_Notes_Model_Category extends GO_Base_Db_ActiveRecord{
		 
	public $aclField='acl_id';	
	
	public $tableName='no_categories';
	
	protected $relations=array(
				'notes' => array('type'=>self::HAS_MANY, 'model'=>'GO_Notes_Model_Note', 'field'=>'category_id', 'delete'=>true),
				'user' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'user_id')
		);
}