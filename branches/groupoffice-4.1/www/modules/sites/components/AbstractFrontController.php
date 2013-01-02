<?php

/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Abstract Controller class to be extenden bij controllers that are used for page rendering
 * Can be used for frontend views, cms module, sites module, or other module that need to render webpages
 *
 * @package GO.base.controller
 * @copyright Copyright Intermesh
 * @version $Id AbstractFrontController.php 2012-06-05 10:01:09 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
abstract class GO_Sites_Components_AbstractFrontController extends GO_Base_Controller_AbstractController
{
//	/**
//	 * Frontend action can be accessed without moduel access
//	 * @return array actions that can be accessed withou module access 
//	 */
//	protected function allowWithoutModuleAccess()
//	{
//		return array('*');
//	}
//	/**
//	 * By default allow guest to the frontend
//	 * Override again of pages requere login
//	 * @return array action that can be accessed as guest 
//	 */
//	protected function allowGuests() {
//		return array('*');
//	}
	
	/**
	 * @var string the name of the layout to be applied to this controller's views.
	 * Defaults to main, meaning no layout will be applied.
	 * This file will be found in the layouts folder of yout theme
	 */
	public $layout = 'main';

	/**
	 * The name of the template folder/
	 * This will be site bij de Website component when a site is loaded from the database.
	 * Not to be set manualy
	 * @var string
	 */
	public $template = 'default';

	/**
	 * The title of the page
	 * @var string
	 */
	private $_pageTitle;
	
	/**
	 * the name of the action that is running. Empty string if none
	 * @var string name of runned action 
	 */
	private $_action ='';
	
	protected $description="";

	public function getPageTitle()
	{
		if ($this->_pageTitle !== null)
			return $this->_pageTitle;
		else
		{
			return $this->_pageTitle = ucfirst($this->_action);
		}
	}

	/**
	 * Using scripts components to render css and javascripts
	 * @param string $output the html to be rendered
	 * @return string the html to be rendere with scripts 
	 */
	public function processOutput($output)
	{
		//output is passed as refference
		GOS::site()->scripts->render($output);

		return $output;
	}

	public function setPageTitle($val)
	{
		$this->_pageTitle = $val;
	}

	/**
	 * Render a view file with layout wrapped
	 * 
	 * @param string $view name of the view to be rendered
	 * @param array $data data tp be extracted om PHP variables
	 * @param boolean $return return rendering result if true
	 * @return string the redering result if $return is true 
	 */
	public function render($view, $data = null, $return = false)
	{
		$output = $this->renderPartial($view, $data, true);
		if (($layoutFile = $this->getLayoutFile($this->layout)) !== false)
			$output = $this->renderFile($layoutFile, array('content' => $output), true);

		$output = $this->processOutput($output); //use script component to register script files in views

		if ($return)
			return $output;
		else
			echo $output;
	}

	/**
	 * Renders a view file.
	 * @param string $view name of the view to be rendered
	 * @param array $data data to be extracted info PHP variables and made available to the view
	 * @param boolean $return return the rendered result instead of echoing
	 * @return type
	 * @throws CException 
	 */
	public function renderPartial($view, $data = null, $return = false)
	{
		if (($viewFile = $this->getViewFile($view)) !== false)
		{
			$output = $this->renderFile($viewFile, $data, true);
			if ($return)
				return $output;
			else
				echo $output;
		}
		else
			throw new GO_Base_Exception_NotFound('cannot find the requested view ' . $view);
	}

	/**
	 * This extracts the content of $_data_ the be used into the view file
	 * 
	 * @param string $_viewFile_ the path to the viewfile to be rendered
	 * @param array $_data_ contains the data to be used into the view
	 * @param boolean $_return_ true if the rendered contect should be returned
	 * @return string the rendered page 
	 */
	public function renderFile($_viewFile_, $_data_ = null, $_return_ = false)
	{
		// use special variable names here to avoid conflict when extracting data
		if (is_array($_data_))
			extract($_data_, EXTR_PREFIX_SAME, 'data');
		else
			$data = $_data_;
		if ($_return_)
		{
			ob_start();
			ob_implicit_flush(false);
			require($_viewFile_);
			return ob_get_clean();
		}
		else
			require($_viewFile_);
	}

	public function getTemplatePath()
	{
		return GO::config()->root_path . 'modules/sites/templates/' . $this->template . '/';
	}

	private function getViewPath($viewName = false)
	{
		if(isset($viewName) && (substr($viewName, 0, 2) == '//'))
			return $this->getTemplatePath() . 'views/';
		else
			return $this->getTemplatePath() . 'views/' . $this->getModule()->id . '/';
	}

	/**
	 * Returns to url of the template folder.
	 * Will search in root/{templatename}
	 * Can be used by Views for inserting css or js files from template folder
	 * @return string  url to template assets
	 * @throws GO_Base_Exception_NotFound when the template directory doesn't excists
	 */
	public function getTemplateUrl()
	{
		
		
		$template_url = $this->template."/";

		if(file_exists($template_url)) //look in root/[templatename]
			return GOS::site()->urlManager->getBaseUrl()."/". $template_url;
		else
			return GO::config()->host . 'modules/sites/templates/' . $this->template . '/assets/';
				
//		$template_url = GO::config()->host . 'modules/sites/templates/' . $this->template . '/assets/';
//		if(file_exists($template_url)) //look in sites module
//			return $template_url;

		throw new GO_Base_Exception_NotFound('Could not find the template directory '. $template_url);
	}

	/**
	 * Returns the path to the viewfile based on the used template and module
	 * It will search for a template first if not found look in the views/site/ folder
	 * the default viewfile provided by the module
	 * @param string $viewName name to the viewfile
	 * @return string path of the viewfile
	 */
	public function getViewFile($viewName)
	{	
		$theme_view_file = $this->getViewPath($viewName) . $viewName . '.php';
		if(file_exists($theme_view_file))
			return $theme_view_file;
		return GO::config()->root_path . 'modules/'. $this->getModule()->id . '/views/site/' . $viewName . '.php';
	}

	/**
	 * Returns the path to the layoutfile based on the used template and module
	 * @param string $layoutName name to the layoutfile
	 * @return string path of the layoutName
	 */
	public function getLayoutFile($layoutName)
	{
		return $this->getTemplatePath() . 'layouts/' . $layoutName . '.php';
	}

	/**
	 * Creates a relative URL for the specified action defined in this controller.
	 * 
	 * @param string $route the URL route. 
	 * @param array $params additional GET parameters (name=>value). Both the name and value will be URL-encoded.
	 * @return string the constructed URL
	 */
	public function createUrl($route, $params = array(), $relative = true)
	{

		if (!$relative)
			return GOS::site()->getRequest()->getHostInfo() . GOS::site()->getUrlManager()->createUrl($route, $params);
		return GOS::site()->getUrlManager()->createUrl($route, $params);
	}

	/**
	 * Redirect to another page.
	 * 
	 * @param mixed $url String or array with route and params.
	 * @param int $statusCode HTTP Status code
	 */
	protected function redirect($url = '', $statusCode = 302)
	{
		if (is_array($url))
		{
			$route = isset($url[0]) ? $url[0] : '';
			$url = $this->createUrl($route, array_splice($url, 1));
		}
		GOS::site()->getRequest()->redirect($url, true, $statusCode);
	}

	/**
	 * Get the url to return to from session when login failed. This is usually called after login in
	 * @return string the url
	 */
	public function getReturnUrl()
	{
		if (isset(GO::session()->values['sites']['returnUrl']))
		{
			$returnUrl = GO::session()->values['sites']['returnUrl'];
			//unset(GO::session ()->values['sites']['returnUrl']);
			return $returnUrl;
		}
		else
			return GOS::site()->getHomeUrl(); //Homepage
	}
	
	/**
	 * Checks if a user is logged in, if the user has permission to the module and if the user has access to a specific action.
	 * 
	 * @param string $action
	 * @return boolean boolean
	 */
	protected function _checkPermission($action){
		
		$allowGuests = $this->allowGuests();
		
		if(!in_array($action, $allowGuests) && !in_array('*', $allowGuests)){			
			//check for logged in user
			if(!GO::user())
				return false;			
		}
		
		$module = $this->getModule();
		return !$module || GO::modules()->isInstalled($module->id);
	}

	public function run($action = '', $params = array(), $render = true, $checkPermissions = true)
	{
		try
		{
			if (empty($action))
				$this->_action = $action = strtolower($this->defaultAction);
			else
				$this->_action = $action = strtolower($action);

			$ignoreAcl = in_array($action, $this->ignoreAclPermissions()) || in_array('*', $this->ignoreAclPermissions());
			if($ignoreAcl){		
				$oldIgnore = GO::setIgnoreAclPermissions(true);				
			}
			
			if (!$this->_checkPermission($action))
				throw new GO_Base_Exception_AccessDenied();

			$this->beforeAction();
			
			$methodName = 'action' . $action;
			$this->$methodName();
			
			//restore old value for acl permissions if this method was allowed for guests.
			if(isset($oldIgnore))
				GO::setIgnoreAclPermissions($oldIgnore);
		}
		catch (GO_Base_Exception_AccessDenied $e)
		{
			if(!GO::user()){
				//Path the page you tried to visit into lastPath session for redirecting after login
				GO::session()->values['sites']['returnUrl'] = GOS::site()->getRequest()->getRequestUri();
				$loginpath = !empty(GOS::site()->getSite()->login_url) ? GOS::site()->getSite()->login_url : '/sites/site/login';
				$this->redirect(array($loginpath));
			}  else {
				$controller = new GO_Sites_Controller_Site();
				$controller->template = $this->template;
				$controller->render('error', array('error' => $e));
			}
			//$this->render('error', array('error'=>$e));
		}
		catch (GO_Base_Exception_NotFound $e){
			header("HTTP/1.0 404 Not Found");
      header("Status: 404 Not Found");
			
			$controller = new GO_Sites_Controller_Site();
			$controller->template = $this->template;
			$controller->setPageTitle("404 Not found");
			$controller->render('404');
		}
		catch (Exception $e)
		{
			$controller = new GO_Sites_Controller_Site();
			$controller->template = $this->template;
			$controller->render('error', array('error' => $e));
		}
//		catch(Exception $e)
//		{
//			echo $e->getMessage();
//		}
	}
	
	/**
	 * override this methode to execute some code just before calling an action on the controller 
	 */
	protected function beforeAction()
	{
		
	}

}

?>
