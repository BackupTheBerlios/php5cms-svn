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
 * @package auth
 */
 
class UnixUser
{
	/**
	 * @access public
	 */
	var $name;
	
	/**
	 * @access public
	 */
	var $passwd;
	
	/**
	 * @access public
	 */
	var $uid;
	
	/**
	 * @access public
	 */
	var $gid;
	
	/**
	 * @access public
	 */
	var $quota;
	
	/**
	 * @access public
	 */
	var $comment;
	
	/**
	 * @access public
	 */
	var $gcos;
	
	/**
	 * @access public
	 */
	var $dir;
	
	/**
	 * @access public
	 */
	var $shell;
	
	/**
	 * @access public
	 */
	var $expire;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function UnixUser()
	{
		$this->name       = '';
		$this->passwd     = '';
		$this->uid        = '';
		$this->gid        = '';
		$this->quota      = '';
		$this->comment    = '';
		$this->gcos       = '';
		$this->dir        = '';
		$this->shell      = '';
		$this->expire     = '';
	}
} // END OF UnixUser

?>
