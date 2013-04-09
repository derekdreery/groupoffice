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
 * @version $Id: GO_Site_Model_Content.php 7607 2013-03-27 15:36:16Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */
 
/**
 * The GO_Site_Model_Content model
 *
 * @package GO.modules.Site
 * @version $Id: GO_Site_Model_Content.php 7607 2013-03-27 15:36:16Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 *
 * @property int $id
 * @property int $user_id
 * @property int $ctime
 * @property int $mtime
 * @property String $title
 * @property String $slug
 * @property String $meta_title
 * @property String $meta_description
 * @property String $meta_keywords
 * @property String $content
 * @property int $status
 * @property int $parent_id
 * @property int $site_id
 * @property int $sort_order
 */

class GO_Site_Model_Content extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Site_Model_Content
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	/*
	 * Attach the customfield model to this model.
	 */
	public function customfieldsModel() {
		return 'GO_Site_Customfields_Model_Content';
	}
	
//	protected function init() {
//		$this->columns['slug']['unique'] = array('site_id');
//		parent::init();
//	}
	
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
		 return 'site_content';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array(
			'children' => array('type' => self::HAS_MANY, 'model' => 'GO_Site_Model_Content', 'field' => 'parent_id', 'delete' => true, GO_Base_Db_FindParams::newInstance()->order('sort_order')),
			
			'site'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Site_Model_Site", 'field'=>'site_id'),
			'parent'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Site_Model_Content", 'field'=>'parent_id')
		 );
	 }
	 
	 	 
	 protected function beforeSave() {
		 
		 $this->cleanSlug();
		 
		 return parent::beforeSave();
	 }
	 
	 /**
	  * Find a content item by it's slug (and siteId)
	  * 
	  * @param string $slug
	  * @param int $siteId
	  * @return GO_Site_Model_Content
	  * @throws GO_Base_Exception_NotFound
	  */
	 public static function findBySlug($slug, $siteId=false){
		 
		 if(!$siteId)
			$model = self::model()->findSingleByAttribute('slug', $slug);
		 else
			$model = self::model()->findSingleByAttributes(array('slug'=>$slug,'site_id'=>$siteId));
		 
		 if(!$model)
			 Throw new GO_Base_Exception_NotFound('There is no page found with the slug: '.$slug);
		 
		 return $model;
	 }
	 
	 /**
	  * Get the slug of the parent to suggest
	  * 
	  * @return string
	  */
	 public function getParentSlug(){
		 
		 if($this->parent)
			 return $this->parent->slug;
		 else
			 return '';
	 }
	 
	 public function cleanSlug(){
		 //TODO: Clean the slug
	 }
	 
	 /**
	  * Get the url to this content item.
	  * 
	  * @param string $route parameter can be set when you have "special" 
	  * controller actions to handle your content
	  * @return string
	  */
	 public function getUrl($route='site/front/content'){
		 return Site::urlManager()->createUrl($route,array('slug'=>$this->slug));
	 }
	 
	 /**
	  * Check if this content item has children
	  * 
	  * @return boolean
	  */
	 public function hasChildren(){
		 $child = $this->children(GO_Base_Db_FindParams::newInstance()->single());
		 return !empty($child); 
	 }
	 
	 /**
	  * # Backend Functionality
	  * 
	  * Get the tree array for the children of the current item
	  * 
	  * @return array
	  */
	 public function getChildrenTree(){
		 $tree = array();
		 $children = $this->children;
		 		 	 
		 foreach($children as $child){
			 
			 $hasChildren = $child->hasChildren();
			 
			 $childNode = array(
				'id' => $child->site_id.'_content_'.$child->id,
				'content_id'=>$child->id,
				'site_id'=>$child->site->id, 
				'iconCls' => 'go-model-icon-GO_Site_Model_Content', 
				'text' => $child->title,
				'hasChildren' => $hasChildren,
				'expanded' => !$hasChildren,
				'children' => $children
			);
			 
			$tree[] = $childNode;
		 }
		 
		 return $tree;
	 }	 
}