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
 
class SvgAnimate extends SvgElement
{
	/**
	 * @access public
	 */
    var $mAttributeName;
	
	/**
	 * @access public
	 */
    var $mAttributeType;
	
	/**
	 * @access public
	 */
    var $mFrom;
	
	/**
	 * @access public
	 */
    var $mTo;
	
	/**
	 * @access public
	 */
    var $mBegin;
	
	/**
	 * @access public
	 */
    var $mDur;
	
	/**
	 * @access public
	 */
    var $mFill;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgAnimate( $attributeName, $attributeType = "", $from = "", $to = "", $begin = "", $dur = "", $fill = "" )
    {
        $this->mAttributeName = $attributeName;
        $this->mAttributeType = $attributeType;
        $this->mFrom          = $from;
        $this->mTo            = $to;
        $this->mBegin         = $begin;
        $this->mDur           = $dur;
        $this->mFill          = $fill;
    }
    
	
	/**
	 * @access public
	 */
    function printElement()
    {
        print( "<animate attributeName=\"$this->mAttributeName\" " );
        
        // Print the attributes only if they are defined.
        if ( $this->mAttributeType != "" ) { print ("attributeType=\"$this->mAttributeType\" "); }
        if ( $this->mFrom != "" )
			print( "from=\"$this->mFrom\" " );
			
        if ( $this->mTo != "" )
			print( "to=\"$this->mTo\" " );
			
        if ( $this->mBegin != "" )
			print( "begin=\"$this->mBegin\" " );
			
        if ( $this->mDur != "" )
			print( "dur=\"$this->mDur\" " );
			
        if ( $this->mFill != "" )
			print( "fill=\"$this->mFill\" " );
        
		// Print children, start and end tag.
        if ( is_array( $this->mElements ) )
		{    
            print( ">\n" );
            parent::printElement();
            print( "</animate>\n" );
        }
		else
		{
            print( "/>\n" );
        }
    }

	/**
	 * @access public
	 */    
    function setShape( $attributeName, $attributeType = "", $from = "", $to = "", $begin = "", $dur = "", $fill = "" )
    {
        $this->mAttributeName = $attributeName;
        $this->mAttributeType = $attributeType;
        $this->mFrom          = $from;
        $this->mTo            = $to;
        $this->mBegin         = $begin;
        $this->mDur           = $dur;
        $this->mFill          = $fill;
    }
} // END OF SvgAnimate

?>
