<?php

class GO_Log_Controller_Log extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Log_Model_Log';
	
	protected function allowGuests() {
		return array('rotate');
	}

	protected function actionRotate($params){
		
		if(!$this->isCli())
			throw new Exception("This action may only be ran on the command line");
		
		$findParams = GO_Base_Db_FindParams::newInstance();
		
		$findParams->getCriteria()->addCondition('ctime', GO_Base_Util_Date::date_add(time(),-14));
		
		$stmt = GO_Log_Model_Log::model()->find($findParams);
		
		if($stmt->rowCount()){
			$logPath = '/var/log/groupoffice/'.GO::config()->id.'.csv';

			$csvLogFile = new GO_Base_Fs_CsvFile($logPath);
			$csvLogFile->parent()->create();

			while($log = $stmt->fetch()){
				if(!$csvLogFile->putRecord(array_values($log->getAttributes('formatted'))))
					throw new Exception("Could not write to CSV log file: ".$csvLogFile->path());

				$log->delete();
			}
		}
	}
}

