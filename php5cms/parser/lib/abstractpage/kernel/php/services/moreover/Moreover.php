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


using( 'services.moreover.MoreoverArticle' );
using( 'services.moreover.MoreoverCategory' );
using( 'services.moreover.MoreoverChannel' );


/**
 * This collection of classes are my attempt to create a complete
 * API in PHP to the Moreover.com content syndication service.
 * It has the ability to retrieve a listing of all current content
 * channels and categories as well as retrieve all articles for
 * those categories.
 *
 * @package services_moreover
 */

class Moreover extends PEAR
{
	/**
	 * @access public
	 */
	var $getArticleURL;
	
	/**
	 * @access public
	 */
	var $getChannelURL;
	
	/**
	 * @access public
	 */
	var $getSearchURL;
	
	/**
	 * @access public
	 */
	var $ArticleObjects;
	
	/**
	 * @access public
	 */
	var $ChannelObjects;
	
	/**
	 * @access private
	 */
	var $_XMLParser;
	
	/**
	 * @access private
	 */
	var $_CurrentTag;
	
	/**
	 * @access private
	 */
	var $_CurrentArticle;
	
	/**
	 * @access private
	 */
	var $_CurrentChannel;
	
	/**
	 * @access private
	 */
	var $_CurrentCategory;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Moreover()
	{
		$this->ArticleObjects = array();
		$this->ChannelObjects = array();

		// Setup the proper URL strings.
		$this->getArticleURL  = "http://www.moreover.com/cgi-local/page?o=xml&c=";
		$this->getSearchURL   = "http://p.moreover.com/cgi-local/page?o=xml&k=";
		$this->getChannelURL  = "http://w.moreover.com/categories/nested_category_list.xml";
	}

	
	/**
	 * @access public
	 */
	function getArticles()
	{
		$this->_clearArrays ();

		foreach ( func_get_args () as $argument )
		{
			if ( is_array( $argument ) )
			{
				foreach ( $argument as $arg )
				{
					$res = $this->_interactAndParse( $this->getArticleURL . urlencode( $arg ) );
					
					if ( PEAR::isError( $res ) )
						return $res;
				}
			}
			else
			{
				$res = $this->_interactAndParse( $this->getArticleURL . urlencode( $argument ) );
				
				if ( PEAR::isError( $res ) )
					return $res;
			}
		}

		return $this->ArticleObjects;
	}

	/**
	 * @access public
	 */
	function getChannel( $channel )
	{
		$output = array();
		$this->getChannels();

		foreach ( $this->ChannelObjects as $channelObj )
		{
			if ( $channelObj->ChannelName == $channel )
			{
				$output = $channelObj->CategoryObjects;
				break;
			}
		}

		return $output;
	}

	/**
	 * @access public
	 */
	function getChannels()
	{
		$this->_clearArrays();
		$this->_interactAndParse( $this->getChannelURL );

		return $this->ChannelObjects;
	}

	/**
	 * @access public
	 */
	function searchArticles()
	{
		$this->_clearArrays();

		foreach ( func_get_args() as $argument )
		{
			if ( is_array( $argument ) )
			{
				foreach ( $argument as $arg )
					$this->_interactAndParse( $this->getSearchURL . urlencode( $arg ) );
			}
			else
			{
				$this->_interactAndParse( $this->getSearchURL . urlencode( $argument ) );
			}
		}

		return $this->ArticleObjects;
	}

	
	// private methods

	/**
	 * @access private
	 */		
	function _clearArrays()
	{
		$this->ArticleObjects = array();
		$this->ChannelObjects = array();
	}

	/**
	 * @access private
	 */		
	function _interactAndParse( $XMLFile )
	{
		// initilize the XML parser
		$this->_XMLParser = xml_parser_create( 'UTF-8' );
		xml_parser_set_option( $this->_XMLParser, XML_OPTION_CASE_FOLDING, true );
		xml_parser_set_option( $this->_XMLParser, XML_OPTION_TARGET_ENCODING, 'UTF-8' );
		xml_set_element_handler( $this->_XMLParser, "_tagOpen", "_tagClose" );
		xml_set_character_data_handler( $this->_XMLParser, "_cdata" );
		xml_set_object( $this->_XMLParser, &$this );
		
		if ( !( $fp = fopen( $XMLFile, 'r' ) ) )
		{
			$this->_cleanUp();
			return PEAR::raiseError( "Could not open xml file for parsing." );
		}
					
		while ( $data = fread( $fp, 4096 ) )
		{
			if ( !( $data = utf8_encode( $data ) ) )
			{
				$this->_cleanUp();
				return PEAR::raiseError( "Problems encoding UTF8." );
			}
			
			if ( !xml_parse( $this->_XMLParser, $data, feof( $fp ) ) )
			{
				$this->_cleanUp();
				
				return PEAR::raiseError(
					sprintf( 
						"XML error: %s at line %d\n\n",
						xml_error_string( xml_get_error_code( $this->_XMLParser ) ),
						xml_get_current_line_number( $this->_XMLParser ) 
					)
				);
			}
		}
		
		fclose( $fp );
		$this->_cleanUp();
		
		return true;
	}

	/**
	 * @access private
	 */		
	function _tagOpen( $parser, $tag, $attributes )
	{
		$this->_CurrentTag = $tag;
		
		switch ( $tag )
		{
			case "ARTICLE":
				$this->_CurrentArticle  = new MoreoverArticle();
				break;
				
			case "CATEGORY":
				$this->_CurrentCategory = new MoreoverCategory();
				break;
				
			case "CHANNEL":
				$this->_CurrentChannel  = new MoreoverChannel();
					break;
		}
	}

	/**
	 * @access private
	 */		
	function _tagClose( $parser, $tag )
	{
		switch ( $tag )
		{
			case "ARTICLE":
				array_push( $this->ArticleObjects, $this->_CurrentArticle );
				break;
				
			case "CATEGORY":
				array_push( $this->_CurrentChannel->CategoryObjects, $this->_CurrentCategory );
				break;
			
			case "CHANNEL":
				array_push( $this->ChannelObjects, $this->_CurrentChannel );
				break;
		}
	}

	/**
	 * @access private
	 */		
	function _cdata( $parser, $cdata )
	{
		switch ( $this->_CurrentTag )
		{
			case "URL":
				if ( !$this->_CurrentArticle->URL )
					$this->_CurrentArticle->URL = $cdata;
				
				break;
			
			case "HEADLINE_TEXT":
				if ( !$this->_CurrentArticle->HeadlineText )
					$this->_CurrentArticle->HeadlineText = $cdata;
				
				break;
			
			case "SOURCE":
				if ( !$this->_CurrentArticle->Source )
					$this->_CurrentArticle->Source = $cdata;
				
				break;
			
			case "MEDIA_TYPE":
				if ( !$this->_CurrentArticle->MediaType )
					$this->_CurrentArticle->MediaType = $cdata;
				
				break;
			
			case "CLUSTER":
				if ( !$this->_CurrentArticle->Cluster )
					$this->_CurrentArticle->Cluster = $cdata;
				
				break;
			
			case "TAGLINE":
				if ( !$this->_CurrentArticle->Tagline )
					$this->_CurrentArticle->Tagline = $cdata;
				
				break;
			
			case "DOCUMENT_URL":
				if ( !$this->_CurrentArticle->DocumentURL )
					$this->_CurrentArticle->DocumentURL = $cdata;
				
				break;
			
			case "HARVEST_TIME":
				if ( !$this->_CurrentArticle->HarvestTime )
					$this->_CurrentArticle->HarvestTime = $cdata;
				
				break;
			
			case "ACCESS_REGISTRATION":
				if ( !$this->_CurrentArticle->AccessRegistration )
					$this->_CurrentArticle->AccessRegistration = $cdata;
				
				break;
			
			case "ACCESS_STATUS":
				if ( !$this->_CurrentArticle->AccessStatus )
					$this->_CurrentArticle->AccessStatus = $cdata;
				
				break;
			
			case "CHANNEL_NAME":
				if ( !$this->_CurrentChannel->ChannelName )
					$this->_CurrentChannel->ChannelName = $cdata;
				
				break;
			
			case "CATEGORY_NAME":
				if ( !$this->_CurrentCategory->CategoryName )
					$this->_CurrentCategory->CategoryName = $cdata;
				
				break;
			
			case "FEED_NAME":
				if ( !$this->_CurrentCategory->FeedName )
					$this->_CurrentCategory->FeedName = $cdata;
				
				break;
		}
	}

	/**
	 * @access private
	 */		
	function _cleanUp()
	{
		xml_parser_free( $this->_XMLParser );
	}
} // END OF Moreover

?>
