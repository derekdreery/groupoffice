<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.9 $ $Date: 2006/11/22 09:35:40 $
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

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('gallery');
require_once($GO_LANGUAGE->get_language_file('gallery'));

require_once($GO_MODULES->path.'classes/gallery.class.inc');
$ig = new gallery();

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$gallery_id = isset($_REQUEST['gallery_id']) ? $_REQUEST['gallery_id'] : 0;


require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();	

switch($task)
{
	case 'save_images':
		
		if(isset($_POST['images']))
		{
			foreach($_POST['images'] as $id=>$image)
			{				
				$image['id']=$id;
				$ig->update_image($image);
			}			
		}
		
		break;

	case 'save':					
		$user_id = (isset($_POST['user_id']['value']) && $_POST['user_id']['value'] > 0) ? $_POST['user_id']['value'] : $GO_SECURITY->user_id;
		
		$gallery['name'] = smart_addslashes(trim($_POST['name']));		
		$gallery['description'] = smart_addslashes(trim($_POST['description']));		
		$gallery['thumbwidth'] = smart_addslashes(trim($_POST['thumbwidth']));	
		$gallery['maxcolumns'] = smart_addslashes(trim($_POST['maxcolumns']));	
		$gallery['maxrows'] = smart_addslashes(trim($_POST['maxrows']));
		$gallery['resizeto'] = smart_addslashes(trim($_POST['resizeto']));	
		
		if ($gallery['name'] != "")
		{
			if ($gallery_id > 0)
			{
				$existing_gallery = $ig->get_gallery_by_name($gallery['name']);

				if ($existing_gallery && $existing_gallery['id'] != $gallery_id)
				{
					$feedback = $bs_gallery_exists;

				}else
				{
					$gallery['id']=$gallery_id;
					
					$ig->update_gallery($gallery);							
					if ($_POST['close'] == 'true')
					{
						header('Location: '.$return_to);
						exit();
					}
				}
			}else
			{
				$gallery['user_id'] = $GO_SECURITY->user_id;
				
				if ($ig->get_gallery_by_name($gallery['name']))
				{
					$feedback = $bs_gallery_exists;
				}else
				{
					$gallery['acl_read'] = $GO_SECURITY->get_new_acl('gallery read', $gallery['user_id']);
					$gallery['acl_write'] = $GO_SECURITY->get_new_acl('gallery write', $gallery['user_id']);
					
					$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $gallery['acl_write']);
		
					if ($gallery_id = $ig->add_gallery($gallery))
					{
						if ($_POST['close'] == 'true')
						{
							header('Location: '.$return_to);
							exit();
						}
					}else
					{
						$feedback = $strSaveError;
					}
				}
			}
		}else
		{
			$feedback = $error_missing_field;
		}
	break;
}

if ($gallery_id > 0)
{
	$gallery = $ig->get_gallery($gallery_id);
	$title = $gallery['name'];
	
	if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_read']) && 
	!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_write']))
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
}else
{

	$gallery['user_id'] = $GO_SECURITY->user_id;
	$gallery['name'] = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$gallery['description'] = isset($_POST['description']) ? smart_stripslashes($_POST['description']) : '';
	$gallery['thumbwidth'] = isset($_POST['thumbwidth']) ? smart_stripslashes($_POST['thumbwidth']) : '100';
	$gallery['maxcolumns'] = isset($_POST['maxcolumns']) ? smart_stripslashes($_POST['maxcolumns']) : '5';
	$gallery['maxrows'] = isset($_POST['maxrows']) ? smart_stripslashes($_POST['maxrows']) : '5';
	$gallery['resizeto'] = isset($_POST['resizeto']) ? smart_stripslashes($_POST['resizeto']) : '640';
	$title = $ig_new_gallery;
}

$tabstrip = new tabstrip('gallery_strip', $title);
$tabstrip->set_attribute('style','width:100%;height:100%;');
$tabstrip->set_return_to($return_to);


if ($gallery_id > 0)
{
	$tabstrip->add_tab('gallery', $strProperties);
	$tabstrip->add_tab('images', $ig_images);
	$tabstrip->add_tab('read_permissions', $strReadRights);
	$tabstrip->add_tab('write_permissions', $strWriteRights);
}


$form = new form('gallery_form');

$form->add_html_element(new input('hidden', 'gallery_id',$gallery_id,false));
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));
$form->add_html_element(new input('hidden', 'return_to',$return_to,false));
$form->add_html_element(new input('hidden', 'link_back',$link_back,false));

switch($tabstrip->get_active_tab_id())
{	
	case 'read_permissions':
		$tabstrip->innerHTML .= get_acl($gallery['acl_read']);
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".$return_to."'"));
	break;
	
	case 'write_permissions':
		$tabstrip->innerHTML .= get_acl($gallery['acl_write']);
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".$return_to."'"));
	break;
	case 'images':
		$image_file_path = $GO_CONFIG->local_path.'gallery/'.$gallery_id.'/';	
		
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();	
		if(!is_dir($image_file_path))
		{
			mkdir_recursive($image_file_path);	
			//$fs->add_share($gallery['user_id'], $image_file_path, 'gallery', $gallery['acl_read'], $gallery['acl_write']);
		}

		load_control('datatable');
		$table = new datatable('ig_files_list');
		$table->set_attribute('cellspacing', '3');
		
		if($table->task=='delete')
		{
			foreach($table->selected as $image_id)
			{
				$ig->delete_image($image_id);
			}		
		}
		
		
		$table->add_column(new table_heading());
		//$table->add_column(new table_heading($strName));
		$table->add_column(new table_heading($strDescription));
		
		$GO_HEADER['head'] = $table->get_header();
		
		
		$files = $fs->get_files_sorted($GO_CONFIG->local_path.'gallery/'.$gallery_id);
		while($file = array_shift($files))
		{			
			if(!$ig->image_exists($gallery_id, $file['name']))
			{
				if($gallery['resizeto']>0)
				{
					$ig->resize_image($file['path'], $gallery['resizeto']);	
				}
				
				$image['filename']=addslashes($file['name']);
				$image['gallery_id']=$gallery_id;
				
				$dimensions = getimagesize($file['path']); 
				$image['width']=$dimensions[0];
				$image['height']=$dimensions[1];
				$ig->add_image($image);
			}
		}

		//$link_back = $GO_MODULES->modules['gallery']['url'].'add_new.php?gallery_id='.$gallery_id.'&return_to='.urlencode($link_back);
		
		$menu = new button_menu();
		$menu->add_button(
				'upload', 
				$strUpload, 
				'add.php?gallery_id='.$gallery_id.'&return_to='.
						urlencode($link_back));
		$menu->add_button(
			'delete_big', 
			$cmdDelete, 
			$table->get_delete_handler());
			
		$form->add_html_element($menu);
		if($table->offset!=0)
		{
			$table->offset=5;
		}
		$count = $ig->get_images($gallery_id, $table->start, $table->offset);
		$table->set_pagination($count);
		
		while($ig->next_record())
		{
			$row = new table_row($ig->f('id'));
			
			$path = $GO_CONFIG->local_path.'gallery/'.$gallery_id.'/'.$ig->f('filename');
			$url = $GO_CONFIG->local_url.'gallery/'.$gallery_id.'/'.$ig->f('filename');
			
			if($ig->f('width') > $ig->f('height'))
			{
				$dimension = '&w=80';
			}else {
				$dimension = '&h=80';
			}
			
			$cell = new table_cell();
			$cell->set_attribute('style', 'width:80px;padding:2px;text-align:center');
			$thumb = new image('', $GO_CONFIG->control_url.'phpthumb/phpThumb.php?src='.$path.$dimension);
			//$link = new hyperlink("javascript:popup('".$url."','".($ig->f('width')+20)."','".($ig->f('height')+20)."');",$thumb->get_html());
			$cell->add_html_element($thumb);			
			$row->add_cell($cell);
			
			/*$cell = new table_cell();
			$cell->set_attribute('style', 'vertical-align:top;padding:2px;');
			$input = new input('text', 'images['.$ig->f('id').'][name]', $ig->f('name'));
			$input->set_attribute('style', 'width:100%;');			
			$cell->add_html_element($input);
			$row->add_cell($cell);*/
			
			$cell = new table_cell();
			$cell->set_attribute('style', 'vertical-align:top;padding:2px');
			$input = new textarea('images['.$ig->f('id').'][description]', $ig->f('description'));
			$input->set_attribute('style', 'width:100%;height:80px;');			
			$cell->add_html_element($input);
			$row->add_cell($cell);
			
			$table->add_row($row);			
		}
		
		
		
		$tabstrip->add_html_element($table);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		
		$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save_images';document.forms[0].submit()"));
		$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save_images';document.forms[0].submit()"));
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".$return_to."'"));	

	break;
	

	default:
	$GO_HEADER['body_arguments'] = 'onload="javascript:document.gallery_form.name.focus();"';
	
	if(isset($feedback))
	{
		$p = new html_element('p',$feedback);
		$p->set_attribute('class','Error');
		$tabstrip->add_html_element($p);
	}
	
	$table = new table();

	$row = new table_row();
	$row->add_cell(new table_cell($strName.': '));
	$input = new input('text', 'name',$gallery['name']);
	$input->set_attribute('maxlength','100');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($strDescription.': '));
	$input = new textarea('description',$gallery['description']);
	$input->set_attribute('style','width:400px;height:80px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	
	$row = new table_row();
	$row->add_cell(new table_cell($ig_thumbwidth.': '));	
	$input = new input('text', 'thumbwidth', $gallery['thumbwidth']);
	$input->set_attribute('style','width:70px;');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($ig_maxcolumns.': '));	
	$input = new input('text', 'maxcolumns', $gallery['maxcolumns']);
	$input->set_attribute('style','width:70px;');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($ig_maxrows.': '));	
	$input = new input('text', 'maxrows', $gallery['maxrows']);
	$input->set_attribute('style','width:70px;');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($ig_resizeto.': '));	
	$input = new input('text', 'resizeto', $gallery['resizeto']);
	$input->set_attribute('style','width:70px;');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$tabstrip->add_html_element($table);
	
	$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save';document.forms[0].submit()"));
	$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save';document.forms[0].submit()"));
	$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".$return_to."'"));	
	break;
}

$form->add_html_element($tabstrip);

require_once($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
require_once($GO_THEME->theme_path.'footer.inc');
