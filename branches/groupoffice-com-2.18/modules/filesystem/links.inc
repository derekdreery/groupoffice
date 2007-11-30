<?php
require($GO_LANGUAGE->get_language_file('filesystem'));
require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();
$fs2 = new filesystem();

global $GO_SECURITY;

$fs_settings = $fs->get_settings($GO_SECURITY->user_id);
$gota_supported = file_exists($GO_MODULES->modules['filesystem']['path'].'jnlp.php');

$datatable = new datatable('filesystem_links',false, $form_name);
$datatable->set_attribute('style','margin-bottom:20px;');

if($datatable->task == 'delete')
{
	foreach($datatable->selected as $delete_path)
	{
		if (!$fs->delete($delete_path)) {
			$feedback = $GLOBALS['strAccessDenied'];
			break;
		}
	}
}elseif($datatable->task=='unlink')
{
	global $GO_LINKS;

	foreach($datatable->selected as $delete_path)
	{
		if($remote_link_id = $fs->get_link_id_by_path($delete_path))
		{
			$GO_LINKS->delete_link($link_id, $remote_link_id);
		}
	}
}

$links = $GO_LINKS->get_links($link_id, 6);

if(count($links))
{
	$datatable->add_column(new table_heading($GLOBALS['strName']));
	$datatable->add_column(new table_heading($strType));
	$datatable->add_column(new table_heading($strSize));
	$datatable->add_column(new table_heading($GLOBALS['strDate']));
	$datatable->add_column(new table_heading('&nbsp;'));

	$fs->get_linked_items($links);
	while($fs->next_record())
	{
		$row = new table_row($fs->f('path'));

		$filename = basename($fs->f('path'));

		if(is_dir($fs->f('path')))
		{
			$row->set_attribute('ondblclick', "javascript:document.location='".$GO_MODULES->modules['filesystem']['url'].'index.php?path='.urlencode($fs->f('path'))."'");
				
			if ($fs2->get_share(addslashes($fs->f('path')))) {
				$image = new image('shared_folder');
			} else {
				$image = new image('folder');
			}
			$image->set_attribute('align','absmiddle');
			$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
				
			$row->add_cell(new table_cell($image->get_html().$filename));
			$row->add_cell(new table_cell($fbFolder));
			$row->add_cell(new table_cell('-'));
			$row->add_cell(new table_cell(get_timestamp(get_time(filemtime($fs->f('path'))))));
		}else
		{
			/*$row->set_attribute('ondblclick', "javascript:document.location='".
				$GO_MODULES->modules['filesystem']['url'].'index.php?task=properties&path='.
				urlencode($fs->f('path'))."&return_to=".urlencode($return_to)."';");
					
				$row->set_attribute('ondblclick', "javascript:document.location='".
				$GO_MODULES->modules['filesystem']['url'].'download.php?path='.
				urlencode($fs->f('path'))."';");*/
				
			$extension = get_extension($fs->f('path'));
				
			if($fs_settings['open_properties']=='1')// || eregi('htm', $extension) || eregi('eml', $extension))
			{
				$row->set_attribute('ondblclick', "javascript:window.location.href='".
				$GO_MODULES->modules['filesystem']['url'].'index.php?task=properties&path='.urlencode($fs->f('path'))."';");
			}else
			{
				if($fs_settings['use_gota']=='1' && $gota_supported && !in_array($extension, $nogota_extensions))
				{
					$row->set_attribute('ondblclick', "javascript:window.location.href='".
					$GO_MODULES->modules['filesystem']['url'].'jnlp.php?path='.urlencode($fs->f('path'))."';");
				}else
				{
					if($fs->is_viewable_in_browser($file['path']))
					{
						$row->set_attribute('ondblclick', "javascript:window.open('".
						$GO_MODULES->modules['filesystem']['url'].'download.php?mode=inline&path='.urlencode($fs->f('path'))."');");
					}else
					{
						$row->set_attribute('ondblclick', "javascript:window.location.href='".
						$GO_MODULES->modules['filesystem']['url'].'download.php?mode=download&path='.urlencode($fs->f('path'))."';");
					}
				}
					
					
			}

				
				
			$extension = get_extension($filename);
				
			$image = new image(false, get_filetype_image($extension));
			$image->set_attribute('align','absmiddle');
			$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
				
			$row->add_cell(new table_cell($image->get_html().$filename));
			$row->add_cell(new table_cell(get_filetype_description($extension)));
			$row->add_cell(new table_cell(format_size(filesize($fs->f('path')))));
			$row->add_cell(new table_cell(get_timestamp(get_time(filemtime($fs->f('path'))))));
				
				
			$img = new image('properties_small');
			$img->set_attribute('style','border:0px');
				
			$link = new hyperlink($GO_MODULES->modules['filesystem']['url'].'index.php?task=properties&path='.
			urlencode($fs->f('path'))."&return_to=".urlencode($return_to), $img->get_html(), $GLOBALS['strProperties']);
			$cell = new table_cell($link->get_html());
			$cell->set_attribute('style','text-align:right');
				
			$row->add_cell($cell);
				
		}
		$datatable->add_row($row);
	}

	$link = new hyperlink($GO_MODULES->modules['filesystem']['url'], $GLOBALS['lang_modules']['filesystem']);
	$this->add_html_element(new html_element('h2', $link->get_html()));
	$this->innerHTML .= $datatable->get_html();

	$this->delete_tasks .= $datatable->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $datatable->get_task_var().'.value="unlink";';
}
