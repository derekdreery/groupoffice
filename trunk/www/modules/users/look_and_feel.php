<?php
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');
load_basic_controls();
require_once($GO_LANGUAGE->get_base_language_file('preferences'));

$user=$GO_USERS->get_user($_REQUEST['user_id']);

$table = new table();

if ($GO_CONFIG->allow_themes == true)
{
	$row = new table_row();
	$row->add_cell(new table_cell($pref_theme.':'));

	$select = new select('theme', $user['theme']);
	$themes = $GO_THEME->get_themes();
	foreach($themes as $theme)
	{
		$select->add_value($theme, $theme);
	}
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);
}

$row = new table_row();
$row->add_cell(new table_cell($pref_startmodule.':'));

$select = new select('start_module', $user['start_module']);
$GO_MODULES->get_modules();
while ($GO_MODULES->next_record())
{
	if ($GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_read')) ||
	$GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_write'))
	)
	{
		$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));
		if(file_exists($language_file))
		{
			require_once($language_file);
		}
		$lang_var = isset($lang_modules[$GO_MODULES->f('id')]) ?
		$lang_modules[$GO_MODULES->f('id')] : $GO_MODULES->f('id');
		$select->add_value($GO_MODULES->f('id'), $lang_var);
	}
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($pref_max_rows_list.':'));

$select = new select('max_rows_list', $user['max_rows_list']);

$select->add_value('10','10');
$select->add_value('15','15');
$select->add_value('20','20');
$select->add_value('25','25');
$select->add_value('30','30');
$select->add_value('50','50');
$select->add_value('75','75');
$select->add_value('100','100');
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($pref_name_order.':'));

$select = new select('sort_name', $user['sort_name']);
$select->add_value('first_name', $strFirstName);
$select->add_value('last_name', $strLastName);

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);
?>
<div class="inner-tab">
<form id="lookandfeel-form" class="x-form" method="post">
<?php
echo $table->get_html();
?>
</form>
</div>
<script type="text/javascript">

var lookandfeelForm;

function submitlookandfeelForm()
{
	lookandfeelForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_lookandfeel','user_id' : <?php echo $user['id']; ?>},
		waitMsg: GOlang['waitMsgSave'],

		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	lookandfeelForm = new Ext.BasicForm('lookandfeel-form', {
		waitMsgTarget: 'box-bd'
	});

	user.destroyDialogButtons();
	var dialog = user.getDialog();

	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdOk'],
		handler: function(){
			submitlookandfeelForm();
			dialog.hide();
		}
	}, this);
	
	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdApply'],
		handler: function(){
			submitlookandfeelForm();
		}
	}, this);

	dialog.addButton('Close', dialog.hide, dialog);

});
</script>