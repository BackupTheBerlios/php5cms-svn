<?php
import('p5c.BaseObject');

/**
 * Base class for command implementations.
 *
 * @access   public
 * @abstract
 * @package  p5c
 * @author   Manuel Heße <manuel.hesse@xplib.de>
 * @version  $Revision$
 * @see      p5c_BaseObject
 */

abstract class p5c_Command extends p5c_BaseObject {
	
	
	// {{{ properties
	
	
	/**
	 * The view instance.
	 *
	 * @access protected
	 * @var    p5c_View
	 */
	protected $view = null;
	
	
	// }}}
	// {{{ init & uninit
	
	
	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
	} // end public function __construct()
	
	
	// }}}
	// {{{ interface access methods
	
	
	/**
	 * Returns the view to use or NULL.
	 *
	 * @access public
	 * @return p5c_View      The view or null
	 */
	public function getView() {
		return $this->view;
	} // endpublic function getView()
	
	
	// }}}
	// {{{ abstract execution method
	
	
	/**
	 * Executes the concrete p5c_Command instance.
	 *
	 * @access   public
	 * @abstract
	 * @param    p5c_http_Request
	 * @param    p5c_http_Response
	 */
	abstract function execute(p5c_http_Request $request, 
	                          p5c_http_Response $response);
	
	
	// }}}
	
	
} // end abstract class p5c_Command extends p5c_BaseObject
?>
