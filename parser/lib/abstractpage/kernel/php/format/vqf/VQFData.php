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

 
define( "VQFDATA_CM_STEREO",           0x00 );
define( "VQFDATA_CM_MONO",             0x01 );
define( "VQFDATA_OF_SONG_TITLE",       0x2F );
define( "VQFDATA_OF_CHANNELMODE",      0x1B );
define( "VQFDATA_OF_BITRATE",          0x1F );
define( "VQFDATA_OF_ARTIST_SONG_DIFF",   16 );


/**
 * VQF Metadata Extraction Class
 *
 * Description
 *
 * This class will extract the metadata (artist, song title, bitrate and channel mode)
 * from a VQF (twinVQ)-encoded audio file. This is useful for online audio search
 * engines etc.
 *		
 * It will not return data for song title or artist if these are not explicity set when
 * encoded.
 *
 * Example
 *
 * $vqf = new VQFData;
 * $vqf->read( "test.vqf" );
 * echo $vqf->getSongTitle()   . "\n";
 * echo $vqf->getArtist()      . "\n";
 * echo $vqf->getChannelMode() . "\n";
 * echo $vqf->getBitrate()     . "kbps\n";
 *
 * @package format_vqf
 */
 
class VQFData extends PEAR
{
	/**
	 * @access public
	 */
    var $songTitle;
	
	/**
	 * @access public
	 */
    var $artist;
	
	/**
	 * @access public
	 */
    var $channelMode;
	
	/**
	 * @access public
	 */
    var $bitrate;


	/**
	 * The parameter must be the first few hundred bytes of the VQF file. 
	 * I would recommend passing in 128 bytes although you could theoretically 
	 * pass in less depending on the length of the artist/song title.
	 *
	 * @access public
	 */
	function read( $filename )
	{
		$fp = fopen( $filename, "rb" );
		
		if ( !$fp )
		{
			return false;
		}
		else
		{
			$header = fread( $fp, 128 );
			fclose( $fp );
			$this->_decodeVqfHeader( $header );
			
			return true;
		}
	}

	/**
	 * Returns the song title.
	 *
	 * @access public
	 */
    function getSongTitle()
    {
      	return $this->songTitle;
    }

	/**
	 * Returns the artist name.
	 *
	 * @access public
	 */
    function getArtist() 
	{
      	return $this->artist;
    }

	/**
	 * Returns the channel mode (either "Stereo" or "Mono).
	 *
	 * @access public
	 */
    function getChannelMode() 
	{
      	return $this->channelMode;
    }

	/**
	 * Returns the bitrate as an integer.
	 *
	 * @access public
	 */
    function getBitrate() 
	{
      	return $this->bitrate;
    }
	
	
	// private methods
	
	/**
	 * @access private
	 */
    function _decodeVqfHeader( $header )
    {
      	if ( ord( $header[VQFDATA_OF_CHANNELMODE] ) == VQFDATA_CM_MONO )
        	$this->channelMode = "Mono";
      	else if ( ord( $header[VQFDATA_OF_CHANNELMODE] ) == VQFDATA_CM_STEREO )
        	$this->channelMode = "Stereo";

      	$this->bitrate   = ord( $header[VQFDATA_OF_BITRATE] );
      	$titleLength     = ord( $header[VQFDATA_OF_SONG_TITLE] );
      	$this->songTitle = substr( $header, VQFDATA_OF_SONG_TITLE + 1, $titleLength );
      
	  	$artistBegin  = VQFDATA_OF_SONG_TITLE + VQFDATA_OF_ARTIST_SONG_DIFF + strlen( $this->songTitle );
      	$artistLength = ord( $header[$artistBegin] );
      	$this->artist = substr( $header, $artistBegin + 1, $artistLength );
    }
} // END OF VQFData

?>
