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


define( 'VALIDATION_NUM',            '0-9' );
define( 'VALIDATION_SPACE',          '\s'  );
define( 'VALIDATION_ALPHA_LOWER',    'a-z' );
define( 'VALIDATION_ALPHA_UPPER',    'A-Z' );
define( 'VALIDATION_ALPHA',          VALIDATION_ALPHA_LOWER  . VALIDATION_ALPHA_UPPER   );
define( 'VALIDATION_EALPHA_LOWER',   VALIDATION_ALPHA_LOWER  . '·ÈÌÛ˙‡ËÏÚ˘‰ÎÔˆ¸‚ÍÓÙ˚ÒÁ' );
define( 'VALIDATION_EALPHA_UPPER',   VALIDATION_ALPHA_UPPER  . '¡…Õ”⁄¿»Ã“ŸƒÀœ÷‹¬ Œ‘€—«' );
define( 'VALIDATION_EALPHA',         VALIDATION_EALPHA_LOWER . VALIDATION_EALPHA_UPPER  );
define( 'VALIDATION_PUNCTUATION',    VALIDATION_SPACE        . '\.,;\:&"\'\?\!\(\)'     );
define( 'VALIDATION_NAME',           VALIDATION_EALPHA       . VALIDATION_SPACE . "'"   );
define( 'VALIDATION_STREET',         VALIDATION_NAME         . "/\\∫™"                  );

define( "VALIDATION_CC_TYPE_MC", 0 ); // Master Card
define( "VALIDATION_CC_TYPE_VS", 1 ); // Visa
define( "VALIDATION_CC_TYPE_AX", 2 ); // American Express
define( "VALIDATION_CC_TYPE_DC", 3 ); // Diners Club
define( "VALIDATION_CC_TYPE_DS", 4 ); // Discover
define( "VALIDATION_CC_TYPE_JC", 5 ); // JCB


/**
 * @package util_validation
 */
 
class Validation extends PEAR
{
	/**
     * Validate a ISBN (International Standard Book Number) number.
     *
     *
     * @param  string  $isbn number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
	 * @access public
     */
    function is_isbn( $isbn )
    {
        if ( preg_match( "/[^0-9 IXSBN-]/", $isbn ) )
            return false;

        if ( !ereg( "^ISBN", $isbn ) )
            return false;

        $isbn = ereg_replace( "-", "", $isbn );
        $isbn = ereg_replace( " ", "", $isbn );
        $isbn = eregi_replace( "ISBN", "", $isbn );
		
        if ( strlen( $isbn ) != 10 )
            return false;
        
        if ( preg_match( "/[^0-9]{9}[^0-9X]/", $isbn ) )
            return false;

        $t = 0;
		
        for ( $i = 0; $i < strlen( $isbn ) - 1; $i++ )
            $t += $isbn[$i] * ( 10 - $i );
        
        $f = $isbn[9];
		
        if ( $f == "X" )
            $t += 10;
        else
            $t += $f;
        
        if ( $t % 11 )
            return false;
        else
            return true;
    }
	
	/**
 	 * Validate an ISSN number.
 	 *
 	 * This function validates an ISSN (International Standard Serial Number).
 	 *
 	 * @param string $issn ISSN number in format nnnn-nnn[nx]
 	 * @return bool True if the ISSN number is valid, false otherwise
 	 */
	function is_issn( $issn )
	{
    	if ( !preg_match( "/^([0-9]{4})-([0-9]{3}[0-9X])$/", $issn, $matches ) )
        	return false;
    	else
        	$issn = $matches[1] . $matches[2];

    	$chksum = 0;

    	for ( $i = 0; $i < strlen( $issn ) - 1; ++$i )
        	$chksum += $issn[$i] * ( 8 - $i );

    	$chksum += $issn[7] == "X"? 10 : $issn[7];

    	if ( $chksum % 11 )
        	return false;
    	else
        	return true;
	}

    /**
     * Validate a SSCC (Serial Shipping Container Code).
     *
     * This function checks given SSCC number
     * used to identify logistic units.
     * http://www.ean-ucc.org/
     * http://www.uc-council.org/checkdig.htm
     *
     * @param  string  $sscc number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
	 * @access public
     */
    function is_sscc( $sscc )
    {
        static $weights_sscc = array( 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3 );

        $sscc = str_replace( array( '-', '/', ' ', "\t", "\n" ), '', $sscc );

        // check if this is a 18-digit number
        if ( !is_numeric( $sscc ) || strlen( $sscc ) != 18 )
            return false;

        return Validation::_check_control_number( $sscc, $weights_sscc, 10, 10 );
    }
	
    /**
     * Validate a UCC-12 (U.P.C.) ID number
     *
     * This function checks given UCC-12 number used to identify
     * trade items, locations, and special applications (e.g., * coupons)
     * http://www.ean-ucc.org/
     * http://www.uc-council.org/checkdig.htm
     *
     * @param  string  $ucc number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
	 * @access public
     */
    function is_ucc12( $ucc )
    {
        static $weights_ucc12 = array(3,1,3,1,3,1,3,1,3,1,3);

        $ucc = str_replace( array( '-', '/', ' ', "\t", "\n" ), '', $ucc );

        // check if this is a 12-digit number
        if ( !is_numeric( $ucc ) || strlen( $ucc ) != 12 )
            return false;

        return Validation::_check_control_number( $ucc, $weights_ucc12, 10, 10 );
    }
	
    /**
     * Validate a ISMN (International Standard Music Number).
     *
     * This function checks given ISMN number (ISO Standard 10957)
     * ISMN identifies all printed music publications from all over the world
     * whether available for sale, hire or gratis--whether a part, a score,
     * or an element in a multi-media kit:
     * http://www.ismn-international.org/
     *
     * @param  string  $ismn ISMN number
     * @return bool    true if number is valid, otherwise false
     * @access public
     */
    function is_ismn( $ismn )
    {
        static $weights_ismn = array( 3, 1, 3, 1, 3, 1, 3, 1, 3 );

        $ismn = eregi_replace( "ISMN", "", $ismn );
        $ismn = eregi_replace( "M", "3", $ismn ); // change first M to 3
        $ismn = str_replace( array( '-', '/', ' ', "\t", "\n" ), '', $ismn );

        // check if this is a 10-digit number
        if ( !is_numeric( $ismn ) || strlen( $ismn ) != 10 )
            return false;

        return Validation::_check_control_number( $ismn, $weights_ismn, 10, 10 );
    }
	
    /**
     * Validate a EAN/UCC-8 number.
     *
     * This function checks given EAN8 number
     * used to identify trade items and special applications.
     * http://www.ean-ucc.org/
     * http://www.uc-council.org/checkdig.htm
     *
     * @param  string  $ean number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
     * @access public
     */
    function is_ean8( $ean )
    {
        static $weights_ean8 = array( 3, 1, 3, 1, 3, 1, 3 );

        $ean = str_replace( array( '-', '/', ' ', "\t", "\n" ), '', $ean );

        // check if this is a 8-digit number
        if ( !is_numeric( $ean ) || strlen( $ean ) != 8 )
            return false;

        return Validation::_check_control_number( $ean, $weights_ean8, 10, 10 );
    }

    /**
     * Validate a EAN/UCC-13 number.
     *
     * This function checks given EAN/UCC-13 number used to identify
     * trade items, locations, and special applications (e.g., coupons)
     * http://www.ean-ucc.org/
     * http://www.uc-council.org/checkdig.htm
     *
     * @param  string  $ean number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
     * @access public
     */
    function is_ean13( $ean )
    {
        static $weights_ean13 = array( 1, 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3 );

        $ean = str_replace( array( '-', '/', ' ', "\t", "\n" ), '', $ean );

        // check if this is a 13-digit number
        if ( !is_numeric( $ean ) || strlen( $ean ) != 13 )
            return false;

        return Validation::_check_control_number( $ean, $weights_ean13, 10, 10 );
    }

    /**
     * Validate a EAN/UCC-14 number.
     *
     * This function checks given EAN/UCC-14 number
     * used to identify trade items.
     * http://www.ean-ucc.org/
     * http://www.uc-council.org/checkdig.htm
     *
     * @param  string  $ean number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
     * @access public
     */
    function is_ean14( $ean )
    {
        static $weights_ean14 = array( 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3 );

        $ean = str_replace( array( '-', '/', ' ', "\t", "\n" ), '', $ean );

        // check if this is a 14-digit number
        if ( !is_numeric( $ean ) || strlen( $ean ) != 14 )
            return false;

        return Validation::_check_control_number( $ean, $weights_ean14, 10, 10 );
    }
	
    /**
     * Validate a number.
     *
     * @param string    $number     Number to validate
     * @param array     $options    array where:
     *                              'decimal'   is the decimal char or false when decimal not allowed
     *                                          i.e. ',.' to allow both ',' and '.'
     *                              'dec_prec'  Number of allowed decimals
     *                              'min'       minimun value
     *                              'max'       maximum value
	 * @access public
     */
    function is_number( $number, $options )
    {
        $decimal = $dec_prec = $min = $max = null;

        if ( is_array( $options ) )
            extract( $options );

        $dec_prec  = $dec_prec? "{1,$dec_prec}" : '+';
        $dec_regex = $decimal ? "[$decimal][0-9]$dec_prec" : '';

        if ( !preg_match( "|^[-+]?\s*[0-9]+($dec_regex)?\$|", $number ) )
            return false;
        
        if ( $decimal != '.' )
            $number = strtr( $number, $decimal, '.' );
        
        $number = (float)str_replace( ' ', '', $number );
		
        if ( $min !== null && $min > $number )
            return false;
        
        if ( $max !== null && $max < $number )
            return false;
        
        return true;
    }

	/**
	 * Checks to see if all the characters are alphabetical.
	 *
	 * @param string $data string to be validated
	 * @return boolean 
	 * @access public
	 */
	function is_alpha( $data )
	{
		return eregi( "^[a-z]+$", $data );
	}

	/**
	 * Checks to see if all the characters are alphabetic and/or numeric.
	 *
	 * @param string $data string to be validated
	 * @return boolean
	 * @access public
	 */
	function is_alphanumeric( $data)
	{
		if ( ereg( "[[:space:]]", $data ) )
			return false;
		else
			return ereg( "^[a-zA-Z_0-9]+", $data );
	}
	
	/**
	 * Checks to see if all the data is a string.
	 * Data must contain at least one string character.
	 *
	 * @param string $data string to be validated
	 * @return boolean
	 * @access public
	 */
	function is_loosestring( $data )
	{
		if ( strlen( $data ) > 0 )
		{
			if ( is_string( $data ) )
			{
				$tmp = strip_tags( $data, $this->_allowed_html );
				
				if ( strlen( $tmp ) < strlen( $data ) )
					return false;
				else
					return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks to see if the data is a valid time.
	 *
	 * @param string $data string to be validated
	 * @param int $mask sets validation to 12hour or 24hour
	 *                  0 any format mask, default
	 *                  1 hh:mm:ss
	 *                  2 hh:mm
	 *                  3 hh:mm:ss am | pm
	 *                  4 hh:mm am | pm
	 * @return boolean
	 * @access public
	 */
	function is_time( $data, $mask = 0 )
	{
		$masks[1] = "^(0{1}|1{1}|2{1})([0-9]{1}):(0{1}|1{1}|2{1}|3{1}|4{1}|5{1})([0-9]{1}):(0{1}|1{1}|2{1}|3{1}|4{1}|5{1})([0-9]{1})$";
		$masks[2] = "^(0{1}|1{1}|2{1})([0-9]{1}):(0{1}|1{1}|2{1}|3{1}|4{1}|5{1})([0-9]{1})$";
		$masks[3] = "^(0{1}|1{1}|2{1})([0-9]{1}):(0{1}|1{1}|2{1}|3{1}|4{1}|5{1})([0-9]{1}):(0{1}|1{1}|2{1}|3{1}|4{1}|5{1})([0-9]{1}) (am | pm)$";
		$masks[4] = "^(0{1}|1{1}|2{1})([0-9]{1}):(0{1}|1{1}|2{1}|3{1}|4{1}|5{1})([0-9]{1})$ (am | pm)";
		
		/*
		 * make sure $mask is clean
		 */
		$mask = intval( $mask );
		
		if ( $mask < 0 )
			$mask = 0;
		
		if ( $mask > count( $masks ) )
			$mask = 0;
		
		if ( $mask > 0 )
		{
			if ( ereg( $masks[$mask], $data ) )
				return true;
			else
				return false;
		}
		else
		{
			// mask is 0 test all the format masks
		}
	}

	/**
	 * Checks to see if all the data is a strong password.
	 * A strong password meets the following criteria:
	 * - minimum of 8 letters
	 * - contains punctuation and numbers
	 * - should not be 'password', 'god' or 'sex'
	 * - should be mixed case
	 *
	 * The character ranges are based on the ASCII table:
	 * 33-47, 58-64, 91-96, 123-126 are punctuation
	 * 48-57 are numbers
	 * 65-90 are upper case letters
	 * 97-122 are lower case letters
	 *
	 * For my sanity, I prohibit quotes in the password. (ASCII 34,39,96)
	 *
	 * A password is considered strong if it scores 4 or better.  Passwords score points based on following rules:
	 * lower case letters, no more than 50%, 1 point
	 * upper case letters, no more than 50%, 1 point
	 * numbers, at least 20%, 1 point
	 * punctuation, at least 20%, 1 point
	 * length is 10 or more characters, 1 point
	 *
	 * @param string data		string to be validated
	 * @return boolean
	 * @access public
	 */
	function is_strong_password( $data )
	{
		if ( is_string( $data ) )
		{
			if ( strlen( $data ) > 7 )
			{
				if ( strlen( $data ) > 9 )
					$score["length"]++;
				
				$len = strlen( $data );
				
				for ( $x = 0; $x < $len; $x++ )
				{
					$ord = ord( substr( $data, $x, 1 ) );
					
					if ( ( $ord != 34 ) || ( $ord != 39 ) || ( $ord != 96 ) )
					{
						if ( ( ( $ord > 32 ) && ( $ord < 48 ) ) || ( ( $ord > 57 ) && ( $ord < 65 ) ) || ( ( $ord > 90 ) && ( $ord < 97 ) ) || ( ( $ord > 122 ) && ( $ord < 127 ) ) )
						{
							// character is punctuation
							$score["punct"]++;
						}
						else if ( ( $ord > 47 ) && ( $ord < 58 ) )
						{
							// character is a number
							$score["num"]++;
						}
						else if ( ( $ord > 64 ) && ( $ord < 91 ) )
						{
							// character is uppercase
							$score["upper"]++;
						}
						else if ( ( $ord > 96 ) && ( $ord < 123 ) )
						{
							// character is lowercase
							$score["lower"]++;
						}
						else{
							// invalid character
						}
					}
					else
					{
						return false;
					}
				}
				
				if ( $score["length"] > 0 )
					$score["abs"]++;
				
				if ( $score["punct"] > 0 )
				{
					if ( ( ( $score["punct"] / $len ) * 100 ) > 19 )
						$score["abs"]++;
				}
				
				if ( $score["num"] > 0 )
				{
					if ( ( ( $score["num"] / $len ) * 100 ) > 19 )
						$score["abs"]++;
				}
				
				if ( $score["lower"] > 0 )
				{
					if ( ( ( $score["lower"] / $len ) * 100 ) < 51 )
						$score["abs"]++;
				}
				
				if ( $score["upper"] > 0 )
				{
					if ( ( ( $score["upper"] / $len ) * 100 ) < 51 )
						$score["abs"]++;
				}
				
				if ( $score["abs"] > 3 )
					return true;
				else
					return false;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Validate a money string.
	 *
	 * Note: Lame approach, we have to change this.
	 *
     * @param  string    $money         String to validate
     * @return bool      true if money is valid, otherwise false
	 * @access public
     */	
	function is_money( $money )
	{
		return ( preg_match( '^\$?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$', $money ) );
	}
	
    /**
     * Validate a URL.
     *
     * @param  string    $url            URL to validate
     * @param  boolean   $domain_check   Check or not if the domain exists
	 * @access public
     */
    function is_url( $url, $domain_check = false )
    {
		$valid_url = eregi( "^((ht|f)tp://)((([a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3}))|(([0-9]{1,3}\.){3}([0-9]{1,3})))((/|\?)[a-z0-9~#%&'_\+=:\?\.-]*)*)$", $url );
		
        $purl = parse_url( $url );
		
        if ( $valid_url && $domain_check && function_exists( 'checkdnsrr' ) && preg_match( '|^http$|i', @$purl['scheme'] ) && !empty( $purl['host'] ) ) 
		{
			if ( checkdnsrr( $purl['host'], 'A' ) )
				return true;
			else
				return false;
        }
		else
		{
        	return $valid_url;
		}
    }

	function is_ip( $ip, $resolve_check = false  )
	{
		$valid_ip = preg_match( "/^([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])\.([01]?\d\d?|2[0-4]\d|25[0-4])$/", $ip );
		
		if ( $valid_ip && $resolve_check && function_exists( 'checkdnsrr' ) )
		{
			$hostname = gethostbyaddr( $ip );

			if ( $hostname == $ip )
				return false;

			if ( $hostname )
			{
				if ( !checkdnsrr( $hostname ) )
					return false;
			
				if ( ( gethostbyname( $hostname ) ) != $ip )
					return false;
			}
			else
			{
				return false;
			}
		
			return true;
		}
		else
		{
			return $valid_ip;
		}
	}
	
    /**
     * Validate a email.
     *
     * @param  string    $email          URL to validate
     * @param  boolean   $domain_check   Check or not if the domain exists
	 * @access public
     */
    function is_email( $email, $check_domain = false )
    {
		$valid_email = ereg( "^([0-9,a-z,A-Z]+)([.,_]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$", $email );
		
		if ( $valid_email && $check_domain && function_exists( 'checkdnsrr' ) ) 
		{
	    	list ( $Username, $domain ) = split ("@", $email);  

		    // That MX(mail exchanger) record exists in domain check.  
   	 		if ( checkdnsrr( $domain, 'MX' ) || checkdnsrr( $domain, 'A' ) )
			{		
				// Getmxrr function does to store MX record address about $domain in arrangement form to $mxhost.  
				// $connectAddress socket connection address.  
				$connectAddress = $mxhost[0];
			}  
    		else
			{  
        		// If there is no MX record simply @ to next time address socket connection do .  
        		$connectAddress = $domain;
			}  

			$connect = fsockopen( $connectAddress, 25 );

    		// success in socket connection  
    		if ( $connect )    
    		{  
				// Judgment is that service is preparing though begin by 220 getting string after connection.  
        		if ( ereg ( "^220", $out = fgets( $connect, 1024 ) ) )
				{        
            		// inform client's reaching to server who connect
            		fputs( $connect, "HELO $_SERVER[HTTP_HOST]\r\n" );  

					// receive server's answering cord
					$out = fgets( $connect, 1024 );  
			
        	    	// inform sender's address to server  
            		fputs( $connect, "MAIL FROM: <{$email}>\r\n" );  

					// receive server's answering cord
					$from = fgets( $connect, 1024 ); 
			
        	    	// inform listener's address to server  
            		fputs( $connect, "RCPT TO: <{$email}>\r\n" );  

					// receive server's answering cord
					$to = fgets( $connect, 1024 );
			
        	    	// finish connection  
            		fputs( $connect, "QUIT\r\n" );  
	            	fclose( $connect );  

					// Server's answering cord about MAIL and TO command checks.  
					// Server about listener's address reacts to 550 codes if there does not exist   
					// checking that mailbox is in own E-Mail account.  
  					if ( !ereg( "^250", $from ) || !ereg( "^250", $to ) )
						return false;
        		}  
    		}
    		// failure in socket connection  
    		else
			{  
        		return false;
    		}
	
    		return true;
		}
		else
		{
			return $valid_email;
		}
    }
	
	/**
	 * @access public
	 */
	function is_blacklisted( $ip ) 
	{
    	$dnsbl_check = array(
			"bl.spamcop.net",
			"relays.osirusoft.com",
			"list.dsbl.org",
			"sbl.spamhaus.org"
		);
    
		if ( $ip ) 
		{
       		$quads = explode( ".", $ip );
        	$rip   = $quads[3] . "." . $quads[2] . "." . $quads[1] . "." . $quads[0];
        
			for ( $i = 0; $i < count( $dnsbl_check ); $i++ ) 
			{
            	if ( checkdnsrr( $rip . "." . $dnsbl_check[$i], "A" ) )
                	$listed .= $dnsbl_check[$i] . " ";
         	}
       
	   		if ( $listed ) 
				return $listed; 
			else 
				return false; 
    	}
	}
	 
	/**
	 * Validate a country code.
	 *
	 * Note: This is completely lame - i18n stuff which should be iso-ed.
	 *
     * @param   string  $country
     * @return  string
	 * @access  public
	 */
	function is_country( $country = "" )
	{
		if ( empty( $country ) || ( strlen( $country ) != 2 ) )
			return false;

		$country = strtoupper( $country );

		$countrycodes = array(			
			"ad" => "Andorra",
			"ae" => "United Arab Emirates",
			"af" => "Afghanistan",
			"ag" => "Antigua and Barbuda",
			"ai" => "Anguilla",
			"al" => "Albania",
			"am" => "Armenia",
			"an" => "Netherlands Antilles",
			"ao" => "Angola",
			"aq" => "Antarctica",
			"ar" => "Argentina",
			"as" => "American Samoa",
			"at" => "Austria",
			"au" => "Australia",
			"aw" => "Aruba",
			"az" => "Azerbaijan",
			"ba" => "Bosnia Hercegovina",
			"bb" => "Barbados",
			"bd" => "Bangladesh",
			"be" => "Belgium",
			"bf" => "Burkina Faso",
			"bg" => "Bulgaria",
			"bh" => "Bahrain",
			"bi" => "Burundi",
			"bj" => "Benin",
			"bm" => "Bermuda",
			"bn" => "Brunei Darussalam",
			"bo" => "Bolivia",
			"br" => "Brazil",
			"bs" => "Bahamas",
			"bt" => "Bhutan",
			"bv" => "Bouvet Island",
			"bw" => "Botswana",
			"by" => "Belarus (Byelorussia)",
			"bz" => "Belize",
			"ca" => "Canada",
			"cc" => "Cocos Islands",
			"cd" => "Congo, The Democratic Republic of the",
			"cf" => "Central African Republic",
			"cg" => "Congo",
			"ch" => "Switzerland",
			"ci" => "Ivory Coast",
			"ck" => "Cook Islands",
			"cl" => "Chile",
			"cm" => "Cameroon",
			"cn" => "China",
			"co" => "Colombia",
			"cr" => "Costa Rica",
			"cs" => "Czechoslovakia",
			"cu" => "Cuba",
			"cv" => "Cape Verde",
			"cx" => "Christmas Island",
			"cy" => "Cyprus",
			"cz" => "Czech Republic",
			"de" => "Germany",
			"dj" => "Djibouti",
			"dk" => "Denmark",
			"dm" => "Dominica",
			"do" =>	"Dominican Republic",
			"dz" => "Algeria",
			"ec" => "Ecuador",
			"ee" => "Estonia",
			"eg" => "Egypt",
			"eh" => "Western Sahara",
			"er" => "Eritrea",
			"es" => "Spain",
			"et" => "Ethiopia",
			"fi" => "Finland",
			"fj" => "Fiji",
			"fk" => "Falkland Islands",
			"fm" => "Micronesia",
			"fo" => "Faroe Islands",
			"fr" => "France",
			"fx" => "France, Metropolitan FX",
			"ga" => "Gabon",
			"gb" => 'United Kingdom (Great Britain)',
			"gd" => "Grenada",
			"ge" => "Georgia",
			"gf" => "French Guiana",
			"gh" => "Ghana",
			"gi" => "Gibraltar",
			"gl" => "Greenland",
			"gm" => "Gambia",
			"gn" => "Guinea",
			"gp" => "Guadeloupe",
			"gq" => "Equatorial Guinea",
			"gr" => "Greece",
			"gs" => "South Georgia and the South Sandwich Islands",
			"gt" => "Guatemala",
			"gu" => "Guam",
			"gw" => "Guinea-bissau",
			"gy" => "Guyana",
			"hk" => "Hong Kong",
			"hm" => "Heard and McDonald Islands",
			"hn" => "Honduras",
			"hr" => "Croatia",
			"ht" => "Haiti",
			"hu" => "Hungary",
			"id" => "Indonesia",
			"ie" => "Ireland",
			"il" => "Israel",
			"in" => "India",
			"io" => "British Indian Ocean Territory",
			"iq" => "Iraq",
			"ir" => "Iran",
			"is" => "Iceland",
			"it" => "Italy",
			"jm" => "Jamaica",
			"jo" => "Jordan",
			"jp" => "Japan",
			"ke" => "Kenya",
			"kg" => "Kyrgyzstan",
			"kh" => "Cambodia",
			"ki" => "Kiribati",
			"km" => "Comoros",
			"kn" => "Saint Kitts and Nevis",
			"kp" => "North Korea",
			"kr" => "South Korea",
			"kw" => "Kuwait",
			"ky" => "Cayman Islands",
			"kz" => "Kazakhstan",
			"la" => "Laos",
			"lb" => "Lebanon",
			"lc" => "Saint Lucia",
			"li" => "Lichtenstein",
			"lk" => "Sri Lanka",
			"lr" => "Liberia",
			"ls" => "Lesotho",
			"lt" => "Lithuania",
			"lu" => "Luxembourg",
			"lv" => "Latvia",
			"ly" => "Libya",
			"ma" => "Morocco",
			"mc" => "Monaco",
			"md" => "Moldova Republic",
			"mg" => "Madagascar",
			"mh" => "Marshall Islands",
			"mk" => 'Macedonia, The Former Yugoslav Republic of',
			"ml" => "Mali",
			"mm" => "Myanmar",
			"mn" => "Mongolia",
			"mo" => "Macau",
			"mp" => "Northern Mariana Islands",
			"mq" => "Martinique",
			"mr" => "Mauritania",
			"ms" => "Montserrat",
			"mt" => "Malta",
			"mu" => "Mauritius",
			"mv" => "Maldives",
			"mw" => "Malawi",
			"mx" => "Mexico",
			"my" => "Malaysia",
			"mz" => "Mozambique",
			"na" => "Namibia",
			"nc" => "New Caledonia",
			"ne" => "Niger",
			"nf" => "Norfolk Island",
			"ng" => "Nigeria",
			"ni" => "Nicaragua",
			"nl" => "Netherlands",
			"no" => "Norway",
			"np" => "Nepal",
			"nr" => "Nauru",
			"nt" => "Neutral Zone",
			"nu" => "Niue",
			"nz" => "New Zealand",
			"om" => "Oman",
			"pa" => "Panama",
			"pe" => "Peru",
			"pf" => "French Polynesia",
			"pg" => "Papua New Guinea",
			"ph" => "Philippines",
			"pk" => "Pakistan",
			"pl" => "Poland",
			"pm" => "St. Pierre and Miquelon",
			"pn" => "Pitcairn",
			"pr" => "Puerto Rico",
			"pt" => "Portugal",
			"pw" => "Palau",
			"py" => "Paraguay",
			"qa" => "Qatar",
			"re" => "Reunion",
			"ro" => "Romania",
			"ru" => "Russia",
			"rw" => "Rwanda",
			"sa" => "Saudi Arabia",
			"sb" => "Solomon Islands",
			"sc" => "Seychelles",
			"sd" => "Sudan",
			"se" => "Sweden",
			"sg" => "Singapore",
			"sh" => "St. Helena",
			"si" => "Slovenia",
			"sj" => "Svalbard and Jan Mayen Islands",
			"sk" => "Slovakia (Slovak Republic)",
			"sl" => "Sierra Leone",
			"sm" => "San Marino",
			"sn" => "Senegal",
			"so" => "Somalia",
			"sr" => "Suriname",
			"st" => "Sao Tome and Principe",
			"sv" => "El Salvador",
			"sy" => "Syria",
			"sz" => "Swaziland",
			"tc" => "Turks and Caicos Islands",
			"td" => "Chad",
			"tf" => "French Southern Territories",
			"tg" => "Togo",
			"th" => "Thailand",
			"tj" => "Tajikistan",
			"tk" => "Tokelau",
			"tm" => "Turkmenistan",
			"tn" => "Tunisia",
			"to" => "Tonga",
			"tp" => "East Timor",
			"tr" => "Turkey",
			"tt" => "Trinidad, Tobago",
			"tv" => "Tuvalu",
			"tw" => "Taiwan",
			"tz" => "Tanzania",
			"ua" => "Ukraine",
			"ug" => "Uganda",
			"uk" => "United Kingdom",
			"um" => "United States Minor Islands",
			"us" => "United States of America",
			"uy" => "Uruguay",
			"uz" => "Uzbekistan",
			"va" => "Vatican City",
			"vc" => "Saint Vincent, Grenadines",
			"ve" => "Venezuela",
			"vg" => "Virgin Islands (British)",
			"vi" => "Virgin Islands (USA)",
			"vn" => "Viet Nam",
			"vu" => "Vanuatu",
			"wf" => "Wallis and Futuna Islands",
			"ws" => "Samoa",
			"ye" => "Yemen",
			"yt" => "Mayotte",
			"yu" => "Yugoslavia",
			"za" => "South Africa",
			"zm" => "Zambia",
			"zr" => "Zaire",
			"zw" => "Zimbabwe"
		);

		return ( isset( $countrycodes[$countrycode] ) )? $countrycodes[$countrycode] : "";
	}
	
	/**
	 * @param  boolean   $bigsix_check
	 * @access public
	 */
	function is_bigsix( $tld )
	{
		if ( empty( $tld ) )
			return false;
		
		if ( eregi( "^\.", $tld ) )
			$tld = eregi_replace( "^\.", "", $tld );
		
		$bigsix = array(
			"com"  => "com",
			"edu"  => "edu",
			"net"  => "net",
			"org"  => "org",
			"biz"  => "biz",
			"info" => "info"
		);
		
		$tld = strtolower( $tld );

		if ( isset( $bigsix[$tld] ) )
			return true;

		return false;
	}
	
    /**
     * Validate an entire credit card.
     *
     * Use this function to validate an entire credit card based on the 
     * $options array. It checks for a valid number, valid type (ie. the type
     * and number match) and a valid expiry date.
     *
     * @param array $options   array where:
     *                         'number' is the credit card number
     *                         'month' is the expiration month
     *                         'year' is the expiration year
     *                         'type' is the credit card type 
     *
     * @return bool
	 * @access public
     */
    function is_creditcard( $options )
    {
        if ( is_array( $options ) ) 
		{
            extract( $options );

            return ( Validation::is_creditcard_luhn( $number ) &&
                     Validation::is_creditcard_expirydate( $month, $year ) &&
                     Validation::is_creditcard_type( $number, $type ) );
        }

        return false;
    }
	
    /**
     * Validate a number according to Luhn check algorithm.
     *
     * This function checks given number according Luhn check
     * algorithm. It is published on several places, also here:
     *
     *      http://www.webopedia.com/TERM/L/Luhn_formula.html
     *      http://www.merriampark.com/anatomycc.htm
     *      http://hysteria.sk/prielom/prielom-12.html#3 (Slovak language)
     *      http://www.speech.cs.cmu.edu/~sburke/pub/luhn_lib.html (Perl lib)
     *
     * @param  string  $number number (only numeric chars will be considered)
     * @return bool    true if number is valid, otherwise false
	 * @access public
     */
    function is_creditcard_luhn( $creditCard )
    {
        $creditCard = preg_replace( '/[^0-9]/', '',$creditCard );

        if ( empty( $creditCard ) || ( $len_number = strlen( $creditCard ) ) <= 0 )
            return false;
        
        $sum = 0;
        for ( $k = $len_number % 2; $k < $len_number; $k += 2 ) 
		{
            if ( ( intval( $creditCard{$k} ) * 2 ) > 9 )
                $sum += ( intval( $creditCard{$k}) * 2) - 9;
            else
                $sum += intval( $creditCard{$k} ) * 2;
        }
		
        for ( $k = ( $len_number % 2 ) ^ 1; $k < $len_number; $k += 2 )
            $sum += intval( $creditCard{$k} );
        
        return ( ( $sum % 10 )? false : true );
    }
	
    /**
     * Validate a credit card expiration date.
     *
     * Function checks to make sure that the month is between 1 and 12 and
     * then makes sure that the expiration month has not passed.
     *
     * @param int $month expiration month (eg. 01-12)
     * @param int $year expiration year (eg. 2004)
     * @return bool true if card has not expired, false otherwise
	 * @access public
     */
    function is_creditcard_expirydate( $month, $year )
    {
        $month_options = array(
			'min'     => 1,
			'max'     => 12,
			'decimal' => false
		);

        $year_options  = array(
			'min'     => date( "Y" ),
			'decimal' => false
		);

        if ( Validation::is_number( $month, $month_options ) && Validation::is_number( $year, $year_options ) ) 
		{
          	if ( ( $month >= date( "m" ) && $year == date( "Y" ) ) || ( $year > date( "Y" ) ) )
				return true;
        }
  
        return false;
    }

	/**
     * Validate a credit card type.
     *
     * Validate the credit card number against the given credit card type. 
     * For instance, check that a Discover Card starts with 6011.
     *
     * @param int $creditCard credit card number
     * @param int $type credit card type (see VALIDATION_CC_TYPE_*)
     * @return bool true if card number matches type, false otherwise
	 * @access public
     */
    function is_creditcard_type( $creditCard, $type )
    {
        switch ( $type ) 
		{
            case VALIDATION_CC_TYPE_MC:
                return ereg( '^5[1-5][0-9]{14}$', $creditCard );
            
			case VALIDATION_CC_TYPE_VS:
                return ereg( '^4[0-9]{12}([0-9]{3})?$', $creditCard );
				
            case VALIDATION_CC_TYPE_AX:
                return ereg( '^3[47][0-9]{13}$', $creditCard );
				
            case VALIDATION_CC_TYPE_DC:
                return ereg( '^3(0[0-5]|[68][0-9])[0-9]{11}$', $creditCard );
				
            case VALIDATION_CC_TYPE_DS:
                return ereg( '^6011[0-9]{12}$', $creditCard );
				
            case VALIDATION_CC_TYPE_JC:
                return ereg( '^(3[0-9]{4}|2131|1800)[0-9]{11}$', $creditCard );
				
            default:
                return false;
        }
    }

    /**
     * Run basic checking on a credit card's CVV2 number.
     *
     * This function does basic checking on a credit card's CVV2 number based
     * on the type passed. American Express CVV2 numbers are 4 digis, while
     * all the others are 3 digits. This function is not included in 
     * Validation::card().
     *
     * @param int $cvv2 Either a 3 or 4 digit number
     * @param int $type credit card type (see VALIDATION_CC_TYPE_*)
     * @return bool true if cvv2 number matches type, false otherwise
	 * @access public
     */
    function is_creditcard_cvv2( $cvv2, $type )
    {
        switch ( $type ) 
		{
            case VALIDATION_CC_TYPE_AX:
                $options = array(
					'min'     => 1000,
					'max'     => 9999,
					'decimal' => false
				);

                break;
            case VALIDATION_CC_TYPE_MC:

            case VALIDATION_CC_TYPE_VS:

            case VALIDATION_CC_TYPE_DC:

            case VALIDATION_CC_TYPE_DS:

            case VALIDATION_CC_TYPE_JC:
                $options = array(
					'min'     => 100,
					'max'     => 999,
					'decimal' => false
				);

                break;

            default:
                return false;
        }

        return Validation::is_number( $cvv2, $options );
    }
	
	
	// private methods
	
	/** 
	 * URL responds to requests?
	 * This will obviously fail if you're not connected to
	 * the internet, or if there are connection problems.
	 *
	 * @return bool
	 */
	function _url_responds( $url = "" )
	{
		if ( empty( $url ) )
			return false;

		$fd = @fopen( $url, "r" );
		
		if ( $fd )
		{
			@fclose( $fd );
			return true;
		}
		else
		{
			return false;
		}
	}
	
    /**
     * Validates a number.
     *
     * @param string $number number to validate
     * @param array $weights reference to array of weights
     * @param int $modulo (optionsl) number
     * @param int $subtract (optional) numbier
     * @return bool
     */
    function _check_control_number( $number, &$weights, $modulo = 10, $subtract = 0 ) 
	{
        if ( strlen( $number ) < count( $weights ) )
            return false;

        $target_digit  = substr( $number, count( $weights ), 1 );
        $control_digit = Validation::_get_control_number( $number, $weights, $modulo, $subtract, $target_digit === 'X' );

        if ( $control_digit == -1 )
            return false;

        if ( $target_digit === 'X' && $control_digit == 10 )
            return true;

        if ( $control_digit != $target_digit )
            return false;

        return true;
    }
	
    /**
     * Calculates control digit for a given number.
     *
     * @param string $number number string
     * @param array $weights reference to array of weights
     * @param int $modulo (optionsl) number
     * @param int $subtract (optional) number
     * @param bool $allow_high (optional) true if function can return number higher than 10
     * @return int -1 calculated control number is returned
     */
    function _get_control_number( $number, &$weights, $modulo = 10, $subtract = 0, $allow_high = false ) 
	{
        // calc sum
        $sum = Validation::_mult_weights( $number, $weights );

        if ($sum == -1)
            return -1;

		// calculate control digit
        $mod = Validation::_modf( $sum, $modulo );

        if ( $subtract > $mod )
            $mod = $subtract - $mod;

        if ( $allow_high === false )
          	$mod %= 10; // change 10 to zero
        
		return $mod;
    }
	
    function _modf( $val, $div ) 
	{
        if ( function_exists( 'bcmod' ) )
            return bcmod( $val,$div );
        else if ( function_exists( 'fmod' ) )
            return fmod( $val, $div );
        
        $r = $a / $b;
        $i = intval( $r );
		
        return intval( ( $r - $i ) * $b );
    }

    /**
     * Calculates sum of product of number digits with weights.
     *
     * @param string $number number string
     * @param array $weights reference to array of weights
     * @return int returns product of number digits with weights
     */
    function _mult_weights( $number, &$weights ) 
	{
        if ( !is_array( $weights ) )
            return -1;

        $sum   = 0;
        $count = min( count( $weights ), strlen( $number ) );
		
        if ( $count == 0 ) // empty string or weights array
            return -1;
			
        for ( $i = 0; $i < $count; ++$i )
            $sum += intval( substr( $number, $i, 1 ) ) * $weights[$i];

        return $sum;
    }
	
	/**
	 * @access private
	 */
    function _substr( &$date, $num, $opt = false )
    {
        if ( $opt && strlen( $date ) >= $opt && preg_match('/^[0-9]{' . $opt . '}/', $date, $m ) )
            $ret = $m[0];
        else
            $ret = substr( $date, 0, $num );
        
        $date = substr( $date, strlen( $ret ) );
        return $ret;
    }
} // END OF Validation

?>
