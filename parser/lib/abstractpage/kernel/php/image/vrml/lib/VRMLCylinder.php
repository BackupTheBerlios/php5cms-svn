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


using( 'image.vrml.lib.VRMLNode' );


/**
 * @package image_vrml_lib
 */
 
class VRMLCylinder extends VRMLNode
{
	/**
	 * Constructor
	 */
	function VRMLCylinder( $p_radius = "1.0", $p_height = "2.0", $p_side = "TRUE", $p_bottom = "TRUE", $p_top = "TRUE" )
	{
		$this->attributes = array(
			"radius" => $p_radius,
			"height" => $p_height,
			"side"   => $p_side,
			"bottom" => $p_bottom,
			"top"    => $p_top
		);
		
		$this->node_name = "Cylinder";
	}
} // END OF VRMLCylinder

?>
