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
 
class SvgLine extends SvgElement
{
	/**
	 * @access public
	 */
    var $mX1;
	
	/**
	 * @access public
	 */
    var $mY1;
	
	/**
	 * @access public
	 */
    var $mX2;
	
	/**
	 * @access public
	 */
    var $mY2;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgLine( $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $style = "", $transform = "" )
    {
        $this->mX1        = $x1;
        $this->mY1        = $y1;
        $this->mX2        = $x2;
        $this->mY2        = $y2;
        $this->mStyle     = $style;
        $this->mTransform = $transform;
    }
	

	/**
	 * @access public
	 */    
    function printElement()
    {
        print( "<line x1=\"$this->mX1\" y1=\"$this->mY1\" x2=\"$this->mX2\" y2=\"$this->mY2\" " );
        
		// Print children, start and end tag.
        if ( is_array( $this->mElements ) )
		{    
            $this->printStyle();
            $this->printTransform();
            print( ">\n" );
            parent::printElement();
            print( "</line>\n" );
           
        }
		// Print short tag.
		else
		{    
            $this->printStyle();
            $this->printTransform();
            print( "/>\n" );
        }
    }

	/**
	 * @access public
	 */    
    function setShape( $x1, $y1, $x2, $y2 )
    {
        $this->mX1 = $x1;
        $this->mY1 = $y1;
        $this->mX2 = $x2;
        $this->mY2 = $y2;
    }
} // END OF SvgLine

?>
