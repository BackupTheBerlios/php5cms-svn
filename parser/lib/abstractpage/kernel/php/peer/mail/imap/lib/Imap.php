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


using( 'peer.mail.imap.lib.ImapFolder' );
using( 'peer.Net' );


/**
 * TODO: 
 *    - SEARCH
 *    - FETCH(HEADER,PIECE Done, need local file fetch)
 *    - STORE(???)
 *    - COPY(Duh)
 *
 *    - UID(???)
 *    - Utf7 encode/decode foldernames automagically(Utf7.object)
 *    - AUTHENTICATE
 *    - STATUS
 *    - CHECK
 *    - CLOSE
 *    - EXPUNGE
 *    - X<atom>(???)
 *
 * @package peer_mail_imap_lib
 */

class Imap extends PEAR
{
	/**
	 * @access public
	 */
	var $net;
	
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
	var $user_name;
	
	/**
	 * @access public
	 */
	var $password;

	/**
	 * @access public
	 */
	var $logged_in;

	/**
	 * @access public
	 */
	var $current_folder;
	
	/**
	 * @access public
	 */
	var $command_counter;

	/**
	 * @access public
	 */
	var $server_capabilities;

	/**
	 * @access public
	 */
	var $auto_cleanup;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Imap( $server = 'localhost', $port = 143, $user = '', $password = '' )
	{
		$this->net = new Net();
		$this->net->debug->Off();

		$this->server				= $server;
		$this->user_name			= $user;
		$this->password				= $password;
		$this->port					= $port;
		
		$this->logged_in			= 0;
		$this->command_counter		= 0;
		$this->new_line				= "\r\n";
		$this->current_folder 		= '';
		$this->server_capabilities  = array();
		
		// disconnects from server when doing a Logout
		$this->auto_cleanup  = 1;
	}

	
	/**
	 * @access public
	 * @todo check status of connection
	 */
	function Open()
	{
		if ( ! $this->net->connected )
		{
			$this->net->server = $this->server;
			$this->net->port   = $this->port;
			
			$this->net->Open();
			
			if ( $this->net->connected == 1 )
			{
				// Get the header garbage / hello
				// This might not work on all imap servers (gasp)
				$this->ReadMultiLine( '* OK' );
				$this->current_folder = '';
				
				// Get the server capabilities
				$this->Capability();
			}
		}
		
		return $this->net->connected;
	}

	/**
	 * @access public
	 */
	function Close()
	{
		return $this->net->Close();
	}

	/**
	 * @access public
	 */
	function Authenticate()
	{
		// KERBEROUSE and other support
		return false;
	}

	/**
	 * @access public
	 */
	function Login()
	{
		if ( ! $this->Open() )
			return false;

		if ( $this->logged_in != 1 && $this->net->connected == 1 )
		{
			$this->SendLine( 'LOGIN ' . $this->user_name . ' ' . $this->password );
			list( $command, $lines ) = $this->ReadMultiLine();
			$this->logged_in = $this->CommandOk( $command );
			$this->current_folder = '';
		}
		
		return $this->logged_in;
	}

	/**
	 * @access public
	 */
	function Logout()
	{
		if ( !$this->Open() )
			return false;
			
		if ( $this->logged_in == 1 && $this->net->connected == 1 )
		{
			$this->SendLine( 'LOGOUT' );
			list( $command, $lines ) = $this->ReadMultiLine();
			$this->logged_in = $this->CommandOk( $command );
			$this->current_folder = '';
		}
		
		// autocleanup
		if ( $this->auto_cleanup == 1 )
			$this->Close();

		return $this->logged_in;
	}

	/**
	 * @access public
	 */
	function Capability()
	{
		if ( !$this->Open() )
			return false;
	  
		$this->server_capabilities = array();
		$this->SendLine( 'CAPABILITY' );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
		{
			for ( $i = 0; $i < count( $lines ); $i++ )
			{
				$lines[$i] = str_replace( "\n", '', $lines[ $i ] );
				$lines[$i] = str_replace( "\r", '', $lines[ $i ] );
				
				if ( ereg( '\* CAPABILITY (.+)', $lines[ $i ], $regs ) )
				{
					$capa = explode( ' ', $regs[ 1 ] );
					
					for ( $x = 0; $x < count( $capa ); $x++ )
						$this->server_capabilities[ $capa[ $x ] ] = 1;
				}
			}
		}
		
		return true;
	}

	/**
	 * @access public
	 */
	function Noop()
	{
		if ( !$this->Open() )
			return false;
			
		$this->SendLine( 'NOOP' );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		return $lines;
	}

	
	// Authed functions

	/**
	 * @access public
	 */	
	function CreateFolder( $folder_name )
	{
		if ( !$this->Login() )
			return false;
		 
		$this->SendLine( 'CREATE ' . $folder_name );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
			return true;
      
		return false;
	}

	/**
	 * @access public
	 */
	function RemoveFolder( $folder_name )
	{
		if ( !$this->Login() )
			return false;

		$this->SendLine( 'DELETE ' . $folder_name );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
			return true;
      
		return false;
	}

	/**
	 * @access public
	 */
	function RenameFolder( $folder_name, $new_name )
	{
		if ( !$this->Login() )
			return false;
      
		$this->SendLine( 'RENAME ' . $folder_name . ' ' . $new_name );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
			return true;
      
		return false;
	}

	/**
	 * @access public
	 */
	function SubscribeToFolder( $folder_name )
	{
		if ( !$this->Login() )
			return false;
      
		$this->SendLine( 'SUBSCRIBE ' . $folder_name );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
			return true;
      
		return false;
	}

	/**
	 * @access public
	 */
	function UnsubscribeFromFolder( $folder_name )
	{
		if ( !$this->Login() )
			return false;
      
		$this->SendLine( 'UNSUBSCRIBE ' . $folder_name );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
			return true;
      
		return false;
	}

	/**
	 * @access public
	 */
	function ListFolders( $folder_pat = '', $folder_root = '' )
	{
		if ( !$this->Login() )
			return undef;
     
		$this->SendLine( 'LIST "' . $folder_root . '" "' . $folder_pat . '"' );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
		{
			$folders = array();
			
			for ( $i = 0; $i < count( $lines ); $i++ )
			{
				if ( eregi( '\\Noselect', $lines[ $i ] ) )
				{
					// Cannot select it - probably a directory
					$this->net->debug->Message( "Imap", "Not selectable." );
					$this->net->debug->Message( $lines[ $i ] );
				}
				else if ( ereg( '\* LIST \(\) (.+) (.+)', $lines[ $i ], $regs ) )
				{
					$folders = str_replace( "\r", '', str_replace( "\n", '', $regs[ 2 ] ) );
				}
				else if ( ereg( '\* LIST \(.+\) (.+) (.+)', $lines[ $i ], $regs ) )
				{
					// Strip the context off the leading
					$folder = $regs[ 2 ];
					$folder = ereg_replace( '^' . $folder_root, '', $folder );
					
					// No leading slashes please
					$folder    = ereg_replace( '^/', '', $folder );
					$folder    = str_replace( "\n", '',  $folder );
					$folder    = str_replace( "\r", '',  $folder );
					$folders[] = $folder;
				}
				else
				{
					$this->net->debug->Message( "Unhandled folder list exception." );
					$this->net->debug->Message( $lines[ $i ] );
				}
			}
			
			return $folders;
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function ListSubscribedFolders( $pat = '', $context = '' )
	{
		if ( ! $this->Login() )
			return undef;
      
		$this->SendLine( 'LSUB "' . $context . '" "' . $pat . '"' );
		list( $command, $lines ) = $this->ReadMultiLine();
		
		if ( $this->CommandOk( $command ) )
		{
			$folders = array(); 
			
			for ( $i = 0; $i < count( $lines ); $i++ )
			{
				if ( ereg( '\\Noselect', $lines[ $i ] ) )
				{
					$this->net->debug( 'non selectable' );
					$this->net->debug( $lines[ $i ] );
				}
				else if ( ereg( '\* LSUB \(\) \"(.+)\" (.+)', $lines[ $i ], $regs ) )
				{
					$folders[] = str_replace( "\r", '', str_replace( "\n", '', $regs[ 2 ] ) );
				}
				else if ( ereg( '\* LSUB \(.+\) \"(.+)\" (.+)', $lines[ $i ], $regs ) )
				{
					$folders[] = str_replace( "\r", '', str_replace( "\n", '', $regs[ 2 ] ) );
				}
				else
				{
					$this->net->debug( "Unhandled lsub exception." );
					$this->net->debug( $lines[ $i ] );
				}
			}
			
			return $folders;
		}
		
		return undef;
	}

	/**
	 * @access public
	 */
	function SelectFolder( $folder_name = '' )
	{
		if ( ! $this->Login() )
			return undef;

		// Keep folder state.
		if ( $folder_name != '' )
			$this->current_folder = $folder_name;
		else
			$folder_name = $this->current_folder;

		$this->SendLine( 'SELECT ' . $folder_name );
		list( $command, $lines ) = $this->ReadMultiLine();

		if ( $this->CommandOk( $command ) )
		{
			// It returned a folder description
			// Parse the description
			$imap_folder = new ImapFolder();
			$imap_folder->folder_name = $this->current_folder;

			for ( $i = 0; $i < count( $lines ); $i++ )
			{
				if ( ereg( '([0-9]+) EXISTS', $lines[ $i ], $regs ) )
					$imap_folder->messages = $regs[ 1 ];
           
				if ( ereg( '([0-9]+) RECENT', $lines[ $i ], $regs ) )
					$imap_folder->recent = $regs[ 1 ];
			}

			$imap_folder->imap_obj = $this;
			return $imap_folder;
		}

		return undef;
	}

	
	// Communication with the server stuff
	
	/**
	 * @access public
	 */
	function CommandOk( $command )
	{
		if ( ereg( '^OK', $command ) )
			return true;

		return false;
	}

	/**
	 * @access public
	 */
	function RawSend( $bleh )
	{
		$this->Open();
		$this->net->SendLine( $bleh );
	}

	/**
	 * @access public
	 */
	function RawSendLine( $line )
	{
		$this->Open();
		$this->net->SendLine( $line . $this->new_line );
	}

	/**
	 * @access public
	 */
	function SendLine( $line )
	{
		$this->Open();
		$this->command_counter++;
		$this->net->SendLine( $this->command_counter . ' ' . $line . $this->new_line );
	}

	/**
	 * @access public
	 */
	function ReadMultiLineToFile( $tag = '', $file )
	{
		// Loop it until we find the command tag we are looking for.
		// write all data to disk otherwise
		$seen_command = 0;
		$command      = '';
		$first_line   = 1;

		if ( $tag == '' )
			$tag   = $this->command_counter . ' ';
		else
			$tag  .= ' ';
      
		$first_line = $this->net->ReadLine();
		
		// FETCH (UID 3 BODY[2] {24360}
		$size_of = 0;
      
		if ( ereg( '\{([0-9]+)\}', $first_line, $regs ) )
			$size_of = $regs[ 1 ];
      
		// single byte reader
		for ( $i = 0; $i < $size_of; $i++ )
		{
			$byte = $this->net->ReadLine( 1 );
			fwrite( $file, $byte );
		}

		while ( $seen_command == 0 )
		{
			$line = $this->net->ReadLine();
			$target_tag = substr( $line, 0, strlen( $tag ) );
			
			if ( $target_tag == $tag )
			{
				$command = substr( $line, strlen( $tag ) );
				$seen_command = 1;
			}
		}

		return Array( $command );
	}

	/**
	 * @access public
	 */
	function ReadMultiLine( $tag = '' )
	{
		// Loop it until we find the command tag we are looking for.
		// Init the array of lines that we are going to work with.
      
		$lines = array();
		
		$seen_command = 0;
		$command = '';

		if ( $tag == '' )
			$tag   = $this->command_counter . ' ';
		else
			$tag  .= ' ';
		
		while ( $seen_command == 0 )
		{
			$line = $this->net->ReadLine();
			$target_tag = substr( $line, 0, strlen( $tag ) );
			
			if ( $target_tag == $tag )
			{
				$command = substr( $line, strlen( $tag ) );
				$seen_command = 1;
			}
			else
			{
				$lines[] = $line;
			}
		}

		return Array( $command, $lines );
	}

	/**
	 * @access public
	 */
	function ReadSingleLine()
	{
		$line = $this->net->ReadLine();
      
		if ( ereg( '^' . $this->command_counter . ' (.+)', $line, $regs ) )
			return $regs[ 1 ];
      
		return undef;
	}
} // END OF Imap

?>
