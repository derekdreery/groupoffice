<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.10 $ $Date: 2006/03/06 08:48:30 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
class activities_list extends html_element
{
	var $project_id =0;
	var $contact_id=0;
	var $company_id=0;
	var $file_id = '';
	
	var $form_name;
	var $id;
	
	var $delete_tasks='';
	
	
	function activities_list($type, $id, $form_name='0', $return_to)
	{
	
		global $GO_MODULES,$GO_LINKS;
		
		
		$this->add_html_element(new input('hidden', 'link[column]', $type));
		$this->add_html_element(new input('hidden', 'link[id]', $id));
		$this->add_html_element(new input('hidden', 'link[task]'));
		
		$this->form_name = $form_name;

		if(isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
		{
			$links = $GO_LINKS->get_links($id, 1);
			var_dump($links);
			if(count($links))
			{
				require_once($GO_MODULES->modules['calendar']['class_path'].'events_list.class.inc');
				$tl = new events_list('todos_list', true, false,0,0,0, $links, false, $form_name, $return_to);
				if(count($tl->rows))
				{
					$link = new hyperlink($GO_MODULES->modules['calendar']['url'], $GLOBALS['lang_modules']['todos']);
					$this->add_html_element(new html_element('h2', $link->get_html()));			
					$this->add_html_element($tl);
					
					$this->delete_tasks .= $tl->get_task_var().'.value="delete";';
				}		
				require_once($GO_MODULES->modules['calendar']['class_path'].'events_list.class.inc');
				$el = new events_list('events_list',false,true,0, 0, 0,$links, false, $form_name, $return_to);
				if(count($el->rows))
				{
					$link = new hyperlink($GO_MODULES->modules['calendar']['url'], $GLOBALS['lang_modules']['calendar']);
					$this->add_html_element(new html_element('h2', $link->get_html()));			
					$this->add_html_element($el);
					
					$this->delete_tasks .= $el->get_task_var().'.value="delete";';
				}
			}			
		}
		/*
		if($notes && isset($GO_MODULES->modules['notes']) && $GO_MODULES->modules['notes']['read_permission'])
		{
			require_once($GO_MODULES->modules['notes']['class_path'].'notes_list.class.inc');
			$nl = new notes_list('notes_list', 0, $this->project_id,$this->contact_id,$this->company_id,$this->file_id, false, $form_name, $return_to);
			
			if(count($nl->rows))
			{
				$link = new hyperlink($GO_MODULES->modules['notes']['url'], $GLOBALS['lang_modules']['notes']);
				$this->add_html_element(new html_element('h2', $link->get_html()));			
				$this->add_html_element($nl);
				
				$this->delete_tasks .= $nl->get_task_var().'.value="delete";';
			}				
		}
		
		if($projects && isset($GO_MODULES->modules['projects']) && $GO_MODULES->modules['projects']['read_permission'])
		{
			require_once($GO_MODULES->modules['projects']['class_path'].'projects_list.class.inc');
			$pl = new projects_list('projects_list', $this->project_id,$this->contact_id,$this->company_id,false, $form_name, $return_to);
			
			if(count($pl->rows))
			{
				$link = new hyperlink($GO_MODULES->modules['projects']['url'], $GLOBALS['lang_modules']['projects']);
				$this->add_html_element(new html_element('h2', $link->get_html()));			
				$this->add_html_element($pl);
				
				$this->delete_tasks .= $pl->get_task_var().'.value="delete";';
			}				
		}
		
		if(!empty($file_path) && isset($GO_MODULES->modules['filesystem']) && $GO_MODULES->modules['filesystem']['read_permission'])
		{
			require_once($GO_MODULES->modules['filesystem']['class_path'].'filesystem_list.class.inc');
			$fsl = new filesystem_list('fs_list', $file_path);
			$fsl_html = $fsl->get_html();
			if($fsl_html != '')
			{
				$link = new hyperlink($GO_MODULES->modules['filesystem']['url'].'index.php?path='.urlencode($file_path), $GLOBALS['lang_modules']['filesystem']);
				$this->add_html_element(new html_element('h2', $link->get_html()));			
				$this->innerHTML .= $fsl_html;
				
				$this->delete_tasks .= $fsl->get_task_var().'.value="delete";';
			}	
		}*/
		
		if(empty($this->innerHTML))
		{
			$this->add_html_element(new html_element('p', $GLOBALS['strNoActivities']));
		}else
		{
			$this->innerHTML .= 
		'<script type="text/javascript">

			function confirm_delete_al()
			{
				var count = 0;

				for (var i=0;i<document.forms["'.$this->form_name.'"].elements.length;i++)
				{
					if((
								document.forms["'.$this->form_name.'"].elements[i].name == "todos_list[selected][]" ||
								document.forms["'.$this->form_name.'"].elements[i].name == "events_list[selected][]" ||
								document.forms["'.$this->form_name.'"].elements[i].name == "projects_list[selected][]" ||
								document.forms["'.$this->form_name.'"].elements[i].name == "notes_list[selected][]" ||
								document.forms["'.$this->form_name.'"].elements[i].name == "fs_list[selected][]"
							)
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
			}</script>';
		}	
	}
	
	function get_delete_handler()
	{
		return 'javascript:confirm_delete_al();';
	}
	
	function get_html()
	{
		
		return $this->innerHTML;
	}
}
