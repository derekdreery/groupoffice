<?php
require('../../Group-Office.php');

load_basic_controls();
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('updateclient');



require($GO_MODULES->modules['updateclient']['class_path'].'updateclient.class.inc');

require($GO_LANGUAGE->get_language_file('updateclient'));


$GO_CONFIG->set_help_url($uc_help_url);

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';



$form = new form('updateclient_form');
$form->add_html_element(new input('hidden','task','',false));



$host = $GO_CONFIG->get_setting('updateclient_host');
$username = $GO_CONFIG->get_setting('updateclient_username');
$password = $GO_CONFIG->get_setting('updateclient_password');

if(empty($username))
{
	header('Location: settings.php');
	exit();
}


$uc= new updateclient($host, $username,$password);


if($task=='update')
{
	if(!is_dir($GO_CONFIG->file_storage_path.'updateclient'))
	{
		mkdir_recursive($GO_CONFIG->file_storage_path.'updateclient');
	}
	system($GO_CONFIG->cmd_sudo.' '.$GO_MODULES->path.'upgrade.php > '.$GO_CONFIG->file_storage_path.'updateclient/log.txt 2>&1 &');
	
	$form->add_html_element(new html_element('h1', 'Updating'));
	$form->add_html_element(new html_element('p','Please wait until the status screen reports the update is complete.'));
	$form->innerHTML .= '<h3>Status:</h3><iframe id="updater" src="status.php" style="width:500px;height:48px;"></iframe><br />';
		
	$form->add_html_element(new button($cmdContinue, "document.location='".$_SERVER['PHP_SELF']."';"));
	//$output = $uc->get_package($_POST['package_id']);	
	
}else {
	
	$menu = new button_menu();
	$menu->add_button('cms_settings','Settings','settings.php');
	$form->add_html_element($menu);
	
	
	$form->add_html_element(new html_element('h1',$lang_modules['updateclient']));
	
	
	$table = new table();
	$table->set_attribute('class','go_table');
	
	$th = new table_heading($strName);
	$th = new table_heading('Status');
	$th->set_attribute('colspan','99');
	$table->add_column($th);


	if(!$uc->check())
	{
		exit('Failed to connect to update server!');
	}
	
	if($uc->status=='401')
	{
		$p = new html_element('p','Wrong username or password');
		$p->set_attribute('class','Error');
		$form->add_html_element($p);
	}elseif(count($uc->packages))
	{
		$updates=false;
		for($i=0;$i<count($uc->packages);$i++)
		{
			$row = new table_row();
			$row->add_cell(new table_cell($uc->packages[$i]['name']));		
			
			if($uc->packages[$i]['date']>$uc->packages[$i]['local_date'])
			{
				$row->add_cell(new table_cell('Update available!'));		
				$updates=true;
			}else {
				$row->add_cell(new table_cell('No updates'));		
			}
				
			$table->add_row($row);			
		}
		$form->add_html_element($table);
		
		if($updates)
		{
			$button = new button('Update', 'javascript:update();');
			$form->add_html_element($button);
		}
	}else {
		$row = new table_row();
		$cell = new table_cell('No updates available');
		$row->add_cell($cell);
		$table->add_row($row);
		$form->add_html_element($table);
	}
}
require($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
?>
<script type="text/javascript">
function update()
{
	document.updateclient_form.task.value='update';
	document.updateclient_form.submit();
}

function loopstatus()
{
	while(true)
	{
		setTimeout(updateStatus,1000);
	}
}

function updateStatus()
{
	sUrl='status.php';
	request = YAHOO.util.Connect.asyncRequest('GET', sUrl, {success:handleSuccess,failure:handleFailure});	
	
	setTimeout(updateStatus,1000);
}
function startUpdater()
{
	sUrl = "upgrader.php?package_index=<?php if(isset($_POST['package_id'])) echo $_POST['package_id']; ?>";
	request = YAHOO.util.Connect.asyncRequest('GET', sUrl);
	//document.getElementById('updater').src="upgrader.php?package_index=<?php if(isset($_POST['package_id'])) echo $_POST['package_id']; ?>";
	
}
var handleFailure = function(o){
	if(o.responseText !== undefined){
		var div = document.getElementById('status');
		div.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
		div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
		div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
	}
}

var handleSuccess = function(o){
	document.getElementById('status').innerHTML = o.responseText;
}

</script>
<?php
require($GO_THEME->theme_path.'footer.inc');
?>
