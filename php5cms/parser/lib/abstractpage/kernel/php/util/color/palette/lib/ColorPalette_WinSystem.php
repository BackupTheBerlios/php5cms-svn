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
 
class ColorPalette_WinSystem extends ColorPalette
{	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ColorPalette_WinSystem( $params = array() )
	{
		$this->ColorPalette( "WinSystem", $params );
		
		$this->_palette = array(
			'activeborder',
			'activecaption',
			'appworkspace',
			'background',
			'buttonface',
			'buttonhighlight',
			'buttonshadow',
			'buttontext',
			'captiontext',
			'graytext',
			'highlight',
			'highlighttext',
			'inactiveborder',
			'inactivecaption',
			'inactivecaptiontext',
			'infobackground',
			'infotext',
			'menu',
			'menutext',
			'scrollbar',
			'threeddarkshadow',
			'threedface',
			'threedhighlight',
			'threedlightshadow',
			'threedshadow',
			'window',
			'windowframe',
			'windowtext'
		);
	}
} // END OF WindowsSystemColor

?>
