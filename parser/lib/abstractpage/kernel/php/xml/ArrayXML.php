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
 * Array_XML - Convert Associative Array to XML 
 *
 * Example:
 *
 * $array = new ArrayXML( $xml->ReturnArray(), $xml->ReturnReplaced(), $xml->ReturnAttributes() ) 
 * $xml = $array->Return();
 *
 * @package xml
 */
		 
class ArrayXML extends PEAR
{ 
	/**
	 * @access private
	 */
	var $_ignore;
	
	/**
	 * @access private
	 */
	var $_err;
	
	/**
	 * @access private
	 */
	var $_errline;
	
	/**
	 * @access private
	 */
	var $_replace;
	
	/**
	 * @access private
	 */
	var $_attribs;
	
	/**
	 * @access private
	 */
	var $_parent; 
	
	/**
	 * @access private
	 */
	var $_data;
	
	/**
	 * @access private
	 */
    var $_name = array(); 
	
	/**
	 * @access private
	 */
    var $_rep = array(); 
	
	/**
	 * @access private
	 */
    var $_parser = 0; 
	
	/**
	 * @access private
	 */
    var $_level = 0; 

	
	/**
	 * Constructor
	 *
	 * @access public
	 */ 
    function ArrayXML( &$data, $replace = array(), $attribs = array() )
	{
		$this->_attribs = $attribs; 
        $this->_replace = $replace; 
        $this->_data    = $this->_processArray( $data ); 
    }
	
	
	/**
	 * @access public
	 */
    function getXML()
	{ 
        return $this->_data; 
    }
	
	
	// private

	/**
	 * @access private
	 */	
    function _processArray( &$array, $level = 0 )
	{ 
        foreach ( $array as $name => $value )
		{ 
        	$tlevel  = $level; 
        	$isarray = false; 
        	$attrs   = ''; 

        	if ( is_array( $value ) && sizeof( $value ) > 0 && array_key_exists( 0, $value ) )
			{
				$tlevel  = $level - 1;
				$isarray = true;
			} 
        	else if ( !is_int( $name ) )
			{
				$this->_rep[$name]++;
			}
        	else
			{
				$name = $this->_parent;
				$this->_rep[$name]++;
			} 

			if ( is_array( $this->_attribs[$tlevel][$name][$this->_rep[$name]-1] ) )
			{ 
            	foreach ( $this->_attribs[$tlevel][$name][$this->_rep[$name]-1] as $aname => $avalue )
				{ 
            		unset( $value[$aname] ); 
            		$attrs .= " $aname=\"$avalue\""; 
            	} 
        	} 
        
			if ( $this->_replace[$name] )
				$name = $this->_replace[$name]; 

        	$this->_parent = $name; 
        
			is_array( $value )? $output = $this->_processArray( $value, $tlevel + 1 ) : $output = htmlspecialchars( $value ); 
        
			$isarray? 
            	$return .= $output : 
            	$return .= "<$name$attrs>$output</$name>\n"; 
        } 
     
	 	return $return; 
	} 
} // END OF ArrayXML

?>
