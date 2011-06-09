<?php
class GO_Controller_Default extends GO_Base_Controller_AbstractController{
	
	protected $defaultAction='Init';
	
	protected function actionInit(){
		$this->render('init');
	}
	
	protected function registerClientScript($url){
		
	}
	
	/**
	 *
	 * @param type $text 
	 */
	protected function registerInlineScript($text){
		
	}
	
	protected function registerCssFile(){
		
	}
}