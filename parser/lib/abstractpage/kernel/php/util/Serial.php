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
 * @package util
 */
 
class Serial extends PEAR
{
	/**
	 * @access public
	 */	
	var $sep;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Serial( $s = "\t" )
	{
		$this->sep = $s ;
	}
	

	/**
	 * @access public
	 */		
	function saveDataToString( $_val )
	{
		switch( $_type = gettype( $_val ) )
		{
			case 'integer':
				$_str = "i$this->sep$_val$this->sep";
				break;
			
			case 'double':
				$_str = "d$this->sep$_val$this->sep";
				break;
			
			case 'string':
				$_len = strlen( $_val );
				$_str = "s$this->sep$_len$this->sep$_val$this->sep";
				break;
				
			case 'array':
				$_len = count( $_val );
				$_str = "a$this->sep$_len$this->sep";
				
				while ( $_x = each( $_val ) )
				{
					$_str .= $this->saveDataToString( $_x[0] );
					$_str .= $this->saveDataToString( $_x[1] );
				}
				
				break;
				
			default:
				print( "cannot saveDataToString value of type [$_type]<BR>\n" );
				$_str = '';
		}
		
		return $_str ;
	}
	
	/**
	 * each array element may become a new global variable
	 * if read back thru loadVarsFromString
	 *
	 * @access public
	 */
	function saveArrayToString( $_val )
	{
		while ( $_x = each( $_val ) )
		{
			$_str .= ( $_x[0] . $this->sep );
			$_str .= $this->saveDataToString( $_x[1] );
		}
		
		return $_str;
	}

	/**
	 * @access public
	 */		
	function saveVarToString( $_name )
	{
		if ( isset( $GLOBALS[$_name] ) )
			return ( "$_name$this->sep" . $this->saveDataToString( $GLOBALS[$_name] ) );
		else
			return ( '' );
	}
	
	/**
	 * @access public
	 */	
	function saveVarsToString( $_list )
	{
		if ( is_string( $_list ) )
			$_list = explode( " ", $_list );
		
		$_len = count( $_list );
		$_str = '';
		
		for ( $_i = 0; $_i < $_len; $_i++ )
			$_str .= $this->saveVarToString( $_list[$_i] );
		
		return $_str;
	}
	
	/**
	 * @access public
	 */	
	function loadDataFromString( &$_str )
	{
		$_i    = strpos( $_str, $this->sep ); 
		$_type = substr( $_str, 0, $_i ); 
		$_str  = substr( $_str, ( $_i + 1 ) );
		
		switch ( $_type )
		{
			case 'i':
				$_i   = strpos( $_str, $this->sep );
				$_val = intval( substr ( $_str, 0, $_i ) );
				$_str = substr( $_str, ($_i + 1 ) );
				
				break;
				
			case 'd':
				$_i   = strpos( $_str, $this->sep );
				$_val = doubleval( substr( $_str, 0, $_i ) );
				$_str = substr( $_str, ($_i + 1 ) );
				
				break;
			
			case 's':
				$_i   = strpos( $_str, $this->sep );
				$_len = substr( $_str, 0, $_i );
				$_str = substr( $_str, ($_i + 1 ) );
				$_val = substr( $_str , 0, $_len );
				$_str = substr( $_str, ( $_len + 1 ) );
				
				break;
			
			case 'a':
				$_i   = strpos( $_str, $this->sep );
				$_len = substr( $_str, 0, $_i );
				$_str = substr( $_str, ( $_i + 1 ) );
				
				for ( $_i = 0; $_i < $_len; $_i++ )
				{
					$_k = $this->loadDataFromString( $_str );
					$_val[$_k] = $this->loadDataFromString( $_str );
				}
				
				break;
				
			default:
				print( "invalid type [$_type] found in loadDataFromString<BR>\n" );
				$_val = 0;
				break;
		}
		
		return ( $_val );
	}
	
	/**
	 * read back string created by saveArrayToString
	 * or read back into one array a set of independant globals
	 * written thru saveVarsToString (avoiding name space pollution)
	 *
	 * @access public
	 */
	function loadArrayFromString( &$_str )
	{
		// until the string is empty or nearly empty
		while ( $_str && ( $_str != $this->sep ) )
		{
			$_i   = strpos( $_str, $this->sep );
			$_nam = substr( $_str, 0, $_i );
			$_str = substr( $_str, ( $_i + 1 ) );
			
			$_val[$_nam] = $this->loadDataFromString( $_str );
		}
		
		return ( $_val );
	}

	/**
	 * @access public
	 */		
	function loadVarFromString( &$_str, $_pre = '' )
	{
		$_i   = strpos( $_str, $this->sep );
		$_nam = substr( $_str, 0, $_i );
		$_str = substr( $_str, ( $_i + 1 ) );
		
		$GLOBALS["$_pre$_nam"] = $this->loadDataFromString( $_str );
	}
	
	/**
	 * @access public
	 */	
	function condiLoadVarFromString( &$_str, $_pre = '' )
	{
		$_i   = strpos( $_str, $this->sep );
		$_nam = substr( $_str, 0, $_i );
		$_str = substr( $_str, ( $_i + 1 ) );
		$_v   = $this->loadDataFromString( $_str );
		
		if ( !$GLOBALS["$_pre$_nam"] )
			$GLOBALS["$_pre$_nam"] = $_v;
	}
	
	/**
	 * @access public
	 */	
	function loadVarsFromString( &$_str, $_pre = '' )
	{
		if ( is_array( $_str ) )
		{
			$_x   = '';
			$_len = count( $_str );
			
			for ( $_i = 0; $_i < $_len; $_i++ )
				$_x .= "$_str[$_i]\n";
			
			$_str = $_x;
			unset( $_x );
		}
		
		while ( $_str && ( $_str != "\n" ) )
			$this->loadVarFromString( $_str, $_pre );
	}
	
	/**
	 * @access public
	 */	
	function condiLoadVarsFromString( &$_str, $_pre = '' )
	{
		if ( is_array( $_str ) )
		{
			$_x   = '';
			$_len = count( $_str );
			
			for ( $_i = 0; $_i < $_len; $_i++ )
				$_x .= "$_str[$_i]\n" ;
			
			$_str = $_x;
			unset( $_x );
		}
		
		while ( $_str && ( $_str != "\n" ) )
			$this->condiLoadVarFromString( $_str,$_pre );
	}
	
	
	// helper
	
	function unsetVars( $list, $pre = '' )
	{
		$arr = explode( ' ', $list );
		$len = count( $arr );
		
		for ( $i = 0; $i < $len; $i++ )
		{
			$tmp = $arr[$i];
			$tmp = 'unset($GLOBALS[\'' . $pre . $tmp . '\'])';
			
			eval( " $tmp ; " ); 
		}
	}
} // END OF Serial

?>
