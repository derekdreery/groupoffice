<?php
class GO_Site_Components_Router{

	/**
	 * @return string the route of the default controller, action or module. Defaults to 'sites/site'.
	 */
	public $defaultController = 'sites/site';
	
	/**
	 *
	 * @var GO_Site_Controller_Abstract
	 */
	private $_controller;
	
	/**
	 * Creates a controller instance based on a route.
	 * The route should contain the controller ID and the action ID.
	 * It may also contain additional GET variables. All these must be concatenated together with slashes.
	 *
	 * @param string $route the route of the request.
	 * @return array the controller instance and the action ID. Null if the controller class does not exist or the route is invalid.
	 */
	public function runController() {

		$aroute = explode('/', $this->getRoute());
		$module_id = $aroute[0];
		if (!isset($aroute[1]) || !isset($aroute[2])){
			$controller_id = 'front';
			$action_id = 'content';
			$module_id='site';
		}else
		{
			$controller_id = $aroute[1];
			$action_id = $aroute[2];
		}

		$className = 'GO_' . ucfirst($module_id) . '_Controller_' . ucfirst($controller_id); //TODO: set $module
		//$classFile = GO::config()->root_path . 'modules/' . $module_id . '/controller' . DIRECTORY_SEPARATOR . ucfirst($controller_id) . 'Controller.php';

		if (class_exists($className)) {
			//if (is_subclass_of($className, 'GO_Site_Components_AbstractFrontController')) {
						
			$action = $this->getControllerAction($action_id);
			$controller = new $className;
			$this->_controller = $controller;
			$controller->run($action, $_REQUEST);
			
		}else
		{
			//404
			echo "404 not found (".$className.")";
		}
	}
	
	public function getRoute() {
		$route = Site::urlManager()->parseUrl(Site::request());
	
		if (($route = trim($route, '/')) === '')
			$route = $this->defaultController;
		if (!Site::urlManager()->caseSensitive)
			$route = strtolower($route);
		return $route;
	}
	
	public function getController() {
		return $this->_controller;
	}
	
	/**
	 * Parses a path info into an action ID and GET variables.
	 * @param string $pathInfo path info
	 * @return string action ID
	 */
	protected function getControllerAction($pathInfo) {
		if (($pos = strpos($pathInfo, '/')) !== false) {
			$manager = Site::urlManager();
			$manager->parsePathInfo((string) substr($pathInfo, $pos + 1));
			$actionID = substr($pathInfo, 0, $pos);
			return $manager->caseSensitive ? $actionID : strtolower($actionID);
		}
		else
			return $pathInfo;
	}
}