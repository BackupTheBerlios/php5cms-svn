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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'template.atl.ATL_Context' );


/**
 * Array resolver.
 *
 * Resolve pathes relative to arrays and hashtables.
 *
 * @package template_atl
 */
 
class ATL_ArrayResolver extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
    function &get( &$array, $subpath )
    {
        list( $first, $next ) = ATL_Context::path_explode( $subpath );

        if ( $first == "length" && !$next && !array_key_exists( "length", $array ) )
            return count( $array );
        
        if ( $next === false ) 
		{
            if ( $first == 'count' )
				return count( $array );
				
            if ( $first == 'keys' )
				return array_keys( $array );
				
            if ( $first == 'values' )
				return array_values( $array );
				
            if ( !array_key_exists( $first, $array ) )
				return PEAR::raiseError( "Context does not contains key '$first'." );
            
            return $array[$first];
        }

        if ( !array_key_exists( $first, $array ) )
           	return PEAR::raiseError( "Context does not contains key '$first'." );
        
        $temp =& $array[$first];

        if ( $temp )
            return ATL_Context::resolve( $next, $temp, $array );
    }
} // END OF ATL_ArrayResolver

?>
