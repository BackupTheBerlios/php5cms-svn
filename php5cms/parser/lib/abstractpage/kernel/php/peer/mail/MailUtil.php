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


using( 'peer.mail.MailRFC822' );


/**
 * @package peer_mail
 */
 
class MailUtil
{
    /**
     * Take a set of recipients and parse them, returning an array of
     * bare addresses (forward paths) that can be passed to sendmail
     * or an smtp server with the rcpt to: command.
     *
     * @param mixed Either a comma-seperated list of recipients
     *              (RFC822 compliant), or an array of recipients,
     *              each RFC822 valid.
     *
     * @return array An array of forward paths (bare addresses).
     * @access private
	 * @static
     */
    function parseRecipients( $recipients )
    {
        // if we're passed an array, assume addresses are valid and
        // implode them before parsing.
        if ( is_array( $recipients ) )
            $recipients = implode( ', ', $recipients );

        // Parse recipients, leaving out all personal info. This is
        // for smtp recipients, etc. All relevant personal information
        // should already be in the headers.
        $addresses  = MailRFC822::parseAddressList( $recipients, 'localhost', false );
        $recipients = array();
        
		if ( is_array( $addresses ) ) 
		{
            foreach ( $addresses as $ob )
                $recipients[] = $ob->mailbox . '@' . $ob->host;
        }

        return $recipients;
    }
	
	/**
     * Implements mail function using php's built-in mail() command.
     * 
     * @param mixed $recipients Either a comma-seperated list of recipients
     *              (RFC822 compliant), or an array of recipients,
     *              each RFC822 valid. This may contain recipients not
     *              specified in the headers, for Bcc:, resending
     *              messages, etc.
     *
     * @param array $headers The array of headers to send with the mail, in an
     *              associative array, where the array key is the
     *              header name (ie, 'Subject'), and the array value
     *              is the header value (ie, 'test'). The header
     *              produced from those values would be 'Subject:
     *              test'.
     *
     * @param string $body The full text of the message body, including any
     *               Mime parts, etc.
     *
     * @return mixed Returns true on success, or a PEAR_Error
     *               containing a descriptive error message on
     *               failure.
	 *
     * @access public
	 * @static
     */	
    function send( $recipients, $headers, $body )
    {
        // if we're passed an array of recipients, implode it.
        if ( is_array( $recipients ) )
            $recipients = implode( ', ', $recipients );
        
        // get the Subject out of the headers array so that we can
        // pass it as a seperate argument to mail().
        $subject = '';
        
		if ( isset( $headers['Subject'] ) ) 
		{
            $subject = $headers['Subject'];
            unset( $headers['Subject'] );
        }
        
        // flatten the headers out.
        list(,$text_headers) = MailUtil::_prepareHeaders( $headers );
        
        return mail( $recipients, $subject, $body, $text_headers );
    }
	
	/**
	 * @access public
	 * @static
	 */
	function parse( $email ) 
	{
		do 
		{
			$pos = strpos( $email, '@' );
			
			if ( $pos === false ) 
				break;
			
			$user = substr( $email, 0, $pos  );
			$host = substr( $email, $pos + 1 );
			
			return array( $user, $host );
		} while ( false );
		
		return false;
	}

	/**
	 * @access public
	 * @static
	 */
	function toPronounceable( $email, $lang = 'en' ) 
	{
		$from = array( '@', '.', '_',  '-');
		
		if ( is_array( $lang ) ) 
		{
			$to = $lang;
		} 
		else 
		{
			switch ( $lang ) 
			{
				case 'de':
					$to = array( ' AT ', ' PUNKT ', ' UNTERSTRICH ', ' STRICH ' );
					break;
					
				case 'de2':
					$to = array( ' AFFENSCHWANZ ', ' PUNKT ', ' UNTERSTRICH ', ' MINUS ' );
					break;
				
				default:
					$to = array( ' AT ', ' DOT ', ' UNDERSCORE ', ' DASH ' );
			}
		}

		return str_replace( $from, $to, $email );
	}

	/**
	 * Use this method to return the email in message/rfc822 format.
	 * Useful for adding an email to another email as an attachment.
	 *
	 * @access public
	 * @static
	 */
	function _getRFC822( $to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = '' )
	{
		// make up the date header as according to RFC822
		$date = 'Date: ' . date( 'D, d M y H:i:s' );

		if ( $to_name != '' )
			$to = 'To: "' . $to_name . '" <' . $to_addr . '>';
		else
			$to = $to_addr;

		if ( $from_name != '' )
			$from = 'From: "' . $from_name . '" <' . $from_addr . '>';
		else
			$from = $from_addr;

		if ( is_string( $subject ) )
			$subject = 'Subject: ' . $subject;
		if ( is_string( $headers ) )
			$headers = explode( "\n", trim( $headers ) );
		
		for ( $i = 0; $i < count( $headers ); $i++ )
		{
			if ( is_array( $headers[$i] ) )
			{
				for ( $j = 0; $j < count( $headers[$i] ); $j++ )
				{
					if ( $headers[$i][$j] != '' )
						$xtra_headers[] = $headers[$i][$j];
				}
			}
                        		
			if ( $headers[$i] != '' )
				$xtra_headers[] = $headers[$i];
		}
				
		if ( !isset( $xtra_headers ) )
			$xtra_headers = array();

		return $date . "\n" . $from . "\n" . $to . "\n" . $subject . "\n" . implode( "\n", $headers ) . "\n" . implode( "\n", $xtra_headers ) . "\n\n" . $this->mime;
	}
	
	/**
	 * @access public
	 * @static
	 */
	function htmlEncode( $email ) 
	{
		return MailUtil::_charToHtml( $email );
	}
	
	
	// private methods

    /**
     * Take an array of mail headers and return a string containing
     * text usable in sending a message.
     *
     * @param array $headers The array of headers to prepare, in an associative
     *              array, where the array key is the header name (ie,
     *              'Subject'), and the array value is the header
     *              value (ie, 'test'). The header produced from those
     *              values would be 'Subject: test'.
     *
     * @return mixed Returns false if it encounters a bad address,
     *               otherwise returns an array containing two
     *               elements: Any From: address found in the headers,
     *               and the plain text version of the headers.
	 *
     * @access private
	 * @static
     */
    function _prepareHeaders( $headers )
    {
        // Look out for the From: value to use along the way.
        $text_headers = '';  // text representation of headers
        $from = null;

        foreach ( $headers as $key => $val ) 
		{
            if ( $key == 'From' ) 
			{
                $from_arr = MailRFC822::parseAddressList( $val, 'localhost', false );
                $from = $from_arr[0]->mailbox . '@' . $from_arr[0]->host;

                if ( strstr( $from, ' ' ) ) 
				{
                    // Reject outright envelope From addresses with spaces.
                    return false;
                }
				
                $text_headers .= $key . ': ' . $val . "\n";
            } 
			else if ( $key == 'Received' ) 
			{
                // put Received: headers at the top, since Receieved:
                // after Subject: in the header order is somtimes used
                // as a spam trap.
                $text_headers = $key . ': ' . $val . "\n" . $text_headers;
            } 
			else 
			{
                $text_headers .= $key . ': ' . $val . "\n";
            }
        }

        return array( $from, $text_headers );
    }
	
	/**
	 * @access private
	 * @static
	 */
	function _charToHtml( $param = '', $reverse = false ) 
	{
		if ( $param == '' ) 
			return $param;
			
		static $lookFor = array(
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", 
			"K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", 
			"U", "V", "W", "X", "Y", "Z",
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", 
			"k", "l", "m", "n", "o", "p", "q", "r", "s", "t", 
			"u", "v", "w", "x", "y", "z",
			"@"
		);
		
		static $replaceWith = null;
		
		if ( is_null( $replaceWith ) ) 
		{
			$replaceWith = array();
			$size = sizeOf( $lookFor );
			
			for ( $i = 0; $i < $size; $i++ ) 
				$replaceWith[$lookFor[$i]] = '&#' . ord( $lookFor[$i] ) . ';';
		}

		if ( $reverse ) 
		{
			$reverseReplace = array_flip( $replaceWith );
			return strtr( $param, $reverseReplace );
		}

		return strtr( $param, $replaceWith );
	}
} // END OF MailUtil

?>
