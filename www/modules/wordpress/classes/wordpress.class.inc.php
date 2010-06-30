<?php
class wordpress extends db{
	public function __construct(){
		parent::__construct();

		$this->set_parameters('localhost', 'wordpress', 'root', '');
	}

}