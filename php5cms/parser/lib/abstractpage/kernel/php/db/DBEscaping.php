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
 * Extremely Versitile Escaping
 * 
 * Using a hash array, a user may escape or un-escape any string using any string.
 * Also unescape it, or customize it for any database, or other application.
 *
 * @package db
 */
 
class DBEscaping extends PEAR
{
	/**
	 * @access public
	 */
	var $str_buff;
	
	/**
	 * @access public
	 */
	var $escaped;
	
	/**
	 * @access public
	 */
	var $esc_with;
	
	/**
	 * @access public
	 */
	var $sybase_magic;
	
	/**
	 * @access public
	 */
	var $gpc_magic;
	
	/**
	 * Associative array of arrays of escape patterns. 
	 * for instance, "ANSI_KWDS", "OR_KWDS", "PHP_SYBASE", "PHP_MYSQL"
	 * add others as you see fit
 	 *
	 * Always set the '\' character as the first one in your array,
	 * and only once.
	 *
	 * @access public
	 */
	var $escapes = array( 
		"OR_KWDS" => array( // ( 'ORACLE KEYWORDS'->escape the following: '\','"', and other 'keywords in list below )
			"\\"    => "\\",
			"\""    => "\\",
			"'"     => "'",
			"AND"   => "\\",
			"&"     => "\\",
			"OR"    => "\\",
			"|"     => "\\",
			"ACCUM" => "\\",
			","     => "\\",
			"MINUS" => "\\",
			"-"     => "\\",
			";"     => "\\",
			"\$"    => "\\",
			"!"     => "\\",
			"?"     => "\\",
			">"     => "\\",
			"*"     => "\\",
			"#"     => "\\",
			":"     => "\\",
			"%"     => "\\",
			"_"     => "\\",
			"("     => "\\",
			")"     => "\\",
			"["     => "\\",
			"]"     => "\\",
			"{"     => "\\",
			"}"     => "\\",  
			"EXEC"  => "\\",
			"@"     => "\\",
			"SQE"   => "\\",
			"SYN"   => "\\",
			"PT"    => "\\",
			"RT"    => "\\",
			"TT"    => "\\",
			"BT"    => "\\",
			"NT"    => "\\",
			"BTG"   => "\\",
			"NTG"   => "\\",
			"BTP"   => "\\",
			"NTP"   => "\\"
		)
	);
	
	
	/**
	 * @access public
	 */
  	function Esc_DB_Str( &$db_string, $esc_set, $test = false )
	{
    	// REMEMBER!! - Always set the '\' character as the first one in your array, and only once
    	$ret_val = false;
    
		if ( !isset( $this->escapes[$esc_set] )    || 
			 !is_array( $this->escapes[$esc_set] ) || 
			 !isset( $db_string) || 
			 !is_string( $db_string ) )
		{
      		;
    	}
		else
		{
      		$num = 0;
      		reset( $this->escapes[$esc_set] );
     	 	$this->str_buff = $db_string;
			
			while ( $key_val = each( $this->escapes[$esc_set] ) )
			{
        		$this->escaped  = $key_val[0];
        		$this->esc_with = $key_val[1];
        
				if ( isset( $this->escaped ) && isset( $this->esc_with ) )
				{
          			$this->_insert_esc_str();
          			$ret_val = true;
          
		  			if ( isset( $test ) && $test == true )
					{
						$key = $this->escaped;
						$val = $this->esc_with;
						echo( "<pre><br>\n$num key=$key val=$val<br>\n<br>\n$this->str_buff<br>\n<br>\n</pre>" );
						$num++;
					}
				}
			}
		}
	
		if ( $ret_val )
			$db_string = $this->str_buff;
    
		return $ret_val;
  	}
  
  	/**
	 * @access public
	 */
  	function Unesc_DB_Str( &$db_string, $esc_set, $test = false )
	{
		// REMEMBER!! - Always set the '\' character as the first one in your array, and only once
    	$reversed_escapes = array_reverse( $this->escapes[$esc_set] );
    	$ret_val = false;
		
    	if( !isset( $reversed_escapes ) || !is_array( $reversed_escapes ) || !isset( $db_string ) || !is_string( $db_string ) )
		{
      		;
    	}
		else
		{
      		$num = 0;
      		reset( $reversed_escapes );
      		$this->str_buff = $db_string;
      
	  		while ( $key_val = each( $reversed_escapes ) )
			{
        		$this->escaped  = $key_val[0];
        		$this->esc_with = $key_val[1];
        		
				if ( isset( $this->escaped ) && isset( $this->esc_with ) )
				{
          			$this->_remove_esc_str();
          			$ret_val = true;
          
		  			if ( isset( $test ) && $test == true )
					{
            			$key = $this->escaped;
            			$val = $this->esc_with;
            
						echo( "<pre><br>\n$num key=$key val=$val<br>\n<br>\n$this->str_buff<br>\n<br>\n</pre>" );
						$num++;
					}
				}
			}
		}
		
		if ( $ret_val )
			$db_string = $this->str_buff;

		return $ret_val;
	}

	
	// private methods

	/**
	 * Assumes target and escaped char strings are set, and escaped char string != ""
	 *
	 * @access private
	 */
	function _insert_esc_str()
	{
    	$parts    = explode( strtolower( $this->escaped ), strtolower( $this->str_buff ) );
    	$pos      = 0;
    	$tmp_str  ="";
    	$find_len = strlen( $this->escaped );
    
		for ( $index = 0; isset( $parts[$index] ); $index++ )
		{
      		$part_len  = strlen( $parts[$index] );
      		$tmp_str  .= substr( $this->str_buff, $pos, $part_len );
      		$pos += $part_len;
			
      		if ( isset( $parts[$index + 1] ) )
			{
        		$tmp_str .= $this->esc_with;
        		$tmp_str .= substr( $this->str_buff, $pos, $find_len );
        		$pos += $find_len;
      		}
    	}
    
		$this->str_buff = $tmp_str;
  	}

	/**
	 * @access private
	 */  
  	function _remove_esc_str()
	{
    	$find_str     = $this->esc_with . $this->escaped;
    	$parts        = explode( strtolower( $find_str ), strtolower( $this->str_buff ) );
    	$pos          = 0;
    	$tmp_str      = "";
    	$esc_with_len = strlen( $this->esc_with );
    	$escaped_len  = strlen( $this->escaped  );
    	$find_len     = strlen( $find_str );
    
		for ( $index = 0; isset( $parts[$index] ); $index++ )
		{
      		$part_len = strlen( $parts[$index] );
      		$tmp_str .= substr( $this->str_buff, $pos, $part_len );
      		$pos += $part_len;
      
	  		if ( isset( $parts[$index + 1] ) )
			{
        		$tmp_str .= substr( $this->str_buff, $pos + $esc_with_len, $escaped_len );
        		$pos += $find_len;
      		}
    	}
    
		$this->str_buff = $tmp_str;
  	}
} // END OF DBEscaping

?>
