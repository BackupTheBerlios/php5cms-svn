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


using( 'image.svg.lib.SvgFragment' );


/**
 * This extends the SvgFragment class. It wraps the SvgFrament output with
 * a content header, xml definition and doctype.
 *
 * @package image_svg_lib
 */
 
class SvgDocument extends SvgFragment
{
    /**
	 * Constructor
	 */ 
    function SvgDocument( $width = "100%", $height = "100%", $style = "" )
    {
        $this->SvgFragment( $width, $height, "", "", $style );
    }
    
	
    function printElement()
    {
        header( "Content-Type: image/svg+xml" );    
        print( '<?xml version="1.0" encoding="iso-8859-1"?>'."\n" );    
        print( '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
	        "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">'."\n" );
        
        parent::printElement();
    }
} // END OF SvgDocument

?>
