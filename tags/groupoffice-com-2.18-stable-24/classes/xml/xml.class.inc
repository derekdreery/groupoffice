<?php
/**
* @copyright 2003
* @author Nicolas Bougues, <nicolas@bougues.net>
* @version $Revision: 1.12 $ $Date: 2005/11/25 16:06:03 $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your
* option) any later version.
**/

/**
* A simple class to manage XML documents
*
* @package Framework
* @subpackage XML  
* @since    Group-Office 2.10
*/

class xml_node_c {
	function get_attribute($id)
  {
  	if(isset($this->_attribs[$id]))
  	{
  		return $this->_attribs[$id];
		}
		return false;
  }
};

class xml_tree_node_c extends xml_node_c {

  var $_name;
  var $_children;
  var $_attribs;

  function children_count () {
    return count($this->_children);
  }

  function &children() {
    return $this->_children;
  }

  function &get_child ($name) {

    $seg = substr ($name, 0, strcspn ($name, "/"));
		
    if (is_array ($this->_children)) {
      foreach ($this->_children as $child) {

				if (strcasecmp ($child->get_name(), $seg) == 0) {
				
				  if ($seg == $name) {
				    return $child;
				  } else {
				    $child = &$child->get_child (substr ($name,
								      strcspn ($name, "/") + 1));
						return $child;
				  }
				}
      }
    } 

    //    printf ("get_child in %s for %s(%s) returning null\n", 
    //	    $this->_name, $name, $seg);
    $child = null;
    return $child;
  }

  function get_data ($name='') {
    
    //    printf ("Get data for %s\n", $name);
		
	if($name=='')
	{
		return trim($this->_children[0]->get_data());
	}else
	{
	    $tmp = $this->get_child ($name);
	    
	    if ($tmp)
	    {
	      return trim($tmp->_children[0]->get_data());
	    }else
	    {
	      return NULL;
	     }
	 }
  }
  
  

  function &xml_tree_node_c ($name, $attribs) {
    $this->_name = $name;
    $this->_attribs = $attribs;
    $this->_children = array();
    return $this;
  }

  function set_attribs ($attribs) {
    $this->_attribs = $attribs;
  }

  function &add_child ($xml_node) {
    return ($this->_children[] = &$xml_node);
  }

  function &new_child ($name, $attribs) {
    $tmp = &$this->add_child (new xml_tree_node_c ($name, $attribs));
    return $tmp;
  }

  function &new_child_data ($name, $attribs, $data) {
    $tmp = &$this->add_child (new xml_tree_node_c ($name, $attribs));
    $tmp->add_child (new xml_text_node_c ($data));

    return $tmp;
  }

  function get_name () {

    return $this->_name;
  }

  function print_node ($recurse, $rec_level) {

    $result = "";

    /*for ($i = 0; $i < 2*$rec_level; $i++)
      $result .= " ";*/

    $result .= "<" . $this->_name;

    if (is_array ($this->_attribs)) {
      foreach ($this->_attribs as $key => $val) {
	$result .= " " . $key . "=\"" . $val . "\""; 
      }
    }

		if(count($this->_children) > 0)
		{
    	$result .= ">";

	    $newl = 0;
	
	    if ($recurse) {
	      foreach ($this->_children as $child) {
					if (!$newl && (get_class ($child) != "xml_text_node_c")) {
					  $newl = 1;
					  $result .= "\n";
					}
					$result .= $child->print_node ($recurse, $rec_level+1);
	      }
	    }
	
	   /* if ($newl)
	      for ($i = 0; $i < 2*$rec_level; $i++)
	        $result .= " ";*/
	
	    $result .= "</" . $this->_name . ">\n";
	  }else
	  {
	  	//$result .= "/>\n";
	  	$result .= "></" . $this->_name . ">\n";
	  }

    return $result;
  }
   function get_xml()
	{
		return $this->print_node(1,0);
	}

	function get_size()
	{
		return strlen($this->get_xml());
	}
};

class xml_text_node_c extends xml_node_c {

  var $_data;

  function &xml_text_node_c ($data) {
    $this->_data = $data;
    return $this;
  }

  function get_data () {
    return $this->_data;
  }
  
	function get_name () {
		
    return isset($this->_name) ? $this->_name : false;
  }

  function print_node ($recurse, $rec_level) {
    
    $result = "";

    $result .= $this->_data;

    return $result;
  }
  function get_xml()
	{
		return "<?xml version=\"1.0\" ?>\n".
			$this->print_node(1,0);
	}

	function get_size()
	{
		return strlen($this->get_xml());
	}
};



function text_to_xml ($xml_text) {

  $xml_parser = xml_parser_create ();
  xml_parser_set_option ($xml_parser, XML_OPTION_CASE_FOLDING, FALSE);

  if (!xml_parse_into_struct ($xml_parser, $xml_text, $values, $index)) {
  $error = sprintf("XML error: %s at line %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser));		
  	go_log(LOG_DEBUG, sprintf($error));
  	go_log(LOG_DEBUG, "XML: ".$xml_text);
  	
    die($error);
  }
  
  xml_parser_free($xml_parser);

  // Now, make the objects

  $stackpos = 0;
  $stack = array();

  foreach ($values as $val) {
    switch ($val["type"]) {
    case "open":
      if (count($stack)) {
	$stack[$stackpos] = & $stack[$stackpos-1]->new_child ($val["tag"],
							      @$val["attributes"]);
      } else {
	$stack[$stackpos] =  &new xml_tree_node_c ($val["tag"],
						   @$val["attributes"]);
      }
      $stackpos++;
      break;
    case "complete":

      if (count($stack))
	$stack[$stackpos] = 
	  & $stack[$stackpos-1]->add_child (new xml_tree_node_c ($val["tag"], 
								 @$val["attributes"]));
      else {
	$stack[$stackpos] = & new xml_tree_node_c ($val["tag"], 
						   @$val["attributes"]);
      }
      
      $stack[$stackpos]->add_child (new xml_text_node_c (@$val["value"]));
      break;
    case "close":
      if ($stackpos > 0) {
	$stackpos--;
      }
      break;
    default:
      //      printf ("UNHANDLED: %s\n", $val["type"]);
      //      print_r ($val);
    }
  }

  return $stack[0];
}
