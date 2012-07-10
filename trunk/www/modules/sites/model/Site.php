<?php

/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The Active Record of a Site
 *
 * @package GO.modules.sites
 * @copyright Copyright Intermesh
 * @version $Id Site.php 2012-06-27 17:02:53 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Sites_Model_Site extends GO_Base_Db_ActiveRecord {
	
	public static function model($className=__CLASS__) {	
		return parent::model($className);
	}
	
	/**
	 * Get the tablename of this model
	 * @return string The tablename of this model
	 */
	public function tableName() {
		return 'si_sites';
	}
	
	function defaultAttributes() {
		return array(
				'domain'=>$_SERVER['SERVER_NAME'],
				'template'=>'Example',
				'ssl'=>false,
				'mod_rewrite'=>false,
				'login_path'=>'/sites/default/login',
				'register_user_groups '=>'Website Users',
				'user_id'=>GO::user()->id
			);
	}
	
	/**
	 * Get the relations of this model.
	 * 
	 * @return array The relational models 
	 */
	public function relations() {
		return array(
				'content' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Content', 'field' => 'site_id', 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order('sort')->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('parent_id', null)->addCondition('hidden',0)), 'delete' => false),
				'allcontent' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Content', 'field' => 'site_id', 'delete' => true)
			);
	}	
	
	public function getDefaultGroupNames() {
		if (!empty($this->register_user_groups))
			return explode(',',$this->register_user_groups);
		else
			return array();
	}
	
	/**
	 * Function to create the default site users group.
	 */
	private function _createDefaultGroups() {
		foreach($this->getDefaultGroupNames() as $groupName)
		{		
			$group = GO_Base_Model_Group::model()->findSingleByAttribute('name', $groupName);

			if(!$group)
			{
				$group = new GO_Base_Model_Group();
				$group->name = $groupName;
				$group->admin_only = true;
				$group->save();
			}
		}
	}
	
	protected function afterSave($wasNew) {		
		$this->_createDefaultGroups();		
		return parent::afterSave($wasNew);
	}
}
?>
