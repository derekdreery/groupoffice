<?php

class GO_Base_Html_Checkbox extends GO_Base_Html_Input {

	public static function render($attributes,$echo=true) {
		$i = new self($attributes);
		if($echo)
			echo $i->getHtml();
		else
			return $i->getHtml();
	}
	
	protected function init() {
		$this->attributes['type']='checkbox';
		
//		if($this->isPosted)
//			$this->attributes['extra']='checked';
		
		$this->attributes['class']='checkbox';
	}


}