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


/**
 * This is the base class for the different Svg Element Objects. 
 * Extend this class to create a new Svg Element.
 *
 * @package image_svg_lib
 */
 
class SvgElement extends PEAR
{
	/**
	 * @access public
	 */
	var $mStyle;
	
	/**
	 * @access public
	 */
    var $mTransform;
	
	/**
	 * Initialize so warnings aren't issued when not used
	 * @access public
	 */
    var $mElements = "";
    
	
	/**
	 *  Most Svg elements can contain child elements. This method calls the
     * printElement method of any child element added to this object by use
     * of the addChild method.
	 *
	 * @access public
	 */
    function printElement()
    {
        // Loop and call
        if ( is_array( $this->mElements ) )
		{
            foreach ( $this->mElements as $child )
                $child->printElement();
        }
    }
    
	/**
	 *  This method adds an object reference to the mElements array.
	 *
	 * @access public
	 */
    function addChild( &$element )
    {
        $this->mElements[] =& $element;
    }
    
	/**
	 * This method sends a message to the passed element requesting to be
     * added as a child.
	 *
	 * @access public
	 */
    function addParent( &$parent )
    {
        if ( is_subclass_of( $parent, "SvgElement" ) ) 
            $parent->addChild( $this );
    }
    
	/**
	 * Most Svg elements have a style attribute.
     * It is up to the dervied class to call this method.
	 *
	 * @access public
	 */
	function printStyle()
    {
        if ( $this->mStyle != "" )
            print("style=\"$this->mStyle\" ");
    }
	
	/**
	 * This enables the style property to be set after initialization.
	 *
	 * @access public
	 */
    function setStyle( $string )
    {
        $this->mStyle = $string;
    }
    
	/**
	 * Most Svg elements have a transform attribute.
     * It is up to the dervied class to call this method.
	 *
	 * @access public
	 */
    function printTransform()
    {
        if ( $this->mTransform != "" )
            print( "transform=\"$this->mTransform\" " );
    }
    
	/**
	 * This enables the transform property to be set after initialization.
	 *
	 * @access public
	 */
    function setTransform( $string )
    {
        $this->mTransform = $string;
    }
    
	/**
	 * Print out the object for debugging.
	 *
	 * @access public
	 */
    function debug()
    {
        print( "<pre>" );
        print_r( $this );
        print( "</pre>" );
    }
} // END OF SvgElement

?>
