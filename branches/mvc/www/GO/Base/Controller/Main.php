<?php
class GO_Base_Controller_Main extends GO_Base_Controller_AbstractController{
	protected function actionIndex(){
		$this->render('index');
	}
	
	protected function registerClientScript($url){
		
	}
	
	/**
	 *
	 * @param type $text 
	 */
	protected function registerInlineScript($text){
		
	}
}