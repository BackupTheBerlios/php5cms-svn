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


using( 'peer.dime.DIMERecord' );


/**
 * @package peer_dime
 */
 
class DIMEMessage extends PEAR
{
	/**
	 * @access public
	 */
	var $type;
	
	/**
	 * @access public
	 */
    var $typestr;

	/**
	 * @access public
	 */	
    var $record_size = 4096;
	
	/**
	 * @access public
	 */
    var $parts = array();
	
	/**
	 * @access public
	 */
    var $currentPart = -1;
	
	/**
	 * @access public
	 */
    var $stream = null;
    
	/**
	 * @access public
	 */
    var $mb = 1;
	
	/**
	 * @access public
	 */
    var $me = 0;
	
	/**
	 * @access public
	 */
    var $cf = 0;
	
	/**
	 * @access public
	 */
    var $id = null;

	/**
	 * @access private
	 */	
	var $_currentRecord;
	
	/**
	 * @access private
	 */	
	var $_proc = array();
	
	
    /**
     * Constructor
     *
     * This currently takes a file pointer as provided
     * by fopen.
     *
     * @access public
     */
    function DIMEMessage( $stream = null, $record_size = 4096 )
    {
        $this->stream = $stream;
        $this->record_size = $record_size;
    }
    
    
	/**
	 * @access public
	 */
    function startChunk( &$data, $typestr = '', $id = null, $type = DIME_TYPE_UNKNOWN )
    {
        $this->me = 0;
        $this->cf = 1;
		
        $this->type    = $type;
        $this->typestr = $typestr;
        
		if ( $id )
            $this->id = $id;
        else
            $this->id = md5( time() );
        
        return $this->_makeRecord( $data, $this->typestr, $this->id, $this->type );
    }

	/**
	 * @access public
	 */
    function doChunk( &$data )
    {
        $this->me = 0;
        $this->cf = 1;
        
		return $this->_makeRecord( $data, null, null, DIME_TYPE_UNCHANGED );
    }

	/**
	 * @access public
	 */
    function endChunk()
    {
        $this->cf = 0;

        $data = null;
        $rec  = $this->_makeRecord( $data, null, null, DIME_TYPE_UNCHANGED );

        $this->id = 0;
        $this->cf = 0;
        $this->id = 0;

        $this->type = DIME_TYPE_UNKNOWN;
        $this->typestr = null;

        return $rec;
    }
    
	/**
	 * @access public
	 */
    function endMessage()
    {
        $this->me = 1;

        $data = null;
        $rec  = $this->_makeRecord( $data, null, null, DIME_TYPE_NONE );

        $this->me = 0;
        $this->mb = 1;
        $this->id = 0;
        
		return $rec;
    }
    
    /**
     * Given a chunk of data, it creates DIME records and writes them to the stream.
	 *
	 * @access public
     */
    function sendData( &$data, $typestr = '', $id = null, $type = DIME_TYPE_UNKNOWN )
    {
        $len = strlen( $data );
		
        if ( $len > $this->record_size ) 
		{
            $chunk = substr( $data, 0, $this->record_size );
            $p     = $this->record_size;
            $rec   = $this->startChunk( $chunk, $typestr, $id, $type );
			
            fwrite( $this->stream, $rec );
			
            while ( $p < $len ) 
			{
                $chunk  = substr( $data, $p, $this->record_size );
                $p     += $this->record_size;
                $rec    = $this->doChunk( $chunk );
				
                fwrite( $this->stream, $rec );
            }
			
            $rec = $this->endChunk();
            fwrite( $this->stream, $rec );
			
            return;
        }
		
        $rec = $this->_makeRecord( $data, $typestr, $id, $type );
        fwrite( $this->stream, $rec );
    }

	/**
	 * @access public
	 */    
    function sendEndMessage()
    {
        $rec = $this->endMessage();
        fwrite( $this->stream, $rec );
    }
    
    /**
     * Given a filename, it reads the file, creates records and writes them to the stream.
	 *
	 * @access public
     */
    function sendFile( $filename, $typestr = '', $id = null, $type = DIME_TYPE_UNKNOWN )
    {
        $f = fopen( $filename, "rb" );

        if ( $f ) 
		{
            if ( $data = fread( $f, $this->record_size ) )
                $this->startChunk( $data, $typestr, $id, $type );
            
            while ( $data = fread( $f, $this->record_size ) )
                $this->doChunk( $data, $typestr, $id, $type );
            
            $this->endChunk();
            fclose( $f );
        }
    }

    /**
     * Given data, encode it in DIME.
	 *
	 * @access public
     */
    function encodeData( $data, $typestr = '', $id = null, $type = DIME_TYPE_UNKNOWN )
    {
        $len  = strlen( $data );
        $resp = '';
		
        if ( $len > $this->record_size ) 
		{
            $chunk  = substr( $data, 0, $this->record_size );
            $p      = $this->record_size;
            $resp  .= $this->startChunk( $chunk, $typestr, $id, $type );
			
            while ( $p < $len ) 
			{
                $chunk  = substr( $data, $p, $this->record_size );
                $p     += $this->record_size;
                $resp  .= $this->doChunk( $chunk );
            }
			
            $resp .= $this->endChunk();
        } 
		else 
		{
            $resp .= $this->_makeRecord( $data, $typestr, $id, $type );
        }
		
        return $resp;
    }

    /**
     * Given a filename, it reads the file, creates records and writes them to the stream.
	 *
	 * @access public
     */
    function encodeFile( $filename, $typestr = '', $id = null, $type = DIME_TYPE_UNKNOWN )
    {
        $f = fopen( $filename, "rb" );
		
        if ( $f ) 
		{
            if ( $data = fread( $f, $this->record_size ) )
                $resp = $this->startChunk( $data, $typestr, $id, $type );
            
            while ( $data = fread( $f, $this->record_size ) )
                $resp = $this->doChunk( $data, $typestr, $id, $type );
            
            $resp = $this->endChunk();
            fclose( $f );
        }
		
        return $resp;
    }
        
    /**
     * Decodes a DIME encrypted string of data.
	 *
	 * @access public
     */
    function decodeData( &$data ) 
	{
        while ( strlen( $data ) >= DIME_RECORD_HEADER ) 
		{
            $err = $this->_processData( $data );
			
            if ( PEAR::isError( $err ) )
                return $err;
        }
    }
    
    /**
     * Reads the stream and creates an array of records.
     *
     * It can accept the start of a previously read buffer
     * this is usefull in situations where you need to read
     * headers before discovering that the data is DIME encoded
     * such as in the case of reading an HTTP response.
	 *
	 * @access public
     */
    function read( $buf = null )
    {
        while ( $data = fread( $this->stream, 8192 ) ) 
		{
            if ( $buf ) 
			{
                $data = $buf . $data;
                $buf  = null;
            }

            $err = $this->decodeData( $data );
			
            if ( PEAR::isError( $err ) )
                return $err;
            
            // store any leftover data to be used again
            // should be < DIME_RECORD_HEADER bytes
            $buf = $data;
        }
		
        if ( !$this->_currentRecord || !$this->_currentRecord->isEnd() )
            return PEAR::raiseError( "Reached stream end without end record." );
        
        return null;
    }
	
	
	// private methods
	
	/**
     * Creates Net_DIME_Records from provided data.
	 *
	 * @access private
     */
    function _processData( &$data )
    {
        $leftover = null;
        
		if ( !$this->_currentRecord ) 
		{
            $this->_currentRecord = new DIMERecord;
            $data = $this->_currentRecord->decode( $data );
        } 
		else 
		{
            $data = $this->_currentRecord->addData( $data );
        }
				
        if ( $this->_currentRecord->_haveData ) 
		{
            if ( ( count( $this->parts ) == 0 ) && !$this->_currentRecord->isStart() )
                return PEAR::raiseError( "First Message is not a DIME begin record." );

            if ( $this->_currentRecord->isEnd() && $this->_currentRecord->getDataLength() == 0 )
                return null;
            
            if ( $this->currentPart < 0 && !$this->_currentRecord->isChunk() ) 
			{
                $this->parts[] = array();
                $this->currentPart = count( $this->parts ) - 1;
				
                $this->parts[$this->currentPart]['id']   = $this->_currentRecord->getID();
                $this->parts[$this->currentPart]['type'] = $this->_currentRecord->getType();
                $this->parts[$this->currentPart]['data'] = $this->_currentRecord->getData();
                
				$this->currentPart = -1;
            } 
			else 
			{
                if ( $this->currentPart < 0 ) 
				{
                    $this->parts[] = array();
                    $this->currentPart = count( $this->parts ) - 1;
					
                    $this->parts[$this->currentPart]['id']   = $this->_currentRecord->getID();
                    $this->parts[$this->currentPart]['type'] = $this->_currentRecord->getType();
                    $this->parts[$this->currentPart]['data'] = $this->_currentRecord->getData();
                } 
				else 
				{
                    $this->parts[$this->currentPart]['data'] .= $this->_currentRecord->getData();
                    
					if ( !$this->_currentRecord->isChunk() ) 
					{
                        // we reached the end of the chunk
                        $this->currentPart = -1;
                    }
                }
            }
			
            if ( !$this->_currentRecord->isEnd() ) 
				$this->_currentRecord = null;
        }
		
        return null;
    }
	
	/**
	 * @access private
	 */
	function _makeRecord( &$data, $typestr = '', $id = null, $type = DIME_TYPE_UNKNOWN )
    {
        $record = new DIMERecord;
        
		if ( $this->mb ) 
		{
            $record->setMB();
            
			// all subsequent records are not message begin!
            $this->mb = 0; 
        }
		
        if ( $this->me )
			$record->setME();
        
		if ( $this->cf ) 
			$record->setCF();
        
		$record->setData( $data );
        $record->setType( $typestr, $type );
		
        if ( $id ) 
			$record->setID( $id );

        return $record->encode();
    }
} // END OF DIMEMessage

?>
