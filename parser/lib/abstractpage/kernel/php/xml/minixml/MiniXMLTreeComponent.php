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
 * MiniXMLTreeComponent class 
 * This class is only to be used as a base class
 * for others.
 *
 * It presents the minimal interface we can expect
 * from any component in the XML hierarchy.
 *
 * All methods of this base class 
 * simply return NULL except a little default functionality
 * included in the parent() method.
 *
 * Warning: This class is not to be instatiated.
 * Derive and override.
 *
 * @package xml_minixml
 */

class MiniXMLTreeComponent extends PEAR
{	
	/**
	 * @access public
	 */
	var $xparent;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MiniXMLTreeComponent ()
	{
		$this->xparent = null;
	}
	
	
	/**
	 * Get set function for the element name.
	 *
	 * @access public
	 */
	function name( $setTo = null )
	{
		return null;
	}
	
	/**
	 * Function to fetch an element.
	 *
	 * @access public
	 */
	function &getElement( $name )
	{
		return null;
	}
	
	/**
	 * Function that returns the value of this 
	 * component and its children.
	 *
	 * @access public
	 */
	function getValue()
	{
		return null;
	}
	
	/**
	 * The parent() method is used to get/set the element's parent.
	 *
	 * If the NEWPARENT parameter is passed, sets the parent to NEWPARENT
	 * (NEWPARENT must be an instance of a class derived from MiniXMLTreeComponent)
	 *
	 * Returns a reference to the parent MiniXMLTreeComponent if set, NULL otherwise.
	 *
	 * @access public
	 */
	function &parent( &$setParent )
	{	
		if ( !is_null( $setParent ) )
		{
			// Parents can only be MiniXMLElement objects.
			if ( !method_exists( $setParent, 'MiniXMLTreeComponent' ) )
				return null;

			$this->xparent = $setParent;
		}
		
		return $this->xparent;		
	}
	
	/**
	 * Return a stringified version of the XML representing
	 * this component and all sub-components.
	 *
	 * @access public
	 */
	function toString( $depth = 0 )
	{
		return null;
	}

	/**
	 * Debugging aid, dump returns a nicely formatted dump of the current structure of the
	 * MiniXMLTreeComponent-derived object.
	 *
	 * @access public
	 */
	function dump()
	{
		return var_dump( $this );
	}
	
	
	// private methods
	

	/**
	 * @access private
	 */
	function _spaceStr( $numSpaces )
	{
		$retStr = '';
		
		if ( $numSpaces < 0 )
			return $retStr;
			
		for ( $i = 0; $i < $numSpaces; $i++ )
			$retStr .= ' ';
		
		return $retStr;
	}
} // END OF MiniXMLTreeComponent

?>
