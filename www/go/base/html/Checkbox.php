<?php

class GO_Base_Html_Checkbox extends GO_Base_Html_Input {

	public static function render($attributes) {
		$i = new self($attributes);
		echo $i->getHtml();
	}
	
	protected function init() {
		$this->attributes['type']='checkbox';
		
		if($this->isPosted)
			$this->attributes['extra']='checked';
		
		$this->attributes['class']='checkbox';
	}


}