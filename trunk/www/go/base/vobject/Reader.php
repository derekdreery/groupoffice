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
	
	public static function parseDuration($duration){
		preg_match('/(-?)P([0-9]+[WD])?T?([0-9]+H)?([0-9]+M)?([0-9]+S)?/', (string) $duration, $matches);
		//var_dump($matches);


		$negative = $matches[1]=='-' ? -1 : 1;

		$days = 0;
		$weeks = 0;
		$hours=0;
		$mins=0;
		$secs = 0;
		for($i=2;$i<count($matches);$i++){
			$d = substr($matches[$i],-1);
			switch($d){
				case 'D':
					$days += intval($matches[$i]);
					break;
				case 'W':
					$weeks += intval($matches[$i]);
					break;
				case 'H':
					$hours += intval($matches[$i]);
					break;
				case 'M':
					$mins += intval($matches[$i]);
					break;
				case 'S':
					$secs += intval($matches[$i]);
					break;
			}
		}

		return $negative*(($weeks * 60 * 60 * 24 * 7) + ($days * 60 * 60 * 24) + ($hours * 60 * 60) + ($mins * 60) + ($secs));	
	}	
}