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

 
using( 'xml.XML' );
using( 'xml.XMLParser' );
using( 'xml.XMLNode' );
 

/**
 * @package xml
 */
 
class XMLTree extends XML
{
	/**
	 * @access public
	 */
    var $root = null;
	
	/**
	 * @access public
	 */
	var $children = array();
	
	/**
	 * @access public
	 */
	var $nodeType = 'node';

	/**
	 * @access private
	 */
    var $_cnt;
	
	/**
	 * @access private
	 */
	var $_cdata;
	
	/**
	 * @access private
	 */
	var $_objs;
    
	
    /**
     * Constructor
     *
     * @access  public
     * @param   array params default null
     */
    function XMLTree( $params = null ) 
	{
      	$this->_objs = array();        
      	$this->root  = &new XMLNode( 'document' );
      	
		$this->XML( $params );
    }
    
	
    /**
     * Retrieve XML representation.
     *
     * @access  public
     * @param   bool indent default true whether to indent
     * @return  string
     */
    function getSource( $indent = true ) 
	{
      	return ( isset( $this->root )? $this->root->getSource( $indent ) : null );
    }
     
    /**
     * Add a child to this tree.
     *
     * @access  public
     * @param   &XMLNode child 
     * @return  &XMLNode the added child
     */   
    function &addChild( &$child ) 
	{
      	return $this->root->addChild( $child );
    }

    /**
     * Construct an XML tree from a string.
     *
     * @static
     * @access  public
     * @param   string string
     * @param   string c default __CLASS__ class name
     * @return  &XMLTree
     */
    function &fromString( $string, $c = __CLASS__ ) 
	{
      	$parser = &new XMLParser();
      	$tree   = &new $c();
      
        $parser->callback = &$tree;
        $result = $parser->parse( $string, 1 );
        $parser->__destruct();

      	return $tree;
    }
    
    /**
     * Construct an XML tree from a file.
	 *
     * @static
     * @access  public
     * @param   &File file
     * @param   string c default __CLASS__ class name
     * @return  &XMLTree
     */ 
    function &fromFile( &$file, $c = __CLASS__ ) 
	{
      	$parser = &new XMLParser();
      	$tree   = &new $c();

        $parser->callback   = &$this;
        $parser->dataSource = $file->uri;
        $file->open( FILE_MODE_READ );
        $string = $file->read( $file->size() );
        $file->close();
        
        // Now, parse it
        $result = $parser->parse( $string );
        $parser->__destruct();

      	return $tree;
    }
    
    /**
     * Callback function for XMLParser.
     *
     * @access  magic
     */
    function onStartElement( $parser, $name, $attrs ) 
	{
      	$this->_cdata= "";

      	$element = new $this->nodeType( array(
        	'name'          => $name,
        	'attribute'     => $attrs,
        	'content'       => ''
      	) );  

      	if ( !isset( $this->_cnt ) ) 
		{
        	$this->root     = &$element;
        	$this->_objs[1] = &$element;
        	$this->_cnt     = 1;
      	} 
		else 
		{
        	$this->_cnt++;
        	$this->_objs[$this->_cnt] = &$element;
      	}
    }
   
    /**
     * Callback function for XMLParser.
     *
     * @access  magic
     */
    function onEndElement( $parser, $name ) 
	{
      	if ( $this->_cnt > 1 ) 
		{
        	$node = &$this->_objs[$this->_cnt];
        	$node->content = $this->_cdata;
        	$parent = &$this->_objs[$this->_cnt - 1];
        	$parent->addChild( $node );
        	$this->_cdata = "";
      	}
      
	  	$this->_cnt--;
    }

    /**
     * Callback function for XMLParser.
     *
     * @access  magic
     */
    function onCData( $parser, $cdata ) 
	{
      	$this->_cdata .= $cdata;
    }

    /**
     * Callback function for XMLParser.
     *
     * @access  magic
     */
    function onDefault( $parser, $data ) 
	{
    }
} // END OF XMLTree

?>
