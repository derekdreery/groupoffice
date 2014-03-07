<?php
namespace GO\Files\Controller;
use GO;
class Email extends GO\Base\Controller\AbstractJsonController {
	
	protected function actionCheckDeleteCron( $params ) {
		
		if (empty(GO::modules()->cron)) {
			echo json\encode(array('success'=>true,'data'=>array('enabled'=>false,'reason'=>'noCronModule')));
			exit();
		}
		
		$cronJob = GO\Base\Cron\CronJob::model()->findSingleByAttribute('job','GO\Files\Cron\DeleteExpiredLinks');
		
		if (!$cronJob) {
			echo json_encode(array('success'=>true,'data'=>array('enabled'=>false,'reason'=>'noCronJob')));
			exit();
		}
		
		echo json_encode(array('success'=>true,'data'=>array('enabled'=>$cronJob->active)));
		
	}
	
}