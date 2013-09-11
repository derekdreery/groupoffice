<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 * @property int $errors
 * @property int $sent
 * @property int $total
 * @property int $status
 * @property int $alias_id
 * @property int $addresslist_id
 * @property int $ctime
 * @property string $message_path
 * @property string $subject
 * @property int $user_id
 * @property int $id
 * 
 * @property GO_Base_Fs_File $logFile
 * @property GO_Base_Fs_File $messageFile
 */
class GO_Addressbook_Model_SentMailing extends GO_Base_Db_ActiveRecord {
	const STATUS_RUNNING=1;
	const STATUS_FINISHED=2;
	const STATUS_PAUSED=3;

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Addressbook 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'ab_sent_mailings';
	}
	
	public function relations() {
		return array(
				'addresslist' => array('type' => self::BELONGS_TO, 'model' => 'GO_Addressbook_Model_Addresslist', 'field' => 'addresslist_id'),
				'contacts' => array('type'=>self::MANY_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'contact_id', 'linkModel' => 'GO_Addressbook_Model_SentMailingContact'),
				'companies' => array('type'=>self::MANY_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'company_id', 'linkModel' => 'GO_Addressbook_Model_SentMailingCompany')
		);
	}

	/**
	 * Clears or initializes the sending status of the mailing.
	 */
	public function reset() {
		$nMailsToSend = 0;

		// Clear list of company recipients to send to, if there are any in this list
		$this->removeAllManyMany('companies');

		// Add company recipients to this list and count them
		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($this->addresslist_id)->companies();
		while ($company = $stmt->fetch()) {
			$this->addManyMany('companies', $company->id);			
			$nMailsToSend++;			
		}

		// Clear list of contact recipients to send to, if there are any in this list
		$this->removeAllManyMany('contacts');

		// Add contact recipients to this list and count them
		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($this->addresslist_id)->contacts();
		while ($contact = $stmt->fetch()) {
			$this->addManyMany('contacts', $contact->id);			
			$nMailsToSend++;			
		}

		$this->setAttributes(
						array(
								"status" => self::STATUS_RUNNING,
								"total" => $nMailsToSend
						)
		);
		$this->save();
	}
	
	protected function getLogFile(){
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'log/mailings/'.$this->id.'.log');		
		return $file;
	}
	
	protected function getMessageFile(){
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.$this->message_path);		
		return $file;
	}
	
	protected function beforeDelete() {
		if($this->status==self::STATUS_RUNNING)
			throw new Exception("Can't delete a running mailing. Pause it first.");
		return parent::beforeDelete();
	}
	
	protected function afterDelete() {
		
		$this->logFile->delete();
		$this->messageFile->delete();
		
		return parent::afterDelete();
	}

}