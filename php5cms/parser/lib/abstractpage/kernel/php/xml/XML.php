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


if ( !defined( 'XML_ENCODING_DEFAULT' ) ) 
	define( 'XML_ENCODING_DEFAULT', 'iso-8859-1' );

if ( !defined( 'XML_DECLARATION' ) )
	define( 'XML_DECLARATION', '<?xml version="1.0" encoding="' . XML_ENCODING_DEFAULT . '" ?>' );

/**
 * error code for invalid chars in XML name
 */
define( "XML_ERROR_INVALID_CHARS", 51 );

/**
 * error code for invalid chars in XML name
 */
define( "XML_ERROR_INVALID_START", 52 );

/**
 * error code for non-scalar tag content
 */
define( "XML_ERROR_NON_SCALAR_CONTENT", 60 );
    
/**
 * replace XML entities
 */
define( "XML_REPLACE_ENTITIES", 1 );

/**
 * embedd content in a CData Section
 */
define( "XML_CDATA_SECTION", 2 );


/**
 * @package xml
 */

class XML extends PEAR
{
	/**
	 * @access public
	 */
    var $version = '1.0';
	
	/**
	 * @access private
	 */
	var $_encoding = 'iso-8859-1';
    
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XML()
	{
	}
	
	
    /**
     * Set encoding.
     *
     * @access  public
     * @param   string e encoding
     */
    function setEncoding( $e ) 
	{
      	$this->_encoding = $e;
    }
    
    /**
     * Retrieve encoding.
     *
     * @access  public
     * @return  string encoding
     */
    function getEncoding()
	{
      	return $this->_encoding;
    }
    
    /**
     * Returns XML declaration.
     *
     * @access  public
     * @return  string declaration
     */
    function getDeclaration()
	{
		return XML::getXMLDeclaration( $this->version, $this->getEncoding() );
    }
	
	
	// helper methods
	
	/**
	 * Generates Xml data based on variable introspection.
	 *
	 * @access	public
	 * @param	mixed	$var
	 * @param	integer	$level
	 * @param	string	$name
	 * @param	boolean	$child
	 * @return	boolean
	 * @static
	 */
	function marshal( $var = null, $level = 0, $name = null, $child = false )
	{
		//automatically process if subclassed
		if ( is_null( $var ) )  
			$xml = $this->marshal( $this ); 
		
		// build tags
		if ( is_null( $name ) ) 
		{
			$open = $close = XML::_processTags( $var );
		} 
		else if ( $child ) 
		{
			$open  = "value key='$name'";
			$tags  = XML::_processTags( $var );
			$close = 'value';
		} 
		else 
		{
			$open = $close = $name;
		}
		
		// build xml
		if ( is_object( $var ) ) 
		{
			$vars  = get_object_vars( $var );
			$xml  .= XML::_tab( $level ) . "<$open>\n";
			
			if ( $child )  
				$xml .= XML::_tab( $level + 1 ) . "<$tags>\n"; 
			
			// process each var in object
			$xml .= XML::_processVar( $var, $level, $child );
			
			if ( $child ) 
				$xml .= XML::_tab( $level + 1 ) . "</$tags>\n"; 
			
			$xml .= XML::_tab( $level ) . "</$close>\n";
		} 
		else if ( is_array( $var ) ) 
		{
			$xml .= XML::_tab( $level ) . "<$open>\n";
			
			if ( $child )
				$xml .= XML::_tab( $level + 1 ) . "<$tags>\n";
				
			// process each var in array
			$xml .= XML::_processVar( $var, $level, $child );
			
			if ( $child )
				$xml .= XML::_tab( $level + 1 ) . "</$tags>\n";
				
			$xml .= XML::_tab( $level ) . "</$close>\n";
		} 
		else if ( is_string( $var ) ) 
		{
			$xml .= XML::_tab( $level );
			$xml .= "<$open>" . XML::replaceEntities( $var ) . "</$close>\n";
		} 
		else if ( is_bool( $var ) ) 
		{
			$xml .= XML::_tab( $level );
			$xml .= "<$open>" . ( ( $var )? 'true' : 'false' ) . "</$close>\n";
		} 
		else if ( is_numeric( $var ) ) 
		{
			$xml .= XML::_tab( $level ) . "<$open>" . strval( $var ) . "</$close>\n";
		}
		
		return $xml;
	}
	
	/**
	 * Transform a document from the supplied Xml data and Xsl stylesheet array
	 * (pipelining) optionally processing any runtime parameters to the
	 * transformer.
	 *
	 * @access	public
	 * @param	string	$xml
	 * @param	array	$xsls
	 * @param	array	$params
	 * @return	string
	 * @static
	 */
	function transform( $xml, $xsls, $params = array() )
	{
		// automatically process if subclassed
		if ( is_null( $xml ) )
			$xml = $this->marshal( $this );
			
		$xslt = xslt_create();
		
		// process each stylesheet in pipeline
		foreach ( $xsls as $xsl ) 
		{
			// utilize previously processed Xml if available
			if ( !is_null( $result ) )
				$xml = $result;
				
			// force the processor to accept an Xml string instead of a file
			$result = xslt_process( $xslt, 'arg:/_xml', $xsl, null, array( '/_xml' => $xml ), $params );
		}
		
		xslt_free( $xslt );
		return $result;
	}

	/**
	 * @access  public
     * @static
	 */
	function getChildren( $vals, &$i ) 
	{
    	$children = array();
    
		if ( isset( $vals[$i]['value'] ) ) 
			$children[] = $vals[$i]['value'];

   	 	while ( ++$i < count( $vals ) ) 
		{
        	switch ( $vals[$i]['type'] ) 
			{
        		case 'cdata':
            		$children[] = $vals[$i]['value'];
            		break;

        		case 'complete':
            		$children[] = array(
                		'tag'        => $vals[$i]['tag'],
                		'attributes' => isset( $vals[$i]['attributes'] )? $vals[$i]['attributes'] : null,
                		'value'      => isset( $vals[$i]['value'] )? $vals[$i]['value'] : null
            		);
            
					break;

        		case 'open':
            		$children[] = array(
                		'tag'        => $vals[$i]['tag'],
                		'attributes' => isset( $vals[$i]['attributes'] )? $vals[$i]['attributes'] : null,
                		'children'   => XML::getChildren( $vals, $i )
            		);
            
					break;

        		case 'close':
            		return $children;
        	}
    	}
	}
	
	/**
	 * @access  public
     * @static
	 */
	function condenseTree( $tree ) 
	{
    	foreach ( $tree['children'] as $index => $node ) 
		{
        	if ( isset($node['children'] ) ) 
			{
            	$tree['children'][$index] = XML::condenseTree( $node );
        	} 
			else if ( isset( $node['value'] ) && !$node['attributes'] ) 
			{
            	$tree['values'][$node['tag']] = $node['value'];
            	unset( $tree['children'][$index] );
        	}
    	}
     
    	if ( !$tree['children'] ) 
			unset( $tree['children'] );
		
   	 	return $tree;
	}
	
	/**
	 * @param	ressource	$file Path to XML file
	 * @access  public
     * @static
	 * @return	array
	 */
	function getTree( $file ) 
	{
    	// search for XML file in include path somewhere
    	$data = join( '', file( $file, 1 ) );

    	$parser = xml_parser_create();
    	xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
    	xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE,   1 );
    	xml_parse_into_struct( $parser, $data, $vals, $index );
    	xml_parser_free( $parser );

    	return array(
        	'tag'        => $vals[0]['tag'],
        	'attributes' => isset( $vals[0]['attributes'] )? $vals[0]['attributes'] : null,
        	'children'   => XML::getChildren( $vals, $i = 0 )
    	);
	}
	
	/**
	 * @access  public
     * @static
	 */
	function format( $xml )
	{
		// escapes backslash brackets
		$xml = str_replace( "\n",       "", $xml );
		$xml = str_replace( "\r",       "", $xml );
		$xml = str_replace( "\\>",  "&GT;", $xml );
		$xml = str_replace( "\\<",  "&LT;", $xml );
	
		$out = preg_replace_callback( "|\<[^\>]*\>[^\<]*|", "_xmlFormatElement", $xml );
    	$out = str_replace( '&GT;',    '>', $out );
    	$out = str_replace( '&LT;',    '<', $out );
    	$out = str_replace( '<',    '&lt;', $out );
    	$out = str_replace( '>',    '&gt;', $out );

		return nl2br( $out );
	}
	
	/**
	 * Converts all applicable characters to xml entities. This is similar to
	 * the htmlentities() php function.
	 *
	 * @access	public
	 * @param	string	$xml
	 * @param	boolean	$utf8
	 * @return	string
	 */
	function replaceEntities( $xml, $utf8 = false )
	{
		// http://www.w3.org/TR/1998/REC-xml-19980210#sec-predefined-ent
		$entities = array(
			'<'  => '&lt;',
			'>'  => '&gt;',
			'`'  => '&apos;',
			'\"' => '&quot;',
			'&'  => '&amp;',
		);
		
		if ( is_string( $xml ) ) 
		{
			if ( $utf8 )
				$xml = utf8_encode( $xml );
				
			$xml = strtr( $xml, $entities );
		}
		
		return $xml;
	}
	
	/**
 	 * Convert an 2D-Array into XML.
 	 *
 	 * Parameters:
 	 * @param	string		$tag the XML-tag
 	 * @param	array		$content the content should be inserted
 	 * @param	array		$first attributes for the parent tag
	 * @access  public
     * @static
	 * @return 	string
 	 */
	function arrayToXML( $tag, $content, $first = array() )
	{
		$result  = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n\n";
		$result	.= "<" .  $tag; 

		if ( is_array( $first ) )
		{
			foreach ( $first as $name => $value )
				$result	.=	' ' . $name . '="' . $value . '"';
		}
			
		$result .= ">\n";
	
		for ( $i = 0; $i < count( $content ); $i++ )
		{
			$result	.=	"<" . $tag . "_entry no=\"$i\" ";
						
			while ( list( $key, $value ) = each( $content[$i] ) )
				$result .= "$key=\"$value\" ";
						
			$result	.= "/>\n";
		}
	
		$result	.=	"</" . $tag . ">\n";
		return $result;
	}
	
	/**
 	 * Put an entire XML in a tree form. 
 	 * Each node has the following structure: 
 	 *      /->  tag 
 	 * node +--> attributes 
 	 *      \->  children 
	 *
	 * @param	ressource	$file Path to XML file
	 * @access  public
     * @static
	 * @return	array
 	 */
	function xmlToArray( $file ) 
	{
		$data = implode( '', file( $file ) ); 
		$p = xml_parser_create();
	
		xml_parser_set_option( $p, XML_OPTION_SKIP_WHITE, 1 ); 
		xml_parse_into_struct( $p, $data, &$vals, &$index ); 
		xml_parser_free( $p ); 

	   	$tree = array(); 
   		$i = 0; 
  
  		array_push( $tree, array( 
			'tag'        => $vals[$i]['tag'], 
			'attributes' => $vals[$i]['attributes'], 
			'children'   => XML::_getXMLChildren( $vals, $i ) 
		) );
	 
		return $tree; 
	}

  	function translateValue( $value )
	{
    	// switch on
		if ( eregi( '^yes$',  $value ) || 
    	     eregi( '^on$',   $value ) ||
    	     eregi( '^true$', $value ) ) 
    	{
        	return true;
      	}
    
		// switch off
    	if ( eregi( '^no$',    $value ) ||
        	 eregi( '^off$',   $value ) ||
        	 eregi( '^false$', $value ) ) 
      	{
      		return false;
      	}
    
		return $value;
  	}
	
    /**
     * Build an xml declaration.
     *
     * <code>
     * // get an XML declaration:
     * $xmlDecl = XML::getXMLDeclaration( "1.0", "UTF-8", true );
     * </code>
     *
     * @access   public
     * @static
     * @param    string  $version     xml version
     * @param    string  $encoding    character encoding
     * @param    boolean $standAlone  document is standalone (or not)
     * @return   string  $decl xml declaration
     * @uses     XML::attributesToString() to serialize the attributes of the XML declaration
     */
    function getXMLDeclaration( $version = "1.0", $encoding = null, $standalone = null )
    {
        $attributes = array(
			"version" => $version,
		);
		
        // add encoding
        if ( $encoding !== null )
            $attributes["encoding"] = $encoding;
        
        // add standalone, if specified
        if ( $standalone !== null )
            $attributes["standalone"] = $standalone? "yes" : "no";
        
        return sprintf( "<?xml%s?>", XML::attributesToString( $attributes, false ) );
    }

    /**
     * Build a document type declaration.
     *
     * <code>
     * // get a doctype declaration:
     * $xmlDecl = XML::getDocTypeDeclaration("rootTag","myDocType.dtd");
     * </code>
     *
     * @access   public
     * @static
     * @param    string  $root         name of the root tag
     * @param    string  $uri          uri of the doctype definition (or array with uri and public id)
     * @param    string  $internalDtd  internal dtd entries   
     * @return   string  $decl         doctype declaration
     */
    function getDocTypeDeclaration( $root, $uri = null, $internalDtd = null )
    {
        if ( is_array( $uri ) )
            $ref = sprintf( ' PUBLIC "%s" "%s"', $uri["id"], $uri["uri"] );
        else if ( !empty( $uri ) )
            $ref = sprintf( ' SYSTEM "%s"', $uri );
        else
            $ref = "";

        if ( empty( $internalDtd ) )
            return sprintf( "<!DOCTYPE %s%s>", $root, $ref );
        else
            return sprintf( "<!DOCTYPE %s%s [\n%s\n]>", $root, $ref, $internalDtd );
    }

    /**
     * Create string representation of an attribute list.
     *
     * <code>
     * // build an attribute string
     * $att = array(
     *		"foo"   =>  "bar",
     *		"argh"  =>  "tomato"
     * );
     *
     * $attList = XML::attributesToString($att);    
     * </code>
     *
     * @access   public
     * @static
     * @param    array   $attributes  attribute array
     * @param    boolean $sort        sort attribute list alphabetically
     * @return   string  $string      string representation of the attributes
     */
    function attributesToString( $attributes, $sort = true )
    {
		$string = "";
		
		if ( is_array( $attributes ) && !empty( $attributes ) ) 
		{
            if ( $sort )
                ksort( $attributes );
            
            foreach ( $attributes as $key => $value )
                $string .= " " . $key . '="' . XML::replaceEntities( $value ) . '"';
        }
		
        return $string;
    }

    /**
     * Create a tag.
     *
     * This method will call XML::createTagFromArray(), which
     * is more flexible.
     *
     * <code>
     * // create an XML tag:
     * $tag = XML::createTag("myNs:myTag", array("foo" => "bar"), "This is inside the tag", "http://www.w3c.org/myNs#");
     * </code>
     *
     * @access   public
     * @static
     * @param    string  $qname             qualified tagname (including namespace)
     * @param    array   $attributes        array containg attributes
     * @param    mixed   $content
     * @param    string  $namespaceUri      URI of the namespace
     * @param    integer $replaceEntities   whether to replace XML special chars in content, embedd it in a CData section or none of both
     * @return   string  $string            XML tag
     * @see      XML::createTagFromArray()
     * @uses     XML::createTagFromArray() to create the tag
     */
    function createTag( $qname, $attributes = array(), $content = null, $namespaceUri = null, $replaceEntities = XML_REPLACE_ENTITIES )
    {
        $tag = array(
			"qname"      => $qname,
			"attributes" => $attributes
		);

        // add tag content
        if ( $content !== null )
            $tag["content"] = $content;
        
        // add namespace Uri
        if ( $namespaceUri !== null )
            $tag["namespaceUri"] = $namespaceUri;

        return XML::createTagFromArray( $tag, $replaceEntities );
    }

    /**
     * Create a tag from an array.
	 *
     * This method awaits an array in the following format
     * <pre>
     * array(
     *  "qname"        => $qname         // qualified name of the tag
     *  "namespace"    => $namespace     // namespace prefix (optional, if qname is specified or no namespace)
     *  "localpart"    => $localpart,    // local part of the tagname (optional, if qname is specified)
     *  "attributes"   => array(),       // array containing all attributes (optional)
     *  "content"      => $content,      // tag content (optional)
     *  "namespaceUri" => $namespaceUri  // namespaceUri for the given namespace (optional)
     *   )
     * </pre>
     *
     * <code>
     * $tag = array(
     *		"qname"        => "foo:bar",
     *		"namespaceUri" => "http://foo.com",
     *		"attributes"   => array( "key" => "value", "argh" => "fruit&vegetable" ),
     *		"content"      => "I'm inside the tag",
     * );
	 *
     * // creating a tag with qualified name and namespaceUri
     * $string = XML::createTagFromArray( $tag );
     * </code>
     *
     * @access   public
     * @static
     * @param    array   $tag               tag definition
     * @param    integer $replaceEntities   whether to replace XML special chars in content, embedd it in a CData section or none of both
     * @return   string  $string            XML tag
     * @see      XML::createTag()
     * @uses     XML::attributesToString() to serialize the attributes of the tag
     * @uses     XML::splitQualifiedName() to get local part and namespace of a qualified name
     */
    function createTagFromArray( $tag, $replaceEntities = XML_REPLACE_ENTITIES )
    {
        if ( isset( $tag["content"]) && !is_scalar( $tag["content"] ) )
            return PEAR::raiseError( "Supplied non-scalar value as tag content.", XML_ERROR_NON_SCALAR_CONTENT );

        // if no attributes hav been set, use empty attributes
        if ( !isset( $tag["attributes"] ) || !is_array( $tag["attributes"] ) )
            $tag["attributes"] = array();
        
        // qualified name is not given
        if ( !isset( $tag["qname"] ) ) 
		{
            // check for namespace
            if ( isset( $tag["namespace"] ) && !empty( $tag["namespace"] ) )
                $tag["qname"] = $tag["namespace"].":".$tag["localPart"];
            else
                $tag["qname"] = $tag["localPart"];
        } 
		// namespace URI is set, but no namespace
		else if ( isset( $tag["namespaceUri"] ) && !isset( $tag["namespace"] ) ) 
		{
            $parts = XML::splitQualifiedName( $tag["qname"] );
            $tag["localPart"] = $parts["localPart"];
			
            if ( isset( $parts["namespace"] ) )
                $tag["namespace"] = $parts["namespace"];
        }

        if ( isset( $tag["namespaceUri"]) && !empty( $tag["namespaceUri"] ) ) 
		{
            // is a namespace given
            if ( isset( $tag["namespace"] ) && !empty( $tag["namespace"] ) ) 
			{
                $tag["attributes"]["xmlns:" . $tag["namespace"]] = $tag["namespaceUri"];
            } 
			else 
			{
                // define this Uri as the default namespace
                $tag["attributes"]["xmlns"] = $tag["namespaceUri"];
            }
        }

        // create attribute list
        $attList = XML::attributesToString( $tag["attributes"] );
		
        if ( !isset( $tag["content"] ) || (string)$tag["content"] == '' ) 
		{
            $tag = sprintf( "<%s%s/>", $tag["qname"], $attList );
        } 
		else 
		{
            if ( $replaceEntities == XML_REPLACE_ENTITIES ) 
			{
                $tag["content"] = XML::replaceEntities( $tag["content"] );
            } 
			else if ( $replaceEntities == XML_CDATA_SECTION ) 
			{
				$tag["content"] = XML::createCDataSection( $tag["content"] );
			}
			
            $tag = sprintf( "<%s%s>%s</%s>", $tag["qname"], $attList, $tag["content"], $tag["qname"] );
        }
		
        return  $tag;
    }

    /**
     * Create a start element.
     *
     * <code>
     * // create an XML start element:
     * $tag = XML::createStartElement( "myNs:myTag", array( "foo" => "bar" ) ,"http://www.w3c.org/myNs#" );
     * </code>
     *
     * @access   public
     * @static
     * @param    string  $qname             qualified tagname (including namespace)
     * @param    array   $attributes        array containg attributes
     * @param    string  $namespaceUri      URI of the namespace
     * @return   string  $string            XML start element
     * @see      XML::createEndElement(), XML::createTag()
     */
    function createStartElement( $qname, $attributes = array(), $namespaceUri = null )
    {
        // if no attributes hav been set, use empty attributes
        if ( !isset( $attributes ) || !is_array( $attributes ) )
            $attributes = array();
        
        if ( $namespaceUri != null )
            $parts = XML::splitQualifiedName( $qname );

        if ( $namespaceUri != null ) 
		{
            // is a namespace given
            if ( isset( $parts["namespace"] ) && !empty( $parts["namespace"] ) ) 
			{
                $attributes["xmlns:" . $parts["namespace"]] = $namespaceUri;
            } 
			else 
			{
                // define this Uri as the default namespace
                $attributes["xmlns"] = $namespaceUri;
            }
        }

        // create attribute list
        $attList = XML::attributesToString( $attributes );
        $element = sprintf( "<%s%s>", $qname, $attList );

        return  $element;
    }

    /**
     * Create an end element.
     *
     * <code>
     * // create an XML start element:
     * $tag = XML::createEndElement("myNs:myTag");
     * </code>
     *
     * @access   public
     * @static
     * @param    string  $qname             qualified tagname (including namespace)
     * @return   string  $string            XML end element
     * @see      XML::createStartElement(), XML::createTag()
     */
    function createEndElement( $qname )
    {
        $element = sprintf( "</%s>", $qname );
        return $element;
    }
    
    /**
     * Create a CData section.
     *
     * <code>
     * // create a CData section
     * $tag = XML::createCDataSection( "I am content." );
     * </code>
     *
     * @access   public
     * @static
     * @param    string  $data              data of the CData section
     * @return   string  $string            CData section with content
     */
    function createCDataSection( $data )
    {
        return  sprintf( "<![CDATA[%s]]>", $data );
    }

	/**
	 * Strip CDATA surrounding.
	 *
	 * @param  string  $content
	 * @return string
	 * @access public
	 * @static
	 */
	function stripCData( $content )
	{
		$content = preg_replace( "/<!\[CDATA\[/s", "", $content );
		$content = preg_replace( "/\]\]>/s",       "", $content );

		return $content;
	}
	
    /**
     * Split qualified name and return namespace and local part.
     *
     * <code>
     * // split qualified tag
     * $parts = XML::splitQualifiedName("xslt:stylesheet");
     * </code>
     * the returned array will contain two elements:
     * <pre>
     * array(
     *		"namespace" => "xslt",
     *		"localPart" => "stylesheet"
     * );
     * </pre>
     *
     * @access public
     * @static
     * @param  string    $qname  qualified tag name
     * @return array     $parts  array containing namespace and local part
     */
    function splitQualifiedName( $qname )
    {
		if ( strstr( $qname, ':' ) ) 
		{
            $tmp = explode( ":", $qname );
			
            return array(
				"namespace" => $tmp[0],
				"localPart" => $tmp[1]
			);
        }
		
        return array(
			"namespace" => null,
			"localPart" => $qname
		);
    }

    /**
     * Check, whether string is valid XML name.
     *
     * <p>XML names are used for tagname, attribute names and various
     * other, lesser known entities.</p>
     * <p>An XML name may only consist of alphanumeric characters,
     * dashes, undescores and periods, and has to start with a letter
     * or an underscore.
     * </p>
     *
     * <code>
     * // verify tag name
     * $result = XML::isValidName( "invalidTag?" );
	 *
     * if ( PEAR::isError( $result ) )
     *    print "Invalid XML name: " . $result->getMessage();
     * </code>
     *
     * @access  public
     * @static
     * @param   string  $string string that should be checked
     * @return  mixed   $valid  true, if string is a valid XML name, Error otherwise
     * @todo    support for other charsets
     */
    function isValidName( $string )
    {
        // check for invalid chars
        if ( !preg_match( "/^[[:alnum:]_\-.]+$/", $string ) )
            return PEAR::raiseError( "XML name may only contain alphanumeric chars, period, hyphen and underscore", XML_ERROR_INVALID_CHARS );

        //  check for invalid starting character
        if ( !preg_match( "/[[:alpha:]_]/", $string{0} ) )
            return PEAR::raiseError( "XML name may only start with letter or underscore", XML_ERROR_INVALID_START );

        // XML name is valid
        return true;
    }
	
	
	// private methods
	
	/**
	 * @access  private
     * @static	
	 * @return 	array
	 */
	function _getXMLChildren( $vals, &$i ) 
	{	 
		$children = array(); 
	
		if ( $vals[$i]['value'] ) 
			array_push( $children, $vals[$i]['value'] ); 

		while ( ++$i < count( $vals ) )
		{ 
			switch ( $vals[$i]['type'] ) 
			{ 
				case 'cdata': 
					array_push( $children, $vals[$i]['value'] ); 
					break; 

				case 'complete': 
					array_push( $children, array( 'tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'] ) ); 
					break; 

				case 'open': 
					array_push( $children, array( 'tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'], 'children' => XML::_getXMLChildren( $vals, $i ) ) ); 
          			break; 

       			case 'close': 
          			return $children; 
     		} 
  		} 
	}

	/**
	 * Utility method used to process the var and return the correct tags.
	 *
	 * @access	private
	 * @param	mixed	$var
	 * @return	string
	 */
	function _processTags( $var )
	{
		if ( is_object( $var ) )
			$tags = get_class( $var );
		else if ( is_array( $var ) )
			$tags = 'array';
		else
			$tags = 'value';
		
		return $tags;
	}
	
	/**
	 * Utility method used to process the var and return the correct Xml.
	 *
	 * @access	private
	 * @param	mixed	$var
	 * @param	integer	$level
	 * @param	boolean	$child
	 * @return	string
	 */
	function _processVar( $var, $level, $child )
	{
		foreach ( $var as $key => $value ) 
		{
			if ( !is_null( $value ) ) 
			{
				$tabs  = ( $child )? $level + 2 : $level + 1;
				$xml  .= XML::marshal( $value, $tabs, $key, ( is_array( $var ) ) );
			}
		}
		
		return $xml;
	}
	
	/**
	 * Utility method used to tab Xml data to a specified level.
	 *
	 * @access	private
	 * @param	integer	$level
	 * @return	string
	 */
	function _tab( $level )
	{
		$tabs = null;
		
		for ( $i = 0; $i < $level; $i++ )
			$tabs.= "\t";
		
		return $tabs;
	}
} // END OF XML

?>
