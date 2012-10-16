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
 * @version $Id UserPriceController.php 2012-10-15 10:51:19 mdhart $
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * @package GO.servermanager.controller
 */
/**
 * Description of file
 *
 * @package GO.servermanager.controller
 * @copyright Copyright Intermesh
 * @version $Id UserPriceController.php 2012-10-15 10:51:19 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_ServerManager_Controller_UserPrice extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_ServerManager_Model_UserPrice';

	protected function beforeSubmit(&$response, &$model, &$params)
	{
		$model = GO_ServerManager_Model_UserPrice::model()->findByPk($params['max_users']);
		if($model == null)
			$model = new GO_ServerManager_Model_UserPrice();
	}
}
?>
