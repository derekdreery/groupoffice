<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

class go_opentts extends db
{
        var $user_id = 0;
        var $logged_in = false;
        var $group_everyone = "2";
        var $group_root = "1";
		var $ldap;
		var $imap;

        function go_opentts()
        {
			global $GO_CONFIG;
			$this->db();

			if (isset($_SESSION['GO_SESSION']['user_id']) && $_SESSION['GO_SESSION']['user_id'] > 0)
			{
				$this->logged_in=true;
				$this->user_id=$_SESSION['GO_SESSION']['user_id'];
			}			
        }
}
?>
