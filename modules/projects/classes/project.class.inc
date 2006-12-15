<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2006/03/16 09:06:09 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
class project extends html_element
{
	var $project;
	var $link_back;
	var $maxlength;
	
	function project($project, $link_back='')
	{
		$this->project=$project;
		$this->link_back=$link_back;
	}
	
	function set_maxlength($maxlength)
	{
		$this->maxlength=$maxlength;
	}
	
	function get_html()
	{
		global $GO_MODULES, $GO_LANGUAGE;		
				
		$project_name = $this->project['description'] == '' ? $this->project['name'] : $this->project['name'].' ('.$this->project['description'].')';
		if(isset($this->maxlength))
		{
			 $project_name = cut_string($project_name , $this->maxlength, false);
		}
		
		$img = new image('projects');
		$img->set_attribute('align','middle');
		$img->set_attribute('style','border:0px;margin-right:5px;width:16px;height:16px;');
							
		$link = new hyperlink($GO_MODULES->modules['projects']['url'].'project.php?project_id='.$this->project['id'].'&return_to='.
			rawurlencode($this->link_back), $img->get_html().			
			htmlspecialchars($project_name));
		$link->set_attribute('style','display:block');
		$link->set_tooltip(new project_tooltip($this->project));					
		return $link->get_html();
	}
}

class project_tooltip extends tooltip
{
	function project_tooltip($project)
	{
		global $GO_MODULES, $GO_LANGUAGE;			

		$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];

		$title = $GLOBALS['sc_start_at'].': '.date($date_format, $project['start_time']).
		'<br />'.$GLOBALS['sc_end_at'].': '.date($date_format, $project['end_time']);

		$project_name = $project['description'] == '' ? $project['name'] : $project['name'].' ('.$project['description'].')';
		
		$this->tooltip($title, $project_name);
	}	
}
?>
