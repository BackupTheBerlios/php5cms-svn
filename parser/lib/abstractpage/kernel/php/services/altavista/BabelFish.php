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
 * @package services_altavista
 */
 
class BabelFish extends PEAR
{
	/**
	 * @access public
	 */
	var $_host;
	
	/**
	 * @access public
	 */
	var $_supported = array(
		"en_zh", // Englisch ins Chinesische
		"en_fr", // Englisch ins Französiche
		"en_de", // Englisch ins Deutsche
		"en_it", // Englisch ins Italienische
		"en_ja", // Englisch ins Japanische
		"en_ko", // Englisch ins Koreanische
		"en_pt", // Englisch ins Portuguiesische
		"en_es", // Englisch ins Spanische
		"zh_en", // Chinesisch ins Englische
		"fr_en", // Französisch ins Englische
		"fr_de", // Französisch ins Deutsche
		"de_en", // Deutsch ins Englische
		"de_fr", // Deutsch ins Französische
		"it_en", // Italienisch ins Englische
		"ja_en", // Japanisch ins Englische
		"ko_en", // Koreanisch ins Englische
		"pt_en", // Portuguiesisch ins Englische
		"ru_en", // Russisch ins Englische
		"es_en", // Spanisch ins Englische	
	);
	
	
	/**
	 * Constructor
	 */
	function BabelFish()
	{
		$this->_host = 'babelfish.altavista.com';
	}
	
	
	/**
	 * @access public
	 */
	function translate( $text, $from = "en", $to = "de" )
	{
		if ( !$this->isSupported( $from, $to ) )
			return PEAR::raiseError( "Translation mode is not supported." );
			
		$lp   = strtolower( $from ) . "_" . strtolower( $to );
    	$vars = "doit=$DOIT&tt=$TT&urltext=" . urlencode( utf8_encode( $text ) ) . "&lp=$lp";
    	$url  = "/tr?$vars";

    	$fp = fsockopen( $this->_host, 80, $errno, $errstr, 30 );

    	if ( !$fp ) 
		{
      		return PEAR::raiseError( "Connection failed: $errstr ($errno)." );
    	} 
		else 
		{
        	$addinfo .= "Accept-Language: en\r\nAccept-Charset: iso-8859-1,*,utf-8\r\n";
        	fputs( $fp, "GET $url HTTP/1.1\r\nHost: $this->_host\r\n$addinfo\r\n" );
		
        	while ( !feof( $fp ) )
            	$html .= fgets( $fp, 128 );
        
        	fclose( $fp );
    	}
	
		// remove linebreaks
    	$html = ereg_replace( "\n", " ", $html );

	    // capture translated text
    	ereg( "name=\"q\" value=(.*)>", $html, $saida );
    	$translation = $saida[1];
    	$translation = ereg_replace( ">.*$", "", $translation );
    	$translation = utf8_decode( $translation );

 	   	return $translation;
	}
	
	/**
	 * @access public
	 */
	function isSupported( $from, $to )
	{
		return in_array( strtolower( $from ) . "_" . strtolower( $to ), $this->_supported );
	}
} // END OF BabelFish

?>
