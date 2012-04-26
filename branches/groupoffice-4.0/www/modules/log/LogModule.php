<?php
class GO_Log_LogModule extends GO_Base_Module{
	/**
	 * Initialize the listeners for the ActiveRecords
	 */
	public static function initListeners(){	
		if(GO::modules()->isInstalled("servermanager")){
			$c = new GO_Servermanager_Controller_Installation();
			$c->addListener('report', 'GO_Log_LogModule', 'rotateLog');
		}
	}	
	
	public static function rotateLog(GO_ServerManager_Model_Installation $installation){
		
		echo "Running log rotate for ".$installation->name."\n";
		
		$cmd ='/usr/share/groupoffice/groupofficecli.php -r=log/log/rotate -c="'.$installation->configPath.'"  2>&1';		
		
		exec($cmd, $output, $return_var);

		if($return_var!=0)
			echo "Error: ".implode("\n", $output)."\n\n";
	}
}