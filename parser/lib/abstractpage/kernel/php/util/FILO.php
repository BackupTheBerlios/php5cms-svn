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
 * Simple Stack Implementation (First In Last Out).
 *
 * @package util
 */
 
class FILO extends PEAR
{
	/**
	 * @access public
	 */
	var $elements;
  
  	
	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function FILO()
	{
    	$this->zero();
  	}


	/**
	 * @access public
	 */
  	function push( $elm )
	{
    	array_push( $this->elements, $elm );
  	}

	/**
	 * @access public
	 */
  	function pop()
	{
    	return array_pop( $this->elements );
  	}

	/**
	 * @access public
	 */
  	function zero()
	{
    	$this->elements = array();
  	}
} // END OF FILO

?>
