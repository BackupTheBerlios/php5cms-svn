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
 * The NodeList provides an abstraction to an ordered collection of nodes. This 
 * class is used internally when retrieving all childs of a node.
 *
 * @package xml_dom_lib
 */
 
class NodeList extends PEAR
{
	/**
	 * @var		array		$nodes
	 * @access	private
	 */
	var $nodes = array();
	 
	 
	/** 
	 * the number of nodes in the list
	 * The number of nodes in the list. The range of valid child node indices 
	 * is 0 to length-1 inclusive.
	 *
	 * @return	int
	 * @access	public
	 */
	function getLength()
	{
		return sizeof( $this->nodes );
	}
	
	/** 
	 * Returns the indexth item in the collection.
	 * 
	 * Returns the indexth item in the collection. If index is greater than or 
	 * equal to the number of nodes in the list, this returns null.
	 * 
	 * @param	int			$index
	 * @return 	object 		Node
	 * @access	public
	 */
	function item( $index )
	{
		if ( ( $index >= 0 ) && ( $index < sizeof( $this->nodes ) ) )
			return $this->nodes[$index];
		
		return false;
	}
} // END OF NodeList

?>
