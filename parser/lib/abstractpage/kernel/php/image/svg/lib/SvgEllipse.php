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
 
class SvgEllipse extends SvgElement
{
	/**
	 * @access public
	 */
    var $mCx;
	
	/**
	 * @access public
	 */
    var $mCy;
	
	/**
	 * @access public
	 */
    var $mRx;
	
	/**
	 * @access public
	 */
    var $mRy;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgEllipse( $cx = 0, $cy = 0, $rx = 0, $ry = 0, $style = "", $transform = "" )
    {
        $this->mCx        = $cx;
        $this->mCy        = $cy;
        $this->mRx        = $rx;
        $this->mRy        = $ry;
        $this->mStyle     = $style;
        $this->mTransform = $transform;
    }
    
	
	/**
	 * @access public
	 */
    function printElement()
    {
        print( "<ellipse cx=\"$this->mCx\" cy=\"$this->mCy\" rx=\"$this->mRx\" ry=\"$this->mRy\" " );
        
		// Print children, start and end tag.
        if ( is_array( $this->mElements ) )
		{    
            $this->printStyle();
            $this->printTransform();
            print( ">\n" );
            parent::printElement();
            print( "</ellipse>\n" );
            
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
    function setShape( $cx, $cy, $rx, $ry )
    {
        $this->mCx = $cx;
        $this->mCy = $cy;
        $this->mRx = $rx;
        $this->mRy = $ry;
    }
} // END OF SvgEllipse

?>
