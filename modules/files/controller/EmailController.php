<?php
class GO_Files_Controller_Email extends GO_Base_Controller_AbstractJsonController {
	
	protected function actionCheckDeleteCron( $params ) {
		
		if (!GO::modules()->isInstalled('cron')) {
			echo json_encode(array('success'=>true,'data'=>array('enabled'=>false,'reason'=>'noCronModule')));
			exit();
		}
		
		$cronJob = GO_Base_Cron_CronJob::model()->findSingleByAttribute('job','GO_Files_Cron_DeleteExpiredLinks');
		
		if (!$cronJob) {
			echo json_encode(array('success'=>true,'data'=>array('enabled'=>false,'reason'=>'noCronJob')));
			exit();
		}
		
		echo json_encode(array('success'=>true,'data'=>array('enabled'=>$cronJob->active)));
		
	}
	
}