<?php
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
 * @package GO.modules.comments.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Comments_Model_Comment model
 *
 * @package GO.modules.comments.model
 * @property string $comments
 * @property int $mtime
 * @property int $ctime
 * @property int $user_id
 * @property int $model_type_id
 * @property int $model_id
 * @property int $id
 * @property int $category_id
 */

class GO_Comments_Model_Comment extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Comments_Model_Comment 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {
		$this->columns['model_id']['required']=true;
		$this->columns['model_type_id']['required']=true;
		$this->columns['category_id']['required']=GO_Comments_CommentsModule::commentsRequired();
		
		return parent::init();
	}


	public function tableName(){
		return 'co_comments';
	}
	
	public function relations(){
		return array(	
			'category' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Comments_Model_Category', 'field'=>'category_id'),		);
	}
	
	public function getAttachedObject(){
		
		$modelType = GO_Base_Model_ModelType::model()->findByPk($this->model_type_id);
		
		if($modelType){
			$obj = GO::getModel($modelType->model_name)->findByPk($this->model_id);
			
			if($obj)
				return $obj;
		}
		
		return false;
	}
	
	
}
