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
 * The GO_Tools_Controller_Tools controller
 *
 * @package GO.modules.Tools
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */

class GO_Tools_Controller_Tools extends GO_Base_Controller_AbstractJsonController{
	
	public function actionStore($params){
	
		$columnModel = new GO_Base_Data_ColumnModel(false,array(),array('name','script'));
		
		$store = new GO_Base_Data_ArrayStore($columnModel);

		$store->addRecord(array('name'=>GO::t('dbcheck','tools'),'script'=>GO::url('maintenance/checkDatabase')));
		$store->addRecord(array('name'=>GO::t('buildsearchcache','tools'),'script'=>GO::url('maintenance/buildSearchCache')));
		$store->addRecord(array('name'=>GO::t('rm_duplicates','tools'),'script'=>GO::url('maintenance/removeDuplicates')));
		
		if(GO::modules()->files)
			$store->addRecord(array('name'=>'Sync filesystem with files database','script'=>GO::url('files/folder/syncFilesystem')));
		
		if(GO::modules()->filesearch)
			$store->addRecord(array('name'=>'Update filesearch index','script'=>GO::url('filesearch/filesearch/sync')));

		echo $this->renderStore($store);
	}
	
}