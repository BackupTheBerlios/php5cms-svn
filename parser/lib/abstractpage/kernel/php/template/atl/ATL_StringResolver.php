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
 * String resolver.
 *
 * Add method to string type.
 *
 * @package template_atl
 */
 
class ATL_StringResolver extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
    function get( &$str, $subpath )
    {
        list( $first, $next ) = ATL_Context::path_explode( $subpath );
        
		if ( $next ) 
			return PEAR::raiseError( "String methods have no sub path (string.$subpath)." );
        
        if ( $first == "len" )
            return strlen( $str );
        
		return PEAR::raiseError( "String type has no method named '$first'." );
    }
} // END OF ATL_StringResolver

?>
