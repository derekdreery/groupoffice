<?php

/**
 * VObject Property
 *
 * A property in VObject is usually in the form PARAMNAME:paramValue.
 * An example is : SUMMARY:Weekly meeting 
 *
 * Properties can also have parameters:
 * SUMMARY;LANG=en:Weekly meeting.
 *
 * Parameters can be accessed using the ArrayAccess interface. 
 *
 * @package Sabre
 * @subpackage VObject
 * @copyright Copyright (C) 2007-2011 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class GO_Base_VObject_VCalendar_Property extends Sabre\VObject\Property {


    /**
     * Turns the object back into a serialized blob. 
     * 
     * @return string 
     */
    public function serialize() {

        $str = $this->name;
        if ($this->group) $str = $this->group . '.' . $this->name;

        if (count($this->parameters)) {
            foreach($this->parameters as $param) {
                
                $str.=';' . $param->serialize();

            }
        }
        $src = array(
            '\\',
            "\n",
//						";",
        );
        $out = array(
            '\\\\',
            '\n',
//						"\\;",
        );
				
				$quoteSemiColonFields = array('note','location','summary','description');
				if(in_array(strtolower($this->name),$quoteSemiColonFields)){
					$src[]=';';
					$out[]='\\;';
				}
				
        $str.=':' . str_replace($src, $out, $this->value)."\r\n";
				
				//workaround funambol bug. They want it one long big string
				return $str;

//        $out = '';
//        while(strlen($str)>0) {
//            if (strlen($str)>75) {
//                $out.= substr($str,0,75) . "\r\n";
//                $str = substr($str,75);
//            } else {
//                $out.=$str . "\r\n";
//                $str='';
//                break;
//            }
//        }
//
//        return $out;

    }
}
