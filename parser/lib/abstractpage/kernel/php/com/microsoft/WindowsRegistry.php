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


using( 'com.microsoft.COMObject' );


define( "HKCR", "HKEY_CLASSES_ROOT",   true );
define( "HKCU", "HKEY_CURRENT_USER",   true );
define( "HKLM", "HKEY_LOCAL_MACHINE",  true );
define( "HKU",  "HKEY_USERS",		   true );
define( "HKCC", "HKEY_CURRENT_CONFIG", true );


/**
 * Class for accessing Windows Registry.
 *
 * @package com_microsoft
 */
 
class WindowsRegistry extends PEAR
{
	/**
	 * @access private
	 */
	var $_shell;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function WindowsRegistry()
	{
		$this->_shell = &new COMObject( 'WScript.Shell' );
	}


	/**
	 * Read key.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function read( $key )
	{
		if ( !$this->keyExists( $key ) )
			return PEAR::raiseError( "The key $key doesn't exist." );
		else 
			return $this->_shell->RegRead( $key );
	}

	/**
	 * Write key.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @access public
	 */
	function write( $key, $value )
	{
		if ( !$this->keyExists( $key ) )
			return PEAR::raiseError( "The key $key doesn't exist." );
		else 
			return $this->_shell->RegWrite( $key, $value );
	}

	/**
	 * Delete key.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function delete( $key )
	{
		if ( !$this->keyExists( $key ) )
			return PEAR::raiseError( "The key $key doesn't exist." );
		else 
			return $this->_shell->RegDelete( $key );
	}

	/**
	 * Check if key exists.
	 *
	 * @access public
	 */
	function keyExists( $key )
	{
		return ( @$this->_shell->RegRead( $key ) != null );
	}
} // END OF WindowsRegistry

?>
