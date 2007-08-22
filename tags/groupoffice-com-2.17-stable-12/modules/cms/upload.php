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

load_basic_controls();
load_control('datatable');
load_control('dynamic_tabstrip');

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = 'browse.php';

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

$GO_THEME->load_module_theme('cms');

//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));
if(isset($_REQUEST['site_id']))
{
	$_SESSION['site_id'] =$_REQUEST['site_id'];
}

$tmp_dir = $GO_CONFIG->tmpdir.'/'.$GO_SECURITY->user_id.'/cms/batch_upload/';
/*
if(file_exists($tmp_dir))
{
	exec('rm -Rf '.$tmp_dir);
}*/
mkdir_recursive($tmp_dir);

$url = $GO_CONFIG->local_url.'cms/'.$_SESSION['site_id'].'batch_upload/';
$folder = $GO_CONFIG->local_path.'cms/'.$_SESSION['site_id'].'batch_upload/';
mkdir_recursive($folder);

require($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();




switch($task)
{
	case 'upload':
		for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i ++) {
			if (is_uploaded_file($_FILES['file']['tmp_name'][$i])) {
				$destination =$tmp_dir.$_FILES['file']['name'][$i];
				move_uploaded_file($_FILES['file']['tmp_name'][$i], $destination);					
			}
		}
		
		if(!count($fs->get_files($tmp_dir)))
		{
			$feedback = $fbNoFile;
		}
	break;
	
	case 'save_images':
		
		foreach($_POST['file_ids'] as $filename=>$file_id)
		{
			rename($tmp_dir.$filename, $folder.$filename);
			
			$str = str_replace('%url%', $url.$filename, smart_stripslashes($_POST['template']));
			$str = str_replace('%filename%', $filename, $str);
			
			$file = $cms->get_file($file_id);
			
			$update_file['id']=$file_id;
			$update_file['content']=addslashes($file['content'].$str);
			
			$cms->__update_file($update_file);			
		}
		break;
}

$form = new form('upload_form');

$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));




$files = $fs->get_files($tmp_dir);
if(count($files))
{
	
	
			
	
	function buildTree($folder_id, $select, $path='/')
	{
		$cms = new cms();
		$cms->get_files($folder_id);
		
		while($cms->next_record())
		{
			$select->add_value($cms->f('id'), $path.$cms->f('name'));
		}
	
		$cms->get_folders($folder_id);
		while($cms->next_record())
		{
			$select = buildTree($cms->f('id'), $select, $path.$cms->f('name').'/');
		}
		return $select;
	}
	
	$site = $cms->get_site($_SESSION['site_id']);
	
		
		
	load_control('datatable');
	
	$tabstrip = new tabstrip('batch_upload_strip', $fbUpload);
	$tabstrip->set_attribute('style','width:100%;height:100%;');
	$tabstrip->set_return_to($return_to);
	
	$input = new input('text', 'template', '<a href="%url%" target="_blank">%filename%</a><br />');
	$input->set_attribute('style', 'width:400px;');
	$tabstrip->innerHTML .= 'Template:&nbsp;'.$input->get_html();

	$table = new datatable('cms_files_list');
	$table->set_attribute('cellspacing', '3');
	
	
	//$table->add_column(new table_heading());
	$table->add_column(new table_heading($strName));
	$table->add_column(new table_heading($strName));
	
	$GO_HEADER['head'] = $table->get_header();

	$files = $fs->get_files_sorted($tmp_dir);
	$count=1;
	while($file = array_shift($files))
	{			
		$row = new table_row();
		
		//$form->add_html_element(new input('hidden','images['.$count.'][path]', $file['path']));
		
		$dimensions = getimagesize($file['path']);
		
		if($dimensions[0] > $dimensions[1])
		{
			$dimension = '&w=80';
		}else {
			$dimension = '&h=80';
		}
		
		/*$cell = new table_cell();
		$cell->set_attribute('style', 'width:80px;padding:2px;text-align:center');
		$thumb = new image('', $GO_CONFIG->control_url.'phpthumb/phpThumb.php?src='.$file['path'].$dimension);
		$cell->add_html_element($thumb);			
		$row->add_cell($cell);*/
		
		$row->add_cell(new table_cell($file['name']));
		

		$select = new select('file_ids['.$file['name'].']');	
		$select = buildTree($site['root_folder_id'], $select);
		
		$cell = new table_cell($select->get_html());
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
	
	$tabstrip->add_html_element(new html_element('p', $cms_batch_upload_text));
			
	$tabstrip->add_html_element(new html_element('p', $fbSelect.':'));
	
	$input = new input('file','file[]');
	$input->set_attribute('id', 'file_element');
	$input->set_attribute('size','50');
	
	$tabstrip->add_html_element($input);
	if($GO_CONFIG->use_jupload)
	{
		$tabstrip->add_html_element(new button($fbMultipleFiles, 'javascript:openPopup(\'upload\',\''.
			$GO_CONFIG->control_url.'JUpload/jupload.php?post_url='.
			urlencode($GO_MODULES->modules['cms']['full_url'].'jupload.php?sid='.session_id()).'&onunload=opener.upload()\',\'640\',\'400\');', '120'));
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