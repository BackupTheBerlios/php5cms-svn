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


using( 'peer.im.jabber.JabberStandardConnector' );


/**
 * The Jabber Instant Messaging project is an Open Source project seeking
 * to provide a complete cross protocol messaging solution.  The problem
 * with current IM solutions is that they are all proprietary and cannot
 * talk to each other.  Jabber seeks to get rid of those barriers by
 * allowing a Jabber client to talk with an AOL user, or an IRC chat room,
 * or any number of other programs.
 *
 * For more information about the Jabber project visit http://www.jabber.org.
 *
 * Jabber is a PHP class that provides access to the Jabber protocol. 
 * Using this OOP class, I provide a clean interface to
 * writing anything from a full client to a simple protocol tester.
 *
 * Requirements:
 *
 * - PHP 4.1.0+ with XML support, mhash support optional (if you want to use
 *   digest or 0k authentication)
 *
 * @package peer_im_jabber
 */

class Jabber extends PEAR
{
	/**
	 * @access public
	 */
	var $server;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $username;
	
	/**
	 * @access public
	 */
	var $password;
	
	/**
	 * @access public
	 */
	var $resource;
	
	/**
	 * @access public
	 */
	var $jid;

	/**
	 * @access public
	 */
	var $connection;
	
	/**
	 * @access public
	 */
	var $delay_disconnect;

	/**
	 * @access public
	 */
	var $stream_id;
	
	/**
	 * @access public
	 */
	var $roster;

	/**
	 * @access public
	 */
	var $enable_logging;
	
	/**
	 * @access public
	 */
	var $log_array;
	
	/**
	 * @access public
	 */
	var $log_filename;
	
	/**
	 * @access public
	 */
	var $log_filehandler;

	/**
	 * @access public
	 */
	var $iq_sleep_timer;
	
	/**
	 * @access public
	 */
	var $last_ping_time;

	/**
	 * @access public
	 */
	var $packet_queue;
	
	/**
	 * @access public
	 */
	var $subscription_queue;

	/**
	 * @access public
	 */
	var $iq_version_name;
	
	/**
	 * @access public
	 */
	var $iq_version_os;
	
	/**
	 * @access public
	 */
	var $iq_version_version;
	
	/**
	 * @access public
	 */
	var $error_codes;

	/**
	 * @access public
	 */
	var $CONNECTOR;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Jabber( $username, $password, $server = 'localhost', $port = 5222 )
	{
		$this->username				= $username;
		$this->password				= $password;
		$this->server				= $server;
		$this->port					= $port;
		
		$this->resource				= null;
		$this->enable_logging		= false;
		$this->log_array			= array();
		$this->log_filename			= '';
		$this->log_filehandler		= false;

		$this->packet_queue			= array();
		$this->subscription_queue	= array();

		$this->iq_sleep_timer		= 1;
		$this->delay_disconnect		= 1;

		$this->iq_version_name		= "Abstractpage Jabber Class";
		$this->iq_version_version	= "0.3.1";
		$this->iq_version_os		= $_SERVER['SERVER_SOFTWARE'];

		$this->connection_class		= "JabberStandardConnector";

		$this->error_codes = array(
			400 => "Bad Request",
			401 => "Unauthorized",
			402 => "Payment Required",
			403 => "Forbidden",
			404 => "Not Found",
			405 => "Not Allowed",
			406 => "Not Acceptable",
			407 => "Registration Required",
			408 => "Request Timeout",
			409 => "Conflict",
			500 => "Internal Server Error",
			501 => "Not Implemented",
			502 => "Remove Server Error",
			503 => "Service Unavailable",
			504 => "Remove Server Timeout",
			510 => "Disconnected"
		);
	}


	/**
	 * @access public
	 */
	function connect()
	{
		$this->_create_logfile();
		$this->CONNECTOR = new $this->connection_class;

		if ( $this->CONNECTOR->openSocket( $this->server, $this->port ) )
		{
			$this->sendPacket( "<?xml version='1.0' encoding='UTF-8' ?>\n" );
			$this->sendPacket( "<stream:stream to='{$this->server}' xmlns='jabber:client' xmlns:stream='http://etherx.jabber.org/streams'>\n" );

			sleep( 2 );

			if ( $this->_check_connected() )
			{
				return true;
			}
			else
			{
				$this->addToLog( "ERROR: connect() #1" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: connect() #2" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function disconnect()
	{
		if ( is_int( $this->delay_disconnect ) )
			sleep( $this->delay_disconnect );

		$this->sendPacket( "</stream:stream>" );
		$this->CONNECTOR->closeSocket();
		$this->_close_logfile();
		$this->printLog();
	}

	/**
	 * @access public
	 */
	function sendAuth()
	{
		$this->auth_id	= "auth_" . md5( time() . $_SERVER['REMOTE_ADDR'] );
		$this->resource	= ( $this->resource != null )? $this->resource : ( "Abstractpage Jabber Class " . md5( $this->auth_id ) );
		$this->jid		= "{$this->username}@{$this->server}/{$this->resource}";

		// request available authentication methods
		$payload = "<username>{$this->username}</username>";
		$packet	 = $this->sendIq( null, "get", $this->auth_id, "jabber:iq:auth", $payload );

		// was a result returned?
		if ( $this->getInfoFromIqType( $packet ) == "result" && $this->getInfoFromIqId( $packet ) == $this->auth_id )
		{
			// yes, now check for auth method availability in descending order (best to worst)
			if ( !function_exists( mhash ) )
				$this->addToLog( "ATTENTION: sendAuth() - mhash() is not available; screw 0k and digest method, we need to go with plaintext auth" );

			// auth_0k
			if ( function_exists( mhash ) && isset( $packet['iq']['#']['query'][0]['#']['sequence'][0]["#"] ) && isset( $packet['iq']['#']['query'][0]['#']['token'][0]["#"] ) )
				return $this->_sendauth_0k($packet['iq']['#']['query'][0]['#']['token'][0]["#"], $packet['iq']['#']['query'][0]['#']['sequence'][0]["#"]);
			// digest
			else if ( function_exists( mhash ) && isset( $packet['iq']['#']['query'][0]['#']['digest'] ) )
				return $this->_sendauth_digest();
			// plain text
			else if ( $packet['iq']['#']['query'][0]['#']['password'] )
				return $this->_sendauth_plaintext();
			
			// dude, you're fucked
			$this->addToLog( "ERROR: sendAuth() #2 - No auth method available!" );
			return false;
		}
		else
		{
			// no result returned
			$this->addToLog( "ERROR: sendAuth() #1" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function accountRegistration( $reg_email = null, $reg_name = null )
	{
		$packet = $this->sendIq( $this->server, "get", "reg_01", "jabber:iq:register" );

		if ( $packet )
		{
			$key = $this->getInfoFromIqKey( $packet ); // just in case a key was passed back from the server
			unset( $packet );

			$payload = "<username>{$this->username}</username>
						<password>{$this->password}</password>
						<email>$reg_email</email>
						<name>$reg_name</name>\n";

			$payload .= ( $key )? "<key>$key</key>\n" : "";
			$packet   = $this->sendIq( $this->server, "set", "reg_01", "jabber:iq:register", $payload );

			if ( $this->getInfoFromIqType( $packet ) == "result" )
			{
				if ( isset( $packet['iq']['#']['query'][0]['#']['registered'][0]['#'] ) )
					$return_code = 1;
				else
					$return_code = 2;

				if ( $this->resource )
					$this->jid = "{$this->username}@{$this->server}/{$this->resource}";
				else
					$this->jid = "{$this->username}@{$this->server}";
			}
			else if ( $this->getInfoFromIqType( $packet ) == "error" )
			{
				if ( isset( $packet['iq']['#']['error'][0]['#'] ) )
					$return_code = "Error " . $packet['iq']['#']['error'][0]['@']['code'] . ": " . $packet['iq']['#']['error'][0]['#'];
			}

			return $return_code;
		}
		else
		{
			return 3;
		}
	}

	/**
	 * @access public
	 */
	function sendPacket( $xml )
	{
		$xml = trim( $xml );

		if ( $this->CONNECTOR->writeToSocket( $xml ) )
		{
			$this->addToLog( "SEND: $xml" );
			return true;
		}
		else
		{
			$this->addToLog( "ERROR: sendPacket() #1" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function listen()
	{
		unset( $incoming );

		while ( $line = $this->CONNECTOR->readFromSocket( 4096 ) )
			$incoming .= $line;

		$incoming = trim( $incoming );

		if ( $incoming != "" )
			$this->addToLog( "RECV: $incoming" );

		if ( $incoming != "" )
		{
			$temp = $this->_split_incoming( $incoming );

			for ( $a = 0; $a < count( $temp ); $a++ )
				$this->packet_queue[] = $this->xmlize( $temp[$a] );
		}

		return true;
	}

	/**
	 * @access public
	 */
	function stripJID( $jid = null )
	{
		preg_match( "/(.*)\/(.*)/Ui", $jid, $temp );
		return ( $temp[1] != "" )? $temp[1] : $jid;
	}

	/**
	 * @access public
	 */
	function sendMessage( $to, $type = "normal", $id = null, $content = null, $payload = null )
	{
		if ( $to && is_array( $content ) )
		{
			if ( !$id )
				$id = $type . "_" . time();

			$content = $this->_array_htmlspecialchars( $content );
			$xml     = "<message to='$to' type='$type' id='$id'>\n";

			if ( $content['subject'] )
				$xml .= "<subject>" . $content['subject'] . "</subject>\n";

			if ( $content['thread'] )
				$xml .= "<thread>" . $content['thread'] . "</thread>\n";

			$xml .= "<body>" . $content['body'] . "</body>\n";
			$xml .= $payload;
			$xml .= "</message>\n";

			if ( $this->sendPacket( $xml ) )
			{
				return true;
			}
			else
			{
				$this->addToLog( "ERROR: sendMessage() #1" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: sendMessage() #2" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function sendPresence( $type = null, $to = null, $status = null, $show = null, $priority = null )
	{
		$xml  = "<presence";
		$xml .= ( $to   )? " to='$to'"     : "";
		$xml .= ( $type )? " type='$type'" : "";
		$xml .= ( $status || $show || $priority )? ">\n" : " />\n";

		$xml .= ( $status   )? "	<status>$status</status>\n" : "";
		$xml .= ( $show     )? "	<show>$show</show>\n"       : "";
		$xml .= ( $priority )? "	<priority>$priority</priority>\n" : "";

		$xml .= ( $status || $show || $priority )? "</presence>\n" : "";

		if ( $this->sendPacket( $xml ) )
		{
			return true;
		}
		else
		{
			$this->addToLog( "ERROR: sendPresence() #1" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function sendError( $to, $id = null, $error_number, $error_message = null )
	{
		$xml  = "<iq type='error' to='$to'";
		$xml .= ( $id )? " id='$id'" : "";
		$xml .= ">\n";
		$xml .= "	<error code='$error_number'>";
		$xml .= ( $error_message )? $error_message : $this->error_codes[$error_number];
		$xml .= "</error>\n";
		$xml .= "</iq>";

		$this->sendPacket( $xml );
	}

	/**
	 * @access public
	 */
	function rosterUpdate()
	{
		$roster_request_id = "roster_" . time();
		$incoming_array    = $this->sendIq( null, "get", $roster_request_id, "jabber:iq:roster" );

		if ( is_array( $incoming_array ) )
		{
			if ( $incoming_array['iq']['@']['type'] == "result" && 
				 $incoming_array['iq']['@']['id']   == $roster_request_id && 
				 $incoming_array['iq']['#']['query']['0']['@']['xmlns'] == "jabber:iq:roster" )
			{
				$number_of_contacts = count( $incoming_array['iq']['#']['query'][0]['#']['item'] );
				$this->roster = array();

				for ( $a = 0; $a < $number_of_contacts; $a++ )
				{
					$this->roster[$a] = array(	
						"jid"			=> strtolower( $incoming_array['iq']['#']['query'][0]['#']['item'][$a]['@']['jid'] ),
						"name"			=> $incoming_array['iq']['#']['query'][0]['#']['item'][$a]['@']['name'],
						"subscription"	=> $incoming_array['iq']['#']['query'][0]['#']['item'][$a]['@']['subscription'],
						"group"			=> $incoming_array['iq']['#']['query'][0]['#']['item'][$a]['#']['group'][0]['#']
					);
				}

				return true;
			}
			else
			{
				$this->addToLog( "ERROR: rosterUpdate() #1" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: rosterUpdate() #2" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function rosterAddUser( $jid = null, $id = null, $name = null)
	{
		$id = ( $id )? $id : "adduser_" . time();

		if ( $jid )
		{
			$payload  = "		<item jid='$jid'";
			$payload .= ( $name )? " name='" . htmlspecialchars( $name ) . "'" : "";
			$payload .= "/>\n";
			$packet   = $this->sendIq( null, "set", $id, "jabber:iq:roster", $payload );

			if ( $this->getInfoFromIqType( $packet ) == "result" )
			{
				$this->rosterUpdate();
				return true;
			}
			else
			{
				$this->addToLog( "ERROR: rosterAddUser() #2" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: rosterAddUser() #1" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function rosterRemoveUser( $jid = null, $id = null )
	{
		if ( $jid && $id )
		{
			$packet = $this->sendIq( null, "set", $id, "jabber:iq:roster", "<item jid='$jid' subscription='remove'/>" );

			if ( $this->getInfoFromIqType( $packet ) == "result" )
			{
				$this->rosterUpdate();
				return true;
			}
			else
			{
				$this->addToLog( "ERROR: rosterRemoveUser() #2" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: rosterRemoveUser() #1" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function rosterExistsJID( $jid = null )
	{
		if ( $jid )
		{
			if ( $this->roster )
			{
				for ( $a = 0; $a < count( $this->roster ); $a++ )
				{
					if ( $this->roster[$a]['jid'] == strtolower( $jid ) )
						return true;
				}
			}
			else
			{
				$this->addToLog( "ERROR: rosterExistsJID() #2" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: rosterExistsJID() #1" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function getFirstFromQueue()
	{
		return array_shift( $this->packet_queue );
	}

	/**
	 * @access public
	 */
	function getFromQueueById( $packet_type, $id )
	{
		$found_message = false;

		foreach ( $this->packet_queue as $key => $value )
		{
			if ( $value[$packet_type]['@']['id'] == $id )
			{
				$found_message = $value;
				unset( $this->packet_queue[$key] );

				break;
			}
		}

		return ( is_array( $found_message ) )? $found_message : false;
	}

	/**
	 * @access public
	 */
	function callHandler( $packet = null )
	{
		$packet_type	= $this->_get_packet_type( $packet );

		if ( $packet_type == "message" )
		{
			$type		= $packet['message']['@']['type'];
			$type		= ( $type != "" )? $type : "normal";
			$funcmeth	= "handler_message_$type";
		}
		else if ( $packet_type == "iq" )
		{
			$namespace	= $packet['iq']['#']['query'][0]['@']['xmlns'];
			$namespace	= str_replace( ":", "_", $namespace );
			$funcmeth	= "handler_iq_$namespace";
		}
		else if ( $packet_type == "presence" )
		{
			$type		= $packet['presence']['@']['type'];
			$type		= ( $type != "" )? $type : "available";
			$funcmeth	= "handler_presence_$type";
		}

		if ( $funcmeth != "" )
		{
			if ( function_exists( $funcmeth ) )
			{
				call_user_func( $funcmeth, $packet );
			}
			else if ( method_exists( $this, $funcmeth ) )
			{
				call_user_func( array( &$this, $funcmeth ), $packet );
			}
			else
			{
				$this->handler_NOT_IMPLEMENTED( $packet );
				$this->addToLog( "ERROR: callHandler() #1 - neither method nor function $funcmeth() available" );
			}
		}
	}

	/**
	 * @access public
	 */
	function cruiseControl( $seconds = -1 )
	{
		$count = 0;

		while ( $count != $seconds )
		{
			$this->listen();

			do 
			{
				$packet = $this->getFirstFromQueue();

				if ( $packet )
					$this->callHandler( $packet );
			} while ( count( $this->packet_queue ) > 1 );

			$count += 0.25;
			usleep( 250000 );
			
			if ( $this->last_ping_time != date( "H:i" ) )
			{
				$this->sendPacket( " " );
				$this->last_ping_time = date( "H:i" );
			}
		}

		return true;
	}

	/**
	 * @access public
	 */
	function subscriptionAcceptRequest( $to = null )
	{
		return ( $to )? $this->sendPresence( "subscribed", $to ) : false;
	}

	/**
	 * @access public
	 */
	function subscriptionDenyRequest( $to = null )
	{
		return ( $to )? $this->sendPresence( "unsubscribed", $to ) : false;
	}

	/**
	 * @access public
	 */
	function subscribe( $to = null )
	{
		return ( $to )? $this->sendPresence( "subscribe", $to ) : false;
	}

	/**
	 * @access public
	 */
	function unsubscribe( $to = null )
	{
		return ( $to )? $this->sendPresence( "unsubscribe", $to ) : false;
	}

	/**
	 * @access public
	 */
	function sendIq( $to = null, $type = "get", $id = null, $xmlns = null, $payload = null )
	{
		if ( !preg_match( "/^(get|set|result|error)$/", $type ) )
		{
			unset( $type );
			$this->addToLog( "ERROR: sendIq() #2 - type must be 'get', 'set', 'result' or 'error'" );
			
			return false;
		}
		else if ( $id && $xmlns )
		{
			$xml  = "<iq type='$type' id='$id'";
			$xml .= ( $to )? " to='$to'" : "";
			$xml .= ">
						<query xmlns='$xmlns'>
							$payload
						</query>
					</iq>";

			$this->sendPacket( $xml );
			sleep( $this->iq_sleep_timer );
			$this->listen();

			return ( preg_match( "/^(get|set)$/", $type ) )? $this->getFromQueueById( "iq", $id ) : true;
		}
		else
		{
			$this->addToLog( "ERROR: sendIq() #1 - to, id and xmlns are mandatory" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function getVCard( $jid = null, $id = null )
	{
		if ( !$id )
			$id = "vCard_" . md5( time() . $_SERVER['REMOTE_ADDR'] );

		if ( $jid )
		{
			$xml = "<iq type='get' to='$jid' id='$id'>
						<vCard xmlns='vcard-temp'/>
					</iq>";

			$this->sendPacket( $xml );
			sleep( $this->iq_sleep_timer );
			$this->listen();

			return $this->getFromQueueById( "iq", $id );
		}
		else
		{
			$this->addToLog( "ERROR: getVCard() #1 - to and id are mandatory" );
			return false;
		}
	}

	/**
	 * @access public
	 */
	function printLog()
	{
		if ( $this->enable_logging )
		{
			if ( $this->log_filehandler )
			{
				echo "<h2>Logging enabled, logged events have been written to the file {$this->log_filename}.</h2>\n";
			}
			else
			{
				echo "<h2>Logging enabled, logged events below:</h2>\n";
				echo "<pre>\n";
				echo ( count( $this->log_array ) > 0 )? implode( "\n\n\n", $this->log_array ) : "No logged events.";
				echo "</pre>\n";
			}
		}
	}

	/**
	 * @access public
	 */
	function addToLog( $string )
	{
		if ( $this->enable_logging )
		{
			if ( $this->log_filehandler )
				fwrite( $this->log_filehandler, $string . "\n\n" );
			else
				$this->log_array[] = htmlspecialchars( $string );
		}
	}


	// <message/> parsers

	/**
	 * @access public
	 */
	function getInfoFromMessageFrom( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['message']['@']['from'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromMessageType( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['message']['@']['type'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromMessageId( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['message']['@']['id'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromMessageThread( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['message']['#']['thread'][0]['#'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromMessageSubject( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['message']['#']['subject'][0]['#'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromMessageBody( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['message']['#']['body'][0]['#'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromMessageError( $packet = null )
	{
		$error = preg_replace( "/^\/$/", "", ( $packet['message']['#']['error'][0]['@']['code'] . "/" . $packet['message']['#']['error'][0]['#'] ) );
		return ( is_array( $packet ) )? $error : false;
	}


	// <iq/> parsers

	/**
	 * @access public
	 */
	function getInfoFromIqFrom( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['iq']['@']['from'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromIqType( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['iq']['@']['type'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromIqId( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['iq']['@']['id'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromIqKey( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['iq']['#']['query'][0]['#']['key'][0]['#'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromIqError( $packet = null )
	{
		$error = preg_replace( "/^\/$/", "", ( $packet['iq']['#']['error'][0]['@']['code'] . "/" . $packet['iq']['#']['error'][0]['#'] ) );
		return ( is_array( $packet ) )? $error : false;
	}


	// <presence/> parsers

	/**
	 * @access public
	 */
	function getInfoFromPresenceFrom( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['presence']['@']['from'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromPresenceType( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['presence']['@']['type'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromPresenceStatus( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['presence']['#']['status'][0]['#'] : false;
	}
	
	/**
	 * @access public
	 */
	function getInfoFromPresenceShow( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['presence']['#']['show'][0]['#'] : false;
	}

	/**
	 * @access public
	 */
	function getInfoFromPresencePriority( $packet = null )
	{
		return ( is_array( $packet ) )? $packet['presence']['#']['priority'][0]['#'] : false;
	}

	
	// <message/> handlers

	/**
	 * @access public
	 */
	function handler_message_normal( $packet )
	{
		$from = $packet['message']['@']['from'];
		$this->addToLog( "EVENT: Message (type normal) from $from" );
	}

	/**
	 * @access public
	 */
	function handler_message_chat( $packet )
	{
		$from = $packet['message']['@']['from'];
		$this->addToLog( "EVENT: Message (type chat) from $from" );
	}

	/**
	 * @access public
	 */
	function handler_message_groupchat( $packet )
	{
		$from = $packet['message']['@']['from'];
		$this->addToLog( "EVENT: Message (type groupchat) from $from" );
	}

	/**
	 * @access public
	 */
	function handler_message_headline( $packet )
	{
		$from = $packet['message']['@']['from'];
		$this->addToLog( "EVENT: Message (type headline) from $from" );
	}

	/**
	 * @access public
	 */
	function handler_message_error( $packet )
	{
		$from = $packet['message']['@']['from'];
		$this->addToLog( "EVENT: Message (type error) from $from" );
	}

	// <iq/> handlers

	/**
	 * Application version updates.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_autoupdate( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:autoupdate from $from" );
	}

	/**
	 * Interactive server component properties.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_agent( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:agent from $from" );
	}

	/**
	 * Method to query interactive server components.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_agents( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:agents from $from" );
	}

	/**
	 * Simple client authentication.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_auth( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:auth from $from" );
	}

	/**
	 * Out of band data.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_oob( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:oob from $from" );
	}

	/**
	 * Method to store private data on the server.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_private( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:private from $from" );
	}

	/**
	 * Method for interactive registration.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_register( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:register from $from" );
	}
	
	/**
	 * Client roster management.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_roster( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:roster from $from" );
	}

	/**
	 * Method for searching a user database.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_search( $packet )
	{
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: jabber:iq:search from $from" );
	}

	/**
	 * Method for requesting the current time.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_time( $packet )
	{
		$type = $this->getInfoFromIqType( $packet );
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );
		$id   = ( $id != "" )? $id : "time_" . time();

		if ( $type == "get" )
		{
			$payload = "<utc>" . gmdate( "Ydm\TH:i:s" ) . "</utc>
						<tz>" . date( "T" ) . "</tz>
						<display>" . date( "Y/d/m h:i:s A" ) . "</display>";

			$this->sendIq( $from, "result", $id, "jabber:iq:time", $payload );
		}

		$this->addToLog( "EVENT: jabber:iq:time (type $type) from $from" );
	}

	/**
	 * Method for requesting version.
	 *
	 * @access public
	 */
	function handler_iq_jabber_iq_version( $packet )
	{
		$type = $this->getInfoFromIqType( $packet );
		$from = $this->getInfoFromIqFrom( $packet );
		$id	  = $this->getInfoFromIqId( $packet );
		$id	  = ( $id != "" )? $id : "version_" . time();

		if ( $type == "get" )
		{
			$payload = "<name>{$this->iq_version_name}</name>
						<os>{$this->iq_version_os}</os>
						<version>{$this->iq_version_version}</version>";

			$this->sendIq( $from, "result", $id, "jabber:iq:version", $payload );
		}

		$this->addToLog( "EVENT: jabber:iq:version (type $type) from $from" );
	}


	// <presence/> handlers

	/**
	 * @access public
	 */
	function handler_presence_available( $packet )
	{
		$from = $this->getInfoFromPresenceFrom( $packet );

		$show_status = $this->getInfoFromPresenceStatus( $packet ) . " / " . $this->getInfoFromPresenceShow( $packet );
		$show_status = ( $show_status != " / " )? " ($addendum)" : "";

		$this->addToLog( "EVENT: Presence (type: available) - $from is available $show_status" );
	}

	/**
	 * @access public
	 */
	function handler_presence_unavailable( $packet )
	{
		$from = $this->getInfoFromPresenceFrom( $packet );

		$show_status = $this->getInfoFromPresenceStatus( $packet ) . " / " . $this->getInfoFromPresenceShow( $packet );
		$show_status = ( $show_status != " / " )? " ($addendum)" : "";

		$this->addToLog( "EVENT: Presence (type: unavailable) - $from is unavailable $show_status" );
	}

	/**
	 * @access public
	 */
	function handler_presence_subscribe( $packet )
	{
		$from = $this->getInfoFromPresenceFrom( $packet );
		$this->subscriptionAcceptRequest( $from );
		$this->rosterUpdate();

		$this->log_array[] = "<b>Presence:</b> (type: subscribe) - Subscription request from $from, was added to \$this->subscription_queue, roster updated";
	}

	/**
	 * @access public
	 */
	function handler_presence_subscribed( $packet )
	{
		$from = $this->getInfoFromPresenceFrom( $packet );
		$this->rosterUpdate();

		$this->addToLog( "EVENT: Presence (type: subscribed) - Subscription allowed by $from, roster updated" );
	}

	/**
	 * @access public
	 */
	function handler_presence_unsubscribe( $packet )
	{
		$from = $this->getInfoFromPresenceFrom( $packet );
		$this->sendPresence( "unsubscribed", $from );
		$this->rosterUpdate();

		$this->addToLog( "EVENT: Presence (type: unsubscribe) - Request to unsubscribe from $from, was automatically approved, roster updated" );
	}

	/**
	 * @access public
	 */
	function handler_presence_unsubscribed( $packet )
	{
		$from = $this->getInfoFromPresenceFrom( $packet );
		$this->rosterUpdate();

		$this->addToLog( "EVENT: Presence (type: unsubscribed) - Unsubscribed from $from's presence" );
	}

	/**
	 * Generic handler for unsupported requests.
	 *
	 * @access public
	 */
	function handler_NOT_IMPLEMENTED( $packet )
	{
		$packet_type = $this->_get_packet_type( $packet );
		$from		 = call_user_func( array( &$this, "GetInfoFrom" . ucfirst( $packet_type ) . "From" ), $packet );
		$id			 = call_user_func( array( &$this, "GetInfoFrom" . ucfirst( $packet_type ) . "Id"   ), $packet );

		$this->sendError( $from, $id, 501 );
		$this->addToLog( "EVENT: Unrecognized <$packet_type/> from $from" );
	}

	/**
	 * @access public
	 */
	function xmlize( $data )
	{
		$vals = $index = $array = array();
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE,   1 );
		xml_parse_into_struct( $parser, $data, $vals, $index );
		xml_parser_free( $parser );

		$i = 0;
		$tagname = $vals[$i]['tag'];
		$array[$tagname]['@'] = $vals[$i]['attributes'];
		$array[$tagname]['#'] = $this->_xml_depth( $vals, $i );

		return $array;
	}

	/**
	 * @access public
	 */
	function traverseXMLize( $array, $arrName = "array", $level = 0 )
	{
		if ( $level == 0 )
			echo "<pre>";

		while ( list( $key, $val ) = @each( $array ) )
		{
			if ( is_array( $val ) )
				$this->traverseXMLize( $val, $arrName . "[" . $key . "]", $level + 1 );
			else
				echo '$' . $arrName . '[' . $key . '] = "' . $val . "\"\n";
		}

		if ( $level == 0 )
			echo "</pre>";
	}
	
	
	// private methods

	/**
	 * @access private
	 */
	function _sendauth_0k( $zerok_token, $zerok_sequence )
	{
		// initial hash of password
		$zerok_hash = mhash( MHASH_SHA1, $this->password );
		$zerok_hash = bin2hex( $zerok_hash );

		// sequence 0: hash of hashed-password and token
		$zerok_hash = mhash( MHASH_SHA1, $zerok_hash . $zerok_token );
		$zerok_hash = bin2hex( $zerok_hash );

		// repeat as often as needed
		for ( $a = 0; $a < $zerok_sequence; $a++ )
		{
			$zerok_hash = mhash( MHASH_SHA1, $zerok_hash );
			$zerok_hash = bin2hex( $zerok_hash );
		}

		$payload = "<username>{$this->username}</username>
					<hash>$zerok_hash</hash>
					<resource>{$this->resource}</resource>";

		$packet = $this->sendIq( null, "set", $this->auth_id, "jabber:iq:auth", $payload );

		// was a result returned?
		if ( $this->getInfoFromIqType( $packet ) == "result" && $this->getInfoFromIqId( $packet ) == $this->auth_id )
		{
			return true;
		}
		else
		{
			$this->addToLog( "ERROR: _sendauth_0k() #1" );
			return false;
		}
	}

	/**
	 * @access private
	 */
	function _sendauth_digest()
	{
		$payload = "<username>{$this->username}</username>
					<resource>{$this->resource}</resource>
					<digest>" . bin2hex( mhash( MHASH_SHA1, $this->stream_id . $this->password ) ) . "</digest>";

		$packet = $this->sendIq( null, "set", $this->auth_id, "jabber:iq:auth", $payload );

		// was a result returned?
		if ( $this->getInfoFromIqType( $packet ) == "result" && $this->getInfoFromIqId( $packet ) == $this->auth_id )
		{
			return true;
		}
		else
		{
			$this->addToLog( "ERROR: _sendauth_digest() #1" );
			return false;
		}
	}

	/**
	 * @access private
	 */
	function _sendauth_plaintext()
	{
		$payload = "<username>{$this->username}</username>
					<password>{$this->password}</password>
					<resource>{$this->resource}</resource>";

		$packet = $this->sendIq( null, "set", $this->auth_id, "jabber:iq:auth", $payload );

		// was a result returned?
		if ( $this->getInfoFromIqType( $packet ) == "result" && $this->getInfoFromIqId( $packet ) == $this->auth_id )
		{
			return true;
		}
		else
		{
			$this->addToLog( "ERROR: _sendauth_plaintext() #1" );
			return false;
		}
	}

	/**
	 * @access private
	 */
	function _listen_incoming()
	{
		unset( $incoming );

		while ( $line = $this->CONNECTOR->readFromSocket( 4096 ) )
			$incoming .= $line;

		$incoming = trim( $incoming );

		if ( $incoming != "" )
			$this->addToLog( "RECV: $incoming" );

		return $this->xmlize( $incoming );
	}

	/**
	 * @access private
	 */
	function _check_connected()
	{
		$incoming_array = $this->_listen_incoming();

		if ( is_array( $incoming_array ) )
		{
			if ( $incoming_array["stream:stream"]['@']['from']  == $this->server   && 
				 $incoming_array["stream:stream"]['@']['xmlns'] == "jabber:client" && 
				 $incoming_array["stream:stream"]['@']["xmlns:stream"] == "http://etherx.jabber.org/streams" )
			{
				$this->stream_id = $incoming_array["stream:stream"]['@']['id'];
				return true;
			}
			else
			{
				$this->addToLog( "ERROR: _check_connected() #1" );
				return false;
			}
		}
		else
		{
			$this->addToLog( "ERROR: _check_connected() #2" );
			return false;
		}
	}

	/**
	 * @access private
	 */
	function _get_packet_type( $packet = null )
	{
		if ( is_array( $packet ) )
		{
			reset( $packet );
			$packet_type = key( $packet );
		}

		return ( $packet_type )? $packet_type : false;
	}

	/**
	 * @access private
	 */
	function _split_incoming( $incoming )
	{
		$temp  = preg_split( "/<(message|iq|presence|stream)/", $incoming, -1, PREG_SPLIT_DELIM_CAPTURE );
		$array = array();

		for ( $a = 1; $a < count( $temp ); $a = $a + 2 )
			$array[] = "<" . $temp[$a] . $temp[($a + 1)];

		return $array;
	}

	/**
	 * @access private
	 */
	function _create_logfile()
	{
		if ( $this->log_filename != '' && $this->enable_logging )
			$this->log_filehandler = fopen( $this->log_filename, 'w' );
	}

	/**
	 * @access private
	 */
	function _close_logfile()
	{
		if ( $this->log_filehandler )
			fclose( $this->log_filehandler );
	}

	/**
	 * @access private
	 */
	function _array_htmlspecialchars( $array )
	{
		if ( is_array( $array ) )
		{
			foreach ( $array as $k => $v )
			{
				if ( is_array( $v ) )
					$v = $this->_array_htmlspecialchars( $v );
				else
					$v = htmlspecialchars( $v );
			}
		}

		return $array;
	}

	/**
	 * @access private
	 */
	function _xml_depth( $vals, &$i )
	{
		$children = array();

		if ( $vals[$i]['value'] )
			array_push( $children, trim( $vals[$i]['value'] ) );

		while ( ++$i < count( $vals ) )
		{
			switch ( $vals[$i]['type'] )
			{
				case 'cdata':
					array_push( $children, trim( $vals[$i]['value'] ) );
	 				break;

				case 'complete':
					$tagname = $vals[$i]['tag'];
					$size    = sizeof( $children[$tagname] );
					$children[$tagname][$size]['#'] = trim( $vals[$i]['value'] );
					
					if ( $vals[$i]['attributes'] )
						$children[$tagname][$size]['@'] = $vals[$i]['attributes'];
					
					break;

				case 'open':
					$tagname = $vals[$i]['tag'];
					$size    = sizeof( $children[$tagname] );
					
					if ( $vals[$i]['attributes'] )
					{
						$children[$tagname][$size]['@'] = $vals[$i]['attributes'];
						$children[$tagname][$size]['#'] = $this->_xml_depth( $vals, $i );
					}
					else
					{
						$children[$tagname][$size]['#'] = $this->_xml_depth( $vals, $i );
					}
					
					break;

				case 'close':
					return $children;
					break;
			}
		}

		return $children;
	}
} // END OF Jabber

?>
