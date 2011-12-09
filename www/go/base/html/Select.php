<?php

class GO_Base_Html_Select extends GO_Base_Html_Input {

	public static function render($attributes) {
		$i = new self($attributes);
		echo $i->getHtml();
	}
	
	protected function init() {
		$this->attributes['type']='select';
		$this->attributes['class'].=' select';
	}
}