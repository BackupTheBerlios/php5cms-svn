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
 * @package peer_mail_pop3
 */
 
class POP3 extends PEAR
{
	/**
	 * @access public
	 */
	var $hostname;
	
	/**
	 * @access public
	 */
	var $port;

	/**
	 * @access private
	 */
	var $_connection = 0;
	
	/**
	 * @access private
	 */
	var $_state = "DISCONNECTED";
	
	/**
	 * @access private
	 */
	var $_greeting = "";
	
	/**
	 * @access private
	 */
	var $_must_update = 0;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function POP3( $host = '', $port = 110 )
	{
		$this->hostname = $host;
		$this->port     = $port;
	}
	
	
	/**
	 * Set the object variable $hostname to the POP3 server address.
	 *
	 * @access public
	 */
	function open()
	{
		if ( $this->_state != "DISCONNECTED" )
			return PEAR::raiseError( "A connection is already opened." );
		
		$res = $this->_openConnection();
		
		if ( PEAR::isError( $res ) )
			return $res;

		$this->_greeting = $this->_getLine();

		if ( gettype( $this->_greeting ) != "string" || strtok( $this->_greeting, " " ) != "+OK" )
		{
			$this->_closeConnection();
			return PEAR::raiseError( "POP3 server greeting was not found." );
		}
		
		$this->_greeting    = strtok( "\r\n" );
		$this->_must_update = 0;
		$this->_state       = "AUTHORIZATION";
		
		return true;;
	}
	
	/**
	 * This method must be called at least if there are any messages to be deleted.
	 *
	 * @access public
	 */
	function close()
	{
		if ( $this->_state == "DISCONNECTED" )
			return PEAR::raiseError( "No connection was opened." );
		
		if ( $this->_must_update )
		{
			if ( $this->_putLine( "QUIT" ) == 0 )
				return PEAR::raiseError( "Could not send the QUIT command." );
				
			$response = $this->_getLine();
			
			if ( gettype( $response ) != "string" )
				return PEAR::raiseError( "Could not get quit command response." );
			
			if ( strtok( $response, " " ) != "+OK" )
				return PEAR::raiseError( "Could not quit the connection: " );
		}
		
		$this->_closeConnection();
		$this->_state = "DISCONNECTED";
		
		return true;;
	}

	/**
	 * Pass the user name and password of POP account. 
	 * Set $apop to 1 or 0 wether you want to login using APOP method or not.
	 *
	 * @access public
	 */
	function login( $user, $password, $apop )
	{
		if ( $this->_state != "AUTHORIZATION" )
			return PEAR::raiseError( "Connection is not in AUTHORIZATION state." );
		
		if ( $apop )
		{
			if ( $this->_putLine( "APOP $user " . md5( $this->_greeting . $password ) ) == 0 )
				return PEAR::raiseError( "Could not send the APOP command." );
			
			$response = $this->_getLine();
			
			if ( gettype( $response ) != "string" )
				return PEAR::raiseError( "Could not get APOP login command response." );

			if ( strtok( $response, " " ) != "+OK" )
				return PEAR::raiseError( "APOP login failed." );
		}
		else
		{
			if ( $this->_putLine( "USER $user" ) == 0 )
				return PEAR::raiseError( "Could not send the USER command." );
			
			$response = $this->_getLine();
			
			if ( gettype( $response ) != "string" )
				return PEAR::raiseError( "Could not get user login entry response." );
			
			if ( strtok( $response, " " ) != "+OK" )
				return PEAR::raiseError( "User error." );
			
			if ( $this->_putLine( "PASS $password" ) == 0 )
				return PEAR::raiseError( "Could not send the PASS command." );
				
			$response = $this->_getLine();
			
			if ( gettype( $response ) != "string" )
				return PEAR::raiseError( "Could not get login password entry response." );
			
			if ( strtok( $response, " " ) != "+OK" )
				return PEAR::raiseError( "Password error." );
		}

		$this->_state = "TRANSACTION";
		return true;;
	}

	/**
	 * Pass references to variables to hold the number of
	 * messages in the mail box and the size that they take in bytes.
	 *
	 * @access public
	 */
	function statistics( $messages, $size )
	{
		if ( $this->_state != "TRANSACTION" )
			return PEAR::raiseError( "Connection is not in TRANSACTION state." );
		
		if ( $this->_putLine( "STAT" ) == 0 )
			return PEAR::raiseError( "Could not send the STAT command." );
			
		$response = $this->_getLine();
		
		if ( gettype( $response ) != "string" )
			return PEAR::raiseError( "Could not get the statistics command response." );
		
		if ( strtok( $response, " " ) != "+OK" )
			return PEAR::raiseError( "Could not get the statistics.");
		
		$messages = strtok( " " );
		$size     = strtok( " " );
		
		return true;;
	}

	/**
	 * The $message argument indicates the number of a
     * message to be listed. If you specify an empty string it will list all
     * messages in the mail box. The $unique_id flag indicates if you want
     * to list the each message unique identifier, otherwise it will
     * return the size of each message listed. If you list all messages the
     * result will be returned in an array.
	 *
	 * @access public
	 */
	function listMessages( $message, $unique_id )
	{
		if ( $this->_state != "TRANSACTION" )
			return PEAR::raiseError( "Connection is not in TRANSACTION state." );
			
		if ( $unique_id )
			$list_command = "UIDL";
		else
			$list_command = "LIST";
			
		if ( $this->_putLine( "$list_command $message" ) == 0 )
			return PEAR::raiseError( "Could not send the list command." );
		
		$response = $this->_getLine();
		
		if ( gettype( $response ) != "string" )
			return PEAR::raiseError( "Could not get message list command response." );
		
		if ( strtok( $response, " " ) != "+OK" )
			return PEAR::raiseError( "Could not get the message listing." );
		
		if ( $message == "" )
		{
			for ( $messages = array();; )
			{
				$response = $this->_getLine();
				
				if ( gettype( $response ) != "string" ) 
					return PEAR::raiseError( "Could not get message list response." );

				if ( $response == "." )
					break;
	
				$message = intval( strtok( $response, " " ) );

				if ( $unique_id )
					$messages[$message] = strtok(" ");
				else
					$messages[$message] = intval( strtok( " " ) );
			}

			return ( $messages );
		}
		else
		{
			$message = intval( strtok( " " ) );
			return ( intval( strtok( " " ) ) );
		}
	}

	/**
	 * The $message argument indicates the number of
     * a message to be listed. Pass a reference variables that will hold the
     * arrays of the $header and $body lines. The $lines argument tells how
     * many lines of the message are to be retrieved. Pass a negative number
     * if you want to retrieve the whole message.
	 *
	 * @access public
	 */
	function retrieveMessage( $message, $headers, $body, $lines )
	{
		if ( $this->_state != "TRANSACTION" )
			return PEAR::raiseError( "Connection is not in TRANSACTION state." );
		
		if ( $lines < 0 )
		{
			$command   = "RETR";
			$arguments = "$message";
		}
		else
		{
			$command   = "TOP";
			$arguments = "$message $lines";
		}
		
		if ( $this->_putLine( "$command $arguments" ) == 0 )
			return PEAR::raiseError( "Could not send command." );
		
		$response = $this->_getLine();
		
		if ( gettype( $response ) != "string" )
			return PEAR::raiseError( "Could not get message retrieval command response." );
		
		if ( strtok( $response, " " ) != "+OK" )
			return PEAR::raiseError( "Could not retrieve the message." );

		for ( $headers = $body = array(), $line = 0;; $line++ )
		{
			$response = $this->_getLine();
			
			if ( gettype( $response ) != "string" )
				return PEAR::raiseError( "Could not retrieve the message." );
			
			switch( $response )
			{
				case "." :
					return true;;
					
				case "" :
					break 2;
					
				default :
					if ( substr( $response, 0, 1 ) == "." )
						$response = substr( $response, 1, strlen( $response ) - 1 );
					
					break;
			}
			
			$headers[$line] = $response;
		}
		
		for ( $line = 0;; $line++ )
		{
			$response = $this->_getLine();
			
			if ( gettype( $response ) != "string" )
				return PEAR::raiseError( "Could not retrieve the message." );
			
			switch( $response )
			{
				case "." :
					return("");
					
				default :
					if ( substr( $response, 0, 1 ) == "." )
						$response = substr( $response, 1, strlen( $response ) - 1 );
					
					break;
			}
			
			$body[$line] = $response;
		}
		
		return true;;
	}

	/**
	 * The $message argument indicates the number of
     * a message to be marked as deleted. Messages will only be effectively
     * deleted upon a successful call to the close method.
	 *
	 * @access public
	 */
	function deleteMessage( $message )
	{
		if ( $this->_state != "TRANSACTION" )
			return PEAR::raiseError( "Connection is not in TRANSACTION state." );
			
		if ( $this->_putLine( "DELE $message" ) == 0 )
			return PEAR::raiseError( "Could not send the DELE command." );
			
		$response = $this->_getLine();
		
		if ( gettype( $response ) != "string" )
			return PEAR::raiseError( "Could not get message delete command response." );

		if ( strtok( $response, " " ) != "+OK" )
			return PEAR::raiseError( "Could not delete the message." );
		
		$this->_must_update = 1;
		return true;;
	}

	/**
	 * Reset the list of marked to be deleted messages. 
	 * No messages will be marked to be deleted upon a successful
     * call to this method.
	 *
	 * @access public
	 */
	function resetDeletedMessages()
	{
		if ( $this->_state != "TRANSACTION" )
			return PEAR::raiseError( "Connection is not in TRANSACTION state." );
			
		if ( $this->_putLine( "RSET" ) == 0 )
			return PEAR::raiseError( "Could not send the RSET command." );
		
		$response = $this->_getLine();
		
		if ( gettype( $response ) != "string" )
			return PEAR::raiseError( "Could not get reset deleted messages command response." );
			
		if ( strtok( $response, " " ) != "+OK" )
			return PEAR::raiseError( "Could not reset deleted messages." );
		
		$this->_must_update = 0;
		return true;;
	}

	/**
	 * Just pings the server to prevent it auto-close the
     * connection after an idle timeout (tipically 10 minutes). Not very
     * useful for most likely uses of this class. It's just here for
     * protocol support completeness.
	 *
	 * @access public
	 */
	function issueNOOP()
	{
		if ( $this->_state != "TRANSACTION" )
			return PEAR::raiseError( "Connection is not in TRANSACTION state." );
		
		if ( $this->_putLine( "NOOP" ) == 0 )
			return PEAR::raiseError( "Could not send the NOOP command." );
		
		$response = $this->_getLine();
		
		if ( gettype( $response ) != "string" )
			return PEAR::raiseError( "Could not NOOP command response." );
		
		if ( strtok( $response, " " ) != "+OK" )
			return PEAR::raiseError( "Could not issue the NOOP command." );

		return true;;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _getLine()
	{
		for ( $line = "";; )
		{
			if ( feof( $this->_connection ) )
				return false;

			$line  .= fgets( $this->_connection, 100 );
			$length = strlen( $line );
			
			if ( $length >= 2 && substr( $line, $length - 2, 2 ) == "\r\n" )
				return ( substr( $line, 0, $length - 2 ) );
		}
	}

	/**
	 * @access private
	 */
	function _putLine( $line )
	{
		return ( fputs( $this->_connection, "$line\r\n" ) );
	}

	/**
	 * @access private
	 */
	function _openConnection()
	{
		if ( $this->hostname == "" )
			return PEAR::raiseError( "Invalid hostname." );
			
		switch ( ( $this->_connection = fsockopen( $this->hostname, $this->port ) ) )
		{
			case -3 :
				return PEAR::raiseError( "Socket could not be created." );
				
			case -4 :
				return PEAR::raiseError( "DNS lookup on host failed." );
				
			case -5 :
				return PEAR::raiseError( "Connection refused or timed out." );

			case -6 :
				return PEAR::raiseError( "fdopen() call failed." );

			case -7 :
				return PEAR::raiseError( "setvbuf() call failed." );
				
			default :
				return true;
		}
	}

	/**
	 * @access private
	 */
	function _closeConnection()
	{
		if ( $this->_connection != 0 )
		{
			fclose( $this->_connection );
			$this->_connection = 0;
		}
	}	
} // END OF POP3

?>
