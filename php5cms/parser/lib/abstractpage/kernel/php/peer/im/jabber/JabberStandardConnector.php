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
|Authors: Carlo Zottmann <carlo@g-blog.net>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package peer_im_jabber
 */
 
class JabberStandardConnector extends PEAR
{
	/**
	 * @access public
	 */
	var $active_socket;


	/**
	 * @access public
	 */
	function openSocket($server, $port)
	{
		if ($this->active_socket = fsockopen($server, $port))
		{
			socket_set_blocking( $this->active_socket, 0 );
			socket_set_timeout( $this->active_socket, 31536000 );

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @access public
	 */
	function closeSocket()
	{
		return fclose( $this->active_socket );
	}

	/**
	 * @access public
	 */
	function writeToSocket( $data )
	{
		return fwrite( $this->active_socket, $data );
	}

	/**
	 * @access public
	 */
	function readFromSocket( $chunksize )
	{
		set_magic_quotes_runtime( 0 );
		$buffer = fread( $this->active_socket, $chunksize );
		set_magic_quotes_runtime( get_magic_quotes_gpc() );

		return $buffer;
	}
} // END OF JabberStandardConnector

?>
