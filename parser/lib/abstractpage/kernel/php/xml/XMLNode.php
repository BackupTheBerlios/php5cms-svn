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
using( 'xml.XMLPCData' );
using( 'xml.XMLCData' );


define( 'XMLNODE_INDENT_DEFAULT', 0 );
define( 'XMLNODE_INDENT_WRAPPED', 1 );
define( 'XMLNODE_INDENT_NONE',    2 );


/**
 * Class representing a node.
 *
 * @package xml
 */

class XMLNode extends XML
{
	/**
	 * @access public
	 */
    var $name = '';
	
	/**
	 * @access public
	 */
	var $attribute = array();
	
	/**
	 * @access public
	 */
	var $content = '';
	
	/**
	 * @access public
	 */
	var $children = array();


    /**
     * Constructor
     *
     * <code>
     *   $n = &new XMLNode( 'document' );
     *   $n = &new XMLNode( 'text', 'Hello World' );
     *   $n = &new XMLNode( 'article', '', array( 'id' => 42 ) );
     *   $n = &new XMLNode( array(
     *     'name'    => 'changedby',
     *     'content' => 'me'
     *   ));
     * </code>
     *
     * @access  public
     * @param   mixed*
     * @throws  Error
     */
    function XMLNode()
	{
      	switch ( func_num_args() ) 
		{
        	case 0: 
         		$this->XML();
          		break;
          
        	case 1:
          		if ( is_array( $arg = func_get_arg( 0 ) ) ) 
				{
            		$this->XML( $arg );
            		break;
          		}
          
		  		$this->name = $arg;
          		break;
          
        	case 2:
          		list( $this->name, $this->content ) = func_get_args();
          		$this->XML();
          		break;
          
        	case 3:
          		list( $this->name, $this->content, $this->attribute ) = func_get_args();
          		$this->XML();
          		break;
          
        	default:
				$this = new PEAR_Error( 'Wrong number of arguments passed.' );
				return;
      	}
    }

    
    /**
     * Create a node from an array.
     *
     * Usage example:
     * <code>
     *   $n = &XMLNode::fromArray( $array, 'elements' );
     * </code>
     *
     * @static
     * @access  public
     * @param   array arr
     * @param   string name default 'array'
     */
    function &fromArray( $arr, $name = 'array' ) 
	{
      	$n = &new XMLNode( $name );
      	$n->_recurse( $n, $arr );
      
	  	return $n;  
    }
    
    /**
     * Create a node from an object. Will use class name as node name
     * if the optional argument name is omitted.
     *
     * Usage example:
     * <code>
     *   $n = &XMLNode::fromObject( $object );
     * </code>
     *
     * @static
     * @access  public
     * @param   object obj
     * @param   string name default null
     */
    function &fromObject( $obj, $name = null ) 
	{
      	return XMLNode::fromArray(
        	get_object_vars( $obj ), 
        	( $name === null )? get_class( $obj ) : $name
      	);
    }
    
    /**
     * Set content.
     *
     * @access  public
     * @param   string contennt
     */
    function setContent( $content ) 
	{
      	$this->content= $content;
    }
    
    /**
     * Get content (all CDATA).
     *
     * @access  public
     * @return  string content
     */
    function getContent()
	{
      	return $this->content;
    }

    /**
     * Set an attribute.
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setAttribute( $name, $value ) 
	{
      	$this->attribute[$name]= $value;
    }
    
    /**
     * Retrieve an attribute by its name.
     *
     * @access  public
     * @param   string name
     * @return  string
     */
    function getAttribute( $name ) 
	{
      	return $this->attribute[$name];
    }

    /**
     * Checks whether a specific attribute is existant.
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    function hasAttribute( $name ) 
	{
      	return isset( $this->attribute[$name] );
    }
    
    /**
     * Retrieve XML representation.
     *
     * Setting indent to 0 (XMLNODE_INDENT_DEFAULT) yields this result:
     * <pre>
     *   <item>  
     *     <title>Website created</title>
     *     <link/>
     *     <description>The first version of the XP web site is online</description>
     *     <dc:date>2002-12-27T13:10:00</dc:date>
     *   </item>
     * </pre>
     *
     * Setting indent to 1 (XMLNODE_INDENT_WRAPPED) yields this result:
     * <pre>
     *   <item>
     *     <title>
     *       Website created
     *     </title>
     *     <link/>
     *     <description>
     *       The first version of the XP web site is online
     *     </description>
     *     <dc:date>
     *       2002-12-27T13:10:00
     *     </dc:date>  
     *   </item>
     * </pre>
     *
     * Setting indent to 2 (XMLNODE_INDENT_NONE) yields this result (wrapped for readability,
     * returned XML is on one line):
     * <pre>
     *   <item><title>Website created</title><link></link><description>The 
     *   first version of the XP web site is online</description><dc:date>
     *   2002-12-27T13:10:00</dc:date></item>
     * </pre>
     *
     * @access  public
     * @param   int indent default XMLNODE_INDENT_WRAPPED
     * @param   string inset default ''
     * @return  string XML
     */
    function getSource( $indent = XMLNODE_INDENT_WRAPPED, $inset = '' ) 
	{
      	$xml = $inset . '<' . $this->name;
      
	  	if ( is_a( $this->content, 'XMLPCData' ) )
        	$content = $this->content->pcdata;
      	else if ( is_a( $this->content, 'XMLCData' ) )
        	$content = '<![CDATA[' . str_replace( ']]>', ']]&gt;', $this->content->cdata ) . ']]>';
      	else
        	$content= htmlspecialchars( $this->content );

      	switch ( $indent ) 
		{
        	case XMLNODE_INDENT_DEFAULT:
        
			case XMLNODE_INDENT_WRAPPED:
          		if ( !empty( $this->attribute ) ) 
				{
            		$sep = ( sizeof( $this->attribute ) < 3 )? '' : "\n" . $inset;
            
					foreach ( array_keys( $this->attribute ) as $key )
              			$xml .= $sep . ' ' . $key . '="' . htmlspecialchars( $this->attribute[$key] ) . '"';
            
            		$xml.= $sep;
          		}

          		// No content and no children => close tag
          		if ( 0 == strlen( $content ) ) 
				{
            		if ( empty( $this->children ) )
              			return $xml . "/>\n";
            
            		$xml .= '>';
          		} 
				else 
				{
            		$xml .= '>' . ( $indent? "\n  " . $inset . $content : trim( $content ) );
          		}

          		if ( !empty( $this->children ) ) 
				{
            		$xml .= ( $indent? '' : $inset ) . "\n";
            
					foreach ( array_keys($this->children) as $key )
              			$xml .= $this->children[$key]->getSource( $indent, $inset . '  ' );
            
            		$xml = ( $indent? substr( $xml, 0, -1 ) : $xml ) . $inset;
          		}
          
		  		return $xml . ( $indent? "\n" . $inset : '' ) . '</' . $this->name . ">\n";
          
        	case XMLNODE_INDENT_NONE:
          		foreach ( array_keys( $this->attribute ) as $key )
            		$xml .= ' ' . $key . '="' . htmlspecialchars( $this->attribute[$key] ) . '"';
          
          		$xml .= '>' . trim( $content );
          
          		if ( !empty( $this->children ) ) 
				{
            		foreach ( array_keys( $this->children ) as $key )
              			$xml .= $this->children[$key]->getSource( $indent, $inset );
          		}
          
		  		return $xml . '</' . $this->name . '>';
      	}
    }
    
    /**
     * Add a child node.
     *
     * @access  public
     * @param   &XMLNode child
     * @return  &XMLNode added child
     * @throws  Error
     */
    function &addChild( &$child ) 
	{
      	if ( !is_a( $child, 'XMLNode' ) ) 
        	return PEAR::raiseError( "Parameter child must be a XMLNode." );
      
      	$this->children[] = &$child;
      	return $child;
    }
	
	
	// private methods
	
    /**
     * Recurse an array.
     *
     * @access  protected
     * @param   &XMLNode element to add array to
     * @param   array a
     */
    function _recurse( &$e, $a ) 
	{
      	foreach ( array_keys( $a ) as $field ) 
		{
        	$child = &$e->addChild( new XMLNode( is_numeric( $field )? preg_replace( '=s$=', '', $e->name ) : $field ) );
        
			if ( is_array( $a[$field] ) ) 
          		$this->_recurse( $child, $a[$field] );
			else if ( is_object( $a[$field] ) ) 
          		$this->_recurse( $child, get_object_vars( $a[$field] ) );
			else 
          		$child->setContent( $a[$field] );
      	}
	}
} // END OF XMLNode

?>
