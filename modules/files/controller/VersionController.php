<?php

/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO\Files\Model\Template controller
 *
 * @package GO.modules.files
 * @version $Id: GO\Files\Model\Template.php 7607 2011-09-29 08:42:37Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

namespace GO\files\Controller;


class VersionController extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO\Files\Model\Version';

	protected function actionDownload($params){
		$version = \GO\Files\Model\Version::model()->findByPk($params['id']);
		$file = $version->getFilesystemFile();
	  \GO\Base\Util\Http::outputDownloadHeaders($file);		
		$file->output();
	}
	
	protected function getStoreParams($params) {		
		$findParams = \GO\Base\Db\FindParams::newInstance()->ignoreAcl();
		$findParams->getCriteria()->addCondition('file_id', $params['file_id']);		
		
		return $findParams;
	}
	protected function formatColumns(\GO\Base\Data\ColumnModel $columnModel) {
		
		$columnModel->formatColumn('user_name', '$model->user->name');
		
		return parent::formatColumns($columnModel);
	}
}