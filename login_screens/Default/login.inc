<?php
require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_header.inc');

$form = new form('login_form', 'post', $GO_CONFIG->host.'index.php');
$form->set_attribute('target','_parent');

$input = new input('hidden', 'load_frames', $load_frames);
$form->add_html_element($input);

$input = new input('hidden', 'task', 'login');
$form->add_html_element($input);
if (isset($_REQUEST['return_to']))
{
	$input = new input('hidden', 'return_to', $_REQUEST['return_to']);
	$form->add_html_element($input);
}

$table = new table();
$table->set_attribute('cellspacing','2');
$table->set_attribute('cellpadding','1');
$table->set_attribute('style','vertical-align:middle;');
/*
$row = new table_row();

$h1 = new html_element('h1',$GO_CONFIG->title);
$cell = new table_cell($h1->get_html());
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','text-align:center;');

$row->add_cell($cell);
$table->add_row($row);*/

$row = new table_row();


$cell = new table_cell();

if($GO_CONFIG->allow_registration)
{
	$h2=new html_element('h2', $login_no_account);
	$cell->add_html_element($h2);
	
	$p = new html_element('p', $login_no_account_text);
	$cell->add_html_element($p);
	
	$href = 'register.php';
	if (isset($_REQUEST['return_to']))
	{
		$href = add_params_to_url($href, 'return_to='.urlencode($_REQUEST['return_to']));
	}
	$hyperlink = new hyperlink($href, sprintf($login_register, $GO_CONFIG->title));
	
	$cell->add_html_element($hyperlink);
	
	$h2 = new html_element('h2',sprintf($login_to, $GO_CONFIG->title));
	$h2->set_attribute('style','margin-top:20px;');
	$cell->add_html_element($h2);
											
}else {
	$h2 = new html_element('h2',sprintf($login_to, $GO_CONFIG->title));
	$cell->add_html_element($h2);
}
				
$cell->set_attribute('colspan','2');
//$cell->set_attribute('style','text-align:center;');

if (isset($feedback)) $cell->innerHTML .= $feedback; 
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell($login_language.':');
//$cell->set_attribute('style', 'text-align:right');
$row->add_cell($cell);

$select = new select('SET_LANGUAGE', $GO_LANGUAGE->language['code']);
$select->set_attribute('onchange', 'javascript:set_language(this);');
$languages = $GO_LANGUAGE->get_languages();
while($language = array_shift($languages))
{
	$select->add_value($language['code'], $language['description']);
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$cell = new table_cell($login_username.':');
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);

$cell = new table_cell();
$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$input = new input('text', 'username', $username);
$input->set_attribute('style','width:200px');
$cell->innerHTML .= $input->get_html();

if (count($auth_sources) > 1)
{
	$cookie_key = isset($_COOKIE['GO_AUTH_SOURCE_KEY']) ? $_COOKIE['GO_AUTH_SOURCE_KEY'] : '0';
  $auth_source_key = isset($_REQUEST['auth_source_key']) ? $_REQUEST['auth_source_key'] : $cookie_key;
  
	$select = new select('auth_source_key', $auth_source_key);
	for($i=0;$i<count($auth_sources); $i++)
	{
		$select->add_value($i, $auth_sources[$i]['name']);
	}
	
	$cell->innerHTML .= '@'.$select->get_html();
	
}else
{
	$input = new input('hidden', 'auth_source_key','0');
	$cell->innerHTML .= $input->get_html();
}

$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell($login_password.':');
$cell->set_attribute('style', 'whitespace:nowrap;');
$row->add_cell($cell);

$cell = new table_cell();
$input = new input('password', 'password');
$input->set_attribute('style','width:200px');

$hyperlink = new hyperlink('lost_password.php', $login_lost_password);
//$hyperlink->set_attribute('class', 'normal');

$cell->innerHTML .= $input->get_html().$hyperlink->get_html();
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();

$checkbox = new checkbox('remind', 'remind', 'true', $login_remind, false);

$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','padding-top:10px;');
$row->add_cell($cell);

$table->add_row($row);

$row = new table_row();
$button = new button($cmdLogin, 'javascript:document.login_form.submit();');
$cell = new table_cell($button->get_html());
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','padding-top:10px;');
$row->add_cell($cell);
$table->add_row($row);
$form->innerHTML .= $table->get_html();
echo $form->get_html();
?>

<script type="text/javascript" language="javascript">

document.login_form.username.focus();

function set_language(dropbox)
{
	document.location='<?php echo $_SERVER['PHP_SELF']; ?>?SET_LANGUAGE='+dropbox.value;
}
</script>
</form>
<?php
require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc');
?>
