<?php
/************************************************************************/
/* TTS: Ticket tracking system                                          */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2002 by Meir Michanie                                  */
/* http://www.riunx.com                                                 */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/



//Classes
class Modules{
	function list_module($module_name=NULL){
		global $name;
		if (is_dir("modules/$name/modules/$module_name/"))
		echo "admin\n<br>";
$handle = opendir("modules/$name/modules/$module_name/");
while (false !== ($file = readdir($handle))) { 
        echo "$file\n";
    }
		return TRUE;
	}
}
