<?php

require( '../../../../../prepend.php' );

using( 'security.crypt.GNUPGP' );


$gpg = new GNUPGP();

if ( PEAR::isError( $gpg ) )
	die( $gpg->getMessage() );


$gpg->userName       = $userName;
$gpg->userEmail      = $userEmail;
$gpg->recipientName  = $recipientName;
$gpg->recipientEmail = $recipientEmail;
$gpg->message        = $message;


function tablist( $key_Array )
{
	echo "<table border=1>";
	echo "<tr><th>Type</th><th>Trust</th><th>Length</th><th>Algor.</th>";
	echo "<th>KeyID</th><th>Creation</th><th>Expiration</th><th>Local ID</th>";
	echo "<th>Ownertrust</th><th>User ID</th><th>???</th><th>???</th></tr>";
	
	for ( $i = 2; $i < count( $key_Array ); $i++ )
	{
		$tmp = explode( ":", $key_Array[$i]); 
		echo "<tr>";
		echo "<td>" . $tmp[0]  . "</td>";						// type
		echo "<td>" . $tmp[1]  . "</td>";						// trust
		echo "<td>" . $tmp[2]  . "</td>";						// length
		echo "<td>" . $tmp[3]  . "</td>";						// algorithm
		echo "<td>" . $tmp[4]  . "</td>";						// KeyID
		echo "<td>" . $tmp[5]  . "</td>";						// Creation date
		echo "<td>" . $tmp[6]  . "</td>";						// Expiration date
		echo "<td>" . $tmp[7]  . "</td>";						// Local ID
		echo "<td>" . $tmp[8]  . "</td>";						// Ownertrust
		echo "<td>" . htmlspecialchars( $tmp[9] ) . "</td>";	// User ID
		echo "<td>" . $tmp[10] . "</td>";						// ???
		echo "<td>" . $tmp[11] . "</td>";						// ???
		echo "</tr>";
		
		if ( $tmp[0] == "sub" )
			echo "<tr><td colspan=\"12\">&nbsp;</td></tr>";
	}
	
	echo "</table>";
	echo "<br><br>";
	echo "<pre>1. Field:  Type of record<br>
		<ul>
	    		<li>pub = public key
	    		<li>sub = subkey (secondary key)
	    		<li>sec = secret key
	    		<li>ssb = secret subkey (secondary key)
	    		<li>uid = user id (only field 10 is used)
	    		<li>fpr = fingerprint: (fingerprint is in field 10)
	    		<li>pkd = public key data (special field format, see below)
		</ul>
		</pre><br>";
	echo "<pre>2. Field:  A letter describing the calculated trust. This is a single
	    letter, but be prepared that additional information may follow
	    in some future versions. (not used for secret keys)<br>
	    	<ul>
			<li>o = Unknown (this key is new to the system)
			<li>d = The key has been disabled
			<li>r = The key has been revoked
			<li>e = The key has expired
			<li>q = Undefined (no value assigned)
			<li>n = Don't trust this key at all
			<li>m = There is marginal trust in this key
			<li>f = The key is full trusted.
			<li>u = The key is ultimately trusted; this is only used for keys for which the secret key is also available.
		</ul>
		   </pre><br>";
	echo "<pre>3. Field:  length of key in bits.</pre><br><br>";
	echo "<pre>4. Field:  Algorithm:<br>
		<ul>
			<li>1 = RSA
			<li>16 = ElGamal (encrypt only)
		       	<li>17 = DSA (sometimes called DH, sign only)
		       	<li>20 = ElGamal (sign and encrypt)
		</ul>
		</pre><br>";
	echo "<pre>5. Field:  KeyID.</pre><br><br>";
	echo "<pre>6. Field:  Creation Date (in UTC).</pre><br><br>";
	echo "<pre>7. Field:  Key expiration date or empty if none.</pre><br><br>";
	echo "<pre>8. Field:  Local ID: record number of the dir record in the trustdb.
	    This value is only valid as long as the trustdb is not
	    deleted. You can use \"#<local-id> as the user id when
	    specifying a key. This is needed because keyids may not be
	    unique - a program may use this number to access keys later.</pre><br><br>";
	echo "<pre> 9. Field:  Ownertrust (primary public keys only)
	    This is a single letter, but be prepared that additional
	    information may follow in some future versions.</pre><br><br>";
	echo "<pre>10. Field:  User-ID.  The value is quoted like a C string to avoid
	    control characters (the colon is quoted \"\x3a\").</pre><br><br>";
	echo "<pre>11. Field: ????.</pre><br><br>";
	echo "<pre>12. Field: ????.</pre><br><br>";
}


switch ( $action )
{
	case "gen_key":
		$result = $gpg->genKey( $userName, $comment, $userEmail, $passphrase );
		
		if ( PEAR::isError( $result ) )
		{
			echo $result->getMessage();
			exit();
		} 
		else 
		{
			echo "<h3>The key was generated sucessful.</h3>";
		}
		break;
	
	case "list_key":
		$result = $gpg->listKeys();
		
		if ( PEAR::isError( $result ) )
		{
			echo $result->getMessage();
			exit();
		} 
		else 
		{
			echo "<h3>This is the keys on the <pre color=red>" . $gpg->userName . "</pre>'s keyring</h3><br>";
			tablist( $gpg->keyArray );
		}
		
		break;
	
	case "export_key":
		$result = $gpg->exportKey();
		
		if ( PEAR::isError( $result ) )
		{
			echo $result->getMessage();
			exit();
		}  
		else 
		{
			echo "<h3>This is the <pre color=red>".$gpg->userEmail."</pre>'s Public Key</h3><br>";
			echo "<form><textarea rows=\"30\" cols=\"80\">" . $gpg->public_key . "</textarea>";
		}
		
		break;
	
	case "import_key":
		$result = $gpg->importKey( $key );
		
		if ( PEAR::isError( $result ) )
		{
			echo $result->getMessage();
			exit();
		} 
		else 
		{
			echo "<h3>The keys was imported successful.</h3><br>";
			$result = $gpg->listKeys();
			
			if ( PEAR::isError( $result ) )
			{
				echo $result->getMessage();
				exit();
			} 
			else 
			{
				echo "<h3>This is the keys on the <pre color=red>".$gpg->userEmail."</pre>'s keyring</h3><br>";
				tablist( $gpg->keyArray );
			}
		}
		
		break;
	
	case "remove_key":
		if ( !empty( $keyID ) )
			$key = $keyID;
		else if ( !empty( $emailID ) )
			$key = $emailID;
		else 
			$key = $nameID;

		$result = $gpg->removeKey( $key );
		
		if ( PEAR::isError( $result ) )
		{
			echo $result->getMessage();
			exit();
		}  
		else 
		{
			echo "<h3>The key was successful removed.</h3><br>";
			$result = $gpg->listKeys();
			
			if ( PEAR::isError( $result ) )
			{
				echo $result->getMessage();
				exit();
			} 
			else 
			{
				echo "<h3>This is the keys on the <pre color=red>".$gpg->userEmail."</pre>'s keyring</h3><br>";
				tablist($gpg->keyArray);
			}
		}
		
		break;
	
	case "encrypt_msg":
		if ( empty( $userEmail ) )
		{
			echo "The \"From User:\" can't be empty!";
			exit();
		}
		
		if ( empty( $recipientEmail ) )
		{
			echo "The \"To Email:\" can't be empty!";
			exit();
		}
		
		$result = $gpg->encryptMessage();
		
		if ( PEAR::isError( $result ) ) 
		{
			echo $result->getMessage();
			exit();
		} 
		else 
		{
			echo "<h3>The message was successful encrypted!</h3><br>";
			echo "<form><textarea rows=\"20\" cols=\"80\">" . $gpg->encrypted_message . "</textarea></form>";
		}
		break;
		
	case "decrypt_msg":
		if ( empty( $userEmail ) )
		{
			echo "The \"Name\" can't be empty!";
			exit();
		}
		
		if ( empty( $passphrase ) )
		{
			echo "The \"Passphrase\" can't be empty!";
			exit();
		}
		
		if ( empty( $message ) )
		{
			echo "The \"Message\" can't be empty!";
			exit();
		}
		
		$result = $gpg->decryptMessage( $message, $passphrase );
		
		if ( PEAR::isError( $result ) ) 
		{
			echo $result->getMessage();
			exit();
		} 
		else 
		{
			echo "<h3>The message was successful decrypted!</h3><br>";
			echo "<form><textarea rows=\"20\" cols=\"80\">" . $gpg->decrypted_message . "</textarea></form>";
		}
		
		break;
}

?>
