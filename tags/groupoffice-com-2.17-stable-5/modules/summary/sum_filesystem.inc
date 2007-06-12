<?php
/**
 * @copyright Intermesh 200
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.0 $ $Date: 2007/06/05 13:26:14 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

 
if (isset ($GO_MODULES->modules['filesystem']) && $GO_MODULES->modules['filesystem']['read_permission']) {
	require_once ($GO_LANGUAGE->get_language_file('filesystem'));	
	require_once ($GO_CONFIG->class_path.'filesystem.class.inc');

	$fs = new filesystem();
	
	
	$em_table = new table();
	$em_table->set_attribute('style', 'border:0px;margin-top:10px;');
	
	$img = new image('filesystem');
	$img->set_attribute('style', 'border:0px;margin-right:10px;');
	
	$em_cell = new table_cell();
	$em_cell->set_attribute('valign','top');
	$em_cell->add_html_element($img);
	
	$em_row = new table_row();
	$em_row->add_cell($em_cell);
	
	$link = new hyperlink($GO_MODULES->modules['filesystem']['url'], $lang_modules['filesystem']);
	
	$h2 = new html_element('h2',$link->get_html());
	$em_row->add_cell(new table_cell($h2->get_html()));
	$em_table->add_row($em_row);
	
	
	$em_row = new table_row();
	$em_row->add_cell(new table_cell('&nbsp;'));	
	$em_cell = new table_cell();
	$em_cell->add_html_element(new html_element('h3', $sum_latest_files));
	$em_row->add_cell($em_cell);
	$em_table->add_row($em_row);
	
	$fs2 = new filesystem();
	$fs->get_latest_files();
	$count=0;
	while($fs->next_record())
	{
		if(!is_dir($fs->f('path')))
		{
			if($fs2->has_read_permission($GO_SECURITY->user_id, addslashes($fs->f('path'))))
			{		
				$count++;	
				$extension=get_extension($fs->f('path'));
				$image = new image(false, get_filetype_image($extension));
				$image->set_attribute('align','absmiddle');
				$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
				
				$link = new hyperlink($GO_MODULES->modules['filesystem']['url'].'index.php?path='.urlencode(dirname($fs->f('path'))),
					$image->get_html().strip_extension(basename($fs->f('path'))).' ('.get_filetype_description($extension).')');
				
				$em_row = new table_row();
				$em_row->add_cell(new table_cell('&nbsp;'));	
				$em_cell = new table_cell();
				$em_cell->add_html_element($link);
				$em_row->add_cell($em_cell);
				$em_table->add_row($em_row);				
			}
		}
		if($count==10)
		{
			break;
		}
	}
	
	
	if($count)
	{
		$cell->add_html_element($em_table);
	}
	
}