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

 
using( 'xml.XMLTree' );
using( 'xml.XMLNode' );
using( 'util.datetime.Date' );

  
define( 'RDFNEWSFEED_RDF', 0x0000 );
define( 'RDFNEWSFEED_RSS', 0x0001 );
 
 
/**
 * RDF- and RSS- newsfeeds.
 *
 * @link http://www.w3.org/RDF/
 * @link http://dublincore.org/2001/08/14/dces#
 * @link http://dublincore.org/2001/08/14/dces_deDE
 * @package xml_rdf
 */

class RDFNewsFeed extends XMLTree
{
	/**
	 * @access public
	 */
    var $channel;
	
	/**
	 * @access public
	 */
	var $image;
	
	/**
	 * @access public
	 */
	var $items;
      
	/**
	 * @access public
	 */
    var $type = RDFNEWSFEED_RDF;


    /**
     * Constructor
     *
     * @access public
     */
    function RDFNewsFeed()
	{
		$this->XMLTree();
		
	  	$this->root = &new XMLNode( 'rdf:RDF',
			'',
        	array(
          		'xmlns:rdf'   => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
          		'xmlns:dc'    => 'http://purl.org/dc/elements/1.1/',
          		'xmlns'       => 'http://my.netscape.com/rdf/simple/0.9/'
        	)
      	);
      
	  	$this->channel = &new stdClass();
      	$this->image   = &new stdClass();
      	$this->items   = array();
    }
    
	
    /**
     * Sets the channel element.
     *
     * @access  public
     * @param   string title
     * @param   string link
     * @param   string description default ''
     * @param   string Date default null date defaulting to the current time
     * @param   string language default '' e.g. en_US, de_DE, fr_FR, ...
     * @param   string creator default ''
     * @param   string publisher default ''
     * @param   string rights default ''
     */
    function setChannel( $title, $link, $description = '', $date = null, $language = '', $creator = '', $publisher = '', $rights = '' ) 
	{
      	if ( $date === null ) 
			$date = &new Date( time() );
      
      	$this->channel->title       = $title;
      	$this->channel->link        = $link;
      	$this->channel->description = $description;
      	$this->channel->date        = $date;
      	$this->channel->language    = $language;
      	$this->channel->creator     = $creator;
      	$this->channel->publisher   = $publisher;
      	$this->channel->copyright   = $rights;
      
      	$node = &XMLNode::fromArray( array(
        	'title'         => $title,
        	'link'          => $link,
        	'description'   => $description,
        	'dc:language'   => $language,
        	'dc:date'       => $date->toString( 'Y-m-d\TH:i:s' ),
        	'dc:creator'    => $creator,
        	'dc:publisher'  => $publisher,
        	'dc:rights'     => $rights
      	), 'channel' );
      
	  	if ( !isset( $this->channel->node ) ) 
			$node = &$this->root->addChild( $node );
      
	  	$this->channel->node = &$node;
    }
    
    /**
     * Set the channel image.
     *
     * @access  public
     * @param   string title
     * @param   string url
     * @param   string link default ''
     */
    function setImage( $title, $url, $link = '' ) 
	{
      	$this->image->title = $title;
      	$this->image->url   = $url;
      	$this->image->title = $title;

      	$node = &XMLNode::fromArray( array(
        	'title' => $title,
        	'url'   => $url,
        	'link'  => $link
      	), 'image' );
      
	  	if ( !isset( $this->image->node ) ) 
			$node = &$this->root->addChild( $node );
      
	  	$this->image->node = &$node;
    }
    
    /**
     * Adds an item.
     *
     * @access  public
     * @param   string title
     * @param   string link
     * @param   string description default ''
     * @param   string Date default null date defaulting to current date/time
     * @return  object the added item
     */
    function &addItem( $title, $link, $description = '', $date = null ) 
	{
      	if ( $date === null )
        	$date = isset( $this->channel->date )? $this->channel->date : new Date( time() );
      
      	$item              = &new stdClass();
      	$item->title       = $title;
      	$item->link        = $link;
      	$item->description = $description;
      
      	$node= &XMLNode::fromArray( array(
        	'title'         => $title,
        	'link'          => $link,
        	'description'   => $description,
        	'dc:date'       => $date->toString( 'Y-m-d\TH:i:s' )
      	), 'item' );
      
	  	$item->node = &$this->root->addChild( $node );
      	$this->items[]= &$item;
      
      	return $item;
    }

    /**
     * Callback for XML parser.
     *
     * @access  public
     */
    function onStartElement( $parser, $name, $attrs ) 
	{
      	parent::onStartElement( $parser, $name, $attrs );
      
      	switch ( $this->_pathname() ) 
		{
        	case '/rss/':
          		$this->type = RDFNEWSFEED_RSS;
          		break;
          
        	case '/rdf:rdf/':
          		$this->type = RDFNEWSFEED_RDF;
          		break;

        	case '/rdf:rdf/channel/': 
        
			case '/rss/channel/':
          		$this->channel->node = &$this->_objs[$this->_cnt];
          		break;
          
        	case '/rdf:rdf/image/': 
        
			case '/rss/image/':
          		$this->image->node = &$this->_objs[$this->_cnt];
          		break;
          
        	case '/rdf:rdf/item/': 
        
			case '/rss/channel/item/':
          		$this->items[] = &new stdClass();
          		$this->items[sizeof( $this->items ) - 1]->node = &$this->_objs[$this->_cnt];
          		break;
      	}
    }          

    /**
     * Callback for XML parser.
     *
     * @access  public
     * @throws  Error in case an unrecognized element is encountered
     */
    function onEndElement( $parser, $name ) 
	{
      	static $trans;
      
      	$path = $this->_pathname();
      	parent::onEndElement( $parser, $name );
      
	  	if ( $this->_cnt <= 0 ) 
			return;

      	// replace &lt; &amp;, &#XX; etc.
      	if ( !isset( $trans ) ) 
			$trans = array_flip( get_html_translation_table( HTML_ENTITIES ) );
      
	  	$cdata = preg_replace(
        	'/&#([0-9]+);/me', 
        	'chr(\'\1\')', 
        	strtr( trim( $this->_objs[$this->_cnt + 1]->content ), $trans )
      	);
      
      	$name = strtr( substr( $path, 0, -1 ), array(
        	'/rdf:rdf/' => '',
        	'/rss/'     => ''
      	) );
      
	  	switch ( $name ) 
		{
        	case 'channel/title':
          		$this->channel->title = $cdata;
          		break;
          
        	case 'channel/link':
          		$this->channel->link = $cdata;
          		break;
          
        	case 'channel/description':
          		$this->channel->description = $cdata;
          		break;

        	case 'channel/language':
        
			case 'channel/dc:language':
          		$this->channel->language = $cdata;
          		break;

        	case 'channel/copyright':
        	
			case 'channel/dc:rights':
          		$this->channel->copyright = $cdata;
          		break;

        	case 'channel/pubdate': // 14 May 2002
        
			case 'channel/dc:date': // 2002-07-12T15:59
          		$this->channel->date = &new Date( str_replace( 'T', ' ', $cdata ) );
          		break;

        	case 'channel/dc:publisher':
          		$this->channel->publisher = $cdata;
          		break;

        	case 'channel/dc:creator':
          		$this->channel->creator = $cdata;
          		break;

        	case 'channel/image/url':
        	
			case 'image/url':
          		$this->image->url = $cdata;
          		break;
        
        	case 'channel/image/title':
        
			case 'image/title':
          		$this->image->title = $cdata;
          		break;

        	case 'channel/image/link':
        
			case 'image/link':
          		$this->image->link = $cdata;
          		break;

        	case 'channel/item/title':
        
			case 'item/title':
          		$this->items[sizeof( $this->items ) - 1]->title = $cdata;
          		break;

        	case 'channel/item/description':
        
			case 'item/description':
          		$this->items[sizeof( $this->items ) - 1]->description = $cdata;
          		break;
         
        	case 'channel/item/link': 
        
			case 'item/link':
          		$this->items[sizeof( $this->items ) - 1]->link = $cdata;
          		break;
          
        	case 'channel/item/date':
        
			case 'item/dc:date': // 2002-07-12T15:59
          		$this->items[sizeof( $this->items ) - 1]->date = &new Date( str_replace( 'T', ' ', $cdata ) );
          		break;

        	default:
				return PEAR::raiseError( 'Unrecognized element "' . $name . '".' );
      	}
    }
	
	
	// private methods
	
    /**
     * @access  private
     * @return  string path, e.g. /rdf:rdf/item/rc:summary/
     */
    function _pathname()
	{
      	$path = '';
      
	  	for ( $i= $this->_cnt; $i> 0; $i-- )
        	$path = strtolower( $this->_objs[$i]->name ) . '/' . $path;
      
      	return '/' . $path;
    }
} // END OF RDFNewsFeed

?>
