<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/12/05 11:37:39 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('translate');
//require_once($GO_LANGUAGE->get_language_file('translate'));

load_basic_controls();

require($GO_MODULES->modules['translate']['class_path'].'translate.class.inc');
$tl = new translate();

$source_lang=isset($_POST['source_lang']) ? smart_addslashes($_POST['source_lang']) : 'en';
$dest_lang=isset($_POST['dest_lang']) ? smart_addslashes($_POST['dest_lang']) : '';
$src_file=isset($_POST['src_file']) ? smart_addslashes($_POST['src_file']) : '';
$task=isset($_POST['task']) ? smart_addslashes($_POST['task']) : '';


if($task=='download')
{
	
	$file = $GO_CONFIG->local_path.'translate/translations-'.$dest_lang.'-'.$GO_CONFIG->version.'.tar.gz';
	
	mkdir_recursive(dirname($file));
	chdir($GO_CONFIG->file_storage_path.'translate/');

	exec($GO_CONFIG->cmd_tar.' -czf "'.$file.'" "'.$dest_lang.'"');
	header('Location: '.$GO_CONFIG->local_url.'translate/translations-'.$dest_lang.'-'.$GO_CONFIG->version.'.tar.gz');
	exit();
}elseif($task=='email')
{
	$_SESSION['attach_array'] = array ();
	require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc");
	$email = new email();


	$file = $GO_CONFIG->local_path.'translate/translations-'.$dest_lang.'-'.$GO_CONFIG->version.'.tar.gz';
	
	mkdir_recursive(dirname($file));
	chdir($GO_CONFIG->file_storage_path.'translate/');

	exec($GO_CONFIG->cmd_tar.' -czf "'.$file.'" "'.$dest_lang.'"');
	
	$email->register_attachment($file, basename($file), filesize($file), mime_content_type($file));


	//$order = $billing->get_order($order_id);
	$email ='"Intermesh translation office" <translations@intermesh.nl>';
	$subject = 'Group-Office translation';

	$GO_HEADER['body_arguments'] = 'onload="'.
	'popup(\''.$GO_MODULES->modules['email']['url'].'send.php?email_file=true&mail_subject='.
	urlencode($subject).'&mail_to='.urlencode($email).'\',\''.
	$GO_CONFIG->composer_width.'\',\''.$GO_CONFIG->composer_height.'\');"';
}



if(isset($_POST['names']) && !empty($_POST['save_dest_file']))
{
	for($i=0;$i<count($_POST['names']);$i++)
	{
		$vars[smart_stripslashes($_POST['names'][$i])]=str_replace('\'', '\\\'', smart_stripslashes($_POST['values'][$i]));
	}
	$tl->save_file(smart_stripslashes($_POST['save_dest_file']),$vars);
}

require_once($GO_THEME->theme_path."header.inc");
$form = new form('translate_form');
$form->add_html_element(new input('hidden','src_file',$src_file));
$form->add_html_element(new input('hidden','task','',false));



$select = new select('source_lang', $source_lang);
$select->set_attribute('onchange','document.translate_form.submit();');
$languages = $GO_LANGUAGE->get_languages();
foreach($languages as $language)
{
	$select->add_value($language['code'], $language['description']);
}



$select1 = new select('dest_lang', $dest_lang);
$select1->set_attribute('onchange','document.translate_form.submit();');
$select1->add_value('','Please select your language ISO code');
$tl->get_iso_codes();
while($tl->next_record())
{
	$select1->add_value(strtolower($tl->f('iso_code_2')), $tl->f('iso_code_2'));
}
$p = new html_element('h1','Translate Group-Office');
$form->add_html_element($p);
$p = new html_element('p','Contribute to the Group-Office project by translating it into your language!');
$form->add_html_element($p);
$p = new html_element('p', 'It\'s recommended that you select English as source language because it\'s always up to date.');
$form->add_html_element($p);

$p = new html_element('p', 'Translate: '.$select->get_html().' into: '.$select1->get_html());
$form->add_html_element($p);



if(!empty($dest_lang))
{
	
	if(is_executable($GO_CONFIG->cmd_tar))
	{
		$form->add_html_element(new button('Dowload language pack','javascript:dotask(\'download\');','300px'));
		$form->add_html_element(new button('Send translation to Intermesh','javascript:dotask(\'email\');','300px'));
	}else {
		$p = new html_element('p','Can\'t create tar archives because '.$GO_CONFIG->cmd_tar.' is not executable. Install it or put the right command into config.php');
		$p->set_attribute('class','Error');
		$form->add_html_element($p);
	}

	$form->innerHTML.='<br /><br />';
	$table = new table();
	$table->set_attribute('style','width:100%');
	$row = new table_row();
	$cell = new table_cell();
	$cell->set_attribute('style','vertical-align:top');
	$cell->add_html_element(new html_element('h3','Source file'));
	$files = $tl->get_language_files('en');

	$next_path='';
	foreach($files as $index=>$path)
	{
		if(empty($src_file))
		{
			$src_file=$path;
		}
		if(isset($next_index))
		{
			$next_path=$path;
			unset($next_index);
		}
		$link = new hyperlink("javascript:set_file('".$path."');", $index);
		$link->set_attribute('class','selectableItem');
		if($path == $src_file)
		{
			$link->set_attribute('style','font-weight:bold;');
			$next_index=true;
		}
		
		
		$dest_dir = dirname($path);
		$dest_dir = str_replace($GO_CONFIG->root_path, $GO_CONFIG->file_storage_path.'translate/'.$dest_lang.'/', $dest_dir);		
		$dest_file = $dest_dir.'/'.$dest_lang.'.inc';
		mkdir_recursive($dest_dir);
		if(!file_exists($dest_file))
		{
			$file = dirname($path).'/'.$dest_lang.'.inc';
			if(file_exists($file))
			{
				copy($file, $dest_file);
			}else{
				copy($path, $dest_file);
			}
		}
		
		if($tl->files_are_different($path, $dest_file))
		{
			if($path == $src_file)
			{
				$link->set_attribute('style','font-weight:bold;color:red;');
			}else {
				$link->set_attribute('style','color:red;');		
			}			
		}		
		$cell->add_html_element($link);
	}
	$row->add_cell($cell);

	$cell = new table_cell();
	$cell->set_attribute('style','vertical-align:top');

	if(!empty($src_file))
	{
		
		$dest_dir = dirname($src_file);
		$dest_dir = str_replace($GO_CONFIG->root_path, $GO_CONFIG->file_storage_path.'translate/'.$dest_lang.'/', $dest_dir);		
		$dest_file = $dest_dir.'/'.$dest_lang.'.inc';
		
		$form->add_html_element(new input('hidden','save_dest_file',$dest_file, false));

		$cell->add_html_element(new html_element('h3','Translate the bold printed strings'));
		$src_vars = $tl->get_vars($src_file);
		$dest_vars = $tl->get_vars($dest_file);

		$tl_table = new table();
		$tl_table->set_attribute('style','width:100%');
		foreach($src_vars as $name=>$value)
		{
			$tl_row = new table_row();
			$tl_cell = new table_cell('<br /><b>'.htmlentities($src_vars[$name]).'</b> ['.$name.']');
			$tl_row->add_cell($tl_cell);
			$tl_table->add_row($tl_row);
			
			
			$tl_row = new table_row();

			$value = isset($dest_vars[$name]) ? $dest_vars[$name] : '';

			$form->add_html_element(new input('hidden', 'names[]', $name));

			$input = new input('text', 'values[]', $value);
			$input->set_attribute('style','width:100%');
			$input->set_attribute('onfocus','this.select();');
			$input->set_attribute('autocomplete','off');

			$tl_cell = new table_cell($input->get_html());
			$tl_cell->set_attribute('style','width:50%');
			$tl_row->add_cell($tl_cell);

			$tl_table->add_row($tl_row);
		}
		$cell->add_html_element($tl_table);
		$cell->add_html_element(new button($cmdSave, 'javascript:set_file(\''.$next_path.'\');'));
	}
	$row->add_cell($cell);
	$table->add_row($row);
	$form->add_html_element($table);
}






echo $form->get_html();
?>
<script type="text/javascript">
function set_file(file)
{
	document.translate_form.task.value='';
	document.translate_form.src_file.value=file;
	document.translate_form.submit();
}

function dotask(task)
{
	document.translate_form.task.value=task;
	document.translate_form.submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>