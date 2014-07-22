<?php

namespace GO\Customfields\Customfieldtype;


class Text extends AbstractCustomfieldtype{
	
	public function name(){
		return 'Text';
	}
	
	public function includeInSearches() {
		return true;
	}
}