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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * ATL internal xml parser.
 *
 * Note: 
 *
 * I didn't use the XML/Parser package because of reference problems due to
 * call_user_func and call_user_method.
 *
 * This problem should vanished with automatic object referencing in php 5
 * (ZendEngine2) remind me to remove this parser at this time.
 * 
 * This class uses "xml_*" php functions to parse xml data. 
 *
 * To create a new xml parser, extends this class and implements following
 * methods.
 *
 * - onElementStart( $tag, $attributes ) 
 * - onElementClose( $tag )
 * - onElementData( $data )
 * - onSpecific( $data )
 *
 * Here's an exemple of xml parser implementation.
 * 
 * class MyParser extends ATL_TEMPLATE_XML_Parser
 * {
 *     function onElementStart( $tag, $attributes )
 *     {
 *         echo "new tag $tag with attributes :", ATL_STRING_LINEFEED;
 *         print_r( $attributes );
 *     }
 *     
 *     function onElementClose( $tag )
 *     {
 *         echo "tag ",$tag," is closed", ATL_STRING_LINEFEED;
 *     }
 *
 *     function onElementData( $data )
 *     {
 *         echo "some plain text : ", $data, ATL_STRING_LINEFEED;
 *     }
 *
 *     function onSpecific( $data )
 *     {
 *         echo "non xml data maybe <?xml...?> :", $data, ATL_STRING_LINEFEED;
 *     }
 * };
 *
 * // MyParser usage :
 * $p = new MyParser();
 * $p->parse( $myString );
 *
 * @package template_atl
 */
 
class ATL_TEMPLATE_XML_Parser extends PEAR
{
	/**
	 * @access private
	 */
	var $_parser;
	
	/**
	 * @access private
	 */
    var $_error = null;
	
	/**
	 * @access private
	 */
    var $_file = '#string';
	
	/**
	 * @access private
	 */
    var $_tags = array();

	/**
	 * @access private
	 */
    var $_xmlErrors = array(
        XML_ERROR_NONE                      	=> "XML_ERROR_NONE",
        XML_ERROR_NO_MEMORY                 	=> "XML_ERROR_NO_MEMORY",
        XML_ERROR_SYNTAX                    	=> "XML_ERROR_SYNTAX",
        XML_ERROR_NO_ELEMENTS               	=> "XML_ERROR_NO_ELEMENTS",
        XML_ERROR_INVALID_TOKEN             	=> "XML_ERROR_INVALID_TOKEN",
        XML_ERROR_UNCLOSED_TOKEN            	=> "XML_ERROR_UNCLOSED_TOKEN",
        XML_ERROR_PARTIAL_CHAR              	=> "XML_ERROR_PARTIAL_CHAR",
        XML_ERROR_TAG_MISMATCH              	=> "XML_ERROR_TAG_MISMATCH",
        XML_ERROR_DUPLICATE_ATTRIBUTE       	=> "XML_ERROR_DUPLICATE_ATTRIBUTE",
        XML_ERROR_JUNK_AFTER_DOC_ELEMENT    	=> "XML_ERROR_JUNK_AFTER_DOC_ELEMENT",
        XML_ERROR_PARAM_ENTITY_REF          	=> "XML_ERROR_PARAM_ENTITY_REF",
        XML_ERROR_UNDEFINED_ENTITY          	=> "XML_ERROR_UNDEFINED_ENTITY",
        XML_ERROR_RECURSIVE_ENTITY_REF      	=> "XML_ERROR_RECURSIVE_ENTITY_REF",
        XML_ERROR_ASYNC_ENTITY              	=> "XML_ERROR_ASYNC_ENTITY",
        XML_ERROR_BAD_CHAR_REF             	 	=> "XML_ERROR_BAD_CHAR_REF",
        XML_ERROR_BINARY_ENTITY_REF         	=> "XML_ERROR_BINARY_ENTITY_REF",
        XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF => "XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF",
        XML_ERROR_MISPLACED_XML_PI          	=> "XML_ERROR_MISPLACED_XML_PI",
        XML_ERROR_UNKNOWN_ENCODING          	=> "XML_ERROR_UNKNOWN_ENCODING",
        XML_ERROR_INCORRECT_ENCODING        	=> "XML_ERROR_INCORRECT_ENCODING",
        XML_ERROR_UNCLOSED_CDATA_SECTION    	=> "XML_ERROR_UNCLOSED_CDATA_SECTION",
        XML_ERROR_EXTERNAL_ENTITY_HANDLING  	=> "XML_ERROR_EXTERNAL_ENTITY_HANDLING",
	);
        

    /**
     * Constructor
	 *
	 * @access public
     */
    function ATL_TEMPLATE_XML_Parser()
    {
        $this->_parser = xml_parser_create();
        xml_set_object( $this->_parser, $this );
        xml_set_element_handler( $this->_parser, "_onElementStart", "_onElementClose" );
        xml_set_character_data_handler( $this->_parser, "_onElementData" );
        xml_set_default_handler( $this->_parser, "_onSpecific" );
        xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, 0 );
    }


	/**
	 * @access public
	 */    
    function parseString( $data )
    {
        return $this->_parse( $data, true );
    }
    
	/**
	 * @access public
	 */ 
    function parseFile( $path )
    {
        $this->_file = $path;
        $fp = @fopen( $path, "r" );
        
		if ( !$fp )
            return PEAR::raiseError( $php_errormsg );
        
        while ( $data = fread( $fp, 1024 ) ) 
		{
            $err = $this->_parse( $data, feof( $fp ) );
			
            if ( PEAR::isError( $err ) ) 
			{
                fclose($fp);
                return $err;
            }
        }
		
        fclose( $fp );
    }

	
    /**
     * Return current parser line number.
     *
     * @return int
	 * @access public
     */
    function getLineNumber()
    {
        return xml_get_current_line_number( $this->_parser );
    }


	// abstract methods

    /**
     * Abstract callback called when a new xml tag is opened.
     *
     * @param string tag Tag name
     * @param hashtable attributes Associative array of attributes
     */
    function onElementStart( $tag, $attributes )
	{
	}

    /**
     * Abstract callback called when a tag is closed.
     *
     * @param string tag Tag name
     */
    function onElementClose( $tag )
	{
	}

    /**
     * Abstract callback called when some #cdata is found.
     *
     * @param string data Content
     */
    function onElementData( $data )
	{
	}

    /**
     * Abstract callback called when non tags entities appear in the document.
     *
     * This method is called by <?xml ...?> <% %> and other specific things like
     * <?php ?>.
     *
     * @param string data strange data content.
     */
    function onSpecific( $data )
	{
	}
	
	
	// private methods
	
    /**
     * Parse specified data and call parser implementation of callback methods.
     *
     * @param  data string Xml data to parse.
	 * @access private
     */
    function _parse( $data, $eof = true )
    {
        $data = str_replace( '&', '&amp;', $data );
        
		if ( !xml_parse( $this->_parser, $data ) ) 
		{
            // ATL errors first
            if ( PEAR::isError( $this->_error ) )
                return $this->_error;
            
            // then look for parser errors
            $err = xml_get_error_code( $this->_parser );
			
            return PEAR::raiseError( $this->_xmlErrors[$err] . ' in ' . $this->_file . ' around line ' . $this->getLineNumber() );
        }
		
        if ( PEAR::isError( $this->_error ) )
            return $this->_error;
        
        return true;
    }

	/**
	 * @access private
	 */
    function _onElementStart( $parser, $tag, $attributes )
    { 
        if ( PEAR::isError( $this->_error ) ) 
			return;
        
		$this->_error = $this->onElementStart( $tag, $attributes );
    }

	/**
	 * @access private
	 */
    function _onElementClose( $parser, $tag )
    {
        if ( PEAR::isError( $this->_error ) ) 
			return;
        
		$this->_error = $this->onElementClose( $tag );
    }

	/**
	 * @access private
	 */
    function _onElementData( $parser, $data )
    {
        if ( PEAR::isError( $this->_error ) ) 
			return;
        
		$this->_error = $this->onElementData( $data );
    }

	/**
	 * @access private
	 */
    function _onSpecific( $parser, $data )
    {
        if ( PEAR::isError( $this->_error ) ) 
			return;
        
		$this->_error = $this->onSpecific( $data );
    }
} // END OF ATL_TEMPLATE_XML_Parser

?>
