<?php
//require vendor lib SabreDav vobject
//require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');
		
class GO_Base_VObject_VCalendar extends Sabre\VObject\Component {

	/**
	 * Creates a new component.
	 *
	 * By default this object will iterate over its own children, but this can 
	 * be overridden with the iterator argument
	 * 
	 * @param string $name 
	 * @param Sabre_VObject_ElementList $iterator
	 */
	public function __construct($name='VCALENDAR', Sabre\VObject\ElementList $iterator = null) {

		parent::__construct($name, $iterator);
		
		$this->version='2.0';
		$this->prodid='-//Intermesh//NONSGML Group-Office '.GO::config()->version.'//EN';		
	}
}