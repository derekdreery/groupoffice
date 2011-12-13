<?php

class GO_Base_Html_Input {
	
	public static $errors;

	protected $attributes;
	
	protected $isPosted=false;
	
	/**
	 * Set error message for a form input field
	 */
	public static function setError($inputName, $errorMsg){
		GO::session()->values['formErrors'][$inputName]=$errorMsg;
	}
	
	/**
	 * Check if any input has errors.
	 * 
	 * @return boolean 
	 */
	public static function hasErrors(){
		return !empty(GO::session()->values['formErrors']);
	}
	
	public static function checkRequired(){
		
		
		if(isset($_POST['required'])){
			foreach($_POST['required'] as $inputName){
				if ($pos = strpos($inputName, '[')) {
					$key1 = substr($inputName, 0, $pos);
					$key2 = substr($inputName, $pos + 1, -1);
					if(empty($_POST[$key1][$key2]))
						self::setError($inputName, 'This field is required');
				}else
				{
					if(empty($_POST[$inputName]))
						self::setError($inputName, 'This field is required');
				}
			}
		}
		
		return !self::hasErrors();
	}
	
	/**
	 *
	 * @param type $inputName
	 * @return string 
	 */
	public static function getErrorMsg($inputName){
		return isset(GO::session()->values['formErrors'][$inputName]) ? GO::session()->values['formErrors'][$inputName] : false;
	}
	
	public static function getError($inputName){
		$errorMsg = self::getErrorMsg($inputName);
		if($errorMsg){
			return '<div class="errortext">'.$errorMsg.'</div>';
		}else
		{
			return '';
		}
	}
	
	public static function printError($inputName){
		echo self::getError($inputName);
	}
	
	public static function render($attributes,$echo=true) {
		$i = new self($attributes);
		if($echo)
			echo $i->getHtml();
		else
			return $i->getHtml();
	}

	public function __construct($attributes) {
		$this->attributes = $attributes;
		
		if(!empty($this->attributes['label'])){
			if(!empty($this->attributes['required'])){
				$this->attributes['label'] .= '<span class="required">*</span>';
			}		
		}
		
		if (!isset($this->attributes['value']))
			$this->attributes['value'] = '';

		if (!isset($this->attributes['extra']))
			$this->attributes['extra'] = '';

		if (!isset($this->attributes['class']))
			$this->attributes['class'] = 'input';

		if (!isset($this->attributes['renderContainer']))
			$this->attributes['renderContainer'] = true;

		$this->attributes['required'] = empty($this->attributes['required']) ? false : true;
		
		
		if (empty($this->attributes['forget_value'])) {
			if ($pos = strpos($this->attributes['name'], '[')) {
				$key1 = substr($this->attributes['name'], 0, $pos);
				$key2 = substr($this->attributes['name'], $pos + 1, -1);

				$this->isPosted = isset($_POST[$key1][$key2]);
				$this->attributes['value'] = isset($_POST[$key1][$key2]) ? ($_POST[$key1][$key2]) : $this->attributes['value'];
			} else {
				$this->attributes['value'] = isset($_POST[$this->attributes['name']]) ? ($_POST[$this->attributes['name']]) : $this->attributes['value'];
				$this->isPosted = isset($_POST[$this->attributes['name']]);
			}
		}
		
//		if($this->isPosted && empty($this->attributes['value']) && $this->attributes['required'])
//			self::setError ($this->attributes['name'], "This field is required");
		

		if(empty($this->attributes['value']) && !empty($this->attributes['empty_text'])){
			$this->attributes['value'] = $this->attributes['empty_text'];
		}
		if(empty($this->attributes['type'])){
			$this->attributes['type']='text';
		}
		
		$this->init();
		
	}
	
	protected function init(){
		return true;
	}
	
	protected function renderNormalInput(){
		
		$html = '<input class="'.$this->attributes['class'].'" type="'.$this->attributes['type'].'" name="'.$this->attributes['name'].'" value="'.$this->attributes['value'].'" '.$this->attributes['extra'];

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
		
		if (!empty($this->attributes['empty_text'])) {
			$html .= '<input type="hidden" name="empty_texts[]" value="' . $this->attributes['name'] . ':' . $this->attributes['empty_text'] . '" />';
		}
		
		return $html;
	}
	
	protected function renderMultiInput(){
		
		if(!empty($this->attributes['options']) && (!isset($this->attributes['options'][0]) || !is_array($this->attributes['options'][0]))){
			$oldoptions = $this->attributes['options'];
			$this->attributes['options'] = array();
			foreach($oldoptions as $value=>$label){
				
				$option = array();
				$option['label']= $label;
				$option['value']= $value;
				
				$this->attributes['options'][] = $option;
			}
		}
		
		
		$html = '';
		
		if($this->attributes['type'] == 'select')
			$html .= '<select class="'.$this->attributes['class'].'" name="'.$this->attributes['name'].'" value="'.$option['value'].'">';
		
		foreach($this->attributes['options'] as $option){
			if($this->attributes['type'] == 'select'){
				
				$html .= '<option';
				
				$html .= ' value="'.$option['value'].'"';
				if($this->attributes['value']==$option['value'])
					$html .= ' selected';
			
				$html .='>';
				$html .= $option['label'];
				$html .= '</option>';
			} else {
				$html .= '<label>';
				$html .= '<input class="'.$this->attributes['class'].'" type="'.$this->attributes['type'].'" name="'.$this->attributes['name'].'" value="'.$option['value'].'" ';
				if(!empty($option['extra']))
					$html .= $option['extra'];

				if(!empty($this->attributes['value'])){
					if($this->attributes['value']==$option['value']){
						if($this->attributes['type'] == 'checkbox' || $this->attributes['type'] == 'radio')
							$html .= 'checked';						
					}
				}

				$html .= '/>';
				$html .= $option['label'];
				$html .= '</label>';
			}
		}
		
		if($this->attributes['type'] == 'select')
			$html .= '</select>';
		
		return $html;
	}

	public function getHtml() {
		
		// Check for errors
		if(self::getErrorMsg($this->attributes['name']))
			$this->attributes['class'].=' error';
		
		$html = '';
		
		// The opening div for the row
		if(!empty($this->attributes['renderContainer']))
		{
			$html .= '<div class="formrow';
			if(!empty($this->attributes['rowClass']))
				$html .= ' '.$this->attributes['rowClass'];
			$html .= '">';
		}
		// The label div
		if(!empty($this->attributes['label'])){
			$html .= '<div class="formlabel';
			if(!empty($this->attributes['labelClass']))
				$html .= ' '.$this->attributes['labelClass'];
			$html .= '"';
			if(!empty($this->attributes['labelStyle']))
				$html .= 'style="'.$this->attributes['labelStyle'].'"';
			$html .= '>'.$this->attributes['label'].' :</div>';
		}
		
		// Check for multiple input fields or not
		if(!empty($this->attributes['options'])){
			$html .= $this->renderMultiInput();
		} else {
			$html .= $this->renderNormalInput();
		}
		
		// The error div is created when there is an error.
			$html .= $this->getError($this->attributes['name']);

		if($this->attributes['required'])
			$html .= '<input type="hidden" name="required[]" value="'.$this->attributes['name'].'" />';
		
		// Close the row div
		if(!empty($this->attributes['renderContainer'])) {
			$html .= '<div style="clear:both;"></div>';
			$html .= '</div>';
		}
		
		
		unset(GO::session()->values['formErrors'][$this->attributes['name']]);

		return $html;
	}

}