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
 * Class to generate vCard files to import into Netscape, Outlook etc.
 * Complies to version 2.1 of the vCard specification.
 *
 * Supports the following attributes:
 * Name, Formatted Name, Phone and fax numbers, Birthday, Address,
 * Address label, email address, notes, and URL.
 *
 * @package format_vformat
 */
 
class VCard extends PEAR
{
	/**
	 * @access public
	 */
	var $properties;
	
	/**
	 * @access public
	 */
	var $filename;
	
	/**
	 * @access public
	 */
	var $mailer;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function VCard()
	{
		$this->mailer = ap_ini_get( "agent_name", "settings" );
	}
	

	/**
	 * @access public
	 */	
	function setPhoneNumber( $number, $type = "" )
	{
		// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		
		$key = "TEL";
		
		if ( $type != "" )
			$key .= ";" . $type;
			
		$key .= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = $this->_quoted_printable_encode( $number );
	}
	
	/**
	 * Note: untested!
	 *
	 * @access public
	 */
	function setPhoto( $type, $photo )
	{
		// $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode( $photo );
	}

	/**
	 * @access public
	 */	
	function setFormattedName( $name )
	{
		$this->properties["FN"] = $this->_quoted_printable_encode( $name );
	}
	
	/**
	 * @access public
	 */
	function setName( $family = "", $first = "", $additional = "", $prefix = "", $suffix = "" )
	{
		$this->properties["N"] = "$family;$first;$additional;$prefix;$suffix";
		$this->filename = "$first%20$family.vcf";
		
		if ( $this->properties["FN"] == "" )
			$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
	}
	
	/**
	 * @access public
	 */
	function setBirthday( $date )
	{
		// $date format is YYYY-MM-DD
		$this->properties["BDAY"] = $date;
	}
	
	/**
	 * @access public
	 */
	function setAddress( $postoffice = "", $extended = "", $street = "", $city = "", $region = "", $zip = "", $country = "", $type = "HOME;POSTAL" )
	{
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = "ADR";
		
		if ( $type != "" )
			$key.= ";$type";
		
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		
		$this->properties[$key] =
			$this->_encode( $name     ) . ";" . 
			$this->_encode( $extended ) . ";" . 
			$this->_encode( $street   ) . ";".
			$this->_encode( $city     ) . ";".
			$this->_encode( $region   ) . ";".
			$this->_encode( $zip      ) . ";".
			$this->_encode( $country  );
		
		if ( $this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == "" )
		{
			// $this->setLabel( $postoffice, $extended, $street, $city, $region, $zip, $country, $type );
		}
	}
	
	/**
	 * @access public
	 */
	function setLabel( $postoffice = "", $extended = "", $street = "", $city = "", $region = "", $zip = "", $country = "", $type = "HOME;POSTAL" )
	{
		$label = "";
		
		if ( $postoffice != "" )
			$label.= "$postoffice\r\n";
		
		if ( $extended != "" )
			$label.= "$extended\r\n";
		
		if ( $street != "" )
			$label.= "$street\r\n";
		
		if ( $zip != "" )
			$label.= "$zip ";
		
		if ( $city != "" )
			$label.= "$city\r\n";
		
		if ( $region != "" )
			$label.= "$region\r\n";
		
		if ( $country != "" )
			$country.= "$country\r\n";
		
		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = $this->_quoted_printable_encode( $label );
	}
	
	/**
	 * @access public
	 */
	function setEmail( $address )
	{
		$this->properties["EMAIL;INTERNET"] = $address;
	}
	
	/**
	 * @access public
	 */
	function setNote( $note )
	{
		$this->properties["NOTE;ENCODING=QUOTED-PRINTABLE"] = $this->_quoted_printable_encode( $note );
	}
	
	/**
	 * @access public
	 */
	function setURL( $url, $type = "" )
	{
		// $type may be WORK | HOME
		$key = "URL";
		
		if ( $type != "" )
			$key.= ";$type";
		
		$this->properties[$key] = $url;
	}
	
	/**
	 * @access public
	 */
	function getVCard()
	{
		$text  = "BEGIN:VCARD\r\n";
		$text .= "VERSION:2.1\r\n";
		
		foreach( $this->properties as $key => $value )
			$text .= "$key:$value\r\n";
		
		$text .= "REV:" . date( "Y-m-d" ) . "T" . date( "H:i:s" ) . "Z\r\n";
		$text .= "MAILER:" . $this->mailer . "\r\n";
		$text .= "END:VCARD\r\n";
		
		return $text;
	}
	
	/**
	 * @access public
	 */
	function getFileName()
	{
		return $this->filename;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _encode( $string )
	{
		return $this->_escape( $this->_quoted_printable_encode( $string ) );
	}

	/**
	 * @access private
	 */
	function _escape( $string )
	{
		return str_replace( ";", "\;", $string );
	}

	/**
	 * @access public
	 */
	function _quoted_printable_encode( $input, $line_max = 76 )
	{
		$hex       = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines     = preg_split( "/(?:\r\n|\r|\n)/", $input );
		$eol       = "\r\n";
		$linebreak = "=0D=0A";
		$escape    = "=";
		$output    = "";

		for ( $j = 0; $j < count( $lines ); $j++ )
		{
			$line    = $lines[$j];
			$linlen  = strlen( $line );
			$newline = "";
		
			for ( $i = 0; $i < $linlen; $i++ )
			{
				$c   = substr( $line, $i, 1 );
				$dec = ord( $c );
				
				if ( ( $dec == 32 ) && ( $i == ( $linlen - 1 ) ) )
				{
					// convert space at eol only
					$c = "=20"; 
				}
				else if ( ( $dec == 61 ) || ( $dec < 32 ) || ( $dec > 126 ) )
				{
					// always encode "\t", which is *not* required
					$h2 = floor( $dec / 16 );
					$h1 = floor( $dec % 16 ); 
					$c  = $escape . $hex["$h2"] . $hex["$h1"]; 
				}
			
				if ( ( strlen( $newline ) + strlen( $c ) ) >= $line_max )
				{
					// CRLF is not counted
					$output  .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
					$newline  = "    ";
				}
			
				$newline .= $c;
			}
		
			$output .= $newline;
		
			if ( $j < count( $lines ) - 1 )
				$output .= $linebreak;
		}

		return trim( $output );
	}
} // END OF VCard

?>
