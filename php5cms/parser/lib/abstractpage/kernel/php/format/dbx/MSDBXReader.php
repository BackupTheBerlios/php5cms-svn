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
 * Microsoft DBX file reader /such as Outlook Express Mailbox database files/
 * I've tested this class with 15 folders. ~ 10MB - 319 mails, the class processed them for 0.6 sec.
 *
 * @package format_dbx
 */

class MSDBXReader extends PEAR
{
	/**
	 * @access public
	 */
	var $fname = null;
	
	/**
	 * @access public
	 */
	var $mails = array();
	
	/**
	 * @access public
	 */
	var $tmp = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MSDBXReader( $fname )
	{
		$this->fname = $fname;

		// open file [fname]
		$fp = @fopen( $fname, "rb" );
		
		if ( !$fp )
		{
			$this = new PEAR_Error( "Cannot open file " . $fname );
			return;
		}
		
		// seek to read fileInfo
		fseek( $fp, 0xC4 );
		$header_info = @unpack( "Lposition/LDataLength/nHeaderLength/nFlagCount", @fread( $fp, 12 ) );
		
		// tables count in DBX
		$tables = $header_info['position'];

		// go to the first table offest and process it
		if ( $header_info['position'] > 0 )
		{
			fseek( $fp, 0x30 );
			$buf = unpack( "Lposition", fread( $fp, 4 ) );
			$position = $buf['position'];
			$this->readIndex( $fp, $position );
			$res = true;
		}

		fclose( $fp );
	}	

	
	/**
	 * @access public
	 */
	function clear()
	{
		$this->fname = '';
		unset( $this->mails );
		$this->mails = array();
		unset( $this->tmp );
		$this->tmp = array();
	}

	/**
	 * Helper function to read a null-terminated string from binary file.
	 *
	 * @access public
	 */
	function readString( &$buf, $pos )
	{
		$str = '';
		
		if ( $len = strpos( substr( $buf, $pos ), chr( 0 ) ) )
			$str = substr( $buf, $pos, $len );
			
		return $str;
	}

	/**
	 * @access public
	 */
	function readMessage( $fp, $position )
	{
		$msg = false;
		
		if ( $position > 0 ) 
		{
			fseek( $fp, 0xC4 );
			$IndexItemsCount = array_pop( unpack( "S", fread( $fp, 4 ) ) );
			
			if ( $IndexItemsCount > 0 )
			{
				fseek( $fp, $position );
				
				$msg  = '';
				$part = 0;
				
				while ( !feof( $fp ) ) 
				{
					$part++;
					$s = fread( $fp, 528 );
					
					if ( strlen( $s ) == 0 )
						break;
					
					$msg_item = unpack( "LFilePos/LUnknown/LItemSize/LNextItem/a511Content", $s );
					
					if ( $msg_item['FilePos'] <> $position )
						return PEAR::raiseError( "Read part of message verify error " . $part );
						
					$msg .= substr( $msg_item['Content'], 0, $msg_item['ItemSize'] );
					$position = $msg_item['NextItem'];
					
					if ( $position == 0 )
						break;
					
					fseek( $fp, $position );
				}
			}
		}
		
		return $msg;
	}

	/**
	 * @access public
	 */
	function readMessageInfo( $fp, $position )
	{
		$message_info = array();
		fseek( $fp, $position );
		$msg_header = unpack( "Lposition/LDataLength/SHeaderLength/SFlagCount", fread( $fp, 12 ) );
		
		if ( $msg_header['position'] != $position )
			return PEAR::raiseError( "Message info verify error." );

		$message_info['HeaderPosition'] = $position;
		
		$flags        = ( $msg_header['FlagCount'] & 0xFF );
		$DataSize     = $msg_header['DataLength'] - ( $flags * 4 );
		$size	      = 4 * $flags;
		$FlagsBuffer  = fread( $fp, $size );
		$size	      = $DataSize;
		$DataBuffer   = fread( $fp, $size );
		$message_info = array();
		
		// process flags
		for ( $i = 0; $i < $flags; $i++ ) 
		{
			$pos = 0;
			$f   = array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) );
			
			// print "FLAG:" . sprintf( "0x%x",( $f & 0xFF ) ) . "<br>";
			
			switch ( $f & 0xFF ) 
			{
				case 0x1:
					$pos = $pos + ( $f >> 8 );	
					$message_info['MsgFlags']  = array_pop( unpack( "C", substr( $DataBuffer, $pos, 1 ) ) );
					$pos++;
					$message_info['MsgFlags'] += array_pop( unpack( "C", substr( $DataBuffer, $pos, 1 ) ) ) * 256;
					$pos++;
					$message_info['MsgFlags'] += array_pop( unpack( "C", substr( $DataBuffer, $pos, 1 ) ) ) * 65536;
					break;

				case 0x2:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Sent'] = array_pop( unpack( "L", substr( $DataBuffer, $pos, 4 ) ) );
					break;

				case 0x4:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['position'] = array_pop( unpack( "L", substr( $DataBuffer, $pos, 4 ) ) );
					break;

				case 0x7:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['MessageID'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0x8:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Subject'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0x9:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['From_reply'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0xA:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['References'] = $this->readString( $DataBuffer, $pos );
					break;
				
				case 0xB:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Newsgroup'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0xD:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['From'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0xE:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Reply_To'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0x12:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Received'] = array_pop( unpack( "L", substr( $DataBuffer, $pos, 4 ) ) );
					break;

				case 0x13:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Receipt'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0x1A:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['Account'] = $this->readString( $DataBuffer, $pos );
					break;

				case 0x1B:
					$pos += array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					$message_info['AccountID'] = intval( $this->readString( $DataBuffer, $pos ) );
					break;

				case 0x80:
					$message_info['Msg'] = array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					break;

				case 0x81:
					$message_info['MsgFlags'] = array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					break;

				case 0x84:
					$message_info['position'] = array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					break;

				case 0x91:
					$message_info['size'] = array_pop( unpack( "L", substr( $FlagsBuffer, $i * 4, 4 ) ) ) >> 8;				
					break;
			}
		}	
		
		return $message_info;
	}

	/**
	 * @access public
	 */
	function readIndex( $fp, $position )
	{
		fseek( $fp, $position );
		$index_header = unpack( "LFilePos/LUnknown1/LPrevIndex/LNextIndex/LCount/LUnknown", fread( $fp, 24 ) );
		
		if ( $index_header['FilePos'] != $position )
			return PEAR::raiseError( "Verify error." );
		
		$this->tmp[$position] = true; // push it into list of processed items
		
		if ( ( $index_header['NextIndex'] > 0 ) && ( $this->tmp[$index_header['NextIndex']] != true ) )
			$this->readIndex( $fp, $index_header['NextIndex'] );
			
		if ( ( $index_header['PrevIndex'] > 0 ) && ( $this->tmp[$index_header['PrevIndex']] != true ) )
			$this->readIndex( $fp, $index_header['PrevIndex'] );
			
		$icount = $index_header['Count'] >> 8;
		
		if ( $icount > 0 )
		{
			fseek( $fp, $index_header['FilePos'] + 24 );
			$buf = fread( $fp, 12 * $icount );
			
			for ( $i = 0; $i < $icount; $i++ ) 
			{
				$hdr_buf   = substr( $buf, $i * 12, 12 );
				$IndexItem = unpack( "LHeaderPos/LChildIndex/LUnknown", $hdr_buf );
				
				if ( $IndexItem['HeaderPos'] > 0 )
				{
					if ( strtolower( $this->fname ) == 'folders.dbx' )
					{ 
						// read_folder( $fp, $IndexItem['HeaderPos'] );
						print 'Read folder not implemented in v1.0a<br>';
					}
					else
					{
						$mail['info']    = $this->readMessageInfo( $fp, $IndexItem['HeaderPos'] );
						$mail['content'] = $this->readMessage( $fp, $mail['info']['position'] );
						$this->mails[] = $mail;
					}
				}
				
				if ( ( $IndexItem['ChildIndex'] > 0 ) && ( $this->tmp[$IndexItem['ChildIndex']] != true ) )
					$this->readIndex( $fp, $IndexItem['ChildIndex'] );
			}
		}
		
		return true;
	}

	/**
	 * Debug function to display human readble message flags (Just for debugging purpose).
	 *
	 * @access public
	 */
	function decodeFlags( $x )
	{
		$decode_flag['DOWNLOADED']           = 0x1;
		$decode_flag['MARKED']               = 0x20;
		$decode_flag['READED']               = 0x80;
		$decode_flag['DOWNLOAD_LATER']       = 0x100;
		$decode_flag['NEWS_MSG']             = 0x800;  // to verify
		$decode_flag['ATTACHMENTS']          = 0x4000;
		$decode_flag['REPLY']                = 0x80000;
		$decode_flag['INSPECT_CONVERSATION'] = 0x400000;
		$decode_flag['IGNORE_CONVERSATION']  = 0x800000;

		$decoded_flags = '';

		if ( ( $x & $decode_flag['NEWS_MSG'] ) != 0 )
			$decoded_flags .= "NEWS MESSAGE\n<br>";
		
		if ( ( $x & $decode_flag['DOWNLOAD_LATER'] ) != 0 )
			$decoded_flags .= "DOWNLOAD LATER\n<br>";
		
		if ( ( $x & $decode_flag['DOWNLOADED'] ) != 0 )
			$decoded_flags .= "DOWNLOADED\n<br>";
		
		if ( ( $x & $decode_flag['READED'] ) != 0 )
			$decoded_flags .= "READED\n<br>";
		
		if ( ( $x & $decode_flag['MARKED'] ) != 0 )
			$decoded_flags .= "MARKED\n<br>";
		
		if ( ( $x & $decode_flag['ATTACHMENTS'] ) != 0 )
			$decoded_flags .= "ATTACHMENTS\n<br>";
		
		if ( ( $x & $decode_flag['REPLY'] ) != 0 )
			$decoded_flags .= "REPLY\n<br>";
		
		if ( ( $x & $decode_flag['INSPECT_CONVERSATION'] ) != 0 )
			$decoded_flags .= "INSPECT CONVERSATION\n<br>";
		
		if ( ( $x & $decode_flag['IGNORE_CONVERSATION'] ) != 0 )
			$decoded_flags .= "IGNORE CONVERSATION\n<br>";

		return $decoded_flags;
	}
} // END OF MSDBXReader

?>
