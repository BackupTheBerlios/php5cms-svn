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
 * @package util_var
 */
 
class VarSerialisation extends PEAR
{
	/**
	 * This is a dummy fn to get the copy of the var then pass that copy by 
	 * reference to _serialize() fn that may alter the var with escaping.
	 *
	 * @access public
	 * @static
	 */
	function serialize( $var, $string_escape = false ) 
	{
		$str = VarSerialisation::_serialize( $var );
	
		if ( $string_escape )
			$str = str_replace( "\n", "\\n", addslashes( $str ) );
	
		return $str;
	}

	/**
	 * This is a dummy fn to get the copy of the var then pass that copy by 
	 * reference to _unserialize() fn that may alter the var with escaping.
	 *
	 * @access public
	 * @static
	 */
	function unserialize( $str )
	{
		$str   = str_replace( "\r\n", "\n", $str );
		$str   = str_replace( "\r",   "\n", $str );
		$lines = explode( "\n", trim( $str ) );
		$i = 0;
	
		list( $var, $name ) = VarSerialisation::_unserialize( $lines, $i );
		return $var;
	}
	
	
	// private methods
	
	/**
	 * @access public
	 * @static
	 */
	function _serialize( &$var ) 
	{
		if (func_num_args() == 3 ) 
		{
			$name   = func_get_arg( 1 );
			$indent = func_get_arg( 2 );
		} 
		else 
		{
			$indent = '';
		}

		$str  = "";
		$type = gettype( $var );

		switch ( $type ) 
		{
			// normal vars
			case "string":
				$var = str_replace( '~',  '~~',   $var );
				$var = str_replace( '<',  '~l~',  $var );
				$var = str_replace( '>',  '~g~',  $var );
				$var = str_replace( "\n", '<lf>', $var );
				$var = str_replace( "\r", '<cr>', $var );
		
			case "integer":
		
			case "double":
				if ( isset( $name ) )
					$str .= $indent . '<name_type>' . gettype( $name ) . '</name_type><name>' . $name . '</name>';
			
				$str .= '<val_type>' . $type . '</val_type><val>' . $var . "</val>\n";
		
				break;

			case "boolean":
				if ( isset( $name ) )
					$str .= $indent . '<name_type>' . gettype( $name ) . '</name_type><name>' . $name . '</name>';
			
				$str .= '<val_type>' . $type . '</val_type><val>' . ( $var? 1 : 0 ) . "</val>\n";
				
				break;

			// recursive vars
			case "array":
				if ( isset( $name ) )
					$str .= $indent . '<name_type>' . gettype( $name ) . '</name_type><name>' . $name . '</name>';
			
				$str .= '<val_type>' . $type . "</val_type>\n";
			
				for ( reset( $var ); ( $k = key( $var ) ) !== null; next( $var ) )
					$str .= VarSerialisation::_serialize( $var[$k], $k, $indent . ' ' );

				break;

			case "NULL" :
				$str .= $indent . "\n";
				break;
		}
			
		return $str;
	}

	/**
	 * @access public
	 * @static
	 */
	function _unserialize( &$lines, &$i, $indent = '' ) 
	{
		$str = &$lines[$i];
		
		// if it's blank then return null
		if ( $str == "" ) 
			return array( null, null );

		$name_type = "";
		$name = null;

		$e = '/^' . $indent . '<name_type>(.*)<\/name_type><name>(.*)<\/name>(.*)$/';
	
		if ( preg_match( $e, $str, $matches ) ) 
		{
			$name_type = $matches[1];
			$name      = $matches[2];
			$str       = $matches[3];
		
			settype( $name, $name_type );
		}

		// ok, so it's an array
		if ( $str == '<val_type>array</val_type>' )
		{
			$indent_len = strlen( $indent );
			$i++;
			$val = array();
		
			// while the indent is still the same unserialize our contents
			while ( isset( $lines[$i] ) && $indent . ' ' == substr( $lines[$i], 0, $indent_len + 1 ) ) 
			{
				list( $var, $key ) = VarSerialisation::_unserialize( $lines, $i, $indent . ' ' );
				$val[$key] = $var;
				$i++;
			}
			
			$i--;
			return array( $val, $name );
		}

		$val_type = "";
		$val = null;

		$e = '/^<val_type>(.*)<\/val_type><val>(.*)<\/val>$/';
	
		if ( preg_match( $e, $str, $matches ) ) 
		{
			$val_type = $matches[1];
			$val = $matches[2];
		
			settype( $val, $val_type );

			// if this is a string then we need to reverse the escaping process
			if ( $val_type == "string" ) 
			{
				$val = str_replace( '<cr>', "\r", $val );
				$val = str_replace( '<lf>', "\n", $val );
				$val = str_replace( '~g~',  '>',  $val );
				$val = str_replace( '~l~',  '<',  $val );
				$val = str_replace( '~~',   '~',  $val );
			}
		}

		return array( $val, $name );
	}
} // END OF VarSerialisation

?>
