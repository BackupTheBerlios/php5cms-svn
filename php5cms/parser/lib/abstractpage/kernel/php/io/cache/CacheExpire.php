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
 * @package io_cache
 */
 
class CacheExpire
{
	/**
	 * @access public
	 */
	var $hour;
	
	/**
	 * @access public
	 */
	var $min;
	
	/**
	 * @access public
	 */
	var $sec;
	
	/**
	 * @access public
	 */
	var $mon;
	
	/**
	 * @access public
	 */
	var $day;
	
	/**
	 * @access public
	 */
	var $year;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function CacheExpire()
	{
		$this->hour  = 0;
		$this->min   = 0;
		$this->sec   = 0;
		$this->month = 0;
		$this->day   = 0;
		$this->year  = 0;
	}
} // END OF CacheExpire

?>
