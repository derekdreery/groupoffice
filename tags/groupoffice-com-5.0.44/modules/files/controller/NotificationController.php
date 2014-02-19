<?php

class GO_files_Controller_Notification extends GO_Base_Controller_AbstractModelController {
    
    protected $model = 'GO_Files_Model_FolderNotification';
    
    protected function actionUnsent($params){
        GO_Files_Model_FolderNotification::model()->notifyUser();

        $response = array(
            'success' => true
        );

        return $response;
    }
}