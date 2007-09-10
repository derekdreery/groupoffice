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

load_basic_controls();

$handler=isset($_REQUEST['handler']) ? smart_stripslashes($_REQUEST['handler']) : '';
$multiselect=(isset($_REQUEST['multiselect']) && $_REQUEST['multiselect']=='true') ? 'true' : 'false';

require_once($GO_CONFIG->class_path.'/base/search.class.inc');
$search = new search();

$form = new form('search_form');
$form->add_html_element(new input('hidden','handler',$handler));
$form->add_html_element(new input('hidden','multiselect',$multiselect));



if(isset($_POST['query']))
{
	$_SESSION['global_search']['query']=smart_stripslashes($_POST['query']);
}else {
	$_SESSION['global_search']['query'] = isset($_SESSION['global_search']['query']) ? $_SESSION['global_search']['query'] : '';
}



$form->add_html_element(new html_element('h1', $search_global));
$form->add_html_element(new html_element('p',$search_text.':'));

$input = new input('text','query',$_SESSION['global_search']['query']);
$input->set_attribute('onfocus','this.select();');
$input->set_attribute('autocomplete','off');
$form->add_html_element($input);



$types = $search->get_search_types($GO_SECURITY->user_id);

if(count($types)>1)
{
	if(isset($_POST['query']))
	{		
		$selected_types=isset($_POST['selected_types']) ? $_POST['selected_types'] : array();
		$selected_types_string = implode(',', $selected_types);	
		$GO_CONFIG->save_setting('search_selected_types', $selected_types_string, $GO_SECURITY->user_id);
	}else
	{
		$selected_types_string = $GO_CONFIG->get_setting('search_selected_types',$GO_SECURITY->user_id);
		$selected_types = empty($selected_types_string) ? array() :explode(',', $selected_types_string);	
	}
	
	
	$table = new table();	
	
	
	$row = new table_row();
	
	foreach($types as $type)
	{			
	
		$type_check = in_array($type['link_type'], $selected_types);
		
		$checkbox = new checkbox(
		'type_'.$type['link_type'],
		'selected_types[]',
		$type['link_type'],
		$type['type'],
		$type_check);
	
		$row->add_cell(new table_cell($checkbox->get_html()));
	
	}
	$table->add_row($row);
	$form->add_html_element($table);
}








$button = new button($cmdSearch,'javascript:document.search_form.submit();');
$button->set_attribute('style','margin:0px;width:100px;');
$form->add_html_element($button);

$link_back = $_SERVER['PHP_SELF'];



if(!empty($_SESSION['global_search']['query']))
{
	
	
	
	
	
	
	load_control('datatable');
	$datatable = new datatable('global_select_table',false, 'search_form');
	$datatable->add_column(new table_heading($strName,'name'));
	$datatable->add_column(new table_heading($strType,'type'));
	$datatable->add_column(new table_heading($strModifiedAt,'mtime'));
	//$datatable->add_column(new table_heading($strDescription,'description'));

	$datatable->multiselect=($multiselect=='true');


	$GO_HEADER['head']=$datatable->get_header();

	$query=addslashes($_SESSION['global_search']['query']);


	//$search->reset();
	$count = $search->global_search($GO_SECURITY->user_id, $query,$datatable->start, $datatable->offset, $datatable->sort_index, $datatable->sql_sort_order,$selected_types);

	$datatable->set_pagination($count);

	if($count==1)
	{
		$form->add_html_element(new html_element('p', $count.' '.$search_result));
	}else {
		$form->add_html_element(new html_element('p', $count.' '.$search_results));
	}

	if($count>0)
	{

		while($search->next_record())
		{
			$row = new table_row($search->f('link_id'));
			$cell = new table_cell($search->f('name'));
			if($search->f('description')!='')
			{
				$cell->innerHTML .= '<p style="font-size:10px;margin-top:0px">'.$search->f('description').'</p>';
			}
			
			$row->add_cell($cell);
			$row->add_cell(new table_cell($search->f('type')));
			$row->add_cell(new table_cell(get_timestamp($search->f('mtime'))));


			if(empty($handler))
			{
				$row->set_attribute('ondblclick', "javascript:document.location='".add_params_to_url($search->f('url'),'return_to='.urlencode($link_back))."';");
			}else {
				$row->set_attribute('ondblclick', 'javascript:submit_to_handler();');
			}

			$datatable->add_row($row);
		}
	}else {
		$row = new table_row();
		$cell = new table_cell($strNoItems);
		$cell->set_attribute('colspan','99');
		$row->add_cell($cell);
		$datatable->add_row($row);
	}

	$form->add_html_element($datatable);


	if($multiselect=='true')
	{
		$button = new button($cmdOk, 'javascript:submit_to_handler();');
		$form->add_html_element($button);
	}

}


$GO_HEADER['body_arguments']='onload="document.search_form.query.select();"';
require($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
?>
<script type="text/javascript"">
function submit_to_handler()
{
	document.search_form.action='<?php echo base64_decode($handler); ?>';
	document.search_form.submit();
}

</script>
<?php
require($GO_THEME->theme_path.'footer.inc');