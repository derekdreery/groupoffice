<?php
class GO_Base_Controller_Main extends GO_Base_Controller_AbstractController{
	protected function actionIndex(){
		$this->render('index');
	}
}