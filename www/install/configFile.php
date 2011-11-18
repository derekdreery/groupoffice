<?php

require('header.php');


$configFile= GO::config()->get_config_file();
if(!$configFile){
	
	//check ifconfig exists and if the config file is writable
	$config_location1 = '/etc/groupoffice/'.$_SERVER['SERVER_NAME'].'/config.php';
	//$config_location2 = dirname(substr($_SERVER['SCRIPT_FILENAME'], 0 ,-strlen($_SERVER['PHP_SELF']))).'/config.php';
	$config_location2 = GO::config()->root_path.'config.php';
	
	printHead();
	echo '<h1>Create config file</h1>';
	
	echo 'The configuration file does not exist. You must create an empty writable file at one of the following locations:<br />';
	echo '<ol><li>'.$config_location1.'</li><li>'.$config_location2.' (or any directory up that path)</li></ol>';
	echo 'Placing it outside of the web root is recommended because it\'s more secure. The sensitive information is kept outside the document root but it does require root privileges on this machine.<br />The second advantage is that you will be able to separate the source from the configuration. This can be very usefull with multiple installations on one machine.';
	echo '<br /><br />If you choose the first location then you have to make sure that in Apache\'s httpd.conf the following is set:<br /><br />';
	echo '<font color="#003399">';
	echo '<i>UseCanonicalName On</i></font><br />';
	echo 'This is to make sure it always finds your configuration file at the correct location.';
	echo '<br /><br /><font color="#003399">';
	echo '<i>$ touch config.php (Or FTP an empty config.php to the server)<br />';
	echo '$ chmod 666 config.php</i></font>';
	echo '<br /><br />If it does exist and you still see this message then it might be that safe_mode is enabled and the config.php is owned by another user then the '.GO::config()->product_name.' files.';
	
}elseif(!is_writable($configFile))
{
	printHead();
	echo '<h1>Config file not writable</h1>';
	echo '<input type="hidden" name="task" value="license" />';
	echo 'The configuration file \''.$configFile.'\' exists but is not writable. If you wish to make changes then you have to make \''.$configFile.'\' writable during the configuration process.';
	echo '<br /><br />Correct this and refresh this page.';
	echo '<br /><br /><font color="#003399"><i>$ chmod 666 '.$configFile.'<br /></i></font>';
}else
{
	try{
		if($_SERVER['REQUEST_METHOD']=='POST'){

				$f = new GO_Base_Fs_Folder($_POST['file_storage_path']);
				if(!$f->exists())
					throw new Exception("File storage folder doesn't exist. Please make sure it exists.");

				GO::config()->file_storage_path=$f->path().'/';

				$f = new GO_Base_Fs_Folder($_POST['tmpdir']);
				if(!$f->exists())
					throw new Exception("Temporary folder doesn't exist. Please make sure it exists.");

				GO::config()->tmpdir=$f->path().'/';
				GO::config()->save();
			
				redirect("database.php");
		}
					
	}
	catch(Exception $e){
		$error=$e->getMessage();
	}
	
	printHead();
	echo '<h1>File storage</h1>';
	if(isset($error)) errorMessage($error);
	
	?>
	<p>
	<?php echo GO::config()->product_name; ?> needs a place to store protected data. This folder should not be accessible through the webserver. Create a writable path for this purpose now and enter it in the box below.<br />
	The path should be have 0777 permissions or should be owned by the webserver user. You probably need to be root to do the last.
	<br /><br />
	<div class="cmd">
	$ su<br />
	$ mkdir /home/groupoffice<br />
	$ chown www-data:www-data /home/groupoffice<br />
	</div>
	</p>

	<?


	GO_Base_Html_Input::render(array(
			"label"=>"Protected files path",
			"name"=>"file_storage_path",
			"value"=>GO::config()->file_storage_path
	));
	?>
	<p>
	<?php echo GO::config()->product_name; ?> needs a place to store temporary data such as session data or file uploads. Create a writable path for this purpose now and enter it in the box below.<br />
		The /tmp directory is a good option.
	</p>
	<?php
	
	GO_Base_Html_Input::render(array(
			"label"=>"Temporary files path",
			"name"=>"tmpdir",
			"value"=>GO::config()->tmpdir
	));
}

continueButton();

printFoot();