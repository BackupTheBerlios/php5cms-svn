<?php
import('p5c.BaseObject');

class p5c_http_Response extends p5c_BaseObject {
	
	
	// {{{ properties 
	
	
	/**
	 * GoF Singleton Pattern instance.
	 *
	 * @access private
	 * @static
	 * @var    p5c_http_Response
	 */
	private static $instance = null;
	
	
	// }}}
	// {{{ init & uninit
	
	
	/**
	 * GoF Singleton Pattern create method.
	 *
	 * @access public
	 * @static
	 * @final
	 * @return p5c_http_Response
	 */
	public static final function getInstance() {
		if (self::$instance == null) {
			self::$instance = new p5c_http_Response();
		}
		return self::$instance;
	} // end public static final function getInstance()
	
	
	/**
	 * Constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
	} // end protected function __construct() 
	
	
	// }}}
	
	
} // end class p5c_http_Response extends p5c_BaseObject
?>
