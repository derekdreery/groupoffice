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

$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['PHP_SELF']."?path=".$urlencoded_path;

$form->add_html_element(new input('hidden','return_to', $return_to));
$form->add_html_element(new input('hidden','MAX_FILE_SIZE', $GO_CONFIG->max_file_size));

$tabstrip = new tabstrip('upload_tab', $fbUpload);
$tabstrip->set_return_to(htmlspecialchars($return_to));
$tabstrip->set_attribute('style','width:100%');

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}
		
$tabstrip->add_html_element(new html_element('h3', $fbPath.': '.str_replace($GO_CONFIG->file_storage_path,'/', $fv->path)));
$tabstrip->add_html_element(new html_element('p', $fbSelect.':'));

$input = new input('file','file[]');
$input->set_attribute('id', 'file_element');
$input->set_attribute('size','50');

$tabstrip->add_html_element($input);
if($GO_CONFIG->use_jupload)
{
	$tabstrip->add_html_element(new button($fbMultipleFiles, 'javascript:document.location=\''.
		$GO_CONFIG->control_url.'JUpload/jupload.php?post_url='.
		urlencode($GO_MODULES->full_url.'jupload.php?sid='.session_id()).
		'&afterUploadURL='.urlencode($return_to).'\'', '120'));
}





$p = new html_element('p', 
	$fbMaxUploadSize.": ".format_size($GO_CONFIG->max_file_size)." (".
	number_format($GO_CONFIG->max_file_size, 0, 
	$_SESSION['GO_SESSION']['decimal_seperator'],
	$_SESSION['GO_SESSION']['thousands_seperator'])." bytes)");

if ( $GO_CONFIG->user_quota != 0 ) {
  
	$usedspace = $fs->get_usedspace($fv->path);
  $p->innerHTML .= "<br />$fbFreeQuotaSpace: ".format_size( ($GO_CONFIG->user_quota*1024 - $usedspace) );
}
$tabstrip->add_html_element($p);

$p = new html_element('p');
$p->set_attribute('id', 'status');
$p->set_attribute('class', 'Success');


$tabstrip->add_html_element($p);


$tabstrip->add_html_element(new button($cmdOk, "javascript:upload()"));
$tabstrip->add_html_element(new button($cmdCancel, "javascript:cancel();"));

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
?>
<script type="text/javascript">

function upload()
{
	var status = null;
	if (status = get_object("status"))
	{
		status.innerHTML = "<?php echo $fbPleaseWait; ?>";
	}
	document.forms[0].task.value='post_upload';
	document.forms[0].submit();
}

function cancel()
{
	document.location='<?php echo htmlspecialchars($return_to); ?>';
}

</script>
