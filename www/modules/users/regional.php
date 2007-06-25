<?php
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');
load_basic_controls();
require_once($GO_LANGUAGE->get_base_language_file('preferences'));

$user=$GO_USERS->get_user($_REQUEST['user_id']);


$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($pref_language.':'));

$select = new select('language', $user['language']);
$languages = $GO_LANGUAGE->get_languages();
foreach($languages as $language)
{
	$select->add_value($language['code'], $language['description']);
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($pref_timezone.':'));

$select = new select('timezone', $user['timezone']);
$select->add_value('12','+12 GMT');
$select->add_value('11.5','+11.5 GMT');
$select->add_value('11','+11 GMT');
$select->add_value('10.5','+10.5 GMT');
$select->add_value('10','+10 GMT');
$select->add_value('9.5','+9.5 GMT');
$select->add_value('9','+9 GMT');
$select->add_value('8.5','+8.5 GMT');
$select->add_value('8','+8 GMT');
$select->add_value('7.5','+7.5 GMT');
$select->add_value('7','+7 GMT');
$select->add_value('6.5','+6.5 GMT');
$select->add_value('6','+6 GMT');
$select->add_value('5.5','+5.5 GMT');
$select->add_value('5','+5 GMT');
$select->add_value('4.5','+4.5 GMT');
$select->add_value('4','+4 GMT');
$select->add_value('3.5','+3.5 GMT');
$select->add_value('3','+3 GMT');
$select->add_value('2.5','+2.5 GMT');
$select->add_value('2','+2 GMT');
$select->add_value('1.5','+1.5 GMT');
$select->add_value('1','+1 GMT');
$select->add_value('0','GMT');
$select->add_value('-1','-1 GMT');
$select->add_value('-1.5','-1.5 GMT');
$select->add_value('-2','-2 GMT');
$select->add_value('-2.5','-2.5 GMT');
$select->add_value('-3','-3 GMT');
$select->add_value('-3.5','-3.5 GMT');
$select->add_value('-4','-4 GMT');
$select->add_value('-4.5','-4.5 GMT');
$select->add_value('-5','-5 GMT');
$select->add_value('-5.5','-5.5 GMT');
$select->add_value('-6','-6 GMT');
$select->add_value('-6.5','-6.5 GMT');
$select->add_value('-7','-7 GMT');
$select->add_value('-7.5','-7.5 GMT');
$select->add_value('-8','-8 GMT');
$select->add_value('-8.5','-8.5 GMT');
$select->add_value('-9','-9 GMT');
$select->add_value('-9.5','-9.5 GMT');
$select->add_value('-10','-10 GMT');
$select->add_value('-10.5','-10.5 GMT');
$select->add_value('-11','-11 GMT');
$select->add_value('-11.5','-11.5 GMT');
$select->add_value('-12','-12 GMT');

$checkbox = new checkbox('DST', 'DST', '1', $adjust_to_dst, $_SESSION['GO_SESSION']['DST']);

$row->add_cell(new table_cell($select->get_html().$checkbox->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($pref_date_format.':'));

$select = new select('date_format', $user['date_format']);
for ($i=0;$i<count($GO_CONFIG->date_formats);$i++)
{
	$friendly[strpos($GO_CONFIG->date_formats[$i], 'Y')]=$strYear;
	$friendly[strpos($GO_CONFIG->date_formats[$i], 'm')]=$strMonth;
	$friendly[strpos($GO_CONFIG->date_formats[$i], 'd')]=$strDay;

	$strFriendly = $friendly[0].$user['date_seperator'].
	$friendly[1].$user['date_seperator'].
	$friendly[2];

	$select->add_value($GO_CONFIG->date_formats[$i], $strFriendly);
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($pref_date_seperator.':'));

$select = new select('date_seperator', $user['date_seperator']);
for ($i=0;$i<count($GO_CONFIG->date_seperators);$i++)
{
	$select->add_value($GO_CONFIG->date_seperators[$i], $GO_CONFIG->date_seperators[$i]);
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($pref_time_format.':'));

$select = new select('time_format', $user['time_format']);
$select->add_value($GO_CONFIG->time_formats[0], $strTwentyfourHourFormat);
$select->add_value($GO_CONFIG->time_formats[1], $strTwelveHourFormat);

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($pref_first_weekday.':'));

$select = new select('first_weekday', $user['first_weekday']);
$select->add_value('0', $full_days[0]);
$select->add_value('1', $full_days[1]);

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($pref_thousands_seperator.':'));

$input = new input('text', 'thousands_seperator', $user['thousands_seperator']);
$input->set_attribute('style', 'width:50px;');
$input->set_attribute('maxlength', '1');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($pref_decimal_seperator.':'));

$input = new input('text', 'decimal_seperator', $user['decimal_seperator']);
$input->set_attribute('style', 'width:50px;');
$input->set_attribute('maxlength', '1');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($pref_currency.':'));

$input = new input('text', 'currency', $user['currency']);
$input->set_attribute('style', 'width:50px;');
$input->set_attribute('maxlength', '3');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);
?>
<div class="inner-tab">
<form id="regional-form" class="x-form" method="post">
<?php
echo $table->get_html();
?>
</form>
</div>
<script type="text/javascript">

var regionalForm;

function submitregionalForm()
{
	regionalForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_regional','user_id' : <?php echo $user['id']; ?>},
		waitMsg: GOlang['waitMsgSave'],
		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	regionalForm = new Ext.BasicForm('regional-form', {
		waitMsgTarget: 'box-bd'
	});

	user.destroyDialogButtons();
	var dialog = user.getDialog();

	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdOk'],
		handler: function(){
			submitregionalForm();
			dialog.hide();
		}
	}, this);
	
	dialog.addButton({
		id: 'ok',
		text: GOlang['cmdApply'],
		handler: function(){
			submitregionalForm();
		}
	}, this);

	dialog.addButton('Close', dialog.hide, dialog);

});

</script>