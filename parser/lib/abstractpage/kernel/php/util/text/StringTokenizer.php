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
 * A string tokenizer allows you to break a string into tokens,
 * these being delimited by any character in the delimiter.
 * 
 * @package util_text
 */

class StringTokenizer extends PEAR
{
	/**
	 * @access public
	 */
    var $delim;
	
	/**
	 * @access public
	 */
	var $tok;

    
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     * @param   string delim default ' '
     */
    function StringTokenizer( $str, $delim = ' ' ) 
	{
      	$this->delim = $delim;
      	$this->tok   = strtok( $str, $this->delim );
    }
    
	
    /**
     * Tests if there are more tokens available.
     *
     * @access  public
     * @return  bool more tokens
     */
    function hasMoreTokens()
	{
      	return ( $this->tok !== false );
    }
    
    /**
     * Returns the next token from this tokenizer's string.
     *
     * @access  public
     * @return  string next token
     */
    function nextToken()
	{
      	$tok = $this->tok;
      	$this->tok = strtok( $this->delim );
      
	  	return $tok;
    }
} // END OF StringTokenizer

?>
