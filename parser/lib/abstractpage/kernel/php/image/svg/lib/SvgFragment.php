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
 
class SvgFragment extends SvgElement
{
	/**
	 * @access public
	 */
    var $mWidth;
	
	/**
	 * @access public
	 */
    var $mHeight;
	
	/**
	 * @access public
	 */
    var $mX;
	
	/**
	 * @access public
	 */
    var $mY;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgFragment( $width = "100%", $height = "100%", $x = 0, $y = 0, $style = "" )
    {
        $this->mWidth  = $width;
        $this->mHeight = $height;
        $this->mStyle  = $style;
        $this->mX      = $x;
        $this->mY      = $y;
    }
    
	
	/**
	 * @access public
	 */
    function printElement()
    {
        print( "<svg width=\"$this->mWidth\" height=\"$this->mHeight\" " );
        
        if ( $this->mX != "" )
            print( "x=\"$this->mX\" " );
        
        if ( $this->mY != "" )
            print( "y=\"$this->mY\" " );
        
        print( 'xmlns="http://www.w3.org/2000/svg" ' );
        print( 'xmlns:xlink="http://www.w3.org/1999/xlink" ' );
        $this->printStyle();
        print( ">\n" );
        parent::printElement();
        print( "</svg>\n" );
    }

	/**
	 * @access public
	 */    
    function bufferObject()
    {
        ob_start();
        $this->printElement();
        $buff = ob_get_contents();
	    ob_end_clean();
        return $buff;
    }
} // END OF SvgFragment

?>
