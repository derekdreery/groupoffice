<?php

class GO_Site_Controller_Front extends GO_Site_Components_Controller {
	protected function allowGuests() {
		return array('content','thumb','search','ajaxwidget');
	}
	
	protected function actionContent($params){
		$content = empty($params['slug']) ? false : GO_Site_Model_Content::model()->findBySlug($params['slug']);
		
		if(!$content){
			$this->render('/site/404');
		}else{
			
			$this->setPageTitle($content->metaTitle);
			Site::scripts()->registerMetaTag($content->meta_description, 'description');
			Site::scripts()->registerMetaTag($content->meta_keywords, 'keywords');
			
			$this->render($content->template,array('content'=>$content));
		}
	}
	
	/**
	 * Search through the site content
	 * 
	 * @param array $params
	 * @throws Exception
	 */
	protected function actionSearch($params){
		
		if(!isset($params['searchString']))
			Throw new Exception('No searchstring provided');
		
		$searchString = $params['searchString'];
		
		
		$searchParams = GO_Base_Db_FindParams::newInstance()
						->select('*')
						->criteria(GO_Base_Db_FindCriteria::newInstance()
										->addSearchCondition('title', $searchString, false)
										->addSearchCondition('meta_title', $searchString, false)
										->addSearchCondition('meta_description', $searchString, false)
										->addSearchCondition('meta_keywords', $searchString, false)
										->addSearchCondition('content', $searchString, false)
							);
		
		$columnModel = new GO_Base_Data_ColumnModel();
		$store = new GO_Base_Data_DbStore('GO_Site_Model_Content',$columnModel,$params,$searchParams);
	
		$this->render('search', array('searchResults'=>$store));
	}
	
	
	
	protected function actionThumb($params){
			
		$rootFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'site/'.Site::model()->id);
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'site/'.Site::model()->id.'/'.$params['src']);
		$folder = $file->parent();
		
		$ok = $folder->isSubFolderOf($rootFolder);
		
		if(!$ok)
			Throw new GO_Base_Exception_AccessDenied();
		
		
		$c = new GO_Core_Controller_Core();
		return $c->run('thumb', $params, true, false);
	}
	
	
	protected function actionAjaxWidget($params){
		if(!isset($params['widget_class']))
			Throw new Exception ('Widget class not given.');
		
		if(!isset($params['widget_method']))
			Throw new Exception('Widget method not given.');
			
		$widgetClassName = $params['widget_class'];
		$widgetMethod = $params['widget_method'];
				
		$response = $widgetClassName::$widgetMethod($params);

		echo $response;
	}
	
	
}