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


using( 'peer.mail.imap.IMAP_Mail' );
  
  
/**
 * @package peer_mail_imap
 */

class IMAP_Folder extends PEAR
{
	/**
	 * @access public
	 */
   	var $folderName;
	
	/**
	 * @access public
	 */
	var $activeMailbox;

	/**
	 * @access public
	 */      
    var $msg;
	
	/**
	 * @access public
	 */
	var $ptr;
	
	/**
	 * @access public
	 */
	var $imap;
      
	  
    /**
     * Constructor
     *
     * @access public
     * @param  string folderName
     * @param  IMAP_Client clt
     */
    function IMAP_Folder( $folderName )
	{
      	$this->folderName    = $folderName;
      	$this->activeMailbox = '';
      	$this->imap          = null;
      	$this->ptr           = null;
    }
    
	
    /**
     * Fetches all headers.
     * 
     * @access public
     * @return bool success
     */
    function init()
	{
      	if ( !$this->isActive() && !$this->open() )
        	return false;
      
      	$cntHeaders = $this->imap->_numMsg();
      	
		if ( $this->msg !== null )
		{
        	for ( $i = 0; $i < count( $this->msg ); $i++ )
          		unset( $this->msg[$i] );
      	}

      	$this->msg = array ();
      	
		for ( $i = 0; $i < $cntHeaders; $i++ )
		{
        	$hdr = $this->imap->_getHeader( $i + 1 );
        	
			$mail = &new IMAP_Mail( $i + 1, $hdr );
        	$mail->setIMAP( $this->imap );

        	$this->msg[$i] = &$mail;
      	}
  	}
    
    /**
     * Sets the imap object.
     *
     * @access public
     * @param  IMAP_Client clt
     */
    function setIMAP( &$imap )
	{
      	$this->imap = &$imap;
    }

    /**
     * Is this the active mailbox?
     *
     * @access public
     * @return bool isActive
     */    
    function isActive()
	{
      	return ( $this->activeMailbox == $this->folderName );
    }
    
    /**
     * Set new active mailbox.
     *
     * @access public
     * @param string activeMailbox
     */
    function setActiveMailbox( $mbx )
	{
      	$this->activeMailbox = $mbx;
    }
  
    /**
     * Open this folder.
     *
     * @access public
     * @return bool success
     */
    function open()
	{
      	return $this->imap->_openMailbox( $this->folderName );
    }
    
    /** 
     * Iterate through mails.
     *
     * @access public
     * @return IMAP_Mail obj
     */
    function &getNextMail()
	{
      	if ( $this->ptr === null )
        	$this->ptr = 0;
        
      	if ( !isset( $this->msg[$this->ptr] ) ) 
		{
        	$this->ptr = null;
        	return null;
      	}
      
      	return $this->msg[$this->ptr++];
    }
    
    /**
     * Commits all changes.
     *
     * @access public
     * @return bool success
     */
    function expunge()
	{
      	if ( !$this->isActive() )
        	return PEAR::raiseError( 'Inactive folder cannot be expunged.' );
      
      
      	$retval = $this->imap->_expunge();
      	$this->init ();
      	
		return $retval;
    }
} // END OF IMAP_Folder

?>
