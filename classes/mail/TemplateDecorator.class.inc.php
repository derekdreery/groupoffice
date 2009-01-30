<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.mail
 */

/**
 * Require all mail classes that are used by this class
 */
require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');

/**
 * This class can be used to replace fields in a batch mail operation.
 * Swift documentation can be found here:
 *
 * {@link http://www.swiftmailer.org/wikidocs/"target="_blank Documentation}
 *
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @license AGPL Affero General Public License
 * @package go.mail
 * @uses Swift
 * @since Group-Office 3.0
 */

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
