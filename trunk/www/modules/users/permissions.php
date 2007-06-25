<?php
require('../../Group-Office.php');

load_control('checkbox');
require($GO_LANGUAGE->get_language_file('users'));

$user_id = smart_addslashes($_REQUEST['user_id']);
?>
<div class="inner-tab">
<form id="permissions-form" class="x-form" method="post">
<div id="col1" style="float:left;width:300px;">
<fieldset style="width:100%;">
<legend>Module permissions</legend>
<table>
<tr>
<th><?php echo $admin_module; ?></th>
<th><?php echo $admin_use; ?></th>
<th><?php echo $admin_manage; ?></th>
</tr>
<?php
$module_count = $GO_MODULES->get_modules('0');
while($GO_MODULES->next_record())
{
	//require language file to obtain module name in the right language
	$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));

	if(file_exists($language_file))
	{
		require_once($language_file);
	}

	$lang_var = isset($lang_modules[$GO_MODULES->f('id')]) ? $lang_modules[$GO_MODULES->f('id')] : $GO_MODULES->f('id');


	if($user_id > 0)
	{
		$read_check = $GO_SECURITY->has_permission($user_id, $GO_MODULES->f('acl_read'));
	}else
	{
		$modules_read = isset($_POST['modules_read']) ? $_POST['modules_read'] : array();
		$read_check = in_array($GO_MODULES->f('id'), $modules_read);
	}

	$read_checkbox = new checkbox(
	$GO_MODULES->f('acl_read'),
	'modules_read[]',
	$GO_MODULES->f('id'),
	'',
	$read_check);



	if($user_id > 0)
	{
		$write_check = $GO_SECURITY->has_permission($user_id, $GO_MODULES->f('acl_write'));
	}else
	{
		$modules_write = isset($_POST['modules_write']) ? $_POST['modules_write'] : array();
		$write_check = in_array($GO_MODULES->f('id'), $modules_write);
	}

	$write_checkbox = new checkbox(
	$GO_MODULES->f('acl_write'),
	'modules_write[]',
	$GO_MODULES->f('id'),
	'',
	$write_check);


	echo '<tr><td>'.$lang_var.'</td>'.
	'<td>'.$read_checkbox->get_html().'</td>'.
	'<td>'.$write_checkbox->get_html().'</td></tr>';
}
?>
</table>
</fieldset>


<fieldset style="width:100%;">
<legend>User groups</legend>
<table>
<input type="hidden" name="user_groups[]" value="<?php echo $GO_CONFIG->group_everyone; ?>" />
<?php
$GO_GROUPS->get_groups();
$groups2 = new $go_groups_class();

while($GO_GROUPS->next_record())
{
	if ($GO_GROUPS->f('id') != $GO_CONFIG->group_everyone)
	{
		$group_check= $groups2->is_in_group($user_id, $GO_GROUPS->f('id'));


		$checkbox = new checkbox(
		'group_'.$GO_GROUPS->f('id'),
		'user_groups[]',
		$GO_GROUPS->f('id'),
		$GO_GROUPS->f('name'),
		$group_check);

		if($user_id == 1 && $GO_GROUPS->f('id') == $GO_CONFIG->group_root)
		{
			$checkbox->set_attribute('disabled','true');
			$checkbox->set_attribute('checked','true');
			echo '<input type="hidden" name="user_groups[]" value="'.$GO_GROUPS->f('id').'" />';
		}
		
		echo '<tr><td>'.$checkbox->get_html().'</td></tr>';
	}
}
?>
</table>
</fieldset>

</div>

<div id="col2" style="float:left;width:300px;margin-left:30px;">
<fieldset style="width:100%;">
<legend>Visibility</legend>
<table>
<input type="hidden" name="user_groups[]" value="<?php echo $GO_CONFIG->group_everyone; ?>" />
<?php
$GO_GROUPS->get_groups();
$groups2 = new $go_groups_class();

$user = $GO_USERS->get_user($user_id);

while($GO_GROUPS->next_record())
{
	if ($GO_GROUPS->f('id') != $GO_CONFIG->group_everyone)
	{
		$visible_group_check= $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']);

		$checkbox = new checkbox(
		'vgroup_'.$GO_GROUPS->f('id'),
		'visible_user_groups[]',
		$GO_GROUPS->f('id'),
		$GO_GROUPS->f('name'),
		$visible_group_check);

		if($user_id == 1 && $GO_GROUPS->f('id') == $GO_CONFIG->group_root)
		{
			$checkbox->set_attribute('disabled','true');
			$checkbox->set_attribute('checked','true');
			echo '<input type="hidden" name="user_groups[]" value="'.$GO_GROUPS->f('id').'" />';
		}
		
		echo '<tr><td>'.$checkbox->get_html().'</td></tr>';
	}
}
?>
</table>
</fieldset>
</div>

</form>
</div>
<script type="text/javascript">




var permissionsForm;

function submitPermissionsForm()
{
	permissionsForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_permissions','user_id' : <?php echo $user_id; ?>},
		waitMsg: GOlang['waitMsgSave'],
		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	permissionsForm = new Ext.BasicForm('permissions-form', {
		waitMsgTarget: 'box-bd'
	});

	user.destroyDialogButtons();
	var dialog = user.getDialog();

	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdOk'],
		handler: function(){
			submitPermissionsForm();
			dialog.hide();
		}
	}, this);
	
	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdApply'],
		handler: function(){
			submitPermissionsForm();
		}
	}, this);

	dialog.addButton('Close', dialog.hide, dialog);

});

</script>


