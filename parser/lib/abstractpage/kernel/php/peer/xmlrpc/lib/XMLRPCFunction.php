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


/**
 * Handles XML-RPC server functions.
 *
 * @package peer_xmlrpc_lib
 */

class XMLRPCFunction extends PEAR
{
	/**
	 * name of the XML-RPC function
	 * @access public
	 */
    var $Name;

	/**
	 * list of parameters and their type
	 * @access public
	 */
    var $ParameterList;  
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function XMLRPCFunction( $name, $parameters = 0 )
    {
        $this->Name =& $name;
        $this->ParameterList = array();
    }

	
	/**
	 * Returns the function name.
	 *
	 * @access public
	 */
    function name()
    {
        return $this->Name;
    }

	/**
	 * Adds a new parameter to the parameter list.
	 *
	 * @access public
	 */
    function addParameter( $param )
    {
        $this->ParameterList[] = $param;
    }

	/**
	 * Returns the parameter list.
	 *
	 * @access public
	 */
    function parameters()
    {
        return $this->ParameterList;
    }  
} // END OF XMLRPCFunction

?>
