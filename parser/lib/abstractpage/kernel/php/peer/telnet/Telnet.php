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


using( 'peer.Socket' );


/**
 * Connect to a telnet host.
 *
 * @package peer_telnet
 */

class Telnet extends Socket 
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Telnet() 
	{
		$this->Socket();
		
		$this->setPort( 23 );
	}

	
	/**
	 * Specify the terminal type.
	 *
	 * @access public
	 */
	function setTerminalType( $ttype ) 
	{
		$this->ttype = $ttype;
	}

	/**
	 * Connect to the server.
	 *
	 * @access public
	 */
	function connect() 
	{
		$result = $this->open();
		$this->setBlocking( false );
		
		return $result;
	}

	/**
	 * Get the next cooked character.
	 *
	 * @access public
	 */
	function getchar() 
	{
		return $this->_nextcooked();
	}

	/**
	 * Determine whether data is waiting to be read.
	 *
	 * @access public
	 */
	function pending() 
	{
		$this->_bufread();
		
		if ( strlen( $this->data ) > 0 )
			return true;
		else
			return false;
	}

	/**
	 * Read a string from the server.
	 *
	 * @access public
	 */
	function read( $length = null ) 
	{
		$text  = '';
		$nread = 0;
		
		if ( $length !== null ) 
		{
			// read exact number of characters
			while ( !$this->eof() && $nread < $length ) 
			{
				$text .= $this->_nextcooked();
				$nread++;
			}
		} 
		else 
		{
			// read as many characters as are available
			while ( !$this->eof() && $this->pending() )
				$text .= $this->_nextcooked();
		}
		
		return $text;
	}

	/**
	 * Wait for a regular expression to be matched.
	 *
	 * @access public
	 */
	function expect( $string ) 
	{
		$text = '';
		
		while ( !$this->eof() && !preg_match( $string, $text ) )
			$text .= $this->_nextcooked();
		
		return $text;
	}

	
	// private methods
	
	/**
	 * @access private
	 */
	function _IAC( $cmd, $option = null ) 
	{
		if ( $option !== null )
			$this->write( chr( 255 ) . chr( $cmd ) . chr( $option ) );
		else
			$this->write( chr( 255 ) . chr( $cmd ) );
	}

	/**
	 * @access private
	 */
	function _nextraw() 
	{
		while ( !$this->pending() ) 
		{
			usleep( 20000 );
			$this->_bufread();
		}
		
		$char = substr( $this->data, 0, 1 );
		$this->data = substr( $this->data, 1, strlen( $this->data ) - 1 );
		
		return ord( $char );
	}

	/**
	 * @access private
	 */
	function _nextcooked() 
	{
		$char = $this->_nextraw();
		
		while ( $char == 255 ) 
		{
			$command = $this->_nextraw();

			if ( $command == 255 ) 
			{
				// two IAC's in a row means char 255
				return chr( 255 );
			}

			// WILL, WONT, DO, DONT have options
			if ( $command >= 251 && $command <= 254 )
				$option = $this->_nextraw();
			else
				$option = null;

			// process telnet command
			$this->_process( $command, $option );

			// get next character and continue
			$char = $this->_nextraw();
		}
		
		return chr( $char );
	}

	/**
	 * @access private
	 */
	function _process( $command, $option ) 
	{
		/* DEBUG
		$descr = array(
			240 => "SE",
			241 => "NOP",
			242 => "MARK",
			243 => "BREAK",
			244 => "INTR",
			245 => "ABORT",
			246 => "ARE-YOU-THERE",
			247 => "ERASE-CHAR",
			248 => "ERASE-LINE",
			249 => "GO-AHEAD",
			250 => "SB",
			251 => "WILL",
			252 => "WONT",
			253 => "DO",
			254 => "DONT",
			255 => "IAC"
		);

		echo "RECV {$descr[$command]}";
		
		if ( $option !== null )
			echo " {$option}";
		
		echo "\n";
		*/

		if ( $command == 253 ) 
		{
			// respond to DO commands
			switch ( $option ) 
			{
				case 31:	// Window size
			
				case 32:	// Term speed
			
				case 33:	// Remote flow control
			
				case 34:	// LINEMODE
			
				case 35:	// X Display
			
				case 36:	// Old Env.
			
				case 39:	// New Env.
			
				case 37:	// Authentication
			
				default:
					$this->_wont( $option );
					break;

				case 24:	// TERMINAL-TYPE
					$this->_will( $option );
					break;
			}
		} 
		else if ( $command == 251 ) 
		{
			// respond to WILL commands
			switch ( $option ) 
			{
				case 3:		// Suppress go ahead
				
				case 5:		// Give status
					$this->_do( $option );
					break;

				case 1:		// Echo

				case 38:	// Encrypt

				default:
					$this->_dont( $option );
					break;
			}
		} 
		else if ( $command == 250 ) 
		{
			// subnegotiation
			$option = $this->_nextraw();

			$params = array();
			$next   = $this->_nextraw();
			
			while ( $next !== 255 ) 
			{
				$params[] = $next;
				$next = $this->_nextraw();
			}
			
			$end = $this->_nextraw();
			
			if ( $end != 240 ) 
			{
				trigger_error(
					"telnet::process() --- error in subnegotiation",
					E_USER_ERROR
				);
			}

			$this->_subprocess( $option, $params );
		} 
		else 
		{
			// unsupported
			trigger_error(
				"telnet::process() --- unsupported command ({$command})",
				E_USER_ERROR
			);
		}
	}

	/**
	 * @access private
	 */
	function _subprocess( $option, $params ) 
	{
		if ( $option == 24 ) 
		{
			// TERMINAL-TYPE
			//   IS    0 (response)
			//   SEND  1 (request)
			if ( $params[0] == 1 ) 
			{
				// respond to TERMINAL-TYPE SEND with TERMINAL-TYPE IS
				$this->_subnegotiate( 24, array( chr( 0 ), $this->ttype ) );
			}
		} 
		else 
		{
			trigger_error(
				"telnet::subprocess() --- unsupported option ({$option})",
				E_USER_ERROR
			);
		}
	}

	/**
	 * @access private
	 */
	function _bufread() 
	{
		$this->data .= parent::read( 1024 );
	}

	/**
	 * @access private
	 */
	function _subnegotiate( $option, $params ) 
	{
		$this->_IAC( 250, $option );
		
		for ( $i = 0; $i < count( $params ); $i++ )
			$this->write( $params[$i] );
		
		$this->_IAC( 240 );
	}

	/**
	 * @access private
	 */
	function _will( $option ) 
	{
		$this->_IAC( 251, $option );
	}

	/**
	 * @access private
	 */
	function _wont( $option ) 
	{
		$this->_IAC( 252, $option );
	}

	/**
	 * @access private
	 */
	function _do( $option ) 
	{
		$this->_IAC( 253, $option );
	}

	/**
	 * @access private
	 */
	function _dont( $option ) 
	{
		$this->_IAC( 254, $option );
	}
} // END OF Telnet

?>
