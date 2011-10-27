<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Tasks_Controller_Category controller
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Controller_Category.php 7607 2011-09-20 10:07:50Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

class GO_Sites_Controller_Site extends GO_Base_Controller_AbstractController{
	
	/**
	 * This default action should be overrriden
	 */
	public function actionIndex($params){
		
		$params['p']='Contact';
		
		//find page
		
		$this->_renderPage($page);
		
	}
	
}
