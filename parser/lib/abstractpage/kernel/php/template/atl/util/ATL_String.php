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


using( 'template.atl.util.ATL_StringIterator' );


/**
 * Represents end of line string sequence '\n' of '<br/>' depending on
 * script context.
 */
if ( !defined( 'ATL_STRING_LINEFEED' ) ) 
{ 
    if ( isset( $GLOBALS['REQUEST_URI'] ) ) 
        define( 'ATL_STRING_LINEFEED', "<br/>\n" ); 
    else
        define( 'ATL_STRING_LINEFEED', "\n" );
}


/**
 * This class is an attempt to normalize php strings in an oo way.
 *
 * PHP string related functions have different arguments scheme that decrease
 * code readability in OO programs. This class provides some php functions
 * in a comprehensible String class.
 *
 * The second interest reside in the ATL_StringIterator class which normalize
 * iteration over strings just like any other iterable object.
 *
 * @package template_atl_util
 */
 
class ATL_String extends PEAR
{
	/**
	 * @access private
	 */
    var $_str;


    /**
     * Constructor
     *
     * @param mixed $str 
     *        May be an object from a class implementing the
     *        'toString()' method or a php variable that can be 
     *        casted into a string.
     *
     * @throws Error
     */
    function ATL_String( $str = "" )
    {
        if ( is_object( $str ) ) 
		{
            if ( !method_exists( $str, "toString" ) ) 
			{
				return PEAR::raiseError( 'String constructor requires string or object implementing toString() method.', null, PEAR_ERROR_DIE );
            }
			
            $this->_str = $str->toString();
        } 
		else 
		{
            $this->_str = $str;
        }
    }

	
    /**
     * Append string values of arguments to this string.
     *
     * @param mixed ... 
     *        php variables or objects implementing the toString()
     *        method.
	 * @access public
     */
    function append()
    {
        $args = func_get_args();
        foreach ( $args as $arg ) 
		{
            if ( is_object( $arg ) )
                $this->_str .= $arg->toString();
            else
                $this->_str .= $arg;
        }
    }
    
    /**
     * Append some elements to the buffer with an ATL_STRING_LINEFEED terminator.
     * 
     * @param mixed ... 
     *        List of php variables and/or objects implementing the
     *        toString() method.
	 * @access public
     */
    function appendln()
    {
        $args = func_get_args();
		
        foreach ( $args as $arg ) 
		{
            if ( is_object( $arg ) )
                $this->_str .= $arg->toString();
            else
                $this->_str .= $arg;
        }
		
        $this->_str .= ATL_STRING_LINEFEED;
    }
	
    /**
     * Retrieve char at specified position.
     *
     * @param  int $i Character position
     * @return char
	 * @access public
     */
    function charAt( $i )
    {
        return $this->_str[$i];
    }
    
    /**
     * Find first index of needle in current string.
     *
     * Return the first position of needle in string or *false* if 
     * not found.
     * 
     * @param  mixed $needle  object implementing toString or php variable.
     * @param  int   $pos     Search start position 
     * @return int
	 * @access public
     */
    function indexOf( $needle, $pos = 0 )
    {
        if ( is_object( $needle ) )
			$needle = $needle->toString();
			
        return strpos( $this->_str, $needle, $pos );
    }
    
    /**
     * Find last occurence of needle in current string.
     *
     * Returns the last position of needle in string or *false* if 
     * not found.
     *
     * @param  mixed needle object implementing toString or php variable.
     * @return int
	 * @access public
     */
    function lastIndexOf( $needle )
    {
        if ( is_object( $needle ) )
			$needle = $needle->toString();
			
        return strrpos( $this->_str, $needle );
    }
    
    /**
     * Returns true if the string match the specified regex.
     *
     * @param  mixed $regex object implementing toString or php string.
     * @return boolean
	 * @access public
     */
    function matches( $regex )
    {
        if ( is_object( $regex ) )
			$regex = $regex->toString();
			
        return preg_match( $regex, $this->_str );
    }

    /**
     * Returns true if the string contains specified substring.
     *
     * @param  mixed $str  object implementing toString or php string.
     * @return boolean
	 * @access public
     */
    function contains( $str )
    {
        if ( is_object( $str ) )
			$str = $str->toString();
			
        return ( strpos( $this->_str, $str ) !== false );
    }
    
    /**
     * Returns true if the string begins with specified token.
     *
     * @param  mixed $str object implementing toString or php string.
     * @return boolean
	 * @access public
     */
    function startsWith( $str )
    {
        if ( is_object( $str ) )
			$str = $str->toString();
			
        return preg_match( '|^' . preg_quote( $str ) . '|', $this->_str );
    }
    
    /**
     * Returns true if the string ends with specified token.
     *
     * @param  mixed $str object implementing toString or php string.
     * @return boolean
	 * @access public
     */
    function endsWith( $str )
    {
        if ( is_object( $str ) )
			$str = $str->toString();
			
        return preg_match( '|' . preg_quote( $str ) . '$|', $this->_str );
    }
    
    /**
     * Replace occurences of 'old' with 'new'.
     * 
     * @param  mixed $old token to replace
     * @param  mixed $new new token
     * @return string
	 * @access public
     */
    function replace( $old, $new )
    {
        if ( is_object( $old ) )
			$old = $old->toString();
			
        if ( is_object( $new ) )
			$new = $new->toString();
			
        return str_replace( $old, $new, $this->_str );
    }
    
    /**
     * Split this string using specified separator.
     *
     * @param  mixed $sep Separator token
     * @return array of phpstrings
	 * @access public
     */
    function split( $sep )
    {
        if ( is_object( $sep ) )
			$sep = $sep->toString();
			
        return split( $sep, $this->_str );
    }
    
    /**
     * Retrieve a sub string.
     *
     * @param int $start 
     *        start offset
     *        
     * @param int $end   
     *        End offset (up to end if no specified)
     *        
     * @return string
	 * @access public
     */
    function substr( $start, $end = false )
    {
        if ( $end === false )
			return substr( $this->_str, $start );
			
        return substr( $this->_str, $start, ( $end - $start ) );
    }

    /**
     * Retrieve a sub string giving its length.
     *
     * @param int $start  
     *        start offset
     *        
     * @param int $length 
     *        length from the start offset
     *        
     * @return string
	 * @access public
     */
    function extract( $start, $length )
    {
        return substr( $this->_str, $start, $length );
    }
    
    /**
     * Return  this string lower cased.
     * @return string
	 * @access public
     */
    function toLowerCase()
    {
        return strtolower( $this->_str );
    }
    
    /**
     * Return  this string upper cased.
     * @return string
	 * @access public
     */
    function toUpperCase()
    {
        return strtoupper( $this->_str );
    }
    
    /**
     * Remove  white characters from the beginning and the end of the string.
     * @return string
	 * @access public
     */
    function trim()
    {
        return trim( $this->_str );
    }
    
    /**
     * Test if this string equals specified object.
     *
     * @param mixed $o   
     *        php string or object implementing the toString() method.
     * @return boolean
	 * @access public
     */
    function equals( $o )
    {
        if ( is_object( $o ) )
            return $this->_str == $str->toString();
        else 
            return $this->_str == $o;
    }
    
    /**
     * Return  the length of this string.
     * @return int
	 * @access public
     */
    function length()
    {
        return strlen( $this->_str );
    }
    
    /**
     * Return  the php string handled by this object.
     * @return string
	 * @access public
     */
    function toString()
    {
        return $this->_str;
    }

    /**
     * Create a string iterator for this String object.
     *
     * ATL_StringIterators iterate at a character level.
     *
     * @return ATL_StringIterator
	 * @access public
     */
    function &getNewIterator()
    {
        return new ATL_StringIterator( $this );
    }
} // END OF ATL_String

?>
