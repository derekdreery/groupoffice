<?php
require('../../Group-Office.php');

load_basic_controls();
require($GO_LANGUAGE->get_language_file('users'));

$user_id = !empty($_REQUEST['user_id']) ? smart_addslashes($_REQUEST['user_id']) : '0';

if($user_id>0)
{
	$user=$GO_USERS->get_user($user_id);
}else {
	$user['first_name'] = isset($_POST['first_name']) ?  smart_stripslashes(trim($_POST['first_name'])) : '';
	$user['middle_name'] = isset($_POST['middle_name']) ?  smart_stripslashes(trim($_POST['middle_name'])) : '';
	$user['last_name'] = isset($_POST['last_name']) ?  smart_stripslashes(trim($_POST['last_name'])) : '';
	$user['initials'] = isset($_POST['initials']) ? smart_stripslashes($_POST["initials"]) : '';
	$user['title'] = isset($_POST['title']) ? smart_stripslashes($_POST["title"]) : '';
	$user['birthday'] = isset($_POST['birthday']) ? smart_stripslashes($_POST["birthday"]) : '';
	$user['email'] = isset($_POST['email']) ? smart_stripslashes($_POST["email"]) : '';
	$user['home_phone'] = isset($_POST['home_phone']) ? smart_stripslashes($_POST["home_phone"]) : '';
	$user['work_phone'] = isset($_POST['work_phone']) ? smart_stripslashes($_POST["work_phone"]) : '';
	$user['fax'] = isset($_POST['fax']) ? smart_stripslashes($_POST["fax"]) : '';
	$user['cellular'] = isset($_POST['cellular']) ? smart_stripslashes($_POST["cellular"]) : '';
	$user['country_id'] = isset($_POST['country_id']) ? smart_addslashes($_POST["country_id"]) : $GO_CONFIG->default_country_id;
	$user['state'] = isset($_POST['state']) ? smart_stripslashes($_POST["state"]) : '';
	$user['city'] = isset($_POST['city']) ? smart_stripslashes($_POST["city"]) : '';
	$user['zip'] = isset($_POST['zip']) ? smart_stripslashes($_POST["zip"]) : '';
	$user['address'] = isset($_POST['address']) ? smart_stripslashes($_POST["address"]) : '';
	$user['address_no'] = isset($_POST['address_no']) ? smart_stripslashes($_POST["address_no"]) : '';
	$user['company'] = isset($_POST['company']) ? smart_stripslashes($_POST["company"]) : '';
	$user['department'] =  isset($_POST['department']) ? smart_stripslashes($_POST["department"]) : '';
	$user['function'] =  isset($_POST['function']) ? smart_stripslashes($_POST["function"]) : '';
	$user['work_country_id'] = isset($_POST['work_country_id']) ?  smart_addslashes($_POST["work_country_id"]) : $GO_CONFIG->default_country_id;
	$user['work_state'] = isset($_POST['work_state']) ? smart_stripslashes($_POST["work_state"]) : '';
	$user['work_city'] = isset($_POST['work_city']) ? smart_stripslashes($_POST["work_city"]) : '';
	$user['work_zip'] = isset($_POST['work_zip']) ? smart_stripslashes($_POST["work_zip"]) : '';
	$user['work_address'] = isset($_POST['work_address']) ? smart_stripslashes($_POST["work_address"]) : '';
	$user['work_address_no'] = isset($_POST['work_address_no']) ? smart_stripslashes($_POST["work_address_no"]) : '';
	$user['work_fax'] = isset($_POST['work_fax']) ? smart_stripslashes($_POST["work_fax"]) : '';
	$user['homepage'] = isset($_POST['homepage']) ? smart_stripslashes($_POST["homepage"]) : '';
	$user['sex'] = isset($_POST['sex']) ? smart_stripslashes($_POST["sex"]) : 'M';
	$user['language'] = isset($_POST['language']) ? smart_stripslashes($_POST['language']) : $GO_CONFIG->language;
	$user['theme'] = isset($_POST['theme']) ? smart_stripslashes($_POST['theme']) : $GO_CONFIG->theme;
	$user['username'] = isset($_POST['username']) ? smart_stripslashes($_POST['username']) : '';
	$user['enabled'] = '1';
	$user['link_id']=0;
}
?>
<div class="inner-tab">
<form id="profile-form" class="x-form" method="post">

<?php

$maintable = new table();
$mainrow = new table_row();

$table = new table();

$row = new table_row();
$cell = new table_cell();
$checkbox = new checkbox('enabled', 'enabled','1',$users_enabled, ($user['enabled'] == '1'));
if($user_id==0)
{
	$checkbox->set_attribute('onclick', 'javascript:show_pass(this.checked);');
}
$cell->add_html_element($checkbox);
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

if($user_id==0)
{
	$row = new table_row();
	$row->add_cell(new table_cell($strUsername.'*:'));
	$input = new input('text','username',$user['username'],true,true);
	$input->set_attribute('style','width:200px');
	$input->set_attribute('maxlength','50');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);


	$row = new table_row('passrow1');
	$row->add_cell(new table_cell($admin_password.':'));
	$input = new input('password', 'pass1', '',false,true);
	$input->set_attribute('style','width:200px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row('passrow2');
	$row->add_cell(new table_cell($admin_confirm_password.':'));
	$input = new input('password', 'pass2', '',false,true);
	$input->set_attribute('style','width:200px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
}




$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strFirstName.'*:'));
$input = new input('text','first_name',$user['first_name'],true,true);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strMiddleName.':'));
$input = new input('text','middle_name',$user['middle_name']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strLastName.'*:'));
$input = new input('text','last_name', $user['last_name'],true,true);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strTitle.' / '.$strInitials.':'));
$input1 = new input('text','title', $user['title']);
$input1->set_attribute('style','width:95px');
$input1->set_attribute('maxlength','12');

$span = new html_element('span', ' / ');
$span->set_attribute('style', 'width: 20px;text-align:center;');

$input2 = new input('text','initials', $user['initials']);
$input2->set_attribute('style','width:95px');
$input2->set_attribute('maxlength','50');

$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strSex.':'));
$radiogroup = new radiogroup('sex', $user['sex']);
$male_button = new radiobutton('sex_m', 'M');
$female_button = new radiobutton('sex_f', 'F');

$row->add_cell(new table_cell($radiogroup->get_option($male_button, $strSexes['M']).$radiogroup->get_option($female_button, $strSexes['F'])));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strBirthday.':'));

$input = new input('text','birthday',$user['birthday'],true,true);
$input->set_attribute('id','birthday');
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','address', $user['address']);
$input->set_attribute('style','width:150px');
$input->set_attribute('maxlength','50');

$input1 = new input('text','address_no', $user['address_no']);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','zip', $user['zip']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','city', $user['city']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','state', $user['state']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$select = new select('country_id', $user['country_id']);
$select->set_attribute('style','width:200px;');
$select->add_value('0', $cmdPleaseSelect);
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strEmail.'*'));
$input = new input('text','email', $user['email'],true,true);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$input = new input('text','home_phone', $user['home_phone']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$input = new input('text','fax', $user['fax']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCellular.':'));
$input = new input('text','cellular', $user['cellular']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');
$cell->set_attribute('style','width:350px');
$mainrow->add_cell($cell);


$table = new table();


$row = new table_row();
$row->add_cell(new table_cell($strCompany.':'));
$input = new input('text','company', $user['company']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strDepartment.':'));
$input = new input('text','department', $user['department']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFunction.':'));
$input = new input('text','function', $user['function']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strAddress.':'));
$input = new input('text','work_address', $user['work_address']);
$input->set_attribute('style','width:150px');
$input->set_attribute('maxlength','50');

$input1 = new input('text','work_address_no', $user['work_address_no']);
$input1->set_attribute('style','width:47px');
$input1->set_attribute('maxlength','10');

$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strZip.':'));
$input = new input('text','work_zip', $user['work_zip']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCity.':'));
$input = new input('text','work_city', $user['work_city']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','50');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strState.':'));
$input = new input('text','work_state', $user['work_state']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','30');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strCountry.':'));
$select = new select('work_country_id', $user['work_country_id']);
$select->set_attribute('style','width:200px;');
$select->add_value('0', $cmdPleaseSelect);
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strPhone.':'));
$input = new input('text','work_phone', $user['work_phone']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strFax.':'));
$input = new input('text','work_fax', $user['work_fax']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','20');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($strHomepage.':'));
$input = new input('text','homepage', $user['homepage']);
$input->set_attribute('style','width:200px');
$input->set_attribute('maxlength','100');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$cell = new table_cell($table->get_html());
$cell->set_attribute('valign','top');

$mainrow->add_cell($cell);
$maintable->add_row($mainrow);

echo $maintable->get_html();
?>
</form>
</div>
<script type="text/javascript">

user.setLinkID(<?php echo $user['link_id']; ?>);

var profileForm;

function submitForm()
{
	profileForm.submit(
	{
		url:'./action.php',
		params: {'task' : 'save_profile','user_id' : <?php echo $user_id; ?>},
		waitMsg:GOlang['waitMsgSave'],
		success:function(form, action){
			//reload grid
			//users.getDataSource().reload();
			
			if(action.result.user_id)
			{
				user.setUserID(action.result.user_id);	
				Ext.MessageBox.alert(GOlang['strSuccess'], action.result.errors);			
			}
			
		},

		failure: function(form, action) {
			Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
		}
	});
}


Ext.onReady(function(){
	profileForm = new Ext.BasicForm('profile-form', {
		waitMsgTarget: 'box-bd'
	});
	
	var dob = new Ext.form.DateField({
        name: 'birthday',
        width:190,
        allowBlank:false,
        value: '<?php echo db_date_to_date($user['birthday']); ?>',
        format: '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>',
        width:200
    });
    dob.applyTo('birthday');

	user.destroyDialogButtons();
	var dialog = user.getDialog();

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


