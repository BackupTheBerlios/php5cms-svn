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


using( 'peer.mail.imap.IMAP_Folder' );


/**
 * IMAP Client
 *
 * Example
 * <code>
 * $imap= &new IMAP_Client( array(
 *     'host' => 'imap.foo.bar',
 *     'user' => 'baz',
 *     'pass' => '**censored**'
 * ) );
 *
 * $imap->init();
 * $inbox = &$imap->getFolder( 'INBOX' );
 * $inbox->init();
 *
 * while ( ( $mail = &$inbox->getNextMail() ) !== null )
 * {
 *     echo sprintf( "%d - From: %20s %s\n",
 *       	$mail->getFlags(),
 *       	$mail->getFromAddress(),
 *       	$mail->getSubject()
 *     );
 * }
 * </code>
 *
 * @package peer_mail_imap
 */   

class IMAP_Client extends PEAR
{
	/**
	 * @access public
	 */
    var $host;
	
	/**
	 * @access public
	 */
	var $mbx;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $proto;

	/**
	 * @access public
	 */
	var $user;
	
	/**
	 * @access public
	 */
	var $pass;
	
	/**
	 * @access public
	 */
	var $mailbox;
    
	/**
	 * @access private
	 */
    var $_hdl;


    /**
     * Constructor
     *
     * @access  public
     * @param   string host default 'localhost' LDAP server
     * @param   array
     */
    function IMAP_Client( $params )
	{
      	$this->proto   = 'imap';
      	$this->port    = 143;
      	$this->mbx     = 'INBOX';
      	$this->mailbox = array ();
    }
    
    
    /**
     * Connect to IMAP server.
     *
     * @access public
     * @return IMAP resource handle
     */
    function connect()
	{
      	if ( ( $this->_hdl= imap_open( $this->_getIMAPConnectionString(), $this->user, $this->pass ) ) === false )
          	return PEAR::raiseError( 'Cannot connect to ' . $this->_getIMAPConnectionString() );
        
        return $this->_hdl;
    }
	
    /**
     * Returns folder object.
     *
     * @access public
     * @param  string folder
     * @return IMAP_Folder folder
     */
    function &getFolder( $folderName ) 
	{
      	$folderName = $this->_getIMAPFolderString( $folderName );
      
      	if ( !$this->existsMailbox( $folderName ) )
        	return null;
      
      	return $this->mailbox[$folderName];
    }
    
    /**
     * Close IMAP connection.
     *
     * @access public
     * @return bool success
     */
    function close()
	{
      	return imap_close( $this->_hdl );
    }
    
    /**
     * Load all mailbox names.
     *
     * @access public
     * @return bool success
     */
    function init()
	{
      	if ( !$this->_hdl )
        	$this->connect();
        
      	$mailboxes = imap_list (
        	$this->_hdl, 
        	$this->_getIMAPConnectionString(),
        	'*'
      	);
      
      	foreach ( $mailboxes as $mbx ) 
		{
        	$this->mailbox[$mbx]= &new IMAP_Folder( $mbx );
        	$this->mailbox[$mbx]->setIMAP( $this );
      	}
      
      	return true;
    }
    
    /**
     * Show all mailboxes.
     *
     * @access public
     * @return array mailboxes
     */
    function getMailboxes()
	{
      	return array_keys( $this->mailbox );
    }
    
	/**
	 * @access public
	 */
    function existsMailbox( $mbx )
	{
      	return isset( $this->mailbox[$mbx] );
    }
    
	
	// private methods
	
    /**
     * Returns IMAP folder string.
     *
     * @access public
     * @param  string foldername default null
     * @return string folderstring
     */
    function _getIMAPFolderString( $folderName = null ) 
	{
      	if ( $folderName === null ) 
        	$folderName = $this->mbx;
      
      	return sprintf( '{%s:%d/%s}%s',
        	$this->host,
        	$this->port,
        	$this->proto,
        	$folderName
      	);
    }
	
    /**
     * Create the IMAP reference string.
     *
     * @access private
     * @return string ref
     */
    function _getIMAPConnectionString()
	{
      	return sprintf( '{%s:%d/%s}',
        	$this->host,
        	$this->port,
        	$this->proto
      	);
    }
	
    /**
     * Expunges the mailbox.
     *
     * @access public
     * @return bool success
     *
     */
    function _expunge()
	{
      	return imap_expunge( $this->_hdl );
    }
    
    /**
     * Fetches mail body.
     *
     * @access public
     * @param int messageid
     * @return string body
     */
    function _getImapBody( $id )
	{
      	return $this->imap_body( $this->_hdl, $id );
    }
    
    /** 
     * Move mail to another folder.
     *
     * @access public
     * @param int messageid
     * @param string newfolder
     * @return bool success
     */
    function _moveMail( $id, $folder ) 
	{
      	$fqFoldername = $this->_getIMAPFolderString( $folder );
      
	  	if ( !$this->existsMailbox( $fqFolderName ) )
        	return PEAR::raiseError( 'Mail cannot be moved to nonexistant folder.' );
      
      	return imap_mail_move( $this->_hdl, $id, $folder );
    }
    
    /**
     * Delete mail.
     *
     * @access public
     * @param int messageid
     * @return bool success
     */
    function _deleteMail( $id ) 
	{
      	return imap_delete( $this->_hdl, $id );
    }
    
    /**
     * Return number of messages in active folder.
     *
     * @access public
     * @return int count
     */
    function _numMsg()
	{
      	return imap_num_msg( $this->_hdl );
    }
    
    /**
     * Get mail's headers.
     *
     * @access public
     * @param int index
     * @return object header
     */    
    function _getHeader( $idx )
	{
      	return imap_headerinfo( $this->_hdl, $idx );
    }
    
    /**
     * Switch to new Folder, notify all mailboxes.
     *
     * @access public
     * @param string newfolder
     * @return bool success
     */
    function _openMailbox( $newMailbox ) 
	{
      	// Does this mailbox exists?
      	if ( !$this->existsMailbox( $newMailbox ) )
        	return PEAR::raiseError( 'Mailbox does not exist: ' . $newMailbox );
      
      	$this->mbx = $newMailbox;
      	
		if ( imap_reopen( $this->_hdl, $newMailbox ) === true ) 
		{
        	// Notify mailboxes about folder change
        	foreach ( $this->mailbox as $name => $mbx )
          		$this->mailbox[$name]->setActiveMailbox( $newMailbox );
        
        	return true;
      	} 
		else
		{
        	return false;
		}
    }
} // END OF IMAP_Client

?>
