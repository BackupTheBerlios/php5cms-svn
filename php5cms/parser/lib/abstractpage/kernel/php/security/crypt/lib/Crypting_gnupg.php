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


using( 'security.crypt.lib.Crypting' );


/**
 * This class allows PGP encryption of plain text by calling an existing
 * installation of the GNU Privacy Guard on *nix systems.
 * 
 * GPG must be installed, have a default private key and public keys 
 * imported for any user specified in the recipients array.  You should
 * also create a temp directory for use by the GPG executable.
 * 
 * In order to reach your keyring, PHP will likely have to run as a CGI 
 * under your user (using suexec).  If this is unacceptable, a workaround 
 * is to give the PHP user (usually "nobody") access to your keyrings with
 * chmod, but, of course, this potentially allows all local PHP developers
 * access to your private key if they know where to look.  Note that this 
 * is not of great concern if the system is used as purely one-way secure
 * transmission from the server to an individual, eg. online order form 
 * contents encrypted and sent to seller.
 * 
 * Keep in mind that the CGI version of PHP may be of a different version 
 * than mod_PHP and will not have access to a mod_PHP session file unless a
 * mod_PHP script 1st chmod'ed the particular file.
 *
 * @todo some better error reporting, it's quite lousy by now
 * @package security_crypt_lib
 */
 
class Crypting_gnupg extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = false;

	/**
	 * @access private
	 */
    var $_temp_hash;
	
	/**
	 * @access private
	 */
    var $_gnupg_home;
	
	
	/**
	 * Constructor
	 *
	 * @param array
	 *      o gnupghome  string  path to gnupg installation (must be read/writeable)
	 * 		o gnupgtemp  string  path to gnupg temp dir (must be read/writeable)
	 *
	 * @access public
	 */
	function Crypting_gnupg( $options = array() )
	{
		$this->Crypting( $options );
		
		// TODO: do some path checking
		
		if ( !isset( $options['gnupghome'] ) )
		{
			$this = new PEAR_Error( 'No path to .gnupg given.' );
			return;
		}
		else
		{
			$this->_gnupg_home = $options['gnupghome'];
		}	
		
		if ( !isset( $options['gnupgtemp'] ) )
		{
			$this = new PEAR_Error( 'No path to temporary gnupg dir given.' );
			return;
		}
		else
		{
	        $this->_temp_hash  = $options['gnupgtemp'] . md5( microtime() );
		}
	}
	
	
	/**
	 * Encrypt text.
	 *
	 * @param  string $plaintext
	 * @param  array  $params (recipients - Recipients such as array( 'Paul McCartney', 'one@two.com' ) )
	 * @return string
	 * @access public
	 */
	function encrypt( $plaintext, $params = array() )
	{
        if ( !$this->_saveplain( $plaintext ) )
        	return false;

        $plainfile = $this->_temp_hash . '.plain'; 
        $gpgedfile = $this->_temp_hash . '.gpg'; 

        // form command        
        $command = "gpg -e -q --no-secmem-warning ";

        foreach ( $params['recipients'] as $recipient )
            $command .= "-r '$recipient' ";
        
        $command .= "-ao '$gpgedfile' '$plainfile'  2>&1";
        
        // set env variable
        putenv( "GNUPGHOME=" . $this->_gnupg_home );

        // encrypt
        $result .= exec( $command );

        // read in and clean up        
        $encrypted = $this->_readgpg();
        $this->_deleteFiles();    

        return ( $result == "" )? $encrypted : false;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
    function _saveplain( &$plain_text )
    {
        $plainfile = $this->_temp_hash . ".plain";
        $fp = fopen( $plainfile, 'w' );
        
		if ( !$fp || fwrite( $fp, $plain_text ) == -1 )
            return false;
		
        fclose( $fp );
        return true;
    }

	/**
	 * @access private
	 */
    function _readgpg()
    {
        $gpgedfile = $this->_temp_hash . ".gpg"; 
        $fp = fopen( $gpgedfile, 'r' );
        
		if ( !$fp )
            return false;
		
        $encrypted = fread( $fp, filesize ( $gpgedfile ) );
        fclose( $fp );
        
		return $encrypted;
    }

	/**
	 * @access private
	 */    
    function _deleteFiles()
    {
        $plainfile = $this->_temp_hash . '.plain'; 
        $gpgedfile = $this->_temp_hash . '.gpg'; 
        
        if ( !unlink( $plainfile ) || !unlink( $gpgedfile ) )
            return false;
		
        return true;
    }
} // END OF Crypting_gnupg

?>
