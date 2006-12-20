<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2005/07/28 07:54:34 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */
require('../Group-Office.php');

if(isset($_GET['expand_id']))
{
	$_SESSION[$_GET['treeview_id']]->setOpen(smart_stripslashes($_GET['expand_id']), true);
}elseif(isset($_GET['collapse_id']))
{
	$_SESSION[$_GET['treeview_id']]->setClosed(smart_stripslashes($_GET['collapse_id']));
}
