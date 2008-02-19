<?php

$GO_INCLUDES[]='../updateclient/classes/updateclient.class.inc';
require('../../Group-Office.php');

load_basic_controls();
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('updateclient');

require($GO_LANGUAGE->get_language_file('updateclient'));

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$GO_CONFIG->save_setting('updateclient_host', smart_addslashes($_POST['host']));
	$GO_CONFIG->save_setting('updateclient_username', smart_addslashes($_POST['username']));
	$GO_CONFIG->save_setting('updateclient_password', smart_addslashes($_POST['password']));
	
	header('Location: index.php');
	exit();
}




$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';



$form = new form('updateclient_form');
$form->add_html_element(new input('hidden','task','',false));


$form->add_html_element(new html_element('h1',$lang_modules['updateclient']));



$host = $GO_CONFIG->get_setting('updateclient_host');
if(!$host)
{
	preg_match('@^(?:http://)?([^/]+)@i',
	   		$GO_CONFIG->full_url, $matches);
	   		
	$host=$matches[1];
}
$username = $GO_CONFIG->get_setting('updateclient_username');
$password = $GO_CONFIG->get_setting('updateclient_password');


$uc= new updateclient($host, $username,$password);

$table =new table();

$row = new table_row();
$cell = new table_cell('Servidor:'); /* Host */
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);

$cell = new table_cell();
$input = new input('text', 'host', $host);
$input->set_attribute('style','width:200px');
$cell->innerHTML .= $input->get_html();
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('Nome utilizador:'); /* Username */
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);

$cell = new table_cell();
$input = new input('text', 'username', $username);
$input->set_attribute('style','width:200px');
$cell->innerHTML .= $input->get_html();
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('Password:');
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);

$cell = new table_cell();
$input = new input('password', 'password');
$input->set_attribute('style','width:200px');
$cell->innerHTML .= $input->get_html();

$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);

$form->add_html_element(new button($cmdOk,'javascript:document.forms[0].submit();'));
require($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
require($GO_THEME->theme_path.'footer.inc');
?>