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


using( 'xml.xpath.lib.XPathBase' );


/**
 * @package xml_xpath_lib
 */
 
class XPathEngine extends XPathBase
{  
	// List of supported XPath axes.
	// What a stupid idea from W3C to take axes name containing a '-' (dash)
	// NOTE: We replace the '-' with '_' to avoid the conflict with the minus operator.
	//       We will then do the same on the users Xpath querys
	//   -sibling => _sibling
	//   -or-     =>     _or_
	//  
	// This array contains a list of all valid axes that can be evaluated in an
	// XPath expression.
  	var $axes = array( 
		'child', 
		'descendant', 
		'parent', 
		'ancestor',
    	'following_sibling', 
		'preceding_sibling', 
		'following', 
		'preceding',
    	'attribute', 
		'text', 
		'namespace', 
		'self', 
		'descendant_or_self',
    	'ancestor_or_self'
	);
  
  	// List of supported XPath functions.
  	// What a stupid idea from W3C to take function name containing a '-' (dash)
  	// NOTE: We replace the '-' with '_' to avoid the conflict with the minus operator.
  	//       We will then do the same on the users Xpath querys 
  	//   starts-with      => starts_with
  	//   substring-before => substring_before
  	//   substring-after  => substring_after
  	//   string-length    => string_length
  	//
  	// This array contains a list of all valid functions that can be evaluated
  	// in an XPath expression.
  	var $functions = array( 
		'last', 
		'position', 
		'count', 
		'id', 
		'name',
    	'string', 
		'concat', 
		'starts_with', 
		'contains', 
		'substring_before',
    	'substring_after', 
		'substring', 
		'string_length', 
		'normalize_space', 
		'translate',
    	'boolean', 
		'not', 
		'true', 
		'false', 
		'lang', 
		'number', 
		'sum', 
		'floor',
    	'ceiling', 
		'round'
	);
    
  	// List of supported XPath operators.
  	//
  	// This array contains a list of all valid operators that can be evaluated
  	// in a predicate of an XPath expression. The list is ordered by the
  	// precedence of the operators (lowest precedence first).
 	var $operators = array( 
		' or ', 
		' and ', 
		'=', 
		'!=', 
		'<=', 
		'<', 
		'>=', 
		'>',
    	'+', 
		'-', 
		'*', 
		' div ', 
		' mod ' 
	);
  
	// The index and tree that is created during the analysis of an XML source.
	var $nodeIndex = array();
	var $nodeRoot  = array();
  
  	var $emptyNode = array(
		'name'        => '',       	// The tag name. E.g. In <FOO bar="aaa"/> it would be 'FOO'
		'attributes'  => array(),  	// The attributes of the tag E.g. In <FOO bar="aaa"/> it would be array('bar'=>'aaa')
		'childNodes'  => array(),  	// Array of pointers to child nodes.
		'textParts'   => array(),  	// Array of text parts between the cilderen E.g. <FOO>aa<A>bb<B/>cc</A>dd</FOO> -> array('aa','bb','cc','dd')
		'parentNode'  => null,     	// Pointer to parent node or null if this node is the 'super root'
		//-- *!* Following vars are set by the indexer and is for optimisation only *!*
		'depth'       => 0,  		// The tag depth (or tree level) starting with the root tag at 0.
		'pos'         => 0,  		// Is the zero-based position this node has in the parents 'childNodes'-list.
		'contextPos'  => 1,  		// Is the one-based position this node has by counting the siblings tags (tags with same name)
		'xpath'       => ''  		// Is the abs. XPath to this node.
	);
  
	// These variable used during the parse XML source
	var $nodeStack = array();		// The elements that we have still to close.
	var $parseStackIndex = 0;		// The current element of the nodeStack[] that we are adding to while 
									// parsing an XML source.  Corresponds to the depth of the xml node.
									// in our input data.
	var $parseOptions = array(); 	// Used to set the PHP's XML parser options (see xml_parser_set_option)
	var $parsedTextLocation = ''; 	// A reference to where we have to put char data collected during XML parsing
	var $parsInCData = 0 ;      	// Is >0 when we are inside a CDATA section.  
	var $parseSkipWhiteCache = 0;   // A cache of the skip whitespace parse option to speed up the parse.

  	// This is the array of error strings, to keep consistency.
  	var $errorStrings = array(
    	'AbsoluteXPathRequired' => "The supplied xPath '%s' does not *uniquely* describe a node in the xml document.",
    	'NoNodeMatch'           => "The supplied xPath-query '%s' does not match *any* node in the xml document."
    );
    
	
  	/**
   	 * Constructor
   	 *
   	 * Optionally you may call this constructor with the XML-filename to parse and the 
   	 * XML option vector. Each of the entries in the option vector will be passed to
   	 * xml_parser_set_option().
   	 *
   	 * A option vector sample: 
   	 *   $xmlOpt = array(XML_OPTION_CASE_FOLDING => false, 
   	 *                   XML_OPTION_SKIP_WHITE => true);
  	 *
   	 * @param  $userXmlOptions (array) (optional) Vector of (<optionID>=><value>, 
   	 *                                 <optionID>=><value>, ...).  See PHP's
   	 *                                 xml_parser_set_option() docu for a list of possible
   	 *                                 options.
   	 * @see   importFromFile(), importFromString(), setXmlOption()
   	 */
	function XPathEngine( $userXmlOptions = array() )
	{
    	parent::XPathBase();
    	$this->setType( 'XPathEngine' );
		
		// Default to not folding case
    	$this->parseOptions[XML_OPTION_CASE_FOLDING] = false;
    
		// And not skipping whitespace
    	$this->parseOptions[XML_OPTION_SKIP_WHITE] = false;
    
    	// Now merge in the overrides.
    	// Don't use PHP's array_merge!
    	if ( is_array( $userXmlOptions ) )
		{
      		foreach ( $userXmlOptions as $key => $val )
				$this->parseOptions[$key] = $val;
    	}
  	}
  
  
	/**
   	 * Resets the object so it's able to take a new xml sting/file
   	 *
   	 * Constructing objects is slow.  If you can, reuse ones that you have used already
   	 * by using this reset() function.
   	 */
  	function reset()
	{
    	parent::reset();
    
		$this->properties['xmlFile']  = ''; 
    	$this->parseStackIndex = 0;
    	$this->parsedTextLocation = '';
    	$this->parsInCData = 0;
    	$this->nodeIndex = array();
    	$this->nodeRoot  = array();
    	$this->nodeStack = array();
  	}

	/**
 	 * Returns the property/ies you want.
  	 * 
  	 * if $param is not given, all properties will be returned in a hash.
 	 *
  	 * @param  $param (string) the property you want the value of, or null for all the properties
 	 * @return        (mixed)  string OR hash of all params, or null on an unknown parameter.
 	 */
 	function getProperties( $param = null )
	{
    	$this->properties['hasContent']      = !empty( $this->nodeRoot );
    	$this->properties['caseFolding']     = $this->parseOptions[XML_OPTION_CASE_FOLDING];
    	$this->properties['skipWhiteSpaces'] = $this->parseOptions[XML_OPTION_SKIP_WHITE];
    
    	if ( empty( $param ) )
			return $this->properties;
    
    	if ( isSet( $this->properties[$param] ) )
      		return $this->properties[$param];
    	else
      		return null;
  	}
  
	/**
	 * xml_parser_set_option -- set options in an XML parser.
	 *
	 * @param $optionID (int) The option ID (e.g. XML_OPTION_SKIP_WHITE)
	 * @param $value    (int) The option value.
	 * @see XML parser functions in PHP doc
	 */
	function setXmlOption( $optionID, $value )
	{
    	if ( !is_numeric( $optionID ) )
			return;
     
	 	$this->parseOptions[$optionID] = $value;
  	}
   
	/**
	 * Controls whether case-folding is enabled for this XML parser.
	 *
	 * When it comes to XML, case-folding simply means uppercasing all tag- 
	 * and attribute-names (NOT the content) if set to true.  Note if you
	 * have this option set, then your XPath queries will also be case folded 
	 * for you.
	 *
	 * @param $onOff (bool) (default true) 
	 * @see XML parser functions in PHP doc
	 */
	function setCaseFolding( $onOff = true )
	{
    	$this->parseOptions[XML_OPTION_CASE_FOLDING] = $onOff;
  	}
  
	/**
	 * Controls whether skip-white-spaces is enabled for this XML parser.
	 *
	 * When it comes to XML, skip-white-spaces will trim the tag content.
	 * This will speed up performance, but will make your data less human 
	 * readable when you come to write it out.
	 *
	 * @param $onOff (bool) (default true) 
	 * @see XML parser functions in PHP doc
	 */
	function setSkipWhiteSpaces( $onOff = true )
	{
    	$this->parseOptions[XML_OPTION_SKIP_WHITE] = $onOff;
  	}
   
  	/**
   	 * Get the node defined by the $absoluteXPath.
 	 *
   	 * @param   $absoluteXPath (string) (optional, default is 'super-root') xpath to the node.
   	 * @return                 (array)  The node, or false if the node wasn't found.
   	 */
  	function &getNode( $absoluteXPath = '' )
	{
    	if ( $absoluteXPath === '/' )
			$absoluteXPath = '';
    
		if ( !isSet( $this->nodeIndex[$absoluteXPath] ) )
			return false;
    
		return $this->nodeIndex[$absoluteXPath];
	}
  
	/**
	 * Get a the content of a node text part or node attribute.
	 * 
	 * If the absolute Xpath references an attribute (Xpath ends with attribute::), 
	 * then the text value of that node-attribute is returned.
	 * Otherwise the Xpath is referencing a text part of the node. This can be either a 
	 * direct reference to a text part (Xpath ends with text()[<nr>]) or indirect reference 
	 * (a simple abs. Xpath to a node).
	 * 1) Direct Reference (xpath ends with text()[<part-number>]):
	 *   If the 'part-number' is omitted, the first text-part is assumed; starting by 1.
	 *   Negative numbers are allowed, where -1 is the last text-part a.s.o.
	 * 2) Indirect Reference (a simple abs. Xpath to a node):
	 *   Default is to return the *whole text*; that is the concated text-parts of the matching
	 *   node. (NOTE that only in this case you'll only get a copy and changes to the returned  
	 *   value wounld have no effect). Optionally you may pass a parameter 
	 *   $textPartNr to define the text-part you want;  starting by 1.
	 *   Negative numbers are allowed, where -1 is the last text-part a.s.o.
	 *
	 * NOTE I : The returned value can be fetched by reference
	 *          E.g. $text =& wholeText(). If you wish to modify the text.
	 * NOTE II: text-part numbers out of range will return false
	 * SIDENOTE:The function name is a suggestion from W3C in the XPath specification level 3.
	 *
	 * @param   $absoluteXPath  (string)  xpath to the node (See above).
	 * @param   $textPartNr     (int)     If referring to a node, specifies which text part 
	 *                                    to query.
	 * @return                  (&string) A *reference* to the text if the node that the other 
	 *                                    parameters describe or false if the node is not found.
	 */
	function &wholeText( $absoluteXPath, $textPartNr = null )
	{
    	$status = false;
    	$text   = null;
    
		// try-block
    	do
		{
			if ( preg_match( ";(.*)/(attribute::|@)([^/]*)$;U", $absoluteXPath, $matches ) )
			{
        		$absoluteXPath = $matches[1];
        		$attribute = $matches[3];
        
				if ( !isSet( $this->nodeIndex[$absoluteXPath]['attributes'][$attribute] ) )
				{
          			$this->_displayError( "The $absoluteXPath/attribute::$attribute value isn't a node in this document.", __LINE__, __FILE__, false );
          			break; // try-block
        		}
        
				$text   =& $this->nodeIndex[$absoluteXPath]['attributes'][$attribute];
        		$status =  true;
        
				break; // try-block
      		}
      
      		if ( !isSet( $this->nodeIndex[$absoluteXPath] ) )
			{
        		$this->_displayError( "The $absoluteXPath value isn't a node in this document.", __LINE__, __FILE__, false );
        		break; // try-block
      		}
      
      		// Get the amount of the text parts in the node.
     		$textPartSize = sizeOf( $this->nodeIndex[$absoluteXPath]['textParts'] );
      
      		// Xpath contains a 'text()'-function, thus goes right to a text node. If so interpete the Xpath.
      		if ( preg_match(":(.*)/text\(\)(\[(.*)\])?$:U", $absoluteXPath, $matches ) )
			{
        		$absoluteXPath = $matches[1];
        
				// default to the first text node if a text node was not specified
        		$textPartNr = isSet( $matches[2] )? substr($matches[2],1,-1) : 1;
				
				// Support negative indexes like -1 === last a.s.o.
				if ( $textPartNr < 0 )
					$textPartNr = $textPartSize + $textPartNr + 1;
        
				if ( ( $textPartNr <= 0 ) || ( $textPartNr > $textPartSize ) )
				{
          			$this->_displayError( "The $absoluteXPath/text()[$textPartNr] value isn't a node in this document.", __LINE__, __FILE__, false );
          			break; // try-block
        		}
        
				$text   =& $this->nodeIndex[$absoluteXPath]['textParts'][$textPartNr - 1];
        		$status =  true;
       
	    		break; // try-block
      		}
      
      		// At this point we have been given an xpath with neither a 'text()' nor 'attribute::' axis at the end
      		// So we assume a get to text is wanted and use the optioanl fallback parameters $textPartNr
      
      		// If $textPartNr == null we return a *copy* of the whole concated text-parts
      		if ( is_null( $textPartNr ) )
			{
        		unset( $text );
				
        		$text   = implode( '', $this->nodeIndex[$absoluteXPath]['textParts'] );
        		$status = true;
        
				break; // try-block
      		}
      
      		// Support negative indexes like -1 === last a.s.o.
      		if ( $textPartNr < 0 )
				$textPartNr = $textPartSize + $textPartNr + 1;
				
      		if ( ( $textPartNr <= 0 ) || ( $textPartNr > $textPartSize ) )
			{
        		$this->_displayError( "The $absoluteXPath has no text part at pos [$textPartNr] (Note: text parts start with 1).", __LINE__, __FILE__, false );
        		break; // try-block
      		}
      
	  		$text   =& $this->nodeIndex[$absoluteXPath]['textParts'][$textPartNr - 1];
      		$status =  true;
    	} while ( false ); // END try-block
    
    	if ( !$status )
			return false;
    
		return $text;
  	}

	/**
	 * Returns the containing XML as marked up HTML with specified nodes hi-lighted
	 *
	 * @param $absoluteXPath    (string) The address of the node you would like to export.
	 *                                   If empty the whole document will be exported.
	 * @param $hilighXpathList  (array)  A list of nodes that you would like to highlight
	 * @return                  (mixed)  The Xml document marked up as HTML so that it can
	 *                                   be viewed in a browser, including any XML headers.
	 *                                   false on error.
	 * @see _export()    
	 */
	function exportAsHtml( $absoluteXPath = '', $hilightXpathList = array() )
	{
    	$htmlString = $this->_export( $absoluteXPath, $xmlHeader = null, $hilightXpathList );
    
		if ( !$htmlString )
			return false;
    
		return "<pre>\n" . $htmlString . "\n</pre>"; 
  	}
  
	/**
	 * Given a context this function returns the containing XML
	 *
	 * @param $absoluteXPath  (string) The address of the node you would like to export.
	 *                                 If empty the whole document will be exported.
	 * @param $xmlHeader      (array)  The string that you would like to appear before
	 *                                 the XML content.  ie before the <root></root>.  If you
	 *                                 do not specify this argument, the xmlHeader that was 
	 *                                 found in the parsed xml file will be used instead.
	 * @return                (mixed)  The Xml fragment/document, suitable for writing
	 *                                 out to an .xml file or as part of a larger xml file, or
	 *                                 false on error.
	 * @see _export()    
	 */
	function exportAsXml( $absoluteXPath = '', $xmlHeader = null )
	{
    	$this->hilightXpathList = null;
    	return $this->_export( $absoluteXPath, $xmlHeader ); 
  	}
    
	/**
	 * Generates a XML string with the content of the current document and writes it to a file.
	 *
	 * Per default includes a <?xml ...> tag at the start of the data too. 
	 *
	 * @param     $fileName       (string) 
	 * @param     $absoluteXPath  (string) The path to the parent node you want(see text above)
	 * @param     $xmlHeader      (string) default is '< ? xml version="1.0" ? >'
	 * @return                    (string) The returned string contains well-formed XML data 
	 *                                     or false on error.
	 * @see       exportAsXml(), exportAsHtml()
	 */
	function exportToFile( $fileName, $absoluteXPath = '', $xmlHeader = '<?xml version="1.0"?>' )
	{   
    	$status = false;
    
		// try-block
		do
		{
			// Did we open the file ok?
      		if ( !( $hFile = fopen( $fileName, "wb" ) ) )
			{
        		$errStr = "Failed to open the $fileName xml file.";
        		break; // try-block
      		}
      
	  		// Lock the file
      		if ( !flock( $hFile, LOCK_EX ) )
			{
        		$errStr = "Couldn't get an exclusive lock on the $fileName file.";
        		break; // try-block
      		}
      
			if ( !( $xmlOut = $this->_export( $absoluteXPath, $xmlHeader ) ) )
			{
        		$errStr = "Export failed";
        		break; // try-block
      		}
      
      		if ( !fwrite( $hFile, $xmlOut ) )
			{
        		$errStr = "Write error when writing back the $fileName file.";
        		break; // try-block
      		}
      
      		// Flush and unlock the file.
      		@fflush( $hFile );
      		$status = true;
    	} while ( false );
    
		@flock( $hFile, LOCK_UN );
		@fclose( $hFile );
    
    	if ( !$status )
			$this->_displayError( $errStr, __LINE__, __FILE__, false );
    
		return $status;
  	}

	/**
	 * Generates a XML string with the content of the current document.
	 *
	 * This is the start for extracting the XML-data from the node-tree. We do some preperations
	 * and then call _InternalExport() to fetch the main XML-data. You optionally may pass 
	 * xpath to any node that will then be used as top node, to extract XML-parts of the 
	 * document. Default is '', meaning to extract the whole document.
	 *
	 * You also may pass a 'xmlHeader' (usually something like <?xml version="1.0"? > that will
	 * overwrite any other 'xmlHeader', if there was one in the original source.
	 * Finaly, when exproting to HTML, you may pass a vector xPaths you want to hi-light.
	 * The hi-lighted tags and attributes will receive a nice color. 
	 * 
	 * NOTE I : The output can have 2 formats:
	 *       a) If "skip white spaces" is/was set. (Not Recommended - slower)
	 *          The output is formatted by adding indenting and carriage returns.
	 *       b) If "skip white spaces" is/was *NOT* set.
	 *          'as is'. No formatting is done. The output should the same as the 
	 *          the original parsed XML source. 
	 *
	 * @param  $absoluteXPath (string) (optional, default is root) The node we choose as top-node
	 * @param  $xmlHeader     (string) (optional) content before <root/> (see text above)
	 * @param  $hilightXpath  (array)  (optional) a vector of xPaths to nodes we wat to 
	 *                                 hi-light (see text above)
	 * @return                (mixed)  The xml string, or false on error.
	 */
	function _export( $absoluteXPath = '', $xmlHeader = null, $hilightXpathList = '' )
	{
    	// Check whether a root node is given.
		if ( empty( $absoluteXpath ) )
			$absoluteXpath = '';
    
		if ( $absoluteXpath == '/' )
			$absoluteXpath = '';
    
		if ( !isSet( $this->nodeIndex[$absoluteXpath] ) )
		{
     	 	// If the $absoluteXpath was '' and it didn't exist, then the document is empty
      		// and we can safely return ''.
      		if ( $absoluteXpath == '' )
				return '';
      
	  		$this->_displayError( "The given xpath '{$absoluteXpath}' isn't a node in this document.", __LINE__, __FILE__, false );
      		return false;
    	}
    
    	$this->hilightXpathList = $hilightXpathList;
    	$this->indentStep = '  ';
    	$hilightIsActive = is_array( $hilightXpathList );
    
		if ( $hilightIsActive )
      		$this->indentStep = '&nbsp;&nbsp;&nbsp;&nbsp;';
    
		// Cache this now
    	$this->parseSkipWhiteCache = isSet( $this->parseOptions[XML_OPTION_SKIP_WHITE] )? $this->parseOptions[XML_OPTION_SKIP_WHITE] : false;

		
		// Get the starting node and begin with the header

    	// Get the start node. The super root is a special case.
    	$startNode = null;
    
		if ( empty( $absoluteXPath ) )
		{
      		$superRoot = $this->nodeIndex[''];
      
	  		// If they didn't specify an xml header, use the one in the object.
      		if ( is_null( $xmlHeader ) )
        		$xmlHeader = $this->parseSkipWhiteCache? trim( $superRoot['textParts'][0] ) : $superRoot['textParts'][0];
      
      		if ( isSet( $superRoot['childNodes'][0] ) )
				$startNode = $superRoot['childNodes'][0];
    	}
		else
		{
      		$startNode = $this->nodeIndex[$absoluteXPath];
    	}

    	if ( !empty( $xmlHeader ) ) 
      		$xmlOut = $this->parseSkipWhiteCache? $xmlHeader . "\n" : $xmlHeader;
    	else
      		$xmlOut = '';

    	// Output the document.

    	if ( ( $xmlOut .= $this->_InternalExport( $startNode ) ) === false )
      		return false;

    	// Convert our markers to hi-lights.
    	if ( $hilightIsActive )
		{
      		$from   = array( '<', '>', chr( 2 ), chr( 3 ) );
      		$to     = array( '&lt;', '&gt;', '<font color="#FF0000"><b>', '</b></font>' );
      		$xmlOut = str_replace( $from, $to, $xmlOut );
    	}
    
		return $xmlOut; 
  	}  

	/**
	 * Export the xml document starting at the named node.
	 *
	 * @param $node (node)   The node we have to start exporting from
	 * @return      (string) The string representation of the node.
	 */
	function _InternalExport( $node )
	{
    	$bDebugThisFunction = false;

    	if ( $bDebugThisFunction )
		{
     	 	$aStartTime = $this->_beginDebugFunction( "_InternalExport" );
      		echo "Exporting node: " . $node['xpath'] . "<br>\n";
    	}

    	// Quick out.
    	if ( empty( $node ) )
			return '';

    	// The output starts as empty.
    	$xmlOut = '';

		// This loop will output the text before the current child of a parent then the 
		// current child.  Where the child is a short tag we output the child, then move
		// onto the next child.  Where the child is not a short tag, we output the open tag, 
		// then queue up on currentParentStack[] the child.  
		//
		// When we run out of children, we then output the last text part, and close the 
		// 'parent' tag before popping the stack and carrying on.
		//
		// To illustrate, the numbers in this xml file indicate what is output on each
		// pass of the while loop:
		//
		// 1
		// <1>2
		//  <2>3
		//   <3/>4
		//  </4>5
		//  <5/>6
		// </6>

		// Although this is neater done using recursion, there's a 33% performance saving
		// to be gained by using this stack mechanism.

		// Only add CR's if "skip white spaces" was set. Otherwise leave as is.
		$CR = $this->parseSkipWhiteCache? "\n" : '';
		$currentIndent   = '';
		$hilightIsActive = is_array( $this->hilightXpathList );

		// To keep track of where we are in the document we use a node stack.  The node 
		// stack has the following parallel entries:
		//   'Parent'     => (array) A copy of the parent node that who's children we are 
		//                           exporting
		//   'ChildIndex' => (array) The child index of the corresponding parent that we
		//                           are currently exporting.
		//   'Highlighted'=> (bool)  If we are highlighting this node.  Only relevant if
		//                           the hilight is active.

		// Setup our node stack.  The loop is designed to output children of a parent, 
		// not the parent itself, so we must put the parent on as the starting point.
		$nodeStack['Parent'] = array( $node['parentNode'] );
		 
    	// And add the childpos of our node in it's parent to our "child index stack".
    	$nodeStack['ChildIndex'] = array( $node['pos'] );
    
		// We start at 0.
    	$nodeStackIndex = 0;

    	// We have not to output text before/after our node, so blank it.  (As the nodeStack[]
    	// holds copies of nodes, we can do this ok without affecting the document.)
    	$nodeStack['Parent'][0]['textParts'][$node['pos']] = '';

    	// While we still have data on our stack
    	while ( $nodeStackIndex >= 0 )
		{
      		// Count the children and get a copy of the current child.
      		$iChildCount  = count( $nodeStack['Parent'][$nodeStackIndex]['childNodes'] );
      		$currentChild = $nodeStack['ChildIndex'][$nodeStackIndex];
			
      		// Only do the auto indenting if the $parseSkipWhiteCache flag was set.
      		if ( $this->parseSkipWhiteCache )
        		$currentIndent = str_repeat( $this->indentStep, $nodeStackIndex );

      		if ( $bDebugThisFunction )
        		echo "Exporting child " . ( $currentChild + 1 ) . " of node {$nodeStack['Parent'][$nodeStackIndex]['xpath']}\n";

      		// Add the text before our child.

      		// Add the text part before the current child
      		if ( !empty( $nodeStack['Parent'][$nodeStackIndex]['textParts'][$currentChild] ) )
			{
        		// Only add CR indent if there were children.
        		if ( $iChildCount )
          			$xmlOut .= $CR.$currentIndent;
        
				$xmlOut .= $nodeStack['Parent'][$nodeStackIndex]['textParts'][$currentChild];
      		}
      
	  		if ( $iChildCount && $nodeStackIndex )
				$xmlOut .= $CR;

      		// Are there any more children?
      		if ( $iChildCount <= $currentChild )
			{
        		// Nope, so output the last text before the closing tag
        		if ( !empty( $nodeStack['Parent'][$nodeStackIndex]['textParts'][$currentChild + 1] ) ) 
          			$xmlOut .= $currentIndent . $nodeStack['Parent'][$nodeStackIndex]['textParts'][$currentChild + 1] . $CR;

        		// Now close this tag, as we are finished with this child.

        		// Potentially output an (slightly smaller indent).
        		if ( $this->parseSkipWhiteCache && count( $nodeStack['Parent'][$nodeStackIndex]['childNodes'] ) )
          			$xmlOut .= str_repeat( $this->indentStep, $nodeStackIndex - 1 );

        		// Check whether the xml-tag is to be hilighted.
        		$highlightStart = $highlightEnd = '';
        
				if ( $hilightIsActive )
				{
          			$currentXpath = $nodeStack['Parent'][$nodeStackIndex]['xpath'];
          
		  			if ( in_array( $currentXpath, $this->hilightXpathList ) )
					{
            			// yes, we hilight
            			$highlightStart = chr( 2 );
            			$highlightEnd   = chr( 3 );
          			}
        		}
        
				$xmlOut .= $highlightStart . '</' . $nodeStack['Parent'][$nodeStackIndex]['name'].'>' . $highlightEnd;
        
				// Decrement the $nodeStackIndex to go back to the next unfinished parent.
        		$nodeStackIndex--;

        		// If the index is 0 we are finished exporting the last node, as we may have been
        		// exporting an internal node.
        		if ( $nodeStackIndex == 0 )
					break;

        		// Indicate to the parent that we are finished with this child.
        		$nodeStack['ChildIndex'][$nodeStackIndex]++;

        		continue;
      		}
			
			// Ok, there are children still to process.

      		// Queue up the next child (I can copy because I won't modify and copying is faster.)
      		$nodeStack['Parent'][$nodeStackIndex + 1] = $nodeStack['Parent'][$nodeStackIndex]['childNodes'][$currentChild];

      		// Work out if it is a short child tag.
      		$iGrandChildCount = count( $nodeStack['Parent'][$nodeStackIndex + 1]['childNodes'] );
      		$shortGrandChild  = ( ( $iGrandChildCount == 0 ) && ( implode( '', $nodeStack['Parent'][$nodeStackIndex + 1]['textParts'])=='' ) );

			// Assemble the attribute string first.
      		$attrStr = '';
      
	  		foreach ( $nodeStack['Parent'][$nodeStackIndex + 1]['attributes'] as $key => $val )
			{
        		// Should we hilight the attribute?
        		if ( $hilightIsActive && in_array( $currentXpath . '/attribute::' . $key, $this->hilightXpathList ) )
				{
          			$hiAttrStart = chr( 2 );
          			$hiAttrEnd   = chr( 3 );
        		}
				else
				{
          			$hiAttrStart = $hiAttrEnd = '';
        		}
        
				$attrStr .= ' ' . $hiAttrStart . $key . '="' . $val . '"' . $hiAttrEnd;
      		}

			// Work out what goes before and after the tag content

			$beforeTagContent = $currentIndent;
      
	  		if ( $shortGrandChild )
				$afterTagContent = '/>';
      		else
				$afterTagContent = '>';

      		// Check whether the xml-tag is to be hilighted.
      		if ( $hilightIsActive )
			{
        		$currentXpath = $nodeStack['Parent'][$nodeStackIndex + 1]['xpath'];
        
				if ( in_array( $currentXpath, $this->hilightXpathList ) )
				{
          			// yes, we hilight
          			$beforeTagContent .= chr( 2 );
          			$afterTagContent  .= chr( 3 );
        		}
      		}
      
	  		$beforeTagContent .= '<';
			$xmlOut .= $beforeTagContent . $nodeStack['Parent'][$nodeStackIndex + 1]['name'].$attrStr . $afterTagContent;

      		// If it is a short tag, then we've already done this child, we just move to the next.
      		if ( $shortGrandChild )
			{
        		// Move to the next child, we need not go deeper in the tree.
        		$nodeStack['ChildIndex'][$nodeStackIndex]++;
        
				// But if we are just exporting the one node we'd go no further.
        		if ( $nodeStackIndex == 0 )
					break;
      		}
			else
			{
        		// Else queue up the child going one deeper in the stack.
        		$nodeStackIndex++;
        
				// Start with it's first child.
        		$nodeStack['ChildIndex'][$nodeStackIndex] = 0;
      		}
    	}

    	$result = $xmlOut;

    	if ( $bDebugThisFunction )
      		$this->_closeDebugFunction( $aStartTime, $result );
    
		return $result;
	}
     
  	/**
	 * Reads a file or URL and parses the XML data.
   	 *
   	 * Parse the XML source and (upon success) store the information into an internal structure.
  	 *
   	 * @param     $fileName (string) Path and name (or URL) of the file to be read and parsed.
	 * @return              (bool)   true on success, false on failure (check getLastError())
	 * @see       importFromString(), getLastError(), 
	 */
  	function importFromFile( $fileName )
	{
    	$status = false;
    	$errStr = '';
    
		// try-block
		do
		{
      		// Remember file name. Used in error output to know in which file it happend.
      		$this->properties['xmlFile'] = $fileName;
      
	  		// If we already have content, then complain.
      		if ( !empty( $this->nodeRoot ) )
			{
        		$errStr = 'Called when this object already contains xml data. Use reset().';
        		break; // try-block
      		}
      
	  		// The the source is an url try to fetch it.
      		if ( preg_match( ';^http(s)?://;', $fileName ) )
			{
        		// Read the content of the url...this is really prone to errors, and we don't really
				// check for too many here...for now, suppressing both possible warnings...we need
				// to check if we get a none xml page or something of that nature in the future
				$xmlString = @implode( '', @file( $fileName ) );
				
        		if ( !empty( $xmlString ) )
          			$status = true;
        		else
          			$errStr = "The url '{$fileName}' could not be found or read.";
        
        		break;
      		} 
      
      		// Reaching this point we're dealing with a real file (not an url). Check if the file exists and is readable.
      		
			// Read the content from the file.
			if ( !is_readable( $fileName ) )
			{
        		$errStr = "File '{$fileName}' could not be found or read.";
        		break; // try-block
      		}
      
	  		if ( is_dir( $fileName ) )
			{
        		$errStr = "'{$fileName}' is a directory.";
        		break; // try-block
      		}
      
	  		// Read the file
      		if ( !( $fp = @fopen( $fileName, 'rb' ) ) )
			{
				$errStr = "Failed to open '{$fileName}' for read.";
				break; // try-block
      		}
      		
			$xmlString = fread( $fp, filesize( $fileName ) );
      		@fclose( $fp );
      
      		$status = true;
    	} while ( false );
    
    	if ( !$status )
		{
      		$this->_displayError( 'In importFromFile(): '. $errStr, __LINE__, __FILE__, false );
      		return false;
    	}
    
		return $this->importFromString( $xmlString );
	}
  
	/**
	 * Reads a string and parses the XML data.
	 *
	 * Parse the XML source and (upon success) store the information into an internal structure.
	 * If a parent xpath is given this means that XML data is to be *appended* to that parent.
	 *
	 * ### If a function uses setLastError(), then say in the function header that getLastError() is useful.
	 *
	 * @param  $xmlString           (string) Name of the string to be read and parsed.
	 * @param  $absoluteParentPath  (string) Node to append data too (see above)
	 * @return                      (bool)   true on success, false on failure 
	 *                                       (check getLastError())
	 */
	function importFromString( $xmlString, $absoluteParentPath = '' )
	{
    	$bDebugThisFunction = false;

    	if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( "importFromString" );
      		echo "Importing from string of length " . strlen( $xmlString ) . " to node '$absoluteParentPath'\n<br>";
      		echo "Parser options:\n<br>";
      		print_r( $this->parseOptions );
    	}

    	$status = false;
    	$errStr = '';
    
		// try-block
		do
		{
      		// If we already have content, then complain.
      		if ( !empty( $this->nodeRoot ) && empty( $absoluteParentPath ) )
			{
        		$errStr = 'Called when this object already contains xml data. Use reset() or pass the parent Xpath as 2ed param to where tie data will append.';
        		break; // try-block
      		}
      
	  		// Check whether content has been read.
      		if ( empty( $xmlString ) )
			{
        		$status = true;
        
				// If we were importing to root, build a blank root.
        		if ( empty( $absoluteParentPath ) )
          			$this->nodeRoot = array();
        
        		$this->reindexNodeTree();
        		break; // try-block
      		}
			else
			{
        		$xmlString = $this->_translateAmpersand( $xmlString );
      		}
      
			// Restart our node index with a root entry.
			$nodeStack = array();
			$this->parseStackIndex = 0;

			// If a parent xpath is given this means that XML data is to be *appended* to that parent.
			if ( !empty( $absoluteParentPath ) )
			{
        		// Check if parent exists
        		if ( !isSet( $nodeIndex[$absoluteParentPath] ) )
				{
          			$errStr = "You tried to append XML data to a parent '$absoluteParentPath' that does not exist.";
          			break; // try-block
        		} 
        
				// Add it as the starting point in our array.
        		$this->nodeStack[0] =& $nodeIndex[$absoluteParentPath];
      		}
			else
			{
        		// Build a 'super-root'
        		$this->nodeRoot = $this->emptyNode;
        		$this->nodeRoot['name'] = '';
        		$this->nodeRoot['parentNode'] = null;
        
				// Put it in as the start of our node stack.
        		$this->nodeStack[0] =& $this->nodeRoot;
      		}

      		// Point our text buffer reference at the next text part of the root
			$this->parsedTextLocation =& $this->nodeStack[0]['textParts'][];
      		$this->parsInCData = 0;
      
	  		// We cache this now.
      		$this->parseSkipWhiteCache = isSet( $this->parseOptions[XML_OPTION_SKIP_WHITE] )? $this->parseOptions[XML_OPTION_SKIP_WHITE] : false;
      
      		// Create an XML parser.
      		$parser = xml_parser_create();
      
	  		// Set default XML parser options.
      		if ( is_array( $this->parseOptions ) )
			{
        		foreach ( $this->parseOptions as $key => $val )
          			xml_parser_set_option( $parser, $key, $val );
      		}
      
      		// Set the object and the element handlers for the XML parser.
      		xml_set_object( $parser, &$this );
      		xml_set_element_handler( $parser, '_handleStartElement', '_handleEndElement' );
      		xml_set_character_data_handler( $parser, '_handleCharacterData' );
      		xml_set_default_handler( $parser, '_handleDefaultData' );
      		xml_set_processing_instruction_handler( $parser, '_handlePI' );
     
      		if ( $bDebugThisFunction )
       			$this->_profileFunction( $aStartTime, "Setup for parse" );

			// Parse the XML source and on error generate an error message.
			if ( !xml_parse( $parser, $xmlString, true ) )
			{
        		$source = empty( $this->properties['xmlFile'] )? 'string' : 'file ' . basename( $this->properties['xmlFile'] ) . "'";
        		
				$errStr = "XML error in given {$source} on line " .
					xml_get_current_line_number( $parser )   . '  column ' . 
					xml_get_current_column_number( $parser ) . '. Reason:' . 
					xml_error_string( xml_get_error_code( $parser ) );
        
				break; // try-block
      		}
      
      		// Free the parser.
      		@xml_parser_free( $parser );
      
	  		// And we don't need this any more.
      		$this->nodeStack = array();

      		if ( $bDebugThisFunction )
       			$this->_profileFunction( $aStartTime, "Parse Object" );
      
      		$this->reindexNodeTree();

      		if ( $bDebugThisFunction )
       			$this->_profileFunction( $aStartTime, "Reindex Object" );
      
      		$status = true;
    	} while ( false );
    
		if ( !$status )
		{
      		$this->_displayError( 'In importFromString(): ' . $errStr, __LINE__, __FILE__, false );
      		$bResult = false;
    	}
		else
		{
      		$bResult = true;
    	}

    	if ( $bDebugThisFunction )
      		$this->_closeDebugFunction( $aStartTime, $bResult );

    	return $bResult;
  	}

	/**
	 * Handles opening XML tags while parsing.
	 *
	 * While parsing a XML document for each opening tag this method is
	 * called. It'll add the tag found to the tree of document nodes.
	 *
	 * @param $parser     (int)    Handler for accessing the current XML parser.
	 * @param $name       (string) Name of the opening tag found in the document.
	 * @param $attributes (array)  Associative array containing a list of
	 *                             all attributes of the tag found in the document.
	 * @see _handleEndElement(), _handleCharacterData()
	 */
	function _handleStartElement( $parser, $nodeName, $attributes )
	{
    	if ( empty( $nodeName ) )
		{
      		$this->_displayError( 'XML error in file at line' . xml_get_current_line_number( $parser ) . '. Empty name.', __LINE__, __FILE__ );
      		return;
    	}

    	// Trim accumulated text if necessary.
    	if ( $this->parseSkipWhiteCache )
		{
      		$iCount = count( $this->nodeStack[$this->parseStackIndex]['textParts'] );
      		$this->nodeStack[$this->parseStackIndex]['textParts'][$iCount - 1] = rtrim( $this->parsedTextLocation );
    	} 

    	if ( $this->bDebugXmlParse )
		{
      		echo "<blockquote>" . htmlspecialchars( "Start node: <" . $nodeName . ">" ) . "<br>";
      		echo "Appended to stack entry: $this->parseStackIndex<br>\n";
      		echo "Text part before element is: " . htmlspecialchars( $this->parsedTextLocation );
    	}

    	// Add a node and set path to current.
    	if ( !$this->_internalAppendChild( $this->parseStackIndex, $nodeName ) )
		{
      		$this->_displayError( 'Internal error during parse of XML file at line' . xml_get_current_line_number( $parser ) . '. Empty name.', __LINE__, __FILE__ );
      		return;
    	}    

    	// We will have gone one deeper then in the stack.
    	$this->parseStackIndex++;

    	// Point our parseTxtBuffer reference at the new node.
    	$this->parsedTextLocation =& $this->nodeStack[$this->parseStackIndex]['textParts'][0];
    
    	// Set the attributes.
    	if ( !empty( $attributes ) )
		{
      		if ( $this->bDebugXmlParse )
			{
        		echo 'Attributes: <br>';
        		print_r( $attributes );
        		echo '<br>';
      		}
      
	  		$this->nodeStack[$this->parseStackIndex]['attributes'] = $attributes;
    	}
  	}
  
	/**
	 * Handles closing XML tags while parsing.
	 *
	 * While parsing a XML document for each closing tag this method is called.
	 *
	 * @param $parser (int)    Handler for accessing the current XML parser.
	 * @param $name   (string) Name of the closing tag found in the document.
	 * @see       _handleStartElement(), _handleCharacterData()
	 */
	function _handleEndElement( $parser, $name )
	{
    	if ( ( $this->parsedTextLocation == '' ) && empty( $this->nodeStack[$this->parseStackIndex]['textParts'] ) )
		{
      		// We reach this point when parsing a tag of format <foo/>. The 'textParts'-array 
      		// should stay empty and not have an empty string in it.
    	}
		else
		{
      		// Trim accumulated text if necessary.
      		if ( $this->parseSkipWhiteCache )
			{
        		$iCount = count( $this->nodeStack[$this->parseStackIndex]['textParts'] );
        		$this->nodeStack[$this->parseStackIndex]['textParts'][$iCount - 1] = rtrim( $this->parsedTextLocation );
      		}
    	}

    	if ( $this->bDebugXmlParse )
		{
      		echo "Text part after element is: " . htmlspecialchars( $this->parsedTextLocation ) . "<br>\n";
      		echo htmlspecialchars( "Parent:<{$this->parseStackIndex}>, End-node:</$name> '" . $this->parsedTextLocation ) . "'<br>Text nodes:<pre>\n";
      		$dataPartsCount = count( $this->nodeStack[$this->parseStackIndex]['textParts'] );
			
      		for ( $i = 0; $i < $dataPartsCount; $i++ )
				echo "$i:" . htmlspecialchars( $this->nodeStack[$this->parseStackIndex]['textParts'][$i] ) . "\n";
      
      		var_dump( $this->nodeStack[$this->parseStackIndex]['textParts'] );
      		echo "</pre></blockquote>\n";
    	}

    	// Jump back to the parent element.
    	$this->parseStackIndex--;

    	// Set our reference for where we put any more whitespace
    	$this->parsedTextLocation =& $this->nodeStack[$this->parseStackIndex]['textParts'][];

    	// Note we leave the entry in the stack, as it will get blanked over by the next element
    	// at this level.  The safe thing to do would be to remove it too, but in the interests 
    	// of performance, we will not bother, as were it to be a problem, then it would be an
    	// internal bug anyway.
    	if ( $this->parseStackIndex < 0 )
		{
      		$this->_displayError( 'Internal error during parse of XML file at line' . xml_get_current_line_number( $parser ) . '. Empty name.', __LINE__, __FILE__ );
      		return;
    	}    
  	}
  
	/**
	 * Handles character data while parsing.
	 *
	 * While parsing a XML document for each character data this method
	 * is called. It'll add the character data to the document tree.
	 *
	 * @param $parser (int)    Handler for accessing the current XML parser.
	 * @param $text   (string) Character data found in the document.
	 * @see       _handleStartElement(), _handleEndElement()
	 */
	function _handleCharacterData( $parser, $text )
	{
    	if ( $this->parsInCData > 0 )
			$text = $this->_translateAmpersand( $text, $reverse = true );
    
    	if ( $this->bDebugXmlParse )
			echo "Handling character data: '" . htmlspecialchars( $text ) . "'<br>";
    
		if ( $this->parseSkipWhiteCache && !empty( $text ) && !$this->parsInCData )
		{
      		// Special case CR. CR always comes in a separate data. Trans. it to '' or ' '. 
      		// If txtBuffer is already ending with a space use '' otherwise ' '.
      		$bufferHasEndingSpace = ( empty( $this->parsedTextLocation ) || substr( $this->parsedTextLocation, -1 ) === ' ' )? true : false;
      
	  		if ( $text == "\n" )
			{
        		$text = $bufferHasEndingSpace? '' : ' ';
      		}
			else
			{
        		if ( $bufferHasEndingSpace )
          			$text = ltrim( preg_replace( '/\s+/', ' ', $text ) );
        		else
          			$text = preg_replace( '/\s+/', ' ', $text );
      		}
      
	  		if ( $this->bDebugXmlParse )
				echo "'Skip white space' is ON. reduced to : '" . htmlspecialchars( $text ) . "'<br>";
    	}
    
		$this->parsedTextLocation .= $text;
  	}
  
	/**
	 * Default handler for the XML parser.  
	 *
	 * While parsing a XML document for string not caught by one of the other
	 * handler functions, we end up here.
	 *
	 * @param $parser (int)    Handler for accessing the current XML parser.
 	 * @param $text   (string) Character data found in the document.
	 * @see       _handleStartElement(), _handleEndElement()
	 */
	function _handleDefaultData( $parser, $text )
	{
		// try-block
    	do
		{
      		if ( !strcmp( $text, '<![CDATA[' ) )
			{
        		$this->parsInCData++;
      		}
			else if ( !strcmp( $text, ']]>' ) )
			{
        		$this->parsInCData--;
        
				if ( $this->parsInCData < 0 )
					$this->parsInCData = 0;
      		}
      
	  		$this->parsedTextLocation .= $this->_translateAmpersand( $text, $reverse = true );
			
      		if ( $this->bDebugXmlParse )
				echo "Default handler data: " . htmlspecialchars( $text ) . "<br>";    
      
	  		break; // try-block
    	} while ( false );
  	}
  
	/**
	 * Handles processing instruction (PI)
	 *
	 * A processing instruction has the following format: 
	 * <?  target data  ? > e.g.  <? dtd version="1.0" ? >
	 *
	 * Currently I have no bether idea as to left it 'as is' and treat the PI data as normal 
	 * text (and adding the surrounding PI-tags <? ? >). 
	 *
	 * @param     $parser (int)    Handler for accessing the current XML parser.
	 * @param     $target (string) Name of the PI target. E.g. XML, PHP, DTD, ... 
	 * @param     $data   (string) Associative array containing a list of
	 * @see       PHP's manual "xml_set_processing_instruction_handler"
	 */
	function _handlePI( $parser, $target, $data )
	{
    	$data = $this->_translateAmpersand( $data, $reverse = true );
    	$this->parsedTextLocation .= "<?{$target} {$data}?>";
    
		return true;
  	}

	/**
	 * Adds a new node to the XML document tree during xml parsing.
	 *
	 * This method adds a new node to the tree of nodes of the XML document
	 * being handled by this class. The new node is created according to the
	 * parameters passed to this method.  This method is a much watered down
	 * version of appendChild(), used in parsing an xml file only.
	 * 
	 * It is assumed that adding starts with root and progresses through the
	 * document in parse order.  New nodes must have a corresponding parent. And
	 * once we have read the </> tag for the element we will never need to add
	 * any more data to that node.  Otherwise the add will be ignored or fail.
	 *
	 * The function is faciliated by a nodeStack, which is an array of nodes that
	 * we have yet to close.
	 *
	 * @param   $stackParentIndex (int)    The index into the nodeStack[] of the parent
	 *                                     node to which the new node should be added as 
	 *                                     a child. *READONLY*
	 * @param   $nodeName         (string) Name of the new node. *READONLY*
	 * @return                    (bool)   true if we successfully added a new child to 
	 *                                     the node stack at index $stackParentIndex + 1,
	 *                                     false on error.
	 */
	function _internalAppendChild( $stackParentIndex, $nodeName )
	{
    	// This call is likely to be executed thousands of times, so every 0.01ms counts.
    	// If you want to debug this function, you'll have to comment the stuff back in
    	// $bDebugThisFunction = false;

    	if ( !isSet( $this->nodeStack[$stackParentIndex] ) )
		{
      		$errStr = "Invalid parent. You tried to append the tag '{$nodeName}' to an non-existing parent in our node stack '{$stackParentIndex}'.";
      		$this->_displayError( 'In _internalAppendChild(): '. $errStr, __LINE__, __FILE__, false ); 

      		return false;
    	}

    	// Retrieve the parent node from the node stack.  This is the last node at that 
    	// depth that we have yet to close.  This is where we should add the text/node.
    	$parentNode =& $this->nodeStack[$stackParentIndex];
          
    	// Brand new node please.
    	$newChildNode = $this->emptyNode;
    
		// Save the vital information about the node.
		$newChildNode['name'] = $nodeName;
		$parentNode['childNodes'][] =& $newChildNode;
    
		// Add to our node stack.
		$this->nodeStack[$stackParentIndex + 1] =& $newChildNode;

    	return true;
  	}
  
	/**
	 * Update nodeIndex and every node of the node-tree. 
	 *
	 * Call after you have finished any tree modifications other wise a match with 
	 * an xPathQuery will produce wrong results.  The $this->nodeIndex[] is recreated 
	 * and every nodes optimization data is updated.  The optimization data is all the
	 * data that is duplicate information, would just take longer to find. Child nodes 
	 * with value null are removed from the tree.
	 *
	 * By default the modification functions in this component will automatically re-index
	 * the nodes in the tree.  Sometimes this is not the behaver you want. To surpress the 
	 * reindex, set the functions $autoReindex to false and call reindexNodeTree() at the 
	 * end of your changes.  This sometimes leads to better code (and less CPU overhead).
	 *
	 * Sample:
	 * =======
	 * Given the xml is <AAA><B/>.<B/>.<B/></AAA> | Goal is <AAA>.<B/>.</AAA>  (Delete B[1] and B[3])
	 *   $xPathSet = $xPath->match('//B'); # Will result in array('/AAA[1]/B[1]', '/AAA[1]/B[2]', '/AAA[1]/B[3]');
	 * Three ways to do it.
	 * 1) Top-Down  (with auto reindexing) - Safe, Slow and you get easily mix up with the the changing node index
	 *    removeChild('/AAA[1]/B[1]'); // B[1] removed, thus all B[n] become B[n-1] !!
	 *    removeChild('/AAA[1]/B[2]'); // Now remove B[2] (That originaly was B[3])
	 * 2) Bottom-Up (with auto reindexing) -  Safe, Slow and the changing node index (caused by auto-reindex) can be ignored.
	 *    for ($i=sizeOf($xPathSet)-1; $i>=0; $i--) {
	 *      if ($i==1) continue; 
	 *      removeChild($xPathSet[$i]);
	 *    }
	 * 3) // Top-down (with *NO* auto reindexing) - Fast, Safe as long as you call reindexNodeTree()
	 *    foreach($xPathSet as $xPath) {
	 *      // Specify no reindexing
	 *      if ($xPath == $xPathSet[1]) continue; 
	 *      removeChild($xPath, $autoReindex=false);
	 *      // The object is now in a slightly inconsistent state.
	 *    }
	 *    // Finally do the reindex and the object is consistent again
	 *    reindexNodeTree();
	 *
	 * @return (bool) true on success, false otherwise.
	 * @see _recursiveReindexNodeTree()
	 */
	function reindexNodeTree()
	{
    	$this->nodeIndex = array();
    	$this->nodeIndex[''] =& $this->nodeRoot;
    
		// Quick out for when the tree has no data.
    	if ( empty( $this->nodeRoot ) )
			return true;
    
		return $this->_recursiveReindexNodeTree( '' );
  	}
  
	/**
	 * Here's where the work is done for reindexing (see reindexNodeTree)
	 *
	 * @param  $absoluteParentPath (string) the xPath to the parent node
	 * @return                     (bool)   true on success, false otherwise.
	 * @see reindexNodeTree()
	 */
	function _recursiveReindexNodeTree( $absoluteParentPath )
	{
    	$parentNode =& $this->nodeIndex[$absoluteParentPath];
    
    	// Check for any 'dead' child nodes first and concate the text parts if found.
    	for ( $i = sizeOf( $parentNode['childNodes'] ) - 1; $i >= 0; $i-- )
		{
      		// Check if the child node still exits (it may have been removed).
      		if ( !empty( $parentNode['childNodes'][$i] ) )
				continue;
      
	  		// Child node was removed. We got to merge the text parts then.
      		$parentNode['textParts'][$i] .= $parentNode['textParts'][$i + 1];
      		array_splice( $parentNode['textParts'],  $i + 1, 1 ); 
      		array_splice( $parentNode['childNodes'], $i,     1 );
    	}
    
		// Now start a reindex.
		$contextHash = array();
		$childSize   = sizeOf( $parentNode['childNodes'] );
		
    	for ( $i = 0; $i < $childSize; $i++ )
		{
      		$childNode =& $parentNode['childNodes'][$i];
      
	  		// Make sure that theire is a text-part infornt of every node. (May be empty).
      		if ( !isSet( $parentNode['textParts'][$i] ) )
				$parentNode['textParts'][$i] = '';
      
	  		// Count the nodes with same name (to determin theire context position).
      		$childName = $childNode['name'];
      
	  		if ( empty( $contextHash[$childName] ) ) 
        		$contextHash[$childName] = 1;
      		else
        		$contextHash[$childName]++;
      
      		// Make the node-index hash.
			$newPath = $absoluteParentPath . '/' . $childName . '[' . $contextHash[$childName] . ']';
      		$this->nodeIndex[$newPath] =& $childNode;
      
	  		// Update the node info (optimisation).
      		$childNode['parentNode'] =& $parentNode;
      		$childNode['depth']      =  $parentNode['depth'] +1;
      		$childNode['pos']        =  $i;
      		$childNode['contextPos'] =  $contextHash[$childName];
      		$childNode['xpath']      =  $newPath;
      
	  		$this->_recursiveReindexNodeTree( $newPath );
    	}
    
		// Make sure that theire is a text-part after the last node.
    	if ( !isSet( $parentNode['textParts'][$i] ) )
			$parentNode['textParts'][$i] = '';
    
		return true;
  	}
  
	/** 
	 * Clone a node and it's child nodes.
	 *
	 * NOTE: If the node has children you *MUST* use the reference operator!
	 *       E.g. $clonedNode =& cloneNode($node);
	 *       Otherwise the children will not point back to the parent, they will point 
	 *       back to your temporary variable instead.
	 *
	 * @param   $node (mixed)  Either a node (hash array) or an abs. Xpath to a node in 
	 *                         the current doc
	 * @return        (&array) A node and it's child nodes.
	 */
	function &cloneNode( $node )
	{
    	if ( is_string( $node ) && isSet( $this->nodeIndex[$node] ) )
      		$node = $this->nodeIndex[$node];
    
    	$childSize = sizeOf( $node['childNodes'] );
    
		for ( $i = 0; $i < $childSize; $i++ )
		{
      		$childNode =& $this->cloneNode($node['childNodes'][$i]); // copy child 
      		$node['childNodes'][$i]  =& $childNode;	// reference the copy
      		$childNode['parentNode'] =& $node;		// child references the parent.
    	}
    
		return $node;
  	}
  
	/**
	Nice to have but __sleep() has a bug. 
    (2002-2 PHP V4.1. See bug #15350)
  
  	/**
   	 * PHP cals this function when you call PHP's serialize. 
   	 *
   	 * It prevents cyclic referencing, which is why print_r() of an XPath object doesn't work.
   	 *
  	function __sleep()
	{
    	// Destroy recursive pointers.
    	$keys = array_keys( $this->nodeIndex );
    	$size = sizeOf( $keys );
    
		for ( $i = 0; $i < $size; $i++ )
      		unset( $this->nodeIndex[$keys[$i]]['parentNode'] );
    
    	unset( $this->nodeIndex );
  	}
  
  	/**
   	 * PHP cals this function when you call PHP's unserialize. 
   	 *
   	 * It reindexes the node-tree
   	 *
  	function __wakeup()
	{
    	$this->reindexNodeTree();
  	}
	 */

	/**
	 * Matches (evaluates) an XPath expression.
	 *
	 * This method tries to evaluate an XPath expression by parsing it. A XML source must 
	 * have been imported before this method is able to work.
	 *
	 * @param     $xPathQuery  (string) XPath expression to be evaluated.
	 * @param     $baseXPath   (string) (default is super-root) Full path of a document node, 
	 *                                  from which the XPath expression should  start evaluating.
	 * @return                 (array)  The returned vector contains a list of the full document 
	 *                                  Xpaths of all nodes that match the evaluated XPath 
	 *                                  expression.  Returns false on error.
	 */
	function match( $xPathQuery, $baseXPath = '' )
	{
    	// Replace a double slashes, because they'll cause problems otherwise.
    	static $slashes2descendant = array(
        	'//@' => '/descendant::*/attribute::', 
        	'//'  => '/descendant::', 
        	'/@'  => '/attribute::'
		);
		
    	// Stupid idea from W3C to take axes name containing a '-' (dash) !!!
    	// We replace the '-' with '_' to avoid the conflict with the minus operator.
    	static $dash2underscoreHash = array( 
        	'-sibling'         => '_sibling', 
        	'-or-'             => '_or_',
        	'starts-with'      => 'starts_with', 
        	'substring-before' => 'substring_before',
        	'substring-after'  => 'substring_after', 
        	'string-length'    => 'string_length',
        	'normalize-space'  => 'normalize_space'
		);
    
    	if ( empty( $xPathQuery ) )
			return array();

    	// Special case for when document is empty.
    	if ( empty( $this->nodeRoot ) )
			return array();

    	if ( !isSet( $this->nodeIndex[$baseXPath] ) )
		{
      		$this->_displayError( sprintf( $this->errorStrings['AbsoluteXPathRequired'], $xPathQuery ), __LINE__, __FILE__, false );
      		return false;
    	}
    
    	// Replace a double slashes, and '-' (dash) in axes names.
    	$xPathQuery = strtr( $xPathQuery, $slashes2descendant  );
    	$xPathQuery = strtr( $xPathQuery, $dash2underscoreHash );
    
    	return $this->_internalEvaluate( $xPathQuery, $baseXPath );
  	}
  	
	/**
   	 * Alias for the match function
   	 *
   	 * @see match()
   	 */
  	function evaluate( $xPathQuery, $baseXPath = '' )
	{
    	return $this->match( $xPathQuery, $baseXPath );
  	}
  
	/**
	 * Internal recursive evaluate an-XPath-expression function.
	 *
	 * $this->evaluate() is the entry point and does some inits, while this 
	 * function is called recursive internaly for every sub-xPath expresion we find.
	 *
	 * @param  $xPathQuery  (string) XPath expression to be evaluated.
	 * @param  $contextPath (mixed) (string or array) Full path of a document node, starting
	 *                              from which the XPath expression should be evaluated.
	 * @return              (array) Vector of absolute XPath's, or false on error.
	 * @see    evaluate()
	 */
	function _internalEvaluate( $xPathQuery, $contextPath = '' )
	{
    	// If you are having difficulty using this function.  Then set this to true and 
    	// you'll get diagnostic info displayed to the output.
    	$bDebugThisFunction = false;
    
    	if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( "evaluate" );
      		echo "Path: $xPathQuery\n Context: $contextPath\n";
    	}
    
    	// Numpty check
    	if ( empty( $xPathQuery ) )
		{
      		$this->_displayError( "The $xPathQuery argument must have a value.", __LINE__, __FILE__ );
      		return false;
    	}
    
		// Split the paths that are sparated by '|' into distinct xPath expresions.
		$xpathQueryList = ( strpos( $xPathQuery, '|' ) === false )? array( $xPathQuery ) : $this->_bracketExplode( '|', $xPathQuery );
    
		if ( $bDebugThisFunction )
		{
			echo "<hr>Split the paths that are sparated by '|'\n";
			print_r( $xpathQueryList );
    	}
		
    	// Create an empty set to save the result.
    	$result = array();
    
    	// Run through all paths.
    	foreach ( $xpathQueryList as $xQuery )
		{
      		// mini syntax check
      		if ( !$this->_bracketsCheck( $xQuery ) )
				$this->_displayError( 'While parsing an XPath expression, in the predicate ' . str_replace( $xQuery, '<b>' . $xQuery . '</b>', $xPathQuery ) . ', there was an invalid number of brackets or a bracket mismatch.', __LINE__, __FILE__ );
      
      		// Save the current path.
      		$this->currentXpathQuery = $xQuery;
      
	  		// Split the path at every slash *outside* a bracket.
      		$steps = $this->_bracketExplode( '/', $xQuery );
			
      		if ( $bDebugThisFunction )
			{
				echo "<hr>Split the path '$xQuery' at every slash *outside* a bracket.\n ";
				print_r( $steps );
			}
			
      		// Check whether the first element is empty.
      		if ( empty( $steps[0] ) )
			{
        		// Remove the first and empty element. It's a starting  '//'.
        		array_shift( $steps );
      		}
      
	  		// Start to evaluate the steps.
      		$nodes = $this->_evaluateStep( $contextPath, $steps );
      
	  		// Remove duplicated nodes.
      		$nodes = array_unique( $nodes );
      
	  		// Add the nodes to the result set.
      		$result = array_merge( $result, $nodes );
    	}

    	if ( $bDebugThisFunction )
      		$this->_closeDebugFunction( $aStartTime, $result );
    
    	return $result;
  	}
  
	/**
	 * Evaluate a step from a XPathQuery expression at a specific contextPath.
	 *
	 * Steps are the arguments of a XPathQuery when divided by a '/'. A contextPath is a 
	 * absolute XPath (or vector of XPaths) to a starting node(s) from which the step should 
	 * be evaluated.
	 *
	 * @param  $contextPath  (mixed) String or vector.  A absolute XPath OR vector of XPaths 
	 *                               (see above)
	 * @param  $steps        (array) Vector containing the remaining steps of the current 
	 *                               XPathQuery expression.
	 * @return               (array) Vector of absolute XPath's as a result of the step 
	 *                               evaluation.
	 * @see    evaluate()
	 */
	function _evaluateStep( $contextPath, $steps )
	{
    	// If you are having difficulty using this function.  Then set this to true and 
    	// you'll get diagnostic info displayed to the output.
    	$bDebugThisFunction = false;
    
		if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( __LINE__ . ":_evaluateStep(contextPath:[$contextPath], steps:[$steps])" );
      
	  		if ( is_array( $contextPath ) )
			{
        		echo "Context:\n";
        		print_r( $contextPath );
      		}
			else
			{
        		echo "Context: $contextPath\n";
      		}
      
	  		echo "Steps: ";
      		print_r( $steps );
      		echo "<hr>\n";
    	}
    
		$xPathSet = array(); // Create an empty array for saving the abs. XPath's found.
    
		// We may have an "array" of one context.  If so convert it from 
    	// array to single string.  Often, this function will be called with
    	// a /Path1[1]/Path[3]/Path[2] sytle predicate.
    	if ( is_array( $contextPath ) && ( count( $contextPath ) == 1 ) )
			$contextPath = $contextPath[0];
    
		// Check whether the context is an array of contexts.
    	if ( is_array( $contextPath ) )
		{
      		// Run through the array.
      		$size = sizeOf( $contextPath );
      
	  		for ( $i = 0; $i < $size; $i++ )
			{
        		if ( $bDebugThisFunction )
					echo __LINE__.":Evaluating step for the {$contextPath[$i]} context...\n";        
        
				// Call this method for this single path.
        		$xPathSet = array_merge( $xPathSet, $this->_evaluateStep( $contextPath[$i], $steps ) );
      		}
    	}
		else
		{
      		$contextPaths = array(); // Create an array to save the new contexts.
      		$step = trim( array_shift( $steps ) ); // Get this step.
      
	  		if ( $bDebugThisFunction )
				echo __LINE__.":Evaluating step $step\n";
      
      		$axis = $this->_getAxis( $step, $contextPath ); // Get the axis of the current step.
      
	  		if ( $bDebugThisFunction )
			{ 
				echo __LINE__ . ":Axis of step is:\n"; 
				print_r( $axis ); 
				echo "\n";
			}
      
			// Check whether it's a function.
			if ( $axis['axis'] == 'function' ) 
			{
        		// Check whether an array was return by the function.
        		if ( is_array( $axis['node-test'] ) )
				{
					// Add the results to the list of contexts.
          			$contextPaths = array_merge( $contextPaths, $axis['node-test'] );
        		}
				else
				{
					// Add the result to the list of contexts.
          			$contextPaths[] = $axis['node-test'];
        		}
      		}
			else
			{
				// Create the name of the method.
        		$method = '_handleAxis_' . $axis['axis'];
      
        		// Check whether the axis handler is defined. If not display an error message.
        		if ( !method_exists( &$this, $method ) )
					$this->_displayError( 'While parsing an XPath expression, the axis ' . $axis['axis'] . ' could not be handled, because this version does not support this axis.', __LINE__, __FILE__ );
        
        		if ( $bDebugThisFunction )
					echo __LINE__.":Calling user method $method\n";        
        
				// Perform an axis action.
        		$contextPaths = $this->$method( $axis, $contextPath );
				
        		if ( $bDebugThisFunction )
				{
					echo __LINE__ . ":We found these contexts from this step:\n";
					print_r( $contextPaths );
					echo "\n";
				}
        
        		// Check whether there are predicates.
        		if ( count( $axis['predicate'] ) > 0 )
				{
          			if ( $bDebugThisFunction )
						echo __LINE__.":Filtering contexts by predicate...\n";        
          
          			// Check whether each node fits the predicates.
          			$contextPaths = $this->_checkPredicates( $contextPaths, $axis['predicate'], $axis['node-test'] );
        		}
      		}
      
      		// Check whether there are more steps left.
      		if ( count( $steps ) > 0 )
			{
        		if ( $bDebugThisFunction )
					echo __LINE__.":Evaluating next step given the context of the first step...\n";        
        
				// Continue the evaluation of the next steps.
        		$xPathSet = $this->_evaluateStep( $contextPaths, $steps );
      		}
			else
			{
        		$xPathSet = $contextPaths; // Save the found contexts.
      		}
    	}
    
    	if ( $bDebugThisFunction )
			$this->_closeDebugFunction( $aStartTime, $xPathSet );
    
    	return $xPathSet;
  	}
  
	/**
	 * Checks whether a node matches predicates.
	 *
	 * This method checks whether a list of nodes passed to this method match
	 * a given list of predicates. 
	 *
	 * @param  $xPathSet   (array)  Array of full paths of all nodes to be tested.
	 * @param  $predicates (array)  Array of predicates to use.
	 * @param  $nodeTest   (string) The node test used to filter the node set.  Passed 
	 *                              to evaluatePredicate()
	 * @return             (array)  Vector of absolute XPath's that match the given predicates.
	 * @see    _evaluateStep()
	 */
	function _checkPredicates( $xPathSet, $predicates, $nodeTest )
	{
    	// If you are having difficulty using this function.  Then set this to true and 
    	// you'll get diagnostic info displayed to the output.
    	$bDebugThisFunction = false;
    
		if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( "_checkPredicates(Nodes:[$xPathSet], Predicates:[$predicates])" );
      		echo "XPathSet:";
      		print_r( $xPathSet );
      		echo "Predicates:";
      		print_r( $predicates );
      		echo "<hr>";
    	}
    
		// Create an empty set of nodes.
    	$result = array();
    
		// Run through all nodes.
		$nSize = sizeOf( $xPathSet );
		
    	for ( $i = 0; $i < $nSize; $i++ )
		{
      		$xPath = $xPathSet[$i];
      
	  		// Create a variable whether to add this node to the node-set.
      		$add = true;
      
      		// Run through all predicates.
      		$pSize = sizeOf( $predicates );
			
      		for ( $j = 0; $j < $pSize; $j++ )
			{
        		$predicate = $predicates[$j]; 
        
				if ( $bDebugThisFunction )
					echo "Evaluating predicate \"$predicate\"\n";
        
				// Check whether the predicate is just an number.
        		if ( preg_match( '/^\d+$/', $predicate ) )
				{
          			if ( $bDebugThisFunction )
						echo "Taking short cut and calling _handleFunction_position() directly.\n";
          
		  			// Take a short cut. If it is just a position, then call 
					// _handleFunction_position() directly.  70% of the
					// time this will be the case. ## N.S
					$check = (bool)( $predicate == $this->_handleFunction_position( $xPath, '', $nodeTest ) );
          
		  			// Enhance the predicate.
					// $predicate .= "=position()";
				}
				else
				{                
          			// Else do the predicate check the long and thorough way.
          			$check = $this->_evaluatePredicate( $xPath, $predicate, $nodeTest );
        		}
        
				// Check whether it's a string.
        		if ( is_string( $check ) && ( ( $check == '' ) || ( $check == $predicate ) ) )
				{
					// Set the result to false.
          			$check = false;
        		} 
        		else if ( is_bool( $check ) ) 
				{
					// 0 and 1 are both bools and ints.  We need to capture the bools
					// as they might have been the intended result
        		}
				else
				{
					// Check whether it's an integer.
          			if ( is_int( $check ) )
					{
            			// Check whether it's the current position.
            			$check = (bool)( $check == $this->_handleFunction_position( $xPath, '', $nodeTest ) );
          			}
        		}
        
				if ( $bDebugThisFunction )
					echo "Node $xPath matches predicate $predicate: " . ( $check? "TRUE" : "FALSE" ) ."\n";
        		
				// Check whether the predicate is OK for this node.
        		$add = $add && $check;
      		}
       
      		// Check whether to add this node to the node-set.
      		if ( $add )
			{
				// Add the node to the node-set.
        		$result[] = $xPath;
      		}
			
      		if ( $bDebugThisFunction )
				echo "Node $xPath matches: " . ( $add? "TRUE" : "FALSE" ) ."\n\n";        
    	}
    
		if ( $bDebugThisFunction )
      		$this->_closeDebugFunction( $aStartTime, $result );
    
		return $result;
  	}
  
	/**
	 * Evaluates an XPath function
	 *
	 * This method evaluates a given XPath function with its arguments on a
	 * specific node of the document.
	 *
	 * @param  $function      (string) Name of the function to be evaluated.
	 * @param  $arguments     (string) String containing the arguments being
	 *                                 passed to the function.
	 * @param  $absoluteXPath (string) Full path to the document node on which the
	 *                                 function should be evaluated.
	 * @return                (mixed)  This method returns the result of the evaluation of
	 *                                 the function. Depending on the function the type of the 
	 *                                 return value can be different.
	 * @see    evaluate()
	 */
	function _evaluateFunction( $function, $arguments, $absoluteXPath, $nodeTest = '' )
	{
    	// If you are having difficulty using this function.  Then set this to true and 
    	// you'll get diagnostic info displayed to the output.
    	$bDebugThisFunction = false;
    
		if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( "_evaluateFunction(Function:[$function], Arguments:[$arguments], node:[$absoluteXPath], nodeTest:[$nodeTest])" );
      
	  		if ( is_array( $arguments ) )
			{
        		echo "Arguments:\n";
        		print_r( $arguments );
      		}
			else
			{
        		echo "Arguments: $arguments\n";
      		}
      
	  		echo "<hr>\n";
    	}

    	// Remove whitespaces.
    	$function  = trim( $function  );
    	$arguments = trim( $arguments );
		
    	// Create the name of the function handling function.
    	$method = '_handleFunction_' . $function;
    
    	// Check whether the function handling function is available.
    	if ( !method_exists( &$this, $method ) )
		{
      		// Display an error message.
      		$this->_displayError( "While parsing an XPath expression, " .
        		"the function \"$function\" could not be handled, because this " .
        		"version does not support this function.", __LINE__, __FILE__
			);
		}
    
		if ( $bDebugThisFunction )
			echo "Calling function $method($absoluteXPath, $arguments)\n"; 
    
    	// Return the result of the function.
    	$result = $this->$method( $absoluteXPath, $arguments, $nodeTest );
    
    	// Return the nodes found.
    	if ( $bDebugThisFunction )
      		$this->_closeDebugFunction( $aStartTime, $result );
    
    	// Return the result.
    	return $result;
  	}
  
	/**
	 * Evaluates a predicate on a node.
	 *
	 * This method tries to evaluate a predicate on a given node.
	 *
	 * @param  $absoluteXPath (string) Full path of the node on which the predicate
	 *                                 should be evaluated.
	 * @param  $predicate     (string) String containing the predicate expression
	 *                                 to be evaluated.
	 * @param  $nodeTest      (string) The node test used to filter the node set.
	 * @return                (mixed)  This method is called recursively. The first call 
	 *                                 should return a boolean value, whether the node 
	 *                                 matches the predicateor not. Any call to the 
	 *                                 method being made during the recursion
	 *                                 may also return other types for further processing.
	 * @see    evaluate()
	 */
	function _evaluatePredicate( $absoluteXPath, $predicate, $nodeTest )
	{
    	// If you are having difficulty using this function. Then set this to true and 
    	// you'll get diagnostic info displayed to the output.
    	$bDebugThisFunction = false;
    
		if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( "_evaluatePredicate" );
			
      		echo "Node: [$absoluteXPath]\n";
      		echo "Predicate: [$predicate]\n";
      		echo "Node Test: [$nodeTest]\n";
      		echo "<hr>";
    	}
    
		// try-block
    	do
		{
      		// Numpty check
      		if ( !is_string( $predicate ) )
			{
        		// Display an error message.
        		$this->_displayError( "While parsing an XPath expression " .
					"there was an error in the following predicate, " .
					"because it was not a string. It was a '" . $predicate . "'", __LINE__, __FILE__
				);
        
				$result = false;
        		break; // try-block
      		}
      
      		$predicate = trim( $predicate );
      
	  		// Numpty check. If they give us an empty string, then this is an error.
      		if ( $predicate === '' )
			{ 
				// Display an error message.
				$this->_displayError( "While parsing an XPath expression " . 
					"there was an error in the predicate " .
					"because it was the null string. If you wish to seach ".
					"for the empty string, you must use ''.", __LINE__, __FILE__
				);
        
				$result = false;
        		break; // try-block
      		}
      
      		// Quick ways out.
      		// If it is a literal string, then we return the literal string.
      		$stringDelimiterMismatsh = 0;
      
	  		if ( preg_match( ':^"(.*)"$:', $predicate, $regs ) )
			{
        		$result = $regs[1];
        		$stringDelimiterMismatsh = strpos( ' ' . $result, '"' );
        
				if ( $bDebugThisFunction )
					echo "Predicate is literal: \"{$result}\"\n";        
      		}
			else if ( preg_match( ":^'(.*)'$:", $predicate, $regs ) )
			{
        		$result = $regs[1];
        		$stringDelimiterMismatsh = strpos( ' ' . $result, "'" );
				
				if ( $bDebugThisFunction )
					echo "Predicate is literal '{$result}'\n";        
      		}
      
	  		if ( isSet( $result ) )
				break; // try-block
      
      		if ( $stringDelimiterMismatsh > 0 )
			{
        		$this->_displayError( "While parsing an XPath expression there was an string delimiter miss match at pos [{$stringDelimiterMismatsh}] in the predicate string '{$predicate}'.", __LINE__, __FILE__ );
        		$result = false;
        		break; // try-block
      		}
    
      		// Check whether the predicate is just a digit.
      		if ( is_numeric( $predicate ) )
			{
        		// Return the value of the digit.
        		$result = doubleval( $predicate );
				
        		if ( $bDebugThisFunction )
					echo "Predicate is double: '{$result}'\n";        
        
				break; // try-block
      		}
      
      		// Check for operators.
      		// Set the default position and the type of the operator.
      		$position = 0;
      		$operator = '';
      
      		// Run through all operators and try to find one.
      		$opSize = sizeOf( $this->operators );
      
	  		for ( $i = 0; $i < $opSize; $i++ )
			{
        		if ( $position > 0 )
					break;
        
				$operator = $this->operators[$i];
        
				// Quickcheck. If not present don't wast time searching 'the hard way'.
        		if ( strpos( $predicate, $operator ) === false )
					continue;
        
				// Special check
        		$position = $this->_searchString( $predicate, $operator );
				
        		// Check whether a operator was found.
        		if ( $position <= 0 )
					continue;
        
				// Check whether it's the equal operator.
        		if ( $operator == '=' )
				{
          			// Also look for other operators containing the equal sign.
          			switch ( $predicate[$position - 1] )
					{
            			case '<': 
              				$position--;
              				$operator = '<=';
              				
							break;
            
						case '>': 
              				$position--;
              				$operator = '>=';
              
			  				break;
            
						case '!': 
              				$position--;
              				$operator = '!=';
              
			  				break;
            
						default:
							;
          			}
        		}
        
				if ( $operator == '*' )
				{
          			// Get some substrings.
          			$character = substr( $predicate, $position -  1,  1 );
          			$attribute = substr( $predicate, $position - 11, 11 );
        
          			// Check whether it's an attribute selection.
          			if ( ( $character == '@' ) || ( $attribute == 'attribute::' ) )
					{
            			// Don't use the operator.
            			$operator = '';
            			$position = -1;
          			}
        		}
      		}
      
			// Check whether an operator was found.        
      		if ( $position > 0 )
			{
        		if ( $bDebugThisFunction )
					echo "\nPredicate operator is a [$operator] at pos '$position'";        
        
				// Get the left and the right part of the expression.
        		$left_predicate  = trim( substr( $predicate, 0, $position ) );
        		$right_predicate = trim( substr( $predicate, $position + strlen( $operator ) ) );
				
        		if ( $bDebugThisFunction )
					echo "\nLEFT:[$left_predicate]  oper:[$operator]  RIGHT:[$right_predicate]";        
      
				// Remove whitespaces.
				$left_predicate  = trim( $left_predicate  );
				$right_predicate = trim( $right_predicate );
				
        		// Evaluate the left and the right part.
        		if ( $bDebugThisFunction )
					echo "\nEvaluating LEFT:[$left_predicate]";
        
				$left = $this->_evaluatePredicate( $absoluteXPath, $left_predicate, $nodeTest );
        
				if ( $bDebugThisFunction )
					echo "$left_predicate evals as: $left - ";
        
				// Only evaluate the right part if we need to.
        		$right = false;
        
				if ( !$left && ( $operator == ' and ' ) )
				{
          			if ( $bDebugThisFunction )
						echo "\nNo point in evaluating the right predicate: [$right_predicate]";
        		}
				else
				{
          			if ( $bDebugThisFunction )
						echo "\nEvaluating RIGHT:[$right_predicate]";
          
		  			$right = $this->_evaluatePredicate( $absoluteXPath, $right_predicate, $nodeTest );
          
		  			if ( $bDebugThisFunction )
						echo "$right_predicate evals as: $right \n";
        		}
        
				// Check the kind of operator.
        		$b_result = false;
        
				switch ( $operator )
				{
          			case ' or ':	// Return the two results connected by an 'or'.
						$b_result = (bool)( $left || $right );
						break;
					
					case ' and ':	// Return the two results connected by an 'and'.
            			$b_result = (bool)( $left && $right );
            			break;
          
		  			case '=':		// Compare the two results.
            			$b_result = (bool)( $left == $right ); 
            			break;                    
          
		  			case '!=':		// Check whether the two results are not equal.
            			$b_result = (bool)( $left != $right );
            			break;                    
          
		  			case '<=':		// Compare the two results.
            			$b_result = (bool)( $left <= $right );
            			break;                    
          
		  			case '<':		// Compare the two results.
            			$b_result = (bool)( $left < $right );
            			break;                
          
		  			case '>=':		// Compare the two results.
            			$b_result = (bool)( $left >= $right );
            			break;                    
          
		  			case '>':		// Compare the two results.
            			$b_result = (bool)( $left > $right );
            			break;                    
          
		  			case '+':		// Return the result by adding one result to the other.
            			$b_result = $left + $right;
            			break;                    
          
		  			case '-':		// Return the result by decrease one result by the other.
            			$b_result = $left - $right;
            			break;                    
          
		  			case '*':		// Return a multiplication of the two results.
            			$b_result =  $left * $right;
            			break;                    
          
		  			case ' div ':	// Return a division of the two results.
            			if ( $right == 0 )
						{
              				// Display an error message.
              				$this->_displayError( 'While parsing an XPath predicate, a error due a division by zero occured.', __LINE__, __FILE__ );
            			}
						else
						{
              				// Return the result of the division.
              				$b_result = $left / $right;
            			}
            
						break;
          
		  			case ' mod ':	// Return a modulo of the two results.
            			$b_result = $left % $right;
            			break;                    
        		}
        
				$result = $b_result;
      		}
      
	  		if ( isSet( $result ) )
				break; // try-block
 
      		// Check for functions.
      		// Check whether the predicate is a function.
      		// do not catch the text() node, which looks like a function in its pattern
      		if ( preg_match( ':\(:U', $predicate ) && !preg_match( ":text\(\)(\[\d*\])?$:", $predicate ) )
			{
        		// Get the position of the first bracket.
        		$start = strpos( $predicate, '(' );
        
				// If we search for the right bracket from the end of the string, we can support nested function calls.  
        		$end = strrpos( $predicate, ')' );
      
        		// Get everything before, between and after the brackets.
        		$before  = substr( $predicate, 0, $start );
        		$between = substr( $predicate, $start + 1, $end - $start - 1 );
        		$after   = substr( $predicate, $end + 1 );
        
        		// Trim each string.
        		$before  = trim( $before  );
        		$between = trim( $between );
        		$after   = trim( $after   );
        
        		if ( $bDebugThisFunction )
					echo "\nPredicate is function \"$before\"";        
        
				// Check whether there's something after the bracket.
        		if ( !empty( $after ) )
				{
          			// Display an error message.
          			$this->_displayError( 'While parsing an XPath expression there was an error in the predicate ' .
            			str_replace( $predicate, '<b>' . $predicate . '</b>', $this->currentXpathQuery ) .
            			'. After a closing bracket there was something unknown: "' . $after . '"', __LINE__, __FILE__
					);
        		}
        
        		// Check whether it's a function.
        		if ( empty( $before ) && empty( $after ) )
				{
          			// Evaluate the content of the brackets.
          			$result = $this->_evaluatePredicate( $absoluteXPath, $between, $nodeTest );
        		}
        		else if ( in_array( $before, $this->functions ) )
				{
          			// Return the evaluated function.
          			$result = $this->_evaluateFunction( $before, $between, $absoluteXPath, $nodeTest );
        		} 
        		else
				{
          			// Display an error message.
          			$this->_displayError( 'While parsing a predicate in an XPath expression, a function '.
            			str_replace( $before, '<b>' . $before . '</b>', $this->currentXpathQuery ) . 
            			' was found, which is not yet supported by the parser.', __LINE__, __FILE__
					);
        		}
      		}
      	
			if ( isSet( $result ) )
				break; // try-block
      
      		// Else it must just be an XPath expression.
      		// Check whether it's an XPath expression.
      		if ( $bDebugThisFunction )
				echo "\nPredicate is XPath expression that is to be evaluated.";
      
	  		$tmpXpathSet = $this->_internalEvaluate( $predicate, $absoluteXPath );
      
	  		if ( $bDebugThisFunction )
			{
				echo "\nResult of XPath expression"; 
				print_r( $tmpXpathSet );
			}
      	
			if ( count( $tmpXpathSet ) > 0 )
			{
        		// Convert the array.
        		$tmpXpathSet = explode( "|", implode( "|", $tmpXpathSet ) );
        
				// Get the value of the first result (which means we want to concat all the text...unless
        		// a specific text() node has been given, and it will switch off to substringData
        		$result = $this->wholeText( $tmpXpathSet[0] );            
      		}
		} while ( false );
    
    	// Else no content so return the empty string.
    	if ( !isSet( $result ) )
			$result = '';
    
    	if ( $bDebugThisFunction )
		{
      		echo "<pre>";
      		var_dump( $result );
      		echo "</pre>";
      		$this->_closeDebugFunction( $aStartTime, $result );
    	}
    
		return $result;
  	}
  
  	/**
   	 * Checks whether a node matches a node-test.
   	 *
   	 * This method checks whether a node in the document matches a given node-test.
   	 *
   	 * @param  $contextPath (string)  Full xpath of the node, which should be tested for 
   	 *                                matching the node-test.
   	 * @param  $nodeTest    (string)  String containing the node-test for the node.
   	 * @return              (boolean) This method returns true if the node matches the 
   	 *                                node-test, otherwise false.
   	 * @see    evaluate()
   	 */
	function _checkNodeTest( $contextPath, $nodeTest )
	{
    	if ( $nodeTest == '*' )
		{
			// Add this node to the node-set.
      		return true;
    	}
		// Check whether it's a function.
    	else if ( preg_match( '/\(/U', $nodeTest ) )
		{
      		// Get the type of function to use.
      		$function = $this->_prestr( $nodeTest, '(' );
      
	  		// Check whether the node fits the method.
      		switch ( $function )
			{
        		case 'node':   // Add this node to the list of nodes.
          			return true;
        		
				case 'text':   // Check whether the node has some text.
          			$tmp = implode( '', $this->nodeIndex[$contextPath]['textParts'] );
          
		  			if ( !empty( $tmp ) )
					{
						// Add this node to the list of nodes.
            			return true;
          			}
          
		  			break;

				/**
				NOT supported (yet?)          
        
				case 'comment':  // Check whether the node has some comment.
          			if ( !empty( $this->nodeIndex[$contextPath]['comment'] ) )
					{
						// Add this node to the list of nodes.
            			return true;
          			}
          
		  			break;
        
				case 'processing-instruction':
          			$literal = $this->_afterstr( $axis['node-test'], '('   ); // Get the literal argument.
          			$literal = substr( $literal, 0, strlen( $literal ) - 1 ); // Cut the literal.
          
          			// Check whether a literal was given.
          			if ( !empty( $literal ) )
					{
            			// Check whether the node's processing instructions are matching the literals given.
            			if ( $this->nodeIndex[$context]['processing-instructions'] == $literal )
						{
							// Add this node to the node-set.
              				return true;
            			}
          			}
					else
					{
            			// Check whether the node has processing instructions.
            			if ( !empty( $this->nodeIndex[$contextPath]['processing-instructions'] ) )
						{
							// Add this node to the node-set.
              				return true;
            			}
          			}
          
		  			break;
				*/
        
				default:  // Display an error message.
          			$this->_displayError(
						'While parsing an XPath expression there was an undefined function called "' .
             			str_replace( $function, '<b>' . $function . '</b>', $this->currentXpathQuery ) .'"', __LINE__, __FILE__
					);
      		}
    	}
    	else if ( preg_match( '/^[a-zA-Z0-9\-_]+/', $nodeTest ) )
		{
      		// Check whether the node-test can be fulfilled.
      		if ( !strcmp( $this->nodeIndex[$contextPath]['name'], $nodeTest ) )
			{
				// Add this node to the node-set.
        		return true;
      		}
    	}
		// Display an error message.
    	else
		{
      		$this->_displayError(
				"While parsing the XPath expression \"{$this->currentXpathQuery}\" ".
        		"an empty and therefore invalid node-test has been found.", __LINE__, __FILE__
			);
    	}
    
		// Don't add this context.
		return false;
	}

	/**
	 * Retrieves axis information from an XPath expression step.
	 *
	 * This method tries to extract the name of the axis and its node-test
	 * from a given step of an XPath expression at a given node.
	 *
	 * @param  $step     (string) String containing a step of an XPath expression.
	 * @param  $nodePath (string) Full document path of the node on which the step is executed.
	 * @return           (array)  Contains information about the axis found in the step.
	 * @see    _evaluateStep()
	 */
	function _getAxis( $step, $nodePath )
	{
    	// Create an array to save the axis information.
    	$axis = array(
      		'axis'      => '',
      		'node-test' => '',
      		'predicate' => array()
    	);
    
		// parse block
    	do
		{
      		$parseBlock = 1;
      
      		// Check whether the step is empty or only self. 
      		if ( empty( $step ) || ( $step == '.' ) || ( $step == 'current()' ) )
			{
        		// Set it to the default value.
        		$step = '.';
        		$axis['axis'] = 'self';
        		$axis['node-test'] = '*';
        
				break $parseBlock;
      		}
      
      		// Check whether is an abbreviated syntax.
      		if ( $step == '*' )
			{
        		// Use the child axis and select all children.
        		$axis['axis'] = 'child';
        		$axis['node-test'] = '*';
        
				break $parseBlock;
      		}
      
			// Check whether it's all wrapped in a function.  will be like count(.*) where .* is anything
			// text() will try to be matched here, so just explicitly ignore it
			$regex = ":(.*)\s*\((.*)\)$:U";
      
	  		if ( preg_match( $regex, $step, $match ) && $step != "text()" )
			{
        		$function = $match[1];
        		$data     = $match[2];
        
				// Save the evaluated function.
				if ( in_array( $function, $this->functions ) )
				{
          			$axis['axis'] = 'function';
          			$axis['node-test'] = $this->_evaluateFunction( $function, $data, $nodePath );
        		} 
       	 		else
				{
          			// Use the child axis and a function.
          			$axis['axis'] = 'child';
          			$axis['node-test'] = $step;
        		}
        
				break $parseBlock;
      		}
      
      		// Check whether there are predicates and add the predicate to the list 
      		// of predicates without []. Get contents of every [] found.
      		$regex = '/\[(.*)\]/';
      		preg_match_all( $regex, $step, $regs );
			 
      		if ( !empty( $regs[1] ) )
			{
        		$axis['predicate'] = $regs[1];
        
				// Reduce the step.
        		$step = preg_replace( $regex, "", $step ); // $this->_prestr( $step, '[' );
      		}
      
      		// Check whether the axis is given in plain text.
      		if ( $this->_searchString( $step, '::' ) > -1 )
			{
        		// Split the step to extract axis and node-test.
        		$axis['axis'] = $this->_prestr( $step, '::' );
        		$axis['node-test'] = $this->_afterstr( $step, '::' );
				
        		if ( !empty( $this->parseOptions[XML_OPTION_CASE_FOLDING] ) )
				{
          			// Case in-sensitive
          			$axis['node-test'] = strtoupper( $axis['node-test'] );
        		}
        
				break $parseBlock;
      		}
      
      		if ( $step[0] == '@' )
			{
        		// Use the attribute axis and select the attribute.
        		$axis['axis'] = 'attribute';
        		$axis['node-test'] = substr( $step, 1 );
				
        		if ( !empty( $this->parseOptions[XML_OPTION_CASE_FOLDING] ) )
				{
          			// Case in-sensitive
          			$axis['node-test'] = strtoupper( $axis['node-test'] );
        		}
        
				break $parseBlock;
      		}
      
      		if ( eregi( '\]$', $step ) )
			{
        		// Use the child axis and select a position.
        		$axis['axis'] = 'child';
        		$axis['node-test'] = substr( $step, strpos( $step, '[' ) );
        
				break $parseBlock;
      		}
      
      		if ( $step == '..' )
			{
        		// Select the parent axis.
        		$axis['axis'] = 'parent';
        		$axis['node-test'] = '*';
        
				break $parseBlock;
      		}
      
      		if ( preg_match( '/^[a-zA-Z0-9\-_]+$/', $step ) )
			{
        		// Select the child axis and the child.
        		$axis['axis'] = 'child';
        		$axis['node-test'] = $step;
        
				if ( !empty( $this->parseOptions[XML_OPTION_CASE_FOLDING] ) )
				{
          			// Case in-sensitive
          			$axis['node-test'] = strtoupper( $axis['node-test'] );
        		}
        
				break $parseBlock;
      		} 
      
      		if ( $step == "text()" )
			{
        		// Handle the text node.
        		$axis["axis"]      = "child";
        		$axis["node-test"] = "cdata";
        
				break $parseBlock;
      		}
      
      		// Default will be to fall back to using the child axis and a name.
      		$axis['axis']      = 'child';
      		$axis['node-test'] = $step;
      
	  		if ( !empty( $this->parseOptions[XML_OPTION_CASE_FOLDING] ) )
			{
        		// Case in-sensitive
        		$axis['node-test'] = strtoupper( $axis['node-test'] );
      		}
    	} while ( false );
    
		// Check whether it's a valid axis.
		if ( !in_array( $axis['axis'], array_merge( $this->axes, array( 'function' ) ) ) )
		{
      		// Display an error message.
      		$this->_displayError(
				'While parsing an XPath expression, in the step ' .
        		str_replace( $step, '<b>' . $step . '</b>', $this->currentXpathQuery ) .
        		' the invalid axis ' . $axis['axis'] . ' was found.', __LINE__, __FILE__, false
			);
    	}
    
		// Return the axis information.
    	return $axis;
	}
   
	/**
	 * Handles the XPath child axis.
	 *
	 * This method handles the XPath child axis.  It essentially filters out the
	 * children to match the name specified after the '/'.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should 
	 *                               be processed.
	 * @return              (array)  A vector containing all nodes that were found, during 
	 *                               the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_child( $axis, $contextPath )
	{
		// Create an empty node-set to hold the results of the child matches
    	$xPathSet = array();
    
		if ( $axis["node-test"] == "cdata" )
		{
      		if ( !isSet( $this->nodeIndex[$contextPath]['textParts'] ) )
				return '';
      
	  		$tSize = sizeOf( $this->nodeIndex[$contextPath]['textParts'] );
      
	  		for ( $i = 1; $i <= $tSize; $i++ ) 
        		$xPathSet[] = $contextPath . '/text()[' . $i . ']';
    	}
    	else
		{
      		// Get a list of all children.
      		$allChildren = $this->nodeIndex[$contextPath]['childNodes'];
      
      		// Run through all children in the order they where set.
      		$cSize = sizeOf( $allChildren );
			
      		for ( $i = 0; $i < $cSize; $i++ )
			{
        		$childPath = $contextPath . '/' . $allChildren[$i]['name'] . '[' . $allChildren[$i]['contextPos'] . ']';
        
				// node test check
				if ( $this->_checkNodeTest( $childPath, $axis['node-test'] ) )
				{
					// Add the child to the node-set.
          			$xPathSet[] = $childPath;
        		}
      		}
    	}
    
		// Return the nodeset.
		return $xPathSet;
	}
  
	/**
	 * Handles the XPath parent axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the 
	 *                               evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_parent( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Check whether the parent matches the node-test.
    	$parentPath = $this->getParentXPath( $contextPath );
    
		if ( $this->_checkNodeTest( $parentPath, $axis['node-test'] ) )
		{
			// Add this node to the list of nodes.
			$xPathSet[] = $parentPath;
    	}
    
		// Return the nodeset.
		return $xPathSet;
	}
  
	/**
	 * Handles the XPath attribute axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_attribute( $axis, $contextPath )
	{
    	$xPathSet = array(); // Create an empty node-set.
    
    	// Check whether all nodes should be selected.
    	$nodeAttr = $this->nodeIndex[$contextPath]['attributes'];
    
		if ( $axis['node-test'] == '*' )
		{
			// Run through the attributes.
			foreach ( $nodeAttr as $key => $dummy )
			{
				// Add this node to the node-set.
        		$xPathSet[] = $contextPath . '/attribute::' . $key;
      		}
    	}
    	else if ( !empty( $nodeAttr[$axis['node-test']] ) )
		{
			// Add this node to the node-set.
      		$xPathSet[] = $contextPath . '/attribute::'. $axis['node-test'];
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
   
	/**
	 * Handles the XPath self axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_self( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Check whether the context match the node-test.
    	if ( $this->_checkNodeTest( $contextPath, $axis['node-test'] ) )
		{
			// Add this node to the node-set.
      		$xPathSet[] = $contextPath;
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath descendant axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
  	function _handleAxis_descendant( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Get a list of all children.
    	$allChildren = $this->nodeIndex[$contextPath]['childNodes'];
    
    	// Run through all children in the order they where set.
    	$cSize = sizeOf( $allChildren );
    
		for ( $i = 0; $i < $cSize; $i++ )
		{
      		$childPath = $allChildren[$i]['xpath'];
      
	  		// Check whether the child matches the node-test.
      		if ( $this->_checkNodeTest( $childPath, $axis['node-test'] ) )
			{
				// Add the child to the list of nodes.
        		$xPathSet[] = $childPath;
      		}
      
	  		// Recurse to the next level.
      		$xPathSet = array_merge( $xPathSet, $this->_handleAxis_descendant( $axis, $childPath ) );
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath ancestor axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_ancestor( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
        
		// Get the parent of the current node.
    	$parentPath = $this->getParentXPath( $contextPath );
    
    	// Check whether the parent isn't super-root.
    	if ( !empty( $parentPath ) )
		{
      		// Check whether the parent matches the node-test.
      		if ( $this->_checkNodeTest( $parentPath, $axis['node-test'] ) )
			{
				// Add the parent to the list of nodes.
        		$xPathSet[] = $parentPath;
      		}
      
	  		// Handle all other ancestors.
      		$xPathSet = array_merge( $xPathSet, $this->_handleAxis_ancestor( $axis, $parentPath ) );
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath namespace axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_namespace( $axis, $contextPath )
	{
    	$this->_displayError( "The axis 'namespace is not suported'", __LINE__, __FILE__, false );
  	}
  
	/**
	 * Handles the XPath following axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_following( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
		// try-block
    	do
		{
      		$node     = $this->nodeIndex[$contextPath]; // Get the current node
      		$position = $node['pos']; // Get the current tree position.
      		$parent   = $node['parentNode'];
      		
			// Check if there is a following sibling at all; if not end.
      		if ( $position >= sizeOf( $parent['childNodes'] ) )
				break; // try-block
      
	  		// Build the starting abs. XPath
      		$startXPath = $parent['childNodes'][$position + 1]['xpath'];
      
	  		// Run through all nodes of the document.
      		$nodeKeys = array_keys( $this->nodeIndex );
      		$nodeSize = sizeOf( $nodeKeys );
			
      		for ( $k = 0; $k < $nodeSize; $k++ )
			{
        		if ( $nodeKeys[$k] == $startXPath )
					break; // Check whether this is the starting abs. XPath
      		}
      
	  		for (; $k < $nodeSize; $k++ )
			{
        		// Check whether the node fits the node-test.
        		if ( $this->_checkNodeTest( $nodeKeys[$k], $axis['node-test'] ) )
				{
					// Add the node to the list of nodes.
          			$xPathSet[] = $nodeKeys[$k];
        		}
      		}
    	} while ( false );

		// Return the nodeset.
		return $xPathSet;
	}
  
	/**
	 * Handles the XPath preceding axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_preceding( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Run through all nodes of the document.
    	foreach ( $this->nodeIndex as $xPath => $dummy )
		{
			// skip super-Root
      		if ( empty( $xPath ) )
				continue;
      
			// Check whether this is the context node.
			if ( $xPath == $contextPath ) 
			{
				// After this we won't look for more nodes.
        		break;
      		}
			
      		if ( !strncmp( $xPath, $contextPath, strLen( $xPath ) ) )
        		continue;
      
      		// Check whether the node fits the node-test.
      		if ( $this->_checkNodeTest( $xPath, $axis['node-test'] ) )
			{
				// Add the node to the list of nodes.
        		$xPathSet[] = $xPath;
      		}
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath following-sibling axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_following_sibling( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Get all children from the parent.
    	$siblings = $this->_handleAxis_child( $axis, $this->getParentXPath( $contextPath ) );
		
    	// Create a flag whether the context node was already found.
    	$found = false;
    
    	// Run through all siblings.
    	$size = sizeOf( $siblings );
    
		for ( $i = 0; $i < $size; $i++ )
		{
      		$sibling = $siblings[$i];
      
      		// Check whether the context node was already found.
      		if ( $found )
			{
        		// Check whether the sibling matches the node-test.
        		if ( $this->_checkNodeTest( $sibling, $axis['node-test'] ) )
				{
					// Add the sibling to the list of nodes.
          			$xPathSet[] = $sibling;
        		}
      		}
			
			// Check if we reached *this* context node.
			if ( $sibling == $contextPath )
			{
				// Continue looking for other siblings.
        		$found = true;
      		}
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath preceding-sibling axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_preceding_sibling( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Get all children from the parent.
    	$siblings = $this->_handleAxis_child( $axis, $this->getParentXPath( $contextPath ) );
    
    	// Run through all siblings.
    	$size = sizeOf( $siblings );
		
    	for ( $i = 0; $i < $size; $i++ )
		{
      		$sibling = $siblings[$i];
      
	  		// Check whether this is the context node.
      		if ( $sibling == $contextPath )
			{
				// Don't continue looking for other siblings.
        		break;
      		}
			
      		// Check whether the sibling matches the node-test.
      		if ( $this->_checkNodeTest( $sibling, $axis['node-test'] ) )
			{
				// Add the sibling to the list of nodes.
        		$xPathSet[] = $sibling;
      		}
    	}
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath descendant-or-self axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_descendant_or_self( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Read the nodes.
    	$xPathSet = array_merge(
			$this->_handleAxis_self( $axis, $contextPath ),
			$this->_handleAxis_descendant( $axis, $contextPath )
		);
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath ancestor-or-self axis.
	 *
	 * This method handles the XPath ancestor-or-self axis.
	 *
	 * @param  $axis        (array)  Array containing information about the axis.
	 * @param  $contextPath (string) xpath to starting node from which the axis should be processed.
	 * @return              (array)  A vector containing all nodes that were found, during the evaluation of the axis.
	 * @see    evaluate()
	 */
	function _handleAxis_ancestor_or_self( $axis, $contextPath )
	{
		// Create an empty node-set.
    	$xPathSet = array();
    
    	// Read the nodes.
    	$xPathSet = array_merge(
			$this->_handleAxis_self( $axis, $contextPath ),
			$this->_handleAxis_ancestor( $axis, $contextPath )
		);
    
		// Return the nodeset.
		return $xPathSet;
  	}
  
	/**
	 * Handles the XPath function last.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_last( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Calculate the size of the context.
    	$parentNode = $this->nodeIndex[$absoluteXPath]['parentNode'];
    
		if ( $nodeTest == "*" )
		{
      		$contextPos = sizeOf( $parentNode['childNodes'] );
    	}
    	else if ( $nodeTest == "cdata" )
		{
      		$absoluteXPath = substr( $absoluteXPath, 0, strrpos( $absoluteXPath, "/text()" ) );
      		$contextPos    = sizeOf( $this->nodeIndex[$absoluteXPath]['textParts'] );
    	}
    	else
		{
      		$contextPos = 0;
      		$name = $this->nodeIndex[$absoluteXPath]['name'];
      
	  		foreach ( $parentNode['childNodes'] as $childNode )
				$contextPos += ( $childNode['name'] === $name )? 1 : 0;
    	}
    
		// Return the size.
		return $contextPos;
	}
  
	/**
	 * Handles the XPath function position.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_position( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Return the context-position.
    	if ( $nodeTest == "*" )
		{
			// if we are matching all children, then we need to find the position regardless of name
      		// 'pos' is zero-based, not one based.
      		$contextPos = $this->nodeIndex[$absoluteXPath]['pos'] + 1;
    	}
		// if we are looking for text nodes, we go about it a bit differently
    	else if ( $nodeTest == "cdata" )
		{
			$contextPos = substr( $absoluteXPath, strrpos( $absoluteXPath, "[" ) + 1, -1 );
    	}
    	else
		{
      		$contextPos = $this->nodeIndex[$absoluteXPath]['contextPos'];
    	}
    
		return $contextPos;
  	}
  
	/**
	 * Handles the XPath function count.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_count( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Evaluate the argument of the method as an XPath and return the number of results.
    	return count( $this->_internalEvaluate( $arguments, $absoluteXPath ) );
  	}
  
	/**
	 * Handles the XPath function id.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_id( $absoluteXPath, $arguments, $nodeTest )
	{
    	$arguments = trim( $arguments );         // Trim the arguments.
    	$arguments = explode( ' ', $arguments ); // Now split the arguments into an array.
    
		// Create a list of nodes.
    	$resultXPaths = array();
    
		// Run through all nodes of the document.
    	$keys  = array_keys( $this->nodeIndex );
    	$kSize = $sizeOf( $keys );
    
		for ( $i = 0; $i < $kSize; $i++ )
		{
			// skip super-Root
      		if ( empty( $keys[$i] ) )
				continue;
      
	  		if ( in_array( $this->nodeIndex[$keys[$i]]['attributes']['id'], $arguments ) )
			{
				// Add this node to the list of nodes.
        		$resultXPaths[] = $absoluteXPath;
      		}
    	}
    
		// Return the list of nodes.
		return $resultXPaths;
  	}
  
	/**
	 * Handles the XPath function name.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_name( $absoluteXPath, $arguments, $nodeTest )
	{
		// Return the name of the node.
    	return $this->nodeIndex[$absoluteXPath]['name'];
  	}
  
	/**
	 * Handles the XPath function string.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_string( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Check what type of parameter is given
    	if ( preg_match( '/^[0-9]+(\.[0-9]+)?$/', $arguments ) || preg_match( '/^\.[0-9]+$/', $arguments ) )
		{
			// Convert the digits to a number.
      		$number = doubleval( $arguments );
			
			// Return the number.
      		return strval( $number );
    	}
		// Check whether it's true or false and return as string.
    	else if ( is_bool( $arguments ) )
		{
      		if ( $arguments === true )
				return 'TRUE';
			else
				return 'FALSE';
    	}
    	else if ( !empty( $arguments ) )
		{
      		// Use the argument as an XPath.
      		$result = $this->_internalEvaluate( $arguments, $absoluteXPath );
      		$result = explode( '|', implode( '|', $result ) ); // Get the first argument.
			
			// Return the first result as a string.
      		return $result[0];
    	}
    	else if ( empty( $arguments ) )
		{
			// Return the current node.
      		return $absoluteXPath;
    	}
    	else
		{
			// Return an empty string.
      		return '';
    	}
  	}
  
	/**
	 * Handles the XPath function concat.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_concat( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Split the arguments.
    	$arguments = explode( ',', $arguments );
    
		// Run through each argument and evaluate it.
    	$size = sizeof( $arguments );
    
		for ( $i = 0; $i < $size; $i++ )
		{
			// Trim each argument.
      		$arguments[$i] = trim($arguments[$i]);
      
	  		// Evaluate it.
      		$arguments[$i] = $this->_evaluatePredicate( $absoluteXPath, $arguments[$i], $nodeTest );
    	}
    
		// Put the string together and return it.
		$arguments = implode( '', $arguments );
		
    	return $arguments;
  	}
  
	/**
	 * Handles the XPath function starts-with.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_starts_with( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Get the arguments.
    	$first  = trim( $this->_prestr( $arguments,   ',' ) );
    	$second = trim( $this->_afterstr( $arguments, ',' ) );
		
    	// Evaluate each argument.
    	$first  = $this->_evaluatePredicate( $absoluteXPath, $first, $nodeTest  );
    	$second = $this->_evaluatePredicate( $absoluteXPath, $second, $nodeTest );
		
    	// Check whether the first string starts with the second one.
    	return (bool)ereg( '^' . $second, $first );
  	}
  
	/**
	 * Handles the XPath function contains.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_contains( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Get the arguments.
    	$first  = trim( $this->_prestr( $arguments,   ',' ) );
    	$second = trim( $this->_afterstr( $arguments, ',' ) );
		
    	// echo "Predicate: $arguments First: " . $first . " Second: " . $second . "\n";
    
		// Evaluate each argument.
    	$first  = $this->_evaluatePredicate( $absoluteXPath, $first, $nodeTest  );
    	$second = $this->_evaluatePredicate( $absoluteXPath, $second, $nodeTest );
		
    	// echo $second.": ".$first."\n";
    
		// If the search string is null, then the provided there is a value it will contain it as
    	// it is considered that all strings contain the empty string.
		if ( $second === '' )
			return true;
			
    	// Check whether the first string starts with the second one.
    	if ( strpos( $first, $second ) === false )
			return false;
    	else
      		return true;
  	}
  
	/**
	 * Handles the XPath function substring-before.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_substring_before( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Get the arguments.
    	$first  = trim( $this->_prestr( $arguments,   ',' ) );
    	$second = trim( $this->_afterstr( $arguments, ',' ) );
		
    	// Evaluate each argument.
    	$first  = $this->_evaluatePredicate( $absoluteXPath, $first,  $nodeTest );
    	$second = $this->_evaluatePredicate( $absoluteXPath, $second, $nodeTest );
		
    	// Return the substring.
    	return $this->_prestr( strval( $first ), strval( $second ) );
  	}
  
	/**
	 * Handles the XPath function substring-after.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_substring_after( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Get the arguments.
    	$first  = trim( $this->_prestr( $arguments,   ',' ) );
    	$second = trim( $this->_afterstr( $arguments, ',' ) );
		
    	// Evaluate each argument.
    	$first  = $this->_evaluatePredicate( $absoluteXPath, $first,  $nodeTest );
    	$second = $this->_evaluatePredicate( $absoluteXPath, $second, $nodeTest );
		
    	// Return the substring.
    	return $this->_afterstr( strval( $first ), strval( $second ) );
  	}
  
	/**
	 * Handles the XPath function substring.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_substring( $absoluteXPath, $arguments, $nodeTest )
	{
    	// Split the arguments.
    	$arguments = explode( ",", $arguments );
    	$size = sizeOf( $arguments );
		
		// Run through all arguments.
    	for ( $i = 0; $i < $size; $i++ )
		{
			// Trim the string.
      		$arguments[$i] = trim( $arguments[$i] );
      
	  		// Evaluate each argument.
      		$arguments[$i] = $this->_evaluatePredicate( $absoluteXPath, $arguments[$i], $nodeTest );
    	}
    
		// Check whether a third argument was given and return the substring..
    	if ( !empty( $arguments[2] ) )
      		return substr( strval( $arguments[0]), $arguments[1] - 1, $arguments[2] );
    	else
      		return substr( strval( $arguments[0] ), $arguments[1] - 1 );
  	}
  
	/**
	 * Handles the XPath function string-length.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_string_length( $absoluteXPath, $arguments, $nodeTest )
	{
		// Trim the argument.
    	$arguments = trim( $arguments );
    
		// Evaluate the argument.
    	$arguments = $this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
		
		// Return the length of the string.
    	return strlen( strval( $arguments ) );
  	}

	/**
	 * Handles the XPath function normalize-space.
	 *
	 * The normalize-space function returns the argument string with whitespace
	 * normalized by stripping leading and trailing whitespace and replacing sequences
	 * of whitespace characters by a single space.
	 * If the argument is omitted, it defaults to the context node converted to a string,
	 * in other words the string-value of the context node
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                 (stri)g trimed string
	 * @see    evaluate()
	 */
	function _handleFunction_normalize_space( $absoluteXPath, $arguments, $nodeTest )
	{
    	if ( empty( $arguments ) )
      		$arguments = $this->getParentXPath( $absoluteXPath ) . '/' . $this->nodeIndex[$absoluteXPath]['name'] . '[' . $this->nodeIndex[$absoluteXPath]['contextPos'] . ']';
    	else
       		$arguments = $this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
    
    	$arguments = trim( preg_replace( ";[[:space:]]+;s", ' ', $arguments ) );
    	return $arguments;
  	}

	/**
	 * Handles the XPath function translate.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_translate( $absoluteXPath, $arguments, $nodeTest )
	{
		// Split the arguments.
    	$arguments = explode( ',', $arguments );
		
    	$size = sizeOf( $arguments );
		
		// Run through all arguments.
    	for ( $i = 0; $i < $size; $i++ )
		{
			// Trim the argument.
      		$arguments[$i] = trim( $arguments[$i] );
      
	  		// Evaluate the argument.
      		$arguments[$i] = $this->_evaluatePredicate( $absoluteXPath, $arguments[$i], $nodeTest );
    	}
    
		// Return the translated string.
		return strtr( $arguments[0], $arguments[1], $arguments[2] );
  	}

	/**
	 * Handles the XPath function boolean.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_boolean( $absoluteXPath, $arguments, $nodeTest )
	{
		// Trim the arguments.
    	$arguments = trim( $arguments );
    
		// Check what type of parameter is given
    	if ( preg_match( '/^[0-9]+(\.[0-9]+)?$/', $arguments ) || preg_match( '/^\.[0-9]+$/', $arguments ) )
		{
			// Convert the digits to a number.
      		$number = doubleval( $arguments );
			
      		// If number zero return false else true.
      		if ( $number == 0 )
				return false;
			else
				return true;
    	}
    	else if ( empty( $arguments ) )
		{
			// Sorry, there were no arguments.
      		return false;
    	}
    	else
		{
      		// Try to evaluate the argument as an XPath.
      		$result = $this->_internalEvaluate( $arguments, $absoluteXPath );
      
	  		// If we found something return true else false.
      		if ( count( $result ) > 0 )
				return false;
			else
				return true;
    	}
  	}
  
	/**
	 * Handles the XPath function not.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_not( $absoluteXPath, $arguments, $nodeTest )
	{
		// Trim the arguments.
    	$arguments = trim( $arguments );
		
    	// Return the negative value of the content of the brackets.
    	return !$this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
  	}
  
	/**
	 * Handles the XPath function true.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_true( $absoluteXPath, $arguments, $nodeTest )
	{
    	return true;
  	}
  
  	/**
   	 * Handles the XPath function false.
   	 *
   	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
   	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
   	 * @return                (mixed)  Depending on the type of function being processed
   	 * @see    evaluate()
   	 */
  	function _handleFunction_false( $absoluteXPath, $arguments, $nodeTest )
	{
    	return false;
  	}
  
	/**
	 * Handles the XPath function lang.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_lang( $absoluteXPath, $arguments, $nodeTest )
	{
    	$arguments   = trim( $arguments ); // Trim the arguments.
    	$currentNode = $this->nodeIndex[$absoluteXPath];
    
		// Run through the ancestors.
		while ( !empty( $currentNode['name'] ) )
		{
      		// Check whether the node has an language attribute.
      		if ( isSet( $currentNode['attributes']['xml:lang'] ) )
			{
        		// Check whether it's the language, the user asks for; if so return true else false
        		return eregi( '^' . $arguments, $currentNode['attributes']['xml:lang'] );
      		}
      
	  		// Move up to parent.
	  		$currentNode = $currentNode['parentNode'];
    	}
    
		return false;
  	}
  
	/**
	 * Handles the XPath function number.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_number( $absoluteXPath, $arguments, $nodeTest )
	{
    	if ( !is_numeric( $arguments ) )
      		$arguments = $this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
    
    	// Check the type of argument.
    	if ( is_numeric( $arguments ) )
		{
			// Return the argument as a number.
      		return doubleval( $arguments );
    	}
		// Return true/false as a number.
    	else if ( is_bool( $arguments ) )
		{
      		if ( $arguments === true )
				return 1;
			else
				return 0;  
    	}
  	}

	/**
	 * Handles the XPath function sum.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_sum( $absoluteXPath, $arguments, $nodeTest )
	{
		// Trim the arguments.
    	$arguments = trim( $arguments );
		
    	// Evaluate the arguments as an XPath expression.
    	$result = $this->_internalEvaluate( $arguments, $absoluteXPath );
		
		// Create a variable to save the sum.
    	$sum = 0;
    
		// Run through all results.
    	$size = sizeOf( $result );
    
		for ( $i = 0; $i < $size; $i++ )
		{
			// Get the value of the node.
      		$value = $this->substringData( $result[$i] );
      
	  		// Add it to the sum.
	  		$sum += doubleval( $value );
		}
		
		return $sum;
  	}

  	/**
   	 * Handles the XPath function floor.
   	 *
   	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
   	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
   	 * @return                (mixed)  Depending on the type of function being processed
   	 * @see    evaluate()
   	 */
  	function _handleFunction_floor( $absoluteXPath, $arguments, $nodeTest )
	{
    	if ( !is_numeric( $arguments ) )
      		$arguments = $this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
    
		// Convert the arguments to a number.
    	$arguments = doubleval( $arguments );
    
		return floor( $arguments );
  	}
  
  	/**
   	 * Handles the XPath function ceiling.
  	 *
   	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
   	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
   	 * @return                (mixed)  Depending on the type of function being processed
   	 * @see    evaluate()
   	 */
  	function _handleFunction_ceiling( $absoluteXPath, $arguments, $nodeTest )
	{
    	if ( !is_numeric( $arguments ) )
      		$arguments = $this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
    
		// Convert the arguments to a number.
    	$arguments = doubleval( $arguments );
    
		return ceil( $arguments );
  	}
  
	/**
	 * Handles the XPath function round.
	 *
	 * @param  $absoluteXPath (string) Full xpath of the node on which the function should be processed.
	 * @param  $arguments     (string) String containing the arguments that were passed to the function.
	 * @return                (mixed)  Depending on the type of function being processed
	 * @see    evaluate()
	 */
	function _handleFunction_round( $absoluteXPath, $arguments, $nodeTest )
	{
    	if ( !is_numeric( $arguments ) )
      		$arguments = $this->_evaluatePredicate( $absoluteXPath, $arguments, $nodeTest );
    
		// Convert the arguments to a number.
    	$arguments = doubleval( $arguments );
		
    	return round( $arguments );
	}

	/**
	 * Compare to nodes if they are equal
	 *
	 * 2 nodes are considered equal if the abs. xpath is equal.
	 * 
	 * @param  $node1 (mixed) Either a xpath string to an node OR a real tree-node (hash-array)
	 * @param  $node2 (mixed) Either a xpath string to an node OR a real tree-node (hash-array)
	 * @return        (bool)  true if equal (see text above), false if not (and on error).
	 */
	function equalNodes( $node1, $node2 )
	{
    	$xPath_1 = is_string( $node1 )? $node1 : $this->getNodePath( $node1 );
    	$xPath_2 = is_string( $node2 )? $node2 : $this->getNodePath( $node2 );
    
		return ( strncasecmp( $xPath_1, $xPath_2, strLen( $xPath_1 ) ) == 0 );
  	}
  
	/**
	 * Get the Xpath string of a node that is in a document tree.
	 *
	 * @param $node (array)  A real tree-node (hash-array)   
	 * @return      (string) The string path to the node or false on error.
	 */
	function getNodePath( $node )
	{
    	if ( !empty( $node['xpath'] ) )
			return $node['xpath'];
    
		$pathInfo = array();
    
		do
		{
      		if ( empty( $node['name'] ) || empty( $node['parentNode'] ) )
				break; // End criteria
      
	  		$pathInfo[] = array(
				'name'       => $node['name'], 
				'contextPos' => $node['contextPos']
			);
      
	  		$node = $node['parentNode'];
    	} while ( true );
    
    	$xPath = '';
    
		for ( $i = sizeOf( $pathInfo ) - 1; $i >= 0; $i-- )
      		$xPath .= '/' . $pathInfo[$i]['name'] . '[' . $pathInfo[$i]['contextPos'] . ']';
    
    	if ( empty( $xPath ) )
			return false;
    
		return $xPath;
  	}
  
	/**
	 * Retrieves the absolute parent XPath expression.
	 *
	 * The parents stored in the tree are only relative parents...but all the parent
	 * information is stored in the xPath expression itself...so instead we use a function
	 * to extract the parent from the absolute xpath expression
	 *
	 * @param  $childPath (string) String containing an absolute XPath expression
	 * @return            (string) returns the absolute XPath of the parent
	 */
	function getParentXPath( $absoluteXPath )
	{
     	$lastSlashPos = strrpos( $absoluteXPath, '/' ); 
     
	 	// it's already the root path
	 	if ( $lastSlashPos == 0 )
		{
			// 'super-root'
       		return '';
     	}
		else
		{
       		return ( substr( $absoluteXPath, 0, $lastSlashPos ) );
     	}
   	}
  
	/**
	 * Returns true if the given node has child nodes below it
	 *
	 * @param  $absoluteXPath (string) full path of the potential parent node
	 * @return                (bool)   true if this node exists and has a child, false otherwise
	 */
	function hasChildNodes( $absoluteXPath )
	{
    	return (bool)( isSet( $this->nodeIndex[$absoluteXPath] ) && sizeOf( $this->nodeIndex[$absoluteXPath]['childNodes'] ) );
  	}
  
	/**
	 * Translate all ampersands to it's literal entities '&amp;' and back.
	 *
	 * I wasn't aware of this problem at first but it's important to understand why we do this.
	 * At first you must know:
	 * a) PHP's XML parser *translates* all entities to the equivalent char E.g. &lt; is returned as '<'
	 * b) PHP's XML parser (in V 4.1.0) has problems with most *literal* entities! The only one's that are 
	 *    recognized are &amp;, &lt; &gt; and &quot;. *ALL* others (like &nbsp; &copy; a.s.o.) cause an 
	 *    XML_ERROR_UNDEFINED_ENTITY error. I reported this as bug at http://bugs.php.net/bug.php?id=15092
	 *    (It turned out not to be a 'real' bug, but one of those nice W3C-spec things).
	 * 
	 * Forget position b) now. It's just for info. Because the way we will solve a) will also solve b) too. 
	 *
	 * THE PROBLEM
	 * To understand the problem, here a sample:
	 * Given is the following XML:    "<AAA> &lt; &nbsp; &gt; </AAA>"
	 *   Try to parse it and PHP's XML parser will fail with a XML_ERROR_UNDEFINED_ENTITY becaus of 
	 *   the unknown litteral-entity '&nbsp;'. (The numeric equivalent '&#160;' would work though). 
	 * Next try is to use the numeric equivalent 160 for '&nbsp;', thus  "<AAA> &lt; &#160; &gt; </AAA>"
	 *   The data we receive in the tag <AAA> is  " <   > ". So we get the *translated entities* and 
	 *   NOT the 3 entities &lt; &#160; &gt. Thus, we will not even notice that there were entities at all!
	 *   In *most* cases we're not able to tell if the data was given as entity or as 'normal' char.
	 *   E.g. When receiving a quote or a single space were not able to tell if it was given as 'normal' char
	 *   or as &nbsp; or &quot;. Thus we loose the entity-information of the XML-data!
	 * 
	 * THE SOLUTION
	 * The better solution is to keep the data 'as is' by replacing the '&' before parsing begins.
	 * E.g. Taking the original input from above, this would result in "<AAA> &amp;lt; &amp;nbsp; &amp;gt; </AAA>"
	 * The data we receive now for the tag <AAA> is  " &lt; &nbsp; &gt; ". and that's what we want.
	 * 
	 * The bad thing is, that a global replace will also replace data in section that are NOT translated by the 
	 * PHP XML-parser. That is comments (<!-- -->), IP-sections (stuff between <? ? >) and CDATA-block too.
	 * So all data comming from those sections must be reversed. This is done during the XML parse phase.
	 * So:
	 * a) Replacement of all '&' in the XML-source.
	 * b) All data that is not char-data or in CDATA-block have to be reversed during the XML-parse phase.
	 *
	 * @param  $xmlSource (string) The XML string
	 * @return            (string) The XML string with translated ampersands.
	 */
	function _translateAmpersand( $xmlSource, $reverse = false )
	{
    	return ( $reverse? str_replace( '&amp;', '&', $xmlSource ) : str_replace( '&', '&amp;', $xmlSource ) );
  	}
} // END OF CLASS XPathEngine

?>
