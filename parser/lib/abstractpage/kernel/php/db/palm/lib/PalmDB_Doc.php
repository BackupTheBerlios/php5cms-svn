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
|Authors: Eduardo Pascual Martinez                                     |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'db.palm.lib.PalmDB' );


define( 'PALMDB_DOC_RECORD_SIZE', 4096 );


/**
 * Class extender for PalmOS DOC files.
 *
 * @package db_palm_lib
 */
 
class PalmDB_Doc extends PalmDB
{
   	var $bookmarks = array(); // bookmarks stored in the doc file
                              // $bookmarks[position] = "name"
   	
	var $is_compressed = false;
   	var $compressed_data = array(); // Filled when saving DOC file

   
   	/**
     * Constructor
	 */
   function PalmDB_Doc( $params = array() ) 
   {
      	$this->PalmDB( array( 
			'type'    => 'TEXt', 
			'creator' => 'REAd', 
			'name'    => isset( $params['name' ] )? $params['name' ] : '' 
		) );

      	$this->eraseDocText();
      	$this->is_compressed = isset( $params['compressed' ] )? $params['compressed' ] : true;
   	}


   	/**
	 * Gets all of the document's text and returns it as a string.
	 */
   	function getDocText()
	{
      	$String = '';
      	$i = 1;
      
	  	while ( isset( $this->records[$i] ) ) 
		{
         	$String .= pack( 'H*', $this->records[$i] );
	 		$i++;
      	}
      
	  	return $String;
   	}
   
   	/**
	 * Erases all text in the document.
	 */
   	function eraseDocText()
	{
      	$this->records = array();
      
	  	// Record 0 is reserved for header information
      	$this->goToRecord( 1 );
   	}
   
   	/**
	 * Appends $String to the end of the document.
	 */
   	function addDocText( $String ) 
	{
      	// Temporarily say the DOC is not compressed so that we get the
      	// real size of the record
      	$isCompressed = $this->is_compressed;
      	$this->is_compressed = false;
      	$SpaceLeft = PALMDB_DOC_RECORD_SIZE - $this->getRecordSize();
      
	  	while ( $String ) 
		{
         	if ( $SpaceLeft > 0 ) 
			{
	    		$this->appendString( $String, $SpaceLeft );
	    		$String = substr( $String, $SpaceLeft );
	    		$SpaceLeft = PALMDB_DOC_RECORD_SIZE - $this->getRecordSize();
	 		}
			else
			{
	    		$this->goToRecord( '+1' );
	    		$SpaceLeft = PALMDB_DOC_RECORD_SIZE;
	 		}
      	}
      
      	// Return to the correct is_compressed true/false state
      	$this->is_compressed = $isCompressed;
   	}
   
   	/**
	 * Creates the informational record (record 0).
	 * Used only for writing the file.
	 */
   	function makeDocRecordZero()
	{
      	$oldRec = $this->goToRecord( 0 );
      	$this->deleteRecord();
      
	  	if ( $this->is_compressed )
			$this->appendInt16( 2 ); // "Version"   2 = compressed
		else
			$this->appendInt16( 1 ); // "Version"   1 = uncompressed
      
	  	$this->appendInt16( 0 ); // Reserved
      
      	$Content_Length = 0;
      	$MaxIndex = 0;
      	ksort( $this->records, SORT_NUMERIC );
      	$keys = array_keys( $this->records );
      	array_shift( $keys );
      	$MaxIndex = array_pop( $keys );
      	$keys[] = $MaxIndex;
      
      	// Temporarily say the doc is uncompressed so that we get
      	// the real length of the uncompressed record
      	$isCompressed = $this->is_compressed;
      	$this->is_compressed = false;
      
      	foreach ( $keys as $index ) 
		{
         	$Content_Length += $this->getRecordSize( $index );
	 		$this->record_attrs[$index] = 0x40; // dirty + private
      	}
      
      	// Return to the correct state of is_compressed
      	$this->is_compressed = $isCompressed;
      
      	$this->appendInt32( $Content_Length );        // Doc Size
      	$this->appendInt16( $MaxIndex );              // Number of Records
      	$this->appendInt16( PALMDB_DOC_RECORD_SIZE ); // Record size
      	$this->appendInt32( 0 );                      // Reserved
        
		// possibly used for position in doc?
	 	// Don't care -- we are merely creating the doc file
      	$this->goToRecord( $oldRec );
   	}

   	/**
	 * Overrides the output function.
	 */
   	function writeToStdout()
	{
		if ( $this->is_compressed )
         	$this->compressData();
      
	  	PalmDB::writeToStdout();
   	}
   
   	/**
	 * Overrides the save function.
	 */
   	function writeToFile( $file ) 
	{
      	if ( $this->is_compressed )
         	$this->compressData();
      
	  	PalmDB::writeToFile( $file );
   	}
   
   	/**
	 * Returns the size of the record specified, or the current record if
     * no record is specified.
	 */
   	function getRecordSize( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
      
	  	if ( $num == 0 )
         	return 16;
      
	  	if ( !isset( $this->records[$num] ) )
		{
         	$bookmark = -1;
	 
	 		while ( !isset( $this->records[$num] ) && $num > 0 ) 
			{
	    		$bookmark++;
	    		$num--;
	 		}
	 
	 		if ( $bookmark < count( $this->bookmarks ) )
	    		return 20;
      	}
      
	  	// If it is compressed, getRecord() will compress the record before
      	// returning the data.  Since the data is hex encoded, divide the
      	// size of the resulting string by 2.
      	if ( $this->is_compressed )
         	return strlen( $this->compressed_data[$num] ) / 2;
      
	  	return PalmDB::getRecordSize( $num );
   	}
   
   	/**
	 * Returns the data of the specified record, or the current record if no
     * record is specified. If the record doesn't exist, returns ''.
	 */
   	function getRecord( $num = false ) 
	{
      	if ( $num === false )
         	$num = $this->current_record;
	 
      	if ( $num == 0 ) 
		{
         	$this->makeDocRecordZero();
	 		return $this->records[0];
      	}
      
      	if ( !isset( $this->records[$num] ) ) 
		{
         	$bookmark = -1;
	 
	 		while ( !isset( $this->records[$num] ) && $num > 0 ) 
			{
	    		$bookmark++;
	    		$num--;
	 		}
	 
	 		// Sort bookmarks in order of appearance
	 		ksort( $this->bookmarks );
	 
	 		if ( $bookmark < count( $this->bookmarks ) ) 
			{
  	    		$Positions = array_keys( $this->bookmarks );
	    		$Desired   = $this->bookmarks[$Positions[$bookmark]];
	    		$str  = $this->string( $Desired, 15 );
	    		$str  = $this->padString( $str, 16 );
	    		$str .= $this->int32( $Positions[$bookmark] );
	    
				return $str;
	 		}
         
		 	return '';
      	}
      
      	if ( $this->is_compressed )
         	return $this->compressed_data[$num];
	 
      	return $this->records[$num];
   	}

   	/**
	 * Compresses the entire doc file.
	 *
     * The compressed information is cached for better performance with
     * successive writes.
	 */
   	function compressData()
	{
      	$this->compressed_data = array();
      
	  	foreach ( $this->records as $index => $str )
	 		$this->compressed_data[$index] = $this->compressRecord( $str );
   	}
   
   	/**
     * Compresses a single string. Please note that the string passed in and
     * the string returned are both hex encoded!
     *
     * 0x00 = represents itself
     * 0x01 - 0x08 = Read next n bytes verbatim
     * 0x09 - 0x7F = Represents itself
     * 0x80 - 0xBF = Read next byte to make 16-bit number. Remove top 2 bits.
     *               Next 11 bits = how far back to read. Last 3 bits should
     *               be (# of bytes to copy - 3), with the last three bits
     *               never being zero.
     * 0xC0 - 0xFF = Space + 7-bit char
     *
     * If I use *1 or *2 for compress code bytes, I can illustrate a few
     * problems with this compression code. I think that every byte counts
     * on such a limited device, so maybe this compression code could be 
     * optimized a bit more. Anyone have a good plan?
     *
     *  abcdefghijgabcgabcdefghij
     * should compress to
     *  abcdefghijgabcg*1     *1 = abcdefghij
     * instead of
     *  abcdefghijgabc*1*2    *1 = gabc   *2 = defghij
     *
     * Admittedly, the loss is small, and possibly would take lots of CPU
     * time to remove a single byte, but maybe there is an efficient
     * algorithm out there that I'm not finding.
     *
     * I've tried thinking of a recusion technique and a looping technique, but
     * I can't think of anything that will provide the best compression in
     * every circumstance.  Until then, I'll just keep this semi-fast stuff
     * here.
	 */
   	function compressRecord( $In ) 
   	{
      	$Out     = '';
      	$Literal = '';
      	$pos     = 0;

      	while ( $pos < strlen( $In ) ) 
		{
         	// Search for a string
	 		$lastMatchPos  = 0;
	 		$lastMatchSize = 2;
	 		$Key = substr( $In, $pos, 2 );

         	// Start one character before what we want.
         	$StartingPos = $pos - 4094;
	 
	 		if ( $StartingPos < 0 )
	    		$StartingPos = -2;
	 
	 		// Moves a minimum of 1 character
		 	$StartingPos = $this->findNextStartingPos( $In, $pos, $lastMatchSize, $StartingPos );
	    
	 		while ( $StartingPos != $pos ) 
			{
	    		// Attempt the matching
	    		// Remember -- $pos and $potential are pointing at hex-encoded
	    		// strings!
	    		$size = ( $lastMatchSize + 1 ) * 2;
	 
	    		if ( $size > 20 )
	       			$size = 21;
	    
				while ( $size < 21 && $size + $StartingPos < $pos && $pos + $size < strlen( $In ) && $In[$StartingPos + $size] == $In[$pos + $size] )
	       			$size++;
	    
				if ( $size % 2 )
	       			$size--;
	    
				if ( $size / 2 > $lastMatchSize ) 
				{
	       			$lastMatchPos  = ( $pos - $StartingPos ) / 2;
	       			$lastMatchSize = $size / 2;
				}
	    
				// Move $StartingPos ahead
				$StartingPos = $this->findNextStartingPos( $In, $pos, $lastMatchSize, $StartingPos );
	 		}

	 		// Done searching. If we found a match that works ...
	 		if ( $lastMatchSize > 2 ) 
			{
	    		// Use a simple form of LZ77
	    		$pos += $lastMatchSize * 2;
	    		$lastMatchSize -= 3;
	    		$lastMatchSize  = $lastMatchSize &  0x07;
	    		$lastMatchPos   = $lastMatchPos  << 3;
	    		$lastMatchPos   = $lastMatchPos  &  0x3FF8;
	    		$Command = 0x8000 + $lastMatchPos + $lastMatchSize;
	    
				if ( $Literal != '' ) 
				{
	       			$Out .= $this->encodeLiteral( $Literal );
	       			$Literal = '';
	    		}
	    
				$Out .= $this->int16( $Command );
	 		} 
			else 
			{
  	    		$KeyVal = hexdec( $Key );
	    
				if ( $Literal != '' && substr( $Literal, -2 ) == '20' && $KeyVal >= 0x40 && $KeyVal <= 0x7F ) 
				{
	       			// Space encoding of a normal character
	       			$Literal = substr( $Literal, 0, strlen( $Literal ) - 2 );
	       
		   			if ( $Literal != '' ) 
					{
	          			$Out .= $this->encodeLiteral( $Literal );
		  				$Literal = '';
	       			}
	       
		   			$KeyVal += 0x80;
	       			$Out .= sprintf( '%02x', $KeyVal & 0xFF );
	       			$pos += 2;
	    		} 
				else 
				{
	       			// Literal encoding of char
	      	 		$Literal .= $Key;
	       			$pos += 2;
	    		}
	 		}
      	}
      
      	if ( $Literal != '' )
         	$Out .= $this->encodeLiteral( $Literal );
	 
      	return $Out;
   	}

	/**
	 * Finds the next possible spot for compression.
	 */
   	function findNextStartingPos( $In, $pos, $matchSize, $startingPos ) 
	{
      	// If we found a match that consumed the rest of the string, we found
      	// the best match already
      	if ( strlen( $In ) - $pos <= $matchSize * 2 )
         	return $pos;
	 
      	// Step ahead 1 char
     	$startingPos += 2;
      
      	while ( 1 ) 
		{
         	// Look for a match that has one more character
         	$startingPos = strpos( $In, substr( $In, $pos, ( $matchSize + 1 ) * 2 ), $startingPos );
	 
	 		// If no more matches, return $pos
	 		if ( $startingPos === false )
	    		return $pos;
	 
	 		// Make sure that we don't go too far
	 		if ( $startingPos + ( $matchSize * 2 ) >= $pos )
	    		return $pos;
	    
	 		// If we are on an even char (remember?  we are hex encoded)
	 		if ( $startingPos % 2 == 0 )
	    		return $startingPos;
	    
	 		// We are not on an even char -- skip ahead 1/2 of a char
	 		// and then search again
	 		$startingPos++;
      	}
   	}
   
   	/**
	 * Encodes the literal string for the compressRecord() function.
	 */
   	function encodeLiteral( $Literal ) 
	{
      	$pos = 0;
      	$Out = '';
      
	  	while ( $pos < strlen( $Literal ) ) 
		{
         	$Key = substr( $Literal, $pos, 2 );
	 		$KeyValue = hexdec( $Key );
	 
	 		if ( $KeyValue == 0 || ( $KeyValue >= 0x09 && $KeyValue <= 0x7f ) ) 
			{
	    		$Out .= $Key;
	    		$pos += 2;
	 		} 
			else 
			{
	    		$L = strlen( $Literal ) - $pos;
	    
				if ( $L > 16 )
	       			$L = 16;
	    
				$Out .= '0' . ( $L / 2 ) . substr( $Literal, $pos, $L );
	    		$pos += $L;
	 		}
      	}
      
      	return $Out;
   	}
   
   	/**
	 * Returns a list of records to write to a file in the order specified.
	 */
   	function getRecordIDs()
	{
		$ids = PalmDB::getRecordIDs( );
		
      	if ( !isset( $this->records[0] ) )
         	array_unshift( $ids, 0 );
      
	  	$Max = 0;
      
	  	foreach ( $ids as $val ) 
		{
         	if ( $Max <= $val )
	    		$Max = $val + 1;
      	}
      
	  	foreach ( $this->bookmarks as $val )
         	$ids[] = $Max++;
      
      	return $ids;
   	}
   
   	/**
	 * Returns the number of records to write.
	 */
   	function getRecordCount()
	{
      	$c = count( $this->records );
      
	  	if ( !isset( $this->records[0] ) && $c )
         	$c++;
      
	  	$c += count( $this->bookmarks );
      	return $c;
   	}
   
   	/**
	 * Adds a bookmark.
     * $Name must be 15 chars or less (automatically trimmed)
     * $Pos is the position to add the bookmark at, or the current position if
     * not specified. Returns true on error
     * If $Pos already has a bookmark defined, this will blindly overwrite that
     * bookmark.
	 */
   	function addBookmark( $Name, $Pos = false ) 
	{
      	if ( $Name == '' )
         	return true;
      
	  	if ( $Pos === false ) 
		{
         	$Pos = 0;
	 
	 		// Temporarily set the is_compressed to false so that we get an
	 		// accurate reading of the # of uncompressed bytes
	 		$isCompressed = $this->is_compressed;
	 		$this->is_compressed = false;
	 
         	foreach ( $this->records as $id => $data ) 
			{
	    		if ( $id != 0 )
	       			$Pos += $this->getRecordSize( $id );
	 		}
	 
	 		// Set the is_compressed back to what it was originally
	 		$this->is_compressed = $isCompressed;
      	}
      
	  	$this->bookmarks[$Pos] = $Name;
      	return false;
   	}
   
   	function readFile( $file ) 
	{
      	$Ret = PalmDB::readFile( $file );
		
      	if ( $Ret != false )
         	return $Ret;
			
      	if ( !isset( $this->records[0] ) )
         	return true;
      
	  	if ( $this->parseRecordZero() )
         	return true;
      
	  	if ( $this->is_compressed )
         	$this->decompressRecords();
   	}

   	function parseRecordZero()
	{
      	// Int16 = Version  [0-3]
      	// Int16 = reserved  [4-7]
      	// Int32 = uncompressed doc size  [8-15]
      	// Int16 = Number of records  [16-19]
      	// Int16 = Record size
      	// Int32 = reserved (current spot in doc?)
      
      	// Reads info from the header
      	// Also rips out bookmarks
      	$Version = substr( $this->records[0], 0, 4 );
      	$Version = hexdec( $Version );
		
      	if ( $Version == 1 )
         	$this->is_compressed = false;
      	else if ( $Version == 2 )
         	$this->is_compressed = true;
      	else
       		return true;

      	// Rip out bookmarks
      	$RecordNumber = substr( $this->records[0], 16, 4 );
      	$RecordNumber = hexdec( $RecordNumber );
		
      	foreach ( $this->records as $index => $data ) 
		{
        	if ( $index > $RecordNumber ) 
			{
	    		// 16 bytes = bookmark name
	    		// Int32 = Spot
	    		$name = substr( $data, 0, 32 );
	    		$name = pack( 'H*', $name );
	    		$spot = substr( $data, 32, 8 );
	    		$spot = hexdec( $spot );
				
	    		$this->bookmarks[$spot] = $name;
	    		unset( $this->records[$index] );
	 		}
      	}
      
      	unset( $this->records[0] );
      	return false;
   	}

   	function decompressRecords()
	{
      	foreach ( $this->records as $index => $data )
         	$this->records[$index] = $this->decompressRecord( $data );
   	}
   
   	function decompressRecord( $data ) 
	{
      	$pos = 0;
      	$Out = '';
      
	  	while ( $pos < strlen( $data ) )
		{
         	$Key = substr( $data, $pos, 2 );
	 		$KeyVal = hexdec( $Key );
	 
	 		if ( $KeyVal == 00 || ( $KeyVal >= 0x09 && $KeyVal <= 0x7F ) ) 
			{
	    		// Represents itself
	    		$pos += 2;
	    		$Out .= $Key;
	 		} 
			else if ( $KeyVal >= 0x01 && $KeyVal <= 0x08 ) 
			{
	    		// Read next N bytes verbatim
	    		$Out .= substr( $data, $pos, $KeyVal * 2 );
	    		$pos += $KeyVal * 2;
	 		} 
			else if ( $KeyVal >= 0xC0 && $KeyVal <= 0xFF ) 
			{
	    		// Space + 7-bit char
	    		$Out .= '20' . sprintf( '%02x', $KeyVal & 0x7F );
	    		$pos += 2;
	 		} 
			else 
			{
	    		// Like LZ77 compression
	    		$BigByte  = $KeyVal  &  0x3F;
	    		$BigByte  = $BigByte << 8;
	    		$BigByte += hexdec( substr( $data, $pos + 2, 2 ) );
	    		$pos     += 4;

	    		$CopyBits  = $BigByte & 0x7;
	    		$CopyBits += 3;
	    
	    		$PosBits  = $BigByte >> 3;
	    		$PosBits &= 0x7FF;
	    		$PosBits  = strlen( $Out ) - ( $PosBits * 2 );
				
	    		if ( $PosBits >= 0 )
	       			$Out .= substr( $Out, $PosBits, $CopyBits * 2 );
	 		}
      	}
      
      	return $Out;
   	}
} // END OF PalmDB_Doc

?>
