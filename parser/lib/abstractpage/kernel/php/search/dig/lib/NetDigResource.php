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
 * @package search_dig_lib
 */
 
class NetDigResource extends PEAR
{
	/**
	 * @access public
	 */
	var $host;
	
	/**
	 * @access public
	 */
	var $ttl;
	
	/**
	 * @access public
	 */
	var $class;
	
	/**
	 * @access public
	 */
	var $type;
	
	/**
	 * @access public
	 */
	var $data;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function NetDigResource( $host = false, $ttl = false, $class = false, $type = false, $data = false )
	{
		$this->host	 = $host;
		$this->ttl	 = $ttl;
		$this->class = $class;
		$this->type	 = $type;
		$this->data	 = $data;
	}
} // END OF NetDigResource

?>
