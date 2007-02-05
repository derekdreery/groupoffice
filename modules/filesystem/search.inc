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

$time = get_time();
$year = date("Y", $time);
$month = date("m", $time);
$day = date("j", $time);
$hour = date("H", $time);
$minute = date("i", $time);

$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';

$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
$mins = array("00","05","10","15","20","25","30","35","34","45","50","55");
for ($i=1;$i<32;$i++)
{
	$num_days[] = $i;
}

for ($i=1;$i<13;$i++)
{
	$num_months[] = $i;
}
$search_on_modified_earlier_check = isset($_REQUEST['search_on_modified_earlier']) ? 'checked' : '';
$search_on_modified_later_check = isset($_REQUEST['search_on_modified_later']) ? 'checked' : '';

$img = new image('fs_search');
$img->set_attribute('style','border:0px;height:32px;width:32px;margin-right:5px;');

$form->add_html_element(new html_element('h1',$img->get_html().$search_title));

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($fbPath.':'));
$row->add_cell(new table_cell(str_replace($GO_CONFIG->file_storage_path,'/', $fv->path)));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($fs_keyword.':*'));
$input = new input('text','keyword', $keyword);
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($fs_modified_after.':'));
$datepicker = new date_picker('modified_later', $_SESSION['GO_SESSION']['date_format']);
$row->add_cell(new table_cell($datepicker->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($fs_modified_before.':'));
$datepicker = new date_picker('modified_before', $_SESSION['GO_SESSION']['date_format']);
$row->add_cell(new table_cell($datepicker->get_html()));
$table->add_row($row);

$form->add_html_element($table);

$form->add_html_element(new button($fs_search,'javascript:search();'));
$form->add_html_element(new button($cmdClose,"javascript:document.location='".$_SERVER['PHP_SELF']."';"));


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($keyword != '')
	{
		if ($_POST['modified_before'] != '')
		{
			$modified_before = date_to_unixtime($_POST['modified_before']);
		}else
		{
			$modified_before = 0;
		}
		if (isset($_POST['modified_later']))
		{
			$modified_later = date_to_unixtime($_POST['modified_later']);
		}else
		{
			$modified_later = 0;
		}
		$results = $fs->search($fv->path, $keyword,$modified_later,$modified_before);
		$count = count($results);
		
		$datatable = new datatable('filesystem_search_results');
		$datatable->add_column(new table_heading($strName));
		$datatable->add_column(new table_heading($fbLocation));

		while($result = array_shift($results))
		{
			if (!is_dir($result['path']))
			{					
				$short_name = cut_string(strip_extension($result['name']), 30);
				$extension = get_extension($result['name']);	
				
				$image = new image(false, get_filetype_image($extension));
				$image->set_attribute('align','absmiddle');
				$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
				
				$sort_prefix = $datatable->sort_ascending ? '1' : '0';	

				$row = new table_row($result['path']);
				
				$row->set_attribute('ondblclick', "javascript:window.location.href='download.php?path=".urlencode($result['path'])."';");
				$cell = new table_cell($sort_prefix.$result['name'], $image->get_html().$result['name']);
				$row->add_cell($cell);
				$row->add_cell(new table_cell(dirname(str_replace($GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/','/',$result['path']))));
				$datatable->add_row($row);
	
			}else
			{
				if ($fs->get_share($result['path'])) {
					$image = new image('shared_folder');
				} else {
					$image = new image('folder');
				}
				$image->set_attribute('align','absmiddle');
				$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
				
				$sort_prefix = $datatable->sort_ascending ? '0' : '1';						
				
				$row = new table_row($result['path']);						
				$row->set_attribute('ondblclick',  "document.location: index.php?path=".urlencode($result['path'])."';");
				
				$cell = new table_cell($sort_prefix.$result['name'], $image->get_html().$result['name']);
		
				$row->add_cell($cell);
				$row->add_cell(new table_cell(dirname(str_replace($GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/','/',$result['path']))));
				$datatable->add_row($row);
			}
		}
		
		$row = new table_row();
		$cell = new table_cell($count.' '.$fbItems);
		$cell->set_attribute('colspan','99');
		$cell->set_attribute('class','small');
		$cell->set_attribute('height','18');
		$row->add_cell($cell);				
		if(count($datatable->rows))
		{
			$datatable->add_footer($row);
		}else
		{
			$datatable->add_row($row);
		}
		$form->add_html_element($datatable);
	}else
	{
		$p = new html_element('p', $fs_enter_keyword);
		$p->set_attribute('class','Error');
		
		$form->add_html_element($p);

	}
}
?>
<script type="text/javascript" language="javascript">
function search()
{
	document.forms[0].task.value='search';
	document.forms[0].submit();
}
update_form();
</script>
