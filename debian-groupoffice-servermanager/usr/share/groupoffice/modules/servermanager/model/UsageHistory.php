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
 * @copyright Copyright Intermesh BV
 * @version $Id UsageHistory.php 2012-09-03 10:13:14 mdhart $
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * @package GO.servermanager.model
 */
/**
 * The active record for logging the usage of an installation
 *
 * @package GO.servermanager.model
 * @copyright Copyright Intermesh
 * @version $Id UsageHistory.php 2012-09-03 10:13:14 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * 
 * @property int $id
 * @property int $ctime
 * @property string $user_count
 * @property double $database_usage
 * @property double $file_storage_usage
 * @property double $mailbox_usage
 * @property int $total_logins
 */
class GO_ServerManager_Model_UsageHistory extends GO_Base_Db_ActiveRecord
{
	
	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_usage_history';
	}
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function getDatabaseUsageText()
	{
		return GO_Base_Util_Number::formatSize($this->database_usage*1024);
	}
	public function getFileStorageUsageText()
	{
		return GO_Base_Util_Number::formatSize($this->file_storage_usage*1024);
	}
	public function getMailboxUsageText()
	{
		return GO_Base_Util_Number::formatSize($this->mailbox_usage*1024);
	}
	public function getTotalUsageText()
	{
		return GO_Base_Util_Number::formatSize($this->getTotalUsage()*1024);
	}
	
	/**
	 * get the total usage of database, files and mailbox
	 * @return double $totalUsage 
	 */
	public function getTotalUsage()
	{
		return $this->database_usage + $this->file_storage_usage + $this->mailbox_usage;
	}

}

?>
