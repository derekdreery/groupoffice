<?php
class highlight {
	var $keyword;
	var $replacement;
	var $hightlight_bad_tags = array("A","IMG");
	var $begin_boundary;
	var $end_boundary;

	function  __construct($keyword=false,$replacement=false) {
		$this -> keyword = $keyword;
		$this -> replacement = $replacement;
	}
	function highlighter($matches) {

		if (!in_array(strtoupper($matches[2]),$this -> hightlight_bad_tags)) {

			$regex = "#".$this->begin_boundary."(".$this -> keyword.")".$this->end_boundary."#si";

			$proceed =  preg_replace($regex,str_replace("{keyword}",$matches[3],$this -> replacement),$matches[0]);
		}
		else {
			$proceed = $matches[0];
		}
		return stripslashes($proceed);
	}
	function process($text,$keyword = false,$replacement = false) {
		if($keyword != false) $this -> keyword = $keyword;
		if($replacement != false) $this -> replacement = $replacement;

		if((isset($this -> keyword)) AND (isset($this -> replacement))) {
			if(substr($this->keyword,0,1)=='*') {
				$this->keyword=substr($this->keyword,1);
				$this->begin_boundary='';
			}else {
				$this->begin_boundary='\\b';
			}

			if(substr($this->keyword,-1)=='*') {
				$this->keyword=substr($this->keyword,0,-1);
				$this->end_boundary='';
			}else {
				$this->end_boundary='\\b';
			}
			$regex="#(<([A-Za-z]+)[^>]*[\>]*)*".$this->begin_boundary."(".$this -> keyword.")".$this->end_boundary."(.*?)(<\/\\2>)*#si";
			return preg_replace_callback($regex,array(&$this, 'highlighter'), $text);
		}else {
			return $text;
		}
	}
}
