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
 
class ID3_MAC extends ID3
{
	function getMonkeysAudioHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat']   = 'mac';
		$MP3fileInfo['bitrate_mode'] = 'vbr';

		rewind( $fd );
		$MACheaderData = fread( $fd, 40 );

		$MP3fileInfo['monkeys_audio']['raw']['header_tag']           = substr( $MACheaderData, 0, 4 );
		$MP3fileInfo['monkeys_audio']['raw']['nVersion']             = ID3::littleEndianToInt( substr( $MACheaderData,  4, 2 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nCompressionLevel']    = ID3::littleEndianToInt( substr( $MACheaderData,  6, 2 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nFormatFlags']         = ID3::littleEndianToInt( substr( $MACheaderData,  8, 2 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nChannels']            = ID3::littleEndianToInt( substr( $MACheaderData, 10, 2 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nSampleRate']          = ID3::littleEndianToInt( substr( $MACheaderData, 12, 4 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nWAVHeaderBytes']      = ID3::littleEndianToInt( substr( $MACheaderData, 16, 4 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nWAVTerminatingBytes'] = ID3::littleEndianToInt( substr( $MACheaderData, 20, 4 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nTotalFrames']         = ID3::littleEndianToInt( substr( $MACheaderData, 24, 4 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nFinalFrameSamples']   = ID3::littleEndianToInt( substr( $MACheaderData, 28, 4 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nPeakLevel']           = ID3::littleEndianToInt( substr( $MACheaderData, 32, 4 ) );
		$MP3fileInfo['monkeys_audio']['raw']['nSeekElements']        = ID3::littleEndianToInt( substr( $MACheaderData, 38, 2 ) );

		$MP3fileInfo['monkeys_audio']['flags']['8-bit']         = (bool)( $MP3fileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0001 );
		$MP3fileInfo['monkeys_audio']['flags']['crc-32']        = (bool)( $MP3fileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0002 );
		$MP3fileInfo['monkeys_audio']['flags']['peak_level']    = (bool)( $MP3fileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0004 );
		$MP3fileInfo['monkeys_audio']['flags']['24-bit']        = (bool)( $MP3fileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0008 );
		$MP3fileInfo['monkeys_audio']['flags']['seek_elements'] = (bool)( $MP3fileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0010 );
		$MP3fileInfo['monkeys_audio']['flags']['no_wav_header'] = (bool)( $MP3fileInfo['monkeys_audio']['raw']['nFormatFlags'] & 0x0020 );
		$MP3fileInfo['monkeys_audio']['version']                = $MP3fileInfo['monkeys_audio']['raw']['nVersion'] / 1000;
		$MP3fileInfo['monkeys_audio']['compression']            = ID3_MAC::monkeyCompressionLevelNameLookup( $MP3fileInfo['monkeys_audio']['raw']['nCompressionLevel'] );
		$MP3fileInfo['monkeys_audio']['samples_per_frame']      = ID3_MAC::monkeySamplesPerFrame( $MP3fileInfo['monkeys_audio']['raw']['nVersion'], $MP3fileInfo['monkeys_audio']['raw']['nCompressionLevel'] );
		$MP3fileInfo['monkeys_audio']['bits_per_sample']        = ( $MP3fileInfo['monkeys_audio']['flags']['24-bit'] ? 24 : ($MP3fileInfo['monkeys_audio']['flags']['8-bit'] ? 8 : 16));
		$MP3fileInfo['monkeys_audio']['channels']               = $MP3fileInfo['monkeys_audio']['raw']['nChannels'];
		$MP3fileInfo['channels']                                = $MP3fileInfo['monkeys_audio']['channels'];
		$MP3fileInfo['monkeys_audio']['frequency']              = $MP3fileInfo['monkeys_audio']['raw']['nSampleRate'];
		$MP3fileInfo['frequency']                               = $MP3fileInfo['monkeys_audio']['frequency'];
		$MP3fileInfo['monkeys_audio']['peak_level']             = $MP3fileInfo['monkeys_audio']['raw']['nPeakLevel'];
		$MP3fileInfo['monkeys_audio']['peak_ratio']             = $MP3fileInfo['monkeys_audio']['peak_level'] / pow( 2, $MP3fileInfo['monkeys_audio']['bits_per_sample'] - 1 );
		$MP3fileInfo['monkeys_audio']['frames']                 = $MP3fileInfo['monkeys_audio']['raw']['nTotalFrames'];
		$MP3fileInfo['monkeys_audio']['samples']                = ( ( $MP3fileInfo['monkeys_audio']['frames'] - 1 ) * $MP3fileInfo['monkeys_audio']['samples_per_frame'] ) + $MP3fileInfo['monkeys_audio']['raw']['nFinalFrameSamples'];
		$MP3fileInfo['monkeys_audio']['playtime']               = $MP3fileInfo['monkeys_audio']['samples'] / $MP3fileInfo['monkeys_audio']['frequency'];
		$MP3fileInfo['playtime_seconds']                        = $MP3fileInfo['monkeys_audio']['playtime'];
		$MP3fileInfo['monkeys_audio']['compressed_size']        = $MP3fileInfo['filesize'];
		$MP3fileInfo['monkeys_audio']['uncompressed_size']      = $MP3fileInfo['monkeys_audio']['samples'] * $MP3fileInfo['monkeys_audio']['channels'] * ( $MP3fileInfo['monkeys_audio']['bits_per_sample'] / 8 );
		$MP3fileInfo['monkeys_audio']['compression_ratio']      = $MP3fileInfo['monkeys_audio']['compressed_size'] / ($MP3fileInfo['monkeys_audio']['uncompressed_size'] + $MP3fileInfo['monkeys_audio']['raw']['nWAVHeaderBytes']);
		$MP3fileInfo['monkeys_audio']['bitrate']                = ( ( $MP3fileInfo['monkeys_audio']['samples'] * $MP3fileInfo['monkeys_audio']['channels'] * $MP3fileInfo['monkeys_audio']['bits_per_sample'] ) / $MP3fileInfo['monkeys_audio']['playtime'] ) * $MP3fileInfo['monkeys_audio']['compression_ratio'];
		$MP3fileInfo['bitrate_audio']                           = $MP3fileInfo['monkeys_audio']['bitrate'];

		return true;
	}

	function getAPEtagFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$id3v1tagsize     = 128;
		$apetagheadersize = 32;
		fseek( $fd, 0 - $id3v1tagsize - $apetagheadersize, SEEK_END );
		$APEfooterID3v1 = fread( $fd, $id3v1tagsize + $apetagheadersize );
	
		// APE tag found before ID3v1
		if ( substr( $APEfooterID3v1, 0, strlen( 'APETAGEX' ) ) == 'APETAGEX' ) 
		{
			$APEfooterData   = substr( $APEfooterID3v1, 0, $apetagheadersize );
			$APEfooterOffset = 0 - $apetagheadersize - $id3v1tagsize;
		} 
		// APE tag found, no ID3v1
		else if ( substr( $APEfooterID3v1, $id3v1tagsize, strlen( 'APETAGEX' ) ) == 'APETAGEX' ) 
		{
			$APEfooterData   = substr( $APEfooterID3v1, $id3v1tagsize, $apetagheadersize );
			$APEfooterOffset = 0 - $apetagheadersize;
		}
		// APE tag not found 
		else 
		{
			return false;
		}

		$MP3fileInfo['fileformat']    = 'ape';
		$MP3fileInfo['ape']['footer'] = ID3_MAC::parseAPEheaderFooter( $APEfooterData );

		if ( isset( $MP3fileInfo['ape']['footer']['flags']['header'] ) && $MP3fileInfo['ape']['footer']['flags']['header'] ) 
		{
			fseek( $fd, $APEfooterOffset - $MP3fileInfo['ape']['footer']['raw']['tagsize'] + $apetagheadersize - $apetagheadersize, SEEK_END );
			$APEtagData = fread( $fd, $MP3fileInfo['ape']['footer']['raw']['tagsize'] + $apetagheadersize );
		} 
		else 
		{
			fseek( $fd, $APEfooterOffset - $MP3fileInfo['ape']['footer']['raw']['tagsize'] + $apetagheadersize, SEEK_END );
			$APEtagData = fread( $fd, $MP3fileInfo['ape']['footer']['raw']['tagsize'] );
		}
	
		$offset = 0;
	
		if ( isset( $MP3fileInfo['ape']['footer']['flags']['header']) && $MP3fileInfo['ape']['footer']['flags']['header'] ) 
		{
			$MP3fileInfo['ape']['header'] = ID3_MAC::parseAPEheaderFooter( substr( $APEtagData, 0, $apetagheadersize ) );
			$offset += $apetagheadersize;
		}

		$handykeys = array(
			'title', 
			'artist', 
			'album', 
			'track', 
			'genre', 
			'comment', 
			'year'
		);
		
		for ( $i = 0; $i < $MP3fileInfo['ape']['footer']['raw']['tag_items']; $i++ ) 
		{
			$value_size     = ID3::littleEndianToInt( substr( $APEtagData, $offset, 4 ) );
			$offset        += 4;
			$item_flags     = ID3::littleEndianToInt( substr( $APEtagData, $offset, 4 ) );
			$offset        += 4;
			$ItemKeyLength  = strpos( $APEtagData, chr( 0 ), $offset ) - $offset;
			$item_key       = substr( $APEtagData, $offset, $ItemKeyLength );
			$offset        += $ItemKeyLength + 1; // skip 0x00 terminator
			$data           = substr( $APEtagData, $offset, $value_size );
			$offset        += $value_size;

			$MP3fileInfo['ape']['items']["$item_key"]['raw']['value_size'] = $value_size;
			$MP3fileInfo['ape']['items']["$item_key"]['raw']['item_flags'] = $item_flags;
		
			if ( $MP3fileInfo['ape']['footer']['tag_version'] >= 2 )
				$MP3fileInfo['ape']['items']["$item_key"]['flags'] = ID3_MAC::parseAPEtagFlags( $item_flags );
		
			$MP3fileInfo['ape']['items']["$item_key"]['data'] = $data;
			
			if ( ID3_MAC::APEtagItemIsUTF8Lookup( $item_key ) )
				$MP3fileInfo['ape']['items']["$item_key"]['data_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['ape']['items']["$item_key"]['data'], 3 );
		
			if ( in_array( strtolower( $item_key ), $handykeys ) )
				$MP3fileInfo['ape'][strtolower( $item_key )] = $MP3fileInfo['ape']['items']["$item_key"]['data_ascii'];		
		}

		return true;
	}

	// see http://www.uni-jena.de/~pfk/mpp/sv8/apeheader.html
	function parseAPEheaderFooter( $APEheaderFooterData ) 
	{
		$headerfooterinfo['raw']['footer_tag']   = substr( $APEheaderFooterData, 0, 8 );
		$headerfooterinfo['raw']['version']      = ID3::littleEndianToInt( substr( $APEheaderFooterData,  8, 4 ) );
		$headerfooterinfo['raw']['tagsize']      = ID3::littleEndianToInt( substr( $APEheaderFooterData, 12, 4 ) );
		$headerfooterinfo['raw']['tag_items']    = ID3::littleEndianToInt( substr( $APEheaderFooterData, 16, 4 ) );
		$headerfooterinfo['raw']['global_flags'] = ID3::littleEndianToInt( substr( $APEheaderFooterData, 20, 4 ) );
		$headerfooterinfo['raw']['reserved']     = substr( $APEheaderFooterData, 24, 8 );
		$headerfooterinfo['tag_version']         = $headerfooterinfo['raw']['version'] / 1000;

		if ( $headerfooterinfo['tag_version'] >= 2 )
			$headerfooterinfo['flags'] = ID3_MAC::parseAPEtagFlags( $headerfooterinfo['raw']['global_flags'] );
	
		return $headerfooterinfo;
	}

	function parseAPEtagFlags( $rawflagint ) 
	{
		// "Note: APE Tags 1.0 do not use any of the APE Tag flags.
		// All are set to zero on creation and ignored on reading."
		// http://www.uni-jena.de/~pfk/mpp/sv8/apetagflags.html
		$flags['header']            = (bool)( $rawflagint & 0x80000000 );
		$flags['footer']            = (bool)( $rawflagint & 0x40000000 );
		$flags['this_is_header']    = (bool)( $rawflagint & 0x20000000 );
		$flags['item_contents_raw'] = ( $rawflagint & 0x00000006 ) >> 1;
		$flags['item_contents']     = ID3_MAC::APEcontentTypeFlagLookup( $flags['item_contents_raw'] );
		$flags['read_only']         = (bool)( $rawflagint & 0x00000001 );

		return $flags;
	}

	function monkeyCompressionLevelNameLookup( $compressionlevel ) 
	{
		static $MonkeyCompressionLevelNameLookup = array();
		
		if ( count( $MonkeyCompressionLevelNameLookup ) < 1 ) 
		{
			$MonkeyCompressionLevelNameLookup[0]    = 'unknown';
			$MonkeyCompressionLevelNameLookup[1000] = 'fast';
			$MonkeyCompressionLevelNameLookup[2000] = 'normal';
			$MonkeyCompressionLevelNameLookup[3000] = 'high';
			$MonkeyCompressionLevelNameLookup[4000] = 'extra-high';
			$MonkeyCompressionLevelNameLookup[5000] = 'insane';
		}
	
		return ( isset( $MonkeyCompressionLevelNameLookup["$compressionlevel"] )? $MonkeyCompressionLevelNameLookup["$compressionlevel"] : 'invalid' );
	}

	function monkeySamplesPerFrame( $versionid, $compressionlevel ) 
	{
		if ( $versionid >= 3950 )
			return 294912; // 73728 * 4
		else if ( $versionid >= 3900 )
			return 73728;
		else if ( ( $versionid >= 3800 ) && ( $compressionlevel == 4000 ) )
			return 73728;
		else
			return 9216;
	}

	function APEcontentTypeFlagLookup( $contenttypeid ) 
	{
		static $APEcontentTypeFlagLookup = array();
	
		if ( count( $APEcontentTypeFlagLookup ) < 1 ) 
		{
			$APEcontentTypeFlagLookup[0] = 'utf-8';
			$APEcontentTypeFlagLookup[1] = 'binary';
			$APEcontentTypeFlagLookup[2] = 'external';
			$APEcontentTypeFlagLookup[3] = 'reserved';
		}
	
		return ( isset( $APEcontentTypeFlagLookup["$contenttypeid"] )? $APEcontentTypeFlagLookup["$contenttypeid"] : 'invalid' );
	}

	function APEtagItemIsUTF8Lookup( $itemkey ) 
	{
		static $APEtagItemIsUTF8Lookup = array();
	
		if ( count( $APEtagItemIsUTF8Lookup ) < 1 ) 
		{
			$APEtagItemIsUTF8Lookup[] = 'Title';
			$APEtagItemIsUTF8Lookup[] = 'Subtitle';
			$APEtagItemIsUTF8Lookup[] = 'Artist';
			$APEtagItemIsUTF8Lookup[] = 'Album';
			$APEtagItemIsUTF8Lookup[] = 'Debut Album';
			$APEtagItemIsUTF8Lookup[] = 'Publisher';
			$APEtagItemIsUTF8Lookup[] = 'Conductor';
			$APEtagItemIsUTF8Lookup[] = 'Track';
			$APEtagItemIsUTF8Lookup[] = 'Composer';
			$APEtagItemIsUTF8Lookup[] = 'Comment';
			$APEtagItemIsUTF8Lookup[] = 'Copyright';
			$APEtagItemIsUTF8Lookup[] = 'Publicationright';
			$APEtagItemIsUTF8Lookup[] = 'File';
			$APEtagItemIsUTF8Lookup[] = 'Year';
			$APEtagItemIsUTF8Lookup[] = 'Record Date';
			$APEtagItemIsUTF8Lookup[] = 'Record Location';
			$APEtagItemIsUTF8Lookup[] = 'Genre';
			$APEtagItemIsUTF8Lookup[] = 'Media';
			$APEtagItemIsUTF8Lookup[] = 'Related';
			$APEtagItemIsUTF8Lookup[] = 'ISRC';
			$APEtagItemIsUTF8Lookup[] = 'Abstract';
			$APEtagItemIsUTF8Lookup[] = 'Language';
			$APEtagItemIsUTF8Lookup[] = 'Bibliography';
		}
	
		return in_array( $itemkey, $APEtagItemIsUTF8Lookup );
	}
} // END OF ID3_MAC

?>
