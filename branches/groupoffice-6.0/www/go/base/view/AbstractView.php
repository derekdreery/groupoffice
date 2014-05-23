<?php
namespace GO\Base\View;

use GO;

abstract class AbstractView{
	
	abstract public function render($viewName, $data);
	
	
	/**
	 * Default headers to send. 
	 */
	protected function headers(){
		//iframe hack for file uploads fails with application/json				
		if(!GO::request()->isJson()){
			header('Content-Type: text/html; charset=UTF-8');
		}else
		{
			header('Content-Type: application/json; charset=UTF-8');
		}

		foreach(GO::config()->extra_headers as $header){
			header($header);
		}
	}
}