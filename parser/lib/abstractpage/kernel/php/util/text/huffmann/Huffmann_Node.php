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
|         David H. <exaton@free.dot.fr>                                |
+----------------------------------------------------------------------+
*/


/** 
 * @package util_text_huffmann
 */
 
class Huffmann_Node extends PEAR
{
	/**
	 * Coded character 
	 *
	 * @access private
	 */
	var $_char;
	
	/**
	 * Character weight in the Huffman tree 
	 *
	 * @access private
	 */
	var $_w;
	
	/**
	 * Parent ID 
	 *
	 * @access private
	 */
	var $_par;
	
	/**
	 * ID of Child 0 
	 *
	 * @access private
	 */
	var $_child0;
	
	/**
	 * ID of Child 1 
	 *
	 * @access private
	 */
	var $_child1;
	
	/**
	 * "Done" in finding lightest nodes in original tree construction 
	 *
	 * @access private
	 */
	var $_lndone;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Huffmann_Node( $char, $w, $par = -1, $child0 = -1, $child1 = -1 )
	{
		$this->_char   = $char;
		$this->_w      = $w;
		$this->_par    = $par;
		$this->_child0 = $child0;
		$this->_child1 = $child1;
		$this->_lndone = false;
	}
} // END OF Huffmann_Node

?>
