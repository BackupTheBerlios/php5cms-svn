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


using( 'util.validation.lib.Validation' );


define( 'ID3_FREAD_BUFFER_SIZE', 16384 ); // number of bytes to read in at once

define( 'ID3_GIF_SIG',   chr( 0x47 ) . chr( 0x49 ) . chr( 0x46 ) ); // 'GIF'

define( 'ID3_PNG_SIG',   chr( 0x89 ) . chr( 0x50 ) . chr( 0x4E ) . chr( 0x47 ) . chr( 0x0D ) . chr( 0x0A ) . chr( 0x1A ) . chr( 0x0A ) );

define( 'ID3_JPG_SIG',   chr( 0xFF ) . chr( 0xD8 ) . chr( 0xFF ) );
define( 'ID3_JPG_SOS',   chr( 0xDA ) ); // Start Of Scan - image data start
define( 'ID3_JPG_SOF0',  chr( 0xC0 ) ); // Start Of Frame N
define( 'ID3_JPG_SOF1',  chr( 0xC1 ) ); // N indicates which compression process
define( 'ID3_JPG_SOF2',  chr( 0xC2 ) ); // Only SOF0-SOF2 are now in common use
define( 'ID3_JPG_SOF3',  chr( 0xC3 ) );
define( 'ID3_JPG_SOF5',  chr( 0xC5 ) );
define( 'ID3_JPG_SOF6',  chr( 0xC6 ) );
define( 'ID3_JPG_SOF7',  chr( 0xC7 ) );
define( 'ID3_JPG_SOF9',  chr( 0xC9 ) );
define( 'ID3_JPG_SOF10', chr( 0xCA ) );
define( 'ID3_JPG_SOF11', chr( 0xCB ) );
define( 'ID3_JPG_SOF13', chr( 0xCD ) );
define( 'ID3_JPG_SOF14', chr( 0xCE ) );
define( 'ID3_JPG_SOF15', chr( 0xCF ) );
define( 'ID3_JPG_EOI',   chr( 0xD9 ) ); // End Of Image (end of datastream)


/**
 * What it does:
 * 
 * Reads & parses (to varying degrees):
 * 
 * - APE tags: v1 and v2
 * - ASF: ASF, Windows Media Audio (WMA), Windows Media Video (WMV)
 * - BMP (Windows & OS/2, uncompressed / RLE4 / RLE8)
 * - GIF
 * - JPEG
 * - Lyrics 3: v1 and v2
 * - MIDI
 * - Monkey's Audio
 * - MP3: ID3v1 & ID3v1.1
 * - MP3: ID3v2.2, ID3v2.3, ID3v2.4
 * - MP3: MPEG-audio information (bitrate, sampling frequency, etc)
 * - MP3: Lyrics3 v1 & v2
 * - MPEG-1 video frame size, bitrate, aspect ratio, etc
 * - Ogg Vorbis: stream information, comment tags
 * - PNG
 * - RIFF: AVI audio/video information (codecs, bitrates, frame sizes, etc)
 * - RIFF: WAV audio information (bitrate, sampling frequency, etc)
 * - ZIP
 * 
 * Writes:
 * 
 * - ID3v1 & ID3v1.1
 * - ID3v2.3 & ID3v2.4
 * - Ogg Vorbis tags
 * 
 * 
 * Requirements:
 * 
 * - PHP 4.1.0 (or higher)
 * - GD  <1.6 for GIF and JPEG functions
 * - GD >=1.6 for PNG and JPEG functions
 * 
 * 
 * Notes:
 * 
 * If the ID3v2.x parser loses track of where it is, it will return something
 * in $mp3info['error'], stating where it hit an error. If nothing is
 * returned in that array element, you can assume the entire tag parsed OK.
 * 
 * Conforms to ID3v2.2, ID3v2.3 and ID3v2.4 specs as published at www.id3.org
 * 
 * 
 * Usage:
 * 
 * $mp3info = ID3::getAllMP3info( <filename> );
 * $mp3info = ID3::getAllMP3info( '/home/mp3s/song.mp3' );
 * $mp3info = ID3::getAllMP3info( 'c:\\mp3s\\song.mp3' );
 * $mp3info = ID3::getAllMP3info( 'http://www.example.com/song.mp3' );
 * 
 * 
 * What does the returned data structure look like?
 * 
 * array() 
 * {
 *   	['exist'] 			 => bool()						// does this file actually exist?
 *   	['filename'] 		 => string()					// filename including extension, not including path
 *   	['filesize'] 		 => int()						// in bytes
 *   	['getID3version'] 	 => string()					// ex: '1.4.0'
 *   	['error'] 			 => array()						// if present, what error occured
 *   	['fileformat'] 		 => string()					// 'mp3', 'mp2', 'zip', 'ogg', 'id3', 'mpg', 'riff', 'wav', 'midi', 'asf', 'mac', 'ape', 'gif', 'jpg', 'png', 'bmp', 'mpc', 'real'
 *   	['bitrate_audio'] 	 => float()						// total bitrate for audio stream (if present) in bits per second
 *   	['bitrate_video'] 	 => float()						// total bitrate for video stream (if present) in bits per second
 *   	['bitrate']			 => float()						// total bitrate (audio + video) in bits per second
 *   	['bitrate_mode'] 	 => string()					// 'cbr' or 'vbr' or 'abr'
 *   	['resolution_x'] 	 => int()						// horizontal resolution of video stream, if present
 *   	['resolution_y'] 	 => int()						// vertical resolution of video stream, if present
 *   	['playtime_seconds'] => float()						// playtime in floating-point seconds
 *   	['playtime_string']  => string()					// playtime in minutes:seconds format
 *   	['audiobytes'] 		 => int()						// bytes of MPEG audio, with ID3v2 headers stripped
 *   	['audiodataoffset']  => int()						// byte offset where first detected MPEG audio frame occurs.
 * 															// Should be either zero (no ID3v2) or size of ID3v2 header.
 *   	['id3'] => array() 
 *   	{
 *     		['id3v1'] => array( 8 ) 
 * 			{
 *       		['title']   => string()
 *       		['artist']  => string()
 *       		['album']   => string()
 *       		['year']    => string()
 *       		['comment'] => string()
 *       		['genreid'] => int()
 *       		['genre']   => string()
 *       		['track']   => int()
 *     		}
 *     		['id3v2'] => array( 8 )  						// ID3v2.x data 
 * 			{					
 *       		['header']            => bool()
 *       		['majorversion']      => int()
 *       		['minorversion']      => int()
 *       		['flags']['unsynch']  => bool()
 *       		['flags']['exthead']  => bool()
 *       		['flags']['experim']  => bool()
 *       		['flags']['isfooter'] => bool()
 *       		['headerlength']      => int()				// in bytes, including the 6/10-byte ID3v2 header
 *       		['title']             => string()
 *       		['artist']            => string()
 *       		['album']             => string()
 *       		['year']              => string()
 *       		['track']             => string()
 *       		['totaltracks']       => string()
 *       		['genre']             => string()
 *       		['genreid']           => int()
 *       		['genrelist']         => array()
 *       		['comment']           => string()
 *       		['padding']           => array() 
 * 				{
 *         			['start']    => int()					// start of padding, byte offset from beginning of file
 *         			['length']   => int()					// amount of padding, in bytes
 *         			['valid']    => bool()					// TRUE if padding consists entirely of null bytes
 *         			['errorpos'] => int()					// position of non-null byte, byte offset from beginning of file
 *       		}
 *       		[<3- or 4-char frame name>] => array		// see http://www.id3.org/id3v2.4.0-structure.txt
 *       		{											//   for details on which 4-character name represents which data
 *         			['flags']      => string()				// NOTE: the actual structure varies depending on the FrameID
 *         			['datalength'] => int()					// length of frame data (in bytes) not including 6/10-byte frame header
 *         			['dataoffset'] => int()					// offset of beginning of frame from beginning of file, in *de-unsynchronized* bytes
 *         			['asciidata']  => string()				// approximate translation from text-encodings other than ISO-8859-1 (ie UTF-16, UTF-16BE and UTF-8)
 *       		}
 *     		}
 *   	}
 *   	['lyrics3'] => array()								// for MP3 files with Lyrics3 tag only 
 * 		{					
 *   	}	
 *   	['ogg'] => array()   								// for Ogg Vorbis files only
 * 		{						
 *  	   	['comments'] => array() 
 * 			{
 *       		[n] => array() 
 * 				{
 *  	       		['key']   => string						// 'TITLE', 'ARTIST', etc [http://www.xiph.org/ogg/vorbis/doc/v-comment.html]
 *  	       		['value'] => string						// 'Yellow Submarine', 'The Beatles', etc
 *       		}
 *  	   	}
 *   	}
 *   	['riff'] => array() 								// for RIFF/WAV files only
 * 		{						
 *  	   	['raw'] => array()   							// data as read in, unprocessed
 * 			{						
 *       		['riff'] => array() 
 * 				{
 *         			['size'] => int()						// in bytes
 *       		}
 *       		['WAVE'] => array() 
 * 				{
 *         			['size'] => int()						// in bytes
 *       		}
 *       		['fmt '] => array()   						// note the trailing space
 * 				{					
 *         			['size']            => int()			// in bytes
 *         			['wFormatTag']      => int()			// waveform format code
 *         			['nChannels']       => int()			// 1 (mono) or 2 (stereo)
 *         			['nSamplesPerSec']  => int()          	// samples per second (aka frequency)
 *         			['nAvgBytesPerSec'] => int()          	// byterate, bytes per second
 *         			['nBlockAlign']     => int()?        	// The block alignment (in bytes) of the waveform data
 *       		}
 *       		['rgad'] => array() 
 * 				{
 *         			['size']                => int()  		// in bytes
 *         			['fPeakAmplitude']      => float()    	// 1 means the .wav file peaks at digital full scale (equivalent to -32768 for 16-bit wav)
 *         			['nRadioRgAdjust']      => int()    	// meaningless by itself, see below array
 *         			['nAudiophileRgAdjust'] => int()     	// meaningless by itself, see below array
 *         			['radio']               => array() 		// settings for Radio Gain Adjustment
 * 					{                
 *           			['name']       => int()				// represents 'Radio' or 'not set'
 *           			['originator'] => int()				// how/by whom the RGAD was set/calculated
 *           			['signbit']    => int()				// 1->negative, 0->positive
 *           			['adjustment'] => int()				// absolute value of adjustment, multiplied by 10
 *         			}
 *        	 		['audiophile'] => array() 
 * 					{           
 *           			['name']       => int()				// represents 'Audiophile' or 'not set'
 *           			['originator'] => int()				// how/by whom the RGAD was set/calculated
 *           			['signbit']    => int()				// 1->negative, 0->positive
 *           			['adjustment'] => int()				// absolute value of adjustment, multiplied by 10
 *         			}
 *       		}
 *       		['data'] => array() 
 * 				{
 *         			['size'] => int()						// in bytes
 *       		}
 *     		}
 *     		['rgad'] => array() 
 * 			{
 *       		['peakamplitude'] => float()				// 1 means the .wav file peaks at digital full scale (equivalent to -32768 for 16-bit wav)
 *       		['radio'] => array() 
 * 				{
 *      	   		['name']       => string()				// 'Radio Gain Adjustment'
 *      	   		['originator'] => string()				// how/by whom the RGAD was set/calculated
 *      	   		['adjustment'] => float()				// adjustment in dB
 *       		}
 *       		['audiophile'] => array() 
 * 				{
 *      	   		['name']       => string()				// 'Audiophile Gain Adjustment'
 *      	   		['originator'] => string()				// how/by whom the RGAD was set/calculated
 *      	   		['adjustment'] => float()				// adjustment in dB
 *       		}
 *     		}
 *     		['audio'] => array() 
 * 			{
 *       		[n] => array() 
 * 				{
 *         			['format']        => string()			// MS-PCM, IBM mu-law, IBM a-law, IBM ADPCM
 *         			['channels']      => int()				// 1 (mono) or 2 (stereo)
 *         			['channelmode']   => string()			// 'mono' or 'stereo'
 *         			['frequency']     => int()				// sampling frequency in Hz
 *         			['bitrate']       => int()				// in bits per second
 *         			['bitspersample'] => int()
 *       		}
 *     		}
 *   	}
 *   	['mpeg'] => array() 
 * 		{
 *  	   	['audio'] => array()  // MPEG audio data 
 * 			{					
 *       		['version']       => string()				// MPEG audio version - 1, 2, or 2.5
 *       		['layer']         => string()				// MPEG audio layer   - I, II or III
 *       		['protection']    => boolean()
 *       		['bitrate']       => int()					// in kbps, ex: 128 (CBR files only)
 *       		['frequency']     => int()					// in Hz, ex: 44100
 *        		['padding']       => boolean()
 *       		['private']       => boolean()
 *       		['channelmode']   => string()				// mono, stereo, joint stereo or dual channel
 *       		['channels']      => int()					// 1 or 2
 *       		['modeextension'] => string()				// IS, MS, IS+MS for Layer III; 4-31, 8-31, 12-31, 16-31 for Layer I or Layer II
 *       		['copyright']     => boolean()
 *       		['original']      => boolean()
 *       		['emphasis']      => string()				// none, 50/15 ms or CCIT J.17
 *       		['raw'] => array() 
 * 				{
 *         			// same as above, but unparsed integer values
 *       		}
 *       		['VBR_bitrate']             => double()		// exact average bitrate in kbps (VBR files only)
 *       		['bitratemode']             => string()		// 'VBR' or 'CBR'
 *       		['VBR_method']              => string()		// 'Xing' or 'Fraunhofer' (VBR files only)
 *       		['VBR_frames']              => int()		// NOT including the Xing / Fraunhofer (VBRI) header frame (VBR files only)
 *       		['VBR_bytes']               => int()		// should be the same as ['audiobytes'] (VBR files only)
 *       		['VBR_quality']             => int()		// 0-100 (VBR, Fraunhofer only)
 *       		['VBR_seek_offsets']        => int()		// number of seek offsets
 *       		['VBR_seek_offsets_stride'] => int()		// offset "stride" (number of frames between offsets)
 *       		['VBR_offsets_relative']    => array()  	// array of seek offsets (from previous offset)
 *       		['VBR_offsets_absolute']    => array()		// array of seek offsets (from beginning of file)
 *     			['video']                   => array()   	// MPEG video data 
 * 				{					
 *       			['framesize_horizontal']    => int()	// frame width in pixels  (ex: 352)
 *       			['framesize_vertical']      => int()  	// frame height in pixels (ex: 240)
 *       			['pixel_aspect_ratio']      => float() 	// pixel aspect ratio (ex: 1.095)
 *       			['pixel_aspect_ratio_text'] => string() // pixel aspect ratio (ex: '4:3, 525 line, NTSC, CCIR601')
 *       			['frame_rate']              => int()	// frames per second  (ex: 25)
 *       			['bitrate_type']            => int()	// 'constant' or 'variable'
 *       			['bitrate_bps']             => int()	// bits per second (ex: 1150000)
 *       			['raw']                     => array() 
 * 					{
 *         				// same as above, but unparsed integer values
 *       			}
 *   			}
 *   			['replay_gain'] => array() 
 * 				{
 *     				['radio'] => array() 
 * 					{
 *       				['peak']       => double()			// peak level - 1.0 = 100%
 *       				['originator'] => string()			// who set the replay gain
 *       				['adjustment'] => double()			// adjustment in dB
 *     				}
 *     				['audiophile'] => array() 
 * 					{
 *       				['peak']       => double()			// peak level - 1.0 = 100%
 *       				['originator'] => string()			// who set the replay gain
 *       				['adjustment'] => double()			// adjustment in dB
 *     				}
 *   			}
 *   			['asf'] => array() 
 * 				{
 *   			}
 *   			['mpc'] => array() 
 * 				{
 *   			}
 *   			['real'] => array() 
 * 				{
 *   			}
 *   			['jpg'] => array() 
 * 				{
 *   			}
 *   			['gif'] => array() 
 * 				{
 *   			}
 *   			['png'] => array() 
 * 				{
 *   			}
 *   			['bmp'] => array() 
 * 				{
 *     				['type_os']      => string()			// 'OS/2' or 'Windows'
 *     				['type_version'] => int()				// 1 or
 *     				['header']       => array() 
 * 					{
 *     				}
 *     				['palette'] => array() 
 * 					{
 *     				}
 *     				['data'] => array() 
 * 					{
 *     				}
 *   			}
 *   			['flac'] => array() 
 * 				{
 *   			}
 *   			['vqf'] => array() 
 * 				{
 *   			}
 *   			['aac'] => array() 
 * 				{
 *   			}
 *   			['quicktime'] => array() 
 * 				{
 *   			}
 * 			}
 * 		}
 * }
 * 
 * 
 * Reference material:
 * 
 * - http://www.id3.org/id3v2.4.0-structure.txt
 * - http://www.id3.org/id3v2.4.0-frames.txt
 * - http://www.id3.org/id3v2.4.0-changes.txt
 * - http://www.id3.org/id3v2.3.0.txt
 * - http://www.id3.org/id3v2-00.txt
 * - http://www.id3.org/mp3frame.html
 * - http://minnie.tuhs.org/pipermail/mp3encoder/2001-January/001800.html <mathewhendry@hotmail.com>
 * - http://www.dv.co.yu/mpgscript/mpeghdr.htm
 * - http://www.mp3-tech.org/programmer/frame_header.html
 * - http://users.belgacom.net/gc247244/extra/tag.html
 * - http://www.id3.org/iso4217.html
 * - http://www.unicode.org/Public/MAPPINGS/ISO8859/8859-1.TXT
 * - http://www.xiph.org/ogg/vorbis/doc/framing.html
 * - http://www.xiph.org/ogg/vorbis/doc/v-comment.html
 * - http://leknor.com/code/php/class.ogg.php.txt
 * - http://www.id3.org/iso639-2.html
 * - http://www.psc.edu/general/software/packages/ieee/ieee.html
 * - http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/ieee-expl.html
 * - http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/binary.html
 * - http://www.jmcgowan.com/avi.html
 * - http://www.wotsit.org/
 * - http://www.herdsoft.com/ti/davincie/davp3xo2.htm
 * - http://www.mathdogs.com/vorbis-illuminated/bitstream-appendix.html
 * - "Standard MIDI File Format" by Dustin Caldwell (from www.wotsit.org)
 * - http://midistudio.com/Help/GMSpecs_Patches.htm
 * - http://www.xiph.org/archives/vorbis/200109/0459.html
 * - http://www.replaygain.org/
 * - http://download.microsoft.com/download/winmediatech40/Doc/1.0/WIN98MeXP/EN-US/ASF_Specification_v.1.0.exe
 * - http://mediaxw.sourceforge.net/files/doc/Active%20Streaming%20Format%20(ASF)%201.0%20Specification.pdf
 * - http://www.uni-jena.de/~pfk/mpp/sv8/
 * - http://jfaul.de/atl/
 * - http://www.uni-jena.de/~pfk/mpp/
 * - http://www.libpng.org/pub/png/spec/png-1.2-pdg.html
 * - http://www.real.com/devzone/library/creating/rmsdk/doc/rmff.htm
 * - http://www.fastgraph.com/help/bmp_os2_header_format.html
 * - http://netghost.narod.ru/gff/graphics/summary/os2bmp.htm
 * - http://flac.sourceforge.net/format.html
 * - http://www.research.att.com/projects/mpegaudio/mpeg2.html
 * - http://www.audiocoding.com/wiki/index.php?page=AAC
 * - http://www.geocities.com/xhelmboyx/quicktime/formats/qtm-layout.txt
 * - http://developer.apple.com/techpubs/quicktime/qtdevdocs/RM/frameset.htm
 *
 *
 * @package format_id3_lib
 */

class ID3 extends PEAR
{
	function getAllMP3info( $filename, $assumedFormat = '', $allowedFormats = array() ) 
	{
		if ( count( $allowedFormats ) < 1 ) 
		{
			// Simply comment out any format listed here that you don't want parsed.
			// Commenting out 'aac' is recommended unless you expect to encounter
			// some AAC files, due to its similarity to MP3, as well as slow parsing

			$allowedFormats[] = 'aac';       // audio       - Advanced Audio Coding
			$allowedFormats[] = 'asf';       // audio/video - Advanced Streaming Format, Windows Media Video, Windows Media Audio
			$allowedFormats[] = 'bmp';       // still image - Bitmap (Windows, OS/2; uncompressed, RLE8, RLE4)
			$allowedFormats[] = 'flac';      // audio       - Free Lossless Audio Codec
			$allowedFormats[] = 'gif';       // still image - Graphics Interchange Format
			$allowedFormats[] = 'jpg';       // still image - JPEG (Joint Photographic Experts Group)
			$allowedFormats[] = 'la';        // audio       - Lossless Audio
			$allowedFormats[] = 'mac';       // audio       - Monkey's Audio [Compressor]
			$allowedFormats[] = 'midi';      // audio       - MIDI (Musical Instrument Digital Interface)
			$allowedFormats[] = 'mp3';       // audio       - MPEG-1 audio, Layer-3
			$allowedFormats[] = 'mpc';       // audio       - Musepack / MPEGplus
			$allowedFormats[] = 'mpeg';      // audio/video - MPEG (Moving Pictures Experts Group)
			$allowedFormats[] = 'ogg';       // audio       - Ogg Vorbis
			$allowedFormats[] = 'png';       // still image - Portable Network Graphics
			$allowedFormats[] = 'quicktime'; // audio/video - Quicktime
			$allowedFormats[] = 'real';      // audio/video - RealAudio, RealVideo
			$allowedFormats[] = 'riff';      // audio/video - RIFF (Resource Interchange File Format), WAV, AVI
			$allowedFormats[] = 'vqf';       // audio       - transform-domain weighted interleave Vector Quantization Format
			$allowedFormats[] = 'zip';       // data        - compressed data
		}
	
		$MP3fileInfo['fileformat'] = '';      // filled in later
		$MP3fileInfo['error']      = array(); // filled in later, unset if not used
		$MP3fileInfo['exist']      = false;

		if ( strstr( $filename, 'http://' ) || strstr( $filename, 'ftp://' ) ) 
		{
			// remote file - copy locally first and work from there

			$MP3fileInfo['filename'] = $filename;
			$localfilepointer = tmpfile();
			ob_start();
		
			if ( $fp = fopen( $filename, 'rb' ) ) 
			{
				$MP3fileInfo['exist']    = true;
				$MP3fileInfo['filesize'] = 0;
				
				while ( $buffer = fread( $fp, ID3_FREAD_BUFFER_SIZE ) )
					$MP3fileInfo['filesize'] += fwrite( $localfilepointer, $buffer );
			
				fclose( $fp );
			} 
			else 
			{
				$MP3fileInfo['error'][] = strip_tags( ob_get_contents() );
			}
		
			ob_end_clean();
		} 
		else 
		{
			// local file

			if ( !file_exists( $filename ) ) 
			{
				// this code segment is needed for the file browser demonstrated in check.php
				// but may interfere with finding a filename that actually does contain apparently
				// escaped characters (like "file\'name.mp3") and/or
				// %xx-format characters (like "file%20name.mp3")
				$filename = stripslashes( $filename );
				
				if ( !file_exists( $filename ) )
					$filename = rawurldecode( $filename );
			}
		
			$MP3fileInfo['filename'] = basename( $filename );
			
			if ( $localfilepointer = @fopen( $filename, 'rb' ) ) 
			{
				$MP3fileInfo['exist'] = true;
				clearstatcache();
				$MP3fileInfo['filesize'] = filesize( $filename );
			}
		}

		if ( $MP3fileInfo['exist'] ) 
		{
			rewind( $localfilepointer );
			$formattest = fread( $localfilepointer, ID3_FREAD_BUFFER_SIZE );

			if ( ID3::parseAsThisFormat( 'zip', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_ZIP' );
				ID3_ZIP::getZipHeaderFilepointer( $filename, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat('ogg', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_OGG' );
				ID3_OGG::getOggHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'riff', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_RIFF' );
				ID3_RIFF::getRIFFHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'la', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_LA' );
				ID3_LA::getLAHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'mpeg', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_MPEG' );
				ID3_MPEG::getMPEGHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'asf', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_ASF' );
				ID3_ASF::getASFHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'mac', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_MAC' );
				
				ID3_MAC::getMonkeysAudioHeaderFilepointer( $localfilepointer, $MP3fileInfo );
				ID3_MAC::getAPEtagFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'mpc', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_MPC' );
				ID3_MPC::getMPCHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'midi', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_Midi' );
			
				// do not parse all MIDI tracks - much faster
				if ( $assumedFormat === false )
					ID3_Midi::getMIDIHeaderFilepointer( $localfilepointer, $MP3fileInfo, false );
				else
					ID3_Midi::getMIDIHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'jpg', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_JPG' );
				ID3_JPG::getJPGHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'gif', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_GIF' );
				ID3_GIF::getGIFHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'png', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_PNG' );
				ID3_PNG::getPNGHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'bmp', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_BMP' );
				ID3_BMP::getBMPHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'real', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_Real' );
				ID3_Real::getRealHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'flac', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_Flac' );
				ID3_Flac::getFLACHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'vqf', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_VQF' );
				ID3_VQF::getVQFHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'quicktime', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_Quicktime' );
				ID3_Quicktime::getQuicktimeHeaderFilepointer( $localfilepointer, $MP3fileInfo );
			} 
			else if ( ID3::parseAsThisFormat( 'aac', $assumedFormat, $allowedFormats, $formattest ) ) 
			{
				using( 'format.id3.lib.ID3_AAC' );
			
				if ( !ID3_AAC::getAACADIFheaderFilepointer( $localfilepointer, $MP3fileInfo ) ) 
				{
					$dummy = $MP3fileInfo;
					unset( $dummy['error'] );
					
					if ( ID3_AAC::getAACADTSheaderFilepointer( $localfilepointer, $dummy ) )
						$MP3fileInfo = $dummy;
				}
			} 
			else if ( in_array( 'mp3', $allowedFormats ) && ( $allowedFormats !== false ) && ( ( $assumedFormat == 'mp3' ) || ( ( $assumedFormat == '' ) && ( ( substr( $formattest, 0, 3 ) == 'ID3' ) || ( substr( ID3::bigEndianToBin( substr( $formattest, 0, 2 ) ), 0, 11 ) == '11111111111' ) ) ) ) ) 
			{
				// assume MP3 format (or possibly AAC)
				using( 'format.id3.lib.ID3_MP3' );
				ID3_MP3::getMP3headerFilepointer( $localfilepointer, $MP3fileInfo, true );

				if ( !isset( $MP3fileInfo['audiodataoffset'] ) )
					$MP3fileInfo['audiobytes'] = 0;
				else
					$MP3fileInfo['audiobytes'] = $MP3fileInfo['filesize'] - $MP3fileInfo['audiodataoffset'];
			
				if ( isset( $MP3fileInfo['id3']['id3v1'] ) )
					$MP3fileInfo['audiobytes'] -= 128;
			
				if ( isset( $mp3info['lyrics3']['raw']['lyrics3tagsize'] ) )
					$MP3fileInfo['audiobytes'] -= $mp3info['lyrics3']['raw']['lyrics3tagsize'];
			
				if ( $MP3fileInfo['audiobytes'] <= 0 )
					unset( $MP3fileInfo['audiobytes'] );
			
				if ( !isset( $MP3fileInfo['playtime_seconds'] ) && isset( $MP3fileInfo['audiobytes'] ) && isset( $MP3fileInfo['bitrate_audio'] ) && ( $MP3fileInfo['bitrate_audio'] > 0 ) )
					$MP3fileInfo['playtime_seconds'] = ($MP3fileInfo['audiobytes'] * 8) / $MP3fileInfo['bitrate_audio'];
			}
		}
	
		$CombinedBitrate  = 0;
		$CombinedBitrate += ( isset( $MP3fileInfo['bitrate_audio'] )? $MP3fileInfo['bitrate_audio'] : 0 );
		$CombinedBitrate += ( isset( $MP3fileInfo['bitrate_video'] )? $MP3fileInfo['bitrate_video'] : 0 );
	
		if ( ( $CombinedBitrate > 0 ) && !isset( $MP3fileInfo['bitrate'] ) )
			$MP3fileInfo['bitrate'] = $CombinedBitrate;

		if ( isset( $MP3fileInfo['playtime_seconds'] ) && ( $MP3fileInfo['playtime_seconds'] > 0 ) && !isset( $MP3fileInfo['playtime_string'] ) )
			$MP3fileInfo['playtime_string'] = ID3::playtimeString( $MP3fileInfo['playtime_seconds'] );
	
		if ( isset( $MP3fileInfo['error'] ) && !sizeof( $MP3fileInfo['error'] ) )
			unset($MP3fileInfo['error']);
	
		if ( isset($MP3fileInfo['fileformat'] ) && !$MP3fileInfo['fileformat'] )
			unset( $MP3fileInfo['fileformat'] );
	
		unset( $SourceArrayKey );
	
		// these entries appear in order of precedence
		if ( isset( $MP3fileInfo['asf'] ) )
			$SourceArrayKey = $MP3fileInfo['asf'];
		else if ( isset( $MP3fileInfo['ape'] ) )
			$SourceArrayKey = $MP3fileInfo['ape'];
		else if ( isset( $MP3fileInfo['id3']['id3v2'] ) )
			$SourceArrayKey = $MP3fileInfo['id3']['id3v2'];
		else if ( isset( $MP3fileInfo['id3']['id3v1'] ) )
			$SourceArrayKey = $MP3fileInfo['id3']['id3v1'];
		else if ( isset( $MP3fileInfo['ogg'] ) )
			$SourceArrayKey = $MP3fileInfo['ogg'];
		else if ( isset( $MP3fileInfo['vqf'] ) )
			$SourceArrayKey = $MP3fileInfo['vqf'];
		else if ( isset( $MP3fileInfo['RIFF'] ) )
			$SourceArrayKey = $MP3fileInfo['RIFF'];
		else if ( isset( $MP3fileInfo['quicktime'] ) )
			$SourceArrayKey = $MP3fileInfo['quicktime'];
			
		if ( isset( $SourceArrayKey ) ) 
		{
			$handyaccesskeystocopy = array(
				'title', 
				'artist', 
				'album', 
				'year', 
				'genre', 
				'comment', 
				'track'
			);
			
			foreach ( $handyaccesskeystocopy as $keytocopy ) 
			{
				if ( isset( $SourceArrayKey["$keytocopy"] ) )
					$MP3fileInfo["$keytocopy"] = $SourceArrayKey["$keytocopy"];
			}
		}
		
		if ( isset( $MP3fileInfo['track'] ) )
			$MP3fileInfo['track'] = (int)$MP3fileInfo['track'];

		if ( isset( $localfilepointer ) && is_resource( $localfilepointer ) && ( get_resource_type( $localfilepointer ) == 'file' ) ) 
			fclose( $localfilepointer );
	
		if ( isset( $localfilepointer ) )
			unset( $localfilepointer );
	
 		return $MP3fileInfo;
	}

	function getBasicMP3info( $filename, $getID3v1 = true, $getID3v2 = true, $getMPEG = true ) 
	{
		// optimized-for-speed version that doesn't have all the security checks or flexibility
		// of different file formats of getAllMP3info (only works for MP3). Only works on local files.
		// Supposed to be faster, but isn't much faster than getAllMP3info() unless getMPEG == false
		// For testing only, will be removed in future/final releases

		$MP3fileInfo['fileformat'] = '';      // filled in later
		$MP3fileInfo['error']      = array(); // filled in later, unset if not used
		$MP3fileInfo['filename']   = basename( $filename );
		$MP3fileInfo['exist']      = false;

		if ( $fd = @fopen( $filename, 'rb' ) ) 
		{
			$MP3fileInfo['exist'] = true;
			clearstatcache();
			$MP3fileInfo['filesize'] = filesize( $filename );

			if ( $getID3v1 ) 
			{
				if ( $ID3v1info = ID3::getID3v1Filepointer( $fd ) ) 
				{
					$MP3fileInfo['id3']['id3v1'] = $ID3v1info;
					$MP3fileInfo['fileformat']   = 'id3';
				}
			}

			$audiodataoffset = 0;
		
			if ( $getID3v2 ) 
			{
				ID3::getID3v2Filepointer( $fd, $MP3fileInfo );
				
				if ( isset( $MP3fileInfo['id3']['id3v2']['header'] ) ) 
				{
					$MP3fileInfo['fileformat'] = 'id3';
					$audiodataoffset = $MP3fileInfo['id3']['id3v2']['headerlength'];
				}
				// no ID3v2 header 
				else 
				{
					if ( isset( $MP3fileInfo['id3']['id3v2'] ) )
						unset( $MP3fileInfo['id3']['id3v2'] );
				}
			}

			if ( isset( $MP3fileInfo['id3'] ) && !isset( $MP3fileInfo['id3']['id3v1'] ) && !isset( $MP3fileInfo['id3']['id3v2'] ) )
				unset( $MP3fileInfo['id3'] );

			if ( $getMPEG ) 
			{
				using( 'format.id3.lib.ID3_MP3' );
				ID3_MP3::getOnlyMPEGaudioInfo( $fd, $MP3fileInfo, $audiodataoffset, false );

				if ( !isset( $MP3fileInfo['audiodataoffset'] ) )
					$MP3fileInfo['audiobytes'] = 0;
				else
					$MP3fileInfo['audiobytes'] = $MP3fileInfo['filesize'] - $MP3fileInfo['audiodataoffset'];
				
				if ( isset($MP3fileInfo['id3']['id3v1'] ) )
					$MP3fileInfo['audiobytes'] -= 128;
			
				if ( isset( $mp3info['lyrics3']['raw']['lyrics3tagsize'] ) )
					$MP3fileInfo['audiobytes'] -= $mp3info['lyrics3']['raw']['lyrics3tagsize'];
			
				if ( $MP3fileInfo['audiobytes'] <= 0 )
					unset( $MP3fileInfo['audiobytes'] );
			
				if ( !isset( $MP3fileInfo['playtime_seconds'] ) && isset( $MP3fileInfo['audiobytes'] ) && isset( $MP3fileInfo['bitrate_audio'] ) && ( $MP3fileInfo['bitrate_audio'] > 0 ) )
					$MP3fileInfo['playtime_seconds'] = ( $MP3fileInfo['audiobytes'] * 8 ) / $MP3fileInfo['bitrate_audio'];
			}
		}
	
		$CombinedBitrate  = 0;
		$CombinedBitrate += ( isset( $MP3fileInfo['bitrate_audio'] )? $MP3fileInfo['bitrate_audio'] : 0 );
		$CombinedBitrate += ( isset( $MP3fileInfo['bitrate_video'] )? $MP3fileInfo['bitrate_video'] : 0 );
	
		if ( ( $CombinedBitrate > 0 ) && !isset( $MP3fileInfo['bitrate'] ) )
			$MP3fileInfo['bitrate'] = $CombinedBitrate;
	
		if ( isset( $MP3fileInfo['playtime_seconds'] ) && ( $MP3fileInfo['playtime_seconds'] > 0 ) && !isset( $MP3fileInfo['playtime_string'] ) )
			$MP3fileInfo['playtime_string'] = ID3::playtimeString( $MP3fileInfo['playtime_seconds'] );
	
		if ( isset( $MP3fileInfo['error'] ) && !sizeof( $MP3fileInfo['error'] ) )
			unset( $MP3fileInfo['error'] );
	
		if ( isset( $MP3fileInfo['fileformat'] ) && !$MP3fileInfo['fileformat'] )
			unset( $MP3fileInfo['fileformat'] );
	
		unset( $SourceArrayKey );
		
		// these entries appear in order of precedence
		if ( isset( $MP3fileInfo['id3']['id3v2'] ) )
			$SourceArrayKey = $MP3fileInfo['id3']['id3v2'];
		else if ( isset( $MP3fileInfo['id3']['id3v1'] ) )
			$SourceArrayKey = $MP3fileInfo['id3']['id3v1'];
	
		if ( isset( $SourceArrayKey ) ) 
		{
			$handyaccesskeystocopy = array(
				'title', 
				'artist', 
				'album', 
				'year', 
				'genre', 
				'comment', 
				'track'
			);
		
			foreach ( $handyaccesskeystocopy as $keytocopy ) 
			{
				if ( isset( $SourceArrayKey["$keytocopy"] ) )
					$MP3fileInfo["$keytocopy"] = $SourceArrayKey["$keytocopy"];
			}
		}
	
		if ( isset( $MP3fileInfo['track'] ) )
			$MP3fileInfo['track'] = (int)$MP3fileInfo['track'];

		if ( isset( $fd ) && is_resource( $fd ) && ( get_resource_type( $fd ) == 'file' ) )
			fclose( $fd );
	
		if ( isset( $fd ) )
			unset( $fd );
	
 		return $MP3fileInfo;
	}
	
	function parseAsThisFormat( $format, $assumedFormat, $allowedFormats, $formattest ) 
	{
		if ( $assumedFormat == $format )
			return true;

		$FormatTestStrings['PK']   = 'zip';  // ZIP
		$FormatTestStrings['OggS'] = 'ogg';  // Ogg Vorbis
		$FormatTestStrings['RIFF'] = 'riff'; // RIFF: WAVE / AVI
		$FormatTestStrings['SDSS'] = 'riff'; // simply a renamed RIFF-WAVE format, identical except for the 1st 4 chars, used by SmartSound QuickTracks (www.smartsound.com)
		$FormatTestStrings['MThd'] = 'midi'; // MIDI
		$FormatTestStrings['MAC '] = 'mac';  // Monkey's Audio
		$FormatTestStrings['MP+']  = 'mpc';  // Musepack / MPEGplus
		$FormatTestStrings['BM']   = 'bmp';  // Bitmap
		$FormatTestStrings['GIF']  = 'gif';  // GIF
		$FormatTestStrings['.RMF'] = 'real'; // RealAudio / RealVideo
		$FormatTestStrings['fLaC'] = 'flac'; // FLAC
		$FormatTestStrings['TWIN'] = 'vqf';  // VQF
		$FormatTestStrings['ADIF'] = 'aac';  // AAC
		$FormatTestStrings['LA02'] = 'la';   // LA (v0.2)
		$FormatTestStrings['LA03'] = 'la';   // LA (v0.3)

		// JPEG
		$FormatTestStrings2['jpg']  = chr( 0xFF ) . chr( 0xD8 ) . chr( 0xFF );

		// MPRG
		$FormatTestStrings2['mpeg'] = chr( 0x00 ) . chr( 0x00 ) . chr( 0x01 ) . chr( 0xBA );
		
		// ASF / WMA (Windows Media Audio) / WMV (Windows Media Video)
		$FormatTestStrings2['asf']  = chr( 0x30 ) . chr( 0x26 ) . chr( 0xB2 ) . chr( 0x75 ) . chr( 0x8E ) . chr( 0x66 ) . chr( 0xCF ) . chr( 0x11 ) . chr( 0xA6 ) . chr( 0xD9 ) . chr( 0x00 ) . chr( 0xAA ) . chr( 0x00 ) . chr( 0x62 ) . chr( 0xCE ) . chr( 0x6C );
		
		// PNG
		$FormatTestStrings2['png']  = chr( 0x89 ) . chr( 0x50 ) . chr( 0x4E ) . chr( 0x47 ) . chr( 0x0D ) . chr( 0x0A ) . chr( 0x1A ) . chr( 0x0A );

		if ( $assumedFormat == '' ) 
		{
			foreach ( $FormatTestStrings as $key => $value ) 
			{
				if ( $format == $value ) 
				{
					if ( substr( $formattest, 0, strlen( $key ) ) == $key )
						return true;
				}
			}
		
			foreach ( $FormatTestStrings2 as $key => $value ) 
			{
				if ( $format == $key ) 
				{
					if ( substr( $formattest, 0, strlen( $value ) ) == $value )
						return true;
				}
			}
		}

		if ( $format == 'quicktime' ) 
		{
			switch ( substr( $formattest, 4, 4 ) ) 
			{
				case 'cmov':
			
				case 'free':
			
				case 'mdat':
			
				case 'moov':
			
				case 'pnot':
			
				case 'skip':
			
				case 'wide':
					return true;
					break;

				default:
					// not a recognized quicktime atom, disregard
					break;
			}
		}
	
		return false;
	}

	function RGADnameLookup( $namecode ) 
	{
		static $RGADname = array();
	
		if ( count( $RGADname ) < 1 ) 
		{
			$RGADname[bindec( '000' )] = 'not set';
			$RGADname[bindec( '001' )] = 'Radio Gain Adjustment';
			$RGADname[bindec( '010' )] = 'Audiophile Gain Adjustment';
		}

		return ( isset( $RGADname["$namecode"] )? $RGADname["$namecode"] : '' );
	}

	function RGADoriginatorLookup( $originatorcode ) 
	{
		static $RGADoriginator = array();
	
		if ( count( $RGADoriginator ) < 1 ) 
		{
			$RGADoriginator[0] = 'unspecified';
			$RGADoriginator[1] = 'pre-set by artist/producer/mastering engineer';
			$RGADoriginator[2] = 'set by user';
			$RGADoriginator[3] = 'determined automatically';
		}

		return ( isset( $RGADoriginator["$originatorcode"] )? $RGADoriginator["$originatorcode"] : '' );
	}

	function RGADadjustmentLookup( $rawadjustment, $signbit ) 
	{
		$adjustment = $rawadjustment / 10;
	
		if ( $signbit == 1 )
			$adjustment *= -1;
	
		return (float)$adjustment;
	}

	function RGADgainString( $namecode, $originatorcode, $replaygain ) 
	{
		if ( $replaygain < 0 )
			$signbit = '1';
		else
			$signbit = '0';
	
		$storedreplaygain = round( $replaygain * 10 );
		
		$gainstring  = str_pad( decbin( $namecode ),       3, '0', STR_PAD_LEFT );
		$gainstring .= str_pad( decbin( $originatorcode ), 3, '0', STR_PAD_LEFT );
		$gainstring .= $signbit;
		$gainstring .= str_pad( decbin( round( $replaygain * 10 ) ), 9, '0', STR_PAD_LEFT );

		return $gainstring;
	}

	function ID3v2FrameProcessing( $frame_name, $frame_flags, &$MP3fileInfo ) 
	{
		// define $frame_arrayindex once here (used for many frames), override or ignore as neccesary
		$frame_arrayindex = count( $MP3fileInfo['id3']['id3v2']["$frame_name"] ); // 'data', 'datalength'

		if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) )
			$frame_arrayindex--;

		if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] ) )
			$frame_arrayindex--;

		if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] ) )
			$frame_arrayindex--;

		if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] ) )
			$frame_arrayindex--;

		if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['timestampformat'] ) )
			$frame_arrayindex--;

		// frame flags are not part of the ID3v2.2 standard
		if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
		{
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 ) 
			{
				// Frame Header Flags
				// %abc00000 %ijk00000
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['TagAlterPreservation']  = (bool)substr( $frame_flags,  0, 1 ); // a - Tag alter preservation
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['FileAlterPreservation'] = (bool)substr( $frame_flags,  1, 1 ); // b - File alter preservation
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['ReadOnly']              = (bool)substr( $frame_flags,  2, 1 ); // c - Read only
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['compression']           = (bool)substr( $frame_flags,  8, 1 ); // i - Compression
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['Encryption']            = (bool)substr( $frame_flags,  9, 1 ); // j - Encryption
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['GroupingIdentity']      = (bool)substr( $frame_flags, 10, 1 ); // k - Grouping identity
			} 
			else if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 4 ) 
			{
				// Frame Header Flags
				// %0abc0000 %0h00kmnp
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['TagAlterPreservation']  = (bool)substr( $frame_flags,  1, 1 ); // a - Tag alter preservation
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['FileAlterPreservation'] = (bool)substr( $frame_flags,  2, 1 ); // b - File alter preservation
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['ReadOnly']              = (bool)substr( $frame_flags,  3, 1 ); // c - Read only
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['GroupingIdentity']      = (bool)substr( $frame_flags,  9, 1 ); // h - Grouping identity
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['compression']           = (bool)substr( $frame_flags, 12, 1 ); // k - Compression
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['Encryption']            = (bool)substr( $frame_flags, 13, 1 ); // m - Encryption
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['Unsynchronisation']     = (bool)substr( $frame_flags, 14, 1 ); // n - Unsynchronisation
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['DataLengthIndicator']   = (bool)substr( $frame_flags, 15, 1 ); // p - Data length indicator
			}
			
			//	Frame-level de-unsynchronization - ID3v2.4
			if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['Unsynchronisation'] ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = ID3::deUnSynchronise( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );

			//	Frame-level de-compression
			if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['compression'] ) ) 
			{
				// it's on the wishlist :)
			}
		}

		if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'UFID' ) ) ||  // 4.1 UFID Unique file identifier
			 ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'UFI'  ) ) ) 	// 4.1 UFI  Unique file identifier
		{
			// There may be more than one 'UFID' frame in a tag,
			// but only one with the same 'Owner identifier'.
			// <Header for 'Unique file identifier', ID: 'UFID'>
			// Owner identifier        <text string> $00
			// Identifier              <up to 64 bytes binary data>

			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ) );
			$frame_idstring = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 0, $frame_terminatorpos );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['ownerid'] = $frame_idstring;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']    = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( chr( 0 ) ) );
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'TXXX' ) ) || // 4.2.2 TXXX User defined text information frame
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'TXX'  ) ) )  // 4.2.2 TXX  User defined text information frame
		{
			// There may be more than one 'TXXX' frame in each tag,
			// but only one with the same description.
			// <Header for 'User defined text information frame', ID: 'TXXX'>
			// Text encoding     $xx
			// Description       <text string according to encoding> $00 (00)
			// Value             <text string according to encoding>

			$frame_offset = 0;
			$frame_textencoding  = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup( 'terminator', $frame_textencoding ), $frame_offset );
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 ) 
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid'] = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']   = ID3::textEncodingLookup( 'encoding', $frame_textencoding );

			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description'] = $frame_description;
		
			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || (  $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) ) 
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription'] = ID3::roughTranslateUnicodeToASCII( $frame_description, $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ) );
			
			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidata'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data'], $frame_textencoding );
			
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 )
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.2. T??[?] Text information frame 
		else if ( $frame_name{0} == 'T' ) 
		{
			// There may only be one text information frame of its kind in an tag.
			// <Header for 'Text information frame', ID: 'T000' - 'TZZZ',
			// excluding 'TXXX' described in 4.2.6.>
			// Text encoding                $xx
			// Information                  <text string(s) according to encoding>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );

			// $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr($MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset);
			// this one-line method should work, but as a safeguard against null-padded data, do it the safe way
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup( 'terminator', $frame_textencoding ), $frame_offset );

			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			if ( $frame_terminatorpos ) 
			{
				// there are null bytes after the data - this is not according to spec
				// only use data up to first null byte
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			} 
			else 
			{
				// no null bytes following data, just use all data
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			}

			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['compression'] ) || !$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['compression'] )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['asciidata'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['encodingid']    = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['encoding']      = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'WXXX' ) ) || // 4.3.2 WXXX User defined URL link frame
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'WXX'  ) ) )  // 4.3.2 WXX  User defined URL link frame
		{ 
			// There may be more than one 'WXXX' frame in each tag,
			// but only one with the same description
			// <Header for 'User defined URL link frame', ID: 'WXXX'>
			// Text encoding     $xx
			// Description       <text string according to encoding> $00 (00)
			// URL               <text string>

			$frame_offset = 0;
			$frame_textencoding  = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup( 'terminator', $frame_textencoding ), $frame_offset );
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 ) 
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );

			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup( 'terminator', $frame_textencoding ) );
			
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			if ( $frame_terminatorpos ) 
			{
				// there are null bytes after the data - this is not according to spec
				// only use data up to first null byte
				$frame_urldata = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 0, $frame_terminatorpos );
			} 
			else 
			{
				// no null bytes following data, just use all data
				$frame_urldata = $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'];
			}

			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid']  = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']    = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['url']         = $frame_urldata;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description'] = $frame_description;

			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ($MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription'] = ID3::roughTranslateUnicodeToASCII( $frame_description, $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.3. W??? URL link frames 
		else if ( $frame_name{0} == 'W' ) 
		{
			// There may only be one URL link frame of its kind in a tag,
			// except when stated otherwise in the frame description
			// <Header for 'URL link frame', ID: 'W000' - 'WZZZ', excluding 'WXXX'
			// described in 4.3.2.>
			// URL              <text string>

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['url'] = trim( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 ) && ( $frame_name == 'IPLS' ) ) || // 4.4  IPLS Involved people list (ID3v2.3 only)
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'IPL'  ) ) )  // 4.4  IPL  Involved people list (ID3v2.2 only)
		{
			// There may only be one 'IPL' frame in each tag
			// <Header for 'User defined URL link frame', ID: 'IPL'>
			// Text encoding     $xx
			// People list strings    <textstrings>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['encodingid']    = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['encoding']      = ID3::textEncodingLookup( 'encoding', $MP3fileInfo['id3']['id3v2']["$frame_name"]['encodingid'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data']          = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['asciidata']     = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'MCDI' ) ) || // 4.4   MCDI Music CD identifier
			      ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'MCI'  ) ) )  // 4.5   MCI  Music CD identifier
		{
			// There may only be one 'MCDI' frame in each tag
			// <Header for 'Music CD identifier', ID: 'MCDI'>
			// CD TOC                <binary data>

			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			// no other special processing needed
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'ETCO' ) ) || // 4.5   ETCO Event timing codes
			      ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'ETC'  ) ) )  // 4.6   ETC  Event timing codes
		{
			// There may only be one 'ETCO' frame in each tag
			// <Header for 'Event timing codes', ID: 'ETCO'>
			// Time stamp format    $xx
			// Where time stamp format is:
			// $01  (32-bit value) MPEG frames from beginning of file
			// $02  (32-bit value) milliseconds from beginning of file
			// Followed by a list of key events in the following format:
			// Type of event   $xx
			// Time stamp      $xx (xx ...)
			// The 'Time stamp' is set to zero if directly at the beginning of the sound
			// or after the previous event. All events MUST be sorted in chronological order.

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['timestampformat'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );

			while ( $frame_offset < strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['typeid']    = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['type']      = ID3::ETCOEventLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['typeid'] );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['timestamp'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
				$frame_offset += 4;
			}
			
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 )
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'MLLT' ) ) || // 4.6   MLLT MPEG location lookup table
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'MLL'  ) ) )  // 4.7   MLL MPEG location lookup table
		{
			// There may only be one 'MLLT' frame in each tag
			// <Header for 'Location lookup table', ID: 'MLLT'>
			// MPEG frames between reference  $xx xx
			// Bytes between reference        $xx xx xx
			// Milliseconds between reference $xx xx xx
			// Bits for bytes deviation       $xx
			// Bits for milliseconds dev.     $xx
			// Then for every reference the following data is included;
			// Deviation in bytes         %xxx....
			// Deviation in milliseconds  %xxx....

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framesbetweenreferences'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 0, 2 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bytesbetweenreferences']  = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 2, 3 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['msbetweenreferences']     = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 5, 3 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsforbytesdeviation']   = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 8, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsformsdeviation']      = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 9, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 10 );
	
			while ( $frame_offset < strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) )
				$deviationbitstream .= ID3::bigEndianToBin( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
		
			while ( strlen( $deviationbitstream ) ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['bytedeviation'] = bindec( substr( $deviationbitstream, 0, $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsforbytesdeviation'] ) );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['msdeviation']   = bindec( substr( $deviationbitstream, $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsforbytesdeviation'], $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsformsdeviation'] ) );
				$deviationbitstream = substr( $deviationbitstream, $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsforbytesdeviation'] + $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsformsdeviation'] );
				$frame_arrayindex++;
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'SYTC' ) ) || // 4.7   SYTC Synchronised tempo codes
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'STC'  ) ) )  // 4.8   STC  Synchronised tempo codes
		{
			// There may only be one 'SYTC' frame in each tag
			// <Header for 'Synchronised tempo codes', ID: 'SYTC'>
			// Time stamp format   $xx
			// Tempo data          <binary data>
			// Where time stamp format is:
			// $01  (32-bit value) MPEG frames from beginning of file
			// $02  (32-bit value) milliseconds from beginning of file

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['timestampformat'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
		
			while ( $frame_offset < strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['tempo'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			
				if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['tempo'] == 255 )
					$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['tempo'] += ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['timestamp'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
				$frame_offset += 4;
				$frame_arrayindex++;
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'USLT' ) ) || // 4.8   USLT Unsynchronised lyric/text transcription
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'ULT'  ) ) )  // 4.9   ULT  Unsynchronised lyric/text transcription
		{
			// There may be more than one 'Unsynchronised lyrics/text transcription' frame
			// in each tag, but only one with the same language and content descriptor.
			// <Header for 'Unsynchronised lyrics/text transcription', ID: 'USLT'>
			// Text encoding        $xx
			// Language             $xx xx xx
			// Content descriptor   <text string according to encoding> $00 (00)
			// Lyrics/text          <full text string according to encoding>
		
			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_language = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 );
			$frame_offset += 3;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup('terminator', $frame_textencoding), $frame_offset );

			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid']   = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']     = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']         = $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['language']     = $frame_language;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['languagename'] = ID3::languageLookup( $frame_language, false );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description']  = $frame_description;

			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description'], $frame_textencoding );
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidata'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data'], $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'SYLT' ) ) || // 4.9   SYLT Synchronised lyric/text
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'SLT'  ) ) )  // 4.10  SLT  Synchronised lyric/text
		{
			// There may be more than one 'SYLT' frame in each tag,
			// but only one with the same language and content descriptor.
			// <Header for 'Synchronised lyrics/text', ID: 'SYLT'>
			// Text encoding        $xx
			// Language             $xx xx xx
			// Time stamp format    $xx
			// $01  (32-bit value) MPEG frames from beginning of file
			// $02  (32-bit value) milliseconds from beginning of file
			// Content type         $xx
			// Content descriptor   <text string according to encoding> $00 (00)
			// Terminated text to be synced (typically a syllable)
			// Sync identifier (terminator to above string)   $00 (00)
			// Time stamp                                     $xx (xx ...)

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_language = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 );
			$frame_offset += 3;

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['timestampformat'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['contenttypeid']   = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['contenttype']     = ID3::SYTLContentTypeLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['contenttypeid'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid']      = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']        = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['language']        = $frame_language;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['languagename']    = ID3::languageLookup( $frame_language, false );

			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}

			$timestampindex = 0;
			$frame_remainingdata = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
		
			while ( strlen( $frame_remainingdata ) ) 
			{
				$frame_offset = 0;
				$frame_terminatorpos = strpos( $frame_remainingdata, ID3::textEncodingLookup( 'terminator', $frame_textencoding ) );
			
				if ( $frame_terminatorpos === false ) 
				{
					$frame_remainingdata = '';
				} 
				else 
				{
					if ( ord( substr( $frame_remainingdata, $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 ) 
						$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
				
					$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']["$timestampindex"]['data'] = substr( $frame_remainingdata, $frame_offset, $frame_terminatorpos - $frame_offset );
				
					if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
						$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']["$timestampindex"]['asciidata'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']["$timestampindex"]['data'], $frame_textencoding );

					$frame_remainingdata = substr( $frame_remainingdata, $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ) );
					
					if ( ( $timestampindex == 0 ) && ( ord( $frame_remainingdata{0} ) != 0 ) ) 
					{
						// timestamp probably omitted for first data item
					}	
					else 
					{
						$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']["$timestampindex"]['timestamp'] = ID3::bigEndianToInt( substr( $frame_remainingdata, 0, 4 ) );
						$frame_remainingdata = substr( $frame_remainingdata, 4 );
					}
				
					$timestampindex++;
				}
			}

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data' ]);
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'COMM' ) ) || // 4.10  COMM Comments
				    ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'COM'  ) ) )  // 4.11  COM  Comments
		{
			// There may be more than one comment frame in each tag,
			// but only one with the same language and content descriptor.
			// <Header for 'Comment', ID: 'COMM'>
			// Text encoding          $xx
			// Language               $xx xx xx
			// Short content descrip. <text string according to encoding> $00 (00)
			// The actual text        <full text string according to encoding>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_language = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 );
			$frame_offset += 3;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup( 'terminator', $frame_textencoding ), $frame_offset );

			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup('terminator', $frame_textencoding ) ), 1 ) ) === 0 ) 
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			$frame_text = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid']   = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']     = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['language']     = $frame_language;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['languagename'] = ID3::languageLookup( $frame_language, false );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description']  = $frame_description;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']         = $frame_text;
	
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription'] = ID3::roughTranslateUnicodeToASCII( $frame_description, $frame_textencoding );
		
			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidata'] = ID3::roughTranslateUnicodeToASCII( $frame_text, $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		// 4.11  RVA2 Relative volume adjustment (2) (ID3v2.4+ only)
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 4 ) && ( $frame_name == 'RVA2' ) ) 
		{
			// There may be more than one 'RVA2' frame in each tag,
			// but only one with the same identification string
			// <Header for 'Relative volume adjustment (2)', ID: 'RVA2'>
			// Identification          <text string> $00
			// The 'identification' string is used to identify the situation and/or
			// device where this adjustment should apply. The following is then
			// repeated for every channel:
			// Type of channel         $xx
			// Volume adjustment       $xx xx
			// Bits representing peak  $xx
			// Peak volume             $xx (xx ...)

			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ) );
			$frame_idstring = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], 0, $frame_terminatorpos );
		
			if ( ord( $frame_idstring ) === 0 )
				$frame_idstring = '';
		
			$frame_remainingdata = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( chr( 0 ) ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description'] = $frame_idstring;
		
			while ( strlen( $frame_remainingdata ) ) 
			{
				$frame_offset = 0;
				$frame_channeltypeid = substr( $frame_remainingdata, $frame_offset++, 1 );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]["$frame_channeltypeid"]['channeltypeid'] = $frame_channeltypeid;
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]["$frame_channeltypeid"]['channeltype']   = ID3::RVA2ChannelTypeLookup( $frame_channeltypeid );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]["$frame_channeltypeid"]['volumeadjust']  = ID3::bigEndianToInt( substr( $frame_remainingdata, $frame_offset, 2), false, true ); // 16-bit signed
				$frame_offset += 2;
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]["$frame_channeltypeid"]['bitspeakvolume'] = ord( substr( $frame_remainingdata, $frame_offset++, 1 ) );
				$frame_bytespeakvolume = ceil( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_channeltypeid"]['bitspeakvolume'] / 8 );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]["$frame_channeltypeid"]['peakvolume'] = ID3::bigEndianToInt( substr( $frame_remainingdata, $frame_offset, $frame_bytespeakvolume ) );
				$frame_remainingdata = substr( $frame_remainingdata, $frame_offset + $frame_bytespeakvolume );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]["$frame_channeltypeid"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 ) && ( $frame_name == 'RVAD' ) ) || // 4.12  RVAD Relative volume adjustment (ID3v2.3 only)
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'RVA'  ) ) )  // 4.12  RVA  Relative volume adjustment (ID3v2.2 only)
		{
			// There may only be one 'RVA' frame in each tag
			// <Header for 'Relative volume adjustment', ID: 'RVA'>
			// ID3v2.2 => Increment/decrement     %000000ba
			// ID3v2.3 => Increment/decrement     %00fedcba
			// Bits used for volume descr.        $xx
			// Relative volume change, right      $xx xx (xx ...) // a
			// Relative volume change, left       $xx xx (xx ...) // b
			// Peak volume right                  $xx xx (xx ...)
			// Peak volume left                   $xx xx (xx ...)
			// ID3v2.3 only, optional (not present in ID3v2.2):
			// Relative volume change, right back $xx xx (xx ...) // c
			// Relative volume change, left back  $xx xx (xx ...) // d
			// Peak volume right back             $xx xx (xx ...)
			// Peak volume left back              $xx xx (xx ...)
			// ID3v2.3 only, optional (not present in ID3v2.2):
			// Relative volume change, center     $xx xx (xx ...) // e
			// Peak volume center                 $xx xx (xx ...)
			// ID3v2.3 only, optional (not present in ID3v2.2):
			// Relative volume change, bass       $xx xx (xx ...) // f
			// Peak volume bass                   $xx xx (xx ...)

			$frame_offset = 0;
			$frame_incrdecrflags = ID3::bigEndianToBin( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['right'] = (bool)substr( $frame_incrdecrflags, 6, 1 );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['left']  = (bool)substr( $frame_incrdecrflags, 7, 1 );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsvolume'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_bytesvolume = ceil( $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsvolume'] / 8 );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['right'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );

			if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['right'] === false )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['right'] *= -1;
		
			$frame_offset += $frame_bytesvolume;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['left'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
		
			if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['left'] === false )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['left'] *= -1;
		
			$frame_offset += $frame_bytesvolume;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakvolume']['right'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
			$frame_offset += $frame_bytesvolume;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakvolume']['left']  = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
			$frame_offset += $frame_bytesvolume;
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			
				if ( strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) > 0 ) 
				{
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['rightrear'] = (bool)substr( $frame_incrdecrflags, 4, 1 );
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['leftrear']  = (bool)substr( $frame_incrdecrflags, 5, 1 );
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['rightrear'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume) );
				
					if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['rightrear'] === false )
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['rightrear'] *= -1;
				
					$frame_offset += $frame_bytesvolume;
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['leftrear'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
				
					if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['leftrear'] === false )
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['leftrear'] *= -1;
				
					$frame_offset += $frame_bytesvolume;
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakvolume']['rightrear'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
					$frame_offset += $frame_bytesvolume;
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakvolume']['leftrear']  = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
					$frame_offset += $frame_bytesvolume;
				}
				
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			
				if ( strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) > 0 ) 
				{
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['center'] = (bool)substr( $frame_incrdecrflags, 3, 1 );
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['center'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
				
					if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['center'] === false )
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['center'] *= -1;
				
					$frame_offset += $frame_bytesvolume;
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakvolume']['center'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
					$frame_offset += $frame_bytesvolume;
				}
				
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			
				if ( strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] ) > 0 ) 
				{
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['bass'] = (bool)substr( $frame_incrdecrflags, 2, 1 );
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['bass'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
				
					if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]['incdec']['bass'] === false )
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['volumechange']['bass'] *= -1;
				
					$frame_offset += $frame_bytesvolume;
					$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakvolume']['bass'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesvolume ) );
					$frame_offset += $frame_bytesvolume;
				}
			}
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		}
		// 4.12  EQU2 Equalisation (2) (ID3v2.4+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 4 ) && ( $frame_name == 'EQU2' ) ) 
		{
			// There may be more than one 'EQU2' frame in each tag,
			// but only one with the same identification string
			// <Header of 'Equalisation (2)', ID: 'EQU2'>
			// Interpolation method  $xx
			// $00  Band
			// $01  Linear
			// Identification        <text string> $00
			// The following is then repeated for every adjustment point
			// Frequency          $xx xx
			// Volume adjustment  $xx xx

			$frame_offset = 0;
			$frame_interpolationmethod = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_terminatorpos       = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_idstring            = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );

			if ( ord( $frame_idstring ) === 0 )
				$frame_idstring = '';
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description'] = $frame_idstring;
			$frame_remainingdata = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( chr( 0 ) ) );
		
			while ( strlen( $frame_remainingdata ) ) 
			{
				$frame_frequency = ID3::bigEndianToInt( substr( $frame_remainingdata, 0, 2 ) ) / 2;
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']["$frame_frequency"] = ID3::bigEndianToInt( substr( $frame_remainingdata, 2, 2 ), false, true );
				$frame_remainingdata = substr( $frame_remainingdata, 4 );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['interpolationmethod'] = $frame_interpolationmethod;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 ) && ( $frame_name == 'EQUA' ) ) || // 4.12  EQUA Equalisation (ID3v2.3 only)
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'EQU'  ) ) )  // 4.13  EQU  Equalisation (ID3v2.2 only)
		{
			// There may only be one 'EQUA' frame in each tag
			// <Header for 'Relative volume adjustment', ID: 'EQU'>
			// Adjustment bits    $xx
			// This is followed by 2 bytes + ('adjustment bits' rounded up to the
			// nearest byte) for every equalisation band in the following format,
			// giving a frequency range of 0 - 32767Hz:
			// Increment/decrement   %x (MSB of the Frequency)
			// Frequency             (lower 15 bits)
			// Adjustment            $xx (xx ...)

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['adjustmentbits'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 );
			$frame_adjustmentbytes = ceil( $MP3fileInfo['id3']['id3v2']["$frame_name"]['adjustmentbits'] / 8  );
			$frame_remainingdata = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			
			while ( strlen( $frame_remainingdata ) ) 
			{
				$frame_frequencystr = ID3::bigEndianToBin( substr( $frame_remainingdata, 0, 2 ) );
				$frame_incdec = (bool)substr( $frame_frequencystr, 0, 1 );
				$frame_frequency = bindec( substr( $frame_frequencystr, 1, 15 ) );
				
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_frequency"]['incdec']     = $frame_incdec;
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_frequency"]['adjustment'] = ID3::bigEndianToInt( substr( $frame_remainingdata, 2, $frame_adjustmentbytes ) );
			
				if ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_frequency"]['incdec'] === false )
					$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_frequency"]['adjustment'] *= -1;
			
				$frame_remainingdata = substr($frame_remainingdata, 2 + $frame_adjustmentbytes);
			}
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'RVRB' ) ) || // 4.13  RVRB Reverb
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'REV'  ) ) )  // 4.14  REV  Reverb
		{
			// There may only be one 'RVRB' frame in each tag.
			// <Header for 'Reverb', ID: 'RVRB'>
			// Reverb left (ms)                 $xx xx
			// Reverb right (ms)                $xx xx
			// Reverb bounces, left             $xx
			// Reverb bounces, right            $xx
			// Reverb feedback, left to left    $xx
			// Reverb feedback, left to right   $xx
			// Reverb feedback, right to right  $xx
			// Reverb feedback, right to left   $xx
			// Premix left to right             $xx
			// Premix right to left             $xx

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['left']  = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['right'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bouncesL']      = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bouncesR']      = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['feedbackLL']    = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['feedbackLR']    = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['feedbackRR']    = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['feedbackRL']    = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['premixLR']      = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['premixRL']      = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'APIC' ) ) || // 4.14  APIC Attached picture
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'PIC'  ) ) )  // 4.15  PIC  Attached picture
		{
			// There may be several pictures attached to one file,
			// each in their individual 'APIC' frame, but only one
			// with the same content descriptor
			// <Header for 'Attached picture', ID: 'APIC'>
			// Text encoding      $xx
			// ID3v2.3+ => MIME type          <text string> $00
			// ID3v2.2  => Image format       $xx xx xx
			// Picture type       $xx
			// Description        <text string according to encoding> $00 (00)
			// Picture data       <binary data>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );

			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) 
			{
				$frame_imagetype = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 );
			
				if ( strtolower( $frame_imagetype ) == 'ima' ) 
				{
					// complete hack for mp3Rage (www.chaoticsoftware.com) that puts ID3v2.3-formatted
					// MIME type instead of 3-char ID3v2.2-format image type  (thanks xbhoff@pacbell.net)
					$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
					$frame_mimetype = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
				
					if ( ord( $frame_mimetype ) === 0 )
						$frame_mimetype = '';
				
					$frame_imagetype = strtoupper( str_replace( 'image/', '', strtolower( $frame_mimetype ) ) );
				
					if ( $frame_imagetype == 'JPEG' )
						$frame_imagetype = 'JPG';
				
					$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
				} 
				else 
				{
					$frame_offset += 3;
				}
			}
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] > 2 ) 
			{
				$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
				$frame_mimetype = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			
				if ( ord( $frame_mimetype ) === 0 )
					$frame_mimetype = '';
			
				$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
			}

			$frame_picturetype   = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup('terminator', $frame_textencoding), $frame_offset );		
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid'] = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']   = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['imagetype'] = $frame_imagetype;
			else
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['mime']      = $frame_mimetype;
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['picturetypeid'] = $frame_picturetype;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['picturetype']   = ID3::APICPictureTypeLookup( $frame_picturetype );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description']   = $frame_description;

			if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription'] = ID3::roughTranslateUnicodeToASCII( $frame_description, $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ) );
			$imagechunkcheck = ID3::getDataImageSize( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data'] );

			if ( ( $imagechunkcheck[2 ] >= 1 ) && ( $imagechunkcheck[2] <= 3 ) ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['image_mime'] = ID3::imageTypesLookup( $imagechunkcheck[2] );

				if ( $imagechunkcheck[0] )
					$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['image_width']  = $imagechunkcheck[0];
			
				if ( $imagechunkcheck[1] )
					$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['image_height'] = $imagechunkcheck[1];
			
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['image_bytes'] = strlen( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data'] );
			}

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		
			if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] ) ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			}
			
			if ( isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] ) ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
			}
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'GEOB' ) ) || // 4.15  GEOB General encapsulated object
			 	  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'GEO'  ) ) )  // 4.16  GEO  General encapsulated object
		{
			// There may be more than one 'GEOB' frame in each tag,
			// but only one with the same content descriptor
			// <Header for 'General encapsulated object', ID: 'GEOB'>
			// Text encoding          $xx
			// MIME type              <text string> $00
			// Filename               <text string according to encoding> $00 (00)
			// Content description    <text string according to encoding> $00 (00)
			// Encapsulated object    <binary data>

			$frame_offset = 0;
			$frame_textencoding = ord( substr($MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_mimetype = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );

			if ( ord( $frame_mimetype ) === 0 )
				$frame_mimetype = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup('terminator', $frame_textencoding), $frame_offset );
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_filename = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_filename ) === 0 )
				$frame_filename = '';
		
			$frame_offset = $frame_terminatorpos + strlen( ID3::textEncodingLookup('terminator', $frame_textencoding ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup('terminator', $frame_textencoding), $frame_offset );
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			$frame_offset = $frame_terminatorpos + strlen( ID3::textEncodingLookup('terminator', $frame_textencoding ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['objectdata']  = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid']  = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']    = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['mime']        = $frame_mimetype;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['filename']    = $frame_filename;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description'] = $frame_description;

			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			
				if ( !isset( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] ) || ( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']['compression'] === false ) )
					$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription'] = ID3::roughTranslateUnicodeToASCII( $frame_description, $frame_textencoding );
			
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'PCNT' ) ) || // 4.16  PCNT Play counter
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'CNT'  ) ) )  // 4.17  CNT  Play counter
		{
			// There may only be one 'PCNT' frame in each tag.
			// When the counter reaches all one's, one byte is inserted in
			// front of the counter thus making the counter eight bits bigger
			// <Header for 'Play counter', ID: 'PCNT'>
			// Counter        $xx xx xx xx (xx ...)

			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data']          = ID3::bigEndianToInt( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'POPM' ) ) || // 4.17  POPM Popularimeter
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'POP'  ) ) )  // 4.18  POP  Popularimeter
		{
			// There may be more than one 'POPM' frame in each tag,
			// but only one with the same email address
			// <Header for 'Popularimeter', ID: 'POPM'>
			// Email to user   <text string> $00
			// Rating          $xx
			// Counter         $xx xx xx xx (xx ...)

			$frame_offset = 0;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_emailaddress  = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_emailaddress ) === 0 )
				$frame_emailaddress = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
			$frame_rating = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] = ID3::bigEndianToInt(substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['email']  = $frame_emailaddress;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['rating'] = $frame_rating;
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'RBUF' ) ) || // 4.18  RBUF Recommended buffer size
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'BUF'  ) ) )  // 4.19  BUF  Recommended buffer size
		{
			// There may only be one 'RBUF' frame in each tag
			// <Header for 'Recommended buffer size', ID: 'RBUF'>
			// Buffer size               $xx xx xx
			// Embedded info flag        %0000000x
			// Offset to next tag        $xx xx xx xx

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['buffersize'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 ) );
			$frame_offset += 3;

			$frame_embeddedinfoflags = ID3::bigEndianToBin( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['flags']['embededinfo'] = (bool)substr( $frame_embeddedinfoflags, 7, 1 );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['nexttagoffset'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		}
		// 4.20  Encrypted meta frame (ID3v2.2 only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'CRM' ) ) 
		{
			// There may be more than one 'CRM' frame in a tag,
			// but only one with the same 'owner identifier'
			// <Header for 'Encrypted meta frame', ID: 'CRM'>
			// Owner identifier      <textstring> $00 (00)
			// Content/explanation   <textstring> $00 (00)
			// Encrypted datablock   <binary data>

			$frame_offset = 0;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_ownerid = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_ownerid ) === 0 )
				$frame_ownerid = count( $MP3fileInfo['id3']['id3v2']["$frame_name"] ) - 1;
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_description   = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_description ) === 0 )
				$frame_description = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['ownerid']       = $frame_ownerid;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']          = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description']   = $frame_description;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'AENC' ) ) || // 4.19  AENC Audio encryption
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'CRA'  ) ) )  // 4.21  CRA  Audio encryption
		{
			// There may be more than one 'AENC' frames in a tag,
			// but only one with the same 'Owner identifier'
			// <Header for 'Audio encryption', ID: 'AENC'>
			// Owner identifier   <text string> $00
			// Preview start      $xx xx
			// Preview length     $xx xx
			// Encryption info    <binary data>

			$frame_offset = 0;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_ownerid = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_ownerid ) === 0 )
				$frame_ownerid == '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['ownerid'] = $frame_ownerid;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['previewstart'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['previewlength'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encryptioninfo'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_ownerid"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		} 
		else if ( ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'LINK' ) ) || // 4.20  LINK Linked information
				  ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) && ( $frame_name == 'LNK'  ) ) )  // 4.22  LNK  Linked information 
		{
			// There may be more than one 'LINK' frame in a tag,
			// but only one with the same contents
			// <Header for 'Linked information', ID: 'LINK'>
			// ID3v2.3+ => Frame identifier   $xx xx xx xx
			// ID3v2.2  => Frame identifier   $xx xx xx
			// URL                            <text string> $00
			// ID and additional data         <text string(s)>

			$frame_offset = 0;
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['frameid'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 );
				$frame_offset += 3;
			} 
			else 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['frameid'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 );
				$frame_offset += 4;
			}

			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_url = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_url ) === 0 )
				$frame_url = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['url'] = $frame_url;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['additionaldata'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
		
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
				unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.21  POSS Position synchronisation frame (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'POSS' ) ) 
		{
			// There may only be one 'POSS' frame in each tag
			// <Head for 'Position synchronisation', ID: 'POSS'>
			// Time stamp format         $xx
			// Position                  $xx (xx ...)

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['timestampformat'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['position']        = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong']   = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		}
		// 4.22  USER Terms of use (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'USER' ) ) 
		{
			// There may be more than one 'Terms of use' frame in a tag,
			// but only one with the same 'Language'
			// <Header for 'Terms of use frame', ID: 'USER'>
			// Text encoding        $xx
			// Language             $xx xx xx
			// The actual text      <text string according to encoding>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_language = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 3 );
			$frame_offset += 3;
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['language']     = $frame_language;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['languagename'] = ID3::languageLookup( $frame_language, false  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['encodingid']   = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['encoding']     = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['data']         = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );

			if ( !$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['flags']['compression'] )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['asciidata'] = ID3::roughTranslateUnicodeToASCII( $MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['data'], $frame_textencoding );
		
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['flags']         = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_language"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.23  OWNE Ownership frame (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'OWNE' ) ) 
		{
			// There may only be one 'OWNE' frame in a tag
			// <Header for 'Ownership frame', ID: 'OWNE'>
			// Text encoding     $xx
			// Price paid        <text string> $00
			// Date of purch.    <text string>
			// Seller            <text string according to encoding>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['encodingid'] = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['encoding']   = ID3::textEncodingLookup( 'encoding', $frame_textencoding );

			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_pricepaid = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]['pricepaid']['currencyid'] = substr( $frame_pricepaid, 0, 3 );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['pricepaid']['currency']   = ID3::lookupCurrency( $MP3fileInfo['id3']['id3v2']["$frame_name"]['pricepaid']['currencyid'], 'units' );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['pricepaid']['value']      = substr( $frame_pricepaid, 3 );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]['purchasedate'] = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 8 );
		
			if ( !ID3::isValidDateStampString( $MP3fileInfo['id3']['id3v2']["$frame_name"]['purchasedate'] ) )
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['purchasedateunix'] = mktime( 0, 0, 0, substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['purchasedate'], 4, 2 ), substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['purchasedate'], 6, 2 ), substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['purchasedate'], 0, 4 ) );
		
			$frame_offset += 8;

			$MP3fileInfo['id3']['id3v2']["$frame_name"]['seller']        = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		}
		// 4.24  COMR Commercial frame (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'COMR' ) ) 
		{
			// There may be more than one 'commercial frame' in a tag,
			// but no two may be identical
			// <Header for 'Commercial frame', ID: 'COMR'>
			// Text encoding      $xx
			// Price string       <text string> $00
			// Valid until        <text string>
			// Contact URL        <text string> $00
			// Received as        $xx
			// Name of seller     <text string according to encoding> $00 (00)
			// Description        <text string according to encoding> $00 (00)
			// Picture MIME type  <string> $00
			// Seller logo        <binary data>

			$frame_offset = 0;
			$frame_textencoding = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );

			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_pricestring   = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			$frame_offset        = $frame_terminatorpos + strlen( chr( 0 ) );
			$frame_rawpricearray = explode( '/', $frame_pricestring );
	
			foreach ( $frame_rawpricearray as $key => $val ) 
			{
				$frame_currencyid = substr( $val, 0, 3 );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['price']["$frame_currencyid"]['currency'] = ID3::lookupCurrency( $frame_currencyid, 'units' );
				$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['price']["$frame_currencyid"]['value']    = substr( $val, 3 );
			}

			$frame_datestring = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 8 );
			$frame_offset += 8;

			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_contacturl = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );

			$frame_receivedasid  = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup('terminator', $frame_textencoding), $frame_offset );
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) ), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_sellername = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_sellername ) === 0 )
				$frame_sellername = '';
		
			$frame_offset = $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], ID3::textEncodingLookup( 'terminator', $frame_textencoding), $frame_offset );
		
			if ( ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding )), 1 ) ) === 0 )
				$frame_terminatorpos++; // strpos() fooled because 2nd byte of Unicode chars are often 0x00
		
			$frame_description = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			
			if ( ord( $frame_description) === 0 )
				$frame_description = '';
		
			$frame_offset        = $frame_terminatorpos + strlen( ID3::textEncodingLookup( 'terminator', $frame_textencoding ) );
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_mimetype      = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
			$frame_offset        = $frame_terminatorpos + strlen( chr( 0 ) );

			$frame_sellerlogo = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encodingid']        = $frame_textencoding;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['encoding']          = ID3::textEncodingLookup( 'encoding', $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['pricevaliduntil']   = $frame_datestring;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['contacturl']        = $frame_contacturl;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['receivedasid']      = $frame_receivedasid;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['receivedas']        = ID3::COMRReceivedAsLookup( $frame_receivedasid );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['sellername']        = $frame_sellername;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciisellername']   = ID3::roughTranslateUnicodeToASCII( $frame_sellername, $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['description']       = $frame_description;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['asciidescription']  = ID3::roughTranslateUnicodeToASCII( $frame_description, $frame_textencoding );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['mime']              = $frame_mimetype;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['logo']              = $frame_sellerlogo;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']             = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong']     = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.25  ENCR Encryption method registration (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'ENCR' ) ) 
		{
			// There may be several 'ENCR' frames in a tag,
			// but only one containing the same symbol
			// and only one containing the same owner identifier
			// <Header for 'Encryption method registration', ID: 'ENCR'>
			// Owner identifier    <text string> $00
			// Method symbol       $xx
			// Encryption data     <binary data>

			$frame_offset = 0;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_ownerid = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_ownerid ) === 0 )
				$frame_ownerid = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['ownerid']       = $frame_ownerid;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['methodsymbol']  = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']          = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']         = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.26  GRID Group identification registration (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'GRID' ) ) 
		{
			// There may be several 'GRID' frames in a tag,
			// but only one containing the same symbol
			// and only one containing the same owner identifier
			// <Header for 'Group ID registration', ID: 'GRID'>
			// Owner identifier      <text string> $00
			// Group symbol          $xx
			// Group dependent data  <binary data>

			$frame_offset = 0;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_ownerid = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_ownerid ) === 0 )
				$frame_ownerid = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['ownerid']       = $frame_ownerid;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['groupsymbol']   = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']          = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']         = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.27  PRIV Private frame (ID3v2.3+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'PRIV' ) ) 
		{
			// The tag may contain more than one 'PRIV' frame
			// but only with different contents
			// <Header for 'Private frame', ID: 'PRIV'>
			// Owner identifier      <text string> $00
			// The private data      <binary data>

			$frame_offset = 0;
			$frame_terminatorpos = strpos( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], chr( 0 ), $frame_offset );
			$frame_ownerid = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_terminatorpos - $frame_offset );
		
			if ( ord( $frame_ownerid) === 0 )
				$frame_ownerid = '';
		
			$frame_offset = $frame_terminatorpos + strlen( chr( 0 ) );

			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['ownerid']       = $frame_ownerid;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']          = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']         = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.28  SIGN Signature frame (ID3v2.4+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 4 ) && ( $frame_name == 'SIGN' ) ) 
		{
			// There may be more than one 'signature frame' in a tag,
			// but no two may be identical
			// <Header for 'Signature frame', ID: 'SIGN'>
			// Group symbol      $xx
			// Signature         <binary data>

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['groupsymbol']   = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['data']          = substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset);
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['flags']         = $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'];
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['flags'] );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data']  );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['datalength'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]["$frame_arrayindex"]['dataoffset'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'];
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] );
		}
		// 4.29  SEEK Seek frame (ID3v2.4+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 4 ) && ( $frame_name == 'SEEK' ) ) 
		{
			// There may only be one 'seek frame' in a tag
			// <Header for 'Seek frame', ID: 'SEEK'>
			// Minimum offset to next tag       $xx xx xx xx

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['data']          = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
		}
		// 4.30  ASPI Audio seek point index (ID3v2.4+ only) 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 4 ) && ( $frame_name == 'ASPI' ) ) 
		{
			// There may only be one 'audio seek point index' frame in a tag
			// <Header for 'Seek Point Index', ID: 'ASPI'>
			// Indexed data start (S)         $xx xx xx xx
			// Indexed data length (L)        $xx xx xx xx
			// Number of index points (N)     $xx xx
			// Bits per index point (b)       $xx
			// Then for every index point the following data is included:
			// Fraction at index (Fi)          $xx (xx)

			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['datastart'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
			$frame_offset += 4;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['indexeddatalength'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
			$frame_offset += 4;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['indexpoints'] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsperpoint'] = ord( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset++, 1 ) );
			$frame_bytesperpoint = ceil( $MP3fileInfo['id3']['id3v2']["$frame_name"]['bitsperpoint'] / 8 );
		
			for ( $i = 0; $i < $frame_indexpoints; $i++ ) 
			{
				$MP3fileInfo['id3']['id3v2']["$frame_name"]['indexes']["$i"] = ID3::bigEndianToInt( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, $frame_bytesperpoint ) );
				$frame_offset += $frame_bytesperpoint;
			}
			
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		}
		// Replay Gain Adjustment 
		else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] >= 3 ) && ( $frame_name == 'RGAD' ) ) 
		{
			// http://privatewww.essex.ac.uk/~djmrob/replaygain/file_format_id3v2.html
			// There may only be one 'RGAD' frame in a tag
			// <Header for 'Replay Gain Adjustment', ID: 'RGAD'>
			// Peak Amplitude                      $xx $xx $xx $xx
			// Radio Replay Gain Adjustment        %aaabbbcd %dddddddd
			// Audiophile Replay Gain Adjustment   %aaabbbcd %dddddddd
			//   a - name code
			//   b - originator code
			//   c - sign bit
			//   d - replay gain adjustment
	
			$frame_offset = 0;
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['peakamplitude'] = ID3::bigEndianToFloat( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 4 ) );
			$frame_offset += 4;
			$radioadjustment = ID3::decToBin( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
			$audiophileadjustment = ID3::decToBin( substr( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'], $frame_offset, 2 ) );
			$frame_offset += 2;
	
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['name']            = ID3::binToDec( substr( $radioadjustment,      0, 3 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['originator']      = ID3::binToDec( substr( $radioadjustment,      3, 3 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['signbit']         = ID3::binToDec( substr( $radioadjustment,      6, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['adjustment']      = ID3::binToDec( substr( $radioadjustment,      7, 9 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['name']       = ID3::binToDec( substr( $audiophileadjustment, 0, 3 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['originator'] = ID3::binToDec( substr( $audiophileadjustment, 3, 3 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['signbit']    = ID3::binToDec( substr( $audiophileadjustment, 6, 1 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['adjustment'] = ID3::binToDec( substr( $audiophileadjustment, 7, 9 ) );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['radio']['name']                   = ID3::RGADnameLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['name'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['radio']['originator']             = ID3::RGADoriginatorLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['originator'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['radio']['adjustment'] 			   = ID3::RGADadjustmentLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['adjustment'], $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['radio']['signbit'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['audiophile']['name']              = ID3::RGADnameLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['name'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['audiophile']['originator']        = ID3::RGADoriginatorLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['originator'] );
			$MP3fileInfo['id3']['id3v2']["$frame_name"]['audiophile']['adjustment']        = ID3::RGADadjustmentLookup( $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['adjustment'], $MP3fileInfo['id3']['id3v2']["$frame_name"]['raw']['audiophile']['signbit'] );

			$MP3fileInfo['replay_gain']['radio']['peak']            = $MP3fileInfo['id3']['id3v2']["$frame_name"]['peakamplitude'];
			$MP3fileInfo['replay_gain']['radio']['originator']      = $MP3fileInfo['id3']['id3v2']["$frame_name"]['radio']['originator'];
			$MP3fileInfo['replay_gain']['radio']['adjustment']      = $MP3fileInfo['id3']['id3v2']["$frame_name"]['radio']['adjustment'];
			$MP3fileInfo['replay_gain']['audiophile']['originator'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['audiophile']['originator'];
			$MP3fileInfo['replay_gain']['audiophile']['adjustment'] = $MP3fileInfo['id3']['id3v2']["$frame_name"]['audiophile']['adjustment'];	

			$MP3fileInfo['id3']['id3v2']["$frame_name"]['framenamelong'] = ID3::frameNameLongLookup( $frame_name );
			unset( $MP3fileInfo['id3']['id3v2']["$frame_name"]['data'] );
		}

		return true;
	}

	function lookupCurrency( $currencyid, $item ) 
	{
		static $CurrencyLookup = array();
	
		if ( count( $CurrencyLookup ) < 1 ) 
		{
			$CurrencyLookup['AED']['country'] = 'United Arab Emirates';
			$CurrencyLookup['AFA']['country'] = 'Afghanistan';
			$CurrencyLookup['ALL']['country'] = 'Albania';
			$CurrencyLookup['AMD']['country'] = 'Armenia';
			$CurrencyLookup['ANG']['country'] = 'Netherlands Antilles';
			$CurrencyLookup['AOA']['country'] = 'Angola';
			$CurrencyLookup['ARS']['country'] = 'Argentina';
			$CurrencyLookup['ATS']['country'] = 'Austria';
			$CurrencyLookup['AUD']['country'] = 'Australia';
			$CurrencyLookup['AWG']['country'] = 'Aruba';
			$CurrencyLookup['AZM']['country'] = 'Azerbaijan';
			$CurrencyLookup['BAM']['country'] = 'Bosnia and Herzegovina';
			$CurrencyLookup['BBD']['country'] = 'Barbados';
			$CurrencyLookup['BDT']['country'] = 'Bangladesh';
			$CurrencyLookup['BEF']['country'] = 'Belgium';
			$CurrencyLookup['BGL']['country'] = 'Bulgaria';
			$CurrencyLookup['BHD']['country'] = 'Bahrain';
			$CurrencyLookup['BIF']['country'] = 'Burundi';
			$CurrencyLookup['BMD']['country'] = 'Bermuda';
			$CurrencyLookup['BND']['country'] = 'Brunei Darussalam';
			$CurrencyLookup['BOB']['country'] = 'Bolivia';
			$CurrencyLookup['BRL']['country'] = 'Brazil';
			$CurrencyLookup['BSD']['country'] = 'Bahamas';
			$CurrencyLookup['BTN']['country'] = 'Bhutan';
			$CurrencyLookup['BWP']['country'] = 'Botswana';
			$CurrencyLookup['BYR']['country'] = 'Belarus';
			$CurrencyLookup['BZD']['country'] = 'Belize';
			$CurrencyLookup['CAD']['country'] = 'Canada';
			$CurrencyLookup['CDF']['country'] = 'Congo/Kinshasa';
			$CurrencyLookup['CHF']['country'] = 'Switzerland';
			$CurrencyLookup['CLP']['country'] = 'Chile';
			$CurrencyLookup['CNY']['country'] = 'China';
			$CurrencyLookup['COP']['country'] = 'Colombia';
			$CurrencyLookup['CRC']['country'] = 'Costa Rica';
			$CurrencyLookup['CUP']['country'] = 'Cuba';
			$CurrencyLookup['CVE']['country'] = 'Cape Verde';
			$CurrencyLookup['CYP']['country'] = 'Cyprus';
			$CurrencyLookup['CZK']['country'] = 'Czech Republic';
			$CurrencyLookup['DEM']['country'] = 'Germany';
			$CurrencyLookup['DJF']['country'] = 'Djibouti';
			$CurrencyLookup['DKK']['country'] = 'Denmark';
			$CurrencyLookup['DOP']['country'] = 'Dominican Republic';
			$CurrencyLookup['DZD']['country'] = 'Algeria';
			$CurrencyLookup['EEK']['country'] = 'Estonia';
			$CurrencyLookup['EGP']['country'] = 'Egypt';
			$CurrencyLookup['ERN']['country'] = 'Eritrea';
			$CurrencyLookup['ESP']['country'] = 'Spain';
			$CurrencyLookup['ETB']['country'] = 'Ethiopia';
			$CurrencyLookup['EUR']['country'] = 'Euro Member Countries';
			$CurrencyLookup['FIM']['country'] = 'Finland';
			$CurrencyLookup['FJD']['country'] = 'Fiji';
			$CurrencyLookup['FKP']['country'] = 'Falkland Islands (Malvinas)';
			$CurrencyLookup['FRF']['country'] = 'France';
			$CurrencyLookup['GBP']['country'] = 'United Kingdom';
			$CurrencyLookup['GEL']['country'] = 'Georgia';
			$CurrencyLookup['GGP']['country'] = 'Guernsey';
			$CurrencyLookup['GHC']['country'] = 'Ghana';
			$CurrencyLookup['GIP']['country'] = 'Gibraltar';
			$CurrencyLookup['GMD']['country'] = 'Gambia';
			$CurrencyLookup['GNF']['country'] = 'Guinea';
			$CurrencyLookup['GRD']['country'] = 'Greece';
			$CurrencyLookup['GTQ']['country'] = 'Guatemala';
			$CurrencyLookup['GYD']['country'] = 'Guyana';
			$CurrencyLookup['HKD']['country'] = 'Hong Kong';
			$CurrencyLookup['HNL']['country'] = 'Honduras';
			$CurrencyLookup['HRK']['country'] = 'Croatia';
			$CurrencyLookup['HTG']['country'] = 'Haiti';
			$CurrencyLookup['HUF']['country'] = 'Hungary';
			$CurrencyLookup['IDR']['country'] = 'Indonesia';
			$CurrencyLookup['IEP']['country'] = 'Ireland (Eire)';
			$CurrencyLookup['ILS']['country'] = 'Israel';
			$CurrencyLookup['IMP']['country'] = 'Isle of Man';
			$CurrencyLookup['INR']['country'] = 'India';
			$CurrencyLookup['IQD']['country'] = 'Iraq';
			$CurrencyLookup['IRR']['country'] = 'Iran';
			$CurrencyLookup['ISK']['country'] = 'Iceland';
			$CurrencyLookup['ITL']['country'] = 'Italy';
			$CurrencyLookup['JEP']['country'] = 'Jersey';
			$CurrencyLookup['JMD']['country'] = 'Jamaica';
			$CurrencyLookup['JOD']['country'] = 'Jordan';
			$CurrencyLookup['JPY']['country'] = 'Japan';
			$CurrencyLookup['KES']['country'] = 'Kenya';
			$CurrencyLookup['KGS']['country'] = 'Kyrgyzstan';
			$CurrencyLookup['KHR']['country'] = 'Cambodia';
			$CurrencyLookup['KMF']['country'] = 'Comoros';
			$CurrencyLookup['KPW']['country'] = 'Korea';
			$CurrencyLookup['KWD']['country'] = 'Kuwait';
			$CurrencyLookup['KYD']['country'] = 'Cayman Islands';
			$CurrencyLookup['KZT']['country'] = 'Kazakstan';
			$CurrencyLookup['LAK']['country'] = 'Laos';
			$CurrencyLookup['LBP']['country'] = 'Lebanon';
			$CurrencyLookup['LKR']['country'] = 'Sri Lanka';
			$CurrencyLookup['LRD']['country'] = 'Liberia';
			$CurrencyLookup['LSL']['country'] = 'Lesotho';
			$CurrencyLookup['LTL']['country'] = 'Lithuania';
			$CurrencyLookup['LUF']['country'] = 'Luxembourg';
			$CurrencyLookup['LVL']['country'] = 'Latvia';
			$CurrencyLookup['LYD']['country'] = 'Libya';
			$CurrencyLookup['MAD']['country'] = 'Morocco';
			$CurrencyLookup['MDL']['country'] = 'Moldova';
			$CurrencyLookup['MGF']['country'] = 'Madagascar';
			$CurrencyLookup['MKD']['country'] = 'Macedonia';
			$CurrencyLookup['MMK']['country'] = 'Myanmar (Burma)';
			$CurrencyLookup['MNT']['country'] = 'Mongolia';
			$CurrencyLookup['MOP']['country'] = 'Macau';
			$CurrencyLookup['MRO']['country'] = 'Mauritania';
			$CurrencyLookup['MTL']['country'] = 'Malta';
			$CurrencyLookup['MUR']['country'] = 'Mauritius';
			$CurrencyLookup['MVR']['country'] = 'Maldives (Maldive Islands)';
			$CurrencyLookup['MWK']['country'] = 'Malawi';
			$CurrencyLookup['MXN']['country'] = 'Mexico';
			$CurrencyLookup['MYR']['country'] = 'Malaysia';
			$CurrencyLookup['MZM']['country'] = 'Mozambique';
			$CurrencyLookup['NAD']['country'] = 'Namibia';
			$CurrencyLookup['NGN']['country'] = 'Nigeria';
			$CurrencyLookup['NIO']['country'] = 'Nicaragua';
			$CurrencyLookup['NLG']['country'] = 'Netherlands (Holland)';
			$CurrencyLookup['NOK']['country'] = 'Norway';
			$CurrencyLookup['NPR']['country'] = 'Nepal';
			$CurrencyLookup['NZD']['country'] = 'New Zealand';
			$CurrencyLookup['OMR']['country'] = 'Oman';
			$CurrencyLookup['PAB']['country'] = 'Panama';
			$CurrencyLookup['PEN']['country'] = 'Peru';
			$CurrencyLookup['PGK']['country'] = 'Papua New Guinea';
			$CurrencyLookup['PHP']['country'] = 'Philippines';
			$CurrencyLookup['PKR']['country'] = 'Pakistan';
			$CurrencyLookup['PLN']['country'] = 'Poland';
			$CurrencyLookup['PTE']['country'] = 'Portugal';
			$CurrencyLookup['PYG']['country'] = 'Paraguay';
			$CurrencyLookup['QAR']['country'] = 'Qatar';
			$CurrencyLookup['ROL']['country'] = 'Romania';
			$CurrencyLookup['RUR']['country'] = 'Russia';
			$CurrencyLookup['RWF']['country'] = 'Rwanda';
			$CurrencyLookup['SAR']['country'] = 'Saudi Arabia';
			$CurrencyLookup['SBD']['country'] = 'Solomon Islands';
			$CurrencyLookup['SCR']['country'] = 'Seychelles';
			$CurrencyLookup['SDD']['country'] = 'Sudan';
			$CurrencyLookup['SEK']['country'] = 'Sweden';
			$CurrencyLookup['SGD']['country'] = 'Singapore';
			$CurrencyLookup['SHP']['country'] = 'Saint Helena';
			$CurrencyLookup['SIT']['country'] = 'Slovenia';
			$CurrencyLookup['SKK']['country'] = 'Slovakia';
			$CurrencyLookup['SLL']['country'] = 'Sierra Leone';
			$CurrencyLookup['SOS']['country'] = 'Somalia';
			$CurrencyLookup['SPL']['country'] = 'Seborga';
			$CurrencyLookup['SRG']['country'] = 'Suriname';
			$CurrencyLookup['STD']['country'] = 'São Tome and Principe';
			$CurrencyLookup['SVC']['country'] = 'El Salvador';
			$CurrencyLookup['SYP']['country'] = 'Syria';
			$CurrencyLookup['SZL']['country'] = 'Swaziland';
			$CurrencyLookup['THB']['country'] = 'Thailand';
			$CurrencyLookup['TJR']['country'] = 'Tajikistan';
			$CurrencyLookup['TMM']['country'] = 'Turkmenistan';
			$CurrencyLookup['TND']['country'] = 'Tunisia';
			$CurrencyLookup['TOP']['country'] = 'Tonga';
			$CurrencyLookup['TRL']['country'] = 'Turkey';
			$CurrencyLookup['TTD']['country'] = 'Trinidad and Tobago';
			$CurrencyLookup['TVD']['country'] = 'Tuvalu';
			$CurrencyLookup['TWD']['country'] = 'Taiwan';
			$CurrencyLookup['TZS']['country'] = 'Tanzania';
			$CurrencyLookup['UAH']['country'] = 'Ukraine';
			$CurrencyLookup['UGX']['country'] = 'Uganda';
			$CurrencyLookup['USD']['country'] = 'United States of America';
			$CurrencyLookup['UYU']['country'] = 'Uruguay';
			$CurrencyLookup['UZS']['country'] = 'Uzbekistan';
			$CurrencyLookup['VAL']['country'] = 'Vatican City';
			$CurrencyLookup['VEB']['country'] = 'Venezuela';
			$CurrencyLookup['VND']['country'] = 'Viet Nam';
			$CurrencyLookup['VUV']['country'] = 'Vanuatu';
			$CurrencyLookup['WST']['country'] = 'Samoa';
			$CurrencyLookup['XAF']['country'] = 'Communauté Financière Africaine';
			$CurrencyLookup['XAG']['country'] = 'Silver';
			$CurrencyLookup['XAU']['country'] = 'Gold';
			$CurrencyLookup['XCD']['country'] = 'East Caribbean';
			$CurrencyLookup['XDR']['country'] = 'International Monetary Fund';
			$CurrencyLookup['XPD']['country'] = 'Palladium';
			$CurrencyLookup['XPF']['country'] = 'Comptoirs Français du Pacifique';
			$CurrencyLookup['XPT']['country'] = 'Platinum';
			$CurrencyLookup['YER']['country'] = 'Yemen';
			$CurrencyLookup['YUM']['country'] = 'Yugoslavia';
			$CurrencyLookup['ZAR']['country'] = 'South Africa';
			$CurrencyLookup['ZMK']['country'] = 'Zambia';
			$CurrencyLookup['ZWD']['country'] = 'Zimbabwe';
			$CurrencyLookup['AED']['units']   = 'Dirhams';
			$CurrencyLookup['AFA']['units']   = 'Afghanis';
			$CurrencyLookup['ALL']['units']   = 'Leke';
			$CurrencyLookup['AMD']['units']   = 'Drams';
			$CurrencyLookup['ANG']['units']   = 'Guilders';
			$CurrencyLookup['AOA']['units']   = 'Kwanza';
			$CurrencyLookup['ARS']['units']   = 'Pesos';
			$CurrencyLookup['ATS']['units']   = 'Schillings';
			$CurrencyLookup['AUD']['units']   = 'Dollars';
			$CurrencyLookup['AWG']['units']   = 'Guilders';
			$CurrencyLookup['AZM']['units']   = 'Manats';
			$CurrencyLookup['BAM']['units']   = 'Convertible Marka';
			$CurrencyLookup['BBD']['units']   = 'Dollars';
			$CurrencyLookup['BDT']['units']   = 'Taka';
			$CurrencyLookup['BEF']['units']   = 'Francs';
			$CurrencyLookup['BGL']['units']   = 'Leva';
			$CurrencyLookup['BHD']['units']   = 'Dinars';
			$CurrencyLookup['BIF']['units']   = 'Francs';
			$CurrencyLookup['BMD']['units']   = 'Dollars';
			$CurrencyLookup['BND']['units']   = 'Dollars';
			$CurrencyLookup['BOB']['units']   = 'Bolivianos';
			$CurrencyLookup['BRL']['units']   = 'Brazil Real';
			$CurrencyLookup['BSD']['units']   = 'Dollars';
			$CurrencyLookup['BTN']['units']   = 'Ngultrum';
			$CurrencyLookup['BWP']['units']   = 'Pulas';
			$CurrencyLookup['BYR']['units']   = 'Rubles';
			$CurrencyLookup['BZD']['units']   = 'Dollars';
			$CurrencyLookup['CAD']['units']   = 'Dollars';
			$CurrencyLookup['CDF']['units']   = 'Congolese Francs';
			$CurrencyLookup['CHF']['units']   = 'Francs';
			$CurrencyLookup['CLP']['units']   = 'Pesos';
			$CurrencyLookup['CNY']['units']   = 'Yuan Renminbi';
			$CurrencyLookup['COP']['units']   = 'Pesos';
			$CurrencyLookup['CRC']['units']   = 'Colones';
			$CurrencyLookup['CUP']['units']   = 'Pesos';
			$CurrencyLookup['CVE']['units']   = 'Escudos';
			$CurrencyLookup['CYP']['units']   = 'Pounds';
			$CurrencyLookup['CZK']['units']   = 'Koruny';
			$CurrencyLookup['DEM']['units']   = 'Deutsche Marks';
			$CurrencyLookup['DJF']['units']   = 'Francs';
			$CurrencyLookup['DKK']['units']   = 'Kroner';
			$CurrencyLookup['DOP']['units']   = 'Pesos';
			$CurrencyLookup['DZD']['units']   = 'Algeria Dinars';
			$CurrencyLookup['EEK']['units']   = 'Krooni';
			$CurrencyLookup['EGP']['units']   = 'Pounds';
			$CurrencyLookup['ERN']['units']   = 'Nakfa';
			$CurrencyLookup['ESP']['units']   = 'Pesetas';
			$CurrencyLookup['ETB']['units']   = 'Birr';
			$CurrencyLookup['EUR']['units']   = 'Euro';
			$CurrencyLookup['FIM']['units']   = 'Markkaa';
			$CurrencyLookup['FJD']['units']   = 'Dollars';
			$CurrencyLookup['FKP']['units']   = 'Pounds';
			$CurrencyLookup['FRF']['units']   = 'Francs';
			$CurrencyLookup['GBP']['units']   = 'Pounds';
			$CurrencyLookup['GEL']['units']   = 'Lari';
			$CurrencyLookup['GGP']['units']   = 'Pounds';
			$CurrencyLookup['GHC']['units']   = 'Cedis';
			$CurrencyLookup['GIP']['units']   = 'Pounds';
			$CurrencyLookup['GMD']['units']   = 'Dalasi';
			$CurrencyLookup['GNF']['units']   = 'Francs';
			$CurrencyLookup['GRD']['units']   = 'Drachmae';
			$CurrencyLookup['GTQ']['units']   = 'Quetzales';
			$CurrencyLookup['GYD']['units']   = 'Dollars';
			$CurrencyLookup['HKD']['units']   = 'Dollars';
			$CurrencyLookup['HNL']['units']   = 'Lempiras';
			$CurrencyLookup['HRK']['units']   = 'Kuna';
			$CurrencyLookup['HTG']['units']   = 'Gourdes';
			$CurrencyLookup['HUF']['units']   = 'Forints';
			$CurrencyLookup['IDR']['units']   = 'Rupiahs';
			$CurrencyLookup['IEP']['units']   = 'Pounds';
			$CurrencyLookup['ILS']['units']   = 'New Shekels';
			$CurrencyLookup['IMP']['units']   = 'Pounds';
			$CurrencyLookup['INR']['units']   = 'Rupees';
			$CurrencyLookup['IQD']['units']   = 'Dinars';
			$CurrencyLookup['IRR']['units']   = 'Rials';
			$CurrencyLookup['ISK']['units']   = 'Kronur';
			$CurrencyLookup['ITL']['units']   = 'Lire';
			$CurrencyLookup['JEP']['units']   = 'Pounds';
			$CurrencyLookup['JMD']['units']   = 'Dollars';
			$CurrencyLookup['JOD']['units']   = 'Dinars';
			$CurrencyLookup['JPY']['units']   = 'Yen';
			$CurrencyLookup['KES']['units']   = 'Shillings';
			$CurrencyLookup['KGS']['units']   = 'Soms';
			$CurrencyLookup['KHR']['units']   = 'Riels';
			$CurrencyLookup['KMF']['units']   = 'Francs';
			$CurrencyLookup['KPW']['units']   = 'Won';
			$CurrencyLookup['KWD']['units']   = 'Dinars';
			$CurrencyLookup['KYD']['units']   = 'Dollars';
			$CurrencyLookup['KZT']['units']   = 'Tenge';
			$CurrencyLookup['LAK']['units']   = 'Kips';
			$CurrencyLookup['LBP']['units']   = 'Pounds';
			$CurrencyLookup['LKR']['units']   = 'Rupees';
			$CurrencyLookup['LRD']['units']   = 'Dollars';
			$CurrencyLookup['LSL']['units']   = 'Maloti';
			$CurrencyLookup['LTL']['units']   = 'Litai';
			$CurrencyLookup['LUF']['units']   = 'Francs';
			$CurrencyLookup['LVL']['units']   = 'Lati';
			$CurrencyLookup['LYD']['units']   = 'Dinars';
			$CurrencyLookup['MAD']['units']   = 'Dirhams';
			$CurrencyLookup['MDL']['units']   = 'Lei';
			$CurrencyLookup['MGF']['units']   = 'Malagasy Francs';
			$CurrencyLookup['MKD']['units']   = 'Denars';
			$CurrencyLookup['MMK']['units']   = 'Kyats';
			$CurrencyLookup['MNT']['units']   = 'Tugriks';
			$CurrencyLookup['MOP']['units']   = 'Patacas';
			$CurrencyLookup['MRO']['units']   = 'Ouguiyas';
			$CurrencyLookup['MTL']['units']   = 'Liri';
			$CurrencyLookup['MUR']['units']   = 'Rupees';
			$CurrencyLookup['MVR']['units']   = 'Rufiyaa';
			$CurrencyLookup['MWK']['units']   = 'Kwachas';
			$CurrencyLookup['MXN']['units']   = 'Pesos';
			$CurrencyLookup['MYR']['units']   = 'Ringgits';
			$CurrencyLookup['MZM']['units']   = 'Meticais';
			$CurrencyLookup['NAD']['units']   = 'Dollars';
			$CurrencyLookup['NGN']['units']   = 'Nairas';
			$CurrencyLookup['NIO']['units']   = 'Gold Cordobas';
			$CurrencyLookup['NLG']['units']   = 'Guilders';
			$CurrencyLookup['NOK']['units']   = 'Krone';
			$CurrencyLookup['NPR']['units']   = 'Nepal Rupees';
			$CurrencyLookup['NZD']['units']   = 'Dollars';
			$CurrencyLookup['OMR']['units']   = 'Rials';
			$CurrencyLookup['PAB']['units']   = 'Balboa';
			$CurrencyLookup['PEN']['units']   = 'Nuevos Soles';
			$CurrencyLookup['PGK']['units']   = 'Kina';
			$CurrencyLookup['PHP']['units']   = 'Pesos';
			$CurrencyLookup['PKR']['units']   = 'Rupees';
			$CurrencyLookup['PLN']['units']   = 'Zlotych';
			$CurrencyLookup['PTE']['units']   = 'Escudos';
			$CurrencyLookup['PYG']['units']   = 'Guarani';
			$CurrencyLookup['QAR']['units']   = 'Rials';
			$CurrencyLookup['ROL']['units']   = 'Lei';
			$CurrencyLookup['RUR']['units']   = 'Rubles';
			$CurrencyLookup['RWF']['units']   = 'Rwanda Francs';
			$CurrencyLookup['SAR']['units']   = 'Riyals';
			$CurrencyLookup['SBD']['units']   = 'Dollars';
			$CurrencyLookup['SCR']['units']   = 'Rupees';
			$CurrencyLookup['SDD']['units']   = 'Dinars';
			$CurrencyLookup['SEK']['units']   = 'Kronor';
			$CurrencyLookup['SGD']['units']   = 'Dollars';
			$CurrencyLookup['SHP']['units']   = 'Pounds';
			$CurrencyLookup['SIT']['units']   = 'Tolars';
			$CurrencyLookup['SKK']['units']   = 'Koruny';
			$CurrencyLookup['SLL']['units']   = 'Leones';
			$CurrencyLookup['SOS']['units']   = 'Shillings';
			$CurrencyLookup['SPL']['units']   = 'Luigini';
			$CurrencyLookup['SRG']['units']   = 'Guilders';
			$CurrencyLookup['STD']['units']   = 'Dobras';
			$CurrencyLookup['SVC']['units']   = 'Colones';
			$CurrencyLookup['SYP']['units']   = 'Pounds';
			$CurrencyLookup['SZL']['units']   = 'Emalangeni';
			$CurrencyLookup['THB']['units']   = 'Baht';
			$CurrencyLookup['TJR']['units']   = 'Rubles';
			$CurrencyLookup['TMM']['units']   = 'Manats';
			$CurrencyLookup['TND']['units']   = 'Dinars';
			$CurrencyLookup['TOP']['units']   = 'Pa\'anga';
			$CurrencyLookup['TRL']['units']   = 'Liras';
			$CurrencyLookup['TTD']['units']   = 'Dollars';
			$CurrencyLookup['TVD']['units']   = 'Tuvalu Dollars';
			$CurrencyLookup['TWD']['units']   = 'New Dollars';
			$CurrencyLookup['TZS']['units']   = 'Shillings';
			$CurrencyLookup['UAH']['units']   = 'Hryvnia';
			$CurrencyLookup['UGX']['units']   = 'Shillings';
			$CurrencyLookup['USD']['units']   = 'Dollars';
			$CurrencyLookup['UYU']['units']   = 'Pesos';
			$CurrencyLookup['UZS']['units']   = 'Sums';
			$CurrencyLookup['VAL']['units']   = 'Lire';
			$CurrencyLookup['VEB']['units']   = 'Bolivares';
			$CurrencyLookup['VND']['units']   = 'Dong';
			$CurrencyLookup['VUV']['units']   = 'Vatu';
			$CurrencyLookup['WST']['units']   = 'Tala';
			$CurrencyLookup['XAF']['units']   = 'Francs';
			$CurrencyLookup['XAG']['units']   = 'Ounces';
			$CurrencyLookup['XAU']['units']   = 'Ounces';
			$CurrencyLookup['XCD']['units']   = 'Dollars';
			$CurrencyLookup['XDR']['units']   = 'Special Drawing Rights';
			$CurrencyLookup['XPD']['units']   = 'Ounces';
			$CurrencyLookup['XPF']['units']   = 'Francs';
			$CurrencyLookup['XPT']['units']   = 'Ounces';
			$CurrencyLookup['YER']['units']   = 'Rials';
			$CurrencyLookup['YUM']['units']   = 'New Dinars';
			$CurrencyLookup['ZAR']['units']   = 'Rand';
			$CurrencyLookup['ZMK']['units']   = 'Kwacha';
			$CurrencyLookup['ZWD']['units']   = 'Zimbabwe Dollars';
		}

		return ( isset( $CurrencyLookup["$currencyid"]["$item"] )? $CurrencyLookup["$currencyid"]["$item"] : '' );
	}

	function languageLookup( $languagecode, $casesensitive = false ) 
	{
		// ISO 639-2 - http://www.id3.org/iso639-2.html
		if ( $languagecode == 'XXX' )
			return 'unknown';
	
		if ( !$casesensitive )
			$languagecode = strtolower( $languagecode );
	
		static $LanguageLookup = array();
	
		if ( count( $LanguageLookup ) < 1 ) 
		{
			$LanguageLookup['aar'] = 'Afar';
			$LanguageLookup['abk'] = 'Abkhazian';
			$LanguageLookup['ace'] = 'Achinese';
			$LanguageLookup['ach'] = 'Acoli';
			$LanguageLookup['ada'] = 'Adangme';
			$LanguageLookup['afa'] = 'Afro-Asiatic (Other)';
			$LanguageLookup['afh'] = 'Afrihili';
			$LanguageLookup['afr'] = 'Afrikaans';
			$LanguageLookup['aka'] = 'Akan';
			$LanguageLookup['akk'] = 'Akkadian';
			$LanguageLookup['alb'] = 'Albanian';
			$LanguageLookup['ale'] = 'Aleut';
			$LanguageLookup['alg'] = 'Algonquian Languages';
			$LanguageLookup['amh'] = 'Amharic';
			$LanguageLookup['ang'] = 'English, Old (ca. 450-1100)';
			$LanguageLookup['apa'] = 'Apache Languages';
			$LanguageLookup['ara'] = 'Arabic';
			$LanguageLookup['arc'] = 'Aramaic';
			$LanguageLookup['arm'] = 'Armenian';
			$LanguageLookup['arn'] = 'Araucanian';
			$LanguageLookup['arp'] = 'Arapaho';
			$LanguageLookup['art'] = 'Artificial (Other)';
			$LanguageLookup['arw'] = 'Arawak';
			$LanguageLookup['asm'] = 'Assamese';
			$LanguageLookup['ath'] = 'Athapascan Languages';
			$LanguageLookup['ava'] = 'Avaric';
			$LanguageLookup['ave'] = 'Avestan';
			$LanguageLookup['awa'] = 'Awadhi';
			$LanguageLookup['aym'] = 'Aymara';
			$LanguageLookup['aze'] = 'Azerbaijani';
			$LanguageLookup['bad'] = 'Banda';
			$LanguageLookup['bai'] = 'Bamileke Languages';
			$LanguageLookup['bak'] = 'Bashkir';
			$LanguageLookup['bal'] = 'Baluchi';
			$LanguageLookup['bam'] = 'Bambara';
			$LanguageLookup['ban'] = 'Balinese';
			$LanguageLookup['baq'] = 'Basque';
			$LanguageLookup['bas'] = 'Basa';
			$LanguageLookup['bat'] = 'Baltic (Other)';
			$LanguageLookup['bej'] = 'Beja';
			$LanguageLookup['bel'] = 'Byelorussian';
			$LanguageLookup['bem'] = 'Bemba';
			$LanguageLookup['ben'] = 'Bengali';
			$LanguageLookup['ber'] = 'Berber (Other)';
			$LanguageLookup['bho'] = 'Bhojpuri';
			$LanguageLookup['bih'] = 'Bihari';
			$LanguageLookup['bik'] = 'Bikol';
			$LanguageLookup['bin'] = 'Bini';
			$LanguageLookup['bis'] = 'Bislama';
			$LanguageLookup['bla'] = 'Siksika';
			$LanguageLookup['bnt'] = 'Bantu (Other)';
			$LanguageLookup['bod'] = 'Tibetan';
			$LanguageLookup['bra'] = 'Braj';
			$LanguageLookup['bre'] = 'Breton';
			$LanguageLookup['bua'] = 'Buriat';
			$LanguageLookup['bug'] = 'Buginese';
			$LanguageLookup['bul'] = 'Bulgarian';
			$LanguageLookup['bur'] = 'Burmese';
			$LanguageLookup['cad'] = 'Caddo';
			$LanguageLookup['cai'] = 'Central American Indian (Other)';
			$LanguageLookup['car'] = 'Carib';
			$LanguageLookup['cat'] = 'Catalan';
			$LanguageLookup['cau'] = 'Caucasian (Other)';
			$LanguageLookup['ceb'] = 'Cebuano';
			$LanguageLookup['cel'] = 'Celtic (Other)';
			$LanguageLookup['ces'] = 'Czech';
			$LanguageLookup['cha'] = 'Chamorro';
			$LanguageLookup['chb'] = 'Chibcha';
			$LanguageLookup['che'] = 'Chechen';
			$LanguageLookup['chg'] = 'Chagatai';
			$LanguageLookup['chi'] = 'Chinese';
			$LanguageLookup['chm'] = 'Mari';
			$LanguageLookup['chn'] = 'Chinook jargon';
			$LanguageLookup['cho'] = 'Choctaw';
			$LanguageLookup['chr'] = 'Cherokee';
			$LanguageLookup['chu'] = 'Church Slavic';
			$LanguageLookup['chv'] = 'Chuvash';
			$LanguageLookup['chy'] = 'Cheyenne';
			$LanguageLookup['cop'] = 'Coptic';
			$LanguageLookup['cor'] = 'Cornish';
			$LanguageLookup['cos'] = 'Corsican';
			$LanguageLookup['cpe'] = 'Creoles and Pidgins, English-based (Other)';
			$LanguageLookup['cpf'] = 'Creoles and Pidgins, French-based (Other)';
			$LanguageLookup['cpp'] = 'Creoles and Pidgins, Portuguese-based (Other)';
			$LanguageLookup['cre'] = 'Cree';
			$LanguageLookup['crp'] = 'Creoles and Pidgins (Other)';
			$LanguageLookup['cus'] = 'Cushitic (Other)';
			$LanguageLookup['cym'] = 'Welsh';
			$LanguageLookup['cze'] = 'Czech';
			$LanguageLookup['dak'] = 'Dakota';
			$LanguageLookup['dan'] = 'Danish';
			$LanguageLookup['del'] = 'Delaware';
			$LanguageLookup['deu'] = 'German';
			$LanguageLookup['din'] = 'Dinka';
			$LanguageLookup['div'] = 'Divehi';
			$LanguageLookup['doi'] = 'Dogri';
			$LanguageLookup['dra'] = 'Dravidian (Other)';
			$LanguageLookup['dua'] = 'Duala';
			$LanguageLookup['dum'] = 'Dutch, Middle (ca. 1050-1350)';
			$LanguageLookup['dut'] = 'Dutch';
			$LanguageLookup['dyu'] = 'Dyula';
			$LanguageLookup['dzo'] = 'Dzongkha';
			$LanguageLookup['efi'] = 'Efik';
			$LanguageLookup['egy'] = 'Egyptian (Ancient)';
			$LanguageLookup['eka'] = 'Ekajuk';
			$LanguageLookup['ell'] = 'Greek, Modern (1453-)';
			$LanguageLookup['elx'] = 'Elamite';
			$LanguageLookup['eng'] = 'English';
			$LanguageLookup['enm'] = 'English, Middle (ca. 1100-1500)';
			$LanguageLookup['epo'] = 'Esperanto';
			$LanguageLookup['esk'] = 'Eskimo (Other)';
			$LanguageLookup['esl'] = 'Spanish';
			$LanguageLookup['est'] = 'Estonian';
			$LanguageLookup['eus'] = 'Basque';
			$LanguageLookup['ewe'] = 'Ewe';
			$LanguageLookup['ewo'] = 'Ewondo';
			$LanguageLookup['fan'] = 'Fang';
			$LanguageLookup['fao'] = 'Faroese';
			$LanguageLookup['fas'] = 'Persian';
			$LanguageLookup['fat'] = 'Fanti';
			$LanguageLookup['fij'] = 'Fijian';
			$LanguageLookup['fin'] = 'Finnish';
			$LanguageLookup['fiu'] = 'Finno-Ugrian (Other)';
			$LanguageLookup['fon'] = 'Fon';
			$LanguageLookup['fra'] = 'French';
			$LanguageLookup['fre'] = 'French';
			$LanguageLookup['frm'] = 'French, Middle (ca. 1400-1600)';
			$LanguageLookup['fro'] = 'French, Old (842- ca. 1400)';
			$LanguageLookup['fry'] = 'Frisian';
			$LanguageLookup['ful'] = 'Fulah';
			$LanguageLookup['gaa'] = 'Ga';
			$LanguageLookup['gae'] = 'Gaelic (Scots)';
			$LanguageLookup['gai'] = 'Irish';
			$LanguageLookup['gay'] = 'Gayo';
			$LanguageLookup['gdh'] = 'Gaelic (Scots)';
			$LanguageLookup['gem'] = 'Germanic (Other)';
			$LanguageLookup['geo'] = 'Georgian';
			$LanguageLookup['ger'] = 'German';
			$LanguageLookup['gez'] = 'Geez';
			$LanguageLookup['gil'] = 'Gilbertese';
			$LanguageLookup['glg'] = 'Gallegan';
			$LanguageLookup['gmh'] = 'German, Middle High (ca. 1050-1500)';
			$LanguageLookup['goh'] = 'German, Old High (ca. 750-1050)';
			$LanguageLookup['gon'] = 'Gondi';
			$LanguageLookup['got'] = 'Gothic';
			$LanguageLookup['grb'] = 'Grebo';
			$LanguageLookup['grc'] = 'Greek, Ancient (to 1453)';
			$LanguageLookup['gre'] = 'Greek, Modern (1453-)';
			$LanguageLookup['grn'] = 'Guarani';
			$LanguageLookup['guj'] = 'Gujarati';
			$LanguageLookup['hai'] = 'Haida';
			$LanguageLookup['hau'] = 'Hausa';
			$LanguageLookup['haw'] = 'Hawaiian';
			$LanguageLookup['heb'] = 'Hebrew';
			$LanguageLookup['her'] = 'Herero';
			$LanguageLookup['hil'] = 'Hiligaynon';
			$LanguageLookup['him'] = 'Himachali';
			$LanguageLookup['hin'] = 'Hindi';
			$LanguageLookup['hmo'] = 'Hiri Motu';
			$LanguageLookup['hun'] = 'Hungarian';
			$LanguageLookup['hup'] = 'Hupa';
			$LanguageLookup['hye'] = 'Armenian';
			$LanguageLookup['iba'] = 'Iban';
			$LanguageLookup['ibo'] = 'Igbo';
			$LanguageLookup['ice'] = 'Icelandic';
			$LanguageLookup['ijo'] = 'Ijo';
			$LanguageLookup['iku'] = 'Inuktitut';
			$LanguageLookup['ilo'] = 'Iloko';
			$LanguageLookup['ina'] = 'Interlingua (International Auxiliary language Association)';
			$LanguageLookup['inc'] = 'Indic (Other)';
			$LanguageLookup['ind'] = 'Indonesian';
			$LanguageLookup['ine'] = 'Indo-European (Other)';
			$LanguageLookup['ine'] = 'Interlingue';
			$LanguageLookup['ipk'] = 'Inupiak';
			$LanguageLookup['ira'] = 'Iranian (Other)';
			$LanguageLookup['iri'] = 'Irish';
			$LanguageLookup['iro'] = 'Iroquoian uages';
			$LanguageLookup['isl'] = 'Icelandic';
			$LanguageLookup['ita'] = 'Italian';
			$LanguageLookup['jav'] = 'Javanese';
			$LanguageLookup['jaw'] = 'Javanese';
			$LanguageLookup['jpn'] = 'Japanese';
			$LanguageLookup['jpr'] = 'Judeo-Persian';
			$LanguageLookup['jrb'] = 'Judeo-Arabic';
			$LanguageLookup['kaa'] = 'Kara-Kalpak';
			$LanguageLookup['kab'] = 'Kabyle';
			$LanguageLookup['kac'] = 'Kachin';
			$LanguageLookup['kal'] = 'Greenlandic';
			$LanguageLookup['kam'] = 'Kamba';
			$LanguageLookup['kan'] = 'Kannada';
			$LanguageLookup['kar'] = 'Karen';
			$LanguageLookup['kas'] = 'Kashmiri';
			$LanguageLookup['kat'] = 'Georgian';
			$LanguageLookup['kau'] = 'Kanuri';
			$LanguageLookup['kaw'] = 'Kawi';
			$LanguageLookup['kaz'] = 'Kazakh';
			$LanguageLookup['kha'] = 'Khasi';
			$LanguageLookup['khi'] = 'Khoisan (Other)';
			$LanguageLookup['khm'] = 'Khmer';
			$LanguageLookup['kho'] = 'Khotanese';
			$LanguageLookup['kik'] = 'Kikuyu';
			$LanguageLookup['kin'] = 'Kinyarwanda';
			$LanguageLookup['kir'] = 'Kirghiz';
			$LanguageLookup['kok'] = 'Konkani';
			$LanguageLookup['kom'] = 'Komi';
			$LanguageLookup['kon'] = 'Kongo';
			$LanguageLookup['kor'] = 'Korean';
			$LanguageLookup['kpe'] = 'Kpelle';
			$LanguageLookup['kro'] = 'Kru';
			$LanguageLookup['kru'] = 'Kurukh';
			$LanguageLookup['kua'] = 'Kuanyama';
			$LanguageLookup['kum'] = 'Kumyk';
			$LanguageLookup['kur'] = 'Kurdish';
			$LanguageLookup['kus'] = 'Kusaie';
			$LanguageLookup['kut'] = 'Kutenai';
			$LanguageLookup['lad'] = 'Ladino';
			$LanguageLookup['lah'] = 'Lahnda';
			$LanguageLookup['lam'] = 'Lamba';
			$LanguageLookup['lao'] = 'Lao';
			$LanguageLookup['lat'] = 'Latin';
			$LanguageLookup['lav'] = 'Latvian';
			$LanguageLookup['lez'] = 'Lezghian';
			$LanguageLookup['lin'] = 'Lingala';
			$LanguageLookup['lit'] = 'Lithuanian';
			$LanguageLookup['lol'] = 'Mongo';
			$LanguageLookup['loz'] = 'Lozi';
			$LanguageLookup['ltz'] = 'Letzeburgesch';
			$LanguageLookup['lub'] = 'Luba-Katanga';
			$LanguageLookup['lug'] = 'Ganda';
			$LanguageLookup['lui'] = 'Luiseno';
			$LanguageLookup['lun'] = 'Lunda';
			$LanguageLookup['luo'] = 'Luo (Kenya and Tanzania)';
			$LanguageLookup['mac'] = 'Macedonian';
			$LanguageLookup['mad'] = 'Madurese';
			$LanguageLookup['mag'] = 'Magahi';
			$LanguageLookup['mah'] = 'Marshall';
			$LanguageLookup['mai'] = 'Maithili';
			$LanguageLookup['mak'] = 'Macedonian';
			$LanguageLookup['mak'] = 'Makasar';
			$LanguageLookup['mal'] = 'Malayalam';
			$LanguageLookup['man'] = 'Mandingo';
			$LanguageLookup['mao'] = 'Maori';
			$LanguageLookup['map'] = 'Austronesian (Other)';
			$LanguageLookup['mar'] = 'Marathi';
			$LanguageLookup['mas'] = 'Masai';
			$LanguageLookup['max'] = 'Manx';
			$LanguageLookup['may'] = 'Malay';
			$LanguageLookup['men'] = 'Mende';
			$LanguageLookup['mga'] = 'Irish, Middle (900 - 1200)';
			$LanguageLookup['mic'] = 'Micmac';
			$LanguageLookup['min'] = 'Minangkabau';
			$LanguageLookup['mis'] = 'Miscellaneous (Other)';
			$LanguageLookup['mkh'] = 'Mon-Kmer (Other)';
			$LanguageLookup['mlg'] = 'Malagasy';
			$LanguageLookup['mlt'] = 'Maltese';
			$LanguageLookup['mni'] = 'Manipuri';
			$LanguageLookup['mno'] = 'Manobo Languages';
			$LanguageLookup['moh'] = 'Mohawk';
			$LanguageLookup['mol'] = 'Moldavian';
			$LanguageLookup['mon'] = 'Mongolian';
			$LanguageLookup['mos'] = 'Mossi';
			$LanguageLookup['mri'] = 'Maori';
			$LanguageLookup['msa'] = 'Malay';
			$LanguageLookup['mul'] = 'Multiple Languages';
			$LanguageLookup['mun'] = 'Munda Languages';
			$LanguageLookup['mus'] = 'Creek';
			$LanguageLookup['mwr'] = 'Marwari';
			$LanguageLookup['mya'] = 'Burmese';
			$LanguageLookup['myn'] = 'Mayan Languages';
			$LanguageLookup['nah'] = 'Aztec';
			$LanguageLookup['nai'] = 'North American Indian (Other)';
			$LanguageLookup['nau'] = 'Nauru';
			$LanguageLookup['nav'] = 'Navajo';
			$LanguageLookup['nbl'] = 'Ndebele, South';
			$LanguageLookup['nde'] = 'Ndebele, North';
			$LanguageLookup['ndo'] = 'Ndongo';
			$LanguageLookup['nep'] = 'Nepali';
			$LanguageLookup['new'] = 'Newari';
			$LanguageLookup['nic'] = 'Niger-Kordofanian (Other)';
			$LanguageLookup['niu'] = 'Niuean';
			$LanguageLookup['nla'] = 'Dutch';
			$LanguageLookup['nno'] = 'Norwegian (Nynorsk)';
			$LanguageLookup['non'] = 'Norse, Old';
			$LanguageLookup['nor'] = 'Norwegian';
			$LanguageLookup['nso'] = 'Sotho, Northern';
			$LanguageLookup['nub'] = 'Nubian Languages';
			$LanguageLookup['nya'] = 'Nyanja';
			$LanguageLookup['nym'] = 'Nyamwezi';
			$LanguageLookup['nyn'] = 'Nyankole';
			$LanguageLookup['nyo'] = 'Nyoro';
			$LanguageLookup['nzi'] = 'Nzima';
			$LanguageLookup['oci'] = 'Langue d\'Oc (post 1500)';
			$LanguageLookup['oji'] = 'Ojibwa';
			$LanguageLookup['ori'] = 'Oriya';
			$LanguageLookup['orm'] = 'Oromo';
			$LanguageLookup['osa'] = 'Osage';
			$LanguageLookup['oss'] = 'Ossetic';
			$LanguageLookup['ota'] = 'Turkish, Ottoman (1500 - 1928)';
			$LanguageLookup['oto'] = 'Otomian Languages';
			$LanguageLookup['paa'] = 'Papuan-Australian (Other)';
			$LanguageLookup['pag'] = 'Pangasinan';
			$LanguageLookup['pal'] = 'Pahlavi';
			$LanguageLookup['pam'] = 'Pampanga';
			$LanguageLookup['pan'] = 'Panjabi';
			$LanguageLookup['pap'] = 'Papiamento';
			$LanguageLookup['pau'] = 'Palauan';
			$LanguageLookup['peo'] = 'Persian, Old (ca 600 - 400 B.C.)';
			$LanguageLookup['per'] = 'Persian';
			$LanguageLookup['phn'] = 'Phoenician';
			$LanguageLookup['pli'] = 'Pali';
			$LanguageLookup['pol'] = 'Polish';
			$LanguageLookup['pon'] = 'Ponape';
			$LanguageLookup['por'] = 'Portuguese';
			$LanguageLookup['pra'] = 'Prakrit uages';
			$LanguageLookup['pro'] = 'Provencal, Old (to 1500)';
			$LanguageLookup['pus'] = 'Pushto';
			$LanguageLookup['que'] = 'Quechua';
			$LanguageLookup['raj'] = 'Rajasthani';
			$LanguageLookup['rar'] = 'Rarotongan';
			$LanguageLookup['roa'] = 'Romance (Other)';
			$LanguageLookup['roh'] = 'Rhaeto-Romance';
			$LanguageLookup['rom'] = 'Romany';
			$LanguageLookup['ron'] = 'Romanian';
			$LanguageLookup['rum'] = 'Romanian';
			$LanguageLookup['run'] = 'Rundi';
			$LanguageLookup['rus'] = 'Russian';
			$LanguageLookup['sad'] = 'Sandawe';
			$LanguageLookup['sag'] = 'Sango';
			$LanguageLookup['sah'] = 'Yakut';
			$LanguageLookup['sai'] = 'South American Indian (Other)';
			$LanguageLookup['sal'] = 'Salishan Languages';
			$LanguageLookup['sam'] = 'Samaritan Aramaic';
			$LanguageLookup['san'] = 'Sanskrit';
			$LanguageLookup['sco'] = 'Scots';
			$LanguageLookup['scr'] = 'Serbo-Croatian';
			$LanguageLookup['sel'] = 'Selkup';
			$LanguageLookup['sem'] = 'Semitic (Other)';
			$LanguageLookup['sga'] = 'Irish, Old (to 900)';
			$LanguageLookup['shn'] = 'Shan';
			$LanguageLookup['sid'] = 'Sidamo';
			$LanguageLookup['sin'] = 'Singhalese';
			$LanguageLookup['sio'] = 'Siouan Languages';
			$LanguageLookup['sit'] = 'Sino-Tibetan (Other)';
			$LanguageLookup['sla'] = 'Slavic (Other)';
			$LanguageLookup['slk'] = 'Slovak';
			$LanguageLookup['slo'] = 'Slovak';
			$LanguageLookup['slv'] = 'Slovenian';
			$LanguageLookup['smi'] = 'Sami Languages';
			$LanguageLookup['smo'] = 'Samoan';
			$LanguageLookup['sna'] = 'Shona';
			$LanguageLookup['snd'] = 'Sindhi';
			$LanguageLookup['sog'] = 'Sogdian';
			$LanguageLookup['som'] = 'Somali';
			$LanguageLookup['son'] = 'Songhai';
			$LanguageLookup['sot'] = 'Sotho, Southern';
			$LanguageLookup['spa'] = 'Spanish';
			$LanguageLookup['sqi'] = 'Albanian';
			$LanguageLookup['srd'] = 'Sardinian';
			$LanguageLookup['srr'] = 'Serer';
			$LanguageLookup['ssa'] = 'Nilo-Saharan (Other)';
			$LanguageLookup['ssw'] = 'Siswant';
			$LanguageLookup['ssw'] = 'Swazi';
			$LanguageLookup['suk'] = 'Sukuma';
			$LanguageLookup['sun'] = 'Sudanese';
			$LanguageLookup['sus'] = 'Susu';
			$LanguageLookup['sux'] = 'Sumerian';
			$LanguageLookup['sve'] = 'Swedish';
			$LanguageLookup['swa'] = 'Swahili';
			$LanguageLookup['swe'] = 'Swedish';
			$LanguageLookup['syr'] = 'Syriac';
			$LanguageLookup['tah'] = 'Tahitian';
			$LanguageLookup['tam'] = 'Tamil';
			$LanguageLookup['tat'] = 'Tatar';
			$LanguageLookup['tel'] = 'Telugu';
			$LanguageLookup['tem'] = 'Timne';
			$LanguageLookup['ter'] = 'Tereno';
			$LanguageLookup['tgk'] = 'Tajik';
			$LanguageLookup['tgl'] = 'Tagalog';
			$LanguageLookup['tha'] = 'Thai';
			$LanguageLookup['tib'] = 'Tibetan';
			$LanguageLookup['tig'] = 'Tigre';
			$LanguageLookup['tir'] = 'Tigrinya';
			$LanguageLookup['tiv'] = 'Tivi';
			$LanguageLookup['tli'] = 'Tlingit';
			$LanguageLookup['tmh'] = 'Tamashek';
			$LanguageLookup['tog'] = 'Tonga (Nyasa)';
			$LanguageLookup['ton'] = 'Tonga (Tonga Islands)';
			$LanguageLookup['tru'] = 'Truk';
			$LanguageLookup['tsi'] = 'Tsimshian';
			$LanguageLookup['tsn'] = 'Tswana';
			$LanguageLookup['tso'] = 'Tsonga';
			$LanguageLookup['tuk'] = 'Turkmen';
			$LanguageLookup['tum'] = 'Tumbuka';
			$LanguageLookup['tur'] = 'Turkish';
			$LanguageLookup['tut'] = 'Altaic (Other)';
			$LanguageLookup['twi'] = 'Twi';
			$LanguageLookup['tyv'] = 'Tuvinian';
			$LanguageLookup['uga'] = 'Ugaritic';
			$LanguageLookup['uig'] = 'Uighur';
			$LanguageLookup['ukr'] = 'Ukrainian';
			$LanguageLookup['umb'] = 'Umbundu';
			$LanguageLookup['und'] = 'Undetermined';
			$LanguageLookup['urd'] = 'Urdu';
			$LanguageLookup['uzb'] = 'Uzbek';
			$LanguageLookup['vai'] = 'Vai';
			$LanguageLookup['ven'] = 'Venda';
			$LanguageLookup['vie'] = 'Vietnamese';
			$LanguageLookup['vol'] = 'Volapük';
			$LanguageLookup['vot'] = 'Votic';
			$LanguageLookup['wak'] = 'Wakashan Languages';
			$LanguageLookup['wal'] = 'Walamo';
			$LanguageLookup['war'] = 'Waray';
			$LanguageLookup['was'] = 'Washo';
			$LanguageLookup['wel'] = 'Welsh';
			$LanguageLookup['wen'] = 'Sorbian Languages';
			$LanguageLookup['wol'] = 'Wolof';
			$LanguageLookup['xho'] = 'Xhosa';
			$LanguageLookup['yao'] = 'Yao';
			$LanguageLookup['yap'] = 'Yap';
			$LanguageLookup['yid'] = 'Yiddish';
			$LanguageLookup['yor'] = 'Yoruba';
			$LanguageLookup['zap'] = 'Zapotec';
			$LanguageLookup['zen'] = 'Zenaga';
			$LanguageLookup['zha'] = 'Zhuang';
			$LanguageLookup['zho'] = 'Chinese';
			$LanguageLookup['zul'] = 'Zulu';
			$LanguageLookup['zun'] = 'Zuni';
		}

		return ( isset( $LanguageLookup["$languagecode"] )? $LanguageLookup["$languagecode"] : '' );
	}

	function ETCOEventLookup( $index ) 
	{
		static $EventLookup = array();
	
		if ( count( $EventLookup ) < 1 ) 
		{
			$EventLookup[0x00] = 'padding (has no meaning)';
			$EventLookup[0x01] = 'end of initial silence';
			$EventLookup[0x02] = 'intro start';
			$EventLookup[0x03] = 'main part start';
			$EventLookup[0x04] = 'outro start';
			$EventLookup[0x05] = 'outro end';
			$EventLookup[0x06] = 'verse start';
			$EventLookup[0x07] = 'refrain start';
			$EventLookup[0x08] = 'interlude start';
			$EventLookup[0x09] = 'theme start';
			$EventLookup[0x0A] = 'variation start';
			$EventLookup[0x0B] = 'key change';
			$EventLookup[0x0C] = 'time change';
			$EventLookup[0x0D] = 'momentary unwanted noise (Snap, Crackle & Pop)';
			$EventLookup[0x0E] = 'sustained noise';
			$EventLookup[0x0F] = 'sustained noise end';
			$EventLookup[0x10] = 'intro end';
			$EventLookup[0x11] = 'main part end';
			$EventLookup[0x12] = 'verse end';
			$EventLookup[0x13] = 'refrain end';
			$EventLookup[0x14] = 'theme end';
			$EventLookup[0x15] = 'profanity';
			$EventLookup[0x16] = 'profanity end';
		
			for ( $i = 0x17; $i <= 0xDF; $i++ )
				$EventLookup["$i"] = 'reserved for future use';
		
			for ( $i = 0xE0; $i <= 0xEF; $i++ )
				$EventLookup["$i"] = 'not predefined synch 0-F';
		
			for ( $i = 0xF0; $i <= 0xFC; $i++ )
				$EventLookup["$i"] = 'reserved for future use';
		
			$EventLookup[0xFD] = 'audio end (start of silence)';
			$EventLookup[0xFE] = 'audio file ends';
			$EventLookup[0xFF] = 'one more byte of events follows';
		}

		return ( isset( $EventLookup["$index"] )? $EventLookup["$index"] : '' );
	}

	function SYTLContentTypeLookup( $index ) 
	{
		static $SYTLContentTypeLookup = array();
	
		if ( count( $SYTLContentTypeLookup ) < 1 ) 
		{
			$SYTLContentTypeLookup[0x00] = 'other';
			$SYTLContentTypeLookup[0x01] = 'lyrics';
			$SYTLContentTypeLookup[0x02] = 'text transcription';
			$SYTLContentTypeLookup[0x03] = 'movement/part name';          // (e.g. 'Adagio')
			$SYTLContentTypeLookup[0x04] = 'events';                      // (e.g. 'Don Quijote enters the stage')
			$SYTLContentTypeLookup[0x05] = 'chord';                       // (e.g. 'Bb F Fsus')
			$SYTLContentTypeLookup[0x06] = 'trivia/\'pop up\' information';
			$SYTLContentTypeLookup[0x07] = 'URLs to webpages';
			$SYTLContentTypeLookup[0x08] = 'URLs to images';
		}

		return ( isset( $SYTLContentTypeLookup["$index"] )? $SYTLContentTypeLookup["$index"] : '' );
	}

	function APICPictureTypeLookup( $index ) 
	{
		static $APICPictureTypeLookup = array();
	
		if ( count( $APICPictureTypeLookup ) < 1 ) 
		{
			$APICPictureTypeLookup[0x00] = 'Other';
			$APICPictureTypeLookup[0x01] = '32x32 pixels \'file icon\' (PNG only)';
			$APICPictureTypeLookup[0x02] = 'Other file icon';
			$APICPictureTypeLookup[0x03] = 'Cover (front)';
			$APICPictureTypeLookup[0x04] = 'Cover (back)';
			$APICPictureTypeLookup[0x05] = 'Leaflet page';
			$APICPictureTypeLookup[0x06] = 'Media (e.g. label side of CD)';
			$APICPictureTypeLookup[0x07] = 'Lead artist/lead performer/soloist';
			$APICPictureTypeLookup[0x08] = 'Artist/performer';
			$APICPictureTypeLookup[0x09] = 'Conductor';
			$APICPictureTypeLookup[0x0A] = 'Band/Orchestra';
			$APICPictureTypeLookup[0x0B] = 'Composer';
			$APICPictureTypeLookup[0x0C] = 'Lyricist/text writer';
			$APICPictureTypeLookup[0x0D] = 'Recording Location';
			$APICPictureTypeLookup[0x0E] = 'During recording';
			$APICPictureTypeLookup[0x0F] = 'During performance';
			$APICPictureTypeLookup[0x10] = 'Movie/video screen capture';
			$APICPictureTypeLookup[0x11] = 'A bright coloured fish';
			$APICPictureTypeLookup[0x12] = 'Illustration';
			$APICPictureTypeLookup[0x13] = 'Band/artist logotype';
			$APICPictureTypeLookup[0x14] = 'Publisher/Studio logotype';
		}

		return ( isset( $APICPictureTypeLookup["$index"] )? $APICPictureTypeLookup["$index"] : '' );
	}

	function COMRReceivedAsLookup( $index ) 
	{
		static $COMRReceivedAsLookup = array();
	
		if ( count( $COMRReceivedAsLookup ) < 1 ) 
		{
			$COMRReceivedAsLookup[0x00] = 'Other';
			$COMRReceivedAsLookup[0x01] = 'Standard CD album with other songs';
			$COMRReceivedAsLookup[0x02] = 'Compressed audio on CD';
			$COMRReceivedAsLookup[0x03] = 'File over the Internet';
			$COMRReceivedAsLookup[0x04] = 'Stream over the Internet';
			$COMRReceivedAsLookup[0x05] = 'As note sheets';
			$COMRReceivedAsLookup[0x06] = 'As note sheets in a book with other sheets';
			$COMRReceivedAsLookup[0x07] = 'Music on other media';
			$COMRReceivedAsLookup[0x08] = 'Non-musical merchandise';
		}

		return ( isset( $COMRReceivedAsLookup["$index"] )? $COMRReceivedAsLookup["$index"] : '' );
	}

	function RVA2ChannelTypeLookup( $index ) 
	{
		static $RVA2ChannelTypeLookup = array();
	
		if ( count( $RVA2ChannelTypeLookup ) < 1 ) 
		{
			$RVA2ChannelTypeLookup[0x00] = 'Other';
			$RVA2ChannelTypeLookup[0x01] = 'Master volume';
			$RVA2ChannelTypeLookup[0x02] = 'Front right';
			$RVA2ChannelTypeLookup[0x03] = 'Front left';
			$RVA2ChannelTypeLookup[0x04] = 'Back right';
			$RVA2ChannelTypeLookup[0x05] = 'Back left';
			$RVA2ChannelTypeLookup[0x06] = 'Front centre';
			$RVA2ChannelTypeLookup[0x07] = 'Back centre';
			$RVA2ChannelTypeLookup[0x08] = 'Subwoofer';
		}

		return ( isset( $RVA2ChannelTypeLookup["$index"] )? $RVA2ChannelTypeLookup["$index"] : '' );
	}

	function frameNameLongLookup( $framename ) 
	{
		static $FrameNameLongLookup = array();
	
		if ( count( $FrameNameLongLookup ) < 1 ) 
		{
			$FrameNameLongLookup['AENC'] = 'Audio encryption';
			$FrameNameLongLookup['APIC'] = 'Attached picture';
			$FrameNameLongLookup['ASPI'] = 'Audio seek point index';
			$FrameNameLongLookup['BUF']  = 'Recommended buffer size';
			$FrameNameLongLookup['CNT']  = 'Play counter';
			$FrameNameLongLookup['COM']  = 'Comments';
			$FrameNameLongLookup['COMM'] = 'Comments';
			$FrameNameLongLookup['COMR'] = 'Commercial frame';
			$FrameNameLongLookup['CRA']  = 'Audio encryption';
			$FrameNameLongLookup['CRM']  = 'Encrypted meta frame';
			$FrameNameLongLookup['ENCR'] = 'Encryption method registration';
			$FrameNameLongLookup['EQU']  = 'Equalization';
			$FrameNameLongLookup['EQU2'] = 'Equalisation (2)';
			$FrameNameLongLookup['EQUA'] = 'Equalization';
			$FrameNameLongLookup['ETC']  = 'Event timing codes';
			$FrameNameLongLookup['ETCO'] = 'Event timing codes';
			$FrameNameLongLookup['GEO']  = 'General encapsulated object';
			$FrameNameLongLookup['GEOB'] = 'General encapsulated object';
			$FrameNameLongLookup['GRID'] = 'Group identification registration';
			$FrameNameLongLookup['IPL']  = 'Involved people list';
			$FrameNameLongLookup['IPLS'] = 'Involved people list';
			$FrameNameLongLookup['LINK'] = 'Linked information';
			$FrameNameLongLookup['LNK']  = 'Linked information';
			$FrameNameLongLookup['MCDI'] = 'Music CD identifier';
			$FrameNameLongLookup['MCI']  = 'Music CD Identifier';
			$FrameNameLongLookup['MLL']  = 'MPEG location lookup table';
			$FrameNameLongLookup['MLLT'] = 'MPEG location lookup table';
			$FrameNameLongLookup['OWNE'] = 'Ownership frame';
			$FrameNameLongLookup['PCNT'] = 'Play counter';
			$FrameNameLongLookup['PIC']  = 'Attached picture';
			$FrameNameLongLookup['POP']  = 'Popularimeter';
			$FrameNameLongLookup['POPM'] = 'Popularimeter';
			$FrameNameLongLookup['POSS'] = 'Position synchronisation frame';
			$FrameNameLongLookup['PRIV'] = 'Private frame';
			$FrameNameLongLookup['RBUF'] = 'Recommended buffer size';
			$FrameNameLongLookup['REV']  = 'Reverb';
			$FrameNameLongLookup['RVA']  = 'Relative volume adjustment';
			$FrameNameLongLookup['RVA2'] = 'Relative volume adjustment (2)';
			$FrameNameLongLookup['RVAD'] = 'Relative volume adjustment';
			$FrameNameLongLookup['RVRB'] = 'Reverb';
			$FrameNameLongLookup['SEEK'] = 'Seek frame';
			$FrameNameLongLookup['SIGN'] = 'Signature frame';
			$FrameNameLongLookup['SLT']  = 'Synchronized lyric/text';
			$FrameNameLongLookup['STC']  = 'Synced tempo codes';
			$FrameNameLongLookup['SYLT'] = 'Synchronised lyric/text';
			$FrameNameLongLookup['SYTC'] = 'Synchronised tempo codes';
			$FrameNameLongLookup['TAL']  = 'Album/Movie/Show title';
			$FrameNameLongLookup['TALB'] = 'Album/Movie/Show title';
			$FrameNameLongLookup['TBP']  = 'BPM (Beats Per Minute)';
			$FrameNameLongLookup['TBPM'] = 'BPM (beats per minute)';
			$FrameNameLongLookup['TCM']  = 'Composer';
			$FrameNameLongLookup['TCO']  = 'Content type';
			$FrameNameLongLookup['TCOM'] = 'Composer';
			$FrameNameLongLookup['TCON'] = 'Content type';
			$FrameNameLongLookup['TCOP'] = 'Copyright message';
			$FrameNameLongLookup['TCR']  = 'Copyright message';
			$FrameNameLongLookup['TDA']  = 'Date';
			$FrameNameLongLookup['TDAT'] = 'Date';
			$FrameNameLongLookup['TDEN'] = 'Encoding time';
			$FrameNameLongLookup['TDLY'] = 'Playlist delay';
			$FrameNameLongLookup['TDOR'] = 'Original release time';
			$FrameNameLongLookup['TDRC'] = 'Recording time';
			$FrameNameLongLookup['TDRL'] = 'Release time';
			$FrameNameLongLookup['TDTG'] = 'Tagging time';
			$FrameNameLongLookup['TDY']  = 'Playlist delay';
			$FrameNameLongLookup['TEN']  = 'Encoded by';
			$FrameNameLongLookup['TENC'] = 'Encoded by';
			$FrameNameLongLookup['TEXT'] = 'Lyricist/Text writer';
			$FrameNameLongLookup['TFLT'] = 'File type';
			$FrameNameLongLookup['TFT']  = 'File type';
			$FrameNameLongLookup['TIM']  = 'Time';
			$FrameNameLongLookup['TIME'] = 'Time';
			$FrameNameLongLookup['TIPL'] = 'Involved people list';
			$FrameNameLongLookup['TIT1'] = 'Content group description';
			$FrameNameLongLookup['TIT2'] = 'Title/songname/content description';
			$FrameNameLongLookup['TIT3'] = 'Subtitle/Description refinement';
			$FrameNameLongLookup['TKE']  = 'Initial key';
			$FrameNameLongLookup['TKEY'] = 'Initial key';
			$FrameNameLongLookup['TLA']  = 'Language(s)';
			$FrameNameLongLookup['TLAN'] = 'Language(s)';
			$FrameNameLongLookup['TLE']  = 'Length';
			$FrameNameLongLookup['TLEN'] = 'Length';
			$FrameNameLongLookup['TMCL'] = 'Musician credits list';
			$FrameNameLongLookup['TMED'] = 'Media type';
			$FrameNameLongLookup['TMOO'] = 'Mood';
			$FrameNameLongLookup['TMT']  = 'Media type';
			$FrameNameLongLookup['TOA']  = 'Original artist(s)/performer(s)';
			$FrameNameLongLookup['TOAL'] = 'Original album/movie/show title';
			$FrameNameLongLookup['TOF']  = 'Original filename';
			$FrameNameLongLookup['TOFN'] = 'Original filename';
			$FrameNameLongLookup['TOL']  = 'Original Lyricist(s)/text writer(s)';
			$FrameNameLongLookup['TOLY'] = 'Original lyricist(s)/text writer(s)';
			$FrameNameLongLookup['TOPE'] = 'Original artist(s)/performer(s)';
			$FrameNameLongLookup['TOR']  = 'Original release year';
			$FrameNameLongLookup['TORY'] = 'Original release year';
			$FrameNameLongLookup['TOT']  = 'Original album/Movie/Show title';
			$FrameNameLongLookup['TOWN'] = 'File owner/licensee';
			$FrameNameLongLookup['TP1']  = 'Lead artist(s)/Lead performer(s)/Soloist(s)/Performing group';
			$FrameNameLongLookup['TP2']  = 'Band/Orchestra/Accompaniment';
			$FrameNameLongLookup['TP3']  = 'Conductor/Performer refinement';
			$FrameNameLongLookup['TP4']  = 'Interpreted, remixed, or otherwise modified by';
			$FrameNameLongLookup['TPA']  = 'Part of a set';
			$FrameNameLongLookup['TPB']  = 'Publisher';
			$FrameNameLongLookup['TPE1'] = 'Lead performer(s)/Soloist(s)';
			$FrameNameLongLookup['TPE2'] = 'Band/orchestra/accompaniment';
			$FrameNameLongLookup['TPE3'] = 'Conductor/performer refinement';
			$FrameNameLongLookup['TPE4'] = 'Interpreted, remixed, or otherwise modified by';
			$FrameNameLongLookup['TPOS'] = 'Part of a set';
			$FrameNameLongLookup['TPRO'] = 'Produced notice';
			$FrameNameLongLookup['TPUB'] = 'Publisher';
			$FrameNameLongLookup['TRC']  = 'ISRC (International Standard Recording Code)';
			$FrameNameLongLookup['TRCK'] = 'Track number/Position in set';
			$FrameNameLongLookup['TRD']  = 'Recording dates';
			$FrameNameLongLookup['TRDA'] = 'Recording dates';
			$FrameNameLongLookup['TRK']  = 'Track number/Position in set';
			$FrameNameLongLookup['TRSN'] = 'Internet radio station name';
			$FrameNameLongLookup['TRSO'] = 'Internet radio station owner';
			$FrameNameLongLookup['TSI']  = 'Size';
			$FrameNameLongLookup['TSIZ'] = 'Size';
			$FrameNameLongLookup['TSOA'] = 'Album sort order';
			$FrameNameLongLookup['TSOP'] = 'Performer sort order';
			$FrameNameLongLookup['TSOT'] = 'Title sort order';
			$FrameNameLongLookup['TSRC'] = 'ISRC (international standard recording code)';
			$FrameNameLongLookup['TSS']  = 'Software/hardware and settings used for encoding';
			$FrameNameLongLookup['TSSE'] = 'Software/Hardware and settings used for encoding';
			$FrameNameLongLookup['TSST'] = 'Set subtitle';
			$FrameNameLongLookup['TT1']  = 'Content group description';
			$FrameNameLongLookup['TT2']  = 'Title/Songname/Content description';
			$FrameNameLongLookup['TT3']  = 'Subtitle/Description refinement';
			$FrameNameLongLookup['TXT']  = 'Lyricist/text writer';
			$FrameNameLongLookup['TXX']  = 'User defined text information frame';
			$FrameNameLongLookup['TXXX'] = 'User defined text information frame';
			$FrameNameLongLookup['TYE']  = 'Year';
			$FrameNameLongLookup['TYER'] = 'Year';
			$FrameNameLongLookup['UFI']  = 'Unique file identifier';
			$FrameNameLongLookup['UFID'] = 'Unique file identifier';
			$FrameNameLongLookup['ULT']  = 'Unsychronized lyric/text transcription';
			$FrameNameLongLookup['USER'] = 'Terms of use';
			$FrameNameLongLookup['USLT'] = 'Unsynchronised lyric/text transcription';
			$FrameNameLongLookup['WAF']  = 'Official audio file webpage';
			$FrameNameLongLookup['WAR']  = 'Official artist/performer webpage';
			$FrameNameLongLookup['WAS']  = 'Official audio source webpage';
			$FrameNameLongLookup['WCM']  = 'Commercial information';
			$FrameNameLongLookup['WCOM'] = 'Commercial information';
			$FrameNameLongLookup['WCOP'] = 'Copyright/Legal information';
			$FrameNameLongLookup['WCP']  = 'Copyright/Legal information';
			$FrameNameLongLookup['WOAF'] = 'Official audio file webpage';
			$FrameNameLongLookup['WOAR'] = 'Official artist/performer webpage';
			$FrameNameLongLookup['WOAS'] = 'Official audio source webpage';
			$FrameNameLongLookup['WORS'] = 'Official Internet radio station homepage';
			$FrameNameLongLookup['WPAY'] = 'Payment';
			$FrameNameLongLookup['WPB']  = 'Publishers official webpage';
			$FrameNameLongLookup['WPUB'] = 'Publishers official webpage';
			$FrameNameLongLookup['WXX']  = 'User defined URL link frame';
			$FrameNameLongLookup['WXXX'] = 'User defined URL link frame';

			$FrameNameLongLookup['TFEA'] = 'Featured Artist';        // from Helium2 [www.helium2.com]
			$FrameNameLongLookup['TSTU'] = 'Recording Studio';       // from Helium2 [www.helium2.com]
			$FrameNameLongLookup['rgad'] = 'Replay Gain Adjustment'; // from http://privatewww.essex.ac.uk/~djmrob/replaygain/file_format_id3v2.html
		}

		return ( isset( $FrameNameLongLookup["$framename"] )? $FrameNameLongLookup["$framename"] : '' );
	}

	function textEncodingLookup( $type, $encoding ) 
	{
		// http://www.id3.org/id3v2.4.0-structure.txt
		// Frames that allow different types of text encoding contains a text encoding description byte. Possible encodings:
		// $00  ISO-8859-1. Terminated with $00.
		// $01  UTF-16 encoded Unicode with BOM. All strings in the same frame SHALL have the same byteorder. Terminated with $00 00.
		// $02  UTF-16BE encoded Unicode without BOM. Terminated with $00 00.
		// $03  UTF-8 encoded Unicode. Terminated with $00.

		$TextEncodingLookup['encoding']   = array( 'ISO-8859-1', 'UTF-16', 'UTF-16BE', 'UTF-8' );
		$TextEncodingLookup['terminator'] = array( chr( 0 ), chr( 0 ) . chr( 0 ), chr( 0 ) . chr( 0 ), chr( 0 ) );

		return ( isset( $TextEncodingLookup["$type"]["$encoding"] )? $TextEncodingLookup["$type"]["$encoding"] : '' );
	}

	function isValidID3v2FrameName( $framename, $id3v2majorversion ) 
	{
		if ( ( $id3v2majorversion == 2 ) && ( strlen( $framename ) != 3 ) )
			return false;
		else if ( ( $id3v2majorversion >= 3) && ( strlen( $framename ) != 4 ) )
			return false;
	
		return ereg( '[A-Z][A-Z0-9]{3}', $framename );
	}

	function isANumber( $numberstring, $allowdecimal = false, $allownegative = false ) 
	{
		for ( $i = 0; $i < strlen( $numberstring ); $i++ ) 
		{
			if ( ( chr( $numberstring{$i} ) < chr( '0' ) ) || (chr( $numberstring{$i} ) > chr( '9' ) ) ) 
			{
				if ( ( $numberstring{$i} == '.' ) && $allowdecimal ) 
				{
					// allowed
				} 
				else if ( ( $numberstring{$i} == '-' ) && $allownegative && ( $i == 0 ) ) 
				{
					// allowed
				} 
				else 
				{
					return false;
				}
			}
		}
	
		return true;
	}

	function isValidDateStampString( $datestamp ) 
	{
		if ( strlen( $datestamp ) != 8 )
			return false;
	
		if ( !ID3::isANumber( $datestamp, false ) )
		return false;
	
		$year  = substr( $datestamp, 0, 4 );
		$month = substr( $datestamp, 4, 2 );
		$day   = substr( $datestamp, 6, 2 );
	
		if ( ( $year == 0 ) || ( $month == 0 ) || ( $day == 0 ) )
			return false;
	
		if ( $month > 12 )
			return false;
	
		if ( $day > 31 )
			return false;
	
		if ( ( $day > 30 ) && ( ( $month == 4 ) || ( $month == 6 ) || ( $month == 9 ) || ( $month == 11 ) ) )
			return false;
	
		if ( ( $day > 29 ) && ( $month == 2 ) )
			return false;
	
		return true;
	}

	function ID3v2HeaderLength( $majorversion ) 
	{
		if ( $majorversion == 2 )
			return 6;
		else
			return 10;
	}

	function getID3v1Filepointer( $fd ) 
	{
		$offset = 0;
		fseek( $fd, -128, SEEK_END );
		$id3v1tag = fread( $fd, 128 );

		if ( substr( $id3v1tag, 0, 3 ) == 'TAG' ) 
		{
			$id3v1info['title']   = trim( substr( $id3v1tag,  3, 30 ) );
			$id3v1info['artist']  = trim( substr( $id3v1tag, 33, 30 ) );
			$id3v1info['album']   = trim( substr( $id3v1tag, 63, 30 ) );
			$id3v1info['year']    = trim( substr( $id3v1tag, 93,  4 ) );
			$id3v1info['comment'] = substr( $id3v1tag, 97, 30 ); // can't remove NULLs yet, track detection depends on them
			$id3v1info['genreid'] = ord( substr( $id3v1tag, 127, 1 ) );
	
			if ( ( substr( $id3v1info['comment'], 28, 1 ) === chr( 0 ) ) && ( substr( $id3v1info['comment'], 29, 1 ) !== chr( 0 ) ) ) 
			{
				$id3v1info['track']   = ord( substr( $id3v1info['comment'], 29, 1 ) );
				$id3v1info['comment'] = substr( $id3v1info['comment'], 0, 28 );
			}
		
			$id3v1info['comment'] = trim( $id3v1info['comment'] );
			$id3v1info['genre']   = ID3::lookupGenre( $id3v1info['genreid'] );
	
			return $id3v1info;
		} 
		else 
		{
			return false;
		}
	}

	function getID3v2Filepointer( $fd, &$MP3fileInfo ) 
	{
		//	Overall tag structure:
		//		+-----------------------------+
		//		|      Header (10 bytes)      |
		//		+-----------------------------+
		//		|       Extended Header       |
		//		| (variable length, OPTIONAL) |
		//		+-----------------------------+
		//		|   Frames (variable length)  |
		//		+-----------------------------+
		//		|           Padding           |
		//		| (variable length, OPTIONAL) |
		//		+-----------------------------+
		//		| Footer (10 bytes, OPTIONAL) |
		//		+-----------------------------+
		//
		//	Header
		//		ID3v2/file identifier      "ID3"
		//		ID3v2 version              $04 00
		//		ID3v2 flags                (%ab000000 in v2.2, %abc00000 in v2.3, %abcd0000 in v2.4.x)
		//		ID3v2 size             4 * %0xxxxxxx
	
		rewind( $fd );
		$header = fread( $fd, 10 );

		if ( substr( $header, 0, 3 ) == 'ID3' ) 
		{
			$MP3fileInfo['id3']['id3v2']['header'] = true;
			$MP3fileInfo['id3']['id3v2']['majorversion'] = ord( $header{3} );
			$MP3fileInfo['id3']['id3v2']['minorversion'] = ord( $header{4} );
		}

		// this script probably won't correctly parse ID3v2.5.x and above
		if ( isset( $MP3fileInfo['id3']['id3v2']['header'] ) && ( $MP3fileInfo['id3']['id3v2']['majorversion'] <= 4 ) ) 
		{
			$id3_flags = ID3::bigEndianToBin( $header{5} );
			
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) 
			{
				// %ab000000 in v2.2
				$MP3fileInfo['id3']['id3v2']['flags']['unsynch']     = $id3_flags{0}; // a - Unsynchronisation
				$MP3fileInfo['id3']['id3v2']['flags']['compression'] = $id3_flags{1}; // b - Compression
			} 
			else if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 ) 
			{
				// %abc00000 in v2.3
				$MP3fileInfo['id3']['id3v2']['flags']['unsynch']     = $id3_flags{0}; // a - Unsynchronisation
				$MP3fileInfo['id3']['id3v2']['flags']['exthead']     = $id3_flags{1}; // b - Extended header
				$MP3fileInfo['id3']['id3v2']['flags']['experim']     = $id3_flags{2}; // c - Experimental indicator
			} 
			else if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 4 ) 
			{
				// %abcd0000 in v2.4
				$MP3fileInfo['id3']['id3v2']['flags']['unsynch']     = $id3_flags{0}; // a - Unsynchronisation
				$MP3fileInfo['id3']['id3v2']['flags']['exthead']     = $id3_flags{1}; // b - Extended header
				$MP3fileInfo['id3']['id3v2']['flags']['experim']     = $id3_flags{2}; // c - Experimental indicator
				$MP3fileInfo['id3']['id3v2']['flags']['isfooter']    = $id3_flags{3}; // d - Footer present
			}

			$MP3fileInfo['id3']['id3v2']['headerlength'] = ID3::bigEndianToInt( substr( $header, 6, 4 ), 1 ) + ID3::ID3v2HeaderLength( $MP3fileInfo['id3']['id3v2']['majorversion'] );

			// Extended Header
			if ( isset( $MP3fileInfo['id3']['id3v2']['flags']['exthead'] ) && $MP3fileInfo['id3']['id3v2']['flags']['exthead'] ) 
			{
				//			Extended header size   4 * %0xxxxxxx
				//			Number of flag bytes       $01
				//			Extended Flags             $xx
				//			Where the 'Extended header size' is the size of the whole extended header, stored as a 32 bit synchsafe integer.
			
				$extheader = fread( $fd, 4 );
				$MP3fileInfo['id3']['id3v2']['extheaderlength'] = ID3::bigEndianToInt( $extheader, 1 );

				//			The extended flags field, with its size described by 'number of flag  bytes', is defined as:
				//				%0bcd0000
				//			b - Tag is an update
				//				Flag data length       $00
				//			c - CRC data present
				//				Flag data length       $05
				//				Total frame CRC    5 * %0xxxxxxx
				//			d - Tag restrictions
				//				Flag data length       $01

				$extheaderflagbytes = fread( $fd, 1 );
				$extheaderflags     = fread( $fd, $extheaderflagbytes );
				$id3_exthead_flags  = ID3::bigEndianToBin( substr( $header, 5, 1 ) );
				
				$MP3fileInfo['id3']['id3v2']['exthead_flags']['update'] = substr( $id3_exthead_flags, 1, 1 );
				$MP3fileInfo['id3']['id3v2']['exthead_flags']['CRC']    = substr( $id3_exthead_flags, 2, 1 );

				if ( $MP3fileInfo['id3']['id3v2']['exthead_flags']['CRC'] ) 
				{
					$extheaderrawCRC = fread( $fd, 5 );
					$MP3fileInfo['id3']['id3v2']['exthead_flags']['CRC'] = ID3::bigEndianToInt( $extheaderrawCRC, 1 );
				}
			
				$MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions'] = substr( $id3_exthead_flags, 3, 1 );

				if ( $MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions'] ) 
				{
					// Restrictions           %ppqrrstt
					$extheaderrawrestrictions = fread( $fd, 1 );
					$MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions_tagsize']  = ( bindec('11000000') & ord( $extheaderrawrestrictions ) ) >> 6; // p - Tag size restrictions
					$MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions_textenc']  = ( bindec('00100000') & ord( $extheaderrawrestrictions ) ) >> 5; // q - Text encoding restrictions
					$MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions_textsize'] = ( bindec('00011000') & ord( $extheaderrawrestrictions ) ) >> 3; // r - Text fields size restrictions
					$MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions_imgenc']   = ( bindec('00000100') & ord( $extheaderrawrestrictions ) ) >> 2; // s - Image encoding restrictions
					$MP3fileInfo['id3']['id3v2']['exthead_flags']['restrictions_imgsize']  = ( bindec('00000011') & ord( $extheaderrawrestrictions ) ) >> 0; // t - Image size restrictions
				}
			}

			// Frames
			//		All ID3v2 frames consists of one frame header followed by one or more
			//		fields containing the actual information. The header is always 10
			//		bytes and laid out as follows:
			//
			//		Frame ID      $xx xx xx xx  (four characters)
			//		Size      4 * %0xxxxxxx
			//		Flags         $xx xx

			$sizeofframes = $MP3fileInfo['id3']['id3v2']['headerlength'] - ID3::ID3v2HeaderLength( $MP3fileInfo['id3']['id3v2']['majorversion'] );
		
			if ( isset( $MP3fileInfo['id3']['id3v2']['extheaderlength'] ) )
				$sizeofframes -= $MP3fileInfo['id3']['id3v2']['extheaderlength'];
		
			if ( isset( $MP3fileInfo['id3']['id3v2']['flags']['isfooter'] ) && $MP3fileInfo['id3']['id3v2']['flags']['isfooter'] )
				$sizeofframes -= 10; // footer takes last 10 bytes of ID3v2 header, after frame data, before audio
		
			if ( $sizeofframes > 0 ) 
			{
				$framedata = fread( $fd, $sizeofframes ); // read all frames from file into $framedata variable

				// if entire frame data is unsynched, de-unsynch it now (ID3v2.3.x)
				if ( isset( $MP3fileInfo['id3']['id3v2']['flags']['unsynch'] ) && $MP3fileInfo['id3']['id3v2']['flags']['unsynch'] && ( $MP3fileInfo['id3']['id3v2']['majorversion'] <= 3 ) )
					$framedata = ID3::deUnSynchronise( $framedata );
			
				//		[in ID3v2.4.0] Unsynchronisation [S:6.1] is done on frame level, instead
				//		of on tag level, making it easier to skip frames, increasing the streamability
				//		of the tag. The unsynchronisation flag in the header [S:3.1] indicates that
				//		there exists an unsynchronised frame, while the new unsynchronisation flag in
				//		the frame header [S:4.1.2] indicates unsynchronisation.

				$framedataoffset = 10; // how many bytes into the stream - start from after the 10-byte header
			
				// cycle through until no more frame data is left to parse
				while ( isset( $framedata) && ( strlen( $framedata ) > 0 ) ) 
				{
					if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) 
					{
						// Frame ID  $xx xx xx (three characters)
						// Size      $xx xx xx (24-bit integer)
						// Flags     $xx xx

						$frame_header = substr( $framedata, 0, 6 ); // take next 6 bytes for header
						$framedata    = substr( $framedata, 6 );    // and leave the rest in $framedata
						$frame_name   = substr( $frame_header, 0, 3 );
						$frame_size   = ID3::bigEndianToInt( substr( $frame_header, 3, 3 ), 0 );
						$frame_flags  = ''; // not used for anything, just to avoid E_NOTICEs
					} 
					else if ( $MP3fileInfo['id3']['id3v2']['majorversion'] > 2 ) 
					{
						// Frame ID  $xx xx xx xx (four characters)
						// Size      $xx xx xx xx (32-bit integer in v2.3, 28-bit synchsafe in v2.4+)
						// Flags     $xx xx

						$frame_header = substr( $framedata, 0, 10 ); // take next 10 bytes for header
						$framedata    = substr( $framedata, 10 );    // and leave the rest in $framedata

						$frame_name = substr( $frame_header, 0, 4 );
						
						if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 3 )
							$frame_size = ID3::bigEndianToInt( substr( $frame_header, 4, 4 ), 0 ); // 32-bit integer
						else  // ID3v2.4+
							$frame_size = ID3::bigEndianToInt( substr( $frame_header, 4, 4 ), 1 ); // 32-bit synchsafe integer (28-bit value)

						if ( $frame_size < ( strlen( $framedata ) + 4 ) ) 
						{
							$nextFrameID = substr( $framedata, $frame_size, 4 );
						
							if ( ID3::isValidID3v2FrameName( $nextFrameID, $MP3fileInfo['id3']['id3v2']['majorversion'] ) ) 
							{
								// next frame is OK
							} 
							else if ( ( $frame_name == chr( 0 ) . 'MP3' ) || ( $frame_name == ' MP3' ) || ( $frame_name == 'MP3e' ) ) 
							{
								// MP3ext known broken frames - "ok" for the purposes of this test
							} 
							else if ( ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 4 ) && ( ID3::isValidID3v2FrameName( substr( $framedata, ID3::bigEndianToInt( substr( $frame_header, 4, 4 ), 0 ), 4 ), 3 ) ) ) 
							{
								$MP3fileInfo['error'][] = 'ID3v2 tag written as ID3v2.4, but with non-synchsafe integers (ID3v2.3 style). Older versions of Helium2 (www.helium2.com) is a known culprit of this. Tag has been parsed as ID3v2.3.';
								$MP3fileInfo['id3']['id3v2']['majorversion'] = 3;
								$frame_size = ID3::bigEndianToInt( substr( $frame_header, 4, 4 ), 0 ); // 32-bit integer
							}
						}


						$frame_flags = ID3::bigEndianToBin( substr( $frame_header, 8, 2 ) );
					}
					
					// padding encountered
					if ( $frame_name == chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) ) 
					{
						// $MP3fileInfo['id3']['id3v2']['padding']['start']  = $MP3fileInfo['id3']['id3v2']['headerlength'] - strlen($framedata);
						$MP3fileInfo['id3']['id3v2']['padding']['start']  = $framedataoffset;
						$MP3fileInfo['id3']['id3v2']['padding']['length'] = strlen( $framedata );
						$MP3fileInfo['id3']['id3v2']['padding']['valid']  = true;
				
						for ( $i = 0; $i < $MP3fileInfo['id3']['id3v2']['padding']['length']; $i++ ) 
						{
							if ( substr( $framedata, $i, 1 ) != chr( 0 ) ) 
							{
								$MP3fileInfo['id3']['id3v2']['padding']['valid']    = false;
								$MP3fileInfo['id3']['id3v2']['padding']['errorpos'] = $MP3fileInfo['id3']['id3v2']['padding']['start'] + $i;
							
								break;
							}
						}
					
						break; // skip rest of ID3v2 header
					}

					if ( ( $frame_size <= strlen( $framedata ) ) && ( ID3::isValidID3v2FrameName( $frame_name, $MP3fileInfo['id3']['id3v2']['majorversion'] ) ) ) 
					{
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['data']       = substr( $framedata, 0, $frame_size );
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['datalength'] = ID3::castAsInt( $frame_size );
						$MP3fileInfo['id3']['id3v2']["$frame_name"]['dataoffset'] = $framedataoffset;
						$framedata = substr( $framedata, $frame_size );

						// in getid3.frames.php - this function does all the FrameID-level parsing
						ID3::ID3v2FrameProcessing( $frame_name, $frame_flags, $MP3fileInfo );
						$framedataoffset += ( $frame_size + ID3::ID3v2HeaderLength( $MP3fileInfo['id3']['id3v2']['majorversion'] ) );
					}
					// invalid frame length or FrameID 
					else 
					{
						$MP3fileInfo['error'][] = 'Error parsing "' . $frame_name . '" (' . $framedataoffset . ' bytes into the ID3v2.' . $MP3fileInfo['id3']['id3v2']['majorversion'] . ' tag).';
					
						if ( !ID3::isValidID3v2FrameName( $frame_name, $MP3fileInfo['id3']['id3v2']['majorversion'] ) ) 
						{
							$MP3fileInfo['error'][] = '(ERROR: !ID3::isValidID3v2FrameName("' . str_replace( chr( 0 ), ' ', $frame_name ) . '", ' . $MP3fileInfo['id3']['id3v2']['majorversion'] . '))).';
						
							if ( ( $frame_name == chr( 0 ) . 'MP3' ) || ( $frame_name == ' MP3' ) || ( $frame_name == 'MP3e' ) ) 
								$MP3fileInfo['error'][] = '[Note: this particular error has been known to happen with tags edited by "MP3ext (www.mutschler.de/mp3ext/)"]';
							else if ( $frame_name == 'COM ' )
								$MP3fileInfo['error'][] = '[Note: this particular error has been known to happen with tags edited by "iTunes X v2.0.3"]';
						} 
						else if ( $frame_size > strlen( $framedata ) )
						{
							$MP3fileInfo['error'][] = '(ERROR: $frame_size (' . $frame_size . ') > strlen($framedata) (' . strlen( $framedata ) . ')).';
						}
					
						if ( ( $frame_size <= strlen( $framedata ) ) && ( ID3::isValidID3v2FrameName( substr( $framedata, $frame_size, 4 ), $MP3fileInfo['id3']['id3v2']['majorversion'] ) ) ) 
						{
							// next frame is valid, just skip the current frame
							$framedata = substr( $framedata, $frame_size );
						} 
						else 
						{
							// next frame is invalid too, abort processing
							unset( $framedata );
						}
					}
				}
			}
	
			//	Footer
			//	The footer is a copy of the header, but with a different identifier.
			//		ID3v2 identifier           "3DI"
			//		ID3v2 version              $04 00
			//		ID3v2 flags                %abcd0000
			//		ID3v2 size             4 * %0xxxxxxx

			if ( isset( $MP3fileInfo['id3']['id3v2']['flags']['isfooter'] ) && $MP3fileInfo['id3']['id3v2']['flags']['isfooter'] ) 
			{
				$footer = fread( $fd, 10 );
			
				if ( substr( $footer, 0, 3 ) == '3DI' ) 
				{
					$MP3fileInfo['id3']['id3v2']['footer'] = true;
					$MP3fileInfo['id3']['id3v2']['majorversion_footer'] = ord( substr( $footer, 3, 1 ) );
					$MP3fileInfo['id3']['id3v2']['minorversion_footer'] = ord( substr( $footer, 4, 1 ) );
				}
			
				if ( $MP3fileInfo['id3']['id3v2']['majorversion_footer'] <= 4 ) 
				{
					$id3_flags = ID3::bigEndianToBin( substr( $footer, 5, 1 ) );
					$MP3fileInfo['id3']['id3v2']['flags']['unsynch_footer']  = substr( $id3_flags, 0, 1 );
					$MP3fileInfo['id3']['id3v2']['flags']['extfoot_footer']  = substr( $id3_flags, 1, 1 );
					$MP3fileInfo['id3']['id3v2']['flags']['experim_footer']  = substr( $id3_flags, 2, 1 );
					$MP3fileInfo['id3']['id3v2']['flags']['isfooter_footer'] = substr( $id3_flags, 3, 1 );	

					$MP3fileInfo['id3']['id3v2']['footerlength'] = ID3::bigEndianToInt( substr( $footer, 6, 4 ), 1 );
				}
			}

			// Translate most common ID3v2 FrameIDs to easier-to-understand names
			if ( $MP3fileInfo['id3']['id3v2']['majorversion'] == 2 ) 
			{
				if ( isset( $MP3fileInfo['id3']['id3v2']['TT2'] ) )                  
					$MP3fileInfo['id3']['id3v2']['title']   = $MP3fileInfo['id3']['id3v2']['TT2']['asciidata']; 
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TP1'] ) )                  
					$MP3fileInfo['id3']['id3v2']['artist']  = $MP3fileInfo['id3']['id3v2']['TP1']['asciidata'];
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TAL'] ) )                  
					$MP3fileInfo['id3']['id3v2']['album']   = $MP3fileInfo['id3']['id3v2']['TAL']['asciidata'];
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TYE'] ) )                  
					$MP3fileInfo['id3']['id3v2']['year']    = $MP3fileInfo['id3']['id3v2']['TYE']['asciidata'];
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TRK'] ) )                  
					$MP3fileInfo['id3']['id3v2']['track']   = $MP3fileInfo['id3']['id3v2']['TRK']['asciidata']; 
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TCO'] ) )                  
					$MP3fileInfo['id3']['id3v2']['genre']   = $MP3fileInfo['id3']['id3v2']['TCO']['asciidata'];
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['COM'][0]['asciidata'] ) )  
					$MP3fileInfo['id3']['id3v2']['comment'] = $MP3fileInfo['id3']['id3v2']['COM'][0]['asciidata'];
			} 
			else 
			{
				if ( isset( $MP3fileInfo['id3']['id3v2']['TIT2'] ) )                 
					$MP3fileInfo['id3']['id3v2']['title']   = $MP3fileInfo['id3']['id3v2']['TIT2']['asciidata'];   
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TPE1'] ) )                 
					$MP3fileInfo['id3']['id3v2']['artist']  = $MP3fileInfo['id3']['id3v2']['TPE1']['asciidata'];    
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TALB'] ) )                 
					$MP3fileInfo['id3']['id3v2']['album']   = $MP3fileInfo['id3']['id3v2']['TALB']['asciidata'];    
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TYER'] ) )                 
					$MP3fileInfo['id3']['id3v2']['year']    = $MP3fileInfo['id3']['id3v2']['TYER']['asciidata'];    
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TRCK'] ) )                 
					$MP3fileInfo['id3']['id3v2']['track']   = $MP3fileInfo['id3']['id3v2']['TRCK']['asciidata'];    
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['TCON'] ) )                 
					$MP3fileInfo['id3']['id3v2']['genre']   = $MP3fileInfo['id3']['id3v2']['TCON']['asciidata'];    
			
				if ( isset( $MP3fileInfo['id3']['id3v2']['COMM'][0]['asciidata'] ) ) 
					$MP3fileInfo['id3']['id3v2']['comment'] = $MP3fileInfo['id3']['id3v2']['COMM'][0]['asciidata']; 
			}
		
			if ( isset( $MP3fileInfo['id3']['id3v2']['genre'] ) ) 
			{
				$MP3fileInfo['id3']['id3v2']['genrelist'] = ID3::parseID3v2GenreString( $MP3fileInfo['id3']['id3v2']['genre'] );
				
				if ( $MP3fileInfo['id3']['id3v2']['genrelist']['genreid'][0] !== '' )
					$MP3fileInfo['id3']['id3v2']['genreid'] = $MP3fileInfo['id3']['id3v2']['genrelist']['genreid'][0];
			
				$MP3fileInfo['id3']['id3v2']['genre'] = $MP3fileInfo['id3']['id3v2']['genrelist']['genre'][0];
			}
		
			if ( isset( $MP3fileInfo['id3']['id3v2']['track'] ) && strpos( $MP3fileInfo['id3']['id3v2']['track'], '/') !== false ) 
			{
				$tracktotaltracks = explode( '/', $MP3fileInfo['id3']['id3v2']['track'] );
				$MP3fileInfo['id3']['id3v2']['track'] = $tracktotaltracks[0];
				$MP3fileInfo['id3']['id3v2']['totaltracks'] = $tracktotaltracks[1];
			}
		}
		// MajorVersion is > 4, or no ID3v2 header present 
		else 
		{
			if ( isset( $MP3fileInfo['id3']['id3v2']['header'] ) ) 
			{ 
				// MajorVersion is > 4
				$MP3fileInfo['error'][] = 'This script only parses up to ID3v2.4.x - this tag is ID3v2.' . $MP3fileInfo['id3']['id3v2']['majorversion'] . '.' . $MP3fileInfo['id3']['id3v2']['minorversion'] . '.';
			} 
			else 
			{
				// no ID3v2 header present - this is fine, just don't process anything.
			}
		}

		return true;
	}

	function parseID3v2GenreString( $genrestring ) 
	{
		// Parse genres into arrays of genreName and genreID
		// ID3v2.2.x, ID3v2.3.x: '(21)' or '(4)Eurodisco' or '(51)(39)' or '(55)((I think...)'
		// ID3v2.4.x: '21' $00 'Eurodisco' $00
		$returnarray = null;
	
		if ( strpos( $genrestring, chr( 0 ) ) !== false ) 
		{
			$unprocessed = trim( $genrestring ); // trailing nulls will cause an infinite loop.
			$genrestring = '';
		
			while ( strpos( $unprocessed, chr( 0 ) ) !== false ) 
			{
				// convert null-seperated v2.4-format into v2.3 ()-seperated format
				$endpos       = strpos( $unprocessed, chr( 0 ) );
				$genrestring .= '(' . substr( $unprocessed, 0, $endpos ) . ')';
				$unprocessed  = substr( $unprocessed, $endpos + 1 );
			}
		
			unset( $unprocessed );
		}
	
		while ( strpos( $genrestring, '(' ) !== false ) 
		{
			$startpos = strpos( $genrestring, '(' );
			$endpos   = strpos( $genrestring, ')' );
		
			if ( substr( $genrestring, $startpos + 1, 1 ) == '(' ) 
			{
				$genrestring = substr( $genrestring, 0, $startpos ) . substr( $genrestring, $startpos + 1 );
				$endpos--;
			}

			$element     = substr( $genrestring, $startpos + 1, $endpos - ( $startpos + 1 ) );
			$genrestring = substr( $genrestring, 0, $startpos ) . substr( $genrestring, $endpos + 1 );
	
			// $element is a valid genre id/abbreviation
			if ( ID3::lookupGenre( $element ) !== '' ) 
			{
				// avoid duplicate entires
				if ( !is_array( $returnarray['genre'] ) || !in_array( ID3::lookupGenre( $element ), $returnarray['genre'] ) ) 
				{
					if ( ( $element == 'CR' ) && ( $element == 'RX' ) )
						$returnarray['genreid'][] = $element;
					else
						$returnarray['genreid'][] = (int)$element;
				
					$returnarray['genre'][] = ID3::lookupGenre( $element );
				}
			} 
			else 
			{
				// avoid duplicate entires
				if ( !is_array( $returnarray['genre'] ) || !in_array( $element, $returnarray['genre'] ) ) 
				{
					$returnarray['genreid'][] = '';
					$returnarray['genre'][]   = $element;
				}
			}
		}
	
		if ( $genrestring ) 
		{
			// avoid duplicate entires
			if ( !is_array( $returnarray['genre'] ) || !in_array( $genrestring, $returnarray['genre'] ) ) 
			{
				$returnarray['genreid'][] = '';
				$returnarray['genre'][]   = $genrestring;
			}
		}

		return $returnarray;
	}

	function getURLImageSize( $urlpic ) 
	{
		if ( $fd = @fopen( $urlpic, 'rb' ) )
		{
			$imgData = fread( $fd, filesize( $urlpic ) );
			fclose( $fd );
		
			return ID3::getDataImageSize( $imgData );
		} 
		else 
		{
			return array('', '', '');
		}
	}

	function getDataImageSize( $imgData ) 
	{
		$height = '';
		$width  = '';
		$type   = '';
	
		if ( ( substr( $imgData, 0, 3 ) == ID3_GIF_SIG ) && ( strlen( $imgData ) > 10 ) ) 
		{
			$dim    = unpack( 'v2dim', substr( $imgData, 6, 4 ) );
			$width  = $dim['dim1'];
			$height = $dim['dim2'];
			$type   = 1;
		} 
		else if ( ( substr( $imgData, 0, 8 ) == ID3_PNG_SIG ) && ( strlen( $imgData ) > 24 ) ) 
		{
			$dim    = unpack( 'N2dim', substr( $imgData, 16, 8 ) );
			$width  = $dim['dim1'];
			$height = $dim['dim2'];
			$type   = 3;
		} 
		else if ( ( substr( $imgData, 0, 3 ) == ID3_JPG_SIG ) && ( strlen( $imgData ) > 4 ) ) 
		{
			///////////////// JPG CHUNK SCAN ////////////////////
			$imgPos = 2;
			$type   = 2;
			$buffer = strlen( $imgData ) - 2;
		
			while ( $imgPos < strlen( $imgData ) ) 
			{
				// synchronize to the marker 0xFF
				$imgPos = strpos( $imgData, 0xFF, $imgPos ) + 1;
				$marker = $imgData[$imgPos];
			
				do 
				{
					$marker = ord( $imgData[$imgPos++] );
				} while ( $marker == 255 );
			
				// find dimensions of block
				switch ( chr( $marker ) ) 
				{
					// Grab width/height from SOF segment (these are acceptable chunk types)
					case ID3_JPG_SOF0:
			
					case ID3_JPG_SOF1:
			
					case ID3_JPG_SOF2:
			
					case ID3_JPG_SOF3:
			
					case ID3_JPG_SOF5:
			
					case ID3_JPG_SOF6:
			
					case ID3_JPG_SOF7:
			
					case ID3_JPG_SOF9:
			
					case ID3_JPG_SOF10:
			
					case ID3_JPG_SOF11:
			
					case ID3_JPG_SOF13:
			
					case ID3_JPG_SOF14:
			
					case ID3_JPG_SOF15:
						$dim    = unpack( 'n2dim', substr( $imgData, $imgPos + 3, 4 ) );
						$height = $dim['dim1'];
						$width  = $dim['dim2'];
						
						break 2; // found it so exit
				
					case ID3_JPG_EOI:
				
					case ID3_JPG_SOS:
						return false; 	  // End loop in case we find one of these markers
				
					default:              // We're not interested in other markers
						$skiplen = ( ord( $imgData[$imgPos++] ) << 8 ) + ord( $imgData[$imgPos++] ) - 2;
						
						// if the skip is more than what we've read in, read more
						$buffer -= $skiplen;
					
						// if the buffer of data is too low, read more file
						if ( $buffer < 512 ) 
						{
							// $imgData .= fread( $fd,$skiplen+1024 );
							// $buffer += $skiplen + 1024;
							return false; // End loop in case we find run out of data
						}
					
						$imgPos += $skiplen;
						break;
				}
			}
		}

		return array( $width, $height, $type );
	}

	function imageTypesLookup( $imagetypeid ) 
	{
		static $ImageTypesLookup = array();
	
		if ( count( $ImageTypesLookup ) < 1 ) 
		{
			$ImageTypesLookup[1]  = 'gif';
			$ImageTypesLookup[2]  = 'jpg';
			$ImageTypesLookup[3]  = 'png';
			$ImageTypesLookup[4]  = 'swf';
			$ImageTypesLookup[5]  = 'psd';
			$ImageTypesLookup[6]  = 'bmp';
			$ImageTypesLookup[7]  = 'tiff (little-endian)';
			$ImageTypesLookup[8]  = 'tiff (big-endian)';
			$ImageTypesLookup[9]  = 'jpc';
			$ImageTypesLookup[10] = 'jp2';
			$ImageTypesLookup[11] = 'jpx';
			$ImageTypesLookup[12] = 'jb2';
			$ImageTypesLookup[13] = 'swc';
			$ImageTypesLookup[14] = 'iff';
		}
	
		return ( isset( $ImageTypesLookup["$imagetypeid"] )? $ImageTypesLookup["$imagetypeid"] : '' );
	}

	function arrayOfGenres() 
	{
		static $GenreLookup = array();
	
		if ( count( $GenreLookup ) < 1 ) 
		{
			$GenreLookup[0]    = 'Blues';
			$GenreLookup[1]    = 'Classic Rock';
			$GenreLookup[2]    = 'Country';
			$GenreLookup[3]    = 'Dance';
			$GenreLookup[4]    = 'Disco';
			$GenreLookup[5]    = 'Funk';
			$GenreLookup[6]    = 'Grunge';
			$GenreLookup[7]    = 'Hip-Hop';
			$GenreLookup[8]    = 'Jazz';
			$GenreLookup[9]    = 'Metal';
			$GenreLookup[10]   = 'New Age';
			$GenreLookup[11]   = 'Oldies';
			$GenreLookup[12]   = 'Other';
			$GenreLookup[13]   = 'Pop';
			$GenreLookup[14]   = 'R&B';
			$GenreLookup[15]   = 'Rap';
			$GenreLookup[16]   = 'Reggae';
			$GenreLookup[17]   = 'Rock';
			$GenreLookup[18]   = 'Techno';
			$GenreLookup[19]   = 'Industrial';
			$GenreLookup[20]   = 'Alternative';
			$GenreLookup[21]   = 'Ska';
			$GenreLookup[22]   = 'Death Metal';
			$GenreLookup[23]   = 'Pranks';
			$GenreLookup[24]   = 'Soundtrack';
			$GenreLookup[25]   = 'Euro-Techno';
			$GenreLookup[26]   = 'Ambient';
			$GenreLookup[27]   = 'Trip-Hop';
			$GenreLookup[28]   = 'Vocal';
			$GenreLookup[29]   = 'Jazz+Funk';
			$GenreLookup[30]   = 'Fusion';
			$GenreLookup[31]   = 'Trance';
			$GenreLookup[32]   = 'Classical';
			$GenreLookup[33]   = 'Instrumental';
			$GenreLookup[34]   = 'Acid';
			$GenreLookup[35]   = 'House';
			$GenreLookup[36]   = 'Game';
			$GenreLookup[37]   = 'Sound Clip';
			$GenreLookup[38]   = 'Gospel';
			$GenreLookup[39]   = 'Noise';
			$GenreLookup[40]   = 'Alt. Rock';
			$GenreLookup[41]   = 'Bass';
			$GenreLookup[42]   = 'Soul';
			$GenreLookup[43]   = 'Punk';
			$GenreLookup[44]   = 'Space';
			$GenreLookup[45]   = 'Meditative';
			$GenreLookup[46]   = 'Instrumental Pop';
			$GenreLookup[47]   = 'Instrumental Rock';
			$GenreLookup[48]   = 'Ethnic';
			$GenreLookup[49]   = 'Gothic';
			$GenreLookup[50]   = 'Darkwave';
			$GenreLookup[51]   = 'Techno-Industrial';
			$GenreLookup[52]   = 'Electronic';
			$GenreLookup[53]   = 'Folk/Pop';
			$GenreLookup[54]   = 'Eurodance';
			$GenreLookup[55]   = 'Dream';
			$GenreLookup[56]   = 'Southern Rock';
			$GenreLookup[57]   = 'Comedy';
			$GenreLookup[58]   = 'Cult';
			$GenreLookup[59]   = 'Gangsta';
			$GenreLookup[60]   = 'Top 40';
			$GenreLookup[61]   = 'Christian Rap';
			$GenreLookup[62]   = 'Pop/Funk';
			$GenreLookup[63]   = 'Jungle';
			$GenreLookup[64]   = 'Native American';
			$GenreLookup[65]   = 'Cabaret';
			$GenreLookup[66]   = 'New Wave';
			$GenreLookup[67]   = 'Psychadelic';
			$GenreLookup[68]   = 'Rave';
			$GenreLookup[69]   = 'Showtunes';
			$GenreLookup[70]   = 'Trailer';
			$GenreLookup[71]   = 'Lo-Fi';
			$GenreLookup[72]   = 'Tribal';
			$GenreLookup[73]   = 'Acid Punk';
			$GenreLookup[74]   = 'Acid Jazz';
			$GenreLookup[75]   = 'Polka';
			$GenreLookup[76]   = 'Retro';
			$GenreLookup[77]   = 'Musical';
			$GenreLookup[78]   = 'Rock & Roll';
			$GenreLookup[79]   = 'Hard Rock';
			$GenreLookup[80]   = 'Folk';
			$GenreLookup[81]   = 'Folk/Rock';
			$GenreLookup[82]   = 'National Folk';
			$GenreLookup[83]   = 'Swing';
			$GenreLookup[84]   = 'Fast-Fusion';
			$GenreLookup[85]   = 'Bebob';
			$GenreLookup[86]   = 'Latin';
			$GenreLookup[87]   = 'Revival';
			$GenreLookup[88]   = 'Celtic';
			$GenreLookup[89]   = 'Bluegrass';
			$GenreLookup[90]   = 'Avantgarde';
			$GenreLookup[91]   = 'Gothic Rock';
			$GenreLookup[92]   = 'Progressive Rock';
			$GenreLookup[93]   = 'Psychedelic Rock';
			$GenreLookup[94]   = 'Symphonic Rock';
			$GenreLookup[95]   = 'Slow Rock';
			$GenreLookup[96]   = 'Big Band';
			$GenreLookup[97]   = 'Chorus';
			$GenreLookup[98]   = 'Easy Listening';
			$GenreLookup[99]   = 'Acoustic';
			$GenreLookup[100]  = 'Humour';
			$GenreLookup[101]  = 'Speech';
			$GenreLookup[102]  = 'Chanson';
			$GenreLookup[103]  = 'Opera';
			$GenreLookup[104]  = 'Chamber Music';
			$GenreLookup[105]  = 'Sonata';
			$GenreLookup[106]  = 'Symphony';
			$GenreLookup[107]  = 'Booty Bass';
			$GenreLookup[108]  = 'Primus';
			$GenreLookup[109]  = 'Porn Groove';
			$GenreLookup[110]  = 'Satire';
			$GenreLookup[111]  = 'Slow Jam';
			$GenreLookup[112]  = 'Club';
			$GenreLookup[113]  = 'Tango';
			$GenreLookup[114]  = 'Samba';
			$GenreLookup[115]  = 'Folklore';
			$GenreLookup[116]  = 'Ballad';
			$GenreLookup[117]  = 'Power Ballad';
			$GenreLookup[118]  = 'Rhythmic Soul';
			$GenreLookup[119]  = 'Freestyle';
			$GenreLookup[120]  = 'Duet';
			$GenreLookup[121]  = 'Punk Rock';
			$GenreLookup[122]  = 'Drum Solo';
			$GenreLookup[123]  = 'A Cappella';
			$GenreLookup[124]  = 'Euro-House';
			$GenreLookup[125]  = 'Dance Hall';
			$GenreLookup[126]  = 'Goa';
			$GenreLookup[127]  = 'Drum & Bass';
			$GenreLookup[128]  = 'Club-House';
			$GenreLookup[129]  = 'Hardcore';
			$GenreLookup[130]  = 'Terror';
			$GenreLookup[131]  = 'Indie';
			$GenreLookup[132]  = 'BritPop';
			$GenreLookup[133]  = 'Negerpunk';
			$GenreLookup[134]  = 'Polsk Punk';
			$GenreLookup[135]  = 'Beat';
			$GenreLookup[136]  = 'Christian Gangsta Rap';
			$GenreLookup[137]  = 'Heavy Metal';
			$GenreLookup[138]  = 'Black Metal';
			$GenreLookup[139]  = 'Crossover';
			$GenreLookup[140]  = 'Contemporary Christian';
			$GenreLookup[141]  = 'Christian Rock';
			$GenreLookup[142]  = 'Merengue';
			$GenreLookup[143]  = 'Salsa';
			$GenreLookup[144]  = 'Trash Metal';
			$GenreLookup[145]  = 'Anime';
			$GenreLookup[146]  = 'Jpop';
			$GenreLookup[147]  = 'Synthpop';
			$GenreLookup[255]  = 'Unknown';

			$GenreLookup['CR'] = 'Cover';
			$GenreLookup['RX'] = 'Remix';
		}
	
		return $GenreLookup;
	}

	function lookupGenre( $genreid, $returnkey = false ) 
	{
		if ( ( $genreid != 'RX' ) && ( $genreid === 'CR' ) ) 
			$genreid = (int)$genreid; // to handle 3 or '3' or '03'
	
		$GenreLookup = ID3::arrayOfGenres();
	
		if ( $returnkey ) 
		{
			$LowerCaseNoSpaceSearchTerm = strtolower( str_replace( ' ', '', $genreid ) );
		
			foreach ( $GenreLookup as $key => $value ) 
			{
				if ( strtolower( str_replace( ' ', '', $value ) ) == $LowerCaseNoSpaceSearchTerm )
					return $key;
			}
		
			return '';
		} 
		else 
		{
			return ( isset( $GenreLookup["$genreid"] )? $GenreLookup["$genreid"] : '' );
		}
	}

	function generateID3v2TagFlags( $majorversion = 4, $Unsynchronisation = false, $Compression = false, $ExtendedHeader = false, $Experimental = false, $Footer = false ) 
	{
		if ( $majorversion == 4 ) 
		{
			// %abcd0000
			$flag  = ID3::boolToIntString( $Unsynchronisation ); // a - Unsynchronisation
			$flag .= ID3::boolToIntString( $ExtendedHeader );    // b - Extended header
			$flag .= ID3::boolToIntString( $Experimental );      // c - Experimental indicator
			$flag .= ID3::boolToIntString( $Footer );            // d - Footer present
			$flag .= '0000';
		} 
		else if ( $majorversion == 3 ) 
		{
			// %abc00000
			$flag  = ID3::boolToIntString( $Unsynchronisation ); // a - Unsynchronisation
			$flag .= ID3::boolToIntString( $ExtendedHeader );    // b - Extended header
			$flag .= ID3::boolToIntString( $Experimental );      // c - Experimental indicator
			$flag .= '00000';
		} 
		else if ( $majorversion == 2 ) 
		{
			// %ab000000
			$flag  = ID3::boolToIntString( $Unsynchronisation ); // a - Unsynchronisation
			$flag .= ID3::boolToIntString( $Compression );       // b - Compression
			$flag .= '000000';
		} 
		else 
		{
			return false;
		}
	
		return chr( bindec( $flag ) );
	}

	function generateID3v2FrameFlags( $majorversion = 4, $TagAlter = false, $FileAlter = false, $ReadOnly = false, $Compression = false, $Encryption = false, $GroupingIdentity = false, $Unsynchronisation = false, $DataLengthIndicator = false ) 
	{
		if ( $majorversion == 4 ) 
		{
			// %0abc0000 %0h00kmnp
			$flag1  = '0';
			$flag1 .= ID3::boolToIntString( $TagAlter  ); // a - Tag alter preservation  (true == discard)
			$flag1 .= ID3::boolToIntString( $FileAlter ); // b - File alter preservation (true == discard)
			$flag1 .= ID3::boolToIntString( $ReadOnly  ); // c - Read only (true == read only)
			$flag1 .= '0000';

			$flag2  = '0';
			$flag2 .= ID3::boolToIntString( $GroupingIdentity );    // h - Grouping identity (true == contains group information)
			$flag2 .= '00';
			$flag2 .= ID3::boolToIntString( $Compression );         // k - Compression (true == compressed)
			$flag2 .= ID3::boolToIntString( $Encryption  );         // m - Encryption  (true == encrypted)
			$flag2 .= ID3::boolToIntString( $Unsynchronisation );   // n - Unsynchronisation (true == unsynchronised)
			$flag2 .= ID3::boolToIntString( $DataLengthIndicator ); // p - Data length indicator (true == data length indicator added)
		} 
		else if ( $majorversion == 3 ) 
		{
			// %abc00000 %ijk00000
			$flag1  = ID3::boolToIntString( $TagAlter  ); // a - Tag alter preservation  (true == discard)
			$flag1 .= ID3::boolToIntString( $FileAlter ); // b - File alter preservation (true == discard)
			$flag1 .= ID3::boolToIntString( $ReadOnly  ); // c - Read only (true == read only)
			$flag1 .= '00000';

			$flag2  = ID3::boolToIntString( $Compression );         // i - Compression (true == compressed)
			$flag2 .= ID3::boolToIntString( $Encryption  );         // j - Encryption  (true == encrypted)
			$flag2 .= ID3::boolToIntString( $GroupingIdentity );    // k - Grouping identity (true == contains group information)
			$flag2 .= '00000';
		} 
		else 
		{
			return false;
		}
	
		return chr( bindec( $flag1 ) ) . chr( bindec( $flag2 ) );
	}

	function generateID3v2FrameData( $frame_name, $frame_data, $majorversion = 4, $showerrors = false ) 
	{
		if ( !ID3::isValidID3v2FrameName( $frame_name, $majorversion ) )
			return false;
	
		$error     = '';
		$framedata = '';
	
		if ( $majorversion == 2 ) 
		{
			ksort( $frame_data );
			reset( $frame_data );
			
			switch ( $frame_name ) 
			{
				case 'TXX':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'WXX':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'IPL':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'MCI':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'ETC':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'MLL':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'STC':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'ULT':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'SLT':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'COM':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'RVA':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'EQU':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'REV':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'PIC':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'GEO':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'CNT':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'POP':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'BUF':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'CRM':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'CRA':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				case 'LNK':
					$error .= $frame_name . ' not yet supported<br>';
					break;
		
				default:
					if ( $frame_name{0} == 'T' ) 
					{
						// T??
						$error .= $frame_name . ' not yet supported<br>';
					} 
					else if ( $frame_name{0} == 'W' ) 
					{
						// W??
						$error .= $frame_name . ' not yet supported<br>';
					} 
					else 
					{
						$error .= $frame_name . ' not yet supported<br>';
						return false;
					}
			}
		}
		// $majorversion > 2 
		else 
		{
			switch ( $frame_name ) 
			{
				case 'UFID':
					// 4.1   UFID Unique file identifier
					// Owner identifier        <text string> $00
					// Identifier              <up to 64 bytes binary data>
					if ( strlen( $frame_data['data'] ) > 64 ) 
					{
						$error .= 'Identifier not allowed to be longer than 64 bytes in ' . $frame_name . ' (supplied data was ' . strlen( $frame_data['data'] ) . ' bytes long)<br>';
					} 
					else 
					{
						$framedata .= str_replace( chr( 0 ), '', $frame_data['ownerid'] ) . chr( 0 );
						$framedata .= substr( $frame_data['data'], 0, 64 ); // max 64 bytes - truncate anything longer
					}
				
					break;

				case 'TXXX':
					// 4.2.2 TXXX User defined text information frame
					// Text encoding     $xx
					// Description       <text string according to encoding> $00 (00)
					// Value             <text string according to encoding>
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['data'];
					}
		
					break;
	
				case 'WXXX':
					// 4.3.2 WXXX User defined URL link frame
					// Text encoding     $xx
					// Description       <text string according to encoding> $00 (00)
					// URL               <text string>
	
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( !isset( $frame_data['url'] ) || !ID3::isValidURL( $frame_data['url'], false, false ) ) 
					{
						$error .= 'Invalid URL in ' . $frame_name . ' (' . $frame_data['url'].')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['url'];
					}
			
					break;
		
				case 'IPLS':
					// 4.4  IPLS Involved people list (ID3v2.3 only)
					// Text encoding     $xx
					// People list strings    <textstrings>
		
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= $frame_data['data'];
					}
			
					break;
			
				case 'MCDI':
					// 4.4   MCDI Music CD identifier
					// CD TOC                <binary data>
					$framedata .= $frame_data['data'];
					break;
	
				case 'ETCO':
					// 4.5   ETCO Event timing codes
					// Time stamp format    $xx
					//   Where time stamp format is:
					// $01  (32-bit value) MPEG frames from beginning of file
					// $02  (32-bit value) milliseconds from beginning of file
					//   Followed by a list of key events in the following format:
					// Type of event   $xx
					// Time stamp      $xx (xx ...)
					//   The 'Time stamp' is set to zero if directly at the beginning of the sound
					//   or after the previous event. All events MUST be sorted in chronological order.
		
					if ( ( $frame_data['timestampformat'] > 2 ) || ( $frame_data['timestampformat'] < 1 ) ) 
					{
						$error .= 'Invalid Time Stamp Format byte in ' . $frame_name . ' (' . $frame_data['timestampformat'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['timestampformat'] );
					
						foreach ( $frame_data as $key => $val ) 
						{
							if ( !ID3::isValidETCOevent( $val['typeid'], $majorversion ) ) 
							{
								$error .= 'Invalid Event Type byte in ' . $frame_name . ' (' . $val['typeid'].')<br>';
							} 
							else if ( ( $key != 'timestampformat' ) && ( $key != 'flags' ) ) 
							{
								if ( ( $val['timestamp'] > 0 ) && ( $previousETCOtimestamp >= $val['timestamp'] ) ) 
								{
									// The 'Time stamp' is set to zero if directly at the beginning of the sound
									// or after the previous event. All events MUST be sorted in chronological order.
									$error .= 'Out-of-order timestamp in ' . $frame_name . ' (' . $val['timestamp'] . ') for Event Type (' . $val['typeid'].')<br>';
								} 
								else 
								{
									$framedata .= chr( $val['typeid'] );
									$framedata .= ID3::bigEndianToString( $val['timestamp'], 4, false );
								}
							}
						}
					}
			
					break;
					
				case 'MLLT':
					// 4.6   MLLT MPEG location lookup table
					// MPEG frames between reference  $xx xx
					// Bytes between reference        $xx xx xx
					// Milliseconds between reference $xx xx xx
					// Bits for bytes deviation       $xx
					// Bits for milliseconds dev.     $xx
					//   Then for every reference the following data is included;
					// Deviation in bytes         %xxx....
					// Deviation in milliseconds  %xxx....
			
					if ( ( $frame_data['framesbetweenreferences'] > 0 ) && ( $frame_data['framesbetweenreferences'] <= 65535 ) )
						$framedata .= ID3::bigEndianToString( $frame_data['framesbetweenreferences'], 2, false );
					else
						$error .= 'Invalid MPEG Frames Between References in ' . $frame_name . ' (' . $frame_data['framesbetweenreferences'] . ')<br>';
				
					if ( ( $frame_data['bytesbetweenreferences'] > 0 ) && ( $frame_data['bytesbetweenreferences'] <= 16777215 ) )
						$framedata .= ID3::bigEndianToString( $frame_data['bytesbetweenreferences'], 3, false );
					else
						$error .= 'Invalid bytes Between References in ' . $frame_name . ' (' . $frame_data['bytesbetweenreferences'] . ')<br>';
				
					if ( ( $frame_data['msbetweenreferences'] > 0 ) && ( $frame_data['msbetweenreferences'] <= 16777215 ) )
						$framedata .= ID3::bigEndianToString( $frame_data['msbetweenreferences'], 3, false );
					else
						$error .= 'Invalid Milliseconds Between References in ' . $frame_name . ' (' . $frame_data['msbetweenreferences'] . ')<br>';
				
					if ( !ID3::isWithinBitRange( $frame_data['bitsforbytesdeviation'], 8, false ) ) 
					{
						if ( ( $frame_data['bitsforbytesdeviation'] % 4 ) == 0 )
							$framedata .= chr( $frame_data['bitsforbytesdeviation'] );
						else
							$error .= 'Bits For Bytes Deviation in ' . $frame_name . ' (' . $frame_data['bitsforbytesdeviation'] . ') must be a multiple of 4.<br>';		
					} 
					else 
					{
						$error .= 'Invalid Bits For Bytes Deviation in ' . $frame_name . ' (' . $frame_data['bitsforbytesdeviation'] . ')<br>';
					}
					
					if ( !ID3::isWithinBitRange( $frame_data['bitsformsdeviation'], 8, false ) ) 
					{
						if ( ( $frame_data['bitsformsdeviation'] % 4 ) == 0 )
							$framedata .= chr( $frame_data['bitsformsdeviation'] );
						else
							$error .= 'Bits For Milliseconds Deviation in ' . $frame_name . ' (' . $frame_data['bitsforbytesdeviation'] . ') must be a multiple of 4.<br>';
					} 
					else 
					{
						$error .= 'Invalid Bits For Milliseconds Deviation in ' . $frame_name . ' (' . $frame_data['bitsformsdeviation'] . ')<br>';
					}
				
					foreach ( $frame_data as $key => $val ) 
					{
						if ( ( $key != 'framesbetweenreferences' ) && ( $key != 'bytesbetweenreferences' ) && ( $key != 'msbetweenreferences' ) && ( $key != 'bitsforbytesdeviation' ) && ( $key != 'bitsformsdeviation' ) && ( $key != 'flags' ) ) 
						{
							$unwrittenbitstream .= str_pad( ID3::decToBin( $val['bytedeviation'] ), $frame_data['bitsforbytesdeviation'], '0', STR_PAD_LEFT );
							$unwrittenbitstream .= str_pad( ID3::decToBin( $val['msdeviation']   ), $frame_data['bitsformsdeviation'],    '0', STR_PAD_LEFT );
						}
					}
				
					for ( $i = 0; $i < strlen( $unwrittenbitstream ); $i += 8 ) 
					{
						$highnibble  = bindec( substr( $unwrittenbitstream, $i, 4 ) ) << 4;
						$lownibble   = bindec( substr( $unwrittenbitstream, $i + 4, 4 ) );
						$framedata  .= chr( $highnibble & $lownibble );
					}
				
					break;
	
				case 'SYTC':
					// 4.7   SYTC Synchronised tempo codes
					// Time stamp format   $xx
					// Tempo data          <binary data>
					// Where time stamp format is:
					// $01  (32-bit value) MPEG frames from beginning of file
					// $02  (32-bit value) milliseconds from beginning of file
					if ( ( $frame_data['timestampformat'] > 2 ) || ( $frame_data['timestampformat'] < 1 ) ) 
					{
						$error .= 'Invalid Time Stamp Format byte in ' . $frame_name . ' (' . $frame_data['timestampformat'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['timestampformat'] );

						foreach ( $frame_data as $key => $val ) 
						{
							if ( !ID3::isValidETCOevent( $val['typeid'], $majorversion ) ) 
							{
								$error .= 'Invalid Event Type byte in ' . $frame_name . ' (' . $val['typeid'] . ')<br>';
							} 
							else if ( ( $key != 'timestampformat' ) && ( $key != 'flags' ) ) 
							{
								if ( ( $val['tempo'] < 0 ) || ( $val['tempo'] > 510 ) ) 
								{
									$error .= 'Invalid Tempo (max = 510) in ' . $frame_name . ' (' . $val['tempo'] . ') at timestamp (' . $val['timestamp'] . ')<br>';
								} 
								else 
								{
									if ( $val['tempo'] > 255 ) 
									{
										$framedata .= chr( 255 );
										$val['tempo'] -= 255;
									}
					
									$framedata .= chr( $val['tempo'] );
									$framedata .= ID3::bigEndianToString( $val['timestamp'], 4, false );
								}
							}
						}
					}
		
					break;
	
				case 'USLT':
					// 4.8   USLT Unsynchronised lyric/text transcription
					// Text encoding        $xx
					// Language             $xx xx xx
					// Content descriptor   <text string according to encoding> $00 (00)
					// Lyrics/text          <full text string according to encoding>
	
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( ID3::languageLookup( $frame_data['language'], true ) == '' ) 
					{
						$error .= 'Invalid Language in ' . $frame_name . ' (' . $frame_data['language'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= strtolower( $frame_data['language'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['data'];
					}
			
					break;
			
				case 'SYLT':
					// 4.9   SYLT Synchronised lyric/text
					// Text encoding        $xx
					// Language             $xx xx xx
					// Time stamp format    $xx
					//   $01  (32-bit value) MPEG frames from beginning of file
					//   $02  (32-bit value) milliseconds from beginning of file
					// Content type         $xx
					// Content descriptor   <text string according to encoding> $00 (00)
					//   Terminated text to be synced (typically a syllable)
					//   Sync identifier (terminator to above string)   $00 (00)
					//   Time stamp                                     $xx (xx ...)
		
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( ID3::languageLookup( $frame_data['language'], true ) == '' ) 
					{
						$error .= 'Invalid Language in ' . $frame_name . ' (' . $frame_data['language'] . ')<br>';
					} 
					else if ( ( $frame_data['timestampformat'] > 2 ) || ( $frame_data['timestampformat'] < 1 ) ) 
					{
						$error .= 'Invalid Time Stamp Format byte in ' . $frame_name . ' (' . $frame_data['timestampformat'] . ')<br>';
					} 
					else if ( !ID3::isValidSYLTtype( $frame_data['contenttypeid'], $majorversion ) )
					{
						$error .= 'Invalid Content Type byte in ' . $frame_name . ' (' . $frame_data['contenttypeid'] . ')<br>';
					} 
					else if ( !is_array( $frame_data['data'] ) ) 
					{	
						$error .= 'Invalid Lyric/Timestamp data in ' . $frame_name . ' (must be an array)<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= strtolower( $frame_data['language'] );
						$framedata .= chr( $frame_data['timestampformat'] );
						$framedata .= chr( $frame_data['contenttypeid'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
			
						ksort( $frame_data['data'] );
			
						foreach ( $frame_data['data'] as $key => $val ) 
						{
							$framedata .= $val['data'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
							$framedata .= ID3::bigEndianToString( $val['timestamp'], 4, false );
						}
					}
				
					break;
			
				case 'COMM':
					// 4.10  COMM Comments
					// Text encoding          $xx
					// Language               $xx xx xx
					// Short content descrip. <text string according to encoding> $00 (00)
					// The actual text        <full text string according to encoding>
			
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( ID3::languageLookup( $frame_data['language'], true ) == '' ) 
					{
						$error .= 'Invalid Language in ' . $frame_name . ' (' . $frame_data['language'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= strtolower( $frame_data['language'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['data'];
					}
	
					break;
	
				case 'RVA2':
					// 4.11  RVA2 Relative volume adjustment (2) (ID3v2.4+ only)
					// Identification          <text string> $00
					//   The 'identification' string is used to identify the situation and/or
					//   device where this adjustment should apply. The following is then
					//   repeated for every channel:
					// Type of channel         $xx
					// Volume adjustment       $xx xx
					// Bits representing peak  $xx
					// Peak volume             $xx (xx ...)
			
					$framedata .= str_replace( chr( 0 ), '', $frame_data['description'] ) . chr( 0 );
					
					foreach ( $frame_data as $key => $val ) 
					{
						if ( $key != 'description' ) 
						{
							$framedata .= chr( $val['channeltypeid'] );
							$framedata .= ID3::bigEndianToString( $val['volumeadjust'], 2, false, true ); // signed 16-bit
						
							if ( !ID3::isWithinBitRange( $frame_data['bitspeakvolume'], 8, false ) ) 
							{
								$framedata .= chr( $val['bitspeakvolume'] );
				
								if ( $val['bitspeakvolume'] > 0 )
									$framedata .= ID3::bigEndianToString( $val['peakvolume'], ceil( $val['bitspeakvolume'] / 8 ), false, false );
							} 
							else 
							{
								$error .= 'Invalid Bits Representing Peak Volume in ' . $frame_name . ' (' . $val['bitspeakvolume'] . ') (range = 0 to 255)<br>';
							}
						}
					}
				
					break;
		
				case 'RVAD':
					// 4.12  RVAD Relative volume adjustment (ID3v2.3 only)
					// Increment/decrement     %00fedcba
					// Bits used for volume descr.        $xx
					// Relative volume change, right      $xx xx (xx ...) // a
					// Relative volume change, left       $xx xx (xx ...) // b
					// Peak volume right                  $xx xx (xx ...)
					// Peak volume left                   $xx xx (xx ...)
					// Relative volume change, right back $xx xx (xx ...) // c
					// Relative volume change, left back  $xx xx (xx ...) // d
					// Peak volume right back             $xx xx (xx ...)
					// Peak volume left back              $xx xx (xx ...)
					// Relative volume change, center     $xx xx (xx ...) // e
					// Peak volume center                 $xx xx (xx ...)
					// Relative volume change, bass       $xx xx (xx ...) // f
					// Peak volume bass                   $xx xx (xx ...)
				
					if ( !ID3::isWithinBitRange( $frame_data['bitsvolume'], 8, false ) ) 
					{
						$error .= 'Invalid Bits For Volume Description byte in ' . $frame_name . ' (' . $frame_data['bitsvolume'] . ') (range = 1 to 255)<br>';
					} 
					else 
					{
						$incdecflag .= '00';
						$incdecflag .= ID3::boolToIntString( $frame_data['incdec']['right']     ); // a - Relative volume change, right
						$incdecflag .= ID3::boolToIntString( $frame_data['incdec']['left']      ); // b - Relative volume change, left
						$incdecflag .= ID3::boolToIntString( $frame_data['incdec']['rightrear'] ); // c - Relative volume change, right back
						$incdecflag .= ID3::boolToIntString( $frame_data['incdec']['leftrear']  ); // d - Relative volume change, left back
						$incdecflag .= ID3::boolToIntString( $frame_data['incdec']['center']    ); // e - Relative volume change, center
						$incdecflag .= ID3::boolToIntString( $frame_data['incdec']['bass']      ); // f - Relative volume change, bass
						$framedata  .= chr( bindec( $incdecflag ) );
						$framedata  .= chr( $frame_data['bitsvolume'] );
						$framedata  .= ID3::bigEndianToString( $frame_data['volumechange']['right'], ceil( $frame_data['bitsvolume'] / 8 ), false );
						$framedata  .= ID3::bigEndianToString( $frame_data['volumechange']['left'],  ceil( $frame_data['bitsvolume'] / 8 ), false );
						$framedata  .= ID3::bigEndianToString( $frame_data['peakvolume']['right'],   ceil( $frame_data['bitsvolume'] / 8 ), false );
						$framedata  .= ID3::bigEndianToString( $frame_data['peakvolume']['left'],    ceil( $frame_data['bitsvolume'] / 8 ), false );
				
						if ( $frame_data['volumechange']['rightrear'] || $frame_data['volumechange']['leftrear'] ||
					 		 $frame_data['peakvolume']['rightrear']   || $frame_data['peakvolume']['leftrear']   ||
							 $frame_data['volumechange']['center']    || $frame_data['peakvolume']['center']     ||
							 $frame_data['volumechange']['bass']      || $frame_data['peakvolume']['bass'] ) 
						{
							$framedata .= ID3::bigEndianToString( $frame_data['volumechange']['rightrear'], ceil( $frame_data['bitsvolume'] / 8 ), false );
							$framedata .= ID3::bigEndianToString( $frame_data['volumechange']['leftrear'],  ceil( $frame_data['bitsvolume'] / 8 ), false );
							$framedata .= ID3::bigEndianToString( $frame_data['peakvolume']['rightrear'],   ceil( $frame_data['bitsvolume'] / 8 ), false );
							$framedata .= ID3::bigEndianToString( $frame_data['peakvolume']['leftrear'],    ceil( $frame_data['bitsvolume'] / 8 ), false );
						}
					
						if ( $frame_data['volumechange']['center'] || $frame_data['peakvolume']['center'] || $frame_data['volumechange']['bass'] || $frame_data['peakvolume']['bass'] ) 
						{
							$framedata .= ID3::bigEndianToString( $frame_data['volumechange']['center'], ceil( $frame_data['bitsvolume'] / 8 ), false );
							$framedata .= ID3::bigEndianToString( $frame_data['peakvolume']['center'],   ceil( $frame_data['bitsvolume'] / 8 ), false );
						}
					
						if ( $frame_data['volumechange']['bass'] || $frame_data['peakvolume']['bass'] ) 
						{
							$framedata .= ID3::bigEndianToString( $frame_data['volumechange']['bass'], ceil( $frame_data['bitsvolume'] / 8 ), false );
							$framedata .= ID3::bigEndianToString( $frame_data['peakvolume']['bass'],   ceil( $frame_data['bitsvolume'] / 8 ), false );
						}
					}
				
					break;
			
				case 'EQU2':
					// 4.12  EQU2 Equalisation (2) (ID3v2.4+ only)
					// Interpolation method  $xx
					//   $00  Band
					//   $01  Linear
					// Identification        <text string> $00
					//   The following is then repeated for every adjustment point
					// Frequency          $xx xx
					// Volume adjustment  $xx xx
					
					if ( ( $frame_data['interpolationmethod'] < 0 ) || ( $frame_data['interpolationmethod'] > 1 ) )  
					{
						$error .= 'Invalid Interpolation Method byte in ' . $frame_name . ' (' . $frame_data['interpolationmethod'] . ') (valid = 0 or 1)<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['interpolationmethod'] );
						$framedata .= str_replace( chr( 0 ), '', $frame_data['description'] ) . chr( 0 );
						
						foreach ( $frame_data['data'] as $key => $val ) 
						{
							$framedata .= ID3::bigEndianToString( round( $key * 2 ), 2, false );
							$framedata .= ID3::bigEndianToString( $val, 2, false, true ); // signed 16-bit
						}
					}
		
					break;
	
				case 'EQUA':
					// 4.12  EQUA Equalisation (ID3v2.3 only)
					// Adjustment bits    $xx
					//   This is followed by 2 bytes + ('adjustment bits' rounded up to the
					//   nearest byte) for every equalisation band in the following format,
					//   giving a frequency range of 0 - 32767Hz:
					// Increment/decrement   %x (MSB of the Frequency)
					// Frequency             (lower 15 bits)
					// Adjustment            $xx (xx ...)
	
					if ( !ID3::isWithinBitRange( $frame_data['bitsvolume'], 8, false ) ) 
					{
						$error .= 'Invalid Adjustment Bits byte in ' . $frame_name . ' (' . $frame_data['bitsvolume'] . ') (range = 1 to 255)<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['adjustmentbits'] );

						foreach ( $frame_data as $key => $val ) 
						{
							if ( $key != 'bitsvolume' ) 
							{
								if ( ( $key > 32767 ) || ( $key < 0 ) ) 
								{
									$error .= 'Invalid Frequency in ' . $frame_name . ' (' . $key . ') (range = 0 to 32767)<br>';
								} 
								else 
								{
									if ( $val >= 0 ) 
									{
										// put MSB of frequency to 1 if increment, 0 if decrement
										$key |= 0x8000;
									}
					
									$framedata .= ID3::bigEndianToString( $key, 2, false );
									$framedata .= ID3::bigEndianToString( $val, ceil( $frame_data['adjustmentbits'] / 8 ), false );
								}
							}
						}
					}
				
					break;
			
				case 'RVRB':
					// 4.13  RVRB Reverb
					// Reverb left (ms)                 $xx xx
					// Reverb right (ms)                $xx xx
					// Reverb bounces, left             $xx
					// Reverb bounces, right            $xx
					// Reverb feedback, left to left    $xx
					// Reverb feedback, left to right   $xx
					// Reverb feedback, right to right  $xx
					// Reverb feedback, right to left   $xx
					// Premix left to right             $xx
					// Premix right to left             $xx

					if ( !ID3::isWithinBitRange( $frame_data['left'], 16, false ) ) 
					{
						$error .= 'Invalid Reverb Left in ' . $frame_name . ' (' . $frame_data['left'] . ') (range = 0 to 65535)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['right'], 16, false ) ) 
					{
						$error .= 'Invalid Reverb Left in ' . $frame_name . ' (' . $frame_data['right'] . ') (range = 0 to 65535)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['bouncesL'], 8, false ) ) 
					{
						$error .= 'Invalid Reverb Bounces, Left in ' . $frame_name . ' (' . $frame_data['bouncesL'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['bouncesR'], 8, false ) ) 
					{
						$error .= 'Invalid Reverb Bounces, Right in ' . $frame_name . ' (' . $frame_data['bouncesR'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['feedbackLL'], 8, false ) ) 
					{
						$error .= 'Invalid Reverb Feedback, Left-To-Left in ' . $frame_name . ' (' . $frame_data['feedbackLL'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['feedbackLR'], 8, false ) ) 
					{
						$error .= 'Invalid Reverb Feedback, Left-To-Right in ' . $frame_name . ' (' . $frame_data['feedbackLR'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['feedbackRR'], 8, false ) ) 
					{
						$error .= 'Invalid Reverb Feedback, Right-To-Right in ' . $frame_name . ' (' . $frame_data['feedbackRR'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['feedbackRL'], 8, false ) ) 
					{
						$error .= 'Invalid Reverb Feedback, Right-To-Left in ' . $frame_name . ' (' . $frame_data['feedbackRL'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['premixLR'], 8, false ) ) 
					{
						$error .= 'Invalid Premix, Left-To-Right in ' . $frame_name . ' (' . $frame_data['premixLR'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['premixRL'], 8, false ) ) 
					{
						$error .= 'Invalid Premix, Right-To-Left in ' . $frame_name . ' (' . $frame_data['premixRL'] . ') (range = 0 to 255)<br>';
					} 
					else 
					{
						$framedata .= ID3::bigEndianToString( $frame_data['left'],  2, false );
						$framedata .= ID3::bigEndianToString( $frame_data['right'], 2, false );
						
						$framedata .= chr( $frame_data['bouncesL']   );
						$framedata .= chr( $frame_data['bouncesR']   );
						$framedata .= chr( $frame_data['feedbackLL'] );
						$framedata .= chr( $frame_data['feedbackLR'] );
						$framedata .= chr( $frame_data['feedbackRR'] );
						$framedata .= chr( $frame_data['feedbackRL'] );
						$framedata .= chr( $frame_data['premixLR']   );
						$framedata .= chr( $frame_data['premixRL']   );
					}
	
					break;
	
				case 'APIC':
					// 4.14  APIC Attached picture
					// Text encoding      $xx
					// MIME type          <text string> $00
					// Picture type       $xx
					// Description        <text string according to encoding> $00 (00)
					// Picture data       <binary data>
		
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( !ID3::isValidAPICpicturetype( $frame_data['picturetypeid'], $majorversion ) ) 
					{
						$error .= 'Invalid Picture Type byte in ' . $frame_name . ' (' . $frame_data['picturetypeid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( ( $majorversion >= 3 ) && ( !ID3::isValidAPICimageformat( $frame_data['mime'], $majorversion ) ) ) 
					{
						$error .= 'Invalid MIME Type in ' . $frame_name . ' (' . $frame_data['mime'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( ( $frame_data['mime'] == '-->' ) && ( !ID3::isValidURL( $frame_data['data'], false, false ) ) ) 
					{
						$error .= 'Invalid URL in ' . $frame_name . ' (' . $frame_data['data'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= str_replace( chr( 0 ), '', $frame_data['mime'] ) . chr( 0 );
						$framedata .= chr( $frame_data['picturetypeid'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['data'];
					}
	
					break;
		
				case 'GEOB':
					// 4.15  GEOB General encapsulated object
					// Text encoding          $xx
					// MIME type              <text string> $00
					// Filename               <text string according to encoding> $00 (00)
					// Content description    <text string according to encoding> $00 (00)
					// Encapsulated object    <binary data>
			
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' .$frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
					} 
					else if ( !ID3::isValidMIMEstring( $frame_data['mime'] ) ) 
					{
						$error .= 'Invalid MIME Type in ' . $frame_name . ' (' . $frame_data['mime'] . ')<br>';
					} 
					else if ( !$frame_data['description'] ) 
					{
						$error .= 'Missing Description in ' . $frame_name . '<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= str_replace( chr( 0 ), '', $frame_data['mime'] ) . chr( 0 );
						$framedata .= $frame_data['filename']    . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['data'];
					}
				
					break;
			
				case 'PCNT':
					// 4.16  PCNT Play counter
					// When the counter reaches all one's, one byte is inserted in
					// front of the counter thus making the counter eight bits bigger
					// Counter        $xx xx xx xx (xx ...)
					$framedata .= ID3::bigEndianToString( $frame_data['data'], 4, false );
					break;
	
				case 'POPM':
					// 4.17  POPM Popularimeter
					// When the counter reaches all one's, one byte is inserted in
					// front of the counter thus making the counter eight bits bigger
					// Email to user   <text string> $00
					// Rating          $xx
					// Counter         $xx xx xx xx (xx ...)
					
					if ( !ID3::isWithinBitRange( $frame_data['rating'], 8, false ) ) 
					{
						$error .= 'Invalid Rating byte in ' . $frame_name . ' (' . $frame_data['rating'] . ') (range = 0 to 255)<br>';
					} 
					else if ( !Validation::is_email( $frame_data['email'] ) ) 
					{
						$error .= 'Invalid Email in ' . $frame_name . ' (' . $frame_data['email'] . ')<br>';
					} 
					else 
					{
						$framedata .= str_replace( chr( 0 ), '', $frame_data['email'] ) . chr( 0 );
						$framedata .= chr( $frame_data['rating'] );
						$framedata .= ID3::bigEndianToString( $frame_data['data'], 4, false );
					}
		
					break;
						
				case 'RBUF':
					// 4.18  RBUF Recommended buffer size
					// Buffer size               $xx xx xx
					// Embedded info flag        %0000000x
					// Offset to next tag        $xx xx xx xx
				
					if ( !ID3::isWithinBitRange( $frame_data['buffersize'], 24, false ) ) 
					{
						$error .= 'Invalid Buffer Size in ' . $frame_name . '<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['nexttagoffset'], 32, false ) ) 
					{
						$error .= 'Invalid Offset To Next Tag in ' . $frame_name . '<br>';
					} 
					else 
					{
						$framedata .= ID3::bigEndianToString( $frame_data['buffersize'], 3, false );
						$flag      .= '0000000';
						$flag      .= ID3::boolToIntString( $frame_data['flags']['embededinfo'] );
						$framedata .= chr( bindec( $flag ) );
						$framedata .= ID3::bigEndianToString( $frame_data['nexttagoffset'], 4, false );
					}
				
					break;
			
				case 'AENC':
					// 4.19  AENC Audio encryption
					// Owner identifier   <text string> $00
					// Preview start      $xx xx
					// Preview length     $xx xx
					// Encryption info    <binary data>
			
					if ( !ID3::isWithinBitRange( $frame_data['previewstart'], 16, false ) ) 
					{
						$error .= 'Invalid Preview Start in ' . $frame_name . ' (' . $frame_data['previewstart'] . ')<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['previewlength'], 16, false ) ) 
					{
						$error .= 'Invalid Preview Length in ' . $frame_name . ' (' . $frame_data['previewlength'] . ')<br>';
					} 
					else 
					{
						$framedata .= str_replace( chr( 0 ), '', $frame_data['ownerid'] ) . chr( 0 );
						$framedata .= ID3::bigEndianToString( $frame_data['previewstart'],  2, false );
						$framedata .= ID3::bigEndianToString( $frame_data['previewlength'], 2, false );
						$framedata .= $frame_data['encryptioninfo'];
					}
		
					break;
		
				case 'LINK':
					// 4.20  LINK Linked information
					// Frame identifier               $xx xx xx xx
					// URL                            <text string> $00
					// ID and additional data         <text string(s)>
		
					if ( !ID3::isValidID3v2FrameName( $frame_data['frameid'], $majorversion ) ) 
					{
						$error .= 'Invalid Frame Identifier in ' . $frame_name . ' (' . $frame_data['frameid'] . ')<br>';
					} 
					else if ( !ID3::isValidURL( $frame_data['url'], true, false ) ) 
					{
						$error .= 'Invalid URL in ' . $frame_name . ' (' . $frame_data['url'] . ')<br>';
					} 
					else if ( ( ( $frame_data['frameid'] == 'AENC' ) || ( $frame_data['frameid'] == 'APIC' ) || ( $frame_data['frameid'] == 'GEOB' ) || ( $frame_data['frameid'] == 'TXXX' )) && ( $frame_data['additionaldata'] == '' ) ) 
					{
						$error .= 'Content Descriptor must be specified as additional data for Frame Identifier of ' . $frame_data['frameid'] . ' in ' . $frame_name . '<br>';
					} 
					else if ( ( $frame_data['frameid'] == 'USER') && ( ID3::languageLookup( $frame_data['additionaldata'], true ) == '' ) ) 
					{
						$error .= 'Language must be specified as additional data for Frame Identifier of ' . $frame_data['frameid'] . ' in ' . $frame_name . '<br>';
					} 
					else if ( ( $frame_data['frameid'] == 'PRIV') && ( $frame_data['additionaldata'] == '' ) ) 
					{
						$error .= 'Owner Identifier must be specified as additional data for Frame Identifier of ' . $frame_data['frameid'] . ' in ' . $frame_name . '<br>';
					} 
					else if ( ( ($frame_data['frameid'] == 'COMM' ) || ( $frame_data['frameid'] == 'SYLT' ) || ( $frame_data['frameid'] == 'USLT' ) ) && ( ( ID3::languageLookup( substr( $frame_data['additionaldata'], 0, 3 ), true ) == '' ) || ( substr( $frame_data['additionaldata'], 3 ) == '' ) ) ) 
					{
						$error .= 'Language followed by Content Descriptor must be specified as additional data for Frame Identifier of ' . $frame_data['frameid'] . ' in ' . $frame_name . '<br>';
					} 
					else 
					{
						$framedata .= $frame_data['frameid'];
						$framedata .= str_replace( chr( 0 ), '', $frame_data['url'] ) . chr( 0 );
	
						switch ( $frame_data['frameid'] ) 
						{
							case 'COMM':
				
							case 'SYLT':
				
							case 'USLT':
				
							case 'PRIV':
				
							case 'USER':
				
							case 'AENC':
				
							case 'APIC':
				
							case 'GEOB':
						
							case 'TXXX':
								$framedata .= $frame_data['additionaldata'];
								break;
						
							case 'ASPI':

							case 'ETCO':

							case 'EQU2':

							case 'MCID':

							case 'MLLT':

							case 'OWNE':

							case 'RVA2':

							case 'RVRB':

							case 'SYTC':
	
							case 'IPLS':
		
							case 'RVAD':
			
							case 'EQUA':	
								// no additional data required
								break;

							case 'RBUF':
								if ( $majorversion == 3 ) 
								{
									// no additional data required
								} 
								else 
								{
									$error .= $frame_data['frameid'] . ' is not a valid Frame Identifier in ' . $frame_name . ' (in ID3v2.' . $majorversion . ')<br>';
								}

							default:
								if ( ( substr( $frame_data['frameid'], 0, 1 ) == 'T' ) || ( substr( $frame_data['frameid'], 0, 1 ) == 'W' ) ) 
								{
									// no additional data required
								} 
								else 
								{
									$error .= $frame_data['frameid'].' is not a valid Frame Identifier in ' . $frame_name . ' (in ID3v2.' . $majorversion . ')<br>';
								}
							
								break;
						}
					}
			
					break;
			
				case 'POSS':
					// 4.21  POSS Position synchronisation frame (ID3v2.3+ only)
					// Time stamp format         $xx
					// Position                  $xx (xx ...)
				
					if ( ( $frame_data['timestampformat'] < 1 ) || ( $frame_data['timestampformat'] > 2 ) )
					{
						$error .= 'Invalid Time Stamp Format in '.$frame_name.' ('.$frame_data['timestampformat'].') (valid = 1 or 2)<br>';
					}
					else if ( !ID3::isWithinBitRange( $frame_data['position'], 32, false ) ) 
					{
						$error .= 'Invalid Position in ' . $frame_name . ' (' . $frame_data['position'] . ') (range = 0 to 4294967295)<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['timestampformat'] );
						$framedata .= ID3::bigEndianToString( $frame_data['position'], 4, false );
					}
		
					break;
	
				case 'USER':
					// 4.22  USER Terms of use (ID3v2.3+ only)
					// Text encoding        $xx
					// Language             $xx xx xx
					// The actual text      <text string according to encoding>
	
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ')<br>';
					} 
					else if ( ID3::languageLookup( $frame_data['language'], true ) == '' ) 
					{
						$error .= 'Invalid Language in ' . $frame_name . ' (' . $frame_data['language'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= strtolower( $frame_data['language'] );
						$framedata .= $frame_data['data'];
					}
				
					break;
			
				case 'OWNE':
					// 4.23  OWNE Ownership frame (ID3v2.3+ only)
					// Text encoding     $xx
					// Price paid        <text string> $00
					// Date of purch.    <text string>
					// Seller            <text string according to encoding>
				
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ')<br>';
					} 
					else if ( !ID3::isANumber( $frame_data['pricepaid']['value'], false ) ) 
					{
						$error .= 'Invalid Price Paid in ' . $frame_name . ' (' . $frame_data['pricepaid']['value'] . ')<br>';
					} 
					else if ( !ID3::isValidDateStampString( $frame_data['purchasedate'] ) ) 
					{
						$error .= 'Invalid Date Of Purchase in ' . $frame_name . ' (' . $frame_data['purchasedate'] . ') (format = YYYYMMDD)<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						$framedata .= str_replace( chr( 0 ), '', $frame_data['pricepaid']['value'] ) . chr( 0 );
						$framedata .= $frame_data['purchasedate'];
						$framedata .= $frame_data['seller'];
					}
				
					break;
			
				case 'COMR':
					// 4.24  COMR Commercial frame (ID3v2.3+ only)
					// Text encoding      $xx
					// Price string       <text string> $00
					// Valid until        <text string>
					// Contact URL        <text string> $00
					// Received as        $xx
					// Name of seller     <text string according to encoding> $00 (00)
					// Description        <text string according to encoding> $00 (00)
					// Picture MIME type  <string> $00
					// Seller logo        <binary data>
			
					if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
					{
						$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ')<br>';
					} 
					else if ( !ID3::isValidDateStampString( $frame_data['pricevaliduntil'] ) ) 
					{
						$error .= 'Invalid Valid Until date in ' . $frame_name . ' (' . $frame_data['pricevaliduntil'] . ') (format = YYYYMMDD)<br>';
					} 
					else if ( !ID3::isValidURL( $frame_data['contacturl'], false, true ) ) 
					{
						$error .= 'Invalid Contact URL in ' . $frame_name . ' (' . $frame_data['contacturl'] . ') (allowed schemes: http, https, ftp, mailto)<br>';
					} 
					else if ( !ID3::isValidCOMRreceivedas( $frame_data['receivedasid'], $majorversion ) ) 
					{
						$error .= 'Invalid Received As byte in ' . $frame_name . ' (' . $frame_data['contacturl'] . ') (range = 0 to 8)<br>';
					} 
					else if ( !ID3::isValidMIMEstring( $frame_data['mime'] ) ) 
					{
						$error .= 'Invalid MIME Type in ' . $frame_name . ' (' . $frame_data['mime'] . ')<br>';
					} 
					else 
					{
						$framedata .= chr( $frame_data['encodingid'] );
						unset( $pricestring );
					
						foreach ( $frame_data['price'] as $key => $val ) 
						{
							if ( ID3::isValidPriceString( $key . $val['value'] ) )
								$pricestrings[] = $key . $val['value'];
							else
								$error .= 'Invalid Price String in ' . $frame_name . ' (' . $key.$val['value'] . ')<br>';
						}
						
						$framedata .= implode( '/', $pricestrings );
						$framedata .= $frame_data['pricevaliduntil'];
						$framedata .= str_replace( chr( 0 ), '', $frame_data['contacturl'] ) . chr( 0 );
						$framedata .= chr( $frame_data['receivedasid'] );
						$framedata .= $frame_data['sellername']  . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['description'] . ID3::textEncodingLookup( 'terminator', $frame_data['encodingid'] );
						$framedata .= $frame_data['mime'] . chr( 0 );
						$framedata .= $frame_data['logo'];
					}
				
					break;
	
				case 'ENCR':
					// 4.25  ENCR Encryption method registration (ID3v2.3+ only)
					// Owner identifier    <text string> $00
					// Method symbol       $xx
					// Encryption data     <binary data>
			
					if ( !ID3::isWithinBitRange( $frame_data['methodsymbol'], 8, false ) ) 
					{
						$error .= 'Invalid Group Symbol in ' . $frame_name . ' (' . $frame_data['methodsymbol'] . ') (range = 0 to 255)<br>';
					} 
					else 
					{
						$framedata .= str_replace( chr( 0 ), '', $frame_data['ownerid'] ) . chr( 0 );
						$framedata .= ord( $frame_data['methodsymbol'] );
						$framedata .= $frame_data['data'];
					}
			
					break;
			
				case 'GRID':
					// 4.26  GRID Group identification registration (ID3v2.3+ only)
					// Owner identifier      <text string> $00
					// Group symbol          $xx
					// Group dependent data  <binary data>
			
					if ( !ID3::isWithinBitRange( $frame_data['groupsymbol'], 8, false ) ) 
					{
						$error .= 'Invalid Group Symbol in ' . $frame_name . ' (' . $frame_data['groupsymbol'] . ') (range = 0 to 255)<br>';
					} 
					else 
					{
						$framedata .= str_replace( chr( 0 ), '', $frame_data['ownerid'] ) . chr( 0 );
						$framedata .= ord( $frame_data['groupsymbol'] );
						$framedata .= $frame_data['data'];
					}
				
					break;
			
				case 'PRIV':
					// 4.27  PRIV Private frame (ID3v2.3+ only)
					// Owner identifier      <text string> $00
					// The private data      <binary data>
			
					$framedata .= str_replace( chr( 0 ), '', $frame_data['ownerid'] ) . chr( 0 );
					$framedata .= $frame_data['data'];
					break;
					
				case 'SIGN':
					// 4.28  SIGN Signature frame (ID3v2.4+ only)
					// Group symbol      $xx
					// Signature         <binary data>
		
					if ( !ID3::isWithinBitRange( $frame_data['groupsymbol'], 8, false ) ) 
					{
						$error .= 'Invalid Group Symbol in ' . $frame_name . ' (' . $frame_data['groupsymbol'] . ') (range = 0 to 255)<br>';
					} 
					else 
					{
						$framedata .= ord( $frame_data['groupsymbol'] );
						$framedata .= $frame_data['data'];
					}
		
					break;

				case 'SEEK':
					// 4.29  SEEK Seek frame (ID3v2.4+ only)
					// Minimum offset to next tag       $xx xx xx xx
					if ( !ID3::isWithinBitRange( $frame_data['data'], 32, false ) )
						$error .= 'Invalid Minimum Offset in ' . $frame_name . ' (' . $frame_data['data'] . ') (range = 0 to 4294967295)<br>';
					else
						$framedata .= ID3::bigEndianToString( $frame_data['data'], 4, false );
				
					break;
					
				case 'ASPI':
					// 4.30  ASPI Audio seek point index (ID3v2.4+ only)
					// Indexed data start (S)         $xx xx xx xx
					// Indexed data length (L)        $xx xx xx xx
					// Number of index points (N)     $xx xx
					// Bits per index point (b)       $xx
					// Then for every index point the following data is included:
					// Fraction at index (Fi)          $xx (xx)
				
					if ( !ID3::isWithinBitRange( $frame_data['datastart'], 32, false ) ) 
					{
						$error .= 'Invalid Indexed Data Start in ' . $frame_name . ' (' . $frame_data['datastart'] . ') (range = 0 to 4294967295)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['datalength'], 32, false ) ) 
					{
						$error .= 'Invalid Indexed Data Length in ' . $frame_name . ' (' . $frame_data['datalength'] . ') (range = 0 to 4294967295)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['indexpoints'], 16, false ) ) 
					{
						$error .= 'Invalid Number Of Index Points in ' . $frame_name . ' (' . $frame_data['indexpoints'] . ') (range = 0 to 65535)<br>';
					} 
					else if ( !ID3::isWithinBitRange( $frame_data['bitsperpoint'], 8, false ) ) 
					{
						$error .= 'Invalid Bits Per Index Point in ' . $frame_name . ' (' . $frame_data['bitsperpoint'] . ') (range = 0 to 255)<br>';
					} 
					else if ( $frame_data['indexpoints'] != count( $frame_data['indexes'] ) ) 
					{
						$error .= 'Number Of Index Points does not match actual supplied data in ' . $frame_name . '<br>';
					} 
					else 
					{
						$framedata .= ID3::bigEndianToString( $frame_data['datastart'],    4, false );
						$framedata .= ID3::bigEndianToString( $frame_data['datalength'],   4, false );
						$framedata .= ID3::bigEndianToString( $frame_data['indexpoints'],  2, false );
						$framedata .= ID3::bigEndianToString( $frame_data['bitsperpoint'], 1, false );
						
						foreach ( $frame_data['indexes'] as $key => $val )
							$framedata .= ID3::bigEndianToString( $val, ceil( $frame_data['bitsperpoint'] / 8 ), false );
					}
				
					break;
			
				case 'RGAD':
					// RGAD Replay Gain Adjustment
					// http://privatewww.essex.ac.uk/~djmrob/replaygain/
					// Peak Amplitude                     $xx $xx $xx $xx
					// Radio Replay Gain Adjustment        %aaabbbcd %dddddddd
					// Audiophile Replay Gain Adjustment   %aaabbbcd %dddddddd
					//   a - name code
					//   b - originator code
					//   c - sign bit
					//   d - replay gain adjustment

					if ( ( $frame_data['radio_adjustment'] > 51 ) || ( $frame_data['radio_adjustment'] < -51 ) ) 
					{
						$error .= 'Invalid Radio Adjustment in ' . $frame_name . ' (' . $frame_data['radio_adjustment'] . ') (range = -51.0 to +51.0)<br>';
					} 
					else if ( ( $frame_data['audiophile_adjustment'] > 51 ) || ( $frame_data['audiophile_adjustment'] < -51 ) ) 
					{
						$error .= 'Invalid Audiophile Adjustment in ' . $frame_name . ' (' . $frame_data['audiophile_adjustment'] . ') (range = -51.0 to +51.0)<br>';
					} 
					else if ( !ID3::isValidRGADname( $frame_data['raw']['radio_name'], $majorversion ) ) 
					{
						$error .= 'Invalid Radio Name Code in ' . $frame_name . ' (' . $frame_data['raw']['radio_name'] . ') (range = 0 to 2)<br>';
					} 
					else if ( !ID3::isValidRGADname( $frame_data['raw']['audiophile_name'], $majorversion ) ) 
					{
						$error .= 'Invalid Audiophile Name Code in ' . $frame_name . ' (' . $frame_data['raw']['audiophile_name'] . ') (range = 0 to 2)<br>';
					} 
					else if ( !ID3::isValidRGADoriginator( $frame_data['raw']['radio_originator'], $majorversion ) ) 
					{
						$error .= 'Invalid Radio Originator Code in ' . $frame_name . ' (' . $frame_data['raw']['radio_originator'] . ') (range = 0 to 3)<br>';
					} 
					else if ( !ID3::isValidRGADoriginator( $frame_data['raw']['audiophile_originator'], $majorversion ) ) 
					{
						$error .= 'Invalid Audiophile Originator Code in ' . $frame_name . ' (' . $frame_data['raw']['audiophile_originator'] . ') (range = 0 to 3)<br>';
					} 
					else
					{
						$framedata .= ID3::floatToString( $frame_data['peakamplitude'], 32 );
						
						$framedata .= ID3::RGADgainString( $frame_data['raw']['radio_name'],      $frame_data['raw']['radio_originator'],      $frame_data['radio_adjustment']      );
						$framedata .= ID3::RGADgainString( $frame_data['raw']['audiophile_name'], $frame_data['raw']['audiophile_originator'], $frame_data['audiophile_adjustment'] );
					}
				
					break;
					
				default:
					if ( $frame_name{0} == 'T' ) 
					{
						// 4.2. T???  Text information frames
						// Text encoding                $xx
						// Information                  <text string(s) according to encoding>
					
						if ( !ID3::isValidTextEncoding( $frame_data['encodingid'], $majorversion ) ) 
						{
							$error .= 'Invalid Text Encoding in ' . $frame_name . ' (' . $frame_data['encodingid'] . ') for ID3v2.' . $majorversion . '<br>';
						} 
						else 
						{
							$framedata .= chr( $frame_data['encodingid'] );
							$framedata .= $frame_data['data'];
						}
					} 
					else if ( $frame_name{0} == 'W' ) 
					{
						// 4.3. W???  URL link frames
						// URL              <text string>
					
						if ( !ID3::isValidURL( $frame_data['url'], false, false ) )
							$error .= 'Invalid URL in ' . $frame_name . ' (' . $frame_data['url'] . ')<br>';
						else
							$framedata .= $frame_data['url'];
					} 
					else 
					{
						$error .= $frame_name . ' not yet supported in putid3.php<br>';
					}
				
					break;
			}
		}
	
		if ( $error ) 
		{
			if ( $showerrors )
				echo $error;
		
			return false;
		} 
		else 
		{
			return $framedata;
		}
	}
	
	function ID3v2FrameIsAllowed( $frame_name, $frame_data, $majorversion, $showerrors = false ) 
	{
		static $PreviousFrames = array();
		$error = '';

		if ( $frame_name === null ) 
		{
			// if the writing functions are called multiple times, the static array needs to be
			// cleared - this can be done by calling ID3::ID3v2FrameIsAllowed(null, '', '')
			$PreviousFrames = array();
			return true;
		}

		if ( $majorversion == 4 ) 
		{
			switch ( $frame_name ) 
			{
				case 'UFID':
		
				case 'AENC':
	
				case 'ENCR':
	
				case 'GRID':
					if ( !isset( $frame_data['ownerid'] ) ) 
						$error .= '[ownerid] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['ownerid'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same OwnerID (' . $frame_data['ownerid'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name.$frame_data['ownerid'];
				
					break;
			
				case 'TXXX':
			
				case 'WXXX':
			
				case 'RVA2':
			
				case 'EQU2':
			
				case 'APIC':
			
				case 'GEOB':
					if ( !isset( $frame_data['description'] ) ) 
						$error .= '[description] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name.$frame_data['description'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Description (' . $frame_data['description'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['description'];
				
					break;
			
				case 'USER':
					if ( !isset( $frame_data['language'] ) ) 
						$error .= '[language] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name.$frame_data['language'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Language (' . $frame_data['language'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['language'];
				
					break;
			
				case 'USLT':
			
				case 'SYLT':
			
				case 'COMM':
					if ( !isset( $frame_data['language'] ) )
						$error .= '[language] not specified for ' . $frame_name . '<br>';
					else if ( !isset( $frame_data['description'] ) )
						$error .= '[description] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['language'] . $frame_data['description'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Language + Description (' . $frame_data['language'] . ' + ' . $frame_data['description'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['language'] . $frame_data['description'];
				
					break;
			
				case 'POPM':
					if ( !isset( $frame_data['email'] ) )
						$error .= '[email] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['email'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Email (' . $frame_data['email'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['email'];
				
					break;
			
				case 'IPLS':
			
				case 'MCDI':
			
				case 'ETCO':
			
				case 'MLLT':
			
				case 'SYTC':
			
				case 'RVRB':
			
				case 'PCNT':
			
				case 'RBUF':
			
				case 'POSS':
			
				case 'OWNE':
			
				case 'SEEK':
			
				case 'ASPI':
			
				case 'RGAD':
					if ( in_array( $frame_name, $PreviousFrames ) ) 
						$error .= 'Only one ' . $frame_name . ' tag allowed<br>';
					else
						$PreviousFrames[] = $frame_name;
				
					break;
			
				case 'LINK':
					// this isn't implemented quite right (yet) - it should check the target frame data for compliance
					// but right now it just allows one linked frame of each type, to be safe.
					if ( !isset( $frame_data['frameid'] ) ) 
					{
						$error .= '[frameid] not specified for ' . $frame_name . '<br>';
					} 
					else if ( in_array( $frame_name . $frame_data['frameid'], $PreviousFrames ) ) 
					{
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same FrameID (' . $frame_data['frameid'] . ')<br>';
					} 
					else if ( in_array( $frame_data['frameid'], $PreviousFrames ) ) 
					{
						// no links to singleton tags
						$error .= 'Cannot specify a ' . $frame_name . ' tag to a singleton tag that already exists (' . $frame_data['frameid'] . ')<br>';
					} 
					else 
					{
						$PreviousFrames[] = $frame_name . $frame_data['frameid']; // only one linked tag of this type
						$PreviousFrames[] = $frame_data['frameid'];               // no non-linked singleton tags of this type
					}
				
					break;
			
				case 'COMR':
					// There may be more than one 'commercial frame' in a tag, but no two may be identical
					// Checking isn't implemented at all (yet) - just assumes that it's OK.
					break;
			
				case 'PRIV':
			
				case 'SIGN':
					if ( !isset( $frame_data['ownerid'] ) ) 
						$error .= '[ownerid] not specified for ' . $frame_name . '<br>';
					else if ( !isset( $frame_data['data'] ) )
						$error .= '[data] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['ownerid'] . $frame_data['data'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same OwnerID + Data (' . $frame_data['ownerid'] . ' + ' . $frame_data['data'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['ownerid'] . $frame_data['data'];
				
					break;
			
				default:
					if ( ( $frame_name{0} != 'T' ) && ( $frame_name{0} != 'W' ) ) 
						$error .= 'Frame not allowed in ID3v2.' . $majorversion . ': ' . $frame_name . '<br>';
				
					break;
			}
		} 
		else if ( $majorversion == 3 ) 
		{
			switch ( $frame_name ) 
			{
				case 'UFID':
			
				case 'AENC':
			
				case 'ENCR':
			
				case 'GRID':
					if ( !isset( $frame_data['ownerid'] ) )
						$error .= '[ownerid] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['ownerid'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same OwnerID (' . $frame_data['ownerid'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['ownerid'];
				
					break;
			
				case 'TXXX':
			
				case 'WXXX':
			
				case 'APIC':
			
				case 'GEOB':
					if ( !isset( $frame_data['description'] ) )
						$error .= '[description] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['description'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Description (' . $frame_data['description'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['description'];
				
					break;
			
				case 'USER':
					if ( !isset( $frame_data['language'] ) )
						$error .= '[language] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['language'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Language (' . $frame_data['language'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['language'];
				
					break;
			
				case 'USLT':
			
				case 'SYLT':
			
				case 'COMM':
					if ( !isset( $frame_data['language'] ) )
						$error .= '[language] not specified for ' . $frame_name . '<br>';
					else if ( !isset( $frame_data['description'] ) )
						$error .= '[description] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['language'] . $frame_data['description'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Language + Description (' . $frame_data['language'] . ' + ' . $frame_data['description'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['language'] . $frame_data['description'];
				
					break;
			
				case 'POPM':
					if ( !isset( $frame_data['email'] ) )
						$error .= '[email] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['email'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Email (' . $frame_data['email'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['email'];
			
					break;
			
				case 'IPLS':
			
				case 'MCDI':
			
				case 'ETCO':
			
				case 'MLLT':
			
				case 'SYTC':
			
				case 'RVAD':
			
				case 'EQUA':
			
				case 'RVRB':
			
				case 'PCNT':
			
				case 'RBUF':
			
				case 'POSS':
			
				case 'OWNE':
			
				case 'RGAD':
					if ( in_array( $frame_name, $PreviousFrames ) ) 
						$error .= 'Only one ' . $frame_name . ' tag allowed<br>';
					else
						$PreviousFrames[] = $frame_name;
				
					break;
			
				case 'LINK':
					// this isn't implemented quite right (yet) - it should check the target frame data for compliance
					// but right now it just allows one linked frame of each type, to be safe.
					if ( !isset( $frame_data['frameid'] ) ) 
					{
						$error .= '[frameid] not specified for ' . $frame_name . '<br>';
					} 
					else if ( in_array( $frame_name . $frame_data['frameid'], $PreviousFrames ) ) 
					{
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same FrameID (' . $frame_data['frameid'] . ')<br>';
					} 
					else if ( in_array( $frame_data['frameid'], $PreviousFrames ) ) 
					{
						// no links to singleton tags
						$error .= 'Cannot specify a ' . $frame_name . ' tag to a singleton tag that already exists (' . $frame_data['frameid'] . ')<br>';
					} 
					else 
					{
						$PreviousFrames[] = $frame_name . $frame_data['frameid']; // only one linked tag of this type
						$PreviousFrames[] = $frame_data['frameid'];              // no non-linked singleton tags of this type
					}
				
					break;
			
				case 'COMR':
					// There may be more than one 'commercial frame' in a tag, but no two may be identical
					// Checking isn't implemented at all (yet) - just assumes that it's OK.
					break;
			
				case 'PRIV':
					if ( !isset( $frame_data['ownerid'] ) )
						$error .= '[ownerid] not specified for ' . $frame_name . '<br>';
					else if ( !isset($frame_data['data'] ) )
						$error .= '[data] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['ownerid'] . $frame_data['data'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same OwnerID + Data (' . $frame_data['ownerid'] . ' + ' . $frame_data['data'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['ownerid'] . $frame_data['data'];
				
					break;

				default:
					if ( ( $frame_name{0} != 'T' ) && ( $frame_name{0} != 'W' ) )
						$error .= 'Frame not allowed in ID3v2.' . $majorversion . ': ' . $frame_name . '<br>';
				
					break;
			}
		} 
		else if ( $majorversion == 2 ) 
		{
			switch ( $frame_name ) 
			{
				case 'UFI':
		
				case 'CRM':
	
				case 'CRA':
					if ( !isset( $frame_data['ownerid'] ) )
						$error .= '[ownerid] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['ownerid'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same OwnerID (' . $frame_data['ownerid'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['ownerid'];
				
					break;
			
				case 'TXX':
			
				case 'WXX':
			
				case 'PIC':
			
				case 'GEO':
					if ( !isset( $frame_data['description'] ) )
						$error .= '[description] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['description'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Description (' . $frame_data['description'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['description'];
				
					break;
			
				case 'ULT':
			
				case 'SLT':
			
				case 'COM':
					if ( !isset( $frame_data['language'] ) )
						$error .= '[language] not specified for ' . $frame_name . '<br>';
					else if ( !isset( $frame_data['description'] ) )
						$error .= '[description] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['language'] . $frame_data['description'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Language + Description (' . $frame_data['language'] . ' + ' . $frame_data['description'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['language'] . $frame_data['description'];
				
					break;
			
				case 'POP':
					if ( !isset( $frame_data['email'] ) )
						$error .= '[email] not specified for ' . $frame_name . '<br>';
					else if ( in_array( $frame_name . $frame_data['email'], $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same Email (' . $frame_data['email'] . ')<br>';
					else
						$PreviousFrames[] = $frame_name . $frame_data['email'];
				
					break;
			
				case 'IPL':
			
				case 'MCI':
			
				case 'ETC':
			
				case 'MLL':
			
				case 'STC':
			
				case 'RVA':
			
				case 'EQU':
			
				case 'REV':
			
				case 'CNT':
				
				case 'BUF':
					if ( in_array( $frame_name, $PreviousFrames ) )
						$error .= 'Only one ' . $frame_name . ' tag allowed<br>';
					else
						$PreviousFrames[] = $frame_name;
				
					break;
			
				case 'LNK':
					// this isn't implemented quite right (yet) - it should check the target frame data for compliance
					// but right now it just allows one linked frame of each type, to be safe.
					if ( !isset( $frame_data['frameid'] ) )
					{
						$error .= '[frameid] not specified for ' . $frame_name . '<br>';
					}
					else if ( in_array( $frame_name . $frame_data['frameid'], $PreviousFrames ) )
					{
						$error .= 'Only one ' . $frame_name . ' tag allowed with the same FrameID (' . $frame_data['frameid'] . ')<br>';
					}
					else if ( in_array( $frame_data['frameid'], $PreviousFrames ) ) 
					{
						// no links to singleton tags
						$error .= 'Cannot specify a ' . $frame_name . ' tag to a singleton tag that already exists (' . $frame_data['frameid'] . ')<br>';
					} 
					else 
					{
						$PreviousFrames[] = $frame_name . $frame_data['frameid']; // only one linked tag of this type
						$PreviousFrames[] = $frame_data['frameid'];               // no non-linked singleton tags of this type
					}
				
					break;
			
				default:
					if ( ( $frame_name{0} != 'T' ) && ( $frame_name{0} != 'W' ) ) 
						$error .= 'Frame not allowed in ID3v2.' . $majorversion . ': ' . $frame_name . '<br>';
				
					break;
			}
		}

		if ( $error ) 
		{
			if ( $showerrors )
				echo $error;
		
			return false;
		} 
		else 
		{
			return true;
		}
	}

	function generateID3v2Tag( $data, $majorversion = 4, $minorversion = 0, $paddedlength = 0, $extendedheader = '', $footer = false, $showerrors = true, $noerrorsonly = true ) 
	{
		ID3::ID3v2FrameIsAllowed( null, '', '' ); // clear static array in case this isn't the first call to ID3::generateID3v2Tag()
		$tagstring = '';
		
		if ( is_array( $data ) ) 
		{
			if ( is_array( $extendedheader ) ) 
			{
				// not supported yet
			}
		
			foreach ( $data as $frame_name => $frame_rawinputdata ) 
			{
				if ( !is_array( $frame_rawinputdata ) || !isset( $frame_rawinputdata[0] ) || !is_array( $frame_rawinputdata[0] ) ) 
				{
					// force everything to be arrayed so only one processing loop
					$frame_rawinputdata = array( $frame_rawinputdata );
				}
				
				foreach ( $frame_rawinputdata as $irrelevantindex => $frame_inputdata ) 
				{
					if ( ID3::isValidID3v2FrameName( $frame_name, $majorversion ) ) 
					{
						unset( $frame_length );
						unset( $frame_flags  );
						
						$frame_data = false;
					
						if ( ID3::ID3v2FrameIsAllowed( $frame_name, $frame_inputdata, $majorversion, $showerrors ) ) 
						{
							if ( $frame_data = ID3::generateID3v2FrameData( $frame_name, $frame_inputdata, $majorversion, $showerrors ) ) 
							{
								$FrameUnsynchronisation = false;
							
								if ( $majorversion >= 4 ) 
								{
									// frame-level unsynchronization
									$unsynchdata = ID3::unSynchronise( $frame_data );
									
									if ( strlen( $unsynchdata ) != strlen( $frame_data ) ) 
									{
										// unsynchronization needed
										$FrameUnsynchronisation = true;
										$frame_data = $unsynchdata;
									
										if ( isset( $TagUnsynchronisation ) && $TagUnsynchronisation === false ) 
										{
											// only set to true if ALL frames are unsynchronised
										} 
										else 
										{
											$TagUnsynchronisation = true;
										}
									} 
									else 
									{
										if ( isset( $TagUnsynchronisation ) )
											$TagUnsynchronisation = false;
									}
								
									unset( $unsynchdata );
									$frame_length = ID3::bigEndianToString( strlen( $frame_data ), 4, true );
								} 
								else 
								{
									$frame_length = ID3::bigEndianToString( strlen( $frame_data ), 4, false );
								}
							
								$frame_flags  = ID3::generateID3v2FrameFlags( $majorversion, ID3::ID3v2FrameFlagsLookupTagAlter( $frame_name, $majorversion ), ID3::ID3v2FrameFlagsLookupFileAlter( $frame_name, $majorversion ), false, false, false, false, $FrameUnsynchronisation, false );
							}
						} 
						else 
						{
							if ( $showerrors )
								echo 'Frame "' . $frame_name . '" is NOT allowed<br>';
						}
					
						if ( $frame_data === false ) 
						{
							if ( $showerrors )
								echo 'ID3::generateID3v2FrameData() failed for "' . $frame_name . '"<br>';
						
							if ( $noerrorsonly )
								return false;
							else
								unset( $frame_name );
						}
					} 
					else 
					{
						// ignore any invalid frame names, including 'title', 'header', etc
						unset( $frame_name   );
						unset( $frame_length );
						unset( $frame_flags  );
						unset( $frame_data   );
					}
				
					if ( isset( $frame_name ) && isset( $frame_length ) && isset( $frame_flags ) && isset( $frame_data ) ) 
						$tagstring .= $frame_name . $frame_length . $frame_flags . $frame_data;
				}
			}
		
			if ( $footer ) 
			{
				if ( $showerrors )
					echo 'Footer not supported (yet)<br>';
			
				return false;
			}

			if ( !isset( $TagUnsynchronisation ) )
				$TagUnsynchronisation = true;
		
			if ( $majorversion <= 3 ) 
			{
				// tag-level unsynchronization
				$unsynchdata = ID3::unSynchronise( $tagstring );
			
				if ( strlen( $unsynchdata ) != strlen( $tagstring ) ) 
				{
					// unsynchronization needed
					$TagUnsynchronisation = true;
					$tagstring = $unsynchdata;
				}
			}

			if ( !$footer && ( $paddedlength > ( strlen( $tagstring ) + ID3::ID3v2HeaderLength( $majorversion ) ) ) ) 
			{
				// pad up to $paddedlength bytes if unpadded tag is shorter than $paddedlength
				// "Furthermore it MUST NOT have any padding when a tag footer is added to the tag."
				$tagstring .= @str_repeat( chr( 0 ), $paddedlength - strlen( $tagstring ) );
			}
		
			if ( substr( $tagstring, strlen( $tagstring ) - 1, 1 ) == chr( 255 ) ) 
			{
				// special unsynchronization case:
				// if last byte == $FF then appended a $00
				$TagUnsynchronisation = true;
				$tagstring .= chr( 0 );
			}

			$tagheader  = 'ID3';
			$tagheader .= chr( $majorversion );
			$tagheader .= chr( $minorversion );
			$tagheader .= ID3::generateID3v2TagFlags( $majorversion, $TagUnsynchronisation, false, (bool)$extendedheader, false, $footer );
			$tagheader .= ID3::bigEndianToString( strlen( $tagstring ), 4, true );

			return $tagheader . $tagstring;
		} 
		else 
		{
			return false;
		}
	}

	function writeID3v2( $filename, $data, $majorversion = 4, $minorversion = 0, $overwrite = false, $paddedlength = 0, $showerrors = false ) 
	{
		// File MUST be writeable - CHMOD(646) at least. It's best if the
		// directory is also writeable, because that method is both faster and less susceptible to errors.
	
		if ( is_writeable( $filename ) || ( !file_exists( $filename ) && is_writeable( dirname( $filename ) ) ) ) 
		{
			$error = '';
			$OldMP3fileInfo = ID3::getAllMP3info( $filename );
			
			if ( $overwrite ) 
			{
				// ignore previous data
			} 
			else 
			{
				// merge with existing data
				$data = ID3::arrayJoinMerge( $OldMP3fileInfo, $data );
				$paddedlength = max( $OldMP3fileInfo['id3']['id3v2']['headerlength'], $paddedlength );
			}
		
			if ( $NewID3v2Tag = ID3::generateID3v2Tag( $data['id3']['id3v2'], $majorversion, $minorversion, $paddedlength, '', false, $showerrors, true ) ) 
			{
				if ( ( !file_exists( $filename ) && is_writeable( dirname( $filename ) ) ) || ( is_writeable( $filename ) && ( $OldMP3fileInfo['id3']['id3v2']['headerlength'] == strlen( $NewID3v2Tag ) ) ) ) 
				{
					// best and fastest method - insert-overwrite existing tag (padded to length of old tag if neccesary)
					if ( file_exists( $filename ) ) 
					{
						ob_start();
					
						if ( $fp = fopen( $filename, 'r+b' ) ) 
						{
							rewind( $fp );
							fwrite( $fp, $NewID3v2Tag, strlen( $NewID3v2Tag ) );
							fclose( $fp );
						} 
						else 
						{
							$error .= 'Could not open ' . $filename . ' mode "r+b" - ' . strip_tags( ob_get_contents() ) . '<br>';
						}
					
						ob_end_clean();
					} 
					else 
					{
						ob_start();
					
						if ( $fp = fopen( $filename, 'wb' ) ) 
						{
							rewind( $fp );
							fwrite( $fp, $NewID3v2Tag, strlen( $NewID3v2Tag ) );
							fclose( $fp );
						} 
						else 
						{
							$error .= 'Could not open ' . $filename . ' mode "wb" - ' . strip_tags( ob_get_contents() ) . '<br>';
						}
					
						ob_end_clean();
					}
				} 
				else 
				{
					// new tag is longer than old tag - must rewrite entire file
					if ( is_writeable( dirname( $filename ) ) ) 
					{
						// preferred alternate method - only one copying operation, minimal chance of corrupting
						// original file if script is interrupted, but required directory to be writeable
						ob_start();
					
						if ( $fp_source = fopen( $filename, 'rb' ) ) 
						{
							if ( $OldMP3fileInfo['audiobytes'] > 0 ) 
							{
								rewind( $fp_source );
								
								if ( $OldMP3fileInfo['audiodataoffset'] !== false )
									fseek( $fp_source, $OldMP3fileInfo['audiodataoffset'], SEEK_SET );
							
								ob_start();
							
								if ( $fp_temp = fopen( $filename . 'id3libtmp', 'wb' ) ) 
								{
									fwrite( $fp_temp, $NewID3v2Tag, strlen( $NewID3v2Tag ) );

									while ( $buffer = fread( $fp_source, ID3_FREAD_BUFFER_SIZE ) )
										fwrite( $fp_temp, $buffer, strlen( $buffer ) );
								
									fclose( $fp_temp );
								} 
								else 
								{
									$error .= 'Could not open ' . $filename . 'id3libtmp mode "wb" - ' . strip_tags( ob_get_contents() ) . '<br>';
								}
							
								ob_end_clean();
								fclose( $fp_source );
							}
							// no previous audiodata 
							else 
							{
								ob_start();
							
								if ( $fp_temp = fopen( $filename . 'id3libtmp', 'wb' ) ) 
								{
									fwrite( $fp_temp, $NewID3v2Tag, strlen( $NewID3v2Tag ) );
									fclose( $fp_temp );
								} 
								else 
								{
									$error .= 'Could not open ' . $filename . 'id3libtmp mode "wb" - ' . strip_tags( ob_get_contents() ) . '<br>';
								}
							
								ob_end_clean();
							}
						} 
						else 
						{
							$error .= 'Could not open ' . $filename . ' mode "rb" - ' . strip_tags( ob_get_contents() ) . '<br>';
						}
					
						ob_end_clean();
					
						if ( !$error ) 
						{
							if ( file_exists( $filename ) )
								unlink( $filename );
						
							rename( $filename . 'id3libtmp', $filename );
						}
					} 
					else 
					{
						// less desirable alternate method - double-copies the file, overwrites original file
						// and could corrupt source file if the script is interrupted or an error occurs.
						ob_start();
					
						if ( $fp_source = fopen( $filename, 'rb' ) ) 
						{
							rewind( $fp_source );
							
							if ( $OldMP3fileInfo['audiodataoffset'] !== false )
								fseek( $fp_source, $OldMP3fileInfo['audiodataoffset'], SEEK_SET );
						
							if ( $fp_temp = tmpfile() ) 
							{
								fwrite( $fp_temp, $NewID3v2Tag, strlen( $NewID3v2Tag ) );
	
								while ( $buffer = fread( $fp_source, ID3_FREAD_BUFFER_SIZE ) )
									fwrite( $fp_temp, $buffer, strlen( $buffer ) );
							
								fclose( $fp_source );
								ob_start();
								
								if ( $fp_source = @fopen( $filename, 'wb' ) ) 
								{
									rewind( $fp_temp );

									while ( $buffer = fread( $fp_temp, ID3_FREAD_BUFFER_SIZE ) )
										fwrite( $fp_source, $buffer, strlen( $buffer ) );
								
									fseek( $fp_temp, -128, SEEK_END );
									fclose( $fp_source );
								} 
								else 
								{
									$error .= 'Could not open ' . $filename . ' mode "wb" - ' . strip_tags( ob_get_contents() ) . '<br>';
								}
							
								ob_end_clean();
								fclose( $fp_temp );
							} 
							else 
							{
								$error .= 'Could not create tmpfile()<br>';
							}
						} 
						else 
						{
							$error .= 'Could not open ' . $filename . ' mode "rb" - '.strip_tags( ob_get_contents() ) . '<br>';
						}
					
						ob_end_clean();
					}
				}
			} 
			else 
			{
				$error .= 'ID3::generateID3v2Tag() failed<br>';
			}

			if ( $error ) 
			{
				if ( $showerrors )
					echo $error;
			
				return false;
			}
		
			return true;
		}
	
		return false;
	}

	function removeID3v2( $filename, $showerrors = false ) 
	{
		// File MUST be writeable - CHMOD(646) at least. It's best if the
		// directory is also writeable, because that method is both faster and less susceptible to errors.
		if ( is_writeable( dirname( $filename ) ) ) 
		{
			// preferred method - only one copying operation, minimal chance of corrupting
			// original file if script is interrupted, but required directory to be writeable
			if ( $fp_source = @fopen( $filename, 'rb' ) ) 
			{
				$OldMP3fileInfo = ID3::getAllMP3info( $filename );
				rewind( $fp_source );
				
				if ( $OldMP3fileInfo['audiodataoffset'] !== false )
					fseek( $fp_source, $OldMP3fileInfo['audiodataoffset'], SEEK_SET );
			
				if ( $fp_temp = @fopen( $filename . 'id3libtmp', 'w+b' ) ) 
				{
					while ( $buffer = fread( $fp_source, ID3_FREAD_BUFFER_SIZE ) )
						fwrite( $fp_temp, $buffer, strlen( $buffer ) );
				
					fclose( $fp_temp );
				} 
				else 
				{
					$error .= 'Could not open ' . $filename . 'id3libtmp mode "w+b"<br>';
				}
			
				fclose( $fp_source );
			} 
			else 
			{
				$error .= 'Could not open ' . $filename . ' mode "rb"<br>';
			}
		
			if ( file_exists( $filename ) )
				unlink( $filename );
		
			rename( $filename . 'id3libtmp', $filename );
		} 
		else if ( is_writable( $filename ) ) 
		{
			// less desirable alternate method - double-copies the file, overwrites original file
			// and could corrupt source file if the script is interrupted or an error occurs.
			if ( $fp_source = @fopen( $filename, 'rb' ) ) 
			{
				$OldMP3fileInfo = ID3::getAllMP3info( $filename );
				rewind( $fp_source );
				
				if ( $OldMP3fileInfo['audiodataoffset'] !== false )
					fseek( $fp_source, $OldMP3fileInfo['audiodataoffset'], SEEK_SET );
			
				if ( $fp_temp = tmpfile() ) 
				{
					while ( $buffer = fread( $fp_source, ID3_FREAD_BUFFER_SIZE ) )
						fwrite( $fp_temp, $buffer, strlen( $buffer ) );
				
					fclose( $fp_source );
					
					if ( $fp_source = @fopen( $filename, 'wb' ) ) 
					{
						rewind( $fp_temp );
					
						while ( $buffer = fread( $fp_temp, ID3_FREAD_BUFFER_SIZE ) )
							fwrite( $fp_source, $buffer, strlen( $buffer ) );
					
						fseek( $fp_temp, -128, SEEK_END );
						fclose( $fp_source );
					} 
					else 
					{
						$error .= 'Could not open ' . $filename . ' mode "wb"<br>';
					}
				
					fclose( $fp_temp );
				} 
				else 
				{
					$error .= 'Could not create tmpfile()<br>';
				}
			} 
			else 
			{
				$error .= 'Could not open ' . $filename . ' mode "rb"<br>';
			}
		} 
		else 
		{
			$error .= 'Directory and file both not writeable<br>';
		}
	
		if ( $error )
		{
			if ( $showerrors )
				echo $error;
		
			return false;
		} 
		else 
		{
			return true;
		}
	}
	
	function generateID3v1Tag( $title, $artist, $album, $year, $genre, $comment, $track ) 
	{
		$ID3v1Tag  = 'TAG';
		$ID3v1Tag .= str_pad( substr( $title,  0, 30 ), 30, chr( 0 ), STR_PAD_RIGHT );
		$ID3v1Tag .= str_pad( substr( $artist, 0, 30 ), 30, chr( 0 ), STR_PAD_RIGHT );
		$ID3v1Tag .= str_pad( substr( $album,  0, 30 ), 30, chr( 0 ), STR_PAD_RIGHT );
		$ID3v1Tag .= str_pad( substr( $year,   0,  4 ),  4,      ' ', STR_PAD_LEFT  );

		if ( isset( $track ) && ( $track > 0 ) && ( $track <= 255 ) ) 
		{
			$ID3v1Tag .= str_pad( substr( $comment, 0, 28 ), 28, chr( 0 ), STR_PAD_RIGHT );
			$ID3v1Tag .= chr( 0 );
			
			if ( gettype( $track ) == 'string' )
				$track = (int)$track;
		
			$ID3v1Tag .= chr( $track );
		} 
		else 
		{
			$ID3v1Tag .= str_pad( substr( $comment, 0, 30 ), 30, chr( 0 ), STR_PAD_RIGHT );
		}
	
		if ( ( $genre < 0 ) || ( $genre > 147 ) )
			$genre = 255; // 'unknown' genre
	
		if ( gettype( $genre ) == 'string' ) 
		{
			$genrenumber  = (int)$genre;
			$ID3v1Tag    .= chr( $genrenumber );
		} 
		else if ( gettype( $genre ) == 'integer' ) 
		{
			$ID3v1Tag .= chr( $genre );
		} 
		else 
		{
			$ID3v1Tag .= chr( 255 ); // 'unknown' genre
		}

		return $ID3v1Tag;
	}

	function writeID3v1( $filename, $title = '', $artist = '', $album = '', $year = '', $comment = '', $genre = 255, $track = '', $showerrors = false ) 
	{
		// File MUST be writeable - CHMOD(646) at least
		if ( is_writeable( $filename ) ) 
		{
			$error = '';
		
			if ( $fp_source = @fopen( $filename, 'r+b' ) ) 
			{
				fseek( $fp_source, -128, SEEK_END );
			
				if ( fread( $fp_source, 3 ) == 'TAG' )
					fseek( $fp_source, -128, SEEK_END ); // overwrite existing ID3v1 tag
				else
					fseek( $fp_source,    0, SEEK_END ); // append new ID3v1 tag
			
				fwrite( $fp_source, ID3::generateID3v1Tag( $title, $artist, $album, $year, $genre, $comment, $track ), 128 );
				fclose( $fp_source );
			} 
			else 
			{
				if ( $showerrors )
					echo 'Could not open ' . $filename . ' mode "r+b"<br>';
			
				return false;
			}
		
			return true;
		}
	
		if ( $showerrors )
			echo '!is_writable(' . $filename . ')<br>';
	
		return false;
	}

	function removeID3v1( $filename, $showerrors = false )  
	{
		// File MUST be writeable - CHMOD(646) at least
		if ( is_writeable( $filename ) ) 
		{
			if ( $fp_source = @fopen( $filename, 'r+b' ) ) 
			{
				fseek( $fp_source, -128, SEEK_END );
			
				if ( fread( $fp_source, 3 ) == 'TAG' ) 
				{
					ftruncate( $fp_source, filesize( $filename ) - 128 );
				} 
				else 
				{
					// no ID3v1 tag to begin with - do nothing
				}
			
				fclose( $fp_source );
			} 
			else 
			{
				$error .= 'Could not open ' . $filename . ' mode "r+b"<br>';
			}
		
			if ( $error ) 
			{
				if ( $showerrors )
					echo $error;
			
				return false;
			} 
			else 
			{
				return true;
			}
		} 
		else 
		{
			return false;
		}
	}

	function isValidPriceString( $pricestring ) 
	{
		if ( ID3::languageLookup( substr( $pricestring, 0, 3 ), true ) == '' )
			return false;
		else if ( !ID3::isANumber( substr( $pricestring, 3 ), true ) )
			return false;
	
		return true;
	}

	function ID3v2FrameFlagsLookupTagAlter( $framename, $majorversion ) 
	{
		// unfinished
		switch ( $framename ) 
		{
			case 'RGAD':
				$allow = true;
				// break;
				
			default:
				$allow = false;
				break;
		}
	
		return $allow;
	}

	function ID3v2FrameFlagsLookupFileAlter( $framename, $majorversion ) 
	{
		// unfinished
		switch ( $framename ) 
		{
			case 'RGAD':
				return false;
				break;

			default:
				return false;
				break;
		}
	}

	function isValidETCOevent( $eventid, $majorversion ) 
	{
		if ( ( $eventid < 0 ) || ( $eventid > 0xFF ) ) 
		{
			// outside range of 1 byte
			return false;
		} 
		else if ( ( $eventid >= 0xF0 ) && ( $eventid <= 0xFC ) ) 
		{
			// reserved for future use
			return false;
		} 
		else if ( ( $eventid >= 0x17 ) && ( $eventid <= 0xDF ) ) 
		{
			// reserved for future use
			return false;
		} 
		else if ( ( $eventid >= 0x0E ) && ( $eventid <= 0x16 ) && ( $majorversion == 2 ) ) 
		{
			// not defined in ID3v2.2
			return false;
		} 
		else if ( ( $eventid >= 0x15 ) && ( $eventid <= 0x16 ) && ( $majorversion == 3 ) ) 
		{
			// not defined in ID3v2.3
			return false;
		}
	
		return true;
	}

	function isValidSYLTtype( $contenttype, $majorversion ) 
	{
		if ( ( $contenttype >= 0 ) && ( $contenttype <= 8 ) && ( $majorversion == 4 ) )
			return true;
		else if ( ( $contenttype >= 0 ) && ( $contenttype <= 6 ) && ( $majorversion == 3 ) )
			return true;
	
		return false;
	}

	function isValidRVA2channelType( $channeltype, $majorversion ) 
	{
		if ( ( $channeltype >= 0 ) && ( $channeltype <= 8 ) && ( $majorversion == 4 ) )
			return true;
	
		return false;
	}

	function isValidAPICpicturetype( $picturetype, $majorversion ) 
	{
		if ( ( $picturetype >= 0 ) && ( $picturetype <= 0x14 ) && ( $majorversion >= 2 ) && ( $majorversion <= 4 ) )
			return true;
	
		return false;
	}

	function isValidAPICimageformat( $imageformat, $majorversion ) 
	{
		if ( $imageformat == '-->' ) 
		{
			return true;
		} 
		else if ( $majorversion == 2 ) 
		{
			if ( ( strlen( $imageformat ) == 3 ) && ( $imageformat == strtoupper( $imageformat ) ) )
				return true;
		} 
		else if ( ( $majorversion == 3 ) || ( $majorversion == 4 ) ) 
		{
			if ( ID3::isValidMIMEstring( $imageformat ) )
				return true;
		}
	
		return false;
	}

	function isValidCOMRreceivedas( $receivedas, $majorversion ) 
	{
		if ( ( $majorversion >= 3 ) && ( $receivedas >= 0 ) && ( $receivedas <= 8 ) )
			return true;
	
		return false;
	}

	function isValidRGADname( $RGADname, $majorversion ) 
	{
		if ( ( $RGADname >= 0 ) && ( $RGADname <= 2 ) )
			return true;
	
		return false;
	}

	function isValidRGADoriginator( $RGADoriginator, $majorversion ) 
	{
		if ( ( $RGADoriginator >= 0 ) && ( $RGADoriginator <= 3 ) )
			return true;
	
		return false;
	}

	function isValidMIMEstring( $mimestring ) 
	{
		if ( ( strlen( $mimestring ) >= 3 ) && ( strpos( $mimestring, '/' ) > 0 ) && ( strpos( $mimestring, '/' ) < ( strlen( $mimestring ) - 1 ) ) )
			return true;
	
		return false;
	}

	function isWithinBitRange( $number, $maxbits, $signed = false ) 
	{
		if ( $signed ) 
		{
			if ( ( $number > ( 0 - pow( 2, $maxbits - 1 ) ) ) && ( $number <= pow( 2, $maxbits - 1 ) ) )
				return true;
		} 
		else 
		{
			if ( ( $number >= 0 ) && ( $number <= pow( 2, $maxbits ) ) )
				return true;
		}
	
		return false;
	}

	function isValidTextEncoding( $textencodingbyte, $majorversion ) 
	{
		$textencodingintval = chr( $textencodingbyte );
		
		if ( ( $textencodingintval >= 0 ) && ( $textencodingintval <= 3 ) && ( $majorversion == 4 ) )
			return true;
		else if ( ( $textencodingintval >= 0 ) && ( $textencodingintval <= 1 ) && ( $majorversion == 3 ) )
			return true;
		else if ( ( $textencodingintval >= 0 ) && ( $textencodingintval <= 1 ) && ( $majorversion == 2 ) )
			return true;
	
		return false;
	}

	function safeParseURL( $url ) 
	{
		$parts = @parse_url( $url );
		
		$parts['scheme'] = ( isset( $parts['scheme'] )? $parts['scheme'] : '' );
		$parts['host']   = ( isset( $parts['host']   )? $parts['host']   : '' );
		$parts['user']   = ( isset( $parts['user']   )? $parts['user']   : '' );
		$parts['pass']   = ( isset( $parts['pass']   )? $parts['pass']   : '' );
		$parts['path']   = ( isset( $parts['path']   )? $parts['path']   : '' );
		$parts['query']  = ( isset( $parts['query']  )? $parts['query']  : '' );

		return $parts;
	}

	function isValidURL( $url, $allowUserPass = false ) 
	{
		if ( $url == '' )
			return false;
	
		if ( $allowUserPass !== true ) 
		{
			if ( strstr( $url, '@' ) ) 
			{
				// in the format http://user:pass@test.com  or http://user@test.com
				// but could easily be somebody incorrectly entering an email address in place of a URL
				return false;
			}
		}
	
		if ( $parts = ID3::safeParseURL( $url ) ) 
		{
			if ( ( $parts['scheme'] != 'http' ) && ( $parts['scheme'] != 'https' ) && ( $parts['scheme'] != 'ftp' ) && ( $parts['scheme'] != 'gopher' ) ) 
				return false;
			else if ( !eregi("^([[:alnum:]-]|[\_])*$", $parts['user'], $regs ) )
				return false;
			else if ( !eregi("^([[:alnum:]-]|[\_])*$", $parts['pass'], $regs ) )
				return false;
			else if ( !eregi("^[[:alnum:]/_\.@~-]*$",  $parts['path'], $regs ) )
				return false;
			else if ( !eregi("^[[:alnum:]?&=+:;_()%#/,\.-]*$", $parts['query'], $regs ) )
				return false;
			else
				return true;
		} 
		else 
		{
			return false;
		}
	}


	// helper
	
	function printHexBytes( $string ) 
	{
		$returnstring = '';
		
		for ( $i = 0; $i < strlen( $string ); $i++ )
			$returnstring .= str_pad( dechex( ord( substr( $string, $i, 1 ) ) ), 2, '0', STR_PAD_LEFT ) . ' ';
		
		return $returnstring;
	}

	function printTextBytes( $string ) 
	{
		$returnstring = '';
		
		for ( $i = 0; $i < strlen( $string ); $i++ ) 
		{
			if ( ord( substr( $string, $i, 1 ) ) <= 31 )
				$returnstring .= '   ';
			else
				$returnstring .= ' ' . substr( $string, $i, 1 ) . ' ';
		}
		
		return $returnstring;
	}

	function fixTextFields( $text ) 
	{
		$text = stripslashes( $text );
		$text = str_replace( '\'', '&#39;',  $text );
		$text = str_replace( '"',  '&quot;', $text );
		
		return $text;
	}

	function tableVarDump( $variable ) 
	{
		$returnstring = '';
		
		switch ( gettype( $variable ) ) 
		{
			case 'array':
				$returnstring .= '<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="2">';
				
				foreach ( $variable as $key => $value ) 
				{
					$returnstring .= '<TR><TD VALIGN="TOP"><B>' . str_replace( chr( 0 ), ' ', $key ) . '</B></TD>';
					$returnstring .= '<TD VALIGN="TOP">' . gettype( $value );
					
					if ( is_array( $value ) ) 
						$returnstring .= '&nbsp;(' . count( $value ) . ')';
					else if ( is_string( $value ) )
						$returnstring .= '&nbsp;(' . strlen( $value ) . ')';
					
					if ( ( $key == 'data' ) && isset( $variable['image_mime'] ) && isset( $variable['dataoffset'] ) ) 
					{
						$imagechunkcheck = ID3::getDataImageSize( $value );
						$DumpedImageSRC  = $_REQUEST['filename'] . '.' . $variable['dataoffset'] . '.' . ID3::imageTypesLookup( $imagechunkcheck[2] );

						if ( $tempimagefile = fopen( $DumpedImageSRC, 'wb' ) ) 
						{
							fwrite( $tempimagefile, $value );
							fclose( $tempimagefile );
						}
						
						$returnstring .= '</TD><TD><IMG SRC="' . $DumpedImageSRC . '" WIDTH="' . $imagechunkcheck[0] . '" HEIGHT="' . $imagechunkcheck[1] . '"></TD>';
					} 
					else 
					{
						$returnstring .= '</TD><TD>' . ID3::tableVarDump( $value ) . '</TD>';
					}
				}
				
				$returnstring .= '</TABLE>';
				break;

			case 'boolean':
				$returnstring .= ( $variable? 'TRUE' : 'FALSE' );
				break;

			case 'integer':

			case 'double':

			case 'float':
				$returnstring .= $variable;
				break;

			case 'object':

			case 'null':
				$returnstring .= ID3::stringVarDump( $variable );
				break;

			case 'string':
				$variable = str_replace( chr( 0 ), ' ', $variable );
				$varlen   = strlen( $variable );
				
				for ( $i = 0; $i < $varlen; $i++ ) 
				{
					if ( ereg( '[' . chr( 0x0A ) . chr( 0x0D ) . ' -;A-z]', $variable{$i} ) )
						$returnstring .= $variable{$i};
					else
						$returnstring .= '&#' . str_pad( ord( $variable{$i} ), 3, '0', STR_PAD_LEFT ) . ';';
				}
				
				$returnstring = nl2br( $returnstring );
				break;

			default:
				$imagechunkcheck = ID3::getDataImageSize( substr( $variable, 0, ID3_FREAD_BUFFER_SIZE ) );

				if ( ( $imagechunkcheck[2] >= 1 ) && ( $imagechunkcheck[2] <= 3 ) ) 
				{
					$returnstring .= '<TABLE BORDER="1" CELLSPACING="0" CELLPADDING="2">';
					$returnstring .= '<TR><TD><B>type</B></TD><TD>'   . ID3::imageTypesLookup( $imagechunkcheck[2] ) . '</TD></TR>';
					$returnstring .= '<TR><TD><B>width</B></TD><TD>'  . number_format( $imagechunkcheck[0] ) . ' px</TD></TR>';
					$returnstring .= '<TR><TD><B>height</B></TD><TD>' . number_format( $imagechunkcheck[1] ) . ' px</TD></TR>';
					$returnstring .= '<TR><TD><B>size</B></TD><TD>'   . number_format( strlen( $variable))   . ' bytes</TD></TR></TABLE>';
				} 
				else 
				{
					$returnstring .= nl2br( htmlspecialchars( str_replace( chr( 0 ), ' ', $variable ) ) );
				}
				
				break;
		}
		
		return $returnstring;
	}

	function stringVarDump( $variable ) 
	{
		ob_start();
		var_dump( $variable );
		$dumpedvariable = ob_get_contents();
		ob_end_clean();
		
		return $dumpedvariable;
	}

	function fileextension( $filename, $numextensions = 1 ) 
	{
		if ( strstr( $filename, '.' ) ) 
		{
			$reversedfilename = strrev( $filename );
			$offset = 0;
			
			for ( $i = 0; $i < $numextensions; $i++ ) 
			{
				$offset = strpos( $reversedfilename, '.', $offset + 1 );
				
				if ( $offset === false )
					return '';
			}
			
			return strrev( substr( $reversedfilename, 0, $offset ) );
		}
		
		return '';
	}

	function removeAccents( $string ) 
	{
		return strtr( $string, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy" );
	}

	function moreNaturalSort( $ar1, $ar2 ) 
	{
		if ( $ar1 === $ar2 )
			return 0;
		
		$len1 = strlen( $ar1 );
		$len2 = strlen( $ar2 );
		
		if ( substr( $ar1, 0, min( $len1, $len2 ) ) === substr( $ar2, 0, min( $len1, $len2 ) ) ) 
		{
			// the shorter argument is the beginning of the longer one, like "str" and "string"
			if ( $len1 < $len2 )
				return -1;
			else if ( $len1 > $len2 )
				return 1;
			
			return 0;
		}
		
		$ar1 = ID3::removeAccents( strtolower( trim( $ar1 ) ) );
		$ar2 = ID3::removeAccents( strtolower( trim( $ar2 ) ) );
		
		$translatearray = array( 
			'\'' => '', 
			'"'  => '', 
			'_'  => ' ',
			'('  => '', 
			')'  => '', 
			'-'  => ' ', 
			'  ' => ' ', 
			'.'  => '', 
			','  => '' 
		);
		
		foreach ( $translatearray as $key => $val ) 
		{
			$ar1 = str_replace( $key, $val, $ar1 );
			$ar2 = str_replace( $key, $val, $ar2 );
		}

		if ( $ar1 < $ar2 )
			return -1;
		else if ( $ar1 > $ar2 )
			return 1;
		
		return 0;
	}

	// truncates a floating-point number at the decimal point
	// returns int (if possible, otherwise double)
	function trunc( $floatnumber ) 
	{
		if ( $floatnumber >= 1 )
			$truncatednumber = floor( $floatnumber );
		else if ( $floatnumber <= -1 )
			$truncatednumber = ceil( $floatnumber );
		else
			$truncatednumber = 0;
		
		if ( $truncatednumber <= pow( 2, 30 ) )
			$truncatednumber = (int)$truncatednumber;
		
		return $truncatednumber;
	}

	// convert a double to type int, only if possible
	function castAsInt( $doublenum ) 
	{
		if ( ID3::trunc( $doublenum ) == $doublenum ) 
		{
			// it's not floating point
			if ( $doublenum <= pow( 2, 30 ) ) 
			{
				// it's within int range
				$doublenum = (int)$doublenum;
			}
		}
		
		return $doublenum;
	}

	function decimalBinaryToFloat( $binarynumerator ) 
	{
		$numerator   = ID3::binToDec( $binarynumerator );
		$denominator = ID3::binToDec( str_repeat( '1', strlen( $binarynumerator ) ) );
		
		return ( $numerator / $denominator );
	}

	// see http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/binary.html
	function normalizeBinaryPoint( $binarypointnumber, $maxbits = 52 ) 
	{
		if ( strpos( $binarypointnumber, '.' ) === false )
			$binarypointnumber = '0.' . $binarypointnumber;
		else if ( $binarypointnumber{0} == '.' )
			$binarypointnumber = '0' . $binarypointnumber;
		
		$exponent = 0;
		
		while ( ( $binarypointnumber{0} != '1' ) || ( substr( $binarypointnumber, 1, 1 ) != '.' ) ) 
		{
			if ( substr( $binarypointnumber, 1, 1 ) == '.' ) 
			{
				$exponent--;
				$binarypointnumber = substr( $binarypointnumber, 2, 1 ) . '.' . substr( $binarypointnumber, 3 );
			} 
			else 
			{
				$pointpos  = strpos( $binarypointnumber, '.' );
				$exponent += ( $pointpos - 1 );
				
				$binarypointnumber = str_replace( '.', '', $binarypointnumber );
				$binarypointnumber = $binarypointnumber{0} . '.' . substr( $binarypointnumber, 1 );
			}
		}
		
		$binarypointnumber = str_pad( substr( $binarypointnumber, 0, $maxbits + 2 ), $maxbits + 2, '0', STR_PAD_RIGHT );
		return array( 'normalized' => $binarypointnumber, 'exponent' => (int)$exponent );
	}

	// see http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/binary.html
	function floatToBinaryDecimal( $floatvalue ) 
	{
		$maxbits        = 128; // to how many bits of precision should the calculations be taken?
		$intpart        = ID3::trunc( $floatvalue );
		$floatpart      = abs( $floatvalue - $intpart );
		$pointbitstring = '';
		
		while ( ( $floatpart != 0 ) && ( strlen( $pointbitstring ) < $maxbits ) ) 
		{
			$floatpart      *= 2;
			$pointbitstring .= (string)ID3::trunc( $floatpart );
			$floatpart      -= ID3::trunc( $floatpart );
		}
		
		$binarypointnumber = decbin( $intpart ) . '.' . $pointbitstring;
		return $binarypointnumber;
	}

	function floatToString( $floatvalue, $bits ) 
	{
		// http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/ieee-expl.html
		if ( ( $bits != 32 ) && ( $bits != 64 ) ) 
		{
			return false;
		} 
		else if ( $bits == 32 ) 
		{
			$exponentbits = 8;
			$fractionbits = 23;
		} 
		else if ( $bits == 64 ) 
		{
			$exponentbits = 11;
			$fractionbits = 52;
		}
		
		if ( $floatvalue >= 0 )
			$signbit = '0';
		else
			$signbit = '1';
		
		$normalizedbinary  = ID3::normalizeBinaryPoint( ID3::floatToBinaryDecimal( $floatvalue ), $fractionbits );
		$biasedexponent    = pow( 2, $exponentbits - 1 ) - 1 + $normalizedbinary['exponent']; // (127 or 1023) +/- exponent
		$exponentbitstring = str_pad( decbin( $biasedexponent ), $exponentbits, '0', STR_PAD_LEFT );
		$fractionbitstring = str_pad( substr( $normalizedbinary['normalized'], 2 ), $fractionbits, '0', STR_PAD_RIGHT );

		return ID3::bigEndianToString( ID3::binToDec( $signbit . $exponentbitstring . $fractionbitstring ), $bits % 8, false );
	}
	
	function littleEndianToFloat( $byteword ) 
	{
		return ID3::bigEndianToFloat( strrev( $byteword ) );
	}

	function bigEndianToFloat( $byteword ) 
	{
		// ANSI/IEEE Standard 754-1985, Standard for Binary Floating Point Arithmetic
		// http://www.psc.edu/general/software/packages/ieee/ieee.html
		// http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/ieee.html

		$bitword = ID3::bigEndianToBin( $byteword );
		$signbit = $bitword{0};
		
		// 32-bit DWORD
		if ( strlen( $byteword ) == 4 ) 
		{
			$exponentbits = 8;
			$fractionbits = 23;
		}
		// 64-bit QWORD 
		else if ( strlen( $byteword ) == 8 ) 
		{
			$exponentbits = 11;
			$fractionbits = 52;
		} 
		else 
		{
			return false;
		}
		
		$exponentstring = substr( $bitword, 1, $exponentbits );
		$fractionstring = substr( $bitword, 9, $fractionbits );
		
		$exponent = ID3::binToDec( $exponentstring );
		$fraction = ID3::binToDec( $fractionstring );
		
		if ( ( $exponent == ( pow( 2, $exponentbits ) - 1 ) ) && ( $fraction != 0 ) ) 
		{
			// Not a Number
			$floatvalue = false;
		} 
		else if ( ( $exponent == ( pow( 2, $exponentbits ) - 1 ) ) && ( $fraction == 0 ) ) 
		{
			if ( $signbit == '1' )
				$floatvalue = '-infinity';
			else
				$floatvalue = '+infinity';
		} 
		else if ( ( $exponent == 0 ) && ( $fraction == 0 ) ) 
		{
			if ( $signbit == '1' )
				$floatvalue = -0;
			else
				$floatvalue = 0;
		
			$floatvalue = ( $signbit? 0 : -0 );
		} 
		else if ( ($exponent == 0 ) && ( $fraction != 0 ) ) 
		{
			// These are 'unnormalized' values
			$floatvalue = pow( 2, ( -1 * ( pow( 2, $exponentbits - 1 ) - 2 ) ) ) * ID3::decimalBinaryToFloat( $fractionstring );

			if ( $signbit == '1' )
				$floatvalue *= -1;
		} 
		else if ( $exponent != 0 ) 
		{
			$floatvalue = pow( 2, ( $exponent - ( pow( 2, $exponentbits - 1 ) - 1 ) ) ) * ( 1 + ID3::decimalBinaryToFloat( $fractionstring ) );
			
			if ( $signbit == '1' )
				$floatvalue *= -1;
		}
		
		return (float)$floatvalue;
	}
	
	function bigEndianToInt( $byteword, $synchsafe = false, $signed = false ) 
	{
		$intvalue = 0;
		$bytewordlen = strlen( $byteword );
		
		for ( $i = 0; $i < $bytewordlen; $i++ ) 
		{
			if ( $synchsafe ) // disregard MSB, effectively 7-bit bytes
				$intvalue = $intvalue | ( ord( $byteword{$i} ) & 0x7F ) << ( ( $bytewordlen - 1 - $i ) * 7 );
			else
				$intvalue += ord( $byteword{$i} ) * pow( 256, ( $bytewordlen - 1 - $i ) );
		}
		
		if ( $signed && !$synchsafe ) 
		{
			// synchsafe ints are not allowed to be signed
			switch ( $bytewordlen ) 
			{
				case 1:
			
				case 2:
			
				case 3:
			
				case 4:
					$signmaskbit = 0x80 << ( 8 * ( $bytewordlen - 1 ) );
					
					if ( $intvalue & $signmaskbit )
						$intvalue = 0 - ( $intvalue & ( $signmaskbit - 1 ) );
					
					break;

				default:
					return PEAR::raiseError( 'Cannot have signed integers larger than 32-bits in ID3::bigEndianToInt().', null, PEAR_ERROR_DIE );
					break;
			}
		}
		
		return ID3::castAsInt( $intvalue );
	}

	function littleEndianToInt( $byteword, $signed = false ) 
	{
		return ID3::bigEndianToInt( strrev( $byteword ), false, $signed );
	}
	
	function bigEndianToBin( $byteword ) 
	{
		$binvalue    = '';
		$bytewordlen = strlen( $byteword );
		
		for ( $i = 0; $i < $bytewordlen; $i++ )
			$binvalue .= str_pad( decbin( ord( $byteword{$i} ) ), 8, '0', STR_PAD_LEFT );
		
		return $binvalue;
	}	

	function decToBin( $number ) 
	{
		while ( $number >= 256 ) 
		{
			$bytes[] = ( ( $number / 256 ) - ( floor( $number / 256 ) ) ) * 256;
			$number  = floor( $number / 256 );
		}
		
		$bytes[] = $number;
		$binstring = '';
		
		for ( $i = 0; $i < count( $bytes ); $i++ )
			$binstring = ( ( $i == count( $bytes ) - 1 )? decbin( $bytes["$i"] ) : str_pad( decbin( $bytes["$i"] ), 8, '0', STR_PAD_LEFT ) ) . $binstring;
		
		return $binstring;
	}

	function binToDec( $binstring ) 
	{
		$decvalue = 0;
		
		for ( $i = 0; $i < strlen( $binstring ); $i++ )
			$decvalue += ( (int)substr( $binstring, strlen( $binstring ) - $i - 1, 1 ) ) * pow( 2, $i );
		
		return ID3::castAsInt( $decvalue );
	}

	function binToString( $binstring ) 
	{
		// return 'hi' for input of '0110100001101001'
		$string = '';
		$binstringreversed = strrev( $binstring );
		
		for ( $i = 0; $i < strlen( $binstringreversed ); $i += 8 )
			$string = chr( ID3::binToDec( strrev( substr( $binstringreversed, $i, 8 ) ) ) ) . $string;
		
		return $string;
	}

	function bigEndianToString( $number, $minbytes = 1, $synchsafe = false, $signed = false ) 
	{
		if ( $number < 0 )
			return false;
		
		$maskbyte  = ( ( $synchsafe || $signed )? 0x7F : 0xFF );
		$intstring = '';
		
		if ( $signed ) 
		{
			if ( $minbytes > 4 )
				return PEAR::raiseError( 'Cannot have signed integers larger than 32-bits in ID3::bigEndianToString()', null, PEAR_ERROR_DIE );
			
			$number = $number & ( 0x80 << ( 8 * ( $minbytes - 1 ) ) );
		}
		
		while ( $number != 0 ) 
		{
			$quotient  = ( $number / ( $maskbyte + 1 ) );
			$intstring = chr( ceil( ( $quotient - floor( $quotient ) ) * $maskbyte ) ) . $intstring;
			$number    = floor( $quotient );
		}
		
		return str_pad( $intstring, $minbytes, chr( 0 ), STR_PAD_LEFT );
	}
	
	function littleEndianToString( $number, $minbytes = 1, $synchsafe = false ) 
	{
		while ( $number > 0 ) 
		{
			if ( $synchsafe ) 
			{
				$intstring = $intstring . chr( $number & 127 );
				$number >>= 7;
			} 
			else 
			{
				$intstring = $intstring . chr( $number & 255 );
				$number >>= 8;
			}
		}
		
		return $intstring;
	}

	function boolToIntString( $intvalue ) 
	{
		if ( $intvalue )
			return '1';
		else
			return '0';
	}

	function intStringToBool( $char ) 
	{
		if ( $char == '1' )
			return true;
		else if ( $char == '0' )
			return false;
	}

	function deUnSynchronise( $data ) 
	{
		return str_replace( chr( 0xFF ) . chr( 0x00 ), chr( 0xFF ), $data );
	}

	function unSynchronise( $data ) 
	{
		// Whenever a false synchronisation is found within the tag, one zeroed
		// byte is inserted after the first false synchronisation byte. The
		// format of a correct sync that should be altered by ID3 encoders is as
		// follows:
		// 	 %11111111 111xxxxx
		// And should be replaced with:
		// 	 %11111111 00000000 111xxxxx
		// This has the side effect that all $FF 00 combinations have to be
		// altered, so they won't be affected by the decoding process. Therefore
		// all the $FF 00 combinations have to be replaced with the $FF 00 00
		// combination during the unsynchronisation.

		$data = str_replace( chr( 0xFF ) . chr( 0x00 ), chr( 0xFF ) . chr( 0x00 ) . chr( 0x00 ), $data );
		$unsyncheddata = '';
		
		for ( $i = 0; $i < strlen( $data ); $i++ ) 
		{
			$thischar = $data{$i};
			$unsyncheddata .= $thischar;
			
			if ( $thischar == chr( 255 ) ) 
			{
				$nextchar = ord( substr( $data, $i + 1, 1 ) );
				
				if ( ( $nextchar | 0xE0 ) == 0xE0 ) 
				{
					// previous byte = 11111111, this byte = 111?????
					$unsyncheddata .= chr( 0 );
				}
			}
		}
		
		return $unsyncheddata;
	}

	function isHash( $var ) 
	{
		if ( is_array( $var ) ) 
		{
			$keys = array_keys( $var );
			$all_num = true;
			
			for ( $i = 0; $i < count( $keys ); $i++ ) 
			{
				if ( is_string( $keys["$i"] ) )
					return true;
			}
		}
		
		return false;
	}

	function arrayJoinMerge( $arr1, $arr2 ) 
	{
		if ( is_array( $arr1 ) && is_array( $arr2 ) ) 
		{
			// the same -> merge
			$new_array = array();

			if ( ID3::isHash( $arr1 ) && ID3::isHash( $arr2 ) ) 
			{
				// hashes -> merge based on keys
				$keys = array_merge( array_keys( $arr1 ), array_keys( $arr2 ) );
				
				foreach ( $keys as $key )
					$new_array["$key"] = ID3::arrayJoinMerge( $arr1["$key"], $arr2["$key"] );
			} 
			else 
			{
				// two real arrays -> merge
				$new_array = array_reverse( array_unique( array_reverse( array_merge( $arr1, $arr2 ) ) ) );
			}
			
			return $new_array;
	 	} 
		else 
		{
			// not the same ... take new one if defined, else the old one stays
			return $arr2? $arr2 : $arr1;
		}
	}

	// rough translation of data for application that can't handle Unicode data
	function roughTranslateUnicodeToASCII( $rawdata, $frame_textencoding ) 
	{
		$tempstring = '';
		
		switch ( $frame_textencoding ) 
		{
			case 0: // ISO-8859-1. Terminated with $00.
				$asciidata = $rawdata;
				break;

			case 1: // UTF-16 encoded Unicode with BOM. Terminated with $00 00.
				$asciidata = $rawdata;
				
				if ( substr( $asciidata, 0, 2 ) == chr( 0xFF ) . chr( 0xFE ) )
					$asciidata = substr( $asciidata, 2 ); // remove BOM, only if present (it should be, but...)
				
				if ( substr( $asciidata, strlen( $asciidata ) - 2, 2 ) == chr( 0 ) . chr( 0 ) )
					$asciidata = substr( $asciidata, 0, strlen( $asciidata ) - 2 ); // remove terminator, only if present (it should be, but...)
				
				for ( $i = 0; $i < strlen( $asciidata ); $i += 2 ) 
				{
					if ( ( ord( $asciidata{$i} ) <= 0x7F ) || ( ord( $asciidata{$i} ) >= 0xA0 ) )
						$tempstring .= $asciidata{$i};
					else
						$tempstring .= '?';
				}
				
				$asciidata = $tempstring;
				break;

			case 2: // UTF-16BE encoded Unicode without BOM. Terminated with $00 00.
				$asciidata = $rawdata;

				if ( substr( $asciidata, strlen( $asciidata ) - 2, 2 ) == chr( 0 ) . chr( 0 ) ) 
					$asciidata = substr( $asciidata, 0, strlen( $asciidata ) - 2 ); // remove terminator, only if present (it should be, but...)
				
				for ( $i = 0; $i < strlen( $asciidata ); $i += 2 ) 
				{
					if ( ( ord( $asciidata{$i} ) <= 0x7F ) || ( ord( $asciidata{$i} ) >= 0xA0 ) )
						$tempstring .= $asciidata{$i};
					else
						$tempstring .= '?';
				}
				
				$asciidata = $tempstring;
				break;

			case 3: // UTF-8 encoded Unicode. Terminated with $00.
				$asciidata = utf8_decode( $rawdata );
				break;

			default:
				// shouldn't happen, but in case $frame_textencoding is not 1 <= $frame_textencoding <= 4
				// just pass the data through unchanged.
				$asciidata = $rawdata;
				break;
		}
		
		if ( substr( $asciidata, strlen( $asciidata ) - 1, 1 ) == chr( 0 ) ) 
		{
			// remove null terminator, if present
			$asciidata = ID3::noNullString( $asciidata );
		}
		
		return $asciidata;
	}

	function playtimeString( $playtimeseconds ) 
	{
		$contentseconds = round( ( ( $playtimeseconds / 60 ) - floor( $playtimeseconds / 60 ) ) * 60 );
		$contentminutes = floor( $playtimeseconds / 60 );
		
		return number_format( $contentminutes ) . ':' . str_pad( $contentseconds, 2, 0, STR_PAD_LEFT );
	}

	function closeMatch( $value1, $value2, $tolerance ) 
	{
		return ( abs( $value1 - $value2 ) <= $tolerance );
	}

	function ID3v1matchesID3v2( $id3v1, $id3v2 ) 
	{
		$requiredindices = array( 'title', 'artist', 'album', 'year', 'genre', 'comment' );
		
		foreach ( $requiredindices as $requiredindex ) 
		{
			if ( !isset( $id3v1["$requiredindex"] ) )
				$id3v1["$requiredindex"] = '';
			
			if ( !isset( $id3v2["$requiredindex"] ) )
				$id3v2["$requiredindex"] = '';
		}

		if ( trim( $id3v1['title'] ) != trim( substr( $id3v2['title'], 0, 30 ) ) )
			return false;
		
		if ( trim( $id3v1['artist'] ) != trim( substr( $id3v2['artist'], 0, 30 ) ) )
			return false;
		
		if ( trim( $id3v1['album'] ) != trim( substr( $id3v2['album'], 0, 30 ) ) )
			return false;
		
		if ( trim( $id3v1['year'] ) != trim( substr( $id3v2['year'], 0, 4 ) ) )
			return false;
		
		if ( trim( $id3v1['genre'] ) != trim( $id3v2['genre'] ) )
			return false;
		
		if ( isset( $id3v1['track'] ) ) 
		{
			if ( !isset( $id3v1['track'] ) || ( trim( $id3v1['track'] ) != trim( $id3v2['track'] ) ) )
				return false;
			
			if ( trim( $id3v1['comment'] ) != trim( substr( $id3v2['comment'], 0, 28 ) ) )
				return false;
		} 
		else 
		{
			if ( trim($id3v1['comment'] ) != trim( substr( $id3v2['comment'], 0, 30 ) ) )
				return false;
		}
		
		return true;
	}

	function filetimeToUnixtime( $filetime, $round = true ) 
	{
		// filetime is a 64-bit unsigned integer representing
		// the number of 100-nanosecond intervals since January 1, 1601
		// UNIX timestamp is number of seconds since January 1, 1970
		// 116444736000000000 = 10000000 * 60 * 60 * 24 * 365 * 369 + 89 leap days
		
		if ( $round )
			return round( ( $filetime - 116444736000000000 ) / 10000000 );
		else
			return ( $filetime - 116444736000000000 ) / 10000000;
	}

	function GUIDToBytestring( $GUIDstring )
	{
		// Microsoft defines these 16-byte (128-bit) GUIDs in the strangest way:
		// first 4 bytes are in little-endian order
		// next 2 bytes are appended in little-endian order
		// next 2 bytes are appended in little-endian order
		// next 2 bytes are appended in big-endian order
		// next 6 bytes are appended in big-endian order

		// AaBbCcDd-EeFf-GgHh-IiJj-KkLlMmNnOoPp is stored as this 16-byte string:
		// $Dd $Cc $Bb $Aa $Ff $Ee $Hh $Gg $Ii $Jj $Kk $Ll $Mm $Nn $Oo $Pp

		$hexbytecharstring  = chr( hexdec( substr( $GUIDstring,  6, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring,  4, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring,  2, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring,  0, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 11, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring,  9, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 16, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 14, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 19, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 21, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 24, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 26, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 28, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 30, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 32, 2 ) ) );
		$hexbytecharstring .= chr( hexdec( substr( $GUIDstring, 34, 2 ) ) );

		return $hexbytecharstring;
	}

	function bytestringToGUID( $Bytestring ) 
	{
		$GUIDstring  = strtoupper( str_pad( dechex( ord( $Bytestring{3} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{2} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{1} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{0} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= '-';
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{5} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{4} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= '-';
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{7} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{6} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= '-';
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{8} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{9} ) ),  2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= '-';
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{10} ) ), 2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{11} ) ), 2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{12} ) ), 2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{13} ) ), 2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{14} ) ), 2, '0', STR_PAD_LEFT ) );
		$GUIDstring .= strtoupper( str_pad( dechex( ord( $Bytestring{15} ) ), 2, '0', STR_PAD_LEFT ) );

		return $GUIDstring;
	}

	function bitrateColor( $bitrate ) 
	{
		$bitrate--;
		$bitrate = max( $bitrate,   0 );
		$bitrate = min( $bitrate, 255 );
	
		// $bitrate = max( $bitrate,  32 );
		// $bitrate = min( $bitrate, 143 );
		// $bitrate = ( $bitrate * 2 ) - 32;

		$Rcomponent = max( 255 - ( $bitrate * 2 ), 0 );
		$Gcomponent = max( ( $bitrate * 2 ) - 255, 0 );
		
		if ( $bitrate > 127 )
			$Bcomponent = max( ( 255 - $bitrate ) * 2, 0 );
		else
			$Bcomponent = max( $bitrate * 2, 0 );
		
		return str_pad( dechex( $Rcomponent ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( $Gcomponent ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( $Bcomponent ), 2, '0', STR_PAD_LEFT );
	}

	function bitrateText( $bitrate ) 
	{
		return '<SPAN STYLE="color: #' . ID3::bitrateColor( $bitrate ) . '">' . round( $bitrate ) . ' kbps</SPAN>';
	}

	function imagetypeToMimetype( $imagetypeid ) 
	{
		// only available in PHP v4.?.?+
		static $image_type_to_mime_type = array();
		
		if ( count( $image_type_to_mime_type ) < 1 ) 
		{
			$image_type_to_mime_type[1] = 'image/gif';
			$image_type_to_mime_type[2] = 'image/jpeg';
			$image_type_to_mime_type[3] = 'image/png';
			$image_type_to_mime_type[4] = 'application/x-shockwave-flash';
			$image_type_to_mime_type[7] = 'image/tiff'; // little-endian (Intel)
			$image_type_to_mime_type[8] = 'image/tiff'; // big-endian (Motorola)
		}
		
		return ( isset( $image_type_to_mime_type["$imagetypeid"] )? $image_type_to_mime_type["$imagetypeid"] : '' );
	}
	
	function dateMacToUnix( $macdate ) 
	{
		// Macintosh timestamp: seconds since 00:00h January 1, 1904
		// UNIX timestamp:      seconds since 00:00h January 1, 1970
		return ID3::castAsInt( $macdate - 2082844800 );
	}

	function fixedPoint8_8( $rawdata ) 
	{
		return ID3::bigEndianToInt( substr( $rawdata, 0, 1 ) ) + (float)( ID3::bigEndianToInt( substr( $rawdata, 1, 1 ) ) / pow( 2, 8 ) );
	}

	function fixedPoint16_16( $rawdata ) 
	{
		return ID3::bigEndianToInt( substr( $rawdata, 0, 2 ) ) + (float)( ID3::bigEndianToInt( substr( $rawdata, 2, 2 ) ) / pow( 2, 16 ) );
	}

	function fixedPoint2_30( $rawdata ) 
	{
		$binarystring = ID3::bigEndianToBin( $rawdata );
		return ID3::binToDec( substr( $binarystring, 0, 2 ) ) + (float)( ID3::binToDec( substr( $binarystring, 2, 30 ) ) / pow( 2, 30 ) );
	}

	function noNullString( $nullterminatedstring ) 
	{
		// remove the single null terminator on null terminated strings
		return substr( $nullterminatedstring, 0, strlen( $nullterminatedstring ) - 1 );
	}

	function filesizeNiceDisplay( $filesize, $precision = 2 ) 
	{
		if ( $filesize < 1000 ) 
		{
			$sizeunit  = 'bytes';
			$precision = 0;
		} 
		else 
		{
			$filesize /= 1024;
			$sizeunit  = 'kB';
		}

		if ( $filesize >= 1000 ) 
		{
			$filesize /= 1024;
			$sizeunit  = 'MB';
		}
		
		if ( $filesize >= 1000 ) 
		{
			$filesize /= 1024;
			$sizeunit  = 'GB';
		}
		
		return number_format( $filesize, $precision ) . ' ' . $sizeunit;
	}	
} // END OF ID3

?>
