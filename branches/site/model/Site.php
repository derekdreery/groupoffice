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
		return array();
	}

	public static function launch($siteId = false) {
		if (!$siteId)
			Throw new GO_Base_Exception_NotFound('No site found!');

		$site = self::model()->findByPk($siteId);
		
		if(!$site)
			Throw new GO_Base_Exception_NotFound('No site found in the db!');
		
		$site->run();
	}
	
	public function run(){
		
		if($this->module)
		$config = array();
		
		
		echo 'run';
		
		
	}
	

}