<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.2 $ $Date: 2006/11/23 11:34:44 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package Addressbook
 * @category Addressbook
 */
require_once("Group-Office.php");
$GO_SECURITY->authenticate();

load_basic_controls();
load_control('datatable');

$GO_HEADER['nomessages']=true;
$GO_HEADER['head']=datatable::get_header();
require_once($GO_THEME->theme_path."header.inc");


$h1 = new html_element('h1', $strCreateLink);
echo $h1->get_html();

$p = new html_element('p', sprintf($strLinkText, $_SESSION['GO_SESSION']['link_description']));
echo $p->get_html();

$table = new table();
$table->set_attribute('cellpadding','5');
$table->set_attribute('cellspacing','0');
$table->set_attribute('style','cursor:default;width:100%;');
$table->set_hover_effect();


$link =  "javascript:popup('".$GO_CONFIG->control_url."select/select.php?".
		"search_type=contact&multiselect=true&GO_HANDLER=".base64_encode($GO_CONFIG->control_url."select/link.php")."&pass_value=id&".
		"handler_base64_encoded=true&require_email_address=false".
		"&show_contacts=true&show_companies=true&show_projects=true".
		"&show_files=true','620','400');";

$row = new table_row();
$row->set_attribute('onclick', $link);
$img = new image('search');
$img->set_attribute('style', 'border:0px;width:32px;height:32px');
$row->add_cell(new table_cell($img->get_html()));
$row->add_cell(new table_cell($cmdSearch));

$table->add_row($row);


$row = new table_row();

$link = $GO_LINKS->get_active_link();

foreach($GO_MODULES->modules as $id=>$module)
{
	if($module['read_permission'] && $module_info = $GO_MODULES->get_module_info($id))
	{
		if(isset($module_info['linkable_items']))
		{
			require($GO_LANGUAGE->get_language_file($id));
			foreach($module_info['linkable_items'] as $linkable_item)
			{					
				$new_link = add_params_to_url($module['url'].$linkable_item['url'], 'return_to='.urlencode($link['return_to']));
				
				$row = new table_row();
				$row->set_attribute('onclick', "javascript:document.location='".$new_link."';");
				
				$img = new image($linkable_item['button']);
				$img->set_attribute('style', 'border:0px;width:32px;height:32px');
				$row->add_cell(new table_cell($img->get_html()));
				
				$cell = new table_cell($$linkable_item['language']);
				$cell->set_attribute('style','width:100%;');
				$row->add_cell($cell);
				$table->add_row($row);
			}
		}
	}
}


$link =  'javascript:parent.checker.location=\''.$GO_CONFIG->host.'checker.php?task=deactivate_linking\';document.location=\''.$link['return_to'].'\';';

$row = new table_row();
$row->set_attribute('onclick', $link);
$img = new image('cancel');
$img->set_attribute('style', 'border:0px;width:32px;height:32px');
$row->add_cell(new table_cell($img->get_html()));
$row->add_cell(new table_cell($cmdCancel));

$table->add_row($row);


echo $table->get_html();

require_once($GO_THEME->theme_path."footer.inc"); 
?>
