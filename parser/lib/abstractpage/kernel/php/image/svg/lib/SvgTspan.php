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
 
class SvgTspan extends SvgElement
{
	/**
	 * @access public
	 */
    var $mX;
	
	/**
	 * @access public
	 */
    var $mY;
	
	/**
	 * @access public
	 */
    var $mText;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgTspan( $x = 0, $y = 0, $text = "", $style = "", $transform = "" )
    {
        $this->mX = $x;
        $this->mY = $y;
        $this->mText  = $text;
        $this->mStyle = $style;
        $this->mTransform = $transform;
    }
    
	
	/**
	 * @access public
	 */
    function printElement()
    {
        print( "<tspan x=\"$this->mX\" y=\"$this->mY\" " );
        
		// Print children, start and end tag.
        if ( is_array( $this->mElements ) )
		{
            $this->printStyle();
            $this->printTransform();
            
			print( ">\n" );
            print( $this->mText );
            parent::printElement();
            print( "</tspan>\n" );
        }
		// Print short tag.
		else
		{
            $this->printStyle();
            $this->printTransform();
			
            print( ">\n" );
            print( $this->mText );
            print( "\n</tspan>\n" );
        }
    }
    
	/**
	 * @access public
	 */
    function setShape( $x, $y, $text )
    {
        $this->mX = $x;
        $this->mY = $y;
        $this->mText  = $text;
    }
} // END OF SvgTspan

?>
