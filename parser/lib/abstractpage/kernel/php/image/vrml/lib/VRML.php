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


using( 'image.vrml.lib.VRMLWorldInfo' );


/**
 * @package image_vrml_lib
 */
 
class VRML extends PEAR
{
	var $world_info;
	var $nodes;
	var $file_name;
	
	
	/**
	 * Constructor
	 */
	function VRML( $p_title = "Title", $p_info = array("Info" ), $p_file_name = "test.wrl" )
	{
		$this->world_info = new VRMLWorldInfo( $p_title, $p_info );
		$this->nodes      = array();
		$this->file_name  = $p_file_name;
		
		$this->addNode( $this->world_info );
	}

	
	function addNode( $p_node )
	{
		if ( is_array( $p_node ) )
		{
			foreach( $p_node as $node )
				array_push( $this->nodes, $node );
		}
		else
		{
			array_push( $this->nodes, $p_node );
		}
	}
	
	function generate()
	{
		$page = "#VRML V2.0 utf8";
		
		foreach ( $this->nodes as $node )
			$page .= $node->getNode();
		
		$fp = fopen( $this->file_name, "w" );
		fwrite( $fp, $page );
		fclose( $fp );
	}
} // END OF VRML

?>
