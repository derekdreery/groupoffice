<?php

/**
 * @var GO_Postfixadmin_Model_Domain $domain
 * @property int $domain_id
 * @property string $go_installation_id
 * @property string $username
 * @property string $password
 * @property string $name
 * @property string $maildir
 * @property int $quota Quota in kilobytes
 * @property int $ctime
 * @property int $mtime
 * @property boolean $active
 * @property int $usage Usage in kilobytes
 */
class GO_Postfixadmin_Model_Mailbox extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Postfixadmin_Model_Mailbox 
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'pa_mailboxes';
	}

	public function relations() {
		return array(
			'domain' => array('type' => self::BELONGS_TO, 'model' => 'GO_Postfixadmin_Model_Domain', 'field' => 'domain_id')
		);
	}

	protected function init() {
		$this->columns['username']['unique'] = true;
		$this->columns['username']['required'] = true;
		$this->columns['password']['required'] = true;

		return parent::init();
	}
	
	public function getLogMessage($action) {		
		return $this->username;
	}

	protected function beforeSave() {

		if ($this->isModified("password"))
			$this->password = '{CRYPT}' . crypt($this->password);
		
		$parts = explode('@', $this->username);

		$this->maildir = $this->domain->domain . '/' . $parts[0] . '/';
		return parent::beforeSave();
	}

	protected function afterSave($wasNew) {
		if (!empty($wasNew)) {
			// Create alias
			$aliasModel = GO_Postfixadmin_Model_Alias::model();
			$aliasModel->setAttributes(
							array(
									'goto' => $this->username,
									'domain_id' => $this->domain_id,
									'address' => $this->username,
									'active' => $this->active
							)
			);
			$aliasModel->save();
		}
		return parent::afterSave($wasNew);
	}

//	public function defaultAttributes() {
//		$attr = parent::defaultAttributes();
//		$attr['quota']=$this->domain->default_quota;
//		return $attr;
//	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['quota']=1024*1024*1;//10 GB of quota per domain by default.
		return $attr;
	}

	public function validate() {


		$this->_checkQuota();
		
		if (!empty($this->domain->max_mailboxes) && $this->isNew && $this->domain->getSumMailboxes() >= $this->domain->max_mailboxes)
						throw new Exception('The maximum number of mailboxes for this domain has been reached.');

		return parent::validate();
	}
	
	/**
	 * Get the filesystem folder with mail data.
	 * 
	 * @return \GO_Base_Fs_Folder
	 */
	public function getMaildirFolder(){
		return new GO_Base_Fs_Folder('/home/vmail/'.$this->maildir);
	}
	
	public function cacheUsage(){
		$this->usage = $this->getMaildirFolder()->calculateSize()/1024;
		$this->save();
	}

	private function _checkQuota() {
		$total_quota = $this->domain->total_quota;
		if (!empty($total_quota)) {
			if (empty($this->quota))
				$this->setValidationError('quota', 'You are not allowed to disable mailbox quota');

			if ($this->isNew || $this->isModified("quota")) {

				$existingQuota = $this->isNew ? 0 : $this->getOldAttributeValue("quota");

				$sumUsedQuotaOtherwise = $this->domain->getSumUsedQuota() - $existingQuota; // Domain's used quota w/o the current mailbox's quota.
				if ($sumUsedQuotaOtherwise + $this->quota > $total_quota) {
					$quotaLeft = $total_quota - $sumUsedQuotaOtherwise;
					throw new Exception('The maximum quota has been reached. You have ' . GO_Base_Util_Number::localize($quotaLeft / 1024) . 'MB left');
				}
			}
		}
	}

}