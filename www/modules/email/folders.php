<?php
require('../../Group-Office.php');

load_basic_controls();



$account_id=isset($_REQUEST['account_id']) ? smart_addslashes($_REQUEST['account_id']) : 0;




$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require($GO_LANGUAGE->get_language_file('email'));


require_once($GO_MODULES->class_path."email.class.inc");
$email = new email();


$account = $email->get_account($account_id);
if (!$GO_MODULES->modules['email']['write_permission'] && $account['user_id'] != $GO_SECURITY->user_id)
{
	require_once($GO_CONFIG->root_path."error_docs/403.inc");
	require_once($GO_THEME->theme_path."footer.inc");
	exit();
}



//get all the Group-Office folders as an array
$email->get_folders($account['id']);
$go_mailboxes = array();
while ($email->next_record())
{
	$go_mailboxes[] = $email->Record;

}
$mcount = count($go_mailboxes);



?>

<div class="inner-tab">
<form id="folders-form" class="x-form" method="post">
<?php

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($ml_sent_items.':'));
$select=new select('sent', $account['sent']);
$select->add_value('', $ml_disable);
for ($i=0;$i<$mcount;$i++)
{
	if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
	{
		$select->add_value($go_mailboxes[$i]['name'],
		utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
	}
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ml_drafts.':'));
$select=new select('drafts', $account['drafts']);
$select->add_value('', $ml_disable);
for ($i=0;$i<$mcount;$i++)
{
	if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
	{
		$select->add_value($go_mailboxes[$i]['name'],
		utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
	}
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ml_trash.':'));
$select=new select('trash', $account['trash']);
$select->add_value('', $ml_disable);
for ($i=0;$i<$mcount;$i++)
{
	if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
	{
		$select->add_value($go_mailboxes[$i]['name'],
		utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
	}
}

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ml_spam.':'));
$select=new select('spam', $account['spam']);
$select->add_value('', $ml_disable);
for ($i=0;$i<$mcount;$i++)
{
	if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
	{
		$select->add_value($go_mailboxes[$i]['name'],
		utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
	}
}

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ml_spamtag.':'));
$input = new input('text', 'spamtag', $account['spamtag']);
$input->set_attribute('style','width:100px;');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

echo $table->get_html();
?>
</div>
<script type="text/javascript">


var foldersForm;

function submitForm()
{
	foldersForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_account_folders','account_id' : <?php echo $account_id; ?>},
		waitMsg:GOlang['waitMsgSave'],
		success:function(form, action){
			
		},

		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	foldersForm = new Ext.BasicForm('folders-form', {
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

});
</script>