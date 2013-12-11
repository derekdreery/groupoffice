<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * 
 * The Category controller
 * 
 */
class GO_Smime_Controller_PublicCertificate extends \GO\Base\Controller\AbstractModelController{
	
	protected $model = 'GO_Smime_Model_PublicCertificate';
	
	protected function getStoreParams($params) {
		
		$fp = \GO\Base\Db\FindParams::newInstance();
		
		$fp->getCriteria()->addCondition('user_id', GO::user()->id);
		
		return $fp;
		
	}
}