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
 * @package image_vrml_lib
 */
 
class VRMLNode extends PEAR
{
	var $NL;
	
	var $attributes = array();
	var $node_name  = "";
	var $sub_nodes  = array();
	
	
	/**
	 * Constructor
	 */
	function VRMLNode()
	{
		$this->sub_nodes = array();
		$this->NL = "\n";
	}
	
	
	function addNode( $p_node )
	{
		array_push( $this->sub_nodes, $p_node );
	}
	
	function getNode()
	{
		$page = $this->NL . $this->node_name . "{";
		
		foreach ( $this->attributes as $name => $value )
		{
			if ( is_array( $value ) )
			{
				$page .= $this->NL . $name . " [";
				
				foreach ( $value as $single_value )
				{
					if ( is_object( $single_value ) )
						$page.= $this->NL . " ".$single_value->getNode();
					else
						$page .= $this->NL . "".$single_value;
				}
				
				$page .= $this->NL . "]";			
			}
			else if ( is_object( $value ) )
			{
				$page.= $this->NL . $name . " " . $value->getNode();
			}
			else
			{
				$page .= $this->NL . $name . " " . $value;
			}		
		}
		
		$page .= $this->NL . "}";
		return $page;
	}
} // END OF VRMLNode

?>
