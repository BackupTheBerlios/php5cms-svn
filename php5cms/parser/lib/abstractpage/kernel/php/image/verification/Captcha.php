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
|         Horst Nogajski <horst@nogajski.de>                           |
+----------------------------------------------------------------------+
*/


using( 'image.ImageUtil' );
using( 'security.Secret' );


/**
 * This class generates a picture to use in forms that perform CAPTCHA test
 * (Completely Automated Public Turing to tell Computers from Humans Apart).
 * After the test form is submitted a key entered by the user in a text field
 * is compared by the class to determine whether it matches the text in the picture.
 * 
 * The following enhancements were added:
 * 
 * - Support to make it work with GD library before version 2
 * - Hacking prevention
 * - Optional use of Web safe colors
 * - Limit the number of users attempts
 * - Display an optional refresh link to generate a new picture with a different key
 *   without counting to the user attempts limit verification
 * - Support the use of multiple random TrueType fonts
 * - Control the output image by only three parameters: number of text characters
 *   and minimum and maximum size preserving the size proportion
 * - Preserve all request parameters passed to the page via the GET method,
 *   so the CAPTCHA test can be added to existing scripts with minimal changes
 *
 * @package image_verification
 */

class Captcha extends PEAR
{
	/**
	 * Absolute path to a Tempfolder (with trailing slash!). This must be writeable for PHP and also accessible via HTTP, because the image will be stored there.
	 * @var string
	 * @access public
	 */
	var $tempfolder;

	/**
	 * Absolute path to folder with TrueTypeFonts (with trailing slash!). This must be readable by PHP.
	 * @var string
	 * @access public
	 */
	var $ttf_folder;

	/**
	 * A List with available TrueTypeFonts for random char-creation.
	 * @var mixed[array|string]
	 * @access public
	 */
	var $ttf_range = array(
		'arial.ttf', 
		'courier.ttf', 
		'verdana.ttf'
	);

	/**
	 * How many chars the generated text should have
	 * @var integer
	 * @access public
	 */
	var $chars = 6;

	/**
	 * The minimum size a Char should have
	 * @var integer
	 * @access public
	 */
	var $minsize = 20;

	/**
	 * The maximum size a Char can have
	 * @var integer
	 * @access public
	 */
	var $maxsize = 40;

	/**
	 * Background noisy On/Off (if is Off, a grid will be created)
	 * @var boolean
	 * @access public
	 */
	var $noise = true;

	 /**
	 * This will only use the 216 websafe color pallette for the image.
	 * @var boolean
	 * @access public
 	 */
	var $websafecolors = false;

	/**
	 * Number of tries without success
	 * @var integer
	 * @access public
	 */
	var $maxtry = 3;

	/**
	 * Gives the user the possibility to generate a new captcha-image.
	 * @var boolean
	 * @access public
	 */
	var $refreshlink = true;

	/**
	 * Width of picture
	 * @access private
	 */
	var $_lx;
	
	/** 
	 * Height of picture
	 * @access private 
	 */
	var $_ly;
	
	/**
	 * Image quality
	 * @access private 
	 */
	var $_jpegquality = 80;
	
	/** 
	 * This will multiplyed with number of chars
	 * @access private 
	 */
	var $_noisefactor = 9;
	
	/** 
	 * Number of background-noise-characters
	 * @access private
	 */
	var $_nb_noise;
	
	/** 
	 * Holds the current selected TrueTypeFont
	 * @access private 
	 */
	var $_ttf_file;
	
	/** 
 	 * @access private 
	 */
	var $_public_K;
	
	/** 
	 * @access private 
	 */
	var $_private_K;
	
	/** 
	 * md5-key
	 * @access private 
	 */
	var $_key;
	
	/** 
	 * Public key
	 * @access private 
	 */
	var $_public_key;
	
	/** 
	 * Holds the Version Number of GD-Library
	 * @access private 
	 */
	var $_gd_version;
	
	/**
	 * Keeps the ($_GET) Querystring of the original Request
	 * @access private 
	 */
	var $_query;
	
	/** 
	 * @access private 
	 */
	var $_current_try = 0;
	
	/** 
	 * @access private 
	 */
	var $_r;
	
	/** 
	 * @access private 
	 */
	var $_g;
	
	/** 
	 * @access private 
	 */
	var $_b;

	/** 
	 * @access private 
	 */
	var $_messages = array(
		'msg1'				=> 'You must read and type the chars within 0..9 and A..F, and submit the form.',
		'msg2'				=> 'I cannot read this.',
		'buttontext'		=> 'submit',
		'refreshbuttontext'	=> 'Generate new ID',
		'invalid'			=> 'No valid entry. Please try again: '
	);
	

	/**
	 * Constructor
	 *
	 * Extracts the config array and generate needed params.
	 *
	 * @access public
	 */
	function Captcha( $config, $secure = true )
	{
		$this->ttf_folder = AP_ROOT_PATH . ap_ini_get( "path_fonts",  "path" );
		$this->tempfolder = ap_ini_get( "path_tmp_os", "path" );
			
		// Test for GD-Library(-Version)
		$this->_gd_version = ImageUtil::getGDVersion();
		
		if ( $this->_gd_version == 0 ) 
		{
			$this = new PEAR_Error( "There is no GD-Library-Support enabled. The Captcha-Class cannot be used!" );
			return;
		}

		// Hack prevention
		if ( ( isset( $_GET['maxtry'] ) || isset( $_POST['maxtry'] ) || isset( $_COOKIE['maxtry'] ) ) ||
			 ( isset( $_GET['captcharefresh']  ) || isset( $_COOKIE['captcharefresh'] ) ) ||
			 ( isset( $_POST['captcharefresh'] ) && isset( $_POST['private_key'] ) ) )
		{
			$this = new PEAR_Error( "Sorry. Seems to be hack." );
			return;
		}

		// extracts config Array
		if ( is_array( $config ) )
		{
			$valid = get_class_vars( get_class( $this ) );
			
			foreach ( $config as $k=>$v )
			{
				if ( array_key_exists( $k, $valid ) ) 
					$this->$k = $v;
			}
		}

		$temp = array();
		
		foreach ( $this->ttf_range as $k => $v )
		{
			if ( is_readable( $this->ttf_folder . $v ) ) 
				$temp[] = $v;
		}
			
		$this->ttf_range = $temp;
			
		if ( count( $this->ttf_range ) < 1 ) 
		{
			$this = new PEAR_Error( "Font files not found." );
			return;
		}

		// select first TrueTypeFont
		$this->_changeTTF();

		// get number of noise-chars for background if is enabled
		$this->_nb_noise = $this->noise? ( $this->chars * $this->_noisefactor ) : 0;

		// set dimension of image
		$this->_lx = ( $this->chars + 1 ) * (int)( ( $this->maxsize + $this->minsize ) / 1.5 );
		$this->_ly = (int)( 2.4 * $this->maxsize );
		
		// keep params from original GET-request
		// (if you use POST or COOKIES, you have to implement it yourself, sorry.)
		$this->_query = ( strlen( trim( $_SERVER['QUERY_STRING'] ) ) > 0 )? '?' . strip_tags( $_SERVER['QUERY_STRING'] ) : '';
		$refresh = $_SERVER['PHP_SELF'] . $this->_query;

		// check Postvars
		if ( isset( $_POST['public_key'] ) )  
			$this->_public_K = substr( strip_tags( $_POST['public_key'] ), 0, $this->chars );
		
		if ( isset( $_POST['private_key'] ) ) 
			$this->_private_K = substr( strip_tags( $_POST['private_key'] ), 0, $this->chars );
		
		$this->_current_try = isset( $_POST['hncaptcha'] )? $this->_getTry() : 0;
		
		if ( !isset( $_POST['captcharefresh'] ) ) 
			$this->_current_try++;

		// generate Keys
		$this->_key = Secret::getKey();
		$this->_public_key = substr( md5( uniqid( rand(), true ) ), 0, $this->chars );
	}


	/**
	 * Displays a complete form with captcha-picture.
	 *
	 * @access public
	 * @return string HTML
	 */
	function displayForm()
	{
		$try = $this->_getTry( false );
		
		$s  = '<div id="captcha">';
		$s .= '<form class="captcha" name="captcha1" action="' . $_SERVER['PHP_SELF'] . $this->_query . '" method="POST">' . "\n";
		$s .= '<input type="hidden" name="hncaptcha" value="' . $try . '">' . "\n";
		$s .= '<p class="captcha_notvalid">' . $this->_messages['invalid'] . '</p>';
		$s .= '<p class="captcha_1">' . $this->_displayCaptcha() . "</p>\n";
		$s .= '<p class="captcha_1">' . $this->_messages['msg1'] . '</p>';
		$s .= '<p class="captcha_1"><input class="captcha" type="text" name="private_key" value="" maxlength="' . $this->chars . '" size="' . $this->chars . '">&nbsp;&nbsp;';
		$s .= '<input class="captcha" type="submit" value="' . $this->_messages['buttontext'] . '">' . "</p>\n";
		$s .= '</form>' . "\n";
		
		if ( $this->refreshlink )
		{
			$s .= '<form style="display:inline;" name="captcha2" action="' . $_SERVER['PHP_SELF'] . $this->_query . '" method="POST">'."\n";
			$s .= '<input type="hidden" name="captcharefresh" value="1"><input type="hidden" name="hncaptcha" value="' . $try . '">'."\n";
			$s .= '<p class="captcha_2">' . $this->_messages['msg2'];
			$s .= $this->_publicKeyInput() . '<input class="captcha" type="submit" value="' . $this->_messages['refreshbuttontext'] . '">' . "</p>\n";
			$s .= '</form>' . "\n";
		}
		
		$s .= '</div>';
		return $s;
	}

	/**
	 * Validates POST-vars and return result.
	 *
	 * @access public
	 * @return int 0 = first call | 1 = valid submit | 2 = not valid | 3 = not valid and has reached maximum try's
	 */
	function validateSubmit()
	{
		if ( $this->_checkCaptcha( $this->_public_K, $this->_private_K ) )
		{
			return 1;
		}
		else
		{
			if ( $this->_current_try > $this->maxtry )
				return 3;
			else if ( $this->_current_try > 0 )
				return 2;
			else
				return 0;
		}
	}


	// private methods

	/** 
	 * @access private 
	 */
	function _displayCaptcha()
	{
		$this->_createCaptcha();
		$is = getimagesize( $this->_getFilename() );
		
		return $this->_publicKeyInput() . "\n" . '<img class="captchapict" src="' . $this->_getFilenameURL() . '" ' . $is[3] . '>' . "\n";
	}

	/** 
	 * @access private 
	 */
	function _publicKeyInput()
	{
		return '<input type="hidden" name="public_key" value="' . $this->_public_key . '">';
	}

	/** 
	 * @access private
	 * @return mixed
	 * @throws Error
	 */
	function _createCaptcha()
	{
		$private_key = $this->_generatePrivate();

		// create Image and set the apropriate function depending on GD-Version & websafecolor-value
		if ( $this->_gd_version >= 2 && !$this->websafecolors )
		{
			$func1 = 'imagecreatetruecolor';
			$func2 = 'imagecolorallocate';
		}
		else
		{
			$func1 = 'imageCreate';
			$func2 = 'imagecolorclosest';
		}
		
		$image = $func1( $this->_lx, $this->_ly );

		// Set background color
		$this->_randomColor( 224, 255 );
		$back = imagecolorallocate( $image, $this->_r, $this->_g, $this->_b );
		@imagefilledrectangle( $image, 0, 0, $this->_lx, $this->_ly, $back );

		// allocates the 216 websafe color palette to the image
		if ( $this->_gd_version < 2 || $this->websafecolors ) 
			ImageUtil::makeWebsafeColors( $image );

		// fill with noise or grid
		if ( $this->_nb_noise > 0 )
		{
			// random characters in background with random position, angle, color
			for ( $i = 0; $i < $this->_nb_noise; $i++ )
			{
				srand( (double)microtime() * 1000000 );
				$size = intval( rand( (int)( $this->minsize / 2.3 ), (int)( $this->maxsize / 1.7 ) ) );
				srand( (double)microtime() * 1000000 );
				$angle = intval(rand(0, 360));
				srand( (double)microtime() * 1000000 );
				$x = intval( rand( 0, $this->_lx ) );
				srand( (double)microtime() * 1000000 );
				$y = intval( rand( 0, (int)( $this->_ly - ( $size / 5 ) ) ) );
				$this->_randomColor( 160, 224 );
				$color = $func2( $image, $this->_r, $this->_g, $this->_b );
				srand( (double)microtime() * 1000000 );
				$text = chr( intval( rand( 45, 250 ) ) );
				@imagettftext( $image, $size, $angle, $x, $y, $color, $this->_changeTTF(), $text );
			}
		}
		else
		{
			// generate grid
			for ( $i = 0; $i < $this->_lx; $i += (int)( $this->minsize / 1.5 ) )
			{
				$this->_randomColor( 160, 224 );
				$color = $func2( $image, $this->_r, $this->_g, $this->_b );
				@imageline( $image, $i, 0, $i, $this->_ly, $color );
			}

			for ( $i = 0 ; $i < $this->_ly; $i += (int)( $this->minsize / 1.8 ) )
			{
				$this->_randomColor( 160, 224 );
				$color = $func2( $image, $this->_r, $this->_g, $this->_b );
				@imageline( $image, 0, $i, $this->_lx, $i, $color );
			}
		}

		// generate Text
		for ( $i = 0, $x = intval( rand( $this->minsize,$this->maxsize ) ); $i < $this->chars; $i++ )
		{
			$text = strtoupper( substr( $private_key, $i, 1 ) );
			srand( (double)microtime() * 1000000 );
			$angle = intval( rand( -30, 30 ) );
			srand( (double)microtime() * 1000000 );
			$size = intval( rand( $this->minsize, $this->maxsize ) );
			srand( (double)microtime() * 1000000 );
			$y = intval( rand( (int)( $size * 1.5 ), (int)( $this->_ly - ( $size / 7 ) ) ) );
			$this->_randomColor( 0, 127 );
			$color = $func2( $image, $this->_r, $this->_g, $this->_b );
			$this->_randomColor( 0, 127 );
			$shadow = $func2( $image, $this->_r + 127, $this->_g + 127, $this->_b + 127 );
			@imagettftext( $image, $size, $angle, $x + (int)( $size / 15 ), $y, $shadow, $this->_changeTTF(), $text );
			@imagettftext( $image, $size, $angle, $x, $y - (int)( $size / 15 ), $color, $this->_ttf_file, $text );
			$x += (int)( $size + ( $this->minsize / 5 ) );
		}
		
		@imagejpeg( $image, $this->_getFilename(), $this->_jpegquality );
		$res = file_exists( $this->_getFilename() );
		@imagedestroy( $image );

		if( !$res ) 
			return PEAR::raiseError( 'Unable to safe captcha-image.' );
		else
			return true;
	}

	/** 
	 * @access private 
	 */
	function _randomColor( $min, $max )
	{
		srand( (double)microtime() * 1000000 );
		$this->_r = intval( rand( $min, $max ) );
		srand( (double)microtime() * 1000000 );
		$this->_g = intval( rand( $min, $max ) );
		srand( (double)microtime() * 1000000 );
		$this->_b = intval( rand( $min, $max ) );
	}

	/** 
	 * @access private
	 * @return string
	 */
	function _changeTTF()
	{
		srand( (float) microtime() * 10000000 );
		$key = array_rand( $this->ttf_range );
		$this->_ttf_file = $this->ttf_folder . $this->ttf_range[$key];

		return $this->_ttf_file;
	}

	/** 
	 * @access private 
	 */
	function _checkCaptcha( $public,$private )
	{
		// when check, destroy picture on disk
		if ( file_exists( $this->_getFilename( $public ) ) )
			$res = @unlink( $this->_getFilename( $public ) )? true : false;
	
		$res = ( strtolower( $private ) == strtolower( $this->_generatePrivate( $public ) ) )? true : false;
		return $res;
	}

	/** 
	 * @access private 
	 */
	function _getFilename( $public = "" )
	{
		if ( $public == "" ) 
			$public = $this->_public_key;
		
		return $this->tempfolder . $public . ".jpg";
	}

	/** 
	 * @access private 
	 */
	function _getFilenameURL( $public = "" )
	{
		if ( $public == "" ) 
			$public = $this->_public_key;
		
		return str_replace( $_SERVER['DOCUMENT_ROOT'], '', $this->tempfolder ) . $public . ".jpg";
	}

	/** 
	 * @access private 
	 */
	function _getTry( $in = true )
	{
		$s = array( '0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f' );
		
		if ( $in )
		{
			return (int)substr( strip_tags( $_POST['hncaptcha'] ), ( $this->chars - 1 ) );
		}
		else
		{
			$a = "";
			
			for ( $i = 1; $i < $this->chars; $i++ )
			{
				srand( (double)microtime() * 1000000 );
				$a .= $s[intval( rand( 0, 15 ) )];
			}
			
			return $a . $this->_current_try;
		}
	}

	/** 
	 * @access private 
	 */
	function _generatePrivate( $public = "" )
	{
		if ( $public == "" ) 
			$public = $this->_public_key;
		
		$key = substr( md5( $this->_key . $public ), 16 - $this->chars / 2, $this->chars );
		return $key;
	}
}  // END OF Captcha

?>
