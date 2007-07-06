<?php
require('../../Group-Office.php');

load_basic_controls();



$account_id=isset($_REQUEST['account_id']) ? smart_addslashes($_REQUEST['account_id']) : 0;




$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require($GO_LANGUAGE->get_language_file('email'));


require_once($GO_MODULES->class_path."email.class.inc");
$email = new email();

if($account_id>0)
{
	
	$account = $email->get_account($account_id);
	if (!$GO_MODULES->modules['email']['write_permission'] && $account['user_id'] != $GO_SECURITY->user_id)
	{
		require_once($GO_CONFIG->root_path."error_docs/403.inc");
		require_once($GO_THEME->theme_path."footer.inc");
		exit();
	}
	
}else {
	$account['name'] = isset($_REQUEST['name']) ? smart_stripslashes($_REQUEST['name']) : $_SESSION['GO_SESSION']['name'];
	$account['email'] = isset($_REQUEST['email']) ? smart_stripslashes($_REQUEST['email']) : $_SESSION['GO_SESSION']['email'];
	$account['host'] = isset($_REQUEST['host']) ? smart_stripslashes($_REQUEST['host']) : 'localhost';
	$account['type'] = isset($_REQUEST['type']) ? smart_stripslashes($_REQUEST['type']) : 'imap';
	$account['port'] = isset($_REQUEST['port']) ? smart_stripslashes($_REQUEST['port']) : '143';
	// default value for user is the part of email_address before "@" - djk
	$account['username'] = isset($_REQUEST['email_user']) ? smart_stripslashes($_REQUEST['email_user']) : substr($account['email'], 0, strpos($account['email'],'@'));
	$account['password'] = isset($_REQUEST['email_pass']) ? smart_stripslashes($_REQUEST['email_pass']) : '';
	$account['signature'] = isset($_REQUEST['signature']) ? smart_stripslashes($_REQUEST['signature']) : '';
	$account['mbroot'] = isset($_REQUEST['mbroot']) ? smart_stripslashes($_REQUEST['mbroot']) : '';
	$account['examine_headers'] = isset($_REQUEST['examine_headers']) ? true : false;
	$account['use_ssl'] = isset($_REQUEST['use_ssl']) ? true : false;
	$account['novalidate_cert'] = isset($_REQUEST['novalidate_cert']) ? true : false;
	$account['user_id']=isset($_REQUEST['account_user_id']) ? smart_stripslashes($_REQUEST['account_user_id']) : $GO_SECURITY->user_id;
}
?>

<div class="inner-tab">
<form id="properties-form" class="x-form" method="post">
<?php

$table = new table();

if($admin_permission = $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
{
	//$input = new input('hidden', 'user_id', $account['user_id']);
	
	$row = new table_row();
	$row->add_cell(new table_cell($strOwner.':'));
	$input = new input('text','user-select');
	$input->set_attribute('id','user-select');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}


$row = new table_row();
$row->add_cell(new table_cell($strName.':'));
$input = new input('text','name',$account['name']);
$input->set_attribute('maxlength','100');
$input->set_attribute('style','width:300px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strEmail.':'));
$input = new input('text','mail_address',$account['email']);
$input->set_attribute('maxlength','100');
$input->set_attribute('style','width:300px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);



if (!$GO_MODULES->write_permission)
{
	$form->add_html_element(new input('hidden','use_ssl',$account['use_ssl']));
	$form->add_html_element(new input('hidden','novalidate_cert',$account['novalidate_cert']));
	$form->add_html_element(new input('hidden','host',$account['host']));
	$form->add_html_element(new input('hidden','mbroot',$account['mbroot']));
	$form->add_html_element(new input('hidden','type',$account['type']));
	$form->add_html_element(new input('hidden','port',$account['port']));
	$form->add_html_element(new input('hidden','email_user',$account['username']));
	$form->add_html_element(new input('hidden','email_pass',$account['password']));
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
	$select = new select('type',$account['type']);
	$select->set_attribute('onchange','javascript:change_port();');
	$select->add_value('pop3','POP3');
	$select->add_value('imap','IMAP');
	$cell->add_html_element($select);


	$checkbox = new checkbox('use_ssl', 'use_ssl', '1', 'SSL', $account['use_ssl'], false);
	$checkbox->set_attribute('onclick','javascript:change_port()');
	$cell->add_html_element($checkbox);

	$checkbox = new checkbox('novalidate_cert','novalidate_cert', '1', $ml_novalidate_cert, $account['novalidate_cert']);
	$cell->add_html_element($checkbox);

	$row->add_cell($cell);
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_port.':'));
	$input = new input('text','port',$account['port']);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_host.':'));
	$input = new input('text','host',$account['host']);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($ml_root.':'));
	$input = new input('text','mbroot',$account['mbroot']);
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
	$input = new input('text','email_user',$account['username']);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('autocomplete','off');
	$input->set_attribute('style','width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$row->add_cell(new table_cell($strPassword.':'));
	$input = new input('password','email_pass',$account['password']);
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
$textarea = new textarea('signature',$account['signature']);
$textarea->set_attribute('style','width:300px;height:50px;');
$row->add_cell(new table_cell($textarea->get_html()));
$table->add_row($row);

$row = new table_row();
$checkbox = new checkbox('examine_headers','examine_headers', '1', $ml_examine_headers, ($account['examine_headers']=='1'));
$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

echo $table->get_html();
?>
</div>
<script type="text/javascript">


var propertiesForm;

function submitForm()
{
	propertiesForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_account_properties','account_id' : <?php echo $account_id; ?>},
		waitMsg:GOlang['waitMsgSave'],
		success:function(form, action){
			//reload grid
			//users.getDataSource().reload();
			
			if(action.result.account_id)
			{
				account.setAccountID(action.result.account_id);	
				Ext.MessageBox.alert(GOlang['strSuccess'], action.result.errors);			
			}
			
		},

		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	propertiesForm = new Ext.BasicForm('properties-form', {
		waitMsgTarget: 'box-bd'
	});
	
	account.destroyDialogButtons();
	var dialog = account.getDialog();

	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdOk'],
		handler: function(){
			submitForm();
			dialog.hide();
		}
	}, this);
	
	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdApply'],
		handler: function(){
			submitForm();
		}
	}, this);

	dialog.addButton('Close', dialog.hide, dialog);
	
	
	//generate user combobox
	/*
	var ds = new Ext.data.Store({

		proxy: new Ext.data.HttpProxy({
			url: '<?php echo $GO_MODULES->modules['users']['url']; ?>users_json.php'
		}),

		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'id'
		}, [
		{name: 'id'},
		{name: 'name'}
		]),
		// turn on remote sorting
		remoteSort: true
	});
	ds.setDefaultSort('name', 'asc');
	

	
	var userSelect = new Ext.form.ComboBox({
		store: ds,
		displayField:'name',
		typeAhead: true,
		valueField: 'id',
		triggerAction: 'all',
		emptyText:GOlang['strPleaseSelect'],
		width: 240,
		selectOnFocus:true
	});	
	ds.on('load', function() {userSelect.setValue(<?php echo $account['user_id']; ?>);}, this, {single: true});
	ds.load();
	
	userSelect.applyTo('user-select');*/
	
	<?php
	if($admin_permission)
	{
		$user = $GO_USERS->get_user($account['user_id']);
		echo 'var userCombo = new selectUser("user-select", "user_id", '.$user['id'].', "'.format_name($user['last_name'],$user['first_name'],$user['middle_name']).'",300);';
	}
	?>
	
	

});
document.forms['properties-form'].name.focus();




</script>




