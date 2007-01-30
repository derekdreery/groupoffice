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
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('search');
require_once($GO_LANGUAGE->get_language_file('search'));
load_basic_controls();

$form = new form('search_form');
//$form->set_attribute('style','text-align:center');
//load_control('global_autocomplete');

//$ac = new global_autocomplete('object','search_form');

//$form->add_html_element($ac);


if(isset($_POST['query']))
{
	$_SESSION['search']['query']=smart_stripslashes($_POST['query']);
}else {
	$_SESSION['search']['query'] = isset($_SESSION['search']['query']) ? $_SESSION['search']['query'] : '';
}
$task=isset($_POST['task']) ? smart_stripslashes($_POST['task']) : '';

$input = new input('text','query',$_SESSION['search']['query']);
$form->add_html_element($input);

$button = new button($cmdSearch,'javascript:document.search_form.submit();');
$button->set_attribute('style','margin:0px;width:100px;');
$form->add_html_element($button);

$link_back = $GO_MODULES->modules['search']['url'];

if(!empty($_SESSION['search']['query']))
{
	$query='%'.addslashes($_SESSION['search']['query']).'%';

	require_once($GO_CONFIG->class_path.'/base/search.class.inc');
	$search = new search();
	$search->global_search($GO_SECURITY->user_id, $query,0,0);

	while($search->next_record())
	{
		$div = new html_element('div');
		$link = new hyperlink(add_params_to_url($search->f('url'),'return_to='.urlencode($link_back)), $search->f('name'));
		$link->set_attribute('class','normal');
		$div->add_html_element($link);		
		$div->innerHTML .= '<br />'.$strType.': '.$search->f('type');
		$div->set_attribute('style', 'margin-top:20px;');
		
		$form->add_html_element($div);
	}
}



require($GO_THEME->theme_path.'header.inc');
echo $form->get_html();

require($GO_THEME->theme_path.'footer.inc');