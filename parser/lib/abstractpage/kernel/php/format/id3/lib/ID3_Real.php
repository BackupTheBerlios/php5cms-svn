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
 
class ID3_Real extends ID3
{
	function getRealHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat'] = 'real';
		rewind( $fd );
		$ChunkCounter = 0;
	
		while ( ftell( $fd ) < $MP3fileInfo['filesize'] ) 
		{
			$ChunkData  = fread( $fd, 8 );
			$ChunkName  = substr( $ChunkData, 0, 4 );
			$ChunkSize  = ID3::bigEndianToInt( substr( $ChunkData, 4, 4 ) );

			$MP3fileInfo['real']["$ChunkCounter"]['name']   = $ChunkName;
			$MP3fileInfo['real']["$ChunkCounter"]['offset'] = ftell( $fd ) - 8;
			$MP3fileInfo['real']["$ChunkCounter"]['length'] = $ChunkSize;

			$ChunkData .= fread( $fd, $ChunkSize - 8 );
			$offset = 8;

			switch ( $ChunkName ) 
			{
				case '.RMF': // RealMedia File Header
					$MP3fileInfo['real']["$ChunkCounter"]['object_version'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
					$offset += 2;

					if ( $MP3fileInfo['real']["$ChunkCounter"]['object_version'] == 0 ) 
					{
						$MP3fileInfo['real']["$ChunkCounter"]['file_version']  = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['headers_count'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
					}
				
					break;

				case 'PROP': // Properties Header
					$MP3fileInfo['real']["$ChunkCounter"]['object_version'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
					$offset += 2;
				
					if ( $MP3fileInfo['real']["$ChunkCounter"]['object_version'] == 0 ) 
					{
						$MP3fileInfo['real']["$ChunkCounter"]['max_bit_rate']    = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate']    = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['max_packet_size'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['avg_packet_size'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['num_packets']     = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['duration']        = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['preroll']         = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['index_offset']    = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['data_offset']     = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['num_streams']     = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['flags_raw']       = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;

						$MP3fileInfo['bitrate']                                          =         $MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate'];
						$MP3fileInfo['playtime_seconds']                                 =         $MP3fileInfo['real']["$ChunkCounter"]['duration']  / 1000;
						$MP3fileInfo['real']["$ChunkCounter"]['flags']['save_enabled']   = (bool)( $MP3fileInfo['real']["$ChunkCounter"]['flags_raw'] & 0x0001 );
						$MP3fileInfo['real']["$ChunkCounter"]['flags']['perfect_play']   = (bool)( $MP3fileInfo['real']["$ChunkCounter"]['flags_raw'] & 0x0002 );
						$MP3fileInfo['real']["$ChunkCounter"]['flags']['live_broadcast'] = (bool)( $MP3fileInfo['real']["$ChunkCounter"]['flags_raw'] & 0x0004 );
					}
				
					break;

				case 'MDPR': // Media Properties Header
					$MP3fileInfo['real']["$ChunkCounter"]['object_version'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
					$offset += 2;
				
					if ( $MP3fileInfo['real']["$ChunkCounter"]['object_version'] == 0 ) 
					{
						$MP3fileInfo['real']["$ChunkCounter"]['stream_number']      = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['max_bit_rate']       = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate']       = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['max_packet_size']    = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['avg_packet_size']    = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['start_time']         = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['preroll']            = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['duration']           = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['stream_name_size']   = ID3::bigEndianToInt( substr( $ChunkData, $offset, 1 ) );
						$offset += 1;
						$MP3fileInfo['real']["$ChunkCounter"]['stream_name']        = substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['stream_name_size'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['stream_name_size'];
						$MP3fileInfo['real']["$ChunkCounter"]['mime_type_size']     = ID3::bigEndianToInt( substr( $ChunkData, $offset, 1 ) );
						$offset += 1;
						$MP3fileInfo['real']["$ChunkCounter"]['mime_type']          = substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['mime_type_size'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['mime_type_size'];
						$MP3fileInfo['real']["$ChunkCounter"]['type_specific_len']  = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['type_specific_data'] = substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['type_specific_len'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['type_specific_len'];

						if ( strstr( $MP3fileInfo['real']["$ChunkCounter"]['mime_type'], 'audio' ) )
							$MP3fileInfo['bitrate_audio'] = ( isset( $MP3fileInfo['bitrate_audio'] )? $MP3fileInfo['bitrate_audio'] + $MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate'] : $MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate'] );
						else if ( strstr( $MP3fileInfo['real']["$ChunkCounter"]['mime_type'], 'video' ) )
							$MP3fileInfo['bitrate_video'] = ( isset( $MP3fileInfo['bitrate_video'] )? $MP3fileInfo['bitrate_video'] + $MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate'] : $MP3fileInfo['real']["$ChunkCounter"]['avg_bit_rate'] );
					}
				
					break;

				case 'CONT': // Content Description Header
					$MP3fileInfo['real']["$ChunkCounter"]['object_version'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
					$offset += 2;
				
					if ( $MP3fileInfo['real']["$ChunkCounter"]['object_version'] == 0 ) 
					{
						$MP3fileInfo['real']["$ChunkCounter"]['title_len']     = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['title']         = (string)substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['title_len'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['title_len'];
						$MP3fileInfo['real']["$ChunkCounter"]['author_len']    = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['author']        = (string)substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['author_len'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['author_len'];
						$MP3fileInfo['real']["$ChunkCounter"]['copyright_len'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['copyright']     = (string)substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['copyright_len'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['copyright_len'];
						$MP3fileInfo['real']["$ChunkCounter"]['comment_len']   = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['comment']       = (string)substr( $ChunkData, $offset, $MP3fileInfo['real']["$ChunkCounter"]['comment_len'] );
						$offset += $MP3fileInfo['real']["$ChunkCounter"]['comment_len'];

						if ( $MP3fileInfo['real']["$ChunkCounter"]['author'] )
							$MP3fileInfo['artist'] = $MP3fileInfo['real']["$ChunkCounter"]['author'];
					
						if ( $MP3fileInfo['real']["$ChunkCounter"]['title'] )
							$MP3fileInfo['title'] = $MP3fileInfo['real']["$ChunkCounter"]['title'];
					
						if ( $MP3fileInfo['real']["$ChunkCounter"]['comment'] )
							$MP3fileInfo['comment'] = $MP3fileInfo['real']["$ChunkCounter"]['comment'];
					}
				
					break;

				case 'DATA': // Data Chunk Header
					// do nothing
					break;

				case 'INDX': // Index Section Header
					$MP3fileInfo['real']["$ChunkCounter"]['object_version'] = ID3::bigEndianToInt(substr($ChunkData, $offset, 2));
					$offset += 2;
				
					if ( $MP3fileInfo['real']["$ChunkCounter"]['object_version'] == 0 ) 
					{
						$MP3fileInfo['real']["$ChunkCounter"]['num_indices']       = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['real']["$ChunkCounter"]['stream_number']     = ID3::bigEndianToInt( substr( $ChunkData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['real']["$ChunkCounter"]['next_index_header'] = ID3::bigEndianToInt( substr( $ChunkData, $offset, 4 ) );
						$offset += 4;

						if ( $MP3fileInfo['real']["$ChunkCounter"]['next_index_header'] == 0 ) 
						{
							// last index chunk found, ignore rest of file
							return true;
						} 
						else 
						{
							// non-last index chunk, seek to next index chunk (skipping actual index data)
							fseek( $fd, $MP3fileInfo['real']["$ChunkCounter"]['next_index_header'], SEEK_SET );
						}
					}
				
					break;

				default:
					$MP3fileInfo['error'][] = 'Unhandled RealMedia chunk "' . $ChunkName . '" at offset ' . $MP3fileInfo['real']["$ChunkCounter"]['offset'] . '.';
					break;
			}
		
			$ChunkCounter++;
		}

		return true;
	}
} // END OF ID3_Real

?>
