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


using( 'peer.mail.imap.IMAP_Client' );


define( 'IMAP_RECENT',  0x0001 );
define( 'IMAP_UNSEEN',  0x0002 );
define( 'IMAP_ANWERED', 0x0004 );
define( 'IMAP_DELETED', 0x0008 );
define( 'IMAP_DRAFT',   0x0016 );
define( 'IMAP_FLAGGED', 0x0032 );


/**
 * @package peer_mail_imap
 */

class IMAP_Mail extends PEAR
{
	/**
	 * @access public
	 */
  	var $header;
	
	/**
	 * @access public
	 */
	var $body;

	/**
	 * @access public
	 */	
	var $imapID;

	/**
	 * @access public
	 */	
	var $imap;

      
    /**
     * Constructor
     *
     * @access public
     * @param  array headers
     */
    function IMAP_Mail( $id, $param )
	{
	  	$this->imapID = $id;
      	$this->header = $param;
      	$this->body   = null;
    }
    
	
    /**
     * Set connection object.
     *
     * @access public
     * @param  IMAP_Client objImap
     */
    function setIMAP( &$imapConnection )
	{
      	$this->imap = &$imapConnection;
    }
    
    /**
	 * @access public
	 */
    function getTo()
	{
      	return $this->_getHeader( 'to' );
    }
    
	/**
	 * @access public
	 */
    function getFrom()
	{
      	$from = $this->_getHeader( 'from' );
      	return $from[0]->mailbox . '@' . $from[0]->host;
    }
    
	/**
	 * @access public
	 */
    function getFromAddress()
	{
      	return imap_qprint( $this->_getHeader( 'fromaddress' ) );
    }
    
	/**
	 * @access public
	 */
    function getCc()
	{
      	return $this->_getHeader( 'cc' );
    }
    
	/**
	 * @access public
	 */
    function getSubject()
	{
      	return imap_qprint( $this->_getHeader( 'subject' ) );
    }
    
	/**
	 * @access public
	 */
    function getDate()
	{
        return $this->_getHeader( 'udate' );
    }
    
	/**
	 * @access public
	 */
    function getFlags()
	{
      	$flag = 0;

      	foreach ( array ( 'Recent', 'Unseen', 'Answered', 'Deleted', 'Draft', 'Flagged' ) as $type => $name )
		{
       		if ( ' ' != $this->_getHeader( $name ) )
          		$flag |= pow( 2, $type );
      	}
      
      	return $flag;
    }
    
	/**
	 * @access public
	 */
    function getBody()
	{
      	if ( $this->body === null )
        	$this->body = $this->imap->_getImapBody( $this->imapID );
      
      	return $this->body;
    }
    
	/**
	 * @access public
	 */
    function move( $newFolder )
	{
      	return $this->imap->_moveMail( $this->imapID, $newFolder );
    }
    
	/**
	 * @access public
	 */
    function delete()
	{
      	return $this->imap->_deleteMail( $this->imapID );
    }
  
  
  	// private methods
	
	/**
	 * @access private
	 */
   	function _getHeader( $header )
	{
      	if ( isset( $this->header->{$header} ) )
          	return $this->header->{$header};
      
        return null;
    }
} // END OF IMAP_Mail

?>
