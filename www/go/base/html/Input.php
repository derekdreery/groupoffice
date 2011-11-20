<?php

class GO_Base_Html_Input {
	
	public static $errors;

	protected $attributes;
	
	protected $isPosted=false;
	
	/**
	 * Set error message for a form input field
	 */
	public static function setError($inputName, $errorMsg){
		self::$errors[$inputName]=$errorMsg;
	}
	
	/**
	 * Check if any input has errors.
	 * 
	 * @return boolean 
	 */
	public static function hasErrors(){
		return isset(self::$errors);
	}
	
	public static function checkRequired(){
		if(isset($_POST['required'])){
			foreach($_POST['required'] as $inputName){
				if(empty($_POST[$inputName])){
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
		return isset(self::$errors[$inputName]) ? self::$errors[$inputName] : false;
	}
	
	public static function getError($inputName){
		$errorMsg = self::getErrorMsg($inputName);
		if($errorMsg){
			return '<div class="error">'.$errorMsg.'</div>';
		}else
		{
			return '';
		}
	}
	
	public static function printError($inputName){
		echo self::getError($inputName);
	}
	
	public static function render($attributes){
		$i = new self($attributes);
		echo $i->getHtml();
	}

	public function __construct($attributes) {
		$this->attributes = $attributes;
		
		if(!empty($this->attributes['label'])){
			if(!empty($this->attributes['required'])){
				$this->attributes['label'] .= '*';
			}		
		}
		
		if (!isset($this->attributes['value']))
			$this->attributes['value'] = '';

		if (!isset($this->attributes['extra']))
			$this->attributes['extra'] = '';

		if (!isset($this->attributes['class']))
			$this->attributes['class'] = 'textbox';

		

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
		
		if($this->isPosted && empty($this->attributes['value']) && $this->attributes['required'])
			self::setError ($this->attributes['name'], "This field is required");
		

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
	
	protected function renderInput(){
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

	public function getHtml() {
		$html = '';
		
		$this->attributes['label'] .= self::getError($this->attributes['name']);
		
		if(!empty($this->attributes['label'])){			
			$html .= '<label>';
			if($this->attributes['type']!='checkbox')
				$html .= $this->attributes['label'].':';
		}
		
		
		
		$html .= $this->renderInput();
		
		if(!empty($this->attributes['label'])){
			if($this->attributes['type']=='checkbox')
				$html .= $this->attributes['label'];
			
			$html .= '</label>';
		}

		if ($this->attributes['required'] && ($this->attributes['required'] == 'true' || $this->attributes['required'] == '1')) {
			$html .= '<input type="hidden" name="required[]" value="' . $this->attributes['name'] . '" />';
		}
		return $html;
	}

}