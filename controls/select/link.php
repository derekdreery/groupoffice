<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/05/31 09:32:49 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../../Group-Office.php");

unset($_SESSION['GO_HANDLER']);
session_unregister('GO_HANDLER');

$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);


$values = isset($_POST['select_table']['selected']) ? $_POST['select_table']['selected'] : array();



if($link = $GO_LINKS->get_active_link())
{
	foreach($values as $item_id)
	{	
		switch($_REQUEST['search_type'])
		{		
			case 'contact':
				require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
				$ab = new addressbook();
				
				$link_contact = $ab->get_contact($item_id);
				$link_id = $link_contact['link_id'];
				if(empty($link_contact['link_id']))
				{
					$update_contact['id'] = $item_id;
					$update_contact['link_id'] = $link_id = $GO_LINKS->get_link_id();
					$ab->update_contact($update_contact);
				}
				$link_type=2;
			break;
			
			case 'company':
				require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
				$ab = new addressbook();
				
				$link_company = $ab->get_company($item_id);
				$link_id = $link_company['link_id'];
				if(empty($link_company['link_id']))
				{
					$update_company['id'] = $item_id;
					$update_company['link_id'] = $link_id = $GO_LINKS->get_link_id();
					$ab->update_company($update_company);
				}
				$link_type=3;
			break;
			
			case 'project':
				require_once($GO_MODULES->modules['projects']['class_path'].'projects.class.inc');
				$projects = new projects();
				
				$link_project = $projects->get_project($item_id);
				$link_id = $link_project['link_id'];
				if(empty($link_project['link_id']))
				{
					$update_project['id'] = $item_id;
					$update_project['link_id'] = $link_id = $GO_LINKS->get_link_id();
					$projects->_update_project($update_project);
				}
				$link_type=5;
			break;
			
			case 'file':	
				require_once($GO_CONFIG->class_path.'filesystem.class.inc');
				$fs = new filesystem();
				
				$link_id = $fs->get_link_id($item_id);
				$link_type=6;
			break;
		}
		$GO_LINKS->add_link($link['id'], $link['type'], $link_id, $link_type);
	}
	$GO_LINKS->deactivate_linking();
	
}
?>
<html>
<body onload="javascript:opener.document.location='<?php echo $link['return_to']; ?>';window.close();">
</body>
</html>
