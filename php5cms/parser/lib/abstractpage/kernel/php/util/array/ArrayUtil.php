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
 * Static helper functions.
 *
 * @package util_array
 */
 
class ArrayUtil
{
	/**
	 * Insert elements to array.
	 *
	 * @access public
	 * @static
	 */
	function insert( $array = '', $position = '' , $elements = '' )            
    { 
        if ( $position == '' || $array == '' || $elements == '' || $position < 1 || $position > count( $array ) + 1 ) 
        { 
            return PEAR::raiseError( "No arguments." );
        } 
        else 
        {         
            $left   = array_slice( $array, 0, $position - 1 ); 
            $right  = array_slice( $array, $position - 1 ); 
            $insert = explode( '\,', $elements );                                
            $array  = array_merge( $left, $insert, $right ); 
            
			unset( $left, $right, $insert ); 
        } 
         
        unset( $position, $elements ); 
        return $array; 
    } 

	/**
	 * Delete elements of an arrays.
	 *
	 * @access public
	 * @static
	 */
	function delete( $array = '', $from = '', $to = '' ) 
    { 
        if ( $to == '' )
			$to = $from; 
        
		if ( $array == '' || $from == '' || $to > count( $array ) || $to < 1 || $from > count( $array ) ) 
        {                           
			return PEAR::raiseError( "No arguments." );
        } 
        else if ( $to < $from ) 
        { 
            return PEAR::raiseError( "Wrong parameter relationship." );
        } 
        else 
        {                 
            $left  = array_slice( $array, 0, $from - 1 );
            $right = array_slice( $array, $to );
            $array = array_merge( $left, $right );
			
            unset( $left, $right ); 
        } 
         
        unset ( $from, $to );
        return $array;
    } 

	/**
	 * Delete a key from an array.
	 *
 	 * @return Array
 	 * @param aInput Array
 	 * @param deleteKey mixed
	 * @static
 	 */
	function deleteKey( $aInput, $deleteKey )
 	{
  		$aOutput = array();
  
  		if ( !is_array( $aInput ) )
    		return false;

  		foreach ( $aInput as $key => $value )
   		{
    		if ( $key != $deleteKey )
      			$aOutput[$key] = $value;
   		}
   
   		return $aOutput;
 	}
	
	/**
	 * Move an element of your arrays to another position.
	 *
	 * @access public
	 * @static
	 */
    function move( $array = '', $from = '', $to = '' ) 
    {                                            
        if ( $array == ''|| $from == ''|| $to == '' || $to > count( $array ) || $to < 1 || $from > count( $array ) || $from < 1 ) 
        {                               
            return PEAR::raiseError( "No arguments." );
        } 
        else 
        {     
            $hopper = $array[$from - 1];
            $array  = ArrayUtil::delete( $array, $from ); 
            $array  = ArrayUtil::insert( $array, $to, $hopper );         
        } 

        unset( $hopper, $from, $to ); 
        return $array; 
    } 
     
	/**
	 * Replace an element of your array.
	 *
	 * @access public
	 * @static
	 */
    function replace( $array = '', $position = '', $new_element = ' ' ) 
    {                                    
        if ($array == '' || $position == '' || $position > count( $array ) || $position < 1 ) 
        {                       
            return PEAR::raiseError( "No arguments." );
        } 
        else 
        { 
            $array = ArrayUtil::insert( $array, $position, $new_element ); 
            $array = ArrayUtil::delete( $array, $position + count( explode( '\,', $new_element ) ) ); 
        } 

        unset( $position, $new_element );                     
        return $array; 
    } 
	
	/**
	 * @access public
	 * @static
	 */
	function explode( $separator, $string, $limit = 0 ) 
	{
		$separator = join( '', $separator );

		/*
		ob_start();
		var_dump( $separator );
		$out = ob_get_contents();
		ob_end_clean();
		$out = '<pre>' . str_replace( "=>\n  ", '=>', $out ) . '</pre>';
		echo $out;
		*/

		return split( '[' . $separator . ']', $string );
	}

	/**
	 * @access public
	 * @static
	 */
	function maxSizeOfLevel( $array, $level ) 
	{
		if ( !is_array( $array ) ) 
			return 0;
		
		if ( $level == 1 ) 
			return sizeof( $array );
		
		$ret = 0;
		while ( list( $k ) = each( $array ) ) 
		{
			$t = sizeof( $array[$k] );
			
			if ( $t > $ret ) 
				$ret = $t;
		}

		return $ret;
	}

	/**
	 * @access public
	 * @static
	 */
	function merge() 
	{
		$ret = array();
		
		do 
		{
			$params = func_get_args();
			
			if ( !is_array( $params ) || empty( $params ) ) 
				break;
			
			while ( list(,$p) = each( $params ) ) 
			{
				if ( !is_array( $p ) || empty( $p ) ) 
					continue;
				
				while ( list( $k ) = each( $p ) ) 
					$ret[$k] = &$p[$k];
			}
		} while ( false );
		
		return $ret;
	}

	/**
	 * A more readable option that "print_r".
	 *
	 * @access public
	 * @static
	 */
	function print_a( $TheArray ) 
	{
		echo "<table border=1>\n"; 
		$Keys = array_keys( $TheArray ); 
		
		foreach( $Keys as $OneKey ) 
    	{ 
      		echo "<tr>\n"; 
			echo "<td bgcolor='#727450'>"; 
			echo "<B>" . $OneKey . "</B>"; 
			echo "</td>\n"; 
			echo "<td bgcolor='#C4C2A6'>"; 
        
			if ( is_array( $TheArray[$OneKey] ) ) 
          		ArrayUtil::print_a( $TheArray[$OneKey] ); 
        	else 
          		echo $TheArray[$OneKey]; 
      
	  		echo "</td>\n"; 
      		echo "</tr>\n"; 
    	} 
    
		echo "</table>\n"; 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function arrayToCode( &$array, $name = '$array' ) 
	{
		$ret = '';
		
		switch ( gettype( $array ) ) 
		{
			case 'object':
				$ret .= "{$name} = '';\n";
				break;
			
			case 'array':
				if ( sizeof( $array ) == 0 ) 
				{
					$ret .= "{$name} = array();\n";
				} 
				else 
				{
					while ( list( $key, $val ) = each( $array ) ) 
						$ret .= ArrayUtil::arrayToCode( $val, "{$name}['{$key}']" );
				}
				
				break;
			
			case 'integer':
			
			case 'double':
				$ret .= "{$name} = {$array};\n";
				break;
			
			default:
				$ret .= "{$name} = \"" . addSlashes($array) . "\";\n";
		}
		
		return $ret;
	}

	/**
	 * @access public
	 * @static
	 */
	function inArray( $needle, $haystack, $ignoreCase = true, $ignoreSpaces = true ) 
	{
		if ( !is_array( $haystack ) ) 
			return false;
			
		if ( $ignoreCase )   
			$needle = strtolower( $needle );
		
		if ( $ignoreSpaces ) 
			$needle = trim( $needle );
		
		reset( $haystack );
		while ( list( $k, $v ) = each( $haystack ) ) 
		{
			if ( $ignoreCase )   
				$v = strtolower( $v );
				
			if ( $ignoreSpaces ) 
				$v = trim( $v );
					
			if ( $needle == $v ) 
				return true;
		}

		return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function max( $value_arr )
	{
		$max = $value_arr[0];
		$i = 0;
		$arr_index = $i;
		
		foreach ( $value_arr as $val )
		{
            if ( $val > $max )
			{
                $max = $val;
                $arr_index = $i;
            }
			
            $i++;
        }
		
        $return["arr_index"] = $arr_index;
        $return["max_val"]   = $max;

        return $return;
    }

	/**
	 * @access public
	 * @static
	 */    
    function min( $value_arr )
	{
        $min = $value_arr[0];
        $i = 0;
        $arr_index = $i;
        
		foreach ( $value_arr as $val )
		{
            if ( $val < $min )
			{
                $min = $val;
                $arr_index = $i;
            }
			
            $i++;
        }
		
        $return["arr_index"] = $arr_index;
        $return["min_val"]   = $min;
        
		return $return;
    }

	/**
	 * @access public
	 * @static
	 */
	function randVal( $array ) 
	{
		if ( is_array( $array ) ) 
		{
			$numArgs = sizeof( $array );
			
			if ( $numArgs > 1 ) 
			{
				mt_srand( (double)microtime() * 1000000 );
				$randVal = mt_rand( 1, $numArgs );
				$ret = $array[$randVal -1];
			} 
			else if ( $numArgs == 1 ) 
			{
				$ret = $array[0];
			} 
			else 
			{
				$ret = '';
			}
		} 
		else 
		{
			$ret = '';
		}

		return $ret;
	}

	/**
	 * @access public
	 * @static
	 */
	function getLastKey( $array ) 
	{
		end( $array );
		return key( $array );
	}

	/**
	 * @access public
	 * @static
	 */	
    function numericSum( $val_array = array() )
	{
        $sum = 0;
        
		foreach ( $val_array as $val )
		{
			if ( is_numeric( $val ) )
            	$sum += $val;
		}
		        
		return $sum;
    }

	/**
	 * @access public
	 * @static
	 */
	function setPos( &$array, $findKey ) 
	{
		if ( is_numeric( $findKey ) ) 
			$findKey = (int)$findKey;
		
		reset( $array );
		
		if ( key( $array ) === $findKey ) 
			return;
		
		while ( list( $key ) = each( $array ) ) 
		{
			if ( key( $array ) === $findKey ) 
				return true;
		}

		return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function getPos( $array, $find, $findKey = true, $ignoreCase = false ) 
	{
		if ( !is_array( $array ) || empty( $array ) ) 
			return false;
			
		reset( $array );
		$i = 0;
		
		if ( $ignoreCase ) 
		{
			while ( list( $k ) = each( $array ) ) 
			{
				if ( $findKey ) 
				{
					$t = strtolower( $k );
					
					if ( $t === $find ) 
						return $i;
				} 
				else 
				{
					$t = strtolower( $array[$k] );
					
					if ( $t === $find ) 
						return $i;
				}
				
				$i++;
			}
		} 
		else 
		{
			while ( list( $k ) = each( $array ) ) 
			{
				if ( $findKey ) 
				{
					if ( $k === $find ) 
						return $i;
				} 
				else 
				{
					if ( $array[$k] === $find ) 
						return $i;
				}
				
				$i++;
			}
		}
		
		return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function guessType( $array ) 
	{
		if ( !is_array( $array ) ) 
			return null;
			
		if ( sizeof( $array ) == 0 ) 
			return false;
			
		reset( $array );
		
		if ( !is_numeric( key( $array ) ) ) 
			return 'hash';
		
		$i = 0;
		while ( list( $k ) = each( $array ) ) 
		{
			if ( $i++ > 100 ) 
				break;
			
			if ( !is_numeric( $k ) ) 
				return 'hash';
		}

		if ( $i < 100 ) 
			return 'vector';
			
		reset( $array );
		
		if ( key( $array ) === 0 ) 
			return 'vector_guess';
			
		return 'hash_guess';
	}

	/**
	 * @access public
	 * @static
	 */
	function &padding( $array, $pad_string = ' ', $pad_length = 0, $pad_type = STR_PAD_RIGHT ) 
	{
		if ( !is_array( $array ) ) 
			return;
		
		if ( $pad_length <= 0 ) 
		{
			reset( $array );
			while ( list( $key ) = each( $array ) ) 
				$pad_length = max( strlen( $array[$key] ), $pad_length );
		}

		reset( $array );
		while ( list( $key ) = each( $array ) ) 
			$array[$key] = str_pad( $array[$key], $pad_length, $pad_string, $pad_type );
		
		return $array;
	}

	/**
	 * @access public
	 * @static
	 */
	function &hashKeysToLower( &$hashArray ) 
	{
		$newHash = array();
		
		if ( !is_array( $hashArray ) ) 
			return $newHash;
		
		reset( $hashArray );
		while ( list( $key ) = each( $hashArray ) ) 
			$newHash[strtolower( $key )] = $hashArray[$key];

		return $newHash;
	}

	/**
	 * @access public
	 * @static
	 */	
	function &hashKeysToUpper( &$hashArray ) 
	{
		$newHash = array();
		
		if ( !is_array( $hashArray ) ) 
			return $newHash;
		
		reset( $hashArray );
		while ( list( $key ) = each( $hashArray ) ) 
			$newHash[strtoupper( $key )] = $hashArray[$key];

		return $newHash;
	}

	/**
	 * @access public
	 * @static
	 */
	function splitKeyValue( $array ) 
	{
		return array( array_keys( $array ), array_values( $array ) );
	}
	
	/**
	 * Recursively prints array contents - works goodly on associative arrays.
	 *
	 * @static
	 */
	function arrayContents( &$array, $max_depth, $depth = 0, $ignore_ints = false ) 
	{
		$string = $indent = "";
	
		for ( $i = 0; $i < $depth; $i++ ) 
			$indent .= "\t";
	
		if ( !empty( $max_depth ) && $depth >= $max_depth )
			return $indent."[Max Depth Reached]\n";
	
		if ( count( $array ) == 0 ) 
			return $indent . "[Empty]\n";
	
		reset($array);
		while ( list( $key, $value ) = each( $array ) ) 
		{
			$print_key = str_replace( "\n", "\\n", str_replace( "\r", "\\r", str_replace( "\t", "\\t", addslashes( $key ) ) ) );
		
			if ( $ignore_ints && gettype( $key ) == "integer" ) 
				continue;
		
			$type = gettype( $value );
		
			if ( $type == "array" || $type == "object" ) 
			{
				$string .= $indent
					. ( ( is_string( $key ) )? "\"$print_key\"" :  $key ) . " => "
					. ( ( $type == "array"  )? "array (\n" : "" )
					. ( ( $type == "object" )? "new " . get_class( $value ) . " Object (\n" : "" );
			
				$string .= ArrayUtil::arrayContents( $value, $max_depth, $depth + 1,  $ignore_ints );
				$string .= $indent . "),\n";
			} 
			else 
			{
				if ( is_string( $value ) ) 
					$value = str_replace( "\n", "\\n", str_replace( "\r", "\\r", str_replace( "\t", "\\t", addslashes( $value ) ) ) );
				
				$string .= $indent
					.  ( ( is_string( $key )   )? "\"$print_key\"" : $key   ) . " => "
					.  ( ( is_string( $value ) )? "\"$value\""     : $value ) . ",\n";
			}
		}
	
		$string[strlen( $string ) - 2] = " ";
		return $string;
	}
	
	/**
 	 * Strips the slashes from an entire associative array.
	 *
	 * @access public
	 * @static
	 */
	function arrayStripSlashes( &$array, $strip_keys = false ) 
	{
		if ( is_string( $array ) ) 
			return stripslashes( $array );
	
		$keys_to_replace = array();
	
		foreach ( $array as $key => $value ) 
		{
			if ( is_string( $value ) ) 
				$array[$key] = stripslashes( $value );
			else if ( is_array( $value ) ) 
				ArrayUtil::arrayStripSlashes( $array[$key], $strip_keys );
		
			if ( $strip_keys && $key != ( $stripped_key = stripslashes( $key ) ) )
				$keys_to_replace[$key] = $stripped_key;
		}
		
		// now replace any of the keys that needed strip slashing
		foreach ( $keys_to_replace as $from => $to ) 
		{
			$array[$to] = &$array[$from];
			unset( $array[$from] );
		}
	
		return $array;
	}
	
	/**
 	 * Adds the slashes from an entire associative array.
	 *
	 * @access public
	 * @static
	 */
	function arrayAddSlashes( &$array ) 
	{
		if ( is_string( $array ) ) 
			return addslashes( $array );
	
		$keys_to_replace = array();
	
		foreach ( $array as $key => $value ) 
		{
			if ( is_string( $value ) ) 
				$array[$key] = addslashes( $value );
			else if ( is_array( $value ) ) 
				ArrayUtil::arrayAddSlashes( $array[$key] );
		}
		
		// now replace any of the keys that needed strip adding
		foreach ( $keys_to_replace as $from => $to ) 
		{
			$array[$to] = &$array[$from];
			unset( $array[$from] );
		}
	
		return $array;
	}
	
	/**
 	 * Trim an entire associative array.
	 *
	 * @access public
	 * @static
	 */
	function arrayTrim( &$array, $trim_keys = false ) 
	{
		if ( is_string( $array ) ) 
			return trim( $array );
	
		$keys_to_replace = array();
	
		foreach ( $array as $key => $value ) 
		{
			if ( is_string( $value ) ) 
				$array[$key] = trim( $value );
			else if ( is_array( $value ) ) 
				ArrayUtil::arrayTrim( $array[$key], $trim_keys );
		
			if ( $trim_keys && $key != ( $trimed_key = trim( $key ) ) )
				$keys_to_replace[$key] = $trimed_key;
		}
		
		// now replace any of the keys that needed strip slashing
		foreach ( $keys_to_replace as $from => $to ) 
		{
			$array[$to] = &$array[$from];
			unset( $array[$from] );
		}
	
		return $array;
	}
	
	/**
	 * Merges two arrays recursively, overruling the values of the first array
	 * in case of identical keys, ie. keeping the values of the second.
	 *
	 * @access public
	 * @static
	 */
	function arrayMergeRecursiveOverrule( $arr0, $arr1 ) 
	{
		reset( $arr1 );
		while ( list( $key, $val ) = each( $arr1 ) ) 
		{
			if ( is_array( $arr0[$key] ) ) 
			{
				if ( is_array( $arr1[$key] ) )
					$arr0[$key] = ArrayUtil::arrayMergeRecursiveOverrule( $arr0[$key], $arr1[$key] );
			} 
			else 
			{
				$arr0[$key] = $val;
			}
		}
		
		reset( $arr0 );
		return $arr0;
	}
	
	/** 
	 * Implodes a multidim-array into GET-parameters (eg. &param[key][key2]=value2&param[key][key3]=value3).
	 *
	 * @access public
	 * @static
	 */
	function implodeArrayForUrl( $name, $theArray, $str = "", $skipBlank = 0 )	
	{	
		if ( is_array( $theArray ) )	
		{
			reset( $theArray );
			while ( list( $Akey, $AVal ) = each( $theArray ) )	
			{
				$thisKeyName = $name? $name . "[" . $Akey . "]" : $Akey;

				if ( is_array( $AVal ) )	
				{
					$str = ArrayUtil::implodeArrayForUrl( $thisKeyName, $AVal, $str, $skipBlank );
				} 
				else 
				{
					if ( !$skipBlank || strcmp( $AVal, "" ) )	
						$str .= "&" . $thisKeyName . "=" . rawurlencode( stripslashes( $AVal ) ); // strips slashes because HTTP_POST_VARS / GET_VARS input is with slashes...
				}
			}
		}
		
		return $str;
	}
	
	/**
	 * Removes the value $cmpValue from the $array if found there. Returns the modified array.
	 *
	 * @access public
	 * @static
	 */
	function removeArrayEntryByValue( $array, $cmpValue )	
	{
		if ( is_array( $array ) )	
		{
			reset( $array );
			while ( list( $k, $v ) = each( $array ) )	
			{
				if ( is_array( $v ) )	
				{
					$array[$k] = ArrayUtil::removeArrayEntryByValue( $v, $cmpValue );
				} 
				else 
				{
					if ( !strcmp( $v, $cmpValue ) )
						unset( $array[$k] );
				}
			}
		}
		
		reset( $array );
		return $array;
	}
	
	/**
	 * Returns true if $array1 is identical to $array2.
	 *
	 * @access public
	 * @static
	 */
	function equalArrays( $array1, $array2 ) 
	{
		if ( !( is_array( $array1 ) || is_object( $array1 ) ) || !( is_array( $array1 ) || is_object( $array2 ) ) ) 
			return $array1 == $array2;
	
		reset( $array1 ); 
		reset( $array2 );
	
		while ( 1 ) 
		{
			$s1 = each( $array1 ); 
			$s2 = each( $array2 );
		
			if ( $s1 === false && $s2 === false ) 
				return true;
		
			if ( $s1 === false xor $s2 === false ) 
				return false;
		
			list( $k1, $v1 ) = $s1;
			list( $k2, $v2 ) = $s2;
		
			if ( $k1 != $k2 ) 
				return false;
		
			if ( ( $t = gettype( $v1 ) ) != gettype( $v2 ) ) 
				return false;
			
			switch ( $t ) 
			{
				case "array":
					if ( !ArrayUtil::equalArrays( $v1, $v2 ) ) 
						return false;
				
				case "object":
					if ( !get_class( $v1 ) == get_class( $v2 ) ) 
						return false;
			
					if ( !ArrayUtil::equalArrays( $v1, $v2 ) ) 
						return false;
		
				default:
					if ( $v1 != $v2 ) 
						return false;
					
					break;
			}
		}
  
  		return true;
	}
	
 	/**
	 * Remove all "" entries in a given array.
	 *
	 * @access public
	 * @static
	 */
	function eraseBlankEntries( $array )
	{
    	foreach ( $array as $key => $value )
		{
        	if ( $value == '' ) 
            	unset( $array[$key] );
    	}
    	
		return $array;
	}
	
 	/**
	 * Similar to above, but works on what you sent it.
	 *
	 * @access public
	 * @static
	 */
	function &arrayClearBlanks( &$array )
	{
    	foreach ( $array as $key => $value )  
		{
        	if ( $value == "" )
            	unset( $array[$key] );
    	}
    
		return $array;
	}
	
	/**
	 * Returns 1, 0 or -1 depending on which array should be sorted "first".
	 *
	 * @access public
	 * @static
	 */
	function arraySortCompare( $array1, $array2 ) 
	{
		if ( !( is_array( $array1 ) || is_object( $array1 ) ) || !( is_array( $array1 ) || is_object( $array2 ) ) ) 
		{
			if ( ( is_array( $array1 ) || is_object( $array1 ) ) && !( is_array( $array2 ) || is_object( $array2 ) ) ) 
				return -1;
		
			if ( ( is_array( $array2 ) || is_object( $array2 ) ) && !( is_array( $array1 ) || is_object( $array1 ) ) ) 
				return 1;
		
			if ( is_string( $array1 ) || is_string( $array2 ) ) 
				return strcmp( $array1, $array2 );
		
			if ( $array1 < $array2 ) 
				return -1;
		
			if ( $array1 > $array2 ) 
				return 1;
		
			return 0;
		}
	
		reset( $array1 ); 
		reset( $array2 );
	
		while ( 1 ) 
		{
			$s1 = each( $array1 ); 
			$s2 = each( $array2 );
			
			if ( $s1 === false && $s2 === false ) 
				return  0;
			
			if ( $s1 === false && $s2 !== false ) 
				return  1;
			
			if ( $s1 !== false && $s2 === false ) 
				return -1;
		
			list($k1, $v1) = $s1;
			list($k2, $v2) = $s2;
			
			if ( ( $r = ArrayUtil::arraySortCompare( $v1, $v2 ) ) != 0 )
				return $r;
		}
	
		return 0;
	}
	
	/**
	 * Takes two plain arrays and compares their contents.
	 * It returns two array - a list of elements missing from
	 * second array, and a list of those missing from the first.
	 *
	 * @access public
	 * @static
	 */
	function arrayCompare( &$n, &$o ) 
	{
		if ( !is_array( $n ) ) 
			$n = array();
	
		if ( !is_array( $o ) ) 
			$o = array();
	
		$c = array_intersect( $n, $o );
		return array( array_diff( $n, $c ), array_diff( $o, $c ) );
	}
	
	/**
	 * Merges 2 multi-dimensional arrays preserving the keys and returns
	 * the merged array. If the same keys exist the values in array2 are used.
	 *
	 * @access public
	 * @static
	 */
	function multiArrayMerge( $array1, $array2 ) 
	{
		foreach ( $array2 as $key => $data ) 
		{
			if ( is_array( $data ) && $array1[$key] ) 
				ArrayUtil::multiArrayMerge( &$array1[$key], $array2[$key] );
			else 
				$array1[$key] = $data;
		}
	
		return $array1;
	}
	
	/**
	 * Searches an array for a value and if it finds it, 
	 * removes that element - only the first instance of it.
	 * Doesn't really work on associative arrays.
	 *
	 * @access public
	 * @static
	 */
	function arrayRemoveElement( $v, &$a ) 
	{
		if ( in_array( $v, $a ) )
			array_splice( $a, array_search( $v, $a ), 1 );
	}

	/**
	 * Returns true if there are any common elements between
	 * two array, or false if there are none.
	 *
	 * @access public
	 * @static
	 */
	function arrayAnyCommonElements( &$a1, &$a2 ) 
	{
		reset( $a1 );
		while ( list(,$v) = each( $a1 ) ) 
		{
			if ( in_array( $v, $a2 ) ) 
				return true;
		}
			
		return false;
	}

	/**
	 * Appends the second array to the first.
	 * Doesn't really work on associative arrays.
	 *
	 * @access public
	 * @static
	 */
	function arrayAppend( &$one, $two ) 
	{
		if ( is_array( $one ) && is_array( $two ) ) 
		{
			for ( reset( $two ); ( $k = key( $two ) ) !== null; next( $two ) ) 
				$one[] = $two[$k];
		}
	}

	/**
	 * I use it for sorting the rows of MYSQL_ASSOC-results without the need to 
	 * query the database again... it helps when you have slow querys whose result only needs to be sorted again. 
	 * First transfer the result into an array like this: 
	 * while($row = mysql_fetch_array($result, MYSQL_ASSOC)) $DB_Array[] = $row; 
	 *
	 * Syntax: aasort($assoc_array, array("+first_key", "-second_key", etc..)); 
	 *
	 * Example: aasort($db_array, array("+ID", "-AGE", "+NAME")); 
	 * Where the "+" in front of the keys stands for "ASC" and "-" for "DESC". 
	 * 
	 * This sorts the array first ascending by "ID", then descending by "AGE" and 
	 * finally ascending by "NAME". 
	 *
	 * Note: the function does no error handling... so make sure all keys exist in the 
	 * array and they have a + or - as the first character. 
	 * To keep the $DB_Array between pages use sessions. 
	 *
	 * To use the function with other arrays, they must have the following format: 
	 * 
	 * $array[1] = array("key" => "value", "key" => "value", etc..); 
	 * $array[2] = array("key" => "value", "key" => "value", etc..); 
	 * $array[4] = array("key" => "value", "key" => "value", etc..); 
	 * etc.
	 *
	 * @access public
	 * @static
	 */ 
	function aasort( &$array, $args )
	{ 
    	foreach( $args as $arg )
		{ 
        	$order_field = substr( $arg, 1, strlen( $arg ) );  
        
			foreach ( $array as $array_row ) 
            	$sort_array[$order_field][] = $array_row[$order_field]; 
        
        	$sort_rule .= '$sort_array['.$order_field.'], '.( $arg[0] == "+" ? SORT_ASC : SORT_DESC ).','; 
    	} 
    
		eval ( "array_multisort($sort_rule".' &$array);' ); 
	}
	
	/**
	 * Take two one-dimensional array, step through the first looking for 
     * each element of the second. If any of the elements in the second 
     * array are not in the first, append them. Kind of an extended version 
     * of PHP4's array_merge() function.
	 *
	 * @access public
	 * @static
	 */ 
	function arrayExpand( $base = 0, $extra = 0 )
	{  
    	if ( !is_array( $base ) ) 
        	return false; 

    	$ext_arr = ( is_array( $extra ) )? $extra : array( $extra ); 

    	for ( $i = 0; $i < sizeof( $ext_arr ); $i++ )
		{ 
        	if ( !in_array( $ext_arr[$i], $base ) ) 
            	$base[] = $ext_arr[$i]; 
    	} 

    	return $base; 
	}
	
	/**
	 * arrayRemoveDuplicates() removes duplicate values from an array. It takes 
	 * input array and returns a new array without duplicate values. 
	 * arrayRemoveDuplicates() sorts the input array in case if is_sorted flag is 
	 * "false" (default). Two elements are considered equal if and only if 
 	 * (string) $elem1 === (string) $elem2
	 *
	 * @access public
	 * @static
	 */
	function arrayRemoveDuplicates( $arg_array, $is_sorted = false ) 
  	{ 
    	if ( !$is_sorted ) 
      		sort( $arg_array ); 
    
		if ( count( $arg_array ) > 1 ) 
    	{ 
      		$res = array( $last = $arg_array[0] ); 
      
	  		for ( $i = 1; $i < count( $arg_array ); $i++ ) 
      		{ 
        		if ( !( (string)$arg_array[$i] === (string)$last ) ) 
        		{ 
          			array_push( $res, $arg_array[$i] ); 
          			$last = $arg_array[$i]; 
        		} 
      		} 
      
	  		return $res; 
    	}
		else
		{
      		return $arg_array;
		}
  	}
	
	/**
	 * This function will return $array "compressed", meaning that it 
	 * will change its numeric keys in order to make it a gapless list. 
	 * For Example, when the input is an array having 2, 3, 7, and 197 
	 * as keys, those keys will be respectively changed to 2, 3, 4 and 5. 
	 * String keys (even if it is a string consisting of a number) will 
	 * be returned unchanged. The input array may have any base (lowest key), 
	 * which will be kept. 
	 *
	 * @access public
	 * @static
	 */
	function arrayCompress( $array )
	{ 
    	if ( !is_array( $array ) ) 
        	return false; 
		
		// sorts the array physically by key, so the foreach will 
		// get the pairs in ascending key order 
		ksort( $array ); 
    
    	foreach ( $array as $key => $value )
		{ 
        	// makes sure it gets only the numeric keys 
        	if ( is_long( $key ) )
			{ 
            	// $expected is the key that should appear after 
            	// the last parsed key. If this key isn't equal to 
            	// expected, we make one that is. 
            	if ( isset( $expected ) && $expected != $key )
				{ 
                	$array[$expected++] = $array[$key]; 
                	unset( $array[$key] ); 
            	}
				else
				{ 
                	$expected = $key + 1; 
            	} 
        	} 
    	} 
    	
		return $array; 
	}
	
	/**
	 * @access public
	 * @static
	 */
	function array_sprintf()
	{   
		$args = func_get_args();
		$ar   = array_shift( $args );
		$str  = array_shift( $args );
		$etc  = '$ar["' . implode('"], $ar["', $args ) . '"]';
		
		return $args? eval( "return sprintf(\$str, $etc);" ) : sprintf( $str );
	}
	
    /**
     * There seemed to be no built in function that would merge two arrays recursively and clobber
     * any existing key/value pairs. Array_Merge() is not recursive, and array_merge_recursive
     * seemed to give unsatisfactory results... it would append duplicate key/values.
     *
     * So here's a cross between array_merge and array_merge_recursive.
     *
     * @param    array first array to be merged
     * @param    array second array to be merged
     * @return   array merged array
	 * @access   public
	 * @static
     */
    function arrayMergeClobber( $a1, $a2 )
    {
        if ( !is_array( $a1 ) || !is_array( $a2 ) ) 
			return false;
        
		$newarray = $a1;
        
		while ( list( $key, $val ) = each( $a2 ) )
        {
            if ( isset( $newarray[$key] ) && is_array( $val ) && is_array( $newarray[$key] ) )
            	$newarray[$key] = ArrayUtil::arrayMergeClobber( $newarray[$key], $val );
            else
                $newarray[$key] = $val;
        }
		
        return $newarray;
    }
	
	/**
	 * Implode a multi-dimensional array twice.
	 *
	 * @param  $s1 Seperator to use on the deeper arrays
	 * @param  $s2 Seperator to use on the higher arrays
	 * @param  $a The multi-dimensional array itself
	 * @param  $ev Function to apply to the values of the deeper array
	 * @return string The imploded
	 * @access public
	 * @static
	 */
	function doubleKeyImplode( $s1, $s2, $a, $ev = '' )
	{
		$a2 = array();
		
		foreach ( $a as $k => $v )
		{
			if ( $ev && function_exists( $ev ) )
				$v = $ev( $v );
			
			$a2[] = $k . $s1 . $v;
		}
		
		return implode( $s2, $a2 );
	}
	
	/**
	 * This function sorts an array according to a list of fields.
	 *
	 * @param  $a Array to sort
	 * @param  $fl Field list - array( 'field_name' => 'ASC|DESC', ... )
	 * @access public
	 * @static
	 */ 
	function arfsort( $a, $fl )
	{      
		if ( !is_array( $fl ) ) 
			$fl = array( $fl );
		
		foreach ( array_keys( $a ) as $k )
			$a[$k]['__key__'] = $k;
		
		$GLOBALS['__ARFSORT_LIST__'] = $fl;
		usort( $a, "__arfsort_func" );
		$a2 = array();
		
		foreach ( $a as $v )
		{
			$k = $v['__key__'];
			unset( $v['__key__'] );
			$a2[$k] = $v;
		}
		
		return $a2;
	}  
	
	/**
	 * This function sorts an array of classes according to a list of fields.
	 *
	 * @param  $a Array to sort
	 * @param  $fl Field list (in order of importance)
	 * @param  $pk Preserve keys (boolean)
	 * @access public
	 * @static
	 */
	function classort( $a, $fl, $pk = false )
	{      
		if ( !is_array( $fl ) ) 
			$fl = array( $fl );
		
		if ( $pk )
		{
			foreach( array_keys( $a ) as $k )
				$a[$k]->__key__ = $k;
		}
		
		$GLOBALS['__CLASSORT_LIST__'] = $fl;
		usort( $a, "__classort_func" );
		
		if ( $pk )
		{
			$a2 = array();
			
			foreach( $a as $v )
			{
				$k = $v->__key__;
				unset( $v->__key__ );
				$a2[$k] = $v;
			}
			
			return $a2;
		}
		
		return $a;
	}
	
    /**
     * Given an HTML type array field "example[key1][key2][key3]" breaks up the
     * keys so that they could be used to reference a regular PHP array.
     *
     * @access public
     * @param  string $field The field name to be examined.
	 * @static
     */
    function getArrayParts( $field, &$base, &$keys )
    {
        if ( preg_match( '|([^\[]*)((\[[^\[\]]*\])+)|', $field, $matches ) ) 
		{
            $base = $matches[1];
            $keys = explode( '][', $matches[2] );
            $keys[0] = substr( $keys[0], 1 );
            $keys[count($keys) - 1] = substr( $keys[count($keys) - 1], 0, strlen( $keys[count( $keys ) - 1] ) - 1 );
            
			return true;
        } 
		else 
		{
            return false;
        }
    }

    /**
     * Using an array of keys itarate through the array following the keys to
     * find the final key value. If a value is passed then set that value.
     *
     * @access public
     * @param  array &$array          The array to be used.
     * @param  array &$keys           The key path to follow as an array.
     * @param  optional array $value  If set the target element will have this
     *                               value set to it.
     *
     * @return mixed  The final value of the key path.
	 * @static
     */
    function getElement( &$array, &$keys, $value = null )
    {
        if ( count( $keys ) > 0 ) 
		{
            $key = array_shift( $keys );
			
            if ( isset( $array[$key] ) ) 
                return ArrayUtil::getElement( $array[$key], $keys, $value ); 
			else 
                return $array;
        } 
		else 
		{
            if ( !is_null( $value ) )
                $array = $value;
            
            return $array;
        }
    }

    /**
     * Returns only those elements of an array that match the
     * specified pattern.
     *
     * @access public
     * @param  array $array           An array to search for matches.
     * @param  string $pattern        A regular expression to search for.
     * @param  boolean $return_match  If true the matching elements of the
     *                                array are returned, otherwise the
     *                                elements that don't match are returned.
     *
     * @return array  An array with the matching/not matching elements.
	 * @static
     */
    function grep( $array, $pattern, $return_match = true )
    {
        $grepped = array_filter( $array, create_function( '$a', 'return preg_match( "/' . str_replace( '"', '\"', $pattern ) . '/", $a );' ) );
        
		if ( $return_match )
            return $grepped;
        else
            return array_values( array_diff( $array, $grepped ) );
    }
} // END OF ArrayUtil


/**    
 * Internal sorting function for arfsort().
 */    
function __arfsort_func( $a, $b )
{  
	foreach ( $GLOBALS['__ARFSORT_LIST__'] as $f => $sort )
	{
		if ( !isset( $a[$f] ) && isset( $a[$sort] ) )
		{
			$f = $sort;
			$sort = 'ASC';
		}
		else
		{
			$sort = strtoupper( $sort );
		}
			
		if ( is_numeric( $a[$f] ) && is_numeric( $b[$f] ) )
			$strc = ( $a[$f] == $b[$f]? 0 : ( $a[$f] > $b[$f] ? 1 : -1 ) );
		else
			$strc = strcmp( $a[$f], $b[$f] );
				
		if ( $strc != 0 ) 
			return ( $sort == 'DESC'? -$strc : $strc );
	}
		
	return 0;
}

/**
 * Internal sorting function for classort().
 */
function __classort_func( $a, $b )
{  
	foreach( $GLOBALS['__CLASSORT_LIST__'] as $f => $sort )
	{
		eval( "\$a_test = \$a->$f;" );
			
		if ( !isset( $a_test ) && isset( $a->$sort ) )
		{
			$f = $sort;
			$sort = 'ASC';
		}
		else
		{
			$sort = strtoupper( $sort );
		}
		
		eval( "\$a_test = \$a->$f; \$b_test = \$b->$f;" );
		
		if ( is_numeric( $a_test ) && is_numeric( $b_test ) )
			$strc = ( $a_test == $b_test? 0 : ( $a_test > $b_test ? 1 : -1 ) );
		else 
			$strc = strcmp( $a_test, $b_test );
			
		if ( $strc != 0 ) 
			return ( $sort == 'DESC'? -$strc : $strc );
	}
		
	return 0;
}
	
?>
