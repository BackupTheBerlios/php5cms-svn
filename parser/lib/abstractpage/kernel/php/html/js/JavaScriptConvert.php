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
|Authors: Tal Peer <tal@php.net>                                       |
|         Pierre-Alan Joye <paj@pearfr.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


define( 'JAVASCRIPT_ERROR_INVVAR', 502, true );


/**
 * A class for converting PHP variables into JavaScript variables.
 *
 * Usage example:
 *
 * $js = new JavaScriptConvert;
 * $a = array( 'foo', 'bar', 'buz', 1, 2, 3 );
 * $b = $js->convertArray( $a, 'arr', true  );
 * or
 * echo JavaScriptConvert::convertArray( $a );
 *
 * @package html_js
 */
 
class JavaScriptConvert extends PEAR
{
    /**
     * Used to terminate escape characters in strings, as javascript doesn't allow them.
     *
     * @param string $str the string to be processed
     * @return mixed the processed string
     * @access public
     */
    function escapeString( $str )
    {
        return addslashes( $str );
    }

    /**
     * Converts  a PHP variable into a JS variable.
     * You can safely provide strings, arrays or booleans as arguments for this function.
     *
     * @access public
     * @param  mixed   $var     the variable to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  if true, the JS var will be global
     * @return mixed   a PEAR_Error if no script was started or the converted variable
     */
    function convertVar( $var, $varname, $global = false )
    {
        $var_type = gettype( $var );
		
        switch ( $var_type ) 
		{
            case 'boolean':
                return JavaScriptConvert::convertBoolean( $var, $varname, $global );
                break;
				
            case 'integer':
                $ret = '';
                
				if ( $global )
                    $ret = 'var ';
                
                $ret .= $varname . ' = ' . $var;
                return $ret . "\n";
				
                break;
            
			case 'double':
                $ret = '';
                
				if ( $global )
                    $ret = 'var ';
                
                $ret .= $varname . ' = ' . $var;
                return $ret . "\n";
				
                break;
            
			case 'string':
                return JavaScriptConvert::convertString( $var, $varname, $global );
                break;
				
            case 'array':
                return JavaScriptConvert::convertArray( $var, $varname, $global );
                break;
				
            default:
                return PEAR::raiseError( "Invalid variable.", JAVASCRIPT_ERROR_INVVAR );
                break;
        }
    }
	
    /**
     * Converts  a PHP string into a JS string.
     *
     * @access public
     * @param  string  $str     the string to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  if true, the JS var will be global
     * @return mixed   a PEAR_Error if no script was started or the converted string
     */
    function convertString( $str, $varname, $global = false )
    {
        $var = '';
        
		if ( $global )
            $var = 'var ';
        
        $str  = JavaScriptConvert::escapeString( $str );
        $var .= $varname . ' = "' . $str . '"';
		
        return $var . "\n";
    }
	
    /**
     * Converts a PHP boolean variable into a JS boolean variable.
     *
     * @access public
     * @param  boolean $bool    the boolean variable
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  set to true to make the JS variable global
     * @return mixed   a PEAR_Error on error or a string  with the declaration
     */
    function convertBoolean( $bool, $varname, $global = false )
    {
        $var = '';
        
		if ( $global )
            $var = 'var ';
        
        $var .= $varname . ' = ';
        $var .= $bool? 'true' : 'false';

        return $var . "\n";
    }
	
    /**
     * Converts  a PHP array into a JS array, supports of multu-dimensional array.
     * Keeps keys as they are (associative arrays).
     *
     * @access public
     * @param  string  $arr     the array to convert
     * @param  string  $varname the variable name to declare
     * @param  boolean $global  if true, the JS var will be global
     * @param  int     $level   Not public, used for recursive calls
     * @return mixed   a PEAR_Error if no script was started or the converted array
     */
    function convertArray( $arr, $varname, $global = false, $level = 0 )
    {
        $var = '';
        
		if ( $global )
            $var = 'var ';
        
        if ( is_array( $arr ) )
		{
            $length  = sizeof( $arr );
            $var    .= $varname . ' = Array(' . $length . ")\n";
			
            foreach ( $arr as $key => $cell )
			{
                $jskey = is_int( $key )? $key : '"' . $key . '"';
				
                if ( is_array( $cell ) )
				{
                    $level++;
                    
					$var .= JavaScriptConvert::convertArray( $cell, 'tmp' . $level, $global, $level );
                    $var .= $varname . "[$jskey] = tmp$level\n";
                    $var .= "tmp$level = null\n";
                } 
				else 
				{
                    $value = is_string( $cell )? '"' . JavaScriptConvert::escapeString( $cell ) . '"' : $cell;
                    $var  .= $varname . "[$jskey] = $value\n";
                }
            }
			
            return $var;
        } 
		else 
		{
            return PEAR::raiseError( "Invalid variable type, array expected.", JAVASCRIPT_ERROR_INVVAR );
        }
    }
} // END OF JavaScriptConvert

?>
