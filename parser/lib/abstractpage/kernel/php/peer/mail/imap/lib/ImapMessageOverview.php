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


using( 'peer.mail.imap.lib.ImapMessageOverviewFlags' );


/**
 * @package peer_mail_imap_lib
 */
 
class ImapMessageOverview extends PEAR
{
	/**
	 * @access public
	 */
	var $subject;
	
	/**
	 * @access public
	 */
	var $from;
	
	/**
	 * @access public
	 */
	var $priority;
	
	/**
	 * @access public
	 */
	var $messageid;
	
	/**
	 * @access public
	 */
	var $cc;
	
	/**
	 * @access public
	 */
	var $to;
	
	/**
	 * @access public
	 */
	var $date;
	
	/**
	 * @access public
	 */
	var $size;
	
	/**
	 * @access public
	 */
	var $content_type;
	
	/**
	 * @access public
	 */
	var $flags;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ImapMessageOverview()
	{
		$this->subject      = '(no subject)';
		$this->from         = '(unknown sender)';
		$this->priority     = 0;
		$this->messageid    = '<>';
		$this->cc           = '';
		$this->to           = '';
		$this->date         = '';
		$this->size         = 0;
		$this->content_type = '';
		$this->flags        = new ImapMessageOverviewFlags();
	}
} // END OF ImapMessageOverview

?>
