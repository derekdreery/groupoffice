<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */
 
/**
 * The GO_Users_Model_CfSettingTab model
 *
 * @package GO.modules.Users
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 *
 * @property int $cf_category_id
 */

class GO_Users_Model_CfSettingTab extends \GO\Base\Db\ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Ads_Model_Format
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}


	public function primaryKey() {
		return 'cf_category_id';
	}
	
	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return 'go_cf_setting_tabs';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
	 
	 /**
	  * Get an activestatement witch includes all the customfieldCategories that 
	  * will be showed in the settings tab.
	  * 
	  * @return \GO\Base\Db\ActiveStatement
	  */
	 public function getSettingTabs(){
		 		 
		 $findParams = \GO\Base\Db\FindParams::newInstance()
						 ->ignoreAcl()
						 ->joinModel(array(
								'model'=>'GO_Users_Model_CfSettingTab',									
								'localTableAlias'=>'t', //defaults to "t"
								'localField'=>'id', //defaults to "id"			
								'foreignField'=>'cf_category_id', //defaults to primary key of the remote model
								'tableAlias'=>'cfs', //Optional table alias					
								'type'=>'INNER' //defaults to INNER,
						 ))
						 ->criteria(\GO\Base\Db\FindCriteria::newInstance()->addCondition('extends_model', "GO_Addressbook_Model_Contact"))
						 ->order('sort_index');
		 
		 $stmt = GO_Customfields_Model_Category::model()->find($findParams);
		 return $stmt;
	 }
	 
	 
	 
	 
}