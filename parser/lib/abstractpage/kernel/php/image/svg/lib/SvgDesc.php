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
|Authors: Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'image.svg.lib.SvgElement' );


/**
 * @package image_svg_lib
 */
 
class SvgDesc extends SvgElement
{
	/**
	 * @access public
	 */
    var $mDesc;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgDesc( $desc, $style = "" )
    {
        $this->mDesc  = $desc;
        $this->mStyle = $style;
    }
    
	
	/**
	 * @access public
	 */
    function printElement()
    {
        print( "<desc " );
        $this->printStyle();
        print( ">\n" );
        print( $this->mDesc . "\n" );
        parent::printElement();
        print( "</desc>\n" );
    }
} // END OF SvgDesc

?>
