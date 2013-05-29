#!/usr/bin/php
<?php
$root = 'svn+ssh://svn@svn.intermesh.nl/groupoffice-pro/trunk/modules';
exec('svn ls '.$root, $output, $ret);

if($ret!=0)
	exit(var_dump($output));

$wd = dirname(__FILE__).'/www/modules';
chdir($wd);

foreach($output as $module){
	
	if(substr($module,-1)=='/'){ //check if it's a directory
				
		if(is_dir($module)){
			echo "UPDATE ".rtrim($module,'/')."\n";
		
			$cmd = 'svn up '.$module;
		}else
		{
			echo "CHECKOUT ".rtrim($module,'/')."\n";
			$cmd = 'svn co '.$root.'/'.$module;
		}	
		

		exec($cmd, $output, $ret);

		if($ret!=0)
			exit(var_dump($output));
	}
}

echo "Updating SF.net working copy\n";

chdir(dirname(__FILE__));
system("svn up");

echo "All done!\n";