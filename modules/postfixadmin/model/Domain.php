<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.postfixadmin.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Postfixadmin_Model_Domain model
 *
 * @package GO.modules.postfixadmin.model
 * @property int $user_id
 * @property string $domain
 * @property string $description
 * @property int $max_aliases
 * @property int $max_mailboxes
 * @property int $total_quota
 * @property int $default_quota
 * @property string $transport
 * @property boolean $backupmx
 * @property int $ctime
 * @property int $mtime
 * @property boolean $active
 * @property int $acl_id
 */

class GO_Postfixadmin_Model_Domain extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Postfixadmin_Model_Domain 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'pa_domains';
	}
	

	public function relations() {
		return array(
			'mailboxes' => array('type' => self::HAS_MANY, 'model' => 'GO_Postfixadmin_Model_Mailbox', 'field' => 'domain_id', 'delete' => true),
			'aliases' => array('type' => self::HAS_MANY, 'model' => 'GO_Postfixadmin_Model_Alias', 'field' => 'domain_id', 'delete' => true)
		);
	}
	
	public function getLogMessage($action) {		
		return $this->domain;
	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['total_quota']=1024*1024*10;//10 GB of quota per domain by default.
		$attr['default_quota']=1024*512; //512 MB of default quota
		return $attr;
	}
	
	protected function init() {
		$this->columns['domain']['unique']=true;
		$this->columns['total_quota']['gotype']='number';
		$this->columns['default_quota']['gotype']='number';
		$this->columns['max_aliases']['gotype']='number';
		$this->columns['max_mailboxes']['gotype']='number';
		return parent::init();
	}
		
	/**
	 * @return Int The sum of the current domain's mailbox quotas.
	 */
	public function getSumUsedQuota() {
		$activeRecord = GO_Postfixadmin_Model_Mailbox::model()->find(
			GO_Base_Db_FindParams::newInstance()
				->single()
				->select('SUM(`quota`) AS sum_used_quota')
				->criteria(
					GO_Base_Db_FindCriteria::newInstance()
						->addCondition('domain_id',$this->id)
				)
		);
		return isset($activeRecord->sum_used_quota) ? $activeRecord->sum_used_quota : 0;
	}
	
	public function getSumMailboxes() {
		
		$record = GO_Postfixadmin_Model_Mailbox::model()->find(
			GO_Base_Db_FindParams::newInstance()
				->single()
				->select('COUNT(*) AS count')
				->criteria(
					GO_Base_Db_FindCriteria::newInstance()
						->addCondition('domain_id',$this->id)
				)
		);
		return $record->count;
	}
	
	public function getSumAliases() {
		$record = GO_Postfixadmin_Model_Alias::model()->find(
			GO_Base_Db_FindParams::newInstance()
				->single()
				->select('COUNT(*) AS count')
				->criteria(
					GO_Base_Db_FindCriteria::newInstance()
						->addCondition('domain_id',$this->id)
				)
		);
		return $record->count;
	}

}