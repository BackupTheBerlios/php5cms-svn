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


using( 'peer.mail.imap.lib.ImapMessageOverview' );
using( 'peer.mail.MimeStructureParser' );
using( 'util.datetime.TimeObject' );


// TODO: Should we select every time?

class ImapFolder extends PEAR
{
	var $name;
	var $messages;
	var $recent;
	var $use_uid;
	var $imap_obj;

	
	/**
	 * Constructor
	 */
	function ImapFolder()
	{
		$this->name       = '';
		$this->messages   = 0;
		$this->recent     = '';

		$this->mime_struct_parser = new MimeStructureParser;

		$this->imap_obj   = undef;
		$this->use_uids   = 0;
	}

	
	function _uid()
	{
		if ( $this->use_uids == 1 )
			return 'UID ';

		return '';
	}

	function Remove()
	{
		return $this->imap_obj->RemoveFolder( $this->name );
	}

	function Expunge()
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( 'EXPUNGE' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		if ( $this->imap_obj->CommandOk( $command ) )
			return true;
      
		return false;
	}

	function AppendMessage( $folder, $message )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		if ( strlen( $message ) == 0 )
		{
			// We should do something smarter with empty messages
			return true;
		}

		// $this->imap_obj->net->debug->On();
		$this->imap_obj->SendLine( 'APPEND ' . $folder . ' {' . strlen( $message ) . '}' );

		$this->imap_obj->RawSend( $message . "\r\n" );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		// $this->imap_obj->net->debug->Off();
		if ( $this->imap_obj->CommandOk( $command ) )
			return true;
      
		return false;
	}

	function MoveMessage( $id, $folder )
	{
		if ( $this->CopyMessage( $id, $folder ) )
		{
			$this->DeleteMessage( $id );
			return true;
		}
		
		return false;
	}

	function CopyMessage( $id, $folder )
	{
		$this->imap_obj->SendLine( $this->_uid() . 'COPY ' . $id . ' ' . $folder );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		if ( $this->imap_obj->CommandOk( $command ) )
			return true;

		return false;
	}

	function DeleteMessage( $id )
	{
		if ( $this->SetMessageFlags( $id, 1, 0 ) )
			return true;

		return false;
	}

	function SetMessageFlags( $id, $deleted = 0, $seen = 0 )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		// Build the flag string to use
		$flag_string = array();
		
		if ( $deleted == 1 )
			$flag_string[] = '\Deleted';

		if ( $seen == 1 )
			$flag_string[] = '\Seen';

		$this->imap_obj->SendLine( $this->_uid() . 'STORE ' . $id . ' FLAGS (' . implode( ' ', $flag_string ) . ')' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		if ( $this->imap_obj->CommandOk( $command ) )
			return true;

		return false;
	}

	function GetMessagePart( $id, $part, $tmp_file = ''  )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( $this->_uid() . 'FETCH ' . $id . ' BODY[' . $part . ']' );

		if ( $tmp_file == '' )
		{
			list( $command, $lines ) = $this->imap_obj->ReadMultiLine();
			$part = '';

			// First line is the confirmation stuff...
			// Last line is the message end )
			for ( $i = 1; $i < ( count( $lines ) - 1 ); $i++ )
				$part .= $lines[ $i ];

			return $part;
		}

		// The using a tmp_file method is much better
		if ( $fh = fopen( $tmp_file, 'w' ) )
		{
			list( $command ) = $this->imap_obj->ReadMultiLineToFile( '', $fh );
			fclose( $fh );
			
			if ( $this->imap_obj->CommandOk( $command ) )
				return true;
         
			return false;
		}
		
		return false;
	}

	function GetMessageStructure( $id )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( $this->_uid() . 'FETCH ' . $id . ' BODYSTRUCTURE' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		$fetched_line = '';
		$found_goods  = 0;
		
		for ( $i = 0; $i < count( $lines ) ; $i++ )
		{
			// Okay reassemble the output
			// echo '<!-- L: ' . $i . ' ' . $lines[ $i ] . ' -->';
			if ( ereg( 'FETCH \(UID [0-9]+ BODYSTRUCTURE ', $lines[ $i ] ) || ereg( 'FETCH \(BODYSTRUCTURE ', $lines[ $i ] ) )
			{
				$found_goods    = 1;
				$fetched_lines .= $lines[ $i ];
			}
			
			if ( $found_goods == 1 )
			{
				$fetched_line .= $lines[ $i ];
			}
		}

		$fetched_line = str_replace( "\n", '', $fetched_line );
		$fetched_line = str_replace( "\r", '', $fetched_line );

		// echo( '<!-- Assembled line: ' . $fetched_line . ' -->' );

		// cleans up the bodystructure and gets it ready for parsin later on.
		$fetched_line = trim( substr( $fetched_line, strpos( strtolower( $fetched_line ), "bodystructure" ) + 13 ) );  
		$fetched_line = trim( substr( $fetched_line, 0, -1 ) );
		$end = $this->mime_struct_parser->match_parenthesis( 0, $fetched_line );

		while ( $end == strlen( $fetched_line ) - 1 )
		{ 
			$fetched_line = trim( substr( $fetched_line, 0, -1 ) );
			$fetched_line = trim( substr( $fetched_line, 1 ) );
			$end = $this->mime_struct_parser->match_parenthesis( 0, $fetched_line );
		}

		// echo( '<!-- Assembled line: ' . $fetched_line . ' -->' );
		return $this->mime_struct_parser->parse_structure( $fetched_line, 0 );
	}

	function GetMessageFlags( $id )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( $this->_uid() . 'FETCH ' . $id . ':' . $id . ' FLAGS' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		if ( $this->imap_obj->CommandOk( $command ) )
		{
			$flags_line = $lines[ 0 ];
			
			if ( ereg( 'FETCH \(FLAGS \((.*)\)\)', $flags_line, $regs ) )
			{
				$flags_raw = split( ' ', trim( $regs[ 1 ] ) );
				$flags = array();
				
				for ( $i = 0; $i < count( $flags_raw ); $i++ )
					$flags[] = strtolower( str_replace( '\\', '', $flags_raw[ $i ] ) ); 
           
				return $flags;
			}
		}

		return undef;
	}

	function GetMessageInternalDate( $id )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( $this->_uid() . 'FETCH ' . $id . ' INTERNALDATE' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		if ( $this->imap_obj->CommandOk( $command ) )
		{
			$date_line = $lines[ 0 ];
			
			if ( ereg( 'FETCH \(INTERNALDATE "(.*)"\)', $date_line, $regs ) )
			{
				$date = trim( $regs[ 1 ] );
				
				// Remove the -'s from the date?
				$date_array = explode( ' ', $date );
				$date_array[ 0 ] = str_replace( '-', ' ', $date_array[ 0 ] );
				
				return implode( ' ', $date_array );
			}
		}

		return undef;
	}
	
	function GetMessageSize( $id )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( $this->_uid() . 'FETCH ' . $id . ' RFC822.SIZE' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();
		
		if ( $this->imap_obj->CommandOk( $command ) )
		{
			$line = $lines[ 0 ];
			
			// * 81 FETCH (RFC822.SIZE 749)
			// * 83 FETCH (UID 84 RFC822.SIZE 7478)
			
			if ( ereg( 'RFC822.SIZE ([0-9]+)\)', $line, $regs ) )
				return $regs[ 1 ];
         
			return undef;
		}

		return undef;
	}

	function GetMessageUid( $id )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( 'FETCH ' . $id . ' UID' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();
		
		if ( $this->imap_obj->CommandOk( $command ) )
		{
			$line = $lines[ 0 ];
			
			if ( ereg( 'FETCH \(UID ([0-9]+)\)', $line, $regs ) )
				return $regs[ 1 ];
         
			return undef;
		}

		return undef;
	}

 	function GetMessageOverview( $id )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->SendLine( $this->_uid() . 'FETCH ' . $id . ' BODY.PEEK[HEADER.FIELDS (Date To From Cc Subject Message-Id X-Priority Content-Type)]' );
		list( $command, $lines ) = $this->imap_obj->ReadMultiLine();

		if ( $this->imap_obj->CommandOk( $command ) )
		{
			$temp = new ImapMessageOverview();
			
			$header_patterns = Array(
				'^date:(.*)$'        => 'date',
				'^to:(.*)$'          => 'to',
				'^from:(.*)$'        => 'from',
				'^cc:(.*)$'          => 'cc',
				'^subject:(.*)$'     => 'subject',
				'^message-id:(.*)$'  => 'messageid',
				'^x-priority:(.*)$'  => 'priority',
				'^conten-type:(.*)$' => 'content_type',
			);
			
			for ( $i = 0; $i < count( $lines ); $i++ )
			{
				$line = $lines[ $i ];
				$line = str_replace( "\n", '', $line );
				$line = str_replace( "\r", '', $line );
				reset( $header_patterns );
				$found_match = 0;
            
				while ( list( $pattern, $key ) = each( $header_patterns ) )
				{
					if ( $found_match == 1 )
						next;
						
					if ( eregi( $pattern, $line, $regs ) )
					{  
						$found_match = 1;
						$temp->{ $key } = $regs[ 1 ];
						
						next;
					}
				}
			}

			// Cleanup the data for presentation layer
			$temp->priority  = trim( $temp->priority );
			$temp->messageid = trim( $temp->messageid );
			$temp->subject   = htmlspecialchars( trim( $temp->subject ) );
         
			if ( $temp->subject == '' )
				$temp->subject = '(no subject)';

			$temp->date = trim( $temp->date );
			
			if ( $temp->date == '' )
			{
				// Get the internal date for it then
				$date = $this->GetMessageInternalDate( $id );
				
				if ( $date != undef )
				{
					$temp->date = $date;
				}
				else
				{
					// No date able to be found
				}
			}

			$size = $this->GetMessageSize( $id );
        
			if ( $size != undef )
				$temp->size = $size;

			$flags = $this->GetMessageFlags( $id );
			
			if ( is_array( $flags ) )
			{
				for ( $i = 0; $i < count( $flags ); $i++ )
				{
					$var = $flags[ $i ];
					$temp->flags->{ $var } = 1;
				}
			}

			$temp->id = $id;
			return $temp;
		}

		return undef;
	}

	
	// Folder manipulation commands
	
	function SearchFolder()
	{
	}

	/*
		Valid sort options are :
			[REVERSE] ARRIVAL
			[REVERSE] CC
			[REVERSE] DATE
			[REVERSE] FROM
			[REVERSE] SIZE
			[REVERSE] SUBJECT
			[REVERSE] TO
	*/
	function Sort( $sort_array )
	{
		return $this->SortFolder( $sort_array );
	}

	function SortFolder( $sort_array )
	{
		if ( ! $this->imap_obj->Login() )
			return undef;

		$this->imap_obj->net->debug->Message( 'Sorting' );
		
		while( list( $key, $value ) = each( $this->imap_obj->server_capabilities ) )
			$this->imap_obj->net->debug->Message(  $key . ' :: ' . $value );

		$sort_command =  array();
		$sort_field   = '';
		$sort_reverse = 0;
		
		for ( $i = 0; $i < count( $sort_array ); $i++ )
		{
			$sort_atom = $sort_array[ $i ];
			$sort_atom = strtoupper( $sort_atom );
			$this->imap_obj->net->debug->Message( 'sort atom : ' . $sort_atom );
			
			if ( ereg( 'REVERSE (ARRIVAL|CC|DATE|FROM|SIZE|SUBJECT|TO)', $sort_atom, $regs ) )
			{
				$sort_command[] = $sort_atom;
				$sort_field     = $regs[ 1 ];
				$sort_reverse   = 1;
			}
			else if ( ereg( '(ARRIVAL|CC|DATE|FROM|SIZE|SUBJECT|TO)', $sort_atom, $regs ) )
			{
				$sort_command[] = $sort_atom;
				$sort_field     = $regs[ 1 ];
				$sort_reverse   = 0;
			}
			else
			{
				$this->imap_obj->net->debug->Message( 'Invalid sort atom : ' . $sort_atom );
			}
		}
		
		if ( $this->imap_obj->server_capabilities[ 'SORT' ] == 1 )
		{
			if ( count( $sort_command ) > 0 )
			{
				if ( $this->use_uids == 1 )
					$this->imap_obj->SendLine( 'UID SORT (' . implode( ' ', $sort_command ) . ') UTF-8 ALL' );
				else
					$this->imap_obj->SendLine( 'SORT (' . implode( ' ', $sort_command ) . ') UTF-8 ALL' );
            
				list( $command, $lines ) = $this->imap_obj->ReadMultiLine();
				
				if ( $this->imap_obj->CommandOk( $command ) )
				{
					$uids = array();
					
					if ( ereg( '\* SORT (.+)', $lines[ 0 ], $regs ) )
					{
						$regs[ 1 ] = str_replace( "\n", '', $regs[ 1 ] );
						$regs[ 1 ] = str_replace( "\r", '', $regs[ 1 ] );
						
						$uids = explode( ' ', $regs[ 1 ] );
					}
					
					return $uids;
				}
				
				return undef;
			}
		}
		else
		{
			// TODO: local client sorting 
			$this->imap_obj->net->debug->Message( 'local client sorting' );
			
			// - Pull back the headers.
			// - sort based upon the atoms provided
			if ( $sort_field == 'ARRIVAL' )
				$sort_field = 'date';

			$sort_field = strtolower( $sort_field );
			$msgs = array();
			
			for ( $i = 1; $i < $this->messages; $i++ )
			{
				if ( $this->use_uids )
					$msgs[] = $this->GetMessageOverview( $this->GetMessageUid( $i ) );
				else
					$msgs[] = $this->GetMessageOverview( $i );
			}

			$sorted_array = array();
			
			for ( $i = 1; $i < count( $msgs ); $i++ )
			{
				// Pull the desired field into the sort array
				
				// current atom
				$atom = $msgs[ $i ];
				
				if ( $sort_field == 'date' )
				{
					$date = $atom->{$sort_field};
					// 05:59:PM 2001/05/03

					// This is a massage routine to allow it to sort correctly
					$date = ereg_replace( '  ', ' ', $date );
					$date_elems = explode( ' ', $date );
					
					$months = array();
					$months[ 'jan' ] = '01';
					$months[ 'feb' ] = '02';
					$months[ 'mar' ] = '03';
					$months[ 'apr' ] = '04';
					$months[ 'may' ] = '05';
					$months[ 'jun' ] = '06';
					$months[ 'jul' ] = '07';
					$months[ 'aug' ] = '08';
					$months[ 'sep' ] = '09';
					$months[ 'oct' ] = '10';
					$months[ 'nov' ] = '11';
					$months[ 'dec' ] = '12';
					
					$days = array();
					$days['mon'] = 1;
					$days['tue'] = 1;
					$days['wed'] = 1;
					$days['thr'] = 1;
					$days['thu'] = 1;
					$days['fri'] = 1;
					$days['sat'] = 1;
					$days['sun'] = 1;
					
					if ( $date_elems[ 0 ] > 0 )
					{
						// numeric leader
						if ( $months[ strtolower( $date_elems[1] ) ] != '' )
						{
							// 12 Oct 2000 12:54:50 -0700
							$date = $date_elems[2] . '/' . $months[strtolower( $date_elems[1] )] . '/' . sprintf( '%02d', $date_elems[0] ) . ' ' . $date_elems[3];
						}
					}
					
					// Tue, 10 Oct 2000 16:28:37 -0700

					if ( $days[ strtolower( substr( $date_elems[ 0 ], 0, 3  ) ) ] == 1 )
					{
						// Day leader - 
						// Sat, 16 Sep 2000 01:30:19 -0700
						// Tue, 12 Dec 2000 16:53:17 -0800 (PST)
						$date = $date_elems[3] . '/' . $months[strtolower( $date_elems[2] )] . '/' . sprintf( '%02d', $date_elems[1] ) . ' ' . $date_elems[4];
					}
					
					$time_obj = new TimeObject();
					$sorted_array[ $atom->id ] = $time_obj->Import( $date );
				}
				else
				{
					$sorted_array[ $atom->id ] = $atom->{$sort_field};
				}
			}

			$sort_type = SORT_STRING;
			
			if ( $sort_field == 'size' || $sort_field == 'date' )
				$sort_type = SORT_NUMERIC;

			if ( $sort_reverse == 1 )
				arsort( $sorted_array, $sort_type );
			else
				asort( $sorted_array, $sort_type );

			reset( $sorted_array );
			$sorted_msgs = array();
			
			while ( list( $key, $value) = each( $sorted_array ) )
				$sorted_msgs[] = $key;

			$this->imap_obj->net->debug->Message( implode( ' ', $sorted_msgs )  );
			$this->imap_obj->net->debug->Message( 'local client sorting' );
			
			return $sorted_msgs;
		}
		
		return undef;
	}
} // END OF ImapFolder

?>
