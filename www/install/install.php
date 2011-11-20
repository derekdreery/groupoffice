<?php
require('header.php');

if($_SERVER['REQUEST_METHOD']=="POST"){
	
	if($_POST['password1']!=$_POST['password2'])
		GO_Base_Html_Input::setError ('password1', "The passwords didn't match");
	
	GO_Base_Html_Input::checkRequired();
	
	if(!GO_Base_Html_Input::hasErrors()){
		GO::$ignoreAclPermissions=true;

		GO_Base_Util_SQL::executeSqlFile('install.sql');

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
		
		$admin->save();

		$adminGroup->addUser($admin->id);

		redirect('finished.php');
	}
}

printHead();

?>
<h1>Installation</h1>
<p>
<?php echo GO::config()->product_name; ?> successfully connected to your database!<br />
Enter the administrator account details and click on 'Continue' to create the database for <?php echo GO::config()->product_name; ?>. This can take some time. Don't interrupt this process.
</p>
<?php
$stmt = GO::getDbConnection()->query("SHOW TABLES");
if($stmt->rowCount())
	errorMessage ("Database is not empty. Please use an empty database.");
else{
	?>
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