<?php
import('p5c.BaseObject');

/**
 *
 * @access  public
 * @package p5c
 * @author  Manuel Heße <manuel.hesse@xplib.de>
 * @version $Revision$
 * @see     p5c_BaseObject
 */

class p5c_Config extends p5c_BaseObject {
	
	
	// {{{ properties
	
	
	/**
	 * GoF Singleton Pattern instance.
	 *
	 * @access private
	 * @static
	 * @var    p5c_Config
	 */
	private static $instance = null;
	
	/**
	 * Configuration properties.
	 *
	 * @access private
	 * @var    array
	 */
	private $properties = array();
	
	
	// }}}
	// {{{ init & uninit
	
	
	/**
	 * GoF Singleton Pattern creation method.
	 *
	 * @access public
	 * @static
	 * @final
	 * @return p5c_Config
	 */
	public static final function getInstance() {
		if (self::$instance == null) {
			self::$instance = new p5c_Config();
		}
		return self::$instance;
	} // end public static final function getInstance()
	
	
	/**
	 * Constructor
	 *
	 * @access protected
	 */
	protected function __construct() {
		
		$parserDir = str_replace('/usr/devel/var/src/web', '', realpath(dirname(__FILE__) . '/../../../'));
		
		$this->properties = array(
			
			'system.docroot' => '/var/www/html',
		
			'filemanager.directory'  => $parserDir,
			'filemanager.texttypes' => array(
				'css', 'htm', 'html', 'php', 'tpl', 'txt', 'xhtml', 'xml', 'xsl'
			),
			'filemanager.mediatypes' => array(
				'png', 'gif', 'jpg', 'jpeg', 'mp3', 'mp2', 'mp1', 'mpg', 'mpeg', 
				'asf', 'avi' 
			)
		);
	} // end protected function __construct()
	
	
	// }}}
	// {{{ propert getter and setter methods
	
	
	/**
	 * Returns the property for the given name or NULL.
	 *
	 * @access public
	 * @param  string      property name
	 * @return mixed       value or NULL.
	 */
	public function getProperty($name) {
		
		if ($name == 'parser.template.path') {
			return realpath(dirname(__FILE__) . '/../../template') . '/';
		} else if ($name == 'parser.template.cache') {
			return realpath(dirname(__FILE__) . '/../../cache/template') . '/';
		}
		
		if (isset($this->properties[$name])) {
			return $this->properties[$name];
		}
		return null;
	} // end public function getProperty($name)
	
	
	// }}}
	
	
} // end class p5c_Config extends p5c_BaseObject
?>
