<?php
if ($account && $task != 'save_account')
{

	if (!$GO_MODULES->modules['email']['write_permission'] && $account['user_id'] != $GO_SECURITY->user_id)
	{
		require_once($GO_CONFIG->root_path."error_docs/403.inc");
		require_once($GO_THEME->theme_path."footer.inc");
		exit();
	}

	$page_title=$ml_edit_account;
	$name = $account["name"];
	$mail_address = $account["email"];
	$host = $account["host"];
	$type = $account["type"];
	$port = $account["port"];
	$user = $account["username"];
	$pass = $account['password'];
	$signature = $account["signature"];
	$mbroot = utf7_imap_decode($account["mbroot"]);
	$examine_headers = $account['examine_headers'] == '1' ? true : false;
	$auto_check = $account['auto_check'] == '1' ? true : false;
	$use_ssl = $account['use_ssl'] == '1' ? true : false;
	$novalidate_cert = $account['novalidate_cert'] == '1' ? true : false;
	$account_user_id=$account['user_id'];
}else
{
	$page_title=$ml_new_account;
	$name = isset($_REQUEST['name']) ? smart_stripslashes($_REQUEST['name']) : $_SESSION['GO_SESSION']['name'];
	$mail_address = isset($_REQUEST['mail_address']) ? smart_stripslashes($_REQUEST['mail_address']) : $_SESSION['GO_SESSION']['email'];
	// default value for host is the smtp_server in Group-Office.php - djk
	//triples
	//$host = isset($_REQUEST['host']) ? smart_stripslashes($_REQUEST['host']) : $GO_CONFIG->smtp_server;
	//triples
	$host = isset($_REQUEST['host']) ? smart_stripslashes($_REQUEST['host']) : 'localhost';
	$type = isset($_REQUEST['type']) ? smart_stripslashes($_REQUEST['type']) : 'imap';
	$port = isset($_REQUEST['port']) ? smart_stripslashes($_REQUEST['port']) : '143';
	// default value for user is the part of email_address before "@" - djk
	$user = isset($_REQUEST['email_user']) ? smart_stripslashes($_REQUEST['email_user']) : substr($mail_address, 0, strpos($mail_address,'@'));
	$pass = isset($_REQUEST['email_pass']) ? smart_stripslashes($_REQUEST['email_pass']) : '';
	$signature = isset($_REQUEST['signature']) ? smart_stripslashes($_REQUEST['signature']) : '';
	$mbroot = isset($_REQUEST['mbroot']) ? smart_stripslashes($_REQUEST['mbroot']) : '';
	$examine_headers = isset($_REQUEST['examine_headers']) ? true : false;
	$auto_check = isset($_REQUEST['auto_check']) ? true : false;
	$use_ssl = isset($_REQUEST['use_ssl']) ? true : false;
	$novalidate_cert = isset($_REQUEST['novalidate_cert']) ? true : false;
	$account_user_id=isset($_REQUEST['account_user_id']) ? smart_stripslashes($_REQUEST['account_user_id']) : $GO_SECURITY->user_id;
}




if(isset($feedback))
{
	$p = new html_element('p',$feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$table = new table();

if($GO_MODULES->modules['email']['write_permission'])
{
	$row = new table_row();
	$row->add_cell(new table_cell($strOwner.':'));
	
	load_control('user_autocomplete');
	$xml_fields=array(
		'name'=>'name',
		'id'=>'account_user_id',
		'email'=>'mail_address',
		'username'=>'email_user'
		);
	
	
	
	$ac = new user_autocomplete('account_user_id', $account_user_id, 'email_client',$link_back,$xml_fields,true);
	
	$row->add_cell(new table_cell($ac->get_html()));
	$table->add_row($row);
}

$row = new table_row();
$row->add_cell(new table_cell($strName.':'));
$input = new input('text','name',$name);
$input->set_attribute('maxlength','100');
$input->set_attribute('style','width:300px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strEmail.':'));
$input = new input('text','mail_address',$mail_address);
$input->set_attribute('maxlength','100');
$input->set_attribute('style','width:300px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);



if (!$GO_MODULES->write_permission)
{
	$form->add_html_element(new input('hidden','use_ssl',$use_ssl));
	$form->add_html_element(new input('hidden','novalidate_cert',$novalidate_cert));
	$form->add_html_element(new input('hidden','host',$host));
	$form->add_html_element(new input('hidden','mbroot',$mbroot));
	$form->add_html_element(new input('hidden','type',$type));
	$form->add_html_element(new input('hidden','port',$port));
	$form->add_html_element(new input('hidden','email_user',$user));
	$form->add_html_element(new input('hidden','email_pass',$pass));
}else
{
	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_type.':'));

	$cell = new table_cell();
	$select = new select('type',$type);
	$select->set_attribute('onchange','javascript:change_port();');
	$select->add_value('pop3','POP3');
	$select->add_value('imap','IMAP');
	$cell->add_html_element($select);


	$checkbox = new checkbox('use_ssl', 'use_ssl', '1', 'SSL', $use_ssl, false);
	$checkbox->set_attribute('onclick','javascript:change_port()');
	$cell->add_html_element($checkbox);

	$checkbox = new checkbox('novalidate_cert','novalidate_cert', '1', $ml_novalidate_cert, $novalidate_cert);
	$cell->add_html_element($checkbox);

	$row->add_cell($cell);
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_port.':'));
	$input = new input('text','port',$port);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_host.':'));
	$input = new input('text','host',$host);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_root.':'));
	$input = new input('text','mbroot',$mbroot);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style','width:300px');
	if ($type  == 'pop3')
	{
		$input->set_attribute('disabled','true');
	}
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($strUsername.':'));
	$input = new input('text','email_user',$user);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('autocomplete','off');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($strPassword.':'));
	$input = new input('password','email_pass',$pass);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('autocomplete','off');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
}

$row = new table_row();
$row->add_cell(new table_cell($ml_signature.':'));
$textarea = new textarea('signature',$signature);
$textarea->set_attribute('style','width:300px;height:50px;');
$row->add_cell(new table_cell($textarea->get_html()));
$table->add_row($row);

$row = new table_row();
$checkbox = new checkbox('examine_headers','examine_headers', '1', $ml_examine_headers, $examine_headers);
$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','2');	
$cell->add_html_element(new checkbox('auto_check','auto_check', true, $ml_show_new_mail, $auto_check));
$row->add_cell($cell);
$table->add_row($row);


$tabstrip->add_html_element($table);
$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_account', 'true');"));
$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_account', 'false');"));
$tabstrip->add_html_element(new button($cmdCancel,'javascript:document.location=\''.$return_to.'\''));
?>
<script type="text/javascript">
document.forms[0].name.focus();
function change_port()
{
	if (document.forms[0].type.value == "imap")
	{
		if(document.forms[0].use_ssl.checked)
		{
			document.forms[0].port.value = "993";
		}else
		{
			document.forms[0].port.value = "143";
		}
		document.forms[0].mbroot.disabled = false;
	}else
	{
		if(document.forms[0].use_ssl.checked)
		{
			document.forms[0].port.value = "995";
		}else
		{
			document.forms[0].port.value = "110";
		}
		document.forms[0].mbroot.disabled = true;
	}
}

function save_account()
{
	document.forms[0].task.value='save_account';
	document.forms[0].submit();
}

</script>
