<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'io.config.deep.DeepIni');


/**
 * @package io_config_deep
 */
 
class AbstractPageLicense extends DeepIni
{
	/**
	 * @access private
	 */
	var $_product;
	
	/**
	 * @access private
	 */
	var $_vendor;
	
	/**
	 * @access private
	 */
	var $_version;
	
	/**
	 * @access private
	 */
	var $_build;
	
	/**
	 * @access private
	 */
	var $_serial;

	/**
	 * @access private
	 */	
	var $_modules = array();
	
	/**
	 * @access private
	 */
	var $_applications = array();
	
	
	/**
	 * @access public
	 */
	function read( $filepath = "" )
	{
		$this->parseFile( $filepath );
		
		$pdoc  = $this->root();
		$entry = $pdoc->children[0];
		
		foreach ( $entry->children() as $c )
		{
			if ( strtolower( get_class( $c ) ) == "deepinixmlelement" )
			{
				$cname = strtolower( $c->name );
					
				switch ( $cname )
				{
					case 'product':
						$this->_product = $c->content;
						break;
						
					case 'vendor':
						$this->_vendor  = $c->content;
						break;
						
					case 'version':
						$this->_version = $c->content;
						break;
						
					case 'build':
						$this->_build   = $c->content;
						break;
						
					case 'serial':
						$this->_serial  = $c->content;
						break;
						
					case 'modules':
						break;
						
					case 'applications':
						break;
				}
			}
		}
	}
	
	/**
	 * @access public
	 */
	function getProduct()
	{
		return $this->_product;	
	}
	
	/**
	 * @access public
	 */
	function getVendor()
	{
		return $this->_vendor;	
	}

	/**
	 * @access public
	 */	
	function getVersion()
	{
		return $this->_version;	
	}
	
	/**
	 * @access public
	 */
	function getBuild()
	{
		return $this->_build;	
	}
	
	/**
	 * @access public
	 */
	function getSerial()
	{
		return $this->_serial;	
	}
} // END OF AbstractPageLicense

?>
