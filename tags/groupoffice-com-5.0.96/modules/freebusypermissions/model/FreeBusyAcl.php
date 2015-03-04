<?php
/**
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @package GO.modules.Freebusypermissions
 * @version $Id: GO_Freebusypermissions_Model_FreeBusyAcl.php 7607 2012-07-03 10:31:57Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */
 
/**
 * The GO_Freebusypermissions_Model_FreeBusyAcl model
 *
 * @package GO.modules.Freebusypermissions
 * @version $Id: GO_Freebusypermissions_Model_FreeBusyAcl.php 7607 2012-07-03 10:31:57Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 *
 * @property int $user_id
 * @property int $acl_id
 */

class GO_Freebusypermissions_Model_FreeBusyAcl extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Freebusypermissions_Model_FreeBusyAcl
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return array('user_id','acl_id');
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
		 return 'fb_acl';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
}