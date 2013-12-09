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
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package GO.base.exception
 * 
 * @uses Exception
 */

class GO_Base_Exception_NotFound extends Exception
{

	public function __construct($message='') {
		
		$message = empty($message) ? \GO::t('notFound') : \GO::t('notFound')."\n\n".$message;
		
		parent::__construct($message);
	}
}