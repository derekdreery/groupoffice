<?php
/**
 * This class highlights  text matches a search string (keyword) in html based documents, without destroying html-tags. 
 * This was necessary to scan database-entry written in html by keywords, and display them in another style.
 * 
 * Advanced keyword highlighter, keep HTML tags safe.
 *
 * @author Bojidar Naydenov a.k.a Bojo (bojo2000@mail.bg) & Antony Raijekov a.k.a Zeos (dev@strategma.bg)
 * @version 2.1
 * @copyright GPL
 * 
 * @package go.utils
 */
class highlight 
{
    var $keyword;
	var $replacement;
	var $hightlight_bad_tags = array("A","IMG");	//add here more, if you want to filter them	
	
	//constructor
	function highlight ($keyword=false,$replacement=false)
	{
		$this -> keyword = $keyword;
		$this -> replacement = $replacement;
	} //end func highlight

	//private
	function highlighter($matches)
	{		
		//check for bad tags and keyword					
		if (!in_array(strtoupper($matches[2]),$this -> hightlight_bad_tags))  
		{
			//put template [replacement]
			$proceed =  preg_replace("#\b(".$this -> keyword.")\b#si",str_replace("{keyword}",$matches[3],$this -> replacement),$matches[0]);
		}
		else	//return as-is
		{
			$proceed = $matches[0];
		}
		return stripslashes($proceed);
	} //end func hightlighter

	//main api
	function process($text,$keyword = false,$replacement = false)
	{
		//if there are specific keyword/replacement given
		if($keyword != false) $this -> keyword = $keyword;
		if($replacement != false) $this -> replacement = $replacement;

		//process text array(&$this, 'method_name'), 
		if((isset($this -> keyword)) AND (isset($this -> replacement)))
			return preg_replace_callback("#(<([A-Za-z]+)[^>]*[\>]*)*(".$this -> keyword.")\b(.*?)(<\/\\2>)*#si",array(&$this, 'highlighter'), $text);
		else
			return $text;
	} //end func process

} // end class
