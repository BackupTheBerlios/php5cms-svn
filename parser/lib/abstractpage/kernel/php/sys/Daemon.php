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


define( "DAEMON_VERBOSE",         true );
define( "DAEMON_VERBOSE_LEVEL",      2 );
define( "DAEMON_BUFFER",          1024 );
define( "DAEMON_MAX_CONNECTIONS",    4 );
define( "DAEMON_MAX_SHOW_PROMPT", true );

/**
 * Can be set to 'standalone' for listening on specified port. When run
 * as an inetd service, this class reads from stdin and outputs to
 * stdout. and hence the address/port doesn't make any sense in the
 * inetd context
 */
define( "DAEMON_SERVER_TYPE", 'standalone' ); 
		

/**
 * What does this class do?
 *
 * This class can help you create easy to use TCP Daemons that can
 * listen on a specified port. 
 *
 * The main aim of writing this class was to help ppl easily define
 * their own FTP-like protocols where they can create apps that can read
 * commands and respond in return.
 *
 * This program needs a PHP interpreter compiled with the --enable-sockets option.
 *
 * If you needed to write an app that wants to use a simple command based protocol
 * over the network (like FTP) you can use this daemon. The implementation part
 * should be understandable from the test script provided herewith.
 *
 * To run the daemon, you need the CGI/CLI version of the php interpreter.
 * I wrote this script on Debian GNU/Linux and am assuming that you've put your
 * PHP binary in the place where the standard php4-cgi debian package puts it.
 * (ie., /usr/lib/cgi-bin/php4).
 *
 * If the program starts up successfully, you can try telnetting to port 19123
 * (thats where the sample script starts listening) as following:
 *
 * $ telnet 127.0.0.1 19123
 * GNUPHPtial daemon (0.0.1b) (Debian GNU/Linux)
 * foo> _
 *
 * That is the prompt, you can type in various things there. HELP should show
 * the list of valid commands.
 *
 * @package sys
 */ 
 
class Daemon extends PEAR
{
	/**
	 * @access public
	 */
	var $stdin;
	
	/**
	 * @access public
	 */
	var $stdout;
	
	/**
	 * @access public
	 */
	var $socket;
	
	/**
	 * @access public
	 */
	var $msg_socket;
	
	/**
	 * @access public
	 */
	var $Address;
	
	/**
	 * @access public
	 */
	var $Port;
	
	/**
	 * @access public
	 */
	var $Header;
	
	/**
	 * @access public
	 */
	var $first_time = true;
	
	/**
	 * @access public
	 */
	var $PromptString = 'foo> ';

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Daemon()
	{
		/* 
		 * We set the max. execution time to 0 (disable) to ensure that the php
		 * script does not end abrubptly while listening over a socket ...
		 * or while having a slow interaction with the client
		 */
		set_time_limit( 0 ); 

		/*
		 * Also we need to make sure that the data from / to the client
		 * isn't buffered. So we make all data go through with getting
		 * buffered.
		 */
		ob_implicit_flush();
	}


	/**
	 * @access public
	 */	
	function verbose( $level, $msg )
	{
		if ( DAEMON_VERBOSE && $level <= DAEMON_VERBOSE_LEVEL && DAEMON_SERVER_TYPE != 'inetd' )
			echo str_repeat( "*", $level ) . " $msg " . str_repeat( "*", $level ) . "\n";
	}

	/**
	 * @access public
	 */
	function setAddress( $ipaddr )
	{
		$this->Address = $ipaddr;
	}

	/**
	 * @access public
	 */
	function setPort( $port )
	{
		$this->Port = $port;
	}

	/**
	 * @access public
	 */
	function start()
	{
		if ( DAEMON_SERVER_TYPE == 'inetd' )
		{
			/*
			 * This daemon is already listening to a socket. Thanks to inetd.
			 * we just output to stdout and read from stdin.
 			 */
			$this->stdin = fopen( 'php://stdin', 'r' );
		}
		else
		{
			$this->verbose( 1, "Server Ready for connections" );
			
			// This is being run as a standalone server. lets create a socket
			$sock = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
			$this->verbose( 3, "Socket created" );
			
			if ( $sock < 0 ) 
			{
				// error!
				$this->sock_die( 'Couldn\'t create a socket!', $sock );
			}
			
			socket_setopt( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
			$this->verbose( 3, "Making socket reuseable" );
			$ret = socket_bind( $sock, $this->Address, $this->Port );
			
			if ( $ret < 0 ) 
			{
				// error!
				$this->sock_die( 'Couldn\'t bind socket!', $ret );
			}
			
			$this->verbose( 3, "Socket bind complete" );
			$ret = socket_listen( $sock, DAEMON_MAX_CONNECTIONS );
			
			if ( $ret < 0 ) 
			{
				// error!
				$this->sock_die( 'listen failed!', $ret );
			}

			$this->socket = $sock;
			$this->sock_message_socket_create();
		}
	}

	/**
	 * @access public
	 */
	function sock_message_socket_create()
	{
		$this->msg_socket = socket_accept( $this->socket );
		
		if ( $this->msg_socket < 0 )
		{
			// error
			$this->sock_die( 'socket accept failed!', $this->msg_socket );
		}
		
		socket_setopt( $this->msg_socket, SOL_SOCKET, SO_REUSEADDR, 1 );
	}

	/**
	 * @access public
	 */
	function sock_reset()
	{
		$this->close();
		$this->sock_message_socket_create();
	}

	/**
	 * @access public
	 */
	function close()
	{
		if ( DAEMON_SERVER_TYPE != 'inetd' )
		{
			$this->verbose( 1, "---------------Connection closed------------" );
			socket_shutdown( $this->msg_socket );
			// socket_shutdown ($this->socket);
		}
	}

	/**
	 * @access public
	 */
	function shutdown()
	{
		if ( DAEMON_SERVER_TYPE != 'inetd' )
		{
			// because it just doesn't make sense
			// to have an 'inetd' service shut
			// itself down... ;-/
			$this->println( '*** Server Shutting down ***' );
			$this->verbose( 1, '=======Server Shutdown=========' );
			$this->close();
		} 
	}

	/**
	 * @access public
	 */
	function sock_die( $msg, $return_code, $to_be_closed )
	{
		echo "$msg: " . socket_strerror( $return_code );
		
		if ( $to_be_closed )
			socket_close( $this->msg_socket );
		
		exit;
	}

	/**
	 * @access public
	 */
	function ShowHeader()
	{
		if ( $this->first_time )
		{
			socket_getpeername( $this->msg_socket, $peer_addr, $peer_port );
			$this->verbose( 1, "---------Connection from $peer_addr-----------" );
			$this->Println( $this->Header );
		}
		
		$this->first_time = false;
	}

	/**
	 * @access public
	 */
	function Println( $string )
	{
		// fputs ($this->stdout, trim ($string) . "\n");
		$this->_Print( $string . "\n" );
	}

	/**
	 * @access public
	 */
	function Read()
	{
		if ( DAEMON_SERVER_TYPE == 'inetd' )
		{
			return trim( fgets( $this->stdin, DAEMON_BUFFER ) );
		}
		else
		{
			if ( ( $buf = socket_read( $this->msg_socket, DAEMON_BUFFER ) ) == false )
			{
				// error reading socket
				$this->sock_die( 'Error Reading from socket!', $buf, true );
				// true makes sock_die to close the socket in the end
			}
			else
			{
				$this->verbose( 5, '<<' . $buf );
				return trim( $buf );
			}
		}
	}

	/**
	 * @access public
	 */
	function showError( $Severity, $ErrorString )
	{
		$this->Println( $Severity . ':' . $ErrorString );
	}

	/**
	 * @access public
	 */
	function resetConnection()
	{
		$this->Println( 'goodbye!' );
		$this->first_time = true;
		
		if ( DAEMON_SERVER_TYPE == 'inetd' )
			exit;
		else
			$this->sock_reset();
	}

	/**
	 * @access public
	 */
	function isValidCommand( $cmd )
	{        
		if ( in_array( strtoupper( $cmd ), $this->valid_commands ) )
			return true;
		else
			return false;
	}

	/**
	 * @access public
	 */
	function tokenize( $command_line )
	{
		$raw_tokens = explode( ' ', trim( $command_line ) );

		// the first one is the command
		$command = $raw_tokens[0];

		// the rest are all parameters to the command
		$params = array_slice( $raw_tokens, 1 );
		$tokens['command'] = strtoupper( $command );
		$tokens['params']  = $params;

		return $tokens;
	}

	/**
	 * @access public
	 */
	function Tokenize( $command_line )
	{
		// this function is just an alias for tokenize
		return $this->tokenize( $command_line );
	}

	/**
	 * @access public
	 */
	function setCommands( $array )
	{
		$this->valid_commands = array();
		
		foreach ( $array as $item )
			$this->valid_commands[] = strtoupper( $item );
	}

	/**
	 * @access public
	 */        
	function CommandAction( $command, $callback = false )
	{
		static $defined_functions;
        
		/*
		the function ($callback) that is registered will be called back
		when the specified command is encountered.

		callback_function (string $command, array $params, daemon $this);

		daemon $this can be used to perform more actions here.. such as
		$this->CloseConnection(), etc.,
		*/
		if ( $this->isValidCommand( $command ) )
		{
			// command is valid. see if the name of a callback function was
			// passed to us...
			$command = strtoupper( $command );
			
			if ( $callback )
			{
				if ( !isset( $defined_functions ) )
					$defined_functions = get_defined_functions();
                                
				if ( in_array( $callback, $defined_functions['user'] ) )
				{
					$this->callbacks[$command][] = $callback;
					$this->callbacks[$command]   = array_unique( $this->callbacks[$command] );
				}
				else
				{
					$this->showError( 'FATAL', 'Could not call `' . $callback . '()` Function not defined!' );
					$this->resetConnection();
					
					exit;
				}
			}
			else
			{
				// no call back function was passed. Let's return the list of
				// callback functions that this command has...
				if ( empty( $this->callbacks[$command] ) )
					return array();
				else
					return $this->callbacks[$command];
			}
		}
	}

	/**
	 * @access public
	 */
	function showPrompt()
	{
		$this->_Print( $this->PromptString );
	}

	/**
	 * @access public
	 */
	function listen()
	{
		/*
		 * This is the main loop that will listen for commands and call
		 * the respective callback functions. 
	 	 */
		
		//enter a listening loop
		while ( true )
		{
			$this->ShowHeader();
			
			if ( DAEMON_MAX_SHOW_PROMPT )
				$this->showPrompt();
			
			$command_line = $this->Read();
			
			if ( !empty( $command_line ) )
			{
				$this->verbose( 4, "Received $command_line" );
                        
				$command_set = $this->tokenize( $command_line );
				$cmd    = $command_set['command'];
				$params = $command_set['params'];
                                
				if ( $this->isValidCommand( $cmd ) )
				{
					// see if this is registered in our callback function set
					$callbacks = $this->CommandAction( $cmd );
					
					if ( !empty( $callbacks ) )
					{
						// has callback functions... lets call them one by one
						foreach ( $callbacks as $function )
						{
							// call the callback function
							$status = $function( $command_set['command'], $command_set['params'], &$this ); 
							
							if ( $status == false )
							{
								// function says that we should exit...
								$this->resetConnection();
								// exit;
							}
						}
					}
					else
					{
						// NO EVENTS... 
						$this->Println( '`' . $command_set['command'] . '\' defined but not implemented' );
						$this->verbose( 1, '`' . $command_set['command'] . '\'not implemented!' );
					}
				}
				else
				{
					$this->showError( 'NOTIFY', 'Command `' . $command_set['command'] . '\' is unrecognized' );
				}
			}
		}
	}
	
	
	// private methods

	/**
	 * @access private
	 */	
	function _Print( $string )
	{
		// fputs ($this->stdout, $string);
		if ( DAEMON_SERVER_TYPE == 'inetd' )
		{
			echo $string;
		}
		else
		{
			$this->verbose( 5, '>>' . $string );
			socket_write( $this->msg_socket, $string, strlen( $string ) );
		}
	}
} // END OF Daemon

?>
