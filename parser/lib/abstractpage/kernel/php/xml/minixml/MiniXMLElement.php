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


using( 'xml.minixml.MiniXMLTreeComponent' );
using( 'xml.minixml.MiniXMLElementComment' );
using( 'xml.minixml.MiniXMLElementDocType' );
using( 'xml.minixml.MiniXMLElementEntity' );
using( 'xml.minixml.MiniXMLElementCData' );
using( 'xml.minixml.MiniXMLElement' );
using( 'xml.minixml.MiniXMLNode' );


/**
 * Although the main handle to the xml document is the MiniXMLDoc object,
 * much of the functionality and manipulation involves interaction with
 * MiniXMLElement objects.
 *
 * A MiniXMLElement 
 * has:
 * - a name
 * - a list of 0 or more attributes (which have a name and a value)
 * - a list of 0 or more children (MiniXMLElement or MiniXMLNode objects)
 * - a parent (optional, only if MINIXML_AUTOSETPARENT > 0)
 *
 * @package xml_minixml
 */

class MiniXMLElement extends MiniXMLTreeComponent 
{	
	/**
	 * @access public
	 */
	var $xname;
	
	/**
	 * @access public
	 */
	var $xattributes;
	
	/**
	 * @access public
	 */
	var $xchildren;
	
	/**
	 * @access public
	 */
	var $xnumChildren;
	
	/**
	 * @access public
	 */
	var $xnumElementChildren;

	/**
	 * @access public
	 */
	var $xavoidLoops = MINIXML_AVOIDLOOPS;
	
	
	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function MiniXMLElement( $name = null )
	{
		$this->MiniXMLTreeComponent();
		
		$this->xname               = null;
		$this->xattributes         = array();
		$this->xchildren           = array();
		$this->xnumChildren        = 0;
		$this->xnumElementChildren = 0;

		if ( $name )
			$this->name( $name );
	}	
	

	/**
	 * If a NEWNAME string is passed, the MiniXMLElement's name is set 
	 * to NEWNAME.
	 *
	 * Returns the element's name.
	 *
	 * @access public
	 */
	function name( $setTo = null )
	{
		if ( !is_null( $setTo ) )
		{
			if ( !is_string( $setTo ) )
				return null;
			
			$this->xname = $setTo;
		}
		
		return $this->xname;
	}
	
	/**
	 * The attribute() method is used to get and set the 
	 * MiniXMLElement's attributes (ie the name/value pairs contained
	 * within the tag, <tagname attrib1="value1" attrib2="value2">)
	 *
	 * If SETTO is passed, the attribute's value is set to SETTO.
	 *
	 * If the optional SETTOALT is passed and SETTO is false, the 
	 * attribute's value is set to SETTOALT.  This is usefull in cases
	 * when you wish to set the attribute to a default value if no SETTO is
	 * present.
	 *
	 * Note: if the MINIXML_LOWERCASEATTRIBUTES define is > 0, all attribute names
	 * will be lowercased (while setting and during retrieval)
	 *
	 * Returns the value associated with attribute NAME.
	 *
	 * @access public
	 */
	function attribute( $name, $primValue = null, $altValue = null )
	{
		$value = ( is_null( $primValue )? $altValue : $primValue );

		if ( MINIXML_UPPERCASEATTRIBUTES > 0 )
			$name = strtoupper( $name );
		else if ( MINIXML_LOWERCASEATTRIBUTES > 0 )
			$name = strtolower( $name );
		
		if ( !is_null( $value ) )
			$this->xattributes[$name] = $value;
		
		if ( !is_null( $this->xattributes[$name] ) )
			return $this->xattributes[$name];
		else
			return null;
	}
	
	/**
	 * The text() method is used to get or append text data to this
	 * element (it is appended to the child list as a new MiniXMLNode object).
	 *
	 * If SETTO is passed, a new node is created, filled with SETTO 
	 * and appended to the list of this element's children.
	 *
	 * If the optional SETTOALT is passed and SETTO is false, the 
	 * new node's value is set to SETTOALT.  See the attribute() method
	 * for an example use.
	 * 
	 * Returns a string composed of all child MiniXMLNodes' contents.
	 *
	 * Note: all the children MiniXMLNodes' contents - including numeric 
	 * nodes are included in the return string.
	 *
	 * @access public
	 */
	function text( $setToPrimary = null, $setToAlternate = null )
	{
		$setTo = ( $setToPrimary? $setToPrimary : $setToAlternate );
		
		if ( !is_null( $setTo ) )
			$this->createNode( $setTo );
		
		$retString = '';
		
		// Extract text from all child nodes.
		for ( $i = 0; $i < $this->xnumChildren; $i++ )
		{
			if ( $this->isNode( $this->xchildren[$i] ) )
			{
				$nodeTxt = $this->xchildren[$i]->getValue();
				
				if ( !is_null( $nodeTxt ) )
					$retString .= "$nodeTxt ";	
			}
		}
		
		return $retString;
	}
	
	/**
	 * The numeric() method is used to get or append numeric data to
	 * this element (it is appended to the child list as a MiniXMLNode object).
	 *
	 * If SETTO is passed, a new node is created, filled with SETTO 
	 * and appended to the list of this element's children.
	 *
	 * If the optional SETTOALT is passed and SETTO is false, the 
	 * new node's value is set to SETTOALT.  See the attribute() method
	 * for an example use.
	 * 
	 * Returns a space seperated string composed all child MiniXMLNodes' 
	 * numeric contents.
	 *
	 * Note: ONLY numerical contents are included from the list of child MiniXMLNodes.
	 *
	 * @access public
	 */
	function numeric( $setToPrimary = null, $setToAlternate = null )
	{
		$setTo = ( is_null( $setToPrimary )? $setToAlternate : $setToPrimary );
		
		if ( !is_null( $setTo ) )
			$this->createNode( $setTo );
	}
	
	/**
	 * The comment() method allows you to add a new MiniXMLElementComment to this
	 * element's list of children.
	 *
	 * Comments will return a <!-- CONTENTS --> string when the element's toString()
	 * method is called.
	 *
	 * Returns a reference to the newly appended MiniXMLElementComment
	 *
	 * @access public
	 */
	function &comment( $contents )
	{
		$newEl = new MiniXMLElementComment();
		
		$appendedComment =& $this->appendChild( $newEl );
		$appendedComment->text( $contents );
		
		return $appendedComment;
	}	
	
	/**
	 * Append a new <!DOCTYPE DEFINITION [ ...]> element as a child of this 
	 * element.
	 * 
	 * Returns the appended DOCTYPE element. You will normally use the returned
	 * element to add ENTITY elements, like
	
	 * $newDocType =& $xmlRoot->docType('spec SYSTEM "spec.dtd"');
	 * $newDocType->entity('doc.audience', 'public review and discussion');
	 *
	 * @access public
	 */
	function &docType( $definition )
	{
		$newElement = new MiniXMLElementDocType( $definition );
		$appendedElement =& $this->appendChild( $newElement );
		
		return $appendedElement;
	}
	
	/**
	 * Append a new <!ENTITY NAME "VALUE"> element as a child of this 
	 * element.
	 *
	 * Returns the appended ENTITY element.
	 *
	 * @access public
	 */
	function &entity( $name,$value )
	{
		$newElement = new MiniXMLElementEntity( $name, $value );
		$appendedEl =& $this->appendChild( $newElement );
		
		return $appendedEl;
	}
	
	/**
	 * Append a new <![CDATA[ CONTENTS ]]> element as a child of this element.
	 * Returns the appended CDATA element. 
	 *
	 * @access public
	 */
	function &cdata( $contents )
	{
		$newElement = new MiniXMLElementCData( $contents );
		$appendedChild =& $this->appendChild( $newElement );
		
		return $appendedChild;
	}
		
	/**
	 * Returns a string containing the value of all the element's
	 * child MiniXMLNodes (and all the MiniXMLNodes contained within 
	 * it's child MiniXMLElements, recursively).
	 *
	 * Note: the seperator parameter remains officially undocumented
	 * since I'm not sure it will remain part of the API
	 *
	 * @access public
	 */
	function getValue( $seperator = ' ' )
	{
		$retStr   = '';
		$valArray = array();
		
		for ( $i = 0; $i < $this->xnumChildren; $i++ )
		{
			$value = $this->xchildren[$i]->getValue();
			
			if ( !is_null( $value ) )
				array_push( $valArray, $value );
		}
		
		if ( count( $valArray ) )
			$retStr = implode( $seperator, $valArray );
		
		return $retStr;
	}	
	
	/**
	 * Searches the element and it's children for an element with name NAME.
	 *
	 * Returns a reference to the first MiniXMLElement with name NAME,
	 * if found, null otherwise.
	 *
	 * NOTE: The search is performed like this, returning the first 
	 * 	 element that matches:
	 *
	 * - Check this element for a match
	 * - Check this element's immediate children (in order) for a match.
	 * - Ask each immediate child (in order) to MiniXMLElement::getElement()
	 *  (each child will then proceed similarly, checking all it's immediate
	 *   children in order and then asking them to getElement())
	 *
	 * @access public
	 */
	function &getElement( $name )
	{
		if ( is_null( $name ) )
			return null;

		if ( !$this->xnumChildren )
		{
			// Not match here and and no kids - not found...
			return null;
		}
		
		// Try each child (immediate children take priority).
		for ( $i = 0; $i < $this->xnumChildren; $i++ )
		{
			$childname = $this->xchildren[$i]->name();
			
			if ( $childname )
			{
				if ( MINIXML_CASESENSITIVE > 0 )
				{
					// case sensitive matches only
					if ( strcmp( $name, $childname ) == 0 )
						return $this->xchildren[$i];
				} 
				else 
				{
					// case INsensitive matching
					if ( strcasecmp( $name, $childname ) == 0 )
						return $this->xchildren[$i];
				}
			}
		}
		
		// Use beautiful recursion.
		for ( $i = 0; $i < $this->xnumChildren; $i++ )
		{
			$theelement = $this->xchildren[$i]->getElement( $name );
			
			if ( $theelement )
				return $theelement;
		}
		
		// not found
		return null;		
	}
	
	/**
	 * Attempts to return a reference to the (first) element at PATH
	 * where PATH is the path in the structure (relative to this element) to
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
	 *	$partRate =& $xmlDocument->getElement('partRateRequest');
	 *
	 * 	$accessid =& $partRate->getElementByPath('vendor/accessid');
	 *
	 * Will return what you expect (the accessid element with attributes user = "myusername"
	 * and password = "mypassword").
	 *
	 * BUT be careful:
	 *	$accessid =& $partRate->getElementByPath('partList/partNum');
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
		$names   = split( "/", $path );
		$element = $this;
		
		foreach ( $names as $elementName )
		{
			// Make sure we didn't hit a dead end and that we have a name.
			if ( $element && $elementName )
			{
				// Ask this element to get the next child in path.
				$element = $element->getElement( $elementName );
			}
		}
		
		return $element;
	}	
	
	/** 
	 * Returns the number of immediate children for this element.
	 *
	 * If the optional NAMED parameter is passed, returns only the 
	 * number of immediate children named NAMED.
	 *
	 * @access public
	 */
	function numChildren( $named = null )
	{
		if ( is_null( $named ) )
			return $this->xnumElementChildren;
		
		// We require only children named '$named'.
		$allkids =& $this->getAllChildren( $named );
		
		return count( $allkids );
	}
	
	/**
	 * Returns a reference to an array of all this element's MiniXMLElement children
	 *
	 * Note: although the MiniXMLElement may contain MiniXMLNodes as children, these are
	 * not part of the returned list.
	 *
	 * @access public
	 */
	function &getAllChildren( $name = null )
	{
		$retArray = array();
		$count    = 0;
		
		if ( is_null( $name ) )
		{
			// Return all element children.
			for ( $i = 0; $i < $this->xnumChildren; $i++ )
			{
				if ( method_exists( $this->xchildren[$i], 'MiniXMLElement' ) )
					$retArray[$count++] = $this->xchildren[$i];
			}
		} 
		else 
		{
			// Return only element children with name $name.
			for ( $i = 0; $i < $this->xnumChildren; $i++ )
			{
				if ( method_exists( $this->xchildren[$i], 'MiniXMLElement' ) )
				{
					if ( MINIXML_CASESENSITIVE > 0 )
					{
						if ( $this->xchildren[$i]->name() == $name )
							$retArray[$count++] = $this->xchildren[$i];
					} 
					else 
					{
						if ( strcasecmp( $this->xchildren[$i]->name(), $name ) == 0 )
							$retArray[$count++] = $this->xchildren[$i];
					}
				}				
			}
		}
			
		return $retArray;
	}

	/**
	 * @access public
	 */
	function &insertChild( &$child, $idx = 0 )
	{
		if ( !$this->_validateChild( $child ) )
			return;
		
		/**
		 * Set the parent for the child element to this element if 
		 * avoidLoops or MINIXML_AUTOSETPARENT is set.
		 */
		if ( $this->xavoidLoops || ( MINIXML_AUTOSETPARENT > 0 ) )
		{
			if ( $this->xparent == $child )
			{
				$cname = $child->name();
				return null;
			}
			
			$child->parent( $this );
		}
		
		$nextIdx = $this->xnumChildren;
		$lastIdx = $nextIdx - 1;

		if ( $idx > $lastIdx )
		{
			if ( $idx > $nextIdx )
				$idx = $lastIdx + 1;
			
			$this->xchildren[$idx] = $child;
			$this->xnumChildren++;
			
			if ( $this->isElement( $child ) )
				$this->xnumElementChildren++;
		} 
		else if ( $idx >= 0 )
		{
			$removed = array_splice( $this->xchildren, $idx );
			array_push( $this->xchildren, $child );
			$numRemoved = count( $removed );
			
			for ( $i = 0; $i < $numRemoved; $i++ )
				array_push( $this->xchildren, $removed[$i] );
			
			$this->xnumChildren++;
			
			if ( $this->isElement( $child ) )
				$this->xnumElementChildren++;
		} 
		else 
		{
			$revIdx = ( -1 * $idx ) % $this->xnumChildren;
			$newIdx = $this->xnumChildren - $revIdx;
			
			if ( $newIdx < 0 )
				return null;
			
			return $this->insertChild( $child, $newIdx );
		}
			
		return $child;
	}

	/**
	 * appendChild is used to append an existing MiniXMLElement object to
	 * this element's list.
	 *
	 * Returns a reference to the appended child element.
	 *
	 * NOTE: Be careful not to create loops in the hierarchy, eg
	 * $parent->appendChild($child);
	 * $child->appendChild($subChild);
	 * $subChild->appendChild($parent);
	 *
	 * If you want to be sure to avoid loops, set the MINIXML_AVOIDLOOPS define
	 * to 1 or use the avoidLoops() method (will apply to all children added with createChild())
	 *
	 * @access public
	 */
	function &appendChild( &$child )
	{
		if ( !$this->_validateChild( $child ) )
			return;
		
		/**
		 * Set the parent for the child element to this element if 
		 * avoidLoops or MINIXML_AUTOSETPARENT is set.
		 */
		if ( $this->xavoidLoops || ( MINIXML_AUTOSETPARENT > 0 ) )
		{
			if ( $this->xparent == $child )
			{
				
				$cname = $child->name();
				return null;
			}
			
			$child->parent( $this );
		}
		
		// Note that we're addind a MiniXMLElement child.
		$this->xnumElementChildren++;
		
		// Add the child to the list.
		$idx = $this->xnumChildren++;
		$this->xchildren[$idx] =& $child;
		
		return $this->xchildren[$idx];
	}
	
	/**
	 * prependChild is used to prepend an existing MiniXMLElement object to
	 * this element's list.  The child will be positioned at the begining of 
	 * the elements child list, thus it will be output first in the resulting XML.
	 *
	 * Returns a reference to the prepended child element.
	 *
	 * @access public
	*/
	function &prependChild( $child )
	{
		$this->_validateChild( $child );
		
		/**
		 * Set the parent for the child element to this element if 
		 * avoidLoops or MINIXML_AUTOSETPARENT is set.
		 */
		if ( $this->xavoidLoops || ( MINIXML_AUTOSETPARENT > 0 ) )
		{
			if ( $this->xparent == $child )
			{
				$cname = $child->name();
				return null;
			}
			
			$child->parent( $this );
		}
		
		// Note that we're adding a MiniXMLElement child.
		$this->xnumElementChildren++;
		
		// Add the child to the list.
		$idx = $this->xnumChildren++;
		array_unshift( $this->xchildren, $child );
		
		return $this->xchildren[0];
	}

	/**
	 * Creates a new MiniXMLElement instance and appends it to the list
	 * of this element's children.
	 * The new child element's name is set to ELEMENTNAME.
	 *
	 * If the optional VALUE (string or numeric) parameter is passed,
	 * the new element's text/numeric content will be set using VALUE.
	 *
	 * Returns a reference to the new child element
	 *
	 * Note: don't forget to use the =& (reference assignment) operator
	 * when calling createChild:
	 *
	 * $newChild =& $myElement->createChild('newChildName');
	 *
	 * @access public
	 */
	function &createChild( $name, $value = null )
	{
		if ( !$name )
			return null;
		
		if ( !is_string( $name ) )
			return null;
		
		$child =& new MiniXMLElement( $name );
		$appendedChild =& $this->appendChild( $child );
		
		if ( !is_null( $value ) )
		{
			if ( is_numeric( $value ) )
				$appendedChild->numeric( $value );
			else if ( is_string( $value ) )
				$appendedChild->text( $value );
		}
		
		$appendedChild->avoidLoops( $this->xavoidLoops );
		return $appendedChild;
	}	
	
	/**
	 * Removes CHILD from this element's list of children.
	 *
	 * Returns the removed child, if found, null otherwise.
	 *
	 * @access public
	 */
	function &removeChild( &$child )
	{
		if ( !$this->xnumChildren )
			return null;
		
		$foundChild;
		$idx = 0;
		
		while ( $idx < $this->xnumChildren && ! $foundChild )
		{
			if ( $this->xchildren[$idx] == $child )
				$foundChild =& $this->xchildren[$idx];
			else 
				$idx++;
		}
		
		if ( !$foundChild )
			return null;
		
		array_splice( $this->xchildren, $idx, 1 );
		$this->xnumChildren--;
		
		if ( $this->isElement( $foundChild ) )
			$this->xnumElementChildren--;
		
		unset( $foundChild->xparent );
		return $foundChild;
	}
	
	/**
	 * Removes all children of this element.
	 *
	 * Returns an array of the removed children (which may be empty)
	 *
	 * @access public
	 */
	function &removeAllChildren()
	{
		$emptyArray = array();
		
		if ( !$this->xnumChildren )
			return $emptyArray;
		
		$retList =& $this->xchildren;
		$idx = 0;
		
		while ( $idx < $this->xnumChildren )
			unset( $retList[$idx++]->xparent );
		
		$this->xchildren           = array();
		$this->xnumElementChildren = 0;
		$this->xnumChildren        = 0;
		
		return $retList;
	}

	/**
	 * @access public
	 */
	function &remove()
	{
		$parent =& $this->parent();
		
		if ( !$parent )
			return null;
		
		$removed =& $parent->removeChild( $this );
		return $removed;
	}
	
	/**
	 * The parent() method is used to get/set the element's parent.
	 *
	 * If the NEWPARENT parameter is passed, sets the parent to NEWPARENT
	 * (NEWPARENT must be an instance of MiniXMLElement)
	 *
	 * Returns a reference to the parent MiniXMLElement if set, null otherwise.
	 *
	 * Note: This method is mainly used internally and you wouldn't normally need
	 * to use it.
	 * It get's called on element appends when MINIXML_AUTOSETPARENT or 
	 * MINIXML_AVOIDLOOPS or avoidLoops() > 1
	 *
	 * @access public
	*/ 
	function &parent( &$setParent )
	{
		if ( !is_null( $setParent ) )
		{
			// Parents can only be MiniXMLElement objects.
			if ( !$this->isElement( $setParent ) )
				return null;
			
			$this->xparent = $setParent;
		}
		
		return $this->xparent;
	}
	
	/**
	 * The avoidLoops() method is used to get or set the avoidLoops flag for this element.
	 *
	 * When avoidLoops is true, children with parents already set can NOT be appended to any
	 * other elements.  This is overkill but it is a quick and easy way to avoid infinite loops
	 * in the heirarchy.
	 *
	 * The avoidLoops default behavior is configured with the MINIXML_AVOIDLOOPS define but can be
	 * set on individual elements (and automagically all the element's children) with the 
	 * avoidLoops() method.
	 *
	 * Returns the current value of the avoidLoops flag for the element.
	 *
	 * @access public
	 */
	function avoidLoops( $setTo = null )
	{
		if ( !is_null( $setTo ) )
			$this->xavoidLoops = $setTo;
		
		return $this->xavoidLoops;
	}
	
	/** 
	 * toString returns an XML string based on the element's attributes,
	 * and content (recursively doing the same for all children)
	 *
	 * The optional SPACEOFFSET parameter sets the number of spaces to use
	 * after newlines for elements at this level (adding 1 space per level in
	 * depth).  SPACEOFFSET defaults to 0.
	 *
	 * If SPACEOFFSET is passed as MINIXML_NOWHITESPACES.  
	 * no \n or whitespaces will be inserted in the xml string
	 * (ie it will all be on a single line with no spaces between the tags.
	 *
	 * Returns the XML string.
	 *
	 * Note: Since the toString() method recurses into child elements and because
	 * of the MINIXML_NOWHITESPACES and our desire to avoid testing for this value
	 * on every element (as it does not change), here we split up the toString method
	 * into 2 subs: toStringWithWhiteSpaces(DEPTH) and toStringNoWhiteSpaces().
	 *
	 * Each of these methods, which are to be considered private (?), in turn recurses
	 * calling the appropriate With/No WhiteSpaces toString on it's children - thereby
	 * avoiding the test on SPACEOFFSET
	 *
	 * @access public
	 */
	function toString( $depth = 0 )
	{
		if ( $depth == MINIXML_NOWHITESPACES )
			return $this->toStringNoWhiteSpaces();
		else 
			return $this->toStringWithWhiteSpaces( $depth );
	}

	/**
	 * @access public
	 */
	function toStringWithWhiteSpaces( $depth = 0 )
	{
		$attribString = '';
		$elementName  = $this->xname;
		$spaces       = $this->_spaceStr( $depth );
		$retString    = "$spaces<$elementName";
		
		foreach ( $this->xattributes as $attrname => $attrvalue )
			$attribString .= "$attrname=\"$attrvalue\" ";
		
		if ( $attribString )
		{
			$attribString  = rtrim( $attribString );
			$retString    .= " $attribString";
		}
		
		if ( !$this->xnumChildren )
		{
			// No kids -> no sub-elements, no text, nothing - consider a <unary/> element.
			$retString .= " />\n";
			return $retString;
		} 
		
		/**
		 * If we've gotten this far, the element has
		 * kids or text - consider a <binary>otherstuff</binary> element 
		 */
		$onlyTxtChild = 0;

		if ( $this->xnumChildren == 1 && ! $this->xnumElementChildren )
			$onlyTxtChild = 1;
	
		if ( $onlyTxtChild )
		{
			$nextDepth  = 0;
			$retString .= "> ";
		} 
		else 
		{
			$nextDepth  = $depth+1;
			$retString .= ">\n";
		}
		
		for ( $i = 0; $i < $this->xnumChildren ; $i++ )
		{
			if ( method_exists( $this->xchildren[$i], 'toStringWithWhiteSpaces' ) )
			{
				$newStr = $this->xchildren[$i]->toStringWithWhiteSpaces( $nextDepth );
					
				if ( !is_null( $newStr ) )
				{
					if ( !( preg_match("/\n\$/", $newStr ) || $onlyTxtChild ) )
						$newStr .= "\n";
				
					$retString .= $newStr;
				}
			}
		}		
		
		// add the indented closing tag
		if ( $onlyTxtChild )
			$retString .= " </$elementName>\n";
		else
			$retString .= "$spaces</$elementName>\n";
		
		return $retString;
	}	
	
	/**
	 * @access public
	 */
	function toStringNoWhiteSpaces()
	{
		$retString    = '';
		$attribString = '';
		$elementName  = $this->xname;
		
		foreach ( $this->xattributes as $attrname => $attrvalue )
			$attribString .= "$attrname=\"$attrvalue\" ";
		
		$retString = "<$elementName";
		
		if ( $attribString )
		{
			$attribString  = rtrim( $attribString );
			$retString    .= " $attribString";
		}
		
		if ( !$this->xnumChildren )
		{
			// No kids -> no sub-elements, no text, nothing - consider a <unary/> element.			
			$retString .= " />";
			return $retString;
		}
		
		/**
		 * If we've gotten this far, the element has
		 * kids or text - consider a <binary>otherstuff</binary> element.
		 */
		$retString .= ">";
		
		// Loop over all kids, getting associated strings.
		for ( $i = 0; $i < $this->xnumChildren ; $i++ )
		{
			if ( method_exists( $this->xchildren[$i], 'toStringNoWhiteSpaces' ) )
			{
				$newStr = $this->xchildren[$i]->toStringNoWhiteSpaces();
					
				if ( !is_null( $newStr ) )
					$retString .= $newStr;
			}
		}
		
		// add the indented closing tag
		$retString .= "</$elementName>";
		
		return $retString;
	}
	
	/**
	 * Converts an element to a structure - either an array or a simple string.
	 * 
	 * This method is used by MiniXML documents to perform their toArray() magic.
	 *
	 * @access public
	 */
	function toStructure()
	{
		$retHash  = array();
		$contents = "";
		$numAdded = 0;
		
		foreach ( $this->xattributes as $attrname => $attrvalue )
		{
			$retHash[$attrname] = $attrvalue;
			$numAdded++;
		}
		
		for ( $i = 0; $i < $this->xnumChildren; $i++ )
		{
			if ( $this->isElement( $this->xchildren[$i] ) )
			{
				$name     = $this->xchildren[$i]->name();
				$struct   = $this->xchildren[$i]->toStructure();
				$existing = null;
				
				if ( isset( $retHash[$name] ) )
					$existing =& $retHash[$name];
				
				if ( $existing )
				{
					if ( MiniXMLDoc::numKeyArray( $existing ) )
					{
						array_push( $existing, $struct );
					} 
					else 
					{
						$newArray = array();
						array_push( $newArray, $existing );
						array_push( $newArray, $struct   );
						$retHash[$name] =& $newArray;
					}
				} 
				else 
				{	
					$retHash[$name] = $struct;
				}
			
				$numAdded++;
			} 
			else 
			{
				$contents .= $this->xchildren[$i]->getValue();
			}
		}
		
		if ( $numAdded )
		{
			if ( !empty( $contents ) )
				$retHash['-content'] = $contents;
			
			return $retHash;
		} 
		else 
		{
			return $contents;
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
	 * Creates a new MiniXMLNode instance and appends it to the list
	 * of this element's children.
	 * The new child node's value is set to NODEVALUE.
	 *
	 * Returns a reference to the new child node.
	 *
	 * @access public
	 */
	function &createNode( &$value, $escapeEntities = null )
	{
		$newNode      =  new MiniXMLNode( $value, $escapeEntities );
		$appendedNode =& $this->appendNode( $newNode );
		
		return $appendedNode;
	}
		
	/**
	 * appendNode is used to append an existing MiniXMLNode object to
	 * this element's list.
	 *
	 * Returns a reference to the appended child node.
	 *
	 * @access public
	 */
	function &appendNode( &$node )
	{
		if ( is_null( $node ) )
			return null;
		
		if ( !method_exists( $node, 'MiniXMLNode' ) )
			return null;
		
		if ( MINIXML_AUTOSETPARENT )
		{
			if ( $this->xparent == $node )
				return null;
			
			$node->parent( $this );
		}
		
		$idx = $this->xnumChildren++;
		$this->xchildren[$idx] = $node;
		
		return $this->xchildren[$idx];
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _validateChild( &$child )
	{
		if ( is_null( $child ) )
			return null;
		
		if ( !method_exists( $child, 'MiniXMLElement' ) )
			return null;
		
		// Make sure element is named.
		$cname = $child->name();
		
		if ( is_null( $cname ) )
			return null;
		
		// check for loops
		if ( $child == $this )
			return null;
		else if ( $this->xavoidLoops && $child->parent() )
			return null;
		
		return true;
	}
} // END OF MiniXMLElement

?>
