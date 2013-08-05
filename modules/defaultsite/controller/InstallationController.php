<?php
class GO_Defaultsite_Controller_Installation extends GO_Base_Controller_AbstractJsonController {
	
	protected function actionInstallModules($params){

		$response = array(
			'success'=>true,
			'feedback'=>'' // Needed when an error occurs
		);

		$siteModule = new GO_Base_Model_Module();
		$siteModule->id='site';
		if(GO::modules()->isInstalled('site') || $siteModule->save()){
			$defaultSiteModule = new GO_Base_Model_Module();
			$defaultSiteModule->id='defaultsite';
			if(!$defaultSiteModule->save()){
				$response['success'] = false;
				$response['feedback'] = GO::t('installdefaultsiteerror','defaultsite');
			}
		} else {
			$response['success'] = false;
			$response['feedback'] = GO::t('installsiteerror','defaultsite');
		}

		echo $this->renderJson($response);
	}
}