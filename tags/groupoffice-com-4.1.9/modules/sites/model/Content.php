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
 * Content Active Record will be used for rendering content on a page
 *
 * @package GO.modules.sites
 * @copyright Copyright Intermesh
 * @version $Id Content.php 2012-06-27 16:26:13 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * 
 * @property int $id
 * @property int $user_id
 * @property int $ctime
 * @property int $mtime
 * @property string $title
 * @property string $slug
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $content
 * @property int $status
 * @property int $parent_id
 * @property int $site_id
 * @property GO_Sites_Model_Site $site
 */
class GO_Sites_Model_Content extends GO_Base_Db_ActiveRecord {
	
	const STATUS_PUBLISHED = 1;
	const STATUS_OFFLINE = 3;
	const STATUS_DRAFT = 2;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Sites_Model_Content
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function defaultAttributes()
	{
		return array(
				'user_id'=>GO::user()->id
		);
	}
	
	public function tableName() {
		return 'si_content';
	}
	
	public function relations() {
		return array(
				'content' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Content', 'field' => 'parent_id', 'delete' => true),
				'site'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Sites_Model_Site", 'field'=>'site_id'),
				'parent'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Sites_Model_Content", 'field'=>'parent_id')
				);
	}
	
	public function getUrl($action='/sites/default/content',$relative=true){		
		return GOS::site()->getController()->createUrl($action, array('slug'=>$this->slug), $relative);
	}
	
}
?>
