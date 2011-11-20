<?php

class GO_Base_Html_Select extends GO_Base_Html_Input {

	public static function render($attributes) {
		$i = new self($attributes);
		echo $i->getHtml();
	}

	protected function renderInput() {
		$html = '<select class="' . $this->attributes['class'] . '" name="' . $this->attributes['name'] . '" ' . $this->attributes['extra'] . '>';

		foreach ($this->attributes['options'] as $value => $label){
			
			$html .= '<option value="' . $value . '"';
		
			if($this->attributes['value']==$value){
				$html .= ' selected';
			}
		
			$html .='>' . $label . '</option>';
		}
		$html .= '</select>';

		if (!empty($this->attributes['empty_text'])) {
			$html .= '<input type="hidden" name="empty_texts[]" value="' . $this->attributes['name'] . ':' . $this->attributes['empty_text'] . '" />';
		}

		return $html;
	}

}