<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.4 $ $Date: 2006/11/21 16:25:36 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");

load_basic_controls();
load_control('color_selector');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();


$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$background_id = isset($_REQUEST['background_id']) ? $_REQUEST['background_id'] : 0;




if($task == 'save')
{	
	$background['name'] = smart_addslashes($_POST['name']);
	$background['color'] = smart_addslashes($_POST['color']);
	
	if(empty($background['name']))
	{
		$feedback = $error_missing_field;	
	}else
	{	
		if($background_id>0)
		{
			$background['id']=$background_id;
			$cal->update_background($background);
		}else
		{
			$background_id = $cal->add_background($background);
		}
		
		if($_POST['close']=='true')
		{
			header('Location: '.$return_to);
			exit();
		}
	}
}



if ($background_id > 0 && $task != 'save')
{
	$background = $cal->get_background($background_id);

}else
{
	$background['name'] = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$background['color'] = isset($_POST['color']) ? smart_stripslashes($_POST['color']) : 'ccffff';
}

$tabstrip = new tabstrip('background_strip', $cal_background_color);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));

$GO_HEADER['body_arguments'] = 'onload="javascript:document.forms[0].name.focus();"';
$GO_HEADER['head'] = color_selector::get_header();
require_once($GO_THEME->theme_path.'header.inc');

$form = new form('background_form');

$form->add_html_element(new input('hidden', 'background_id',$background_id,false));
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));
$form->add_html_element(new input('hidden', 'return_to',$return_to,false));



if(isset($feedback))
{
	$p = new html_element('p',$feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$table = new table();
		
$row = new table_row();
$row->add_cell(new table_cell($strName.':'));

$input = new input('text','name', $background['name']);

$row->add_cell(new table_cell($input->get_html()));		
$table->add_row($row);			

$row = new table_row();
$row->add_cell(new table_cell($cal_background_color.': '));

$color_selector = new color_selector('color','color', $background['color']);
$row->add_cell(new table_cell($color_selector->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save';document.forms[0].submit()"));
$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save';document.forms[0].submit()"));
$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));	

$form->add_html_element($tabstrip);

echo $form->get_html();

require_once($GO_THEME->theme_path.'footer.inc');
?>
