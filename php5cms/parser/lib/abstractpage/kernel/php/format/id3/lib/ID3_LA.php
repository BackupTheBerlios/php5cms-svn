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


/**
 * @package format_id3_lib
 */

class ID3_LA extends ID3
{
	function getLAHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$offset = 0;
		rewind( $fd );
		$rawdata = fread( $fd, ID3_FREAD_BUFFER_SIZE );

		switch ( substr( $rawdata, $offset, 4 ) ) 
		{
			case 'LA02':
		
			case 'LA03':
				$MP3fileInfo['fileformat']              = 'la';
				$MP3fileInfo['la']['version_major']     = (int)substr( $rawdata, $offset + 2, 1 );
				$MP3fileInfo['la']['version_minor']     = (int)substr( $rawdata, $offset + 3, 1 );
				$MP3fileInfo['la']['version']           = (float)$MP3fileInfo['la']['version_major'] + ( $MP3fileInfo['la']['version_minor'] / 10 );
				$offset += 4;
			
				$MP3fileInfo['la']['uncompressed_size'] = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
				$offset += 4;

				$WAVEchunk = substr( $rawdata, $offset, 4 );
	
				if ( $WAVEchunk !== 'WAVE' ) 
				{
					$MP3fileInfo['error'][] = 'Expected "WAVE" (' . ID3::printHexBytes( 'WAVE' ) . ') at offset ' . $offset . ', found "' . $WAVEchunk . '" (' . ID3::printHexBytes( $WAVEchunk ) . ') instead.';
					return false;
				}
	
				$offset += 4;

				$MP3fileInfo['la']['format_size'] = 24;
			
				if ( $MP3fileInfo['la']['version'] > 0.2 ) 
				{
					$MP3fileInfo['la']['format_size'] = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
					$MP3fileInfo['la']['header_size'] = 49 + $MP3fileInfo['la']['format_size'] - 24;
					$offset += 4;
				} 
				else 
				{
					// version two didn't support additional data blocks
					$MP3fileInfo['la']['header_size'] = 41;
				}

				$fmt_chunk = substr( $rawdata, $offset, 4 );

				if ( $fmt_chunk !== 'fmt ' ) 
				{
					$MP3fileInfo['error'][] = 'Expected "fmt " (' . ID3::printHexBytes( 'fmt ' ) . ') at offset ' . $offset . ', found "' . $fmt_chunk . '" (' . ID3::printHexBytes( $fmt_chunk ) . ') instead.';
					return false;
				}

				$offset += 4;
				$fmt_size = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
				$offset += 4;
			
				$MP3fileInfo['la']['format_raw']        = ID3::littleEndianToInt( substr( $rawdata, $offset, 2 ) );
				$offset += 2;

				$MP3fileInfo['la']['channels']          = ID3::littleEndianToInt( substr( $rawdata, $offset, 2 ) );
				$offset += 2;
				$MP3fileInfo['la']['sample_rate']       = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['la']['bytes_per_second']  = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['la']['bytes_per_sample']  = ID3::littleEndianToInt( substr( $rawdata, $offset, 2 ) );
				$offset += 2;
				$MP3fileInfo['la']['bits_per_sample']   = ID3::littleEndianToInt( substr( $rawdata, $offset, 2 ) );
				$offset += 2;

				$MP3fileInfo['la']['samples']           = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
				$offset += 4;

				$MP3fileInfo['la']['seekable']          = (bool)ID3::littleEndianToInt( substr( $rawdata, $offset, 1 ) );
				$offset += 1;

				$MP3fileInfo['la']['original_crc']      = ID3::littleEndianToInt( substr( $rawdata, $offset, 4 ) );
				$offset += 4;

				using( 'format.id3.lib.ID3_RIFF' );

				$MP3fileInfo['la']['format']            = ID3_RIFF::RIFFwFormatTagLookup( $MP3fileInfo['la']['format_raw'] );
				$MP3fileInfo['la']['compression_ratio'] = (float)( $MP3fileInfo['filesize'] / $MP3fileInfo['la']['uncompressed_size'] );
				$MP3fileInfo['playtime_seconds']        = (float)( $MP3fileInfo['la']['samples'] / $MP3fileInfo['la']['sample_rate'] ) / $MP3fileInfo['la']['channels'];
				$MP3fileInfo['bitrate_audio']           = ($MP3fileInfo['filesize'] * 8) / $MP3fileInfo['playtime_seconds'];

				break;

			default:
				if ( substr( $rawdata, $offset, 2 ) == 'LA' )
					$MP3fileInfo['error'][] = 'No support for LA version ' . substr( $rawdata, $offset + 2, 1 ) . '.' . substr( $rawdata, $offset + 3, 1 ) . ' which this appears to be.';
				else
					$MP3fileInfo['error'][] = 'Not a LA (Lossless-Audio) file.';
			
				return false;
				break;
		}

		return true;
	}
} // END OF ID3_LA

?>
