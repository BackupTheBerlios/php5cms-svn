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


/**
 * unknown element
 */ 
define( 'CBLELEMENT_ERROR_ELEMENT_NOT_FOUND', 150 );
 
/**
 * unknown attribute
 */ 
define( 'CBLELEMENT_ERROR_ATTRIBUTE_UNKNOWN', 200 );
 
/**
 * attribute is no integer
 */ 
define( 'CBLELEMENT_ERROR_ATTRIBUTE_NO_INTEGER', 201 );
 
/**
 * attribute is no integer
 */ 
define( 'CBLELEMENT_ERROR_ATTRIBUTE_NO_BOOLEAN', 202 );
 
/**
 * attribute contains invalid value
 */ 
define( 'CBLELEMENT_ERROR_ATTRIBUTE_INVALID_VALUE', 203 );

/**
 * invalid child element
 */ 
define( 'CBLELEMENT_ERROR_INVALID_CHILD_ELEMENT', 204 );

 
/**
 * Base class for all elements
 *
 * @todo check for valid sub elements
 * @package xml_cbl_lib
 */
 
class CBLElement extends PEAR
{
	/**
	 * element name
	 *
	 * @access 	public
	 * @var		string
	 */
    var $elementName;

	/**
	 * flag to indicate whether xml entities should be replaced
     *
     * @access	public
     * @var		boolean
     */
    var $replaceEntities = true;

	/**
	 * attributes of the element
	 *
	 * @access	public
	 * @var		array
	 */
    var $attributes = array();

	/**
	 * childNodes of the element
	 *
	 * @access	public
	 * @var		array
	 */
    var $childNodes = array();

	/**
	 * cdata of the element
	 *
	 * @access	public
	 * @var		string
	 */
    var $cdata;

	/**
	 * indicates whether the element is the root element
	 *
	 * @access	public
	 * @var		boolean
	 */
    var $isRoot = false;
	
	/**
	 * stores a reference to the document that created the
	 * element
	 *
	 * @access	private
	 * @var		object CBLDocument
	 */
    var $_doc;

	/**
	 * namespace for CBL elements
	 *
	 * @access 	private
	 * @var		string
	 */
    var $_ns;

	/**
	 * common attributes
	 *
	 * These attributes are supported by all elements
	 *
	 * @access	private
	 * @var		array
	 */	
	var $_commonAttribs = array(
		'id' => array(
			'required' => false,
			'type'     => 'string'
		),
		'class' => array(
			'required' => false,
			'type'     => 'string'		
		),
		'style' => array(
			'required' => false,
			'type'     => 'string'
		)
	);
    
	/**
	 * flag to indicate whether element children should be validated
	 *
	 * @access   private
	 * @var      boolean
	 */
    var $_childValidation;
    
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array   attributes of the element
	 * @param	string  cdata of the element (used by caption, et al)
	 * @param	boolean autobuild flag
	 */
	function CBLElement( $attributes = array(), $cdata = null, $autoBuild = true )
    {
        $this->attributes = $attributes;
        $this->cdata      = $cdata;
    }


	/**
	 * Enable child validation.
	 *
	 * @access   public
	 * @param    boolean
	 */
	function enableChildValidation( $enable = true )
    {
        $this->_childValidation = $enable;
    }
	
	/**
	 * Set the reference to the document.
	 *
	 * @access	public
	 * @param	object CBLDocument     document
	 */
	function setDocument( &$doc )
    {
        $this->_doc = &$doc;
    }

	/**
	 * Set the namespace.
	 *
	 * @access	public
	 * @param	string
	 */
	function setNamespace( $ns )
    {
        $this->_ns = $ns;
    }

	/**
	 * Get the element's id.
	 *
	 * @access	public
	 * @return	string  id of the element
	 */
    function getId()
    {
		if ( isset( $this->attributes['id'] ) )
            return $this->attributes['id'];
        
        return false;
    }

	/**
	 * Get the element's tag name
	 *
	 * @access	public
	 * @return	string  tag name of the element
	 */
	function getElementName()
    {
        return $this->elementName;
    }

	/**
	 * Sets cdata of the element.
	 *
	 * @access 	public
	 * @param	string  data
	 */
    function setCData( $data )
    {
        $this->cdata = $data;
    }

	/**
	 * Sets several attributes at once.
	 *
	 * @access	public
	 * @param	array  attributes
	 */
    function setAttributes( $attribs )
    {
        $this->attributes = array_merge( $this->attributes, $attribs );
    }

	/**
	 * Set an attribute.
	 *
	 * @access	public
	 * @param	string  attribute name
	 * @param	mixed   attribute value
	 */
    function setAttribute( $name, $value )
    {
        $this->attributes[$name] = $value;
    }
    
	/**
	 * Get an attribute.
	 *
	 * @access	public
	 * @param	string  attribute name
	 * @return	mixed   attribute value
	 */
	function getAttribute( $name )
    {
        if ( isset( $this->attributes[$name] ) )
            return $this->attributes[$name];
        
        return false;
    }
    
	/**
	 * Add a child object.
	 *
	 * @access	public
	 * @param	object
	 */
    function appendChild( &$obj )
    {
		if ( $this->_childValidation )
		{
			if ( in_array( $obj->getElementName(), $this->_childElements ) )
			{
		        $this->childNodes[] = &$obj;
				return true;
			}
			else
			{
				return PEAR::raiseError( 'Invalid child element.', CBLELEMENT_ERROR_INVALID_CHILD_ELEMENT );
			}
		}
		else
		{
			$this->childNodes[] = &$obj;
			return true;
		}
    }

	/**
	 * Create a string representation of the element.
	 *
	 * This is just an alias for serialize()
	 *
	 * @access public
	 * @return string string representation of the element and all of its childNodes
	 */
	function toXML()
    {
		return $this->serialize();
    }
    
	/**
	 * Serialize the element.
	 *
	 * @access public
	 * @return string string representation of the element and all of its childNodes
	 */
    function serialize()
	{
		if ( empty( $this->_ns ) )
            $el = $this->elementName;
        else
            $el = sprintf( '%s:%s', $this->_ns, $this->elementName );

        if ( empty( $this->childNodes ) ) 
		{
            if ( $this->cdata !== null )
			{
                $content = $this->cdata;
				
				if ( $this->replaceEntities )
                    $content = XML::replaceEntities( $content );
            }
        }
		else
		{
            $content = '';
            $cnt = count( $this->childNodes );
			
            for ( $i = 0; $i < $cnt; $i++ )
                $content .= $this->childNodes[$i]->serialize();
        }
        
        return XML::createTag(
			$el,
			$this->attributes,
			$content,
			null,
			false
		);
    }
    
	/**
	 * Get an element by its id.
	 *
	 * You should not need to call this method directly
	 *
	 * @access   public
	 * @param    string  id
	 * @return   object CBLElement or false if the element does not exist
	 */
    function &getElementById( $id )
    {
		if ( $this->getId() == $id )
            return $this;
        
        $cnt = count( $this->childNodes );
        
        if ( $cnt == 0 )
            return false;

        for ( $i = 0; $i < $cnt; $i++ )
		{
            $result = &$this->childNodes[$i]->getElementById( $id );
			
            if ( $result === false )
                continue;
            
            return $result;
        }
		
        return false;
    }

	/**
	 * Get a nodelist of elements by their tagname.
	 *
	 * @access   public
	 * @param    string  id
	 * @return   array   array containing CBLElement objects
	 */
    function &getElementsByTagname( $tagname )
    {
        $nodeList = array();
		
        if ( $this->elementName == $tagname )
            $nodeList[] = &$this;

        $cnt = count( $this->childNodes );

        if ( $cnt == 0 )
            return $nodeList;

        for ( $i = 0; $i < $cnt; $i++ )
		{
            $tmp  = &$this->childNodes[$i]->getElementsByTagname( $tagname );
            $cnt2 = count( $tmp );
			
            for ( $j = 0; $j < $cnt2; $j++ )
                $nodeList[] = &$tmp[$j];
        }
		
        return $nodeList;
    }

	/**
	 * Validate the element's attributes.
	 *
	 * Uses the definitions of common attributes as well as the
	 * attribute definitions of the element.
	 *
	 * @access   public
	 * @return   boolean     true on succes, PEAR_Error otherwise
	 */
    function validateAttributes()
    {
		foreach ( $this->attributes as $name => $value )
		{
            if ( isset( $this->_commonAttribs[$name] ) )
                $def = $this->_commonAttribs[$name];
			else if ( isset( $this->_attribDefs[$name] ) )
                $def = $$this->_attribDefs[$name];
			else
                return PEAR::raiseError( 'Unknown attribute ' . $name . '.', CBLELEMENT_ERROR_ATTRIBUTE_UNKNOWN );

			switch ( $def['type'] )
			{
                /*
                 * must be a string
                 */
                case 'string':
                    continue;
                    break;
                
				/*
                 * must be an integer
                 */
                case 'int':
				
                case 'integer':
                    if ( !preg_match( '°^[0-9]+$°', $value ) )
                        return PEAR::raiseError( 'Attribute \'' . $name . '\' must be integer.', CBLELEMENT_ERROR_ATTRIBUTE_NO_INTEGER );
                    
                    break;
					
                /*
                 * enumerated value
                 */
                case 'enum':
                    if ( !in_array( $value, $def['values'] ) )
                        return PEAR::raiseError( 'Attribute \'' . $name . '\' must be one of ' . implode( ', ', $def['values'] ) . '.', CBLELEMENT_ERROR_ATTRIBUTE_INVALID_VALUE );
                    
                    break;
					
                /*
                 * boolean value
                 */
				 
				case 'bool':
				
                case 'boolean':
                    if ( $value != 'true' && $value != 'false' )
                        return PEAR::raiseError( 'Attribute \'' . $name . '\' must be one either \'true\' or \'false\'.', CBLELEMENT_ERROR_ATTRIBUTE_NO_BOOLEAN );
                    
                    break;
            }
        }
		
        return true;
    }

	/**
	 * Get the first child of the element.
	 *
	 * If the element has no childNodes, null will be returned.
	 *
	 * @access   public
	 * @return   object CBLElement
	 */
    function &firstChild()
    {
        if ( isset( $this->childNodes[0] ) )
            return $this->childNodes[0];
        
        $child = null;
        return $child;
    }

	/**
	 * Get last first child of the element.
	 *
	 * If the element has no childNodes, null will be returned.
	 *
	 * @access   public
	 * @return   object CBLElement
	 */
    function &lastChild()
    {
		$cnt = count( $this->childNodes );
		
        if ( $cnt > 0 )
            return $this->childNodes[( $cnt - 1 )];
        
        $child = null;
        return $child;
    }

	/**
	 * Add a description element.
	 *
	 * This can be used by a lot of elements,
	 * thus it has been placed in the base class.
	 *
	 * @access   public
	 * @param    string  text for the description
	 * @param    array   additional attributes
	 * @return   object CBLElement
	 */
    function &addDescription( $text, $atts = array() )
    {
        $desc = &$this->_doc->createElement( 'Description', $atts, $text );
        $this->appendChild( $desc );
		
        return $desc;
    }

	/**
	 * Get a debug info about the element as string.
	 * Use this instead of a print_r on the tree.
	 *
	 * @access   public
	 * @param    integer     nesting depth, no need to pass this
	 * @return   string
	 */
    function getDebug( $indent = '', $last = false )
    {
        $name = $this->getElementName();
        $id   = $this->getId();
		
        if ( $id !== false )
            $name .= " [id=$id]";
        
        if ( $last )
		{
            $debug   = sprintf( "%s   +-%s\n", $indent, $name );
            $indent .= '      ';
        }
		else
		{
            $debug   = sprintf( "%s   +-%s\n", $indent, $name );
            $indent .= '   |  ';
		}
        
		if ( !empty( $this->attributes ) )
		{
            $debug .= sprintf( "%s+-attributes:\n", $indent );
			
            foreach ( $this->attributes as $key => $value )
                $debug .= sprintf("%s|   %s => %s\n", $indent, $key, $value );
        }

        if ( !empty( $this->cdata ) )
            $debug .= sprintf( "%s+-cdata: %s\n", $indent, $this->cdata );
		else
            $debug .= sprintf( "%s+-cdata: null\n", $indent );
        
        if ( !empty( $this->childNodes ) )
		{
            $debug .= sprintf( "%s+-childNodes:\n", $indent );
			
			for ( $i = 0; $i < count( $this->childNodes ); $i++ )
			{
                if ( $i == ( count( $this->childNodes ) - 1 ) )
                    $debug .= $this->childNodes[$i]->getDebug( $indent, true );
                else
                    $debug .= $this->childNodes[$i]->getDebug( $indent );
            }
        }
		
        if ( !$last )
            $debug .= sprintf( "%s\n", $indent );
        
        return $debug;
    }
} // END OF CBLElement

?>
