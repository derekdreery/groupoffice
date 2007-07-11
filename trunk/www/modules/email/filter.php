<?php
require('../../Group-Office.php');

load_basic_controls();



$filter_id=isset($_REQUEST['filter_id']) ? smart_addslashes($_REQUEST['filter_id']) : 0;





$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require($GO_LANGUAGE->get_language_file('email'));


require_once($GO_MODULES->class_path."email.class.inc");
$email = new email();

if ($filter_id > 0)
{
	$filter = $email->get_filter($filter_id);
}else
{
	$filter['account_id']=$_REQUEST['account_id'];
	$filter['field'] = isset($_POST['field']) ? $_POST['field'] : '';
	$filter['keyword'] = isset($_POST['keyword']) ? $_POST['keyword'] : '';
	$filter['folder'] = isset($_POST['folder']) ? $_POST['folder'] : '';
	$filter['priority'] = isset($_POST['priority']) ? $_POST['priority'] : '1';
	$filter['mark_as_read'] = isset($_POST['mark_as_read']);
}
?>
<div class="inner-tab">
<form id="filter-form" name="filter-form" class="x-form" method="post">
<input type="hidden" name="account_id" value="<?php echo $filter['account_id']; ?>" />
<input type="hidden" name="filter_id" value="<?php echo $filter_id; ?>" />
<table border="0" cellpadding="4" cellspacing="0">
<tr>
	<td colspan="2"></td>
</tr>
<tr>
	<td><?php echo $ml_choose_action; ?></td>
	<td><?php echo $ml_search_criteria; ?>:</td>
</tr>
<tr>
	<td>
	<?php
	$select=new select('field', $filter['field']);
	$select->add_value('sender',$ml_email_is);
	$select->add_value('subject',$ml_subject_is);
	$select->add_value('to',$ml_to_is);
	$select->add_value('cc',$ml_cc_is);
	
	echo $select->get_html();
	?>
	</td>
	<td>
	<?php
	$input = new input('text','keyword',$filter['keyword'],true);
	$input->print_html();
	?>
	</td>
</tr>
<tr>
	<td colspan="2"><?php echo $ml_move_to; ?></td>
</tr>
<tr>
	<td>
	<?php
	$select=new select('folder', $filter['folder']);
	$select->add_value('',$cmdPleaseSelect);
	$email->get_subscribed($filter['account_id']);
	while ($email->next_record())
	{
	  if (!($email->f('attributes')&LATT_NOSELECT) && $email->f('name') != 'INBOX')
	  {
	    $select->add_value($email->f('name'), str_replace('INBOX'.$email->f('delimiter'), '', $email->f('name')));
	  }
	}
	echo $select->get_html();
	?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<?php
	$checkbox = new checkbox('mark_as_read','mark_as_read','1', $ml_mark_as_read, ($filter['mark_as_read']=='1'));
	echo $checkbox->get_html();
	?>
	</td>
</tr>
</table>
</form>
</div>

<script type="text/javascript">


var propertiesForm;

function submitForm()
{
	propertiesForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_filter'},
		waitMsg:GOlang['waitMsgSave'],
		success:function(form, action){
			
			if(action.result.filter_id && action.result.filter_id>0)
			{
				filter.setFilterID(action.result.filter_id);
				document.forms['filter-form'].filter_id.value=action.result.filter_id;
				//Ext.MessageBox.alert(GOlang['strSuccess'], action.result.errors);			
			}
			
			account.getFiltersDS().load();
			
		},

		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	propertiesForm = new Ext.BasicForm('filter-form', {
		waitMsgTarget: 'filter-bd'
	});
	
	var dialog = filter.getDialog();
	
	if(typeof(dialog.buttons) == 'undefined')
	{

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
	}

});
document.forms['filter-form'].field.focus();
</script>