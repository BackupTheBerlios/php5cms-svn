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


define( 'SMTP_STATUS_NOT_CONNECTED', 1, true );
define( 'SMTP_STATUS_CONNECTED',     2, true );

if ( !defined( 'SMTP_CRLF' ) )
	define( 'SMTP_CRLF', "\r\n", true );
	

class SMTP extends Base
{
	/**
	 * @access public
	 */
	public $connection;
	
	/**
	 * @access public
	 */
	public $recipients;
	
	/**
	 * @access public
	 */
	public $headers;
	
	/**
	 * @access public
	 */
	public $timeout;
	
	/**
	 * @access public
	 */
	public $errors;
	
	/**
	 * @access public
	 */
	public $status;
	
	/**
	 * @access public
	 */
	public $body;
	
	/**
	 * @access public
	 */
	public $from;
	
	/**
	 * @access public
	 */
	public $host;
	
	/**
	 * @access public
	 */
	public $port;
	
	/**
	 * @access public
	 */
	public $helo;
	
	/**
	 * @access public
	 */
	public $auth;
	
	/**
	 * @access public
	 */
	public $user;
	
	/**
	 * @access public
	 */
	public $pass;
	
	
	/**
	 * Constructor
	 *
	 * @param  array  $params
     *		host    - The hostname of the smtp server		Default: localhost
	 *		port    - The port the smtp server runs on		Default: 25
	 *		helo    - What to send as the HELO command		Default: localhost
	 *				  (typically the hostname of the
	 *				  machine this script runs on)
	 *		auth    - Whether to use basic authentication	Default: false
	 *		user    - Username for authentication			Default: <blank>
	 *		pass    - Password for authentication			Default: <blank>
	 *		timeout - The timeout in seconds for the call	Default: 5
	 *            	  to fsockopen()
	 * @access public
	 */
	public function __construct( $params = array() )
	{			
		$this->timeout	= 5;
		$this->status	= SMTP_STATUS_NOT_CONNECTED;
		$this->host		= 'localhost';
		$this->port		= 25;
		$this->helo		= 'localhost';
		$this->auth		= false;
		$this->user		= '';
		$this->pass		= '';
		$this->errors   = array();

		foreach ( $params as $key => $value )
			$this->$key = $value;
	}


	/**
	 * Connect function. This will, when called
	 * statically, create a new smtp object, 
	 * call the connect public function (ie this function)
	 * and return it. When not called statically,
	 * it will connect to the server and send
	 * the HELO command.
     *
	 * @param  array   $params
	 * @return mixed
	 * @access public
	 */
	public function connect( $params = array() )
	{
		if ( !isset( $this->status ) )
		{
			$obj = new SMTP( $params );
			
			if ( $obj->connect() )
				$obj->status = SMTP_STATUS_CONNECTED;
			
			return $obj;
		}
		else
		{
			$this->connection = fsockopen( $this->host, $this->port, $errno, $errstr, $this->timeout );
			socket_set_timeout( $this->connection, 0, 250000 );

			$greeting = $this->getData();
			
			if ( is_resource( $this->connection ) )
			{
				return $this->auth? $this->ehlo() : $this->helo();
			}
			else
			{
				$this->errors[] = 'Failed to connect to server: ' . $errstr;
				return false;
			}
		}
	}

	/**
	 * Send the mail.
	 *
	 * @param  array  $params
	 *      recipients - Indexed array of recipients
	 *      from       - The from address. (used in MAIL FROM:),
	 *                   this will be the return path
	 *      headers    - Indexed array of headers, one header per array entry
     *      body       - The body of the email
	 *                   It can also contain any of the parameters from the connect()
     *                   function
	 * @access public
	 */
	public function send( $params = array() )
	{
		foreach ( $params as $key => $value )
			$this->set( $key, $value );

		if ( $this->isConnected() )
		{
			// Do we auth or not? Note the distinction between the auth variable and auth() function
			if ( $this->auth )
			{
				if ( !$this->auth() )
					return false;
			}

			$this->mail( $this->from );
			
			if ( is_array( $this->recipients ) )
			{
				foreach ( $this->recipients as $value )
					$this->rcpt( $value );
			}
			else
			{
				$this->rcpt( $this->recipients );
			}
			
			if ( !$this->data() )
				return false;

			// Transparency
			$headers = str_replace( SMTP_CRLF . '.', SMTP_CRLF . '..', trim( implode( SMTP_CRLF, $this->headers ) ) );
			$body    = str_replace( SMTP_CRLF . '.', SMTP_CRLF . '..', $this->body );
			$body    = ( $body[0] == '.' )? '.' . $body : $body;

			$this->sendData( $headers );
			$this->sendData( '' );
			$this->sendData( $body );
			$this->sendData( '.' );

			return ( substr( trim( $this->getData() ), 0, 3 ) === '250' );
		}
		else
		{
			$this->errors[] = 'Not connected!';
			return false;
		}
	}
		
	/**
     * Function to implement HELO cmd.
	 *
	 * @access public
	 */
	public function helo()
	{
		if ( is_resource( $this->connection ) && 
		     $this->sendData( 'HELO ' . $this->helo ) && 
			 substr( trim( $error = $this->getData() ), 0, 3 ) === '250' )
		{
			return true;
		}
		else
		{
			$this->errors[] = 'HELO command failed, output: ' . trim( substr( trim( $error ), 3 ) );
			return false;
		}
	}
		
	/**
	 * Function to implement EHLO cmd.
	 *
	 * @access public
	 */
	public function ehlo()
	{
		if ( is_resource( $this->connection ) && 
			 $this->sendData('EHLO '.$this->helo) && 
			 substr( trim( $error = $this->getData() ), 0, 3 ) === '250' )
		{
			return true;
		}
		else
		{
			$this->errors[] = 'EHLO command failed, output: ' . trim( substr( trim( $error ), 3 ) );
			return false;
		}
	}
		
	/**
	 * Function to implement AUTH cmd.
	 *
	 * @access public
	 */
	public function auth()
	{
		if ( is_resource( $this->connection )
			 && $this->sendData( 'AUTH LOGIN' )
			 && substr( trim( $error = $this->getData() ), 0, 3 ) === '334'
			 && $this->sendData( base64_encode( $this->user ) )				// Send username
			 && substr( trim( $error = $this->getData() ), 0, 3 ) === '334'
			 && $this->sendData( base64_encode( $this->pass ) )				// Send password
			 && substr( trim( $error = $this->getData() ), 0, 3 ) === '235' )
		{
			return true;
		}
		else
		{
			$this->errors[] = 'AUTH command failed: ' . trim( substr( trim( $error ), 3 ) );
			return false;
		}
	}

	/**
	 * Function that handles the MAIL FROM: cmd.
	 *
	 * @access public
     */
	public function mail( $from )
	{
		if ( $this->isConnected() && $this->sendData( 'MAIL FROM:<' . $from . '>' ) && substr( trim( $this->getData() ), 0, 2 ) === '250' )
			return true;
		else
			return false;
	}

	/**
     * Function that handles the RCPT TO: cmd.
	 *
	 * @access public
	 */
	public function rcpt( $to )
	{
		if ( $this->isConnected() && $this->sendData( 'RCPT TO:<' . $to . '>' ) && substr( trim( $error = $this->getData() ), 0, 2 ) === '25' )
		{
			return true;
		}
		else
		{
			$this->errors[] = trim( substr( trim( $error ), 3 ) );
			return false;
		}
	}

	/**
	 * Function that sends the DATA cmd.
	 *
	 * @access public
	 */
	public function data()
	{
		if ( $this->isConnected() && $this->sendData( 'DATA' ) && substr( trim( $error = $this->getData() ), 0, 3 ) === '354' )
		{
			return true;
		}
		else
		{
			$this->errors[] = trim( substr( trim( $error ), 3 ) );
			return false;
		}
	}

	/**
	 * Function to determine if this object is connected to the server or not.
	 *
	 * @access public
     */
	public function isConnected()
	{
		return ( is_resource( $this->connection ) && ( $this->status === SMTP_STATUS_CONNECTED ) );
	}

	/**
 	 * Function to send a bit of data.
	 *
	 * @access public
     */
	public function sendData( $data )
	{
		if ( is_resource( $this->connection ) )
			return fwrite( $this->connection, $data . SMTP_CRLF, strlen( $data ) + 2 );
		else
			return false;
	}

	/**
	 * Function to get data.
	 *
	 * @access public
	 */
	public function &getData()
	{
		$return = '';
		$line   = '';

		if ( is_resource( $this->connection ) )
		{
			while ( strpos( $return, SMTP_CRLF ) === false || substr( $line, 3, 1 ) !== ' ' )
			{
				$line    = fgets( $this->connection, 512 );
				$return .= $line;
			}
			
			return $return;
		}
		else
		{
			return false;
		}
	}

	/**
     * Sets a variable.
	 *
	 * @access public
	 */
	public function set( $var, $value )
	{
		$this->$var = $value;
		return true;
	}
} // END OF SMTP

?>
