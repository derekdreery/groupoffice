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
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.basic
 */

/**
 * Functions to create links between items in Group-Office
 * 
 * This class provides functions to create links between items in Group-Office such as
 * tasks, projects, notes, appointments, files etc.
 *
 * Link types are static ints to improve perfomance. The table below is a type 
 * reference:
 *
 * 1=cal_events
 * 2=ab_contacts
 * 3=ab_companies
 * 4=no_notes
 * 5=pm_projects
 * 6=folders & files
 * 7=bs_orders
 * 8=users
 * 9=em_links
 * 10=timeregistration
 * 11=license
 * 12=tasks
 * 13=installation
 * 14=Project reports
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * 
 * @uses db
 */

class GO_LINKS extends db
{	
	function add_folder($folder)
	{
		$folder['id']=$this->nextid('go_link_folders');
		$this->insert_row('go_link_folders', $folder);
		return $folder['id'];
	}
	
	function update_folder($folder)
	{		
		$this->update_row('go_link_folders', 'id', $folder);		
	}
	
	function get_folder($folder_id)
	{
		$sql = "SELECT * FROM go_link_folders WHERE id=".$this->escape($folder_id);
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	
	function get_folders($link_id, $link_type, $parent_id=0)
	{
		$sql = "SELECT * FROM go_link_folders";
		if($parent_id)
		{
			$sql .= " WHERE parent_id=".intval($parent_id);
		}else
		{
			$sql .= " WHERE link_id=".$this->escape($link_id)." AND link_type=".$this->escape($link_type)." AND parent_id=0";
		}
		
		$this->query($sql);
		
		return $this->num_rows();
	}
	
	function delete_folder($folder_id)
	{		
		$folder = $this->get_folder($folder_id);
		
		$this->get_folders(0,0,$folder_id);
		while($this->next_record())
		{
			$links = new GO_LINKS();
			$links->delete_folder($this->f('id'));	
		}
		
		$sql = "DELETE FROM go_links_".intval($folder['link_type'])." WHERE folder_id=".$this->escape($folder_id);
		$this->query($sql);
		
		$sql = "DELETE FROM go_link_folders WHERE id=".$this->escape($folder_id);
		$this->query($sql);		
	}
	
	function get_link_types()
	{
		
	}
	
	function is_sub_folder($sub_id, $parent_id)
	{
		if($sub_id==0)
		{
			return false;
		}
		$folder = $this->get_folder($sub_id);
		if($folder['parent_id']==$parent_id)
		{
			return true;
		}else
		{
			return $this->is_sub_folder($folder['parent_id'], $parent_id);
		}
	}
	
	function update_link($type, $link)
	{
			$this->update_row('go_links_'.$type, array('id', 'link_id','link_type'), $link);
	}
	
	function add_link($id1, $type1, $id2, $type2, $folder_id1=0, $folder_id2=0, $description1='', $description2='')
	{
		if(!$this->link_exists($id1, $type1, $id2, $type2))
		{
			$link['id'] = $id1;
			$link['folder_id'] = $folder_id1;
			$link['link_type'] = $type2;
			$link['link_id'] = $id2;
			$link['description'] = $description1;
			$link['ctime']=time();
	
			$this->insert_row('go_links_'.$type1,$link);
		}
		
		if(!$this->link_exists($id2, $type2, $id1, $type1))
		{
			$link['id'] = $id2;
			$link['folder_id'] = $folder_id2;
			$link['link_type'] = $type1;
			$link['link_id'] = $id1;
			$link['description'] = $description2;
			$link['ctime']=time();
				
			$this->insert_row('go_links_'.$type2,$link);
		}
		
	}	
	
	function link_exists($link_id1, $type1, $link_id2, $type2)
	{
		$sql = "SELECT * FROM go_links_".intval($type1)." WHERE ".
			"`id`=".intval($link_id1)." AND link_type=".intval($type2)." AND `link_id`=".intval($link_id2);
		$this->query($sql);
		return $this->next_record();
	}
	
	function delete_link($link_id1, $type1, $link_id2=0, $type2=0)
	{		
		//if($link_id1>0)
		//{
			if($link_id2>0)
			{
				$sql = "DELETE FROM go_links_".intval($type1)." WHERE id=".$this->escape($link_id1)." AND link_type=".$this->escape($type2)." AND link_id=".$this->escape($link_id2);
				$this->query($sql);
				
				$sql = "DELETE FROM go_links_".intval($type2)." WHERE id=".$this->escape($link_id2)." AND link_type=".$this->escape($type1)." AND link_id=".$this->escape($link_id1);
				$this->query($sql);
			}else
			{
				$sql = "SELECT * FROM go_links_".intval($type1)." WHERE id=".$this->escape($link_id1);
				$this->query($sql);
				
				$db = new db();
				
				while($this->next_record())
				{
					$db->query("DELETE FROM go_links_".intval($this->f('link_type'))." WHERE link_id=".$this->escape($link_id1)." AND link_type=".$this->escape($type1));
				}
				$this->query("DELETE FROM go_links_".intval($type1)." WHERE id=".$this->escape($link_id1));
			}
		//}
		return true;
	}
	
	function has_links($link_id, $type)
	{
		if($link_id > 0)
		{
			$sql = "SELECT * FROM go_links_".intval($type)." WHERE id=".$this->escape($link_id);
			$this->query($sql);
			return $this->next_record();
		}
		return false;
	}
	
	/*
	 * 
	 * todo
	 
	function copy_links($src_link_id, $src_link_type, $dst_link_id, $dst_link_type)
	{
		$GO_LINKS2 = new GO_LINKS();
		
		$links = $this->get_links($src_link_id, $src_link_type);
		foreach($links as $link)
		{
			$GO_LINKS2->add_link($dst_link_id, $src_link_type, $dst_link_type, $link['link_id'], $link['type']);
		}
	}*/
	
	
	
	/**
	 * Add a LinkDescription
	 *
	 * @param Array $link_description Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_link_description($link_description)
	{
		$link_description['id']=$this->nextid('li_link_descriptions');
		if($this->insert_row('go_link_descriptions', $link_description))
		{
			return $link_description['id'];
		}
		return false;
	}
	/**
	 * Update a LinkDescription
	 *
	 * @param Array $link_description Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_link_description($link_description)
	{
		$r = $this->update_row('go_link_descriptions', 'id', $link_description);
		return $r;
	}
	/**
	 * Delete a LinkDescription
	 *
	 * @param Int $link_description_id ID of the link_description
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_link_description($link_description_id)
	{
		return $this->query("DELETE FROM go_link_descriptions WHERE id=?", 'i', $link_description_id);
	}
	/**
	 * Gets a LinkDescription record
	 *
	 * @param Int $link_description_id ID of the link_description
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_link_description($link_description_id)
	{
		$this->query("SELECT * FROM go_link_descriptions WHERE id=?", 'i', $link_description_id);
		return $this->next_record();		
	}

	/**
	 * Gets all LinkDescriptions
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_link_descriptions($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "* FROM go_link_descriptions ";
		$types='';
		$params=array();
		if(!empty($query))
 		{
 			$sql .= " WHERE description LIKE ?";
 			$types .= 's';
 			$params[]=$query;
 		} 		
		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);	
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		return $this->query($sql, $types, $params);
	}
}
