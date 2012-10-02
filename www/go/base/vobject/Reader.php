<?php
/**
 * Class extended from and based on parts of Sabre_VObject_Reader, with
 * functionality to read multiple VObjects from a single VCard.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @author WilmarVB <wilmar@intermesh.nl>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License 
 */

//require vendor lib SabreDav vobject
//require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');
		
class GO_Base_VObject_Reader extends Sabre_VObject_Reader{
	
  const REGEX_ELEMENT_STRING = "/^(?P<name>[A-Z0-9-\.]+)(?:;(?P<params>([^:^\"]|\"([^\"]*)\")*))?:(?P<value>.*)$/i";
	const REGEX_PARAM_STRING = '/(?<=^|;)(?P<paramName>[A-Z0-9-]+)(=(?P<paramValue>[^\"^;]*|"[^"]*")(?=$|;))?/i';
				
//	public static function prepareData($dataString) {
//		$outputVObjects = array();
//		
//		
//		//remove quoted printable line breaks
//		$dataString = GO_Base_Util_String::normalizeCrlf($dataString,"\n");
//		$dataString = str_replace("=0D=0A=\n", "=0D=0A=",$dataString);
//		
//		$lines = explode("\n",$dataString);
//		$currentlyBusy = false;
//		
//		if (stripos($lines[count($lines)-2],'END:')!==0)
//			throw new Sabre_VObject_ParseException('Invalid VCard: it does not end with the END element.');
//		
//		// Make sure the lines are put in an array separately per VCard.		
//		for ($i=0; $i<count($lines); $i++) {
//			
//			if (stripos($lines[$i],"BEGIN:VCARD")!==false) {
//			
//				if ($currentlyBusy)
//					throw new Sabre_VObject_ParseException('BEGIN element found prematurely in line #'.($i+1).'.');
//				$currentlyBusy=true;
//				$currentVObject = Sabre_VObject_Component::create(strtoupper(substr($lines[$i],6)));
//				
//			} elseif (stripos($lines[$i],"END:VCARD")!==false) {
//			
//				if (!$currentlyBusy)
//					throw new Sabre_VObject_ParseException('END element found prematurely in line #'.($i+1).'.');
//				$currentlyBusy=false;
//				$outputVObjects[] = $currentVObject;
//			
//			} else if (empty($lines[$i]) || $lines[$i][0]==="\t") {
//
//				// DO NOTHING
//				
//			} else {
//				if ($lines[$i+1][0]===" " || $lines[$i+1][0]==="\t" || strpos($lines[$i+1],'\n')===0 || strpos($lines[$i+1],'\r\n')===0) {
//					$lines[$i+1] = $lines[$i].$lines[$i+1];
//					continue;
//				}
//				if (!$currentlyBusy)
//					throw new Sabre_VObject_ParseException('Before line #'.($i+1).', there must be a BEGIN element.');
//
//				$result = preg_match(self::REGEX_ELEMENT_STRING,$lines[$i],$matches);
//        if (!$result)
//          throw new Sabre_VObject_ParseException('Invalid VObject, line ' . ($i+1) . ' did not follow the icalendar/vcard format');
//
//        $vProp = Sabre_VObject_Property::create(
//					strtoupper($matches['name']),
//					preg_replace_callback('#(\\\\(\\\\|N|n|;|,))#', array('self','checkForN'), $matches['value'])
//				);
//
//        if ($matches['params'])
//					foreach(self::readParams($matches['params']) as $param)
//						$vProp->add($param);
//				
//				$currentVObject->add($vProp);
//				
//			}
//			
//		}
//		return $outputVObjects;
//	}
//	
//	protected static function checkForN($matches) {
//		if ($matches[2]==='n' || $matches[2]==='N') {
//			return "\n";
//		} else {
//			return $matches[2];
//		}
//	}
	
//	protected static function readParams($params) {
//		
//		preg_match_all(self::REGEX_PARAM_STRING, $params, $matches,  PREG_SET_ORDER);
//		
//		$outParams = array();	
//		foreach($matches as $match) {
//			$value = isset($match['paramValue'])?$match['paramValue']:null;
//			if (isset($value[0])) {
//				// Stripping quotes, if needed
//				if ($value[0] === '"') $value = substr($value,1,strlen($value)-2);
//			} else {
//				$value = '';
//			}
//			$outParams[] = new Sabre_VObject_Parameter($match['paramName'], preg_replace_callback('#(\\\\(\\\\|N|n|;|,))#',array('self','checkForN'), $value));
//		}
//		return $outParams;
//	}
	
	public static function parseDuration($duration){
		preg_match('/(-?)P([0-9]+[WD])?T?([0-9]+H)?([0-9]+M)?([0-9]+S)?/', (string) $duration, $matches);


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
					
					for($i=0;$i<count($child->children);$i++){
						$property = $child->children[$i];
						if((string) $property->value==""){
							GO_Syncml_Server::debug("Unsetting: ".$property->name);
							array_splice($child->children, $i, 1);
							$i--;
						}
						
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
//					GO::debug($value);
//					$value = GO_Base_Util_String::to_utf8($value);
					$property->setValue($value);				
					unset($property['ENCODING']);
				}
				
				//vcard 2.1 is read as EMAIL;INTERNET=;HOME=:mschering@intermesh.nl
				//We must correct that into EMAIL;TYPE=INTERNET,HOME:mschering@intermesh.nl
				//$param = new Sabre_VObject_Parameter();
				if($property->name=='EMAIL' || $property->name=='TEL' || $property->name=='ADR'){
					$types = array();
					foreach ($property->parameters as $param){
						if(empty($param->value)){
							$types[]=$param->name;
							unset($property[$param->name]);
						}
					}
					if(count($types))
						$property->add(new GO_Base_VObject_Parameter('TYPE', implode(',', $types)));					
				}
				
				if($property->name=='BDAY' && !empty($property->value) && !strpos($property->value, '-')){
					$property->value = substr($property->value,0,4).'-'.substr($property->value,4,2).'-'.substr($property->value,6,2);
//					echo $property->value."\n";
				}
			}
		}	
	}
	
	private static function _quotedPrintableEncode($vobject, $propName){
		if(isset($vobject->$propName) && $vobject->$propName!=''){			
			$oldValue = (string) $vobject->$propName;

			$value = quoted_printable_encode($oldValue);	
			
			//put the quoted printable lines in one big line otherwise funambol won't work.
			$value =  str_replace(array("\r","=\n"), '', $value);
			$value=str_replace('=0A','=0D=0A',$value); //crlf newlines. Didn't work with \r\n before quoted_printable_encode somehow.
			$value=str_replace("\n",'=0D=0A',$value);			
		
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
	
	public static function read($data) {
		
		//remove quoted printable line breaks
		$data = GO_Base_Util_String::normalizeCrlf($data,"\n");
		if(strpos($data,'QUOTED-PRINTABLE')){		
			$data = str_replace("=\n", "",$data);
		}
		//workaround for funambol bug		
		$data = str_replace('EXDATE: ', 'EXDATE:', $data);
		
		return parent::read($data);
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
			
			//vcard 3.0 uses EMAIL;TYPE=INTERNET,HOME:mschering@intermesh.nl
			//We must convert that into EMAIL;INTERNET;HOME:mschering@intermesh.nl for 2.1
			foreach($vobject->children() as $property)
			{
				if(!empty($property['TYPE'])){										
					$types =explode(',',$property['TYPE']);					
					$property->name.=';'.implode(';',$types);								
					unset($property['TYPE']);
				}							
			}
		}	
	}
	
}