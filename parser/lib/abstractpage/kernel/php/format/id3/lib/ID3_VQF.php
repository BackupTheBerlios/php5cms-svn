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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


using( 'format.id3.lib.ID3' );
using( 'format.id3.lib.ID3_VQF' );


/**
 * @package format_id3_lib
 */
 
class ID3_VQF extends ID3
{
	function getVQFHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat']      = 'vqf';
		$MP3fileInfo['bitrate_mode']    = 'cbr';
		$MP3fileInfo['audiodataoffset'] = 0; // should be overridden below

		rewind( $fd );
		$VQFheaderData = fread( $fd, 16 );

		$offset = 0;
		$MP3fileInfo['vqf']['raw']['header_tag'] = substr( $VQFheaderData, $offset, 4 );
		$offset += 4;
		$MP3fileInfo['vqf']['raw']['version']    = substr( $VQFheaderData, $offset, 8 );
		$offset += 8;
		$MP3fileInfo['vqf']['raw']['size']       = ID3::bigEndianToInt( substr( $VQFheaderData, $offset, 4 ) );
		$offset += 4;

		while ( ftell( $fd ) < $MP3fileInfo['filesize'] ) 
		{
			$ChunkBaseOffset = ftell( $fd );
			$chunkoffset     = 0;
			$ChunkData       = fread( $fd, 8 );
			$ChunkName       = substr( $ChunkData, $chunkoffset, 4 );
		
			if ( $ChunkName == 'DATA' ) 
			{
				$MP3fileInfo['audiodataoffset'] = $ChunkBaseOffset;
				break;
			}
		
			$chunkoffset += 4;
			$ChunkSize    = ID3::bigEndianToInt( substr( $ChunkData, $chunkoffset, 4 ) );
			$chunkoffset += 4;
			
			if ( $ChunkSize > ( $MP3fileInfo['filesize'] - ftell( $fd ) ) ) 
			{
				$MP3fileInfo['error'][] = 'Invalid chunk size ' . $ChunkSize . ' for chunk "' . $ChunkName . '" at offset ' . $ChunkBaseOffset . '.';
				break;
			}
		
			$ChunkData .= fread( $fd, $ChunkSize );

			switch ( $ChunkName ) 
			{
				case 'COMM':
					$MP3fileInfo['vqf']["$ChunkName"]['channel_mode']   = ID3::bigEndianToInt( substr( $ChunkData, $chunkoffset, 4 ) );
					$chunkoffset += 4;
					$MP3fileInfo['vqf']["$ChunkName"]['bitrate']        = ID3::bigEndianToInt( substr( $ChunkData, $chunkoffset, 4 ) );
					$chunkoffset += 4;
					$MP3fileInfo['vqf']["$ChunkName"]['sample_rate']    = ID3::bigEndianToInt( substr( $ChunkData, $chunkoffset, 4 ) );
					$chunkoffset += 4;
					$MP3fileInfo['vqf']["$ChunkName"]['security_level'] = ID3::bigEndianToInt( substr( $ChunkData, $chunkoffset, 4 ) );
					$chunkoffset += 4;

					$MP3fileInfo['channels']      = $MP3fileInfo['vqf']["$ChunkName"]['channel_mode'] + 1;
					$MP3fileInfo['frequency']     = ID3_VQF::VQFchannelFrequencyLookup( $MP3fileInfo['vqf']["$ChunkName"]['sample_rate'] );
					$MP3fileInfo['bitrate_audio'] = $MP3fileInfo['vqf']["$ChunkName"]['bitrate'] * 1000;
				
					break;

				case 'NAME':
			
				case 'AUTH':
			
				case '(c) ':
			
				case 'FILE':
			
				case 'COMT':
					$MP3fileInfo['vqf']["$ChunkName"] = substr( $ChunkData, 8 );
					break;

				default:
					$MP3fileInfo['error'][] = 'Unhandled chunk type "' . $ChunkName . '" at offset ' . $ChunkBaseOffset . '.';
					break;
			}
		}

		$MP3fileInfo['playtime_seconds'] = ( ( $MP3fileInfo['filesize'] - $MP3fileInfo['audiodataoffset'] ) * 8 ) / $MP3fileInfo['bitrate_audio'];

		$handytranslationkeys = array(
			'NAME' => 'title', 
			'AUTH' => 'artist', 
			'COMT' => 'comment'
		);
	
		foreach ( $handytranslationkeys as $vqfkey => $standardkey ) 
		{
			if ( isset( $MP3fileInfo['vqf']["$vqfkey"] ) )
				$MP3fileInfo['vqf']["$standardkey"] = $MP3fileInfo['vqf']["$vqfkey"];
		}

		return true;
	}

	function VQFchannelFrequencyLookup( $frequencyid ) 
	{
		static $VQFchannelFrequencyLookup = array();
	
		if ( count( $VQFchannelFrequencyLookup ) < 1 ) 
		{
			$VQFchannelFrequencyLookup[11] = 11025;
			$VQFchannelFrequencyLookup[22] = 22050;
			$VQFchannelFrequencyLookup[44] = 44100;
		}
	
		return ( isset( $VQFchannelFrequencyLookup["$frequencyid"] )? $VQFchannelFrequencyLookup["$frequencyid"] : $frequencyid * 1000 );
	}
} // END OF ID3_VQF

?>
