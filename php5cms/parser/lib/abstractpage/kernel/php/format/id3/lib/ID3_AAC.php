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
 
class ID3_AAC extends ID3
{
	function getAACADIFheaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat']      = 'aac';
		$MP3fileInfo['audiodataoffset'] = 0; // should be overridden below

		rewind( $fd );
		$AACheader = fread( $fd, 1024 );
		$offset = 0;

		if ( substr( $AACheader, 0, 4 ) == 'ADIF' ) 
		{
			$AACheaderBitstream = ID3::bigEndianToBin( $AACheader );
			$bitoffset = 0;

			$MP3fileInfo['aac']['header_type'] = 'ADIF';
			$bitoffset += 32;
			$MP3fileInfo['aac']['header']['mpeg_version'] = 4;

			$MP3fileInfo['aac']['header']['copyright'] = (bool)( substr( $AACheaderBitstream, $bitoffset, 1 ) == '1' );
			$bitoffset += 1;

			if ( $MP3fileInfo['aac']['header']['copyright'] ) 
			{
				$MP3fileInfo['aac']['header']['copyright_id'] = ID3::binToString( substr( $AACheaderBitstream, $bitoffset, 72 ) );
				$bitoffset += 72;
			}
			
			$MP3fileInfo['aac']['header']['original_copy'] = (bool)( substr( $AACheaderBitstream, $bitoffset, 1 ) == '1' );
			$bitoffset += 1;
			$MP3fileInfo['aac']['header']['home']          = (bool)( substr( $AACheaderBitstream, $bitoffset, 1 ) == '1' );
			$bitoffset += 1;
			$MP3fileInfo['aac']['header']['is_vbr']        = (bool)( substr( $AACheaderBitstream, $bitoffset, 1 ) == '1' );
			$bitoffset += 1;
		
			if ( $MP3fileInfo['aac']['header']['is_vbr'] ) 
			{
				$MP3fileInfo['bitrate_mode'] = 'vbr';
				$MP3fileInfo['aac']['header']['bitrate_max'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 23 ) );
				$bitoffset += 23;
			} 
			else 
			{
				$MP3fileInfo['bitrate_mode'] = 'cbr';
				$MP3fileInfo['aac']['header']['bitrate'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 23 ) );
				$bitoffset += 23;
				$MP3fileInfo['bitrate_audio'] = $MP3fileInfo['aac']['header']['bitrate'];
			}

			$MP3fileInfo['aac']['header']['num_program_configs'] = 1 + ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
			$bitoffset += 4;

			for ( $i = 0; $i < $MP3fileInfo['aac']['header']['num_program_configs']; $i++ ) 
			{
				if ( !$MP3fileInfo['aac']['header']['is_vbr'] ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['buffer_fullness'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 20 ) );
					$bitoffset += 20;
				}

				$MP3fileInfo['aac']['program_configs']["$i"]['element_instance_tag']       = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
				$MP3fileInfo['aac']['program_configs']["$i"]['object_type']                = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
				$bitoffset += 2;
				$MP3fileInfo['aac']['program_configs']["$i"]['sampling_frequency_index']   = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
				$MP3fileInfo['aac']['program_configs']["$i"]['num_front_channel_elements'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
				$MP3fileInfo['aac']['program_configs']["$i"]['num_side_channel_elements']  = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
				$MP3fileInfo['aac']['program_configs']["$i"]['num_back_channel_elements']  = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
				$MP3fileInfo['aac']['program_configs']["$i"]['num_lfe_channel_elements']   = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
				$bitoffset += 2;
				$MP3fileInfo['aac']['program_configs']["$i"]['num_assoc_data_elements']    = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 3 ) );
				$bitoffset += 3;
				$MP3fileInfo['aac']['program_configs']["$i"]['num_valid_cc_elements']      = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
				$MP3fileInfo['aac']['program_configs']["$i"]['mono_mixdown_present']       = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
				$bitoffset += 1;
	
				if ( $MP3fileInfo['aac']['program_configs']["$i"]['mono_mixdown_present'] ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['mono_mixdown_element_number'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}
				
				$MP3fileInfo['aac']['program_configs']["$i"]['stereo_mixdown_present'] = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
				$bitoffset += 1;
			
				if ( $MP3fileInfo['aac']['program_configs']["$i"]['stereo_mixdown_present'] ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['stereo_mixdown_element_number'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}

				$MP3fileInfo['aac']['program_configs']["$i"]['matrix_mixdown_idx_present'] = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
				$bitoffset += 1;
	
				if ( $MP3fileInfo['aac']['program_configs']["$i"]['matrix_mixdown_idx_present'] ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['matrix_mixdown_idx'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
					$bitoffset += 2;
					$MP3fileInfo['aac']['program_configs']["$i"]['pseudo_surround_enable'] = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
				}
				
				for ( $j = 0; $j < $MP3fileInfo['aac']['program_configs']["$i"]['num_front_channel_elements']; $j++ )
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['front_element_is_cpe']["$j"]     = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
					$MP3fileInfo['aac']['program_configs']["$i"]['front_element_tag_select']["$j"] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}
				
				for ( $j = 0; $j < $MP3fileInfo['aac']['program_configs']["$i"]['num_side_channel_elements']; $j++ )
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['side_element_is_cpe']["$j"]     = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
					$MP3fileInfo['aac']['program_configs']["$i"]['side_element_tag_select']["$j"] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}
				
				for ( $j = 0; $j < $MP3fileInfo['aac']['program_configs']["$i"]['num_back_channel_elements']; $j++ ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['back_element_is_cpe']["$j"]     = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
					$MP3fileInfo['aac']['program_configs']["$i"]['back_element_tag_select']["$j"] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}
				
				for ( $j = 0; $j < $MP3fileInfo['aac']['program_configs']["$i"]['num_lfe_channel_elements']; $j++ ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['lfe_element_tag_select']["$j"] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}
				
				for ( $j = 0; $j < $MP3fileInfo['aac']['program_configs']["$i"]['num_assoc_data_elements']; $j++ ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['assoc_data_element_tag_select']["$j"] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}
			
				for ( $j = 0; $j < $MP3fileInfo['aac']['program_configs']["$i"]['num_valid_cc_elements']; $j++ ) 
				{
					$MP3fileInfo['aac']['program_configs']["$i"]['cc_element_is_ind_sw']["$j"]          = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
					$MP3fileInfo['aac']['program_configs']["$i"]['valid_cc_element_tag_select']["$j"]   = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
					$bitoffset += 4;
				}

				$bitoffset = ceil( $bitoffset / 8 ) * 8;

				$MP3fileInfo['aac']['program_configs']["$i"]['comment_field_bytes'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 8 ) );
				$bitoffset += 8;
				$MP3fileInfo['aac']['program_configs']["$i"]['comment_field']       = ID3::binToString( substr( $AACheaderBitstream, $bitoffset, 8 * $MP3fileInfo['aac']['program_configs']["$i"]['comment_field_bytes'] ) );
				$bitoffset += 8 * $MP3fileInfo['aac']['program_configs']["$i"]['comment_field_bytes'];

				$MP3fileInfo['aac']['header']['profile_text']                      = ID3_AAC::AACprofileLookup( $MP3fileInfo['aac']['program_configs']["$i"]['object_type'], $MP3fileInfo['aac']['header']['mpeg_version'] );
				$MP3fileInfo['aac']['program_configs']["$i"]['sampling_frequency'] = ID3_AAC::AACsampleRateLookup( $MP3fileInfo['aac']['program_configs']["$i"]['sampling_frequency_index'] );
				$MP3fileInfo['frequency']                                          = $MP3fileInfo['aac']['program_configs']["$i"]['sampling_frequency'];
				$MP3fileInfo['channels']                                           = $MP3fileInfo['aac']['program_configs']["$i"]['num_front_channel_elements'] + $MP3fileInfo['aac']['program_configs']["$i"]['num_side_channel_elements'] + $MP3fileInfo['aac']['program_configs']["$i"]['num_back_channel_elements'] + $MP3fileInfo['aac']['program_configs']["$i"]['num_lfe_channel_elements'];
			
				if ( $MP3fileInfo['aac']['program_configs']["$i"]['comment_field'] )
					$MP3fileInfo['comment'] = ( isset( $MP3fileInfo['comment'] )? $MP3fileInfo['comment'].$MP3fileInfo['aac']['program_configs']["$i"]['comment_field'] : $MP3fileInfo['aac']['program_configs']["$i"]['comment_field'] );
			}
		
			$MP3fileInfo['audiodataoffset']  = ID3::castAsInt( ceil($bitoffset / 8 ) );
			$MP3fileInfo['playtime_seconds'] = ( ( $MP3fileInfo['filesize'] - $MP3fileInfo['audiodataoffset'] ) * 8 ) / $MP3fileInfo['bitrate_audio'];

			return true;
		} 
		else 
		{
			unset( $MP3fileInfo['fileformat'] );
			unset( $MP3fileInfo['audiodataoffset'] );
			unset( $MP3fileInfo['aac'] );
	
			$MP3fileInfo['error'][] = 'AAC-ADIF synch not found (expected "ADIF", found "' . substr( $AACheader, 0, 4 ) . '" instead).';
			return false;
		}
	}

	function getAACADTSheaderFilepointer( &$fd, &$MP3fileInfo, $MaxFramesToScan = 1000000, $ReturnExtendedInfo = false ) 
	{
		$byteoffset  = 0;
		$framenumber = 0;

		static $decbin = array();

		// populate $bindec
		for ( $i = 0; $i < 256; $i++ )
	    	$decbin[chr( $i )] = str_pad( decbin( $i ), 8, '0', STR_PAD_LEFT );

		while ( true ) 
		{
			// breaks out when end-of-file encountered, or invalid data found,
			// or MaxFramesToScan frames have been scanned

			fseek( $fd, $byteoffset, SEEK_SET );

			// First get substring
			$substring = fread( $fd, 10 );
		
			// Initialise $AACheaderBitstream
			$AACheaderBitstream = "";
		
			// Loop thru substring chars		
			for ( $i = 0; $i < 10; $i++ )
		    	$AACheaderBitstream .= $decbin[$substring{$i}];

			$bitoffset = 0;
	
			// Original line: $synctest = ID3::binToDec(substr($AACheaderBitstream, $bitoffset, 12));
			// BinDec() works fine with 12 bit
			$synctest = BinDec( substr( $AACheaderBitstream, $bitoffset, 12 ) );

			$bitoffset += 12;

			if ( $synctest != 0x0FFF ) 
			{
				$MP3fileInfo['error'][] = 'Synch pattern (0xFFF) not found (found 0x' . dechex( $synctest ) . ' instead).';
				return false;
			}
		
			// Gather info for first frame only - this takes time to do 1000 times!
			if ( $framenumber > 0 ) 
			{
				// MPEG-4
				if ( !$AACheaderBitstream[$bitoffset] ) 
					$bitoffset += 20;
				// MPEG-2
				else 
					$bitoffset += 18;
			} 
			else 
			{
				$MP3fileInfo['aac']['header_type'] = 'ADTS';
				$MP3fileInfo['aac']['header']['synch'] = $synctest;
				$MP3fileInfo['fileformat'] = 'aac';
		
				$MP3fileInfo['aac']['header']['mpeg_version'] = ( (substr( $AACheaderBitstream, $bitoffset, 1 ) == '0' )? 4 : 2 );
				$bitoffset += 1;
				$MP3fileInfo['aac']['header']['layer'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
				$bitoffset += 2;
	
				if ( $MP3fileInfo['aac']['header']['layer'] != 0 ) 
				{
					$MP3fileInfo['error'][] = 'Layer error - expected 0x00, found 0x' . dechex( $MP3fileInfo['aac']['header']['layer'] ) . ' instead.';
					return false;
				}
				
				$MP3fileInfo['aac']['header']['crc_present']            = ( ( substr( $AACheaderBitstream, $bitoffset, 1 ) == '0' )? true : false );
				$bitoffset += 1;
				$MP3fileInfo['aac']['header']['profile_id']             = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
				$bitoffset += 2;
				$MP3fileInfo['aac']['header']['profile_text']           = ID3_AAC::AACprofileLookup( $MP3fileInfo['aac']['header']['profile_id'], $MP3fileInfo['aac']['header']['mpeg_version'] );
		
				$MP3fileInfo['aac']['header']['sample_frequency_index'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 4 ) );
				$bitoffset += 4;
		 		$MP3fileInfo['aac']['header']['sample_frequency']       = ID3_AAC::AACsampleRateLookup( $MP3fileInfo['aac']['header']['sample_frequency_index'] );
		 		$MP3fileInfo['frequency']                               = $MP3fileInfo['aac']['header']['sample_frequency'];
		
				$MP3fileInfo['aac']['header']['private']                = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
				$bitoffset += 1;
				$MP3fileInfo['aac']['header']['channel_configuration']  = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 3 ) );
				$bitoffset += 3;
			 	$MP3fileInfo['channels']                                = $MP3fileInfo['aac']['header']['channel_configuration'];
				$MP3fileInfo['aac']['header']['original']               = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
				$bitoffset += 1;
				$MP3fileInfo['aac']['header']['home']                   = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
				$bitoffset += 1;
		
				if ( $MP3fileInfo['aac']['header']['mpeg_version'] == 4 ) 
				{
					$MP3fileInfo['aac']['header']['emphasis'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
					$bitoffset += 2;
				}
	
				if ( $ReturnExtendedInfo )
				{
					$MP3fileInfo['aac']["$framenumber"]['copyright_id_bit']   = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
					$MP3fileInfo['aac']["$framenumber"]['copyright_id_start'] = (bool)ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 1 ) );
					$bitoffset += 1;
				} 
				else 
				{
					$bitoffset += 2;
				}
			}
	
			$FrameLength = BinDec( substr( $AACheaderBitstream, $bitoffset, 13 ) );

			if ( $ReturnExtendedInfo ) 
			{
				$MP3fileInfo['aac']["$framenumber"]['aac_frame_length'] = $FrameLength;
				$bitoffset += 13;
				$MP3fileInfo['aac']["$framenumber"]['adts_buffer_fullness'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 11 ) );
				$bitoffset += 11;

				if ( $MP3fileInfo['aac']["$framenumber"]['adts_buffer_fullness'] == 0x07FF )
					$MP3fileInfo['bitrate_mode'] = 'vbr';
				else
					$MP3fileInfo['bitrate_mode'] = 'cbr';
			
				$MP3fileInfo['aac']["$framenumber"]['num_raw_data_blocks'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 2 ) );
				$bitoffset += 2;
			
				if ( $MP3fileInfo['aac']['header']['crc_present'] ) 
				{
					$MP3fileInfo['aac']["$framenumber"]['crc'] = ID3::binToDec( substr( $AACheaderBitstream, $bitoffset, 16 ) );
					$bitoffset += 16;
				}
			}
		
			$byteoffset += $FrameLength;
		
			if ( ( ++$framenumber < $MaxFramesToScan ) && ( ( $byteoffset + 10 ) < $MP3fileInfo['filesize'] ) ) 
			{
				// keep scanning
			} 
			else 
			{
				$MP3fileInfo['playtime_seconds'] = ( $MP3fileInfo['filesize'] / $byteoffset ) * ( ( $framenumber * 1024 ) / $MP3fileInfo['aac']['header']['sample_frequency'] );
				$MP3fileInfo['bitrate_audio']    = ( $MP3fileInfo['filesize'] * 8 ) / $MP3fileInfo['playtime_seconds'];
			
				return true;
			}
		}
	
		// should never get here.
	}

	function AACsampleRateLookup( $samplerateid ) 
	{
		static $AACsampleRateLookup = array();
	
		if ( count( $AACsampleRateLookup ) < 1 ) 
		{
			$AACsampleRateLookup[0]  = 96000;
			$AACsampleRateLookup[1]  = 88200;
			$AACsampleRateLookup[2]  = 64000;
			$AACsampleRateLookup[3]  = 48000;
			$AACsampleRateLookup[4]  = 44100;
			$AACsampleRateLookup[5]  = 32000;
			$AACsampleRateLookup[6]  = 24000;
			$AACsampleRateLookup[7]  = 22050;
			$AACsampleRateLookup[8]  = 16000;
			$AACsampleRateLookup[9]  = 12000;
			$AACsampleRateLookup[10] = 11025;
			$AACsampleRateLookup[11] = 8000;
			$AACsampleRateLookup[12] = 0;
			$AACsampleRateLookup[13] = 0;
			$AACsampleRateLookup[14] = 0;
			$AACsampleRateLookup[15] = 0;
		}
	
		return ( isset( $AACsampleRateLookup["$samplerateid"] )? $AACsampleRateLookup["$samplerateid"] : 'invalid' );
	}

	function AACprofileLookup( $profileid, $mpegversion ) 
	{
		static $AACprofileLookup = array();
	
		if ( count( $AACprofileLookup ) < 1 ) 
		{
			$AACprofileLookup[2][0] = 'Main profile';
			$AACprofileLookup[2][1] = 'Low Complexity profile (LC)';
			$AACprofileLookup[2][2] = 'Scalable Sample Rate profile (SSR)';
			$AACprofileLookup[2][3] = '(reserved)';
			$AACprofileLookup[4][0] = 'AAC_MAIN';
			$AACprofileLookup[4][1] = 'AAC_LC';
			$AACprofileLookup[4][2] = 'AAC_SSR';
			$AACprofileLookup[4][3] = 'AAC_LTP';
		}
	
		return ( isset( $AACprofileLookup["$mpegversion"]["$profileid"] )? $AACprofileLookup["$mpegversion"]["$profileid"] : 'invalid' );
	}
} // END OF ID3_AAC

?>
