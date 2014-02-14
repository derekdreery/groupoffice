<?php
namespace GO\Email\Controller;

use GO\Base\Controller\AbstractController;

class ViewController extends AbstractController{
	
	protected $layout='html';


	public function actionResponsive(){
		$this->render('load');
	}
	
}