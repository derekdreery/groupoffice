<?php
require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');

class Swift_Plugin_Template_Decorator extends Swift_Plugin_Decorator 
{
	
	public function __construct($replacements=null)
  {
    $this->setReplacements($replacements);
    
    $this->tp = new templates();
  }
  /**
   * Perform a str_replace() over the given value.
   * @param array The list of replacements as (search => replacement)
   * @param string The string to replace
   * @return string
   */
  protected function replace($replacements, $value)
  {
  	$this->tp->replace_fields($value, $replacements);
    return $value;
  }
}
