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
 * @link http://flac.sourceforge.net/format.html
 * @package format_id3_lib
 */

class ID3_Flac extends ID3
{
	function getFLACHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat']   = 'flac';
		$MP3fileInfo['bitrate_mode'] = 'vbr';

		rewind( $fd );
		$StreamMarker = fread( $fd, 4 );
		
		if ( $StreamMarker != 'fLaC' ) 
		{
			$MP3fileInfo['error'][] = 'Invalid stream_marker - expected "fLaC", found "' . $StreamMarker . '".';
			return false;
		}

		do 
		{
			$METAdataBlockOffset            = ftell( $fd );
			$METAdataBlockHeader            = fread( $fd, 4 );
			$METAdataLastBlockFlag          = (bool)(ID3::bigEndianToInt( substr( $METAdataBlockHeader, 0, 1 ) ) & 0x80 );
			$METAdataBlockType              = ID3::bigEndianToInt( substr( $METAdataBlockHeader, 0, 1 ) ) & 0x7F;
			$METAdataBlockLength            = ID3::bigEndianToInt( substr( $METAdataBlockHeader, 1, 3 ) );
			$METAdataBlockTypeText          = ID3_Flac::FLACmetaBlockTypeLookup( $METAdataBlockType );
			$METAdataBlockData              = fread( $fd, $METAdataBlockLength );
			$MP3fileInfo['audiodataoffset'] = ftell( $fd );
			
			$offset = 0;

			$MP3fileInfo['flac']["$METAdataBlockTypeText"]['raw']['offset']          = $METAdataBlockOffset;
			$MP3fileInfo['flac']["$METAdataBlockTypeText"]['raw']['last_meta_block'] = $METAdataLastBlockFlag;
			$MP3fileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_type']      = $METAdataBlockType;
			$MP3fileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_type_text'] = $METAdataBlockTypeText;
			$MP3fileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_length']    = $METAdataBlockLength;
			$MP3fileInfo['flac']["$METAdataBlockTypeText"]['raw']['block_data']      = $METAdataBlockData;

			switch ( $METAdataBlockType ) 
			{
				case 0: // STREAMINFO
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['min_block_size']  = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['max_block_size']  = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['min_frame_size']  = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 3 ) );
					$offset += 3;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['max_frame_size']  = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 3 ) );
					$offset += 3;
					$SampleRateChannelsSampleBitsStreamSamples                        = ID3::bigEndianToBin( substr( $METAdataBlockData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['sample_rate']     = ID3::binToDec( substr( $SampleRateChannelsSampleBitsStreamSamples,  0, 20 ) );
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['channels']        = ID3::binToDec( substr( $SampleRateChannelsSampleBitsStreamSamples, 20,  3 ) ) + 1;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['bits_per_sample'] = ID3::binToDec( substr( $SampleRateChannelsSampleBitsStreamSamples, 23,  5 ) ) + 1;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['samples_stream']  = ID3::binToDec( substr( $SampleRateChannelsSampleBitsStreamSamples, 28, 36 ) );
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]['audio_signature'] = substr( $METAdataBlockData, $offset, 16 );
					$offset += 16;

					$MP3fileInfo['frequency']        = $MP3fileInfo['flac']["$METAdataBlockTypeText"]['sample_rate'];
					$MP3fileInfo['channels']         = $MP3fileInfo['flac']["$METAdataBlockTypeText"]['channels'];
					$MP3fileInfo['playtime_seconds'] = $MP3fileInfo['flac']["$METAdataBlockTypeText"]['samples_stream'] / $MP3fileInfo['flac']["$METAdataBlockTypeText"]['sample_rate'];
					$MP3fileInfo['bitrate_audio']    = ( $MP3fileInfo['filesize'] * 8 ) / $MP3fileInfo['playtime_seconds'];
					
					break;

				case 1: // PADDING
					// ignore
					break;

				case 2: // APPLICATION
					$ApplicationID = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]["$ApplicationID"]['name'] = ID3_Flac::FLACapplicationIDLookup( $ApplicationID );
					$MP3fileInfo['flac']["$METAdataBlockTypeText"]["$ApplicationID"]['data'] = substr( $METAdataBlockData, $offset );
					$offset = strlen( $METAdataBlockData );

					break;

				case 3: // SEEKTABLE
					while ( $offset < strlen( $METAdataBlockData ) ) 
					{
						if ( substr( $METAdataBlockData, $offset, 8 ) == str_repeat( chr( 0xFF ), 8 ) ) 
						{
							// placeholder point
							$MP3fileInfo['flac']["$METAdataBlockTypeText"]['placeholders'] = ( isset( $MP3fileInfo['flac']["$METAdataBlockTypeText"]['placeholders']) ? $MP3fileInfo['flac']["$METAdataBlockTypeText"]['placeholders'] + 1 : 1 );
							$offset += 18;
						} 
						else 
						{
							$SampleNumber                                                              = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 8 ) );
							$offset += 8;
							$MP3fileInfo['flac']["$METAdataBlockTypeText"]["$SampleNumber"]['offset']  = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 8 ) );
							$offset += 8;
							$MP3fileInfo['flac']["$METAdataBlockTypeText"]["$SampleNumber"]['samples'] = ID3::bigEndianToInt( substr( $METAdataBlockData, $offset, 2 ) );
							$offset += 2;
						}
					}
				
					break;

				case 4: // VORBIS_COMMENT
					using( 'format.id3.lib.ID3_OGG' );
					ID3_OGG::parseVorbisComments( $METAdataBlockData, $MP3fileInfo, $METAdataBlockOffset );
				
					break;

				default:
					$MP3fileInfo['error'][] = 'Unhandled METADATA_BLOCK_HEADER.BLOCK_TYPE (' . $METAdataBlockType . ') at offset ' . $METAdataBlockOffset . '.';
					break;
			}
		} while ( $METAdataLastBlockFlag === false );

		if ( isset($MP3fileInfo['flac']['STREAMINFO'] ) ) 
		{
			$MP3fileInfo['flac']['compressed_audio_bytes']   = $MP3fileInfo['filesize'] - $MP3fileInfo['audiodataoffset'];
			$MP3fileInfo['flac']['uncompressed_audio_bytes'] = $MP3fileInfo['flac']['STREAMINFO']['samples_stream'] * $MP3fileInfo['flac']['STREAMINFO']['channels'] * ( $MP3fileInfo['flac']['STREAMINFO']['bits_per_sample'] / 8 );
			$MP3fileInfo['flac']['compression_ratio']        = $MP3fileInfo['flac']['compressed_audio_bytes'] / $MP3fileInfo['flac']['uncompressed_audio_bytes'];
		}

		return true;
	}

	function FLACmetaBlockTypeLookup( $blocktype ) 
	{
		static $FLACmetaBlockTypeLookup = array();
	
		if ( count( $FLACmetaBlockTypeLookup ) < 1 ) 
		{
			$FLACmetaBlockTypeLookup[0] = 'STREAMINFO';
			$FLACmetaBlockTypeLookup[1] = 'PADDING';
			$FLACmetaBlockTypeLookup[2] = 'APPLICATION';
			$FLACmetaBlockTypeLookup[3] = 'SEEKTABLE';
			$FLACmetaBlockTypeLookup[4] = 'VORBIS_COMMENT';
		}
	
		return ( isset( $FLACmetaBlockTypeLookup["$blocktype"] )? $FLACmetaBlockTypeLookup["$blocktype"] : 'reserved' );
	}

	function FLACapplicationIDLookup( $applicationid ) 
	{
		static $FLACapplicationIDLookup = array();
	
		if ( count( $FLACapplicationIDLookup ) < 1 ) 
		{
			// http://flac.sourceforge.net/id.html
			$FLACapplicationIDLookup[0x46746F6C] = 'flac-tools';      // 'Ftol'
			$FLACapplicationIDLookup[0x46746F6C] = 'Sound Font FLAC'; // 'SFFL'
		}
	
		return ( isset( $FLACapplicationIDLookup["$applicationid"] )? $FLACapplicationIDLookup["$applicationid"] : 'reserved' );
	}
} // END OF ID3_Flac

?>
