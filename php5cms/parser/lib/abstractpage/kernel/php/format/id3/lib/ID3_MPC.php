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
 * @link http://www.uni-jena.de/~pfk/mpp/sv8/header.html
 * @package format_id3_lib
 */
 
class ID3_MPC extends ID3
{
	function getMPCHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat']   = 'mpc';
		$MP3fileInfo['bitrate_mode'] = 'vbr';
		$MP3fileInfo['channels']     = 2; // the format appears to be hardcoded for stereo only

		rewind( $fd );
		$MP3fileInfo['mpc']['header']['size'] = 30;
		$MPCheaderData = fread($fd, $MP3fileInfo['mpc']['header']['size']);
		$offset = 0;

		$MP3fileInfo['mpc']['header']['raw']['preamble']        = substr( $MPCheaderData, $offset, 3 ); // should be 'MP+'
		$offset += 3;
		$StreamVersionByte                                      = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 1 ) );
		$offset += 1;
		$MP3fileInfo['mpc']['header']['stream_major_version']   = ( $StreamVersionByte & 0x0F );
		$MP3fileInfo['mpc']['header']['stream_minor_version']   = ( $StreamVersionByte & 0xF0 ) >> 4;
		$MP3fileInfo['mpc']['header']['frame_count']            = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 4 ) );
		$offset += 4;

		switch ( $MP3fileInfo['mpc']['header']['stream_major_version'] ) 
		{
			case 7:
				$MP3fileInfo['fileformat'] = 'SV7';
				break;

			default:
				$MP3fileInfo['error'][] = 'Only MPEGplus/Musepack SV7 supported.';
				return false;
		}

		$FlagsByte1 = ID3::littleEndianToInt(substr($MPCheaderData, $offset, 4));
		$offset += 4;
		$MP3fileInfo['mpc']['header']['intensity_stereo']  = (bool)( ( $FlagsByte1 & 0x80000000 ) >> 31 );
		$MP3fileInfo['mpc']['header']['mid_side_stereo']   = (bool)( ( $FlagsByte1 & 0x40000000 ) >> 30 );
		$MP3fileInfo['mpc']['header']['max_subband']       = ( $FlagsByte1 & 0x3F000000 ) >> 24;
		$MP3fileInfo['mpc']['header']['raw']['profile']    = ( $FlagsByte1 & 0x00F00000 ) >> 20;
		$MP3fileInfo['mpc']['header']['begin_loud']        = (bool)( ( $FlagsByte1 & 0x00080000 ) >> 19 );
		$MP3fileInfo['mpc']['header']['end_loud']          = (bool)( ( $FlagsByte1 & 0x00040000 ) >> 18 );
		$MP3fileInfo['mpc']['header']['raw']['frequency']  = ( $FlagsByte1 & 0x00030000 ) >> 16;
		$MP3fileInfo['mpc']['header']['max_level']         = ( $FlagsByte1 & 0x0000FFFF );

		$MP3fileInfo['mpc']['header']['raw']['title_peak'] = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 2 ) );
		$offset += 2;
		$MP3fileInfo['mpc']['header']['raw']['title_gain'] = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 2 ), true );
		$offset += 2;

		$MP3fileInfo['mpc']['header']['raw']['album_peak'] = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 2 ) );
		$offset += 2;
		$MP3fileInfo['mpc']['header']['raw']['album_gain'] = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 2 ), true );
		$offset += 2;

		$FlagsByte2                                        = ID3::littleEndianToInt( substr( $MPCheaderData, $offset, 4 ) );
		$offset += 4;
		$MP3fileInfo['mpc']['header']['true_gapless']      = (bool)( ( $FlagsByte2 & 0x80000000) >> 31 );
		$MP3fileInfo['mpc']['header']['last_frame_length'] = ( $FlagsByte2 & 0x7FF00000 ) >> 20;

		$offset += 3; // unused?
		$MP3fileInfo['mpc']['header']['raw']['encoder_version'] = ID3::littleEndianToInt( substr($MPCheaderData, $offset, 1 ) );
		$offset += 1;

		$MP3fileInfo['mpc']['header']['profile']                = ID3_MPC::MPCprofileNameLookup( $MP3fileInfo['mpc']['header']['raw']['profile'] );
		$MP3fileInfo['mpc']['header']['frequency']              = ID3_MPC::MPCfrequencyLookup( $MP3fileInfo['mpc']['header']['raw']['frequency'] );
		$MP3fileInfo['frequency']                               = $MP3fileInfo['mpc']['header']['frequency'];
		$MP3fileInfo['mpc']['header']['samples']                = ( ( ( $MP3fileInfo['mpc']['header']['frame_count'] - 1 ) * 1152 ) + $MP3fileInfo['mpc']['header']['last_frame_length'] ) * $MP3fileInfo['channels'];
		$MP3fileInfo['playtime_seconds']                        = ( $MP3fileInfo['mpc']['header']['samples'] / $MP3fileInfo['channels'] ) / $MP3fileInfo['frequency'];
		$MP3fileInfo['bitrate_audio']                           = ( ( $MP3fileInfo['filesize'] - $MP3fileInfo['mpc']['header']['size']  ) * 8) / $MP3fileInfo['playtime_seconds'];
		$MP3fileInfo['mpc']['header']['title_peak']             = $MP3fileInfo['mpc']['header']['raw']['title_peak'];
		$MP3fileInfo['mpc']['header']['title_peak_db']          = ID3_MPC::MPCpeakDBLookup( $MP3fileInfo['mpc']['header']['title_peak'] );
		$MP3fileInfo['mpc']['header']['title_gain_db']          = $MP3fileInfo['mpc']['header']['raw']['title_gain'] / 100;
		$MP3fileInfo['mpc']['header']['album_peak']             = $MP3fileInfo['mpc']['header']['raw']['album_peak'];
		$MP3fileInfo['mpc']['header']['album_peak_db']          = ID3_MPC::MPCpeakDBLookup( $MP3fileInfo['mpc']['header']['album_peak'] );
		$MP3fileInfo['mpc']['header']['album_gain_db']          = $MP3fileInfo['mpc']['header']['raw']['album_gain'] / 100;
		$MP3fileInfo['mpc']['header']['encoder_version']        = ID3_MPC::MPCencoderVersionLookup( $MP3fileInfo['mpc']['header']['raw']['encoder_version'] );

		if ( $MP3fileInfo['mpc']['header']['title_peak_db'] ) 
		{
			$MP3fileInfo['replay_gain']['radio']['peak']        = $MP3fileInfo['mpc']['header']['title_peak'];
			$MP3fileInfo['replay_gain']['radio']['adjustment']  = $MP3fileInfo['mpc']['header']['title_gain_db'];
		} 
		else 
		{
			$MP3fileInfo['replay_gain']['radio']['peak']        = ID3::castAsInt( round( $MP3fileInfo['mpc']['header']['max_level'] * 1.18 ) ); // why? I don't know - see mppdec.c
			$MP3fileInfo['replay_gain']['radio']['adjustment']  = 0;
		}
	
		if ( $MP3fileInfo['mpc']['header']['album_peak_db'] ) 
		{
			$MP3fileInfo['replay_gain']['audiophile']['peak']       = $MP3fileInfo['mpc']['header']['album_peak'];
			$MP3fileInfo['replay_gain']['audiophile']['adjustment'] = $MP3fileInfo['mpc']['header']['album_gain_db'];
		}

		return true;
	}

	function MPCprofileNameLookup( $profileid ) 
	{
		static $MPCprofileNameLookup = array();
	
		if ( count( $MPCprofileNameLookup ) < 1 ) 
		{
			$MPCprofileNameLookup[0]  = 'no profile';
			$MPCprofileNameLookup[1]  = 'Experimental';
			$MPCprofileNameLookup[2]  = 'unused';
			$MPCprofileNameLookup[3]  = 'unused';
			$MPCprofileNameLookup[4]  = 'unused';
			$MPCprofileNameLookup[5]  = 'below Telephone (q = 0.0)';
			$MPCprofileNameLookup[6]  = 'below Telephone (q = 1.0)';
			$MPCprofileNameLookup[7]  = 'Telephone (q = 2.0)';
			$MPCprofileNameLookup[8]  = 'Thumb (q = 3.0)';
			$MPCprofileNameLookup[9]  = 'Radio (q = 4.0)';
			$MPCprofileNameLookup[10] = 'Standard (q = 5.0)';
			$MPCprofileNameLookup[11] = 'Extreme (q = 6.0)';
			$MPCprofileNameLookup[12] = 'Insane (q = 7.0)';
			$MPCprofileNameLookup[13] = 'BrainDead (q = 8.0)';
			$MPCprofileNameLookup[14] = 'above BrainDead (q = 9.0)';
			$MPCprofileNameLookup[15] = 'above BrainDead (q = 10.0)';
		}
	
		return ( isset( $MPCprofileNameLookup["$profileid"] )? $MPCprofileNameLookup["$profileid"] : 'invalid' );
	}

	function MPCfrequencyLookup( $frequencyid ) 
	{
		static $MPCfrequencyLookup = array();
		
		if ( count( $MPCfrequencyLookup ) < 1 ) 
		{
			$MPCfrequencyLookup[0] = 44100;
			$MPCfrequencyLookup[1] = 48000;
			$MPCfrequencyLookup[2] = 37800;
			$MPCfrequencyLookup[3] = 32000;
		}
	
		return ( isset( $MPCfrequencyLookup["$frequencyid"] )? $MPCfrequencyLookup["$frequencyid"] : 'invalid' );
	}

	function MPCpeakDBLookup( $intvalue ) 
	{
		if ( $intvalue > 0 )
			return ( ( log10( $intvalue ) / log10( 2 ) ) - 15 ) * 6;
	
		return false;
	}

	function MPCencoderVersionLookup( $encoderversion ) 
	{
		// Encoder version * 100  (106 = 1.06)
		// EncoderVersion % 10 == 0        Release (1.0)
		// EncoderVersion %  2 == 0        Beta (1.06)
		// EncoderVersion %  2 == 1        Alpha (1.05a...z)

		if ( ( $encoderversion % 10 ) == 0 ) 
		{
			// release version
			return number_format( $encoderversion / 100, 2 );
		} 
		else if ( ( $encoderversion % 2 ) == 0 ) 
		{
			// beta version
			return number_format( $encoderversion / 100, 2 ) . ' beta';
		} 
		else 
		{
			// alpha version
			return number_format( $encoderversion / 100, 2 ) . ' alpha';
		}
	}
} // END OF ID3_MPC

?>
