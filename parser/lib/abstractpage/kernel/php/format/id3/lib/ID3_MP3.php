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


// number of frames to scan to determine if MPEG-audio sequence is valid
define( 'ID3_MP3_VALID_CHECK_FRAMES', 10 );


/**
 * @package format_id3_lib
 */
 
class ID3_MP3 extends ID3
{
	function getMP3headerFilepointer( &$fd, &$MP3fileInfo, $TryAsAAC ) 
	{
		// get all information about an MP3 file - ID3v1, ID3v2, Lyrics3, MPEG-audio
		$MP3fileInfo['fileformat'] = '';
	
		if ( !$fd ) 
		{
			$MP3fileInfo['error'][] = 'Could not open file.';
			return false;
		} 
		else 
		{
			fseek( $fd, -128 - 9 - 6, SEEK_END );
			
			$lyrics3_id3v1 = fread( $fd, 128 + 9 + 6 );
			$lyrics3lsz    = substr( $lyrics3_id3v1,  0,   6 );
			$lyrics3end    = substr( $lyrics3_id3v1,  6,   9 ); // LYRICSEND or LYRICS200
			$id3v1tag      = substr( $lyrics3_id3v1, 15, 128 );
	
			if ( $lyrics3end == 'LYRICSEND' ) 
			{
				// Lyrics3 v1 and ID3v1
				$lyrics3size = 5100;
				ID3_MP3::getLyrics3Filepointer( $MP3fileInfo, $fd, -128 - $lyrics3size, 1, $lyrics3size );
			} 
			else if ( $lyrics3end == 'LYRICS200' ) 
			{
				// Lyrics3 v2 and ID3v1
				$lyrics3size = $lyrics3lsz + 6 + strlen( 'LYRICS200' ); // LSZ = lyrics + 'LYRICSBEGIN'; add 6-byte size field; add 'LYRICS200'
				ID3_MP3::getLyrics3Filepointer( $MP3fileInfo, $fd, -128 - $lyrics3size, 2, $lyrics3size );
			} 
			else if ( substr( $lyrics3_id3v1, strlen( $lyrics3_id3v1 ) - 1 - 9, 9 ) == 'LYRICSEND' ) 
			{
				// Lyrics3 v1, no ID3v1 (I think according to Lyrics3 specs there MUST be ID3v1, but just in case :)
				$lyrics3size = 5100;
				ID3_MP3::getLyrics3Filepointer( $MP3fileInfo, $fd, 0 - $lyrics3size, 1, $lyrics3size );
			} 
			else if ( substr( $lyrics3_id3v1, strlen( $lyrics3_id3v1 ) - 1 - 9, 9 ) == 'LYRICS200' ) 
			{
				// Lyrics3 v2, no ID3v1 (I think according to Lyrics3 specs there MUST be ID3v1, but just in case :)
				$lyrics3size = $lyrics3lsz + 6 + strlen( 'LYRICS200' ); // LSZ = lyrics + 'LYRICSBEGIN'; add 6-byte size field; add 'LYRICS200'
				ID3_MP3::getLyrics3Filepointer( $MP3fileInfo, $fd, 0 - $lyrics3size, 2, $lyrics3size );
			}

			if ( substr( $id3v1tag, 0, 3 ) == 'TAG' ) 
			{
				$MP3fileInfo['id3']['id3v1'] = ID3::getID3v1Filepointer( $fd );
				$MP3fileInfo['fileformat']   = 'id3';
			}

			ID3::getID3v2Filepointer( $fd, $MP3fileInfo );
		
			if ( isset( $MP3fileInfo['id3']['id3v2']['header'] ) ) 
			{
				$MP3fileInfo['fileformat'] = 'id3';
				$audiodataoffset = $MP3fileInfo['id3']['id3v2']['headerlength'];
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['footer'] ) )
					$audiodataoffset += 10;
			}
			// no ID3v2 header 
			else 
			{
				if ( isset( $MP3fileInfo['id3']['id3v2'] ) )
					unset($MP3fileInfo['id3']['id3v2']);
			
				$audiodataoffset = 0;
			}
		
			if ( $audiodataoffset < $MP3fileInfo['filesize'] ) 
			{
				ID3_MP3::getOnlyMPEGaudioInfo( $fd, $MP3fileInfo, $audiodataoffset, false );

				// some convoluted code to try and distinguish MP3 files (above) from AAC-ADTS (below)
				// since they both start with a 11/12-bit synch pattern
				if ( !isset( $MP3fileInfo['audiodataoffset'] ) && $TryAsAAC ) 
				{
					fseek( $fd, $audiodataoffset, SEEK_SET );
					$Header4Bytes = fread( $fd, 4 );
					
					if ( !ID3_MP3::MPEGaudioHeaderValid( ID3_MP3::MPEGaudioHeaderDecode( substr( $Header4Bytes, 0, 4 ) ) ) ) 
					{
						$dummy = $MP3fileInfo;
						$dummy['error'] = '';
						
						unset( $dummy['mpeg'] );
						unset( $dummy['fileformat'] );
					
						using( 'format.id3.lib.ID3_AAC' );
					
						if ( ID3_AAC::getAACADTSheaderFilepointer( $fd, $dummy ) )
							$MP3fileInfo = $dummy;
					}
				}
			}
		
			if ( isset( $MP3fileInfo['audiodataoffset'] ) && ( ( isset( $MP3fileInfo['id3']['id3v2'] ) && ( $MP3fileInfo['audiodataoffset'] > $MP3fileInfo['id3']['id3v2']['headerlength'] ) ) || ( !isset( $MP3fileInfo['id3']['id3v2'] ) && ( $MP3fileInfo['audiodataoffset'] > 0 ) ) ) ) 
			{
				$errStr = 'Unknown data before synch ';
			
				if ( isset($MP3fileInfo['id3']['id3v2']['headerlength'] ) )
					$errStr .= '(ID3v2 header ends at ' . $MP3fileInfo['id3']['id3v2']['headerlength'] . ', ';
				else
					$errStr .= '(should be at beginning of file, ';
			
				$errStr .= 'synch detected at ' . $MP3fileInfo['audiodataoffset'] . ').';
				$MP3fileInfo['error'][] = $errStr;
			}
		
			if ( isset( $MP3fileInfo['mpeg']['audio']['layer'] ) && ( $MP3fileInfo['mpeg']['audio']['layer'] == 'II' ) ) 
				$MP3fileInfo['fileformat'] = 'mp2';
		
			if ( !isset( $MP3fileInfo['fileformat'] ) || !$MP3fileInfo['fileformat'] ) 
			{
				$MP3fileInfo['error'][] = 'Synch not found.';
				
				unset( $MP3fileInfo['audiodataoffset'] );
				unset( $MP3fileInfo['fileformat'] );
			}
		} 
	
		if ( isset( $MP3fileInfo['id3'] ) && !isset( $MP3fileInfo['id3']['id3v2'] ) && !isset( $MP3fileInfo['id3']['id3v1'] ) )
			unset( $MP3fileInfo['id3'] );
	
		if ( isset( $MP3fileInfo['mpeg']['audio']['bitratemode'] ) )
			$MP3fileInfo['bitrate_mode'] = strtolower( $MP3fileInfo['mpeg']['audio']['bitratemode'] );
	
		return true;
	}

	function decodeMPEGaudioHeader( $fd, $offset, &$MP3fileInfo, $recursivesearch = true, $ScanAsCBR = false ) 
	{
		if ( $offset >= $MP3fileInfo['filesize'] ) 
		{
			$MP3fileInfo['error'][] = 'End of file encounter looking for MPEG synch.';
			return false;
		}
	
		fseek( $fd, $offset, SEEK_SET );
		$headerstring = fread( $fd, 192 );

		// MP3 audio frame structure:
		// $aa $aa $aa $aa [$bb $bb] $cc...
		// where $aa..$aa is the four-byte mpeg-audio header (below)
		// $bb $bb is the optional 2-byte CRC
		// and $cc... is the audio data

		$MPEGheaderRawArray = ID3_MP3::MPEGaudioHeaderDecode( substr( $headerstring, 0, 4 ) );

		if ( ID3_MP3::MPEGaudioHeaderValid( $MPEGheaderRawArray ) ) 
		{
			$MP3fileInfo['mpeg']['audio']['raw'] = $MPEGheaderRawArray;
		} 
		else 
		{
			$MP3fileInfo['error'][] = 'Invalid MPEG audio header at offset ' . $offset . '.';
			return false;
		}

		$MP3fileInfo['mpeg']['audio']['version']       = ID3_MP3::MPEGaudioVersionLookup( $MP3fileInfo['mpeg']['audio']['raw']['version'] );
		$MP3fileInfo['mpeg']['audio']['layer']         = ID3_MP3::MPEGaudioLayerLookup( $MP3fileInfo['mpeg']['audio']['raw']['layer'] );
		$MP3fileInfo['mpeg']['audio']['protection']    = ID3_MP3::MPEGaudioCRCLookup( $MP3fileInfo['mpeg']['audio']['raw']['protection'] );
		$MP3fileInfo['mpeg']['audio']['bitrate']       = ID3_MP3::MPEGaudioBitrateLookup( $MP3fileInfo['mpeg']['audio']['version'], $MP3fileInfo['mpeg']['audio']['layer'], $MP3fileInfo['mpeg']['audio']['raw']['bitrate'] );
		$MP3fileInfo['mpeg']['audio']['frequency']     = ID3_MP3::MPEGaudioFrequencyLookup( $MP3fileInfo['mpeg']['audio']['version'], $MP3fileInfo['mpeg']['audio']['raw']['frequency'] );
		$MP3fileInfo['mpeg']['audio']['padding']       = (bool)$MP3fileInfo['mpeg']['audio']['raw']['padding'];
		$MP3fileInfo['mpeg']['audio']['private']       = (bool)$MP3fileInfo['mpeg']['audio']['raw']['private'];
		$MP3fileInfo['mpeg']['audio']['channelmode']   = ID3_MP3::MPEGaudioChannelModeLookup( $MP3fileInfo['mpeg']['audio']['raw']['channelmode'] );
		$MP3fileInfo['mpeg']['audio']['channels']      = ( ( $MP3fileInfo['mpeg']['audio']['channelmode'] == 'mono' ) ? 1 : 2 );
		$MP3fileInfo['mpeg']['audio']['modeextension'] = ID3_MP3::MPEGaudioModeExtensionLookup( $MP3fileInfo['mpeg']['audio']['layer'], $MP3fileInfo['mpeg']['audio']['raw']['modeextension'] );
		$MP3fileInfo['mpeg']['audio']['copyright']     = (bool)$MP3fileInfo['mpeg']['audio']['raw']['copyright'];
		$MP3fileInfo['mpeg']['audio']['original']      = (bool)$MP3fileInfo['mpeg']['audio']['raw']['original'];
		$MP3fileInfo['mpeg']['audio']['emphasis']      = ID3_MP3::MPEGaudioEmphasisLookup( $MP3fileInfo['mpeg']['audio']['raw']['emphasis'] );

		$MP3fileInfo['frequency'] = $MP3fileInfo['mpeg']['audio']['frequency'];
		$MP3fileInfo['channels']  = $MP3fileInfo['mpeg']['audio']['channels'];

		if ( $MP3fileInfo['mpeg']['audio']['protection'] )
			$MP3fileInfo['mpeg']['audio']['crc'] = ID3::bigEndianToInt( substr( $headerstring, 4, 2 ) );

		// For Layer II there are some combinations of bitrate and mode which are not allowed.
		if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'II' ) 
		{
			$MP3fileInfo['fileformat'] = 'mp2';
		
			switch ( $MP3fileInfo['mpeg']['audio']['channelmode'] ) 
			{
				case 'mono':
					if ( ( $MP3fileInfo['mpeg']['audio']['bitrate'] == 'free' ) || ( $MP3fileInfo['mpeg']['audio']['bitrate'] <= 192 ) ) 
					{
						// these are ok
					} 
					else 
					{
						$MP3fileInfo['error'][] = $MP3fileInfo['mpeg']['audio']['bitrate'] . 'kbps not allowed in Layer II, ' . $MP3fileInfo['mpeg']['audio']['channelmode'] . '.';
					}
				
					break;
		
				case 'stereo':
			
				case 'joint stereo':
			
				case 'dual channel':
					if ( ( $MP3fileInfo['mpeg']['audio']['bitrate'] == 'free' ) || ( $MP3fileInfo['mpeg']['audio']['bitrate'] == 64 ) || ( $MP3fileInfo['mpeg']['audio']['bitrate'] >= 96 ) ) 
					{
						// these are ok
					} 
					else 
					{
						$MP3fileInfo['error'][] = $MP3fileInfo['mpeg']['audio']['bitrate'] . 'kbps not allowed in Layer II, ' . $MP3fileInfo['mpeg']['audio']['channelmode'] . '.';
					}
				
					break;
			}
		}

		if ( $MP3fileInfo['mpeg']['audio']['bitrate'] != 'free' ) 
		{
			if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
			{
				if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'I' ) 
				{
					$FrameLengthCoefficient = 48;
					$FrameLengthPadding     = ( $MP3fileInfo['mpeg']['audio']['padding']? 4 : 0 ); // "For Layer I slot is 32 bits long, for Layer II and Layer III slot is 8 bits long."
				} 
				// Layer II / III
				else 
				{
					$FrameLengthCoefficient = 144;
					$FrameLengthPadding     = ( $MP3fileInfo['mpeg']['audio']['padding']? 1 : 0 ); // "For Layer I slot is 32 bits long, for Layer II and Layer III slot is 8 bits long."
				}
			}
			// MPEG-2 / MPEG-2.5 
			else 
			{
				if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'I' ) 
				{
					$FrameLengthCoefficient = 24;
					$FrameLengthPadding     = ( $MP3fileInfo['mpeg']['audio']['padding']? 4 : 0 ); // "For Layer I slot is 32 bits long, for Layer II and Layer III slot is 8 bits long."
				} 
				// Layer II / III
				else 
				{
					$FrameLengthCoefficient = 72;
					$FrameLengthPadding     = ( $MP3fileInfo['mpeg']['audio']['padding']? 1 : 0 ); // "For Layer I slot is 32 bits long, for Layer II and Layer III slot is 8 bits long."
				}
			}
			
			// FrameLengthInBytes = ((Coefficient * BitRate) / SampleRate) + Padding
			// http://66.96.216.160/cgi-bin/YaBB.pl?board=c&action=display&num=1018474068
			// -> "Finding the next frame synch" on www.r3mix.net forums if the above link goes dead
			if ( $MP3fileInfo['frequency'] > 0 )
				$MP3fileInfo['mpeg']['audio']['framelength'] = (int)floor( ( $FrameLengthCoefficient * 1000 * $MP3fileInfo['mpeg']['audio']['bitrate'] ) / $MP3fileInfo['frequency'] ) + $FrameLengthPadding;	
		}
	
		$MP3fileInfo['bitrate_audio'] = 1000 * $MP3fileInfo['mpeg']['audio']['bitrate'];


		// Variable-bitrate headers

		if ( substr( $headerstring, 4 + 32, 4 ) == 'VBRI' ) 
		{
			// Fraunhofer VBR header is hardcoded 'VBRI' at offset 0x24 (36)
			// specs taken from http://minnie.tuhs.org/pipermail/mp3encoder/2001-January/001800.html

			$MP3fileInfo['mpeg']['audio']['bitratemode'] = 'VBR';
			$MP3fileInfo['mpeg']['audio']['VBR_method']  = 'Fraunhofer';

			$SideInfoData = substr( $headerstring, 4 + 2, 32 );

			$FraunhoferVBROffset = 4 + 32 + strlen( 'VBRI' );

			$Fraunhofer_EncoderVersion = substr( $headerstring, $FraunhoferVBROffset, 2 );
			$FraunhoferVBROffset += 2;
			$MP3fileInfo['mpeg']['audio']['VBR_encoder_version'] = ID3::bigEndianToInt( $Fraunhofer_EncoderVersion );

			$Fraunhofer_EncoderDelay = substr( $headerstring, $FraunhoferVBROffset, 2 );
			$FraunhoferVBROffset += 2;
			$MP3fileInfo['mpeg']['audio']['VBR_encoder_delay'] = ID3::bigEndianToInt( $Fraunhofer_EncoderDelay );

			$Fraunhofer_quality = substr( $headerstring, $FraunhoferVBROffset, 2 );
			$FraunhoferVBROffset += 2;
			$MP3fileInfo['mpeg']['audio']['VBR_quality'] = ID3::bigEndianToInt( $Fraunhofer_quality );

			$Fraunhofer_Bytes = substr( $headerstring, $FraunhoferVBROffset, 4 );
			$FraunhoferVBROffset += 4;
			$MP3fileInfo['mpeg']['audio']['VBR_bytes'] = ID3::bigEndianToInt( $Fraunhofer_Bytes );

			$Fraunhofer_Frames = substr( $headerstring, $FraunhoferVBROffset, 4 );
			$FraunhoferVBROffset += 4;
			$MP3fileInfo['mpeg']['audio']['VBR_frames'] = ID3::bigEndianToInt( $Fraunhofer_Frames );

			$Fraunhofer_SeekOffsets = substr( $headerstring, $FraunhoferVBROffset, 2 );
			$FraunhoferVBROffset += 2;
			$MP3fileInfo['mpeg']['audio']['VBR_seek_offsets'] = ID3::bigEndianToInt( $Fraunhofer_SeekOffsets );

			$FraunhoferVBROffset += 4; // hardcoded $00 $01 $00 $02  - purpose unknown

			$Fraunhofer_OffsetStride = substr( $headerstring, $FraunhoferVBROffset, 2 );
			$FraunhoferVBROffset += 2;
			$MP3fileInfo['mpeg']['audio']['VBR_seek_offsets_stride'] = ID3::bigEndianToInt( $Fraunhofer_OffsetStride );

			$previousbyteoffset = $offset;
	
			for ( $i = 0; $i < $MP3fileInfo['mpeg']['audio']['VBR_seek_offsets']; $i++ ) 
			{
				$Fraunhofer_OffsetN = ID3::bigEndianToInt( substr( $headerstring, $FraunhoferVBROffset, 2 ) );
				$FraunhoferVBROffset += 2;
				$MP3fileInfo['mpeg']['audio']['VBR_offsets_relative']["$i"] = $Fraunhofer_OffsetN;
				$MP3fileInfo['mpeg']['audio']['VBR_offsets_absolute']["$i"] = $Fraunhofer_OffsetN + $previousbyteoffset;
				$previousbyteoffset += $Fraunhofer_OffsetN;
			}
		} 
		else 
		{
			// Xing VBR header is hardcoded 'Xing' at a offset 0x0D (13), 0x15 (21) or 0x24 (36)
			// depending on MPEG layer and number of channels

			if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
			{
				// MPEG-1 (mono)
				if ( $MP3fileInfo['mpeg']['audio']['channelmode'] == 'mono' ) 
				{
					$VBRidOffset  = 4 + 17; // 0x15
					$SideInfoData = substr( $headerstring, 4 + 2, 17 );
				}
				// MPEG-1 (stereo, joint-stereo, dual-channel) 
				else 
				{
					$VBRidOffset  = 4 + 32; // 0x24
					$SideInfoData = substr( $headerstring, 4 + 2, 32 );
				}
			} 
			// 2 or 2.5
			else 
			{
				// MPEG-2, MPEG-2.5 (mono)
				if ( $MP3fileInfo['mpeg']['audio']['channelmode'] == 'mono' ) 
				{
					$VBRidOffset  = 4 + 9; // 0x0D
					$SideInfoData = substr( $headerstring, 4 + 2, 9 );
				}
				// MPEG-2, MPEG-2.5 (stereo, joint-stereo, dual-channel) 
				else 
				{
					$VBRidOffset  = 4 + 17; // 0x15
					$SideInfoData = substr( $headerstring, 4 + 2, 17 );
				}
			}

			if ( ( substr( $headerstring, $VBRidOffset, strlen( 'Xing' ) ) == 'Xing') || ( substr( $headerstring, $VBRidOffset, strlen( 'Info' ) ) == 'Info' ) ) 
			{
				// 'Xing' is traditional Xing VBR frame, 'Info' is LAME-encoded CBR
				// "This was done to avoid CBR files to be recognized as traditional Xing VBR files by some decoders."

				$MP3fileInfo['mpeg']['audio']['bitratemode'] = 'VBR';
				$MP3fileInfo['mpeg']['audio']['VBR_method']  = 'Xing';

				$XingVBROffset = $VBRidOffset + strlen( 'Xing' );
				$MP3fileInfo['mpeg']['audio']['xing_flags_raw'] = substr( $headerstring, $XingVBROffset, 4 );
				$XingVBROffset += 4;
				$XingHeader_byte4 = ID3::bigEndianToBin(substr($MP3fileInfo['mpeg']['audio']['xing_flags_raw'], 3, 1));
				$MP3fileInfo['mpeg']['audio']['xing_flags']['frames']    = (bool)$XingHeader_byte4{4};
				$MP3fileInfo['mpeg']['audio']['xing_flags']['bytes']     = (bool)$XingHeader_byte4{5};
				$MP3fileInfo['mpeg']['audio']['xing_flags']['toc']       = (bool)$XingHeader_byte4{6};
				$MP3fileInfo['mpeg']['audio']['xing_flags']['vbr_scale'] = (bool)$XingHeader_byte4{7};
	
				if ( $MP3fileInfo['mpeg']['audio']['xing_flags']['frames'] ) 
				{
					$MP3fileInfo['mpeg']['audio']['VBR_frames'] = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 4 ) );
					$XingVBROffset += 4;
				}
			
				if ( $MP3fileInfo['mpeg']['audio']['xing_flags']['bytes'] ) 
				{
					$MP3fileInfo['mpeg']['audio']['VBR_bytes'] = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 4 ) );
					$XingVBROffset += 4;
				}

				if ( $MP3fileInfo['mpeg']['audio']['xing_flags']['toc'] ) 
				{
					$LAMEtocData    = substr( $headerstring, $XingVBROffset, 100 );
					$XingVBROffset += 100;
					
					for ( $i = 0; $i < 100; $i++ )
						$MP3fileInfo['mpeg']['audio']['toc']["$i"] = ord( $LAMEtocData{$i} );
				}
			
				if ( $MP3fileInfo['mpeg']['audio']['xing_flags']['vbr_scale'] ) 
				{
					$MP3fileInfo['mpeg']['audio']['VBR_scale'] = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 4 ) );
					$XingVBROffset += 4;
				}
				
				if ( substr( $headerstring, $XingVBROffset, 4 ) == 'LAME' ) 
				{
					$MP3fileInfo['mpeg']['audio']['LAME']['short_version'] = substr( $headerstring, $XingVBROffset, 9 );
					$XingVBROffset += 9;

					$LAMEtagRevisionVBRmethod = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 1 ) );
					$XingVBROffset += 1;
					$MP3fileInfo['mpeg']['audio']['LAME']['tag_revision']      = ( $LAMEtagRevisionVBRmethod & 0xF0 ) >> 4;
					$MP3fileInfo['mpeg']['audio']['LAME']['vbr_method_raw']    = $LAMEtagRevisionVBRmethod & 0x0F;
					$MP3fileInfo['mpeg']['audio']['LAME']['vbr_method']        = ID3_MP3::LAMEvbrMethodLookup( $MP3fileInfo['mpeg']['audio']['LAME']['vbr_method_raw'] );

					$MP3fileInfo['mpeg']['audio']['LAME']['lowpass_frequency'] = 100 * ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 1 ) );
					$XingVBROffset += 1;

					// http://privatewww.essex.ac.uk/~djmrob/replaygain/rg_data_format.html
					$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']['peak_amplitude'] = ID3::bigEndianToFloat( substr( $headerstring, $XingVBROffset, 4 ) );
					$XingVBROffset += 4;

					$RadioReplayGainRaw  = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 2 ) );
					$XingVBROffset      += 4;
					$ReplayGainID        = ( $RadioReplayGainRaw & 0xE000 ) >> 13;
					$ReplayGainNameKey   = '';
	
					switch ( $ReplayGainID ) 
					{
						case 1:
							$ReplayGainNameKey = 'radio';
							break;

						case 2:
							$ReplayGainNameKey = 'audiophile';
							break;

						case 0:  // replay gain not set
					
						default: // reserved
							break;
					}
		
					if ( $ReplayGainNameKey ) 
					{
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['raw']['name']        = ( $RadioReplayGainRaw & 0xE000 ) >> 13;
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['raw']['originator']  = ( $RadioReplayGainRaw & 0x1C00 ) >> 10;
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['raw']['sign_bit']    = ( $RadioReplayGainRaw & 0x0200 ) >> 9;
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['raw']['gain_adjust'] =   $RadioReplayGainRaw & 0x01FF;
			
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['name']       = ID3::RGADnameLookup( $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']['radio_replay_gain']['raw']['name'] );
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['originator'] = ID3::RGADoriginatorLookup( $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']['radio_replay_gain']['raw']['originator'] );
						$MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['gain_db']    = ID3::RGADadjustmentLookup( $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']['radio_replay_gain']['raw']['gain_adjust'], $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']['radio_replay_gain']['raw']['sign_bit'] );

						$MP3fileInfo['replay_gain']["$ReplayGainNameKey"]['peak']       = $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']['peak_amplitude'];
						$MP3fileInfo['replay_gain']["$ReplayGainNameKey"]['originator'] = $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['originator'];
						$MP3fileInfo['replay_gain']["$ReplayGainNameKey"]['adjustment'] = $MP3fileInfo['mpeg']['audio']['LAME']['RGAD']["$ReplayGainNameKey"]['gain_db'];
					}

					$EncodingFlagsATHtype = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 1 ) );
					$XingVBROffset += 1;
				
					$MP3fileInfo['mpeg']['audio']['LAME']['encoding_flags']['nspsytune']   = (bool)( $EncodingFlagsATHtype & 0x10 );
					$MP3fileInfo['mpeg']['audio']['LAME']['encoding_flags']['nssafejoint'] = (bool)( $EncodingFlagsATHtype & 0x20 );
					$MP3fileInfo['mpeg']['audio']['LAME']['encoding_flags']['nogap_next']  = (bool)( $EncodingFlagsATHtype & 0x40 );
					$MP3fileInfo['mpeg']['audio']['LAME']['encoding_flags']['nogap_prev']  = (bool)( $EncodingFlagsATHtype & 0x80 );
					$MP3fileInfo['mpeg']['audio']['LAME']['ath_type'] = $EncodingFlagsATHtype & 0x0F;

					$ABRbitrateMinBitrate = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 1 ) );
					$XingVBROffset += 1;
	
					// Average BitRate (ABR)
					if ( $MP3fileInfo['mpeg']['audio']['LAME']['vbr_method_raw'] == 2 ) 
					{
						$MP3fileInfo['mpeg']['audio']['LAME']['bitrate_abr'] = $ABRbitrateMinBitrate;
					}
					// Variable BitRate (VBR) - minimum bitrate 
					else if ( $ABRbitrateMinBitrate > 0 ) 
					{
						$MP3fileInfo['mpeg']['audio']['LAME']['bitrate_min'] = $ABRbitrateMinBitrate;
					}

					$EncoderDelays = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 3 ) );
					$XingVBROffset += 3;
					$MP3fileInfo['mpeg']['audio']['LAME']['encoder_delay'] = ( $EncoderDelays & 0xFFF000 ) >> 12;
					$MP3fileInfo['mpeg']['audio']['LAME']['end_padding']   =   $EncoderDelays & 0x000FFF;

					$MiscByte = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 1 ) );
					$XingVBROffset += 1;
					
					$MP3fileInfo['mpeg']['audio']['LAME']['noise_shaping_raw']       =   $EncodingFlagsATHtype & 0x03;
					$MP3fileInfo['mpeg']['audio']['LAME']['stereo_mode_raw']         = ( $EncodingFlagsATHtype & 0x1C ) >> 2;
					$MP3fileInfo['mpeg']['audio']['LAME']['not_optimal_quality_raw'] = ( $EncodingFlagsATHtype & 0x20 ) >> 5;
					$MP3fileInfo['mpeg']['audio']['LAME']['source_sample_freq_raw']  = ( $EncodingFlagsATHtype & 0xC0 ) >> 6;
					
					$MP3fileInfo['mpeg']['audio']['LAME']['noise_shaping']       = $MP3fileInfo['mpeg']['audio']['LAME']['noise_shaping_raw'];
					$MP3fileInfo['mpeg']['audio']['LAME']['stereo_mode']         = ID3_MP3::LAMEmiscStereoModeLookup( $MP3fileInfo['mpeg']['audio']['LAME']['stereo_mode_raw'] );
					$MP3fileInfo['mpeg']['audio']['LAME']['not_optimal_quality'] = (bool)$MP3fileInfo['mpeg']['audio']['LAME']['not_optimal_quality_raw'];
					$MP3fileInfo['mpeg']['audio']['LAME']['source_sample_freq']  = ID3_MP3::LAMEmiscSourceSampleFrequencyLookup( $MP3fileInfo['mpeg']['audio']['LAME']['source_sample_freq_raw'] );

					$MP3fileInfo['mpeg']['audio']['LAME']['mp3_gain_raw'] = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 1 ), false, true );
					$XingVBROffset += 1;
					$MP3fileInfo['mpeg']['audio']['LAME']['mp3_gain'] = 1.5 * $MP3fileInfo['mpeg']['audio']['LAME']['mp3_gain_raw'];

					$ReservedBytes = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 2 ) );
					$XingVBROffset += 2;

					$MP3fileInfo['mpeg']['audio']['LAME']['audio_bytes']  = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 4 ) );
					$XingVBROffset += 4;

					$MP3fileInfo['mpeg']['audio']['LAME']['music_crc']    = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 2 ) );
					$XingVBROffset += 2;	

					$MP3fileInfo['mpeg']['audio']['LAME']['lame_tag_crc'] = ID3::bigEndianToInt( substr( $headerstring, $XingVBROffset, 2 ) );
					$XingVBROffset += 2;
				}
			} 
			else 
			{
				// not Fraunhofer or Xing VBR methods, must be CBR
				$MP3fileInfo['mpeg']['audio']['bitratemode'] = 'CBR';
			}
		}
		
		if ( ( $MP3fileInfo['mpeg']['audio']['bitratemode'] == 'VBR' ) && isset( $MP3fileInfo['mpeg']['audio']['VBR_frames'] ) && ( $MP3fileInfo['mpeg']['audio']['VBR_frames'] > 0 ) ) 
		{
			$MP3fileInfo['mpeg']['audio']['VBR_frames']--; // don't count the Xing / VBRI frame
		
			if ( ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) && ( $MP3fileInfo['mpeg']['audio']['layer'] == 'I' ) )
				$MP3fileInfo['mpeg']['audio']['VBR_bitrate'] = ( ( ( $MP3fileInfo['mpeg']['audio']['VBR_bytes'] / $MP3fileInfo['mpeg']['audio']['VBR_frames'] ) * 8 ) * ( $MP3fileInfo['frequency'] /  384 ) ) / 1000;
			else if ( ( ( $MP3fileInfo['mpeg']['audio']['version'] == '2' ) || ( $MP3fileInfo['mpeg']['audio']['version'] == '2.5' ) ) && ( $MP3fileInfo['mpeg']['audio']['layer'] == 'III' ) )
				$MP3fileInfo['mpeg']['audio']['VBR_bitrate'] = ( ( ( $MP3fileInfo['mpeg']['audio']['VBR_bytes'] / $MP3fileInfo['mpeg']['audio']['VBR_frames'] ) * 8 ) * ( $MP3fileInfo['frequency'] /  576 ) ) / 1000;
			else
				$MP3fileInfo['mpeg']['audio']['VBR_bitrate'] = ( ( ( $MP3fileInfo['mpeg']['audio']['VBR_bytes'] / $MP3fileInfo['mpeg']['audio']['VBR_frames'] ) * 8 ) * ( $MP3fileInfo['frequency'] / 1152 ) ) / 1000;
		
			if ( $MP3fileInfo['mpeg']['audio']['VBR_bitrate'] > 0 ) 
			{
				$MP3fileInfo['bitrate_audio'] = 1000 * $MP3fileInfo['mpeg']['audio']['VBR_bitrate'];
				unset( $MP3fileInfo['mpeg']['audio']['bitrate'] ); // to avoid confusion
			}
		}
		
		
		// End variable-bitrate headers

		if ( isset( $MP3fileInfo['mpeg']['audio']['framelength'] ) ) 
		{
			$nextframetestoffset = $offset + $MP3fileInfo['mpeg']['audio']['framelength'];
		} 
		else 
		{
			$MP3fileInfo['error'][] = 'Frame at offset(' . $offset . ') is has an invalid frame length.';
			return false;
		}

		if ( $recursivesearch ) 
		{
			for ( $i = 0; $i < ID3_MP3_VALID_CHECK_FRAMES; $i++ ) 
			{
				// check next ID3_MP3_VALID_CHECK_FRAMES frames for validity, to make sure we haven't run across a false synch
				if ( $nextframetestoffset >= $MP3fileInfo['filesize'] ) 
				{
					// end of file
					break;
				}
			
				$nextframetestarray = array(
					'error'    => '', 
					'filesize' => $MP3fileInfo['filesize']
				);
				
				if ( ID3_MP3::decodeMPEGaudioHeader( $fd, $nextframetestoffset, $nextframetestarray, false ) ) 
				{
					if ( $ScanAsCBR ) 
					{
						// force CBR mode, used for trying to pick out invalid audio streams with
						// valid(?) VBR headers
						if ( $nextframetestarray['mpeg']['audio']['bitrate'] != $MP3fileInfo['mpeg']['audio']['bitrate'] )
							return false;
					}

					// next frame is OK, get ready to check the one after that
					if ( isset( $nextframetestarray['mpeg']['audio']['framelength'] ) && ( $nextframetestarray['mpeg']['audio']['framelength'] > 0 ) ) 
					{
						$nextframetestoffset += $nextframetestarray['mpeg']['audio']['framelength'];
					} 
					else 
					{
						$MP3fileInfo['error'][] = 'Frame at offset(' . $offset . ') is has an invalid frame length.';
						return false;
					}
				} 
				else 
				{
					// next frame is not valid, note the error and fail, so scanning can contiue for a valid frame sequence
					$MP3fileInfo['error'][] = 'Frame at offset(' . $offset . ') is valid, but the next one at (' . $nextframetestoffset . ') is not.';

					return false;
				}
			}
		}

		if ( false ) 
		{
			// experimental side info parsing section - not returning anything useful yet

			$SideInfoBitstream = ID3::bigEndianToBin( $SideInfoData );
			$SideInfoOffset    = 0;

			if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
			{
				// MPEG-1 (mono)
				if ( $MP3fileInfo['mpeg']['audio']['channelmode'] == 'mono' ) 
				{
					$MP3fileInfo['mpeg']['audio']['side_info']['main_data_begin'] = substr( $SideInfoBitstream, $SideInfoOffset, 9 );
					$SideInfoOffset += 9;
					$SideInfoOffset += 5;
				}
				// MPEG-1 (stereo, joint-stereo, dual-channel) 
				else 
				{
					$MP3fileInfo['mpeg']['audio']['side_info']['main_data_begin'] = substr( $SideInfoBitstream, $SideInfoOffset, 9 );
					$SideInfoOffset += 9;
					$SideInfoOffset += 3;
				}
			}
			// 2 or 2.5 
			else 
			{
				// MPEG-2, MPEG-2.5 (mono)
				if ( $MP3fileInfo['mpeg']['audio']['channelmode'] == 'mono' ) 
				{
					$MP3fileInfo['mpeg']['audio']['side_info']['main_data_begin'] = substr( $SideInfoBitstream, $SideInfoOffset, 8 );
					$SideInfoOffset += 8;
					$SideInfoOffset += 1;
				}
				// MPEG-2, MPEG-2.5 (stereo, joint-stereo, dual-channel) 
				else 
				{
					$MP3fileInfo['mpeg']['audio']['side_info']['main_data_begin'] = substr( $SideInfoBitstream, $SideInfoOffset, 8 );
					$SideInfoOffset += 8;
					$SideInfoOffset += 2;
				}
			}

			if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
			{
				for ( $channel = 0; $channel < $MP3fileInfo['channels']; $channel++ ) 
				{
					for ( $scfsi_band = 0; $scfsi_band < 4; $scfsi_band++ ) 
					{
						$MP3fileInfo['mpeg']['audio']['scfsi']["$channel"]["$scfsi_band"] = substr( $SideInfoBitstream, $SideInfoOffset, 1 );
						$SideInfoOffset += 2;
					}
				}
			}
			
			for ( $granule = 0; $granule < ( ( $MP3fileInfo['mpeg']['audio']['version'] == '1' )? 2 : 1 ); $granule++ ) 
			{
				for ( $channel = 0; $channel < $MP3fileInfo['channels']; $channel++ ) 
				{
					$MP3fileInfo['mpeg']['audio']['part2_3_length']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 12 );
					$SideInfoOffset += 12;
					$MP3fileInfo['mpeg']['audio']['big_values']["$granule"]["$channel"]     = substr( $SideInfoBitstream, $SideInfoOffset,  9 );
					$SideInfoOffset += 9;
					$MP3fileInfo['mpeg']['audio']['global_gain']["$granule"]["$channel"]    = substr( $SideInfoBitstream, $SideInfoOffset,  8 );
					$SideInfoOffset += 8;
				
					if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
					{
						$MP3fileInfo['mpeg']['audio']['scalefac_compress']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 4 );
						$SideInfoOffset += 4;
					} 
					else 
					{
						$MP3fileInfo['mpeg']['audio']['scalefac_compress']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 9 );
						$SideInfoOffset += 9;
					}
					
					$MP3fileInfo['mpeg']['audio']['window_switching_flag']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 1 );
					$SideInfoOffset += 1;

					if ( $MP3fileInfo['mpeg']['audio']['window_switching_flag']["$granule"]["$channel"] == '1' ) 
					{
						$MP3fileInfo['mpeg']['audio']['block_type']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 2 );
						$SideInfoOffset += 2;
						$MP3fileInfo['mpeg']['audio']['mixed_block_flag']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 1 );
						$SideInfoOffset += 1;

						for ( $region = 0; $region < 2; $region++ ) 
						{
							$MP3fileInfo['mpeg']['audio']['table_select']["$granule"]["$channel"]["$region"] = substr( $SideInfoBitstream, $SideInfoOffset, 5 );
							$SideInfoOffset += 5;
						}
					
						$MP3fileInfo['mpeg']['audio']['table_select']["$granule"]["$channel"][2] = 0;

						for ( $window = 0; $window < 3; $window++ ) 
						{
							$MP3fileInfo['mpeg']['audio']['subblock_gain']["$granule"]["$channel"]["$window"] = substr( $SideInfoBitstream, $SideInfoOffset, 3 );
							$SideInfoOffset += 3;
						}
					} 
					else 
					{
						for ( $region = 0; $region < 3; $region++ ) 
						{
							$MP3fileInfo['mpeg']['audio']['table_select']["$granule"]["$channel"]["$region"] = substr( $SideInfoBitstream, $SideInfoOffset, 5 );
							$SideInfoOffset += 5;
						}

						$MP3fileInfo['mpeg']['audio']['region0_count']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 4 );
						$SideInfoOffset += 4;
						$MP3fileInfo['mpeg']['audio']['region1_count']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 3 );
						$SideInfoOffset += 3;
						$MP3fileInfo['mpeg']['audio']['block_type']["$granule"]["$channel"] = 0;
					}

					if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
					{
						$MP3fileInfo['mpeg']['audio']['preflag']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 1 );
						$SideInfoOffset += 1;
					}
					
					$MP3fileInfo['mpeg']['audio']['scalefac_scale']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 1 );
					$SideInfoOffset += 1;
					$MP3fileInfo['mpeg']['audio']['count1table_select']["$granule"]["$channel"] = substr( $SideInfoBitstream, $SideInfoOffset, 1 );
					$SideInfoOffset += 1;
				}
			}
		}

		return true;
	}

	function getOnlyMPEGaudioInfo( $fd, &$MP3fileInfo, $audiodataoffset, $BitrateHistogram = false ) 
	{
		// looks for synch, decodes MPEG audio header
		// you may call this function directly if you don't need any ID3 info
		fseek( $fd, $audiodataoffset );
		$header = '';
		$SynchSeekOffset = 0;
	
		while ( !isset( $MP3fileInfo['fileformat'] ) || ( $MP3fileInfo['fileformat'] == '' ) || ( $MP3fileInfo['fileformat'] == 'id3' ) ) 
		{
			if ( ( $SynchSeekOffset > ( strlen( $header ) - 8192 ) ) && !feof( $fd ) ) 
			{
				if ( $SynchSeekOffset > ( ID3_FREAD_BUFFER_SIZE * 4 ) ) 
				{
					// if a synch's not found within the first 64k bytes, then give up
					$MP3fileInfo['error'][] = 'could not find valid MPEG synch within the first ' . ( ID3_FREAD_BUFFER_SIZE * 4 ) . ' bytes.';
				
					if ( isset( $MP3fileInfo['bitrate_audio'] ) )
						unset( $MP3fileInfo['bitrate_audio'] );
				
					if ( isset( $MP3fileInfo['mpeg']['audio'] ) )
						unset( $MP3fileInfo['mpeg']['audio'] );
				
					if ( isset( $MP3fileInfo['mpeg'] ) && ( !is_array( $MP3fileInfo['mpeg'] ) || ( count( $MP3fileInfo['mpeg'] ) == 0 ) ) )
						unset( $MP3fileInfo['mpeg'] );
				
					return false;
				} 
				else if ( $header .= fread( $fd, ID3_FREAD_BUFFER_SIZE ) ) 
				{
					// great
				} 
				else 
				{
					$MP3fileInfo['error'][] = 'Could not find valid MPEG synch before end of file.';
				
					if ( isset( $MP3fileInfo['bitrate_audio'] ) )
						unset( $MP3fileInfo['bitrate_audio'] );
				
					if ( isset( $MP3fileInfo['mpeg']['audio'] ) )
						unset( $MP3fileInfo['mpeg']['audio'] );
				
					if ( isset( $MP3fileInfo['mpeg'] ) && ( !is_array( $MP3fileInfo['mpeg'] ) || ( count( $MP3fileInfo['mpeg'] ) == 0 ) ) )
						unset( $MP3fileInfo['mpeg'] );
				
					return false;
				}
			}
		
			// synch detected
			if ( ( ord( $header{$SynchSeekOffset} ) == 0xFF ) && substr( ID3::bigEndianToBin( substr( $header, $SynchSeekOffset, 2 ) ), 0, 11 ) == '11111111111' ) 
			{
				if ( !isset( $FirstFrameMP3fileInfo ) && !isset( $MP3fileInfo['mpeg']['audio'] ) ) 
				{
					$FirstFrameMP3fileInfo     = $MP3fileInfo;
					$FirstFrameAudioDataOffset = $audiodataoffset + $SynchSeekOffset;
				
					if ( !ID3_MP3::decodeMPEGaudioHeader( $fd, $audiodataoffset + $SynchSeekOffset, $FirstFrameMP3fileInfo, false ) ) 
					{
						// if this is the first valid MPEG-audio frame, save it in case it's a VBR header frame and there's
						// garbage between this frame and a valid sequence of MPEG-audio frames, to be restored below
						unset( $FirstFrameMP3fileInfo );
					}
				}
		
				$dummy = $MP3fileInfo; // only overwrite real data if valid header found
		
				if ( ID3_MP3::decodeMPEGaudioHeader( $fd, $audiodataoffset + $SynchSeekOffset, $dummy, true ) ) 
				{
					$MP3fileInfo = $dummy;
					$MP3fileInfo['audiodataoffset'] = $audiodataoffset + $SynchSeekOffset;
					$MP3fileInfo['fileformat'] = 'mp3';
				
					if ( isset( $FirstFrameMP3fileInfo['mpeg']['audio']['bitratemode'] ) && ( $FirstFrameMP3fileInfo['mpeg']['audio']['bitratemode'] == 'VBR' ) ) 
					{
						if ( !ID3::closeMatch( $MP3fileInfo['bitrate_audio'], $FirstFrameMP3fileInfo['bitrate_audio'], 1 ) ) 
						{
							// If there is garbage data between a valid VBR header frame and a sequence
							// of valid MPEG-audio frames the VBR data is no longer discarded.
							$MP3fileInfo = $FirstFrameMP3fileInfo;
							$MP3fileInfo['audiodataoffset'] = $FirstFrameAudioDataOffset;
							$GarbageOffsetStart = $FirstFrameAudioDataOffset + $FirstFrameMP3fileInfo['mpeg']['audio']['framelength'];
							$GarbageOffsetEnd   = $audiodataoffset + $SynchSeekOffset;
						
							if ( ID3_MP3::decodeMPEGaudioHeader( $fd, $GarbageOffsetEnd, $dummy, true, true ) ) 
							{
								$MP3fileInfo = $dummy;
								$MP3fileInfo['error'][] = 'apparently-valid VBR header not used because could not find ' . ID3_MP3_VALID_CHECK_FRAMES . ' consecutive MPEG-audio frames immediately after VBR header (garbage data for ' . ( $GarbageOffsetEnd - $GarbageOffsetStart ) . ' bytes between ' . $GarbageOffsetStart . ' and ' . $GarbageOffsetEnd . '), but did find valid CBR stream starting at ' . $GarbageOffsetEnd . '.';
								$MP3fileInfo['audiodataoffset'] = $GarbageOffsetEnd;
							} 
							else 
							{
								$MP3fileInfo['error'][] = 'Using data from VBR header even though could not find ' . ID3_MP3_VALID_CHECK_FRAMES . ' consecutive MPEG-audio frames immediately after VBR header (garbage data for ' . ( $GarbageOffsetEnd - $GarbageOffsetStart ) . ' bytes between ' . $GarbageOffsetStart . ' and ' . $GarbageOffsetEnd . ').';
							}
						}
					}

					if ( $BitrateHistogram ) 
					{
						$MP3fileInfo['mpeg']['audio']['stereo_distribution'] = array(
							'stereo'       => 0, 
							'joint stereo' => 0, 
							'dual channel' => 0, 
							'mono'         => 0
						);

						if ( $MP3fileInfo['mpeg']['audio']['version'] == '1' ) 
						{
							if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'III' ) 
							{
								$MP3fileInfo['mpeg']['audio']['bitrate_distribution'] = array(
									'free' => 0, 
									32     => 0, 
									40     => 0, 
									48     => 0, 
									56     => 0, 
									64     => 0, 
									80     => 0, 
									96     => 0, 
									112    => 0, 
									128    => 0, 
									160    => 0, 
									192    => 0, 
									224    => 0, 
									256    => 0, 
									320    => 0
								);
							} 
							else if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'II' ) 
							{
								$MP3fileInfo['mpeg']['audio']['bitrate_distribution'] = array(
									'free' => 0, 
									32     => 0, 
									48     => 0, 
									56     => 0, 
									64     => 0, 
									80     => 0, 
									96     => 0, 
									112    => 0, 
									128    => 0, 
									160    => 0, 
									192    => 0, 
									224    => 0, 
									256    => 0, 
									320    => 0, 
									384    => 0
								);
							} 
							else if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'I' ) 
							{
								$MP3fileInfo['mpeg']['audio']['bitrate_distribution'] = array(
									'free' => 0, 
									32     => 0, 
									64     => 0, 
									96     => 0, 
									128    => 0, 
									160    => 0, 
									192    => 0, 
									224    => 0, 
									256    => 0, 
									288    => 0, 
									320    => 0, 
									352    => 0, 
									384    => 0, 
									416    => 0, 
									448    => 0
								);
							}
						} 
						else if ( $MP3fileInfo['mpeg']['audio']['layer'] == 'I' ) 
						{
							$MP3fileInfo['mpeg']['audio']['bitrate_distribution'] = array(
								'free' => 0, 
								32     => 0, 
								48     => 0, 
								56     => 0, 
								64     => 0, 
								80     => 0, 
								96     => 0, 
								112    => 0, 
								128    => 0, 
								144    => 0, 
								160    => 0, 
								176    => 0, 
								192    => 0, 
								224    => 0, 
								256    => 0
							);
						} 
						else 
						{
							$MP3fileInfo['mpeg']['audio']['bitrate_distribution'] = array(
								'free' => 0, 
								8      => 0, 
								16     => 0, 
								24     => 0, 
								32     => 0, 
								40     => 0, 
								48     => 0, 
								56     => 0, 
								64     => 0, 
								80     => 0, 
								96     => 0, 
								112    => 0, 
								128    => 0, 
								144    => 0, 
								160    => 0
							);
						}

						$dummy = array( 'filesize' => $MP3fileInfo['filesize'], 'error' => $MP3fileInfo['error'] );
						$synchstartoffset = $MP3fileInfo['audiodataoffset'];
					
						while ( ID3_MP3::decodeMPEGaudioHeader( $fd, $synchstartoffset, $dummy, false ) ) 
						{
							$thisframebitrate = ID3_MP3::MPEGaudioBitrateLookup( ID3_MP3::MPEGaudioVersionLookup( $dummy['mpeg']['audio']['raw']['version'] ), ID3_MP3::MPEGaudioLayerLookup( $dummy['mpeg']['audio']['raw']['layer'] ), $dummy['mpeg']['audio']['raw']['bitrate'] );
							$MP3fileInfo['mpeg']['audio']['bitrate_distribution']["$thisframebitrate"]++;
							$MP3fileInfo['mpeg']['audio']['stereo_distribution'][$dummy['mpeg']['audio']['channelmode']]++;
						
							if ( !isset( $dummy['mpeg']['audio']['framelength'] ) ) 
							{
								$MP3fileInfo['error'][] = 'Invalid/missing framelength in histogram analysis - aborting.';
								break;
							}

							$synchstartoffset += $dummy['mpeg']['audio']['framelength'];
						}
					}

					break;
				}
			}
		
			if ( !isset( $MP3fileInfo['fileformat'] ) || ( $MP3fileInfo['fileformat'] == '' ) || ( $MP3fileInfo['fileformat'] == 'id3' ) ) 
			{
				$SynchSeekOffset++;
			
				if ( ( $audiodataoffset + $SynchSeekOffset ) >= $MP3fileInfo['filesize'] ) 
				{
					// end of file
					$MP3fileInfo['error'][] = 'Could not find valid MPEG synch before end of file.';
				
					if ( isset( $MP3fileInfo['bitrate_audio'] ) )
						unset( $MP3fileInfo['bitrate_audio'] );
				
					if ( isset( $MP3fileInfo['mpeg']['audio'] ) )
						unset( $MP3fileInfo['mpeg']['audio'] );
				
					if ( isset( $MP3fileInfo['mpeg'] ) && ( !is_array( $MP3fileInfo['mpeg'] ) || ( count( $MP3fileInfo['mpeg'] ) == 0 ) ) )
						unset( $MP3fileInfo['mpeg'] );
				
					return false;
				}
			}
		}
	
		return true;
	}

	function getMP3header( $filename, &$MP3fileInfo ) 
	{
		$fd = fopen( $filename, 'rb' );
		return ID3_MP3::getMP3headerFilepointer( $fd, $MP3fileInfo );
	}

	function MPEGaudioVersionLookup( $rawversion ) 
	{
		$MPEGaudioVersionLookup = array( '2.5', false, '2', '1' );
		return ( isset( $MPEGaudioVersionLookup["$rawversion"] )? $MPEGaudioVersionLookup["$rawversion"] : false );
	}

	function MPEGaudioLayerLookup( $rawlayer ) 
	{
		$MPEGaudioLayerLookup = array( false, 'III', 'II', 'I' );
		return ( isset( $MPEGaudioLayerLookup["$rawlayer"] )? $MPEGaudioLayerLookup["$rawlayer"] : false );
	}

	function MPEGaudioBitrateLookup( $version, $layer, $rawbitrate ) 
	{
		$MPEGaudioBitrateLookup['1']['I']     = array( 'free', 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448 );
		$MPEGaudioBitrateLookup['1']['II']    = array( 'free', 32, 48, 56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 384 );
		$MPEGaudioBitrateLookup['1']['III']   = array( 'free', 32, 40, 48,  56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320 );
		$MPEGaudioBitrateLookup['2']['I']     = array( 'free', 32, 48, 56,  64,  80,  96, 112, 128, 144, 160, 176, 192, 224, 256 );
		$MPEGaudioBitrateLookup['2.5']['I']   = $MPEGaudioBitrateLookup['2']['I'];
		$MPEGaudioBitrateLookup['2']['II']    = array( 'free',  8, 16, 24,  32,  40,  48,  56,  64,  80,  96, 112, 128, 144, 160 );
		$MPEGaudioBitrateLookup['2']['III']   = $MPEGaudioBitrateLookup['2']['II'];
		$MPEGaudioBitrateLookup['2.5']['II']  = $MPEGaudioBitrateLookup['2']['II'];
		$MPEGaudioBitrateLookup['2.5']['III'] = $MPEGaudioBitrateLookup['2']['II'];

		return ( isset( $MPEGaudioBitrateLookup["$version"]["$layer"]["$rawbitrate"] )? $MPEGaudioBitrateLookup["$version"]["$layer"]["$rawbitrate"] : false );
	}

	function MPEGaudioFrequencyLookup( $version, $rawfrequency ) 
	{
		$MPEGaudioFrequencyLookup['1']   = array( 44100, 48000, 32000 );
		$MPEGaudioFrequencyLookup['2']   = array( 22050, 24000, 16000 );
		$MPEGaudioFrequencyLookup['2.5'] = array( 11025, 12000,  8000 );
	
		return ( isset( $MPEGaudioFrequencyLookup["$version"]["$rawfrequency"] )? $MPEGaudioFrequencyLookup["$version"]["$rawfrequency"] : false );
	}

	function MPEGaudioChannelModeLookup( $rawchannelmode ) 
	{
		$MPEGaudioChannelModeLookup = array(
			'stereo', 
			'joint stereo', 
			'dual channel', 
			'mono'
		);
	
		return ( isset( $MPEGaudioChannelModeLookup["$rawchannelmode"] )? $MPEGaudioChannelModeLookup["$rawchannelmode"] : false );
	}

	function MPEGaudioModeExtensionLookup( $layer, $rawmodeextension ) 
	{
		$MPEGaudioModeExtensionLookup['I']   = array( '4-31', '8-31', '12-31', '16-31' );
		$MPEGaudioModeExtensionLookup['II']  = array( '4-31', '8-31', '12-31', '16-31' );
		$MPEGaudioModeExtensionLookup['III'] = array(     '',   'IS',    'MS', 'IS+MS' );

		return ( isset( $MPEGaudioModeExtensionLookup["$layer"]["$rawmodeextension"] )? $MPEGaudioModeExtensionLookup["$layer"]["$rawmodeextension"] : false );
	}

	function MPEGaudioEmphasisLookup( $rawemphasis ) 
	{
		$MPEGaudioEmphasisLookup = array( 'none', '50/15ms', false, 'CCIT J.17' );
		return ( isset( $MPEGaudioEmphasisLookup["$rawemphasis"] )? $MPEGaudioEmphasisLookup["$rawemphasis"] : false );
	}

	function MPEGaudioCRCLookup( $CRCbit ) 
	{
		// inverse boolean cast :)
		if ( $CRCbit == '0' )
			return true;
		else
			return false;
	}

	function MPEGaudioHeaderValid( $rawarray ) 
	{
		if ( ( $rawarray['synch'] & 0x0FFE ) != 0x0FFE )
			return false;
	
		$decodedVersion = ID3_MP3::MPEGaudioVersionLookup( $rawarray['version'] );
		$decodedLayer   = ID3_MP3::MPEGaudioLayerLookup( $rawarray['layer'] );
	
		if ( $decodedVersion === false )
			return false;
	
		if ( $decodedLayer === false )
			return false;
	
		if ( ID3_MP3::MPEGaudioBitrateLookup( $decodedVersion, $decodedLayer, $rawarray['bitrate'] ) === false )
			return false;
		
		if ( ID3_MP3::MPEGaudioFrequencyLookup( $decodedVersion, $rawarray['frequency'] ) === false )
			return false;
	
		if ( ID3_MP3::MPEGaudioChannelModeLookup( $rawarray['channelmode'] ) === false )
			return false;
	
		if ( ID3_MP3::MPEGaudioModeExtensionLookup( $decodedLayer, $rawarray['modeextension'] ) === false )
			return false;
	
		if ( ID3_MP3::MPEGaudioEmphasisLookup( $rawarray['emphasis'] ) === false )
			return false;
	
		// These are just either set or not set, you can't mess that up :)
		// $rawarray['protection'];
		// $rawarray['padding'];
		// $rawarray['private'];
		// $rawarray['copyright'];
		// $rawarray['original'];

		return true;
	}

	function MPEGaudioHeaderDecode( $Header4Bytes ) 
	{
		// AAAA AAAA  AAAB BCCD  EEEE FFGH  IIJJ KLMM
		// A - Frame sync (all bits set)
		// B - MPEG Audio version ID
		// C - Layer description
		// D - Protection bit
		// E - Bitrate index
		// F - Sampling rate frequency index
		// G - Padding bit
		// H - Private bit
		// I - Channel Mode
		// J - Mode extension (Only if Joint stereo)
		// K - Copyright
		// L - Original
		// M - Emphasis

		$MPEGrawHeader['synch']         = ( ID3::bigEndianToInt( substr( $Header4Bytes, 0, 2 ) ) & 0xFFE0 ) >> 4;
		$MPEGrawHeader['version']       = ( ord( $Header4Bytes{1} ) & 0x18 ) >> 3; //    BB
		$MPEGrawHeader['layer']         = ( ord( $Header4Bytes{1} ) & 0x06 ) >> 1; //      CC
		$MPEGrawHeader['protection']    = ( ord( $Header4Bytes{1} ) & 0x01 );      //        D
		$MPEGrawHeader['bitrate']       = ( ord( $Header4Bytes{2} ) & 0xF0 ) >> 4; // EEEE
		$MPEGrawHeader['frequency']     = ( ord( $Header4Bytes{2} ) & 0x0C ) >> 2; //     FF
		$MPEGrawHeader['padding']       = ( ord( $Header4Bytes{2} ) & 0x02 ) >> 1; //       G
		$MPEGrawHeader['private']       = ( ord( $Header4Bytes{2} ) & 0x01 );      //        H
		$MPEGrawHeader['channelmode']   = ( ord( $Header4Bytes{3} ) & 0xC0 ) >> 6; // II
		$MPEGrawHeader['modeextension'] = ( ord( $Header4Bytes{3} ) & 0x30 ) >> 4; //   JJ
		$MPEGrawHeader['copyright']     = ( ord( $Header4Bytes{3} ) & 0x08 ) >> 3; //     K
		$MPEGrawHeader['original']      = ( ord( $Header4Bytes{3} ) & 0x04 ) >> 2; //      L
		$MPEGrawHeader['emphasis']      = ( ord( $Header4Bytes{3} ) & 0x03 );      //       MM

		return $MPEGrawHeader;
	}

	function LAMEvbrMethodLookup( $VBRmethodID ) 
	{
		static $LAMEvbrMethodLookup = array();
	
		if ( count( $LAMEvbrMethodLookup ) < 1 ) 
		{
			$LAMEvbrMethodLookup[0x00] = 'unknown';
			$LAMEvbrMethodLookup[0x01] = 'no vbr / cbr';
			$LAMEvbrMethodLookup[0x02] = 'abr';
			$LAMEvbrMethodLookup[0x03] = 'vbr-old / vbr-rh';
			$LAMEvbrMethodLookup[0x04] = 'vbr-mtrh';
			$LAMEvbrMethodLookup[0x05] = 'vbr-new / vbr-mt';
		}
	
		return ( isset( $LAMEvbrMethodLookup["$VBRmethodID"] )? $LAMEvbrMethodLookup["$VBRmethodID"] : '' );
	}

	function LAMEmiscStereoModeLookup( $StereoModeID ) 
	{
		static $LAMEmiscStereoModeLookup = array();

		if ( count( $LAMEmiscStereoModeLookup ) < 1 ) 
		{
			$LAMEmiscStereoModeLookup[0] = 'mono';
			$LAMEmiscStereoModeLookup[1] = 'stereo';
			$LAMEmiscStereoModeLookup[2] = 'dual';
			$LAMEmiscStereoModeLookup[3] = 'joint';
			$LAMEmiscStereoModeLookup[4] = 'forced';
			$LAMEmiscStereoModeLookup[5] = 'auto';
			$LAMEmiscStereoModeLookup[6] = 'intensity';
			$LAMEmiscStereoModeLookup[7] = 'other';
		}
	
		return ( isset( $LAMEmiscStereoModeLookup["$StereoModeID"] )? $LAMEmiscStereoModeLookup["$StereoModeID"] : '' );
	}

	function LAMEmiscSourceSampleFrequencyLookup( $SourceSampleFrequencyID ) 
	{
		static $LAMEmiscSourceSampleFrequencyLookup = array();
	
		if ( count( $LAMEmiscSourceSampleFrequencyLookup ) < 1 ) 
		{
			$LAMEmiscSourceSampleFrequencyLookup[0] = '<= 32 kHz';
			$LAMEmiscSourceSampleFrequencyLookup[1] = '44.1 kHz';
			$LAMEmiscSourceSampleFrequencyLookup[2] = '48 kHz';
			$LAMEmiscSourceSampleFrequencyLookup[3] = '> 48kHz';
		}
	
		return ( isset( $LAMEmiscStereoModeLookup["$SourceSampleFrequencyID"] )? $LAMEmiscStereoModeLookup["$SourceSampleFrequencyID"] : '' );
	}

	// http://www.volweb.cz/str/tags.htm
	function getLyrics3Filepointer( &$mp3info, $fd, $offset, $version, $length ) 
	{
		fseek( $fd, $offset, SEEK_END );
		$rawdata = fread( $fd, $length );

		if ( substr( $rawdata, 0, 11 ) == 'LYRICSBEGIN' ) 
		{
			if ( $version == 1 ) 
			{
				if ( substr( $rawdata, strlen( $rawdata ) - 9, 9 ) == 'LYRICSEND' ) 
				{
					$mp3info['lyrics3']['raw']['lyrics3version'] = $version;
					$mp3info['lyrics3']['raw']['lyrics3tagsize'] = $length;
					$mp3info['lyrics3']['raw']['LYR'] = trim( substr( $rawdata, 11, strlen( $rawdata ) - 11 - 9 ) );
					ID3_MP3::lyrics3LyricsTimestampParse( $mp3info );
				} 
				else 
				{
					$mp3info['error'] .= "\n" . '"LYRICSEND" expected at ' . ( ftell( $fd ) - 11 + $length - 9 ) . ' but found "' . substr( $rawdata, strlen( $rawdata ) - 9, 9 ) . '" instead';
				}
			} 
			else if ( $version == 2 ) 
			{
				if ( substr( $rawdata, strlen( $rawdata ) - 9, 9 ) == 'LYRICS200' ) 
				{
					$mp3info['lyrics3']['raw']['lyrics3version'] = $version;
					$mp3info['lyrics3']['raw']['lyrics3tagsize'] = $length;
					$mp3info['lyrics3']['raw']['unparsed'] = substr( $rawdata, 11, strlen( $rawdata ) - 11 - 9 - 6 ); // LYRICSBEGIN + LYRICS200 + LSZ
					$rawdata = $mp3info['lyrics3']['raw']['unparsed'];
				
					while ( strlen( $rawdata ) > 0 ) 
					{
						$fieldname = substr( $rawdata, 0, 3 );
						$fieldsize = (int)substr( $rawdata, 3, 5 );
						$mp3info['lyrics3']['raw']["$fieldname"] = substr( $rawdata, 8, $fieldsize );
						$rawdata = substr( $rawdata, 3 + 5 + $fieldsize );
					}

					if ( isset( $mp3info['lyrics3']['raw']['IND'] ) ) 
					{
						$flagnames = array( 'lyrics', 'timestamps', 'inhibitrandom' );
						
						for ( $i = 0; $i < count( $flagnames ); $i++ )  
						{
							if ( strlen( $mp3info['lyrics3']['raw']['IND'] ) > $i )
								$mp3info['lyrics3']['flags'][$flagnames["$i"]] = ID3::intStringToBool( substr( $mp3info['lyrics3']['raw']['IND'], $i, 1 ) );
						}
					}
				
					$fieldnametranslation = array(
						'ETT' => 'title', 
						'EAR' => 'artist', 
						'EAL' => 'album', 
						'INF' => 'comment', 
						'AUT' => 'author'
					);
				
					foreach ( $fieldnametranslation as $key => $value ) 
					{
						if ( isset( $mp3info['lyrics3']['raw']["$key"] ) )
							$mp3info['lyrics3']["$value"] = $mp3info['lyrics3']['raw']["$key"];
					}
				
					if ( isset($mp3info['lyrics3']['raw']['IMG'] ) ) 
					{
						$imagestrings = explode( chr( 0x0D ) . chr( 0x0A ), $mp3info['lyrics3']['raw']['IMG'] );
					
						foreach ( $imagestrings as $key => $imagestring ) 
						{
							if ( strpos( $imagestring, '||' ) !== false ) 
							{
								$imagearray = explode( '||', $imagestring );
								
								$mp3info['lyrics3']['images']["$key"]['filename']     = $imagearray[0];
								$mp3info['lyrics3']['images']["$key"]['description']  = $imagearray[1];
								$mp3info['lyrics3']['images']["$key"]['timestamp']    = ID3_MP3::lyrics3Timestamp2Seconds( $imagearray[2] );
							}
						}
					}
				
					if ( isset( $mp3info['lyrics3']['raw']['LYR'] ) )
						ID3_MP3::lyrics3LyricsTimestampParse( $mp3info );
				} 
				else 
				{
					$mp3info['error'] .= "\n" . '"LYRICS200" expected at ' . ( ftell( $fd ) - 11 + $length - 9 ) . ' but found "' . substr( $rawdata, strlen( $rawdata ) - 9, 9 ) . '" instead';
				}
			} 
			else 
			{
				$mp3info['error'] .= "\n" . 'Cannot process Lyrics3 version ' . $version . ' (only v1 and v2)';
			}
		} 
		else 
		{
			$mp3info['error'] .= "\n" . '"LYRICSBEGIN" expected at ' . ( ftell( $fd ) - 11 ) . ' but found "' . substr( $rawdata, 0, 11 ) . '" instead';
		}
	}

	function lyrics3Timestamp2Seconds( $rawtimestamp ) 
	{
		if ( ereg( "(\[[0-9]{2}:[0-9]{2}\])", $rawtimestamp ) )
			return ( (int)substr( $rawtimestamp, 1, 2 ) * 60 ) + (int)substr( $rawtimestamp, 4, 2 );
		else
			return null;
	}

	function lyrics3LyricsTimestampParse( &$mp3info ) 
	{
		$lyricsarray = explode( chr( 0x0D ) . chr( 0x0A ), $mp3info['lyrics3']['raw']['LYR'] );
	
		foreach ( $lyricsarray as $key => $lyricline ) 
		{
			$regs = array();
			unset( $thislinetimestamps );
			
			while ( ereg( "(\[[0-9]{2}:[0-9]{2}\])", $lyricline, $regs ) ) 
			{
				$thislinetimestamps[] = ID3_MP3::lyrics3Timestamp2Seconds( $regs[0] );
				$lyricline = str_replace( $regs[0], '', $lyricline );
			}
			
			$notimestamplyricsarray["$key"] = $lyricline;
		
			if ( isset( $thislinetimestamps ) && is_array( $thislinetimestamps ) ) 
			{
				sort( $thislinetimestamps );
				
				foreach ( $thislinetimestamps as $timestampkey => $timestamp ) 
				{
					if ( isset( $mp3info['lyrics3']['synchedlyrics']["$timestamp"] ) ) 
					{
						// timestamps only have a 1-second resolution, it's possible that multiple lines
						// could have the same timestamp, if so, append
						$mp3info['lyrics3']['synchedlyrics']["$timestamp"] .= chr( 0x0D ) . chr( 0x0A ) . $lyricline;
					} 
					else 
					{
						$mp3info['lyrics3']['synchedlyrics']["$timestamp"] = $lyricline;
					}
				}
			}
		}
	
		$mp3info['lyrics3']['unsynchedlyrics'] = implode( chr( 0x0D ) . chr( 0x0A ), $notimestamplyricsarray );
	
		if ( isset( $mp3info['lyrics3']['synchedlyrics'] ) && is_array( $mp3info['lyrics3']['synchedlyrics'] ) )
			ksort( $mp3info['lyrics3']['synchedlyrics'] );
	
		return $mp3info;
	}
} // END OF ID3_MP3

?>
