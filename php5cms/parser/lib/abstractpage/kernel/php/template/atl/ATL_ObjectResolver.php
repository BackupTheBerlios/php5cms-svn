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
 * Object resolver.
 *
 * Resolver pathes relatives to object.
 *
 * @package template_atl
 */
 
class ATL_ObjectResolver extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
    function &get( &$obj, $subpath )
    {       
        list( $first, $next ) = ATL_Context::path_explode( $subpath );
        
        if ( method_exists( $obj, $first ) ) 
		{ 
            // reference to a variable of the handled object
            $value =& $obj->$first(); 
        } 
		else if ( array_key_exists( $first, $obj ) ) 
		{
            // reference to an object variable
            $value =& $obj->$first;
        } 
		else if ( is_a( $obj, 'atl_dictionary' ) && $obj->containsKey( $first ) ) 
		{
            return $obj->get( $first );
        } 
		else if ( is_a( $obj, 'atl_array' ) && preg_match( '/^[0-9]+$/', $first ) ) 
		{
            return $obj->get( (int)$first );
        } 
		else 
		{
			return PEAR::raiseError( $subpath . " not found." );
        }
		
        // more to resolve in value
        if ( $next )
            return ATL_Context::resolve( $next, $value, $obj );
        
        return $value;        
    }
} // END OF ATL_ObjectResolver

?>
