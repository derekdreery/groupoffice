<?php

class GO_Addressbook_Controller_SiteModule extends GO_Sites_Controller_SiteBaseModule {

	protected function actionCreateDefaultPages($site_id){
		$defaultPages = array(
//				'ticketlist'=>array('controller'=>'GO_Tickets_Controller_Site','template'=>'ticketlist','action'=>'ticketlist','title'=>'Tickets','login_required'=>true),
//				'ticket'=>array('controller'=>'GO_Tickets_Controller_Site','template'=>'ticket','action'=>'ticket','title'=>'Ticket','login_required'=>true)
				);
		
		foreach($defaultPages as $p=>$c){
			$page = new GO_Sites_Model_Page();
			$page->site_id = $site_id;
			$page->path = $p;
			$page->name = $c['title'];
			$page->title = $c['title'];
			$page->controller = $c['controller'];
			$page->controller_action = $c['action'];
			$page->template = $c['template'];
			$page->save();
		}		
	}
}