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


using( 'xml.xpath.lib.XPathEngine' );


define( 'XPATH_QUERYHIT_ALL',    1 );
define( 'XPATH_QUERYHIT_FIRST',  2 );
define( 'XPATH_QUERYHIT_UNIQUE', 3 );


/**
 * @package xml_xpath_lib
 */

class XPathImplementation extends XPathEngine
{    
	/**
 	 * Constructor
	 *
 	 * Optionally you may call this constructor with the XML-filename to parse and the 
 	 * XML option vector. A option vector sample: 
	 *   $xmlOpt = array(XML_OPTION_CASE_FOLDING => false, XML_OPTION_SKIP_WHITE => true);
	 *
 	 * @param  $userXmlOptions (array)  (optional) Vector of (<optionID>=><value>, <optionID>=><value>, ...)
   	 * @param  $fileName       (string) (optional) Filename of XML file to load from.
	 *                                  It is recommended that you call importFromFile()
 	 *                                  instead as you will get an error code.  If the
 	 *                                  import fails, the object will be set to false.
	 * @access public
 	 */
  	function XPathImplementation( $fileName = '', $userXmlOptions = array() )
	{
    	parent::XPathEngine( $userXmlOptions );
		$this->setType( 'XPath' );
		
    	$this->properties['modMatch'] = XPATH_QUERYHIT_ALL;
    
		if ( $fileName )
		{
      		if ( !$this->importFromFile( $fileName ) )
        		$this = false;
    	}
  	}
  
  
	/**
	 * Resets the object so it's able to take a new xml sting/file
	 *
	 * Constructing objects is slow.  If you can, reuse ones that you have used already
	 * by using this reset() function.
	 *
	 * @access public
	 */
	function reset()
	{
    	parent::reset();
    	$this->xpath = '';
    	$this->properties['modMatch'] = XPATH_QUERYHIT_ALL;
  	}

	/**
	 * Resolves and xPathQuery array depending on the property['modMatch']
 	 *
 	 * Most of the modification functions of XPath will also accept a xPathQuery (instead 
 	 * of an absolute Xpath). The only problem is that the query could match more the one 
 	 * node. The question is, if the none, the fist or all nodes are to be modified.
 	 * The behaver can be set with setModMatch()  
	 *
 	 * @param $modMatch (int) One of the following:
 	 *                        - XPATH_QUERYHIT_ALL (default) 
 	 *                        - XPATH_QUERYHIT_FIRST
 	 *                        - XPATH_QUERYHIT_UNIQUE // If the query matches more then one node. 
 	 * @access public
  	 */
  	function setModMatch( $modMatch = XPATH_QUERYHIT_ALL )
	{
    	switch ( $modMatch )
		{
      		case XPATH_QUERYHIT_UNIQUE:
				$this->properties['modMatch'] = XPATH_QUERYHIT_UNIQUE; 
				break;
				
      		case XPATH_QUERYHIT_FIRST: 0
				$this->properties['modMatch'] = XPATH_QUERYHIT_FIRST; 
				break;
				
      		default: 
				$this->properties['modMatch'] = XPATH_QUERYHIT_ALL;
    	}
  	}

	/**
	 * Retrieves the name(s) of a node or a group of document nodes.
	 *          
	 * This method retrieves the names of a group of document nodes
	 * specified in the argument.  So if the argument was '/A[1]/B[2]' then it
	 * would return 'B' if the node did exist in the tree.
	 *          
	 * @param  $xPathQuery (mixed) Array or single full document path(s) of the node(s), 
	 *                             from which the names should be retrieved.
	 * @return             (mixed) Array or single string of the names of the specified 
	 *                             nodes, or just the individual name.  If the node did 
	 *                             not exist, then returns false.
	 * @access public
	 */
	function nodeName( $xPathQuery )
	{
    	// Check for a valid xPathQuery.
		$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'nodeName' );
    
		if ( count( $xPathSet ) == 0 )
			return false;
    
		// for each node, get it's name
    	$result = array();
    	foreach ( $xPathSet as $xPath )
		{
      		$node = &$this->getNode( $xPath );
			
      		if ( !$node ) 
        		continue;
      
      		$result[] = $node['name'];
    	}
    
		// if just a single string, return string
    	if ( count( $xPathSet ) == 1 )
			$result = $result[0];
 
    	return $result;
  	}
  
  	/**
   	 * Removes a node from the XML document.
   	 *
   	 * This method removes a node from the tree of nodes of the XML document. If the node 
   	 * is a document node, all children of the node and its character data will be removed. 
   	 * If the node is an attribute node, only this attribute will be removed, the node to which 
   	 * the attribute belongs as well as its children will remain unmodified.
   	 *
   	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
   	 *       Depending on setModMatch() one, none or multiple nodes are affected.
   	 *
   	 * @param  $xPathQuery  (string) xpath to the node (See note above).
   	 * @param  $autoReindex (bool)   (optional, default=true) Reindex the document to reflect 
   	 *                               the changes.  A performance helper.  See reindexNodeTree()
   	 * @return              (bool)   true on success, false on error;
   	 * @access public
   	 */
  	function removeChild( $xPathQuery, $autoReindex = true )
	{
    	$NULL = null;
    	$bDebugThisFunction = false; // get diagnostic output for this function
    
		if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( 'removeChild' );
      		echo "Node: $xPathQuery\n";
      		echo '<hr>';
    	}
    
		$status = false;
    
		// try-block
		do
		{
      		// check for a valid xPathQuery
      		$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'removeChild' );
			
      		if ( sizeOf( $xPathSet ) === 0 )
			{
        		$this->_displayError( sprintf( $this->errorStrings['NoNodeMatch'], $xPathQuery ), __LINE__, __FILE__, false );
        		break; // try-block
      		}
      
	  		$mustReindex = false;
      
	  		// Make chages from 'bottom-up'. In this manner the modifications will not affect itself.
      		for ( $i = sizeOf( $xPathSet ) - 1; $i >= 0; $i-- )
			{
        		$absoluteXPath = $xPathSet[$i];
        
				// Handle the case of an attribute node
				if ( preg_match( ';/attribute::;', $absoluteXPath ) )
				{
          			$xPath = $this->_prestr( $absoluteXPath, '/attribute::' );       // Get the path to the attribute node's parent.
          			$attribute = $this->_afterstr( $absoluteXPath, '/attribute::' ); // Get the name of the attribute.
          			unset( $this->nodeIndex[$xPath]['attributes'][$attribute] );     // Unset the attribute
          
		  			if ( $bDebugThisFunction )
						echo "We removed the attribute '$attribute' of node '$xPath'.\n";
          
		  			continue;
        		}
        
				// Otherwise remove the node by setting it to null. It will be removed on the next reindexNodeTree() call.
        		$mustReindex = $autoReindex;
        		$theNode = $this->nodeIndex[$absoluteXPath];
        		$theNode['parentNode']['childNodes'][$theNode['pos']] =& $NULL;
        
				if ( $bDebugThisFunction )
					echo "We removed the node '$absoluteXPath'.\n";
      		}
      
	  		// Reindex the node tree again.
      		if ( $mustReindex )
				$this->reindexNodeTree();
      
	  		$status = true;
    	} while ( false );
    
    	if ( $bDebugThisFunction )
			$this->_closeDebugFunction( $aStartTime, $status );
			
    	return $status;
  	}
  
	/**
	 * Replace a node with any data string. The $data is taken 1:1.
	 *
	 * This function will delete the node you define by $absoluteXPath (plus it's sub-nodes) and 
	 * substitute it by the string $text. Often used to push in not well formed HTML.
	 * WARNING: 
	 *   The $data is taken 1:1. 
	 *   You are in charge that the data you enter is valid XML if you intend
	 *   to export and import the content again.
	 *
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param  $xPathQuery  (string) xpath to the node (See note above).
	 * @param  $data        (string) String containing the content to be set. *READONLY*
	 * @param  $autoReindex (bool)   (optional, default=true) Reindex the document to reflect 
	 *                               the changes.  A performance helper.  See reindexNodeTree()
	 * @return              (bool)   true on success, false on error;
	 * @access public
	 */
	function replaceChildByData( $xPathQuery, $data, $autoReindex = true )
	{
    	$NULL = null;
    	$bDebugThisFunction = false; // get diagnostic output for this function
    
		if ( $bDebugThisFunction )
		{
      		$aStartTime = $this->_beginDebugFunction( 'replaceChildByData' );
      		echo "Node: $xPathQuery\n";
    	}
    
		$status = false;
    
		// try-block
		do
		{
      		// check for a valid xPathQuery
      		$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'replaceChildByData' );
      
	  		if ( sizeOf( $xPathSet ) === 0 )
			{
        		$this->_displayError( sprintf( $this->errorStrings['NoNodeMatch'], $xPathQuery ), __LINE__, __FILE__, false );
        		break; // try-block
      		}
      
	  		$mustReindex = false;
      
	  		// Make chages from 'bottom-up'. In this manner the modifications will not affect itself.
      		for ( $i = sizeOf( $xPathSet ) - 1; $i >= 0; $i-- )
			{
        		$absoluteXPath = $xPathSet[$i];
        		$mustReindex   = $autoReindex;
        		$theNode = $this->nodeIndex[$absoluteXPath];
        		$pos = $theNode['pos'];
        		$theNode['parentNode']['textParts'][$pos]  .= $data;
        		$theNode['parentNode']['childNodes'][$pos] =& $NULL;
        
				if ( $bDebugThisFunction )
					echo "We replaced the node '$absoluteXPath' with data.\n";
      		}
      
	  		// Reindex the node tree again.
      		if ( $mustReindex )
				$this->reindexNodeTree();
      
	  		$status = true;
    	} while ( false );
    
    	if ( $bDebugThisFunction )
			$this->_closeDebugFunction( $aStartTime, $status? 'Success' : '!!! FAILD !!!' );
    
		return $status;
  	}
  
	/**
	 * Replace the node(s) that matches the xQuery with the passed node (or passed node-tree)
	 * 
	 * If the passed node is a string it's assumed to be XML and replaceChildByXml() 
	 * will be called.
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param  $xPathQuery  (string) Xpath to the node being replaced.
	 * @param  $node        (array)  A doc node.
	 * @param  $autoReindex (bool)   (optional, default=true) Reindex the document to reflect 
	 *                               the changes.  A performance helper.  See reindexNodeTree()
	 * @return              (array)  The last replaced $node (can be a whole sub-tree)
	 * @access public
	 */
	function &replaceChild( $xPathQuery, $node, $autoReindex = true )
	{
    	$NULL = null;
    
		if ( is_string( $node ) )
		{
      		if ( !( $node = $this->_xml2Document( $node ) ) )
				return false;
    	}
    
		// Special case if it's 'super root'. We then have to take the child node == top node
    	if ( empty( $node['name'] ) )
			$node = $node['childNodes'][0];
    
    	$status = false;
    
		// try-block
		do
		{
      		// Check for a valid xPathQuery.
      		$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'replaceChild' );
      
	  		if ( sizeOf( $xPathSet ) === 0 )
			{
        		$this->_displayError( sprintf( $this->errorStrings['NoNodeMatch'], $xPathQuery ), __LINE__, __FILE__, false );
        		break; // try-block
      		}
      
	  		$mustReindex = false;
      
	  		// Make chages from 'bottom-up'. In this manner the modifications will not affect itself.
      		for ( $i = sizeOf( $xPathSet ) - 1; $i >= 0; $i-- )
			{
        		$absoluteXPath  =  $xPathSet[$i];
        		$mustReindex    =  $autoReindex;
        		$childNode      =& $this->nodeIndex[$absoluteXPath];
        		$parentNode     =& $childNode['parentNode'];
        		$childNode['parentNode'] =& $NULL;
        		$childPos = $childNode['pos'];
        		$parentNode['childNodes'][$childPos] =& $this->cloneNode( $node );
      		}
			
      		if ( $mustReindex )
				$this->reindexNodeTree();
      
	  		$status = true;
    	} while ( false );
    
    	if ( !$status )
			return false;
    
		return $childNode;
  	}
  
	/**
   	 * Insert passed node (or passed node-tree) at the node(s) that matches the xQuery.
   	 *
   	 * With parameters you can define if the 'hit'-node is shifted to the right or left 
   	 * and if it's placed before of after the text-part.
   	 * Per derfault the 'hit'-node is shifted to the right and the node takes the place 
   	 * the of the 'hit'-node. 
   	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
   	 *       Depending on setModMatch() one, none or multiple nodes are affected.
   	 * 
   	 * E.g. Following is given:           AAA[1]           
   	 *                                  /       \          
   	 *                              ..BBB[1]..BBB[2] ..    
   	 *
   	 * a) insertChild('/AAA[1]/BBB[1]', <node CCC>)
   	 * b) insertChild('/AAA[1]/BBB[1]', <node CCC>, $shiftRight=false)
   	 * c) insertChild('/AAA[1]/BBB[1]', <node CCC>, $shiftRight=false, $afterText=false)
   	 *
   	 * a)                          b)                           c)                        
   	 *          AAA[1]                       AAA[1]                       AAA[1]          
   	 *        /    |   \                   /    |   \                   /    |   \        
   	 *  ..BBB[1]..CCC[1]BBB[2]..     ..BBB[1]..BBB[2]..CCC[1]     ..BBB[1]..BBB[2]CCC[1]..
   	 *
   	 * #### Do a complete review of the "(optional)" tag after several arguments.
   	 *
   	 * @param  $xPathQuery  (string) Xpath to the node to append.
   	 * @param  $node        (array)  A doc node.
   	 * @param  $shiftRight  (bool)   (optional, default=true) Shift the target node to the right.
   	 * @param  $afterText   (bool)   (optional, default=true) Insert after the text.
   	 * @param  $autoReindex (bool)   (optional, default=true) Reindex the document to reflect 
   	 *                                the changes.  A performance helper.  See reindexNodeTree()
   	 * @return              (bool)   true on success, false on error.
   	 * @access public
   	 */
  	function insertChild( $xPathQuery, $node, $shiftRight = true, $afterText = true, $autoReindex = true )
	{
    	if ( is_string( $node ) )
		{
      		if ( !( $node = $this->_xml2Document( $node ) ) )
				return false;
    	}
		
    	// Special case if it's 'super root'. We then have to take the child node == top node.
    	if ( empty( $node['name'] ) )
			$node = $node['childNodes'][0];
  
    	// Check for a valid xPathQuery.
    	$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'appendChild' );
    
		if ( sizeOf( $xPathSet ) === 0 )
		{
      		$this->_displayError( sprintf( $this->errorStrings['NoNodeMatch'], $xPathQuery ), __LINE__, __FILE__, false );
      		return false;
    	}
    
    	$mustReindex = false;
    
		// Make chages from 'bottom-up'. In this manner the modifications will not affect itself.
    	for ( $i = sizeOf( $xPathSet ) - 1; $i >= 0; $i-- )
		{
      		$absoluteXPath =  $xPathSet[$i];
      		$mustReindex   =  $autoReindex;
      		$childNode     =& $this->nodeIndex[$absoluteXPath];
      		$parentNode    =& $childNode['parentNode'];
      
	  		// Special case: It not possible to add siblings to the top node.
      		if ( empty( $parentNode['name'] ) )
				continue;
      
	  		$newNode =& $this->cloneNode( $node );
      		$pos = $shiftRight? $childNode['pos'] : $childNode['pos'] + 1;
			
      		$parentNode['childNodes'] = array_merge(
				array_slice( $parentNode['childNodes'], 0, $pos ),
				array( $newNode ),
				array_slice( $parentNode['childNodes'], $pos )
			);
			
			$pos += $afterText? 1 : 0;
      
	  		$parentNode['textParts'] = array_merge(
				array_slice( $parentNode['textParts'], 0, $pos ),
				'',
				array_slice( $parentNode['textParts'], $pos )
			);
		}
    
		if ( $mustReindex )
			$this->reindexNodeTree();
    
		return true;
  	}
  
	/**
	 * Appends a child to anothers children.
	 *
	 * If you intend to do a lot of appending, you should leave autoIndex as false
	 * and then call reindexNodeTree() when you are finished all the appending.
	 *
	 * @param  $xPathQuery  (string) Xpath to the node to append to.
	 * @param  $node        (array)  A doc node.
	 * @param  $afterText   (bool)   (optional, default=false) Insert after the text.
	 * @param  $autoReindex (bool)   (optional, default=true) Reindex the document to reflect 
	 *                               the changes.  A performance helper.  See reindexNodeTree()
	 * @return              (bool)   true on success, false on error.
	 * @access public
	 */
	function appendChild( $xPathQuery, $node, $afterText = false, $autoReindex = true )
	{
    	if ( is_string( $node ) )
		{
      		if ( !( $node = $this->_xml2Document( $node ) ) )
				return false;
    	}
    
		// Special case if it's 'super root'. We then have to take the child node == top node.
    	if ( empty( $node['name'] ) )
			$node = $node['childNodes'][0];
  
    	// Check for a valid xPathQuery.
		$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'appendChild' );
		
    	if ( sizeOf( $xPathSet ) === 0 )
		{
      		$this->_displayError( sprintf( $this->errorStrings['NoNodeMatch'], $xPathQuery ), __LINE__, __FILE__, false );
      		return false;
    	}
    
    	$mustReindex = false;
    	$result = false;
    
		// Make chages from 'bottom-up'. In this manner the modifications will not affect itself.
    	for ( $i = sizeOf( $xPathSet ) - 1; $i >= 0; $i-- )
		{
      		$absoluteXPath =  $xPathSet[$i];
      		$mustReindex   =  $autoReindex;
      		$parentNode    =& $this->nodeIndex[$absoluteXPath];
      		$newNode       =& $this->cloneNode($node);
      		
			$pos = count($parentNode['childNodes']);
      		$parentNode['childNodes'][] =& $newNode;
      		$pos -= $afterText ? 0 : 1;
      
	  		$parentNode['textParts'] = array_merge(
				array_slice( $parentNode['textParts'], 0, $pos ),
				'',
				array_slice( $parentNode['textParts'], $pos )
			);
			
			$result[] = "$absoluteXPath/{$newNode['name']}";
    	}
    
		if ( $mustReindex )
			$this->reindexNodeTree();
    
		if ( count( $result ) == 1 )
			$result = $result[0];
    
		return $result;
  	}
  
	/**
	 * Inserts a node before the reference node with the same parent.
	 *
	 * If you intend to do a lot of appending, you should leave autoIndex as false
	 * and then call reindexNodeTree() when you are finished all the appending.
	 *
	 * @param  $xPathQuery  (string) Xpath to the node to insert new node before
	 * @param  $node        (array)  A doc node.
	 * @param  $afterText   (bool)   (optional, default=FLASE) Insert after the text.
	 * @param  $autoReindex (bool)   (optional, default=true) Reindex the document to reflect 
	 *                               the changes.  A performance helper.  See reindexNodeTree()
	 * @return              (bool)   true on success, false on error.
	 * @access public
	 */
	function insertBefore( $xPathQuery, $node, $afterText = true, $autoReindex = true )
	{
    	return $this->insertChild( $xPathQuery, $node, $shiftRight = true, $afterText, $autoReindex );
  	}
  
	/** 
	 * Retrieves a dedecated attribute value or a hash-array of all attributes of a node.
	 * 
	 * The first param $absoluteXPath must be a valid xpath OR a xpath-query that results 
	 * to *one* xpath. If the second param $attrName is not set, a hash-array of all attributes 
	 * of that node is returned.
	 *
	 * Optionally you may pass an attrubute name in $attrName and the function will return the 
	 * string value of that attribute.
	 *
	 * @param  $absoluteXPath (string) Full xpath OR a xpath-query that results to *one* xpath.
	 * @param  $attrName      (string) (Optional) The name of the attribute. See above.
	 * @return                (mixed)  hash-array or a string of attributes depending if the 
	 *                                 parameter $attrName was set (see above).  false if the 
	 *                                 node or attribute couldn't be found.
	 * @access public
	 */
	function getAttributes( $absoluteXPath, $attrName = null )
	{
    	// Numpty check
		if ( !isSet( $this->nodeIndex[$absoluteXPath] ) )
		{
      		$xPathSet = $this->_resolveXPathQuery( $absoluteXPath, 'setAttributes' );
      
	  		if ( empty( $xPathSet ) )
				return false;
      
	  		// only use the first entry
      		$absoluteXPath = $xPathSet[0];
    	}
    
    	// Return the complete list or just the desired element
    	if ( is_null( $attrName ) )
      		return $this->nodeIndex[$absoluteXPath]['attributes'];
    	else if ( isSet( $this->nodeIndex[$absoluteXPath]['attributes'][$attrName] ) )
      		return $this->nodeIndex[$absoluteXPath]['attributes'][$attrName];
    
    	return false;
  	}
  
	/**
	 * Set attributes of a node(s).
	 *
	 * This method sets a number single attributes. An existing attribute is overwritten (default)
	 * with the new value, but setting the last param to false will prevent overwritten.
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param  $xPathQuery (string) xpath to the node (See note above).
	 * @param  $name       (string) Attribute name.
	 * @param  $value      (string) Attribute value.   
	 * @param  $overwrite  (bool)   If the attribute is already set we overwrite it (see text above)
	 * @return             (bool)   true on success, false on failure.
	 * @access public
	 */
	function setAttribute( $xPathQuery, $name, $value, $overwrite = true )
	{
    	return $this->setAttributes( $xPathQuery, array( $name => $value ), $overwrite );
  	}
  
  	/**
   	 * Version of setAttribute() that sets multiple attributes to node(s).
   	 *
   	 * This method sets a number of attributes. Existing attributes are overwritten (default)
   	 * with the new values, but setting the last param to false will prevent overwritten.
   	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
   	 *       Depending on setModMatch() one, none or multiple nodes are affected.
   	 *
   	 * @param  $xPathQuery (string) xpath to the node (See note above).
   	 * @param  $attributes (array)  associative array of attributes to set.
   	 * @param  $overwrite  (bool)   If the attributes are already set we overwrite them (see text above)
   	 * @return             (bool)   true on success, false otherwise
   	 * @access public
   	 */
	function setAttributes( $xPathQuery, $attributes, $overwrite = true )
	{
    	$status = false;
    
		// try-block
		do
		{
      		// The attributes parameter should be an associative array.
      		if ( !is_array( $attributes ) )
				break; // try-block
      
      		// Check for a valid xPathQuery
      		$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'setAttributes' );
      
	  		foreach ( $xPathSet as $absoluteXPath )
			{
        		// Add the attributes to the node.
        		$theNode =& $this->nodeIndex[$absoluteXPath];
        
				if ( empty( $theNode['attributes'] ) )
          			$this->nodeIndex[$absoluteXPath]['attributes'] = $attributes;
        		else
          			$theNode['attributes'] = $overwrite? array_merge( $theNode['attributes'], $attributes ) : array_merge( $attributes, $theNode['attributes'] );
      		}
      
	  		$status = true;
    	} while ( false ); // END try-block
    
    	return $status;
  	}
  
	/**
	 * Removes an attribute of a node(s).
	 *
	 * This method removes *ALL* attributres per default unless the second parameter $attrList is set.
	 * $attrList can be either a single attr-name as string OR a vector of attr-names as array.
	 * E.g. 
	 *  removeAttribute(<xPath>);                     # will remove *ALL* attributes.
	 *  removeAttribute(<xPath>, 'A');                # will only remove attributes called 'A'.
	 *  removeAttribute(<xPath>, array('A_1','A_2')); # will remove attribute 'A_1' and 'A_2'.
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param   $xPathQuery (string) xpath to the node (See note above).
	 * @param   $attrList   (mixed)  (optional) if not set will delete *all* (see text above)
	 * @return              (bool)   true on success, false if the node couldn't be found
	 * @access  public
	 */
	function removeAttribute( $xPathQuery, $attrList = null )
	{
    	// Check for a valid xPathQuery.
    	$xPathSet = $this->_resolveXPathQuery( $xPathQuery, 'removeAttribute' );
    
    	if ( !empty( $attrList ) && is_string( $attrList ) )
			$attrList = array( $attrList );
			
    	if ( !is_array( $attrList ) )
			return false;
    
    	foreach ( $xPathSet as $absoluteXPath )
		{
      		// If the attribute parameter wasn't set then remove all the attributes.
      		if ( $attrList[0] === null )
			{
        		$this->nodeIndex[$absoluteXPath]['attributes'] = array();
        		continue; 
      		}
      
	  		// Remove all the elements in the array then.
      		foreach ( $attrList as $name )
        		unset($this->nodeIndex[$absoluteXPath]['attributes'][$name]);
    	}
    
		return true;
  	}

	/**
	 * Retrieve all the text from a node as a single string.
	 *
	 * Sample  
	 * Given is: <AA> This <BB\>is <BB\>  some<BB\>text </AA>
	 * Return of getData('/AA[1]') would be:  " This is   sometext "
	 * The first param $absoluteXPath must be a valid xpath OR a xpath-query that results 
	 * to *one* xpath. 
	 *
	 * @param  $absoluteXPath (string) Full xpath OR a xpath-query that results to *one* xpath.
	 * @return                (mixed)  The returned string (see above), false if the node 
	 *                                 couldn't be found or is not unique.
	 * @access public
	 */
	function getData( $absoluteXPath )
	{
    	$aDataParts = $this->getDataParts( $absoluteXPath );
    
		if ( $aDataParts === false )
			return false;
    
		return implode( '', $aDataParts );
  	}
  
	/**
	 * Retrieve all the text from a node as a vector of strings
	 * 
	 * Where each element of the array was interrupted by a non-text child element.
	 *
	 * Sample  
	 * Given is: <AA> This <BB\>is <BB\>  some<BB\>text </AA>
	 * Return of getDataParts('/AA[1]') would be:  array([0]=>' This ', [1]=>'is ', [2]=>'  some', [3]=>'text ');
	 * The first param $absoluteXPath must be a valid xpath OR a xpath-query that results 
	 * to *one* xpath. 
	 *
	 * @param  $absoluteXPath   (string) Full xpath OR a xpath-query that results to *one* xpath.
	 * @return                  (mixed)  The returned array (see above), or false if node is not 
	 *                                   found or is not unique.
	 * @access public
	 */
	function getDataParts( $absoluteXPath )
	{
    	// Resolve xPath argument
    	$xPathSet = $this->_resolveXPathQuery( $absoluteXPath, 'getDataParts' );
		
    	if ( count( $xPathSet ) != 1 )
		{
      		$this->_displayError( sprintf( $this->errorStrings['AbsoluteXPathRequired'], $absoluteXPath ), __LINE__, __FILE__, false );
      		return false;
    	}
    
		$absoluteXPath = $xPathSet[0];
    	return $this->nodeIndex[$absoluteXPath]['textParts'];
  	}
  
	/**
	 * Retrieves a sub string of a text-part OR attribute-value.
	 *
	 * This method retrieves the sub string of a specific text-part OR (if the 
	 * $absoluteXPath references an attribute) the the sub string  of the attribute value.
	 * If no 'direct referencing' is used (Xpath ends with text()[<part-number>]), then 
	 * the first text-part of the node ist returned (if exsiting).
	 *
	 * @param  $absoluteXPath (string) Xpath to the node (See note above).   
	 * @param  $offset        (int)    (optional, default is 0) Starting offset. (Just like PHP's substr())
	 * @param  $count         (number) (optional, default is ALL) Character count  (Just like PHP's substr())
	 * @return                (mixed)  The sub string, false if not found or on error
	 * @access public
	 */
	function substringData( $absoluteXPath, $offset = 0, $count = null )
	{
    	if ( !( $text = $this->wholeText( $absoluteXPath ) ) )
			return false;
    
		if ( is_null( $count ) )
      		return substr( $text, $offset );
    	else
      		return substr( $text, $offset, $count );
  	}
  
	/**
	 * Replace a sub string of a text-part OR attribute-value.
	 *
	 * @param  $absoluteXPath (string) Xpath to the node.   
	 * @param  $replacement   (string) The string to replace with.
	 * @param  $offset        (int)    (optional, default is 0) Starting offset. (Just like PHP's substr_replace ())
	 * @param  $count         (number) (optional, default is 0=ALL) Character count  (Just like PHP's substr_replace())
	 * @param  $textPartNr    (int)    (optional) (see _getTextSet() )
	 * @return                (bool)   The new string value on success, false if not found or on error
	 * @access public
	 */
	function replaceData( $xPathQuery, $replacement, $offset = 0, $count = 0, $textPartNr = 1 )
	{
    	if ( !( $textSet = $this->_getTextSet( $xPathQuery, $textPartNr ) ) )
			return false;
    
		$tSize = sizeOf( $textSet );
		
    	for ( $i = 0; $i < $tSize; $i++ )
		{
      		if ( $count )
        		$textSet[$i] = substr_replace( $textSet[$i], $replacement, $offset, $count );
      		else
        		$textSet[$i] = substr_replace( $textSet[$i], $replacement, $offset );
    	}
    
		return true;
  	}
  
	/**
	 * Insert a sub string in a text-part OR attribute-value.
	 *
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param  $xPathQuery (string) xpath to the node (See note above).
	 * @param  $data       (string) The string to replace with.
	 * @param  $offset     (int)    (optional, default is 0) Offset at which to insert the data.
	 * @return             (bool)   The new string on success, false if not found or on error
	 * @access public
	 */
	function insertData( $xPathQuery, $data, $offset = 0 )
	{
    	return $this->replaceData( $xPathQuery, $data, $offset, 0 );
  	}
  
	/**
	 * Append text data to the end of the text for an attribute OR node text-part.
	 *
	 * This method adds content to a node. If it's an attribute node, then
	 * the value of the attribute will be set, otherwise the passed data will append to 
	 * character data of the node text-part. Per default the first text-part is taken.
	 *
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param   $xPathQuery (string) to the node(s) (See note above).
	 * @param   $data       (string) String containing the content to be added.
	 * @param   $textPartNr (int)    (optional, default is 1) (see _getTextSet())
	 * @return              (bool)   true on success, otherwise false
	 * @access public
	 */
	function appendData( $xPathQuery, $data, $textPartNr = 1 )
	{
    	if ( !( $textSet = $this->_getTextSet( $xPathQuery, $textPartNr ) ) )
			return false;
    
		$tSize = sizeOf( $textSet );
		
    	for ( $i = 0; $i < $tSize; $i++ )
      		$textSet[$i] .= $data;
    
    	return true;
  	}
  
	/**
	 * Delete the data of a node.
	 *
	 * This method deletes content of a node. If it's an attribute node, then
	 * the value of the attribute will be removed, otherwise the node text-part. 
	 * will be deleted.  Per default the first text-part is deleted.
	 *
	 * NOTE: When passing a xpath-query instead of an abs. Xpath.
	 *       Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param  $xPathQuery (string) to the node(s) (See note above).
	 * @param  $offset     (int)    (optional, default is 0) Starting offset. (Just like PHP's substr_replace())
	 * @param  $count      (number) (optional, default is 0=ALL) Character count.  (Just like PHP's substr_replace())
	 * @param  $textPartNr (int)    (optional, default is 0) the text part to delete (see _getTextSet())
	 * @return             (bool)   true on success, otherwise false
	 * @access public
	 */
	function deleteData( $xPathQuery, $offset = 0, $count = 0, $textPartNr = 1 )
	{
    	if ( !( $textSet = $this->_getTextSet( $xPathQuery, $textPartNr ) ) )
			return false;
    
		$tSize = sizeOf( $textSet );
		
    	for ( $i = 0; $i < $tSize; $i++ )
		{
      		if ( !$count )
        		$textSet[$i] = "";
      		else
        		$textSet[$i] = substr_replace( $textSet[$i],'', $offset, $count );
    	} 
    
		return true;
  	}

	/**
	 * Decodes the character set entities in the given string.
	 *
	 * This function is given for convenience, as all text strings or attributes
	 * are going to come back to you with their entities still encoded.  You can
	 * use this function to remove these entites.
	 *
	 * ### Provide an option that will do this by default.
	 *
	 * @param $encodedData (mixed) The string or array that has entities you would like to remove
	 * @param $reverse     (bool)  If true entities will be encoded rather than decoded, ie
	 *                             < to &lt; rather than &lt; to <.
	 * @return             (mixed) The string or array returned with entities decoded.
	 * @access public
	 */
	function decodeEntities( $encodedData, $reverse = false )
	{
    	static $aEncodeTbl;
    	static $aDecodeTbl;
    
		// Get the translation entities, but we'll cache the result to enhance performance.
    	if ( empty( $aDecodeTbl ) )
		{
      		// Get the translation entities.
      		$aEncodeTbl = get_html_translation_table( HTML_ENTITIES );
      		$aDecodeTbl = array_flip( $aEncodeTbl );
    	}

    	// If it's just a single string.
    	if ( !is_array( $encodedData ) )
		{
      		if ( $reverse )
        		return strtr( $encodedData, $aEncodeTbl );
      		else
        		return strtr( $encodedData, $aDecodeTbl );
    	}

    	$result = array();
    	foreach ( $encodedData as $string )
		{
      		if ( $reverse )
        		$result[] = strtr( $string, $aEncodeTbl );
      		else
        		$result[] = strtr( $string, $aDecodeTbl );
    	}

    	return $result;
  	}
  
	/**
	 * Parse the XML to a node-tree. A so called 'document'
	 *
	 * @param  $xmlString (string) The string to turn into a document node.
	 * @return            (&array)  a node-tree
	 * @access public
	 */
	function &_xml2Document( $xmlString )
	{
    	$xmlOptions = array(
			XML_OPTION_CASE_FOLDING => $this->getProperties( 'caseFolding' ), 
			XML_OPTION_SKIP_WHITE   => $this->getProperties( 'skipWhiteSpaces' )
		);
    
		$xmlParser =& new XPathEngine( $xmlOptions );
    	$xmlParser->setVerbose( false );
    
		// Parse the XML string.
    	if ( !$xmlParser->importFromString( $xmlString ) )
		{
      		$this->_displayError( $xmlParser->getLastError(), __LINE__, __FILE__, false );
      		return false;
    	}
    
		return $xmlParser->getNode( '/' );
  	}
  
	/**
	 * Get a reference-list to node text part(s) or node attribute(s).
	 * 
	 * If the Xquery references an attribute(s) (Xquery ends with attribute::), 
	 * then the text value of the node-attribute(s) is/are returned.
	 * Otherwise the Xquery is referencing to text part(s) of node(s). This can be either a 
	 * direct reference to text part(s) (Xquery ends with text()[<nr>]) or indirect reference 
	 * (a simple Xquery to node(s)).
	 * 1) Direct Reference (Xquery ends with text()[<part-number>]):
	 *   If the 'part-number' is omitted, the first text-part is assumed; starting by 1.
	 *   Negative numbers are allowed, where -1 is the last text-part a.s.o.
	 * 2) Indirect Reference (a simple  Xquery to node(s)):
	 *   Default is to return the first text part(s). Optionally you may pass a parameter 
	 *   $textPartNr to define the text-part you want;  starting by 1.
	 *   Negative numbers are allowed, where -1 is the last text-part a.s.o.
	 *
	 * NOTE I : The returned vector is a set of references to the text parts / attributes.
	 *          This is handy, if you wish to modify the contents.
	 * NOTE II: text-part numbers out of range will not be in the list
	 * NOTE III:Instead of an absolute xpath you may also pass a xpath-query.
	 *          Depending on setModMatch() one, none or multiple nodes are affected.
	 *
	 * @param   $xPathQuery (string) xpath to the node (See note above).
	 * @param   $textPartNr (int)    String containing the content to be set.
	 * @return              (mixed)  A vector of *references* to the text that match, or 
	 *                               false on error
	 * @access public
	 */
	function _getTextSet( $xPathQuery, $textPartNr = 1 )
	{
    	$status   = false;
    	$funcName = '_getTextSet';
    	$textSet  = array();
    
		// try-block
    	do
		{
      		// Check if it's a Xpath reference to an attribut(s). Xpath ends with attribute::)
      		if ( preg_match( ";(.*)/(attribute::|@)([^/]*)$;U", $xPathQuery, $matches ) )
			{
        		$xPathQuery = $matches[1];
        		$attribute  = $matches[3];
        
				// Quick out
        		if ( isSet( $this->nodeIndex[$xPathQuery] ) )
				{
          			$xPathSet[] = $xPathQuery;
        		}
				else
				{
          			// Try to evaluate the absoluteXPath (since it seems to be an Xquery and not an abs. Xpath)
          			$xPathSet = $this->_resolveXPathQuery( "$xPathQuery/attribute::$attribute", $funcName );
        		}
        
				foreach ( $xPathSet as $absoluteXPath )
				{
          			preg_match( ";(.*)/attribute::([^/]*)$;U", $xPathSet[0], $matches );
          			$absoluteXPath = $matches[1];
          			$attribute     = $matches[2];
					
          			if ( !isSet( $this->nodeIndex[$absoluteXPath]['attributes'][$attribute] ) )
					{
            			$this->_displayError("The $absoluteXPath/attribute::$attribute value isn't a node in this document.", __LINE__, __FILE__, false );
            			continue;
          			}
          
		  			$textSet[] =& $this->nodes[$absoluteXPath]['attributes'][$attribute];
        		}
        
				$status = true;
        		break; // try-block
      		}
      
      		// Check if it's a Xpath reference direct to a text-part(s). (xpath ends with text()[<part-number>])
      		if ( preg_match(":(.*)/text\(\)(\[(.*)\])?$:U", $xPathQuery, $matches) )
			{
        		$xPathQuery = $matches[1];
        	
				// default to the first text node if a text node was not specified
        		$textPartNr = isSet( $matches[2] )? substr( $matches[2], 1, -1 ) : 1;
        
				// Quick check
        		if ( isSet( $this->nodeIndex[$xPathQuery] ) )
				{
          			$xPathSet[] = $xPathQuery;
        		}
				else
				{
          			// Try to evaluate the absoluteXPath (since it seams to be an Xquery and not an abs. Xpath)
          			$xPathSet = $this->_resolveXPathQuery( "$xPathQuery/text()[$textPartNr]", $funcName );
        		}
      		}
      		else
			{
        		// At this point we have been given an xpath with neither a 'text()' or 'attribute::' axis at the end
        		// So this means to get the text-part of the node. If parameter $textPartNr was not set, use the last
        		// text-part.
        		if ( isSet( $this->nodeIndex[$xPathQuery] ) )
				{
          			$xPathSet[] = $xPathQuery;
        		}
				else
				{
          			// Try to evaluate the absoluteXPath (since it seams to be an Xquery and not an abs. Xpath)
          			$xPathSet = $this->_resolveXPathQuery( $xPathQuery, $funcName );
        		}
      		}

      		// Now fetch all text-parts that match. (May be 0,1 or many)
      		foreach ( $xPathSet as $absoluteXPath )
			{
        		unset( $text );
			
        		if ( $text =& $this->wholeText( $absoluteXPath, $textPartNr ) )
          			$textSet[] =& $text;
      		}

      		$status = true;
		} while ( false );
    
    	if ( !$status )
			return false;
    
		return $textSet;
	}
  
	/**
	 * Resolves an xPathQuery vector depending on the property['modMatch']
	 * 
	 * To:
	 *   - all matches, 
	 *   - the first
	 *   - none (If the query matches more then one node.)
	 * see  setModMatch() for details
	 * 
	 * @param  $xPathQuery (string) An xpath query targeting a single node
	 * @param  $function   (string) The function in which this check was called
	 * @return             (array)  Vector of $absoluteXPath's (May be empty)
	 * @access public
	 */
	function _resolveXPathQuery( $xPathQuery, $function )
	{
    	$xPathSet = array();
    
		// try-block
		do
		{
      		if ( isSet( $this->nodeIndex[$xPathQuery] ) )
			{
        		$xPathSet[] = $xPathQuery;
        		break; // try-block
      		}
      
	  		if ( empty( $xPathQuery ) )
				break; // try-block
      
	  		if ( substr( $xPathQuery, -1 ) === '/')
				break; // If the xPathQuery ends with '/' then it cannot be a good query.
      
	  		// If this xPathQuery is not absolute then attempt to evaluate it
      		$xPathSet   = $this->match( $xPathQuery );
      		$resultSize = sizeOf( $xPathSet );
			
      		switch ( $this->properties['modMatch'] )
			{
        		case XPATH_QUERYHIT_UNIQUE: 
          			if ( $resultSize > 1 )
					{
            			$xPathSet = array();
            
						if ( $this->properties['verboseLevel'] )
							$this->_displayError( "Canceled function '{$function}'. The query '{$xPathQuery}' mached {$resultSize} nodes and 'modMatch' is set to XPATH_QUERYHIT_UNIQUE.", __LINE__, __FILE__, false );
					}
					
					break;
        
				case XPATH_QUERYHIT_FIRST: 
          			if ( $resultSize > 1 )
					{
            			$xPathSet = array( $xPathSet[0] );
            
						if ( $this->properties['verboseLevel'] )
							$this->_displayError( "Only modified first node in function '{$function}' because the query '{$xPathQuery}' mached {$resultSize} nodes and 'modMatch' is set to XPATH_QUERYHIT_FIRST.", __LINE__, __FILE__, false );
          			}
          
		  			break;
        
				default:
					; // DO NOTHING
      		}
    	} while ( false );
    
    	if ( $this->properties['verboseLevel'] >= 2 )
			$this->_displayMessage( "'{$xPathQuery}' parameter from '{$function}' returned the following nodes: " . ( count( $xPathSet )? implode( '<br>', $xPathSet) : '[none]' ), __LINE__, __FILE__ );
    
		return $xPathSet;
  	}
} // END OF XPathImplementation

?>
