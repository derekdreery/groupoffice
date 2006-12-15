<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.16 $ $Date: 2006/04/13 18:06:08 $

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
class acl extends html_element{
	/**
	* The name of the HTML form
	*
	* @var     String
	* @access  private
	*/	
	var $form_name;
	
	
	/**
	* The task the ACL class has to perform after form post
	*
	* @var     String
	* @access  private
	*/	
	
	var $task;
	
	/**
	* The acl ID as in acl_items.id
	*
	* @var     Int
	* @access  private
	*/	
	var $acl_id;
	
	/**
	* The the ID of the acl class.
	*
	* @var     Int
	* @access  private
	*/	
	
	var $id;
	
	/**
	* The ACL control is read-only
	*
	* @var     Bool
	* @access  private
	*/	
	var $read_only;
	
  /**
   * Constructor: Initialise control and perform actions
   * 
   * @access public
   * @param Int $acl_id The acl ID as in acl_items.id 
   * @return void
   */
	function acl($acl_id, $form_name='forms[0]') {
		global $GO_SECURITY;

		if ($acl_id > 0) {
			$this->acl_id = $acl_id;
			$this->id = 'acl_'.$acl_id;
			$this->read_only = 
				!($GO_SECURITY->user_owns_acl($GO_SECURITY->user_id, $acl_id) || 
				$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id));
				
			$this->form_name = $form_name;
			
			$this->task = isset($_POST[$this->id]['task']) ? $_POST[$this->id]['task'] : '';

			switch ($this->task) {
				case 'delete_users' :
					$this->delete_users();
					break;

				case 'delete_groups' :
					$this->delete_groups();
					break;

				case 'save_add_groups' :
					$this->add_groups();
					break;

				case 'save_add_users' :
					$this->add_users();
					break;
			}
		} else {
			die('<b>Fatal error:</b> acl id not set');
		}
	}
	
	function get_html()
	{
		$this->add_html_element(new input('hidden', $this->id.'[task]'));
		$this->add_html_element(new input('hidden',$this->id.'[acl_id]', $this->acl_id));
		
		switch ($this->task) {
				case "add_groups" :
					return $this->get_user_groups();
					break;

				case "add_users" :
					return $this->get_users();
					break;

				default :
					return $this->get_acl();
					break;
			}
	}

  /**
   * Prints a select box with users returned from a search action
   * 
   * @access private
   * @param void
   * @return void
   */
	function get_users() {
		global $GO_SECURITY, $GO_CONFIG, $GO_USERS, $cmdAdd, $cmdCancel, $cmdShowAll, $cmdSearch;
		$table = new table();
		
		$search_field = isset ($_POST[$this->id]['search_field']) ? $_POST[$this->id]['search_field'] : '';

		$select = new select('search_field', $search_field);
		foreach ($GO_USERS->get_search_fields() as $fields) {
			$select->add_value($fields[0], $fields[1]);
		}
		
		$row = new table_row();
		$row->add_cell(new table_cell($select->get_html()));		
		$query = isset($_POST[$this->id]['query']) ? smart_stripslashes($_POST[$this->id]['query']) : '';
		$input = new input('text',$this->id.'[query]', $query);
		$input->set_attribute('size','30');
		$input->set_attribute('maxlength','255');	
		$row->add_cell(new table_cell($input->get_html()));
		
		$table->add_row($row);	
		
		$this->add_html_element($table);
		
		$this->add_html_element(new button($cmdSearch, 'javascript:search_'.$this->id.'()'));
		$this->add_html_element(new button($cmdShowAll, "javascript:document.".$this->form_name.".elements['".$this->id."[query]'].value='';search_".$this->id."()"));
		$this->add_html_element(new button($cmdCancel,  'javascript:return_to_acl_'.$this->id.'();'));
		

		if (isset ($_POST[$this->id]['query'])) {
			if ($_POST[$this->id]['query'] != '') {
				$GO_USERS->search('%'.smart_addslashes($_POST[$this->id]['query']).'%', smart_addslashes($search_field), $GO_SECURITY->user_id);
			} else {
				$GO_USERS->get_authorized_users($GO_SECURITY->user_id);
			}
			
			$select = new select($this->id.'[selected_users][]','',true);
			$select->set_attribute('style', 'width: 250px;height: 200px;display:block;');
			
			while ($GO_USERS->next_record()) {
				$middle_name = $GO_USERS->f('middle_name') == '' ? '' : $GO_USERS->f('middle_name').' ';
				$name = $GO_USERS->f('first_name').' '.$middle_name.$GO_USERS->f('last_name');
				
				$select->add_value($GO_USERS->f('id'),$name);
			}
			
			$this->add_html_element($select);
			
			$this->add_html_element(new button($cmdAdd, 'javascript:add_users_'.$this->id.'()'));		
		}

		$this->innerHTML .= '<script type="text/javascript">
		
	      function search_'.$this->id.'()
	      {
					document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="add_users";
					document.'.$this->form_name.'.submit();
	      }
		    function add_users_'.$this->id.'()
		    {
		      document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="save_add_users";
		      document.'.$this->form_name.'.submit();
		    }
		
		    function return_to_acl_'.$this->id.'()
		    {
		      document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="";
		      document.'.$this->form_name.'.submit();
		    }
		
		    var nav4 = window.Event ? true : false;
		    function processkeypress_'.$this->id.'(e)
		    {
		      if(nav4)
		      {
						var whichCode = e.which;
		      }else
		      {
						var whichCode = event.keyCode;
		      }
		
		      if (whichCode == 13)
		      {
						search_'.$this->id.'();
						return true;
		      }
		    }
		    if (window.Event) //if Navigator 4.X
		    {
		      document.captureEvents(Event.KEYPRESS)
		    }
		    document.onkeypress = processkeypress_'.$this->id.';
		    document.'.$this->form_name.'.elements["'.$this->id.'[query]"].focus();
		    </script>
		      ';
		    return $this->innerHTML;
	}

  /**
   * Prints a select box with all the user groups
   * 
   * @access private
   * @param void
   * @return void
   */
	function get_user_groups() {
		global $GO_SECURITY, $GO_CONFIG, $cmdAdd, $cmdCancel, $_SERVER, $query, $GO_GROUPS, $cmdShowAll, $cmdSearch;
		
		
		$select = new select($this->id.'[selected_groups][]','',true);
		$select->set_attribute('style', 'width: 250px;height: 200px;display:block;');
		
		if($this->read_only)
		{
			$select->set_attribute('disabled','disabled');
		}

		if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
		{
			$GO_GROUPS->get_groups();
		}else
		{
			$GO_GROUPS->get_groups($GO_SECURITY->user_id);
		}
		while ($GO_GROUPS->next_record()) {
			$select->add_value($GO_GROUPS->f('id'), $GO_GROUPS->f('name'));
		}
		
		$this->add_html_element($select);		

		if (!$this->read_only) {
			$this->add_html_element(new button($cmdAdd, 'javascript:add_groups_'.$this->id.'()'));
			$this->add_html_element(new button($cmdCancel, 'javascript:return_to_acl_'.$this->id.'()'));
		}


		$this->innerHTML .= '<script type="text/javascript">		
	      function add_groups_'.$this->id.'()
	      {
					document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="save_add_groups";
					document.'.$this->form_name.'.submit();
	      }
		
		    function return_to_acl_'.$this->id.'()
		    {
		      document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="";
		      document.'.$this->form_name.'.submit();
		    }
		    </script>
		      ';
		return $this->innerHTML;
	}

  /**
   * Prints a select box with all groups that are permissioned for this ACL
   * 
   * @access private
   * @param void
   * @return void
   */
	function get_groups_in_acl() {
		global $GO_SECURITY, $GO_GROUPS, $cmdAdd, $cmdDelete;
	
		$select = new select($this->id.'[selected_groups][]','',true);
		$select->set_attribute('style', 'width: 250px;height: 100px;display:block;');
		
		if($this->read_only)
		{
			$select->set_attribute('disabled','disabled');
		}
		
		$groups = $GO_SECURITY->get_group_ids_from_acl( $this->acl_id );

		for ( $i = 0; $i < count( $groups ); $i++ ) {
			$group = $GO_GROUPS->get_group( $groups[$i] );
			$select->add_value( $group['id'], $group['name'] );
		}		
		$this->add_html_element($select);		

		if (!$this->read_only) {
			$this->add_html_element(new button($cmdAdd, 'javascript:add_groups_'.$this->id.'()'));
			$this->add_html_element(new button($cmdDelete, 'javascript:delete_groups_'.$this->id.'(document.'.$this->form_name.'.elements[\''.$this->id.'[selected_GROUPS][]\'])'));
		}
	}

  /**
   * Prints a select box with all users that are permissioned for this ACL
   * 
   * @access private
   * @param void
   * @return void
   */
	function get_users_in_acl() {
		global $GO_SECURITY, $GO_USERS, $cmdAdd, $cmdDelete;

		
		$select = new select($this->id.'[selected_users][]','',true);
		$select->set_attribute('style', 'width: 250px;height: 100px;display:block;');
		if($this->read_only)
		{
			$select->set_attribute('disabled','disabled');
		}
		
		$GO_SECURITY->get_users_in_acl($this->acl_id);

		while ($GO_SECURITY->next_record()) {
			if ($profile = $GO_USERS->get_user($GO_SECURITY->f('user_id'))) {
				$middle_name = $profile["middle_name"] == '' ? '' : $profile["middle_name"].' ';
				$name = $profile["first_name"].' '.$middle_name.$profile["last_name"];
				
				$select->add_value($GO_SECURITY->f('user_id'),$name);
			}
		}
		
		$this->add_html_element($select);
		if (!$this->read_only) {
			$this->add_html_element(new button($cmdAdd, 'javascript:add_users_'.$this->id.'()'));
			$this->add_html_element(new button($cmdDelete, 'javascript:delete_users_'.$this->id.'(document.'.$this->form_name.'.elements[\''.$this->id.'[selected_users][]\'])'));
		}
	}

  /**
   * Prints the ACL control
   * 
   * @access private
   * @param void
   * @return void
   */
	function get_acl() {
		$this->innerHTML .= '<script type="text/javascript">
				function add_groups_'.$this->id.'()
				{
				document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="add_groups";
				document.'.$this->form_name.'.submit();
				}
		
				function add_users_'.$this->id.'()
				{
				document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="add_users";
				document.'.$this->form_name.'.submit();
				}
				function delete_groups_'.$this->id.'()
				{
				document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="delete_groups";
				document.'.$this->form_name.'.submit();
				}
		
				function delete_users_'.$this->id.'()
				{
				  document.'.$this->form_name.'.elements["'.$this->id.'[task]"].value="delete_users";
				  document.'.$this->form_name.'.submit();
				}
				</script>
				  ';
		global $acl_control_auth_groups, $acl_control_auth_users;
		
		$this->add_html_element(new html_element('h3', $acl_control_auth_groups));
		$this->get_groups_in_acl();
		$h3 = new html_element('h3', $acl_control_auth_users);
		$h3->set_attribute('style', 'margin-top: 10px');
		$this->add_html_element($h3);
		$this->get_users_in_acl();
		
		return $this->innerHTML;
	}

  /**
   * Removes the selected user groups from the ACL
   * 
   * @access private
   * @param void
   * @return void
   */
	function delete_groups() {
		global $GO_SECURITY, $GO_CONFIG;

		$selected_groups = isset ($_POST[$this->id]['selected_groups']) ? $_POST[$this->id]['selected_groups'] : array ();

		for ($i = 0; $i < count($selected_groups); $i ++) {
			$GO_SECURITY->delete_group_from_acl($selected_groups[$i], $this->acl_id);

			if ($GO_CONFIG->dav_switch) {
				global $GO_DAV;
				if ($GO_DAV->check_acl($this->acl_id))
					$GO_DAV->grouping($selected_groups[$i], $this->acl_id, "delete");
			}

		}
	}

  /**
   * Removes the selected users from the ACL
   * 
   * @access private
   * @param void
   * @return void
   */
	function delete_users() {
		global $GO_SECURITY, $GO_CONFIG;
		
		$selected_users = isset($_POST[$this->id]['selected_users']) ? $_POST[$this->id]['selected_users'] : array ();

		for ($i = 0; $i < count($selected_users); $i ++) {
			if ($selected_users[$i] != $GO_SECURITY->user_id) {
				$GO_SECURITY->delete_user_from_acl($selected_users[$i], $this->acl_id);

				if ($GO_CONFIG->dav_switch) {
					global $GO_DAV;
					if ($GO_DAV->check_acl($this->acl_id))
						$GO_DAV->delete_user($selected_users[$i], $this->acl_id);
				}

			}
		}
	}

  /**
   * Adds the selected user groups to the ACL
   * 
   * @access private
   * @param void
   * @return void
   */
	function add_groups() {
		global $GO_SECURITY, $GO_CONFIG;

		$selected_groups = isset($_POST[$this->id]['selected_groups']) ? $_POST[$this->id]['selected_groups'] : array ();

		for ($i = 0; $i < count($selected_groups); $i ++) {
			if (!$GO_SECURITY->group_in_acl($selected_groups[$i], $this->acl_id)) {
				$GO_SECURITY->add_group_to_acl($selected_groups[$i], $this->acl_id);

				if ($GO_CONFIG->dav_switch) {
					global $GO_DAV;
					if ($GO_DAV->check_acl($this->acl_id))
						$GO_DAV->grouping($selected_groups[$i], $this->acl_id, "add");
				}

			}
		}
	}
	
  /**
   * Adds the selected users to the ACL
   * 
   * @access private
   * @param void
   * @return void
   */
	function add_users() {
		global $GO_SECURITY, $GO_CONFIG;

		$selected_users = isset($_POST[$this->id]['selected_users']) ? $_POST[$this->id]['selected_users'] : array ();

		for ($i = 0; $i < count($selected_users); $i ++) {
			if (!$GO_SECURITY->user_in_acl($selected_users[$i], $this->acl_id)) {
				$GO_SECURITY->add_user_to_acl($selected_users[$i], $this->acl_id);

				if ($GO_CONFIG->dav_switch) {
					global $GO_DAV;
					if ($GO_DAV->check_acl($this->acl_id))
						$GO_DAV->add_user($selected_users[$i], $this->acl_id);
				}
			}
		}
	}
}
?>
