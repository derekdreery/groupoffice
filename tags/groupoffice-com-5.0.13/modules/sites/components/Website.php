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
 * Website component with url manager
 *
 * @package GO.sites
 * @copyright Copyright Intermesh
 * @version $Id Website.php 2012-06-07 14:11:08 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 * 
 * @property GO_Sites_Components_Notifier $notifier notification object for success/error messages
 * @property GO_Sites_Components_AbstractFrontController $controller The current accessed controller
 * @property GO_Sites_Components_Request $request The request component.
 * @property GO_Sites_Components_UrlManager $urlManager The URL manager component.
 * @property GO_Sites_Components_Scripts $scripts Component for adden clientside scripts to the template
 * @property string $baseUrl The relative URL for the application.
 * @property string $route The homepage URL.
 * @property string $name Name of the website.
 * @property GO_Sites_Model_Site $site website active record object.
 */
class GO_Sites_Components_Website {

	/**
	 * @return string the route of the default controller, action or module. Defaults to 'sites/site'.
	 */
	public $defaultController = 'sites/site';

	/**
	 * @var mixed the application-wide layout. Defaults to 'main' (relative to {@link getLayoutPath layoutPath}).
	 * If this is false, then no layout will be used.
	 */
	public $layout = 'main';

	/**
	 *
	 * @var GO_Sites_Components_AbstractFrontController 
	 */
	private $_controller; //To current controllers that was called
	private $_request;

	/**
	 * The config object
	 * 
	 * @var GO_Sites_Components_Config 
	 */
	private $_siteconfig;
	private $_urlManager;
	private $_language;
	private $_scripts; // The clientscript manager
	private $_notifier; // Notification object for page messeages
	private $_assets; // Assets manager for published assets
	private $_route; // The route that the user inputted
	private $_site; //The site active record object
	private $_theme;

	public function __construct() {
		GOS::setSite($this);
		
		
		if(isset($_GET['site_id']))
			GO::session()->values['sites']['site_id']=$_GET['site_id'];
		
		if(!empty(GO::session()->values['sites']['site_id'])){
			$site = GO_Sites_Model_Site::model()->findByPk(GO::session()->values['sites']['site_id']);
		}else
		{
			// Find the website model from its domainname
			$site = GO_Sites_Model_Site::model()->findSingleByAttribute('domain', $_SERVER["SERVER_NAME"]);
		}
		
		if (!$site)
			throw new GO_Base_Exception_NotFound('Website not found in database');
		
		$this->_site = $site;

		// Set the language of GO itself to the same language as the website.
		GO::language()->setLanguage($this->_site->language);
	}

	/**
	 * Returns a property value
	 * @param string $name the property name
	 * @return mixed the property value
	 * @throws Exception if the property is not defined
	 */
	public function __get($name) {
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->$getter();
		throw new Exception('Property "' . get_class($this) . '.' . $name . '" is not defined.');
	}

	public function getId() {
		return $this->_site->id;
	}

	public function getSite() {
		return $this->_site;
	}

	public function getScripts() {
		if ($this->_scripts == null)
			$this->_scripts = new GO_Sites_Components_Scripts();
		return $this->_scripts;
	}

	public function getLoginUrl() {
		return $this->getUrlManager()->createUrl($this->_site->login_path);
	}

	public function getHomeUrl() {
		return $this->request->getBaseUrl() . '/';
	}

	public function getName() {
		return $this->_site->name;
	}

	public function getConfig() {
		return $this->_siteconfig;
	}

	public function getRoute() {
		return $this->_route;
	}

	public function run($siteconfig = array()) {
		$this->_siteconfig = new GO_Sites_Components_Config($siteconfig);
		$this->processRequest();
	}

	/**
	 * Processes the current request.
	 * It first resolves the request into controller and action,
	 * and then creates the controller to perform the action.
	 */
	public function processRequest() {
		$route = $this->getUrlManager()->parseUrl($this->getRequest());
		$this->runController($route);
	}

	/**
	 * Creates the controller and performs the specified action.
	 * @param string $route the route of the current request. See {@link createController} for more details.
	 * @throws GO_Base_Exception_NotFound if the controller could not be created.
	 */
	public function runController($route) {
		$this->_route = $route;
//		try{
		if (($ca = $this->createController($route)) !== null) {
			list($controller, $actionID) = $ca;
			$controller->template = $this->_site->template;
			$this->_controller = $controller;
			$controller->run($actionID, $_REQUEST);
		}
		else
			throw new GO_Base_Exception_NotFound('Unable to resolve the request "' . $route . '".');
//		} catch (Exception $e)
//		{
//			$controller = new GO_Sites_Controller_Site($this);
//			$controller->template = $this->_site->template;
//			$this->_controller = $controller;
//			$controller->render('error', array('error'=>$e));
//		}
	}

	/**
	 * Creates a controller instance based on a route.
	 * The route should contain the controller ID and the action ID.
	 * It may also contain additional GET variables. All these must be concatenated together with slashes.
	 *
	 * @param string $route the route of the request.
	 * @return array the controller instance and the action ID. Null if the controller class does not exist or the route is invalid.
	 */
	public function createController($route) {

		if (($route = trim($route, '/')) === '')
			$route = $this->defaultController;

		if (!$this->getUrlManager()->caseSensitive)
			$route = strtolower($route);

		$aroute = explode('/', $route);
		$module_id = $aroute[0];
		if (!isset($aroute[1]))
			throw new GO_Base_Exception_NotFound('No controller specified in url');
		$controller_id = $aroute[1];
		if (!isset($aroute[2]))
			throw new GO_Base_Exception_NotFound('No controller action specified in url');
		$action_id = $aroute[2];

		$className = 'GO_' . ucfirst($module_id) . '_Controller_' . ucfirst($controller_id); //TODO: set $module
		$classFile = GO::config()->root_path . 'modules/' . $module_id . '/controller' . DIRECTORY_SEPARATOR . ucfirst($controller_id) . 'Controller.php';

		if (is_file($classFile)) {
			//if (is_subclass_of($className, 'GO_Sites_Components_AbstractFrontController')) {
			return array(
					new $className($this),
					$this->parseActionParams($action_id),
			);
			//}
			//echo is_subclass_of($className, 'GO_Sites_Components_AbstractFrontController')  ? "is" : "not";
			return null;
		}
	}

	/**
	 * Parses a path info into an action ID and GET variables.
	 * @param string $pathInfo path info
	 * @return string action ID
	 */
	protected function parseActionParams($pathInfo) {
		if (($pos = strpos($pathInfo, '/')) !== false) {
			$manager = $this->getUrlManager();
			$manager->parsePathInfo((string) substr($pathInfo, $pos + 1));
			$actionID = substr($pathInfo, 0, $pos);
			return $manager->caseSensitive ? $actionID : strtolower($actionID);
		}
		else
			return $pathInfo;
	}

	/**
	 * 
	 * @return GO_Sites_Components_AbstractFrontController
	 */
	public function getController() {
		return $this->_controller;
	}

	public function getBasePath() {
		return GO::config()->root_path;
	}

	public function getUrlManager() {
		if ($this->_urlManager == null) {
			$this->_urlManager = new GO_Sites_Components_UrlManager();
			$this->_urlManager->rules = require($this->getBasePath() . 'modules/sites/templates/' . $this->_site->template . '/config/urls.php');
			$this->_urlManager->init();
		}
		return $this->_urlManager;
	}

	public function getAssets() {
		if ($this->_assets == null)
			$this->_assets = new GO_Sites_Components_Assets();
		return $this->_assets;
	}

	public function getNotifier() {
		if ($this->_notifier == null)
			$this->_notifier = new GO_Sites_Components_Notifier();
		return $this->_notifier;
	}

	public function getRequest() {
		if ($this->_request == null)
			$this->_request = new GO_Sites_Components_Request();
		return $this->_request;
	}

	public function getLanguage() {
		if ($this->_language == null)
			$this->_language = new GO_Sites_Components_Language($this->_site->language);
		return $this->_language;
	}

}

?>
