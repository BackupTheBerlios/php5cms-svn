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


/**
 * Tree class
 *
 * This class is based on Alexander Aulbach's tree class from
 * PHPLib. His version doesn't work with PHP 4.0, and has some glitches
 * that I wasn't able to correct without considerable effort. So I
 * rewrote the class from scratch. The documentation from PHPLib
 * applies to this new version as well.
 *
 * @package util
 */
 
class Tree extends PEAR
{
	/**
	 * path delimiter character(s)
	 * @access public
	 */	
	var $delimiter = "^";
	
	/**
	 * tree array
	 * @access public
	 */	
    var $tree = array();

	/**
	 * @access public
	 */	
    var $depth = 0;
	
	/**
	 * @access public
	 */	
    var $num_total = 0;
	
	/**
	 * @access public
	 */	
    var $num_sub_elements = 0;
	
	/**
	 * @access public
	 */	
    var $num_in_level = 0;
	
	/**
	 * @access public
	 */	
    var $path = "";
    
	/**
	 * @access public
	 */	
	var $prfx = array();

	
	/**
	 * Constructor, takes tree array as optional argument.
	 *
	 * @access public
	 */
    function Tree( $tree = false )
    {
        if ( is_array( $tree ) )
        	$this->tree = $tree;
    }


	/**
	 * Traverses the tree structure starting from path.
	 *
	 * @access public
	 */	
    function traverse( $path =  "", $key =  "", $value =  "", $num_sub = 0 )
    {
		// escape double quotes in path 
        $path = str_replace( '"',  '\"', $path );
		
		// has a path been passed as argument 
        if ( !empty( $path ) )
        {
			// create array index out of the path components, or assign empty value to $index if no path was specified 
            $index = sprintf( '["%s"]', implode( "\"][\"", explode( $this->delimiter, $path ) ) );

			// get array element specified by path 
            $eval =  "\$current = \$this->tree$index;";

			// don't you love interpreted languages 
            eval( $eval );

			// test for array - just to be sure 
            if ( !is_array( $current ) )
 				return false;
        }
        else
        {
			// no path specified, start at the top-most element 
            $current = $this->tree;
        }

		// Is a pseudo element set at position 0, containing the real value of this element?
        if ( isset( $current[0] ) )
        {
			// yes, move forward and extract the real value 
            list( $k, $value ) = each( $current );
        }

		// Is this the start of a new tree?
        if ( $this->depth != 0 )
        {
			// We're in grow mode! 
            $this->display_grow( $key, $value );
        }
        else
        {
			// This is a new tree 
            $this->display_start();
        }

        $i = 0;

		// total number of sub elements 
        $num_sub = isset( $current[0] )? count( $current ) - 1:  count( $current );
        $this->num_sub_elements = $num_sub;

		// increase depth of nesting 
        $this->depth++;

		// loop through all elements of the current array 
        while ( list( $key, $value ) = each( $current ) )
        {
            $i++;

			// current position in this level 
            $this->num_in_level = $i;

			// increase total element count 
            $this->num_total++;

			// add current element to path 
            $path .= empty( $path )? $key : $this->delimiter . $key;
            $this->path = $path;

			// Does this element have children or is it a leaf?
            if ( !is_array( $value ) )
            {
                $this->display_leaf( $key, $value );
            }
            else
            {
				// traverse sub-trees 			
                $this->traverse( $path, $key, $value, $num_sub );

				// total number of sub elements, set it back to last $num_sub value 
                $this->num_sub_elements = $num_sub;
                $this->display_shrink();
            }

			// remove last element from path 
            $path = substr( $path, 0, strrpos( $path, $this->delimiter ) );
            $this->path = $path;
        }

		// decrease depth of nesting 
        $this->depth--;

        if ( $this->depth == 0 )
			$this->display_end();
    }

	/**
	 * @access public
	 */	
    function display_start()
    {
        $this->flag = true;
    }

	/**
	 * @access public
	 */	
    function display_grow( $key, $value )
    {
        // directly from PHPLib
        echo( "<TT>" . join( $this->prfx, "" ) );
        
		if ( $this->flag )
			echo( "^----" );
        else if ( $this->num_in_level == $this->num_sub_elements )
			echo( "&#160;\---" );
		else
			echo( "O----" );

		echo( sprintf(
			"</TT> %s->'<A HREF=\"?val=%s\">%s</A>'" . " : '%s' (%s) [%s/%s]<BR>\n",
			$key,
			URLEncode( $value ),
			$value,
			$this->path,
			$this->depth,
			$this->num_in_level,
			$this->num_sub_elements
		) );

        if ( $this->num_in_level < $this->num_sub_elements )
			$this->prfx[$this->depth] = "|&#160;&#160;&#160;&#160;";
		else
			$this->prfx[$this->depth] = "&#160;&#160;&#160;&#160;&#160;";
        
		$this->flag = true;
    }

	/**
	 * @access public
	 */	
    function display_shrink()
    {
        unset( $this->prfx[$this->depth] );
    }

	/**
	 * @access public
	 */	
    function display_leaf( $key, $value )
    {
        echo( "<TT>" . join( $this->prfx, "" ) );
		
        if ( $this->flag )
			echo( "*----" );
        else if ( $this->num_in_level == $this->num_sub_elements )
			echo( "&#160;\\---" );
		else
			echo( "+----" );

		echo( sprintf(
			"</TT> %s->'<A HREF=\"?val=%s\">%s</A>'" . " : '%s' (%s) [%s/%s]<BR>\n",
             $key,
			 URLEncode( $value ),
			 $value,
			 $this->path,
			 $this->depth,
			 $this->num_in_level,
			 $this->num_sub_elements
		) );
        
		$this->flag = false;
    }

	/**
	 * @access public
	 */	
    function display_end()
    {
    }
} // END OF Tree

?>
