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
 * @package html_js
 */
 
class Hash2JSObject extends PEAR
{
	var $objectname;
	var $collection;
	
	
	/**
	 * Constructor
	 */
	function Hash2JSObject( $name = "myvar" )
	{
		$this->objectname = $name;
	}
	
	
	function clear()
	{
		$this->collection = "";
	}
	
	function add( $pairs )
	{
		if ( !is_array( $pairs ) )
			return false;
			
		while ( list( $key, $val ) = each( $pairs ) )
			$this->collection[$key] = $val;
	}
	
	function getJSObject()
	{
		$length = count( $this->collection );
		$str    = "var " . $this->objectname . " = {\n";

		$i = 0;
		while ( list( $key, $val ) = each( $this->collection ) )
		{
			if ( is_string( $val ) )
				$val = '"' . $val . '"';
			
			$str .= "\t" . $key . ": " . $val . ( ( $i + 1 == $length )? "" : "," ). "\n";
			$i++;
		}
						
		$str .= "}\n";
		return $str;
	}
} // END OF Hash2JSObject

?>
