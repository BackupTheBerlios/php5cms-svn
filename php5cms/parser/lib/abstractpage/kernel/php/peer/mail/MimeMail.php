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
 * @package peer_mail
 */
 
class MimeMail extends PEAR
{
	/**
	 * @access public
	 */
	var $mime;
	
	/**
	 * @access public
	 */
	var $html;
	
	/**
	 * @access public
	 */
	var $body;
	
	/**
	 * @access public
	 */
	var $do_html;
	
	/**
	 * @access public
	 */
	var $multipart;
	
	/**
	 * @access public
	 */
	var $html_text;
	
	/**
	 * @access public
	 */
	var $html_images;
	
	/**
	 * @access public
	 */
	var $headers;
	
	/**
	 * @access public
	 */
	var $parts;
	
	/**
	 * @access public
	 */
	var $charset;
	
	/**
	 * @access public
	 */
	var $charsetlist;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MimeMail( $headers = '' )
	{
		$this->html_images = array();
		$this->headers     = array();
		$this->parts       = array();
		
		$this->charsetlist = array(
			'iso'  => 'us-ascii',
			'big5' => 'big5',
			'gb'   => 'gb2312'
		);

		$this->charset = 'us-ascii';

		if ( $headers == '' )
			return true;
		
		if ( is_string( $headers ) )
			$headers = explode( "\n", trim( $headers ) );

		for ( $i = 0; $i < count( $headers ); $i++ )
		{
			if ( is_array( $headers[$i] ) )
			{
				for ( $j = 0; $j < count( $headers[$i] ); $j++ )
				{
					if ( $headers[$i][$j] != '' )
						$this->headers[] = $headers[$i][$j];
				}
			}
			
			if ( $headers[$i] != '')
				$this->headers[] = $headers[$i];
		}
	}

	
	/**
	 * Accessor function to set the body text. Body text is used if it's not an html mail being sent.
	 *
	 * @access public
	 */
	function setBody( $text = '' )
	{
		if ( is_string( $text ) )
		{
			$this->body = $text;
			return true;
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function getMime()
	{
		if ( !isset( $this->mime ) )
			$this->mime = '';
		
		return $this->mime;
	}

	/**
	 * Function to set a header. Shouldn't really be necessary as you could use the constructor and send functions,
	 * it's here nonetheless. Takes any number of arguments, which can be either strings or arrays full of strings.
	 * This function is php4 only and will return false otherwise. Will return true upon finishing.
	 *
	 * @access public
	 */
	function addHeader()
	{
		$args = func_get_args();
		
		for ( $i = 0; $i < count( $args ); $i++ )
		{
			if ( is_array( $args[$i] ) )
			{
				for ( $j = 0; $j < count( $args[$i] ); $j++ )
				{
					if ( $args[$i][$j] != '' )
						$this->headers[] = $args[$i][$j];
				}
			}
            
			if ( $args[$i] != '')
				$this->headers[] = $args[$i];
		}

		return true;
	}

	/**
	 * @access public
	 */
	function setCharset( $charset = '', $raw = false )
	{
		if ( $raw == true )
		{
			$this->charset = $charset;
			return true;
		}

		if ( is_string( $charset ) )
		{
			while ( list( $k, $v ) = each( $this->charsetlist ) )
			{
				if ( $k == $charset )
				{
					$this->charset = $v;
					return true;
				}
			}
 		}
            
		return false;
	}

	/**
	 * Adds a html part to the mail. Also replaces image names with content-id's.
	 *
	 * @access public
	 */
	function addHTML( $html, $text )
	{
		$this->do_html   = 1;
		$this->html      = $html;
		$this->html_text = $text;
                
		if ( is_array( $this->html_images ) && count( $this->html_images ) > 0 )
		{
			for ( $i = 0; $i < count( $this->html_images ); $i++ )
				$this->html = ereg_replace( $this->html_images[$i]['name'], 'cid:' . $this->html_images[$i]['cid'], $this->html );
		}
	}

	/**
	 * Builds html part of email.
	 *
	 * @access public
	 */
	function buildHTML( $orig_boundary )
	{
		$sec_boundary = '=_' . md5( uniqid( time() ) );
		$thr_boundary = '=_' . md5( uniqid( time() ) );

		if ( count( $this->html_images ) == 0 )
		{
			$this->multipart .= '--' . $orig_boundary . "\n";
			$this->multipart .= 'Content-Type: multipart/alternative;' . chr( 10 ) . chr( 9 ) . 'boundary="' . $sec_boundary . "\"\n\n\n";

			$this->multipart .= '--' . $sec_boundary . "\n";
			$this->multipart .= 'Content-Type: text/plain; charset="' . $this->charset . '"'."\n";
			$this->multipart .= 'Content-Transfer-Encoding: base64' . "\n\n";
			$this->multipart .= chunk_split( base64_encode( $this->html_text ) ) . "\n\n";

			$this->multipart .= '--' . $sec_boundary . "\n";
			$this->multipart .= 'Content-Type: text/html; charset="' . $this->charset . '"'."\n";
			$this->multipart .= 'Content-Transfer-Encoding: base64' . "\n\n";
			$this->multipart .= chunk_split( base64_encode( $this->html ) ) . "\n\n";
			$this->multipart .= '--' . $sec_boundary . "--\n\n";
		}
		else
		{
			$this->multipart .= '--' . $orig_boundary . "\n";
			$this->multipart .= 'Content-Type: multipart/related;' . chr( 10 ) . chr( 9 ) . 'boundary="' . $sec_boundary . "\"\n\n\n";

			$this->multipart .= '--' . $sec_boundary . "\n";
			$this->multipart .= 'Content-Type: multipart/alternative;' . chr(10) . chr(9) . 'boundary="' . $thr_boundary . "\"\n\n\n";

			$this->multipart .= '--' . $thr_boundary . "\n";
			$this->multipart .= 'Content-Type: text/plain; charset="' . $this->charset . '"' . "\n";
			$this->multipart .= 'Content-Transfer-Encoding: base64' . "\n\n";
			$this->multipart .= chunk_split( base64_encode( $this->html_text ) ) . "\n\n";

			$this->multipart .= '--' . $thr_boundary . "\n";
			$this->multipart .= 'Content-Type: text/html' . "\n";
			$this->multipart .= 'Content-Transfer-Encoding: base64' . "\n\n";
			$this->multipart .= chunk_split( base64_encode( $this->html ) ) . "\n\n";
			$this->multipart .= '--' . $thr_boundary . "--\n\n";

			for ( $i = 0; $i < count( $this->html_images ); $i++ )
			{
				$this->multipart.= '--' . $sec_boundary . "\n";
				$this->buildHTMLImage( $i );
			}

			$this->multipart.= "--".$sec_boundary."--\n\n";
		}
	}
	
	/**
	 * Adds an image to the list of embedded images.
	 *
	 * @access public
	 */
	function addHTMLImage( $file, $name = '', $c_type = 'application/octet-stream' )
	{
		$this->html_images[] = array(
			'body'   => $file,
			'name'   => $name,
			'c_type' => $c_type,
			'cid'    => md5( uniqid( time() ) ) );
	}

	/**
	 * Adds a file to the list of attachments.
	 *
	 * @access public
	 */
	function addAttachment( $file, $name = '', $c_type = 'application/octet-stream' )
	{
		$this->parts[] = array(
			'body'   => $file,
			'name'   => $name,
			'c_type' => $c_type );
	}

	/**
	 * Builds an embedded image part of an html mail.
	 *
	 * @access public
	 */
	function buildHTMLImage( $i )
	{
		$this->multipart .= 'Content-Type: ' . $this->html_images[$i]['c_type'];

		if ( $this->html_images[$i]['name'] != '' )
			$this->multipart .= '; name="' . $this->html_images[$i]['name'] . "\"\n";
		else
			$this->multipart .= "\n";

		$this->multipart .= 'Content-Transfer-Encoding: base64' . "\n";
		$this->multipart .= 'Content-ID: <' . $this->html_images[$i]['cid'] . ">\n\n";
		$this->multipart .= chunk_split( base64_encode( $this->html_images[$i]['body'] ) ) . "\n";
	}

	/**
	 * Builds a single part of a multipart message.
	 *
	 * @access public
	 */
	function buildPart( $i )
	{
		$message_part  = '';
		$message_part .= 'Content-Type: ' . $this->parts[$i]['c_type'];

		if ( $this->parts[$i]['name'] != '' )
			$message_part .= '; name="' . $this->parts[$i]['name'] . "\"\n";
		else
			$message_part .= "\n";

		// Determine content encoding.
		if ( $this->parts[$i]['c_type'] == 'text/plain' )
		{
			$message_part .= 'Content-Transfer-Encoding: base64' . "\n\n";
			$message_part .= chunk_split( base64_encode( $this->parts[$i]['body'] ) ) . "\n";
		}
		else if ( $this->parts[$i]['c_type'] == 'message/rfc822' )
		{
			$message_part .= 'Content-Transfer-Encoding: 7bit'."\n\n";
			$message_part .= $this->parts[$i]['body']."\n";
		}
		else
		{
			$message_part .= 'Content-Transfer-Encoding: base64' . "\n";
			$message_part .= 'Content-Disposition: attachment; filename="' . $this->parts[$i]['name'] . "\"\n\n";
			$message_part .= chunk_split( base64_encode( $this->parts[$i]['body'] ) ) . "\n";
		}

		return $message_part;
	}

	/**
	 * Builds the multipart message from the list ($this->_parts).
	 *
	 * @access public
	 */
	function buildMessage()
	{
		$boundary = '=_' . md5( uniqid( time() ) );

		$this->headers[] = 'MIME-Version: 1.0';
		$this->headers[] = 'Content-Type: multipart/mixed;' . chr(10) . chr(9) . 'boundary="' . $boundary . '"';
		$this->multipart = "This is a MIME encoded message.\n\n";

		if ( isset( $this->do_html ) && $this->do_html == 1 )
			$this->buildHTML( $boundary );
		
		if ( isset( $this->body ) && $this->body != '' )
			$this->parts[] = array( 'body' => $this->body, 'name' => '', 'c_type' => 'text/plain' );

		for ( $i = ( count( $this->parts ) - 1 ); $i >= 0; $i-- )
			$this->multipart .= '--' . $boundary . "\n" . $this->buildPart( $i );

		$this->mime = $this->multipart . "--" . $boundary . "--\n";
	}

	/**
	 * @access public
	 */
	function send( $to_name, $to_addr, $from_name, $from_addr, $subject = '', $headers = '' )
	{
		if ( $to_name != '' )
			$to = '"' . $to_name . '" <' . $to_addr . '>';
		else
			$to = $to_addr;

		if ( $from_name != '' )
			$from = '"' . $from_name . '" <' . $from_addr . '>';
		else
			$from = $from_addr;

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
		
		mail( $to, $subject, $this->mime, 'From: ' . $from . "\n" . implode( "\n", $this->headers ) . "\n" . implode( "\n", $xtra_headers ) );
	}
} // END OF MimeMail

?>
