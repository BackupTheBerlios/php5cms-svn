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
 * Permission base class.
 *
 * @link http://java.sun.com/j2se/1.4.1/docs/guide/security/permissions.html
 * @package security
 */

class Permission extends PEAR
{
	/**
	 * @access public
	 */
    var $name = '';
	
	/**
	 * @access public
	 */
	var $actions = array();
      
	  
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function Permission( $name, $actions ) 
	{
      	$this->name    = $name;
      	$this->actions = $actions;
    }
    
	
    /**
     * Get this permission's name.
     *
     * @access  public
     * @return  string
     */
    function getName()
	{
      	return $this->name;
    }
    
    /**
     * Get this permission's actions.
     *
     * @access  public
     * @return  string[]
     */
    function getActions()
	{
      	return $this->actions;
    }
    
    /**
     * Create a string representation.
     *
     * @access  public
     * @return  string
     */
    function toString()
	{
      	return sprintf(
        	'permission %s: "%s", "%s";',
        	$this->getClassName(),
        	$this->name,
        	implode( ',', $this->actions )
      	);
    }
} // END OF Permission

?>
