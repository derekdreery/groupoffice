<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2006/11/21 16:25:34 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @packages Framework
 * @subpackage Controls
 */

/**
 * Create an ACL control element. You can use this to alter an ACL that is
 * used to secure any object in Group-Office.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/select.class.inc');

class select_users extends select{

	var $id;
	var $task;
	var $form_name;
	var $selected = array();
	var $users = array();
	
	function select_users($id, $form_name='0')
	{
			$this->id = $id;
			$this->form_name = $form_name;		
			$this->task = isset($_POST[$id]['task']) ? $_POST[$id]['task'] : '';
			
			$this->selected = isset($_POST[$id]['selected']) ? $_POST[$id]['selected'] : array();			
	}
	
	function get_header()
	{
		global $GO_CONFIG;
		return '<script type="text/javascript" src="'.$GO_CONFIG->host.'javascript/select_users.js"></script>';
	}
	
	function add_user($id)
	{
		$this->users[]=$id;				
	}
	
	function get_html()
	{
		global $GO_USERS, $GO_SECURITY;
		
		$input = new input('hidden', $this->id.'[task]','',false);
		$this->add_outerhtml_element($input);	
			
		if($this->task == 'search')
		{
			$search_field = isset ($_POST[$this->id]['search_field']) ? $_POST[$this->id]['search_field'] :'';

			$select = new select($this->id.'[search_field]', $search_field);
			foreach ($GO_USERS->get_search_fields() as $fields) {
				$select->add_value($fields[0], $fields[1]);
			}
			
			$table = new table();
			
			$row = new table_row();
			$row->add_cell(new table_cell($select->get_html()));
			
			$query = isset($_POST[$this->id]['query']) ? smart_stripslashes($_POST[$this->id]['query']) : '';
			$input = new input('text',$this->id.'[query]', $query);
			$input->set_attribute('size','30');
			$input->set_attribute('maxlength','255');
			
			$row->add_cell(new table_cell($input->get_html()));
			
			$table->add_row($row);
			
			$this->add_outerhtml_element($table);
			
			$this->add_outerhtml_element(new button($GLOBALS['cmdSearch'], "javascript:search_users('".$this->form_name."', '".$this->id."');"));
			$this->add_outerhtml_element(new button($GLOBALS['cmdShowAll'], 
				"javascript:document.forms['".$this->form_name."'].elements['".$this->id."[query]'].value='';search_users('".$this->form_name."', '".$this->id."');"));
			$this->add_outerhtml_element(new button($GLOBALS['cmdCancel'], "javascript:return_to('".$this->form_name."', '".$this->id."');"));

			if (isset($_POST[$this->id]['query'])) {
							
				if ($_POST[$this->id]['query'] != '') {
					$GO_USERS->search('%'.smart_addslashes($query).'%', smart_addslashes($search_field), $GO_SECURITY->user_id);
				} else {
					$GO_USERS->get_authorized_users($GO_SECURITY->user_id);
				}
				
				$select = new select($this->id.'[selected][]','',true);
				$select->set_attribute('style', 'width: 250px;height: 200px;display:block;');
				
				while ($GO_USERS->next_record()) {
					$middle_name = $GO_USERS->f('middle_name') == '' ? '' : $GO_USERS->f('middle_name').' ';
					$name = $GO_USERS->f('first_name').' '.$middle_name.$GO_USERS->f('last_name');
					
					$select->add_value($GO_USERS->f('id'),$name);
				}
				
				$this->add_outerhtml_element($select);
				
				$this->add_outerhtml_element(new button($GLOBALS['cmdAdd'], "javascript:add_users('".$this->form_name."', '".$this->id."');"));
			}
			return $this->outerHTML;
		}else
		{
			$this->select($this->id.'[selected][]', '', true);
			$this->set_attribute('style', 'width: 250px;height: 200px;display:block;');
			
			foreach($this->users as $user_id)
			{
				$user = $GO_USERS->get_user($user_id);
				$this->add_value($user_id, format_name($user['last_name'], $user['first_name'], $user['middle_name']));
			}
			
					
			$addButton = new button($GLOBALS['cmdAdd'], "javascript:search_users('".$this->form_name."', '".$this->id."');");
			$html = $addButton->get_html();
			
			$deleteButton = new button($GLOBALS['cmdDelete'], "javascript:delete_users('".$this->form_name."', '".$this->id."');");
			$html .= $deleteButton->get_html();
			return parent::get_html().$html;
		}		
	}
	

}
