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
 
class ID3_ASF extends ID3
{
	function getASFHeaderFilepointer( &$fd, &$MP3fileInfo ) 
	{
		ID3_ASF::setConstants();
		
		// ASF structure:
		// * Header Object [required]
		//   * File Properties Object [required]   (global file attributes)
		//   * Stream Properties Object [required] (defines media stream & characteristics)
		//   * Header Extension Object [required]  (additional functionality)
		//   * Content Description Object          (bibliographic information)
		//   * Script Command Object               (commands for during playback)
		//   * Marker Object                       (named jumped points within the file)
		// * Data Object [required]
		//   * Data Packets
		// * Index Object

		// Header Object: (mandatory, one only)
		// Field Name                   Field Type   Size (bits)
		// Object ID                    GUID         128             // GUID for header object - ID3_ASF_Header_Object
		// Object Size                  QWORD        64              // size of header object, including 30 bytes of Header Object header
		// Number of Header Objects     DWORD        32              // number of objects in header object
		// Reserved1                    BYTE         8               // hardcoded: 0x01
		// Reserved2                    BYTE         8               // hardcoded: 0x02

		$MP3fileInfo['fileformat']   = 'asf';
		$MP3fileInfo['bitrate_mode'] = 'cbr'; // set to VBR in "extended_content_description" if neccesary

		rewind( $fd );
		$HeaderObjectData = fread( $fd, 30 );

		$MP3fileInfo['asf']['header_object']['objectid']      = substr( $HeaderObjectData, 0, 16 );
		$MP3fileInfo['asf']['header_object']['objectid_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['header_object']['objectid'] );
	
		if ( $MP3fileInfo['asf']['header_object']['objectid'] != ID3_ASF_Header_Object ) 
		{
			$MP3fileInfo['error'][] = 'ASF header GUID {' . ID3::bytestringToGUID( $MP3fileInfo['asf']['header_object']['objectid'] ) . '} does not match expected "ID3_ASF_Header_Object" GUID (' . ID3::bytestringToGUID( ID3_ASF_Header_Object ) . ').';
			// return false;
			break;
		}

		$MP3fileInfo['asf']['header_object']['objectsize']    = ID3::littleEndianToInt( substr( $HeaderObjectData, 16, 8 ) );
		$MP3fileInfo['asf']['header_object']['headerobjects'] = ID3::littleEndianToInt( substr( $HeaderObjectData, 24, 4 ) );
		$MP3fileInfo['asf']['header_object']['reserved1']     = ID3::littleEndianToInt( substr( $HeaderObjectData, 28, 1 ) );
		$MP3fileInfo['asf']['header_object']['reserved2']     = ID3::littleEndianToInt( substr( $HeaderObjectData, 29, 1 ) );

		$ASFHeaderData = fread( $fd, $MP3fileInfo['asf']['header_object']['objectsize'] - 30 );
		$offset = 0;

		for ( $HeaderObjectsCounter = 0; $HeaderObjectsCounter < $MP3fileInfo['asf']['header_object']['headerobjects']; $HeaderObjectsCounter++ ) 
		{
			$NextObjectGUID = substr( $ASFHeaderData, $offset, 16 );
			$offset += 16;
			$NextObjectGUIDtext = ID3::bytestringToGUID( $NextObjectGUID );
			$NextObjectSize = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
			$offset += 8;
		
			switch ( $NextObjectGUID )
			{
				case ID3_ASF_File_Properties_Object:
					// File Properties Object: (mandatory, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for file properties object - ID3_ASF_File_Properties_Object
					// Object Size                  QWORD        64              // size of file properties object, including 104 bytes of File Properties Object header
					// File ID                      GUID         128             // unique ID - identical to File ID in Data Object
					// File Size                    QWORD        64              // entire file in bytes. Invalid if Broadcast Flag == 1
					// Creation Date                QWORD        64              // date & time of file creation. Maybe invalid if Broadcast Flag == 1
					// Data Packets Count           QWORD        64              // number of data packets in Data Object. Invalid if Broadcast Flag == 1
					// Play Duration                QWORD        64              // playtime, in 100-nanosecond units. Invalid if Broadcast Flag == 1
					// Send Duration                QWORD        64              // time needed to send file, in 100-nanosecond units. Players can ignore this value. Invalid if Broadcast Flag == 1
					// Preroll                      QWORD        64              // time to buffer data before starting to play file, in 1-millisecond units. If <> 0, PlayDuration and PresentationTime have been offset by this amount
					// Flags                        DWORD        32              //
					// * Broadcast Flag             bits         1  (0x01)       // file is currently being written, some header values are invalid
					// * Seekable Flag              bits         1  (0x02)       // is file seekable
					// * Reserved                   bits         30 (0xFFFFFFFC) // reserved - set to zero
					// Minimum Data Packet Size     DWORD        32              // in bytes. should be same as Maximum Data Packet Size. Invalid if Broadcast Flag == 1
					// Maximum Data Packet Size     DWORD        32              // in bytes. should be same as Minimum Data Packet Size. Invalid if Broadcast Flag == 1
					// Maximum Bitrate              DWORD        32              // maximum instantaneous bitrate in bits per second for entire file, including all data streams and ASF overhead

					$MP3fileInfo['asf']['file_properties_object']['objectid']           = $NextObjectGUID;
					$MP3fileInfo['asf']['file_properties_object']['objectid_guid']      = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['file_properties_object']['objectsize']         = $NextObjectSize;
					$MP3fileInfo['asf']['file_properties_object']['fileid']             = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$MP3fileInfo['asf']['file_properties_object']['fileid_guid']        = ID3::bytestringToGUID( $MP3fileInfo['asf']['file_properties_object']['fileid'] );
					$MP3fileInfo['asf']['file_properties_object']['filesize']           = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['asf']['file_properties_object']['creation_date']      = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$MP3fileInfo['asf']['file_properties_object']['creation_date_unix'] = ID3::filetimeToUnixtime( $MP3fileInfo['asf']['file_properties_object']['creation_date'] );
					$offset += 8;
					$MP3fileInfo['asf']['file_properties_object']['data_packets']       = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['asf']['file_properties_object']['play_duration']      = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['asf']['file_properties_object']['send_duration']      = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['asf']['file_properties_object']['preroll']            = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['playtime_seconds'] = ( $MP3fileInfo['asf']['file_properties_object']['play_duration'] / 10000000 ) - ( $MP3fileInfo['asf']['file_properties_object']['preroll'] / 1000 );
					$MP3fileInfo['asf']['file_properties_object']['flags_raw']          = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['asf']['file_properties_object']['flags']['broadcast'] = (bool)( $MP3fileInfo['asf']['file_properties_object']['flags_raw'] & 0x0001 );
					$MP3fileInfo['asf']['file_properties_object']['flags']['seekable']  = (bool)( $MP3fileInfo['asf']['file_properties_object']['flags_raw'] & 0x0002 );
					$MP3fileInfo['asf']['file_properties_object']['min_packet_size']    = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['asf']['file_properties_object']['max_packet_size']    = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['asf']['file_properties_object']['max_bitrate']        = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['bitrate']                                             = $MP3fileInfo['asf']['file_properties_object']['max_bitrate'];
	
					break;

				case ID3_ASF_Stream_Properties_Object:
					// Stream Properties Object: (mandatory, one per media stream)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for stream properties object - ID3_ASF_Stream_Properties_Object
					// Object Size                  QWORD        64              // size of stream properties object, including 78 bytes of Stream Properties Object header
					// Stream Type                  GUID         128             // ID3_ASF_Audio_Media, ID3_ASF_Video_Media or ID3_ASF_Command_Media
					// Error Correction Type        GUID         128             // ID3_ASF_Audio_Spread for audio-only streams, ID3_ASF_No_Error_Correction for other stream types
					// Time Offset                  QWORD        64              // 100-nanosecond units. typically zero. added to all timestamps of samples in the stream
					// Type-Specific Data Length    DWORD        32              // number of bytes for Type-Specific Data field
					// Error Correction Data Length DWORD        32              // number of bytes for Error Correction Data field
					// Flags                        WORD         16              //
					// * Stream Number              bits         7 (0x007F)      // number of this stream.  1 <= valid <= 127
					// * Reserved                   bits         8 (0x7F80)      // reserved - set to zero
					// * Encrypted Content Flag     bits         1 (0x8000)      // stream contents encrypted if set
					// Reserved                     DWORD        32              // reserved - set to zero
					// Type-Specific Data           BYTESTREAM   variable        // type-specific format data, depending on value of Stream Type
					// Error Correction Data        BYTESTREAM   variable        // error-correction-specific format data, depending on value of Error Correct Type

					// There is one ID3_ASF_Stream_Properties_Object for each stream (audio, video) but the
					// stream number isn't known until halfway through decoding the structure, hence it
					// it is decoded to a temporary variable and then stuck in the appropriate index later

					$StreamPropertiesObjectData['objectid']           = $NextObjectGUID;
					$StreamPropertiesObjectData['objectid_guid']      = $NextObjectGUIDtext;
					$StreamPropertiesObjectData['objectsize']         = $NextObjectSize;
					$StreamPropertiesObjectData['stream_type']        = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$StreamPropertiesObjectData['stream_type_guid']   = ID3::bytestringToGUID( $StreamPropertiesObjectData['stream_type'] );
					$StreamPropertiesObjectData['error_correct_type'] = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$StreamPropertiesObjectData['error_correct_guid'] = ID3::bytestringToGUID( $StreamPropertiesObjectData['error_correct_type'] );
					$StreamPropertiesObjectData['time_offset']        = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
					$offset += 8;
					$StreamPropertiesObjectData['type_data_length']   = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$StreamPropertiesObjectData['error_data_length']  = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$StreamPropertiesObjectData['flags_raw']          = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$StreamPropertiesObjectStreamNumber               = $StreamPropertiesObjectData['flags_raw'] & 0x007F;
					$StreamPropertiesObjectData['flags']['encrypted'] = (bool)( $StreamPropertiesObjectData['flags_raw'] & 0x8000 );
					$offset += 4; // reserved - DWORD
					$StreamPropertiesObjectData['type_specific_data'] = substr( $ASFHeaderData, $offset, $StreamPropertiesObjectData['type_data_length'] );
					$offset += $StreamPropertiesObjectData['type_data_length'];
					$StreamPropertiesObjectData['error_correct_data'] = substr( $ASFHeaderData, $offset, $StreamPropertiesObjectData['error_data_length'] );
					$offset += $StreamPropertiesObjectData['error_data_length'];
					$MP3fileInfo['asf']['stream_properties_object']["$StreamPropertiesObjectStreamNumber"] = $StreamPropertiesObjectData;
					unset( $StreamPropertiesObjectData ); // clear for next stream, if any
	
					break;

				case ID3_ASF_Header_Extension_Object:
					// Header Extension Object: (mandatory, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Header Extension object - ID3_ASF_Header_Extension_Object
					// Object Size                  QWORD        64              // size of Header Extension object, including 46 bytes of Header Extension Object header
					// Reserved Field 1             GUID         128             // hardcoded: ID3_ASF_Reserved_1
					// Reserved Field 2             WORD         16              // hardcoded: 0x00000006
					// Header Extension Data Size   DWORD        32              // in bytes. valid: 0, or > 24. equals object size minus 46
					// Header Extension Data        BYTESTREAM   variable        // array of zero or more extended header objects

					$MP3fileInfo['asf']['header_extension_object']['objectid']        = $NextObjectGUID;
					$MP3fileInfo['asf']['header_extension_object']['objectid_guid']   = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['header_extension_object']['objectsize']      = $NextObjectSize;
					$MP3fileInfo['asf']['header_extension_object']['reserved_1']      = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['header_extension_object']['reserved_1_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['header_extension_object']['reserved_1'] );
	
					if ( $MP3fileInfo['asf']['header_extension_object']['reserved_1'] != ID3_ASF_Reserved_1 ) 
					{
						$MP3fileInfo['error'][] = 'header_extension_object.reserved_1 GUID (' . ID3::bytestringToGUID( $MP3fileInfo['asf']['header_extension_object']['reserved_1'] ) . ') does not match expected "ID3_ASF_Reserved_1" GUID (' . ID3::bytestringToGUID( ID3_ASF_Reserved_1 ) . ').';
						//return false;
						break;
					}
	
					$MP3fileInfo['asf']['header_extension_object']['reserved_2'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
	
					if ( $MP3fileInfo['asf']['header_extension_object']['reserved_2'] != 6 ) 
					{
						$MP3fileInfo['error'][] = 'header_extension_object.reserved_2 (' . ID3::printHexBytes( $MP3fileInfo['asf']['header_extension_object']['reserved_2'] ) . ') does not match expected value of "6".';
						// return false;
						break;
					}
		
					$MP3fileInfo['asf']['header_extension_object']['extension_data_size'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['asf']['header_extension_object']['extension_data']      = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['header_extension_object']['extension_data_size'] ) );
					$offset += $MP3fileInfo['asf']['header_extension_object']['extension_data_size'];
			
					break;

				case ID3_ASF_Codec_List_Object:
					// Codec List Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Codec List object - ID3_ASF_Codec_List_Object
					// Object Size                  QWORD        64              // size of Codec List object, including 44 bytes of Codec List Object header
					// Reserved                     GUID         128             // hardcoded: 86D15241-311D-11D0-A3A4-00A0C90348F6
					// Codec Entries Count          DWORD        32              // number of entries in Codec Entries array
					// Codec Entries                array of:    variable        //
					// * Type                       WORD         16              // 0x0001 = Video Codec, 0x0002 = Audio Codec, 0xFFFF = Unknown Codec
					// * Codec Name Length          WORD         16              // number of Unicode characters stored in the Codec Name field
					// * Codec Name                 WCHAR        variable        // array of Unicode characters - name of codec used to create the content
					// * Codec Description Length   WORD         16              // number of Unicode characters stored in the Codec Description field
					// * Codec Description          WCHAR        variable        // array of Unicode characters - description of format used to create the content
					// * Codec Information Length   WORD         16              // number of Unicode characters stored in the Codec Information field
					// * Codec Information          BYTESTREAM   variable        // opaque array of information bytes about the codec used to create the content

					$MP3fileInfo['asf']['codec_list']['objectid']      = $NextObjectGUID;
					$MP3fileInfo['asf']['codec_list']['objectid_guid'] = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['codec_list']['objectsize']    = $NextObjectSize;
					$MP3fileInfo['asf']['codec_list']['reserved']      = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['codec_list']['reserved_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['codec_list']['reserved'] );
	
					if ( $MP3fileInfo['asf']['codec_list']['reserved'] != ID3::GUIDToBytestring( '86D15241-311D-11D0-A3A4-00A0C90348F6' ) ) 
					{
						$MP3fileInfo['error'][] = 'codec_list_object.reserved GUID {' . ID3::bytestringToGUID( $MP3fileInfo['asf']['codec_list']['reserved'] ) . '} does not match expected "ID3_ASF_Reserved_1" GUID (86D15241-311D-11D0-A3A4-00A0C90348F6).';
						// return false;
						break;
					}
					
					$MP3fileInfo['asf']['codec_list']['codec_entries_count'] = ID3::littleEndianToInt(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
				
					for ( $CodecEntryCounter = 0; $CodecEntryCounter < $MP3fileInfo['asf']['codec_list']['codec_entries_count']; $CodecEntryCounter++ ) 
					{
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['type_raw'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['type']     = ID3_ASF::ASFCodecListObjectTypeLookup( $MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['type_raw'] );

						$CodecNameLength = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) ) * 2; // 2 bytes per character
						$offset += 2;
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['name'] = substr( $ASFHeaderData, $offset, $CodecNameLength );
						$offset += $CodecNameLength;
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['name_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['name'], 2 );

						$CodecDescriptionLength = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) ) * 2; // 2 bytes per character
						$offset += 2;
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['description'] = substr( $ASFHeaderData, $offset, $CodecDescriptionLength );
						$offset += $CodecDescriptionLength;
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['description_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['description'], 2 );

						$CodecInformationLength = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['information'] = substr( $ASFHeaderData, $offset, $CodecInformationLength );
						$offset += $CodecInformationLength;

						if ( $MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['type_raw'] == 2 ) 
						{
							// audio codec
							list( $AudioCodecBitrate, $AudioCodecFrequency, $AudioCodecChannels ) = explode( ',', $MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['description_ascii'] );

							if ( !isset( $MP3fileInfo['bitrate_audio'] ) && strstr( $AudioCodecBitrate, 'kbps' ) )
								$MP3fileInfo['bitrate_audio'] = (int)( trim( str_replace( 'kbps', '', $AudioCodecBitrate ) ) * 1000 );
						
							if ( !isset( $MP3fileInfo['bitrate_video'] ) && isset( $MP3fileInfo['bitrate_audio'] ) && isset( $MP3fileInfo['asf']['file_properties_object']['max_bitrate'] ) && ( $MP3fileInfo['asf']['codec_list']['codec_entries_count'] > 1 ) )
								$MP3fileInfo['bitrate_video'] = $MP3fileInfo['asf']['file_properties_object']['max_bitrate'] - $MP3fileInfo['bitrate_audio'];
						
							$AudioCodecFrequency = (int)trim( str_replace( 'kHz', '', $AudioCodecFrequency ) );
						
							switch ( $AudioCodecFrequency ) 
							{
								case 8:
									$MP3fileInfo['frequency'] = 8000;
									break;

								case 11:
									$MP3fileInfo['frequency'] = 11025;
									break;

								case 16:
									$MP3fileInfo['frequency'] = 16000;
									break;

								case 22:
									$MP3fileInfo['frequency'] = 22050;
									break;

								case 32:
									$MP3fileInfo['frequency'] = 32000;
									break;

								case 44:
									$MP3fileInfo['frequency'] = 44100;
									break;

								case 48:
									$MP3fileInfo['frequency'] = 48000;
									break;

								default:
									$MP3fileInfo['error'][] = 'Unknown frequency: ' . $MP3fileInfo['asf']['codec_list']['codec_entries']["$CodecEntryCounter"]['description_ascii'] . '.';
									break;
							}

							if ( !isset( $MP3fileInfo['channels'] ) ) 
							{
								if ( strstr( $AudioCodecChannels, 'stereo' ) )
									$MP3fileInfo['channels'] = 2;
								else if ( strstr( $AudioCodecChannels, 'mono' ) )
									$MP3fileInfo['channels'] = 1;
							}
						}
					}
				
					break;

				case ID3_ASF_Script_Command_Object:
					// Script Command Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Script Command object - ID3_ASF_Script_Command_Object
					// Object Size                  QWORD        64              // size of Script Command object, including 44 bytes of Script Command Object header
					// Reserved                     GUID         128             // hardcoded: 4B1ACBE3-100B-11D0-A39B-00A0C90348F6
					// Commands Count               WORD         16              // number of Commands structures in the Script Commands Objects
					// Command Types Count          WORD         16              // number of Command Types structures in the Script Commands Objects
					// Command Types                array of:    variable        //
					// * Command Type Name Length   WORD         16              // number of Unicode characters for Command Type Name
					// * Command Type Name          WCHAR        variable        // array of Unicode characters - name of a type of command
					// Commands                     array of:    variable        //
					// * Presentation Time          DWORD        32              // presentation time of that command, in milliseconds
					// * Type Index                 WORD         16              // type of this command, as a zero-based index into the array of Command Types of this object
					// * Command Name Length        WORD         16              // number of Unicode characters for Command Name
					// * Command Name               WCHAR        variable        // array of Unicode characters - name of this command

					$MP3fileInfo['asf']['script_command_object']['objectid']      = $NextObjectGUID;
					$MP3fileInfo['asf']['script_command_object']['objectid_guid'] = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['script_command_object']['objectsize']    = $NextObjectSize;
					$MP3fileInfo['asf']['script_command_object']['reserved']      = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['script_command_object']['reserved_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['script_command_object']['reserved'] );
	
					if ( $MP3fileInfo['asf']['script_command_object']['reserved'] != ID3::GUIDToBytestring( '4B1ACBE3-100B-11D0-A39B-00A0C90348F6' ) ) 
					{
						$MP3fileInfo['error'][] = 'script_command_object.reserved GUID {' . ID3::bytestringToGUID( $MP3fileInfo['asf']['script_command_object']['reserved'] ) . '} does not match expected "ID3_ASF_Reserved_1" GUID (4B1ACBE3-100B-11D0-A39B-00A0C90348F6).';
						// return false;
						break;
					}
					
					$MP3fileInfo['asf']['script_command_object']['commands_count'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['script_command_object']['command_types_count'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
		
					for ( $CommandTypesCounter = 0; $CommandTypesCounter < $MP3fileInfo['asf']['script_command_object']['command_types_count']; $CommandTypesCounter++ ) 
					{
						$CommandTypeNameLength = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) ) * 2; // 2 bytes per character
						$offset += 2;
						$MP3fileInfo['asf']['script_command_object']['command_types']["$CommandTypesCounter"]['name'] = substr( $ASFHeaderData, $offset, $CommandTypeNameLength );
						$offset += $CommandTypeNameLength;
						$MP3fileInfo['asf']['script_command_object']['command_types']["$CommandTypesCounter"]['name_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['script_command_object']['command_types']["$CommandTypesCounter"]['name'], 2 );
					}
				
					for ( $CommandsCounter = 0; $CommandsCounter < $MP3fileInfo['asf']['script_command_object']['commands_count']; $CommandsCounter++ ) 
					{
						$MP3fileInfo['asf']['script_command_object']['commands']["$CommandsCounter"]['presentation_time']  = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['asf']['script_command_object']['commands']["$CommandsCounter"]['type_index']         = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;

						$CommandTypeNameLength = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) ) * 2; // 2 bytes per character
						$offset += 2;
						$MP3fileInfo['asf']['script_command_object']['commands']["$CommandsCounter"]['name'] = substr( $ASFHeaderData, $offset, $CommandTypeNameLength );
						$offset += $CommandTypeNameLength;
						$MP3fileInfo['asf']['script_command_object']['commands']["$CommandsCounter"]['name_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['script_command_object']['commands']["$CommandsCounter"]['name'], 2 );
					}
	
					break;

				case ID3_ASF_Marker_Object:
					// Marker Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Marker object - ID3_ASF_Marker_Object
					// Object Size                  QWORD        64              // size of Marker object, including 48 bytes of Marker Object header
					// Reserved                     GUID         128             // hardcoded: 4CFEDB20-75F6-11CF-9C0F-00A0C90349CB
					// Markers Count                DWORD        32              // number of Marker structures in Marker Object
					// Reserved                     WORD         16              // hardcoded: 0x0000
					// Name Length                  WORD         16              // number of bytes in the Name field
					// Name                         WCHAR        variable        // name of the Marker Object
					// Markers                      array of:    variable        //
					// * Offset                     QWORD        64              // byte offset into Data Object
					// * Presentation Time          QWORD        64              // in 100-nanosecond units
					// * Entry Length               WORD         16              // length in bytes of (Send Time + Flags + Marker Description Length + Marker Description + Padding)
					// * Send Time                  DWORD        32              // in milliseconds
					// * Flags                      DWORD        32              // hardcoded: 0x00000000
					// * Marker Description Length  DWORD        32              // number of bytes in Marker Description field
					// * Marker Description         WCHAR        variable        // array of Unicode characters - description of marker entry
					// * Padding                    BYTESTREAM   variable        // optional padding bytes

					$MP3fileInfo['asf']['marker_object']['objectid']      = $NextObjectGUID;
					$MP3fileInfo['asf']['marker_object']['objectid_guid'] = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['marker_object']['objectsize']    = $NextObjectSize;
					$MP3fileInfo['asf']['marker_object']['reserved']      = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['marker_object']['reserved_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['marker_object']['reserved'] );

					if ( $MP3fileInfo['asf']['marker_object']['reserved'] != ID3::GUIDToBytestring( '4CFEDB20-75F6-11CF-9C0F-00A0C90349CB' ) ) 
					{
						$MP3fileInfo['error'][] = 'marker_object.reserved GUID {' . ID3::bytestringToGUID( $MP3fileInfo['asf']['marker_object']['reserved_1'] ) . '} does not match expected "ID3_ASF_Reserved_1" GUID (4CFEDB20-75F6-11CF-9C0F-00A0C90349CB).';
						// return false;
						break;
					}
				
					$MP3fileInfo['asf']['marker_object']['markers_count'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['asf']['marker_object']['reserved_2'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
		
					if ( $MP3fileInfo['asf']['marker_object']['reserved_2'] != 0 ) 
					{
						$MP3fileInfo['error'][] = 'marker_object.reserved_2 (' . ID3::printHexBytes( $MP3fileInfo['asf']['marker_object']['reserved_2'] ) . ') does not match expected value of "0".';
						// return false;
						break;
					}
					
					$MP3fileInfo['asf']['marker_object']['name_length'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['marker_object']['name'] = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['marker_object']['name_length'] );
					$offset += $MP3fileInfo['asf']['marker_object']['name_length'];
					$MP3fileInfo['asf']['marker_object']['name_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['marker_object']['name'], 2 );
	
					for ( $MarkersCounter = 0; $MarkersCounter < $MP3fileInfo['asf']['marker_object']['markers_count']; $MarkersCounter++ ) 
					{
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['offset']                    = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
						$offset += 8;
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['presentation_time']         = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 8 ) );
						$offset += 8;
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['entry_length']              = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['send_time']                 = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['flags']                     = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description_length'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description']        = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description_length'] );
						$offset += $MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description_length'];
						$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description_ascii']  = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description'], 2 );
						$PaddingLength = $MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['entry_length'] - 4 - 4 - 4 - $MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['marker_description_length'];
		
						if ( $PaddingLength > 0 ) 
						{
							$MP3fileInfo['asf']['marker_object']['markers']["$MarkersCounter"]['padding']               = substr($ASFHeaderData, $offset, $PaddingLength);
							$offset += $PaddingLength;
						}
					}
				
					break;

				case ID3_ASF_Bitrate_Mutual_Exclusion_Object:
					// Bitrate Mutual Exclusion Object: (optional)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Bitrate Mutual Exclusion object - ID3_ASF_Bitrate_Mutual_Exclusion_Object
					// Object Size                  QWORD        64              // size of Bitrate Mutual Exclusion object, including 42 bytes of Bitrate Mutual Exclusion Object header
					// Exlusion Type                GUID         128             // nature of mutual exclusion relationship. one of: (ID3_ASF_Mutex_Bitrate, ID3_ASF_Mutex_Unknown)
					// Stream Numbers Count         WORD         16              // number of video streams
					// Stream Numbers               WORD         variable        // array of mutually exclusive video stream numbers. 1 <= valid <= 127

					$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['objectid']      = $NextObjectGUID;
					$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['objectid_guid'] = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['objectsize']    = $NextObjectSize;
					$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['reserved']      = substr( $ASFHeaderData, $offset, 16 );
					$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['reserved_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['reserved'] );
					$offset += 16;
	
					if ( ( $MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['reserved'] != ID3_ASF_Mutex_Bitrate) && ($MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['reserved'] != ID3_ASF_Mutex_Unknown ) ) 
					{
						$MP3fileInfo['error'][] = 'bitrate_mutual_exclusion_object.reserved GUID {' . ID3::bytestringToGUID( $MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['reserved'] ) . '} does not match expected "ID3_ASF_Mutex_Bitrate" GUID (' . ID3::bytestringToGUID( ID3_ASF_Mutex_Bitrate ) . ') or "ID3_ASF_Mutex_Unknown" GUID ('.ID3::bytestringToGUID( ID3_ASF_Mutex_Unknown ) . ').';
						// return false;
						break;
					}

					$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['stream_numbers_count'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;

					for ( $StreamNumberCounter = 0; $StreamNumberCounter < $MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['markers_count']; $StreamNumberCounter++ ) 
					{
						$MP3fileInfo['asf']['bitrate_mutual_exclusion_object']['stream_numbers']["$StreamNumberCounter"] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
					}
		
					break;

				case ID3_ASF_Error_Correction_Object:
					// Error Correction Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Error Correction object - ID3_ASF_Error_Correction_Object
					// Object Size                  QWORD        64              // size of Error Correction object, including 44 bytes of Error Correction Object header
					// Error Correction Type        GUID         128             // type of error correction. one of: (ID3_ASF_No_Error_Correction, ID3_ASF_Audio_Spread)
					// Error Correction Data Length DWORD        32              // number of bytes in Error Correction Data field
					// Error Correction Data        BYTESTREAM   variable        // structure depends on value of Error Correction Type field

					$MP3fileInfo['asf']['error_correction_object']['objectid']              = $NextObjectGUID;
					$MP3fileInfo['asf']['error_correction_object']['objectid_guid']         = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['error_correction_object']['objectsize']            = $NextObjectSize;
					$MP3fileInfo['asf']['error_correction_object']['error_correction_type'] = substr( $ASFHeaderData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['error_correction_object']['error_correction_guid'] = ID3::bytestringToGUID( $MP3fileInfo['asf']['error_correction_object']['error_correction_type'] );
					$MP3fileInfo['asf']['error_correction_object']['error_correction_data_length'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
					$offset += 4;
	
					switch ( $MP3fileInfo['asf']['error_correction_object']['error_correction_type'] ) 
					{
						case ID3_ASF_No_Error_Correction:
							// should be no data, but just in case there is, skip to the end of the field
							$offset += $MP3fileInfo['asf']['error_correction_object']['error_correction_data_length'];
							break;

						case ID3_ASF_Audio_Spread:
							// Field Name                   Field Type   Size (bits)
							// Span                         BYTE         8               // number of packets over which audio will be spread.
							// Virtual Packet Length        WORD         16              // size of largest audio payload found in audio stream
							// Virtual Chunk Length         WORD         16              // size of largest audio payload found in audio stream
							// Silence Data Length          WORD         16              // number of bytes in Silence Data field
							// Silence Data                 BYTESTREAM   variable        // hardcoded: 0x00 * (Silence Data Length) bytes

							$MP3fileInfo['asf']['error_correction_object']['span']                  = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 1 ) );
							$offset += 1;
							$MP3fileInfo['asf']['error_correction_object']['virtual_packet_length'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
							$offset += 2;
							$MP3fileInfo['asf']['error_correction_object']['virtual_chunk_length']  = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
							$offset += 2;
							$MP3fileInfo['asf']['error_correction_object']['silence_data_length']   = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
							$offset += 2;
							$MP3fileInfo['asf']['error_correction_object']['silence_data']          = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['error_correction_object']['silence_data_length'] );
							$offset += $MP3fileInfo['asf']['error_correction_object']['silence_data_length'];
		
							break;

						default:
							$MP3fileInfo['error'][] = 'error_correction_object.error_correction_type GUID ('.ID3::bytestringToGUID( $MP3fileInfo['asf']['error_correction_object']['reserved'] ).') does not match expected "ID3_ASF_No_Error_Correction" GUID (' . ID3::bytestringToGUID( ID3_ASF_No_Error_Correction ) . ') or "ID3_ASF_Audio_Spread" GUID (' . ID3::bytestringToGUID( ID3_ASF_Audio_Spread ) . ').';
							// return false;
							break;
					}

					break;

				case ID3_ASF_Content_Description_Object:
					// Content Description Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Content Description object - ID3_ASF_Content_Description_Object
					// Object Size                  QWORD        64              // size of Content Description object, including 34 bytes of Content Description Object header
					// Title Length                 WORD         16              // number of bytes in Title field
					// Author Length                WORD         16              // number of bytes in Author field
					// Copyright Length             WORD         16              // number of bytes in Copyright field
					// Description Length           WORD         16              // number of bytes in Description field
					// Rating Length                WORD         16              // number of bytes in Rating field
					// Title                        WCHAR        16              // array of Unicode characters - Title
					// Author                       WCHAR        16              // array of Unicode characters - Author
					// Copyright                    WCHAR        16              // array of Unicode characters - Copyright
					// Description                  WCHAR        16              // array of Unicode characters - Description
					// Rating                       WCHAR        16              // array of Unicode characters - Rating

					$MP3fileInfo['asf']['content_description']['objectid']              = $NextObjectGUID;
					$MP3fileInfo['asf']['content_description']['objectid_guid']         = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['content_description']['objectsize']            = $NextObjectSize;
					$MP3fileInfo['asf']['content_description']['title_length']          = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['content_description']['author_length']         = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['content_description']['copyright_length']      = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['content_description']['description_length']    = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['content_description']['rating_length']         = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
					$MP3fileInfo['asf']['content_description']['title']                 = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['content_description']['title_length'] );
					$offset += $MP3fileInfo['asf']['content_description']['title_length'];
					$MP3fileInfo['asf']['content_description']['title_ascii']           = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['content_description']['title'], 2 );
					$MP3fileInfo['asf']['content_description']['author']                = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['content_description']['author_length'] );
					$offset += $MP3fileInfo['asf']['content_description']['author_length'];
					$MP3fileInfo['asf']['content_description']['author_ascii']          = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['content_description']['author'], 2 );
					$MP3fileInfo['asf']['content_description']['copyright']             = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['content_description']['copyright_length'] );
					$offset += $MP3fileInfo['asf']['content_description']['copyright_length'];
					$MP3fileInfo['asf']['content_description']['copyright_ascii']       = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['content_description']['copyright'], 2 );
					$MP3fileInfo['asf']['content_description']['description']           = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['content_description']['description_length'] );
					$offset += $MP3fileInfo['asf']['content_description']['description_length'];
					$MP3fileInfo['asf']['content_description']['description_ascii']     = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['content_description']['description'], 2 );
					$MP3fileInfo['asf']['content_description']['rating']                = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['content_description']['rating_length'] );
					$offset += $MP3fileInfo['asf']['content_description']['rating_length'];
					$MP3fileInfo['asf']['content_description']['rating_ascii']          = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['content_description']['rating'], 2 );

					if ( $MP3fileInfo['asf']['content_description']['title_ascii'] )
						$MP3fileInfo['asf']['title'] = $MP3fileInfo['asf']['content_description']['title_ascii'];
				
					if ( $MP3fileInfo['asf']['content_description']['author_ascii'] )
						$MP3fileInfo['asf']['artist'] = $MP3fileInfo['asf']['content_description']['author_ascii'];
				
					if ( $MP3fileInfo['asf']['content_description']['description_ascii'] )
						$MP3fileInfo['asf']['comment'] = $MP3fileInfo['asf']['content_description']['description_ascii'];

					break;

				case ID3_ASF_Extended_Content_Description_Object:
					// Extended Content Description Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Extended Content Description object - ID3_ASF_Extended_Content_Description_Object
					// Object Size                  QWORD        64              // size of ExtendedContent Description object, including 26 bytes of Extended Content Description Object header
					// Content Descriptors Count    WORD         16              // number of entries in Content Descriptors list
					// Content Descriptors          array of:    variable        //
					// * Descriptor Name Length     WORD         16              // size in bytes of Descriptor Name field
					// * Descriptor Name            WCHAR        variable        // array of Unicode characters - Descriptor Name
					// * Descriptor Value Data Type WORD         16              // Lookup array:
					                                                             // 0x0000 = Unicode String (variable length)
					                                                             // 0x0001 = BYTE array     (variable length)
					                                                             // 0x0002 = BOOL           (DWORD, 32 bits)
					                                                             // 0x0003 = DWORD          (DWORD, 32 bits)
					                                                             // 0x0004 = QWORD          (QWORD, 64 bits)
				    	                                                         // 0x0005 = WORD           (WORD,  16 bits)
					// * Descriptor Value Length    WORD         16              // number of bytes stored in Descriptor Value field
					// * Descriptor Value           variable     variable        // value for Content Descriptor

					$MP3fileInfo['asf']['extended_content_description']['objectid']                  = $NextObjectGUID;
					$MP3fileInfo['asf']['extended_content_description']['objectid_guid']             = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['extended_content_description']['objectsize']                = $NextObjectSize;
					$MP3fileInfo['asf']['extended_content_description']['content_descriptors_count'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
	
					for ( $ExtendedContentDescriptorsCounter = 0; $ExtendedContentDescriptorsCounter < $MP3fileInfo['asf']['extended_content_description']['content_descriptors_count']; $ExtendedContentDescriptorsCounter++ ) 
					{
						$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name_length']  = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name']         = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name_length'] );
						$offset += $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name_length'];
						$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name_ascii']   = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name'], 2 );
						$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_type']   = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_length'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value']        = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_length'] );
						$offset += $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_length'];
			
						switch ( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_type'] ) 
						{
							case 0x0000: // Unicode string
								$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_ascii'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'], 2 );
								break;

							case 0x0001: // BYTE array
								// do nothing
								break;

							case 0x0002: // BOOL
								$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'] = (bool)ID3::littleEndianToInt( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'] );
								break;

							case 0x0003: // DWORD
	
							case 0x0004: // QWORD
		
							case 0x0005: // WORD
								$MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'] = ID3::littleEndianToInt( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'] );
								break;

							default:
								$MP3fileInfo['error'][] = 'extended_content_description.content_descriptors.' . $ExtendedContentDescriptorsCounter . '.value_type is invalid (' . $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_type'] . ').';
								//return false;
								break;
						}

						switch ( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['name_ascii'] ) 
						{
							case 'WM/AlbumTitle':
								$MP3fileInfo['asf']['album'] = $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_ascii'];
								break;

							case 'WM/Genre':
								$MP3fileInfo['asf']['genre'] = $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_ascii'];
								$CleanedGenre = ID3::lookupGenre( ID3::lookupGenre( $MP3fileInfo['asf']['genre'], true ) ); // convert to standard GenreID and back to standard spelling/capitalization
			
								if ( $CleanedGenre != $MP3fileInfo['asf']['genre'] )
									$MP3fileInfo['asf']['genre'] = $CleanedGenre;
							
								break;

							case 'WM/TrackNumber':
								$MP3fileInfo['asf']['track'] = $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'];
								break;

							case 'WM/Track':
								if ( !isset( $MP3fileInfo['asf']['track'] ) )
									$MP3fileInfo['asf']['track'] = 1 + $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'];
							
								break;

							case 'WM/Year':
								$MP3fileInfo['asf']['year'] = $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value_ascii'];
								break;

							case 'IsVBR':
								if ( $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'] )
									$MP3fileInfo['bitrate_mode'] = 'vbr';
							
								break;

							case 'ID3':
								if ( $tempfilehandle = tmpfile() ) 
								{
									$tempMP3fileInfo = array();
									fwrite( $tempfilehandle, $MP3fileInfo['asf']['extended_content_description']['content_descriptors']["$ExtendedContentDescriptorsCounter"]['value'] );
									ID3::getID3v2Filepointer( $tempfilehandle, $tempMP3fileInfo );
									fclose( $tempfilehandle );
									$MP3fileInfo['id3']['id3v2'] = $tempMP3fileInfo['id3']['id3v2'];
								}
							
								break;
							default:
								// do nothing
								// $MP3fileInfo['asf']['comment'] = '';
								break;
						}
					}

					break;

				case ID3_ASF_Stream_Bitrate_Properties_Object:
					// Stream Bitrate Properties Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Stream Bitrate Properties object - ID3_ASF_Stream_Bitrate_Properties_Object
					// Object Size                  QWORD        64              // size of Extended Content Description object, including 26 bytes of Stream Bitrate Properties Object header
					// Bitrate Records Count        WORD         16              // number of records in Bitrate Records
					// Bitrate Records              array of:    variable        //
					// * Flags                      WORD         16              //
					// * * Stream Number            bits         7  (0x007F)     // number of this stream
					// * * Reserved                 bits         9  (0xFF80)     // hardcoded: 0
					// * Average Bitrate            DWORD        32              // in bits per second

					$MP3fileInfo['asf']['stream_bitrate_properties']['objectid']              = $NextObjectGUID;
					$MP3fileInfo['asf']['stream_bitrate_properties']['objectid_guid']         = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['stream_bitrate_properties']['objectsize']            = $NextObjectSize;
					$MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records_count'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
					$offset += 2;
	
					for ( $BitrateRecordsCounter = 0; $BitrateRecordsCounter < $MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records_count']; $BitrateRecordsCounter++ ) 
					{
						$MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records']["$BitrateRecordsCounter"]['flags_raw'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 2 ) );
						$offset += 2;
						$MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records']["$BitrateRecordsCounter"]['flags']['stream_number'] = $MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records']["$BitrateRecordsCounter"]['flags_raw'] & 0x007F;
						$MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records']["$BitrateRecordsCounter"]['bitrate'] = ID3::littleEndianToInt( substr( $ASFHeaderData, $offset, 4 ) );
						$offset += 4;
					}

					break;

				case ID3_ASF_Padding_Object:
					// Padding Object: (optional)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Padding object - ID3_ASF_Padding_Object
					// Object Size                  QWORD        64              // size of Padding object, including 24 bytes of Stream Bitrate Properties Object header
					// Padding Data                 BYTESTREAM   variable        // ignore
	
					$MP3fileInfo['asf']['padding_object']['objectid']       = $NextObjectGUID;
					$MP3fileInfo['asf']['padding_object']['objectid_guid']  = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['padding_object']['objectsize']     = $NextObjectSize;
					$MP3fileInfo['asf']['padding_object']['padding_length'] = $MP3fileInfo['asf']['padding_object']['objectsize'] - 16 - 8;
					$MP3fileInfo['asf']['padding_object']['padding']        = substr( $ASFHeaderData, $offset, $MP3fileInfo['asf']['padding_object']['padding_length'] );

					break;

				default:
					// Implementations shall ignore any standard or non-standard object that they do not know how to handle.
					if ( ID3_ASF::GUIDname( $NextObjectGUIDtext ) )
						$MP3fileInfo['error'][] = 'unhandled GUID "' . ID3_ASF::GUIDname( $NextObjectGUIDtext ) . '" (' . $NextObjectGUIDtext . ') in ASF header at offset ' . ( $offset - 16 - 8 ) . '.';
					else
						$MP3fileInfo['error'][] = 'unknown GUID (' . $NextObjectGUIDtext . ') in ASF header at offset ' . ( $offset - 16 - 8 ) . '.';
				
					$offset += ($NextObjectSize - 16 - 8);
					break;
			}
		}
		
		if ( isset( $MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records_count'] ) ) 
		{
			$ASFbitrateAudio = 0;
			$ASFbitrateVideo = 0;
			
			for ( $BitrateRecordsCounter = 0; $BitrateRecordsCounter < $MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records_count']; $BitrateRecordsCounter++ ) 
			{
				if ( isset( $MP3fileInfo['asf']['codec_list']['codec_entries']["$BitrateRecordsCounter"] ) ) 
				{
					switch ( $MP3fileInfo['asf']['codec_list']['codec_entries']["$BitrateRecordsCounter"]['type_raw'] ) 
					{
						case 1:
							$ASFbitrateVideo += $MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records']["$BitrateRecordsCounter"]['bitrate'];
							break;

						case 2:
							$ASFbitrateAudio += $MP3fileInfo['asf']['stream_bitrate_properties']['bitrate_records']["$BitrateRecordsCounter"]['bitrate'];
							break;

						default:
							// do nothing
							break;
					}
				}
			}
		
			if ( $ASFbitrateAudio > 0 ) 
			{
				$MP3fileInfo['asf']['bitrate_audio'] = $ASFbitrateAudio;
				$MP3fileInfo['bitrate_audio']        = $ASFbitrateAudio;
			}
		
			if ( $ASFbitrateVideo > 0 ) 
			{
				$MP3fileInfo['asf']['bitrate_video'] = $ASFbitrateVideo;
				$MP3fileInfo['bitrate_video']        = $ASFbitrateVideo;
			}
		}
	
		if ( isset( $MP3fileInfo['asf']['stream_properties_object'] ) && is_array( $MP3fileInfo['asf']['stream_properties_object'] ) ) 
		{
			using( 'format.id3.lib.ID3_RIFF' );
		
			foreach( $MP3fileInfo['asf']['stream_properties_object'] as $streamnumber => $streamdata ) 
			{
				switch ( $streamdata['stream_type'] ) 
				{
					case ID3_ASF_Audio_Media:
						// Field Name                   Field Type   Size (bits)
						// Codec ID / Format Tag        WORD         16              // unique ID of audio codec - defined as wFormatTag field of WAVEFORMATEX structure
						// Number of Channels           WORD         16              // number of channels of audio - defined as nChannels field of WAVEFORMATEX structure
						// Samples Per Second           DWORD        32              // in Hertz - defined as nSamplesPerSec field of WAVEFORMATEX structure
						// Average number of Bytes/sec  DWORD        32              // bytes/sec of audio stream  - defined as nAvgBytesPerSec field of WAVEFORMATEX structure
						// Block Alignment              WORD         16              // block size in bytes of audio codec - defined as nBlockAlign field of WAVEFORMATEX structure
						// Bits per sample              WORD         16              // bits per sample of mono data. set to zero for variable bitrate codecs. defined as wBitsPerSample field of WAVEFORMATEX structure
						// Codec Specific Data Size     WORD         16              // size in bytes of Codec Specific Data buffer - defined as cbSize field of WAVEFORMATEX structure
						// Codec Specific Data          BYTESTREAM   variable        // array of codec-specific data bytes

						$audiomediaoffset = 0;
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['format_tag']      = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 2 ) );
						$audiomediaoffset += 2;
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['codec']           = ID3_RIFF::RIFFwFormatTagLookup( $MP3fileInfo['asf']['audio_media']["$streamnumber"]['format_tag'] );
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['channels']        = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 2 ) );
						$audiomediaoffset += 2;
						$MP3fileInfo['channels']                                               = $MP3fileInfo['asf']['audio_media']["$streamnumber"]['channels'];
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['frequency']       = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 4 ) );
						$audiomediaoffset += 4;
						$MP3fileInfo['frequency']                                              = $MP3fileInfo['asf']['audio_media']["$streamnumber"]['frequency'];
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['bytes_sec']       = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 4 ) );
						$audiomediaoffset += 4;
		
						if ( !isset($MP3fileInfo['bitrate_audio'] ) ) 
							$MP3fileInfo['bitrate_audio'] = $MP3fileInfo['asf']['audio_media']["$streamnumber"]['bytes_sec'] * 8;
					
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['block_align']     = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 2 ) );
						$audiomediaoffset += 2;
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['bits_sample']     = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 2 ) );
						$audiomediaoffset += 2;
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['codec_data_size'] = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $audiomediaoffset, 2 ) );
						$audiomediaoffset += 2;
						$MP3fileInfo['asf']['audio_media']["$streamnumber"]['codec_data']      = substr( $streamdata['type_specific_data'], $audiomediaoffset, $MP3fileInfo['asf']['audio_media']["$streamnumber"]['codec_data_size'] );
						$audiomediaoffset += $MP3fileInfo['asf']['audio_media']["$streamnumber"]['codec_data_size'];
	
						break;

					case ID3_ASF_Video_Media:
						// Field Name                   Field Type   Size (bits)
						// Encoded Image Width          DWORD        32              // width of image in pixels
						// Encoded Image Height         DWORD        32              // height of image in pixels
						// Reserved Flags               BYTE         8               // hardcoded: 0x02
						// Format Data Size             WORD         16              // size of Format Data field in bytes
						// Format Data                  array of:    variable        //
						// * Format Data Size           DWORD        32              // number of bytes in Format Data field, in bytes - defined as biSize field of BITMAPINFOHEADER structure
						// * Image Width                LONG         32              // width of encoded image in pixels - defined as biWidth field of BITMAPINFOHEADER structure
						// * Image Height               LONG         32              // height of encoded image in pixels - defined as biHeight field of BITMAPINFOHEADER structure
						// * Reserved                   WORD         16              // hardcoded: 0x0001 - defined as biPlanes field of BITMAPINFOHEADER structure
						// * Bits Per Pixel Count       WORD         16              // bits per pixel - defined as biBitCount field of BITMAPINFOHEADER structure
						// * Compression ID             FOURCC       32              // fourcc of video codec - defined as biCompression field of BITMAPINFOHEADER structure
						// * Image Size                 DWORD        32              // image size in bytes - defined as biSizeImage field of BITMAPINFOHEADER structure
						// * Horizontal Pixels / Meter  DWORD        32              // horizontal resolution of target device in pixels per meter - defined as biXPelsPerMeter field of BITMAPINFOHEADER structure
						// * Vertical Pixels / Meter    DWORD        32              // vertical resolution of target device in pixels per meter - defined as biYPelsPerMeter field of BITMAPINFOHEADER structure
						// * Colors Used Count          DWORD        32              // number of color indexes in the color table that are actually used - defined as biClrUsed field of BITMAPINFOHEADER structure
						// * Important Colors Count     DWORD        32              // number of color index required for displaying bitmap. if zero, all colors are required. defined as biClrImportant field of BITMAPINFOHEADER structure
						// * Codec Specific Data        BYTESTREAM   variable        // array of codec-specific data bytes

						$videomediaoffset = 0;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['image_width']                     = ID3::littleEndianToInt(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$MP3fileInfo['resolution_x']                                                           = $MP3fileInfo['asf']['video_media']["$streamnumber"]['image_width'];
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['image_height']                    = ID3::littleEndianToInt(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$MP3fileInfo['resolution_y']                                                           = $MP3fileInfo['asf']['video_media']["$streamnumber"]['image_height'];
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['flags']                           = ID3::littleEndianToInt(substr($streamdata['type_specific_data'], $videomediaoffset, 1));
						$videomediaoffset += 1;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data_size']                = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 2 ) );
						$videomediaoffset += 2;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['format_data_size'] = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['image_width']      = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['image_height']     = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['reserved']         = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 2 ) );
						$videomediaoffset += 2;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['bits_per_pixel']   = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 2 ) );
						$videomediaoffset += 2;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['codec_fourcc']     = substr( $streamdata['type_specific_data'], $videomediaoffset, 4 );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['codec']            = ID3_RIFF::RIFFfourccLookup( $MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['codec_fourcc'] );
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['image_size']       = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['horizontal_pels']  = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['vertical_pels']    = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['colors_used']      = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['colors_important'] = ID3::littleEndianToInt( substr( $streamdata['type_specific_data'], $videomediaoffset, 4 ) );
						$videomediaoffset += 4;
						$MP3fileInfo['asf']['video_media']["$streamnumber"]['format_data']['codec_data']       = substr( $streamdata['type_specific_data'], $videomediaoffset );
			
						break;

					default:
						break;
				}
			}
		}

		while ( ftell( $fd ) < $MP3fileInfo['filesize'] ) 
		{
			$NextObjectDataHeader = fread( $fd, 24 );
			$offset = 0;
			$NextObjectGUID = substr( $NextObjectDataHeader, 0, 16 );
			$offset += 16;
			$NextObjectGUIDtext = ID3::bytestringToGUID( $NextObjectGUID );
			$NextObjectSize = ID3::littleEndianToInt( substr( $NextObjectDataHeader, $offset, 8 ));
			$offset += 8;

			switch ( $NextObjectGUID ) 
			{
				case ID3_ASF_Data_Object:
					// Data Object: (mandatory, one only)
					// Field Name                       Field Type   Size (bits)
					// Object ID                        GUID         128             // GUID for Data object - ID3_ASF_Data_Object
					// Object Size                      QWORD        64              // size of Data object, including 50 bytes of Data Object header. may be 0 if FilePropertiesObject.BroadcastFlag == 1
					// File ID                          GUID         128             // unique identifier. identical to File ID field in Header Object
					// Total Data Packets               QWORD        64              // number of Data Packet entries in Data Object. invalid if FilePropertiesObject.BroadcastFlag == 1
					// Reserved                         WORD         16              // hardcoded: 0x0101

					$DataObjectData = $NextObjectDataHeader . fread( $fd, 50 - 24 );
					$offset = 24;

					$MP3fileInfo['asf']['data_object']['objectid']           = $NextObjectGUID;
					$MP3fileInfo['asf']['data_object']['objectid_guid']      = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['data_object']['objectsize']         = $NextObjectSize;

					$MP3fileInfo['asf']['data_object']['fileid']             = substr( $DataObjectData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['data_object']['fileid_guid']        = ID3::bytestringToGUID( $MP3fileInfo['asf']['data_object']['fileid'] );
					$MP3fileInfo['asf']['data_object']['total_data_packets'] = ID3::littleEndianToInt( substr( $DataObjectData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['asf']['data_object']['reserved']           = ID3::littleEndianToInt( substr( $DataObjectData, $offset, 2 ) );
					$offset += 2;
			
					if ( $MP3fileInfo['asf']['data_object']['reserved'] != 0x0101 ) 
					{
						$MP3fileInfo['error'][] = 'data_object.reserved (' . ID3::printHexBytes( $MP3fileInfo['asf']['data_object']['reserved'] ) . ') does not match expected value of "0x0101".';
						// return false;
						break;
					}

					// Data Packets                     array of:    variable        //
					// * Error Correction Flags         BYTE         8               //
					// * * Error Correction Data Length bits         4               // if Error Correction Length Type == 00, size of Error Correction Data in bytes, else hardcoded: 0000
					// * * Opaque Data Present          bits         1               //
					// * * Error Correction Length Type bits         2               // number of bits for size of the error correction data. hardcoded: 00
					// * * Error Correction Present     bits         1               // If set, use Opaque Data Packet structure, else use Payload structure
					// * Error Correction Data

					fseek( $fd, ( $MP3fileInfo['asf']['data_object']['objectsize'] - 50 ), SEEK_CUR ); // skip actual video data
					break;

				case ID3_ASF_Simple_Index_Object:
					// Simple Index Object: (optional, recommended, one per video stream)
					// Field Name                       Field Type   Size (bits)
					// Object ID                        GUID         128             // GUID for Simple Index object - ID3_ASF_Data_Object
					// Object Size                      QWORD        64              // size of Simple Index object, including 56 bytes of Simple Index Object header
					// File ID                          GUID         128             // unique identifier. may be zero or identical to File ID field in Data Object and Header Object
					// Index Entry Time Interval        QWORD        64              // interval between index entries in 100-nanosecond units
					// Maximum Packet Count             DWORD        32              // maximum packet count for all index entries
					// Index Entries Count              DWORD        32              // number of Index Entries structures
					// Index Entries                    array of:    variable        //
					// * Packet Number                  DWORD        32              // number of the Data Packet associated with this index entry
					// * Packet Count                   WORD         16              // number of Data Packets to sent at this index entry

					$SimpleIndexObjectData = $NextObjectDataHeader.fread($fd, 56 - 24);
					$offset = 24;

					$MP3fileInfo['asf']['simple_index_object']['objectid']                  = $NextObjectGUID;
					$MP3fileInfo['asf']['simple_index_object']['objectid_guid']             = $NextObjectGUIDtext;
					$MP3fileInfo['asf']['simple_index_object']['objectsize']                = $NextObjectSize;

					$MP3fileInfo['asf']['simple_index_object']['fileid']                    = substr( $SimpleIndexObjectData, $offset, 16 );
					$offset += 16;
					$MP3fileInfo['asf']['simple_index_object']['fileid_guid']               = ID3::bytestringToGUID( $MP3fileInfo['asf']['simple_index_object']['fileid'] );
					$MP3fileInfo['asf']['simple_index_object']['index_entry_time_interval'] = ID3::littleEndianToInt( substr( $SimpleIndexObjectData, $offset, 8 ) );
					$offset += 8;
					$MP3fileInfo['asf']['simple_index_object']['maximum_packet_count']      = ID3::littleEndianToInt( substr( $SimpleIndexObjectData, $offset, 4 ) );
					$offset += 4;
					$MP3fileInfo['asf']['simple_index_object']['index_entries_count']       = ID3::littleEndianToInt( substr( $SimpleIndexObjectData, $offset, 4 ) );
					$offset += 4;

					$IndexEntriesData = $SimpleIndexObjectData . fread( $fd, 6 * $MP3fileInfo['asf']['simple_index_object']['index_entries_count'] );
	
					for ( $IndexEntriesCounter = 0; $IndexEntriesCounter < $MP3fileInfo['asf']['simple_index_object']['index_entries_count']; $IndexEntriesCounter++ ) 
					{
						$MP3fileInfo['asf']['simple_index_object']['index_entries']["$IndexEntriesCounter"]['packet_number'] = ID3::littleEndianToInt( substr( $IndexEntriesData, $offset, 4 ) );
						$offset += 4;
						$MP3fileInfo['asf']['simple_index_object']['index_entries']["$IndexEntriesCounter"]['packet_count']  = ID3::littleEndianToInt( substr( $IndexEntriesData, $offset, 4 ) );
						$offset += 2;
					}

					break;

				default:
					// Implementations shall ignore any standard or non-standard object that they do not know how to handle.
					if ( ID3_ASF::GUIDname( $NextObjectGUIDtext ) )
						$MP3fileInfo['error'][] = 'unhandled GUID "' . ID3_ASF::GUIDname( $NextObjectGUIDtext ) . '" (' . $NextObjectGUIDtext . ') in ASF body at offset ' . ( $offset - 16 - 8 ) . '.';
					else
						$MP3fileInfo['error'][] = 'unknown GUID (' . $NextObjectGUIDtext . ') in ASF body at offset ' . ( ftell( $fd ) - 16 - 8 ) . '.';
					
					fseek( $fd, ( $NextObjectSize - 16 - 8 ), SEEK_CUR );
					break;
			}
		}

		return true;
	}

	function knownGUIDs() 
	{
		static $GUIDarray = array();
		
		if ( count( $GUIDarray ) < 1 ) 
		{
			$GUIDarray['ID3_ASF_Old_ACM_Codec']                       = 'D6E229F3-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_ASCII_Text']                      = 'D6E229EE-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_ASF_Data_Object']                 = 'D6E229D2-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_ASF_Header_Object']               = 'D6E229D1-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_ASF_Index_Object']                = 'D6E229D3-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_ASF_Placeholder_Object']          = 'D6E22A0E-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Audio_Media']                     = 'D6E229E2-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Color_Table_Object']              = 'D6E229E0-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Command_Media']                   = 'D6E229E8-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Component_Download_Object']       = 'D6E229D8-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Content_Description_Object']      = 'D6E229D5-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Data_Unit_Extension_Object']      = 'D6E22A0F-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_DirectShow_Rendering_Filter']     = 'D6E229F7-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_DirectShow_Transform_Filter']     = 'D6E229F6-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_File_Properties_Object']          = 'D6E229D0-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Filename_Command']                = 'D6E229F2-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Frequency_Response_Enhancement']  = 'D6E229FE-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_HTML_Text']                       = 'D6E229F0-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Image_Media']                     = 'D6E229E4-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Index_Parameters_Object']         = 'D6E229DF-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Inter_Media_Dependency_Object']   = 'D6E229DD-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Language_List_Object']            = 'D6E229E1-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Language']                        = 'D6E22A00-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Marker_Object']                   = 'D6E229D7-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Media_Object']                    = 'D6E229FF-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_MIDI_Media']                      = 'D6E229E7-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_No_Color_Table']                  = 'D6E229EC-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_No_Enhancement']                  = 'D6E229F8-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_No_Error_Concealment']            = 'D6E229EA-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Number_of_Channels_Enhancement']  = 'D6E229FD-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Prioritization_Object']           = 'D6E229DB-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Quality_Enhancement']             = 'D6E229FC-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_QuickTime_Codec']                 = 'D6E229F5-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Rating_Object']                   = 'D6E229DE-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_RTP_Extension_Data']              = '96800C63-4C94-11D1-837B-0080C7A37F95';
			$GUIDarray['ID3_ASF_Old_Scalable_Object']                 = 'D6E229DA-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Scrambled_Audio']                 = 'D6E229EB-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Script_Command_Object']           = 'D6E229D6-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_SMPTE_Time']                      = 'D6E229ED-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Spatial_Enhancement']             = 'D6E229FB-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Stream_Group_Object']             = 'D6E229D9-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Stream_Properties_Object']        = 'D6E229D4-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Temporal_Enhancement']            = 'D6E229FA-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Text_Media']                      = 'D6E229E6-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Timecode_Media']                  = 'D6E229E5-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['vASF_Old_Unicode_Text']                    = 'D6E229EF-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Unknown_Enhancement_Type']        = 'D6E229F9-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_URL_Command']                     = 'D6E229F1-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_VCM_Codec']                       = 'D6E229F4-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Old_Video_Media']                     = 'D6E229E3-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Audio_Media']                         = 'F8699E40-5B4D-11CF-A8FD-00805F5C442B';
			$GUIDarray['ID3_ASF_Audio_Spread']                        = 'BFC3CD50-618F-11CF-8BB2-00AA00B4E220';
			$GUIDarray['ID3_ASF_Bitrate_Mutual_Exclusion_Object']     = 'D6E229DC-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Codec_List_Object']                   = '86D15240-311D-11D0-A3A4-00A0C90348F6';
			$GUIDarray['ID3_ASF_Command_Media']                       = '59DACFC0-59E6-11D0-A3AC-00A0C90348F6';
			$GUIDarray['ID3_ASF_Content_Description_Object']          = '75B22633-668E-11CF-A6D9-00AA0062CE6C';
			$GUIDarray['ID3_ASF_Data_Object']                         = '75B22636-668E-11CF-A6D9-00AA0062CE6C';
			$GUIDarray['ID3_ASF_Error_Correction_Object']             = '75B22635-668E-11CF-A6D9-00AA0062CE6C';
			$GUIDarray['ID3_ASF_Extended_Content_Description_Object'] = 'D2D0A440-E307-11D2-97F0-00A0C95EA850';
			$GUIDarray['ID3_ASF_Extended_Content_Encryption_Object']  = '298AE614-2622-4C17-B935-DAE07EE9289C';
			$GUIDarray['ID3_ASF_File_Properties_Object']              = '8CABDCA1-A947-11CF-8EE4-00C00C205365';
			$GUIDarray['ID3_ASF_Header_Extension_Object']             = '5FBF03B5-A92E-11CF-8EE3-00C00C205365';
			$GUIDarray['ID3_ASF_Header_Object']                       = '75B22630-668E-11CF-A6D9-00AA0062CE6C';
			$GUIDarray['ID3_ASF_Marker_Object']                       = 'F487CD01-A951-11CF-8EE6-00C00C205365';
			$GUIDarray['ID3_ASF_Mutex_Bitrate']                       = 'D6E22A01-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_Mutex_Unknown']                       = 'D6E22A02-35DA-11D1-9034-00A0C90349BE';
			$GUIDarray['ID3_ASF_No_Error_Correction']                 = '20FB5700-5B55-11CF-A8FD-00805F5C442B';
			$GUIDarray['ID3_ASF_Padding_Object']                      = '1806D474-CADF-4509-A4BA-9AABCB96AAE8';
			$GUIDarray['ID3_ASF_Reserved_1']                          = 'ABD3D211-A9BA-11CF-8EE6-00C00C205365';
			$GUIDarray['ID3_ASF_Script_Command_Object']               = '1EFB1A30-0B62-11D0-A39B-00A0C90348F6';
			$GUIDarray['ID3_ASF_Simple_Index_Object']                 = '33000890-E5B1-11CF-89F4-00A0C90349CB';
			$GUIDarray['ID3_ASF_Stream_Bitrate_Properties_Object']    = '7BF875CE-468D-11D1-8D82-006097C9A2B2';
			$GUIDarray['ID3_ASF_Stream_Properties_Object']            = 'B7DC0791-A9B7-11CF-8EE6-00C00C205365';
			$GUIDarray['ID3_ASF_Video_Media']                         = 'BC19EFC0-5B4D-11CF-A8FD-00805F5C442B';
			// seen-but-unknown GUIDs -> 2211B3FB-BD23-11D2-B4B7-00A0C955FC6E
		}
	
		return $GUIDarray;
	}

	function GUIDname( $GUIDstring ) 
	{
		$GUIDarray = ID3_ASF::knownGUIDs();
		return array_search( $GUIDstring, $GUIDarray );
	}

	function setConstants()
	{
		$GUIDarray = ID3_ASF::knownGUIDs();
	
		foreach ( $GUIDarray as $GUIDname => $hexstringvalue ) 
		{
			// initialize all GUID constants
			define( $GUIDname, ID3::GUIDToBytestring( $hexstringvalue ) );
		}
	}

	function ASFCodecListObjectTypeLookup( $CodecListType ) 
	{
		static $ASFCodecListObjectTypeLookup = array();
	
		if ( count( $ASFCodecListObjectTypeLookup ) < 1 ) 
		{
			$ASFCodecListObjectTypeLookup[0x0001] = 'Video Codec';
			$ASFCodecListObjectTypeLookup[0x0002] = 'Audio Codec';
			$ASFCodecListObjectTypeLookup[0xFFFF] = 'Unknown Codec';
		}

		return ( isset( $ASFCodecListObjectTypeLookup["$CodecListType"] )? $ASFCodecListObjectTypeLookup["$CodecListType"] : 'Invalid Codec Type' );
	}
} // END OF ID3_ASF

?>
