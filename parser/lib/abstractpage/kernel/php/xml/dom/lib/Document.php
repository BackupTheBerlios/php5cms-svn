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


using( 'xml.dom.lib.Node' );


/**
 * The Document-Class. This is the base class for all dom documents (XML, XHTML, WML ...)
 *
 * @package xml_dom_lib
 */
 
class Document extends Node
{
	/**
	 * Stores the type of the document! Currently not used!
	 * @var		string		$doctype
	 * @access  private
	 */
	var $doctype = "";
	
	/**
	 * Stores the link to the document document itself
	 * @var		object		DomDocument		$document
	 * @access  private
	 */
	var $document;
	
	/**
	 * Stores the root of the document
	 * @var		object		Element			$documentElement
	 * @access 	private
	 */
	var $documentElement;
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Document()
	{
		$this->Node();
	}
		

	/**
	 * create() creates an empty Document
	 * 
	 * This function creates an empty Document! This function must 
	 * be redefined by inheriting classes to provide an appropriate skeleton
	 * for the deriving classes.
	 *
	 * @param		string		$root			The name of the root element!
	 * @return		object		DomDocument
	 * @access      public
	 */
	function create( $root )
	{
		$this->document = xmldoc( 
			"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n". 
			"<" . $root . "/>\n"
		); 
		
		return $this->document;
	}
	
	/** 
	 * createFromFile creates a Document by reading an existing file
	 *
	 * The function creates the Document by reading the File from the specified url. If the 
	 * document is not valid, the function returns false.
	 *
	 * @param		string		$url
	 * @return		object		DomDocument
	 * @access      public
	 */
	function createFromFile( $url )
	{
		$this->document = xmldocfile( $url );

		if ( !strtolower( get_class( $this->document) ) == strtolower( "DomDocument" ) )
			return PEAR::raiseError( "URL not valid." );
		
		return $this->document;
	}
	
	/** 
	 * createFromString creates a Document given in the string
	 *
	 * The function creates the Document using the given string. If the 
	 * document is not valid, the function returns false.
	 *
	 * @param		string		$xmldocument
	 * @return		object		DomDocument
	 * @access      public
	 */
	function createFromString( $xmldocument )
	{
		$this->document = xmldoc( $xmldocument );
		
		if ( !strtolower( get_class( $this->document) ) == strtolower( "DomDocument" ) )
			return PEAR::raiseError( "URL not valid." );
		
		return $this->document;
	}
	
	/**
	 * setDocumentElement sets the document root.
	 *
	 * Set the document root. If the current root has existing child nodes they will be destroyed!
	 *
	 * @param		object		Element		The new document root element
	 * @return		object		Element
	 * @access      public
	 */
	function setDocumentElement( &$Element )
	{
		if ( ( !is_subclass_of( $Element, "Element" ) ) && ( !strtolower( get_class( $Element ) ) == strtolower( "Element" ) ) )
			return PEAR::raiseError( "Root element is of the wrong type." );
		
		$Element->node = $this->document->add_root( $Element->nodeName );
		$Element->isRootNode = true;
		
		if ( $Element->node )
		{
			if ( is_array( $Element->attribute ) )
			{
				reset( $Element->attribute );
				
				while (list( $name, $value ) = each( $Element->attribute  ) )
					$Element->node->setattr( $name, $value );
			}
		}
		
		return $Element;
	}
	
	/**
	 * getRoot returns the document root element.
	 *
	 * returns the document root. An object of type Element will be returned.
	 *
	 * @return 		object Element
	 * @see			getDocumentElement()
	 * @access		public
	 * @deprec		Use getDocumentElement() instead
	 * @access      public
	 */
	function getRoot( )
	{
		return $this->getDocumentElement();
	}
	
	/**
	 * getDocumentElement provides direct access to the root element of the document.
	 *
	 * Returns the document root. An object of type Element will be returned.
	 *
	 * @return		object		Element		$root
	 * @access      public
	 */
	function getDocumentElement()
	{
		$ret             = new Element;
		$ret->node       = $this->document->root();
		$ret->tagName    = $ret->node->name;
		$ret->nodeName   = $ret->node->name;
		$ret->isRootNode = true;
		
		if ( $ret->node )
			return $ret;
		
		return false;
	}
	
	/**
	 * getXPathContext creates an instance of an XPath object linked to the current document.
	 *
	 * Returns an XPath object for searching the document with xpath-expressions.
	 *
	 * Usage:
	 * $xpo = $doc->getXPathContext( );
	 * $nlist = $xpo->eval( "//child::text()" );
	 *
	 * This example will retrieve all text nodes!
	 *
	 * @return		object		XPath		A new instance of the XPath object
	 * @access      public
	 */
	function getXPathContext()
	{
		$ret = new XPath( $this );
		return $ret;
	}
	
	/**
	 * setRoot same as setDocumentElement()
	 *
	 * Returns the document root. An object of type Element will be returned.
	 *
	 * @param		object		Element		The new root element of the document!
	 * @return		object		Element
	 * @see			setDocumentElement()
	 * @access		public
	 * @deprec		Use setDocumentElement() instead
	 */
	function setRoot( &$root )
	{
		return $this->documentElement( $root );
	}
	
	/**
	 * getDocType
	 *
	 * currently does nothing
	 */
	function getDocType()
	{
		// currently nothing!
	}
	
	/**
	 * getElementById
	 *
	 * Parses the document for a node with the given "id" attribute!
	 *
	 * @param		string		$IdToSearch
	 * @return		object		Node 
	 * @access      public
	 */
	function getElementById( $IdToSearch )
	{
		$root = $this->getRoot();
		
		if ( is_object($root) )
			return $root->getElementById( $IdToSearch );
		
		return false;
	}
	
	/**
	 * getElementsByTagName returns a NodeList of all the elements with a given tag name
	 *
	 * Returns a NodeList of all the Elements with a given 
	 * tag name in the order in which they would be encountered in a preorder traversal 
	 * of the Document tree.
	 *
	 * @param		string		$NameToSearch		The Name of the elements to collect and return.
	 * @return		object		NodeList
	 * @access		public
	 */
	function getElementsByTagName( $NameToSearch )
	{
		$root = $this->getDocumentElement();
		
		if ( is_object( $root ) )
		{
			return $root->getElementsByTagName( $NameToSearch );
		}
		else
		{
			$elems 	= new NodeList;
			return elems;
		}
	}
	
	/**
	 * toString returns the entire document as string.
	 *
	 * Returns the document as string.
	 *
	 * @return		string		$doc
	 * @access      public
	 */
	function toString()
	{
		return $this->document->dumpmem();
	}
	
	/**
	 * printDocument sends the entire document to the browser.
	 *
	 * @access      public
	 */
	function printDocument( )
	{
		echo( $this->document->dumpmem() );
	}
} // END OF Document

?>
