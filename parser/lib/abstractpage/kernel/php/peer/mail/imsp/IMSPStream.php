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


using( 'util.Debug' );

 
define( "IMSP_CRLF", "\r\n" );
define( "IMSP_RESPONSE_TAG_LENGTH", 5 );
define( "IMSP_DEFAULT_RESPONSE_LENGTH", 512 );

// These define regExp that should match the respective server response strings.
define( "IMSP_CONNECTION_OK", "^\* OK" );
define( "IMSP_LOGIN_OK", "OK User" );
define( "IMSP_COMMAND_CONTINUATION_RESPONSE", "^\+" );


/**
 * Interactive Message Support Protocol
 * Basic IMSP connectivity and defines the CIMSPStream object.
 *
 * @package peer_mail_imsp
 */
			
class IMSPStream extends PEAR
{
	/**
	 * Property for the username
	 * @access public
	 */
	var $user;
	
	/**
	 * Holds the user's password
	 * @access public
	 */
 	var $pass;
	
	/**
	 * Routines may place exit codes into this property for checking by the client application
	 * @access public
	 */
	var $exitCode;
	
	/**
	 * Defaults to "localhost" for the IMSP server host
	 * @access public
	 */
 	var $imsp_server;
	
	/**
	 * @access public
	 */
	var $imsp_port;
	
	/**
	 * Set this to the type of Authentication Method to be used. (ONLY PLAIN SUPPORTED AT THIS TIME)
	 * @access public
	 */
 	var $auth_method;

	/**
	 * Hierarchy seperator (not used in the class...here as a convienience to the client)
	 * @access public
	 */
	var $seperator = ".";
	
	/**
	 * Reference to the file pointer for the IMSP stream
	 * @access private
	 */
 	var $_stream;

	/**
	 * Holds the current alphabetic portion of command tag
	 * @access private
	 */
 	var $_commandPrefix = "A";
	
	/**
	 * Holds the current numeric portion of the command tag
	 * @access private
	 */
 	var $_commandCount = 1;
	
	/**
	 * Holds the most current command tag
	 * @access private
	 */
 	var $_tag = "";


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function IMSPStream( $server = "localhost", $port = 406, $auth_method = "PLAIN" )
	{
		$this->debug = new Debug();
		$this->debug->Off();

		$this->imsp_server = $server;
		$this->imsp_port   = $port;
		$this->auth_method = $auth_method;
	}


	/**
	 * @access public
	 */
	function login( $username, $pass )
	{
		$this->user = $username;
		$this->pass = $pass;
	
		$this->debug->Message( "Starting IMSP Session" );
 	
		// Now try to open the IMSP connection.
		if ( !$this->imspOpen() )
			return $this->imspError();
	 
		$this->debug->Message( "Connection OK" );

 		/*
		* Everything is ok...so try to login to the server
 	 	* For now, we do things simply, using LOGIN command
		*/
		switch ( $this->auth_method )
		{
 	 		case "PLAIN":
 	 			if ( !$this->imspPlainTextLogin() )
				{
 	 				// Login failed
 	
 	 				if ( $this->exitCode = "" )
	 	 				$this->exit_code = "Login to host failed.";
 				
					return false;
				}
 	 		
				break;
 	 		
			default:
				// must specify an auth method 
				$this->exit_code = "Login to host failed.";
				return $this->imspError( "Must specify an authentication method in the auth_method property." );
 	 	}
 	 
		// OK. We are logged in to the server.
		$this->debug->Message("LOGIN for user $this->user successful.");
 	 
 		return true;
	}
	
	/**
	 * Logs out of the server and closes the IMSP stream.
	 *
	 * @access public
	 */
	function logout()
	{
		$this->debug->Message( "Closing Connection." );
	
		$command_string = "LOGOUT"; // build the command to send to the server
	
		if ( !$this->imspSend( $command_string ) )
		{
			return $this->imspError();
		}
		else
		{
			// Should we test here for the BYE response? Is there a need for this?
			
			fclose( $this->_stream );
			return true;
		}
	}


	// IMSP connectivity functions
	
	/**
	 * @access public
	 */	
	function imspOpen()
	{
		// Now try to open the IMSP connection.
 		$fp = fsockopen( $this->imsp_server, $this->imsp_port );
 	
 		// Check for failure.
 		if ( !$fp )
		{
 			$this->exit_code = "Connection failed.";
 			return $this->imspError();
 		}
 	
 		// save the file pointer
 		$this->_stream = $fp;

 		// get The Server Response
 		$server_response = $this->imspRecieve();
 		 	
 		// check that it is what was expected
		if ( !ereg( IMSP_CONNECTION_OK, $server_response ) )
		{
			fclose( $fp );
			
 			$this->exit_code = "Response not expected.";
 			return $this->imspError();
 		}

		return true;
	}

	/**
	 * @access public
	 */
	function imspSend( $commandText, $includeTag = true, $sendCRLF = true )
	{
		if ( !$this->_stream )
		{
			// no connection
			$this->exit_code = "Connection failure.";
			return false;
		}
	
		if ( $includeTag )
		{
			$this->_tag = $this->getNextCommandTag();
			$command_text = "$this->_tag ";
		}
	
		$command_text .= $commandText;
	
		if ( $sendCRLF )	
			$command_text .= IMSP_CRLF;
	
		$this->debug->Message( "[CLIENT COMMAND] $command_text" );
	
		if ( !fputs( $this->_stream, $command_text ) )
		{
			// something wrong with connection?
			$this->exit_code = "Connection failure.";
			return false;
		}
		else
		{
			return true;	
		}
	}

	/**
	 * @access public
	 */
	function imspRecieve()
	{
		// Recieves a single CRLF terminated server status response from the server.
	
		if ( !$this->_stream )
		{
			// no connection
			$this->exit_code = "Connection failure.";
			return false;
		}
	
		$server_response = trim( fgets( $this->_stream, IMSP_DEFAULT_RESPONSE_LENGTH ) );
	
		$this->debug->Message( "[SERVER RESPONSE] $server_response" );
	
		// should we see if the response is simply an OK, BAD or NO in response to an tagged command?
	
		// parse out the response
		$currentTag = $this->_tag;
	
		if ( ereg( "^" . $currentTag . " NO", $server_response ) )
		{
			// Could not perform action.
			$this->exitCode = "Server returned a NO response.";
			return "NO";
		}
	
		if ( ereg( "^" . $currentTag . " BAD", $server_response ) )
		{
			// bad syntax or entry names
			$this->exitCode = "The server did not understand your request.";
			return "BAD";
		}
	
		if ( ereg( "^" . $currentTag . " OK", $server_response ) ) 
			return "OK";

		// If it was not a "NO", "BAD" or "OK" response, then it is up to the 
		// calling function to decide what to do with it 
		return $server_response;
	}

	/**
	 * @access public
	 */
	function getServerResponseChunks()
	{
		// Retrieves a CRLF terminated response from the server and splits it into an array delimeted by a <space> and returns array.
		$server_response = trim( fgets( $this->_stream, IMSP_DEFAULT_RESPONSE_LENGTH ) );
		$chunks = split( " ",$server_response );
	
		return $chunks;
	}

	/**
	 * @access public
	 */
	function recieveStringLiteral( $length )
	{
		return trim( fread( $this->_stream, $length ) );
	}

	
	// Authentication Routines
	
	/**
	 * @access public
	 */
 	function imspPlainTextLogin()
	{
 		/*
		 * Everything is ok...so try to login to the server
 	 	 * For now, we do things simply, using LOGIN command
		 */
 	 
		// Now build the command string.
 		$command_string = "LOGIN $this->user $this->pass";
 	
 		if ( !$this->imspSend( $command_string ) )
		{
 			$this->exitCode = "Connection failure.";
 			return false;
 		}
		
 		// get the response
 		$server_response = $this->imspRecieve();
 	
 		if ( $server_response != "OK" )
		{
 			// login failed
 			$this->exitCode = "Login failed.";
 			return false;
 		}
 	
 		return true;
 	}
 
 
	// Utility Functions
	
	/**
	 * @access public
	 */
	function getNextCommandTag()
	{	
		$newtag = $this->_commandPrefix;
		
	 	if ( $this->_commandCount < 10 )
			$newtag .= "000" . (string)$this->_commandCount;
		else if ( $this->_commandCount < 100 )
			$newtag .= "00"  . (string)$this->_commandCount;
		else if ( $this->_commandCount < 999 )
			$newtag .= "0"   . (string)$this->_commandCount;
		
		// increment it for the next command
		$this->_commandCount++;
		
		return $newtag;
	}

	/**
	 * @access public
	 */
	function quoteSpacedString( $string )
	{
		if ( ereg( " ", $string ) )
			return "\"" . $string . "\"";
		else
			return $string;
	}

	/**
	 * @access public
	 */
	function imspError( $server_text = "" )
	{
		$code = $this->exitCode = "";
		
		// reset exitCode
		$this->exitCode = "";
		
		return PEAR::raiseError( $code . ". " . ( ( $server_text != "" )? $server_text : "" ) );
	}
} // END OF IMSPStream

?>
