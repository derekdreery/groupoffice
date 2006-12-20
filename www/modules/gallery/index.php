<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/11/22 09:35:40 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This program is protected by copyright law and the Group-Office Professional license.

   You should have received a copy of the Group-Office Proffessional license
   along with Group-Office; if not, write to:
   
   Intermesh
   Reitscheweg 37
   5232BX Den Bosch
   The Netherlands   
   
   info@intermesh.nl
   
 * @package gallery
 * @category gallery
 */
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('gallery');
require_once($GO_LANGUAGE->get_language_file('gallery'));

load_basic_controls();
load_control('datatable');

require_once($GO_MODULES->modules['gallery']['class_path'].'gallery.class.inc');
$gallery = new gallery();

$link_back = $_SERVER['PHP_SELF'];

$GO_HEADER['head'] = datatable::get_header();

require_once($GO_THEME->theme_path."header.inc");

$form = new form('gallery_form');
if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$datatable = new datatable('gallery_table');
$datatable->set_attribute('style','width:100%');

if($datatable->task == 'delete')
{
	foreach($datatable->selected as $gallery_id)
	{
    $gallery->delete_gallery($gallery_id);
	}
}

$menu = new button_menu();
$menu->add_button('add',$cmdAdd,'edit_gallery.php?return_to='.urlencode($link_back));
$menu->add_button('delete_big',$cmdDelete, $datatable->get_delete_handler());

$form->add_html_element($menu);


$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($strOwner));

$ig = new gallery();

if($gallery->get_authorized_galleries($GO_SECURITY->user_id))
{
	while ($gallery->next_record())
	{
		$row = new table_row($gallery->f('id'));
		$row->set_attribute('ondblclick', "document.location='gallery.php?gallery_id=".$gallery->f("id")."&return_to=".urlencode($link_back)."';");
		
		$ig->get_images($gallery->f('id'),0,1);
		if($ig->next_record())
		{
			if($ig->f('width') > $ig->f('height'))
			{
				$dimension = '&w=80';
			}else {
				$dimension = '&h=80';
			}
			
			$path = $GO_CONFIG->local_path.'gallery/'.$gallery->f('id').'/'.$ig->f('filename');
			
			$thumb = new image('', $GO_CONFIG->control_url.'phpthumb/phpThumb.php?src='.$path.$dimension);
			$thumb->set_attribute('style', 'border:1px solid #aaaaaa;');
			$thumb->set_attribute('align', 'left');
			
			$subtable = new table();
			$subtable->set_attribute('cellpadding', '0');
			$subrow = new table_row();
			$subcell = new table_cell('<b>'.$gallery->f("name").'</b>');
			$subcell->set_attribute('colspan','2');
			$subrow->add_cell($subcell);
			$subtable->add_row($subrow);
			
			$subrow = new table_row();
			$subcell = new table_cell($thumb->get_html());
			$subcell->set_attribute('style','width:80px;vertical-align:top;');
			$subrow->add_cell($subcell);
			
			$subcell = new table_cell($gallery->f('description'));
			$subcell->set_attribute('style','width:80px;vertical-align:top;');
			$subrow->add_cell($subcell);
			
			$subtable->add_row($subrow);
					
			//$row->add_cell(new table_cell($subtable->get_html()));
			
			$row->add_cell(new table_cell('<b>'.$gallery->f("name").'</b><br />'.$thumb->get_html().$gallery->f('description')));
		}else {	
			$row->add_cell(new table_cell('<b>'.$gallery->f("name").'</b><br />'.$gallery->f('description')));
		}
		$row->add_cell(new table_cell(show_profile($gallery->f("user_id"))));
			     
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

echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
