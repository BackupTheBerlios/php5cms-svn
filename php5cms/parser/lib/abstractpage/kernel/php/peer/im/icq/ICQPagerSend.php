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
 * ICQPagerSend Class
 *
 * A class to send messages using ICQ WWP service.
 *
 * @package peer_im_icq
 */

class ICQPagerSend extends PEAR
{
	/**
	 * @access public
	 */
	var $from;
	
	/**
	 * @access public
	 */
	var $fromemail;
	
	/**
	 * @access public
	 */
	var $subject;
	
	/**
	 * @access public
	 */
	var $body;
	

	/**
	 * Constructor
	 *
	 * @param  string  $from       name of the sender
	 * @param  string  $fromemail  email of the sender
	 * @param  string  $subject    subject of the message
	 * @param  string  $body       body of the message
	 * @access public
	 */	
	function ICQPagerSend( $from, $fromemail, $subject, $body ) 
	{
		$this->from      = urlencode( $from      );
		$this->fromemail = urlencode( $fromemail );
		$this->subject   = urlencode( $subject   );
		$this->body      = urlencode( $body      );
	}

	
	/**
	 * Sends the message specified in the constructor to the specified UIN.
	 *
	 * @param  int  $uin  uin of the recipient
	 * @access public
	 */
	function sendTo( $uin ) 
	{
		return $this->_sendMessageX( $this->from, $this->fromemail, $this->subject, $this->body, $uin );
	}

	/**
 	 * Sends a message to the specified UIN.
	 *
	 * @param  string  $from       name of the sender
	 * @param  string  $fromemail  email of the sender
	 * @param  string  $subject    subject of the message
	 * @param  string  $body       body of the message
	 * @param  int     $uin        uin of the recipient
	 * @access public
	 */	
	function sendMessage( $from, $fromemail, $subject, $body, $uin ) 
	{
		return ICQPagerSend::_sendMessageX(
			urlencode( $from      ), 
			urlencode( $fromemail ), 
			urlencode( $subject   ), 
			urlencode( $body      ), 
			$uin
		);
	}


	// private methods
	
	/**
	 * @access private
	 */
	function _sendMessageX( $from, $fromemail, $subject, $body, $uin ) 
	{
		$fp = fsockopen( 'wwp.icq.com', 80, $errno, $errstr, 30 );
		
		if ( !$fp )
			return PEAR::raiseError( "Could not connect to server (wwp.icq.com)" );

		if ( strlen( $body ) > 380 )
			$body = substr( $body, 0, 380 );
		
		$q = 'HEAD /scripts/WWPMsg.dll?from=' . $from . '&fromemail=' . $fromemail . '&subject=' . $subject . '&to=' . trim($uin) . '&body=' . $body . " HTTP/1.0\n\n";
		fputs( $fp, $q );
		$res = '';
		
		while ( !feof( $fp ) )
			$res .= fgets( $fp, 1024 );
		
		fclose( $fp );

		if ( strpos( $res, '/whitepages/page_me_ok/' ) )
			return true;
		
		return false;
	}
} // END OF ICQPagerSend

?>
