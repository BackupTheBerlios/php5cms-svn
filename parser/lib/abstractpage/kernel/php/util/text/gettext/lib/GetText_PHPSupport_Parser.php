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


/**
 * This class is used to parse gettext '.po' files into php associative arrays.
 *
 * @package util_text_gettext_lib
 */
 
class GetText_PHPSupport_Parser extends PEAR
{
	/**
	 * @access private
	 */
    var $_currentKey;
	
	/**
	 * @access private
	 */
    var $_currentValue;

	/**
	 * @access private
	 */ 
	var $_hash = array();
	
	
    /**
     * Parse specified .po file.
     *
     * @return hashtable
     * @throws Error
	 * @access public
     */
    function parse( $file )
    {
        $this->_hash         = array();
        $this->_currentKey   = false;
        $this->_currentValue = "";
        
        if ( !file_exists( $file ) ) 
            return PEAR::raiseError( sprintf( 'Unable to locate file "%s"', $file ) );
		
        $i = 0;
        $lines = file( $file );
		
        foreach ( $lines as $line )
            $this->_parseLine( $line, ++$i );
        
        $this->_storeKey();
        return $this->_hash;
    }

	
	// private methods
	
    /**
     * Parse one po line.
     *
     * @access private
     */
    function _parseLine( $line, $nbr )
    {
        if ( preg_match( '/^\s*?#/', $line ) )
			return;
			
        if ( preg_match( '/^\s*?msgid \"(.*?)(?!<\\\)\"/', $line, $m ) ) 
		{
            $this->_storeKey();
            $this->_currentKey = $m[1];
            
			return;
        }
		
        if ( preg_match( '/^\s*?msgstr \"(.*?)(?!<\\\)\"/', $line, $m ) ) 
		{
            $this->_currentValue .= $m[1];
            return;
        }
		
        if ( preg_match( '/^\s*?\"(.*?)(?!<\\\)\"/', $line, $m ) ) 
		{
            $this->_currentValue .= $m[1];
            return;
        }
    }

    /**
     * Store last key/value pair into building hashtable.
     *
     * @access private
     */
    function _storeKey()
    {
        if ( $this->_currentKey === false ) 
			return;
        
		$this->_currentValue = str_replace( '\\n', "\n", $this->_currentValue );
        $this->_hash[$this->_currentKey] = $this->_currentValue;
        $this->_currentKey   = false;
        $this->_currentValue = "";
    }
} // END OF GetText_PHPSupport_Parser

?>
