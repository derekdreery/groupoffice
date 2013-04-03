<?php

/**
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @package GO.modules.Site
 * @version $Id: GO_Site_Model_Site.php 7607 2013-03-27 15:35:31Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */

/**
 * The GO_Site_Model_Site model
 *
 * @package GO.modules.Site
 * @version $Id: GO_Site_Model_Site.php 7607 2013-03-27 15:35:31Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 *
 * @property int $id
 * @property String $name
 * @property int $user_id
 * @property int $mtime
 * @property int $ctime
 * @property String $domain
 * @property String $module
 * @property int $ssl
 * @property int $mod_rewrite
 * @property String $mod_rewrite_base_path
 * @property String $base_path
 * @property int $acl_id
 */
class GO_Site_Model_Site extends GO_Base_Db_ActiveRecord {
	
	/**
	 *
	 * @var GO_Site_Components_Config 
	 */
	private $_config;

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Site_Model_Site
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'acl_id';
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'site_sites';
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
			'content' => array('type' => self::HAS_MANY, 'model' => 'GO_Site_Model_Content', 'field' => 'site_id', 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order('sort_order'),  'delete' => true),
			'contentNodes' => array('type' => self::HAS_MANY, 'model' => 'GO_Site_Model_Content', 'field' => 'site_id', 'findParams'=> GO_Base_Db_FindParams::newInstance()->order('sort_order')->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('parent_id', null)),  'delete' => true)
		);
	}

	/**
	 * Get the config parameters of the site.
	 * 
	 * @return GO_Site_Components_Config
	 */
	public function getConfig(){
		if(!isset($this->_config))
		{
			$this->_config = new GO_Site_Components_Config($this);
		}
		
		return $this->_config;
	}
	
	/**
	 * Return the module that handles the view of the site.
	 * 
	 * @return GO_Base_Model_Module
	 * @throws Exception
	 */
	public function getSiteModule(){
		
		$module = GO::modules()->isInstalled($this->module);		
		
		if(!$module)
			throw new Exception("Module ".$this->module." not found!");
		
		return $module;
	}
	
	public static function getTreeNodes(){
		
		$tree = array();
		$findParams = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl();
		
		$sites = self::model()->find($findParams);
		
		foreach($sites as $site){

			// Site node
			$siteNode = array(
				'id' => 'site_' . $site->id,
				'site_id'=>$site->id, 
				'iconCls' => 'go-model-icon-GO_Site_Model_Site', 
				'text' => $site->name, 
				'expanded' => true,
				'children' => array(
						array(
							'id' => $site->id.'_content',
							'site_id'=>$site->id, 
							'iconCls' => 'go-icon-layout', 
							'text' => GO::t('content','site'),
							'expanded' => true,
							'children' => $site->loadContentNodes()
						)
					)
			);

			$tree[] = $siteNode;
		}
		
		return $tree;
	}
	
	public function loadContentNodes(){
		$treeNodes = array();
		
		$contentItems = $this->contentNodes;
			
		foreach($contentItems as $content){
			$treeNodes[] = array(
					'id' => $this->id.'_content_'.$content->id,
					'site_id'=>$this->id,
					'content_id'=>$content->id,
					'iconCls' => 'go-model-icon-GO_Site_Model_Content', 
					'text' => $content->title
			);
		}
		
		return $treeNodes;
	}
}