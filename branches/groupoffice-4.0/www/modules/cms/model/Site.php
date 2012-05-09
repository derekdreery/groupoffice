<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author WilmarVB <wilmar@intermesh.nl>
 */

 class GO_Cms_Model_Site extends GO_Base_Db_ActiveRecord{
		 
	 /**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Addressbook 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	public function aclField(){
		return 'acl_write';	
	}
	
	public function tableName(){
		return 'cms_sites';
	}
	
	public function hasFiles(){
		return true;
	}
	
	protected function init() {
		$this->columns['name']['unique']=true;
		$this->columns['domain']['unique']=true;
		return parent::init();
	}
	
}