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
 * 
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $login_path
 * @property string $template
 * @property string $register_user_groups
 * @property string $base_path (Deprecated)
 * @property boolean $ssl
 * @property boolean $mod_rewrite
 * @property int $mtime
 * @property int $ctime
 * @property int $user_id
 * @property string $language
 * @property string $reset_password_path
 * @property string $lost_password_path
 */
class GO_Sites_Model_Site extends GO_Base_Db_ActiveRecord {
	
	public static function model($className=__CLASS__) {	
		return parent::model($className);
	}
	
	/*
	 * Attach the customfield model to this model.
	 */
	public function customfieldsModel() {
		return 'GO_Sites_Customfields_Model_Site';
	}
	
	protected function init() {
		parent::init();
		
		$this->columns['name']['required']=true;
		$this->columns['base_path']['required']=true;
		$this->columns['template']['required']=true;
		$this->columns['language']['required']=true;
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
				'base_path'=>GO::config()->host.'modules/sites',
				'template'=>'Example',
				'language'=>'en',
				'ssl'=>false,
				'mod_rewrite'=>false,
				'login_path'=>'/sites/site/login',
				'register_user_groups '=>'',
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
				'content' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Content', 'field' => 'site_id', 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order('sort_order'),  'delete' => true)
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
	
	public function getBaseUrl(){
		if($this->mod_rewrite){
			$url = $this->ssl ? 'https://' : 'http://';
			$url .= $this->domain.rtrim($this->base_path,'/');
		}  else {
			$url = GO::config()->full_url.'modules/sites/index.php?site_id='.$this->id;
		}
		
		return $url;
	}
}
?>
