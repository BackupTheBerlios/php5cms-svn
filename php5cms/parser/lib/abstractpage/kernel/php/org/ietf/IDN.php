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
|         blue birdy <team@phlymail.de>                                |
+----------------------------------------------------------------------+
*/


/**
 * Encode/decode punycode based domain names.
 *
 * The class allows to convert internationalized domain names
 * (see RFC 3492 for details) as they can be used with various registries worldwide
 * to be translated between their original (localized) form and their encoded form
 * as it will be used in the DNS (Domain Name System).
 * 
 * The class provides two public methods, encode() and decode(), which do exactly
 * what you would expect them to do. You are allowed to use complete domain names,
 * simple strings and complete email addresses as well. That means, that you might
 * use any of the following notations:
 * 
 * - www.nörgler.com
 * - xn--nrgler-wxa
 * - xn--brse-5qa.xn--knrz-1ra.info
 * 
 * The methods expect strings as their input and will return you strings.
 *
 * C++ Original: Copyright (c) 2001, 2002 Japan Network Information Center.
 * This PHP class is derived work from the IDN extension for PHP, originally
 * written by JPNIC in C++.
 *
 * Since this original work closely implements the algorithms from RFC 3492
 * as this PHP code does, we consider it being just an add on for all of you
 * whose hosting provider refuses to use the original extension.
 *
 * Examples:
 *
 * 1. Say we wish to encode the domain name nörgler.com:
 * 
 * // Instantiate it
 * $idn = new IDN;
 * 
 * // The input string
 * $input = 'nörgler.com';
 * 
 * // Encode it to its punycode presentation
 * $output = $idn->encode( $input );
 * 
 * // Output, what we got now
 * echo $output; // This will read: xn--nrgler-wxa.com
 * 
 * 
 * 2. We received an email from a punycoded domain and are willing to learn, how
 *    the domain name reads originally
 * 
 * // Instantiate it
 * $idn = new IDN;
 * 
 * // The input string
 * $input = 'andre@xn--brse-5qa.xn--knrz-1ra.info';
 * 
 * // Encode it to its punycode presentation
 * $output = $idn->decode( $input );
 * 
 * // Output, what we got now
 * echo $output; // This will read: andre@börse.knürz.info
 *
 * @package org_ietf
 */

class IDN extends PEAR
{
	/**
	 * @access private
	 */
    var $punycode_prefix = 'xn--';
	
	/**
	 * @access private
	 */
    var $invalid_ucs = 0x80000000;
	
	/**
	 * @access private
	 */
    var $max_ucs = 0x10ffff;
	
	/**
	 * @access private
	 */
    var $punycode_base = 36;
	
	/**
	 * @access private
	 */
    var $punycode_tmin = 1;
	
	/**
	 * @access private
	 */
    var $punycode_tmax = 26;
	
	/**
	 * @access private
	 */
    var $punycode_skew = 38;
	
	/**
	 * @access private
	 */
    var $punycode_damp = 700;
	
	/**
	 * @access private
	 */
    var $punycode_initial_bias = 72;
	
	/**
	 * @access private
	 */
    var $punycode_initial_n = 0x80;
	
	/**
	 * @access private
	 */
    var $punycode_base36 = 'abcdefghijklmnopqrstuvwxyz0123456789';

	
    /** 
	 * Decode a given Domain name.
	 *
	 * @access public
	 */
    function decode( $encoded )
    {
        // Clean up input
        $encoded = trim( $encoded );
		
        // Call actual wrapper
        return $this->_process( $encoded, 'decode' );
    }

    /**
	 * Encode a given Domain name.
	 *
	 * @access public
	 */
    function encode( $decoded )
    {
        // Clean up input
        $decoded = preg_replace( '!ß!', 'ss', strtolower( trim( $decoded ) ) );

        // Call actual wrapper
        return $this->_process( $decoded, 'encode' );
    }
	
	
	// private methods
	
    /**
	 * Wrapper class to provide extended functionality.
     * This allows for processing complete email addresses and domain names.
	 *
	 * @access private
	 */
    function _process( $input, $mode )
    {
        $method = '_' . $mode;
        
		// Maybe it is an email address
        if ( strpos( $input, '@' ) ) 
		{
            list( $email_pref, $input ) = explode( '@', $input, 2 );
            $email_pref .= '@';
        } 
		else 
		{
            $email_pref = '';
        }
		
        // Process any substring
        $arr = explode( '.', $input );
		
        foreach( $arr as $k => $v ) 
		{
            $conv = $this->$method($v);
            
			if ( PEAR::isError( $conv ) )
				return $conv;
				
			$arr[$k] = $conv;
        }
		
        return $email_pref . join( '.', $arr );
    }

    /**
	 * The actual decoding algorithm.
	 *
	 * @access private
	 */
    function _decode( $encoded )
    {
        // We do need to find the Punycode prefix
        if (!preg_match('!^'.preg_quote($this->punycode_prefix, '!').'!', $encoded))
            return PEAR::raiseError( 'This is not a punycode string.');

        $encode_test = preg_replace( '!^' . preg_quote( $this->punycode_prefix, '!' ) . '!', '', $encoded );
		
        // If nothing left after removing the prefix, it is hopeless
        if ( !$encode_test )
            return PEAR::raiseError( 'The given encoded string was empty.' );

        // Find last occurence of the delimiter
        $delim_pos = strrpos( $encoded, '-' );	
        $decoded   = ( $delim_pos > strlen( $this->punycode_prefix ) )? substr( $encoded, strlen( $this->punycode_prefix ), ( $delim_pos - strlen( $this->punycode_prefix ) ) ) : '';
        $deco_len  = strlen( $decoded );
        $enco_len  = strlen( $encoded );

        // We need it later on, believe me
        $this->encoded = $encoded;

        // Wandering through the strings; init
        $is_first = true;
        $bias     = $this->punycode_initial_bias;
        $deco_idx = 0;
        $idx      = 0;
        $enco_idx = $delim_pos + 1;
        $char     = $this->punycode_initial_n;

        while ( $enco_idx < $enco_len ) 
		{
            $len = $this->_getwc( $enco_idx, $enco_len - $enco_idx, $bias, &$delta );
            
			if ( !$len )
                return PEAR::raiseError( 'Invalid encoding.' );
    
            $enco_idx += $len;
            $bias      = $this->_adapt( $delta, $deco_len + 1, $is_first );
            $is_first  = false;
            $idx      += $delta;
            $char     += ( $idx / ( $deco_len + 1 ) ) % 256;
            $deco_idx  = $idx % ( $deco_len + 1 );

            if ( $deco_len > 0 ) 
			{
                for ( $i = $deco_len; $i > $deco_idx; $i-- )
                    $decoded{$i} = $decoded{( $i - 1 )};
                
                $decoded{$deco_idx} = chr( $char );
            } 
			else 
			{
                $decoded = chr( $char );
            }
			
            $deco_len++;
            $idx = $deco_idx + 1;
        }
		
        return $decoded;
    }

    /**
	 * The actual encoding algorithm.
	 *
	 * @access private
	 */
    function _encode( $decoded )
    {
        // No empty strings please
        if ( !$decoded )
           	return PEAR::raiseError( 'The given decoded string was empty.' );

        // We cannot encode a domain name containing the Punycode prefix
        if ( preg_match( '!^' . preg_quote( $this->punycode_prefix, '!' ) . '!', $decoded ) )
           	return PEAR::raiseError( 'This is already a punycode string.' );

        // We will not try to encode string containing of basic code points only
        if ( !preg_match( '![\x80-\xff]!', $decoded ) )
            return PEAR::raiseError( 'The given string does not contain encodable chars.' );

        $deco_len  = strlen( $decoded );
        $codecount = 0; // How many chars have been consumed

        // Start with the prefix; copy it to output
        $encoded = $this->punycode_prefix;
        
		// Copy all basic code points to output
        for ( $i = 0; $i < $deco_len; ++$i ) 
		{
            if ( preg_match( '![0-9a-zA-Z-]!', $decoded{$i} ) ) 
			{
                $encoded .= $decoded{$i};
                $codecount++;
            }
        }
		
        // If we have basic code points in output, add an hyphen to the end
        if ( $codecount ) 
			$encoded .= '-';

        // Now find and encode all non-basic code points
        $is_first  = true;
        $cur_code  = $this->punycode_initial_n;
        $bias      = $this->punycode_initial_bias;
        $delta     = 0;
		
        while ( $codecount < $deco_len ) 
		{
            $limit     = -1;
            $rest      =  0;
            $enco_len  = strlen( $encoded );
            $next_code = $this->max_ucs;
			
            // Find the smallest code point >= the current code point and
            // remember the last ouccrence of it in the input
            for ( $i = $deco_len - 1; $i >= 0; $i-- ) 
			{
                if ( ord( $decoded{$i} ) >= $cur_code && ord( $decoded{$i} ) <= $next_code ) 
				{
                    $next_code = ord( $decoded{$i} );
                    $limit     = $i;
                }
            }
			
            // There must be such code point.
            if ( !( $limit + 1 ) )
                return PEAR::raiseError( 'Codepoint out of range.' );

            $delta    += ( $next_code - $cur_code ) * ( $codecount + 1 );
            $cur_code  = $next_code;

            // Scan input again and encode all characters whose code point is $cur_code
            for ( $i = 0, $rest = $codecount; $i < $deco_len; $i++ ) 
			{
				if ( ord( $decoded{$i} ) < $cur_code ) 
				{
					$delta++;
					$rest--;
				} 
				else if ( ord( $decoded{$i} ) == $cur_code ) 
				{
					$sz = $this->_putwc( $enco_len, $delta, $bias );
					
					if ( !$sz )
						return PEAR::raiseError( 'Invalid input string, cannot encode it.' );
                        
					$encoded  .= $sz;
					
					$codecount++;
					
					$bias      = $this->_adapt( $delta, $codecount, $is_first );
					$delta     = 0;
					$is_first  = false;
				}
            }
			
            $delta += $rest + 1;
            $cur_code++;
        }
		
        return $encoded;
    }

    /**
	 * Convert Delta and Bias back to char and position.
	 *
	 * @access private
	 */
    function _getwc( $char, $len, $bias, &$delta )
    {
        $orglen = $len;
        $v = 0;
        $w = 1;
        
		for ( $k = $this->punycode_base - $bias; $len > 0; $k += $this->punycode_base ) 
		{
            $c = ord( $this->encoded{$char} );
            ++$char;
            $t = ( $k < $this->punycode_tmin )? $this->punycode_tmin : ( ( $k > $this->punycode_tmax)? $this->punycode_tmax : $k );
            $len--;
			
            if ( ord( 'a' ) <= $c && $c <= ord( 'z' ) ) 
                $c = $c - ord( 'a' );
			else if ( ord( 'A' ) <= $c && $c <= ord( 'Z' ) ) 
                $c = $c - ord( 'A' );
			else if ( ord( '0' ) <= $c && $c <= ord( '9' ) ) 
                $c = $c - ord( '0' ) + 26;
			else 
                $c = -1;
			
            if ( $c < 0 ) 
				return false; // invalid character
            
			$v += $c * $w;
            
			if ( $c < $t ) 
			{
                $delta = $v;
                return ( $orglen - $len );
            }
			
            $w  = $w * ( $this->punycode_base - $t );
        }
		
        return false; // final character missing
    }

    /**
	 * Convert char and position to base36 string.
	 *
	 * @access private
	 */
    function _putwc( $len, $delta, $bias )
    {
        $return = '';
        
		for ( $k = $this->punycode_base - $bias; 1; $k += $this->punycode_base ) 
		{
            $t = ( $k < $this->punycode_tmin )? $this->punycode_tmin : ( ( $k > $this->punycode_tmax )? $this->punycode_tmax : $k );

            if ( $delta < $t ) 
				break;
            
			if ( $len < 1 ) 
				return false;
            
			$add = ( $t + ( ( $delta - $t ) % ( $this->punycode_base - $t ) ) );
            $return .= $this->punycode_base36{$add};
            $len--;
            $delta = ( $delta - $t ) / ( $this->punycode_base - $t );
        }
		
        if ( $len < 1 ) 
			return false;
        
		$add = $delta;
        $return .= $this->punycode_base36{$add};
        
		return $return;
    }

    /**
	 * Adapt the bias according to the current code point and position.
	 *
	 * @access private
	 */
    function _adapt( $delta, $npoints, $is_first )
    {
        $k = 0;
        $delta  = $is_first? ( $delta / $this->punycode_damp ) : ( $delta / 2 );
        $delta += $delta / $npoints;
		
        while ( $delta > ( ( $this->punycode_base - $this->punycode_tmin ) * $this->punycode_tmax ) / 2 ) 
		{
            $delta = $delta / ( $this->punycode_base - $this->punycode_tmin );
            $k++;
        }
		
        return ( $this->punycode_base * $k + ( ( ( $this->punycode_base - $this->punycode_tmin + 1 ) * $delta ) / ( $delta + $this->punycode_skew ) ) );
    }
} // END OF IDN

?>
