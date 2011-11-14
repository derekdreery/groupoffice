<?php
//require vendor lib SabreDav vobject
require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');

Sabre_VObject_Reader::$elementMap = array(
        'DTSTART'   => 'Sabre_VObject_Element_DateTime',
        'DTEND'     => 'Sabre_VObject_Element_DateTime',
        'COMPLETED' => 'Sabre_VObject_Element_DateTime',
        'DUE'       => 'Sabre_VObject_Element_DateTime',
				'DTSTAMP'       => 'Sabre_VObject_Element_DateTime',
				'CREATED'       => 'Sabre_VObject_Element_DateTime',
				'RECURRENCE-ID'       => 'Sabre_VObject_Element_DateTime',
				'LAST-MODIFIED'       => 'Sabre_VObject_Element_DateTime',
        'EXDATE'    => 'Sabre_VObject_Element_MultiDateTime',
    );
		
class GO_Base_VObject_Reader extends Sabre_VObject_Reader{
	
}