<?php

class GO_Site_Controller_Front extends GO_Site_Components_Controller {
	protected function allowGuests() {
		return array('content','thumb','search','ajaxwidget', 'sitemap');
	}
	
	protected function actionContent($params){
		$content = empty($params['slug']) ? false : GO_Site_Model_Content::model()->findBySlug($params['slug']);
		
		if(!$content){
			$this->render('/site/404');
		}else{
			
			$this->setPageTitle($content->metaTitle);
			Site::scripts()->registerMetaTag($content->meta_description, 'description');
			Site::scripts()->registerMetaTag($content->meta_keywords, 'keywords');
			
			// Check if the template is not empty
			if(empty($content->template)) {
				$defaultTemplate = Site::config()->getDefaultTemplate();
				if(!empty($defaultTemplate))
					$content->template = $defaultTemplate;
			}
			
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
	
	/**
	 * Will select all content item from a website and pass them to the sitemap template
	 * @param array $params [empty]
	 */
	protected function actionSitemap($params) {
		$sitemap = GO_Site_Model_Content::getTreeNodes(2);
		
		$this->render('sitemap', array('sitemap'=>$sitemap));
	}
	
	/**
	 * This will copy a file in the files module to a public accessable folder
	 * 
	 * @param array $params
	 * - stromg src: path the the file relative the the sites public storage folder.
	 * @return the rsult of the thumb action on the core controller
	 * @throws GO_Base_Exception_AccessDenied when unable to create the folder?
	 */
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
	
	/**
	 * Post to this action to execute a function inside a widget
	 * Using an AJAX call this the controller action
	 * 
	 * @param array $params
	 * - string widget_class eg. 'GO_Site_Widget_Plupload_Widget'
	 * - string widget_method name of the widgets static method eg. 'upload'
	 * @throws Exception when not all required parameters are supplied
	 */
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