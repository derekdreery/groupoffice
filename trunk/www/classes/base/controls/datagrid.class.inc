<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.12 $ $Date: 2006/05/03 15:34:13 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Creates a table of a database table that you can use to insert and update rows
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

class datagrid extends db
{
	var $table_name = '';
	var $form_name = '';
	var $fields = array();
	var $id_field = 0;
	var $query = '';	
	var $start_row = 0;
	var $max_rows = 0;	
	var $row_count = 0;	
	var $delete_tables = array();	
	var $field_values = array();
	var $columns = array();
	
	function datagrid($form_name, $table_name, $id_field_name='', $query='', $delete_tables='')
  {
  	if($delete_tables != '')
  	{
	  	$this->delete_tables = $delete_tables;
  	}
    $this->db();
    $this->table_name = $table_name;
    $this->form_name = $form_name;      
    $this->id_field_name = $id_field_name;   
    $this->query = $query;    

		$this->start_row = isset($_POST[$this->table_name]['start_row']) ? $_POST[$this->table_name]['start_row'] : '0';
		$this->max_rows = isset($_POST[$this->table_name]['max_rows']) ? $_POST[$this->table_name]['max_rows'] : $_SESSION['GO_SESSION']['max_rows_list'];    
  }
  
  function add_column($name, $friendlyname, $type)
  {
  	$this->columns[$name] = array('friendlyname'=>$friendlyname, 'type'=>$type);
  }
  
  function set_field_value($name, $value)
  {
  	$this->field_values[$name] = $value;
  }
  
  function print_datagrid()
  {  
  	return $this->header().$this->data().$this->footer();
  }
  
  function header()
  {
  	if(isset($_POST[$this->table_name]['task']))
    {
    	if($_POST[$this->table_name]['task'] == 'update')
    	{
	    	$this->update();
	    }
    	
    	if($_POST[$this->table_name]['task'] == 'insert')
    	{
    		$this->insert();
    	}
    	if(isset($_POST[$this->table_name]['delete_row_id']) && $_POST[$this->table_name]['delete_row_id'] > 0)
    	{
    		$this->delete(smart_addslashes($_POST[$this->table_name]['delete_row_id']));
    	}
    }
    
  	$headers = 	'<input type="hidden" name="'.$this->table_name.'[task]" value="" />'.
  							'<input type="hidden" name="'.$this->table_name.'[delete_row_id]" value="0" />'.
  							'<input type="hidden" name="'.$this->table_name.'[start_row]" value="'.$this->start_row.'" />'.
  							'<input type="hidden" name="'.$this->table_name.'[max_rows]"  value="'.$this->max_rows.'" />'.
  							'<table class="go_table"><tr>';
  							
    foreach($this->columns as $column)
    {
				$headers .= '<th>'.$column['friendlyname'].'</td>';
    }
    $headers .= '<th>&nbsp;</td></tr>';		
		return $headers;  
  }
  
  function footer()
  {  
  
  	$footer = $this->pagination();
  
  	$footer .= '</table><br />';		
		$footer .= '<input type="button" class="button" style="width: 100px;" '.
								' value="'.$GLOBALS['cmdApply'].'" onclick="document.'.
								$this->form_name.'.elements[\''.$this->table_name.'[task]\'].value=\'update\';document.'.
								$this->form_name.'.submit();" '.
								'onmouseover="javascript:this.className=\'button_mo\';" '.
								'onmouseout="javascript:this.className=\'button\';" />'.
								'&nbsp;&nbsp;'.
								
								'<input type="button" class="button" style="width: 100px;" '.
								' value="'.$GLOBALS['cmdInsert'].'" onclick="document.'.
								$this->form_name.'.elements[\''.$this->table_name.'[task]\'].value=\'insert\';document.'.
								$this->form_name.'.submit();" '.
								'onmouseover="javascript:this.className=\'button_mo\';" '.
								'onmouseout="javascript:this.className=\'button\';" />';						
		
		$footer .= '<script type="text/javascript">'.
								'function delete_row(id, text)'.
								'{if(confirm(text))'.
								'{document.'.$this->form_name.'.elements[\''.$this->table_name.'[delete_row_id]\'].value=id;'.
								'document.'.$this->form_name.'.submit();}}'.
								
								'function change_list(start_row, max_rows)'.
								'{document.'.$this->form_name.'.elements[\''.$this->table_name.'[start_row]\'].value=start_row;'.
								'document.'.$this->form_name.'.elements[\''.$this->table_name.'[max_rows]\'].value=max_rows;'.
								'document.'.$this->form_name .'.submit();}</script>';

		
		
		return $footer;
  }
  
  
  function pagination()
  {
  	global $GO_THEME, $cmdPrevious, $cmdNext, $cmdShowAll;
  	
  	$links = '';
	  $max_links=10;
	  if ($this->max_rows != 0)
	  {
	    if ($this->row_count > $this->max_rows)
	    {
	      $links = '<table class="NavLinks"><tr><td>';
	      $next_first = $this->start_row+$this->max_rows;
	      $previous_first = $this->start_row-$this->max_rows;
	      if ($this->start_row != 0)
	      {
					$links .= '<a href="javascript:change_list(0, '.$this->max_rows.');">&lt&lt</a>&nbsp;';
					$links .= '<a href="javascript:change_list('.$previous_first.', '.$this->max_rows.');">'.$cmdPrevious.'</a>&nbsp;';
	      }else
	      {
					$links .= '<font color="#cccccc">&lt&lt '.$cmdPrevious.'</font>&nbsp;';
	      }
	
	      $start = ($this->start_row-(($max_links/2)*$this->max_rows));	
	      $end = ($this->start_row+(($max_links/2)*$this->max_rows));
	
	      if ($start < 0)
	      {
					$end = $end - $start;
					$start=0;
	      }
	      if ($end > $this->row_count)
	      {
					$end = $this->row_count;
	      }
	      if ($start> 0)
	      {
					$links .= '...&nbsp;';
	      }
	
	      for ($i=$start;$i<$end;$i+=$this->max_rows)
	      {
					$page = ($i/$this->max_rows)+1;
					if ($i==$this->start_row)
					{
					  $links .= '<b><i>'.$page.'</i></b>&nbsp;';
					}else
					{
					  $links .= '<a href="javascript:change_list('.$i.', '.$this->max_rows.');">'.$page.'</a>&nbsp;';
					}
	      }
	
	      if ($end < $this->row_count)
	      {
					$links .= '...&nbsp;';
	      }
	
	      $last_page = floor($this->row_count/$this->max_rows)*$this->max_rows;
	
	      if ($this->row_count > $next_first)
	      {
					$links .= '<a href="javascript:change_list('.$next_first.', '.$this->max_rows.');">'.$cmdNext.'</a>&nbsp;';
					$links .= '<a href="javascript:change_list('.$last_page.', '.$this->max_rows.');">&gt&gt</a>';
	      }else
	      {
					$links .= '<font color="#cccccc">'.$cmdNext.' &gt&gt</font>';
	      }
	      $links .= '</td><td align="right"><a class="normal" href="javascript:change_list(0, 0);">'.$cmdShowAll.'</a></td></tr></table>';	
	      $links = '<tr height="20"><td colspan="99">'.$links.'</td></tr>';	       				
	    }
	    return $links;
	  }
  }
  
  function data()
  {
  	global $GO_THEME, $strDeleteRow, $strNoData;
  	$sql = $this->query;
  	
  	if($this->max_rows > 0)
  	{
  		$this->query($sql);
  		$this->row_count = $this->num_rows(); 		
  	}
  	
  	if($this->max_rows > 0)
  	{
  		$sql .= " LIMIT ".$this->start_row.",".$this->max_rows;
  		$this->query($sql);
  	}else
  	{
			$this->query($sql);
			$this->row_count = $this->num_rows();
  	}
  	
  	
	 	$data = '';
	 	if($this->row_count > 0)
	 	{
		 	$i = 0;	 	
	  	while($this->next_record(MYSQL_ASSOC))
	  	{
	  		$data .= '<tr>';
	

				$data .= '<input type="hidden" name="'.$this->table_name.'[data]['.$i.']['.$this->id_field_name.']" value="'.$this->f($this->id_field_name).'" />';
	  		foreach($this->columns as $field => $properties)
	  		{
  				switch($properties['type'])
  				{
  					case 'checkbox':
  						$data .= '<td><input type="checkbox" name="'.$this->table_name.'[data]['.$i.']['.$field.']" value="1"';
  						if($this->f($field)=='1')
  						{
  							$data .= ' checked';
							}
  						$data .= ' /></td>';
  					break;
  					default:
  						$data .= '<td><input type="text" name="'.$this->table_name.'[data]['.$i.']['.$field.']" class="textbox" value="'.$this->f($field).'" /></td>';
  					break;
  				}
	  		} 
	  		foreach($this->field_values as $fieldname => $value)
				{
					$data .= '<input type="hidden" name="'.$this->table_name.'[data]['.$i.']['.$fieldname.']" value="'.$value.'" />';
				} 		
	  		
	  		$data .= '<td><a href=\'javascript:delete_row("'.$this->f($this->id_field_name).
							'","'.htmlspecialchars($strDeleteRow, ENT_QUOTES).'")\'"><img src="'.
							$GO_THEME->images['delete'].'" border="0"></a></td>';
	  		
				$data.= "</tr>\n";
				$i++;	
	  	}
	 	}else
	 	{
	 			$data .= '<tr><td colspan="99">'.$strNoData.'</td></tr>';
	 	}
  	return $data;
  }
  
  function update()
  {
  	if(isset($_POST[$this->table_name]['data']))
  	{
	  	 for($i=0;$i<count($_POST[$this->table_name]['data']);$i++)
	  	 {
	  	 		$values = $_POST[$this->table_name]['data'][$i];
	  	 		foreach($this->columns as $fieldname=>$properties)
	  	 		{
	  	 			$values[$fieldname] = isset($values[$fieldname]) ? $values[$fieldname] : '0'; 
	  	 		}
	  	 		$this->update_row($this->table_name, $this->id_field_name, $values);
	  	 }
  	}
  }
  
  function insert()
  {
  	$this->update();
  	
  	$next_id = $this->nextid($this->table_name);
  	
  	$sql = "INSERT INTO `".$this->table_name."` (".$this->id_field_name;
  	$values = "VALUES ('$next_id'";
		foreach($this->field_values as $fieldname => $value)
  	{
  			$sql .= ", $fieldname";
  			$values .= ",'$value'";  				
  	}
		$sql .=") ".$values.");";
		return $this->query($sql);
  }
  
  function delete($id)
  {
 		$sql = "DELETE FROM `".$this->table_name."` WHERE ".$this->id_field_name."='$id'"; 		
		$this->query($sql);
		
		for($i=0;$i<count($this->delete_tables);$i++)
		{
			$sql = "DELETE FROM `".$this->delete_tables[$i]['name']."` WHERE `".$this->delete_tables[$i]['id_field']."`='$id'"; 		
			$this->query($sql);
		}
  }
}
