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


using( 'xml.minixml.MiniXMLElement' );
using( 'xml.minixml.MiniXMLElementCData' );
using( 'xml.minixml.MiniXMLElementDocType' );
using( 'xml.minixml.MiniXMLElementEntity' );


// Set to 1 to use case sensitive element name comparisons.
define( "MINIXML_CASESENSITIVE", 0 );

// Set to 1 to autoescape stuff like > and < and & in text, 0 to turn it off.
define( "MINIXML_AUTOESCAPE_ENTITIES", 1 );

// Set to 1 to automatically register parents elements with children.
define( "MINIXML_AUTOSETPARENT", 0 );

// Set to 1 to set the default behavior of 'avoidLoops' to ON, 0 otherwise.
define( "MINIXML_AVOIDLOOPS", 0 );

// Set to 1 to eliminate leading and trailing whitespaces from strings.
define( "MINIXML_IGNOREWHITESPACES", 1 );

// Lower/upper case attribute names.  Choose UPPER or LOWER or neither - not both... UPPER takes precedence.
define( "MINIXML_UPPERCASEATTRIBUTES", 0 ); // Set to 1 to UPPERCASE all attributes, 0 otherwise.
define( "MINIXML_LOWERCASEATTRIBUTES", 0 ); // Set to 1 to lowercase all attributes, 0 otherwise.

/**
 * If you are using lots of $xmlDoc->fromFile('path/to/file.xml') calls, it is possible to use
 * a caching mechanism.  This cache will read the file, store a serialized version of the resulting
 * object and read in the serialize object on subsequent calls.
 *
 * If the original XML file is updated, the cache will automatically be refreshed.
 *
 * To use caching, set MINIXML_USEFROMFILECACHING to 1 and set the
 * MINIXML_FROMFILECACHEDIR to a suitable directory in which the cache files will 
 * be stored (eg, "/tmp")
 */
define( "MINIXML_USEFROMFILECACHING", 0 );
define( "MINIXML_FROMFILECACHEDIR",   "/tmp" );

define( "MINIXML_USE_SIMPLE", 0 );

// Flag that may be passed to the toString() methods.
define( "MINIXML_NOWHITESPACES", -999 );

define( "MINIXML_COMPLETE_REGEX", '/<\s*([^\s>]+)([^>]+)?>(.*?)<\s*\/\\1\s*>\s*([^<]+)?(.*)|\s*<!--(.+?)-->\s*|^\s*<\s*([^\s>]+)([^>]*)\/\s*>\s*([^<>]+)?|<!\[CDATA\s*\[(.*?)\]\]\s*>|<!DOCTYPE\s*([^\[]*)\[(.*?)\]\s*>|<!ENTITY\s*([^"\'>]+)\s*(["\'])([^\14]+)\14\s*>|^([^<]+)(.*)/smi' );
define( "MINIXML_SIMPLE_REGEX",   '/\s*<\s*([^\s>]+)([^>]+)?>(.*?)<\s*\/\\1\s*>\s*([^<]+)?(.*)|\s*<!--(.+?)-->\s*|\s*<\s*([^\s>]+)([^>]*)\/\s*>\s*([^<>]+)?|^([^<]+)(.*)/smi' );


/**
 * MiniXMLDoc class
 *
 * The MiniXMLDoc class is the programmer's handle to MiniXML functionality.
 *
 * A MiniXMLDoc instance is created in every program that uses MiniXML.
 * With the MiniXMLDoc object, you can access the root MiniXMLElement, 
 * find/fetch/create elements and read in or output XML strings.
 *
 * @package xml_minixml
 */
 
class MiniXMLDoc extends PEAR
{
	/**
	 * @access public
	 */
	var $xxmlDoc;
	
	/**
	 * @access public
	 */
	var $xuseSimpleRegex;
	
	/**
	 * @access public
	 */
	var $xRegexIndex;
	
	
	/**
	 * Constructor
	 *
	 * If the optional XMLSTRING is passed, the document will be initialised with
	 * a call to fromString using the XMLSTRING.
	 *
	 * @access public
	 */
	function MiniXMLDoc( $string = null )
	{
		/*
		 * Set up the root element - note that it's name get's translated to a
		 * <? xml version="1.0" ?> string.
		 */
		$this->xxmlDoc = new MiniXMLElement( "ROOT_ELEMENT" );
		$this->xuseSimpleRegex = MINIXML_USE_SIMPLE;
		
		if ( !is_null( $string ) )
			$this->fromString( $string );
	}
	

	/**
	 * @access public
	 */	
	function init()
	{
		$this->xxmlDoc = new MiniXMLElement( "ROOT_ELEMENT" );
	}
	
	/**
	 * Returns a reference the this document's root element
	 * (an instance of MiniXMLElement).
	 *
	 * @access public
	 */
	function &getRoot()
	{
		return $this->xxmlDoc;
	}
	
	/**
	 * Set the document root to the NEWROOT MiniXMLElement object.
	 *
	 * @access public
	 */
	function setRoot( &$root )
	{
		if ( $this->isElement( $root ) )
		{
			$this->xxmlDoc = $root;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns a true value if ELEMENT is an instance of MiniXMLElement,
	 * false otherwise.
	 *
	 * @access public
	 */
	function isElement( &$testme )
	{
		if ( is_null( $testme ) )
			return false;
		
		return method_exists( $testme, 'MiniXMLElement' );
	}
	
	/**
	 * Returns a true value if NODE is an instance of MiniXMLNode,
	 * false otherwise.
	 *
	 * @access public
	 */
	function isNode( &$testme )
	{
		if ( is_null( $testme ) )
			return false;
		
		return method_exists( $testme, 'MiniXMLNode' );
	}
	
	/**
	 * Creates a new MiniXMLElement with name NAME.
	 * This element is an orphan (has no assigned parent)
	 * and will be lost unless it is appended (MiniXMLElement::appendChild())
	 * to an element at some point.
	 *
	 * If the optional VALUE (string or numeric) parameter is passed,
	 * the new element's text/numeric content will be set using VALUE.
	 *
	 * Returns a reference to the newly created element (use the =& operator)
	 *
	 * @access public
	 */
	function &createElement( $name = null, $value = null )
	{
		$newElement = new MiniXMLElement( $name );
		
		if ( !is_null( $value ) )
		{
			if ( is_numeric( $value ) )
				$newElement->numeric( $value );
			else if ( is_string( $value ) )
				$newElement->text( $value );
		}
		
		return $newElement;
	}
	
	/**
	 * Searches the document for an element with name NAME.
	 *
	 * Returns a reference to the first MiniXMLElement with name NAME,
	 * if found, null otherwise.
	 *
	 * NOTE: The search is performed like this, returning the first 
	 * 	 element that matches:
	 *
	 * - Check the Root Element's immediate children (in order) for a match.
	 * - Ask each immediate child (in order) to MiniXMLElement::getElement()
	 *  (each child will then proceed similarly, checking all it's immediate
	 *   children in order and then asking them to getElement())
	 *
	 * @access public
	 */
	function &getElement( $name )
	{	
		$element = $this->xxmlDoc->getElement( $name );
		return $element;
	}
	
	/**
	 * Attempts to return a reference to the (first) element at PATH
	 * where PATH is the path in the structure from the root element to
	 * the requested element.
	 *
	 * For example, in the document represented by:
	 *
	 *	 <partRateRequest>
	 *	  <vendor>
	 *	   <accessid user="myusername" password="mypassword" />
	 *	  </vendor>
	 *	  <partList>
	 *	   <partNum>
	 *	    DA42
	 *	   </partNum>
	 *	   <partNum>
	 *	    D99983FFF
	 *	   </partNum>
	 *	   <partNum>
	 *	    ss-839uent
	 *	   </partNum>
	 *	  </partList>
	 *	 </partRateRequest>
	 *
	 * 	$accessid =& $xmlDocument->getElementByPath('partRateRequest/vendor/accessid');
	 *
	 * Will return what you expect (the accessid element with attributes user = "myusername"
	 * and password = "mypassword").
	 *
	 * BUT be careful:
	 *	$accessid =& $xmlDocument->getElementByPath('partRateRequest/partList/partNum');
	 *
	 * will return the partNum element with the value "DA42".  Other partNums are 
	 * inaccessible by getElementByPath() - Use MiniXMLElement::getAllChildren() instead.
	 *
	 * Returns the MiniXMLElement reference if found, null otherwise.
	 *
	 * @access public
	 */
	function &getElementByPath( $path )
	{
		$element = $this->xxmlDoc->getElementByPath( $path );
		return $element;
	}

	/**
	 * @access public
	 */
	function fromFile( $filename )
	{
		$modified = stat( $filename );
		
		if ( !is_array( $modified ) )
			return null;
		
		if ( MINIXML_USEFROMFILECACHING > 0 )
		{
			$tmpName = MINIXML_FROMFILECACHEDIR . '/' . 'minixml-' . md5( $filename );
			$cacheFileStat = stat( $tmpName );
			
			if ( is_array( $cacheFileStat ) && $cacheFileStat[9] > $modified[9] )
			{
				$fp = @fopen( $tmpName, "r" );

				if ( $fp )
				{
					$tmpFileSize     =  filesize( $tmpName );
					$tmpFileContents =  fread( $fp, $tmpFileSize );
					$serializedObj   =  unserialize( $tmpFileContents );
					$sRoot           =& $serializedObj->getRoot();

					if ( $sRoot )
					{
						$this->setRoot( $sRoot );
						
						// Return immediately, such that we don't refresh the cache.
						return $this->xxmlDoc->numChildren();
					}
				}
			}
		}
		
		ob_start();
		readfile( $filename );
		$filecontents = ob_get_contents();
		ob_end_clean();
		$retVal = $this->fromString( $filecontents );
		
		if ( MINIXML_USEFROMFILECACHING > 0 )
			$this->saveToCache( $filename );
		
		return $retVal;
	}
	
	/**
	 * @access public
	 */
	function saveToCache( $filename )
	{
		$tmpName = MINIXML_FROMFILECACHEDIR . '/' . 'minixml-' . md5( $filename );
		$fp      = @fopen( $tmpName, "w" );
		
		if ( $fp )
		{
			$serialized = serialize( $this );
			fwrite( $fp, $serialized );
			fclose( $fp );
			
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	/** 
	 * Initialise the MiniXMLDoc (and it's root MiniXMLElement) using the 
	 * XML string XMLSTRING.
	 *
	 * Returns the number of immediate children the root MiniXMLElement now
	 * has.
	 *
	 * @access public
	 */
	function fromString( &$XMLString )
	{
		$useSimpleFlag = $this->xuseSimpleRegex;
		
		if ( $this->xuseSimpleRegex || ! preg_match( '/<!DOCTYPE|<!ENTITY|<!\[CDATA/smi', $XMLString ) )
		{
			$this->xuseSimpleRegex = 1;
			
			$this->xRegexIndex = array(
				'biname'		=> 1,
				'biattr'		=> 2,
				'biencl'		=> 3,
				'biendtxt'		=> 4,
				'birest'		=> 5,
				'comment'		=> 6,
				'uname'			=> 7,
				'uattr'			=> 8,
				'uendtxt'		=> 9,
				'plaintxt'		=> 10,
				'plainrest'		=> 11
			);
			
			$regex = MINIXML_SIMPLE_REGEX;
		} 
		else 
		{
			$this->xRegexIndex = array(
				'biname'		=> 1,
				'biattr'		=> 2,
				'biencl'		=> 3,
				'biendtxt'		=> 4,
				'birest'		=> 5,
				'comment'		=> 6,
				'uname'			=> 7,
				'uattr'			=> 8,
				'uendtxt'		=> 9,
				'cdata'			=> 10,
				'doctypedef'	=> 11,
				'doctypecont'	=> 12,
				'entityname'	=> 13,
				'entitydef'		=> 15,
				'plaintxt'		=> 16,
				'plainrest'		=> 17
			);
			
			$regex = MINIXML_COMPLETE_REGEX;
		}
						
		$this->fromSubString( $this->xxmlDoc, $XMLString, $regex );
		$this->xuseSimpleRegex = $useSimpleFlag;
		
		return $this->xxmlDoc->numChildren();		
	}
	
	/**
	 * @access public
	 */
	function fromArray( &$init, $params = null )
	{
		$this->init();
		
		if ( !is_array( $init ) )
			return null;
		
		if ( !is_array( $params ) )
			$params = array();
		
		if ( $params["attributes"] && is_array( $params["attributes"] ) )
		{
			$attribs = array();
			
			foreach ( $params["attributes"] as $attribName => $value )
			{
				if ( !is_array( $attribs[$attribName] ) )
					$attribs[$attribName] = array();
				
				if ( is_array( $value ) )
				{
					foreach ( $value as $v )
						$attribs[$attribName][$v]++;
				} 
				else 
				{
					$attribs[$attribName][$value]++;
				}
			}
			
			// completely replace old attributes by our optimized array
			$params["attributes"] = $attribs;
		} 
		else 
		{
			$params["attributes"] = array();
		}
		
		foreach ( $init as $keyname => $value )
		{
			$sub = $this->_fromArray_getExtractSub( $value );
			$this->$sub( $keyname, $value, $this->xxmlDoc, $params );		
		}
		
		return $this->xxmlDoc->numChildren();		
	}

	/**
	 * @access public
	 */
	function time( $msg )
	{
		error_log( "\nMiniXML msg '$msg', time: ". time() . "\n" );
	}

	/**
	 * @access public
	 */
	function fromSubString( &$parentElement, &$XMLString, &$regex )
	{
		if ( is_null( $parentElement ) || preg_match( '/^\s*$/', $XMLString ) )
			return;

		$matches = array();
		
		if ( preg_match_all( $regex, $XMLString, $matches ) )
		{
			$mcp = $matches;
			$numMatches = count( $mcp[0] );

			for ( $i = 0; $i < $numMatches; $i++ )
			{
				$uname   = $mcp[$this->xRegexIndex['uname']][$i];
				$comment = $mcp[$this->xRegexIndex['comment']][$i];
				
				if ( $this->xuseSimpleRegex )
				{
					$cdata       = null;
					$doctypecont = null;
					$entityname  = null;
				} 
				else 
				{
					$cdata       = $mcp[$this->xRegexIndex['cdata']][$i];
					$doctypecont = $mcp[$this->xRegexIndex['doctypecont']][$i];
					$entityname  = $mcp[$this->xRegexIndex['entityname']][$i];
				}
				
				$plaintext = $mcp[$this->xRegexIndex['plaintxt']][$i];
				
				if ( $uname )
				{
					$ufinaltxt  =  $mcp[$this->xRegexIndex['uendtxt']][$i];
					$newElement =& $parentElement->createChild( $uname );
					$this->_extractAttributesFromString( $newElement, $mcp[$this->xRegexIndex['uattr']][$i] );
					
					if ( $ufinaltxt )
						$parentElement->createNode( $ufinaltxt );
				} 
				else if ( $comment ) 
				{
					$parentElement->comment( $comment );
				} 
				else if ( $cdata ) 
				{
					$newElement = new MiniXMLElementCData( $cdata );
					$parentElement->appendChild( $newElement );
				} 
				else if ( $doctypecont ) 
				{
					$newElement = new MiniXMLElementDocType( $mcp[$this->xRegexIndex['doctypedef']][$i] );
					$appendedChild =& $parentElement->appendChild( $newElement );
					$this->fromSubString( $appendedChild, $doctypecont, $regex );
				} 
				else if ( $entityname ) 
				{
					$newElement = new MiniXMLElementEntity( $entityname, $mcp[$this->xRegexIndex['entitydef']][$i] );
					$parentElement->appendChild( $newElement );
				} 
				else if ( $plaintext ) 
				{
					$afterTxt = $mcp[$this->xRegexIndex['plainrest']][$i];
					
					if ( !preg_match( '/^\s+$/', $plaintext ) )
						$parentElement->createNode( $plaintext );
					
					if ( $afterTxt && !preg_match( '/^\s*$/', $afterTxt ) )
						$this->fromSubString( $parentElement, $afterTxt, $regex );
				} 
				else if ( $mcp[$this->xRegexIndex['biname']] ) 
				{
					$nencl     = $mcp[$this->xRegexIndex['biencl']][$i];
					$finaltxt  = $mcp[$this->xRegexIndex['biendtxt']][$i];
					$otherTags = $mcp[$this->xRegexIndex['birest']][$i];
					
					$newElement =& $parentElement->createChild( $mcp[$this->xRegexIndex['biname']][$i] );
					$this->_extractAttributesFromString( $newElement, $mcp[$this->xRegexIndex['biattr']][$i] );
					
					$plaintxtMatches = array();
					
					if ( preg_match( "/^\s*([^\s<][^<]*)/", $nencl, $plaintxtMatches ) )
					{
						$txt = $plaintxtMatches[1];
						$newElement->createNode( $txt );
						$nencl = preg_replace( "/^\s*([^<]+)/", "", $nencl );
					}

					if ( $nencl && !preg_match( '/^\s*$/', $nencl ) )
						$this->fromSubString( $newElement, $nencl, $regex );
					
					if ( $finaltxt )
						$parentElement->createNode( $finaltxt );
					
					if ( $otherTags && !preg_match( '/^\s*$/', $otherTags ) )
						$this->fromSubString( $parentElement, $otherTags, $regex );	
				}	
			}
		}		
	}	
	
	/**
	 * Converts this MiniXMLDoc object to a string and returns it.
	 *
	 * The optional DEPTH may be passed to set the space offset for the
	 * first element.
	 *
	 * If the optional DEPTH is set to MINIXML_NOWHITESPACES.  
	 * When it is, no \n or whitespaces will be inserted in the xml string
	 * (ie it will all be on a single line with no spaces between the tags.
	 *
	 * Returns a string of XML representing the document.
	 *
	 * @access public
	 */
	function toString( $depth = 0 )
	{
		$retString = $this->xxmlDoc->toString( $depth );
		
		if ( $depth == MINIXML_NOWHITESPACES )
			$xmlhead = "<?xml version=\"1.0\"\\1?>";
		else 
			$xmlhead = "<?xml version=\"1.0\"\\1?>\n ";
		
		$search = array(
			"/<ROOT_ELEMENT([^>]*)>\s*/smi", 
			"/<\/ROOT_ELEMENT>/smi"
		);
		
		$replace = array(
			$xmlhead,
			""
		);
		
		$retString = preg_replace( $search, $replace, $retString );
		return $retString;
	}
	
	/**
	 * Transforms the XML structure currently represented by the MiniXML Document object 
	 * into an array.
	 * 
	 * @access public
	 */
	function &toArray()
	{
		$retVal = $this->xxmlDoc->toStructure();
	
		if ( is_array( $retVal ) )
			return $retVal;
		
		$retArray = array( '-content' => $retVal );
		return $retArray;
	}
	
	/**
	 * Utility function, call the root MiniXMLElement's getValue()
	 *
	 * @access public
	 */
	function getValue()
	{
		return $this->xxmlDoc->getValue();
	}
	
	/**
	 * Debugging aid, dump returns a nicely formatted dump of the current structure of the
	 * MiniXMLDoc object.
	 *
	 * @access public
	 */
	function dump()
	{
		return serialize( $this );
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _fromArray_getExtractSub( $v )
	{
		// is it a string, a numerical array or an associative array?
		$sub = "_fromArray_extract";
		
		if ( is_array( $v ) )
		{
			if ( MiniXMLDoc::numKeyArray( $v ) )
			{
				// All numeric - assume it is a "straight" array.
				$sub .= "ARRAY";
			} 
			else 
			{
				$sub .= "AssociativeARRAY";
			}
		} 
		else 
		{
			$sub .= "STRING";
		}
	
		return $sub;
	}
		
	/**
	 * @access private
	 */
	function _fromArray_extractAssociativeARRAY( $name, &$value, &$parent, &$params )
	{
		$thisElement =& $parent->createChild( $name );
		
		foreach ( $value as $key => $val )
		{
			$sub = $this->_fromArray_getExtractSub( $val );
			$this->$sub( $key, $val, $thisElement, $params );
		}
		
		return;
	}

	/**
	 * @access private
	 */
	function _fromArray_extractARRAY( $name, &$value, &$parent, &$params )
	{
		foreach ( $value as $val )
		{
			$sub = $this->_fromArray_getExtractSub( $val );
			$this->$sub( $name, $val, $parent, $params );
		}
		
		return;
	}

	/**
	 * @access private
	 */	
	function _fromArray_extractSTRING( $name, $value = "", &$parent, &$params )
	{
		$pname = $parent->name();
		
		if ( ( is_array( $params['attributes'][$pname] ) && $params['attributes'][$pname][$name] ) || ( is_array( $params['attributes']['-all'] ) && $params['attributes']['-all'][$name] ) )
			$parent->attribute( $name, $value );
		else if ( $name == '-content' ) 
			$parent->text( $value );
		else 
			$parent->createChild( $name, $value );
		
		return;
	}
	
	/**
	 * private method for extracting and setting the attributs from a
	 * ' a="b" c = "d"' string
	 *
	 * @access private
	 */
	function _extractAttributesFromString( &$element, &$attrString )
	{
		if ( !$attrString )
			return null;
		
		$count   = 0;
		$attribs = array();
		
		// set the attribs 
		preg_match_all( '/([^\s]+)\s*=\s*([\'"])([^\2]+?)\2/sm', $attrString, $attribs );
		
		for ( $i = 0; $i < count( $attribs[0] ); $i++ )
		{
			$attrname = $attribs[1][$i];
			$attrval  = $attribs[3][$i];
			
			if ( $attrname )
			{
				$element->attribute( $attrname, $attrval, '' );
				$count++;
			}
		}
		
		return $count;
	}
	
	
	// static methods

	/**
	 * @access public
	 * @static
	 */
	function numKeyArray( &$v )
	{
		if ( !is_array( $v ) )
			return null;
	
		$arrayKeys    = array_keys( $v );
		$numKeys      = count( $arrayKeys );
		$totalNumeric = 0;
	
		for ( $i = 0; $i < $numKeys; $i++ )
		{
			if ( is_numeric( $arrayKeys[$i] ) && $arrayKeys[$i] == $i )
				$totalNumeric++;
			else 
				return false;
		}
	
		if ( $totalNumeric == $numKeys )
		{
			// All numeric - assume it is a "straight" array.
			return true;
		} 
		else 
		{
			return false;
		}
	}
} // END OF MiniXMLDoc

?>
