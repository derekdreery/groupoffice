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
 * Manages URL rewrite rules for SEO shiny url's
 *
 * @package GO.sites
 * @copyright Copyright Intermesh
 * @version $Id UrlManager.php 2012-06-06 15:23:04 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Sites_Components_UrlManager
{

	public $rules;
	public $useStrictParsing = false;
	public $routeVar = 'r';
	public $caseSensitive = false;
	public $urlSuffix = '';
	public $appendParams = true; //true when get params need to be seperated by slashes in url
	public $matchValue = false;
	private $_urlFormat = 'path';
	private $_rules = array(); //URL rules
	private $_baseUrl;

	const GET_FORMAT = 'get';

	/**
	 * Initializes the application component.
	 */
	public function init()
	{
		//	parent::init();
		$this->processRules();
	}

	/**
	 * Processes the URL rules.
	 *  TODO: cache these url rules into memory
	 */
	protected function processRules()
	{
		if (empty($this->rules) || $this->_urlFormat === self::GET_FORMAT)
			return;

		foreach ($this->rules as $pattern => $route)
			$this->_rules[] = new CUrlRule($route, $pattern);
	}

	/**
	 * Constructs a URL.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters (name=>value). Both the name and value will be URL-encoded.
	 * If the name is '#', the corresponding value will be treated as an anchor
	 * and will be appended at the end of the URL.
	 * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
	 * @return string the constructed URL
	 */
	public function createUrl($route, $params = array(), $ampersand = '&')
	{
		unset($params[$this->routeVar]);
		foreach ($params as $i => $param)
			if ($param === null)
				$params[$i] = '';

		if (isset($params['#']))
		{
			$anchor = '#' . $params['#'];
			unset($params['#']);
		}
		else
			$anchor = '';
		$route = trim($route, '/');

		foreach ($this->_rules as $i => $rule)
		{

			if (($url = $rule->createUrl($this, $route, $params, $ampersand)) !== false)
			{
				if ($rule->hasHostInfo)
					return $url === '' ? '/' . $anchor : $url . $anchor;
				else
					return $this->getBaseUrl() . '/' . $url . $anchor;
			}
		}
		return $this->createUrlDefault($route, $params, $ampersand) . $anchor;
	}

	/**
	 * Creates a URL based on default settings.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @return string the constructed URL
	 */
	protected function createUrlDefault($route, $params, $ampersand)
	{
		$url = rtrim($this->getBaseUrl() . '/' . $route, '/');
		if ($this->appendParams)
		{
			$url = rtrim($url . '/' . $this->createPathInfo($params, '/', '/'), '/');
			return $route === '' ? $url : $url . $this->urlSuffix;
		}
		else
		{
			if ($route !== '')
				$url.=$this->urlSuffix;
			$query = $this->createPathInfo($params, '=', $ampersand);
			return $query === '' ? $url : $url . '?' . $query;
		}
	}

	/**
	 * Returns the base URL of the application.
	 * @return string the base URL of the application (the part after host name and before query string).
	 * If {@link showScriptName} is true, it will include the script name part.
	 * Otherwise, it will not, and the ending slashes are stripped off.
	 */
	public function getBaseUrl()
	{
		if ($this->_baseUrl !== null)
			return $this->_baseUrl;
		else
		{
			$this->_baseUrl = GOS::site()->getRequest()->getBaseUrl();
			return $this->_baseUrl;
		}
	}

	/**
	 * Parses the user request.
	 * @param GO_Sites_Components_Request $request the request component
	 * @return string the route (controllerID/actionID) and perhaps GET parameters in path format.
	 */
	public function parseUrl($request)
	{
		$rawPathInfo = $request->getPathInfo();
		$pathInfo = $this->removeUrlSuffix($rawPathInfo, $this->urlSuffix);
		
		foreach ($this->_rules as $i => $rule)
		{
			if (($r = $rule->parseUrl($this, $request, $pathInfo, $rawPathInfo)) !== false)
				return isset($_GET[$this->routeVar]) ? $_GET[$this->routeVar] : $r;
		}
		$par = explode("/", $pathInfo, 4);
		if(isset($par[3]))
			$this->parsePathInfo($par[3]);
		return $pathInfo;
	}

	/**
	 * Removes the URL suffix from path info.
	 * @param string $pathInfo path info part in the URL
	 * @param string $urlSuffix the URL suffix to be removed
	 * @return string path info with URL suffix removed.
	 */
	public function removeUrlSuffix($pathInfo, $urlSuffix)
	{
		if ($urlSuffix !== '' && substr($pathInfo, -strlen($urlSuffix)) === $urlSuffix)
			return substr($pathInfo, 0, -strlen($urlSuffix));
		else
			return $pathInfo;
	}

	/**
	 * Creates a path info based on the given parameters.
	 * @param array $params list of GET parameters
	 * @param string $equal the separator between name and value
	 * @param string $ampersand the separator between name-value pairs
	 * @param string $key this is used internally.
	 * @return string the created path info
	 */
	public function createPathInfo($params, $equal, $ampersand, $key = null)
	{
		$pairs = array();
		foreach ($params as $k => $v)
		{
			if ($key !== null)
				$k = $key . '[' . $k . ']';

			if (is_array($v))
				$pairs[] = $this->createPathInfo($v, $equal, $ampersand, $k);
			else
				$pairs[] = urlencode($k) . $equal . urlencode($v);
		}
		return implode($ampersand, $pairs);
	}

	public function parsePathInfo($pathInfo)
	{
		if ($pathInfo === '')
			return;
		$segs = explode('/', $pathInfo . '/');
		$n = count($segs);
		for ($i = 0; $i < $n - 1; $i+=2)
		{
			$key = $segs[$i];
			if ($key === '')
				continue;
			$value = $segs[$i + 1];
			if (($pos = strpos($key, '[')) !== false && ($m = preg_match_all('/\[(.*?)\]/', $key, $matches)) > 0)
			{
				$name = substr($key, 0, $pos);
				for ($j = $m - 1; $j >= 0; --$j)
				{
					if ($matches[1][$j] === '')
						$value = array($value);
					else
						$value = array($matches[1][$j] => $value);
				}
				if (isset($_GET[$name]) && is_array($_GET[$name]))
					$value = self::mergeArray($_GET[$name], $value);
				$_REQUEST[$name] = $_GET[$name] = $value;
			}
			else
				$_REQUEST[$key] = $_GET[$key] = $value;
		}
	}

	/**
	 * Merge 2 arrays recusifly. if the same keys accur overwrite.
	 * TODO: place this in a helper class
	 */
	public static function mergeArray($a, $b)
	{
		$args = func_get_args();
		$res = array_shift($args);
		while (!empty($args))
		{
			$next = array_shift($args);
			foreach ($next as $k => $v)
			{
				if (is_integer($k))
					isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
				else if (is_array($v) && isset($res[$k]) && is_array($res[$k]))
					$res[$k] = self::mergeArray($res[$k], $v);
				else
					$res[$k] = $v;
			}
		}
		return $res;
	}

}

class CUrlRule
{

	/**
	 * @var string the URL suffix used for this rule.
	 * For example, ".html" can be used so that the URL looks like pointing to a static HTML page.
	 * Defaults to null, meaning using the value of {@link CUrlManager::urlSuffix}.
	 */
	public $urlSuffix;

	/**
	 * @var boolean whether the rule is case sensitive. Defaults to null, meaning
	 * using the value of {@link CUrlManager::caseSensitive}.
	 */
	public $caseSensitive;

	/**
	 * @var array the default GET parameters (name=>value) that this rule provides.
	 * When this rule is used to parse the incoming request, the values declared in this property
	 * will be injected into $_GET.
	 */
	public $defaultParams = array();

	/**
	 * @var boolean whether the GET parameter values should match the corresponding
	 * sub-patterns in the rule when creating a URL. Defaults to null, meaning using the value
	 * of {@link CUrlManager::matchValue}. When this property is false, it means
	 * a rule will be used for creating a URL if its route and parameter names match the given ones.
	 * If this property is set true, then the given parameter values must also match the corresponding
	 * parameter sub-patterns. Note that setting this property to true will degrade performance.
	 * @since 1.1.0
	 */
	public $matchValue;

	/**
	 * @var string the HTTP verb (e.g. GET, POST, DELETE) that this rule should match.
	 * If this rule can match multiple verbs, please separate them with commas.
	 * If this property is not set, the rule can match any verb.
	 * Note that this property is only used when parsing a request. It is ignored for URL creation.
	 * @since 1.1.7
	 */
	public $verb;

	/**
	 * @var boolean whether this rule is only used for request parsing.
	 * Defaults to false, meaning the rule is used for both URL parsing and creation.
	 * @since 1.1.7
	 */
	public $parsingOnly = false;

	/**
	 * @var string the controller/action pair
	 */
	public $route;

	/**
	 * @var array the mapping from route param name to token name (e.g. _r1=><1>)
	 */
	public $references = array();

	/**
	 * @var string the pattern used to match route
	 */
	public $routePattern;

	/**
	 * @var string regular expression used to parse a URL
	 */
	public $pattern;

	/**
	 * @var string template used to construct a URL
	 */
	public $template;

	/**
	 * @var array list of parameters (name=>regular expression)
	 */
	public $params = array();

	/**
	 * @var boolean whether the URL allows additional parameters at the end of the path info.
	 */
	public $append;

	/**
	 * @var boolean whether host info should be considered for this rule
	 */
	public $hasHostInfo;

	/**
	 * Constructor.
	 * @param string $route the route of the URL (controller/action)
	 * @param string $pattern the pattern for matching the URL
	 */
	public function __construct($route, $pattern)
	{
		$this->route = trim($route, '/');

		$tr2['/'] = $tr['/'] = '\\/';

		if (strpos($route, '<') !== false && preg_match_all('/<(\w+)>/', $route, $matches2))
		{
			foreach ($matches2[1] as $name)
				$this->references[$name] = "<$name>";
		}

		$this->hasHostInfo = !strncasecmp($pattern, 'http://', 7) || !strncasecmp($pattern, 'https://', 8);

		if ($this->verb !== null)
			$this->verb = preg_split('/[\s,]+/', strtoupper($this->verb), -1, PREG_SPLIT_NO_EMPTY);

		if (preg_match_all('/<(\w+):?(.*?)?>/', $pattern, $matches))
		{
			$tokens = array_combine($matches[1], $matches[2]);
			foreach ($tokens as $name => $value)
			{
				if ($value === '')
					$value = '[^\/]+';
				$tr["<$name>"] = "(?P<$name>$value)";
				if (isset($this->references[$name]))
					$tr2["<$name>"] = $tr["<$name>"];
				else
					$this->params[$name] = $value;
			}
		}
		$p = rtrim($pattern, '*');
		$this->append = $p !== $pattern;
		$p = trim($p, '/');
		$this->template = preg_replace('/<(\w+):?.*?>/', '<$1>', $p);
		$this->pattern = '/^' . strtr($this->template, $tr) . '\/';
		if ($this->append)
			$this->pattern.='/u';
		else
			$this->pattern.='$/u';

		if ($this->references !== array())
			$this->routePattern = '/^' . strtr($this->route, $tr2) . '$/u';
	}

	/**
	 * Creates a URL based on this rule.
	 * @param CUrlManager $manager the manager
	 * @param string $route the route
	 * @param array $params list of parameters
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @return mixed the constructed URL or false on error
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		if ($this->parsingOnly)
			return false;

		if ($manager->caseSensitive && $this->caseSensitive === null || $this->caseSensitive)
			$case = '';
		else
			$case = 'i';

		$tr = array();
		if ($route !== $this->route)
		{
			if ($this->routePattern !== null && preg_match($this->routePattern . $case, $route, $matches))
			{
				foreach ($this->references as $key => $name)
					$tr[$name] = $matches[$key];
			}
			else
				return false;
		}

		foreach ($this->defaultParams as $key => $value)
		{
			if (isset($params[$key]))
			{
				if ($params[$key] == $value)
					unset($params[$key]);
				else
					return false;
			}
		}

		foreach ($this->params as $key => $value)
			if (!isset($params[$key]))
				return false;

		if ($manager->matchValue && $this->matchValue === null || $this->matchValue)
		{
			foreach ($this->params as $key => $value)
			{
				if (!preg_match('/' . $value . '/' . $case, $params[$key]))
					return false;
			}
		}

		foreach ($this->params as $key => $value)
		{
			$tr["<$key>"] = urlencode($params[$key]);
			unset($params[$key]);
		}

		$suffix = $this->urlSuffix === null ? $manager->urlSuffix : $this->urlSuffix;

		$url = strtr($this->template, $tr);

		if ($this->hasHostInfo)
		{
			$hostInfo = Yii::app()->getRequest()->getHostInfo();
			if (stripos($url, $hostInfo) === 0)
				$url = substr($url, strlen($hostInfo));
		}

		if (empty($params))
			return $url !== '' ? $url . $suffix : $url;

		if ($this->append)
			$url.='/' . $manager->createPathInfo($params, '/', '/') . $suffix;
		else
		{
			if ($url !== '')
				$url.=$suffix;
			$url.='?' . $manager->createPathInfo($params, '=', $ampersand);
		}

		return $url;
	}

	/**
	 * Parses a URL based on this rule.
	 * @param UrlManager $manager the URL manager
	 * @param HttpRequest $request the request object
	 * @param string $pathInfo path info part of the URL
	 * @param string $rawPathInfo path info that contains the potential URL suffix
	 * @return mixed the route that consists of the controller ID and action ID or false on error
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if ($this->verb !== null && !in_array($request->getRequestType(), $this->verb, true))
			return false;

		if ($manager->caseSensitive && $this->caseSensitive === null || $this->caseSensitive)
			$case = '';
		else
			$case = 'i';

		if ($this->urlSuffix !== null)
			$pathInfo = $manager->removeUrlSuffix($rawPathInfo, $this->urlSuffix);

		// URL suffix required, but not found in the requested URL
		if ($manager->useStrictParsing && $pathInfo === $rawPathInfo)
		{
			$urlSuffix = $this->urlSuffix === null ? $manager->urlSuffix : $this->urlSuffix;
			if ($urlSuffix != '' && $urlSuffix !== '/')
				return false;
		}

		if ($this->hasHostInfo)
			$pathInfo = strtolower($request->getHostInfo()) . rtrim('/' . $pathInfo, '/');

		$pathInfo.='/';

		if (preg_match($this->pattern . $case, $pathInfo, $matches))
		{
			foreach ($this->defaultParams as $name => $value)
			{
				if (!isset($_GET[$name]))
					$_REQUEST[$name] = $_GET[$name] = $value;
			}
			$tr = array();
			foreach ($matches as $key => $value)
			{
				if (isset($this->references[$key]))
					$tr[$this->references[$key]] = $value;
				else if (isset($this->params[$key]))
					$_REQUEST[$key] = $_GET[$key] = $value;
			}
			if ($pathInfo !== $matches[0]) // there're additional GET params
				$manager->parsePathInfo(ltrim(substr($pathInfo, strlen($matches[0])), '/'));
			if ($this->routePattern !== null)
				return strtr($this->route, $tr);
			else
				return $this->route;
		}
		else
			return false;
	}

}

?>
