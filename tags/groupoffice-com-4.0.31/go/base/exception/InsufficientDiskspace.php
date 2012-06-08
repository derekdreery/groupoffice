<?php
/*
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Thrown when a user doesn't have access
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: exceptions.class.inc.php 6002 2010-10-27 13:21:25Z mschering $
 * @copyright Copyright Intermesh
 * @package GO.base.exception
 * 
 * @uses Exception
 */

class GO_Base_Exception_InsufficientDiskspace extends Exception
{

	public function __construct($message='') {
		
		$currentQuota = GO::config()->get_setting('file_storage_usage');
			
		$message = GO::t('quotaExceeded')."\n".sprintf(GO::t('youAreUsing'),  GO_Base_Util_Number::formatSize($currentQuota), GO_Base_Util_Number::formatSize(GO::config()->quota)).$message;
		
		parent::__construct($message);
	}
}