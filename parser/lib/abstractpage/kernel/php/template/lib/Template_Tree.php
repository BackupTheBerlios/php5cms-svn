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
|Authors: Juan M. Casillas <assman@jmcresearch.com>                    |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Tree to hold the hierachical view of parsed tags.
 *
 * @package template_lib
 */

class Template_Tree extends PEAR
{
	/**
	 * @access public
	 */
    var $val;
	
	/**
	 * @access public
	 */
    var $childs;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function Template_Tree()
	{
		$this->childs = array();
    }

	
	/**
	 * @access public
	 */
    function addChild( $value )
	{
		$this->childs[$value] = new Template_Tree();
		$this->childs[$value]->val = $value;
    }

	/**
	 * @access public
	 */
    function lookupAndAdd( $parent, $val )
	{
		for ( $i = 0; $i < count( $this->childs ); $i++ ) 
		{
	    	$obj = &$this->childs[$i];

			if ( $obj->val == $parent ) 
			{
				$obj->addChild( $val );
				return;
			}
		}
		
		for ( $i = 0; $i < count( $this->childs ); $i++ ) 
		{
			$obj = &$this->childs[$i];
			
			if ( is_object( $obj ) )
				$obj->lookupAndAdd( $parent, $val );
		}
    }
} // END OF Template_Tree

?>
