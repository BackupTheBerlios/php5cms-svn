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
 * @package peer_mail_pgp
 */
 
class PGPMail extends PEAR
{
	/**
	 * @access public
	 */
	var $headers;
	
	/**
	 * @access public
	 */
	var $body;
	
	/**
	 * @access public
	 */
	var $encbody;
	
	/**
	 * @access public
	 */
	var $multipart;
	
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
	var $html_text;
	
	/**
	 * @access public
	 */
	var $pp;
	
	/**
	 * @access public
	 */
	var $fp;
	
	/**
	 * @access public
	 */
	var $pcmd;
	
	/**
	 * @access public
	 */
	var $do_html;
	
	/**
	 * @access public
	 */
	var $pgppass;

	/**
	 * @access public
	 */
	var $pathtopgp;
	
	/**
	 * @access public
	 */
	var $encryptcommand;
	
	/**
	 * @access public
	 */
	var $signcommand;
	
	/**
	 * @access public
	 */
	var $pgppath;
	
	/**
	 * @access public
	 */
	var $html_images = array();
	
	/**
	 * @access public
	 */
	var $cids = array();
	
	/**
	 * @access public
	 */
	var $parts = array();
	
	/**
	 * @access public
	 */
	var $keyname = array();

	/**
	 * @access public
	 */	
	var $built = false;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PGPMail( $headers = '' )
	{
		$this->pathtopgp      = ap_ini_get( "file_pgp", "file" );
		$this->pgppath        = '/home/ap/.gnupg';
		
		$this->encryptcommand = "pgpe +batchmode";
		$this->signcommand    = "pgps +batchmode";

		$this->headers        = $headers;
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
				$this->html = ereg_replace( $this->html_images[$i]['name'], 'cid:'.$this->html_images[$i]['cid'], $this->html );
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

		if ( !is_array( $this->html_images ) )
		{
			$this->multipart .= '--' . $orig_boundary . "\n";
			$this->multipart .= 'Content-Type: multipart/alternative; boundary = "' . $sec_boundary . "\"\n\n\n";

			$this->multipart .= '--' . $sec_boundary . "\n";
			$this->multipart .= 'Content-Type: text/plain' . "\n";
			$this->multipart .= 'Content-Transfer-Encoding: 7bit' . "\n\n";
			$this->multipart .= $this->html_text . "\n\n";

			$this->multipart .= '--' . $sec_boundary . "\n";
			$this->multipart .= 'Content-Type: text/html' . "\n";
			$this->multipart .= 'Content-Transfer-Encoding: 7bit' . "\n\n";
			$this->multipart .= $this->html . "\n\n";
			$this->multipart .= '--' . $sec_boundary . "--\n\n";
		}
		else
		{
			$this->multipart .= '--' . $orig_boundary . "\n";
			$this->multipart .= 'Content-Type: multipart/related; boundary = "' . $sec_boundary . "\"\n\n\n";

			$this->multipart.= '--' . $sec_boundary . "\n";
			$this->multipart.= 'Content-Type: multipart/alternative; boundary = "' . $thr_boundary . "\"\n\n\n";

			$this->multipart.= '--' . $thr_boundary . "\n";
			$this->multipart.= 'Content-Type: text/plain' . "\n";
			$this->multipart.= 'Content-Transfer-Encoding: 7bit' . "\n\n";
			$this->multipart.= $this->html_text . "\n\n";

			$this->multipart.= '--' . $thr_boundary . "\n";
			$this->multipart.= 'Content-Type: text/html' . "\n";
			$this->multipart.= 'Content-Transfer-Encoding: 7bit' . "\n\n";
			$this->multipart.= $this->html . "\n\n";
			$this->multipart.= '--' . $thr_boundary . "--\n\n";

			for ( $i = 0; $i < count( $this->html_images ); $i++ )
			{
				$this->multipart .= '--' . $sec_boundary . "\n";
				$this->buildHTMLImage( $i );
			}

			$this->multipart .= "--" . $sec_boundary . "--\n\n";
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
			'body'		=> $file,
			'name'		=> $name,
			'c_type'	=> $c_type,
			'cid'		=> md5( uniqid( time() ) )
		);
	}

	/**
	 * Adds a file to the list of attachments.
	 *
	 * @access public
	 */	
	function addAttachment( $file, $name = '', $c_type = 'application/octet-stream' )
	{
		$this->parts[] = array(
			'body'		=> $file,
			'name'		=> $name,
			'c_type'	=> $c_type
		);
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
			$this->multipart .= '; name = "' . $this->html_images[$i]['name'] . "\"\n";
		else
			$this->multipart .= "\n";

		$this->multipart .= 'Content-ID: <' . $this->html_images[$i]['cid'] . ">\n";
		$this->multipart .= 'Content-Transfer-Encoding: base64' . "\n\n";
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
			$message_part .= '; name = "' . $this->parts[$i]['name'] . "\"\n";
		else
			$message_part .= "\n";

		// determine content encoding.
		if ( $this->parts[$i]['c_type'] == 'text/plain' )
		{
			$message_part .= 'Content-Transfer-Encoding: 7bit' . "\n\n";
			$message_part .= $this->parts[$i]['body'] . "\n";
		}
		else
		{
			$message_part .= 'Content-Transfer-Encoding: base64' . "\n";
			$message_part .= 'Content-Disposition: attachment; filename = "' . $this->parts[$i]['name'] . "\"\n\n";
			$message_part .= chunk_split( base64_encode( $this->parts[$i]['body'] ) ) . "\n";
		}

		return $message_part;
	}

	/**
	 * Builds the multipart message from the list ($this->parts).
	 *
	 * @access public
	 */	
	function buildMessage()
	{
		$boundary = '=_' . md5( uniqid( time() ) );

		$this->multipart  = '';
		$this->multipart .= "MIME-Version: 1.0\n";
		$this->multipart .= "Content-Type: multipart/mixed; boundary = \"".$boundary."\"\n\n";

		if ( isset( $this->do_html ) && $this->do_html == 1 )
			$this->buildHTML( $boundary );

		if ( isset( $this->body ) && $this->body != '' )
		{
			$this->parts[] = array(
				'body'		=> $this->body,
				'name' 		=> '',
				'c_type'	=> 'text/plain'
			);
		}
		
		for ( $i = ( count( $this->parts ) - 1 ); $i >= 0; $i-- )
			$this->multipart .= '--' . $boundary . "\n" . $this->buildPart( $i );

		$this->mime  = $this->multipart . "--" . $boundary . "--\n";
		$this->built = true;
	}

	/**
	 * Adds a Public Key to encrypt with (this MUST be on your public keyring).
	 *
	 * @access public
	 */	
	function addKey( $keyname )
	{
		$this->keyname[] = array( $keyname );
	}

	/**
	 * @access public
	 */		
	function addEncryptedAttachment( $file, $name = '', $c_type = 'application/pgp-encrypted' )
	{
		if ( sizeof( $this->keyname ) == 0 )
			return PEAR::raiseError( "No keys specified." );
		
		$this->pcmd = $this->pathtopgp . $this->encryptcommand;

		for ( $i = count( $this->keyname ) - 1; $i >= 0; $i-- )
			$this->pcmd .= " -r'" . $this->keyname[$i][0] . "' ";

		$attchid = md5( uniqid( time() ) );
		$this->pcmd .= " -o" . $attchid;
		echo( $this->pcmd );
		$pp = popen( $this->pcmd, w );
		fwrite( $pp, $file );
		pclose( $pp );

		$fp = fopen( $attchid, 'r' );
		$encattach = fread( $fp, filesize( $attchid ) );
		fclose( $fp );

		unlink( $attchid );

		if ( !stristr( $name, ".asc" ) )
			$name.= ".asc";
		
		$this->addAttachment( $encattach, $name, $c_type );
		return true;
	}

	/**
	 * Encrypts the Message from the body ($this->mime).
	 *
	 * @access public
	 */	
	function encryptBody()
	{
		$boundary = md5( uniqid( time() ) );

		if ( sizeof( $this->keyname ) == 0 )
			return PEAR::raiseError( "No keys specified." );

		$this->pcmd = $this->pathtopgp . $this->encryptcommand;

		for ( $i = count( $this->keyname ) - 1; $i >= 0; $i-- )
			$this->pcmd .= " -at -r'" . $this->keyname[$i][0] . "' ";

		$this->pcmd .= " -o" . $boundary;
		echo( $this->pcmd );

		$pp = popen( $this->pcmd, w );
		fwrite( $pp, $this->body );
		pclose( $pp );

		$fp = fopen( $boundary, r );
		$this->body = fread( $fp, filesize( $boundary ) );
		fclose( $fp );
		
		unlink( $boundary );
		return true;
	}

	/**
	 * Signs message and appends it to $this->body.
	 *
	 * @access public
	 */	
	function sign( $userid, $pgppass )
	{
		$boundary = md5( uniqid( time() ) );
		putenv( "PGPPASS=$pgppass" );

		$this->pcmd  = $this->pathtopgp . $this->signcommand;
		$this->pcmd .= " -u'" . $userid . "' ";
		$this->pcmd .= "-at -o" . $boundary;
		
		$pp = popen( $this->pcmd, w );
		fwrite( $pp, $this->body );
		pclose( $pp );

		$fp = fopen( $boundary, r );
		$this->body = fread( $fp, filesize( $boundary ) );
		fclose( $fp );
		
		unlink( $boundary );
	}

	/**
	 * Sends the mail.
	 *
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

		$this->headers .= 'From: ' . $from . "\n";
		$this->headers .= $headers;
		$this->mime     = $this->headers . $this->mime;

		mail( $to, $subject, '', $this->mime );
	}
} // END OF PGPMail

?>
