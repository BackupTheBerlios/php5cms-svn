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
 
class ID3_MPEG extends ID3
{
	function getMPEGHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat'] = 'mpg';
	
		if ( !$fd ) 
		{
			$MP3fileInfo['error'][] = 'Could not open file.';
			return false;
		} 
		else 
		{
			// Start code                       32 bits
			// horizontal frame size            12 bits
			// vertical frame size              12 bits
			// pixel aspect ratio                4 bits
			// frame rate                        4 bits
			// bitrate                          18 bits
			// marker bit                        1 bit
			// VBV buffer size                  10 bits
			// constrained parameter flag        1 bit
			// intra quant. matrix flag          1 bit
			// intra quant. matrix values      512 bits (present if matrix flag == 1)
			// non-intra quant. matrix flag      1 bit
			// non-intra quant. matrix values  512 bits (present if matrix flag == 1)

			rewind( $fd );
			$MPEGvideoHeader = fread( $fd, ID3_FREAD_BUFFER_SIZE );
			$offset = 0;
	
			// MPEG video information is found as $00 $00 $01 $B3
			$matching_pattern = chr( 0x00 ) . chr( 0x00 ) . chr( 0x01 ) . chr( 0xB3 );
			
			while ( substr( $MPEGvideoHeader, $offset++, 4 ) !== $matching_pattern ) 
			{
				if ( $offset >= ( strlen( $MPEGvideoHeader ) - 12 ) ) 
				{
					$MPEGvideoHeader .= fread( $fd, ID3_FREAD_BUFFER_SIZE );
					$MPEGvideoHeader  = substr( $MPEGvideoHeader, $offset );
				
					$offset = 0;
				
					if ( strlen( $MPEGvideoHeader ) < 12 ) 
					{
						$MP3fileInfo['error'][] = 'Could not find start of video block before end of file.';
						return false;
					} 
					else if ( ftell( $fd ) >= 100000 ) 
					{
						$MP3fileInfo['error'][] = 'Could not find start of video block in the first 100,000 bytes (this might not be an MPEG-video file?).';
						unset( $MP3fileInfo['fileformat'] );
					
						return false;
					}
				}
			}
		
			$offset += strlen( $matching_pattern ) - 1;

			$FrameSizeAspectRatioFrameRateDWORD = ID3::bigEndianToInt( substr( $MPEGvideoHeader, $offset, 4 ) );
			$offset += 4;

			$assortedinformation = ID3::bigEndianToInt( substr( $MPEGvideoHeader, $offset, 4 ) );
			$offset += 4;

			$MP3fileInfo['mpeg']['video']['raw']['framesize_horizontal'] = ( $FrameSizeAspectRatioFrameRateDWORD & 0xFFF00000 ) >> 20; // 12 bits for horizontal frame size
			$MP3fileInfo['mpeg']['video']['raw']['framesize_vertical']   = ( $FrameSizeAspectRatioFrameRateDWORD & 0x000FFF00 ) >> 8;  // 12 bits for vertical frame size
			$MP3fileInfo['mpeg']['video']['raw']['pixel_aspect_ratio']   = ( $FrameSizeAspectRatioFrameRateDWORD & 0x000000F0 ) >> 4;
			$MP3fileInfo['mpeg']['video']['raw']['frame_rate']           = ( $FrameSizeAspectRatioFrameRateDWORD & 0x0000000F );

			$MP3fileInfo['mpeg']['video']['framesize_horizontal'] = $MP3fileInfo['mpeg']['video']['raw']['framesize_horizontal'];
			$MP3fileInfo['mpeg']['video']['framesize_vertical']   = $MP3fileInfo['mpeg']['video']['raw']['framesize_vertical'];
			$MP3fileInfo['resolution_x'] = $MP3fileInfo['mpeg']['video']['framesize_horizontal'];
			$MP3fileInfo['resolution_y'] = $MP3fileInfo['mpeg']['video']['framesize_vertical'];

			$MP3fileInfo['mpeg']['video']['pixel_aspect_ratio']        = ID3_MPEG::MPEGvideoAspectRatioLookup( $MP3fileInfo['mpeg']['video']['raw']['pixel_aspect_ratio'] );
			$MP3fileInfo['mpeg']['video']['pixel_aspect_ratio_text']   = ID3_MPEG::MPEGvideoAspectRatioTextLookup( $MP3fileInfo['mpeg']['video']['raw']['pixel_aspect_ratio'] );
			$MP3fileInfo['mpeg']['video']['frame_rate']                = ID3_MPEG::MPEGvideoFramerateLookup( $MP3fileInfo['mpeg']['video']['raw']['frame_rate'] );

			$MP3fileInfo['mpeg']['video']['raw']['bitrate'] = ( $assortedinformation & 0xFFFFC000 ) >> 14;
		
			// 18 set bits
			if ( $MP3fileInfo['mpeg']['video']['raw']['bitrate'] == 0x3FFFF ) 
			{
				$MP3fileInfo['mpeg']['video']['bitrate_type'] = 'variable';
				$MP3fileInfo['bitrate_mode']                  = 'vbr';
			} 
			else 
			{
				$MP3fileInfo['mpeg']['video']['bitrate_type'] = 'constant';
				$MP3fileInfo['bitrate_mode']                  = 'cbr';
				$MP3fileInfo['mpeg']['video']['bitrate_bps']  = $MP3fileInfo['mpeg']['video']['raw']['bitrate'] * 400;
				$MP3fileInfo['bitrate_video']                 = $MP3fileInfo['mpeg']['video']['bitrate_bps'];
			}
		
			$MP3fileInfo['mpeg']['video']['raw']['marker_bit']             = ( $assortedinformation & 0x00002000 ) >> 14;
			$MP3fileInfo['mpeg']['video']['raw']['vbv_buffer_size']        = ( $assortedinformation & 0x00001FF8 ) >> 13;
			$MP3fileInfo['mpeg']['video']['raw']['constrained_param_flag'] = ( $assortedinformation & 0x00000004 ) >> 2;
			$MP3fileInfo['mpeg']['video']['raw']['intra_quant_flag']       = ( $assortedinformation & 0x00000002 ) >> 1;

			return true;
		}
	}

	function MPEGvideoFramerateLookup( $rawframerate ) 
	{
		$MPEGvideoFramerateLookup = array( 0, 23.976, 24, 25, 29.97, 30, 50, 59.94, 60 );
		return ( isset( $MPEGvideoFramerateLookup["$rawframerate"] )? (float)$MPEGvideoFramerateLookup["$rawframerate"] : (float)0 );
	}

	function MPEGvideoAspectRatioLookup( $rawaspectratio ) 
	{
		$MPEGvideoAspectRatioLookup = array( 0, 1, 0.6735, 0.7031, 0.7615, 0.8055, 0.8437, 0.8935, 0.9157, 0.9815, 1.0255, 1.0695, 1.0950, 1.1575, 1.2015, 0 );
		return ( isset( $MPEGvideoAspectRatioLookup["$rawaspectratio"] )? (float)$MPEGvideoAspectRatioLookup["$rawaspectratio"] : (float)0 );
	}

	function MPEGvideoAspectRatioTextLookup( $rawaspectratio ) 
	{
		$MPEGvideoAspectRatioTextLookup = array( 'forbidden', 'square pixels', '0.6735', '16:9, 625 line, PAL', '0.7615', '0.8055', '16:9, 525 line, NTSC', '0.8935', '4:3, 625 line, PAL, CCIR601', '0.9815', '1.0255', '1.0695', '4:3, 525 line, NTSC, CCIR601', '1.1575', '1.2015', 'reserved' );
		return ( isset( $MPEGvideoAspectRatioTextLookup["$rawaspectratio"] )? $MPEGvideoAspectRatioTextLookup["$rawaspectratio"] : '' );
	}
} // END OF ID3_MPEG

?>
