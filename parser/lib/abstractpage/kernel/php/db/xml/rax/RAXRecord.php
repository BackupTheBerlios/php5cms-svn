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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


/**
 * @package db_sql_rax
 */
 
class RAXRecord extends PEAR
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RAXRecord( $fields )
	{
		$this->fields = $fields;
	}

	
	/**
	 * @access public
	 */
	function getFieldnames()
	{
		return array_keys( $this->fields );
	}

	/**
	 * @access public
	 */
	function getField ( $field )
	{	
		return trim( $this->fields[$field] );
	}

	/**
	 * @access public
	 */
	function getFields ()
	{	
		return array_values( $this->fields );
	}

	/**
	 * @access public
	 */
	function getRow ()
	{	
		return $this->fields;
	}
} // END OF RaxRecord

?>
