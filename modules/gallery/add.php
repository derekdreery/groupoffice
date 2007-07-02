<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.5 $ $Date: 2006/11/22 09:35:40 $
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
//$GO_MODULES->authenticate('gallery');
require_once($GO_LANGUAGE->get_language_file('gallery'));
require_once ($GO_LANGUAGE->get_language_file('filesystem'));

load_basic_controls();

require_once($GO_MODULES->path.'classes/gallery.class.inc');
$ig = new gallery();

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset ($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$gallery_id = isset($_REQUEST['gallery_id']) ? $_REQUEST['gallery_id'] : 0;


	

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();	

$image_file_path = $GO_CONFIG->local_path.'gallery/'.$gallery_id.'/';	
		

if(!is_dir($image_file_path))
{
	mkdir_recursive($image_file_path);			
}

$tmp_dir = $GO_CONFIG->tmpdir.'gallery/'.$GO_SECURITY->user_id.'/';
if(!is_dir($tmp_dir))
{
	mkdir_recursive($tmp_dir);
}

switch($task)
{
	case 'upload':
		for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i ++) {
			if (is_uploaded_file($_FILES['file']['tmp_name'][$i])) {
				$destination =$tmp_dir.$_FILES['file']['name'][$i];
				move_uploaded_file($_FILES['file']['tmp_name'][$i], $destination);					
			}
		}
		
		if(count($fs->get_files($tmp_dir)))
		{
			$task = 'process_images';
		}else {
			$feedback = $ig_no_file;
		}
		
	break;
		
	case 'save_images':
		
		if(isset($_POST['images']))
		{
			foreach($_POST['images'] as $image)
			{				
				
				$image_file_path = $GO_CONFIG->local_path.'gallery/'.$image['gallery_id'].'/';
				
				$image['filename']=basename($image['path']);
		
				//$image['gallery_id']=$gallery_id;
		
				$dimensions = getimagesize($image['path']); 
				$image['width']=$dimensions[0];
				$image['height']=$dimensions[1];
				$image['user_id']=$GO_SECURITY->user_id;
				
				$image = array_map('smart_addslashes',$image);
				
				$gallery = $ig->get_gallery($image['gallery_id']);
				if($gallery['resizeto']>0)
				{
					$ig->resize_image($image['path'], $gallery['resizeto']);	
				}
								
				rename($image['path'], $image_file_path.$image['filename']);
				
				unset($image['path']);			
				
				$ig->add_image($image);
			}			
		}
		
		header('Location: '.$return_to);
		exit();
		
		break;

	
}


//$gallery = $ig->get_gallery($gallery_id);

/*if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_read']) && 
!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $gallery['acl_write']))
{
	header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit();
}*/




$form = new form('gallery_form');

$form->add_html_element(new input('hidden', 'gallery_id',$gallery_id,false));
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));
$form->add_html_element(new input('hidden', 'return_to',$return_to,false));
$form->add_html_element(new input('hidden', 'link_back',$link_back,false));







if($task == 'process_images')
{
	load_control('datatable');
	
	$tabstrip = new tabstrip('gallery_strip', $ig_upload);
	$tabstrip->set_attribute('style','width:100%;height:100%;');
	$tabstrip->set_return_to($return_to);

	$table = new datatable('ig_files_list');
	$table->set_attribute('cellspacing', '3');
	
	$galleries=array();
	$ig->get_authorized_galleries($GO_SECURITY->user_id);
	while($ig->next_record())
	{
		$galleries[$ig->f('id')]=$ig->f('name');
	}
	
	
	$table->add_column(new table_heading());
	
	if(count($galleries)>1)
	{
		$table->add_column(new table_heading($ig_gallery));
	}
	$table->add_column(new table_heading($strDescription));
	
	$GO_HEADER['head'] = $table->get_header();
	
	

	$files = $fs->get_files_sorted($tmp_dir);
	$count=1;
	while($file = array_shift($files))
	{
		
			
		$row = new table_row();
		
		$form->add_html_element(new input('hidden','images['.$count.'][path]', $file['path']));
		
		$dimensions = getimagesize($file['path']);
		
		if($dimensions[0] > $dimensions[1])
		{
			$dimension = '&w=80';
		}else {
			$dimension = '&h=80';
		}
		
		$cell = new table_cell();
		$cell->set_attribute('style', 'width:80px;padding:2px;text-align:center');
		$thumb = new image('', $GO_CONFIG->control_url.'phpthumb/phpThumb.php?src='.$file['path'].$dimension);
		$cell->add_html_element($thumb);			
		$row->add_cell($cell);
		
		if(count($galleries)>1)
		{
			$cell = new table_cell();
			$cell->set_attribute('style', 'vertical-align:top;padding:2px');
			
			$select = new select('images['.$count.'][gallery_id]',$gallery_id);
			foreach($galleries as $_gallery_id=>$name)
			{
				$select->add_value($_gallery_id, $name);
			}			
			$cell->add_html_element($select);
			$row->add_cell($cell);
		}else 
		{
			$form->add_html_element(new input('hidden','images['.$count.'][gallery_id]', $gallery_id));
		}
		
		
		$description = isset($_POST['images'][$count]['description']) ? smart_stripslashes($_POST['images'][$count]['description']) : '';
	 	
		$cell = new table_cell();
		$cell->set_attribute('style', 'vertical-align:top;padding:2px');
		$input = new textarea('images['.$count.'][description]', $description);
		$input->set_attribute('style', 'width:100%;height:80px;');			
		$cell->add_html_element($input);
		$row->add_cell($cell);
		
		$table->add_row($row);			
		$count++;
	}
	
	
	
	$tabstrip->add_html_element($table);
	$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
	
	$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save_images';document.forms[0].submit()"));
	//$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save_images';document.forms[0].submit()"));
	//$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".$return_to."'"));	
	
	
	$form->add_html_element($tabstrip);
}else {
	
	$GO_HEADER['head'] = '<script type="text/javascript" language="javascript" src="'.$GO_CONFIG->host.'javascript/multifile.js"></script>';
	$GO_HEADER['head'] .= '<style>.deleteButton{background-image:url(\''.$GO_THEME->images['delete'].'\');width:16px;height:16px;cursor:default;display:inline;background-repeat:no-repeat;margin-left:5px;</style>';
		
	$form->add_html_element(new input('hidden','MAX_FILE_SIZE', $GO_CONFIG->max_file_size));
	$form->set_attribute('enctype', 'multipart/form-data');
	$tabstrip = new tabstrip('upload_tab', $fbUpload);
	$tabstrip->set_return_to(htmlspecialchars($return_to));
	$tabstrip->set_attribute('style','width:100%');
	
	if (isset($feedback))
	{
	  $p = new html_element('p', $feedback);
	  $p->set_attribute('class','Error');
	  $tabstrip->add_html_element($p);
	}
			
	$tabstrip->add_html_element(new html_element('p', $fbSelect.':'));
	
	$input = new input('file','file[]');
	$input->set_attribute('id', 'file_element');
	$input->set_attribute('size','50');
	
	$tabstrip->add_html_element($input);
	if($GO_CONFIG->use_jupload)
	{
		$tabstrip->add_html_element(new button($fbMultipleFiles, 'javascript:openPopup(\'upload\',\''.
			$GO_CONFIG->control_url.'JUpload/jupload.php?post_url='.
			urlencode($GO_MODULES->modules['gallery']['full_url'].'jupload.php?sid='.session_id()).'&onunload=opener.upload()\',\'640\',\'400\');', '120'));
	}	
	
	$p = new html_element('p', 
		$fbMaxUploadSize.": ".format_size($GO_CONFIG->max_file_size)." (".
		number_format($GO_CONFIG->max_file_size, 0, 
		$_SESSION['GO_SESSION']['decimal_seperator'],
		$_SESSION['GO_SESSION']['thousands_seperator'])." bytes)");
	
	
	$tabstrip->add_html_element($p);
	
	$p = new html_element('p');
	$p->set_attribute('id', 'status');
	$p->set_attribute('class', 'Success');
	
	
	$tabstrip->add_html_element($p);
	
	
	$tabstrip->add_html_element(new button($cmdOk, "javascript:upload()"));
	$tabstrip->add_html_element(new button($cmdCancel, "javascript:document.location='".htmlspecialchars($return_to)."';"));
	
	$div = new html_element('div', ' ');
	$div->set_attribute('id', 'files_list');
	$div->set_attribute('style', 'margin-top:10px;');
	$tabstrip->add_html_element($div);
	
	$form->add_html_element($tabstrip);
	
	$form->innerHTML .= "<script>
	<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
	var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 0 );
	<!-- Pass in the file element -->
	multi_selector.addElement( document.getElementById( 'file_element' ) );
	</script>";
	
}





require_once($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
?>
<script type="text/javascript">

function upload()
{
	var status = null;
	if (status = get_object("status"))
	{
		status.innerHTML = "<?php echo $fbPleaseWait; ?>";
	}
	document.forms[0].task.value='upload';
	document.forms[0].submit();
}

</script>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
