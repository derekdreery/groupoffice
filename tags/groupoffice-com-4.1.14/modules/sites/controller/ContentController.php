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
 * @version $Id ContentController.php 2012-07-12 15:13:19 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * @package GO.base.db
 */

/**
 * The backend controller for creating content items
 *
 * @package GO.
 * @copyright Copyright Intermesh
 * @version $Id ContentController.php 2012-07-12 15:13:19 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Sites_Controller_Content extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Sites_Model_Content';
	
	/**
	 * TODO: fix the action
	 * @param array $params the $_REQUEST 
	 */
	protected function actionRedirectToFront($params){
		$content = GO_Sites_Model_Content::model()->findByPk($params['id']);
		$site = GO_Sites_Model_Site::model()->findByPk($content->site_id);
		
		$url = "http://www.".$site->domain."/".$content->getUrl(); 
		header('Location: '.$url);
		exit();
	}
	
}
?>
