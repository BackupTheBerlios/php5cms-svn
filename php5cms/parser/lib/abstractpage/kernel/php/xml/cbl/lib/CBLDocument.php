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
 * no filename given
 */
define( 'CBLDOCUMENT_ERROR_NO_FILENAME', 100 );
 
/**
 * file not writeable
 */
define( 'CBLDOCUMENT_ERROR_NOT_WRITEABLE', 101 );

/**
 * element not found
 */
define( 'CBLDOCUMENT_ERROR_ELEMENT_NOT_FOUND', 102 );

/**
 * element not found
 */
define( 'CBLDOCUMENT_ERROR_INVALID_ROOT_ELEMENT', 103 );


/**
 * @package xml_cbl_lib
 */
 
class CBLDocument extends PEAR
{
	/**
	 * filename
	 *
	 * @access private
	 * @var  string
	 */
    var $_filename;

	/**
	 * namespace for CBL elements
	 *
	 * @access private
	 * @var  string
	 */
    var $_ns;

	/**
	 * filename of the DTD
	 *
	 * @access private
	 * @var  string
	 */
	var $_dtd;

	/**
	 * root element
	 *
	 * Should be a window or dialog element
	 *
	 * @access   private
	 * @var      object CBLElement
	 */
	var $_root;
    
	/**
	 * flag to indicate whether element attributes should be validated
	 *
	 * @access   private
	 * @var      boolean
	 */
    var $_autoValidate = false;


	/**
	 * Constructor
	 *
	 * @access   public
	 * @param    string  filename
	 * @param    string  namespace for CBL elements
	 */
	function CBLDocument( $filename = null, $ns = null )
    {
		$this->_filename = $filename;
        $this->_ns       = $ns;
    }


	/**
	 * Set the URI of the DTD.
	 *
	 * @access   public
	 * @param    string  uri of the dtd
	 */
	function setDTD( $uri )
    {
		$this->_dtd = $uri;
    }
    
	/**
	 * Enable validation.
	 *
	 * @access   public
	 * @param    boolean
	 */
	function enableValidation( $enable = true )
    {
        $this->_autoValidate = $enable;
    }
    
	/**
	 * Add the root element.
	 *
	 * @access   public
	 * @param    object  root element
	 */
	function addRoot( &$el )
	{
		if ( is_a( $el, "CBLElement_cbl" ) )
		{
		 	$el->isRoot = true;
			$el->setNamespace( $this->_ns );

			$this->_root = &$el;
			return true;
		}
		else
		{
			return PEAR::raiseError( 'Invalid root element.', CBLDOCUMENT_ERROR_INVALID_ROOT_ELEMENT );
		}
	}
    
	/**
	 * Send the document to the output stream.
	 *
	 * Use this method to display the document.
	 *
	 * @access   public
	 */
	function send()
    {
		header( 'Content-type: text/xml' );
		echo $this->serialize();
    }

	/**
	 * Write the document to a file.
	 *
	 * You may specify a filename to override the filename passed in
	 * the constructor.
	 *
	 * @access   public
	 * @param    string  filename
	 */
    function save( $filename = null )
    {
		if ( $filename == null )
            $filename = $this->_filename;

        if ( empty( $filename ) )
            return PEAR::raiseError( 'No filename specified to write document to.', CBLELEMENT_ERROR_NO_FILENAME );

        $fp = @fopen( $filename, 'wb' );
        
		if ( !$fp )
            return PEAR::raiseError( 'Could not write destination file.', CBLELEMENT_ERROR_NOT_WRITEABLE );
        
        flock( $fp, LOCK_EX );
        fputs( $fp, $this->serialize() );
        flock( $fp, LOCK_UN );
        fclose( $fp );

        return true;
    }

	/**
	 * Serialize the document.
	 *
	 * @access public
	 * @return string
	 */
	function serialize()
	{
		if ( $this->_root == null )
			return;
			
		$doc = XML::getXMLDeclaration() . "\n";

        /**
         * add the DTD
         */
        if ( $this->_dtd != null )
            $doc .= XML::getDocTypeDeclaration( $this->_root->getElementName(), $this->_dtd ) . "\n";
        
        $doc .= $this->_root->serialize();
		return $doc;
    }

	/**
	 * Create any CBL element.
	 *
	 * @access   public
	 * @param    string  element name
	 * @param    array   attributes
	 * @param    string  character data, mainly used for description element
	 * @return   object  CBLElement
	 */
    function &createElement( $name, $attributes = array(), $cdata = null, $replaceEntities = true )
    {
		$class = "CBLElement_" . strtolower( $name );

		using( 'xml.cbl.lib.elements.' . $class );
		
		if ( class_registered( $class ) )
		{
	        $el = &new $class( $attributes, $cdata );
	        $el->setNamespace( $this->_ns );
	        $el->setDocument( $this );
	        $el->replaceEntities = $replaceEntities;
			
	        if ( $this->_autoValidate )
			{
				/*
				 * so we enable child element validation as well
				 */
				$el->enableChildValidation();
				
	            $result = $el->validateAttributes();
			
	            if ( PEAR::isError( $result ) )
	                return $result;
	        }
        
	        return $el;
		}
		else
		{
			return PEAR::raiseError( 'Cannot create element.', CBLDOCUMENT_ERROR_ELEMENT_NOT_FOUND );
		}
    }

	/**
	 * Get an element by its id.
	 *
	 * @access   public
	 * @param    string  id
	 * @return   object  CBLElement
	 */
    function &getElementById( $id )
    {
        return $this->_root->getElementById( $id );
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
        return $this->_root->getElementsByTagname( $tagname );
    }

	/**
	 * Get debug info about the document as string.
	 * Use this instead of a print_r on the tree.
	 *
	 * @access   public
	 * @return   string
	 */
    function getDebug()
    {
		$debug  = "CBLDocument\n";
        $debug .= " +-namespace      : {$this->_ns}\n";
        $debug .= " +childNodes\n";
        $debug .= $this->_root->getDebug( ' ', true );
		
        return $debug;
    }

	/**
	 * Show debug info about the document.
	 * Use this instead of a print_r on the tree.
	 *
	 * @access   public
	 * @uses     getDebugInfo()
	 */
	function showDebug()
    {
        echo $this->getDebug();
    }
} // END OF CBLDocument

?>
