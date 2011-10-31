<?php

class GO_Sites_Form_Input {

	protected $attributes;
	
	public static function render($attributes){
		$i = new GO_Sites_Form_Input($attributes);
		echo $i->getHtml();
	}

	public function __construct($attributes) {
		$this->attributes = $attributes;
	}

	public function getHtml() {
		
		$html = '';
		
		if(!empty($this->attributes['label'])){
			if(!empty($this->attributes['required'])){
				$this->attributes['label'] .= '*';
				
				$html .= '<label>'.$this->attributes['label'];
			}
		}
		
		if (!isset($this->attributes['value']))
			$this->attributes['value'] = '';

		if (!isset($this->attributes['extra']))
			$this->attributes['extra'] = '';

		if (!isset($this->attributes['class']))
			$this->attributes['class'] = 'textbox';

		$isset = false;

		$this->attributes['required'] = empty($this->attributes['required']) ? false : true;

		if (empty($this->attributes['forget_value'])) {
			if ($pos = strpos($this->attributes['name'], '[')) {
				$key1 = substr($this->attributes['name'], 0, $pos);
				$key2 = substr($this->attributes['name'], $pos + 1, -1);

				$isset = isset($_POST[$key1][$key2]);
				$value = isset($_POST[$key1][$key2]) ? ($_POST[$key1][$key2]) : $this->attributes['value'];
			} else {
				$value = isset($_POST[$this->attributes['name']]) ? ($_POST[$this->attributes['name']]) : $this->attributes['value'];
				$isset = isset($_POST[$this->attributes['name']]);
			}
		}
		
		if($isset && empty($value) && $this->attributes['required'])
			$this->attributes['class'].=' error';

		if(empty($value) && !empty($this->attributes['empty_text'])){
			$value = $this->attributes['empty_text'];
		}
		if(empty($this->attributes['type'])){
			$this->attributes['type']='text';
		}
		
		$html .= '<input class="'.$this->attributes['class'].'" type="'.$this->attributes['type'].'" name="'.$this->attributes['name'].'" value="'.$value.'" '.$this->attributes['extra'];


		if (!empty($this->attributes['empty_text'])) {
			$html .= ' onfocus="if(this.value==\'' . $this->attributes['empty_text'] . '\'){this.value=\'\';';

			if (!empty($this->attributes['empty_text_active_class'])) {
				$html .= 'this.className+=\' ' . $this->attributes['empty_text_active_class'] . '\'};"';
			} else {
				$html .= '}"';
			}

			$html .= ' onblur="if(this.value==\'\'){this.value=\'' . $this->attributes['empty_text'] . '\';';
			if (!empty($this->attributes['empty_text_active_class'])) {
				$html .= 'this.className=this.className.replace(\' ' . $this->attributes['empty_text_active_class'] . '\',\'\');';
			}
			$html .= '}"';
		}

		$html .= ' />';

		if ($this->attributes['required'] && ($this->attributes['required'] == 'true' || $this->attributes['required'] == '1')) {
			$html .= '<input type="hidden" name="required[]" value="' . $this->attributes['name'] . '" />';
		}
		if (!empty($this->attributes['empty_text'])) {
			$html .= '<input type="hidden" name="empty_texts[]" value="' . $this->attributes['name'] . ':' . $this->attributes['empty_text'] . '" />';
		}

		if(!empty($this->attributes['label'])){
			$html .= '</params>';
		}

		return $html;
	}

}