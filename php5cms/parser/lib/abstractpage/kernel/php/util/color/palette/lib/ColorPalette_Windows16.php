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


using( 'util.color.palette.lib.ColorPalette' );


/**
 * @package util_color_palette_lib
 */
 
class ColorPalette_Windows16 extends ColorPalette
{	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ColorPalette_Windows16( $params = array() )
	{
		$this->ColorPalette( "Windows16", $params );
		
		$this->_palette = array(
			"#000000",
			"#000099",
			"#009900",
			"#009999",
			"#990000",
			"#990099",
			"#999900",
			"#cccccc",
			"#999999",
			"#0000ff",
			"#00ff00",
			"#00ffff",
			"#ff0000",
			"#ff00ff",
			"#ffff00",
			"#ffffff" 
		);
	}
} // END OF ColorPalette_Windows16

?>
