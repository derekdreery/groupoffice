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

//$file = '/home/mschering/Desktop/users.csv';
$delimiter=';';
$quote='"';

$enabled='0';


require_once("../../Group-Office.php");


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('tools');

load_basic_controls();

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$db = new db();
	$db->halt_on_error="no";
	if($db->query(($_POST['query'])))
	{
		$feedback = 'Query executed successfully. Affected rows: '.$db->affected_rows();		
	}else {
		$feedback = sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
		  	'Invalid SQL: '.($_POST['query']),
	  		$db->Errno,
	      	$db->Error);
	}
	
	
	
	
}


$form = new form();
$form->add_html_element(new html_element('h2','Execute SQL query'));
$form->add_html_element(new html_element('p','WARNING! This tool can totally destroy your Group-Office installation. Use only if you know EXACTLY what you are doing.'));


if(isset($feedback))
{
	$p = new html_element('p',$feedback);
	$p->set_attribute('class','Error');
	$form->add_html_element($p);
}

$query=isset($_POST['query']) ? ($_POST['query']) : '';
$textarea = new textarea('query', $query);
$textarea->set_attribute('style','width:100%;height:200px');

$form->innerHTML .= '<b>Query:</b><br />';
$form->add_html_element($textarea);
$form->add_html_element(new button($cmdOk, 'javascript:document.forms[0].submit();'));
$form->add_html_element(new button($cmdClose, 'javascript:document.location=\'index.php\';'));

require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
?>
<script type="text/javascript">
function save(task, close)
{
	document.users_settings_form.task.value=task;
	document.users_settings_form.close.value=close;
	document.users_settings_form.submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
