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
 * @package peer_mail_imap_lib
 */
 
class ImapMessageStructure extends PEAR
{
	/**
	 * @access public
	 */
	var $parts;

	/**
	 * @access public
	 */
	var $type;
	
	/**
	 * @access public
	 */
	var $subtype;
	
	/**
	 * @access public
	 */
	var $encoding;
	
	/**
	 * @access public
	 */
	var $description;
	
	/**
	 * @access public
	 */
	var $id;
	
	/**
	 * @access public
	 */
	var $num_lines;
	
	/**
	 * @access public
	 */
	var $num_bytes;
	
	/**
	 * @access public
	 */
	var $disposition;
	
	/**
	 * @access public
	 */
	var $parameters;
	
	/**
	 * @access public
	 */
	var $parts;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ImapMessageStructure()
	{
		$this->parts = array();
	}


	/**
	 * @access public
	 */	
	function addEntity( $msg )
	{
		if ( $msg->id == '' )
			return;
			
		$this->parts[count($this->parts)] = $msg;
	}

	/**
	 * @access public
	 */
	function FindFileName()
	{
		$file_name = '';

		if ( is_array( $this->parameters ) )
		{
			for( $x = 0; $x < count( $this->parameters ); $x++ )
			{
				$cur_param = $this->parameters[ $x ];
				
				if ( $cur_param->attribute == 'name' )
					return Array( $cur_param->value, $cur_param );
			}
		}
		
		return Array( '', undef );
	}
} // END OF ImapMessageStructure

?>
