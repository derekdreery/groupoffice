<?php
require('header.php');

$stmt = GO::getDbConnection()->query("SHOW TABLES");
$hasTables = $stmt->rowCount()>0;


if($_SERVER['REQUEST_METHOD']=="POST"){
	
	if(isset($_POST['upgrade'])){
		redirect('upgrade.php');
	}
	
	
	if($hasTables){
		trigger_error("Installation aborted because the database is not empty!", E_USER_ERROR);
		exit();
	}
	
	if($_POST['password1']!=$_POST['password2'])
		GO_Base_Html_Input::setError ('password1', "The passwords didn't match");
	
	GO_Base_Html_Error::checkRequired();
	
	if(!GO_Base_Html_Input::hasErrors()){
		GO::$ignoreAclPermissions=true;

		GO_Base_Util_SQL::executeSqlFile('install.sql');
		
		$dbVersion = GO_Base_Util_Common::countUpgradeQueries("updates.php");
		
		GO::config()->save_setting('version', $dbVersion);
		GO::config()->save_setting('upgrade_mtime', GO::config()->mtime);

		$adminGroup = new GO_Base_Model_Group();
		$adminGroup->id=1;
		$adminGroup->name = GO::t('group_admins');
		$adminGroup->save();

		$everyoneGroup = new GO_Base_Model_Group();
		$everyoneGroup->id=2;
		$everyoneGroup->name = GO::t('group_everyone');
		$everyoneGroup->save();

		$internalGroup = new GO_Base_Model_Group();
		$internalGroup->id=3;
		$internalGroup->name = GO::t('group_internal');
		$internalGroup->save();

		GO::config()->register_user_groups=GO::t('group_internal');
		GO::config()->register_visible_user_groups=GO::t('group_internal');

		$modules = GO::modules()->getAvailableModules();

		foreach($modules as $moduleClass){
			$moduleController = new $moduleClass;
			if($moduleController->autoInstall()){
				$module = new GO_Base_Model_Module();
				$module->id=$moduleController->id();
				$module->save();
			}
		}

		$admin = new GO_Base_Model_User();
		$admin->first_name = GO::t('system');
		$admin->last_name = GO::t('admin');
		$admin->username=$_POST['username'];
		$admin->password=$_POST['password1'];
		$admin->email=GO::config()->webmaster_email=$_POST['email'];
		
		GO::config()->save();
		
		//disable password validation
		GO::config()->password_validate=false;	
		
		$admin->save();

		$adminGroup->addUser($admin->id);
		
		$admin->checkDefaultModels();
		
		
		//module code here because we need the user and the module for this
		if(GO::modules()->files){
			$folder = GO_Files_Model_Folder::model()->findByPath('users/'.$admin->username.'/Public', true);
			$folder->visible=true;
			$acl = $folder->setNewAcl();
			$acl->addGroup(GO::config()->group_everyone, GO_Base_Model_Acl::DELETE_PERMISSION);
			$folder->save();
		}

		//Insert default cronjob record for email reminders
		$cron = new GO_Base_Cron_CronJob();
		
		$cron->name = 'Email Reminders';
		$cron->active = true;
		$cron->runonce = false;
		$cron->minutes = '0,5,10,15,20,25,30,35,40,45,50,55'; // Every 5 minutes
		$cron->hours = '*';
		$cron->monthdays = '*';
		$cron->months = '*';
		$cron->weekdays = '*';
		$cron->job = 'GO_Base_Cron_EmailReminders';
		
		$cron->save();
		
		$cron = new GO_Base_Cron_CronJob();
		
		$cron->name = 'Calculate disk usage';
		$cron->active = true;
		$cron->runonce = false;
		$cron->minutes = '0';
		$cron->hours = '0';
		$cron->monthdays = '*';
		$cron->months = '*';
		$cron->weekdays = '*';
		$cron->job = 'GO_Base_Cron_CalculateDiskUsage';		

		$cron->save();
		
		
		
		redirect('finished.php');
	}
}

printHead();

?>
<h1>Installation</h1>

<?php
if($hasTables){
	
	if(!GO_Base_Db_Utils::tableExists('go_users')){
		errorMessage("Your database is not empty and doesn't contain a valid ".GO::config()->product_name." database. Please use an empty database for a fresh install.");
	}else
	{
		?>
		<p><?php echo GO::config()->product_name; ?> successfully connected to your database!<br />
		A previous version has been detected. Press continue to perform an upgrade. <b>Warning:</b> This can take a long time! Make sure you press continue only once and check the browser loading status.</p>
		<input type="hidden" name="upgrade" value="1" />
		<?php
		continueButton();
	}
}else{
	?>
	<p>
	<?php echo GO::config()->product_name; ?> successfully connected to your database!<br />
	Enter the administrator account details and click on 'Continue' to create the database for <?php echo GO::config()->product_name; ?>. This can take some time. Don't interrupt this process.
	</p>
	<h2>Administrator</h2>
	<?php
	
	GO_Base_Html_Input::render(array(
		"label"=>"Username",
		"name"=>"username",
		"required"=>true
	));
	
	GO_Base_Html_Input::render(array(
		"label"=>"Password",
		"name"=>"password1",
		"required"=>true,
		"type"=>"password"
	));
	GO_Base_Html_Input::render(array(
		"label"=>"Confirm password",
		"name"=>"password2",
		"required"=>true,
		"type"=>"password"
	));
	
	GO_Base_Html_Input::render(array(
		"label"=>"Email",
		"name"=>"email",
		"required"=>true
	));
	
	continueButton();
}

?>
<?php


printFoot();