<?php


namespace GO\Log\Controller;


class LogController extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO\Log\Model\Log';
	
	protected function allowGuests() {
		return array('rotate');
	}
	
	protected function getStoreParams($params) {
		
		return \GO\Base\Db\FindParams::newInstance()->export("log");
	}

	protected function actionRotate($params){
		
		$this->requireCli();
		
		$findParams = \GO\Base\Db\FindParams::newInstance();
		
		$findParams->getCriteria()->addCondition('ctime', \GO\Base\Util\Date::date_add(time(),-\GO::config()->log_max_days), '<');
		
		$stmt = \GO\Log\Model\Log::model()->find($findParams);
		
		$count = $stmt->rowCount();
		echo "Dumping ".$count." records to CSV file\n";
		if($count){
			$logPath = '/var/log/groupoffice/'.\GO::config()->id.'.csv';

			$csvLogFile = new \GO\Base\Fs\CsvFile($logPath);
			$csvLogFile->parent()->create();

			while($log = $stmt->fetch()){
				if(!$csvLogFile->putRecord(array_values($log->getAttributes('formatted'))))
					throw new \Exception("Could not write to CSV log file: ".$csvLogFile->path());

				$log->delete();
			}
		}
		
		echo "Done\n";
	}
}

