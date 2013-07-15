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
 * @property string $language
 * @property type $files_folder_id
 */
class GO_Site_Model_Site extends GO_Base_Db_ActiveRecord {
	
	/**
	 *
	 * @var GO_Site_Components_Config 
	 */
	private $_config;

	private $_treeState;
	
	private $_cf=array();	
	
	private static $fields;
	
	protected function afterLoad() {
		
		$this->_loadFields();
			
		
		return parent::afterLoad();
	}
	
	private function _loadFields(){
		//load cf
		if(!isset(self::$fields)){
			$fields = GO_Customfields_Model_Field::model()->findByModel('GO_Site_Model_Site', false);

			foreach($fields as $field){
				self::$fields[$field->name]= $field;
			}
		}
	}
	
	/**
	 * Site model is cached in the session so we need to reload the static variables
	 * on wake up.
	 */
	public function __wakeup() {
		parent::__wakeup();
		
		$this->_loadFields();
	}
	
	public function __get($name) {
		if(isset(self::$fields[$name])){
			return $this->getCustomFieldValueByName($name);
		}  else {
			return parent::__get($name);
		}

	}

	/*
	 * Attach the customfield model to this model.
	 */
	public function customfieldsModel() {
		return 'GO_Site_Customfields_Model_Site';
	}
	
	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'acl_id';
	}

	public function hasFiles() {
		return true;
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

//	/**
//	 * Get the path to the site's file storage. It is web accessible through an 
//	 * alias /public. This folder contains template files and component assets.
//	 * 
//	 * @return GO_Base_Fs_Folder
//	 */
//	public function getFileStorageFolder(){
//		
//		$folder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'site/'.$this->id.'/');
//		$folder->create();
//		
//		return $folder;
//	}
	
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
							'draggable' => false,
							'site_id'=>$site->id, 
							'iconCls' => 'go-icon-layout', 
							'text' => GO::t('content','site'),
							'expanded' => self::isExpandedNode('site_' . $site->id),
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
			
			$hasChildren = $content->hasChildren();
			
			$treeNodes[] = array(
					'id' => $this->id.'_content_'.$content->id,
					'site_id'=>$this->id,
					'content_id'=>$content->id,
					'iconCls' => 'go-model-icon-GO_Site_Model_Content', 
					//'expanded' => !$hasChildren,
					'expanded' => self::isExpandedNode($this->id.'_content_'.$content->id),
					'hasChildren' => $hasChildren,
					'children'=> $hasChildren ? $content->getChildrenTree() : array(),
					'text' => $content->title
			);
		}
		
		return $treeNodes;
	}
	
	public function getApacheConfig(){
		return '
<VirtualHost *:80>

<Directory /var/www/groupoffice-4.1/www/modules/site>
 Options FollowSymLinks
 AllowOverride All

				RewriteEngine On
				RewriteBase /

				RewriteCond %{REQUEST_FILENAME} !-f
				RewriteCond %{REQUEST_FILENAME} !-d
				RewriteRule ^(.*)\?*$ index.php/$1 [L,QSA]
</Directory>

DocumentRoot /var/www/groupoffice-4.1/www/modules/site
Alias /public /home/groupoffice/site/1/public
ServerName www.giralisgroep.nl
#ErrorLog /var/log/apache2/giralis.nl.log
</VirtualHost>
		';
	}
	
	 public function getCustomFieldValueByName($cfName) {

		if (!key_exists($cfName, $this->_cf)) {

//			$column = $this->getCustomfieldsRecord()->getColumn(self::$fields[$cfName]->columnName());
//			if(!$column)
//				return null;

			$value = $this->getCustomfieldsRecord()->{self::$fields[$cfName]->columnName()};

			$this->_cf[$cfName] = $value;
		}

		return $this->_cf[$cfName];
	}

	public static function isExpandedNode($nodeId) {
		$state = GO::config()->get_setting("site_tree_state", GO::user()->id);

		if (empty($state)) {
			$decoded = base64_decode($nodeId);

			if (stristr($decoded, 'root') || stristr($decoded, 'content'))
				return true;
			else
				return false;
		}

		$treeState = json_decode($state);
		

		return in_array($nodeId, $treeState);
	}
	
	public function buildFilesPath() {
		return 'public/site/'.$this->id.'/files';
	}
	
	public function getPublicUrl(){
		return '/public/site/'.$this->id.'/';
	}
	
	public function getPublicPath(){
		return GO::config()->file_storage_path.'public/site/'.$this->id.'/';
	}

}