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
 * @package peer_irc_lib
 */
 
class IRCListenFor extends PEAR
{
	/**
	 * @access public
	 */
	var $result = array();

	
	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function IRCListenFor( $obj = null )
	{
	}
	
	
	/**
	 * Stores the received answer into the result array.
	 *
	 * @return void
	 * @param  ircdata object
	 * @access public
	 */
	function handler( &$irc, &$ircdata )
	{
		$irc->log( IRC_DEBUG_ACTIONHANDLER, 'IRC_DEBUG_ACTIONHANDLER: listen_for handler called' );
		array_push( $this->result, $ircdata->message );
		$irc->disconnect( true );
	}
} // END OF IRCListenFor

?>
