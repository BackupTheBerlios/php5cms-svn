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


using( 'util.text.encoding.UTF8' );
using( 'io.FolderUtil' );


/* The default parameters to use the GNUpg */
define( "GNUPGP_PARAMS", " --no-tty --no-secmem-warning --home " );

/* This is the minimum lenght accepted of the passphrase */
define( "GNUPGP_PASS_LENGTH", 8 );

/* If one recipient, from the recipient list, do not exist...
   this will return an error else, if one dont exist and all
   the other exist this continues (see function mountRecipients). */
define( "GNUPGP_FAIL_NO_RECIPIENT", 1 );

/* This generate or not logs in the HTTP log */
define( "GNUPGP_GEN_HTTP_LOG", 1 );


/**
 * @package security_crypt
 */
 
class GNUPGP extends PEAR
{
	/**
	 * this is the GNUpg Binary file
	 * @access public
	 */
	var $gpg_bin;
	
	/**
	 * the user name (owner of the keyrings)
	 * @access public
	 */
	var $userName;
	
	/**
	 * the user email (owner email)
	 * @access public
	 */
	var $userEmail;
	
	/**
	 * the subject of message
	 * @access public
	 */
	var $subject;
	
	/**
	 * the clean txt message to encript
	 * @access public
	 */
	var $message;
	
	/**
	 * the passphrase to decrypt the message
	 * @access public
	 */
	var $passphrase;
	
	/**
	 * the returned message encrypted
	 * @access public
	 */
	var $encrypted_message;
	
	/**
	 * the returned message decrypted
	 * @access public
	 */
	var $decrypted_message;
	
	/**
	 * the gpg base path to the private sub-dir of the user
	 * @access public
	 */
	var $gpg_path;
	
	/**
	 * the name of the recipient
	 * @access public
	 */
	var $recipientName;
	
	/**
	 * the recipient email
	 * @access public
	 */
	var $recipientEmail;
	
	/**
	 * this will be filled with the keys on the keyrings
	 * @access public
	 */
	var $keyArray;
	
	/**
	 * this is the variable used to export the owner public key (export_key)
	 * @access public
	 */
	var $public_key;
	
	/**
	 * boolean to indicate if the message will be encrypted with the user owner key
	 * @access public
	 */
	var $encrypt_myself;
	
	/**
	 * array with the list of recipients that are on the keyring
	 * @access public
	 */
	var $valid_keys;
	
	/**
	 * array with a list of recipient that are not on the keyring
	 * @access public
	 */
	var $not_valid_keys;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function GNUPGP()
	{
		$this->gpg_bin        = ap_ini_get( "file_pgp", "file" );
		$this->gpg_path       = "/var/www/gpg/";
		
		$this->subject        = $subject;
		$this->message        = $message;
		$this->recipientEmail = $recipientEmail;
		$this->recipientName  = $recipientName;
		$this->userName       = $userName;
		$this->userEmail      = $userEmail;
		$this->passphrase     = $passphrase;
		$this->encrypt_myself = $encrypt_myself;
	
  		// verifies that the GNUpg binary exists
		if ( !file_exists( $this->gpg_bin ) )
		{
			$this = new PEAR_Error( "GNUpg binary file does not exist." );
			return;
		}

		// check that the GNUpg binary is executable
		if ( !is_executable( $this->gpg_bin ) )
		{
			$this = new PEAR_Error( "GNUpg binary file is not executable." );
			return;
 		}
 	}


	/**
	 * This function check if the private gnupg dir exist for the user $userName.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function checkPrivateDir()
	{
 		// clear the filesystem cache
		clearstatcache();

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";

		// check if the user dir exists
		if ( !is_dir( $priv_path ) )
			return PEAR::raiseError( "The user dir doesn't exist." );

		return true;
 	}

	/**
	 * This function check if the pubring.gpg exists.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function checkPubRing()
	{
 		// clear the filesystem cache
		clearstatcache();

		$file = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg/pubring.gpg";

		// check if the user dir exists
		if ( !file_exists( $file ) )
			return PEAR::raiseError( "The user pubring does not exists." );

		return true;
 	}
 
	/**
	 * This function check the private dir and the pubring.
	 *
	 * @access public
	 * @return boolean
	 */
 	function checkAll()
	{
		$res = $this->checkPrivateDir();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		$res = $this->checkPubRing();
		
		if ( PEAR::isError( $res ) )
			return $res;
		
		return true;
 	}

	/**
  	 * This function return an array of valid recipients to receive the message encrypted
  	 * (to be a valid recipient, the recipient must be on the keyring), and an array with
  	 * invalid recipient (that isn't on the key ring). 
     *
  	 * NOTE!!! IF GNUPGP_FAIL_NO_RECIPIENT IS 0 AND ONE (OR MORE) RECIPIENTS ARE NOT IN THE KEYRING,
  	 * THIS FUNCTION WILL RETURN FALSE (THIS IS THE DEFAULT). OTHERWISE, IF GNUPGP_FAIL_NO_RECIPIENT
     *
  	 * IS SET TO 1, THE FUNCTION WILL NOT RETURN AN ERROR MESSAGE AND WILL CONTINUES NORMALY.
  	 * YOU CAN SET GNUPGP_FAIL_NO_RECIPIENT TO 1 AND MAKE THE USE OF THE $this->not_valid_keys TO 
  	 * FIND WHAT IS THE RECIPIENT THAT ARE NOT IN THE KEYRING.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
  	function mountRecipients( $recipients )
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;
  	
		// clear vars
		unset( $this->valid_recipients, $this->unvalid_recipients );
		unset( $keys, $valid_keys, $not_valid_keys );

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";

		// call the gpg to list the keys
		$tmp = explode( ";", $recipients );	// create a temp array with all the recipients

		for ( $i = 0; $i < count( $tmp ); $i++ )
		{
			// mount the command to list the keys
			$command = $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --with-colons --list-key " . trim( $tmp[$i] );
		
			if ( GNUPGP_GEN_HTTP_LOG )
				$command .= " 2>/dev/null";
		
			// execute the list-key command for all recipients separeted
			exec( $command, $keyArray, $errorcode ); 

			if ( $errorcode )
			{
				if ( GNUPGP_FAIL_NO_RECIPIENT ) 
					return PEAR::raiseError( "One or more recipients are not in the keyring." );
			
				$not_valid_keys .= trim( $tmp[$i] ) . ";";
			} 
			else 
			{
				for ( $j = 0; $j < count( $keyArray ); $j += 2 )
				{
					$keys = array( explode( ":", $keyArray[$j] ) );
					$valid_keys .= $keys[0][9] . ";";
				}
			
				unset( $keyArray );
			}
		}

		$this->valid_keys     = explode( ";", $valid_keys );
		$this->not_valid_keys = explode( ";", $not_valid_keys );
		
 		return true;
  	}
  
	/**
  	 * $keyID = the key(s) to check if exist.
  	 * This can be a simple key or various keys separeded with ';'
     *
  	 * Check if exist the user dir exist and if the keyID is on the keyring.
  	 * Returns false when failed, or true.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function checkKeyID( $keyID )
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";

		// call the gpg to list the keys
		$command = $this->gpg_bin . GNUPGP_PARAMS . $priv_path;

		$tmp = explode( ";", $keyID );
	
		// list-key for every recipient
		for ( $i = 0; $i < count( $tmp ); $i++ )
			$command .= " --list-key " . trim( $tmp[$i] );	

		if ( GNUPGP_GEN_HTTP_LOG )
			$command .= " 2>/dev/null";

		exec( $command, $keyArray, $errocode );
	
		if ( $errorcode )
			return PEAR::raiseError( "The keyID isn't on the keyring." );
		
		if ( count( $keyArray > 0 ) ) 
			return true;
		else 
			return PEAR::raiseError( "The keyID isn't on the keyring." );
 	}

	/**
	 * List all the publics keys on the keyrings.
  	 * Return an array ($this->keyArray) with the keys.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function listKeys()
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;

		$res = $this->checkKeyID( $this->userName );
		
 		if ( PEAR::isError( $res ) )
			return $res;

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";
		$command   = $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --list-key --fingerprint --with-colons";

		if ( GNUPGP_GEN_HTTP_LOG )
			$command .= " 2>/dev/null";

		exec( $command, $keyArray, $errorcode );

		if ( $errorcode )
			return PEAR::raiseError( "Can't list the keys." );

		unset( $this->keyArray );
		$this->keyArray = $keyArray;
	
		return true;	 
 	}

	/**
	 * Encrypt a clean txt message.
  	 * Returns false when failed, or the encrypted message in the $this->encrypted_message, when (if) succeed.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function encryptMessage()
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;
	
 		// first check if the key is on the keyring
		$res = $this->checkKeyID( $this->recipientEmail );
		
		if ( PEAR::isError( $res ) )
			return $res;

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";
	
		// generate token for unique filenames
		$tmpToken = md5( uniqid( rand() ) );

		// create vars to hold paths and filenames
		$plainTxt   = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . DIRECTORY_SEPARATOR . $tmpToken . ".data";
		$cryptedTxt = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . DIRECTORY_SEPARATOR . $tmpToken . ".pgp";

		// open .data file and dump the plaintext contents into this
		$fd = @fopen( $plainTxt, "w+" );
	
		if ( !$fd )
			return PEAR::raiseError( "Can't create the .data file. Verify if you have write access on the dir." );
	
		@fputs( $fd, $this->message );
		@fclose( $fd );

		$this->encrypt_myself = true;

		// invoque the GNUgpg to encrypt the plaintext file
		$command = $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --always-trust --armor";
	
		// mount the valid recipient array
		$res = $this->mountRecipients( $this->recipientEmail );
		
		if ( PEAR::isError( $res ) )
	 		return $res;

		for ( $i = 0; $i < count( $this->valid_keys ); $i++ )
		{
			if ( trim( $this->valid_keys[$i] ) != "" )
		 		$command .= " --recipient '" . $this->valid_keys[$i] . "'";
		}

		// include the message to yourself
		if ( $this->encrypt_myself )
			$command .= " --recipient '$this->userEmail'";

		$command .= " --output '$cryptedTxt' -e $plainTxt";

		if ( GNUPGP_GEN_HTTP_LOG )
			$command .= " 2>/dev/null";

		// execute the command
		system( $command, $errorcode );

		if ( $errorcode )
		{
			@unlink( $plainTxt );		
			return PEAR::raiseError( "Can't crypt the message." );
		} 
		else 
		{
			// open the crypted file and read contents into var
			$fd  = @fopen( $cryptedTxt, "r" );
			$tmp = @fread( $fd, filesize( $cryptedTxt ) );
			@fclose( $fd );
		
			// delete all the files
			@unlink( $plainTxt   );
			@unlink( $cryptedTxt );

			// verifies the PGP signature
			if ( ereg( "-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----", $tmp ) ) 
			{
				$this->encrypted_message = $tmp;
				unset( $tmp );
        		
				return true;
			} 
			else 
			{
				unset( $tmp );
				return PEAR::raiseError( "The header/footer of the crypt message isn't valid." );
			}
		}
 	}

	/**
	 * Decrypt the armored crypted message.
  	 * Returns false when failed, or decrypted message in the $this->decrtypted_message, when (if) succeed.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function decryptMessage( $message, $passphrase )
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;
	
 		// first check if the key is on the keyring
		$res = $this->checkKeyID( $this->recipientEmail );
		
		if ( PEAR::isError( $res ) )
			return $res;
	
		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";

		// check the header/footer of message to see if this is a valid PGP message
 		if ( !ereg( "-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----", $message ) ) 
		{
			unset( $passphrase );
			return PEAR::raiseError( "The header/footer of message not appear to be a valid PGP message." );
		} 
		else 
		{
	 		// generate token for unique filenames
			$tmpToken = md5( uniqid( rand() ) );
		
			// create vars to hold paths and filenames
			$plainTxt   = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . DIRECTORY_SEPARATOR . $tmpToken . ".data";
			$cryptedTxt = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . DIRECTORY_SEPARATOR . $tmpToken . ".gpg";
	
			// create/open .pgp file and dump the crypted contents
			$fd = @fopen( $cryptedTxt, "w+" );
		
			if ( !$fd )
			{
				unset( $passphrase );			
				return PEAR::raiseError( "Can't create the .gpg file. Verify that you have write acces on the directory." );
			}
		
			@fputs( $fd, $message );
			@fclose( $fd );
		
			// create the command to execute
			$command = "echo '$passphrase' | " . $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --batch --passphrase-fd 0 -r '$this->userName' -o $plainTxt --decrypt $cryptedTxt";

			if ( GNUPGP_GEN_HTTP_LOG )
				$command .= " 2>/dev/null";

			// execute the command to decrypt the file
			system( $command, $errcode );

			unset( $passphrase );
		
			// open the decrypted file and read contents into var
			$fd = @fopen( $plainTxt, "r" );
		
			if ( !$fd )
			{
				@unlink( $cryptedTxt );			
				return PEAR::raiseError( "Can't read the .asc file. Verify if you have entered the correct user/password." );
			}
			
			$this->decrypted_message = @fread( $fd, filesize( $plainTxt ) );
			@fclose( $fd );
		
			// delete all the files
			@unlink( $plainTxt );
			@unlink( $cryptedTxt );
	
			return true;		
		}
 	}

	/**
	 * Import public key to keyring. NOTE: IT MUST BE IN ARMORED FORMAT (ASC).
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function importKey( $key = "" )
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;
	
 		// first check if the key is on the keyring
		$res = $this->checkKeyID( $this->recipientEmail );
		
		if ( PEAR::isError( $res ) )
			return $res;

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";

	 	// check if the key to import isn't empty
		if ( $key == "" )
			return PEAR::raiseError( "No public key file specified." );

		// Checks the header/footer to see if is a valid PGP PUBLIC KEY
		if ( !ereg( "-----BEGIN PGP PUBLIC KEY BLOCK-----.*-----END PGP PUBLIC KEY BLOCK-----", $key ) ) 
		{
			return PEAR::raiseError( "The header/footer of message not appear to be a valid PGP message." );
		} 
		else 
		{
		 	// generate token for unique filenames
			$tmpToken = md5( uniqid( rand() ) );
		
			// create vars to hold paths and filenames
			$tmpFile = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . DIRECTORY_SEPARATOR . $tmpToken . ".public.asc";

			// open file and dump in plaintext contents
			$fd = @fopen( $tmpFile, "w+" );

			if ( !$fd )
				return PEAR::raiseError( "Can't creates .tmp file to add the key. Verify that you have write access in the dir." );
	
			@fputs( $fd, $key );
			@fclose( $fd );

			$command = $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --import '$tmpFile'";

			if ( GNUPGP_GEN_HTTP_LOG )
				$command .= " 2>/dev/null";

			system( $command, $errorcode );
	
			if ( $errorcode )
			{
				@unlink( $tmpFile );	
				return PEAR::raiseError( "Can't add the public key." );
			} 
			else 
			{
				@unlink( $tmpFile );
				return true;
			}
		}
 	}

	/**
	 * Export the owner public key in asc armored format.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 * @todo   option to make an file to attachment
	 */
 	function exportKey()
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;

	 	// first check if the key is on the keyring
		$res = $this->checkKeyID( $this->recipientEmail );
		
		if ( PEAR::isError( $res ) )
			return $res;
	
		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";
		$command   = $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --batch --armor --export '" . $this->userEmail . "'";

		if( GNUPGP_GEN_HTTP_LOG )
			$command .= " 2>/dev/null";

		exec( $command, $result, $errorcode );

		if ( $errorcode )
			return PEAR::raiseError( "Can't export the public key." );

		$this->public_key = implode( "\n", $result );
		return true;
 	}
 
	/**
	 * Remove a public key from keyring.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function removeKey( $key = "" )
	{
		$res = $this->checkAll();
		
  		if ( PEAR::isError( $res ) )
			return $res;
	
 		// first check if the key is on the keyring
		$res = $this->checkKeyID( $this->recipientEmail );
		
		if ( PEAR::isError( $res ) )
			return $res;

		$priv_path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail ) . "/.gnupg";

	  	if ( $key == "" )
			return PEAR::raiseError( "No specified public key to remove." );

		$command = $this->gpg_bin . GNUPGP_PARAMS . $priv_path . " --batch --yes --delete-key '$key'";

		if ( GNUPGP_GEN_HTTP_LOG )
			$command .= " 2>/dev/null";

		system( $command, $errorcode );

		if ( $errorcode ) 
			return PEAR::raiseError( "Can't remove the key." );

		return true;
 	}

	/**
	 * Make the generation of keys controlled by a parameter file.
  	 * This feature is not very well tested and is not very well documented.
  	 * Just use this if you do not have how to generate the key in a secure machine.
	 *
	 * @access public
	 * @return mixed
	 * @throws Error
	 */
 	function genKey( $userName, $comment = "", $userEmail, $passphrase )
	{
 		if ( empty( $userName ) )
			return PEAR::raiseError( "The username is empty." );
 	
		if ( empty( $userEmail ) )
			return PEAR::raiseError( "The email is empty." );
 	
		if ( empty( $passphrase ) )
			return PEAR::raiseError( "The passphrase is empty." );
	
		if ( strlen( trim( $passphrase ) ) < GNUPGP_PASS_LENGTH )
			return PEAR::raiseError( "The passphrase is too short." );
	
		if ( $this->checkPrivateDir() )
			return PEAR::raiseError( "The user dir already exist." );

	 	$path = $this->gpg_path . ereg_replace( "[@]", "_", $this->userEmail );

	 	// create the user dir (if this not exists)
 		if ( !file_exists( $path ) )
		{
			if ( !mkdir( $path, 0700 ) )
				return PEAR::raiseError( "Can't create a new user dir." );
		
			if ( !mkdir( $path . "/.gnupg", 0700 ) )
				return PEAR::raiseError( "Can't create the gnupg dir." );
		} 
		else 
		{
			return PEAR::raiseError( "The user dir already exist." );
		}
	
		$utf = new UTF8;
		$utf->loadmap( AP_ROOT_PATH . ap_ini_get( "path_repository_encodings", "path" ) . "8859-1.txt", "iso" );

		// prepares the temporary config file
 		$tmpConfig  = "Key-Type: DSA\r\nKey-Length: 1024\r\nSubkey-Type: ELG-E\r\nSubkey-Length: 2048\r\n";
		$tmpConfig .= "Name-Real: " . $utf->cp2utf( $userName, "iso" ) . "\r\n";
	
		if ( !empty( $comment ) )
			$tmpConfig .= "Name-Comment: " . $utf->cp2utf( $comment ) . "\r\n";
	
		$tmpConfig .= "Name-Email: " . $userEmail . "\r\nExpire-Date: 0\r\nPassphrase: " . $passphrase . "\r\n";
		$tmpConfig .= "%commit\r\n";

		// generate token for unique filenames
		$tmpToken = md5( uniqid( rand() ) );

		// create vars to hold paths and filenames
		$tmpConfigFile = $path . DIRECTORY_SEPARATOR . $tmpToken . ".conf";

		// open .data file and dump the plaintext contents into this
		$fd = @fopen( $tmpConfigFile, "w+" );
	
		if ( !$fd )
			return PEAR::raiseError( "Can't create the temporary config file. Verify if you have write permission on the dir." );
	
		@fputs( $fd, $tmpConfig );
		@fclose( $fd );

		unset( $tmpConfig );

		// invoke the GNUgpg to generate the key
		$home    = $path . "/.gnupg";
		$command = $this->gpg_bin . GNUPGP_PARAMS . "$home --batch --gen-key -a $tmpConfigFile";

		if ( GNUPGP_GEN_HTTP_LOG )
			$command .= " 2>/dev/null";

		system( $command, $errorcode );
		@unlink( $tmpConfigFile );

		if ( $errorcode )
		{
			FolderUtil::removeFoldersRecursivelly( $path );
			return PEAR::raiseError( "Can't generate the key." );	
		} 
		else 
		{
			return true;
		}
 	}
} // END OF GNUPGP

?>
