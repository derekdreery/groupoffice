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
 * @property int $ptime
 * @property string $default_child_template
 */

class GO_Site_Model_Content extends GO_Base_Db_ActiveRecord{

	private $_cf=array();	
	
	private static $fields;
	
	public $parentslug;
	public $baseslug;
	
	protected function afterLoad() {
		
		//load cf
		if(!isset(self::$fields)){
			$fields = GO_Customfields_Model_Field::model()->findByModel('GO_Site_Model_Content', false);

			foreach($fields as $field){
				self::$fields[$field->name]= $field;
			}
		}
		
	
		$this->_loadSlug();
		
		return parent::afterLoad();
	}
	
//	protected function afterCreate() {
//		$this->_loadSlug();
//		return parent::afterCreate();
//	}
	
	private function _loadSlug(){
		
		if($this->isNew && $this->parent){
			$this->parentslug=$this->parent->slug.'/';
			$this->baseslug="";
		}  else {
			
		
			if(($pos = strrpos($this->slug, "/"))){
				$this->parentslug=substr($this->slug,0, $pos+1);
			}else {
				$this->parentslug="";
			}
		
			$this->baseslug=basename($this->slug);
		}
	}
	
	public function __get($name) {
		if(isset(self::$fields[$name])){
			return $this->getCustomFieldValueByName($name);
		}  else {
			return parent::__get($name);
		}

	}
	
	public function __isset($name) {
		if(isset(self::$fields[$name])){
			$var= $this->getCustomFieldValueByName($name);
			return isset($var);
		}  else {
			return parent::__get($name);
		}
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
			'children' => array('type' => self::HAS_MANY, 'model' => 'GO_Site_Model_Content', 'field' => 'parent_id', 'delete' => true, 'findParams' =>GO_Base_Db_FindParams::newInstance()->select('*')->order(array('sort_order','ptime'))),
			'site'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Site_Model_Site", 'field'=>'site_id'),
			'parent'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Site_Model_Content", 'field'=>'parent_id','findParams' =>GO_Base_Db_FindParams::newInstance()->select('*'))
		 );
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
			$siteId = Site::model()->id;
		 
//		 if(!$siteId)
//			$model = self::model()->findSingleByAttribute('slug', $slug);
//		 else
			$model = self::model()->findSingleByAttributes(array('slug'=>$slug,'site_id'=>$siteId));
		 
		 if(!$model)
			 return false;
			 //Throw new GO_Base_Exception_NotFound('There is no page found with the slug: '.$slug);
		 
		 return $model;
	 }
	 
	 /**
	  * Get the url to this content item.
	  * 
	  * @param string $route parameter can be set when you have "special" 
	  * controller actions to handle your content
	  * @return string
	  */
	 public function getUrl($route='site/front/content'){
		 
		// var_dump($this->slug);
		 
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
	  * Check if the given content model is an ancestor of this content model
	  * 
	  * @param GO_Site_Model_Content $parent
	  * @return boolean
	  */
	 public function isChildOf(GO_Site_Model_Content $parent){
		 return strpos($this->slug, $parent->slug)===0;
	 }
	 
	 /**
	  * Check if this contentitem has a parent
	  * 
	  * @return boolean
	  */
	 public function hasParent(){
		 return !empty($this->parent_id);
	 }
	 
	 public function setDefaultTemplate() {
		 if(empty($this->template) && !empty($this->parent->default_child_template)){
			$this->template = $this->parent->default_child_template;
		 }else{
			$config = new GO_Site_Components_Config($this->site);
			$this->template = $config->getDefaultTemplate();
		 }
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
				'slug'=>$child->slug,
				'cls' => 'site-node-content',
				'iconCls' => 'go-model-icon-GO_Site_Model_Content', 
				'text' => $child->title,
				'hasChildren' => $hasChildren,
				//'expanded' => !$hasChildren,
				'expanded' => !$hasChildren || GO_Site_Model_Site::isExpandedNode($child->site_id.'_content_'.$child->id),	 
				'children'=> $hasChildren ? null : array(),
			);
			 
			$tree[] = $childNode;
		 }
		 
		 return $tree;
	 }
	 
	 public function getCustomFieldValueByName($cfName){
		 
		if(!key_exists($cfName, $this->_cf)){
			
//			$column = $this->getCustomfieldsRecord()->getColumn(self::$fields[$cfName]->columnName());
//			if(!$column)
//				return null;

			$value = $this->getCustomfieldsRecord()->{self::$fields[$cfName]->columnName()};

			$this->_cf[$cfName]=$value;

		}

		return $this->_cf[$cfName];
	 }
	 
	 public function beforeValidate() {
		 parent::beforeValidate();
		 if(empty($this->ptime)){
			 $this->ptime = time();
		 }
		 
	 }
	 
	 
	 /**
	  * Get a short text from this contentitem
	  * 
	  * @param int $length
	  * @param boolean $cutwords
	  * @param string $append
	  * @return string
	  */
	 public function getShortText($length=100,$cutwords=false,$append='...'){
		 
		 $text = GO_Base_Util_String::html_to_text($this->content);
		 $text = GO_Base_Util_String::cut_string($text,$length,!$cutwords,$append);
		 
		 return $text;
	 }
	 
	 protected function afterSave($wasNew) {
		 
		 if($this->isModified('slug') && $this->hasChildren()){
			 foreach($this->children as $child){
					$slugArray = explode('/',$child->slug);
					$ownSlug = array_pop($slugArray);

					$child->slug = $child->parent->slug.'/'.$ownSlug;
					$child->save();
			 }
		 }
		
		 return parent::afterSave($wasNew);
	 }
	 
	 public function setAttribute($name, $value, $format = false) {
		 
		 parent::setAttribute($name, $value, $format);
		 
		 if($name=='parent_id'){
			 $this->_loadSlug();
		 }
		 
	 }
	 
	 
	 protected function beforeSave() {
		 
		 // This check is needed to set the correct slug when the item is dragged/dropped to another parent
//		 if($this->isModified('parent_id') && !$this->isNew){
			 
			 if(!empty($this->parent_id))
				$this->slug = $this->parent->slug.'/'.$this->baseslug;
			 else
				$this->slug = $this->baseslug;
//		 }
		 
		 return parent::beforeSave();
	 }
	 
	 public function getHtml(){
		 return self::replaceContentTags($this->content);
	 }
	 
	 public static function replaceContentTags($content=''){

		 $links = GO_Base_Util_TagParser::getTags('site:link', $content);
		 
		 foreach($links as $link){
			 $template = self::processLink($link['params'],$link['xml']);
			 $content = str_replace($link['xml'], $template, $content);
		 }
		 
		 $images = GO_Base_Util_TagParser::getTags('site:img', $content);
		 
		 foreach($images as $image){
			 $template = self::processImage($image['params']);
			 $content = str_replace($image['xml'], $template, $content);
		 }

		 return $content;
	 }
	 
	 public static function processLink($linkAttr, $completeXml) {

		$html = '<a';

		switch ($linkAttr['linktype']) {
			case 'content':
				// $linkAttr['contentid'] = '1';

				if (empty($linkAttr['contentid']))
					$linkAttr['contentid'] = '0';

				$content = GO_Site_Model_Content::model()->findByPk((int) $linkAttr['contentid']);

				if ($content)
					$url = $content->url;
				else
					$url = '#';

				$html .= ' href="' . $url . '"';
				break;
			case 'file':
				// $linkAttr['path'] = 'public/site/1/files/1/contract.png';

				if (empty($linkAttr['path']))
					$linkAttr['path'] = '#';

				$html .= ' href="' . Site::file($linkAttr['path'], false) . '"';
				break;
			case 'manual':
				// $linkAttr['url'] = 'www.google.nl';

				if (empty($linkAttr['url']))
					$linkAttr['url'] = '#';

				$html .= ' href="' . $linkAttr['url'] . '"';
				break;
		}

		if (isset($linkAttr['target']))
			$html .= ' target="_blank"';

		if (!empty($linkAttr['title']))
			$html .= ' title="' . $linkAttr['title'] . '"';

		$html .= '>';

		preg_match('/(<a[^>]*>)(.*?)(<\/a>)/i', $completeXml, $matches);

		if (isset($matches[2]))
			$html .= $matches[2];
		else
			$html .= 'LINK';

		$html .= '</a>';

		return $html;
	}
	 
	 public static function processImage($imageAttr){
		 
		//		if(key_exists('width', $imageAttr)){}
		//		if(key_exists('height', $imageAttr)){}
		//		if(key_exists('zoom', $imageAttr)){}
		//		if(key_exists('crop', $imageAttr)){}
		//		if(key_exists('alt', $imageAttr)){}
		//		if(key_exists('a', $imageAttr)){}
		//		if(key_exists('path', $imageAttr)){}
		//		if(key_exists('align', $imageAttr)){}		 
		 
		$html = '';
		
		if(key_exists('path', $imageAttr)){
			if( key_exists('width', $imageAttr) && key_exists('height', $imageAttr)){
				if(key_exists('crop', $imageAttr))
					$thumb = Site::thumb($imageAttr['path'],array("lw"=>$imageAttr['width'], "ph"=>$imageAttr['height'], "zc"=>1));
				else
					$thumb = Site::thumb($imageAttr['path'],array("lw"=>$imageAttr['width'], "ph"=>$imageAttr['height'], "zc"=>0));
				if(isset($imageAttr['link_to_original']))
					$imageAttr['href'] = Site::file($imageAttr['path']); // Create an url to the original image
				
			} else {
				$thumb = Site::file($imageAttr['path']);
			}
			
			$html .= '<img src="'.$thumb.'"';
			
			if(key_exists('alt', $imageAttr))
				$html .= ' alt="'.$imageAttr['alt'].'"';
			
			if(key_exists('align', $imageAttr))
				$html .= ' style="'.$imageAttr['align'].'"';
			else
				$html .= ' style="display:inline-block;"';
			
			$html .= ' />';
			
			if(key_exists('href', $imageAttr)){
				$target='';
				if(isset($imageAttr['target'])){
					$target = ' target="'.$imageAttr['target'].'"';
				}
				
			 $html = sprintf('<a href="%s"'.$target.'>%s</a>',$imageAttr['href'],$html);
			}
		}
		 return $html;
	 }
	 
	 /**
	  * Get the meta title of this content item.
	  * 
	  * @return string
	  */
	 public function getMetaTitle(){
		 if(!empty($this->meta_title))
			 return $this->meta_title;
		else
			return $this->title;
	 }
	 
	  public static function setTreeSort($extractedParent,$sortOrder,$allowedTypes){
		 
		 $sort = 0;
		 
		 foreach($sortOrder as $sortItem){
			 
			 $extrChild = GO_Site_SiteModule::extractTreeNode($sortItem);
			 
			 if(in_array($extrChild['type'],$allowedTypes)){
				 
				 $modelName = GO_Site_SiteModule::getModelNameFromTreeNodeType($extrChild['type']);
				 
				 $model = $modelName::model()->findByPk($extrChild['modelId']);
				 $model->parent_id = !empty($extractedParent['modelId'])?$extractedParent['modelId']:NULL;
				 $model->sort_order = $sort;
				 if($model->save())				 
					$sort++;
			 }
		 }
		 
		 return array("success"=>true);
	 }
	 
}