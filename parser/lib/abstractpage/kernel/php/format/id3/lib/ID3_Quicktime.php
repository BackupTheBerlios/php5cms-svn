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
 
class ID3_Quicktime extends ID3
{
	function getQuicktimeHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		$MP3fileInfo['fileformat'] = 'quicktime';

		$offset      = 0;
		$atomcounter = 0;
	
		while ( $offset < $MP3fileInfo['filesize'] ) 
		{
			fseek( $fd, $offset, SEEK_SET );
			$AtomHeader = fread( $fd, 8 );
		
			$atomsize = ID3::bigEndianToInt( substr( $AtomHeader, 0, 4 ) );
			$atomname = substr( $AtomHeader, 4, 4 );
			$MP3fileInfo['quicktime']["$atomname"]['name']   = $atomname;
			$MP3fileInfo['quicktime']["$atomname"]['size']   = $atomsize;
			$MP3fileInfo['quicktime']["$atomname"]['offset'] = $offset;

			if ( ( $offset + $atomsize) > $MP3fileInfo['filesize'] ) 
			{
				$MP3fileInfo['error'][] = 'Atom at offset ' . $offset . ' claims to go beyond end-of-file (length: ' . $atomsize . ' bytes).';
				return false;
			}

			switch ( $atomname ) 
			{
				case 'mdat': // Media DATa atom
	
				case 'free': // FREE space atom
	
				case 'skip': // SKIP atom
	
				case 'wide': // 64-bit expansion placeholder atom
					// 'mdat' data is too big to deal with, contains no useful metadata
					// 'free', 'skip' and 'wide' are just padding, contains no useful data at all
					break;

				default:
					$atomHierarchy = array();
					$MP3fileInfo['quicktime']["$atomname"] = ID3_Quicktime::quicktimeParseAtom( $atomname, $atomsize, fread( $fd, $atomsize ), $MP3fileInfo, $offset, $atomHierarchy );
					break;
			}

			$offset += $atomsize;
			$atomcounter++;
		}
	
		if ( !isset( $MP3fileInfo['bitrate'] ) && isset( $MP3fileInfo['quicktime']['mdat']['size'] ) && isset( $MP3fileInfo['playtime_seconds'] ) )
			$MP3fileInfo['bitrate'] = ( $MP3fileInfo['quicktime']['mdat']['size'] * 8 ) / $MP3fileInfo['playtime_seconds'];
	
		return true;
	}

	// http://developer.apple.com/techpubs/quicktime/qtdevdocs/APIREF/INDEX/atomalphaindex.htm
	function quicktimeParseAtom( $atomname, $atomsize, $atomdata, &$MP3fileInfo, $baseoffset, &$atomHierarchy ) 
	{
		array_push( $atomHierarchy, $atomname );
		
		$atomstructure['hierarchy'] = implode( ' ', $atomHierarchy );
		$atomstructure['name']      = $atomname;
		$atomstructure['size']      = $atomsize;
		$atomstructure['offset']    = $baseoffset;
	
		switch ( $atomname ) 
		{
			case 'moov': // MOVie container atom

			case 'trak': // TRAcK container atom

			case 'clip': // CLIPping container atom

			case 'matt': // track MATTe container atom

			case 'edts': // EDiTS container atom

			case 'tref': // Track REFerence container atom

			case 'mdia': // MeDIA container atom

			case 'minf': // Media INFormation container atom

			case 'dinf': // Data INFormation container atom

			case 'udta': // User DaTA container atom

			case 'stbl': // Sample TaBLe container atom

			case 'cmov': // Compressed MOVie container atom

			case 'rmra': // Reference Movie Record Atom

			case 'rmda': // Reference Movie Descriptor Atom
				$atomstructure['subatoms'] = ID3_Quicktime::quicktimeParseContainerAtom( $atomdata, $MP3fileInfo, $baseoffset + 8, $atomHierarchy );
				break;

			case '©cpy':
	
			case '©day':
		
			case '©dir':
	
			case '©ed1':

			case '©ed2':
	
			case '©ed3':
	
			case '©ed4':
	
			case '©ed5':
	
			case '©ed6':

			case '©ed7':

			case '©ed8':

			case '©ed9':

			case '©fmt':

			case '©inf':

			case '©prd':

			case '©prf':

			case '©req':

			case '©src':

			case '©wrt':

			case '©nam':

			case '©cmt':

			case '©wrn':

			case '©hst':

			case '©mak':

			case '©mod':

			case '©PRD':

			case '©swr':

			case '©aut':

			case '©ART':

			case '©trk':

			case '©alb':

			case '©com':

			case '©gen':

			case '©ope':

			case '©url':

			case '©enc':
				$atomstructure['data_length'] = ID3::bigEndianToInt( substr( $atomdata,  0, 2 ) );
				$atomstructure['language_id'] = ID3::bigEndianToInt( substr( $atomdata,  2, 2 ) );
				$atomstructure['data'] = substr( $atomdata, 4 );
				$atomstructure['language'] = ID3_Quicktime::quicktimeLanguageLookup( $atomstructure['language_id'] );
	
				$handyatomtranslatorarray['©cpy'] = 'copyright';
				$handyatomtranslatorarray['©day'] = 'creation_date';
				$handyatomtranslatorarray['©dir'] = 'director';
				$handyatomtranslatorarray['©ed1'] = 'edit1';
				$handyatomtranslatorarray['©ed2'] = 'edit2';
				$handyatomtranslatorarray['©ed3'] = 'edit3';
				$handyatomtranslatorarray['©ed4'] = 'edit4';
				$handyatomtranslatorarray['©ed5'] = 'edit5';
				$handyatomtranslatorarray['©ed6'] = 'edit6';
				$handyatomtranslatorarray['©ed7'] = 'edit7';
				$handyatomtranslatorarray['©ed8'] = 'edit8';
				$handyatomtranslatorarray['©ed9'] = 'edit9';
				$handyatomtranslatorarray['©fmt'] = 'format';
				$handyatomtranslatorarray['©inf'] = 'information';
				$handyatomtranslatorarray['©prd'] = 'producer';
				$handyatomtranslatorarray['©prf'] = 'performers';
				$handyatomtranslatorarray['©req'] = 'system_requirements';
				$handyatomtranslatorarray['©src'] = 'source_credit';
				$handyatomtranslatorarray['©wrt'] = 'writer';

				// http://www.geocities.com/xhelmboyx/quicktime/formats/qtm-layout.txt
				$handyatomtranslatorarray['©nam'] = 'title';
				$handyatomtranslatorarray['©cmt'] = 'comment';
				$handyatomtranslatorarray['©wrn'] = 'warning';
				$handyatomtranslatorarray['©hst'] = 'host_computer';
				$handyatomtranslatorarray['©mak'] = 'make';
				$handyatomtranslatorarray['©mod'] = 'model';
				$handyatomtranslatorarray['©PRD'] = 'product';
				$handyatomtranslatorarray['©swr'] = 'software';
				$handyatomtranslatorarray['©aut'] = 'author';
				$handyatomtranslatorarray['©ART'] = 'artist';
				$handyatomtranslatorarray['©trk'] = 'track';
				$handyatomtranslatorarray['©alb'] = 'album';
				$handyatomtranslatorarray['©com'] = 'comment';
				$handyatomtranslatorarray['©gen'] = 'genre';
				$handyatomtranslatorarray['©ope'] = 'composer';
				$handyatomtranslatorarray['©url'] = 'url';
				$handyatomtranslatorarray['©enc'] = 'encoder';
	
				if ( isset( $handyatomtranslatorarray["$atomname"] ) )
					$MP3fileInfo['quicktime'][$handyatomtranslatorarray["$atomname"]] = $atomstructure['data'];
			
				break;

			case 'play': // auto-PLAY atom
				$atomstructure['autoplay']            = (bool)ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );			
				$MP3fileInfo['quicktime']['autoplay'] = $atomstructure['autoplay'];
				break;

			case 'WLOC': // Window LOCation atom
				$atomstructure['location_x']  = ID3::bigEndianToInt( substr( $atomdata,  0, 2 ) );
				$atomstructure['location_y']  = ID3::bigEndianToInt( substr( $atomdata,  2, 2 ) );
				break;

			case 'LOOP': // LOOPing atom

			case 'SelO': // play SELection Only atom

			case 'AllF': // play ALL Frames atom
				$atomstructure['data'] = ID3::bigEndianToInt( $atomdata );
				break;

			case 'name': 

			case 'MCPS': // Media Cleaner PRo

			case '@PRM': // adobe PReMiere version

			case '@PRQ': // adobe PRemiere Quicktime version
				$atomstructure['data'] = $atomdata;
				break;

			case 'cmvd': // Compressed MooV Data atom
				$MP3fileInfo['error'][] = 'Compressed Quicktime MOOV Data atoms ("cmvd") not supported.';
				break;

			case 'dcom': // Data COMpression atom
				$atomstructure['compression_id']   = $atomdata;
				$atomstructure['compression_text'] = ID3_Quicktime::quicktimeDCOMLookup( $atomdata );
				break;

			case 'rdrf': // Reference movie Data ReFerence atom
				$atomstructure['version']   = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw'] = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) );
				$atomstructure['flags']['internal_data'] = (bool)( $atomstructure['flags_raw'] & 0x000001 );
			
				$atomstructure['reference_type_name'] = substr( $atomdata, 4, 4 );
				$atomstructure['reference_length']    = ID3::bigEndianToInt( substr( $atomdata,  8, 4 ) );
		
				switch ( $atomstructure['reference_type_name'] ) 
				{
					case 'url ':
						$atomstructure['url'] = ID3::noNullString( substr( $atomdata, 12 ) );
						break;

					case 'alis':
						$atomstructure['file_alias'] = substr( $atomdata, 12 );
						break;

					case 'rsrc':
						$atomstructure['resource_alias'] = substr( $atomdata, 12 );
						break;

					default:
						$atomstructure['data'] = substr( $atomdata, 12 );
						break;
				}
			
				break;

			case 'rmqu': // Reference Movie QUality atom
				$atomstructure['movie_quality'] = ID3::bigEndianToInt( $atomdata );
				break;

			case 'rmcs': // Reference Movie Cpu Speed atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['cpu_speed_rating']			= ID3::bigEndianToInt( substr( $atomdata, 4, 2 ) );

				break;

			case 'rmvc': // Reference Movie Version Check atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['gestalt_selector']			= substr( $atomdata, 4, 4 );
				$atomstructure['gestalt_value_mask']		= ID3::bigEndianToInt( substr( $atomdata,  8, 4 ) );
				$atomstructure['gestalt_value']				= ID3::bigEndianToInt( substr( $atomdata, 12, 4 ) );
				$atomstructure['gestalt_check_type']		= ID3::bigEndianToInt( substr( $atomdata, 14, 2 ) );
		
				break;

			case 'rmcd': // Reference Movie Component check atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['component_type']			= substr( $atomdata,  4, 4 );
				$atomstructure['component_subtype']			= substr( $atomdata,  8, 4 );
				$atomstructure['component_manufacturer']	= substr( $atomdata, 12, 4 );
				$atomstructure['component_flags_raw']		= ID3::bigEndianToInt( substr( $atomdata, 16, 4 ) );
				$atomstructure['component_flags_mask']		= ID3::bigEndianToInt( substr( $atomdata, 20, 4 ) );
				$atomstructure['component_min_version']		= ID3::bigEndianToInt( substr( $atomdata, 24, 4 ) );
		
				break;

			case 'rmdr': // Reference Movie Data Rate atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['data_rate']					= ID3::bigEndianToInt( substr( $atomdata,  4, 4 ) );
				$atomstructure['data_rate_bps']				= $atomstructure['data_rate'] * 10;
	
				break;

			case 'rmla': // Reference Movie Language Atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['language_id']				= ID3::bigEndianToInt( substr( $atomdata,  4, 2 ) );
				$atomstructure['language']					= ID3_Quicktime::quicktimeLanguageLookup( $atomstructure['language_id'] );
	
				break;

			case 'rmla': // Reference Movie Language Atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['track_id']					= ID3::bigEndianToInt( substr( $atomdata,  4, 2 ) );
			
				break;

			case 'stsd': // Sample Table Sample Description atom
				$atomstructure['version']					= ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']					= ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries']			= ID3::bigEndianToInt( substr( $atomdata,  4, 4 ) );
				$stsdEntriesDataOffset						= 8;
			
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['sample_description_table']["$i"]['size']             = ID3::bigEndianToInt( substr( $atomdata, $stsdEntriesDataOffset, 4 ) );
					$stsdEntriesDataOffset += 4;
					$atomstructure['sample_description_table']["$i"]['data_format']      = substr( $atomdata, $stsdEntriesDataOffset, 4 );
					$stsdEntriesDataOffset += 4;
					$atomstructure['sample_description_table']["$i"]['reserved']         = ID3::bigEndianToInt( substr( $atomdata, $stsdEntriesDataOffset, 6 ) );
					$stsdEntriesDataOffset += 6;
					$atomstructure['sample_description_table']["$i"]['reference_index']  = ID3::bigEndianToInt( substr( $atomdata, $stsdEntriesDataOffset, 2 ) );
					$stsdEntriesDataOffset += 2;
					$atomstructure['sample_description_table']["$i"]['data']             = substr( $atomdata, $stsdEntriesDataOffset, ( $atomstructure['sample_description_table']["$i"]['size'] - 4 - 4 - 6 - 2 ) );
					$stsdEntriesDataOffset += ( $atomstructure['sample_description_table']["$i"]['size'] - 4 - 4 - 6 - 2 );
				
					$atomstructure['sample_description_table']["$i"]['encoder_version']  = ID3::bigEndianToInt( substr( $atomstructure['sample_description_table']["$i"]['data'], 0, 2 ) );
					$atomstructure['sample_description_table']["$i"]['encoder_revision'] = ID3::bigEndianToInt( substr( $atomstructure['sample_description_table']["$i"]['data'], 2, 2 ) );
					$atomstructure['sample_description_table']["$i"]['encoder_vendor']   = substr( $atomstructure['sample_description_table']["$i"]['data'], 4, 4 );
				
					if ( $atomstructure['sample_description_table']["$i"]['encoder_vendor'] == chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) ) 
					{
						// audio atom
						$atomstructure['sample_description_table']["$i"]['audio_channels']       = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'],  8,  2 ) );
						$atomstructure['sample_description_table']["$i"]['audio_bit_depth']      = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 10,  2 ) );
						$atomstructure['sample_description_table']["$i"]['audio_compression_id'] = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 12,  2 ) );
						$atomstructure['sample_description_table']["$i"]['audio_packet_size']    = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 14,  2 ) );
						$atomstructure['sample_description_table']["$i"]['audio_sample_rate']    = ID3::fixedPoint16_16( substr( $atomstructure['sample_description_table']["$i"]['data'], 16,  4 ) );
					
						$MP3fileInfo['quicktime']['audio']['codec']		= ID3_Quicktime::quicktimeAudioCodecLookup( $atomstructure['sample_description_table']["$i"]['data_format'] );
						$MP3fileInfo['quicktime']['audio']['frequency']	= $atomstructure['sample_description_table']["$i"]['audio_sample_rate'];
						$MP3fileInfo['frequency']						= $atomstructure['sample_description_table']["$i"]['audio_sample_rate'];
						$MP3fileInfo['quicktime']['audio']['channels']	= $atomstructure['sample_description_table']["$i"]['audio_channels'];
						$MP3fileInfo['channels']						= $atomstructure['sample_description_table']["$i"]['audio_channels'];
						$MP3fileInfo['quicktime']['audio']['bit_depth']	= $atomstructure['sample_description_table']["$i"]['audio_bit_depth'];
					} 
					else 
					{
						// video atom
						$atomstructure['sample_description_table']["$i"]['video_temporal_quality']  = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'],  8,  4 ) );
						$atomstructure['sample_description_table']["$i"]['video_spatial_quality']   = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 12,  4 ) );
						$atomstructure['sample_description_table']["$i"]['video_frame_width']       = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 16,  2 ) );
						$atomstructure['sample_description_table']["$i"]['video_frame_height']      = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 18,  2 ) );
						$atomstructure['sample_description_table']["$i"]['video_resolution_x']      = ID3::fixedPoint16_16( substr( $atomstructure['sample_description_table']["$i"]['data'], 20,  4 ) );
						$atomstructure['sample_description_table']["$i"]['video_resolution_y']      = ID3::fixedPoint16_16( substr( $atomstructure['sample_description_table']["$i"]['data'], 24,  4 ) );
						$atomstructure['sample_description_table']["$i"]['video_data_size']         = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 28,  4 ) );
						$atomstructure['sample_description_table']["$i"]['video_frame_count']       = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 32,  2 ) );
						$atomstructure['sample_description_table']["$i"]['video_encoder_name_len']  = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 34,  1 ) );
						$atomstructure['sample_description_table']["$i"]['video_encoder_name']      = substr( $atomstructure['sample_description_table']["$i"]['data'], 35, $atomstructure['sample_description_table']["$i"]['video_encoder_name_len'] );
						$atomstructure['sample_description_table']["$i"]['video_pixel_color_depth'] = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 66,  2 ) );
						$atomstructure['sample_description_table']["$i"]['video_color_table_id']    = ID3::bigEndianToInt(  substr( $atomstructure['sample_description_table']["$i"]['data'], 68,  2 ) );		
						$atomstructure['sample_description_table']["$i"]['video_pixel_color_type']  = ( ( $atomstructure['sample_description_table']["$i"]['video_pixel_color_depth'] > 32 )? 'grayscale' : 'color' );
						$atomstructure['sample_description_table']["$i"]['video_pixel_color_name']  = ID3_Quicktime::quicktimeColorNameLookup( $atomstructure['sample_description_table']["$i"]['video_pixel_color_depth'] );
						
						$MP3fileInfo['quicktime']['video']['codec']				= $atomstructure['sample_description_table']["$i"]['video_encoder_name'];
						$MP3fileInfo['quicktime']['video']['color_depth']		= $atomstructure['sample_description_table']["$i"]['video_pixel_color_depth'];
						$MP3fileInfo['quicktime']['video']['color_depth_name']	= $atomstructure['sample_description_table']["$i"]['video_pixel_color_name'];
				
					}
				
					unset( $atomstructure['sample_description_table']["$i"]['data'] );
				}
			
				break;

			case 'stts': // Sample Table Time-to-Sample atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
				$sttsEntriesDataOffset           = 8;
		
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['time_to_sample_table']["$i"]['sample_count']    = ID3::bigEndianToInt( substr( $atomdata, $sttsEntriesDataOffset, 4 ) );
					$sttsEntriesDataOffset += 4;
					$atomstructure['time_to_sample_table']["$i"]['sample_duration'] = substr( $atomdata, $sttsEntriesDataOffset, 4 );
					$sttsEntriesDataOffset += 4;
				}
			
				break;

			case 'stss': // Sample Table Sync Sample (key frames) atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
				$stssEntriesDataOffset           = 8;
	
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['time_to_sample_table']["$i"] = ID3::bigEndianToInt( substr( $atomdata, $stssEntriesDataOffset, 4 ) );
					$stssEntriesDataOffset += 4;
				}
			
				break;

			case 'stsc': // Sample Table Sample-to-Chunk atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
				$stscEntriesDataOffset           = 8;
		
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['sample_to_chunk_table']["$i"]['first_chunk']        = ID3::bigEndianToInt( substr( $atomdata, $stscEntriesDataOffset, 4 ) );
					$stscEntriesDataOffset += 4;
					$atomstructure['sample_to_chunk_table']["$i"]['samples_per_chunk']  = ID3::bigEndianToInt( substr( $atomdata, $stscEntriesDataOffset, 4 ) );
					$stscEntriesDataOffset += 4;
					$atomstructure['sample_to_chunk_table']["$i"]['sample_description'] = ID3::bigEndianToInt( substr( $atomdata, $stscEntriesDataOffset, 4 ) );
					$stscEntriesDataOffset += 4;
				}
			
				break;

			case 'stsz': // Sample Table SiZe atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['sample_size']    = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 8, 4 ) );
				$stszEntriesDataOffset           = 12;
			
				if ( $atomstructure['sample_size'] == 0 ) 
				{
					for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
					{
						$atomstructure['sample_size_table']["$i"] = ID3::bigEndianToInt( substr( $atomdata, $stszEntriesDataOffset, 4 ) );
						$stszEntriesDataOffset += 4;
					}
				}
			
				break;

			case 'stco': // Sample Table Chunk Offset atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
				$stcoEntriesDataOffset           = 8;
		
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['chunk_offset_table']["$i"] = ID3::bigEndianToInt( substr( $atomdata, $stcoEntriesDataOffset, 4 ) );
					$stcoEntriesDataOffset += 4;
				}
			
				break;

			case 'dref': // Data REFerence atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
				$drefDataOffset                  = 8;
	
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['data_references']["$i"]['size']      = ID3::bigEndianToInt( substr( $atomdata, $drefDataOffset, 4 ) );
					$drefDataOffset += 4;
					$atomstructure['data_references']["$i"]['type']      = substr( $atomdata, $drefDataOffset, 4 );
					$drefDataOffset += 4;
					$atomstructure['data_references']["$i"]['version']   = ID3::bigEndianToInt( substr( $atomdata,  $drefDataOffset, 1 ) );
					$drefDataOffset += 1;
					$atomstructure['data_references']["$i"]['flags_raw'] = ID3::bigEndianToInt( substr( $atomdata,  $drefDataOffset, 3 ) ); // hardcoded: 0x000
					$drefDataOffset += 3;
					$atomstructure['data_references']["$i"]['data']      = substr( $atomdata, $drefDataOffset, ( $atomstructure['data_references']["$i"]['size'] - 4 - 4 - 1 - 3 ) );
					$drefDataOffset += ($atomstructure['data_references']["$i"]['size'] - 4 - 4 - 1 - 3);
					$atomstructure['data_references']["$i"]['flags']['self_reference'] = (bool)( $atomstructure['data_references']["$i"]['flags_raw'] & 0x001 );
				}
			
				break;

			case 'gmin': // base Media INformation atom
				$atomstructure['version']                = ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']              = ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['graphics_mode']          = ID3::bigEndianToInt( substr( $atomdata,  4, 2 ) );
				$atomstructure['opcolor_red']            = ID3::bigEndianToInt( substr( $atomdata,  6, 2 ) );
				$atomstructure['opcolor_green']          = ID3::bigEndianToInt( substr( $atomdata,  8, 2 ) );
				$atomstructure['opcolor_blue']           = ID3::bigEndianToInt( substr( $atomdata, 10, 2 ) );
				$atomstructure['balance']                = ID3::bigEndianToInt( substr( $atomdata, 12, 2 ) );
				$atomstructure['reserved']               = ID3::bigEndianToInt( substr( $atomdata, 14, 2 ) );
	
				break;

			case 'smhd': // Sound Media information HeaDer atom
				$atomstructure['version']                = ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']              = ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['balance']                = ID3::bigEndianToInt( substr( $atomdata,  4, 2 ) );
				$atomstructure['reserved']               = ID3::bigEndianToInt( substr( $atomdata,  6, 2 ) );
	
				break;

			case 'vmhd': // Video Media information HeaDer atom
				$atomstructure['version']                = ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']              = ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) );
				$atomstructure['graphics_mode']          = ID3::bigEndianToInt( substr( $atomdata,  4, 2 ) );
				$atomstructure['opcolor_red']            = ID3::bigEndianToInt( substr( $atomdata,  6, 2 ) );
				$atomstructure['opcolor_green']          = ID3::bigEndianToInt( substr( $atomdata,  8, 2 ) );
				$atomstructure['opcolor_blue']           = ID3::bigEndianToInt( substr( $atomdata, 10, 2 ) );	
				$atomstructure['flags']['no_lean_ahead'] = (bool)( $atomstructure['flags_raw'] & 0x001 );
			
				break;

			case 'hdlr': // HanDLeR reference atom
				$atomstructure['version']                = ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']              = ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['component_type']         = substr( $atomdata,  4, 4 );
				$atomstructure['component_subtype']      = substr( $atomdata,  8, 4 );
				$atomstructure['component_manufacturer'] = substr( $atomdata, 12, 4 );
				$atomstructure['component_flags_raw']    = ID3::bigEndianToInt( substr( $atomdata, 16, 4 ) );
				$atomstructure['component_flags_mask']   = ID3::bigEndianToInt( substr( $atomdata, 20, 4 ) );
				$atomstructure['component_name']         = ID3::pascalToString( substr( $atomdata, 24 ) );
	
				break;

			case 'mdhd': // MeDia HeaDer atom
				$atomstructure['version']                = ID3::bigEndianToInt( substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']              = ID3::bigEndianToInt( substr( $atomdata,  1, 3 ) ); // hardcoded: 0x000
				$atomstructure['creation_time']          = ID3::bigEndianToInt( substr( $atomdata,  4, 4 ) );
				$atomstructure['modify_time']            = ID3::bigEndianToInt( substr( $atomdata,  8, 4 ) );
				$atomstructure['time_scale']             = ID3::bigEndianToInt( substr( $atomdata, 12, 4 ) );
				$atomstructure['duration']               = ID3::bigEndianToInt( substr( $atomdata, 16, 4 ) );
				$atomstructure['language_id']            = ID3::bigEndianToInt( substr( $atomdata, 20, 2 ) );
				$atomstructure['quality']                = ID3::bigEndianToInt( substr( $atomdata, 22, 2 ) );
				$atomstructure['creation_time_unix']     = ID3::dateMacToUnix( ( $atomstructure['creation_time'] );
				$atomstructure['modify_time_unix']       = ID3::dateMacToUnix( ( $atomstructure['modify_time']   );
				$atomstructure['playtime_seconds']       = $atomstructure['duration'] / $atomstructure['time_scale'];
				$atomstructure['language']               = ID3_Quicktime::quicktimeLanguageLookup( $atomstructure['language_id'] );
	
				break;

			case 'pnot': // Preview atom
				$atomstructure['modification_date']      = ID3::bigEndianToInt( substr( $atomdata,  0, 4 ) ); // "standard Macintosh format"
				$atomstructure['version_number']         = ID3::bigEndianToInt( substr( $atomdata,  4, 2 ) ); // hardcoded: 0x00
				$atomstructure['atom_type']              = substr( $atomdata, 6, 4 ); // usually: 'PICT'
				$atomstructure['atom_index']             = ID3::bigEndianToInt( substr( $atomdata, 10, 2 ) ); // usually: 0x01
				$atomstructure['modification_date_unix'] = ID3::dateMacToUnix( ( $atomstructure['modification_date'] );
	
				break;

			case 'crgn': // Clipping ReGioN atom
				$atomstructure['region_size']  	 	     = ID3::bigEndianToInt( substr( $atomdata,  0, 2 ) ); // The Region size, Region boundary box,
				$atomstructure['boundary_box']           = ID3::bigEndianToInt( substr( $atomdata,  2, 8 ) ); // and Clipping region data fields
				$atomstructure['clipping_data']          = substr( $atomdata, 10 ); // constitute a QuickDraw region.
	
				break;

			case 'load': // track LOAD settings atom
				$atomstructure['preload_start_time']             = ID3::bigEndianToInt( substr( $atomdata,  0, 4 ) );
				$atomstructure['preload_duration']               = ID3::bigEndianToInt( substr( $atomdata,  4, 4 ) );
				$atomstructure['preload_flags_raw']              = ID3::bigEndianToInt( substr( $atomdata,  8, 4 ) );
				$atomstructure['default_hints_raw']              = ID3::bigEndianToInt( substr( $atomdata, 12, 4 ) );
		
				$atomstructure['default_hints']['double_buffer'] = (bool)( $atomstructure['default_hints_raw'] & 0x0020 );
				$atomstructure['default_hints']['high_quality']  = (bool)( $atomstructure['default_hints_raw'] & 0x0100 );
	
				break;

			case 'tmcd': // TiMe CoDe atom
	
			case 'chap': // CHAPter list atom
	
			case 'sync': // SYNChronization atom
	
			case 'scpt': // tranSCriPT atom
	
			case 'ssrc': // non-primary SouRCe atom
				for ( $i = 0; $i < ( strlen( $atomdata ) % 4 ); $i++ )
					$atomstructure['track_id']["$i"] = ID3::bigEndianToInt( substr( $atomdata, $i * 4, 4 ) );
			
				break;

			case 'elst': // Edit LiST atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['number_entries'] = ID3::bigEndianToInt( substr( $atomdata, 4, 4 ) );
		
				for ( $i = 0; $i < $atomstructure['number_entries']; $i++ ) 
				{
					$atomstructure['edit_list']["$i"]['track_duration'] = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $i * 12 ) + 0, 4 ) );
					$atomstructure['edit_list']["$i"]['media_time']     = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $i * 12 ) + 4, 4 ) );
					$atomstructure['edit_list']["$i"]['media_rate']     = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $i * 12 ) + 8, 4 ) );
				}
			
				break;

			case 'kmat': // compressed MATte atom
				$atomstructure['version']        = ID3::bigEndianToInt( substr( $atomdata, 0, 1 ) );
				$atomstructure['flags_raw']      = ID3::bigEndianToInt( substr( $atomdata, 1, 3 ) ); // hardcoded: 0x000
				$atomstructure['matte_data_raw'] = substr( $atomdata, 4 );
		
				break;

			case 'ctab': // Color TABle atom
				$atomstructure['color_table_seed']  = ID3::bigEndianToInt( substr( $atomdata, 0, 4 ) ); // hardcoded: 0x00000000
				$atomstructure['color_table_flags'] = ID3::bigEndianToInt( substr( $atomdata, 4, 2 ) ); // hardcoded: 0x8000
				$atomstructure['color_table_size']  = ID3::bigEndianToInt( substr( $atomdata, 6, 2 ) ) + 1;

				for ( $colortableentry = 0; $colortableentry < $atomstructure['color_table_size']; $colortableentry++ ) 
				{
					$atomstructure['color_table']["$colortableentry"]['alpha'] = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $colortableentry * 8 ) + 0, 2 ) );
					$atomstructure['color_table']["$colortableentry"]['red']   = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $colortableentry * 8 ) + 2, 2 ) );
					$atomstructure['color_table']["$colortableentry"]['green'] = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $colortableentry * 8 ) + 4, 2 ) );
					$atomstructure['color_table']["$colortableentry"]['blue']  = ID3::bigEndianToInt( substr( $atomdata, 8 + ( $colortableentry * 8 ) + 6, 2 ) );
				}
			
				break;

			case 'mvhd': // MoVie HeaDer atom
				$atomstructure['version']               = ID3::bigEndianToInt(  substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']             = ID3::bigEndianToInt(  substr( $atomdata,  1, 3 ) );
				$atomstructure['creation_time']         = ID3::bigEndianToInt(  substr( $atomdata,  4, 4 ) );
				$atomstructure['modify_time']           = ID3::bigEndianToInt(  substr( $atomdata,  8, 4 ) );
				$atomstructure['time_scale']            = ID3::bigEndianToInt(  substr( $atomdata, 12, 4 ) );
				$atomstructure['duration']              = ID3::bigEndianToInt(  substr( $atomdata, 16, 4 ) );
				$atomstructure['preferred_rate']        = ID3::fixedPoint16_16( substr( $atomdata, 20, 4 ) );
				$atomstructure['preferred_volume']      = ID3::fixedPoint8_8(   substr( $atomdata, 24, 2 ) );
				$atomstructure['reserved']              = substr( $atomdata, 26, 10 );
				$atomstructure['matrix_a']              = ID3::fixedPoint16_16( substr( $atomdata, 36, 4 ) );
				$atomstructure['matrix_b']              = ID3::fixedPoint16_16( substr( $atomdata, 40, 4 ) );
				$atomstructure['matrix_u']              = ID3::fixedPoint16_16( substr( $atomdata, 44, 4 ) );
				$atomstructure['matrix_c']              = ID3::fixedPoint16_16( substr( $atomdata, 48, 4 ) );
				$atomstructure['matrix_v']              = ID3::fixedPoint16_16( substr( $atomdata, 52, 4 ) );
				$atomstructure['matrix_d']              = ID3::fixedPoint16_16( substr( $atomdata, 56, 4 ) );
				$atomstructure['matrix_x']              = ID3::fixedPoint2_30(  substr( $atomdata, 60, 4 ) );
				$atomstructure['matrix_y']              = ID3::fixedPoint2_30(  substr( $atomdata, 64, 4 ) );
				$atomstructure['matrix_w']              = ID3::fixedPoint2_30(  substr( $atomdata, 68, 4 ) );
				$atomstructure['preview_time']          = ID3::bigEndianToInt(  substr( $atomdata, 72, 4 ) );
				$atomstructure['preview_duration']      = ID3::bigEndianToInt(  substr( $atomdata, 76, 4 ) );
				$atomstructure['poster_time']           = ID3::bigEndianToInt(  substr( $atomdata, 80, 4 ) );
				$atomstructure['selection_time']        = ID3::bigEndianToInt(  substr( $atomdata, 84, 4 ) );
				$atomstructure['selection_duration']    = ID3::bigEndianToInt(  substr( $atomdata, 88, 4 ) );
				$atomstructure['current_time']          = ID3::bigEndianToInt(  substr( $atomdata, 92, 4 ) );
				$atomstructure['next_track_id']         = ID3::bigEndianToInt(  substr( $atomdata, 96, 4 ) );
				$atomstructure['creation_time_unix']    = ID3::dateMacToUnix( ( $atomstructure['creation_time'] );
				$atomstructure['modify_time_unix']      = ID3::dateMacToUnix( ( $atomstructure['modify_time'] );
				$MP3fileInfo['quicktime']['time_scale'] = $atomstructure['time_scale'];
				$MP3fileInfo['playtime_seconds']        = $atomstructure['duration'] / $atomstructure['time_scale'];
	
				break;

			case 'tkhd': // TracK HeaDer atom
				$atomstructure['version']               = ID3::bigEndianToInt(  substr( $atomdata,  0, 1 ) );
				$atomstructure['flags_raw']             = ID3::bigEndianToInt(  substr( $atomdata,  1, 3 ) );
				$atomstructure['creation_time']         = ID3::bigEndianToInt(  substr( $atomdata,  4, 4 ) );
				$atomstructure['modify_time']           = ID3::bigEndianToInt(  substr( $atomdata,  8, 4 ) );
				$atomstructure['trackid']               = ID3::bigEndianToInt(  substr( $atomdata, 12, 4 ) );
				$atomstructure['reserved1']             = ID3::bigEndianToInt(  substr( $atomdata, 16, 4 ) );
				$atomstructure['duration']              = ID3::bigEndianToInt(  substr( $atomdata, 20, 4 ) );
				$atomstructure['reserved2']             = ID3::bigEndianToInt(  substr( $atomdata, 24, 8 ) );
				$atomstructure['layer']                 = ID3::bigEndianToInt(  substr( $atomdata, 32, 2 ) );
				$atomstructure['alternate_group']       = ID3::bigEndianToInt(  substr( $atomdata, 34, 2 ) );
				$atomstructure['volume']                = ID3::fixedPoint8_8(   substr( $atomdata, 36, 2 ) );
				$atomstructure['reserved3']             = ID3::bigEndianToInt(  substr( $atomdata, 38, 2 ) );
				$atomstructure['matrix_a']              = ID3::fixedPoint16_16( substr( $atomdata, 40, 4 ) );
				$atomstructure['matrix_b']              = ID3::fixedPoint16_16( substr( $atomdata, 44, 4 ) );
				$atomstructure['matrix_u']              = ID3::fixedPoint16_16( substr( $atomdata, 48, 4 ) );
				$atomstructure['matrix_c']              = ID3::fixedPoint16_16( substr( $atomdata, 52, 4 ) );
				$atomstructure['matrix_v']              = ID3::fixedPoint16_16( substr( $atomdata, 56, 4 ) );
				$atomstructure['matrix_d']              = ID3::fixedPoint16_16( substr( $atomdata, 60, 4 ) );
				$atomstructure['matrix_x']              = ID3::fixedPoint2_30(  substr( $atomdata, 64, 4 ) );
				$atomstructure['matrix_y']              = ID3::fixedPoint2_30(  substr( $atomdata, 68, 4 ) );
				$atomstructure['matrix_w']              = ID3::fixedPoint2_30(  substr( $atomdata, 72, 4 ) );
				$atomstructure['width']                 = ID3::fixedPoint16_16( substr( $atomdata, 76, 4 ) );
				$atomstructure['height']                = ID3::fixedPoint16_16( substr( $atomdata, 80, 4 ) );

				$atomstructure['flags']['enabled']      = (bool)( $atomstructure['flags_raw'] & 0x0001 );
				$atomstructure['flags']['in_movie']     = (bool)( $atomstructure['flags_raw'] & 0x0002 );
				$atomstructure['flags']['in_preview']   = (bool)( $atomstructure['flags_raw'] & 0x0004 );
				$atomstructure['flags']['in_poster']    = (bool)( $atomstructure['flags_raw'] & 0x0008 );
				
				$atomstructure['creation_time_unix']    = ID3::dateMacToUnix( ( $atomstructure['creation_time'] );
				$atomstructure['modify_time_unix']      = ID3::dateMacToUnix( ( $atomstructure['modify_time']   );

				if ( !isset( $MP3fileInfo['resolution_x'] ) || !isset( $MP3fileInfo['resolution_y'] ) ) 
				{
					$MP3fileInfo['resolution_x'] = 0;
					$MP3fileInfo['resolution_y'] = 0;
				}
				
				$MP3fileInfo['resolution_x']                       = max( $MP3fileInfo['resolution_x'], $atomstructure['width']  );
				$MP3fileInfo['resolution_y']                       = max( $MP3fileInfo['resolution_y'], $atomstructure['height'] );
				$MP3fileInfo['quicktime']['video']['resolution_x'] = $MP3fileInfo['resolution_x'];
				$MP3fileInfo['quicktime']['video']['resolution_y'] = $MP3fileInfo['resolution_y'];
	
				break;

			case 'mdat': // Media DATa atom

			case 'free': // FREE space atom

			case 'skip': // SKIP atom

			case 'wide': // 64-bit expansion placeholder atom
				// When writing QuickTime files, it is sometimes necessary to update an atom's size.
				// It is impossible to update a 32-bit atom to a 64-bit atom since the 32-bit atom
				// is only 8 bytes in size, and the 64-bit atom requires 16 bytes. Therefore, QuickTime
				// puts an 8-byte placeholder atom before any atoms it may have to update the size of.
				// In this way, if the atom needs to be converted from a 32-bit to a 64-bit atom, the
				// placeholder atom can be overwritten to obtain the necessary 8 extra bytes.
				// The placeholder atom has a type of kWideAtomPlaceholderType ( 'wide' ).

				// 'mdat' data is too big to deal with, contains no useful metadata
				// 'free', 'skip' and 'wide' are just padding, contains no useful data at all
				break;

			default:
				$MP3fileInfo['error'][] = 'Unknown QuickTime atom type: "' . $atomname . '" at offset ' . $baseoffset . '.';
				$atomstructure['data']  = $atomdata;
	
				break;
		}
	
		array_pop( $atomHierarchy );
		return $atomstructure;
	}

	function quicktimeParseContainerAtom( $atomdata, &$MP3fileInfo, $baseoffset, &$atomHierarchy ) 
	{
		$atomstructure  = false;
		$subatomoffset  = 0;
		$subatomcounter = 0;

		if ( ( strlen( $atomdata ) == 4 ) && ( ID3::bigEndianToInt( $atomdata ) == 0x00000000 ) ) 
			return false;
	
		while ( $subatomoffset < strlen( $atomdata ) ) 
		{
			$subatomsize = ID3::bigEndianToInt( substr( $atomdata, $subatomoffset + 0, 4 ) );
			$subatomname = substr( $atomdata, $subatomoffset + 4, 4 );
			$subatomdata = substr( $atomdata, $subatomoffset + 8, $subatomsize - 8 );
		
			if ( $subatomsize == 0 ) 
			{
				// Furthermore, for historical reasons the list of atoms is optionally
				// terminated by a 32-bit integer set to 0. If you are writing a program
				// to read user data atoms, you should allow for the terminating 0.
				return $atomstructure;
			}

			$atomstructure["$subatomcounter"] = ID3_Quicktime::quicktimeParseAtom( $subatomname, $subatomsize, $subatomdata, $MP3fileInfo, $baseoffset + $subatomoffset, $atomHierarchy );

			$subatomoffset += $subatomsize;
			$subatomcounter++;
		}
	
		return $atomstructure;
	}

	function quicktimeLanguageLookup( $languageid ) 
	{
		static $QuicktimeLanguageLookup = array();
	
		if ( count( $QuicktimeLanguageLookup ) < 1 ) 
		{
			$QuicktimeLanguageLookup[0]   = 'English';
			$QuicktimeLanguageLookup[1]   = 'French';
			$QuicktimeLanguageLookup[2]   = 'German';
			$QuicktimeLanguageLookup[3]   = 'Italian';
			$QuicktimeLanguageLookup[4]   = 'Dutch';
			$QuicktimeLanguageLookup[5]   = 'Swedish';
			$QuicktimeLanguageLookup[6]   = 'Spanish';
			$QuicktimeLanguageLookup[7]   = 'Danish';
			$QuicktimeLanguageLookup[8]   = 'Portuguese';
			$QuicktimeLanguageLookup[9]   = 'Norwegian';
			$QuicktimeLanguageLookup[10]  = 'Hebrew';
			$QuicktimeLanguageLookup[11]  = 'Japanese';
			$QuicktimeLanguageLookup[12]  = 'Arabic';
			$QuicktimeLanguageLookup[13]  = 'Finnish';
			$QuicktimeLanguageLookup[14]  = 'Greek';
			$QuicktimeLanguageLookup[15]  = 'Icelandic';
			$QuicktimeLanguageLookup[16]  = 'Maltese';
			$QuicktimeLanguageLookup[17]  = 'Turkish';
			$QuicktimeLanguageLookup[18]  = 'Croatian';
			$QuicktimeLanguageLookup[19]  = 'Chinese (Traditional)';
			$QuicktimeLanguageLookup[20]  = 'Urdu';
			$QuicktimeLanguageLookup[21]  = 'Hindi';
			$QuicktimeLanguageLookup[22]  = 'Thai';
			$QuicktimeLanguageLookup[23]  = 'Korean';
			$QuicktimeLanguageLookup[24]  = 'Lithuanian';
			$QuicktimeLanguageLookup[25]  = 'Polish';
			$QuicktimeLanguageLookup[26]  = 'Hungarian';
			$QuicktimeLanguageLookup[27]  = 'Estonian';
			$QuicktimeLanguageLookup[28]  = 'Lettish';
			$QuicktimeLanguageLookup[28]  = 'Latvian';
			$QuicktimeLanguageLookup[29]  = 'Saamisk';
			$QuicktimeLanguageLookup[29]  = 'Lappish';
			$QuicktimeLanguageLookup[30]  = 'Faeroese';
			$QuicktimeLanguageLookup[31]  = 'Farsi';
			$QuicktimeLanguageLookup[31]  = 'Persian';
			$QuicktimeLanguageLookup[32]  = 'Russian';
			$QuicktimeLanguageLookup[33]  = 'Chinese (Simplified)';
			$QuicktimeLanguageLookup[34]  = 'Flemish';
			$QuicktimeLanguageLookup[35]  = 'Irish';
			$QuicktimeLanguageLookup[36]  = 'Albanian';
			$QuicktimeLanguageLookup[37]  = 'Romanian';
			$QuicktimeLanguageLookup[38]  = 'Czech';
			$QuicktimeLanguageLookup[39]  = 'Slovak';
			$QuicktimeLanguageLookup[40]  = 'Slovenian';
			$QuicktimeLanguageLookup[41]  = 'Yiddish';
			$QuicktimeLanguageLookup[42]  = 'Serbian';
			$QuicktimeLanguageLookup[43]  = 'Macedonian';
			$QuicktimeLanguageLookup[44]  = 'Bulgarian';
			$QuicktimeLanguageLookup[45]  = 'Ukrainian';
			$QuicktimeLanguageLookup[46]  = 'Byelorussian';
			$QuicktimeLanguageLookup[47]  = 'Uzbek';
			$QuicktimeLanguageLookup[48]  = 'Kazakh';
			$QuicktimeLanguageLookup[49]  = 'Azerbaijani';
			$QuicktimeLanguageLookup[50]  = 'AzerbaijanAr';
			$QuicktimeLanguageLookup[51]  = 'Armenian';
			$QuicktimeLanguageLookup[52]  = 'Georgian';
			$QuicktimeLanguageLookup[53]  = 'Moldavian';
			$QuicktimeLanguageLookup[54]  = 'Kirghiz';
			$QuicktimeLanguageLookup[55]  = 'Tajiki';
			$QuicktimeLanguageLookup[56]  = 'Turkmen';
			$QuicktimeLanguageLookup[57]  = 'Mongolian';
			$QuicktimeLanguageLookup[58]  = 'MongolianCyr';
			$QuicktimeLanguageLookup[59]  = 'Pashto';
			$QuicktimeLanguageLookup[60]  = 'Kurdish';
			$QuicktimeLanguageLookup[61]  = 'Kashmiri';
			$QuicktimeLanguageLookup[62]  = 'Sindhi';
			$QuicktimeLanguageLookup[63]  = 'Tibetan';
			$QuicktimeLanguageLookup[64]  = 'Nepali';
			$QuicktimeLanguageLookup[65]  = 'Sanskrit';
			$QuicktimeLanguageLookup[66]  = 'Marathi';
			$QuicktimeLanguageLookup[67]  = 'Bengali';
			$QuicktimeLanguageLookup[68]  = 'Assamese';
			$QuicktimeLanguageLookup[69]  = 'Gujarati';
			$QuicktimeLanguageLookup[70]  = 'Punjabi';
			$QuicktimeLanguageLookup[71]  = 'Oriya';
			$QuicktimeLanguageLookup[72]  = 'Malayalam';
			$QuicktimeLanguageLookup[73]  = 'Kannada';
			$QuicktimeLanguageLookup[74]  = 'Tamil';
			$QuicktimeLanguageLookup[75]  = 'Telugu';
			$QuicktimeLanguageLookup[76]  = 'Sinhalese';
			$QuicktimeLanguageLookup[77]  = 'Burmese';
			$QuicktimeLanguageLookup[78]  = 'Khmer';
			$QuicktimeLanguageLookup[79]  = 'Lao';
			$QuicktimeLanguageLookup[80]  = 'Vietnamese';
			$QuicktimeLanguageLookup[81]  = 'Indonesian';
			$QuicktimeLanguageLookup[82]  = 'Tagalog';
			$QuicktimeLanguageLookup[83]  = 'MalayRoman';
			$QuicktimeLanguageLookup[84]  = 'MalayArabic';
			$QuicktimeLanguageLookup[85]  = 'Amharic';
			$QuicktimeLanguageLookup[86]  = 'Tigrinya';
			$QuicktimeLanguageLookup[87]  = 'Galla';
			$QuicktimeLanguageLookup[87]  = 'Oromo';
			$QuicktimeLanguageLookup[88]  = 'Somali';
			$QuicktimeLanguageLookup[89]  = 'Swahili';
			$QuicktimeLanguageLookup[90]  = 'Ruanda';
			$QuicktimeLanguageLookup[91]  = 'Rundi';
			$QuicktimeLanguageLookup[92]  = 'Chewa';
			$QuicktimeLanguageLookup[93]  = 'Malagasy';
			$QuicktimeLanguageLookup[94]  = 'Esperanto';
			$QuicktimeLanguageLookup[128] = 'Welsh';
			$QuicktimeLanguageLookup[129] = 'Basque';
			$QuicktimeLanguageLookup[130] = 'Catalan';
			$QuicktimeLanguageLookup[131] = 'Latin';
			$QuicktimeLanguageLookup[132] = 'Quechua';
			$QuicktimeLanguageLookup[133] = 'Guarani';
			$QuicktimeLanguageLookup[134] = 'Aymara';
			$QuicktimeLanguageLookup[135] = 'Tatar';
			$QuicktimeLanguageLookup[136] = 'Uighur';
			$QuicktimeLanguageLookup[137] = 'Dzongkha';
			$QuicktimeLanguageLookup[138] = 'JavaneseRom';
		}
	
		return ( isset( $QuicktimeLanguageLookup["$languageid"] )? $QuicktimeLanguageLookup["$languageid"] : 'invalid' );
	}

	function quicktimeVideoCodecLookup( $codecid ) 
	{
		static $QuicktimeVideoCodecLookup = array();
	
		if ( count( $QuicktimeVideoCodecLookup ) < 1 ) 
		{
			$QuicktimeVideoCodecLookup['rle '] = 'RLE-Animation';
			$QuicktimeVideoCodecLookup['avr '] = 'AVR-JPEG';
			$QuicktimeVideoCodecLookup['base'] = 'Base';
			$QuicktimeVideoCodecLookup['WRLE'] = 'BMP';
			$QuicktimeVideoCodecLookup['cvid'] = 'Cinepak';
			$QuicktimeVideoCodecLookup['clou'] = 'Cloud';
			$QuicktimeVideoCodecLookup['cmyk'] = 'CMYK';
			$QuicktimeVideoCodecLookup['yuv2'] = 'ComponentVideo';
			$QuicktimeVideoCodecLookup['yuvu'] = 'ComponentVideoSigned';
			$QuicktimeVideoCodecLookup['yuvs'] = 'ComponentVideoUnsigned';
			$QuicktimeVideoCodecLookup['dvc '] = 'DVC-NTSC';
			$QuicktimeVideoCodecLookup['dvcp'] = 'DVC-PAL';
			$QuicktimeVideoCodecLookup['dvpn'] = 'DVCPro-NTSC';
			$QuicktimeVideoCodecLookup['dvpp'] = 'DVCPro-PAL';
			$QuicktimeVideoCodecLookup['fire'] = 'Fire';
			$QuicktimeVideoCodecLookup['flic'] = 'FLC';
			$QuicktimeVideoCodecLookup['b48r'] = '48RGB';
			$QuicktimeVideoCodecLookup['gif '] = 'GIF';
			$QuicktimeVideoCodecLookup['smc '] = 'Graphics';
			$QuicktimeVideoCodecLookup['h261'] = 'H261';
			$QuicktimeVideoCodecLookup['h263'] = 'H263';
			$QuicktimeVideoCodecLookup['IV41'] = 'Indeo4';
			$QuicktimeVideoCodecLookup['jpeg'] = 'JPEG';
			$QuicktimeVideoCodecLookup['PNTG'] = 'MacPaint';
			$QuicktimeVideoCodecLookup['msvc'] = 'Microsoft Video1';
			$QuicktimeVideoCodecLookup['mjpa'] = 'Motion JPEG-A';
			$QuicktimeVideoCodecLookup['mjpb'] = 'Motion JPEG-B';
			$QuicktimeVideoCodecLookup['myuv'] = 'MPEG YUV420';
			$QuicktimeVideoCodecLookup['dmb1'] = 'OpenDML JPEG';
			$QuicktimeVideoCodecLookup['kpcd'] = 'PhotoCD';
			$QuicktimeVideoCodecLookup['8BPS'] = 'Planar RGB';
			$QuicktimeVideoCodecLookup['png '] = 'PNG';
			$QuicktimeVideoCodecLookup['qdrw'] = 'QuickDraw';
			$QuicktimeVideoCodecLookup['qdgx'] = 'QuickDrawGX';
			$QuicktimeVideoCodecLookup['raw '] = 'RAW';
			$QuicktimeVideoCodecLookup['.SGI'] = 'SGI';
			$QuicktimeVideoCodecLookup['b16g'] = '16Gray';
			$QuicktimeVideoCodecLookup['b64a'] = '64ARGB';
			$QuicktimeVideoCodecLookup['SVQ1'] = 'Sorenson Video 1';
			$QuicktimeVideoCodecLookup['SVQ1'] = 'Sorenson Video 3';
			$QuicktimeVideoCodecLookup['syv9'] = 'Sorenson YUV9';
			$QuicktimeVideoCodecLookup['tga '] = 'Targa';
			$QuicktimeVideoCodecLookup['b32a'] = '32AlphaGray';
			$QuicktimeVideoCodecLookup['tiff'] = 'TIFF';
			$QuicktimeVideoCodecLookup['path'] = 'Vector';
			$QuicktimeVideoCodecLookup['rpza'] = 'Video';
			$QuicktimeVideoCodecLookup['ripl'] = 'WaterRipple';
			$QuicktimeVideoCodecLookup['WRAW'] = 'Windows RAW';
			$QuicktimeVideoCodecLookup['y420'] = 'YUV420';
		}
	
		return ( isset( $QuicktimeVideoCodecLookup["$codecid"] )? $QuicktimeVideoCodecLookup["$codecid"] : '' );
	}

	function quicktimeAudioCodecLookup( $codecid ) 
	{
		static $QuicktimeAudioCodecLookup = array();
	
		if ( count( $QuicktimeAudioCodecLookup ) < 1 ) 
		{
			$QuicktimeAudioCodecLookup['.mp3'] = 'Fraunhofer MPEG Layer-III alias';
			$QuicktimeAudioCodecLookup['aac '] = 'ISO/IEC 14496-3 AAC';
			$QuicktimeAudioCodecLookup['agsm'] = 'Apple GSM 10:1';
			$QuicktimeAudioCodecLookup['alaw'] = 'A-law 2:1';
			$QuicktimeAudioCodecLookup['conv'] = 'Sample Format';
			$QuicktimeAudioCodecLookup['dvca'] = 'DV';
			$QuicktimeAudioCodecLookup['dvi '] = 'DV 4:1';
			$QuicktimeAudioCodecLookup['eqal'] = 'Frequency Equalizer';
			$QuicktimeAudioCodecLookup['fl32'] = '32-bit Floating Point';
			$QuicktimeAudioCodecLookup['fl64'] = '64-bit Floating Point';
			$QuicktimeAudioCodecLookup['ima4'] = 'Interactive Multimedia Association 4:1';
			$QuicktimeAudioCodecLookup['in24'] = '24-bit Integer';
			$QuicktimeAudioCodecLookup['in32'] = '32-bit Integer';
			$QuicktimeAudioCodecLookup['lpc '] = 'LPC 23:1';
			$QuicktimeAudioCodecLookup['MAC3'] = 'Macintosh Audio Compression/Expansion (MACE) 3:1';
			$QuicktimeAudioCodecLookup['MAC6'] = 'Macintosh Audio Compression/Expansion (MACE) 6:1';
			$QuicktimeAudioCodecLookup['mixb'] = '8-bit Mixer';
			$QuicktimeAudioCodecLookup['mixw'] = '16-bit Mixer';
			$QuicktimeAudioCodecLookup['mp4a'] = 'ISO/IEC 14496-3 AAC';
			$QuicktimeAudioCodecLookup['MS' . chr( 0x00 ) . chr( 0x02 )] = 'Microsoft ADPCM';
			$QuicktimeAudioCodecLookup['MS' . chr( 0x00 ) . chr( 0x11 )] = 'DV IMA';
			$QuicktimeAudioCodecLookup['MS' . chr( 0x00 ) . chr( 0x55 )] = 'Fraunhofer MPEG Layer III';
			$QuicktimeAudioCodecLookup['NONE'] = 'No Encoding';
			$QuicktimeAudioCodecLookup['Qclp'] = 'Qualcomm PureVoice';
			$QuicktimeAudioCodecLookup['QDM2'] = 'QDesign Music 2';
			$QuicktimeAudioCodecLookup['QDMC'] = 'QDesign Music 1';
			$QuicktimeAudioCodecLookup['ratb'] = '8-bit Rate';
			$QuicktimeAudioCodecLookup['ratw'] = '16-bit Rate';
			$QuicktimeAudioCodecLookup['raw '] = 'raw PCM';
			$QuicktimeAudioCodecLookup['sour'] = 'Sound Source';
			$QuicktimeAudioCodecLookup['sowt'] = 'signed/two\'s complement (Little Endian)';
			$QuicktimeAudioCodecLookup['str1'] = 'Iomega MPEG layer II';
			$QuicktimeAudioCodecLookup['str2'] = 'Iomega MPEG *layer II';
			$QuicktimeAudioCodecLookup['str3'] = 'Iomega MPEG **layer II';
			$QuicktimeAudioCodecLookup['str4'] = 'Iomega MPEG ***layer II';
			$QuicktimeAudioCodecLookup['twos'] = 'signed/two\'s complement (Big Endian)';
			$QuicktimeAudioCodecLookup['ulaw'] = 'mu-law 2:1';
		}
	
		return ( isset( $QuicktimeAudioCodecLookup["$codecid"] )? $QuicktimeAudioCodecLookup["$codecid"] : '' );
	}

	function quicktimeDCOMLookup( $compressionid ) 
	{
		static $QuicktimeDCOMLookup = array();
	
		if ( count( $QuicktimeDCOMLookup ) < 1 ) 
		{
			$QuicktimeDCOMLookup['zlib'] = 'ZLib Deflate';
			$QuicktimeDCOMLookup['adec'] = 'Apple Compression';
		}
	
		return ( isset( $QuicktimeDCOMLookup["$compressionid"] )? $QuicktimeDCOMLookup["$compressionid"] : '' );
	}

	function quicktimeColorNameLookup( $colordepthid ) 
	{
		static $QuicktimeColorNameLookup = array();
	
		if ( count( $QuicktimeColorNameLookup) < 1 ) 
		{
			$QuicktimeColorNameLookup[1]  = '2-color (monochrome)';
			$QuicktimeColorNameLookup[2]  = '4-color';
			$QuicktimeColorNameLookup[4]  = '16-color';
			$QuicktimeColorNameLookup[8]  = '256-color';
			$QuicktimeColorNameLookup[16] = 'thousands (16-bit color)';
			$QuicktimeColorNameLookup[24] = 'millions (24-bit color)';
			$QuicktimeColorNameLookup[32] = 'millions+ (32-bit color)';
			$QuicktimeColorNameLookup[33] = 'black & white';
			$QuicktimeColorNameLookup[34] = '4-gray';
			$QuicktimeColorNameLookup[36] = '16-gray';
			$QuicktimeColorNameLookup[40] = '256-gray';
		}
	
		return ( isset( $QuicktimeColorNameLookup["$colordepthid"] )? $QuicktimeColorNameLookup["$colordepthid"] : 'invalid' );
	}

	function pascalToString( $pascalstring ) 
	{
		// Pascal strings have 1 byte at the beginning saying how many chars are in the string
		return substr( $pascalstring, 1 );
	}
} // END OF ID3_Quicktime

?>
