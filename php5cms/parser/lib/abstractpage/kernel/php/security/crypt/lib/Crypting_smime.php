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
|Authors: Mike Cochrane <mike@graftonhall.co.nz>                       |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'peer.mail.mime.lib.MIME_Part' );
using( 'peer.mail.mime.lib.MIME_Structure' );
using( 'util.Util' );


/**
 * Crypting_smime provides a framework to interact with the OpenSSL library.
 *
 * @package security_crypt_lib
 */
 
class Crypting_smime extends Crypting
{
	/**
	 * Indicates if algorithm is reversible.
	 *
	 * @var	   boolean
	 * @access private
	 */
	var $_is_reversible = true;
	
    /**
     * Object Identifers to name array.
     *
     * @var array $_oids
     */
	var $_oids = array(
		'2.5.4.3'   => 'CommonName',
        '2.5.4.4'   => 'Surname',
        '2.5.4.6'   => 'Country',
        '2.5.4.7'   => 'StateOrProvince',
        '2.5.4.8'   => 'Location',
        '2.5.4.9'   => 'StreetAddress',
        '2.5.4.10'  => 'Organisation',
        '2.5.4.11'  => 'OrganisationalUnit',
        '2.5.4.12'  => 'Title',
        '2.5.4.20'  => 'TelephoneNumber',
        '2.5.4.42'  => 'GivenName',

        '2.5.29.14' => 'id-ce-subjectKeyIdentifier',

        '2.5.29.14' => 'id-ce-subjectKeyIdentifier',
        '2.5.29.15' => 'id-ce-keyUsage',
        '2.5.29.17' => 'id-ce-subjectAltName',
        '2.5.29.19' => 'id-ce-basicConstraints',
        '2.5.29.31' => 'id-ce-CRLDistributionPoints',
        '2.5.29.32' => 'id-ce-certificatePolicies',
        '2.5.29.35' => 'id-ce-authorityKeyIdentifier',
        '2.5.29.37' => 'id-ce-extKeyUsage',

        '1.2.840.113549.1.9.1'   => 'Email',
        '1.2.840.113549.1.1.1'   => 'RSAEncryption',
        '1.2.840.113549.1.1.2'   => 'md2WithRSAEncryption',
        '1.2.840.113549.1.1.4'   => 'md5withRSAEncryption',
        '1.2.840.113549.1.1.5'   => 'SHA-1WithRSAEncryption',
        '1.2.840.10040.4.3'      => 'id-dsa-with-sha-1',

        '1.3.6.1.5.5.7.3.2'      => 'id_kp_clientAuth',

        '2.16.840.1.113730.1.1'  => 'netscape-cert-type',
        '2.16.840.1.113730.1.2'  => 'netscape-base-url',
        '2.16.840.1.113730.1.3'  => 'netscape-revocation-url',
        '2.16.840.1.113730.1.4'  => 'netscape-ca-revocation-url',
        '2.16.840.1.113730.1.7'  => 'netscape-cert-renewal-url',
        '2.16.840.1.113730.1.8'  => 'netscape-ca-policy-url',
        '2.16.840.1.113730.1.12' => 'netscape-ssl-server-name',
        '2.16.840.1.113730.1.13' => 'netscape-comment',
    );

	
    /**
     * Constructor
     *
     * @access public
     *
     * @param optional array $options  Parameter array.
     */
    function Crypting_smime( $options = array() )
    {
		$this->Crypting( $options );
    }

	
    /**
     * Encrypt text using SMIME.
     *
     * @access public
     *
     * @param string $plaintext   The text to be encrypted.
     * @param array $params  The parameters needed for encryption.
     *                       See the individual _encrypt*() functions for
     *                       the parameter requirements.
     *
     * @return string  The encrypted message.
     *                 Returns Error object on error.
     */
    function encrypt( $plaintext, $params = array() )
    {
		if ( !Util::extensionExists( 'openssl' ) )
            return PEAR::raiseError( "The openssl module is required for the Crypting_smime class." );

        if ( array_key_exists( 'type', $params ) ) 
		{
            if ( $params['type'] === 'message' )
                return $this->_encryptMessage( $plaintext, $params );
            else if ( $params['type'] === 'signature' )
                return $this->_encryptSignature( $plaintext, $params );
        }
    }

    /**
     * Decrypt text using smime.
     *
     * @access public
     *
     * @param string $ciphertext   The text to be smime decrypted.
     * @param array $params  The parameters needed for decryption.
     *                       See the individual _decrypt*() functions for
     *                       the parameter requirements.
     *
     * @return string  The decrypted message.
     *                 Returns Error object on error.
     */
    function decrypt( $ciphertext, $params = array() )
    {
		if ( !Util::extensionExists( 'openssl' ) )
            return PEAR::raiseError( "The openssl module is required for the Crypting_smime class." );
			
        if ( array_key_exists( 'type', $params ) ) 
		{
            if ( $params['type'] === 'message' ) 
                return $this->_decryptMessage( $ciphertext, $params );
			else if ( ( $params['type'] === 'signature' ) || ( $params['type'] === 'detached-signature' ) )
                return $this->_decryptSignature( $ciphertext, $params );
        }
    }
	
    /**
     * Verify a passphrase for a given public/private keypair.
     *
     * @access public
     *
     * @param string $public_key   The user's public key.
     * @param string $private_key  The user's private key.
     * @param string $passphrase   The user's passphrase.
     *
     * @return boolean  Returns true on valid passphrase, false on invalid
     *                  passphrase.
     *                  Returns Error on error.
     */
    function verifyPassphrase( $public_key, $private_key, $passphrase )
    {
        /* Check for secure connection. */
        $secure_check = $this->requireSecureConnection();
        
		if ( PEAR::isError( $secure_check ) )
            return $secure_check;

        /* Encrypt a test message. */
        $result = $this->encrypt( 'Test', array(
			'type'   => 'message', 
			'pubkey' => $public_key
		) );
        
		if ( PEAR::isError( $result ) )
            return false;

        /* Try to decrypt the message. */
        $result = $this->decrypt($result, array(
			'type'       => 'message', 
			'pubkey'     => $public_key, 
			'privkey'    => $private_key, 
			'passphrase' => $passphrase
		) );
        
		if ( PEAR::isError( $result ) )
            return false;

        return true;
    }

    /**
     * Verify a signatures using smime.
     *
     * @access public
     *
     * @param string $text  The multipart/signed data to be verified.
     * @param mixed $certs  Either a single or array of root certificates.
     *
     * @return boolean  Returns true on success.
     *                  Returns Error object on error.
     */
    function verify( $text, $certs )
    {
		if ( !Util::extensionExists( 'openssl' ) )
            return PEAR::raiseError( "The openssl module is required for the Crypting_smime class." );

        /* Create temp files for input/output. */
        $input = $this->_createTempFile( 'ap-smime' );
        $dummy = $this->_createTempFile( 'ap-smime' );

        /* Write text to file */
        $fp = fopen( $input, 'w+' );
        fwrite( $fp, $text );
        fclose( $fp );

        $root_certs = array();

		// pick up openssl_cafile array from config

        $result = @openssl_pkcs7_verify( $input, PKCS7_DETACHED, $dummy, $root_certs );

        /* Message verified */
        if ( $result === true )
            return true;

        /* Try again without verfying the signer's cert */
        $result = openssl_pkcs7_verify( $input, PKCS7_DETACHED | PKCS7_NOVERIFY, $dummy );

        if ( $result === true || $result === -1 )
            return PEAR::raiseError( "Message Verified Successfully but the signer's certificate could not be verified." );
        else if ( $result === false )
            return PEAR::raiseError( "Verification failed - this message may have been tampered with." );

        return PEAR::raiseError( "There was an unknown error verifying this message." );
    }

    /**
     * Sign a MIME_Part using S/MIME.
     *
     * @access public
     *
     * @param object MIME_Part $mime_part  The MIME_Part object to sign.
     * @param array $params                The parameters required for
     *                                     signing.
     *
     * @return object MIME_Part  A MIME_Part object that is signed.
     *                           Returns Error object on error.
     */
    function signMIMEPart( $mime_part, $params )
    {
        /* Sign the part as a message */
        $message = $this->encrypt( $mime_part->toString(), $params );

        /* Break the result into its components */
        $mime_message = MIME_Structure::parseTextMIMEMessage( $message );

        $smime_sign = $mime_message->getPart( 2 );
        $smime_sign->setDescription( "S/MIME Cryptographic Signature" );
        $smime_sign->transferDecodeContents();
        $smime_sign->setTransferEncoding( 'base64' );

        $smime_part = &new MIME_Part( 'multipart/signed' );
        $smime_part->setContents( 'This is a cryptographically signed message in MIME format.' . "\n" );
        $smime_part->addPart( $mime_part  );
        $smime_part->addPart( $smime_sign );
        $smime_part->setContentTypeParameter( 'protocol', 'application/x-pkcs7-signature' );
        $smime_part->setContentTypeParameter( 'micalg', 'sha1' );

        return $smime_part;
    }

    /**
     * Encrypt a MIME_Part using S/MIME.
     *
     * @access public
     *
     * @param object MIME_Part $mime_part  The MIME_Part object to encrypt.
     * @param array $params                The parameters required for
     *                                     encryption.
     *
     * @return object MIME_Part  A MIME_Part object that is encrypted.
     *                           Returns Error on error.
     */
    function encryptMIMEPart( $mime_part, $params = array() )
    {
        /* Sign the part as a message */
        $message = $this->encrypt( $mime_part->toString(), $params );

        /* Break the result into its components */
        $mime_message = MIME_Structure::parseTextMIMEMessage( $message );

        $smime_part = $mime_message->getBasePart();
        $smime_part->setDescription( 'S/MIME Encrypted Message' );
        $smime_part->transferDecodeContents();
        $smime_part->setTransferEncoding( 'base64' );

        return $smime_part;
    }

    /**
     * Convert a PEM format certificate to readable HTML version.
     *
     * @access public
     *
     * @param string $cert   PEM format certificate
     *
     * @return string  HTML detailing the certificate.
     */
    function certToHTML( $cert )
    {
        /* Commong Fields */
        $fieldnames['Email']                       = "Email Address";
        $fieldnames['CommonName']                  = "Common Name";
        $fieldnames['Organisation']                = "Organisation";
        $fieldnames['OrganisationalUnit']          = "Organisational Unit";
        $fieldnames['Country']                     = "Country";
        $fieldnames['StateOrProvince']             = "State or Province";
        $fieldnames['Location']                    = "Location";
        $fieldnames['StreetAddress']               = "Street Address";
        $fieldnames['TelephoneNumber']             = "Telephone Number";
        $fieldnames['Surname']                     = "Surname";
        $fieldnames['GivenName']                   = "Given Name";

        /* Netscape Extensions */
        $fieldnames['netscape-cert-type']          = 'Netscape certificate type';
        $fieldnames['netscape-base-url']           = 'Netscape Base URL';
        $fieldnames['netscape-revocation-url']     = 'Netscape Revocation URL';
        $fieldnames['netscape-ca-revocation-url']  = 'Netscape CA Revocation URL';
        $fieldnames['netscape-cert-renewal-url']   = 'Netscape Renewal URL';
        $fieldnames['netscape-ca-policy-url']      = 'Netscape CA policy URL';
        $fieldnames['netscape-ssl-server-name']    = 'Netscape SSL server name';
        $fieldnames['netscape-comment']            = 'Netscape certificate comment';

        /* X590v3 Extensions */
        $fieldnames['id-ce-extKeyUsage']           = 'X509v3 Extended Key Usage';
        $fieldnames['id-ce-basicConstraints']      = 'X509v3 Basic Constraints';
        $fieldnames['id-ce-subjectAltName']        = 'X509v3 Subject Alternative Name';
        $fieldnames['id-ce-subjectKeyIdentifier']  = 'X509v3 Subject Key Identifier';
        $fieldnames['id-ce-certificatePolicies']   = 'Certificate Policies';
        $fieldnames['id-ce-CRLDistributionPoints'] = 'CRL Distribution Points';
        $fieldnames['id-ce-keyUsage']              = 'Key Usage';

        $text = '<pre class="fixed">';
        $cert_details = $this->parseCert( $cert );

        if ( !is_array( $cert_details ) )
            return '<pre>' . 'Unable to extract certificate details' . '</pre>';
        
        $certificate = $cert_details['certificate'];

        /* Subject */
        if ( array_key_exists( 'subject', $certificate ) ) 
		{
            $text .= "<b>" . "Subject" . ":</b>\n";

            foreach ( $certificate['subject'] as $key => $value ) 
			{
                if ( array_key_exists( $key, $fieldnames ) )
                    $text .= sprintf( "&nbsp;&nbsp;%s: %s\n", $fieldnames[$key], $value );
                else
                    $text .= sprintf( "&nbsp;&nbsp;*%s: %s\n", $key, $value );
            }

            $text .= "\n";
        }

        /* Issuer */
        if ( array_key_exists( 'issuer', $certificate ) ) 
		{
            $text .= "<b>" . "Issuer" . ":</b>\n";

            foreach ( $certificate['issuer'] as $key => $value ) 
			{
                if ( array_key_exists( $key, $fieldnames ) )
                    $text .= sprintf( "&nbsp;&nbsp;%s: %s\n", $fieldnames[$key], $value );
                else
                    $text .= sprintf( "&nbsp;&nbsp;*%s: %s\n", $key, $value );
            }

            $text .= "\n";
        }

        /* Dates  */
        $text .= "<b>" . "Validity" . ":</b>\n";
        $text .= sprintf( "&nbsp;&nbsp;%s: %s\n", "Not Before", strftime( "%x %X", $certificate['validity']['notbefore'] ) );
        $text .= sprintf( "&nbsp;&nbsp;%s: %s\n", "Not After",  strftime( "%x %X", $certificate['validity']['notafter']  ) );
        $text .= "\n";

        /* Subject Public Key Info */
        $text .= "<b>" . "Subject Public Key Info" . ":</b>\n";
        $text .= sprintf( "&nbsp;&nbsp;%s: %s\n", "Public Key Algorithm", $certificate['subjectPublicKeyInfo']['algorithm'] );
		
        if ( $certificate['subjectPublicKeyInfo']['algorithm'] = 'rsaEncryption' ) 
		{
            if ( Util::extensionExists( 'bcmath' ) ) 
			{
                $modulus = $certificate['subjectPublicKeyInfo']['subjectPublicKey']['modulus'];
                $modulus_hex = '';
				
                while ( $modulus != '0' ) 
				{
                    $modulus_hex = dechex( bcmod( $modulus, '16' ) ) . $modulus_hex;
                    $modulus = bcdiv( $modulus, '16', 0 );
                }

                if ( strlen( $modulus_hex ) > 64 && strlen( $modulus_hex ) < 128 )
                    str_pad( $modulus_hex, 128, '0', STR_PAD_RIGHT );
                else if ( strlen( $modulus_hex ) > 128 && strlen( $modulus_hex ) < 256 )
                    str_pad( $modulus_hex, 256, '0', STR_PAD_RIGHT );

                $text .= "&nbsp;&nbsp;" . sprintf( "RSA Public Key (%d bit)", strlen( $modulus_hex ) * 4 ) . ":\n";
                $modulus_str = '';
				
                for ( $i = 0; $i < strlen( $modulus_hex ); $i += 2 ) 
				{
                    if ( ( $i % 32 ) == 0 )
                        $modulus_str .= "\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    
                    $modulus_str .= substr( $modulus_hex, $i, 2 ) . ':';
                }

                $text .= sprintf( "&nbsp;&nbsp;&nbsp;&nbsp;%s: %s\n", "Modulus", $modulus_str );
            }

            $text .= sprintf( "&nbsp;&nbsp;&nbsp;&nbsp;%s: %s\n", "Exponent", $certificate['subjectPublicKeyInfo']['subjectPublicKey']['publicExponent'] );
        }
		
        $text .= "\n";

        /* X509v3 extensions */
        if ( array_key_exists( 'extensions', $certificate ) ) 
		{
            $text .= "<b>" . "X509v3 extensions" . ":</b>\n";

            foreach ( $certificate['extensions'] as $key => $value ) 
			{
                if ( is_array( $value ) )
                    $value = "Unsupported Extension";
                
                if ( array_key_exists( $key, $fieldnames ) )
                    $text .= sprintf( "&nbsp;&nbsp;%s:\n&nbsp;&nbsp;&nbsp;&nbsp;%s\n", $fieldnames[$key], wordwrap( $value, 40, "\n&nbsp;&nbsp;&nbsp;&nbsp;" ) );
                else
                    $text .= sprintf( "&nbsp;&nbsp;%s:\n&nbsp;&nbsp;&nbsp;&nbsp;%s\n", $key, wordwrap( $value, 60, "\n&nbsp;&nbsp;&nbsp;&nbsp;" ) );
            }

            $text .= "\n";
        }

        /* Certificate Details */
        $text .= "<b>" . "Certificate Details" . ":</b>\n";
        $text .= sprintf( "&nbsp;&nbsp;%s: %d\n", "Version", $certificate['version'] );
        $text .= sprintf( "&nbsp;&nbsp;%s: %d\n", "Serial Number", $certificate['serialNumber'] );

        foreach ( $cert_details['fingerprints'] as $hash => $fingerprint ) 
		{
            $label = sprintf( "%s Fingerprint", strtoupper( $hash ) );
            $fingerprint_str = '';
            
			for ( $i = 0; $i < strlen( $fingerprint ); $i += 2 )
                $fingerprint_str .= substr( $fingerprint, $i, 2 ) . ':';
            
            $text .= sprintf( "&nbsp;&nbsp;%s:\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s\n", $label, $fingerprint_str );
        }
        $text .= sprintf( "&nbsp;&nbsp;%s: %s\n", "Signature Algorithm", $cert_details['signatureAlgorithm'] );
        $text .= sprintf( "&nbsp;&nbsp;%s:", "Signature" );

        $sig_str = '';
        for ( $i = 0; $i < strlen( $cert_details['signature'] ); $i++ ) 
		{
            if ( ( $i % 16 ) == 0 )
                $sig_str .= "\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            
            $sig_str .= sprintf( "%02x:", ord( $cert_details['signature'][$i] ) );
        }

        $text .= $sig_str;
        $text .= "\n";
        $text .= '</pre>';

        return $text;
    }

    /**
     * Extract the contents of a PEM format certificate to an array.
     *
     * @access public
     *
     * @param string $cert   PEM format certificate
     *
     * @return array  Array containing all extractable information about
     *                 the certificate.
     */
    function parseCert( $cert )
    {
        $cert_split = preg_split( '/(-----((BEGIN)|(END)) CERTIFICATE-----)/', $cert );
		
        if ( !isset( $cert_split[1] ) )
            $raw_cert = base64_decode( $cert );
        else
            $raw_cert = base64_decode( $cert_split[1] );

        $cert_data = Crypting_smime::_parseASN( $raw_cert );
		
        if ( !is_array( $cert_data ) || $cert_data[0] == 'UNKNOWN' )
            return false;

        $cert_details = array();
        $cert_details['fingerprints']['md5'] = md5( $raw_cert );
		
        if ( Util::extensionExists( 'mhash' ) )
            $cert_details['fingerprints']['sha1'] = bin2hex( mhash( MHASH_SHA1, $raw_cert ) );

        $cert_details['certificate']['extensions']   = array();
        $cert_details['certificate']['version']      = $cert_data[1][0][1][0][1] + 1;
        $cert_details['certificate']['serialNumber'] = $cert_data[1][0][1][1][1];
        $cert_details['certificate']['signature']    = $cert_data[1][0][1][2][1][0][1];
        $cert_details['certificate']['issuer']       = $cert_data[1][0][1][3][1];
        $cert_details['certificate']['validity']     = $cert_data[1][0][1][4][1];
        $cert_details['certificate']['subject']      = @$cert_data[1][0][1][5][1];
        $cert_details['certificate']['subjectPublicKeyInfo'] = $cert_data[1][0][1][6][1];

        $cert_details['signatureAlgorithm'] = $cert_data[1][1][1][0][1];
        $cert_details['signature'] = $cert_data[1][2][1];

        // issuer
        $issuer = array();
        foreach ( $cert_details['certificate']['issuer'] as $value )
            $issuer[$value[1][1][0][1]] = $value[1][1][1][1];
        
        $cert_details['certificate']['issuer'] = $issuer;

        // subject
        $subject = array();
        foreach ( $cert_details['certificate']['subject'] as $value )
            $subject[$value[1][1][0][1]] = $value[1][1][1][1];
        
        $cert_details['certificate']['subject'] = $subject;

        // validity
        $vals = $cert_details['certificate']['validity'];
        $cert_details['certificate']['validity'] = array();
        $cert_details['certificate']['validity']['notbefore'] = $vals[0][1];
        $cert_details['certificate']['validity']['notafter']  = $vals[1][1];
		
        foreach ( $cert_details['certificate']['validity'] as $key => $val ) 
		{
            $year   = substr( $val, 0, 2 );
            $month  = substr( $val, 2, 2 );
            $day    = substr( $val, 4, 2 );
            $hour   = substr( $val, 6, 2 );
            $minute = substr( $val, 8, 2 );
			
            if ( $val[11] == '-' || $val[9] == '+' ) 
			{
                // handle time zone offset here
                $seconds = 0;
            } 
			else if ( strtoupper( $val[11] ) == 'Z' ) 
			{
                $seconds = 0;
            } 
			else 
			{
                $seconds = substr( $val, 10, 2 );
				
                if ( $val[11] == '-' || $val[9] == '+' ) 
				{
                    // handle time zone offset here
                }
            }
			
            $cert_details['certificate']['validity'][$key] = mktime( $hour, $minute, $seconds, $month, $day, $year );
        }

        // Split the Public Key into components.
        $subjectPublicKeyInfo = array();
        $subjectPublicKeyInfo['algorithm'] = $cert_details['certificate']['subjectPublicKeyInfo'][0][1][0][1];
		
        if ( $certificate['subjectPublicKeyInfo']['algorithm'] = 'rsaEncryption' ) 
		{
            $subjectPublicKey = Crypting_smime::_parseASN( $cert_details['certificate']['subjectPublicKeyInfo'][1][1] );
            $subjectPublicKeyInfo['subjectPublicKey']['modulus'] = $subjectPublicKey[1][0][1];
            $subjectPublicKeyInfo['subjectPublicKey']['publicExponent'] = $subjectPublicKey[1][1][1];
        }
		
        $cert_details['certificate']['subjectPublicKeyInfo'] = $subjectPublicKeyInfo;

        if ( isset( $cert_data[1][0][1][7] ) && is_array( $cert_data[1][0][1][7][1] ) ) 
		{
            foreach( $cert_data[1][0][1][7][1] as $ext ) 
			{
                $oid = $ext[1][0][1];
                $cert_details['certificate']['extensions'][$oid] = $ext[1][1];
            }
        }

        $i = 9;
        while ( isset( $cert_data[1][0][1][$i] ) ) 
		{
            $oid = $cert_data[1][0][1][$i][1][0][1];
            $cert_details['certificate']['extensions'][$oid] = $cert_data[1][0][1][$i][1][1];
            
			$i++;
        }

        foreach ( $cert_details['certificate']['extensions'] as $oid => $val ) 
		{
            switch ( $oid ) 
			{
			    case 'netscape-base-url':

                case 'netscape-revocation-url':

                case 'netscape-ca-revocation-url':

                case 'netscape-cert-renewal-url':

                case 'netscape-ca-policy-url':

                case 'netscape-ssl-server-name':

                case 'netscape-comment':
                    $val = Crypting_smime::_parseASN( $val[1] );
                    $cert_details['certificate']['extensions'][$oid] = $val[1];

                    break;

                case 'id-ce-subjectAltName':
                    $val = Crypting_smime::_parseASN( $val[1] );
                    $cert_details['certificate']['extensions'][$oid] = '';

                    foreach ( $val[1] as $name ) 
					{
                        if ( !empty( $cert_details['certificate']['extensions'][$oid] ) )
                            $cert_details['certificate']['extensions'][$oid] .= ', ';
                        
                        $cert_details['certificate']['extensions'][$oid] .= $name[1];
                    }
					
                    break;

                case 'netscape-cert-type':
                    $val = Crypting_smime::_parseASN( $val[1] );
                    $val = ord($val[1]);
                    $newVal = '';

                    if ( $val & 0x80 )
                        $newVal .= empty( $newVal )? 'SSL client' : ', SSL client';
                    
                    if ( $val & 0x40 )
                        $newVal .= empty( $newVal )? 'SSL server' : ', SSL server';
                    
                    if ( $val & 0x20 )
                        $newVal .= empty( $newVal )? 'S/MIME' : ', S/MIME';
                    
                    if ( $val & 0x10 )
                        $newVal .= empty( $newVal )? 'Object Signing' : ', Object Signing';
                    
                    if ( $val & 0x04 )
                        $newVal .= empty( $newVal )? 'SSL CA' : ', SSL CA';
                    
                    if ( $val & 0x02 )
                        $newVal .= empty( $newVal )? 'S/MIME CA' : ', S/MIME CA';
                    
                    if ( $val & 0x01 )
                        $newVal .= empty( $newVal )? 'Object Signing CA' : ', Object Signing CA';

                    $cert_details['certificate']['extensions'][$oid] = $newVal;
                    break;

                case 'id-ce-extKeyUsage':
                    $val = Crypting_smime::_parseASN( $val[1] );
                    $val = $val[1];
                    $newVal = '';
					
                    if ( $val[0][1] != 'sequence' )
                        $val = array( $val );
                    else
                        $val = $val[1][1];
                    
                    foreach ( $val as $usage ) 
					{
                        if ( $usage[1] = 'id_kp_clientAuth' )
                            $newVal .= empty( $newVal )? 'TLS Web Client Authentication' : ', TLS Web Client Authentication';
                        else
                            $newVal .= empty( $newVal )? $usage[1] : ', ' . $usage[1];
                    }
					
                    $cert_details['certificate']['extensions'][$oid] = $newVal;
                    break;

                case 'id-ce-subjectKeyIdentifier':
                    $val = Crypting_smime::_parseASN( $val[1] );
                    $val = $val[1];
                    $newVal = '';

                    for ( $i = 0; $i < strlen( $val ); $i++ )
                        $newVal .= sprintf( "%02x:", ord( $val[$i] ) );
                    
                    $cert_details['certificate']['extensions'][$oid] = $newVal;
                    break;

                case 'id-ce-authorityKeyIdentifier':
                    $val = Crypting_smime::_parseASN( $val[1] );

                    if ( $val[0] == 'string' ) 
					{
                        $val = $val[1];
                        $newVal = '';
						
                        for ( $i = 0; $i < strlen( $val ); $i++ )
                            $newVal .= sprintf( "%02x:", ord( $val[$i] ) );
                        
                        $cert_details['certificate']['extensions'][$oid] = $newVal;
                    } 
					else 
					{
                        $cert_details['certificate']['extensions'][$oid] = "Unsupported Extension";
                    }
					
                    break;

                case 'id-ce-basicConstraints':

                case 'default':
                    $cert_details['certificate']['extensions'][$oid] = "Unsupported Extension";
                    break;
            }
        }

        return $cert_details;
    }

	
	// private methods
	
    /**
     * Attempt to parse ASN.1 formated data.
     *
     * @access public
     *
     * @param string $data   ASN.1 formated data
     *
     * @return array  Array contained the extracted values..
     */
    function _parseASN( $data )
    {
        $result = array();

        while ( strlen( $data ) > 1 ) 
		{
            $class = ord( $data[0] );
			
            switch ( $class ) 
			{
                // Sequence
                case 0x30:
                    $len   = ord( $data[1] );
                    $bytes = 0;
                    
					if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $sequence_data = substr( $data, 2 + $bytes, $len );
                    $data   = substr( $data, 2 + $bytes + $len );
                    $values = $this->_parseASN( $sequence_data );
					
                    if ( !is_array( $values ) || is_string( $values[0] ) )
                        $values = array($values);
                    
                    $sequence_values = array();
                    $i = 0;
                    
					foreach ( $values as $val ) 
					{
                        if ( $val[0] == 'extension' )
                            $sequence_values['extensions'][] = $val;
                        else
                            $sequence_values[$i++] = $val;
                    }
					
                    $result[] = array( 'sequence', $sequence_values );
                    break;

                // Set of
                case 0x31:
                    $len   = ord( $data[1] );
                    $bytes = 0;
					
                    if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $sequence_data = substr( $data, 2 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );
                    $result[] = array( 'set', $this->_parseASN( $sequence_data ) );

                    break;

                // Boolean type
                case 0x01:
                    $boolean_value = ( ord( $data[2] ) == 0xff );
                    $data = substr( $data, 3 );
                    $result[] = array( 'boolean', $boolean_value );

                    break;

                // Integer type
                case 0x02:
                    $len   = ord( $data[1] );
                    $bytes = 0;
                    
					if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }

                    $integer_data = substr( $data, 2 + $bytes, $len );
                    $data  = substr( $data, 2 + $bytes + $len );
                    $value = 0;
					
                    if ( $len <= 4 ) 
					{
                        /* Method works fine for small integers */
                        for ( $i = 0; $i < strlen( $integer_data ); $i++ )
                            $value = ( $value << 8 ) | ord( $integer_data[$i] );
                    } 
					else 
					{
                        /* Method works for arbitrary length integers */
                        if ( Util::extensionExists( 'bcmath' ) ) 
						{
                            for ( $i = 0; $i < strlen( $integer_data ); $i++ )
                                $value = bcadd( bcmul( $value, 256 ), ord( $integer_data[$i] ) );
                        } 
						else 
						{
                            $value = -1;
                        }
                    }
					
                    $result[] = array( 'integer(' . $len . ')', $value );
                    break;

                // Bitstring type
                case 0x03:
                    $len   = ord( $data[1] );
                    $bytes = 0;
					
                    if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $bitstring_data = substr( $data, 3 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );
                    $result[] = array( 'bit string', $bitstring_data );

                    break;

                // Octetstring type
                case 0x04:
                    $len   = ord($data[1]);
                    $bytes = 0;
                    
					if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $octectstring_data = substr( $data, 2 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );
                    $result[] = array( 'octet string', $octectstring_data );

                    break;

                // Null type
                case 0x05:
                    $data = substr( $data, 2 );
                    $result[] = array( 'null', null );

                    break;

                // Object identifier type
                case 0x06:
                    $len   = ord( $data[1] );
                    $bytes = 0;
					
                    if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $oid_data = substr( $data, 2 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );

                    // Unpack the OID
                    $plain  = floor( ord( $oid_data[0]) / 40 );
                    $plain .= '.' .  ord( $oid_data[0]) % 40;

                    $value = 0;
                    
					$i = 1;
                    while ( $i < strlen( $oid_data ) ) 
					{
                        $value = $value << 7;
                        $value = $value | ( ord( $oid_data[$i] ) & 0x7f );

                        if ( !( ord( $oid_data[$i] ) & 0x80 ) ) 
						{
                            $plain .= '.' . $value;
                            $value  = 0;
                        }
						
                        $i++;
                    }

                    if ( array_key_exists( $plain, $this->_oids ) )
                        $result[] = array( 'oid', $this->_oids[$plain] );
                    else
                        $result[] = array( 'oid', $plain );

                    break;

                // Character string type
                case 0x12:

                case 0x13:

                case 0x14:

                case 0x15:

                case 0x16:

                case 0x81:

                case 0x80:
                    $len   = ord( $data[1] );
                    $bytes = 0;
                    
					if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $string_data = substr( $data, 2 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );
                    $result[] = array( 'string', $string_data );

                    break;

                // Time types
                case 0x17:
                    $len   = ord( $data[1] );
                    $bytes = 0;
					
                    if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $time_data = substr( $data, 2 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );
                    $result[] = array( 'utctime', $time_data );

                    break;

                // X509v3 extensions?
                case 0x82:
                    $len   = ord( $data[1] );
                    $bytes = 0;
					
                    if ( $len & 0x80 ) 
					{
                        $bytes = $len & 0x0f;
                        $len   = 0;
                        
						for ( $i = 0; $i < $bytes; $i++ )
                            $len = ( $len << 8 ) | ord( $data[$i + 2] );
                    }
					
                    $sequence_data = substr( $data, 2 + $bytes, $len );
                    $data = substr( $data, 2 + $bytes + $len );

                    $result[] = array( 'extension', 'X509v3 extensions' );
                    $result[] = $this->_parseASN( $sequence_data );

                    break;

                // Extensions
                case 0xa0:

                case 0xa3:
                    $extension_data = substr( $data, 0, 2 );
                    $data = substr( $data, 2 );
                    $result[] = array( 'extension', dechex( $extension_data ) );

                    break;

                case 0xe6:
                    $extension_data = substr( $data, 0, 1 );
                    $data = substr( $data, 1 );
                    $result[] = array( 'extension', dechex( $extension_data ) );

                    break;

                case 0xa1:
                    $extension_data = substr( $data, 0, 1 );
                    $data = substr( $data, 6 );
                    $result[] = array( 'extension', dechex( $extension_data ) );

                    break;

                // Unknown
                default:
                    $result[] = array( 'UNKNOWN', dechex( $data ) );
                    $data = '';
					
                    break;
            }
        }
		
        return ( count( $result ) > 1 )? $result : array_pop( $result );
    }

    /**
     * Decrypt an SMIME signed message using a public key.
     *
     * @access private
     *
     * @param string $text   The text to be verified.
     * @param array $params  The parameters needed for verification.
     * <pre>
     * Parameters:
     * ===========
     * 'type'       =>  'signature' or 'detached-signature' (REQUIRED)
     * 'pubkey'     =>  public key. (REQUIRED)
     * 'signature'  =>  signature block. (REQUIRED for detached signature)
     * </pre>
     *
     * @return string  The verification message from gpg.
     *                 If no signature, returns empty string.
     *                 Returns Error object on error.
     */
    function _decryptSignature( $text, $params )
    {
        return PEAR::raiseError( "_decryptSignature() not yet implemented." );
    }
	
    /**
     * Encrypt a message in SMIME format using a public key.
     *
     * @access private
     *
     * @param string $text   The text to be encrypted.
     * @param array $params  The parameters needed for encryption.
     * <pre>
     * Parameters:
     * ===========
     * 'type'    =>  'message' (REQUIRED)
     * 'pubkey'  =>  public key. (REQUIRED)
     * 'email'   =>  E-mail address of recipient. If not present, or not found
     *               in the public key, the first e-mail address found in the
     *               key will be used instead. (Optional)
     * </pre>
     *
     * @return string  The encrypted message.
     *                 Return Error object on error.
     */
    function _encryptMessage( $text, $params )
    {
        $email = null;

        /* Check for required parameters. */
        if ( !array_key_exists( 'pubkey', $params ) )
            return PEAR::raiseError( "A public SMIME key is required to encrypt a message." );

        /* Create temp files for input/output. */
        $input  = $this->_createTempFile( 'ap-smime' );
        $output = $this->_createTempFile( 'ap-smime' );

        /* Store message in file. */
        $fp1 = fopen( $input, 'w+' );
        fputs( $fp1, $text );
        fclose( $fp1 );

        if ( array_key_exists( 'email', $params ) )
            $email = $params['email'];

        /* If we have no email address at this point, use the first email
           address found in the public key. */
        if ( empty( $email ) ) 
		{
            $key_info = openssl_x509_parse( $params['pubkey'] );
			
            if ( is_array( $key_info ) && array_key_exists( 'subject', $key_info ) ) 
			{
                if ( array_key_exists( 'Email', $key_info['subject'] ) )
                    $email = $key_info['subject']['Email'];
                else if ( array_key_exists( 'emailAddress', $key_info['subject'] ) )
                    $email = $key_info['subject']['emailAddress'];
            } 
			else 
			{
                return PEAR::raiseError( "Could not determine the recipient's e-mail address." );
            }
        }

        /* Encrypt the document. */
        $res = openssl_pkcs7_encrypt( $input, $output, $params['pubkey'], array( 'To' => $email ) );
        $result = file( $output );

        if ( empty( $result ) )
            return PEAR::raiseError( "Could not S/MIME encrypt message." );

        return implode('', $result);
    }

    /**
     * Sign a message in SMIME format using a private key.
     *
     * @access private
     *
     * @param string $text   The text to be signed.
     * @param array $params  The parameters needed for signing.
     * <pre>
     * Parameters:
     * ===========
     * 'certs'       =>  Additional signing certs (Optional)
     * 'passphrase'  =>  Passphrase for key (REQUIRED)
     * 'privkey'     =>  Private key (REQUIRED)
     * 'pubkey'      =>  Public key (REQUIRED)
     * 'sigtype'     =>  Determine the signature type to use. (Optional)
     *                   'cleartext'  --  Make a clear text signature
     *                   'detach'     --  Make a detached signature (DEFAULT)
     * 'type'        =>  'signature' (REQUIRED)
     * </pre>
     *
     * @return string  The signed message.
     *                 Return Error object on error.
     */
    function _encryptSignature( $text, $params )
    {
        /* Check for secure connection. */
        $secure_check = $this->requireSecureConnection();
        
		if ( PEAR::isError( $secure_check ) )
            return $secure_check;

        /* Check for required parameters. */
        if ( !array_key_exists( 'pubkey',     $params ) ||
             !array_key_exists( 'privkey',    $params ) ||
             !array_key_exists( 'passphrase', $params ) ) 
		{
            return PEAR::raiseError( "A public S/MIME key, private S/MIME key, and passphrase are required to sign a message." );
        }

        /* Create temp files for input/output/certificates. */
        $input  = $this->_createTempFile( 'ap-smime' );
        $output = $this->_createTempFile( 'ap-smime' );
        $certs  = $this->_createTempFile( 'ap-smime' );

        /* Store message in temporary file. */
        $fp = fopen( $input, 'w+' );
        fputs( $fp, $text );
        fclose( $fp );

        /* Store additional cert in temporary file. */
        $fp = fopen( $certs, 'w+' );
        fputs( $fp, $params['certs'] );
        fclose( $fp );

        /* Determine the signature type to use. */
        $flags = PKCS7_DETACHED;
		
        if ( array_key_exists( 'sigtype', $params ) && $params['sigtype'] == 'cleartext' )
            $flags = PKCS7_TEXT;

        if ( empty( $params['certs'] ) )
            openssl_pkcs7_sign( $input, $output, $params['pubkey'], array( $params['privkey'], $params['passphrase'] ), array(), $flags );
        else
            openssl_pkcs7_sign( $input, $output, $params['pubkey'], array( $params['privkey'], $params['passphrase'] ), array(), $flags, $certs );

        if ( !( $result = file( $output ) ) )
            return PEAR::raiseError( "Could not S/MIME sign message." );

        return implode( '', $result );
    }

    /**
     * Decrypt an SMIME encrypted message using a private/public keypair
     * and a passhprase.
     *
     * @access private
     *
     * @param string $text   The text to be decrypted.
     * @param array $params  The parameters needed for decryption.
     * <pre>
     * Parameters:
     * ===========
     * 'type'        =>  'message' (REQUIRED)
     * 'pubkey'      =>  public key. (REQUIRED)
     * 'privkey'     =>  private key. (REQUIRED)
     * 'passphrase'  =>  Passphrase for Key. (REQUIRED)
     * </pre>
     *
     * @return string  The decrypted message.
     *                 Returns Error object on error.
     */
    function _decryptMessage( $text, $params )
    {
        /* Check for secure connection. */
        $secure_check = $this->requireSecureConnection();
        
		if ( PEAR::isError( $secure_check ) )
            return $secure_check;

        /* Check for required parameters. */
        if ( !array_key_exists( 'pubkey',     $params ) ||
             !array_key_exists( 'privkey',    $params ) ||
             !array_key_exists( 'passphrase', $params ) ) 
		{
            return PEAR::raiseError( "A public S/MIME key, private S/MIME key, and passphrase are required to decrypt a message." );
        }

        /* Create temp files for input/output. */
        $input  = $this->_createTempFile( 'ap-smime' );
        $output = $this->_createTempFile( 'ap-smime' );

        /* Store message in file. */
        $fp1 = fopen( $input, 'w+' );
        fputs( $fp1, trim( $text ) );
        fclose( $fp1 );

        openssl_pkcs7_decrypt( $input, $output, $params['pubkey'], array( $params['privkey'], $params['passphrase'] ) );
        $result = file( $output );

        if ( empty( $result ) )
            return PEAR::raiseError( "Could not decrypt S/MIME data." );

        return implode( '', $result );
    }
} // END OF Crypting_smime

?>
