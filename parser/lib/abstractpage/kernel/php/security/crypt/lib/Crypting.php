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


using( 'sys.ServerUtil' );
using( 'util.Util' );


if ( !defined( "CRYPTING_DEFAULT_SALT" ) )
	define( "CRYPTING_DEFAULT_SALT", "MD5" );


/**
 * @package security_crypt_lib
 */
 
class Crypting extends PEAR
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = false;

	/**
     * @var    array
	 * @access private
     */
    var $_options = array();
	
	/**
	 * @var	   string
	 * @access private
	 */
	var $_salt = null;

	/**
	 * @var	   string
	 * @access private
	 */
	var $_key = null;
	
    /**
     * The temporary directory to use.
     *
     * @var string $_tempdir
     */
    var $_tempdir;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Crypting( $options = array() )
	{
		$this->_options = $options;
	}
	
	
    /**
     * Attempts to return a concrete Crypting instance based on $driver.
     *
     * @param mixed $driver  The type of concrete Crypting subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Crypting  The newly created concrete Crypting instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {	
        $driver = strtolower( $driver );
		
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new Crypting( $options );
	
        $crypt_class = "Crypting_" . $driver;

		using( 'security.crypt.lib.' . $crypt_class );
		
		if ( class_registered( $crypt_class ) )
	        return new $crypt_class( $options );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }

    /**
     * Attempts to return a reference to a concrete Crypting instance
     * based on $driver. It will only create a new instance if no
     * Crypting instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple Crypting instances) are required.
     *
     * This method must be invoked as: $var = &Crypting::singleton()
     *
     * @param mixed $driver  The type of concrete Crypting subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Crypting  The concrete Crypting reference, or false on an
     *                       error.
     */
    function &singleton( $driver, $options = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode('][', $options ) );
        
		if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &Crypting::factory( $driver, $options );

        return $instances[$signature];
    }
	
	/**
	 * @access public
	 */
	function isReversible()
	{
		return $this->_is_reversible;
	}
	
	/**
	 * Set salt.
	 *
	 * @param  string  $salt
	 * @access public
	 */
	function setSalt( $salt )
	{
		$this->_salt = $salt;
	}
	
	/**
	 * Get salt.
	 *
	 * @return string
	 * @access public
	 */
	function getSalt()
	{
		return $this->_salt;
	}
	
	/**
	 * Set key.
	 *
	 * @param  string  $key
	 * @access public
	 */
	function setKey( $key )
	{
		$this->_key = $key;
	}
	
	/**
	 * Get key.
	 *
	 * @return string
	 * @access public
	 */
	function getKey()
	{
		return $this->_key;
	}
	
	/**
	 * Encrypt text.
	 *
	 * @access public
	 */
	function encrypt( $text, $params = array() )
	{
		if ( is_null( $params['salt'] ) )
		{
			if ( $this->_salt !== null )
				$salt = $this->_salt;
			else
				$salt = Crypting::_strongestSalt();
		}
		else
		{
			$salt = $params['salt'];
		}
			
		return crypt( $text, $salt );
	}
	
	/**
	 * Decrypt text.
	 *
	 * @access public
	 */
	function decrypt( $text, $params = array() )
	{
		return PEAR::raiseError( 'Decrypt method not available for this algorithm.' );
	}
	
    /**
     * Outputs error message if we are not using a secure connection.
     *
     * @access public
     *
     * @return object Error       Returns a Error object if there is no
     *                            secure connection.
     */
    function requireSecureConnection()
    {
        if ( !ServerUtil::usingSSLConnection() )
            return PEAR::raiseError( "The encryption features require a secure web connection." );
    }
	
	/**
	 * Check if mcrypt extension is available.
	 *
	 * @static 
	 */
	function useMCrypt()
	{
		if ( Util::extensionExists( "mcrypt" ) && function_exists( "mcrypt_ecb" ) )
			return true;
		else 
			return false;
	}
	
	/**
	 * @static
	 */
	function octetStringToInt( $sString )
	{
		$qNumber = '0';
		$istrlen = strlen( $sString );
	
		for ( $i = 0; $i < $istrlen; $i++ )
		{
			$iChar   = ord( $sString[$istrlen - $i - 1] );
			$qNumber = bcadd( $qNumber, bcmul( $iChar, bcpow( '256', $i ) ) );
		}

		return $qNumber;
	}

	/**
	 * @static
	 */
	function intToOctetString( $qNumber, $iLength = -1 )
	{
		$sString = '';

		if ( $iLength < 0 )
		{
			while ( $qNumber != '0' )
			{
				$sString = chr( bcmod( $qNumber, '256' ) ) . $sString;
				$qNumber = bcdiv( $qNumber, '256' );
			}
		}
		else
		{
			for ( $i = 0; $i < $iLength; $i++ )
			{
				$sString = chr( bcmod( $qNumber, '256' ) ) . $sString;
				$qNumber = bcdiv( $qNumber, '256' );
			}
		}

		return $sString;
	}

	/**
	 * @static
	 */
	function stretch( &$sString1, &$sString2, $sChar )
	{
		$iChars = max( strlen( $sString1 ), strlen( $sString2 ) );
	
		$sString1 = str_pad( $sString1, $iChars, $sChar, STR_PAD_LEFT );
		$sString2 = str_pad( $sString2, $iChars, $sChar, STR_PAD_LEFT );
	}
	
	/**
	 * @static
	 */
	function swap( &$xVariable1, &$xVariable2 )
	{
		$xTemp      = $xVariable1;
		$xVariable1 = $xVariable2;
		$xVariable2 = $xTemp;
	}
	
		
	// private methods

	/**
	 * Get strongest DES-key available for this box.
	 *
	 * @access	private
	 * @static
	 */	
	function _strongestSalt()
	{
    	$pre = "";
    
    	if ( CRYPTING_DEFAULT_SALT == "DES" && CRYPT_STD_DES == 1 )
		{
        	$length = 2;
    	}
    	else if ( CRYPTING_DEFAULT_SALT == "MD5" && CRYPT_MD5 == 1 ) 
		{
        	$length = 9;
        	$pre = "$1$";
    	}
    	else 
		{
        	$length = 2;
    	}
    
    	$salt = "";    
    
		for ( $x = 0; $x < $length; $x++ )
        	$salt .= substr( crypt( rand( 1, 65536 ) ), 3, 1 );
    
    	return $pre . $salt;
	}
	
    /**
     * Create a temporary file that will be deleted at the end of this
     * process.
     *
     * @access private
     *
     * @param optional string  $descrip  Description string to use in filename.
     * @param optional boolean $delete   Delete the file automatically?
     *
     * @return string  Filename of a temporary file.
     */
    function _createTempFile( $descrip = 'ap-crypt', $delete = true )
    {
        if ( empty( $this->_tempdir ) )
            $this->_tempdir = Util::createTempDir();

        return Util::getTempFile( $descrip, $delete, $this->_tempdir, true );
    }
} // END OF Crypting

?>
