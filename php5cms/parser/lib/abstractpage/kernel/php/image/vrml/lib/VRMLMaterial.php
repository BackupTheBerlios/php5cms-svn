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
 
class VRMLMaterial extends VRMLNode
{
	/**
	 * Constructor
	 */
	function VRMLMaterial( $p_diffuseColor = "0.8 0.8 0.8", $p_ambientIntensity = "0.2", $p_emissiveColor = "0.0 0.0 0.0", $p_specularColor = "0.0 0.0 0.0", $p_shininess = "0.2", $p_transparency = "0.0" )
	{
		$this->attributes = array(
			"diffuseColor"     => $p_diffuseColor,
			"ambientIntensity" => $p_ambientIntensity,
			"emissiveColor"    => $p_emissiveColor,
			"specularColor"    => $p_specularColor,
			"shininess"        => $p_shininess,
			"transparency"     => $p_transparency
		);
		
		$this->node_name = "Material";
	}
	
	
	function setTransparency( $p_transparency )
	{
		$this->attributes["transparency"] = $p_transparency;
	}
} // END OF VRMLMaterial

?>
