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
 
class ID3_PNG extends ID3
{
	function getPNGHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat'] = 'png';

		rewind( $fd );
		$PNGfiledata = fread( $fd, ID3_FREAD_BUFFER_SIZE );
		$offset = 0;

		$PNGidentifier = substr( $PNGfiledata, $offset, 8 ); // $89 $50 $4E $47 $0D $0A $1A $0A
		$offset += 8;
	
		if ( $PNGidentifier != chr( 0x89 ) . chr( 0x50 ) . chr( 0x4E ) . chr( 0x47 ) . chr( 0x0D ) . chr( 0x0A ) . chr( 0x1A ) . chr( 0x0A ) ) 
		{
			unset( $MP3fileInfo['fileformat'] );
			return false;
		}
	
		while ( ( ( ftell( $fd ) - ( strlen( $PNGfiledata ) - $offset ) ) < $MP3fileInfo['filesize'] ) ) 
		{
			$chunk['data_length'] = ID3::bigEndianToInt( substr( $PNGfiledata, $offset, 4 ) );
			$offset += 4;
		
			while ( ( (strlen( $PNGfiledata ) - $offset ) < ( $chunk['data_length'] + 4 ) ) && ( ftell( $fd ) < $MP3fileInfo['filesize'] ) ) 
				$PNGfiledata .= fread( $fd, ID3_FREAD_BUFFER_SIZE );
		
			$chunk['type_text'] = substr( $PNGfiledata, $offset, 4 );
			$offset += 4;
			$chunk['type_raw'] = ID3::bigEndianToInt( $chunk['type_text'] );
			$chunk['data'] = substr( $PNGfiledata, $offset, $chunk['data_length'] );
			$offset += $chunk['data_length'];
			$chunk['crc'] = ID3::bigEndianToInt( substr( $PNGfiledata, $offset, 4 ) );
			$offset += 4;
	
			$chunk['flags']['ancilliary']   = (bool)( $chunk['type_raw'] & 0x20000000 );
			$chunk['flags']['private']      = (bool)( $chunk['type_raw'] & 0x00200000 );
			$chunk['flags']['reserved']     = (bool)( $chunk['type_raw'] & 0x00002000 );
			$chunk['flags']['safe_to_copy'] = (bool)( $chunk['type_raw'] & 0x00000020 );

			switch ( $chunk['type_text'] ) 
			{
				case 'IHDR': // Image Header
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['width']                     = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  0, 4 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['height']                    = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  4, 4 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['raw']['bit_depth']          = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  8, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['raw']['color_type']         = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  9, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['raw']['compression_method'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 10, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['raw']['filter_method']      = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 11, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['raw']['interlace_method']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 12, 1 ) );

					$MP3fileInfo['png'][$chunk['type_text']]['compression_method_text']   = ID3_PNG::PNGcompressionMethodLookup( $MP3fileInfo['png'][$chunk['type_text']]['raw']['compression_method'] );
					$MP3fileInfo['png'][$chunk['type_text']]['color_type']['palette']     = (bool)( $MP3fileInfo['png'][$chunk['type_text']]['raw']['color_type'] & 0x01 );
					$MP3fileInfo['png'][$chunk['type_text']]['color_type']['true_color']  = (bool)( $MP3fileInfo['png'][$chunk['type_text']]['raw']['color_type'] & 0x02 );
					$MP3fileInfo['png'][$chunk['type_text']]['color_type']['alpha']       = (bool)( $MP3fileInfo['png'][$chunk['type_text']]['raw']['color_type'] & 0x04 );
	
					break;

				case 'PLTE': // Palette
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					$paletteoffset = 0;

					for ( $i = 0; $i <= 255; $i++ ) 
					{
						// $MP3fileInfo['png'][$chunk['type_text']]['red']["$i"]   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1 ) );
						// $MP3fileInfo['png'][$chunk['type_text']]['green']["$i"] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1 ) );
						// $MP3fileInfo['png'][$chunk['type_text']]['blue']["$i"]  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1 ) );

						$red   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1 ) );
						$green = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1 ) );
						$blue  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $paletteoffset++, 1 ) );

						$MP3fileInfo['png'][$chunk['type_text']]["$i"] = ( ( $red << 16 ) | ( $green << 8 ) | ( $blue ) );
					}
				
					break;

				case 'tRNS': // Transparency
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
		
					switch ( $MP3fileInfo['png']['IHDR']['raw']['color_type'] ) 
					{
						case 0:
							$MP3fileInfo['png'][$chunk['type_text']]['transparent_color_gray']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2 ) );
							break;

						case 2:
							$MP3fileInfo['png'][$chunk['type_text']]['transparent_color_red']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['transparent_color_green'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['transparent_color_blue']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2 ) );

							break;

						case 3:
							for ( $i = 0; $i < strlen( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'] ); $i++ )
								$MP3fileInfo['png'][$chunk['type_text']]['palette_opacity']["$i"] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $i, 1 ) );
						
							break;

						case 4:
	
						case 6:
							$MP3fileInfo['error'][] = 'Invalid color_type in tRNS chunk: '   . $MP3fileInfo['png']['IHDR']['raw']['color_type'] . '.';

						default:
							$MP3fileInfo['error'][] = 'Unhandled color_type in tRNS chunk: ' . $MP3fileInfo['png']['IHDR']['raw']['color_type'] . '.';
							break;
					}
				
					break;

				case 'gAMA': // Image Gamma
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['gamma']  = ID3::bigEndianToInt( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'] ) / 100000;
					break;

				case 'cHRM': // Primary Chromaticities
					$MP3fileInfo['png'][$chunk['type_text']]['header']  = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['white_x'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  0, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['white_y'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  4, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['red_y']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  8, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['red_y']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 12, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['green_y'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 16, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['green_y'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 20, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['blue_y']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 24, 4 ) ) / 100000;
					$MP3fileInfo['png'][$chunk['type_text']]['blue_y']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 28, 4 ) ) / 100000;
	
					break;

				case 'sRGB': // Standard RGB Color Space
					$MP3fileInfo['png'][$chunk['type_text']]['header']                 = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['reindering_intent']      = ID3::bigEndianToInt( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'] );
					$MP3fileInfo['png'][$chunk['type_text']]['reindering_intent_text'] = ID3_PNG::PNGsRGBintentLookup( $MP3fileInfo['png'][$chunk['type_text']]['reindering_intent'] );

					break;

				case 'iCCP': // Embedded ICC Profile
					$MP3fileInfo['png'][$chunk['type_text']]['header']                  = $chunk;
					list( $profilename, $compressiondata )                              = explode( chr( 0x00 ), $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 );
					$MP3fileInfo['png'][$chunk['type_text']]['profile_name']            = $profilename;
					$MP3fileInfo['png'][$chunk['type_text']]['compression_method']      = ID3::bigEndianToInt( substr( $compressiondata, 0, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['compression_profile']     = substr( $compressiondata, 1 );

					$MP3fileInfo['png'][$chunk['type_text']]['compression_method_text'] = ID3_PNG::PNGcompressionMethodLookup( $MP3fileInfo['png'][$chunk['type_text']]['compression_method'] );
					break;

				case 'tEXt': // Textual Data
					$MP3fileInfo['png'][$chunk['type_text']]['header']  = $chunk;
					list( $keyword, $text )                             = explode( chr( 0x00 ), $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 );
					$MP3fileInfo['png'][$chunk['type_text']]['keyword'] = $keyword;
					$MP3fileInfo['png'][$chunk['type_text']]['text']    = $text;

					$textinformationfieldindex = 0;

					if ( isset( $MP3fileInfo['png']['text_information'] ) && is_array( $MP3fileInfo['png']['text_information'] ) )
						$textinformationfieldindex = count( $MP3fileInfo['png']['text_information'] );
					
					$MP3fileInfo['png']['text_information']["$textinformationfieldindex"]['keyword'] = $MP3fileInfo['png'][$chunk['type_text']]['keyword'];
					$MP3fileInfo['png']['text_information']["$textinformationfieldindex"]['text']    = $MP3fileInfo['png'][$chunk['type_text']]['text'];
	
					break;

				case 'zTXt': // Compressed Textual Data
					$MP3fileInfo['png'][$chunk['type_text']]['header']                  = $chunk;
					list( $keyword, $otherdata )                                        = explode( chr( 0x00 ), $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 );
					$MP3fileInfo['png'][$chunk['type_text']]['keyword']                 = $keyword;
					$MP3fileInfo['png'][$chunk['type_text']]['compression_method']      = ID3::bigEndianToInt( substr( $otherdata, 0, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['compressed_text']         = substr( $otherdata, 1 );
					$MP3fileInfo['png'][$chunk['type_text']]['compression_method_text'] = ID3_PNG::PNGcompressionMethodLookup( $MP3fileInfo['png'][$chunk['type_text']]['compression_method'] );
	
					switch ( $MP3fileInfo['png'][$chunk['type_text']]['compression_method'] ) 
					{
						case 0:
							$MP3fileInfo['png'][$chunk['type_text']]['text'] = gzuncompress( $MP3fileInfo['png'][$chunk['type_text']]['compressed_text'] );
							break;

						default:
							// unknown compression method
							break;
					}

					if ( isset( $MP3fileInfo['png'][$chunk['type_text']]['text'] ) ) 
					{
						$textinformationfieldindex = 0;
					
						if ( isset( $MP3fileInfo['png']['text_information'] ) && is_array( $MP3fileInfo['png']['text_information'] ) )
							$textinformationfieldindex = count( $MP3fileInfo['png']['text_information'] );
					
						$MP3fileInfo['png']['text_information']["$textinformationfieldindex"]['keyword'] = $MP3fileInfo['png'][$chunk['type_text']]['keyword'];
						$MP3fileInfo['png']['text_information']["$textinformationfieldindex"]['text']    = $MP3fileInfo['png'][$chunk['type_text']]['text'];
					}
				
					break;

				case 'iTXt': // International Textual Data
					$MP3fileInfo['png'][$chunk['type_text']]['header']                  = $chunk;
					list($keyword, $otherdata)                                          = explode( chr( 0x00 ), $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 );
					$MP3fileInfo['png'][$chunk['type_text']]['keyword']                 = $keyword;
					$MP3fileInfo['png'][$chunk['type_text']]['compression']             = (bool)ID3::bigEndianToInt( substr( $otherdata, 0, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['compression_method']      = ID3::bigEndianToInt( substr($otherdata, 1, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['compression_method_text'] = ID3_PNG::PNGcompressionMethodLookup( $MP3fileInfo['png'][$chunk['type_text']]['compression_method'] );
					list($languagetag, $translatedkeyword, $text)                       = explode( chr( 0x00 ), substr( $otherdata, 2 ), 3 );
					$MP3fileInfo['png'][$chunk['type_text']]['language_tag']            = $languagetag;
					$MP3fileInfo['png'][$chunk['type_text']]['translated_keyword']      = utf8_decode( $translatedkeyword );

					if ( $MP3fileInfo['png'][$chunk['type_text']]['compression'] ) 
					{
						switch ( $MP3fileInfo['png'][$chunk['type_text']]['compression_method'] ) 
						{
							case 0:
								$MP3fileInfo['png'][$chunk['type_text']]['text'] = utf8_decode( gzuncompress( $text ) );
								break;

							default:
								// unknown compression method
								break;
						}
					} 
					else 
					{
						$MP3fileInfo['png'][$chunk['type_text']]['text'] = utf8_decode( $text );
					}

					if ( isset( $MP3fileInfo['png'][$chunk['type_text']]['text'] ) ) 
					{
						$textinformationfieldindex = 0;
					
						if ( isset( $MP3fileInfo['png']['text_information'] ) && is_array( $MP3fileInfo['png']['text_information'] ) )
							$textinformationfieldindex = count( $MP3fileInfo['png']['text_information'] );
					
						$MP3fileInfo['png']['text_information']["$textinformationfieldindex"]['keyword'] = $MP3fileInfo['png'][$chunk['type_text']]['keyword'];
						$MP3fileInfo['png']['text_information']["$textinformationfieldindex"]['text']    = $MP3fileInfo['png'][$chunk['type_text']]['text'];
					}
				
					break;

				case 'bKGD': // Background Color
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				
					switch ( $MP3fileInfo['png']['IHDR']['raw']['color_type'] ) 
					{
						case 0:
					
						case 4:
							$MP3fileInfo['png'][$chunk['type_text']]['background_gray']  = ID3::bigEndianToInt( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'] );
							break;

						case 2:
	
						case 6:
							$MP3fileInfo['png'][$chunk['type_text']]['background_red']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0 * $MP3fileInfo['png']['IHDR']['raw']['bit_depth'], $MP3fileInfo['png']['IHDR']['raw']['bit_depth'] ) );
							$MP3fileInfo['png'][$chunk['type_text']]['background_green'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 1 * $MP3fileInfo['png']['IHDR']['raw']['bit_depth'], $MP3fileInfo['png']['IHDR']['raw']['bit_depth'] ) );
							$MP3fileInfo['png'][$chunk['type_text']]['background_blue']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 * $MP3fileInfo['png']['IHDR']['raw']['bit_depth'], $MP3fileInfo['png']['IHDR']['raw']['bit_depth'] ) );
							
							break;

						case 3:
							$MP3fileInfo['png'][$chunk['type_text']]['background_index'] = ID3::bigEndianToInt( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'] );
							break;

						default:
							break;
					}
			
					break;

				case 'pHYs': // Physical Pixel Dimensions
					$MP3fileInfo['png'][$chunk['type_text']]['header']            = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['pixels_per_unit_x'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 4 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['pixels_per_unit_y'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 4, 4 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['unit_specifier']    = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 8, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['unit']              = ID3_PNG::PNGpHYsUnitLookup( $MP3fileInfo['png'][$chunk['type_text']]['unit_specifier'] );
			
					break;

				case 'sBIT': // Significant Bits
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
				
					switch ( $MP3fileInfo['png']['IHDR']['raw']['color_type'] ) 
					{
						case 0:
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_gray']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1 ) );
							break;

						case 2:
	
						case 3:
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_red']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_green'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_blue']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2, 1 ) );
							break;

						case 4:
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_gray']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_alpha'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1 ) );
							break;

						case 6:
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_red']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_green'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_blue']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2, 1 ) );
							$MP3fileInfo['png'][$chunk['type_text']]['significant_bits_alpha'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 3, 1 ) );
							break;

						default:
							break;
					}
			
					break;

				case 'sPLT': // Suggested Palette
					$MP3fileInfo['png'][$chunk['type_text']]['header']             = $chunk;
					list($palettename, $otherdata)                                 = explode( chr( 0x00 ), $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 );
					$MP3fileInfo['png'][$chunk['type_text']]['palette_name']       = $palettename;
					$sPLToffset = 0;
					$MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bits']  = ID3::bigEndianToInt( substr( $otherdata, $sPLToffset, 1 ) );
					$sPLToffset += 1;
					$MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'] = $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bits'] / 8;
					$paletteCounter = 0;
		
					while ( $sPLToffset < strlen($otherdata ) ) 
					{
						$MP3fileInfo['png'][$chunk['type_text']]['red']["$paletteCounter"]       = ID3::bigEndianToInt( substr( $otherdata, $sPLToffset, $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'] ) );
						$sPLToffset += $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
						$MP3fileInfo['png'][$chunk['type_text']]['green']["$paletteCounter"]     = ID3::bigEndianToInt( substr( $otherdata, $sPLToffset, $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'] ) );
						$sPLToffset += $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
						$MP3fileInfo['png'][$chunk['type_text']]['blue']["$paletteCounter"]      = ID3::bigEndianToInt( substr( $otherdata, $sPLToffset, $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'] ) );
						$sPLToffset += $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
						$MP3fileInfo['png'][$chunk['type_text']]['alpha']["$paletteCounter"]     = ID3::bigEndianToInt( substr( $otherdata, $sPLToffset, $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'] ) );
						$sPLToffset += $MP3fileInfo['png'][$chunk['type_text']]['sample_depth_bytes'];
						$MP3fileInfo['png'][$chunk['type_text']]['frequency']["$paletteCounter"] = ID3::bigEndianToInt( substr( $otherdata, $sPLToffset, 2 ) );
						$sPLToffset += 2;
						$paletteCounter++;
					}
				
					break;

				case 'hIST': // Palette Histogram
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					$hISTcounter = 0;
				
					while ( $hISTcounter < strlen( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'] ) ) 
					{
						$MP3fileInfo['png'][$chunk['type_text']]["$hISTcounter"] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $hISTcounter / 2, 2 ) );
						$hISTcounter += 2;
					}
				
					break;

				case 'tIME': // Image Last-Modification Time
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['year']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 2 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['month']  = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['day']    = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 3, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['hour']   = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 4, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['minute'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 5, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['second'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 6, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['unix']   = gmmktime( $MP3fileInfo['png'][$chunk['type_text']]['hour'], $MP3fileInfo['png'][$chunk['type_text']]['minute'], $MP3fileInfo['png'][$chunk['type_text']]['second'], $MP3fileInfo['png'][$chunk['type_text']]['month'], $MP3fileInfo['png'][$chunk['type_text']]['day'], $MP3fileInfo['png'][$chunk['type_text']]['year'] );
	
					break;

				case 'oFFs': // Image Offset
					$MP3fileInfo['png'][$chunk['type_text']]['header']         = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['position_x']     = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 4 ), false, true );
					$MP3fileInfo['png'][$chunk['type_text']]['position_y']     = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 4, 4 ), false, true );
					$MP3fileInfo['png'][$chunk['type_text']]['unit_specifier'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 8, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['unit']           = ID3_PNG::PNGoFFsUnitLookup( $MP3fileInfo['png'][$chunk['type_text']]['unit_specifier'] );
		
					break;

				case 'pCAL': // Calibration Of Pixel Values
					$MP3fileInfo['png'][$chunk['type_text']]['header']             = $chunk;
					list($calibrationname, $otherdata)                             = explode( chr( 0x00 ), $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2 );
					$MP3fileInfo['png'][$chunk['type_text']]['calibration_name']   = $calibrationname;
					$pCALoffset = 0;
					$MP3fileInfo['png'][$chunk['type_text']]['original_zero']      = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 4 ), false, true );
					$pCALoffset += 4;
					$MP3fileInfo['png'][$chunk['type_text']]['original_max']       = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 4 ), false, true );
					$pCALoffset += 4;
					$MP3fileInfo['png'][$chunk['type_text']]['equation_type']      = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 1 ) );
					$pCALoffset += 1;
					$MP3fileInfo['png'][$chunk['type_text']]['equation_type_text'] = ID3_PNG::PNGpCALequationTypeLookup( $MP3fileInfo['png'][$chunk['type_text']]['equation_type'] );
					$MP3fileInfo['png'][$chunk['type_text']]['parameter_count']    = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset, 1 ) );
					$pCALoffset += 1;
					$MP3fileInfo['png'][$chunk['type_text']]['parameters']         = explode( chr( 0x00 ), substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], $pCALoffset ) );
	
					break;

				case 'sCAL': // Physical Scale Of Image Subject
					$MP3fileInfo['png'][$chunk['type_text']]['header']         = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]['unit_specifier'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['unit']           = ID3_PNG::PNGsCALUnitLookup( $MP3fileInfo['png'][$chunk['type_text']]['unit_specifier'] );
					list($pixelwidth, $pixelheight)                            = explode( chr( 0x00 ), substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]['pixel_width']    = $pixelwidth;
					$MP3fileInfo['png'][$chunk['type_text']]['pixel_height']   = $pixelheight;
	
					break;

				case 'gIFg': // GIF Graphic Control Extension
					$gIFgCounter = 0;
					
					if ( isset( $MP3fileInfo['png'][$chunk['type_text']] ) && is_array( $MP3fileInfo['png'][$chunk['type_text']] ) )
						$gIFgCounter = count( $MP3fileInfo['png'][$chunk['type_text']] );
					
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFgCounter"]['header']          = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFgCounter"]['disposal_method'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 0, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFgCounter"]['user_input_flag'] = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 1, 1 ) );
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFgCounter"]['delay_time']      = ID3::bigEndianToInt( substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 2, 2 ) );
			
					break;

				case 'gIFx': // GIF Application Extension
					$gIFxCounter = 0;
					
					if ( isset( $MP3fileInfo['png'][$chunk['type_text']] ) && is_array( $MP3fileInfo['png'][$chunk['type_text']] ) )
						$gIFxCounter = count( $MP3fileInfo['png'][$chunk['type_text']] );
					
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFxCounter"]['header']                 = $chunk;
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFxCounter"]['application_identifier'] = substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  0, 8 );
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFxCounter"]['authentication_code']    = substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'],  8, 3 );
					$MP3fileInfo['png'][$chunk['type_text']]["$gIFxCounter"]['application_data']       = substr( $MP3fileInfo['png'][$chunk['type_text']]['header']['data'], 11    );
		
					break;

				case 'IDAT': // Image Data
					$idatinformationfieldindex = 0;
					
					if ( isset( $MP3fileInfo['png']['IDAT'] ) && is_array( $MP3fileInfo['png']['IDAT'] ) )
						$idatinformationfieldindex = count( $MP3fileInfo['png']['IDAT'] );
					
					unset( $chunk['data'] );
					$MP3fileInfo['png'][$chunk['type_text']]["$idatinformationfieldindex"]['header'] = $chunk;
				
					break;

				case 'IEND': // Image Trailer
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					break;

				default:
					// unset( $chunk['data'] );
					$MP3fileInfo['png'][$chunk['type_text']]['header'] = $chunk;
					$MP3fileInfo['error'][] = 'Unhandled chunk type: ' . $chunk['type_text'] . '.';
					break;
			}
		}

		if ( isset( $MP3fileInfo['png']['IHDR'] ) ) 
		{
			$MP3fileInfo['resolution_x'] = $MP3fileInfo['png']['IHDR']['width'];
			$MP3fileInfo['resolution_y'] = $MP3fileInfo['png']['IHDR']['height'];
		}

		if ( isset( $MP3fileInfo['png']['text_information'] ) && is_array( $MP3fileInfo['png']['text_information'] ) ) 
		{
			foreach ( $MP3fileInfo['png']['text_information'] as $textdataarray ) 
			{
				switch ( strtolower( $textdataarray['keyword'] ) ) 
				{
					case 'title':
				
					case 'comment':
						$MP3fileInfo[$textdataarray['keyword']] = $textdataarray['text'];
						break;
		
					case 'author':
						$MP3fileInfo['artist'] = $textdataarray['text'];
						break;
		
					default:
						// do nothing
						break;
				}
			}
		}

		return true;
	}

	function PNGsRGBintentLookup( $sRGB ) 
	{
		static $PNGsRGBintentLookup = array();
	
		if ( count( $PNGsRGBintentLookup ) < 1 ) 
		{
			$PNGsRGBintentLookup[0] = 'Perceptual';
			$PNGsRGBintentLookup[1] = 'Relative colorimetric';
			$PNGsRGBintentLookup[2] = 'Saturation';
			$PNGsRGBintentLookup[3] = 'Absolute colorimetric';
		}
		
		return ( isset( $PNGsRGBintentLookup["$sRGB"] )? $PNGsRGBintentLookup["$sRGB"] : 'invalid' );
	}

	function PNGcompressionMethodLookup( $compressionmethod ) 
	{
		static $PNGcompressionMethodLookup = array();
	
		if ( count( $PNGcompressionMethodLookup ) < 1 )
			$PNGcompressionMethodLookup[0] = 'deflate/inflate';
	
		return ( isset( $PNGcompressionMethodLookup["$compressionmethod"] )? $PNGcompressionMethodLookup["$compressionmethod"] : 'invalid' );
	}

	function PNGpHYsUnitLookup( $unitid ) 
	{
		static $PNGpHYsUnitLookup = array();
	
		if ( count( $PNGpHYsUnitLookup ) < 1 ) 
		{
			$PNGpHYsUnitLookup[0] = 'unknown';
			$PNGpHYsUnitLookup[1] = 'meter';
		}
	
		return ( isset( $PNGpHYsUnitLookup["$unitid"] )? $PNGpHYsUnitLookup["$unitid"] : 'invalid' );
	}

	function PNGoFFsUnitLookup( $unitid ) 
	{
		static $PNGoFFsUnitLookup = array();
	
		if ( count( $PNGoFFsUnitLookup ) < 1 ) 
		{
			$PNGoFFsUnitLookup[0] = 'pixel';
			$PNGoFFsUnitLookup[1] = 'micrometer';
		}
	
		return ( isset( $PNGoFFsUnitLookup["$unitid"] )? $PNGoFFsUnitLookup["$unitid"] : 'invalid' );
	}

	function PNGpCALequationTypeLookup( $equationtype ) 
	{
		static $PNGpCALequationTypeLookup = array();
	
		if ( count( $PNGpCALequationTypeLookup ) < 1 ) 
		{
			$PNGpCALequationTypeLookup[0] = 'Linear mapping';
			$PNGpCALequationTypeLookup[1] = 'Base-e exponential mapping';
			$PNGpCALequationTypeLookup[2] = 'Arbitrary-base exponential mapping';
			$PNGpCALequationTypeLookup[3] = 'Hyperbolic mapping';
		}
	
		return ( isset( $PNGpCALequationTypeLookup["$equationtype"] )? $PNGpCALequationTypeLookup["$equationtype"] : 'invalid' );
	}

	function PNGsCALUnitLookup( $unitid ) 
	{
		static $PNGsCALUnitLookup = array();
	
		if ( count( $PNGsCALUnitLookup ) < 1 ) 
		{
			$PNGsCALUnitLookup[0] = 'meter';
			$PNGsCALUnitLookup[1] = 'radian';
		}
	
		return ( isset( $PNGsCALUnitLookup["$unitid"] )? $PNGsCALUnitLookup["$unitid"] : 'invalid' );
	}
} // END OF ID3_PNG

?>
