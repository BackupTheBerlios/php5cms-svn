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


using( 'peer.http.HTTPUtil' );


/**
 * HTML mail class
 *
 * Plain + HTML
 *		multipart/alternative (text, html)
 *		multipart/alternative (text, html)
 *
 * Plain + HTML + billede
 *		multipart/related (m/a, cids)
 *		multipart/alternative (text, html)
 *
 *	 	multipart/related (m/a, cids)
 *		multipart/alternative (text, html)
 *
 *  plain + attachment
 *	multipart/mixed 	
 *
 * HTML + Attachment:
 *		multipart/mixed (text/html , attachments)
 *
 * Plain + HTML + Attachments:
 *		multipart/mixed (m/a, attachments)
 *		multipart/alternative (text, html)
 *
 * Plain + HTML + billede + attachment
 *
 * Calypso and outlook ex.
 *		multipart/mixed (m/r, attachments)
 *		multipart/related  (m/a, cids)
 *		multipart/alternative (text, html)
 *
 * @package peer_mail
 */

class HTMLMail extends PEAR 
{
	/**
	 * @access public
	 */
	var $recipient = "recipient@whatever.com";
	
	/**
	 * This recipient (or list of...) will also receive the mail. Regard it as a copy
	 * @access public
	 */
	var $recipient_copy = "";
	
	/**
	 * @access public
	 */
	var $subject = "This is the subject";
	
	/**
	 * @access public
	 */
	var $from_email = "sender@docuverse.de";
	
	/**
	 * @access public
	 */
	var $from_name = "Mr. Sender";
	
	/**
	 * @access public
	 */
	var $replyto_email = "reply@docuverse.de";
	
	/**
	 * @access public
	 */
	var $replyto_name = "Mr. Reply";
	
	/**
	 * @access public
	 */
	var $organisation = "Your Company";
	
	/**
	 * 1 = highest, 5 = lowest, 3 = normal
	 * @access public
	 */
	var $priority = 3;

	/**
	 * X-mailer
	 * @access public
	 */
	var $mailer = "Abstractpage Mailer";
	
	/**
	 * @access public
	 */
	var $alt_base64 = 0;
	
	/**
	 * This is a prefix that will be added to all links in the mail. 
	 * Example: 'http://www.mydomain.com/jump?userid=###FIELD_uid###&url='. if used, anything after url= is urlencoded.
	 * @access public
	 */
	var $jumperURL_prefix = "";
	
	/**
	 * If set, then the array-key of the urls are inserted instead of the url itself. Smart in order to reduce link-length
	 * @access public
	 */
	var $jumperURL_useId = 0;
	
	/**
	 * If set, this is a list of the media-files (index-keys to the array) that should be represented in the html-mail
	 * @access public
	 */
	var $mediaList = "";
	
	/**
	 * @access public
	 */
	var $http_password = "";
	
	/**
	 * @access public
	 */
	var $http_username = "";

	/*		
	This is how the $theParts-array is normally looking
	var $theParts = array(
		"plain" => array(
			"content" => ""	
		),
		"html" => array(
			"content" => "",
			"path"    => "",
			"media"   => array(),
			"hrefs"   => array()
		),
		"attach" => array()
	);
	*/
	
	/**
	 * @access private
	 */
	var $theParts = array();

	/**
	 * @access private
	 */
	var $messageid = "";
	
	/**
	 * @access private
	 */
	var $returnPath = "";
	
	/**
	 * @access private
	 */
	var $Xid = "";

	/**
	 * @access private
	 */
	var $headers = "";
	
	/**
	 * @access private
	 */
	var $message = "";
	
	/**
	 * @access private
	 */
	var $part = 0;
	
	/**
	 * @access private
	 */
	var $image_fullpath_list = "";
	
	/**
	 * @access private
	 */
	var $href_fullpath_list = "";
	
	/**
	 * @access private
	 */
	var $plain_text_header = 'Content-Type: text/plain; charset=iso-8859-1
	Content-Transfer-Encoding: quoted-printable';
	
	/**
	 * @access private
	 */
	var $html_text_header = 'Content-Type: text/html; charset=iso-8859-1
	Content-Transfer-Encoding: quoted-printable';
	
	
	/**
	 * @access public
	 */
	function start()	
	{
		// Sets the message id.
		$this->messageid = md5( uniqid( "" ) );
	}
	
	/**
	 * @access public
	 */
	function useBase64()	
	{
		$this->plain_text_header = 'Content-Type: text/plain; charset=iso-8859-1
		Content-Transfer-Encoding: base64';
		
		$this->html_text_header = 'Content-Type: text/html; charset=iso-8859-1
		Content-Transfer-Encoding: base64';
		
		$this->alt_base64 = 1;
	}

	/**
	 * @access public
	 */	
	function encodeMsg( $content )	
	{
		return $this->alt_base64? $this->makeBase64( $content ) : $this->quoted_printable( $content );
	}
	
	/**
	 * Adds plain-text and qp-encodes it.
	 *
	 * @access public
	 */
	function addPlain( $content )	
	{
		$content = $this->substHTTPurlsInPlainText( $content );
		$this->setPlain( $this->encodeMsg( $content ) );
	}

	/**
	 * @access public
	 */	
	function addAttachment( $file )	
	{
		// Adds an attachment to the mail.
		$theArr = $this->getExtendedURL( $file ); // We fetch the content and the mime-type.

		if ( $theArr )	
		{
			if ( !$theArr["content_type"] )
				$theArr["content_type"] = "application/octet-stream";
				
			$temp = $this->splitFileref( $file );
			$theArr["filename"] = ( ( $temp["file"] )? $temp["file"] : ( strpos( " " . $theArr["content_type"], "htm" )? "index.html" : "unknown" ) );
			$this->theParts["attach"][] = $theArr;
			
			return true;
		}
		else 
		{ 
			return false;
		}
	}

	/**
	 * @access public
	 */	
	function addHTML( $file )	
	{
		// Adds HTML and media, encodes it from a URL or file.
		$status = $this->fetchHTML( $file );

		if ( !$status )	
			return false;
			
		if ( $this->extractFramesInfo() )
			return "Document was a frameset. Stopped";
		
		$this->extractMediaLinks();
		$this->extractHyperLinks();
		$this->fetchHTMLMedia();
		$this->substMediaNamesInHTML( 0 );	// 0 = relative
		$this->substHREFsInHTML();	
		$this->setHTML( $this->encodeMsg( $this->theParts["html"]["content"] ) );
	}

	/**
	 * External used to extract HTML-parts.
	 *
	 * @access public
	 */
	function extractHtmlInit( $html, $url )	
	{
		$this->theParts["html"]["content"] = $html;
		$this->theParts["html"]["path"]    = $url;
	}

	/**
	 * @access public
	 */
	function send( $recipient )	
	{
		if ( $recipient ) 
			$this->recipient = $recipient;
			
		$this->setHeaders();
		$this->setContent();
		$this->sendTheMail();
	}

	/**
	 * @access public
	 */
	function setHeaders()	
	{
		// Clears the header-string and sets the headers based on object-vars.
		$this->headers = "";
		
		// Message_id
		$this->add_header( "Message-ID: " . $this->messageid );
	
		// Return path
		if ( $this->returnPath )
			$this->add_header( "Return-Path: " . $this->returnPath );
		
		// X-id
		if ( $this->Xid )
			$this->add_header( "X-AbstractpageMID: " . $this->Xid );

		// From
		if ( $this->from_email )	
		{
			if ( $this->from_name )	
			{
				$name = $this->convertName( $this->from_name );
				$this->add_header( "From: $name <$this->from_email>" );
			} 
			else 
			{
				$this->add_header( "From: $this->from_email" );
			}
		}
		
		// Reply
		if ( $this->replyto_email )	
		{
			if ( $this->replyto_name )	
			{
				$name = $this->convertName( $this->replyto_name );
				$this->add_header( "Reply-To: $name <$this->replyto_email>" );
			} 
			else 
			{
				$this->add_header( "Reply-To: $this->replyto_email" );
			}
		}
		
		// Organisation
		if ( $this->organisation )	
		{
			$name = $this->convertName( $this->organisation );
			$this->add_header( "Organisation: $name" );
		}
		
		// mailer
		if ( $this->mailer )
			$this->add_header( "X-Mailer: $this->mailer" );
	
		// priority
		if ( $this->priority )
			$this->add_header( "X-Priority: $this->priority" );
		
		$this->add_header( "Mime-Version: 1.0" );
	}
	
	/**
	 * Sets the recipient(s). If you supply a string, you set one recipient. 
	 * If you supply an array, every value is added as a recipient.
	 *
	 * @access public
	 */
	function setRecipient( $recip )	
	{
		if ( is_array( $recip ) )	
		{
			$this->recipient = "";
			while ( list($key,) = each( $recip ) ) 
				$this->recipient .= $recip[$key] . ",";
			
			$this->recipient = ereg_replace( ",$", "", $this->recipient );
		} 
		else 
		{
			$this->recipient = $recip;
		}
	}

	/**
	 * @access public
	 */	
	function getHTMLContentType()	
	{
		return count( $this->theParts["html"]["media"] )? 'multipart/related;' : 'multipart/alternative;';
	}
	
	/**
	 * @access public
	 */
	function setContent()	
	{
		// Begins building the message-body.
		$this->message = "";
		$boundary = $this->getBoundary();
		
		// Setting up headers.
		if ( count( $this->theParts["attach"] ) )	
		{
			$this->add_header( 'Content-Type: multipart/mixed;' );
			$this->add_header( ' boundary="' . $boundary . '"'  );
			$this->add_message( "This is a multi-part message in MIME format.\n" );
			$this->constructMixed( $boundary );	// Generate (plain/HTML) / attachments.
		} 
		else if ( $this->theParts["html"]["content"] ) 
		{
			$this->add_header( 'Content-Type: ' . $this->getHTMLContentType() );
			$this->add_header( ' boundary="' . $boundary . '"' );
			$this->add_message( "This is a multi-part message in MIME format.\n" );
			$this->constructHTML( $boundary );	// Generate plain/HTML mail.
		} 
		else 
		{
			$this->add_header( $this->plain_text_header );
			$this->add_message( $this->getContent( "plain" ) );	// Generate plain only.
		}
	}
	
	/**
	 * @access public
	 */
	function constructMixed( $boundary )	
	{
		// Here (plain/HTML) is combined with the attachments.
		$this->add_message( "--" . $boundary );
		
		// (plain/HTML) is added
		if ( $this->theParts["html"]["content"] )	
		{
			// HTML and plain
			$newBoundary = $this->getBoundary();
			$this->add_message( "Content-Type: " . $this->getHTMLContentType() );
			$this->add_message( ' boundary="' . $newBoundary . '"' );
			$this->add_message( '' );
			$this->constructHTML( $newBoundary );
		} 
		else 
		{	
			// Purely plain
			$this->add_message( $this->plain_text_header );
			$this->add_message( '' );
			$this->add_message( $this->getContent( "plain" ) );
		}
		
		// attachments are added
		if ( is_array( $this->theParts["attach"] ) )	
		{
			reset( $this->theParts["attach"] );
			while ( list(,$media) = each( $this->theParts["attach"] ) )	
			{
				$this->add_message( "--" . $boundary );
				$this->add_message( "Content-Type: " . $media["content_type"] );
				$this->add_message( ' name="' . $media["filename"] . '"' );
				$this->add_message( "Content-Transfer-Encoding: base64" );
				$this->add_message( "Content-Disposition: attachment;"  );
				$this->add_message( ' filename="' . $media["filename"] . '"' );
				$this->add_message( '' );
				$this->add_message( $this->makeBase64( $media["content"] ) );
			}
		}
		
		$this->add_message( "--" . $boundary . "--\n" );
	}

	/**
	 * @access public
	 */	
	function constructHTML( $boundary )	
	{
		// If media, then we know, the multipart/related content-type has been set before this function call...
		if ( count( $this->theParts["html"]["media"] ) )	
		{	
			$this->add_message( "--" . $boundary );
			
			// HTML has media
			$newBoundary = $this->getBoundary();
			$this->add_message( "Content-Type: multipart/alternative;" );
			$this->add_message( ' boundary="' . $newBoundary . '"' );
			$this->add_message( '' );

			// Adding the plaintext/html mix
			$this->constructAlternative( $newBoundary ); 
			
			$this->constructHTML_media( $boundary );
			$this->add_message( "--" . $boundary . "--\n" );
		} 
		else 
		{
			// Adding the plaintext/html mix, and if no media, then use $boundary instead of $newBoundary.
			$this->constructAlternative( $boundary );
		}
	}
	
	/**
	 * @access public
	 */
	function constructAlternative( $boundary )	
	{
		// Here plain is combined with HTML.
		$this->add_message( "--" . $boundary );

		// plain is added
		$this->add_message( $this->plain_text_header );
		$this->add_message( '' );
		$this->add_message( $this->getContent( "plain" ) );
		$this->add_message( "--" . $boundary );

		// html is added
		$this->add_message( $this->html_text_header );
		$this->add_message( '' );
		$this->add_message( $this->getContent( "html" ) );
		$this->add_message( "--" . $boundary . "--\n" );
	}

	/**
	 * @access public
	 */	
	function constructHTML_media( $boundary )	
	{
		// media is added
		if ( is_array( $this->theParts["html"]["media"] ) )	
		{
			reset( $this->theParts["html"]["media"] );
			while ( list( $key, $media ) = each( $this->theParts["html"]["media"] ) )	
			{
				if ( !$this->mediaList || t3lib_div::inList( $this->mediaList, $key ) )	
				{
					$this->add_message( "--".$boundary );
					$this->add_message( "Content-Type: " . $media["ctype"] );
					$this->add_message( "Content-ID: <part" . $key . "." . $this->messageid . ">" );
					$this->add_message( "Content-Transfer-Encoding: base64" );
					$this->add_message( '' );
					$this->add_message( $this->makeBase64( $media["content"] ) );
				}
			}
		}
		
		$this->add_message( "--" . $boundary . "--\n" );
	}
	
	/**
	 * Sends the mail. 
	 * Requires the recipient, message and headers to be set.
	 *
	 * @access public
	 */
	function sendTheMail() 
	{
		if ( trim( $this->recipient ) && trim( $this->message ) )	
		{
			mail( $this->recipient,
				  $this->subject,
				  $this->message,
				  $this->headers	
			);
			
			// Sending copy:
			if ( $this->recipient_copy )	
			{
				mail( $this->recipient_copy,
					  $this->subject,
					  $this->message,
					  $this->headers	
				);
			}
			
			// Auto response
			if ( $this->auto_respond_msg )	
			{
				$theParts    = explode( "/", $this->auto_respond_msg, 2  );
				$theParts[1] = str_replace( "/", chr( 10 ), $theParts[1] );

				mail( $this->from_email,
					  $theParts[0],
					  $theParts[1],
					  "From: " . $this->recipient	
				);
			}
			
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * @access public
	 */
	function getBoundary()	
	{
		$this->part++;
		return 	"----------" . uniqid( "part_" . $this->part . "_" );
	}
	
	/**
	 * Sets the plain-text part. No processing done.
	 *
	 * @access public
	 */
	function setPlain( $content )	
	{
		$this->theParts["plain"]["content"] = $content;
	}
	
	/**
	 * Sets the HTML-part. No processing done.
	 *
	 * @access public
	 */
	function setHtml( $content )	
	{
		$this->theParts["html"]["content"] = $content;
	}	
	
	/**
	 * Adds a header to the mail. Use this AFTER the setHeaders()-function.
	 *
	 * @access public
	 */
	function add_header( $header )	
	{
		$this->headers .= $header . "\n";
	}
	
	/**
	 * Adds a line of text to the mail-body. Is normally use internally.
	 *
	 * @access public
	 */
	function add_message( $string )	
	{
		$this->message .= $string . "\n";
	}

	/**
	 * @access public
	 */	
	function getContent( $type )	
	{
		return $this->theParts[$type]["content"];
	}
	
	/**
	 * @access public
	 */
	function preview()	
	{
		echo nl2br( HTMLSpecialChars( $this->headers ) );
		echo "<BR>";
		echo nl2br( HTMLSpecialChars( $this->message ) );
	}

	/**
	 * Fetches the HTML-content from either url og local serverfile.
	 *
	 * @access public
	 */
	function fetchHTML( $file )	
	{
		$this->theParts["html"]["content"] = $this->getURL( $file ); // Fetches the content of the page.

		if ( $this->theParts["html"]["content"] )	
		{
			$addr = $this->extParseUrl( $file );
 			$path = ( $addr["scheme"] )? $addr["scheme"] . "://" . $addr["host"] . ( ( $addr["filepath"] )? $addr["filepath"] : "/" ) : $addr["filepath"];
			$this->theParts["html"]["path"] = $path;
			
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * Fetches the mediafiles which are found by extractMediaLinks().
	 *
	 * @access public
	 */
	function fetchHTMLMedia()	
	{
		if ( is_array( $this->theParts["html"]["media"] ) )	
		{
			reset( $this->theParts["html"]["media"] );

			if ( count( $this->theParts["html"]["media"] ) > 0 )	
			{
				while ( list( $key, $media ) = each( $this->theParts["html"]["media"] ) )	
				{
					// We fetch the content and the mime-type.
					$picdata = $this->getExtendedURL( $this->theParts["html"]["media"][$key]["absRef"] );

					if ( is_array( $picdata ) )	
					{
						$this->theParts["html"]["media"][$key]["content"] = $picdata["content"];
						$this->theParts["html"]["media"][$key]["ctype"]   = $picdata["content_type"];
					}
				}
			}
		}
	}
	
	/**
	 * @access public
	 */
	function extractMediaLinks()	
	{
		// extracts all media-links from $this->theParts["html"]["content"]
		$html_code   = $this->theParts["html"]["content"];
		$attribRegex = $this->tag_regex( array( "img","table","td","tr","body","iframe","script","input","embed" ) );
		$codepieces  = split( $attribRegex, $html_code ); // Splits the document by the beginning of the above tags.
		$len         = strlen( $codepieces[0] );
		$pieces      = count( $codepieces );
	
		for ( $i = 1; $i < $pieces; $i++ ) 	
		{
			$tag               = strtolower( strtok( substr( $html_code, $len + 1, 10 ), " " ) );
			$len              += strlen( $tag ) + strlen( $codepieces[$i] ) + 2;
			$dummy             = eregi( "[^>]*", $codepieces[$i], $reg );
			$attributes        = $this->getTagAttributes( $reg[0] );	// Fetches the attributes for the tag.
			$imageData         = array();
			$imageData["ref"]  = ( $attributes["src"] )? $attributes["src"] : $attributes["background"]; // Finds the src or background attribute.

			if ( $imageData["ref"] )	
			{
				$imageData["quotes"]    = ( substr($codepieces[$i], strpos( $codepieces[$i], $imageData["ref"] ) - 1, 1 ) == '"' )? '"' : ''; // Finds out if the value had quotes around it.
				$imageData["subst_str"] = $imageData["quotes"] . $imageData["ref"] . $imageData["quotes"];	// subst_str is the string to look for, when substituting lateron

				if ( $imageData["ref"] && !strstr( $this->image_fullpath_list, "|" . $imageData["subst_str"] . "|" ) )	
				{
					$this->image_fullpath_list .= "|" . $imageData["subst_str"] . "|";
					$imageData["absRef"]        = $this->absRef( $imageData["ref"] );
					$imageData["tag"]           = $tag;
					$imageData["use_jumpurl"]   = $attributes["dmailerping"]? 1 : 0;
					$this->theParts["html"]["media"][] = $imageData;
				}
			}
		}
		
		// Extracts stylesheets.
		$attribRegex = $this->tag_regex( array( "link" ) );
		$codepieces  = split( $attribRegex, $html_code ); // Splits the document by the beginning of the above tags.
		$pieces      = count( $codepieces );

		for ( $i = 1; $i < $pieces; $i++ )	
		{
			$dummy      = eregi( "[^>]*", $codepieces[$i], $reg );
			$attributes = $this->getTagAttributes( $reg[0] ); // Fetches the attributes for the tag.
			$imageData  = array();
			
			if ( strtolower( $attributes["rel"] ) == "stylesheet" && $attributes["href"] )	
			{
				$imageData["ref"]       = $attributes["href"];	// Finds the src or background attribute.
				$imageData["quotes"]    = ( substr($codepieces[$i], strpos( $codepieces[$i], $imageData["ref"] ) - 1, 1 ) == '"' ) ? '"' : ''; // Finds out if the value had quotes around it.
				$imageData["subst_str"] = $imageData["quotes"] . $imageData["ref"] . $imageData["quotes"];	// subst_str is the string to look for, when substituting lateron
				
				if ( $imageData["ref"] && !strstr( $this->image_fullpath_list, "|" . $imageData["subst_str"] . "|" ) )	
				{
					$this->image_fullpath_list .= "|" . $imageData["subst_str"] . "|";
					$imageData["absRef"] = $this->absRef( $imageData["ref"] );
					$this->theParts["html"]["media"][] = $imageData;
				}
			}
		}
		
		// fixes javascript rollovers
		$codepieces = split( quotemeta( ".src" ), $html_code );
		$pieces     = count( $codepieces );
		$expr       = "^[^" . quotemeta( "\"" ) . quotemeta( "'" ) . "]*";      // "'\"" 
		
		for ( $i = 1; $i < $pieces; $i++ )	
		{
			$temp = $codepieces[$i];
			$temp = trim( ereg_replace( "=", "", trim( $temp ) ) );
			ereg( $expr, substr( $temp, 1, strlen( $temp ) ), $reg );

			$imageData["ref"]       = $reg[0];
			$imageData["quotes"]    = substr( $temp, 0, 1 );
			$imageData["subst_str"] = $imageData["quotes"] . $imageData["ref"] . $imageData["quotes"];	// subst_str is the string to look for, when substituting lateron
			$theInfo                = $this->splitFileref( $imageData["ref"] );

			switch ( $theInfo["fileext"] )	
			{
				case "gif":
			
				case "jpeg":
			
				case "jpg":
					if ( $imageData["ref"] && !strstr( $this->image_fullpath_list, "|" . $imageData["subst_str"] . "|" ) )	
					{
						$this->image_fullpath_list .= "|" . $imageData["subst_str"] . "|";
						$imageData["absRef"] = $this->absRef( $imageData["ref"] );
						$this->theParts["html"]["media"][] = $imageData;
					}
	
					break;
			}
		}
	}

	/**
	 * @access public
	 */	
	function extractHyperLinks()	
	{
		// extracts all hyper-links from $this->theParts["html"]["content"]
		$html_code   = $this->theParts["html"]["content"];
		$attribRegex = $this->tag_regex( array( "a", "form", "area" ) );
		$codepieces  = split( $attribRegex, $html_code ); // Splits the document by the beginning of the above tags.
		$len         = strlen( $codepieces[0] );
		$pieces      = count( $codepieces );
		
		for ( $i = 1; $i < $pieces; $i++ )	
		{
			$tag  = strtolower( strtok( substr( $html_code, $len + 1, 10 ), " " ) );
			$len += strlen( $tag ) + strlen( $codepieces[$i] ) + 2;
			
			$dummy      = eregi( "[^>]*", $codepieces[$i], $reg );
			$attributes = $this->getTagAttributes( $reg[0] );	// Fetches the attributes for the tag.
			$hrefData   = "";
			
			if ( $attributes["href"] ) 
				$hrefData["ref"] = $attributes["href"];
			else 
				$hrefData["ref"] = $attributes["action"];
				
			if ( $hrefData["ref"] )	
			{
				$hrefData["quotes"]    = ( substr( $codepieces[$i], strpos( $codepieces[$i], $hrefData["ref"] ) - 1, 1 ) == '"' )? '"' : ''; // Finds out if the value had quotes around it.
				$hrefData["subst_str"] = $hrefData["quotes"] . $hrefData["ref"] . $hrefData["quotes"];	// subst_str is the string to look for, when substituting lateron

				if ( $hrefData["ref"] && substr( trim( $hrefData["ref"] ), 0, 1 ) != "#" && !strstr( $this->href_fullpath_list, "|" . $hrefData["subst_str"] . "|" ) )	
				{
					$this->href_fullpath_list .= "|" . $hrefData["subst_str"] . "|";
					$hrefData["absRef"] = $this->absRef( $hrefData["ref"] );
					$hrefData["tag"]    = $tag;
					$this->theParts["html"]["hrefs"][] = $hrefData;
				}
			}
		}
		
		// Extracts Typo3 specific links made by the openPic() JS function.
		$codepieces = explode( "onClick=\"openPic('", $html_code);
		$pieces = count( $codepieces );
		
		for ( $i = 1; $i < $pieces; $i++ )	
		{
			$showpic_linkArr = explode( "'", $codepieces[$i] );
			$hrefData["ref"] = $showpic_linkArr[0];
			
			if ( $hrefData["ref"] )	
			{
				$hrefData["quotes"]    = "'"; // Finds out if the value had quotes around it.
				$hrefData["subst_str"] = $hrefData["quotes"] . $hrefData["ref"] . $hrefData["quotes"]; // subst_str is the string to look for, when substituting lateron

				if ( $hrefData["ref"] && !strstr( $this->href_fullpath_list, "|" . $hrefData["subst_str"] . "|" ) )	
				{
					$this->href_fullpath_list .= "|" . $hrefData["subst_str"] . "|";
					$hrefData["absRef"] = $this->absRef( $hrefData["ref"] );
					$this->theParts["html"]["hrefs"][] = $hrefData;
				}
			}
		}
	}

	/**
	 * @access public
	 */	
	function extractFramesInfo()	
	{
		// extracts all media-links from $this->theParts["html"]["content"]
		$html_code = $this->theParts["html"]["content"];
		
		if ( strpos( " " . $html_code, "<frame " ) )	
		{
			$attribRegex = $this->tag_regex( "frame" );
			$codepieces  = split( $attribRegex, $html_code, 1000000 ); // Splits the document by the beginning of the above tags.
			$pieces      = count( $codepieces );

			for ( $i = 1; $i < $pieces; $i++ )	
			{
				$dummy           = eregi( "[^>]*", $codepieces[$i], $reg );
				$attributes      = $this->getTagAttributes( $reg[0] ); // Fetches the attributes for the tag.
				$frame           = "";
				$frame["src"]    = $attributes["src"];
				$frame["name"]   = $attributes["name"];
				$frame["absRef"] = $this->absRef( $frame["src"] );
				$theInfo[]       = $frame;
			}
			
			return $theInfo;
		}
	}
	
	/**
	 * This substitutes the media-references in $this->theParts["html"]["content"]
	 * If $absolute is true, then the refs are substituted with http:// ref's indstead of Content-ID's (cid).
	 *
	 * @access public
	 */
	function substMediaNamesInHTML( $absolute )	
	{
		if ( is_array( $this->theParts["html"]["media"] ) )	
		{
			reset( $this->theParts["html"]["media"] );
			while ( list( $key, $val ) = each( $this->theParts["html"]["media"] ) )	
			{
				if ( $val["use_jumpurl"] && $this->jumperURL_prefix )
					$theSubstVal = $this->jumperURL_prefix . rawurlencode( $val["absRef"] );
				else
					$theSubstVal = ( $absolute )? $val["absRef"] : "cid:part" . $key . "." . $this->messageid;
				
				$this->theParts["html"]["content"] = str_replace(
					$val["subst_str"], 
					$val["quotes"].$theSubstVal.$val["quotes"],
					$this->theParts["html"]["content"]	
				);
			}
		}
		
		if ( !$absolute )
			$this->fixRollOvers();
	}
	
	/**
	 * This substitutes the hrefs in $this->theParts["html"]["content"]
	 *
	 * @access public
	 */
	function substHREFsInHTML()	
	{
		if ( is_array( $this->theParts["html"]["hrefs"] ) )	
		{
			reset( $this->theParts["html"]["hrefs"] );
			while ( list( $key, $val ) = each( $this->theParts["html"]["hrefs"] ) )	
			{
				if ( $this->jumperURL_prefix && $val["tag"]!="form" )	
				{	
					// Form elements cannot use jumpurl!
					if ( $this->jumperURL_useId )
						$theSubstVal = $this->jumperURL_prefix . $key;
					else
						$theSubstVal = $this->jumperURL_prefix . rawurlencode( $val["absRef"] );
				} 
				else 
				{
					$theSubstVal = $val["absRef"];
				}
				
				$this->theParts["html"]["content"] = str_replace(
					$val["subst_str"], 
					$val["quotes"].$theSubstVal.$val["quotes"],
					$this->theParts["html"]["content"]	
				);
			}
		}
	}
	
	/**
	 * This substitutes the http:// urls in plain text with links.
	 *
	 * @access public
	 */ 
	function substHTTPurlsInPlainText( $content )	
	{
		if ( $this->jumperURL_prefix )	
		{
			$textpieces = explode( "http://", $content );
			$pieces     = count( $textpieces );
			$textstr    = $textpieces[0];
			
			for ( $i = 1; $i < $pieces; $i++ )	
			{
				$len = strcspn( $textpieces[$i], chr( 32 ) . chr( 9 ) . chr( 13 ) . chr( 10 ) );
	
				if ( trim( substr( $textstr, -1 ) ) == "" && $len )	
				{
					$lastChar = substr( $textpieces[$i], $len - 1, 1 );
	
					if ( !ereg( "[A-Za-z0-9\/#]", $lastChar ) ) 
						$len--; // Included "\/" 3/12
	
					$parts[0] = "http://" . substr( $textpieces[$i], 0, $len );
					$parts[1] = substr( $textpieces[$i], $len );
					
					if ( $this->jumperURL_useId )	
					{
						$this->theParts["plain"]["link_ids"][$i] = $parts[0];
						$parts[0] = $this->jumperURL_prefix . "-" . $i;
					} 
					else 
					{
						$parts[0] = $this->jumperURL_prefix . rawurlencode( $parts[0] );
					}

					$textstr .= $parts[0] . $parts[1];
				} 
				else 
				{
					$textstr .= 'http://' . $textpieces[$i];
				}				
			}
			
			$content = $textstr;
		}
		
		return $content;
	}

	/**
	 * JavaScript rollOvers cannot support graphics inside of mail. 
	 * If these exists we must let them refer to the absolute url. By the way: Roll-overs seems to 
	 * work only on some mail-readers and so far I've seen it work on Netscape 4 message-center (but not 4.5!!)
	 *
	 * @access public
	 */
	function fixRollOvers()	
	{
		$theNewContent = "";
		$theSplit      = explode( ".src", $this->theParts["html"]["content"] );
		
		if ( count( $theSplit ) > 1 )	
		{
			while ( list( $key, $part ) = each( $theSplit ) )	
			{
				$sub = substr( $part, 0, 200 );

				if ( ereg( "cid:part[^ \"']*", $sub, $reg ) )	// "'"
				{
					$thePos = strpos( $part, $reg[0] );			// The position of the string
					ereg( "cid:part([^\.]*).*", $sub, $reg2 );	// Finds the id of the media...
	 				$theSubStr = $this->theParts["html"]["media"][intval( $reg2[1] )]["absRef"];
					
					if ( $thePos && $theSubStr )	
					{		
						// ... and substitutes the javaScript rollover image with this instead
						if ( !strpos( " " . $theSubStr, "http://" ) ) 
							$theSubStr = "http://"; // If the path is NOT and url, the reference is set to nothing
						
						$part = substr( $part, 0, $thePos ) . $theSubStr . substr( $part, $thePos + strlen( $reg[0] ), strlen( $part ) );
					}
				}
				
				$theNewContent .= $part . ( ( ( $key + 1 ) != count( $theSplit ) )? ".src" : "" );
			}
			
			$this->theParts["html"]["content"] = $theNewContent;
		}
	}

	/**
	 * Returns base64-encoded content, which is broken every 76 character.
	 *
	 * @access public
	 */
	function makeBase64( $inputstr )	
	{
		return chunk_split( base64_encode( $inputstr ) );
	}
	
	/**
	 * Reads the URL or file and determines the Content-type by either guessing or opening a connection to the host.
	 *
	 * @access public
	 */
	function getExtendedURL( $url )	
	{
		$res["content"] = $this->getURL( $url );
		
		if ( !$res["content"] )	
			return false;
			
		$pathInfo = parse_url( $url );
		$fileInfo = $this->splitFileref( $pathInfo["path"] );
		
		if ( $fileInfo["fileext"] == "gif" )	
			$res["content_type"] = "image/gif";
			
		if ( $fileInfo["fileext"] == "jpg" || $fileInfo["fileext"] == "jpeg" )	
			$res["content_type"] = "image/jpeg";
			
		if ( $fileInfo["fileext"] == "html" || $fileInfo["fileext"] == "htm" )	
			$res["content_type"] = "text/html";
			
		if ( $fileInfo["fileext"] == "swf" )	
			$res["content_type"] = "application/x-shockwave-flash";
			
		if ( !$res["content_type"] )	
			$res["content_type"] = HTTPUtil::getMimeType( $url );

		return $res;
	}
	
	/**
	 * @access public
	 */
	function addUserPass( $url )	
	{
		$user = $this->http_username;
		$pass = $this->http_password;
		
		if ( $user && $pass && substr( $url, 0, 7 ) == "http://" )
			$url = "http://" . $user . ":" . $pass . "@" . substr( $url, 7 );
		
		return $url;
	}

	/**
	 * @access public
	 */	
	function getURL( $url )	
	{
		$url = $this->addUserPass( $url );
		
		// reads a url or file
		if ( $fd = @fopen( $url, "r" ) )	
		{
			$content = "";
			
			while ( !feof( $fd ) )
				$content .= fread( $fd, 5000 );
			
			fclose( $fd );
			return $content;
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * Reads a url or file and strips the HTML-tags AND removes all empty lines. 
	 * This is used to read plain-text out of a HTML-page.
	 *
	 * @access public
	 */
	function getStrippedURL( $url )	
	{
		if ( $fd = fopen( $url, "r" ) )	
		{
			$content = "";
			while ( !feof( $fd ) )	
			{
				$line = fgetss( $fd, 5000 );
				
				if ( trim( $line ) )
					$content .= trim( $line ) . "\n";
			}
			
			fclose( $fd );
			return $content;
		}
	}
	
	/**
	 * Returns the absolute address of a link. This is based on $this->theParts["html"]["path"] being the root-address.
	 *
	 * @access public
	 */
	function absRef( $ref )	
	{
		$ref     = trim( $ref );
		$urlINFO = parse_url( $ref );
		
		if ( $urlINFO["scheme"] )	
		{
			return $ref;
		} 
		else if ( eregi( "^/", $ref ) )
		{
			$addr = parse_url( $this->theParts["html"]["path"] );
			return $addr["scheme"] . "://" . $addr["host"] . $ref;
		} 
		else 
		{
			// If the reference is relative, the path is added, in order for us to fetch the content.
			return $this->theParts["html"]["path"] . $ref;
		}
	}
	
	/**
	 * Returns an array with path, filename, filebody, fileext.
	 *
	 * @access public
	 */
	function splitFileref( $fileref )	
	{
		if ( ereg( "(.*/)(.*)$", $fileref, $reg ) )	
		{
			$info["path"] = $reg[1];
			$info["file"] = $reg[2];
		} 
		else 
		{
			$info["path"] = "";
			$info["file"] = $fileref;
		}
		
		$reg = "";
		
		if ( ereg( "(.*)\.([^\.]*$)", $info["file"], $reg )	)	
		{
			$info["filebody"]    = $reg[1];
			$info["fileext"]     = strtolower( $reg[2] );
			$info["realFileext"] = $reg[2];
		} 
		else 
		{
			$info["filebody"] = $info["file"];
			$info["fileext"]  = "";
		}
		
		return $info;
	}
	
	/**
	 * Returns an array with file or url-information.
	 *
	 * @access public
	 */
	function extParseUrl( $path )	
	{
		$res = parse_url( $path );
		ereg( "(.*/)([^/]*)$", $res["path"], $reg );
		$res["filepath"] = $reg[1];
		$res["filename"] = $reg[2];

		return $res;
	}

	/**
	 * @access public
	 */	
	function tag_regex( $tagArray )	
	{
		if ( !is_array( $tagArray ) )
			$tagArray = array( $tagArray );
		
		$theRegex = "";
		$c = count( $tagArray );
		
		while ( list(,$tag) = each( $tagArray ) )	
		{
			$c--;
			$theRegex .= "<" . sql_regcase( $tag ) . "[[:space:]]" . ( ( $c )? "|" : "" );
		}
		
		return $theRegex;
	}
	
	/**
	 * Analyses a HTML-tag.
	 *
	 * $tag is either like this "<TAG OPTION ATTRIB=VALUE>" or this " OPTION ATTRIB=VALUE>" which means you can omit the tag-name
	 * returns an array with the attributes as keys in lower-case		
	 * If an attribute is empty (like OPTION) the value of that key is just empty. Check it with is_set();
	 *
	 * @access public
	 */
	function getTagAttributes( $tag )	
	{
		$attributes    = array();
		$tag           = ltrim( eregi_replace( "^<[^ ]*", "", trim( $tag ) ) );
		$tagLen        = strlen( $tag );
		$safetyCounter = 100;
		
		// Find attribute.
		while ( $tag )	
		{
			$value  = "";
			$reg    = split( "[[:space:]=>]", $tag, 2 );
			$attrib = $reg[0];
			$tag    = ltrim( substr( $tag, strlen( $attrib ), $tagLen ) );
			
			if ( substr( $tag, 0, 1 ) == "=" )	
			{
				$tag = ltrim( substr( $tag, 1, $tagLen ) );
				
				if ( substr( $tag, 0, 1 ) == '"' )	
				{	
					// Quotes around the value.
					$reg   = explode( '"', substr( $tag, 1, $tagLen ), 2 );
					$tag   = ltrim( $reg[1] );
					$value = $reg[0];
				} 
				else 
				{	
					// No qoutes around value.
					ereg( "^([^[:space:]>]*)(.*)", $tag, $reg );
					
					$value = trim( $reg[1] );
					$tag   = ltrim( $reg[2] );
					
					if ( substr( $tag, 0, 1 ) == ">" )
						$tag = "";
				}
			}
			
			$attributes[strtolower( $attrib )] = $value;
			$safetyCounter--;
			
			if ( $safetyCounter < 0 )	
				break;
		}
		
		return $attributes;
	}
	
	/**
	 * This functions is buggy. It seems that in the part where the lines are breaked every 76th character, 
	 * that it fails if the break happens right in a quoted_printable encode character!
	 *
	 * @access public
	 */
	function quoted_printable( $string )	
	{
		$newString = "";
		$theLines  = explode( chr( 10 ), $string );	// Break lines. Doesn't work with mac eol's which seems to be 13. But 13-10 or 10 will work.

		while ( list(,$val) = each( $theLines ) )	
		{
			$val       = ereg_replace( chr( 13 ) . "$", "", $val );	// removes possible character 13 at the end of line	
			$newVal    = "";
			$theValLen = strlen( $val );
			$len       = 0;
			
			for ( $index = 0; $index < $theValLen; $index++ )	
			{
				$char   = substr( $val, $index, 1 );
				$ordVal = ord( $char );
				
				if ( $len > ( 76 - 4 ) || ( $len > ( 66 - 4 ) && $ordVal == 32 ) )	
				{
					$len     = 0;
					$newVal .= "=" . chr( 13 ) . chr( 10 );
				}
				
				if ( ( $ordVal >= 33 && $ordVal <= 60 ) || ( $ordVal >= 62 && $ordVal <= 126 ) || $ordVal == 9 || $ordVal == 32 )	
				{
					$newVal .= $char;
					$len++;
				} 
				else 
				{
					$newVal .= sprintf( "=%02X", $ordVal );
					$len += 3;
				}
			}
			
			$newVal     = ereg_replace( chr( 32 ) . "$", "=20", $newVal ); // replaces a possible SPACE-character at the end of a line
			$newVal     = ereg_replace( chr( 9  ) . "$", "=09", $newVal ); // replaces a possible TAB-character at the end of a line
			$newString .= $newVal . chr( 13 ) . chr( 10 );
		}
		
		return $newString;
	}

	/**
	 * @access public
	 */	
	function convertName( $name )	
	{
		if ( ereg( "[^" . chr( 32 ) . "-" . chr( 60 ) . chr( 62 ) . "-" . chr( 127 ) . "]", $name ) )
			return '=?iso-8859-1?B?' . base64_encode( $name ) . '?=';
		else
			return $name;
	}
} // END OF HTMLMail

?>
