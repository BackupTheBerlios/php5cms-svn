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
 
class SvgMarker extends SvgElement
{
	/**
	 * @access public
	 */
    var $mId;
	
	/**
	 * @access public
	 */
    var $mRefX;
	
	/**
	 * @access public
	 */
    var $mRefY;
	
	/**
	 * @access public
	 */
    var $mMarkerUnits;
	
	/**
	 * @access public
	 */
    var $mMarkerWidth;
	
	/**
	 * @access public
	 */
    var $mMarkerHeight;
    
	/**
	 * @access public
	 */
	var $mOrient;
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function SvgMarker( $id, $refX = "", $refY = "", $markerUnits = "", $markerWidth = "", $markerHeight = "", $orient = "" )
    {
        $this->mId           = $id;
        $this->mRefX         = $refX;
        $this->mRefY         = $refY;
        $this->mMarkerUnits  = $markerUnits;
        $this->mMarkerWidth  = $markerWidth;
        $this->mMarkerHeight = $markerHeight;
        $this->mOrient       = $orient;
    }
    

	/**
	 * @access public
	 */	
    function printElement()
    {
        print( "<marker id=\"$this->mId\" " );
        
        // Print the attributes only if they are defined.
        if ( $this->mRefX != "" )
			print( "refX=\"$this->mRefX\" " );
        
		if ( $this->mRefY != "" )
			print( "refY=\"$this->mRefY\" " );
			
        if ( $this->mMarkerUnits != "" )
			print( "markerUnits=\"$this->mMarkerUnits\" " );
			
        if ( $this->mMarkerWidth != "" )
			print( "markerWidth=\"$this->mMarkerWidth\" " );
			
        if ( $this->mMarkerHeight != "" )
			print( "markerHeight=\"$this->mMarkerHeight\" " );
			
        if ( $this->mOrient != "" )
			print( "orient=\"$this->mOrient\" " );
        
		// Print children, start and end tag.
        if ( is_array( $this->mElements ) )
		{    
            print( ">\n" );
            parent::printElement();
            print( "</marker>\n" );
        }
		else
		{
            print( "/>\n" );
        }
    }

	/**
	 * @access public
	 */    
    function setShape( $id, $refX = "", $refY = "", $markerUnits = "", $markerWidth = "", $markerHeight = "", $orient = "" )
    {
        $this->mId           = $id;
        $this->mRefX         = $refX;
        $this->mRefY         = $refY;
        $this->mMarkerUnits  = $markerUnits;
        $this->mMarkerWidth  = $markerWidth;
        $this->mMarkerHeight = $markerHeight;
        $this->mOrient       = $orient;
    }
} // END OF SvgMarker

?>
