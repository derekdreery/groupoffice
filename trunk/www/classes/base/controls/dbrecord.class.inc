<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.9 $ $Date: 2005/05/20 09:24:22 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Can return and save a database record using HTML form elements
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

class dbrecord extends db
{
	var $table_name;
	var $key_field;
	var $key_value;
	var $dbrecord;
	
	function dbrecord($table_name, $key_field, $key_value='')
	{
		$this->db();		
		
		$this->table_name = $table_name;
		$this->key_field = $key_field;
		$this->key_value = $key_value;
		
		$this->get_dbrecord();
	}
	
	function get_dbrecord()
	{
		if(isset($_POST[$this->table_name]))
		{
			$this->dbrecord = array_map('trim', $_POST[$this->table_name]);
			$this->dbrecord = array_map('smart_addslashes', $this->dbrecord);
		}elseif($this->key_value != '')
		{
			$sql = "SELECT * FROM `".$this->table_name."` WHERE `".$this->key_field."`='".$this->key_value."'";
			$this->query($sql);
			if($this->next_record())
			{
				$this->dbrecord = $this->Record;
			}
		}else
		{
			//create new object
			$sql = "SHOW FIELDS FROM `".$this->table_name."`";
			$this->query($sql);
			
			while($this->next_record())
			{
				$this->dbrecord[$this->f('Field')] = '';
			}
		}
	}
	
	function get_elements()
	{	
		$elements = array();		
		
		$sql = "SHOW FIELDS FROM `".$this->table_name."`";
		$this->query($sql);
		
		while($this->next_record())
		{
			$name = $this->f('Field');			
			$length = '';
			$type = $this->f('Type');
			
			if($pos = strpos($type,'('))
			{				
				$length = substr($type, $pos+1, (strlen($type)-$pos-2));
				$type = substr($type,0,$pos);		
			}
			$value = isset($this->dbrecord[$name]) ? $this->dbrecord[$name] : '';
			$elements[$name] =$this->get_element($type, $name, $value, $length);
		}
		return $elements;
	}
	
	function set_value($key, $value)
	{
		$this->dbrecord[$key] = $value;
	}
	
	function get_value($key)
	{
		return $this->dbrecord[$key];
	}
	
	function save()
	{
		if($this->key_value != '')
		{
			$this->dbrecord[$this->key_field] = smart_addslashes($this->key_value);
			if($this->update_row($this->table_name, $this->key_field, $this->dbrecord))
			{
				return $this->key_value;
			}			
		}else
		{			
			$this->dbrecord[$this->key_field] = $this->key_value = $this->nextid($this->table_name);
			if($this->insert_row($this->table_name, $this->dbrecord))
			{
				return $this->key_value;
			}			
		}
		return false;
	}
	
	function get_element($type, $name, $value, $maxlength=0)
	{
		switch($type)
		{
			case 'enum':				
				$radiolist = new radio_list($this->table_name.'['.$name.']', $value);
				
				$options = explode(',', $maxlength);
				$element = '';
				while($option = array_shift($options))
				{
					$option = substr($option,1,strlen($option)-2);
					$element .= $radiolist->get_option($option, $option).'&nbsp;';
				}
			break;
			
			case 'text':
				$element = '<textarea class="textbox" style="width: 400px; height:80px;" name="'.$this->table_name.'['.$name.']">'.$value.'</textarea>';
			break;
			
			default:
				$element = '<input class="textbox" style="width: 200px" type="text" name="'.$this->table_name.'['.$name.']" value="'.$value.'" maxlength="'.$maxlength.'" />';
			break;			
		}
		return $element;
	}
}
?>
