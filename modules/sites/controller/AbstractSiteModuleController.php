<?php

abstract class GO_Sites_Controller_AbstractSiteModule extends GO_Base_Controller_AbstractController {
	
	/**
	 * The ID of the current site
	 * @var int 
	 */
	protected $site_id;
	
	/**
	 * Set the site_id for this controller and create the default pages.
	 * 
	 * @param array $params 
	 */
	protected function actionCreateDefaultPages($params){		
		$this->site_id=$params['site_id'];
		
		$pages = $this->getDefaultPages();
		
		$this->_saveDefaultPages($pages);
		
		$response['success'] = true;
		return $response;
	}
	
	/**
	 * Function that needs to be overridden in the modules.
	 * The return value must be an array with page attributes
	 * 
	 * Example: return array(
	 *						array('path'=>'ticketlist','controller'=>'GO_Tickets_Controller_Site','template'=>'ticketlist','action'=>'ticketlist','title'=>'Tickets'),
	 *						array('path'=>'ticket','controller'=>'GO_Tickets_Controller_Site','template'=>'ticket','action'=>'ticket','title'=>'Ticket')
	 *					);
	 * 
	 * @return array 
	 */
	protected function getDefaultPages(){
		return array();
	}
	
	/**
	 * The function that actually creates the pages
	 * @param array $defaultPages 
	 */
	protected function _saveDefaultPages($defaultPages){
		if(!empty($this->site_id)){
			foreach($defaultPages as $p){
				
				$existing = GO_Sites_Model_Page::model()->findSingleByAttributes(array(
					'site_id'=>$this->site_id,
					'path'=>$p['path']
				));
				
				if(!$existing){				
					$page = new GO_Sites_Model_Page();
					$page->site_id = $this->site_id;
					$page->setAttributes($p);
					$page->save();
				}
			}
		}
	}
}