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
|Authors: Juan M. Casillas <assman@jmcresearch.com>                    |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package template_lib
 */
 
class Template_Stack extends PEAR
{
	/**
	 * @access private
	 */
    var $_data;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function Template_Stack()
	{
		$this->_data = array();
    }

	
	/**
	 * @access public
	 */
    function &top()
	{
		$i   = (int)count( $this->_data ) - 1;
		$obj = null;
		
		while ( $i >= 0 )
		{     
	    	if ( $this->_data[$i]->open == true ) 
			{
				$obj = &$this->_data[$i];
				break;
	    	}
	    	
			$i--;
		}
  
		return $obj;
    }

	/**
	 * @access public
	 */
    function push( $item ) 
	{
		return array_push( $this->_data, $item );
    }

	/**
	 * @access public
	 */
    function pop()
	{
		return array_pop( $this->_data );
    }
} // END OF Template_Stack

?>
