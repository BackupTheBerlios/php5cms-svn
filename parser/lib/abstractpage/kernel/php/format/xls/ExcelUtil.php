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
 * @package format_xls
 */
 
class ExcelUtil extends PEAR
{
	/**
	 * Begin of file header.
	 *
	 * @ccess public
	 * @static
	 */
	function bof()
	{ 
    	return pack( "ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0 );
	} 

	/**
	 * End of file footer.
	 *
	 * @access public
	 * @static
	 */
	function eof()
	{ 
    	return pack( "ss", 0x0A, 0x00 ); 
	}
 
	/**
	 * Write a Number (double) into row, col.
	 *
	 * @access public
	 * @static
	 */
	function writeNumber( $Row, $Col, $Value )
	{ 
		$res  = "";
    	$res .= pack( "sssss", 0x203, 14, $Row, $Col, 0x0 ); 
    	$res .= pack( "d", $Value ); 
    
		return $res;
	}

	/**
	 * Write a label (text) into row, col.
	 *
	 * @access public
	 * @static
	 */
	function writeLabel( $Row, $Col, $Value )
	{ 
    	$L = strlen( $Value );
		$res  = "";
    	$res .= pack( "ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L ); 
    	$res .= $Value; 

		return $res; 
	}
} // END OF ExcelUtil 

?>
