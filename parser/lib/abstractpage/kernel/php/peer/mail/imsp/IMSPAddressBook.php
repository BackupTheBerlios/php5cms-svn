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


using( 'peer.mail.imsp.IMSPStream' );
using( 'util.Debug' );


// These define regExp that should match the respective server response strings.
define( "IMSP_ADDRESSBOOK_RESPONSE",   "^\* ADDRESSBOOK"          );
define( "IMSP_SEARCHADDRESS_RESPONSE", "^\* SEARCHADDRESS"        );
define( "IMSP_FETCHADDRESS_RESPONSE",  "^\* FETCHADDRESS"         );
define( "IMSP_ACL_RESPONSE",           "^\* ACL ADDRESSBOOK"      );
define( "IMSP_MYRIGHTS_RESPONSE",      "^\* MYRIGHTS ADDRESSBOOK" );

// string of supported ACL rights
define( "IMSP_ACL_RIGHTS", "lrwcda" );


/**
 * This file provides the means to work with IMSP ADDRESSBOOKS
 *
 * Example of use:
 *
 *	$myABook = new IMSPAddressBook([imsp_server_name],[port]);      // Create an instance (if no server/port is passed, then it 
 *																	// defaults to localhost 406
 *  $myABook->logon( "username", "password" );						// Note that password is plaintext.
 *  $entry = $myABook->getAddressBookEntry( "abookName", "entryName" ); 
 *	.
 *  .
 *  .
 *  $myABook->logout();
 *
 * @package peer_mail_imsp
 */
 
class IMSPAddressBook extends IMSPStream
{
	/**
	 * Contructor
	 *
	 * @access public
	 */
	function IMSPAddressBook( $local_imsp_server = "", $local_port = "" )
 	{
 		$login = $this->IMSPStream( $local_imsp_server, $local_port );

		$this->debug = new Debug();
		$this->debug->Off();
		
		if ( !$login )
		{
			$this = new PEAR_Error( "Login error." );
			return;
		}
	} 	
 	

	/**
	 * Returns an array containing the names of all the addressbooks
	 * available to the logged in user.
	 * TODO: Perform checking of returned tag response after the ADDRESSBOOK list is finished 
	 * being sent to ensure everything finished 'OK'
	 *
	 * @access public
	 */
	function getAddressBookList()
	{
		$command_string = "ADDRESSBOOK *"; // Build the command.
		
		if ( !$this->imspSend( $command_string ) )
			return $this->imspError( $command_string );
			
		// iterate through the response and populate an array of addressbook names
		$server_response = $this->imspRecieve();
			
		while ( ereg( IMSP_ADDRESSBOOK_RESPONSE, $server_response ) )
		{
			// If this is a ADDRESSBOOK response, then this will split the resonse into the
			// [0] and [1] can be discarded
			// [2] = attributes
			// [3] = delimiter
			// [4] = addressbook name
			$entry = split( " ", $server_response );
			$abooks[] = $entry[4]; // Store it in the array to return later.			
			$server_response = $this->imspRecieve();	
		}
			
		if ( $server_response != "OK" )
		{
			$this->exitCode = "Did not recieve the expected response from the server.";
			return $this->imspError( "Error in getAddressBookList() call" );
		}	
			
		$this->debug->Message( "ADDRESSBOOK command OK." );
		return $abooks;		
	}

	/**
	 * Returns an array containing the names that
	 * match $search critera in the addressbook named $abook,
	 * TODO: Check for the server's response tag to make sure it matches and finished 'OK'
	 *
	 * @access public
	 */
	function searchAddressBook( $abook, $search )
	{	
		$command_text = "SEARCHADDRESS $abook name \"$search\"";
		
		if ( !$this->imspSend( $command_text ) )
			return $this->imspError( $command_text );
			
		$list_complete = false;
		
		while ( !$list_complete )
		{
			$server_response = $this->imspRecieve();
			// $server_response=fgets( $this->_stream, IMSP_DEFAULT_RESPONSE_LENGTH );
						
			if ( ereg( IMSP_SEARCHADDRESS_RESPONSE, $server_response ) )
			{
				$chopped_response = ereg_replace( IMSP_SEARCHADDRESS_RESPONSE, "", $server_response );	// Remove any lingering white space in front or behind.
				$chopped_response = ereg_replace( "\"", "", $chopped_response );						// Get rid of any lingering quotes.
				$abookNames[] = trim( $chopped_response );
			}
			else
			{
				$list_complete = true;
				break;
			}
		}
		
		// Should check for OK or BAD here just to be certain...
		
		$this->debug->Message( "SEARCHADDRESS command OK" );	
		return $abookNames;
	}

	/**
	 * Returns an associative array containing key - values pairs that corespond
	 * to the addressbook fields and values.  Note that there will always be a "name" entry and also note
	 * that the resulting values may need to be escaped for display in a web browser becuase they may 
	 * contain email addresses in the form :: myName <myname@somewhere.com> 
	 *
	 * @access public
	 */
	function getAddressBookEntry( $abook, $name )
	{
		$command_text = "FETCHADDRESS $abook \"$name\"";
	
		if ( !$this->imspSend( $command_text, true, true ) )
			return $this->imspError( $command_text );
	
		$server_response = $this->imspRecieve();
		$entry = $this->_parseFetchAddressResponse( $server_response );
	
		// Get the next server response -- this should be the OK response.
		// $server_response = fgets( $this->_stream, IMSP_DEFAULT_RESPONSE_LENGTH );
		$server_response = $this->imspRecieve();
	
		if ( !$server_response == "OK" )
		{
			// unexpected response
			$this->exitCode = "Did not recieve the expected response from the server.";
			return $this->imspError();
		}
	
		$this->debug->Message("FETCHADDRESS completed OK");
		return $entry;
	}

	/**
	 * Creates a new addressbook. Takes the name of the new book. Note that this should be the 
	 * FULLY QUALIFIED hierarchy such as "jdoe.public" or "jdoe.clients" etc...
	 *
	 * @access public
	 */
	function createAddressBook( $abookName )
	{
		$command_text = "CREATEADDRESSBOOK $abookName";	

		if ( !$this->imspSend( $command_text ) )
			return $this->imspError( $command_text );
	
		$server_response = $this->imspRecieve();
	
		if ( !$server_response )
		{
			// response did not come
			return $this->imspError( $command_text );
		}
	
		switch ( $server_response )
		{
			case "OK":
				$this->exitCode = "Ok.";
				$this->debug->Message( "CREATEADDRESSBOOK completed OK" );

				return true;
		
			case "NO":
				// could not create abook
				$this->exitCode = "Bad mailbox name.";
				return $this->imspError();
		
			case "BAD":
				return $this->imspError();
		
			default:
				// something unexpected!
				$this->exitCode = "Did not recieve the expected response from the server.";
				return $this->imspError( $server_response );
		}
	}

	/**
	 * Deletes an addressbook completely! Returns true or false.
	 *
	 * @access public
	 */
	function deleteAddressBook( $abookName )
	{
		$command_text = "DELETEADDRESSBOOK $abookName" ;
	
		if ( !$this->imspSend( $command_text ) )
		{
			// something failed
			return $this->imspError( $command_text );
		}
	
		$server_response = $this->imspRecieve();
	
		if ( !$server_response )
		{
			// response did not come
			return $this->imspError( $command_text );
		}
	
		switch ( $server_response )
		{
			case "OK":
				$this->exitCode = "Ok.";
				$this->debug->Message( "DELETEADDRESSBOOK completed OK" );
			
				return true;
		
			case "NO":
				// could not DELETE abook
				$this->exitCode = "Bad mailbox name.";
				return $this->imspError( "Addressbook name: $abookName" );
		
			case "BAD":
				return $this->imspError();
		
			default:
				//something unexpected!
				$this->exitCode = "Did not recieve the expected response from the server.";
				return$this->imspError( $server_response );
		}
	}
	
	/**
	 * Renames an addressbook. Takes the Oldname and the NewName 
	 *
	 * @return boolean
	 * @access public
	 */
	function renameAddressBook( $abookOldName, $abookNewName )
	{
		// make sure the new name is OK
		if ( ereg( " ", $abookNewName ) )
		{
			// spaces in names of abooks not valid?
			$this->exitCode = "The server did not understand your request.";
			return false;
		}
	
		$command_text = "RENAMEADDRESSBOOK $abookOldName $abookNewName";
	
		if ( !$this->imspSend( $command_text, true, true ) )
		{
			// something wrong with sending command
			return $this->imspError( $command_text );
		}
	
		$server_response = $this->imspRecieve();
	
		switch ( $server_response )
		{
			case "NO":
				// sorry, can't do it...maybe the addressbook doesnot exist or the new name is taken already
				return $this->imspError( "Perhaps the addressbook $abookOldName doesn't exist or $abookNewName is already taken." );
			
			case "BAD":
				// syntax prob
				return $this->imspError( $command_text );
			
			case "OK":
				$this->debug->Message( "Addressbook $abookOldName successfully changed to $abookNewName" );
				return true;
		
			default:
				// something unexpected
				$this->exitCode = "Did not recieve the expected response from the server.";
				return $this->imspError();
		}		
	}

	/**
	 * $entryInfo should be an associative array containing the entry field names.
	 * There MUST be a KEY named "name" that contains the name of the entry in the abook.
	 *
	 * @access public
	 */
	function addAddress( $abook, $entryInfo ) 
	{
		// $entryInfo must be an array
		if ( getType( $entryInfo ) != "array" )
		{
			$this->exitCode = "Bad argument.";
			return $this->imspError( "In method ->addAddress() \$entryInfo must be an array." );
		}
	
		if ( !$this->lockABook( $abook, $entryInfo["name"] ) )
			return false;
	
		// start building the command string
		$entryName = "\"" . $entryInfo["name"] . "\"";
	
		$command_text = "STOREADDRESS $abook $entryName ";
	
		// start sending the stream (include a new tag, but don't end with CRLF)
		$this->imspSend( $command_text, true, false );
	
		$command_text ="";
	
		while ( list( $key, $value ) = each( $entryInfo ) )
		{
			// do not sent the key name "name"
			if ( $key != "name" )
			{
				// protect from extraneous white space
				$value = trim( $value );
			
				// For some reason, tabs seem to break this so we should replace them with spaces?
				$value = ereg_replace( "\t", "\n\r", $value );
			
				// check for CR to see if we need {}
				if ( ereg( "[\n\r]", $value ) )
				{
					$literalString  = $value;
					$command_text  .= $key . " {" . strlen( $literalString ) . "}";
					$this->imspSend( $command_text, false, true );
					$server_response = $this->imspRecieve();
					$command_text = "";
				
					if ( !ereg( IMSP_COMMAND_CONTINUATION_RESPONSE, $server_response ) )
					{
						// not expected
						$this->exitCode = "Did not recieve the expected response from the server.";
						return $this->imspError( $server_response );
					}
				
					// send the string of octets and be sure to end with CRLF
					$this->imspSend( $literalString, false, false );
				}
				else
				{
					// If we are here, then we don't need to send a string literal (yet).
				 
					if ( ereg( " ", $value ) )
						$value = "\"" . $value . "\"";
		
				 	$command_text .= $key . " " . $value . " ";								
				}
			}	
		}
	
		// send anything that is left of the command
	
		if ( !$this->imspSend( $command_text, false, true ) )
		{
			// trouble sending command.
			return $this->imspError();
		}
	
		// check on success
		$server_response = $this->imspRecieve();
	
		// decide on the response...
		switch ( $server_response )
		{
			case "NO":
				// sorry...can't do it.
				return $this->imspError( "Can not add the requested address." );
			
			case "BAD":
				// sorry...didn't understand you
				return $this->imspError( $command_text );
		}
	
		if ( $server_response != "OK" )
		{
			// Cyrus-IMSP server sends a FETCHADDRESS Response here. Do others? This was not in the RFC.
			$dummy_array = $this->_parseFetchAddressResponse( $server_response );	// Should we keep this info?
			$server_response = $this->imspRecieve();								// Is there more?
														
			// Check it again.
			switch ( $server_response )
			{
				case "NO":
					// Sorry..can't do it
					return $this->imspError( "Can not add the requested address." );
				
				case "BAD":
					// Don't know what your talking about
					return $this->imspError( $command_text );
				
				case "OK":
					$this->debug->Message( "STOREADDRESS Completed successfully." );
				
					// we were successful...so release the lock on the entry
					if ( !$this->unlockABook( $abook, $entryInfo["name"] ) )
					{
						// could not release lock
						$this->exitCode = "That addressbook entry is locked or cannot be unlocked.";
						return $this->imspError();
					}
				
					return true;
			}
		}
	}

	/**
	 * Deletes an abook entry. Takes the name of the abook and the name of the entry ($bookEntry).
	 *
	 * @return boolean
	 * @access public
	 */
	function deleteAddress( $abook, $bookEntry )
	{
		$bookEntry    = $this->quoteSpacedString( $bookEntry );	// get it quoted if it contains spaces
		$command_text = "DELETEADDRESS $abook $bookEntry";		// build the command
	
		if ( !$this->imspSend( $command_text ) )
		{
			// something wrong
			return $this->imspError( $command_text );
		}
	
		$server_response = $this->imspRecieve();
	
		switch ( $server_response )
		{
			case "NO":
				// sorry ... can't do it
				return $this->imspError( $command_text );
				
			case "BAD":
				// Don't know what your talking about.
				return $this->imspError( $command_text );
				
			case "OK":
				$this->debug->Message( "DELETE Completed successfully." );
				return true;
		}
	}

	/**
	 * Attempts to acquire a semephore on the addressbook entry, $bookEntry in 
	 * addressbook $abook.  Will return TRUE || $dummy on success.  Will return FALSE on failure.
	 *
	 * @access public
	 */
	function lockABook( $abook, $bookEntry )
	{ 
	 	$bookEntry    = $this->quoteSpacedString( $bookEntry );
	 	$command_text = "LOCK ADDRESSBOOK $abook $bookEntry";
	 
	 	if ( !$this->imspSend( $command_text ) )
			return $this->imspError( $command_text );
	 
		$server_response = $this->imspRecieve();
	
		do 
		{
		 	switch ( $server_response )
			{
		 		case "NO":
		 			// Could not acquire lock..maybe someone else has it....we should report this in future versions.
		 			$this->exitCode = "That addressbook entry is locked or cannot be unlocked.";
		 			return $this->imspError();
		 	
				case "BAD":
		 			// syntax problem
		 			return $this->imspError();
		 	}
	
			// Check to see if this is a FETCHADDRESS resonse.
			// Do all IMSP implementations return a FETCHADDRESS here?
			$dummy = $this->_parseFetchAddressResponse( $server_response );
		
			// If there was an entry, it will return a FETCHADDRESS response, which we will just
			// toss out and get the next server_response.
			if ( $dummy )
				$server_response = $this->imspRecieve();
		} while ( $server_response != "OK" );
	
		$this->debug->Message( "LOCK ADDRESSBOOK on $abook $bookEntry OK" );
	
		if ( !$dummy )
			return true;
		else
			return $dummy;
	}

	/**
	 * Unlocks a previously locked abook. Takes the name of the addressbook and the name of the entry.
	 *
	 * @return boolean
	 * @access public
	 */
	function unlockABook( $abook, $bookEntry )
	{
		$bookEntry = $this->quoteSpacedString( $bookEntry );	// quote the entry name if needed
		$command_text = "UNLOCK ADDRESSBOOK $abook $bookEntry";	// build the command string
	
		if ( !$this->imspSend( $command_text, true, true ) )
			return $this->imspError( $command_text );
	
		$response = $this->imspRecieve();
	
		switch ( $response )
		{
			case "NO":
				// Could not release the lock for some strange reason...maybe we don't own it?
				return $this->imspError("Could not release the lock...perhaps we are not the owner of the lock.");
		
			case "BAD":
				// some type of syntax error
				return $this->imspError();
		
			case "OK":
				$this->debug->Message( "UNLOCK ADDRESSBOOK on $abook $bookEntry OK" );
				return true;
		}
	}		

	
	/*
	* Access Control List (ACL)  Methods.
	*  
	* The following characters are recognized ACL characters: lrwcda
	* l - "lookup" 	(allows user to see the name and existence of the addressbook)
	* r - "read" 	(allows searching and retreiving addresses from addressbook)
	* w - "write"	(allows creating/editing new addressbook entries - not deleting)
	* c - "create"  (allows creating new addressbooks under the current addressbook hierarchy)
	* d - "delete"  (may delete entries or entire book)
	* a - "admin"   (privledge to set ACL lists for this addressbook - usually only allowed for the owner of the addressbook)
	*
	* Examples:
	* "lr" would be read only for that user
	* "lrw" would be read/write
	*/

	/**
	 * Sets an Access Control List for an abook.
	 * takes the abook name, the username ($ident) and a string containing the ACL characters ($acl)
	 * The ACL string should be a standard ACL type listing of characters such as "lrw" for read/write.
	 *
	 * @access public
	 */
	function setACL( $abook, $ident, $acl )
	{
		// Verify that $acl looks good...
		if ( ereg( "[^" . IMSP_ACL_RIGHTS . "]", $acl ) )
		{
			// error...acl list contained unrecoginzed options
			$this->exitCode = "Bad argument.";
			return $this->imspError( "the setACL() method only accepts the following characters in the ACL " . IMSP_ACL_RIGHTS . "." );
		}
		
		$command_text = "SETACL ADDRESSBOOK $abook $ident $acl";
	
		if ( !$this->imspSend( $command_text ) )
			return $this->imspError( $command_text );
	
		$response = $this->imspRecieve();
	
		switch ( $response )
		{
			case "NO":
				// could not set ACL
				return $this->imspError( "$ident ACL could not be set for addressbook $abook" );
		
			case "BAD":
				// bad syntax
				return $this->imspError();
		
			case "OK":
				return true;
		
			default:
				// don't know why we would make it down here, so return FALSE for now
				$this->exitCode = "Did not recieve the expected response from the server.";
				return $this->imspError();
		}
	}

	/**
	 * Retrieves an addressbook's ACL. 
	 * This function returns an associatve array containing the name of the user as the key and the
	 * ACL string as the value so you would get an array such as this:
	 * $result['jsmith'] = "lrw";
	 * $result['jdoe']   = "r";
	 *
	 * @access public
	 */
	function getACL( $abook )
	{	
		$command_text = "GETACL ADDRESSBOOK $abook";
	
		if (!$this->imspSend( $command_text, true, true ) )
			return $this->imspError( $command_text );
	
		$response = $this->imspRecieve();
	
		switch ( $response )
		{
			case "NO":
				// Could not complete?
				return $this->imspError( "Could not retrieve ACL. Perhaps the addressbook does not exist?" );
		
			case "BAD":
				// Don't know what you said!
				return $this->imspError();
		}
	
		// If we are here, we need to recieve the * ACL Responses
		do
		{	
			// Get an array of responses. 
			// The [3] element should be the addressbook name
			// [4] and [5] will be user/group name and permissions etc...
			$acl = split( " ", $response );												
			
			for ( $i = 4 ; $i < count( $acl ) ; $i += 2 )
				$results[$acl[$i]] = $acl[$i+1];
			
			$response = $this->imspRecieve();
		} while ( ereg( IMSP_ACL_RESPONSE, $response ) );
		
		// Hopefully we can recieve an OK response here.
		
		if ( $response != "OK" )
		{
			// Some weird problem.
			$this->exitCode = "Did not recieve the expected response from the server.";
			return $this->imspError();
		}
		
		return $results;
	}

	/**
	 * Deletes an ACL entry for a abook.
	 * Takes the abook name and the username whose ACL should be deleted.
	 *
	 * @access public
	 */
	function deleteACL( $abook, $ident )
	{
		$command_text = "DELETEACL ADDRESSBOOK $abook $ident";
	
		if ( !$this->imspSend( $command_text ) )
			return $this->imspError( $command_text );
	
		$server_response = $this->imspRecieve();
	
		switch ( $response )
		{
			case "NO":
				// could not complete
				return $this->imspError( "Could not delete the ACL for $ident on addressbook $abook." );
		
			case "BAD":
				// Don't understand!
				return $this->imspError();
		
			case "OK":
				return true;
		
			default:
				// Don't know why we would be here?
				$this->exitCode = "Did not recieve the expected response from the server.";
				return $this->imspError();
		}
	}

	/**
	 * Returns an ACL string containing the rights for the currently logged in user for the addressbook
	 * passed in $abook. Returns FALSE on failure.
	 *
	 * @access public
	 */
	function myRights( $abook )
	{
		$command_text = "MYRIGHTS ADDRESSBOOK $abook";
	
		if ( !$this->imspSend( $command_text ) )
			return $this->imspError( $command_text );
	
		$server_response = $this->imspRecieve();
	
		switch ( $response )
		{
			case "NO":
				// could not complete
				return $this->imspError( "Could not retrieve the ACL for the current user ($this->user)" );
		
			case "BAD":
				// Don't understand!
				return $this->imspError();
		}
	
		if ( !ereg( IMSP_MYRIGHTS_RESPONSE, $server_response ) )
		{
			$this->exitCode = "Did not recieve the expected response from the server.";
			return $this->imspError();
		}	
	
		$temp = split( " ", $server_response );	
		$acl  = $temp[4];	
	
		// Get the OK response.
		$server_response = $this->imspRecieve();
	
		// Check for OK?
		if ( $server_response != "OK" )
		{
			$this->exitCode = "Did not recieve the expected response from the server.";
			return $this->imspError();
		}
		else
		{
			return $acl;
		}
	}
		

	// private methods

	/**
	 * Expects a FETCHADDRESS response to be passed.  Parses it out into an associative array
	 * with name-value pairs from the address book entry
	 *
	 * @access private
	 */
	function _parseFetchAddressResponse( $server_response )
	{
		if ( !ereg( IMSP_FETCHADDRESS_RESPONSE, $server_response ) )
		{
			$this->debug->Message( "[ERROR] Did not recieve expected FETCHADDRESS response from server." );
		
			// Try to decide what the response was here.
			$this->exitCode = "Did not recieve the expected response from the server.";
			return false;
		} 
	
	 	/*
		* Parse out the server response string
		*
		* After choping off the server command response tags and split()'ing the server_response string using
		* a " " as the delimiter, the $parts array contains the chunks of the server returned data.
		*
		* The predifined "name" field starts in $parts[1].  The server should return any single item of data
		* that contains spaces within it as a double quoted string.  So we can interpret the existence of a 
		* double quote at the beginning of a chunk to mean that the next chunk(s) are to be considered part of 
		* the same value.  A double quote at the end of a chunk signifies the end of that value and the chunk
		* following that can be interpreted as a key name.
		*
		* We also need to watch for the server returning a {} response for the value of the key as well.
		*/
		
		$chopped_response = trim( ereg_replace( IMSP_FETCHADDRESS_RESPONSE, "", $server_response ) );
		$parts            = split( " ", $chopped_response );
		$numOfParts       = count( $parts );
		$name             = $parts[1];
		$firstChar        = substr( $name, 0, 1 );
		
		// Check to see if the first char of the name string is a double quote so
		// we know if we have to extract more of the name in the following chunks
				 
		if ( $firstChar == "\"" )
		{
			for ( $i = 2 ; $i < $numOfParts; $i++ )
			{
				$name .=  " " . $parts[$i];
				$lastChar = substr( $parts[$i], strlen( $parts[$i] ) - 1, 1 );
			
				if ( $lastChar == "\"" )
				{
					$nextKey = $i + 1;
					break;
				}
			}
		}
		else
		{
			// If only one chunk for 'name' then we just have to point to the
			// next chunk in the array...which will hopefully be '2'.
			$nextKey = 2;
		}
									
		$lastChar = "";																				
		$entry["name"] = $name;
				
		// Start parsing the rest of the response.
		for ( $i = $nextKey ; $i < $numOfParts ; $i += 2 )
		{
			$key = $parts[$i];
			
			// literal string response?
			if ( ereg( "(^{)([0-9]{1,})(\}$)", $parts[$i+1], $tempArray ) )
			{
				$dataSize    = $tempArray[2];
				$server_data = $this->recieveStringLiteral( $dataSize );
				$entry[$key] = $server_data;
						
				// Read any remaining data from the stream and reset the counter variables
				// so the loop will continue correctly. Note we set $i to -2 because it will
				// be incremented by 2 before the loop will run again.
			
				$parts = $this->getServerResponseChunks();
				$i= -2;
				$numOfParts = count( $parts );
			}
			else
			{
				$entry[$key] = $parts[$i + 1];
					
				// Check to see if the value started with a double quote.
				// This signifies that the value continues to the next element.
					 
				if ( substr( $parts[$i+1], 0, 1 ) == "\"" )
				{
					do
					{
						$nextElement  = $parts[$i+2];
						$entry[$key] .= " " . $nextElement;
						
						// Was this element the last one?
						$lastChar = substr( $nextElement, strlen( $nextElement ) - 1, 1 );
					
						if ( $lastChar == "\"" )
						{
							$done = true;
							$i++;
						
							break;
						}
						else
						{							
							// Check to see if the next element is the last one
							// If so, the do loop will terminate.
							$done = false;
							$lastChar = substr( $parts[$i+3], strlen( $parts[$i+3] ) - 1, 1 );
							$i++;
						}
					} while ( $lastChar != "\"" );
						
					// Do we need to add the final element, or was there only two total?
					if ( !$done )
					{
						$nextElement  = $parts[$i+2];
						$entry[$key] .= " " . $nextElement;
						$i++;
					}
				}					 
			}
		}	
	
		return $entry;
	}
} // END OF IMSPAddressBook

?>
