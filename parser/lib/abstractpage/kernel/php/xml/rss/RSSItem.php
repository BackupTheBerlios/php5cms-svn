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


/**
 * Single RSS item object.
 *
 * @package xml_rss
 */

class RSSItem extends PEAR 
{
	/**
	 * URL
	 *
	 * @access private
	 * @var string
	 */
	var $about;

	/**
	 * Headline
	 *
	 * @access private
	 * @var string
	 */
	var $title;

	/**
	 * URL to the full item
	 *
	 * @access private
	 * @var string
	 */
	var $link;

	/**
	 * Optional description
	 *
	 * @access private
	 * @var string
	 */
	var $description;

	/**
	 * Optional subject (category)
	 *
	 * @access private
	 * @var string
	 */
	var $subject;

	/**
	 * Optional date
	 *
	 * @access private
	 * @var string
	 */
	var $date;

	/**
	 * Author of item
	 *
	 * @access private
	 * @var string
	 */
	var $author;

	/**
	 * Url to comments page (rss 2.0)
	 *
	 * @access private
	 * @var string
	 */
	var $comments;

	/**
	 * Imagelink for this item (mod_im only)
	 *
	 * @access private
	 * @var string
	 */
	var $image;


	/**
	 * Constructor
	 *
	 * @param string $about  URL
	 * @param string $title
	 * @param string $link  URL
	 * @param string $description (optional)
	 * @param string $subject  some sort of category (optional)
	 * @param string $date  format: 2003-05-29T00:03:07+0200 (optional)
	 * @param string $author  some sort of category author of item
	 * @param string $comments  url to comment page rss 2.0 value
	 * @param string $image  optional mod_im value for dispaying a different pic for every item
	 * @uses setAbout()
	 * @uses setTitle()
	 * @uses setLink()
	 * @uses setDescription()
	 * @uses setSubject()
	 * @uses setDate()
	 * @uses setAuthor()
	 * @uses setComments()
	 * @uses setImage()
	 * @access public
	 * @return void
	 */
	function RSSItem( $about = '', $title = '', $link = '', $description = '', $subject = '', $date = '', $author = '', $comments = '', $image = '' ) 
	{
		$this->setAbout( $about );
		$this->setTitle( $title );
		$this->setLink( $link );
		$this->setDescription( $description );
		$this->setSubject( $subject );
		$this->setDate( $date );
		$this->setAuthor( $author );
		$this->setComments( $comments );
		$this->setImage( $image );
	}


	/**
	 * Returns $about variable.
	 *
	 * @return string $about
	 * @see $about
	 * @access public
	 */
	function getAbout() 
	{
		return (string)$this->about;
	}

	/**
	 * Returns $title variable.
	 *
	 * @return string $title
	 * @see $title
	 * @access public
	 */
	function getTitle()
	{
		return (string)$this->title;
	}

	/**
	 * Returns $link variable.
	 *
	 * @return string $link
	 * @see $link
	 * @access public
	 */
	function getLink() 
	{
		return (string)$this->link;
	}

	/**
	 * Returns $description variable.
	 *
	 * @return string $description
	 * @see $description
	 * @access public
	 */
	function getDescription() 
	{
		return (string)$this->description;
	}

	/**
	 * Returns $subject variable.
	 *
	 * @return string $subject
	 * @see $subject
	 * @access public
	 */
	function getSubject() 
	{
		return (string)$this->subject;
	}

	/**
	 * Returns $date variable.
	 *
	 * @return string $date
	 * @see $date
	 * @access public
	 */
	function getDate() 
	{
		return (string)$this->date;
	}

	/**
	 * Returns $author variable.
	 *
	 * @return string $author
	 * @see $author
	 * @access public
	 */
	function getAuthor() 
	{
		return (string)$this->author;
	}

	/**
	 * Returns $comments variable.
	 *
	 * @return string $comments
	 * @see $comments
	 * @access public
	 */
	function getComments()
	{
		return (string)$this->comments;
	}

	/**
	 * Returns $image variable.
	 *
	 * @return string $image
	 * @see $image
	 * @access public
	 */
	function getImage()
	{
		return (string)$this->image;
	}
	
	
	// private methods
	
	/**
	 * Sets $about variable.
	 *
	 * @param string $about
	 * @see $about
	 * @access private
	 * @return void
	 */
	function setAbout( $about = '' ) 
	{
		if ( !isset( $this->about ) && strlen( trim( $about ) ) > 0 )
			$this->about = (string)trim( $about );
	}

	/**
	 * Sets $title variable.
	 *
	 * @param string $title
	 * @see $title
	 * @access private
	 * @return void
	 */
	function setTitle( $title = '' ) 
	{
		if ( !isset( $this->title ) && strlen( trim( $title ) ) > 0 )
			$this->title = (string)trim( $title );
	}

	/**
	 * Sets $link variable.
	 *
	 * @param string $link
	 * @see $link
	 * @access private
	 * @return void
	 */
	function setLink( $link = '' ) 
	{
		if ( !isset( $this->link ) && strlen( trim( $link ) ) > 0 )
			$this->link = (string)trim( $link );
	}

	/**
	 * Sets $description variable.
	 *
	 * @param string $description
	 * @see $description
	 * @access private
	 * @return void
	 */
	function setDescription( $description = '' ) 
	{
		if ( !isset( $this->description ) && strlen( trim( $description ) ) > 0 )
			$this->description = (string)trim( $description );
	}

	/**
	 * Sets $subject variable.
	 *
	 * @param string $subject
	 * @see $subject
	 * @access private
	 * @return void
	 */
	function setSubject( $subject = '' ) 
	{
		if ( !isset( $this->subject ) && strlen( trim( $subject ) ) > 0 )
			$this->subject = (string)trim( $subject );
	}

	/**
	 * Sets $date variable.
	 *
	 * @param string $date
	 * @see $date
	 * @access private
	 * @return void
	 */
	function setDate( $date = '' ) 
	{
		if ( !isset( $this->date ) && strlen( trim( $date ) ) > 0 )
			$this->date = (string)trim( $date );
	}

	/**
	 * Sets $author variable.
	 *
	 * @param string $author
	 * @see $author
	 * @access private
	 * @return void
	 */
	function setAuthor( $author = '' ) 
	{
		if ( !isset( $this->author ) && strlen( trim( $author ) ) > 0 ) 
			$this->author = (string)trim( $author );
	}

	/**
	 * Sets $comments variable.
	 *
	 * @param string $comments
	 * @see $comments
	 * @access private
	 * @return void
	 */
	function setComments( $comments = '' ) 
	{
		if ( !isset( $this->comments ) && strlen( trim( $comments ) ) > 0 )
			$this->comments = (string)trim( $comments );
	}

	/**
	 * Sets $image variable.
	 *
	 * @param string $image
	 * @see $image
	 * @access private
	 * @return void
	 */
	function setImage( $image = '' ) 
	{
		if ( !isset( $this->image ) && strlen( trim( $image ) ) > 0 )
			$this->image = (string)trim( $image );
	}
} // END OF RSSItem

?>
