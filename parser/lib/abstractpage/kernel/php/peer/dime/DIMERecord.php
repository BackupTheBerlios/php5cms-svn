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

 
define( 'DIME_TYPE_UNCHANGED', 0x00   );
define( 'DIME_TYPE_MEDIA',     0x01   );
define( 'DIME_TYPE_URI',       0x02   );
define( 'DIME_TYPE_UNKNOWN',   0x03   );
define( 'DIME_TYPE_NONE',      0x04   );
define( 'DIME_VERSION',        0x0001 );
define( 'DIME_RECORD_HEADER',  12     );
define( 'DIME_FLAGS',          0      );
define( 'DIME_OPTS_LEN',       1      );
define( 'DIME_ID_LEN',         2      );
define( 'DIME_TYPE_LEN',       3      );
define( 'DIME_DATA_LEN',       4      );
define( 'DIME_OPTS',           5      );
define( 'DIME_ID',             6      );
define( 'DIME_TYPE',           7      );
define( 'DIME_DATA',           8      );


/**
 * DIME Encoding/Decoding
 *
 * What is it?
 *   This class enables you to manipulate and build
 *   a DIME encapsulated message.
 *
 * http://www.ietf.org/internet-drafts/draft-nielsen-dime-02.txt
 *
 * 09/18/02 - A huge number of changes to be compliant 
 * 			  with the DIME Specification Release 17 June 2002
 * 
 * TODO: lots of stuff needs to be tested.
 *           Definitily have to go through DIME spec and
 *           make things work right, most importantly, sec 3.3
 *           make examples, document
 *
 * see test/dime_mesage_test.php for example of usage
 *
 * @package peer_dime
 */
 
class DIMERecord extends PEAR
{
	/**
	 * @access public
	 */
    var $OPTS_LENGTH = 0;
	
	/**
	 * @access public
	 */
    var $ID_LENGTH = 0;
	
	/**
	 * @access public
	 */
    var $TYPE_LENGTH = 0; 
	
	/**
	 * @access public
	 */
    var $DATA_LENGTH = 0;
	
	/**
	 * @access public
	 */
    var $padstr = "\0";

    /**
     * Elements
     * [DIME_FLAGS],    16 bits: VERSION:MB:ME:CF:TYPE_T
     * [DIME_OPTS_LEN], 16 bits: OPTIONS_LENGTH
     * [DIME_ID_LEN],   16 bits: ID_LENGTH
     * [DIME_TYPE_LEN], 16 bits: TYPE_LENGTH
     * [DIME_DATA_LEN], 32 bits: DATA_LENGTH
	 * [DIME_OPTS]             : OPTIONS
	 * [DIME_ID]     		   : ID
	 * [DIME_TYPE]             : TYPE
	 * [DIME_DATA]             : DATA
	 * @access public
     */
    var $Elements = array(
		DIME_FLAGS    => 0,  
		DIME_OPTS_LEN => 0, 
		DIME_ID_LEN   => 0, 
		DIME_TYPE_LEN => 0, 
		DIME_DATA_LEN => 0,
		DIME_OPTS     => '',
		DIME_ID       => '',
		DIME_TYPE     => '',
		DIME_DATA     => ''
	);

	/**
	 * @access private
	 */
    var $_haveOpts = false;
	
	/**
	 * @access private
	 */
    var $_haveID = false;
	
	/**
	 * @access private
	 */
    var $_haveType = false;
	
	/**
	 * @access private
	 */
    var $_haveData = false;
	

	/**
	 * @access public
	 */	
    function setMB()
    {
        $this->Elements[DIME_FLAGS] |= 0x0400;
    }

	/**
	 * @access public
	 */	
    function setME()
    {
        $this->Elements[DIME_FLAGS] |= 0x0200;
    }

	/**
	 * @access public
	 */	
    function setCF()
    {
        $this->Elements[DIME_FLAGS] |= 0x0100;
    }

	/**
	 * @access public
	 */	
    function isChunk()
    {
        return $this->Elements[DIME_FLAGS] & 0x0100;
    }

	/**
	 * @access public
	 */	
    function isEnd()
    {
        return $this->Elements[DIME_FLAGS] & 0x0200;
    }

	/**
	 * @access public
	 */	    
    function isStart()
    {
        return $this->Elements[DIME_FLAGS] & 0x0400;
    }

	/**
	 * @access public
	 */	    
    function getID()
    {
        return $this->Elements[DIME_ID];
    }

	/**
	 * @access public
	 */	
    function getType()
    {
        return $this->Elements[DIME_TYPE];
    }

	/**
	 * @access public
	 */	
    function getData()
    {
        return $this->Elements[DIME_DATA];
    }

	/**
	 * @access public
	 */	    
    function getDataLength()
    {
        return $this->Elements[DIME_DATA_LEN];
    }

	/**
	 * @access public
	 */	    
    function setType( $typestring, $type = DIME_TYPE_UNKNOWN )
    {
        $typelen = strlen( $typestring ) & 0xFFFF;
        $type    = $type << 4;
		
        $this->Elements[DIME_FLAGS]    = ( $this->Elements[DIME_FLAGS] & 0xFF0F ) | $type;
        $this->Elements[DIME_TYPE_LEN] = $typelen;
        $this->TYPE_LENGTH             = $this->_getPadLength( $typelen );
        $this->Elements[DIME_TYPE]     = $typestring;
    }

	/**
	 * @access public
	 */	    
    function generateID()
    {
        $id = md5( time() );
        $this->setID( $id );

        return $id;
    }

	/**
	 * @access public
	 */	    
    function setID( $id )
    {
        $idlen = strlen( $id ) & 0xFFFF;
		
        $this->Elements[DIME_ID_LEN] = $idlen;
        $this->ID_LENGTH             = $this->_getPadLength( $idlen );
        $this->Elements[DIME_ID]     = $id;
    }

	/**
	 * @access public
	 */	    
    function setData( $data, $size = 0 )
    {
        $datalen = $size? $size : strlen( $data );
		
        $this->Elements[DIME_DATA_LEN] = $datalen;
        $this->DATA_LENGTH             = $this->_getPadLength( $datalen );
        $this->Elements[DIME_DATA]     = $data;
    }
    
	/**
	 * @access public
	 */	
    function encode()
    {
		// insert version 
	    $this->Elements[DIME_FLAGS] = ( $this->Elements[DIME_FLAGS] & 0x07FF ) | ( DIME_VERSION << 11 );

        // the real dime encoding
        $format = 
			'%c%c%c%c%c%c%c%c%c%c%c%c' .
			'%' . $this->OPTS_LENGTH . 's' .
			'%' . $this->ID_LENGTH   . 's' .
			'%' . $this->TYPE_LENGTH . 's' .
			'%' . $this->DATA_LENGTH . 's';

        return sprintf(
			$format,
			( $this->Elements[DIME_FLAGS]    & 0x0000FF00 ) >> 8,
			( $this->Elements[DIME_FLAGS]    & 0x000000FF ),
			( $this->Elements[DIME_OPTS_LEN] & 0x0000FF00 ) >> 8,
			( $this->Elements[DIME_OPTS_LEN] & 0x000000FF ),
			( $this->Elements[DIME_ID_LEN]   & 0x0000FF00 ) >> 8,
			( $this->Elements[DIME_ID_LEN]   & 0x000000FF ),
			( $this->Elements[DIME_TYPE_LEN] & 0x0000FF00 ) >> 8,
			( $this->Elements[DIME_TYPE_LEN] & 0x000000FF ),
			( $this->Elements[DIME_DATA_LEN] & 0xFF000000 ) >> 24,
			( $this->Elements[DIME_DATA_LEN] & 0x00FF0000 ) >> 16,
			( $this->Elements[DIME_DATA_LEN] & 0x0000FF00 ) >> 8,
			( $this->Elements[DIME_DATA_LEN] & 0x000000FF ),
			str_pad( $this->Elements[DIME_OPTS], $this->OPTS_LENGTH, $this->padstr ),
			str_pad( $this->Elements[DIME_ID],   $this->ID_LENGTH,   $this->padstr ),
			str_pad( $this->Elements[DIME_TYPE], $this->TYPE_LENGTH, $this->padstr ),
			str_pad( $this->Elements[DIME_DATA], $this->DATA_LENGTH, $this->padstr )
		);
	}
   
	/**
	 * @access public
	 */	
    function decode( &$data )
    {
        // REAL DIME decoding
        $this->Elements[DIME_FLAGS]    = ( hexdec( bin2hex( $data[0] ) ) <<  8 ) +   hexdec( bin2hex( $data[1] ) );
        $this->Elements[DIME_OPTS_LEN] = ( hexdec( bin2hex( $data[2] ) ) <<  8 ) +   hexdec( bin2hex( $data[3] ) );
        $this->Elements[DIME_ID_LEN]   = ( hexdec( bin2hex( $data[4] ) ) <<  8 ) +   hexdec( bin2hex( $data[5] ) );
        $this->Elements[DIME_TYPE_LEN] = ( hexdec( bin2hex( $data[6] ) ) <<  8 ) +   hexdec( bin2hex( $data[7] ) );
        $this->Elements[DIME_DATA_LEN] = ( hexdec( bin2hex( $data[8] ) ) << 24 ) + ( hexdec( bin2hex( $data[9] ) ) << 16 ) + ( hexdec( bin2hex( $data[10] ) ) << 8 ) + hexdec( bin2hex( $data[11] ) );

        $p = 12;		
		$version = ( ( $this->Elements[DIME_FLAGS] >> 11 ) & 0x001F );
		
		if ( $version == DIME_VERSION ) 
		{
	        $this->OPTS_LENGTH = $this->_getPadLength( $this->Elements[DIME_OPTS_LEN] );        
	        $this->ID_LENGTH   = $this->_getPadLength( $this->Elements[DIME_ID_LEN]   );
	        $this->TYPE_LENGTH = $this->_getPadLength( $this->Elements[DIME_TYPE_LEN] );
	        $this->DATA_LENGTH = $this->_getPadLength( $this->Elements[DIME_DATA_LEN] );
	                
	        $datalen = strlen( $data );
	        $this->Elements[DIME_OPTS] = substr( $data, $p, $this->Elements[DIME_OPTS_LEN] );
	        $this->_haveOpts = ( strlen( $this->Elements[DIME_OPTS] ) == $this->Elements[DIME_OPTS_LEN] );
			
	        if ( $this->_haveOpts ) 
			{
	            $p += $this->OPTS_LENGTH;		
		        $this->Elements[DIME_ID] = substr( $data, $p, $this->Elements[DIME_ID_LEN] );
		        $this->_haveID = ( strlen( $this->Elements[DIME_ID] ) == $this->Elements[DIME_ID_LEN] );
		        
				if ( $this->_haveID ) 
				{
		            $p += $this->ID_LENGTH;
		            $this->Elements[DIME_TYPE] = substr( $data, $p, $this->Elements[DIME_TYPE_LEN] );
		            $this->_haveType = ( strlen( $this->Elements[DIME_TYPE] ) == $this->Elements[DIME_TYPE_LEN] );

		            if ( $this->_haveType ) 
					{
		                $p += $this->TYPE_LENGTH;
		                $this->Elements[DIME_DATA] = substr( $data, $p, $this->Elements[DIME_DATA_LEN] );
		                $this->_haveData = ( strlen( $this->Elements[DIME_DATA] ) == $this->Elements[DIME_DATA_LEN] );
		                
						if ( $this->_haveData )
		                    $p += $this->DATA_LENGTH;
		                else
		                    $p += strlen( $this->Elements[DIME_DATA] );
		            } 
					else 
					{
		                $p += strlen( $this->Elements[DIME_TYPE] );
					}
		        } 
				else 
				{
		            $p += strlen( $this->Elements[DIME_ID] );
				}
		    } 
			else 
			{
		    	$p += strlen( $this->Elements[DIME_OPTS] );					
	        }
		}
        
		return substr( $data, $p );
    }
    
	/**
	 * @access public
	 */	
    function addData( &$data )
    {
        $datalen = strlen($data);
        $p = 0;
        
		if ( !$this->_haveOpts ) 
		{
            $have = strlen( $this->Elements[DIME_OPTS] );
            $this->Elements[DIME_OPTS] .= substr( $data, $p, $this->Elements[DIME_OPTS_LEN] - $have );
            $this->_haveOpts = ( strlen( $this->Elements[DIME_OPTS] ) == $this->Elements[DIME_OTPS_LEN] );
            
			if ( !$this->_haveOpts ) 
				return null;
            
			$p += $this->OPTS_LENGTH - $have;
        }
		
        if ( !$this->_haveID ) 
		{
            $have = strlen( $this->Elements[DIME_ID] );
            $this->Elements[DIME_ID] .= substr( $data, $p, $this->Elements[DIME_ID_LEN] - $have );
            $this->_haveID = ( strlen( $this->Elements[DIME_ID] ) == $this->Elements[DIME_ID_LEN] );
            
			if ( !$this->_haveID ) 
				return null;
            
			$p += $this->ID_LENGTH - $have;
        }
		
        if ( !$this->_haveType && $p < $datalen ) 
		{
            $have = strlen( $this->Elements[DIME_TYPE] );
            $this->Elements[DIME_TYPE] .= substr( $data, $p, $this->Elements[DIME_TYPE_LEN] - $have );
            $this->_haveType = ( strlen( $this->Elements[DIME_TYPE] ) == $this->Elements[DIME_TYPE_LEN] );
            
			if ( !$this->_haveType ) 
				return null;
            
			$p += $this->TYPE_LENGTH - $have;
        }
		
        if ( !$this->_haveData && $p < $datalen ) 
		{
            $have = strlen( $this->Elements[DIME_DATA] );
            $this->Elements[DIME_DATA] .= substr( $data, $p, $this->Elements[DIME_DATA_LEN] - $have );
            $this->_haveData = ( strlen( $this->Elements[DIME_DATA] ) == $this->Elements[DIME_DATA_LEN] );
            
			if ( !$this->_haveData ) 
				return null;
            
			$p += $this->DATA_LENGTH - $have;
        }
		
        return substr( $data, $p );
    }
	
	
	// private methods
	
	/**
	 * @access private
	 */	
	function _getPadLength( $len )
    {
        $pad = 0;
		
        if ( $len ) 
		{
            $pad = $len % 4;
            
			if ( $pad ) 
				$pad = 4 - $pad;
        }
		
        return $len + $pad;
    }
} // END OF DIMERecord

?>
