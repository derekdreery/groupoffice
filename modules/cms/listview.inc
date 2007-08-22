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

if(isset($_POST['move_folder_up']))
{
	$cms->move_folder_up(smart_addslashes($_POST['move_folder_up']));
}
if(isset($_POST['move_file_up']))
{
	$cms->move_file_up(smart_addslashes($_POST['move_file_up']));
}

if(isset($_POST['move_folder_top']))
{
	$cms->move_folder_top(smart_addslashes($_POST['move_folder_top']));
}
if(isset($_POST['move_file_top']))
{
	$cms->move_file_top(smart_addslashes($_POST['move_file_top']));
}


$datatable->set_attribute('style','margin-top:0px');

$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($strType));
$th = new table_heading($cms_priority);
$th->set_attribute('style','text-align:center');
$datatable->add_column($th);


//list the folders first
$total_size = 0;
$items = $cms->get_items($folder_id);
$count_items = count($items);

while ($item=array_shift($items))
{

	$type = isset($item['extension']) ? 'f' : 'd';

	$row = new table_row($type.$item['id']);

	if($type == 'd')
	{
		$short_name = cut_string($item['name'], 30);

		$row->set_attribute('ondblclick', "javascript:document.location='".$_SERVER['PHP_SELF'].'?folder_id='.$item['id']."';");
		if ($item['disabled'] == '1')
		{
			$img = new image('invisible_folder');
		}else
		{
			$img = new image('folder');
		}
		$img->set_attribute('style','width:16px;height:16px;border:0px;padding-right:5px');
		$img->set_attribute('align','absmiddle');

		$cell = new table_cell($img->get_html().htmlspecialchars($short_name));

		if (isset($_SESSION['cut_folders']) && in_array($item['id'], $_SESSION['cut_folders']))
		{
			$cell->set_attribute('style','color:#7d7d7d');
		}
		$row->add_cell($cell);
		$row->add_cell(new table_cell($fbFolder));
	}else
	{
		$short_name = cut_string(strip_extension($item['name']), 30);

		if(eregi('htm', $item['extension']))
		{
			$row->set_attribute('ondblclick', "javascript:document.location='edit.php?file_id=".$item['id']."&folder_id=".$folder_id."';");
		}else
		{
			$row->set_attribute('ondblclick', "javascript:popup('download.php?site_id=".$_SESSION['site_id']."&file_id=".$item['id']."');");
		}

		$img = new image('',get_filetype_image($item['extension']));
		$img->set_attribute('style','width:16px;height:16px;border:0px;padding-right:5px');
		$img->set_attribute('align','absmiddle');

		$cell = new table_cell($img->get_html().htmlspecialchars($short_name));
		if (isset($_SESSION['cut_files']) && in_array($item['id'], $_SESSION['cut_files']))
		{
			$cell->set_attribute('style','color:#7d7d7d');
		}
		$row->add_cell($cell);
		$row->add_cell(new table_cell(get_filetype_description($item['extension'])));
	}

	$img = new image('move_up');
	$img->set_attribute('style','width:16px;height:16px;border:0px');
	$img->set_attribute('align','absmiddle');

	$link = new hyperlink("javascript:move_".$type."_up(".$item['id'].");", $img->get_html());
	$cell = new table_cell($link->get_html().'&nbsp;');

	$img = new image('move_top');
	$img->set_attribute('style','width:16px;height:16px;border:0px;');
	$img->set_attribute('align','absmiddle');

	$link = new hyperlink("javascript:move_".$type."_top(".$item['id'].");", $img->get_html());
	$cell->add_html_element($link);

	$cell->set_attribute('style','text-align:center');
	$row->add_cell($cell);

	$datatable->add_row($row);
}

$row = new table_row();
$cell = new table_cell($count_items.' '.$strItems.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fbFolderSize.': '.format_size($total_size));
$cell->set_attribute('colspan','99');
$row->add_cell($cell);

$datatable->add_footer($row);

echo $datatable->get_html();
?>
<input type="hidden" name="move_folder_up" />
<input type="hidden" name="move_file_up" />
<input type="hidden" name="move_folder_top" />
<input type="hidden" name="move_file_top" />

<script type="text/javascript" language="javascript">
function move_d_up(folder_id)
{
	document.forms[0].move_folder_up.value=folder_id;
	document.forms[0].submit();
}
function move_f_up(file_id)
{
	document.forms[0].move_file_up.value=file_id;
	document.forms[0].submit();
}

function move_d_top(folder_id)
{
	document.forms[0].move_folder_top.value=folder_id;
	document.forms[0].submit();
}
function move_f_top(file_id)
{
	document.forms[0].move_file_top.value=file_id;
	document.forms[0].submit();
}





function cut_items()
{
	var count = 0;
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
			}
		}
	}

	if (count > 0)
	{
		document.forms[0].task.value = 'cut';
		document.forms[0].submit();
	}else
	{
		alert("<?php echo $fbNoSelect; ?>");
	}
}

function copy_items()
{
	var count = 0;
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
			}
		}
	}

	if (count > 0)
	{
		document.forms[0].task.value = 'copy';
		document.forms[0].submit();
	}else
	{
		alert("<?php echo $fbNoSelect; ?>");
	}
}

function paste_items()
{
	document.forms[0].task.value = 'paste';
	document.forms[0].submit();
}

function save_properties()
{
	document.forms[0].task.value = 'save';
	document.forms[0].submit();
}
</script>
