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
 * @author WilmarVB wilmar@intermesh.nl
 * @property int $id
 * @property string $name
 */

class GO_Comments_Model_Category extends GO_Base_Db_ActiveRecord{

	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName(){
		return 'co_categories';
	}
	
	
	public function relations() {
		return array(
				'comments' => array('type' => self::HAS_MANY, 'model' => 'GO_Comments_Model_Comment', 'field' => 'category_id', 'delete' => true)		);
	}
}
