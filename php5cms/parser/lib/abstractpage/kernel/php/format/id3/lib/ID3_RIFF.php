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
 
class ID3_RIFF extends ID3
{
	function getRIFFHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat']   = 'riff';
		$MP3fileInfo['bitrate_mode'] = 'cbr';

		$offset = 0;
		rewind( $fd );
		$MP3fileInfo['RIFF'] = ID3_RIFF::parseRIFF( $fd, $offset, $MP3fileInfo['filesize'] );
		$streamindex = 0;
	
		if ( !is_array( $MP3fileInfo['RIFF'] ) ) 
		{
			$MP3fileInfo['error'][] = 'Cannot parse RIFF (this is maybe not a RIFF / WAV / AVI file?).';
			unset( $MP3fileInfo['RIFF'] );
			unset( $MP3fileInfo['fileformat'] );
		
			return false;
		}
	
		$arraykeys = array_keys( $MP3fileInfo['RIFF'] );
	
		switch ( $arraykeys[0] ) 
		{
			case 'WAVE':
				$MP3fileInfo['fileformat'] = 'wav';
				
				if ( isset( $MP3fileInfo['RIFF']['WAVE']['fmt '][0]['data'] ) ) 
				{
					$fmtData = $MP3fileInfo['RIFF']['WAVE']['fmt '][0]['data'];
					$MP3fileInfo['RIFF']['raw']['fmt ']['wFormatTag']      = ID3::littleEndianToInt( substr( $fmtData,  0, 2 ) );
					$MP3fileInfo['RIFF']['raw']['fmt ']['nChannels']       = ID3::littleEndianToInt( substr( $fmtData,  2, 2 ) );
					$MP3fileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec']  = ID3::littleEndianToInt( substr( $fmtData,  4, 4 ) );
					$MP3fileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] = ID3::littleEndianToInt( substr( $fmtData,  8, 4 ) );
					$MP3fileInfo['RIFF']['raw']['fmt ']['nBlockAlign']     = ID3::littleEndianToInt( substr( $fmtData, 12, 2 ) );
					$MP3fileInfo['RIFF']['raw']['fmt ']['nBitsPerSample']  = ID3::littleEndianToInt( substr( $fmtData, 14, 2 ) );

					$MP3fileInfo['RIFF']['audio']["$streamindex"]['format']        = ID3_RIFF::RIFFwFormatTagLookup( $MP3fileInfo['RIFF']['raw']['fmt ']['wFormatTag'] );
					$MP3fileInfo['RIFF']['audio']["$streamindex"]['channels']      = $MP3fileInfo['RIFF']['raw']['fmt ']['nChannels'];
					$MP3fileInfo['RIFF']['audio']["$streamindex"]['channelmode']   = ( ( $MP3fileInfo['RIFF']['audio']["$streamindex"]['channels'] == 1 )? 'mono' : 'stereo' );
					$MP3fileInfo['RIFF']['audio']["$streamindex"]['frequency']     = $MP3fileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec'];
					$MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate']       = $MP3fileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] * 8;
					$MP3fileInfo['RIFF']['audio']["$streamindex"]['bitspersample'] = $MP3fileInfo['RIFF']['raw']['fmt ']['nBitsPerSample'];
	
					if ( !isset($MP3fileInfo['frequency'] ) )
						$MP3fileInfo['frequency'] = $MP3fileInfo['RIFF']['audio']["$streamindex"]['frequency'];
			
					if ( !isset( $MP3fileInfo['channels'] ) )
						$MP3fileInfo['channels']  = $MP3fileInfo['RIFF']['audio']["$streamindex"]['channels'];
			
					if ( !isset( $MP3fileInfo['bitrate_audio'] ) && isset( $MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate'] ) && isset( $MP3fileInfo['audiobytes'] ) ) 
					{
							$MP3fileInfo['bitrate_audio']    = $MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate'];
							$MP3fileInfo['playtime_seconds'] = (float)( ( $MP3fileInfo['audiobytes'] * 8 ) / $MP3fileInfo['bitrate_audio'] );
					}
				}
			
				if ( isset( $MP3fileInfo['RIFF']['WAVE']['rgad'][0]['data'] ) ) 
				{
					$rgadData = $MP3fileInfo['RIFF']['WAVE']['rgad'][0]['data'];
					$MP3fileInfo['RIFF']['raw']['rgad']['fPeakAmplitude']      = ID3::littleEndianToFloat( substr( $rgadData, 0, 4 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['nRadioRgAdjust']      = ID3::littleEndianToInt( substr( $rgadData, 4, 2 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['nAudiophileRgAdjust'] = ID3::littleEndianToInt( substr( $rgadData, 6, 2 ) );
	
					$nRadioRgAdjustBitstring      = str_pad( ID3::decToBin( $MP3fileInfo['RIFF']['raw']['rgad']['nRadioRgAdjust']      ), 16, '0', STR_PAD_LEFT );
					$nAudiophileRgAdjustBitstring = str_pad( ID3::decToBin( $MP3fileInfo['RIFF']['raw']['rgad']['nAudiophileRgAdjust'] ), 16, '0', STR_PAD_LEFT );
	
					$MP3fileInfo['RIFF']['raw']['rgad']['radio']['name']       = ID3::binToDec( substr( $nRadioRgAdjustBitstring, 0, 3 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['radio']['originator'] = ID3::binToDec( substr( $nRadioRgAdjustBitstring, 3, 3 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['radio']['signbit']    = ID3::binToDec( substr( $nRadioRgAdjustBitstring, 6, 1 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['radio']['adjustment'] = ID3::binToDec( substr( $nRadioRgAdjustBitstring, 7, 9 ) );
	
					$MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['name']       = ID3::binToDec( substr( $nAudiophileRgAdjustBitstring, 0, 3 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['originator'] = ID3::binToDec( substr( $nAudiophileRgAdjustBitstring, 3, 3 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['signbit']    = ID3::binToDec( substr( $nAudiophileRgAdjustBitstring, 6, 1 ) );
					$MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['adjustment'] = ID3::binToDec( substr( $nAudiophileRgAdjustBitstring, 7, 9 ) );

					$MP3fileInfo['RIFF']['rgad']['peakamplitude'] = $MP3fileInfo['RIFF']['raw']['rgad']['fPeakAmplitude'];
				
					if ( ( $MP3fileInfo['RIFF']['raw']['rgad']['radio']['name'] != 0 ) && ( $MP3fileInfo['RIFF']['raw']['rgad']['radio']['originator'] != 0 ) ) 
					{
						$MP3fileInfo['RIFF']['rgad']['radio']['name']            = ID3::RGADnameLookup( $MP3fileInfo['RIFF']['raw']['rgad']['radio']['name'] );
						$MP3fileInfo['RIFF']['rgad']['radio']['originator']      = ID3::RGADoriginatorLookup( $MP3fileInfo['RIFF']['raw']['rgad']['radio']['originator'] );
						$MP3fileInfo['RIFF']['rgad']['radio']['adjustment']      = ID3::RGADadjustmentLookup( $MP3fileInfo['RIFF']['raw']['rgad']['radio']['adjustment'], $MP3fileInfo['RIFF']['raw']['rgad']['radio']['signbit'] );
					}
				
					if ( ( $MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['name'] != 0 ) && ( $MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['originator'] != 0 ) ) 
					{
						$MP3fileInfo['RIFF']['rgad']['audiophile']['name']       = ID3::RGADnameLookup( $MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['name'] );
						$MP3fileInfo['RIFF']['rgad']['audiophile']['originator'] = ID3::RGADoriginatorLookup( $MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['originator'] );
						$MP3fileInfo['RIFF']['rgad']['audiophile']['adjustment'] = ID3::RGADadjustmentLookup( $MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['adjustment'], $MP3fileInfo['RIFF']['raw']['rgad']['audiophile']['signbit'] );
					}
				}
			
				if ( isset( $MP3fileInfo['RIFF']['WAVE']['fact'][0]['data'] ) ) 
				{
					$MP3fileInfo['RIFF']['raw']['fact']['NumberOfSamples'] = ID3::littleEndianToInt( substr( $MP3fileInfo['RIFF']['WAVE']['fact'][0]['data'], 0, 4 ) );

					if ( isset( $MP3fileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec'] ) && $MP3fileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec'] )
						$MP3fileInfo['playtime_seconds'] = (float)$MP3fileInfo['RIFF']['raw']['fact']['NumberOfSamples'] / $MP3fileInfo['RIFF']['raw']['fmt ']['nSamplesPerSec'];
				
					if ( isset( $MP3fileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] ) && $MP3fileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] ) 
					{
						$MP3fileInfo['audiobytes']    = ID3::castAsInt( round( $MP3fileInfo['playtime_seconds'] * $MP3fileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] ) );
						$MP3fileInfo['bitrate_audio'] = ID3::castAsInt( $MP3fileInfo['RIFF']['raw']['fmt ']['nAvgBytesPerSec'] * 8 );
					}
				}
			
				if ( !isset( $MP3fileInfo['audiobytes'] ) && isset( $MP3fileInfo['RIFF']['WAVE']['data'][0]['size'] ) )
					$MP3fileInfo['audiobytes'] = $MP3fileInfo['RIFF']['WAVE']['data'][0]['size'];
			
				if ( !isset( $MP3fileInfo['bitrate_audio'] ) && isset( $MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate'] ) && isset( $MP3fileInfo['audiobytes'] ) ) 
				{
					$MP3fileInfo['bitrate_audio']    = $MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate'];
					$MP3fileInfo['playtime_seconds'] = (float)( ( $MP3fileInfo['audiobytes'] * 8 ) / $MP3fileInfo['bitrate_audio'] );
				}
			
				break;
		
			case 'AVI ':
				$MP3fileInfo['fileformat'] = 'avi';

				if ( isset( $MP3fileInfo['RIFF']['AVI ']['hdrl']['avih']["$streamindex"]['data'] ) ) 
				{
					$avihData = $MP3fileInfo['RIFF']['AVI ']['hdrl']['avih']["$streamindex"]['data'];
			
					$MP3fileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame']    = ID3::littleEndianToInt( substr( $avihData,  0, 4 ) ); // frame display rate (or 0L)
					$MP3fileInfo['RIFF']['raw']['avih']['dwMaxBytesPerSec']      = ID3::littleEndianToInt( substr( $avihData,  4, 4 ) ); // max. transfer rate
					$MP3fileInfo['RIFF']['raw']['avih']['dwPaddingGranularity']  = ID3::littleEndianToInt( substr( $avihData,  8, 4 ) ); // pad to multiples of this size; normally 2K.
					$MP3fileInfo['RIFF']['raw']['avih']['dwFlags']               = ID3::littleEndianToInt( substr( $avihData, 12, 4 ) ); // the ever-present flags
					$MP3fileInfo['RIFF']['raw']['avih']['dwTotalFrames']         = ID3::littleEndianToInt( substr( $avihData, 16, 4 ) ); // # frames in file
					$MP3fileInfo['RIFF']['raw']['avih']['dwInitialFrames']       = ID3::littleEndianToInt( substr( $avihData, 20, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwStreams']             = ID3::littleEndianToInt( substr( $avihData, 24, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwSuggestedBufferSize'] = ID3::littleEndianToInt( substr( $avihData, 28, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwWidth']               = ID3::littleEndianToInt( substr( $avihData, 32, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwHeight']              = ID3::littleEndianToInt( substr( $avihData, 36, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwScale']               = ID3::littleEndianToInt( substr( $avihData, 40, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwRate']                = ID3::littleEndianToInt( substr( $avihData, 44, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwStart']               = ID3::littleEndianToInt( substr( $avihData, 48, 4 ) );
					$MP3fileInfo['RIFF']['raw']['avih']['dwLength']              = ID3::littleEndianToInt( substr( $avihData, 52, 4 ) );

					$MP3fileInfo['RIFF']['raw']['avih']['flags']['hasindex']     = (bool)( $MP3fileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000010 );
					$MP3fileInfo['RIFF']['raw']['avih']['flags']['mustuseindex'] = (bool)( $MP3fileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000020 );
					$MP3fileInfo['RIFF']['raw']['avih']['flags']['interleaved']  = (bool)( $MP3fileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000100 );
					$MP3fileInfo['RIFF']['raw']['avih']['flags']['trustcktype']  = (bool)( $MP3fileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00000800 );
					$MP3fileInfo['RIFF']['raw']['avih']['flags']['capturedfile'] = (bool)( $MP3fileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00010000 );
					$MP3fileInfo['RIFF']['raw']['avih']['flags']['copyrighted']  = (bool)( $MP3fileInfo['RIFF']['raw']['avih']['dwFlags'] & 0x00020010 );

					$MP3fileInfo['RIFF']['video']["$streamindex"]['frame_width']  = $MP3fileInfo['RIFF']['raw']['avih']['dwWidth'];
					$MP3fileInfo['RIFF']['video']["$streamindex"]['frame_height'] = $MP3fileInfo['RIFF']['raw']['avih']['dwHeight'];
					$MP3fileInfo['RIFF']['video']["$streamindex"]['frame_rate']   = round( 1000000 / $MP3fileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'], 3 );
		
					if ( !isset( $MP3fileInfo['resolution_x'] ) ) 
						$MP3fileInfo['resolution_x'] = $MP3fileInfo['RIFF']['video']["$streamindex"]['frame_width'];
			
					if ( !isset( $MP3fileInfo['resolution_y'] ) )
						$MP3fileInfo['resolution_y'] = $MP3fileInfo['RIFF']['video']["$streamindex"]['frame_height'];
				}
			
				if ( isset( $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'][0]['data'] ) ) 
				{
					if ( is_array( $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'] ) ) 
					{
						for ( $i = 0; $i < count( $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strh'] ); $i++ ) 
						{
							if ( isset( $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strh']["$i"]['data'] ) ) 
							{
								$strhData = $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strh']["$i"]['data'];
							
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['fccType']               = substr( $strhData, 0, 4 );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['fccHandler']            = substr( $strhData, 4, 4 );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwFlags']               = ID3::littleEndianToInt( substr( $strhData,  8, 4 ) ); // Contains AVITF_* flags
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['wPriority']             = ID3::littleEndianToInt( substr( $strhData, 12, 2 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['wLanguage']             = ID3::littleEndianToInt( substr( $strhData, 14, 2 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwInitialFrames']       = ID3::littleEndianToInt( substr( $strhData, 16, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwScale']               = ID3::littleEndianToInt( substr( $strhData, 20, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwRate']                = ID3::littleEndianToInt( substr( $strhData, 24, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwStart']               = ID3::littleEndianToInt( substr( $strhData, 28, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwLength']              = ID3::littleEndianToInt( substr( $strhData, 32, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwSuggestedBufferSize'] = ID3::littleEndianToInt( substr( $strhData, 36, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwQuality']             = ID3::littleEndianToInt( substr( $strhData, 40, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['dwSampleSize']          = ID3::littleEndianToInt( substr( $strhData, 44, 4 ) );
								$MP3fileInfo['RIFF']['raw']['strh']["$i"]['rcFrame']               = ID3::littleEndianToInt( substr( $strhData, 48, 4 ) );

								if ( isset( $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strf']["$i"]['data'] ) ) 
								{
									$strfData = $MP3fileInfo['RIFF']['AVI ']['hdrl']['strl']['strf']["$i"]['data'];
								
									switch ( $MP3fileInfo['RIFF']['raw']['strh']["$i"]['fccType'] ) 
									{
										case 'auds':
											if ( isset( $MP3fileInfo['RIFF']['audio'] ) && is_array( $MP3fileInfo['RIFF']['audio'] ) ) 
												$streamindex = count( $MP3fileInfo['RIFF']['audio'] );
										
											$MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['wFormatTag']      = ID3::littleEndianToInt( substr( $strfData,  0, 2 ) );
											$MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nChannels']       = ID3::littleEndianToInt( substr( $strfData,  2, 2 ) );
											$MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nSamplesPerSec']  = ID3::littleEndianToInt( substr( $strfData,  4, 4 ) );
											$MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nAvgBytesPerSec'] = ID3::littleEndianToInt( substr( $strfData,  8, 4 ) );
											$MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nBlockAlign']     = ID3::littleEndianToInt( substr( $strfData, 12, 2 ) );
											$MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nBitsPerSample']  = ID3::littleEndianToInt( substr( $strfData, 14, 2 ) );

											$MP3fileInfo['RIFF']['audio']["$streamindex"]['format']        = ID3_RIFF::RIFFwFormatTagLookup( $MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['wFormatTag'] );
											$MP3fileInfo['RIFF']['audio']["$streamindex"]['channels']      = $MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nChannels'];
											$MP3fileInfo['RIFF']['audio']["$streamindex"]['channelmode']   = ( ( $MP3fileInfo['RIFF']['audio']["$streamindex"]['channels'] == 1 )? 'mono' : 'stereo' );
											$MP3fileInfo['RIFF']['audio']["$streamindex"]['frequency']     = $MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nSamplesPerSec'];
											$MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate']       = $MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nAvgBytesPerSec'] * 8;
											$MP3fileInfo['RIFF']['audio']["$streamindex"]['bitspersample'] = $MP3fileInfo['RIFF']['raw']['strf']['auds']["$streamindex"]['nBitsPerSample'];
	
											if ( !isset( $MP3fileInfo['frequency'] ) )
												$MP3fileInfo['frequency'] = $MP3fileInfo['RIFF']['audio']["$streamindex"]['frequency'];
										
											if ( !isset( $MP3fileInfo['channels'] ) )
												$MP3fileInfo['channels'] = $MP3fileInfo['RIFF']['audio']["$streamindex"]['channels'];
										
											if ( !isset( $MP3fileInfo['bitrate_audio'] ) && isset( $MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate'] ) )
												$MP3fileInfo['bitrate_audio'] = $MP3fileInfo['RIFF']['audio']["$streamindex"]['bitrate'];
										
											break;

										case 'vids':
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biSize']          = ID3::littleEndianToInt( substr( $strfData,  0, 4 ) ); // number of bytes required by the BITMAPINFOHEADER structure
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biWidth']         = ID3::littleEndianToInt( substr( $strfData,  4, 4 ) ); // width of the bitmap in pixels
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biHeight']        = ID3::littleEndianToInt( substr( $strfData,  8, 4 ) ); // height of the bitmap in pixels. If biHeight is positive, the bitmap is a "bottom-up" DIB and its origin is the lower left corner. If biHeight is negative, the bitmap is a "top-down" DIB and its origin is the upper left corner
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biPlanes']        = ID3::littleEndianToInt( substr( $strfData, 12, 2 ) ); // number of color planes on the target device. In most cases this value must be set to 1
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biBitCount']      = ID3::littleEndianToInt( substr( $strfData, 14, 2 ) ); // Specifies the number of bits per pixels
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['fourcc']          = substr( $strfData, 16, 4 );
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biSizeImage']     = ID3::littleEndianToInt( substr( $strfData, 20, 4 ) ); // size of the bitmap data section of the image (the actual pixel data, excluding BITMAPINFOHEADER and RGBQUAD structures)
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biXPelsPerMeter'] = ID3::littleEndianToInt( substr( $strfData, 24, 4 ) ); // horizontal resolution, in pixels per metre, of the target device
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biYPelsPerMeter'] = ID3::littleEndianToInt( substr( $strfData, 28, 4 ) ); // vertical resolution, in pixels per metre, of the target device
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biClrUsed']       = ID3::littleEndianToInt( substr( $strfData, 32, 4 ) ); // actual number of color indices in the color table used by the bitmap. If this value is zero, the bitmap uses the maximum number of colors corresponding to the value of the biBitCount member for the compression mode specified by biCompression
											$MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['biClrImportant']  = ID3::littleEndianToInt( substr( $strfData, 36, 4 ) ); // number of color indices that are considered important for displaying the bitmap. If this value is zero, all colors are important

											$MP3fileInfo['RIFF']['video']["$streamindex"]['codec'] = ID3_RIFF::RIFFfourccLookup( $MP3fileInfo['RIFF']['raw']['strh']["$i"]['fccHandler'] );

											if ( !$MP3fileInfo['RIFF']['video']["$streamindex"]['codec'] && ID3_RIFF::RIFFfourccLookup( $MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['fourcc'] ) )
												ID3_RIFF::RIFFfourccLookup( $MP3fileInfo['RIFF']['raw']['strf']['vids']["$streamindex"]['fourcc'] );
										
											break;
									}
								}
							}
						}
					}
				}
			
				break;
			
			default:
				unset( $MP3fileInfo['fileformat'] );
				break;
		}

		if ( isset( $MP3fileInfo['RIFF']['WAVE']['INFO'] ) && is_array( $MP3fileInfo['RIFF']['WAVE']['INFO'] ) ) 
		{
			$MP3fileInfo['RIFF']['title']              = trim( substr( $MP3fileInfo['RIFF']['WAVE']['INFO']['DISP'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['DISP'] ) - 1]['data'], 4 ) );
			$MP3fileInfo['RIFF']['artist']             = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['IART'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['IART']) - 1]['data'] );
			$MP3fileInfo['RIFF']['genre']              = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['IGNR'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['IGNR']) - 1]['data'] );
			$MP3fileInfo['RIFF']['comment']            = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['ICMT'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['ICMT']) - 1]['data'] );
			$MP3fileInfo['RIFF']['copyright']          = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['ICOP'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['ICOP']) - 1]['data'] );
			$MP3fileInfo['RIFF']['engineers']          = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['IENG'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['IENG']) - 1]['data'] );
			$MP3fileInfo['RIFF']['keywords']           = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['IKEY'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['IKEY']) - 1]['data'] );
			$MP3fileInfo['RIFF']['originalmedium']     = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['IMED'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['IMED']) - 1]['data'] );
			$MP3fileInfo['RIFF']['name']               = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['INAM'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['INAM']) - 1]['data'] );
			$MP3fileInfo['RIFF']['sourcesupplier']     = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['ISRC'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['ISRC']) - 1]['data'] );
			$MP3fileInfo['RIFF']['digitizer']          = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['ITCH'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['ITCH']) - 1]['data'] );
			$MP3fileInfo['RIFF']['subject']            = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['ISBJ'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['ISBJ']) - 1]['data'] );
			$MP3fileInfo['RIFF']['digitizationsource'] = trim( $MP3fileInfo['RIFF']['WAVE']['INFO']['ISRF'][count( $MP3fileInfo['RIFF']['WAVE']['INFO']['ISRF']) - 1]['data'] );
		}
		
		foreach ( $MP3fileInfo['RIFF'] as $key => $value ) 
		{
			if ( !is_array( $value ) && !$value )
				unset( $MP3fileInfo['RIFF']["$key"] );
		}

		if ( !isset( $MP3fileInfo['playtime_seconds'] ) && isset( $MP3fileInfo['RIFF']['raw']['avih']['dwTotalFrames'] ) && isset( $MP3fileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'] ) )
			$MP3fileInfo['playtime_seconds'] = $MP3fileInfo['RIFF']['raw']['avih']['dwTotalFrames'] * ( $MP3fileInfo['RIFF']['raw']['avih']['dwMicroSecPerFrame'] / 1000000 );
	
		if ( isset( $MP3fileInfo['RIFF']['video'] ) && isset( $MP3fileInfo['bitrate_audio'] ) && ( $MP3fileInfo['bitrate_audio'] > 0 ) ) 
		{
			$MP3fileInfo['bitrate_video'] = ( ( $MP3fileInfo['filesize'] / $MP3fileInfo['playtime_seconds'] ) * 8 ) - $MP3fileInfo['bitrate_audio'];
		
			if ( $MP3fileInfo['bitrate_video'] <= 0 )
				unset( $MP3fileInfo['bitrate_video'] );
		}

		return true;
	}

	function parseRIFF( &$fd, &$offset, $maxoffset ) 
	{
		fseek( $fd, $offset, SEEK_SET );

		while ( $chunkname = fread( $fd, 4 ) ) 
		{
			if ( $chunkname{0} === chr( 0 ) ) 
			{
				// a hack I don't understand - some frames, including (but maybe not limited to)
				// strn, IART, IENG, IGNR, IKEY, IMED, ISRC, ITCH, ISBJ, ISRF
				// are specified as being one byte shorter than is acutally the case to the next
				// chunk, and that extra space is padded with a null. This hack simply detects that
				// the previous chunk had been shorted, and reads the next byte in and discards the null.
				$chunkname = substr( $chunkname, 1, 3 ) . fread( $fd, 1 );
			}
		
			$chunksize = ID3::littleEndianToInt( fread( $fd, 4 ) );
		
			if ( $chunksize <= 0 ) 
			{
				// just in case something goes wrong :)
				break;
			}
		
			switch ( $chunkname ) 
			{
				case 'RIFF':
		
				case 'SDSS': // simply a renamed RIFF-WAVE format, identical except for the 1st 4 chars, used by SmartSound QuickTracks (www.smartsound.com)
	
				case 'LIST':
					$listname = fread( $fd, 4 );
					$offset   = ftell( $fd );
					
					if ( $offset >= $maxoffset )
						$RIFFchunk = array_merge_recursive( $RIFFchunk, ID3_RIFF::parseRIFF( $fd, $offset, $offset + $chunksize ) );
					else
						$RIFFchunk["$listname"] = ID3_RIFF::parseRIFF( $fd, $offset, $offset + $chunksize );
				
					$offset = ftell( $fd ) + $chunksize;
					fseek( $fd, $offset, SEEK_CUR );
					break;
			
				default:
					// skip over
					if ( isset( $RIFFchunk["$chunkname"] ) && is_array( $RIFFchunk["$chunkname"] ) )
						$thisindex = count( $RIFFchunk["$chunkname"] );
					else 
						$thisindex = 0;
				
					$RIFFchunk["$chunkname"]["$thisindex"]['size'] = $chunksize;
				
					if ( $chunksize <= 128 )
						$RIFFchunk["$chunkname"]["$thisindex"]['data'] = fread( $fd, $chunksize );
					else
						fseek( $fd, $chunksize, SEEK_CUR );
				
					$offset = ftell( $fd );
					break;
			}
		}
	
		if ( isset( $RIFFchunk ) )
			return $RIFFchunk;
		else
			return false;
	}

	function RIFFwFormatTagLookup( $wFormatTag ) 
	{
		static $RIFFwFormatTagLookup = array();
	
		if ( count( $RIFFwFormatTagLookup ) < 1 ) 
		{
			$RIFFwFormatTagLookup[0x0000] = 'Unknown';
			$RIFFwFormatTagLookup[0x0001] = 'Microsoft Pulse Code Modulation (PCM)';
			$RIFFwFormatTagLookup[0x0002] = 'Microsoft ADPCM';
			$RIFFwFormatTagLookup[0x0005] = 'IBM CVSD';
			$RIFFwFormatTagLookup[0x0006] = 'Microsoft A-Law';
			$RIFFwFormatTagLookup[0x0007] = 'Microsoft mu-Law';
			$RIFFwFormatTagLookup[0x0010] = 'OKI ADPCM';
			$RIFFwFormatTagLookup[0x0011] = 'Intel DVI/IMA ADPCM';
			$RIFFwFormatTagLookup[0x0012] = 'Videologic Mediaspace ADPCM';
			$RIFFwFormatTagLookup[0x0013] = 'Sierra Semiconductor ADPCM';
			$RIFFwFormatTagLookup[0x0014] = 'Antex Electronics G723 ADPCM';
			$RIFFwFormatTagLookup[0x0015] = 'DSP Solutions DigiSTD';
			$RIFFwFormatTagLookup[0x0016] = 'DSP Solutions DigiFIX';
			$RIFFwFormatTagLookup[0x0017] = 'Dialogic OKI ADPCM';
			$RIFFwFormatTagLookup[0x0020] = 'Yamaha ADPCM';
			$RIFFwFormatTagLookup[0x0021] = 'Speech Compression Sonarc';
			$RIFFwFormatTagLookup[0x0022] = 'DSP Group Truespeech';
			$RIFFwFormatTagLookup[0x0023] = 'Echo Speech EchoSC1';
			$RIFFwFormatTagLookup[0x0024] = 'Audiofile AF36';
			$RIFFwFormatTagLookup[0x0025] = 'Audio Processing Technology APTX';
			$RIFFwFormatTagLookup[0x0026] = 'Audiofile AF10';
			$RIFFwFormatTagLookup[0x0030] = 'Dolby AC2';
			$RIFFwFormatTagLookup[0x0031] = 'Microsoft GSM 6.10';
			$RIFFwFormatTagLookup[0x0033] = 'Antex Electronics ADPCME';
			$RIFFwFormatTagLookup[0x0034] = 'Control Resources VQLPC';
			$RIFFwFormatTagLookup[0x0035] = 'DSP Solutions DigiREAL';
			$RIFFwFormatTagLookup[0x0036] = 'DSP Solutions DigiADPCM';
			$RIFFwFormatTagLookup[0x0037] = 'Control Resources CR10';
			$RIFFwFormatTagLookup[0x0038] = 'Natural MicroSystems VBXADPCM';
			$RIFFwFormatTagLookup[0x0039] = 'Crystal Semiconductor IMA ADPCM';
			$RIFFwFormatTagLookup[0x0040] = 'Antex Electronics GS721 ADPCM';
			$RIFFwFormatTagLookup[0x0050] = 'Microsoft MPEG';
			$RIFFwFormatTagLookup[0x0055] = 'Microsoft ACM: LAME MP3 encoder (ACM)';
			$RIFFwFormatTagLookup[0x0101] = 'IBM mu-law';
			$RIFFwFormatTagLookup[0x0102] = 'IBM A-law';
			$RIFFwFormatTagLookup[0x0103] = 'IBM AVC Adaptive Differential Pulse Code Modulation (ADPCM)';
			$RIFFwFormatTagLookup[0x0161] = 'Microsoft ACM: DivX ;-) Audio';
			$RIFFwFormatTagLookup[0x0200] = 'Creative Labs ADPCM';
			$RIFFwFormatTagLookup[0x0202] = 'Creative Labs Fastspeech8';
			$RIFFwFormatTagLookup[0x0203] = 'Creative Labs Fastspeech10';
			$RIFFwFormatTagLookup[0x0300] = 'Fujitsu FM Towns Snd';
			$RIFFwFormatTagLookup[0x1000] = 'Olivetti GSM';
			$RIFFwFormatTagLookup[0x1001] = 'Olivetti ADPCM';
			$RIFFwFormatTagLookup[0x1002] = 'Olivetti CELP';
			$RIFFwFormatTagLookup[0x1003] = 'Olivetti SBC';
			$RIFFwFormatTagLookup[0x1004] = 'Olivetti OPR';
			$RIFFwFormatTagLookup[0xFFFF] = 'development';
		}

		return ( isset( $RIFFwFormatTagLookup["$wFormatTag"] )? $RIFFwFormatTagLookup["$wFormatTag"] : '' );
	}

	function RIFFfourccLookup( $fourcc ) 
	{
		static $RIFFfourccLookup = array();
	
		if ( count( $RIFFfourccLookup ) < 1 ) 
		{
			$RIFFfourccLookup['3IV1'] = '3ivx v1';
			$RIFFfourccLookup['3IV2'] = '3ivx v2';
			$RIFFfourccLookup['AASC'] = 'Autodesk Animator';
			$RIFFfourccLookup['ABYR'] = 'Kensington ?ABYR?';
			$RIFFfourccLookup['AEMI'] = 'Array VideoONE MPEG1-I Capture';
			$RIFFfourccLookup['AFLC'] = 'Autodesk Animator FLC';
			$RIFFfourccLookup['AFLI'] = 'Autodesk Animator FLI';
			$RIFFfourccLookup['AMPG'] = 'Array VideoONE MPEG';
			$RIFFfourccLookup['ANIM'] = 'Intel RDX (ANIM)';
			$RIFFfourccLookup['AP41'] = 'AngelPotion Definitive';
			$RIFFfourccLookup['ASV1'] = 'Asus Video v1';
			$RIFFfourccLookup['ASV2'] = 'Asus Video v2';
			$RIFFfourccLookup['ASVX'] = 'Asus Video 2.0 (audio)';
			$RIFFfourccLookup['AUR2'] = 'Aura 2 Codec - YUV 4:2:2';
			$RIFFfourccLookup['AURA'] = 'Aura 1 Codec - YUV 4:1:1';
			$RIFFfourccLookup['BINK'] = 'RAD Game Tools Bink Video';
			$RIFFfourccLookup['BT20'] = 'Conexant Prosumer Video';
			$RIFFfourccLookup['BTCV'] = 'Conexant Composite Video Codec';
			$RIFFfourccLookup['BW10'] = 'Data Translation Broadway MPEG Capture';
			$RIFFfourccLookup['CC12'] = 'Intel YUV12';
			$RIFFfourccLookup['CDVC'] = 'Canopus DV';
			$RIFFfourccLookup['CFCC'] = 'Digital Processing Systems DPS Perception';
			$RIFFfourccLookup['CGDI'] = 'Microsoft Office 97 Camcorder Video';
			$RIFFfourccLookup['CHAM'] = 'Winnov Caviara Champagne';
			$RIFFfourccLookup['CJPG'] = 'Creative WebCam JPEG';
			$RIFFfourccLookup['CPLA'] = 'Weitek YUV 4:2:0';
			$RIFFfourccLookup['CRAM'] = 'Microsoft Video 1 (CRAM)';
			$RIFFfourccLookup['CVID'] = 'Radius Cinepak';
			$RIFFfourccLookup['CWLT'] = '?CWLT?';
			$RIFFfourccLookup['CYUV'] = 'Creative YUV';
			$RIFFfourccLookup['CYUY'] = 'ATI YUV';
			$RIFFfourccLookup['DIV3'] = 'DivX v3 MPEG-4 Low-Motion';
			$RIFFfourccLookup['DIV4'] = 'DivX v3 MPEG-4 Fast-Motion';
			$RIFFfourccLookup['DIV5'] = '?DIV5?';
			$RIFFfourccLookup['DIVX'] = 'DivX v4.0+';
			$RIFFfourccLookup['divx'] = 'DivX';
			$RIFFfourccLookup['DMB1'] = 'Matrox Rainbow Runner hardware MJPEG';
			$RIFFfourccLookup['DMB2'] = 'Paradigm MJPEG';
			$RIFFfourccLookup['DSVD'] = '?DSVD?';
			$RIFFfourccLookup['DUCK'] = 'Duck TrueMotion S';
			$RIFFfourccLookup['DVAN'] = '?DVAN?';
			$RIFFfourccLookup['DVE2'] = 'InSoft DVE-2 Videoconferencing';
			$RIFFfourccLookup['DVSD'] = 'miroVideo DV300 software DV';
			$RIFFfourccLookup['dvsd'] = 'Pinnacle miroVideo DV300 software DV';
			$RIFFfourccLookup['DVX1'] = 'DVX1000SP Video Decoder';
			$RIFFfourccLookup['DVX2'] = 'DVX2000S Video Decoder';
			$RIFFfourccLookup['DVX3'] = 'DVX3000S Video Decoder';
			$RIFFfourccLookup['DXT1'] = 'Microsoft DirectX Compressed Texture (DXT1)';
			$RIFFfourccLookup['DXT2'] = 'Microsoft DirectX Compressed Texture (DXT2)';
			$RIFFfourccLookup['DXT3'] = 'Microsoft DirectX Compressed Texture (DXT3)';
			$RIFFfourccLookup['DXT4'] = 'Microsoft DirectX Compressed Texture (DXT4)';
			$RIFFfourccLookup['DXT5'] = 'Microsoft DirectX Compressed Texture (DXT5)';
			$RIFFfourccLookup['DXTC'] = 'Microsoft DirectX Compressed Texture (DXTC)';
			$RIFFfourccLookup['EKQ0'] = 'Elsa ?EKQ0?';
			$RIFFfourccLookup['ELK0'] = 'Elsa ?ELK0?';
			$RIFFfourccLookup['ESCP'] = 'Eidos Escape';
			$RIFFfourccLookup['ETV1'] = 'eTreppid Video ETV1';
			$RIFFfourccLookup['ETV2'] = 'eTreppid Video ETV2';
			$RIFFfourccLookup['ETVC'] = 'eTreppid Video ETVC';
			$RIFFfourccLookup['FLJP'] = 'D-Vision Field Encoded Motion JPEG';
			$RIFFfourccLookup['FRWA'] = 'SoftLab-Nsk Forward Motion JPEG w/ alpha channel';
			$RIFFfourccLookup['FRWD'] = 'SoftLab-Nsk Forward Motion JPEG';
			$RIFFfourccLookup['GLZW'] = 'Motion LZW (gabest@freemail.hu)';
			$RIFFfourccLookup['GPEG'] = 'Motion JPEG (gabest@freemail.hu)';
			$RIFFfourccLookup['GWLT'] = 'Microsoft ?GWLT?';
			$RIFFfourccLookup['H260'] = 'Intel ITU H.260 Videoconferencing';
			$RIFFfourccLookup['H261'] = 'Intel ITU H.261 Videoconferencing';
			$RIFFfourccLookup['H262'] = 'Intel ITU H.262 Videoconferencing';
			$RIFFfourccLookup['H263'] = 'Intel ITU H.263 Videoconferencing';
			$RIFFfourccLookup['H264'] = 'Intel ITU H.264 Videoconferencing';
			$RIFFfourccLookup['H265'] = 'Intel ITU H.265 Videoconferencing';
			$RIFFfourccLookup['H266'] = 'Intel ITU H.266 Videoconferencing';
			$RIFFfourccLookup['H267'] = 'Intel ITU H.267 Videoconferencing';
			$RIFFfourccLookup['H268'] = 'Intel ITU H.268 Videoconferencing';
			$RIFFfourccLookup['H269'] = 'Intel ITU H.269 Videoconferencing';
			$RIFFfourccLookup['HFYU'] = 'Huffman Lossless Codec';
			$RIFFfourccLookup['HMCR'] = 'Rendition Motion Compensation Format (HMCR)';
			$RIFFfourccLookup['HMRR'] = 'Rendition Motion Compensation Format (HMRR)';
			$RIFFfourccLookup['i263'] = 'Intel ITU H.263 Videoconferencing (i263)';
			$RIFFfourccLookup['IAN '] = 'Intel Indeo 4 Codec';
			$RIFFfourccLookup['ICLB'] = 'InSoft CellB Videoconferencing';
			$RIFFfourccLookup['IGOR'] = 'Power DVD';
			$RIFFfourccLookup['IJPG'] = 'Intergraph JPEG';
			$RIFFfourccLookup['ILVC'] = 'Intel Layered Video';
			$RIFFfourccLookup['ILVR'] = 'ITU H.263+ Codec';
			$RIFFfourccLookup['IPDV'] = 'I-O Data Device Giga AVI DV Codec';
			$RIFFfourccLookup['IR21'] = 'Intel Indeo 2.1';
			$RIFFfourccLookup['IV30'] = 'Ligos Indeo 3.0';
			$RIFFfourccLookup['IV31'] = 'Ligos Indeo 3.1';
			$RIFFfourccLookup['IV32'] = 'Ligos Indeo 3.2';
			$RIFFfourccLookup['IV33'] = 'Ligos Indeo 3.3';
			$RIFFfourccLookup['IV34'] = 'Ligos Indeo 3.4';
			$RIFFfourccLookup['IV35'] = 'Ligos Indeo 3.5';
			$RIFFfourccLookup['IV36'] = 'Ligos Indeo 3.6';
			$RIFFfourccLookup['IV37'] = 'Ligos Indeo 3.7';
			$RIFFfourccLookup['IV38'] = 'Ligos Indeo 3.8';
			$RIFFfourccLookup['IV39'] = 'Ligos Indeo 3.9';
			$RIFFfourccLookup['IV40'] = 'Ligos Indeo Interactive 4.0';
			$RIFFfourccLookup['IV41'] = 'Ligos Indeo Interactive 4.1';
			$RIFFfourccLookup['IV42'] = 'Ligos Indeo Interactive 4.2';
			$RIFFfourccLookup['IV43'] = 'Ligos Indeo Interactive 4.3';
			$RIFFfourccLookup['IV44'] = 'Ligos Indeo Interactive 4.4';
			$RIFFfourccLookup['IV45'] = 'Ligos Indeo Interactive 4.5';
			$RIFFfourccLookup['IV46'] = 'Ligos Indeo Interactive 4.6';
			$RIFFfourccLookup['IV47'] = 'Ligos Indeo Interactive 4.7';
			$RIFFfourccLookup['IV48'] = 'Ligos Indeo Interactive 4.8';
			$RIFFfourccLookup['IV49'] = 'Ligos Indeo Interactive 4.9';
			$RIFFfourccLookup['IV50'] = 'Ligos Indeo Interactive 5.0';
			$RIFFfourccLookup['JBYR'] = 'Kensington ?JBYR?';
			$RIFFfourccLookup['JPGL'] = 'Webcam JPEG Light?';
			$RIFFfourccLookup['KMVC'] = 'Karl Mortons Video Codec';
			$RIFFfourccLookup['LEAD'] = 'LEAD Video Codec';
			$RIFFfourccLookup['Ljpg'] = 'LEAD MJPEG Codec';
			$RIFFfourccLookup['M261'] = 'Microsoft H.261';
			$RIFFfourccLookup['M263'] = 'Microsoft H.263';
			$RIFFfourccLookup['M4S2'] = 'Microsoft MPEG-4 (M4S2)';
			$RIFFfourccLookup['m4s2'] = 'Microsoft MPEG-4 (m4s2)';
			$RIFFfourccLookup['MC12'] = 'ATI Motion Compensation Format (MC12)';
			$RIFFfourccLookup['MCAM'] = 'ATI Motion Compensation Format (MCAM)';
			$RIFFfourccLookup['mJPG'] = 'IBM Motion JPEG w/ Huffman Tables';
			$RIFFfourccLookup['MJPG'] = 'Motion JPEG';
			$RIFFfourccLookup['MP42'] = 'Microsoft MPEG-4 (low-motion)';
			$RIFFfourccLookup['MP43'] = 'Microsoft MPEG-4 (fast-motion)';
			$RIFFfourccLookup['MP4S'] = 'Microsoft MPEG-4 (MP4S)';
			$RIFFfourccLookup['mp4s'] = 'Microsoft MPEG-4 (mp4s)';
			$RIFFfourccLookup['MPEG'] = 'MPEG-1';
			$RIFFfourccLookup['MPG4'] = 'Microsoft MPEG-4 Video High Speed Compressor';
			$RIFFfourccLookup['MPGI'] = 'Sigma Designs MPEG';
			$RIFFfourccLookup['MRCA'] = 'FAST Multimedia Mrcodec';
			$RIFFfourccLookup['MRLE'] = 'Microsoft RLE';
			$RIFFfourccLookup['MSVC'] = 'Microsoft Video 1 (MSVC)';
			$RIFFfourccLookup['MTX1'] = 'Matrox ?MTX1?';
			$RIFFfourccLookup['MTX2'] = 'Matrox ?MTX2?';
			$RIFFfourccLookup['MTX3'] = 'Matrox ?MTX3?';
			$RIFFfourccLookup['MTX4'] = 'Matrox ?MTX4?';
			$RIFFfourccLookup['MTX5'] = 'Matrox ?MTX5?';
			$RIFFfourccLookup['MTX6'] = 'Matrox ?MTX6?';
			$RIFFfourccLookup['MTX7'] = 'Matrox ?MTX7?';
			$RIFFfourccLookup['MTX8'] = 'Matrox ?MTX8?';
			$RIFFfourccLookup['MTX9'] = 'Matrox ?MTX9?';
			$RIFFfourccLookup['MV12'] = '?MV12?';
			$RIFFfourccLookup['MWV1'] = 'Aware Motion Wavelets';
			$RIFFfourccLookup['nAVI'] = '?nAVI?';
			$RIFFfourccLookup['NTN1'] = 'Nogatech Video Compression 1';
			$RIFFfourccLookup['NVS0'] = 'nVidia GeForce Texture (NVS0)';
			$RIFFfourccLookup['NVS1'] = 'nVidia GeForce Texture (NVS1)';
			$RIFFfourccLookup['NVS2'] = 'nVidia GeForce Texture (NVS2)';
			$RIFFfourccLookup['NVS3'] = 'nVidia GeForce Texture (NVS3)';
			$RIFFfourccLookup['NVS4'] = 'nVidia GeForce Texture (NVS4)';
			$RIFFfourccLookup['NVS5'] = 'nVidia GeForce Texture (NVS5)';
			$RIFFfourccLookup['NVT0'] = 'nVidia GeForce Texture (NVT0)';
			$RIFFfourccLookup['NVT1'] = 'nVidia GeForce Texture (NVT1)';
			$RIFFfourccLookup['NVT2'] = 'nVidia GeForce Texture (NVT2)';
			$RIFFfourccLookup['NVT3'] = 'nVidia GeForce Texture (NVT3)';
			$RIFFfourccLookup['NVT4'] = 'nVidia GeForce Texture (NVT4)';
			$RIFFfourccLookup['NVT5'] = 'nVidia GeForce Texture (NVT5)';
			$RIFFfourccLookup['PDVC'] = 'I-O Data Device Digital Video Capture DV codec';
			$RIFFfourccLookup['PGVV'] = 'Radius Video Vision';
			$RIFFfourccLookup['PIM1'] = 'Pegasus Imaging ?PIM1?';
			$RIFFfourccLookup['PIM2'] = 'Pegasus Imaging ?PIM2?';
			$RIFFfourccLookup['PIMJ'] = 'Pegasus Imaging Lossless JPEG';
			$RIFFfourccLookup['PVEZ'] = 'Horizons Technology PowerEZ';
			$RIFFfourccLookup['PVMM'] = 'PacketVideo Corporation MPEG-4';
			$RIFFfourccLookup['PVW2'] = 'Pegasus Imaging Wavelet Compression';
			$RIFFfourccLookup['QPEG'] = 'Q-Team QPEG 1.0';
			$RIFFfourccLookup['qpeq'] = 'Q-Team QPEG 1.1';
			$RIFFfourccLookup['RGBT'] = 'Computer Concepts 32-bit support';
			$RIFFfourccLookup['RLE '] = 'Microsoft Run Length Encoder';
			$RIFFfourccLookup['RT21'] = 'Intel Real Time Video 2.1';
			$RIFFfourccLookup['rv20'] = 'RealVideo G2';
			$RIFFfourccLookup['rv30'] = 'RealVideo 8';
			$RIFFfourccLookup['RVX '] = 'Intel RDX (RVX )';
			$RIFFfourccLookup['s422'] = 'Tekram VideoCap C210 YUV 4:2:2';
			$RIFFfourccLookup['SDCC'] = 'Sun Communication Digital Camera Codec';
			$RIFFfourccLookup['SFMC'] = 'CrystalNet Surface Fitting Method';
			$RIFFfourccLookup['SMSC'] = 'Radius ?SMSC?';
			$RIFFfourccLookup['SMSD'] = 'Radius ?SMSD?';
			$RIFFfourccLookup['smsv'] = 'WorldConnect Wavelet Video';
			$RIFFfourccLookup['SPIG'] = 'Radius Spigot';
			$RIFFfourccLookup['SQZ2'] = 'Microsoft VXTreme Video Codec V2';
			$RIFFfourccLookup['STVA'] = 'ST CMOS Imager Data (Bayer)';
			$RIFFfourccLookup['STVB'] = 'ST CMOS Imager Data (Nudged Bayer)';
			$RIFFfourccLookup['STVC'] = 'ST CMOS Imager Data (Bunched)';
			$RIFFfourccLookup['STVX'] = 'ST CMOS Imager Data (Extended CODEC Data Format)';
			$RIFFfourccLookup['STVY'] = 'ST CMOS Imager Data (Extended CODEC Data Format with Correction Data)';
			$RIFFfourccLookup['SV10'] = 'Sorenson Video R1';
			$RIFFfourccLookup['SVQ1'] = 'Sorenson Video';
			$RIFFfourccLookup['TLMS'] = 'TeraLogic Motion Intraframe Codec (TLMS)';
			$RIFFfourccLookup['TLST'] = 'TeraLogic Motion Intraframe Codec (TLST)';
			$RIFFfourccLookup['TM20'] = 'Duck TrueMotion 2.0';
			$RIFFfourccLookup['TM2X'] = 'Duck TrueMotion 2X';
			$RIFFfourccLookup['TMIC'] = 'TeraLogic Motion Intraframe Codec (TMIC)';
			$RIFFfourccLookup['TMOT'] = 'Horizons Technology TrueMotion S';
			$RIFFfourccLookup['TR20'] = 'Duck TrueMotion RealTime 2.0';
			$RIFFfourccLookup['TSCC'] = 'TechSmith Screen Capture Codec';
			$RIFFfourccLookup['TV10'] = 'Tecomac Low-Bit Rate Codec';
			$RIFFfourccLookup['TY0N'] = 'Trident ?TY0N?';
			$RIFFfourccLookup['TY2C'] = 'Trident ?TY2C?';
			$RIFFfourccLookup['TY2N'] = 'Trident ?TY2N?';
			$RIFFfourccLookup['UCOD'] = 'eMajix.com ClearVideo';
			$RIFFfourccLookup['ULTI'] = 'IBM Ultimotion';
			$RIFFfourccLookup['V261'] = 'Lucent VX2000S';
			$RIFFfourccLookup['VCR1'] = 'ATI Video Codec 1';
			$RIFFfourccLookup['VCR2'] = 'ATI Video Codec 2';
			$RIFFfourccLookup['VDOM'] = 'VDOnet VDOWave';
			$RIFFfourccLookup['VDOW'] = 'VDOnet VDOLive (H.263)';
			$RIFFfourccLookup['VDTZ'] = 'Darim Vison VideoTizer YUV';
			$RIFFfourccLookup['VGPX'] = 'VGPixel Codec';
			$RIFFfourccLookup['VIDS'] = 'Vitec Multimedia YUV 4:2:2 CCIR 601 for V422';
			$RIFFfourccLookup['VIFP'] = '?VIFP?';
			$RIFFfourccLookup['VIVO'] = 'Vivo H.263 v2.00';
			$RIFFfourccLookup['VIXL'] = 'Miro Video XL';
			$RIFFfourccLookup['VLV1'] = 'VideoLogic ?VLV1?';
			$RIFFfourccLookup['VP30'] = 'On2 VP3.0';
			$RIFFfourccLookup['VP31'] = 'On2 VP3.1';
			$RIFFfourccLookup['VX1K'] = 'VX1000S Video Codec';
			$RIFFfourccLookup['VX2K'] = 'VX2000S Video Codec';
			$RIFFfourccLookup['VXSP'] = 'VX1000SP Video Codec';
			$RIFFfourccLookup['WBVC'] = 'Winbond W9960';
			$RIFFfourccLookup['WHAM'] = 'Microsoft Video 1 (WHAM)';
			$RIFFfourccLookup['WINX'] = 'Winnov Software Compression';
			$RIFFfourccLookup['WJPG'] = 'AverMedia Winbond JPEG';
			$RIFFfourccLookup['WMV1'] = 'Windows Media Video 7';
			$RIFFfourccLookup['WMV2'] = 'Windows Media Video 8';
			$RIFFfourccLookup['WMV3'] = 'Windows Media Video 9';
			$RIFFfourccLookup['WNV1'] = 'Winnov Hardware Compression';
			$RIFFfourccLookup['x263'] = 'Xirlink H.263';
			$RIFFfourccLookup['XLV0'] = 'NetXL Video Decoder';
			$RIFFfourccLookup['XMPG'] = 'Xing MPEG (I-Frame only)';
			$RIFFfourccLookup['XXAN'] = '?XXAN?';
			$RIFFfourccLookup['Y41P'] = 'Brooktree YUV 4:1:1';
			$RIFFfourccLookup['Y8  '] = 'Grayscale video';
			$RIFFfourccLookup['YC12'] = 'Intel YUV 12 codec';
			$RIFFfourccLookup['YUV8'] = 'Winnov Caviar YUV8';
			$RIFFfourccLookup['YUY2'] = 'Uncompressed YUV 4:2:2';
			$RIFFfourccLookup['YUYV'] = 'Canopus YUV';
			$RIFFfourccLookup['ZLIB'] = '?ZLIB?';
			$RIFFfourccLookup['ZPEG'] = 'Metheus Video Zipper';
		}

		return ( isset( $RIFFfourccLookup["$fourcc"] )? $RIFFfourccLookup["$fourcc"] : '' );
	}
} // END OF ID3_RIFF

?>