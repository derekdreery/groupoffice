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
	
	/**
	 * Converts a vcalendar 1.0 component to an icalendar 2.0 component.
	 * 
	 * @param Sabre_VObject_Component $vobject 
	 */
	public static function convertVCalendarToICalendar(Sabre_VObject_Component $vobject){
		
		if($vobject->version=='1.0'){
			$vobject->version='2.0';
			foreach($vobject->children() as $child)
			{
				if($child instanceof Sabre_VObject_Component){
					foreach($child->children() as $property){
						if(isset($property['ENCODING']) && strtoupper($property['ENCODING'])=='QUOTED-PRINTABLE'){
							$value = quoted_printable_decode($property->value);
							$value = str_replace("\r","",$value);

							$property->setValue($value);				
							unset($property['ENCODING']);
						}
					}
					
					if(isset($child->rrule) && (string) $child->rrule!=''){
						$rrule = new GO_Base_Util_Icalendar_Rrule();
						$rrule->readIcalendarRruleString($child->dtstart->getDateTime()->format('U'), (string) $child->rrule);			
						$child->rrule = str_replace('RRULE:','',$rrule->createRrule());
					}
				}					
			}
		}	
	}
	
	
	/**
	 * Converts an icalendar 2.0 to a vcalendar 1.0 component.
	 * 
	 * @param Sabre_VObject_Component $vobject 
	 */
	public static function convertICalendarToVCalendar(Sabre_VObject_Component $vobject){
		
		$qpProperies = array('location', 'summary', 'description');
		GO::debug("Vobject version:".$vobject->version);
		if($vobject->version=='2.0'){
			$vobject->version='1.0';
			foreach($vobject->children() as $child)
			{
				if($child instanceof Sabre_VObject_Component){
					foreach($qpProperies as $propName){
						self::_quotedPrintableEncode($child, $propName);
					}
					
					if(isset($child->rrule) && (string) $child->rrule!=''){
						$rrule = new GO_Base_Util_Icalendar_Rrule();
						$rrule->readIcalendarRruleString($child->dtstart->getDateTime()->format('U'), (string) $child->rrule);			
						$child->rrule = str_replace('RRULE:','',$rrule->createVCalendarRrule());
					}
				}
			}
		}
	}
	
	/**
	 * Converts a vcalendar 1.0 component to an icalendar 2.0 component.
	 * 
	 * @param Sabre_VObject_Component $vobject 
	 */
	public static function convertVCard21ToVCard30(Sabre_VObject_Component $vobject){
		
		if($vobject->version=='2.1'){
			$vobject->version='3.0';
			foreach($vobject->children() as $property)
			{
				if(isset($property['ENCODING']) && strtoupper($property['ENCODING'])=='QUOTED-PRINTABLE'){
					$value = quoted_printable_decode($property->value);
					$value = str_replace("\r","",$value);

					$property->setValue($value);				
					unset($property['ENCODING']);
				}							
			}
		}	
	}
	
	private static function _quotedPrintableEncode($vobject, $propName){
		if(isset($vobject->$propName) && $vobject->$propName!=''){			
			$oldValue = (string) $vobject->$propName;
			$value =  str_replace(array("\r","=\n","\n"), '', quoted_printable_encode($oldValue));
			$value=str_replace('=0A','=0D=0A',$value); //crlf newlines. Didn't work with \r\n before quoted_printable_encode somehow.
			if($value != $oldValue){
				$newProp = new GO_Base_VObject_VCalendar_Property($propName, $value);							
				$vobject->$propName->add('ENCODING','QUOTED-PRINTABLE');
				foreach($vobject->$propName->parameters as $param){
					$newProp->add($param);
				}
				
				if(!isset($newProp->charset))
					$newProp->add('charset','UTF-8');
				
				unset($vobject->$propName);
				$vobject->add($newProp);
			}
		}
	}
	
	/**
	 * Converts a vcalendar 1.0 component to an icalendar 2.0 component.
	 * 
	 * @param Sabre_VObject_Component $vobject 
	 */
	public static function convertVCard30toVCard21(Sabre_VObject_Component $vobject){
		
		$qpProperies=array('NOTE','FN','N');
		
		if($vobject->version=='3.0'){
			$vobject->version='2.1';
			foreach($qpProperies as $propName){
				self::_quotedPrintableEncode($vobject, $propName);
			}
		}	
	}
	
//	/**
//	 * Parses the file and returns the top component. Additionally a version 1.0 
//	 * vcalendar object will be converted to a version 2.0 object.
//	 * 
//	 * @param string $data 
//	 * @return Sabre_VObject_Element 
//	 */
//	public static function read($data) {
//		$vobject = parent::read($data);		
//		self::convertVCalendarToICalendar($vobject);
//		return $vobject;
//	}
	
}