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


using( 'template.atl.util.ATL_String' );


/**
 * Iterator for php strings and objects implementing toString method.
 *
 * @package template_atl_util
 */
 
class ATL_StringIterator extends PEAR
{
	/**
	 * @access private
	 */
    var $_str;
	
	/**
	 * @access private
	 */
	var $_value;

	/**
	 * @access private
	 */	
    var $_index = -1;
	
	/**
	 * @access private
	 */
    var $_end = false;

    
    /**
     * Constructor a string iterator.
     *
     * @param mixed $str 
     *        String object, string variable, or Object implementing
     *        the toString() method.
	 *
	 * @access public
     */
    function ATL_StringIterator( &$str )
    {
        if ( is_object( $str ) )
            $this->_string = new ATL_String( $str->toString() );
        else if ( is_string( $str ) )
            $this->_string = new ATL_String( $str );
        
        $this->reset();
    }


    /**
     * Reset iterator to the begining of the string.
     *
     * If empty string, this iterator assume it is at the end of string.
	 *
	 * @access public
     */
    function reset()
    {
        $this->_end   = false;
        $this->_index = 0;
        
		if ( $this->_string->length() == 0 )
            $this->_end = true;
        else
            $this->_value = $this->_string->charAt( 0 );
    }
    
    /**
     * Return next character.
     * 
     * @return char
	 * @access public
     */
    function next()
    {
        if ( $this->_end || ++$this->_index >= $this->_string->length() ) 
		{
            $this->_end = true;
            return null;
        }
        
        $this->_value = $this->_string->charAt( $this->_index );
        return $this->_value;
    }
    
    /**
     * Return true if the end of the string is not reached yet.
     *
     * @return boolean
	 * @access public
     */
    function isValid()
    {
        return !$this->_end;
    }
    
    /**
     * Retrieve current iterator index.
     * 
     * @return int
	 * @access public
     */
    function index()
    {
        return $this->_index;
    }
    
    /**
     * Retrieve current iterator value.
     *
     * @return char
	 * @access public
     */
    function value()
    {
        return $this->_value;
    }
} // END OF ATL_StringIterator

?>
