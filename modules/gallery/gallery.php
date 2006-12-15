<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.8 $ $Date: 2006/11/22 09:35:40 $
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


require_once($GO_MODULES->modules['gallery']['class_path'].'gallery.class.inc');
$ig = new gallery();

$gallery_id=$_REQUEST['gallery_id'];
$link_back = $_SERVER['PHP_SELF'].'?gallery_id='.$gallery_id;


$start = isset($_POST['start'][$gallery_id]) ? $_POST['start'][$gallery_id] : 0;
$gallery = $ig->get_gallery($gallery_id);

$offset = $gallery['maxcolumns']*$gallery['maxrows'];

//$GO_HEADER['head'] = datatable::get_header();

require_once($GO_THEME->theme_path."header.inc");

$form = new form('gallery_form');
$form->add_html_element(new input('hidden', 'start['.$gallery_id.']', $start, false));
$form->add_html_element(new input('hidden', 'gallery_id', $gallery_id, false));



$write_permission = $read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_write']);
if(!$write_permission)
{
	if(!$read_permission =$GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_read']))
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
}


$form->add_html_element(new html_element('h1', $gallery['name']));

$menu = new button_menu();

if($write_permission)
{
	$menu->add_button(
			'cms_settings', 
			$cmdSettings, 
			'edit_gallery.php?gallery_id='.$gallery_id.'&return_to='.
					urlencode($link_back));
	
	$menu->add_button(
			'upload', 
			$strUpload, 
			'add.php?gallery_id='.$gallery_id.'&return_to='.
					urlencode($link_back));
}
$menu->add_button(
	'close', 
	$cmdClose, 
	'index.php');
	
$form->add_html_element($menu);


$form->add_html_element(new html_element('p', $gallery['description']));



$table = new table();
$table->set_attribute('style','width:100%;');

$row = new table_row();

$colcount=0;



$count = $ig->get_images($gallery_id, $start, $offset);
		
while($ig->next_record())
{
	$colcount++;
	
	$path = $GO_CONFIG->local_path.'gallery/'.$gallery_id.'/'.$ig->f('filename');
	$url = $GO_CONFIG->local_url.'gallery/'.$gallery_id.'/'.$ig->f('filename');
	
	$cell = new table_cell();
	$cell->set_attribute('style','text-align:center;vertical-align:top');
	
	if($ig->f('width') > $ig->f('height'))
	{
		$dimension = '&w='.$gallery['thumbwidth'];
	}else {
		$dimension = '&h='.$gallery['thumbwidth'];
	}
	
	$thumb = new image('', $GO_CONFIG->control_url.'phpthumb/phpThumb.php?src='.$path.$dimension);
	$thumb->set_attribute('style', 'border:1px solid #aaaaaa;');
	
	$user = $GO_USERS->get_user($ig->f('user_id'));
			$name = format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'first_name');
			
	$link = new hyperlink("javascript:popup('".$url."','".($ig->f('width')+30)."','".($ig->f('height')+40)."');",$thumb->get_html(), $ig_uploaded_by.' '.$name);		
	$cell->add_html_element($link);

	if($ig->f('description')!='')
	{
		$span = new html_element('span', $ig->f('description'));
		$span->set_attribute('style','display:block;');
		$cell->add_html_element($span);
	}
	$row->add_cell($cell);
	if($colcount==$gallery['maxcolumns'])
	{		
		$table->add_row($row);
		$row = new table_row();	
		$colcount=0;
	}
}

if($colcount>0)
{	
	$colspan = $gallery['maxcolumns']-$colcount;
	$cell = new table_cell();
	$cell->set_attribute('colspan',$colspan);
	$row->add_cell($cell);
	
	$table->add_row($row);
}


$row = new table_row();

$cell = new table_cell();
$cell->set_attribute('colspan', '99');
$cell->set_attribute('style','text-align:center;padding-top:10px;');

$number_of_pages = ceil($count/$offset);
$page = $start/$offset;

if($number_of_pages>1)
{
	if($page>0)
	{
		$link = new hyperlink("javascript:document.gallery_form.elements['start[".$gallery_id."]'].value=".($start-$offset).";document.gallery_form.submit();", '&lt;&lt; '.$cmdPrevious);		
		$link->set_attribute('class', 'ig_pagination');
		$cell->add_html_element($link);
	}else {
		$span = new html_element('span', '&lt;&lt; '.$cmdPrevious);
		$span->set_attribute('class', 'ig_paginationDisabled');
		$cell->add_html_element($span);
	}
	
	for ($i=1;$i<=$number_of_pages;$i++)
	{
		$new_start = ($i-1)*$offset;
		$link = new hyperlink("javascript:document.gallery_form.elements['start[".$gallery_id."]'].value=".$new_start.";document.gallery_form.submit();", $i);
		if($new_start==$start)
		{
			$link->set_attribute('class', 'ig_paginationActive');
		}else {
			$link->set_attribute('class', 'ig_pagination');
		}
		$cell->add_html_element($link);
		
		
	}
	if($page<$number_of_pages-1)
	{
		$link = new hyperlink("javascript:document.gallery_form.elements['start[".$gallery_id."]'].value=".($start+$offset).";document.gallery_form.submit();", $cmdNext.' &gt;&gt;');		
		$link->set_attribute('class', 'ig_pagination');
		$cell->add_html_element($link);
	}else {
		$span = new html_element('span', $cmdNext.' &gt;&gt;');
		$span->set_attribute('class', 'ig_paginationDisabled');
		$cell->add_html_element($span);
	}
	$row->add_cell($cell);
	$table->add_row($row);
}
$form->add_html_element($table);

echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
?>