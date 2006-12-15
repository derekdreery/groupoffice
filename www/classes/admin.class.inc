<?php
/*
   Copyright Intermesh 2004
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 19 July 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

class admin extends db
{
  function get_settings($user_id)
  {
    $this->query("SELECT * FROM admin_settings WHERE user_id='$user_id'");
    if ($this->next_record())
    {
      return $this->Record;
    }else
    {
      $this->query("INSERT INTO admin_settings (user_id, sort_field, sort_order) 
	VALUES ('$user_id', 'name', 'ASC')");
      return $this->get_settings($user_id);
    }		
  }
  function set_sorting($user_id, $sort_field, $sort_order)
  {
    $sql = "UPDATE admin_settings SET sort_field='$sort_field' , 
      sort_order='$sort_order' WHERE user_id='$user_id'";
    return $this->query($sql);
  }
}
?>
