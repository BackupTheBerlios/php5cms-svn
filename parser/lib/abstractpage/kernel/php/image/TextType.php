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


using( 'peer.http.url.URLUtil' );


/**
 * TextType Class
 *
 * Usage:
 * $tt =& new TextType();
 * $tt->imgWidth  = 160;
 * $tt->imgHeight = 20;
 * $tt->posX      = 5;
 * $tt->posY      = 14;
 * $tt->fontSize  = 14;
 * $tt->fontFace  = 'courbd';
 * $tt->text      = $text . 'x';
 *
 * $status = $tt->create();
 * $tt->send();
 * $tt->destruct();
 * exit;
 *
 * @package image
 */

class TextType extends PEAR
{
	var $fontFace;
	var $fontAntiAlias;
	var $fontSize;
	var $fontColor;
	var $bgColor;
	var $bgImage;
	var $imgWidth;
	var $imgHeight;
	var $posX;
	var $posY;
	var $imgType;
	var $fontDir;
	
	
	// private properties
	
	var $_text;
	var $_img;
	
	
	/**
	 * Constructor
	 */
	function TextType() 
	{
		$this->fontDir = ap_ini_get( "path_fonts", "path" );
	}

	
	function destruct() 
	{
		@imagedestroy( $this->_img );
	}

	function setFromRequest() 
	{
		if ( isset( $_GET['text'] ) ) 
		{
			$text = $_GET['text'];
		} 
		else 
		{
			$text = $_SERVER['REDIRECT_URL'];
			$pos  = strrpos( $text, '/' );
			
			if ( $pos !== false ) 
				$text = substr( $text, $pos + 1);
				
			$pos = strrpos( $text, '.' );
			
			if ( $pos !== false ) 
			{
				$imgType = strtolower( substr( $text, $pos + 1 ) );
				$text    = substr( $text, 0, $pos );
			}
		}
		
		if ( $imgType != 'png' ) 
			$this->imgType = $imgType;
			
		$this->setText( $text );
	}

	function setText( $text ) 
	{
		if ( !is_array( $text ) ) 
			$text = array( $text );

		$newText = array();
		
		while ( list(,$line) = each( $text ) ) 
		{
			$line = str_replace( '[', '%', $line );
			$line = URLUtil::crossUrlDecode( $line );
			$textArr = explode( "\r\n", $line );
			
			while ( list(,$newLine) = each( $textArr ) ) 
				$newText[] = $newLine;
		}

		if (sizeof( $newText ) > 1 )
			$this->imgHeight *= sizeof( $newText );

		$this->_text = join( "\r\n", $newText );
	}

	function create() 
	{
		if ( $this->bgImage ) 
		{
			$this->_img = imagecreatetruecolor( $this->imgWidth, $this->imgHeight );
			$bgImg = imagecreatefrompng( $this->bgImage );
			imagecopy( $this->_img, $bgImg, 0, 0, 0, 0, imagesx( $bgImg ), imagesy( $bgImg ) );
		} 
		else 
		{
			if ( true ) 
			{
				$this->_img = imagecreate( $this->imgWidth, $this->imgHeight );
			} 
			else 
			{
				$this->_img = imagecreatetruecolor( $this->imgWidth, $this->imgHeight );
			}
		}

		if ( $this->bgColor ) 
		{
			$imgColors['bgColor'] = imagecolorallocate( $this->_img, $this->bgColor[0], $this->bgColor[1], $this->bgColor[2] );
		} 
		else 
		{
			$imgColors['white']   =  'dummy';
			$imgColors['bgColor'] = &$imgColors['white'];
		}

		$imgColors['white']      = imagecolorallocate( $this->_img, 255, 255, 255 );
		$imgColors['nearwhite']  = imagecolorallocate( $this->_img, 254, 255, 255 );
		$imgColors['black']      = imagecolorallocate( $this->_img,   0,   0,   0 );
		$imgColors['nearblack']  = imagecolorallocate( $this->_img,  33,  48,  66 );
		$imgColors['softblue']   = imagecolorallocate( $this->_img, 189, 199, 206 );
		
		if ( is_array( $this->fontColor ) ) 
			$imgColors['fontColor'] = imagecolorallocate( $this->_img, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2] );
		else 
			$imgColors['fontColor'] = $imgColors['nearblack'];

		if ( !$this->bgImage ) 
			imagecolortransparent( $this->_img, $imgColors['bgColor'] );
		
		putenv( "GDFONTPATH=" . $this->fontDir );
		
		$font   = $this->fontFace;
		$text   = $this->_text;
		$status = @imagettftext ($this->_img, $this->fontSize, 0, $this->posX, $this->posY, $imgColors['fontColor'], $font, $text );
		
		if ( !$status ) 
		{
			if ( !imagestring( $this->_img, 3, 1, 1, $text, $imgColors['fontColor'] ) )
				return -1;

			return 0;
		}

		return 1;
	}

	function get() 
	{
	}
	
	function send() 
	{
		$imgType = ( isset( $this->imgType ) )? $this->imgType : 'png';
		
		switch ( $imgType ) 
		{
			case 'gif':
				break;
			
			case 'jpg':
			
			case 'jpeg':
				header( "Content-type: image/jpg" );
				imagejpeg( $this->_img );			
				break;
			
			default:
				header( "Content-type: image/png" );
				imagepng( $this->_img );
		}
	}

	function save( $fullPath = null ) 
	{
		if ( is_null( $fullPath ) ) 
		{
			global $APP;
			$fullPath = $_SERVER["DOCUMENT_ROOT"] . $_SERVER['REDIRECT_URL'];
		}

		$imgType = ( isset( $this->imgType ) )? $this->imgType : 'png';
		
		switch ( $imgType ) 
		{
			case 'gif':
				break;
				
			case 'jpg':
			
			case 'jpeg':
				return (bool)imagejpeg( $this->_img, $fullPath );
				break;
				
			default:
				return (bool)imagepng( $this->_img, $fullPath );
		}
	}

	function fetchFromQuerystring() 
	{
		if ( isset( $_GET['text'] ) ) 
		{
			$text = $_GET['text'];
		} 
		else 
		{
			$text = $_SERVER['REDIRECT_URL'];
			$pos  = strrpos( $text, '/' );
			
			if ( $pos !== false ) 
				$text = substr( $text, $pos + 1 );
			
			$pos = strrpos( $text, '.' );
			
			if ( $pos !== false ) 
				$text = substr( $text, 0, $pos );
		}
	}
} // END OF TextType

?>
