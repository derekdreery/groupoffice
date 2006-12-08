<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.4 $ $Date: 2006/11/21 16:25:34 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once($GO_CONFIG->class_path.'base/controls/datatable.class.inc');

class links_list extends html_element
{
	var $link_id =0;
	var $form_name;
	var $id;
	
	var $delete_tasks='';
	var $unlink_tasks='';
	
	function get_header()
	{
		global $GO_THEME;
		return datatable::get_header();
	}
	
	
	function links_list($link_id, $form_name='0', $return_to)
	{

		global $GO_MODULES,$GO_LINKS, $GO_LANGUAGE, $GO_CONFIG;
		
		$this->link_id=$link_id;
		
		if($link_id > 0)
		{
			$this->form_name = $form_name;
			
			foreach($GO_MODULES->modules as $module)
			{
				if($module['read_permission'] && file_exists($module['path'].'links.inc'))
				{
						require($module['path'].'links.inc');
				}
			}
		}
		
		if(empty($this->innerHTML))
		{
			$this->add_html_element(new html_element('p', $GLOBALS['strNoActivities']));
		}
		
		$this->innerHTML .= 
		'<script type="text/javascript">

			function confirm_delete_al()
			{
				var count = 0;

				for (var i=0;i<document.forms["'.$this->form_name.'"].elements.length;i++)
				{
					if((document.forms["'.$this->form_name.'"].elements[i].name.substr(document.forms["'.$this->form_name.'"].elements[i].name.length-12,12)== "[selected][]")
							&& document.forms["'.$this->form_name.'"].elements[i].checked == true)
					{
						count++;
					}
				}
				switch (count)
				{
					case 0:
						alert("'.$GLOBALS['strNoItemSelected'].'");
					break;

					case 1:
						if (confirm("'.$GLOBALS['strDeletePrefix'].' '.$GLOBALS['strSelectedItem'].' '.$GLOBALS['strDeleteSuffix'].'"))
						{
							'.$this->delete_tasks.'
							document.forms["'.$this->form_name.'"].submit();
						}
					break;

					default:
						if (confirm("'.$GLOBALS['strDeletePrefix'].$GLOBALS['strThis'].' "+count+" '.$GLOBALS['strItems'].$GLOBALS['strDeleteSuffix'].'"))
						{
							'.$this->delete_tasks.'
							document.forms["'.$this->form_name.'"].submit();
						}
					break;
				}
			}
			function confirm_unlink_al()
			{
				var count = 0;

				for (var i=0;i<document.forms["'.$this->form_name.'"].elements.length;i++)
				{
					if((document.forms["'.$this->form_name.'"].elements[i].name.substr(document.forms["'.$this->form_name.'"].elements[i].name.length-12,12)== "[selected][]")
							&& document.forms["'.$this->form_name.'"].elements[i].checked == true)
					{
						count++;
					}
				}
				switch (count)
				{
					case 0:
						alert("'.$GLOBALS['strNoItemSelected'].'");
					break;

					case 1:
						if (confirm("'.$GLOBALS['strUnlinkPrefix'].' '.$GLOBALS['strSelectedItem'].' '.$GLOBALS['strUnlinkSuffix'].'"))
						{
							'.$this->unlink_tasks.'
							document.forms["'.$this->form_name.'"].submit();
						}
					break;

					default:
						if (confirm("'.$GLOBALS['strUnlinkPrefix'].$GLOBALS['strThis'].' "+count+" '.$GLOBALS['strItems'].$GLOBALS['strUnlinkSuffix'].'"))
						{
							'.$this->unlink_tasks.'
							document.forms["'.$this->form_name.'"].submit();
						}
					break;
				}
			}
			</script>';
		
		
	}
	
	function get_delete_handler()
	{
		return 'javascript:confirm_delete_al();';
	}
	
	function get_unlink_handler()
	{
		return 'javascript:confirm_unlink_al();';
	}
	
	function get_html()
	{
		
		return $this->innerHTML;
	}
}
?>
