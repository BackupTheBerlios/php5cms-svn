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
 
class VRMLTransform extends VRMLNode
{
	/**
	 * Constructor
	 */
	function VRMLTransform( $p_children = "NULL", $p_scale = "1 1 1", $p_scaleOrientation = "0 0 1 0", $p_center = "0 0 0", $p_rotation = "0 0 1 0", $p_translation = "4 0 0", $p_bboxCenter = "0 0 0", $p_bboxSize = "-1 -1 -1" )
	{
		$this->attributes = array(
			"scale"            => $p_scale,
			"scaleOrientation" => $p_scaleOrientation,
			"center"           => $p_center,
			"rotation"         => $p_rotation,
			"translation"      => $p_translation,
			"bboxCenter"       => $p_bboxCenter,
			"bboxSize"         => $p_bboxSize,
			"children"         => $p_children
		);
		
		$this->node_name = "Transform";
	}
	
	
	function setTranslation( $p_translation )
	{
		$this->attributes["translation"] = $p_translation;
	}
} // END OF VRMLTransform

?>
