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
 
class NetDigResult extends PEAR
{
	/**
	 * @access public
	 */
	var $status;
	
	/**
	 * @access public
	 */
	var $id;
	
	/**
	 * @access public
	 */
	var $flags;
	
	/**
	 * @access public
	 */
	var $query_count;
	
	/**
	 * @access public
	 */
	var $answer_count;
	
	/**
	 * @access public
	 */
	var $authority_count;
	
	/**
	 * @access public
	 */
	var $additional_count;

	/**
	 * @access public
	 */
	var $dig_version;
	
	/**
	 * @access public
	 */
	var $dig_server;
	
	/**
	 * @access public
	 */
	var $dig_port;

	/**
	 * @access public
	 */
	var $query;
	
	/**
	 * @access public
	 */
	var $answer;
	
	/**
	 * @access public
	 */
	var $authority;
	
	/**
	 * @access public
	 */
	var $additional;

	/**
	 * @access public
	 */
	var $consistency_check;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function NetDigResult()
	{
		$this->status            = false;
		$this->id                = false;
		$this->flags             = false;
		$this->query_count       = false;
		$this->answer_count      = false;
		$this->authority_count   = false;
		$this->additional_count  = false;

		$this->dig_version       = false;
		$this->dig_server        = false;
		$this->dig_port          = false;

		$this->query             = array();
		$this->answer            = array();
		$this->authority         = array();
		$this->additional        = array();

		$this->consistency_check = false;
	}
} // END OF NetDigResult

?>
