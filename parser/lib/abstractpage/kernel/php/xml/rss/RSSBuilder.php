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


using( 'xml.rss.RSSItem' );


/**
 * Class for creating a RSS file
 *
 * @package xml_rss
 */

class RSSBuilder extends PEAR 
{
	/**
	 * Encoding of the XML file
	 *
	 * @access private
	 * @var string
	 */
	var $encoding;

	/**
	 * URL where the RSS document will be made available
	 *
	 * @access private
	 * @var string
	 */
	var $about;

	/**
	 * Title of the rss stream
	 *
	 * @access private
	 * @var string
	 */
	var $title;

	/**
	 * Description of the rss stream
	 *
	 * @access private
	 * @var string
	 */
	var $description;

	/**
	 * Publisher of the rss stream (person, an organization, or a service)
	 *
	 * @access private
	 * @var string
	 */
	var $publisher;

	/**
	 * Creator of the rss stream (person, an organization, or a service)
	 *
	 * @access private
	 * @var string
	 */
	var $creator;

	/**
	 * Creation date of the file (format: 2003-05-29T00:03:07+0200)
	 *
	 * @access private
	 * @var string
	 */
	var $date;

	/**
	 * ISO format language
	 *
	 * @access private
	 * @var string
	 */
	var $language;

	/**
	 * Copyrights for the rss stream
	 *
	 * @access private
	 * @var string
	 */
	var $rights;

	/**
	 * URL to an small image
	 *
	 * @access private
	 * @var string
	 */
	var $image_link;

	/**
	 * Spatial location, temporal period or jurisdiction
	 *
	 * spatial location (a place name or geographic coordinates), temporal
	 * period (a period label, date, or date range) or jurisdiction (such as a
	 * named administrative entity)
	 *
	 * @access private
	 * @var string
	 */
	var $coverage;

	/**
	 * Person, an organization, or a service
	 *
	 * @access private
	 * @var string
	 */
	var $contributor;

	/**
	 * 'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly'
	 *
	 * @access private
	 * @var string
	 */
	var $period;

	/**
	 * Date (format: 2003-05-29T00:03:07+0200)
	 *
	 * Defines a base date to be used in concert with updatePeriod and
	 * updateFrequency to calculate the publishing schedule.
	 *
	 * @access private
	 * @var string
	 */
	var $base;

	/**
	 * Category (rss 2.0)
	 *
	 * @access private
	 * @var string
	 */
	var $category;

	/**
	 * Compiled outputstring
	 *
	 * @access private
	 * @var string
	 */
	var $output;

	/**
	 * Every X hours/days/weeks/...
	 *
	 * @var int
	 * @access private
	 */
	var $frequency;

	/**
	 * Caching time in minutes (rss 2.0)
	 *
	 * @var int
	 * @access private
	 */
	var $cache;

	/**
	 * Array wich all the rss items
	 *
	 * @var array
	 * @access private
	 */
	var $items = array();

	/**
	 * Use DC data
	 *
	 * @var boolean
	 * @access private
	 */
	var $use_dc_data = false;

	/**
	 * Use SY data
	 *
	 * @var boolean
	 * @access private
	 */
	var $use_sy_data = false;

	
	/**
	 * Constructor
	 *
	 * @param string $encoding encoding of the xml file
	 * @param string $about URL where the RSS document will be made available
	 * @param string $title
	 * @param string $description
	 * @param string $image_link  URL
	 * @uses setEncoding()
	 * @uses setAbout()
	 * @uses setTitle()
	 * @uses setDescription()
	 * @uses setImageLink()
	 * @uses setCategory()
	 * @uses etCache()
	 * @access public
	 * @return void
	 */
	function RSSBuilder( $encoding = '', $about = '', $title = '', $description = '', $image_link = '', $category = '', $cache = '' ) 
	{
		$this->setEncoding( $encoding );
		$this->setAbout( $about );
		$this->setTitle( $title );
		$this->setDescription( $description );
		$this->setImageLink( $image_link );
		$this->setCategory( $category );
		$this->setCache( $cache );
	}

	
	/**
	 * Add additional DC data.
	 *
	 * @param string $publisher person, an organization, or a service
	 * @param string $creator person, an organization, or a service
	 * @param string $date  format: 2003-05-29T00:03:07+0200
	 * @param string $language  iso-format
	 * @param string $rights  copyright information
	 * @param string $coverage  spatial location (a place name or geographic coordinates), temporal period (a period label, date, or date range) or jurisdiction (such as a named administrative entity)
	 * @param string $contributor  person, an organization, or a service
	 * @uses setPublisher()
	 * @uses setCreator()
	 * @uses setDate()
	 * @uses setLanguage()
	 * @uses setRights()
	 * @uses setCoverage()
	 * @uses setContributor()
	 * @access public
	 * @return void
	 */
	function addDCdata( $publisher = '', $creator = '', $date = '', $language = '', $rights = '', $coverage = '', $contributor = '' ) 
	{
		$this->setPublisher( $publisher );
		$this->setCreator( $creator );
		$this->setDate( $date );
		$this->setLanguage( $language );
		$this->setRights( $rights );
		$this->setCoverage( $coverage );
		$this->setContributor( $contributor );
		
		$this->use_dc_data = (boolean)true;
	}

	/**
	 * Add additional SY data.
	 *
	 * @param string $period  'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly'
	 * @param int $frequency  every x hours/days/weeks/...
	 * @param string $base  format: 2003-05-29T00:03:07+0200
	 * @uses setPeriod()
	 * @uses setFrequency()
	 * @uses setBase()
	 * @access public
	 * @return void
	 */
	function addSYdata( $period = '', $frequency = '', $base = '' ) 
	{
		$this->setPeriod( $period );
		$this->setFrequency( $frequency );
		$this->setBase( $base );
		
		$this->use_sy_data = (boolean)true;
	}

	/**
	 * Echos the output.
	 *
	 * use this function if you want to directly output the rss stream
	 *
	 * @return void
	 * @access public
	 * @uses createOutput()
	 */
	function outputRSS( $version = '' ) 
	{
		if ( !isset( $this->output ) )
			$this->createOutput( $version );
		
		header( 'content-type: text/xml' );
		header( 'Content-Disposition: inline; filename=rss_' . str_replace( ' ', '', $this->title ) . '.xml' );

		$this->output = '<?xml version="1.0" encoding="' . $this->encoding . '"?>' . "\n" . $this->output;
		echo $this->output;
	}

	/**
	 * Returns the output.
	 * Use this function if you want to have the output stream as a string (for example to write it in a cache file).
	 *
	 * @return void
	 * @access public
	 * @uses createOutput()
	 */
	function getRSSOutput( $version = '' ) 
	{
		if ( !isset( $this->output ) )
			$this->createOutput( $version );
		
		return (string)'<?xml version="1.0" encoding="' . $this->encoding . '"?>' . "\n" . $this->output;
	}
	
	/**
	 * Checks if a given string is a valid iso-language-code.
	 *
	 * @param string $code  String that should validated
	 * @return boolean $isvalid  If string is valid or not
	 * @static
	 * @access public
	 */
	function isValidLanguageCode( $code = '' ) 
	{
		return (boolean)( ( preg_match( '(^([a-zA-Z]{2})$)', $code ) > 0 )? true : false );
	}

	/**
	 * Returns $encoding variable.
	 *
	 * @return string $encoding
	 * @see $image_link
	 * @access public
	 */
	function getEncoding() 
	{
		return (string)$this->encoding;
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
	 * Returns $publisher variable.
	 *
	 * @return string $publisher
	 * @see $publisher
	 * @access public
	 */
	function getPublisher() 
	{
		return (string)$this->publisher;
	}

	/**
	 * Returns $creator variable.
	 *
	 * @return string $creator
	 * @see $creator
	 * @access public
	 */
	function getCreator() 
	{
		return (string)$this->creator;
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
	 * Returns $language variable.
	 *
	 * @return string $language
	 * @see $language
	 * @access public
	 */
	function getLanguage() 
	{
		return (string)$this->language;
	}

	/**
	 * Returns $rights variable.
	 *
	 * @return string $rights
	 * @see $rights
	 * @access public
	 */
	function getRights() 
	{
		return (string)$this->rights;
	}

	/**
	 * Returns $coverage variable.
	 *
	 * @return string $coverage
	 * @see $coverage
	 * @access public
	 */
	function getCoverage() 
	{
		return (string)$this->coverage;
	}

	/**
	 * Returns $contributor variable.
	 *
	 * @return string $contributor
	 * @see $contributor
	 * @access public
	 */
	function getContributor() 
	{
		return (string)$this->contributor;
	}

	/**
	 * Returns $image_link variable.
	 *
	 * @return string $image_link
	 * @see $image_link
	 * @access public
	 */
	function getImageLink()
	{
		return (string)$this->image_link;
	}

	/**
	 * Returns $period variable.
	 *
	 * @return string $period
	 * @see $period
	 * @access public
	 */
	function getPeriod()
	{
		return (string)$this->period;
	}

	/**
	 * Returns $frequency variable.
	 *
	 * @return string $frequency
	 * @see $frequency
	 * @access public
	 */
	function getFrequency() 
	{
		return (int)$this->frequency;
	}

	/**
	 * Returns $base variable.
	 *
	 * @return string $base
	 * @see $base
	 * @access public
	 */
	function getBase() 
	{
		return (string)$this->base;
	}

	/**
	 * Returns $category variable.
	 *
	 * @return string $category
	 * @see $category
	 * @access public
	 */
	function getCategory()
	{
		return (string)$this->category;
	}

	/**
	 * Returns $cache variable.
	 *
	 * @return int $cache
	 * @see $cache
	 * @access public
	 */
	function getCache() 
	{
		return (int)$this->cache;
	}

	/**
	 * Adds another rss item to the object.
	 *
	 * @param string $about  URL
	 * @param string $title
	 * @param string $link  URL
	 * @param string $description (optional)
	 * @param string $subject  some sort of category (optional dc value - only shows up if DC data has been set before)
	 * @param string $date  format: 2003-05-29T00:03:07+0200 (optional dc value - only shows up if DC data has been set before)
	 * @param string $author  some sort of category author of item
	 * @param string $comments  url to comment page rss 2.0 value
	 * @param string $image  optional mod_im value for dispaying a different pic for every item
	 * @return void
	 * @see $items
	 * @uses RSSItem
	 * @access public
	 */
	function addItem( $about = '', $title = '', $link = '', $description = '', $subject = '', $date = '', $author = '', $comments = '', $image = '' )
	{
		$item = new RSSItem( $about, $title, $link, $description, $subject, $date, $author, $comments, $image );
		$this->items[] = $item;
	}

	/**
	 * Deletes a rss item from the array.
	 *
	 * @param int $id  id of the element in the $items array
	 * @return boolean true if item was deleted
	 * @see $items
	 * @access public
	 */
	function deleteItem( $id = -1 ) 
	{
		if ( array_key_exists( $id, $this->items ) ) 
		{
			unset( $this->items[$id] );
			return (boolean)true;
		} 
		else 
		{
			return (boolean)false;
		}
	}

	/**
	 * Returns an array with all the keys of the $items array.
	 *
	 * @return array array with all the keys of the $items array
	 * @see $items
	 * @access public
	 */
	function getItemList()
	{
		return (array)array_keys( $this->items );
	}

	/**
	 * Returns the $items array.
	 *
	 * @return array $items
	 * @access public
	 */
	function getItems()
	{
		return (array)$this->items;
	}

	/**
	 * Returns a single rss item by ID.
	 *
	 * @param int $id  id of the element in the $items array
	 * @return mixed RSSItem or false
	 * @see RSSItem
	 * @access public
	 */
	function getItem( $id = -1 ) 
	{
		if ( array_key_exists( $id, $this->items ) )
			return (object)$this->items[$id];
		else
			return (boolean)false;
	}
	
	
	// private methods
	
	/**
	 * Sets $encoding variable.
	 *
	 * @param string $encoding  encoding of the xml file
	 * @see $encoding
	 * @return void
	 * @access private
	 */
	function setEncoding( $encoding = '' ) 
	{
		if ( !isset( $this->encoding ) )
			$this->encoding = (string)( ( strlen( trim( $encoding ) ) > 0 )? trim( $encoding ) : 'UTF-8' );
	}

	/**
	 * Sets $about variable.
	 *
	 * @param string $about
	 * @see $about
	 * @return void
	 * @access private
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
	 * @return void
	 * @access private
	 */
	function setTitle( $title = '' ) 
	{
		if ( !isset( $this->title ) && strlen( trim( $title ) ) > 0 )
			$this->title = (string)trim( $title );
	}

	/**
	 * Sets $description variable.
	 *
	 * @param string $description
	 * @see $description
	 * @return void
	 * @access private
	 */
	function setDescription( $description = '' ) 
	{
		if ( !isset( $this->description ) && strlen( trim( $description ) ) > 0 )
			$this->description = (string)trim( $description );
	}

	/**
	 * Sets $publisher variable.
	 *
	 * @param string $publisher
	 * @see $publisher
	 * @return void
	 * @access private
	 */
	function setPublisher( $publisher = '' ) 
	{
		if ( !isset( $this->publisher ) && strlen( trim( $publisher ) ) > 0 )
			$this->publisher = (string)trim( $publisher );
	}

	/**
	 * Sets $creator variable.
	 *
	 * @param string $creator
	 * @see $creator
	 * @return void
	 * @access private
	 */
	function setCreator( $creator = '' ) 
	{
		if ( !isset( $this->creator ) && strlen( trim( $creator ) ) > 0 )
			$this->creator = (string)trim( $creator );
	}

	/**
	 * Sets $date variable.
	 *
	 * @param string $date  format: 2003-05-29T00:03:07+0200
	 * @see $date
	 * @return void
	 * @access private
	 */
	function setDate( $date = '' ) 
	{
		if ( !isset( $this->date ) && strlen( trim( $date ) ) > 0 )
			$this->date = (string)trim( $date );
	}

	/**
	 * Sets $language variable.
	 *
	 * @param string $language
	 * @see $language
	 * @uses isValidLanguageCode()
	 * @return void
	 * @access private
	 */
	function setLanguage( $language = '' ) 
	{
		if ( !isset( $this->language ) && $this->isValidLanguageCode( $language ) === true )
			$this->language = (string)trim( $language );
	}

	/**
	 * Sets $rights variable.
	 *
	 * @param string $rights
	 * @see $rights
	 * @return void
	 * @access private
	 */
	function setRights( $rights = '' ) 
	{
		if ( !isset( $this->rights ) && strlen( trim( $rights ) ) > 0 )
			$this->rights = (string)trim( $rights );
	}

	/**
	 * Sets $coverage variable.
	 *
	 * @param string $coverage
	 * @see $coverage
	 * @return void
	 * @access private
	 */
	function setCoverage( $coverage = '' ) 
	{
		if ( !isset( $this->coverage ) && strlen( trim( $coverage ) ) > 0 )
			$this->coverage = (string)trim( $coverage );
	}

	/**
	 * Sets $contributor variable.
	 *
	 * @param string $contributor
	 * @see $contributor
	 * @return void
	 * @access private
	 */
	function setContributor( $contributor = '' ) 
	{
		if ( !isset( $this->contributor ) && strlen( trim( $contributor ) ) > 0 )
			$this->contributor = (string)trim( $contributor );
	}

	/**
	 * Sets $image_link variable.
	 *
	 * @param string $image_link
	 * @see $image_link
	 * @return void
	 * @access private
	 */
	function setImageLink( $image_link = '' ) 
	{
		if ( !isset( $this->image_link ) && strlen( trim( $image_link ) ) > 0 )
			$this->image_link = (string)trim( $image_link );
	}

	/**
	 * Sets $period variable.
	 *
	 * @param string $period  'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly'
	 * @see $period
	 * @return void
	 * @access private
	 */
	function setPeriod( $period = '' ) 
	{
		if ( !isset( $this->period ) && strlen( trim( $period ) ) > 0 ) 
		{
			switch ( $period ) 
			{
				case 'hourly':
			
				case 'daily':
			
				case 'weekly':
			
				case 'monthly':
			
				case 'yearly':
					$this->period = (string)trim( $period );
					break;
			
				default:
					$this->period = (string)'';
					break;
			}
		}
	}

	/**
	 * Sets $frequency variable.
	 *
	 * @param int $frequency
	 * @see $frequency
	 * @return void
	 * @access private
	 */
	function setFrequency( $frequency = '' ) 
	{
		if ( !isset( $this->frequency ) && strlen( trim( $frequency ) ) > 0 )
			$this->frequency = (int)$frequency;
	}

	/**
	 * Sets $base variable.
	 *
	 * @param string $base
	 * @see $base
	 * @return void
	 * @access private
	 */
	function setBase( $base = '' ) 
	{
		if ( !isset( $this->base ) && strlen( trim( $base ) ) > 0 )
			$this->base = (string)trim( $base );
	}

	/**
	 * Sets $category variable.
	 *
	 * @param string $category
	 * @see $category
	 * @return void
	 * @access private
	 */
	function setCategory( $category = '' ) 
	{
		if ( strlen( trim( $category ) ) > 0 )
			$this->category = (string)trim( $category );
	}

	/**
	 * Sets $cache variable.
	 *
	 * @param int $cache
	 * @see $cache
	 * @return void
	 * @access private
	 */
	function setCache( $cache = '' ) 
	{
		if ( strlen( trim( $cache ) ) > 0 )
			$this->cache = (int)$cache;
	}
	
	/**
	 * Creates the output based on the 0.91 rss version.
	 *
	 * @see $output
	 * @return void
	 * @access private
	 */
	function createOutputV090() 
	{
		// not implemented
		$this->createOutputV100();
	}

	/**
	 * Creates the output based on the 0.91 rss version.
	 *
	 * @see $output
	 * @return void
	 * @access private
	 */
	function createOutputV091() 
	{
		$this->output  = '<!DOCTYPE rss SYSTEM "http://my.netscape.com/publish/formats/rss-0.91.dtd">' . "\n";
		$this->output .= '<rss version="0.91">' . "\n";
		$this->output .= '<channel>' . "\n";

		if ( strlen( $this->rights ) > 0 )
			$this->output .= '<copyright>' . $this->rights . '</copyright>' . "\n";

		if ( strlen( $this->date ) > 0 ) 
		{
			$this->output .= '<pubDate>' . $this->date . '</pubDate>' . "\n";
			$this->output .= '<lastBuildDate>' . $this->date . '</lastBuildDate>' . "\n";
		}

		if ( strlen( $this->about ) > 0 )
			$this->output .= '<docs>' . $this->about . '</docs>' . "\n";

		if ( strlen( $this->description ) > 0 )
			$this->output .= '<description>' . $this->description . '</description>' . "\n";

		if ( strlen( $this->about ) > 0 )
			$this->output .= '<link>' . $this->about . '</link>' . "\n";

		if ( strlen( $this->title ) > 0 )
			$this->output .= '<title>' . $this->title . '</title>' . "\n";

		if ( strlen( $this->image_link ) > 0 ) 
		{
			$this->output .= '<image>' . "\n";
			$this->output .= '<title>' . $this->title      . '</title>' . "\n";
			$this->output .= '<url>'   . $this->image_link . '</url>' . "\n";
			$this->output .= '<link>'  . $this->about      . '</link>' . "\n";

			if ( strlen( $this->description ) > 0 )
				$this->output .= '<description>' . $this->description . '</description>' . "\n";
			
			$this->output .= '</image>' . "\n";
		}

		if ( strlen( $this->publisher ) > 0 )
			$this->output .= '<managingEditor>' . $this->publisher . '</managingEditor>' . "\n";

		if ( strlen( $this->creator ) > 0 )
			$this->output .= '<webMaster>' . $this->creator . '</webMaster>' . "\n";

		if ( strlen( $this->language ) > 0 )
			$this->output .= '<language>' . $this->language . '</language>' . "\n";

		if ( count( $this->getItemList() ) > 0 ) 
		{
			foreach ( $this->getItemList() as $id ) 
			{
				$item =& $this->items[$id];

				if ( strlen( $item->getTitle() ) > 0 && strlen( $item->getLink() ) > 0 ) 
				{
					$this->output .= '<item>'  . "\n";
					$this->output .= '<title>' . $item->getTitle() . '</title>' . "\n";
					$this->output .= '<link>'  . $item->getLink()  . '</link>'  . "\n";
					
					if ( strlen( $item->getDescription() ) > 0 )
						$this->output .= '<description>' . $item->getDescription() . '</description>' . "\n";
					
					$this->output .= '</item>' . "\n";
				}
			}
		}

		$this->output .= '</channel>' . "\n";
		$this->output .= '</rss>' . "\n";
	}

	/**
	 * Creates the output based on the 1.0 rss version.
	 *
	 * @see $output
	 * @return void
	 * @access private
	 */
	function createOutputV100() 
	{
		$this->output  = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:im="http://purl.org/rss/1.0/item-images/" ';

		if ( $this->use_dc_data === true )
			$this->output .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';

		if ( $this->use_sy_data === true )
			$this->output .= 'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" ';

		$this->output .= 'xmlns="http://purl.org/rss/1.0/">' . "\n";

		if ( strlen( $this->about ) > 0 )
			$this->output .= '<channel rdf:about="' . $this->about . '">' . "\n";
		else
			$this->output .= '<channel>' . "\n";

		if ( strlen( $this->title ) > 0 )
			$this->output .= '<title>' . $this->title . '</title>' . "\n";

		if ( strlen( $this->about ) > 0 )
			$this->output .= '<link>' . $this->about . '</link>' . "\n";

		if ( strlen( $this->description ) > 0 )
			$this->output .= '<description>' . $this->description . '</description>' . "\n";

		// additional dc data
		if ( strlen( $this->publisher ) > 0 )
			$this->output .= '<dc:publisher>' . $this->publisher . '</dc:publisher>' . "\n";

		if ( strlen( $this->creator ) > 0 )
			$this->output .= '<dc:creator>' . $this->creator . '</dc:creator>' . "\n";

		if ( strlen( $this->date ) > 0 )
			$this->output .= '<dc:date>' .$this->date . '</dc:date>' . "\n";

		if ( strlen( $this->language ) > 0 )
			$this->output .= '<dc:language>' . $this->language . '</dc:language>' . "\n";

		if ( strlen( $this->rights ) > 0 )
			$this->output .= '<dc:rights>' . $this->rights . '</dc:rights>' . "\n";

		if ( strlen( $this->coverage ) > 0 )
			$this->output .= '<dc:coverage>' . $this->coverage . '</dc:coverage>' . "\n";

		if ( strlen( $this->contributor ) > 0 )
			$this->output .= '<dc:contributor>' . $this->contributor . '</dc:contributor>' . "\n";

		// additional SY data
		if ( strlen( $this->period ) > 0 )
			$this->output .= '<sy:updatePeriod>' . $this->period . '</sy:updatePeriod>' . "\n";

		if ( strlen( $this->frequency ) > 0 )
			$this->output .= '<sy:updateFrequency>' . $this->frequency . '</sy:updateFrequency>' . "\n";

		if ( strlen( $this->base ) > 0 )
			$this->output .= '<sy:updateBase>' . $this->base . '</sy:updateBase>' . "\n";

		if ( strlen( $this->image_link ) > 0 ) 
		{
			$this->output .= '<image rdf:about="' . $this->image_link . '">' . "\n";
			$this->output .= '<title>' . $this->title      . '</title>' . "\n";
			$this->output .= '<url>'   . $this->image_link . '</url>'   . "\n";
			$this->output .= '<link>'  . $this->about      . '</link>'  . "\n";

			if ( strlen( $this->description ) > 0 )
				$this->output .= '<description>' . $this->description . '</description>' . "\n";
			
			$this->output .= '</image>' . "\n";
		}

		if ( count( $this->getItemList() ) > 0 ) 
		{
			$this->output .= '<items><rdf:Seq>' . "\n";
			
			foreach ( $this->getItemList() as $id ) 
			{
				$item =& $this->items[$id];
				
				if ( strlen( $item->getAbout() ) > 0 )
					$this->output .= ' <rdf:li resource="' . $item->getAbout() . '" />' . "\n";
			}
			
			$this->output .= '</rdf:Seq></items>' . "\n";
		}
		
		$this->output .= '</channel>' . "\n";

		if ( count( $this->getItemList() ) > 0 ) 
		{
			foreach ( $this->getItemList() as $id ) 
			{
				$item =& $this->items[$id];

				if ( strlen( $item->getTitle() ) > 0 && strlen( $item->getLink() ) > 0 ) 
				{
					if ( strlen( $item->getAbout() ) > 0 )
						$this->output .= '<item rdf:about="' . $item->getAbout() . '">' . "\n";
					else
						$this->output .= '<item>' . "\n";

					$this->output .= '<title>' . $item->getTitle() . '</title>' . "\n";
					$this->output .= '<link>'  . $item->getLink()  . '</link>'  . "\n";

					if ( strlen( $item->getDescription() ) > 0 )
						$this->output .= '<description>' . $item->getDescription() . '</description>' . "\n";

					if ( $this->use_dc_data === true && strlen( $item->getSubject() ) > 0 )
						$this->output .= '<dc:subject>' . $item->getSubject() . '</dc:subject>' . "\n";

					if ( $this->use_dc_data === true && strlen( $item->getDate() ) > 0 )
						$this->output .= '<dc:date>' . $item->getDate() . '</dc:date>' . "\n";

					if ( strlen( $item->getImage() ) > 0 )
						$this->output .= '<im:image>' . $item->getImage() . '</im:image>' . "\n";

					$this->output .= '</item>' . "\n";
				}
			}
		}

		$this->output .= '</rdf:RDF>';
	}

	/**
	 * Creates the output based on the 2.0 rss draft.
	 *
	 * @see $output
	 * @return void
	 * @access private
	 */
	function createOutputV200()
	{
		$this->output  = '<rss version="2.0" xmlns:im="http://purl.org/rss/1.0/item-images/" ';

		if ( $this->use_dc_data === true )
			$this->output .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';

		if ( $this->use_sy_data === true )
			$this->output .= 'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" ';

		$this->output .= '>' . "\n";
		$this->output .= '<channel>' . "\n";

		if ( strlen( $this->rights ) > 0 )
			$this->output .= '<copyright>' . $this->rights . '</copyright>' . "\n";

		if ( strlen( $this->date ) > 0 ) 
		{
			$this->output .= '<pubDate>' . $this->date . '</pubDate>' . "\n";
			$this->output .= '<lastBuildDate>' . $this->date . '</lastBuildDate>' . "\n";
		}

		if ( strlen( $this->about ) > 0 )
			$this->output .= '<docs>' . $this->about . '</docs>' . "\n";

		if ( strlen( $this->description ) > 0 )
			$this->output .= '<description>' . $this->description . '</description>' . "\n";

		if ( strlen( $this->about ) > 0 )
			$this->output .= '<link>' . $this->about . '</link>' . "\n";

		if ( strlen( $this->title ) > 0 )
			$this->output .= '<title>' . $this->title . '</title>' . "\n";

		if ( strlen( $this->image_link ) > 0 ) 
		{
			$this->output .= '<image>' . "\n";
			$this->output .= '<title>' . $this->title      . '</title>' . "\n";
			$this->output .= '<url>'   . $this->image_link . '</url>'   . "\n";
			$this->output .= '<link>'  . $this->about      . '</link>'  . "\n";

			if ( strlen( $this->description ) > 0 )
				$this->output .= '<description>' . $this->description . '</description>' . "\n";
			
			$this->output .= '</image>' . "\n";
		}

		if ( strlen( $this->publisher ) > 0 )
			$this->output .= '<managingEditor>' . $this->publisher . '</managingEditor>' . "\n";

		if ( strlen( $this->creator ) > 0 ) 
		{
			$this->output .= '<webMaster>' . $this->creator . '</webMaster>' . "\n";
			$this->output .= '<generator>' . $this->creator . '</generator>' . "\n";
		}

		if ( strlen( $this->language ) > 0 )
			$this->output .= '<language>' . $this->language . '</language>' . "\n";

		if ( strlen( $this->category ) > 0 )
			$this->output .= '<category>' . $this->category . '</category>' . "\n";

		if ( strlen( $this->cache ) > 0 )
			$this->output .= '<ttl>' . $this->cache . '</ttl>' . "\n";

		// additional dc data
		if ( strlen( $this->publisher ) > 0 )
			$this->output .= '<dc:publisher>' . $this->publisher . '</dc:publisher>' . "\n";

		if ( strlen( $this->creator ) > 0 )
			$this->output .= '<dc:creator>' . $this->creator . '</dc:creator>' . "\n";

		if ( strlen( $this->date ) > 0 )
			$this->output .= '<dc:date>' .$this->date . '</dc:date>' . "\n";

		if ( strlen( $this->language ) > 0 )
			$this->output .= '<dc:language>' . $this->language . '</dc:language>' . "\n";

		if ( strlen( $this->rights ) > 0 )
			$this->output .= '<dc:rights>' . $this->rights . '</dc:rights>' . "\n";

		if ( strlen( $this->coverage ) > 0 )
			$this->output .= '<dc:coverage>' . $this->coverage . '</dc:coverage>' . "\n";

		if ( strlen( $this->contributor ) > 0 )
			$this->output .= '<dc:contributor>' . $this->contributor . '</dc:contributor>' . "\n";

		// additional SY data
		if ( strlen( $this->period ) > 0 )
			$this->output .= '<sy:updatePeriod>' . $this->period . '</sy:updatePeriod>' . "\n";

		if ( strlen( $this->frequency ) > 0 )
			$this->output .= '<sy:updateFrequency>' . $this->frequency . '</sy:updateFrequency>' . "\n";

		if ( strlen( $this->base ) > 0 )
			$this->output .= '<sy:updateBase>' . $this->base . '</sy:updateBase>' . "\n";

		if ( count( $this->getItemList() ) > 0 ) 
		{
			foreach ( $this->getItemList() as $id ) 
			{
				$item =& $this->items[$id];

				if ( strlen( $item->getTitle() ) > 0 && strlen( $item->getLink() ) > 0 ) 
				{
					$this->output .= '<item>'  . "\n";
					$this->output .= '<title>' . $item->getTitle() . '</title>' . "\n";
					$this->output .= '<link>'  . $item->getLink()  . '</link>'  . "\n";

					if ( strlen( $item->getDescription() ) > 0 )
						$this->output .= '<description>' . $item->getDescription() . '</description>' . "\n";

					if ( $this->use_dc_data === true && strlen( $item->getSubject() ) > 0 )
						$this->output .= '<category>' . $item->getSubject() . '</category>' . "\n";

					if ( $this->use_dc_data === true && strlen( $item->getDate() ) > 0 )
						$this->output .= '<pubDate>' . $item->getDate() . '</pubDate>' . "\n";

					if ( strlen( $item->getAbout() ) > 0 )
						$this->output .= '<guid>' . $item->getAbout() . '</guid>' . "\n";

					if ( strlen( $item->getAuthor() ) > 0 )
						$this->output .= '<author>' . $item->getAuthor() . '</author>' . "\n";

					if ( strlen( $item->getComments() ) > 0 )
						$this->output .= '<comments>' . $item->getComments() . '</comments>' . "\n";

					if ( strlen( $item->getImage() ) > 0 )
						$this->output .= '<im:image>' . $item->getImage() . '</im:image>' . "\n";
					
					$this->output .= '</item>' . "\n";
				}
			}
		}

		$this->output .= '</channel>' . "\n";
		$this->output .= '</rss>' . "\n";
	}

	/**
	 * Creates the output.
	 *
	 * @uses createOutputV090()
	 * @uses createOutputV091()
	 * @uses createOutputV200()
	 * @uses createOutputV100()
	 * @return void
	 * @access private
	 */
	function createOutput( $version = '' ) 
	{
		if ( strlen( trim( $version ) ) === 0 )
			$version = '1.0';

		switch ( $version ) 
		{
			case '0.9':
				$this->createOutputV090();
				break;
			
			case '0.91':
				$this->createOutputV091();
				break;
			
			case '2.00':
				$this->createOutputV200();
				break;
			
			case '1.0':
			
			default:
				$this->createOutputV100();
				break;
		}
	}
} // END OF RSSBuilder

?>
