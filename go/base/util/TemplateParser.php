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
 * @version $Id: go_template_parser.class.inc.php 4966 2010-06-03 13:31:46Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.util
 */

/**
 * Parses a template
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: go_template_parser.class.inc.php 4966 2010-06-03 13:31:46Z mschering $
 * @copyright Copyright Intermesh
 * @license AGPL Affero General Public License
 * @package GO.base.util
 * @since Group-Office 3.0
 */
class GO_Base_Util_TemplateParser
{
	public $openTagSymbol = '&lt;';
	public $closeTagSymbol = '&gt;';
	
	private $_tags = array('gotpl');
	private $_attributes;
	private $_values;
	

	private $_leaveEmptyTags=false;

	
	private function _getTag($tag, $content) {
		$start_pos = strpos($content, $this->openTagSymbol.$tag);
		if ($start_pos !== false) {
			$end_pos = $this->_getEndPos($tag, $content, $start_pos);
			$sub_start_pos = 	strpos($content, $this->openTagSymbol.$tag, $start_pos+strlen($this->openTagSymbol.$tag));
			
			if($sub_start_pos!== false)
			{
				$sub_end_pos = $end_pos;
				
				//echo $sub_start_pos.' < '.$sub_end_pos."\n---\n";
				
				while($sub_start_pos<$sub_end_pos)
				{
					$sub_end_pos = $this->_getEndPos($tag, $content, $sub_end_pos);
					$sub_start_pos = 	strpos($content, $this->openTagSymbol.$tag, $sub_start_pos+strlen($this->openTagSymbol.$tag));
					
					if($sub_end_pos)
						$end_pos = $sub_end_pos;
				}	
			}
			if($end_pos === false)
			{
				return false;
			}
			$tag_length = $end_pos-$start_pos;
			return substr($content, $start_pos, $tag_length);
		}
		return false;
	}
	
	
	private function _getEndPos($tag, $content, $offset=0)
	{
		$end_pos = strpos($content, $this->openTagSymbol.'/'.$tag.$this->closeTagSymbol, $offset);
		if($end_pos!==false)
		{
			$end_pos+=strlen($this->openTagSymbol.'/'.$tag.$this->closeTagSymbol);
		}
		return $end_pos;		
	}

	private function _getAttributes($tag) {
		$attributes = array ();
		$in_value = false;
		$in_name = false;
		$name = '';
		$value = '';
		$length = strlen($tag);
		
		$exit=false;
		
		for ($i = 0; $i < $length; $i ++) {
			
			if($exit)
			{
				break;
			}
			$char = $tag[$i];
			switch ($char) {
				case '"' :
					if ($in_value) {
						$in_value = false;

						$attributes[trim($name)] = trim($value);
						$name = '';
						$value = '';
					} else {
						$in_value = true;
					}

					break;

				case ' ' :
					if (!$in_value) {
						$in_name = true;
					} else {
						$value .= $char;
					}
					break;

				case '=' :
					$in_name = false;
					if ($in_value) {
						$value .= $char;
					}
					break;

				default :
					if ($in_name) {
						$name .= $char;
					}

					if ($in_value) {
						$value .= $char;
					}
					break;
			}
		}
		return $attributes;
	}

	
	private function _replaceTags($content)
	{
		foreach($this->_attributes as $tag=>$value)
		{
			if($this->_leaveEmptyTags && empty($value))
				continue;
		
			//echo $tag .' -> '.$value."\n\n";
			
			if(!is_array($value) && !is_object($value)){		
				$content = str_replace('{'.$tag.'}', $value, $content);
				$content = str_replace('%'.$tag.'%', $value, $content);
			}
		}
	
		return $content;
	}
	
	/**
	 * Finds all tags in the string $content and replaces them with the given values.
	 * 
	 * Tags are formatted like this: {attribute_name} or {contact:name}.
	 * 
	 * @param string $content
	 * @param array $attributes eg. array('attributeName'=>'value')
	 * @param boolean $leaveEmptyTags Leave other tags in the document or keep them for further processing.
	 * @return string 
	 */
	public function parse($content, $attributes, $leaveEmptyTags=false)
	{
		$this->_attributes=$attributes;
		
		$this->_leaveEmptyTags=$leaveEmptyTags;
		$content = $this->_fixTags($content);
		$content = $this->_parseTags($content);
		$content = $this->_replaceTags($content);
		
		if(!$leaveEmptyTags){
			$content = preg_replace('/{([^\s}]*)}/U','',$content);
			//$content = preg_replace('/%([^%]*)%/U','',$content); //breaks email templates!
		}		
		return $content;
	}
	
	private static function _fixTagsCallback($tag) {
		//Sometimes people change styles within a {autodata} tag.
		//Then there are XML tags inside the GO template tag.
		//We place them outside the tag.
		//go_debug($tag);
		$tag = stripslashes($tag);
		preg_match_all('/<[^>]*>/', $tag, $matches);

		$replacement = implode('', $matches[0]) . strip_tags($tag);
		//go_debug($replacement);
		//go_debug('****');

		return $replacement;
	}

	private function _fixTags($content) {
		return preg_replace('/{[^}]*}/Ue', "self::_fixTagsCallback('$0')", $content);
	}

	private function _parseTags($content)
	{
			
		foreach($this->_tags as $tagname)
		{
			while ($tag = $this->_getTag($tagname, $content)) {
	
				$attributes = $this->_getAttributes($tag);
						
				$print = !empty($this->_attributes[$attributes['if']]);

				if($print)
				{
					$start_pos = strpos($tag, $this->closeTagSymbol);					
					$tagcontent = substr($tag, $start_pos+strlen($this->closeTagSymbol));					
					$tagcontent = substr($tagcontent,0, strlen($tagcontent)-strlen($this->openTagSymbol.'/'.$tagname.$this->closeTagSymbol));	
					$this->_parseTags($tagcontent);					
				}else
				{
					$tagcontent = '';
				}
								
				$content = str_replace($tag, $tagcontent, $content);
			}
		}
		
		return $content;
		
	}
	
}
