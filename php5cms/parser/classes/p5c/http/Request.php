<?php
import('p5c.BaseObject');

class p5c_http_Request extends p5c_BaseObject {
	
	
	// {{{ properties 
	
	
	/**
	 * GoF Singleton Pattern instance.
	 *
	 * @access private
	 * @static
	 * @var    p5c_http_Request
	 */
	private static $instance = null;
	
	/**
	 * Used request method.
	 * 
	 * @access private
	 * @var    string
	 */
	private $method = 'GET';
	
	/**
	 * Request parameters.
	 *
	 * @access private
	 * @var    array
	 */
	private $parameters = array();
	
	
	// }}}
	// {{{ init & uninit
	
	
	/**
	 * GoF Singleton Pattern create method.
	 *
	 * @access public
	 * @static
	 * @final
	 * @return p5c_http_Request
	 */
	public static final function getInstance() {
		if (self::$instance == null) {
			self::$instance = new p5c_http_Request();
		}
		return self::$instance;
	} // end public static final function getInstance()
	
	
	/**
	 * Constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		
		$params = array_merge($_GET, $_POST);
		
		if (get_magic_quotes_gpc()) {
			foreach ($params as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $subKey => $subValue) {
						$params[$key][$subKey] = stripslashes($subValue);
					}
				} else {
					$params[$key] = stripslashes($value);
				}
			}
		}
		
		if (isset($params['action']) && is_array($params['action'])) {

			foreach ($params['action'] as $key => $value) {
				if ($key == 'x' || $key == 'y' || is_numeric($key)) {
					continue;
				}
					
				if (preg_match('#^([^:]+):([^:]+)$#', $key, $match)) {
					$params['action']       = $match[1];
					$params['action_param'] = $match[2];
				} else {
					$params['action'] = $key;
				}
				break;
			}
		}
		
		$this->parameters = $params;
		
		if (isset($_SERVER['REQUEST_METHOD'])) {
			$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
		} else if (isset($_ENV['REQUEST_METHOD'])) {
			$this->method = strtoupper($_ENV['REQUEST_METHOD']);
		} else if (isset($_POST) && !empty($_POST)) {
			$this->method = 'POST';
		}
	} // end protected function __construct() 
	
	
	// }}}
	// {{{ request parameter access methods
	
	
	/**
	 * Returns the request parameter for the given name or NULL.
	 *
	 * @access public
	 * @param  string      parameter name
	 * @return mixed       value or NULL
	 */
	public function getParameter($name) {
		if (isset($this->parameters[$name])) {
			return $this->parameters[$name];
		}
		return null;
	} // end public function getParameter($name)
	
	
	// }}}
	
	
} // end class p5c_http_Request extends p5c_BaseObject
?>
