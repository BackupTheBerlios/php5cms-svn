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
+----------------------------------------------------------------------+
*/


/**
 * @package util_text_encoding_lib
 */
 
class Encoding extends PEAR
{
	/**
	 * @access public
	 */
	var $encoding_map = array();
	
	/**
	 * @access public
	 */
	var $decoding_map = array();
	
	/**
	 * @access public
	 */
	var $_encoding_type = null;
	
	
	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Encoding( $type )
	{
		$this->_populateEncodingMap();
		$this->_populateDecodingMap();
		
		$this->_encoding_type = $type;
	}
	
	
	/**
	 * @access public
	 * @static
	 */
	function &factory( $name )
	{
		static $registered;
		$registered = array(
			'CP037',		// cp037_IBMUSCanada to Unicode table
			'CP1006',		// IBM CP1006  to Unicode
			'CP1026',		// cp875_IBMGreek to Unicode table
			'CP1140',
			'CP1250',		// cp1250 to Unicode table
			'CP1251',		// cp1251 to Unicode table
			'CP1252',		// cp1252 to Unicode table
			'CP1253',		// cp1253 to Unicode table
			'CP1254',		// cp1254 to Unicode table
			'CP1255',		// cp1255 to Unicode table
			'CP1256',		// cp1256 to Unicode table
			'CP1257',		// cp1257 to Unicode table
			'CP1258',		// cp1258 to Unicode table
			'CP424',		// IBM EBCDIC CP424 (Hebrew) to Unicode table
			'CP437',		// cp437_DOSLatinUS to Unicode table
			'CP500',		// cp500_IBMInternational to Unicode table
			'CP737',		// cp737_DOSGreek to Unicode table
			'CP775',		// cp775_DOSBaltRim to Unicode table
			'CP850',		// cp850_DOSLatin1 to Unicode table
			'CP852',		// cp852_DOSLatin2 to Unicode table
			'CP855',		// cp855_DOSCyrillic to Unicode table
			'CP856',		// cp856_Hebrew_PC to Unicode table
			'CP857',		// cp857_DOSTurkish to Unicode table
			'CP860', 		// cp860_DOSPortuguese to Unicode table
			'CP861', 		// cp861_DOSIcelandic to Unicode table
			'CP862', 		// cp862_DOSHebrew to Unicode table
			'CP863', 		// cp863_DOSCanadaF to Unicode table
			'CP864',		// cp864_DOSArabic to Unicode table
			'CP865',		// cp865_DOSNordic to Unicode table
			'CP866',		// cp866_DOSCyrillicRussian to Unicode table
			'CP869',		// cp869_DOSGreek2 to Unicode table
			'CP874',		// cp874 to Unicode table
			'CP875',		// cp875_IBMGreek to Unicode table
			'ISO8859_2',
			'ISO8859_3',
			'ISO8859_4',
			'ISO8859_5',
			'ISO8859_6',
			'ISO8859_7',
			'ISO8859_8',
			'ISO8859_9',
			'ISO8859_10',
			'ISO8859_13',
			'ISO8859_14',
			'ISO8859_15',
			'KOI8R',		// KOI8-R (RFC1489) to Unicode
			'MacCyrillic',	// cp10007_MacCyrillic to Unicode table
			'MacGreek',		// cp10006_MacGreek to Unicode table
			'MacIceland',	// cp10079_MacIcelandic to Unicode table
			'MacLatin2',	// cp10029_MacLatin2 to Unicode table
			'MacRoman',		// cp10000_MacRoman to Unicode table
			'MacTurkish',	// cp10081_MacTurkish to Unicode table
			'NextStep'		// NextStep Encoding to Unicode
		);
	
		static $aliases;
		$aliases = array(
		    // Latin-1
		    'latin'					=> 'Latin1',
		    'latin1'				=> 'Latin1',
    
		    // UTF-7
		    'utf7'					=> 'UTF7',
		    'u7'					=> 'UTF7',
    
		    // UTF-8
		    'utf'					=> 'UTF8',
		    'utf8'					=> 'UTF8',
		    'u8'					=> 'UTF8',
		    'utf8@ucs2'				=> 'UTF8',
		    'utf8@ucs4'				=> 'UTF8',
    
		    // UTF-16
		    'utf16'					=> 'UTF16',
		    'u16'					=> 'UTF16',
		    'utf_16be'				=> 'UTF16_BE',
		    'utf_16le'				=> 'UTF16_LE',
		    'unicodebigunmarked'	=> 'UTF16_BE',
		    'unicodelittleunmarked'	=> 'UTF16_LE',

		    // ASCII
		    'us_ascii' 				=> 'ASCII',
		    'ansi_x3.4_1968' 		=> 'ASCII', // used on Linux
		    '646' 					=> 'ASCII', // used on Solaris

		    // EBCDIC
		    'ebcdic_cp_us'	 		=> 'CP037',
		    'ibm039' 				=> 'CP037',
		    'ibm1140' 				=> 'CP1140',
    
   	 		// ISO
	   	 	'8859' 					=> 'Latin1',
	    	'iso8859' 				=> 'Latin1',
	    	'iso8859_1' 			=> 'Latin1',
	    	'iso_8859_1'	 		=> 'Latin1',
	    	'iso_8859_10' 			=> 'ISO8859_10',
			'iso_8859_13' 			=> 'ISO8859_13',
			'iso_8859_14' 			=> 'ISO8859_14',
			'iso_8859_15' 			=> 'ISO8859_15',
			'iso_8859_2' 			=> 'ISO8859_2',
			'iso_8859_3' 			=> 'ISO8859_3',
			'iso_8859_4' 			=> 'ISO8859_4',
			'iso_8859_5' 			=> 'ISO8859_5',
			'iso_8859_6' 			=> 'ISO8859_6',
			'iso_8859_7' 			=> 'ISO8859_7',
			'iso_8859_8' 			=> 'ISO8859_8',
			'iso_8859_9' 			=> 'ISO8859_9',

			'KOI8_R'				=> 'KOI8R',
			'KOI8-R'				=> 'KOI8R',
			'koi8_r'				=> 'KOI8R',
			'koi8-r'				=> 'KOI8R',
			'koi8r'					=> 'KOI8R',
		
			// Mac
			'maclatin2' 			=> 'MacLatin2',
			'mac_latin2' 			=> 'MacLatin2',
			'mac_latin_2' 			=> 'MacLatin2',
			'maccentraleurope' 		=> 'MacLatin2',
			'mac_centraleurope' 	=> 'MacLatin2',
			'mac_central_europe' 	=> 'MacLatin2',
			'maccyrillic' 			=> 'MacCyrillic',
			'mac_cyrillic' 			=> 'MacCyrillic',
			'macgreek' 				=> 'MacGreek',
			'mac_greek' 			=> 'MacGreek',
			'maciceland' 			=> 'MacIceland',
			'mac_iceland' 			=> 'MacIceland',
			'macroman' 				=> 'MacRoman',
			'mac_roman' 			=> 'MacRoman',
			'macturkish' 			=> 'MacTurkish',
			'mac_turkish' 			=> 'MacTurkish',
		
			// Windows
			'windows_1251' 			=> 'CP1251',
  		  	'windows_1252' 			=> 'CP1252',
			'windows_1254' 			=> 'CP1254',
			'windows_1255' 			=> 'CP1255',
		
			// NextStep
			'next_step'				=> 'NextStep',
			'nextstep'				=> 'NextStep',
		
			// MBCS
			'dbcs' 					=> 'MBCS',

			// Code pages
			'437' 					=> 'CP437',

			// The codecs for these encodings are not distributed with the
			// Abstractpage core, but are included here for reference
			'jis_7' 				=> 'JIS7',
			'iso_2022_jp' 			=> 'JIS7',
			'ujis' 					=> 'EUC_JP',
			'ajec' 					=> 'EUC_JP',
			'eucjp' 				=> 'EUC_JP',
			'tis260' 				=> 'TACTIS',
			'sjis' 					=> 'SHIFT_JIS',

			// Content transfer/compression encodings
			'rot13' 				=> 'ROT13',
			'base64' 				=> 'Base64',
			'base_64' 				=> 'Base64',
			'zlib' 					=> 'ZLIB',
			'zip' 					=> 'ZLIB',
			'hex' 					=> 'Hex',
			'uu' 					=> 'UU',
			'quopri' 				=> 'QUOPRI',
			'quotedprintable' 		=> 'QUOPRI',
			'quoted_printable' 		=> 'QUOPRI'
		);
		
		$resolved_alias = $aliases[$name];

		if ( in_array( $name, $registered ) || in_array( $resolved_alias, $registered ) )
		{
			$encoding  = in_array( $name, $registered )? $name : $resolved_alias;
			$classname = "Encoding_" . $encoding;
			
			// check if already imported
			using( 'util.text.encoding.lib.' . $classname );
			
			if ( class_registered( $classname ) )
				return new $classname;
			else
				return PEAR::raiseError( "Cannot load Driver." );
		}
		else
		{
			return PEAR::raiseError( "Driver not supported." );
		}
	}
	
	/**
	 * @access public
	 */
	function getEncodingType()
	{
		return $this->_encoding_type;
	}
	
	/**
	 * @access public
	 */
	function encode( $string )
	{
		// TODO
		
		/*
		$mapping = $this->encoding_map;

		for ( $mapped = "", $character = 0; $character < strlen( $string ); $character++ )
  		{
   			$code    = ord( $string[$character] );
   			$mapped .= ( isset( $mapping[$code] ) && ( $mapping[$code] != null ) )? chr( $mapping[$code] ) : $string[$character];
  		}
  
  		return $mapped;
		*/
	}
	
	/**
	 * @access public
	 */
	function decode( $string )
	{
		// TODO
		
		/*
		$mapping = $this->decoding_map;

		for ( $mapped = "", $character = 0; $character < strlen( $string ); $character++ )
  		{
   			$code    = ord( $string[$character] );
   			$mapped .= ( isset( $mapping[$code] ) && ( $mapping[$code] != null ) )? chr( $mapping[$code] ) : $string[$character];
  		}
  
  		return $mapped;
		*/
	}
	
	
	// private methods
	
	/**
	 * @abstract
	 */
	function _populateEncodingMap()
	{
		return PEAR::raiseError( "Abstract method." );
	}
	
	/**
	 * @access private
	 */
	function _populateDecodingMap()
	{
		foreach ( $this->encoding_map as $key => $value )
			$this->decoding_map[$value] = $key;
	}
} // END OF Encoding

?>
