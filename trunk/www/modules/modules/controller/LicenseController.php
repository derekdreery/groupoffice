<?php

namespace GO\Modules\Controller;

//use GO;
use GO\Base\Model\Module;
use GO\Base\Controller\AbstractJsonController;

use GO\Base\Data\DbStore;
use GO\Base\Data\ColumnModel;
use GO\Base\Db\FindParams;
//use GO\Base\Data\JsonResponse;



class LicenseController extends AbstractJsonController{
	/**
	 * Render JSON output that can be used by ExtJS GridPanel
	 * @param array $params the $_REQUEST params
	 */
	protected function actionUsers($module) {
		//Create ColumnModel from model
		$columnModel = new ColumnModel(Module::model());
		
		$columnModel->formatColumn('checked', '\GO\Professional\License::userHasModule($model->username, $module, true)', array('module'=>$module));
		
		$findParams = FindParams::newInstance()			
						->select('t.first_name,t.middle_name,t.last_name,t.username')
						->ignoreAcl()
						->limit(0);
						
		//Create store
		$store = new DbStore('GO\Base\Model\User', $columnModel, $_POST, $findParams);
		$store->defaultSort='username';
		$response = $this->renderStore($store);		
		
		$props = \GO\Professional\License::properties();
		
		$response['license_id']=isset($props['licenseid']) ? $props['licenseid'] : 0;
		$response['hostname']=$_SERVER['HTTP_HOST'];
		
		
		echo $response;
	}
}