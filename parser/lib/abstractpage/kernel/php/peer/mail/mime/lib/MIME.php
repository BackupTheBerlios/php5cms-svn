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
|Authors: Chuck Hagenbuch <chuck@horde.org>                            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'util.text.StringUtil' );
using( 'peer.mail.MailRFC822' );
using( 'util.Util' );


/* We need to (unfortunately) hard code these constants because they reside in
   the imap module, which is not required for Abstractpage.
   These constants are found in the UW-imap c-client distribution:
   ftp://ftp.cac.washington.edu/imap/
   The constants appear in the file include/mail.h */
if ( !Util::extensionExists( 'imap' ) ) 
{
	/* Primary body types */
    define( 'TYPETEXT',           0 );
    define( 'TYPEMULTIPART',      1 );
    define( 'TYPEMESSAGE',        2 );
    define( 'TYPEAPPLICATION',    3 );
    define( 'TYPEAUDIO',          4 );
    define( 'TYPEIMAGE',          5 );
    define( 'TYPEVIDEO',          6 );
    define( 'TYPEOTHER',          8 );

    /* Body encodings */
    define( 'ENC7BIT',            0 );
    define( 'ENC8BIT',            1 );
    define( 'ENCBINARY',          2 );
    define( 'ENCBASE64',          3 );
    define( 'ENCQUOTEDPRINTABLE', 4 );
    define( 'ENCOTHER',           5 );
}

/* Older versions of PHP's imap extension don't define TYPEMODEL. */
if ( !defined( 'TYPEMODEL' ) )
    define('TYPEMODEL', 7);

/** 
 * eturn a code for type()/encoding(). 
 */
define( 'MIME_CODE', 1 );

/** 
 * Return a string for type()/encoding(). 
 */
define( 'MIME_STRING', 2 );


/**
 * The MIME class provides methods for dealing with MIME standards.
 *
 * @package peer_mail_mime_lib
 */
 
class MIME extends PEAR
{
    /**
     * A listing of the allowed MIME types.
     *
     * @var array $mime_types
     */
    var $mime_types = array(
        TYPETEXT        => 'text',
        TYPEMULTIPART   => 'multipart',
        TYPEMESSAGE     => 'message',
        TYPEAPPLICATION => 'application',
        TYPEAUDIO       => 'audio',
        TYPEIMAGE       => 'image',
        TYPEVIDEO       => 'video',
        TYPEMODEL       => 'model',
        TYPEOTHER       => 'other'
    );

    /**
     * A listing of the allowed MIME encodings.
     *
     * @var array $mime_encodings
     */
    var $mime_encodings = array(
        ENC7BIT            => '7bit',
        ENC8BIT            => '8bit',
        ENCBINARY          => 'binary',
        ENCBASE64          => 'base64',
        ENCQUOTEDPRINTABLE => 'quoted-printable',
        ENCOTHER           => 'unknown'
    );

    /**
     * Filter for RFC822.
     *
     * @var array $rfc822_filter
     */
    var $rfc822_filter = "()<>@,;:\\\"[]\1\2\3\4\5\6\7\10\11\12\13\14\15\16\17\20\21\22\23\24\25\26\27\30\31\32\33\34\35\36\37\177";
	
	
    /**
     * Determine if a string contains 8-bit characters.
     *
     * @access public
     *
     * @param string $string  The string to check.
     *
     * @return boolean  True if it does, false if it doesn't.
     */
    function is8bit( $string )
    {
        if ( is_string( $string ) && preg_match( '/[\x80-\xff]+/', $string ) )
            return true;
        else
            return false;
    }

    /**
     * Encode a string containing non-ASCII characters according to RFC 2047.
     *
     * @access public
     *
     * @param string $text     The text to encode.
     * @param string $charset  (optional) The character set of the text.
     *
     * @return string  The text, encoded only if it contains non-ASCII
     *                 characters.
     */
    function encode( $text, $charset = null )
    {
        /* Return if nothing needs to be encoded. */
        if ( !MIME::is8bit( $text ) )
            return $text;

        $charset = StringUtil::lower( $charset );
        $line    = '';

        /* Get the list of elements in the string. */
        $size = preg_match_all( "/([^\s]+)([\s]*)/", $text, $matches, PREG_SET_ORDER );

        foreach ( $matches as $key => $val ) 
		{
            if ( MIME::is8bit( $val[1] ) ) 
			{
                if ( ( ( $key + 1 ) < $size ) && MIME::is8bit( $matches[$key + 1][1] ) )
                    $line .= MIME::_encode( $val[1] . $val[2], $charset ) . ' ';
                else
                    $line .= MIME::_encode( $val[1], $charset ) . $val[2];
            } 
			else 
			{
                $line .= $val[1] . $val[2];
            }
        }

        return rtrim( $line );
    }

    /**
     * Encode a string containing email addresses according to RFC 2047.
     *
     * @access public
     *
     * This differs from MIME::encode() because it keeps email
     * addresses legal, only encoding the personal information.
     *
     * @param string $text                The email addresses to encode.
     * @param optional string $charset    The character set of the text.
     * @param optional string $defserver  The default domain to append to
     *                                    mailboxes.
     *
     * @return string  The text, encoded only if it contains non-ascii
     *                 characters.
     */
    function encodeAddress( $text, $charset = null, $defserver = null )
    {
        /* parseAddressList() does not process the null entry
           'undisclosed-recipients:;' correctly. */
        if ( preg_match( "/undisclosed-recipients:\s*;/i", trim( $text ) ) )
            return $text;

        $parser   = &new MailRFC822();
        $addr_arr = $parser->parseAddressList( $text, $defserver, false, false );
        $text     = '';

        if ( is_array( $addr_arr ) ) 
		{
            foreach ( $addr_arr as $addr ) 
			{
                if ( empty( $addr->personal ) ) 
				{
                    $personal = '';
                } 
				else 
				{
                    if ( ( substr( $addr->personal, 0, 1 ) == '"' ) && ( substr( $addr->personal, -1 ) == '"' ) )
                        $addr->personal = substr( $addr->personal, 1, -1 );
                    
                    $personal = MIME::encode( $addr->personal, $charset );
                }
				
                if ( strlen( $text ) != 0 )
                    $text .= ', ';
                
                $text .= MIME::trimEmailAddress( MIME::rfc822WriteAddress( $addr->mailbox, $addr->host, $personal ) );
            }
        }

        return $text;
    }

    /**
     * Decode an RFC 2047-encoded string.
     *
     * @access public
     *
     * @param string $string  The text to decode.
     *
     * @return string  The decoded text, or the original string if it was not
     *                 encoded.
     */
    function decode( $string, $charset )
    {
        if ( ( $pos = strpos( $string, '=?' ) ) === false )
            return $string;

        /* Take out any spaces between multiple encoded words. */
        $string = preg_replace( '|\?=\s=\?|', '?==?', $string );

        /* Save any preceding text. */
        $preceding = substr( $string, 0, $pos );

        $search = substr( $string, $pos + 2 );
        $d1 = strpos( $search, '?' );
        
		if ( !is_int( $d1 ) )
            return $string;

        $charset = substr( $string, $pos + 2, $d1 );
        $search  = substr( $search, $d1  + 1 );

        $d2 = strpos( $search, '?' );
		
        if ( !is_int( $d2 ) )
            return $string;

        $encoding = substr( $search, 0, $d2  );
        $search   = substr( $search, $d2 + 1 );

        $end = strpos( $search, '?=' );
		
        if ( !is_int( $end ) )
            $end = strlen( $search );

        $encoded_text = substr( $search, 0, $end );
        $rest = substr( $string, ( strlen( $preceding . $charset . $encoding . $encoded_text ) + 6 ) );

        switch ( $encoding ) 
		{
	        case 'Q':
    
		    case 'q':
            	$encoded_text = str_replace( '_', '%20', $encoded_text );
            	$encoded_text = str_replace( '=', '%',   $encoded_text );
            	$decoded = urldecode( $encoded_text );
            	$decoded = StringUtil::convertCharset( $decoded, $charset, $charset );
            
				break;

	        case 'B':
    
		    case 'b':
        	    $decoded = urldecode( base64_decode( $encoded_text ) );
            	$decoded = StringUtil::convertCharset( $decoded, $charset, $charset );
            
				break;

        	default:
            	$decoded = '=?' . $charset . '?' . $encoding . '?' . $encoded_text . '?=';
            	break;
        }

        return $preceding . $decoded . MIME::decode( $rest );
    }

    /**
     * If an email address has no personal information, get rid of any
     * angle brackets (<>) around it.
     *
     * @access public
     *
     * @param string $address  The address to trim.
     *
     * @return string  The trimmed address.
     */
    function trimEmailAddress( $address )
    {
        $address = trim( $address );

        if ( ( substr( $address, 0, 1 ) == '<' ) && ( substr( $address, -1 ) == '>' ) )
            $address = substr( $address, 1, -1 );

        return $address;
    }

    /**
     * Builds an RFC 822 compliant email address.
     *
     * @access public
     *
     * @param string $mailbox            Mailbox name.
     * @param optional string $host      Domain name of mailbox's host.
     * @param optional string $personal  Personal name phrase.
     *
     * @return string  The correctly escaped and quoted
     *                 "$personal <$mailbox@$host>" string.
     */
    function rfc822WriteAddress( $mailbox, $host = null, $personal = '' )
    {
        $address = '';

        if ( !empty( $personal ) ) 
		{
            $vars     = get_class_vars( 'MIME' );
            $address .= MIME::_rfc822Encode( $personal, $vars['rfc822_filter'] . '.' );
            $address .= ' <';
        }

        if ( !is_null( $host ) ) 
		{
            $address .= MIME::_rfc822Encode( $mailbox );
			
            if ( substr( $host, 0, 1 ) != '@' )
                $address .= '@' . $host;
        }

        if ( !empty( $personal ) )
            $address .= '>';

        return $address;
    }

    /**
     * Explodes a RFC 822 string, ignoring a delimiter if preceded by
     * a "\" character, or if delimiter is inside single or double
     * quotes.
     *
     * @access public
     *
     * @param string $str  The RFC 822 string.
     * @param string $delimiter  The delimter.
     *
     * @return array  The exploded string in an array.
     */
    function rfc822Explode( $str, $delimiter )
    {
        $arr      = array();
        $match    = 0;
        $quotes   = array( '"', "'" );
        $in_quote = null;
        $prev     = null;

        if ( in_array( $str[0], $quotes ) ) 
		{
            $in_quote = $str[0];
        } 
		else if ( $str[0] == $delimiter ) 
		{
            $arr[] = '';
            $match = 1;
        }

        for ( $i = 1; $i < strlen( $str ); $i++ ) 
		{
            $char = $str[$i];
            
			if ( in_array( $char, $quotes ) ) 
			{
                if ( $prev !== "\\" ) 
				{
                    if ( $in_quote === $char )
                        $in_quote = null;
                    else if ( is_null( $in_quote ) )
                        $in_quote = $char;
                }
            } 
			else if ( ( $char == $delimiter ) && ( $prev !== "\\" ) && is_null( $in_quote ) ) 
			{
                $arr[] = substr( $str, $match, $i - $match );
                $match = $i + 1;
            }
			
            $prev = $char;
        }

        if ( $match != $i ) 
		{
            /* The string ended without a $delimiter. */
            $arr[] = substr( $str, $match, $i - $match );
        }

        return $arr;
    }

    /**
     * Takes an address object, as returned by imap_header() for example,
     * and formats it as a string.
     *
     * Object Format
     * For the address: John Doe <john_doe@test.com>
     * The object fields are:
     * <pre>
     *   $object->personal = Personal name ("John Doe")
     *   $object->mailbox  = The user's mailbox ("john_doe")
     *   $object->host     = The host the mailbox is on ("example.com")
     * </pre>
     *
     * @access public
     *
     * @param object stdClass $ob     The address object to be turned into a
     *                                string.
     * @param optional mixed $filter  A user@test.com style bare address
     *                                to ignore. Either single string or an
     *                                array of strings. If the address matches
     *                                $filter, an empty string will be returned.
     *
     * @return string  The formatted address (Example: John Doe
     *                 <john_doe@test.com>).
     */
    function addrObject2String( $ob, $filter = '' )
    {
        /* If the personal name is set, decode it. */
        $ob->personal = isset( $ob->personal )? MIME::decode( $ob->personal ) : '';

        /* If both the mailbox and the host are empty, return an empty
           string.  If we just let this case fall through, the call to
           MIME::rfc822WriteAddress() will end up return just a '@', which
           is undesirable. */
        if ( empty( $ob->mailbox ) && empty( $ob->host ) )
            return '';

        /* Make sure these two variables have some sort of value. */
        if ( !isset( $ob->mailbox ) )
            $ob->mailbox = '';
        else if ( $ob->mailbox == 'undisclosed-recipients' )
            return '';
        
        if ( !isset( $ob->host ) )
            $ob->host = '';

        /* Filter out unwanted addresses based on the $filter string. */
        if ( $filter ) 
		{
            if ( !is_array( $filter ) )
                $filter = array( $filter );
            
            foreach ( $filter as $f ) 
			{
                if ( strcasecmp( $f, $ob->mailbox . '@' . $ob->host ) == 0 )
                    return '';
            }
        }

        /* Return the trimmed, formatted email address. */
        return MIME::trimEmailAddress( MIME::rfc822WriteAddress( $ob->mailbox, $ob->host, $ob->personal ) );
    }

    /**
     * Takes an array of address objects, as returned by imap_headerinfo(),
     * for example, and passes each of them through MIME::addrObject2String().
     *
     * @access public
     *
     * @param array $addresses        The array of address objects.
     * @param optional mixed $filter  A user@test.com style bare address
     *                                to ignore. If any address matches
     *                                $filter, it will not be included in the
     *                                final string.
     *
     * @return string  All of the addresses in a comma-delimited string.
     *                 Returns the empty string on error/no addresses found.
     */
    function addrArray2String( $addresses, $filter = '' )
    {
        $addrList = array();

        if ( !is_array( $addresses ) )
            return '';

        foreach ( $addresses as $addr ) 
		{
            $val = MIME::addrObject2String( $addr, $filter );
			
            if ( !empty( $val ) ) 
			{
                $bareAddr = StringUtil::lower( MIME::bareAddress( $val ) );
				
                if ( !array_key_exists( $bareAddr, $addrList ) )
                    $addrList[$bareAddr] = $val;
            }
        }

        if ( empty( $addrList ) )
            return '';
        else
            return implode( ', ', $addrList );
    }

    /**
     * Returns the bare address.
     *
     * @access public
     *
     * @param string $address             The address string.
     * @param optional string $defserver  The default domain to append to
     *                                    mailboxes.
     * @param optional boolean $multiple  Should we return multiple results?
     *
     * @return mixed  If $multiple is false, returns the mailbox@host e-mail
     *                address. If $multiple is true, returns an array of
     *                these addresses.
     */
    function bareAddress( $address, $defserver = null, $multiple = false )
    {
        $addressList = array();

        /* Use built-in IMAP function if available (for speed). */
        if ( Util::extensionExists( 'imap' ) ) 
            $from = imap_rfc822_parse_adrlist( $address, $defserver );
		else 
            $from = MailRFC822::parseAddressList( $address, $defserver, false, false );

        foreach ( $from as $entry ) 
		{
            if ( isset( $entry->mailbox ) && isset( $entry->host ) )
                $addressList[] = $entry->mailbox . '@' . $entry->host;
            else if ( isset( $entry->mailbox ) )
                $addressList[] = $entry->mailbox;
        }

        if ( $multiple )
            return $addressList;
        else
            return array_pop( $addressList );
    }

    /**
     * Get the MIME type for the given input.
     *
     * @access public
     *
     * @param mixed $input              Either the MIME code or type string.
     * @param optional integer $format  If MIME_CODE, return code.
     *                                  If MIME_STRING, returns lowercase
     *                                  string.
     *
     * @return mixed  See above.
     */
    function type( $input, $format = null )
    {
        return MIME::_getCode( $input, $format, 'mime_types' );
    }

    /**
     * Get the MIME encoding for the given input.
     *
     * @access public
     *
     * @param mixed $input              Either the MIME code or encoding
     *                                  string.
     * @param optional integer $format  If MIME_CODE, return code. If
     *                                  MIME_STRING, returns lowercase
     *                                  string. If not set, returns the
     *                                  opposite value.
     *
     * @return mixed  See above.
     */
    function encoding( $input, $format = null )
    {
        return MIME::_getCode( $input, $format, 'mime_encodings' );
    }

    /**
     * Generate a Message-ID string conforming to RFC 2822 [3.6.4].
     *
     * @access public
     *
     * @param string  A message ID string.
     */
    function generateMessageID()
    {
        return '<' . time() . '.' . base_convert( microtime(), 10, 36 ) . '@' . $_SERVER['SERVER_NAME'] . '>';
    }

    /**
     * Add proper linebreaks to a header string.
     * RFC 2822 [2.2.3] says that headers SHOULD be wrapped at 78 characters.
     *
     * @access public
     *
     * @param string $header        The header name.
     * @param string $text          The text of the header field.
     * @param optional string $eol  The EOL string to use.
     *
     * @return string  The header text, with linebreaks inserted.
     */
    function wrapHeaders( $header, $text, $eol = "\r\n" )
    {
        /* Remove any existing linebreaks. */
        $text = preg_replace( "/\r?\n\s?/", '', $text );

        /* Wrap the line. */
        $line = wordwrap( rtrim( $header ) . ': ' . rtrim( $text ), 75, $eol . "\t" );

        /* Make sure there are no empty lines. */
        $line = preg_replace( "/" . $eol . "\t\s*" . $eol . "\t/", "/" . $eol . "\t/", $line );

        return substr( $line, strlen( $header ) + 2 );
    }
	
	
	// private methods
	
    /**
     * Internal recursive function to RFC 2047 encode a string.
     *
     * @access private
     *
     * @param string $text     The text to encode.
     * @param string $charset  The character set of the text.
     *
     * @return string  The text, encoded only if it contains non-ASCII
     *                 characters.
     */
    function _encode( $text, $charset )
    {
        $char_len = strlen( $charset );
        $txt_len  = strlen( $text ) * 2;

        /* RFC 2047 [2] states that no encoded word can be more than 75
           characters long. If longer, you must split the word. */
        if ( ( $txt_len + $char_len + 7 ) > 75 ) 
		{
            $pos = intval( ( 68 - $char_len ) / 2 );
            return MIME::_encode( substr( $text, 0, $pos ), $charset ) . ' ' . MIME::_encode( substr( $text, $pos ), $charset );
        } 
		else 
		{
            return '=?' . $charset . '?b?' . trim( base64_encode( $text ) ) . '?=';
        }
    }
	
    /**
     * Quotes and escapes the given string if necessary.
     *
     * @access private
     *
     * @param string $str              The string to be quoted and escaped.
     * @param optional string $filter  A list of characters that make it
     *                                 necessary to quote the string if they
     *                                 occur.
     *
     * @return string  The correctly quoted and escaped string.
     */
    function _rfc822Encode( $str, $filter = '' )
    {
        if ( empty( $filter ) ) 
		{
            $vars   = get_class_vars( 'MIME' );
            $filter = $vars['rfc822_filter'] . ' ';
        }

        if ( strcspn( $str, $filter ) != strlen( $str ) )
            return '"' . str_replace( '"', '\\"', str_replace( '\\', '\\\\', $str ) ) . '"';
        else
            return $str;
    }
	
    /**
     * Retrieves MIME encoding/type data from the internal arrays.
     *
     * @access private
     *
     * @param mixed $input    Either the MIME code or encoding string.
     * @param string $format  If 'code', returns code.
     *                        If 'string', returns lowercase string.
     *                        If empty set, return the oppposite value.
     * @param string $type    The name of the internal array.
     *
     * @return mixed  See above.
     */
    function _getCode( $input, $format, $type )
    {
        $numeric = is_numeric( $input );

        switch ( $format ) 
		{
        	case MIME_CODE:
            	if ( $numeric ) 
					return $input;
            
				break;

        	case MIME_STRING:
            	if ( !$numeric ) 
					return StringUtil::lower( $input );
            
				break;
        }

        $vars = get_class_vars( 'MIME' );

        if ( $numeric ) 
		{
            if ( array_key_exists( $input, $vars[$type] ) )
                return StringUtil::lower( $vars[$type][$input] );
        } 
		else 
		{
            if ( ( $search = array_search( $input, $vars[$type] ) ) )
                return $search;
        }

        return null;
    }
} // END OF MIME

?>
