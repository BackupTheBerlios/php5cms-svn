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
 
class ID3_BMP extends ID3
{
	function getBMPHeaderFilepointer( &$fd, &$MP3fileInfo, $ExtractPalette = false, $ExtractData = false ) 
	{
		$MP3fileInfo['fileformat'] = 'bmp';
		rewind( $fd );
		$offset = 0;
		$BMPheader = fread( $fd, 14 + 40 );

		// check if the hardcoded-to-1 "planes" is at offset 22 or 26
		$planes22 = ID3::littleEndianToInt( substr( $BMPheader, 22, 2 ) );
		$planes26 = ID3::littleEndianToInt( substr( $BMPheader, 26, 2 ) );
	
		if ( ( $planes22 == 1 ) && ( $planes26 != 1 ) ) 
		{
			$MP3fileInfo['bmp']['type_os']      = 'OS/2';
			$MP3fileInfo['bmp']['type_version'] = 1;
		} 
		else if ( ( $planes26 == 1 ) && ( $planes22 != 1 ) ) 
		{
			$MP3fileInfo['bmp']['type_os']      = 'Windows';
			$MP3fileInfo['bmp']['type_version'] = 1;
		} 
		else if ( $MP3fileInfo['bmp']['header']['raw']['header_size'] == 12 ) 
		{
			$MP3fileInfo['bmp']['type_os']      = 'OS/2';
			$MP3fileInfo['bmp']['type_version'] = 1;
		} 
		else if ( $MP3fileInfo['bmp']['header']['raw']['header_size'] == 40 ) 
		{
			$MP3fileInfo['bmp']['type_os']      = 'Windows';
			$MP3fileInfo['bmp']['type_version'] = 1;
		} 
		else if ( $MP3fileInfo['bmp']['header']['raw']['header_size'] == 84 ) 
		{
			$MP3fileInfo['bmp']['type_os']      = 'Windows';
			$MP3fileInfo['bmp']['type_version'] = 4;
		} 
		else if ( $MP3fileInfo['bmp']['header']['raw']['header_size'] == 100 ) 
		{
			$MP3fileInfo['bmp']['type_os']      = 'Windows';
			$MP3fileInfo['bmp']['type_version'] = 5;
		}

		// BITMAPFILEHEADER [14 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_62uq.asp
		// all versions
		// WORD    bfType;
		// DWORD   bfSize;
		// WORD    bfReserved1;
		// WORD    bfReserved2;
		// DWORD   bfOffBits;
		$MP3fileInfo['bmp']['header']['raw']['identifier']  = substr( $BMPheader, $offset, 2 );
		$offset += 2;
		$MP3fileInfo['bmp']['header']['raw']['filesize']    = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
		$offset += 4;
		$MP3fileInfo['bmp']['header']['raw']['reserved1']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
		$offset += 2;
		$MP3fileInfo['bmp']['header']['raw']['reserved2']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
		$offset += 2;
		$MP3fileInfo['bmp']['header']['raw']['data_offset'] = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
		$offset += 4;

		if ( $MP3fileInfo['bmp']['type_os'] == 'OS/2' ) 
		{
			// OS/2-format BMP
			// http://netghost.narod.ru/gff/graphics/summary/os2bmp.htm

			// DWORD  Size;             /* Size of this structure in bytes */
			// DWORD  Width;            /* Bitmap width in pixels */
			// DWORD  Height;           /* Bitmap height in pixel */
			// WORD   NumPlanes;        /* Number of bit planes (color depth) */
			// WORD   BitsPerPixel;     /* Number of bits per pixel per plane */

			$MP3fileInfo['bmp']['header']['raw']['header_size']    = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['width']          = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
			$offset += 2;
			$MP3fileInfo['bmp']['header']['raw']['height']         = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
			$offset += 2;
			$MP3fileInfo['bmp']['header']['raw']['planes']         = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
			$offset += 2;
			$MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
			$offset += 2;

			$MP3fileInfo['resolution_x'] = $MP3fileInfo['bmp']['header']['raw']['width'];
			$MP3fileInfo['resolution_y'] = $MP3fileInfo['bmp']['header']['raw']['height'];

			if ( $MP3fileInfo['bmp']['type_version'] >= 2 ) 
			{
				// DWORD  Compression;      /* Bitmap compression scheme */
				// DWORD  ImageDataSize;    /* Size of bitmap data in bytes */
				// DWORD  XResolution;      /* X resolution of display device */
				// DWORD  YResolution;      /* Y resolution of display device */
				// DWORD  ColorsUsed;       /* Number of color table indices used */
				// DWORD  ColorsImportant;  /* Number of important color indices */
				// WORD   Units;            /* Type of units used to measure resolution */
				// WORD   Reserved;         /* Pad structure to 4-byte boundary */
				// WORD   Recording;        /* Recording algorithm */
				// WORD   Rendering;        /* Halftoning algorithm used */
				// DWORD  Size1;            /* Reserved for halftoning algorithm use */
				// DWORD  Size2;            /* Reserved for halftoning algorithm use */
				// DWORD  ColorEncoding;    /* Color model used in bitmap */
				// DWORD  Identifier;       /* Reserved for application use */

				$MP3fileInfo['bmp']['header']['raw']['compression']      = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['bmp_data_size']    = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['resolution_h']     = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['resolution_v']     = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['colors_used']      = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['colors_important'] = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['resolution_units'] = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
				$offset += 2;
				$MP3fileInfo['bmp']['header']['raw']['reserved1']        = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
				$offset += 2;
				$MP3fileInfo['bmp']['header']['raw']['recording']        = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
				$offset += 2;
				$MP3fileInfo['bmp']['header']['raw']['rendering']        = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
				$offset += 2;
				$MP3fileInfo['bmp']['header']['raw']['size1']            = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['size2']            = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['color_encoding']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['identifier']       = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;	

				$MP3fileInfo['bmp']['header']['compression'] = ID3_BMP::BMPcompressionOS2Lookup( $MP3fileInfo['bmp']['header']['raw']['compression'] );
			}
		} 
		else if ( $MP3fileInfo['bmp']['type_os'] == 'Windows' ) 
		{
			// Windows-format BMP

			// BITMAPINFOHEADER - [40 bytes] http://msdn.microsoft.com/library/en-us/gdi/bitmaps_1rw2.asp
			// all versions
			// DWORD  biSize;
			// LONG   biWidth;
			// LONG   biHeight;
			// WORD   biPlanes;
			// WORD   biBitCount;
			// DWORD  biCompression;
			// DWORD  biSizeImage;
			// LONG   biXPelsPerMeter;
			// LONG   biYPelsPerMeter;
			// DWORD  biClrUsed;
			// DWORD  biClrImportant;

			$MP3fileInfo['bmp']['header']['raw']['header_size']      = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['width']            = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ), true );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['height']           = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ), true );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['planes']           = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
			$offset += 2;
			$MP3fileInfo['bmp']['header']['raw']['bits_per_pixel']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 2 ) );
			$offset += 2;
			$MP3fileInfo['bmp']['header']['raw']['compression']      = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['bmp_data_size']    = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['resolution_h']     = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ), true );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['resolution_v']     = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ), true );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['colors_used']      = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
			$offset += 4;
			$MP3fileInfo['bmp']['header']['raw']['colors_important'] = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
			$offset += 4;

			$MP3fileInfo['bmp']['header']['compression']             = ID3_BMP::BMPcompressionWindowsLookup( $MP3fileInfo['bmp']['header']['raw']['compression'] );
			$MP3fileInfo['resolution_x']                             = $MP3fileInfo['bmp']['header']['raw']['width'];
			$MP3fileInfo['resolution_y']                             = $MP3fileInfo['bmp']['header']['raw']['height'];

			if ( $MP3fileInfo['bmp']['type_version'] >= 4 ) 
			{
				$BMPheader .= fread( $fd, 44 );

				// BITMAPV4HEADER - [44 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_2k1e.asp
				// Win95+, WinNT4.0+
				// DWORD        bV4RedMask;
				// DWORD        bV4GreenMask;
				// DWORD        bV4BlueMask;
				// DWORD        bV4AlphaMask;
				// DWORD        bV4CSType;
				// CIEXYZTRIPLE bV4Endpoints;
				// DWORD        bV4GammaRed;
				// DWORD        bV4GammaGreen;
				// DWORD        bV4GammaBlue;
	
				$MP3fileInfo['bmp']['header']['raw']['red_mask']     = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['green_mask']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['blue_mask']    = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['alpha_mask']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['cs_type']      = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['ciexyz_red']   = substr( $BMPheader, $offset, 4 );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['ciexyz_green'] = substr( $BMPheader, $offset, 4 );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['ciexyz_blue']  = substr( $BMPheader, $offset, 4 );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['gamma_red']    = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['gamma_green']  = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['gamma_blue']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;

				$MP3fileInfo['bmp']['header']['ciexyz_red']   = ID3::fixedPoint2_30( strrev( $MP3fileInfo['bmp']['header']['raw']['ciexyz_red']   ) );
				$MP3fileInfo['bmp']['header']['ciexyz_green'] = ID3::fixedPoint2_30( strrev( $MP3fileInfo['bmp']['header']['raw']['ciexyz_green'] ) );
				$MP3fileInfo['bmp']['header']['ciexyz_blue']  = ID3::fixedPoint2_30( strrev( $MP3fileInfo['bmp']['header']['raw']['ciexyz_blue']  ) );
			}

			if ( $MP3fileInfo['bmp']['type_version'] >= 5 ) 
			{
				$BMPheader .= fread( $fd, 16 );

				// BITMAPV5HEADER - [16 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_7c36.asp
				// Win98+, Win2000+
				// DWORD        bV5Intent;
				// DWORD        bV5ProfileData;
				// DWORD        bV5ProfileSize;
				// DWORD        bV5Reserved;

				$MP3fileInfo['bmp']['header']['raw']['intent']              = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['profile_data_offset'] = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['profile_data_size']   = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
				$MP3fileInfo['bmp']['header']['raw']['reserved3']           = ID3::littleEndianToInt( substr( $BMPheader, $offset, 4 ) );
				$offset += 4;
			}
		} 
		else 
		{
			$MP3fileInfo['error'][] = 'Unknown BMP format in header.';
			return false;
		}

		if ( $ExtractPalette || $ExtractData ) 
		{
			$PaletteEntries = 0;
		
			if ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] < 16 )
				$PaletteEntries = pow( 2, $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] );
			else if ( isset( $MP3fileInfo['bmp']['header']['raw']['colors_used'] ) && ( $MP3fileInfo['bmp']['header']['raw']['colors_used'] > 0 ) && ( $MP3fileInfo['bmp']['header']['raw']['colors_used'] <= 256 ) )
				$PaletteEntries = $MP3fileInfo['bmp']['header']['raw']['colors_used'];
		
			if ( $PaletteEntries > 0 ) 
			{
				$BMPpalette    = fread( $fd, 4 * $PaletteEntries );
				$paletteoffset = 0;

				for ( $i = 0; $i < $PaletteEntries; $i++ ) 
				{
					// RGBQUAD          - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_5f8y.asp
					// BYTE    rgbBlue;
					// BYTE    rgbGreen;
					// BYTE    rgbRed;
					// BYTE    rgbReserved;
					// $MP3fileInfo['bmp']['palette']['blue']["$i"]  = ID3::littleEndianToInt( substr( $BMPpalette, $paletteoffset++, 1 ) );
					// $MP3fileInfo['bmp']['palette']['green']["$i"] = ID3::littleEndianToInt( substr( $BMPpalette, $paletteoffset++, 1 ) );
					// $MP3fileInfo['bmp']['palette']['red']["$i"]   = ID3::littleEndianToInt( substr( $BMPpalette, $paletteoffset++, 1 ) );
					$blue  = ID3::littleEndianToInt( substr( $BMPpalette, $paletteoffset++, 1 ) );
					$green = ID3::littleEndianToInt( substr( $BMPpalette, $paletteoffset++, 1 ) );
					$red   = ID3::littleEndianToInt( substr( $BMPpalette, $paletteoffset++, 1 ) );
					
					if ( ( $MP3fileInfo['bmp']['type_os'] == 'OS/2' ) && ( $MP3fileInfo['bmp']['type_version'] == 1 ) ) 
					{
						// no padding byte
					} 
					else 
					{
						$paletteoffset++; // padding byte
					}
				
					$MP3fileInfo['bmp']['palette']["$i"] = ( ( $red << 16 ) | ( $green << 8 ) | ( $blue ) );
				}
			}
		}

		if ( $ExtractData ) 
		{
			fseek( $fd, $MP3fileInfo['bmp']['header']['raw']['data_offset'], SEEK_SET );
			$RowByteLength   = ceil( ( $MP3fileInfo['bmp']['header']['raw']['width'] * ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] / 8 ) ) / 4 ) * 4; // round up to nearest DWORD boundry
			$BMPpixelData    = fread( $fd, $MP3fileInfo['bmp']['header']['raw']['height'] * $RowByteLength );
			$pixeldataoffset = 0;

			switch ( $MP3fileInfo['bmp']['header']['raw']['compression'] ) 
			{
				case 0: // BI_RGB
					switch ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] ) 
					{
						case 1:
							for ( $row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 ); $row >= 0; $row-- ) 
							{
								for ( $col = 0; $col < $MP3fileInfo['bmp']['header']['raw']['width']; $col = $col ) 
								{
									$paletteindexbyte = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
				
									for ( $i = 7; $i >= 0; $i-- ) 
									{
										$paletteindex = ( $paletteindexbyte & ( 0x01 << $i ) ) >> $i;
										$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette']["$paletteindex"];
										$col++;
									}
								}
							
								while ( ( $pixeldataoffset % 4 ) != 0 ) 
								{
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
						
							break;

						case 4:
							for ( $row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 ); $row >= 0; $row-- ) 
							{
								for ( $col = 0; $col < $MP3fileInfo['bmp']['header']['raw']['width']; $col = $col ) 
								{
									$paletteindexbyte = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
							
									for ( $i = 1; $i >= 0; $i-- ) 
									{
										$paletteindex = ( $paletteindexbyte & ( 0x0F << ( 4 * $i ) ) ) >> ( 4 * $i );
										$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette']["$paletteindex"];
										$col++;
									}
								}
						
								while ( ( $pixeldataoffset % 4 ) != 0 ) 
								{
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
						
							break;

						case 8:
							for ( $row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 ); $row >= 0; $row-- ) 
							{
								for ( $col = 0; $col < $MP3fileInfo['bmp']['header']['raw']['width']; $col++ ) 
								{
									$paletteindex = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
									$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette']["$paletteindex"];
								}
							
								while ( ( $pixeldataoffset % 4 ) != 0 ) 
								{
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
						
							break;

						case 24:
	
						case 32:
							for ( $row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 ); $row >= 0; $row-- ) 
							{
								for ( $col = 0; $col < $MP3fileInfo['bmp']['header']['raw']['width']; $col++ ) 
								{
									$blue  = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
									$green = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
									$red   = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
									
									if ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] == 32 )
										$paletteoffset++; // filler byte
								
									$MP3fileInfo['bmp']['data']["$row"]["$col"] = ( ( $red << 16 ) | ( $green << 8 ) | ( $blue ) );
								}
						
								while ( ( $pixeldataoffset % 4 ) != 0 ) 
								{
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
						
							break;

						case 16:

						default:
							$MP3fileInfo['error'][] = 'Unknown bits-per-pixel value (' . $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] . ') - cannot read pixel data.';
							break;
					}
				
					break;


				case 1: // BI_RLE8 - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_6x0u.asp
					switch ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] ) 
					{
						case 8:
							$pixelcounter = 0;
						
							while ( $pixeldataoffset < strlen( $BMPpixelData ) ) 
							{
								$firstbyte  = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
								$secondbyte = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
								
								if ( $firstbyte == 0 ) 
								{
									// escaped/absolute mode - the first byte of the pair can be set to zero to
									// indicate an escape character that denotes the end of a line, the end of
									// a bitmap, or a delta, depending on the value of the second byte.
						
									switch ( $secondbyte ) 
									{
										case 0:
											// end of line
											// no need for special processing, just ignore
											break;

										case 1:
											// end of bitmap
											$pixeldataoffset = strlen( $BMPpixelData ); // force to exit loop just in case
											break;

										case 2:
											// delta - The 2 bytes following the escape contain unsigned values
											// indicating the horizontal and vertical offsets of the next pixel
											// from the current position.
											$colincrement = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
											$rowincrement = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
											$col = ( $pixelcounter % $MP3fileInfo['bmp']['header']['raw']['width']) + $colincrement;
											$row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 - ( ( $pixelcounter - $col ) / $MP3fileInfo['bmp']['header']['raw']['width'] ) ) - $rowincrement;
											$pixelcounter = ( $row * $MP3fileInfo['bmp']['header']['raw']['width'] ) + $col;
									
											break;

										default:
											// In absolute mode, the first byte is zero and the second byte is a
											// value in the range 03H through FFH. The second byte represents the
											// number of bytes that follow, each of which contains the color index
											// of a single pixel. Each run must be aligned on a word boundary.
											for ( $i = 0; $i < $secondbyte; $i++ ) 
											{
												$paletteindex = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
												$col = $pixelcounter % $MP3fileInfo['bmp']['header']['raw']['width'];
												$row = $MP3fileInfo['bmp']['header']['raw']['height'] - 1 - ( ( $pixelcounter - $col ) / $MP3fileInfo['bmp']['header']['raw']['width'] );
												$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette']["$paletteindex"];
												$pixelcounter++;
											}
											
											while ( ( $pixeldataoffset % 2 ) != 0 ) 
											{
												// Each run must be aligned on a word boundary.
												$pixeldataoffset++;
											}
										
											break;
									}
								} 
								else 
								{
									// encoded mode - the first byte specifies the number of consecutive pixels
									// to be drawn using the color index contained in the second byte.
									for ( $i = 0; $i < $firstbyte; $i++ ) 
									{
										$col = $pixelcounter % $MP3fileInfo['bmp']['header']['raw']['width'];
										$row = $MP3fileInfo['bmp']['header']['raw']['height'] - 1 - ( ( $pixelcounter - $col ) / $MP3fileInfo['bmp']['header']['raw']['width'] );
										$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette']["$secondbyte"];
										$pixelcounter++;
									}
								}
							}
						
							break;

						default:
							$MP3fileInfo['error'][] = 'Unknown bits-per-pixel value (' . $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] . ') - cannot read pixel data.';
							break;
				
					}
				
					break;

				case 2: // BI_RLE4 - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_6x0u.asp
					switch ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] ) 
					{
						case 4:
							$pixelcounter = 0;
						
							while ( $pixeldataoffset < strlen( $BMPpixelData ) ) 
							{
								$firstbyte  = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
								$secondbyte = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );

								if ( $firstbyte == 0 ) 
								{
									// escaped/absolute mode - the first byte of the pair can be set to zero to
									// indicate an escape character that denotes the end of a line, the end of
									// a bitmap, or a delta, depending on the value of the second byte.
				
									switch ( $secondbyte ) 
									{
										case 0:
											// end of line
											// no need for special processing, just ignore
											break;

										case 1:
											// end of bitmap
											$pixeldataoffset = strlen( $BMPpixelData ); // force to exit loop just in case
											break;

										case 2:
											// delta - The 2 bytes following the escape contain unsigned values
											// indicating the horizontal and vertical offsets of the next pixel
											// from the current position.
											$colincrement = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
											$rowincrement = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
											$col = ( $pixelcounter % $MP3fileInfo['bmp']['header']['raw']['width'] ) + $colincrement;
											$row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 - ( ( $pixelcounter - $col ) / $MP3fileInfo['bmp']['header']['raw']['width'] ) ) - $rowincrement;
											$pixelcounter = ( $row * $MP3fileInfo['bmp']['header']['raw']['width'] ) + $col;
									
											break;

										default:
											// In absolute mode, the first byte is zero. The second byte contains the number
											// of color indexes that follow. Subsequent bytes contain color indexes in their
											// high- and low-order 4 bits, one color index for each pixel. In absolute mode,
											// each run must be aligned on a word boundary.
					
											unset( $paletteindexes );
	
											for ( $i = 0; $i < ceil( $secondbyte / 2 ); $i++ ) 
											{
												$paletteindexbyte = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset++, 1 ) );
												$paletteindexes[] = ( $paletteindexbyte & 0xF0 ) >> 4;
												$paletteindexes[] = ( $paletteindexbyte & 0x0F );
											}
										
											while ( ( $pixeldataoffset % 2 ) != 0 ) 
											{
												// Each run must be aligned on a word boundary.
												$pixeldataoffset++;
											}

											foreach ( $paletteindexes as $paletteindex ) 
											{
												$col = $pixelcounter % $MP3fileInfo['bmp']['header']['raw']['width'];
												$row = $MP3fileInfo['bmp']['header']['raw']['height'] - 1 - ( ( $pixelcounter - $col ) / $MP3fileInfo['bmp']['header']['raw']['width'] );
												$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette']["$paletteindex"];
												$pixelcounter++;
											}
											
											break;
									}
								} 
								else 
								{
									// encoded mode - the first byte of the pair contains the number of pixels to be
									// drawn using the color indexes in the second byte. The second byte contains two
									// color indexes, one in its high-order 4 bits and one in its low-order 4 bits.
									// The first of the pixels is drawn using the color specified by the high-order
									// 4 bits, the second is drawn using the color in the low-order 4 bits, the third
									// is drawn using the color in the high-order 4 bits, and so on, until all the
									// pixels specified by the first byte have been drawn.
	
									$paletteindexes[0] = ( $secondbyte & 0xF0 ) >> 4;
									$paletteindexes[1] = ( $secondbyte & 0x0F );
								
									for ( $i = 0; $i < $firstbyte; $i++ ) 
									{
										$col = $pixelcounter % $MP3fileInfo['bmp']['header']['raw']['width'];
										$row = $MP3fileInfo['bmp']['header']['raw']['height'] - 1 - ( ( $pixelcounter - $col ) / $MP3fileInfo['bmp']['header']['raw']['width']);
										$MP3fileInfo['bmp']['data']["$row"]["$col"] = $MP3fileInfo['bmp']['palette'][$paletteindexes[( $i % 2 )]];
										$pixelcounter++;
									}
								}
							}
			
							break;

						default:
							$MP3fileInfo['error'][] = 'Unknown bits-per-pixel value (' . $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] . ') - cannot read pixel data.';
							break;
					}
				
					break;


				case 3: // BI_BITFIELDS
					switch ( $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] ) 
					{
						case 16:
				
						case 32:
							$redshift   = 0;
							$greenshift = 0;
							$blueshift  = 0;
					
							while ( ( ( $MP3fileInfo['bmp']['header']['raw']['red_mask']   >> $redshift ) & 0x01 ) == 0 )
								$redshift++;
						
							while ( ( ( $MP3fileInfo['bmp']['header']['raw']['green_mask'] >> $greenshift ) & 0x01 ) == 0 )
								$greenshift++;
						
							while ( ( ( $MP3fileInfo['bmp']['header']['raw']['blue_mask']  >> $blueshift ) & 0x01 ) == 0 )
								$blueshift++;
						
							for ( $row = ( $MP3fileInfo['bmp']['header']['raw']['height'] - 1 ); $row >= 0; $row-- ) 
							{
								for ( $col = 0; $col < $MP3fileInfo['bmp']['header']['raw']['width']; $col++ ) 
								{
									$pixelvalue       = ID3::littleEndianToInt( substr( $BMPpixelData, $pixeldataoffset, $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] / 8 ) );
									$pixeldataoffset += $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] / 8;

									$red   = round( ( ( ( $pixelvalue & $MP3fileInfo['bmp']['header']['raw']['red_mask']   ) >> $redshift   ) / ( $MP3fileInfo['bmp']['header']['raw']['red_mask']   >> $redshift   ) ) * 255 );
									$green = round( ( ( ( $pixelvalue & $MP3fileInfo['bmp']['header']['raw']['green_mask'] ) >> $greenshift ) / ( $MP3fileInfo['bmp']['header']['raw']['green_mask'] >> $greenshift ) ) * 255 );
									$blue  = round( ( ( ( $pixelvalue & $MP3fileInfo['bmp']['header']['raw']['blue_mask']  ) >> $blueshift  ) / ( $MP3fileInfo['bmp']['header']['raw']['blue_mask']  >> $blueshift  ) ) * 255 );
			
									$MP3fileInfo['bmp']['data']["$row"]["$col"] = ( ( $red << 16 ) | ( $green << 8 ) | ( $blue ) );
								}
					
								while ( ( $pixeldataoffset % 4 ) != 0 ) 
								{
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
						
							break;

						default:
							$MP3fileInfo['error'][] = 'Unknown bits-per-pixel value (' . $MP3fileInfo['bmp']['header']['raw']['bits_per_pixel'] . ') - cannot read pixel data.';
							break;
					}
				
					break;

				default: // unhandled compression type
					$MP3fileInfo['error'][] = 'Unknown/unhandled compression type value (' . $MP3fileInfo['bmp']['header']['raw']['compression'] . ') - cannot decompress pixel data.';
					break;
			}
		}


		return true;
	}

	function plotBMP( &$BMPinfo ) 
	{
		$starttime = time();
	
		if ( !isset( $BMPinfo['bmp']['data'] ) || !is_array( $BMPinfo['bmp']['data'] ) )
			return false;
	
		set_time_limit( round( $BMPinfo['resolution_x'] * $BMPinfo['resolution_y'] / 10000 ) );
		
		if ( $im = ImageCreateTrueColor( $BMPinfo['resolution_x'], $BMPinfo['resolution_y'] ) ) 
		{
			for ( $row = 0; $row < $BMPinfo['resolution_y']; $row++ ) 
			{
				for ( $col = 0; $col < $BMPinfo['resolution_x']; $col++ ) 
				{
					if ( isset( $BMPinfo['bmp']['data']["$row"]["$col"] ) ) 
					{
						$red   = ( $BMPinfo['bmp']['data']["$row"]["$col"] & 0x00FF0000 ) >> 16;
						$green = ( $BMPinfo['bmp']['data']["$row"]["$col"] & 0x0000FF00 ) >> 8;
						$blue  = ( $BMPinfo['bmp']['data']["$row"]["$col"] & 0x000000FF );
						
						$pixelcolor = ImageColorAllocate( $im, $red, $green, $blue );
						ImageSetPixel( $im, $col, $row, $pixelcolor );
					} 
					else 
					{
						// echo 'ERROR: no data for pixel '.$row.' x '.$col.'<br>';
						// return false;
					}
				}
			}
		
			if ( headers_sent() ) 
			{
				// echo 'plotted ' . ( $BMPinfo['resolution_x'] * $BMPinfo['resolution_y'] ) . ' pixels in ' . ( time() - $starttime ) . ' seconds<br>';
		   		ImageDestroy( $im );
				exit;
			} 
			else 
			{
				header( 'Content-type: image/png' );
				ImagePNG( $im );
		   		ImageDestroy( $im );
			
				return true;
			}
		}
	
		return false;
	}

	function BMPcompressionWindowsLookup( $compressionid ) 
	{
		static $BMPcompressionWindowsLookup = array();
	
		if ( count( $BMPcompressionWindowsLookup ) < 1 ) 
		{
			$BMPcompressionWindowsLookup[0] = 'BI_RGB';
			$BMPcompressionWindowsLookup[1] = 'BI_RLE8';
			$BMPcompressionWindowsLookup[2] = 'BI_RLE4';
			$BMPcompressionWindowsLookup[3] = 'BI_BITFIELDS';
			$BMPcompressionWindowsLookup[4] = 'BI_JPEG';
			$BMPcompressionWindowsLookup[5] = 'BI_PNG';
		}
	
		return ( isset( $BMPcompressionWindowsLookup["$compressionid"] )? $BMPcompressionWindowsLookup["$compressionid"] : 'invalid' );
	}

	function BMPcompressionOS2Lookup( $compressionid ) 
	{
		static $BMPcompressionOS2Lookup = array();
	
		if ( count( $BMPcompressionOS2Lookup ) < 1 ) 
		{
			$BMPcompressionOS2Lookup[0] = 'BI_RGB';
			$BMPcompressionOS2Lookup[1] = 'BI_RLE8';
			$BMPcompressionOS2Lookup[2] = 'BI_RLE4';
			$BMPcompressionOS2Lookup[3] = 'Huffman 1D';
			$BMPcompressionOS2Lookup[4] = 'BI_RLE24';
		}
	
		return ( isset( $BMPcompressionOS2Lookup["$compressionid"] )? $BMPcompressionOS2Lookup["$compressionid"] : 'invalid' );
	}
} // END OF ID3_BMP

?>
