<?php
class GO_Base_Html_Submit extends GO_Base_Html_Input {
	
	public static function render($attributes,$echo=true) {
		$i = new self($attributes);
		if($echo)
			echo $i->getHtml();
		else
			return $i->getHtml();
	}
	
	protected function init() {
		$this->attributes['type']='submit';		
		$this->attributes['class'].=' button submit';
	}
}