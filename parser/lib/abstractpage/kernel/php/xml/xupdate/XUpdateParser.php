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
|Authors: Moritz Heidkamp <moritz.heidkamp@invision-team.de>           |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * Currently valid XUpdate statements.
 */

define( 'XUPDATE_INSERT_BEFORE', 'xupdate:insert-before' );
define( 'XUPDATE_INSERT_AFTER', 'xupdate:insert-after' );
define( 'XUPDATE_APPEND', 'xupdate:append' );
define( 'XUPDATE_UPDATE', 'xupdate:update' );
define( 'XUPDATE_REMOVE', 'xupdate:remove' );
define( 'XUPDATE_RENAME', 'xupdate:rename' );
define( 'XUPDATE_VARIABLE', 'xupdate:variable' );
define( 'XUPDATE_VALUE_OF', 'xupdate:value-of' );
define( 'XUPDATE_IF', 'xupdate:if' );

define( 'XUPDATE_ELEMENT', 'xupdate:element' );
define( 'XUPDATE_ATTRIBUTE', 'xupdate:attribute' );
define( 'XUPDATE_TEXT', 'xupdate:text' );
define( 'XUPDATE_PROCESSING_INSTRUCTION', 'xupdate:processing-instruction' );
define( 'XUPDATE_COMMENT', 'xupdate:comment' );


/**
 * This class provides a parser for the XUpdate language at its current state.
 * XUpdate XML code is parsed and stored in an easy to use array structure.
 * 
 * Example usage:
 * 
 * // create an instance of XUpdateParser
 * $xup =& new XUpdateParser();
 *
 * // parse test.xml (containing valid XUpdate code), die if any error occurs
 * if (($error = $xup->parseFile('test.xml')) !== true) {
 *    die($error);
 * }
 *
 * // get the resulting array containing the query data (the internal array is empty afterwards
 * // unless you don't provide a parameter. $xup->get(false) would prevent the internal array
 * // from being emptied.
 * $query = $xup->get();
 * 
 * // create a simple output of the resulting array
 * print('<pre>');
 * print_r($query);
 * print('</pre>');
 *
 * @link http://www.xmldb.org/xupdate/xupdate-wd.html for additional information.
 * @package xml_xupdate
 */

class XUpdateParser extends PEAR
{
    /**
     * XUpdate version interpreted by this parser.
	 *
     * @var string
     * @access public
     */
    var $version = '1.0';
    
    /**
     * Holds an instance of the expat XML parser.
	 *
     * @var object
     * @access private
     */
    var $_parser;
    
    /**
     * Whether given XML data is a valid XUpdate query or not.
	 *
     * @var boolean
     * @access private
     */
    var $_isValid = false;
    
    /**
     * Array of parsed query data.
	 *
     * @var array
     * @access private
     */
    var $_query = array();
    
    /**
     * Keeping track of current level (stack index).
	 *
     * @var integer
     * @access private
     */
    var $_level = 0;
    
    /**
     * Keeping track of the parsed elements.
	 *
     * @var array
     * @access private
     */
    var $_stack = array();
        
    /**
     * Index of the current statement (inside $_query).
     * Avoids having to call count().
	 *
     * @var integer
     * @access private
     */
    var $_index = 0;
    
    /** 
     * Are we inside a valid statement?
	 *
     * @var boolean
     * @access private
     */
    var $_inStatement = false;
    
    /**
     * Are we inside an xupdate:element?
	 *
     * @var boolean
     * @access private
     */
    var $_inElement = false;
    
    /**
     * Buffer containing last xupdate:attribute's name.
	 *
     * @var string
     * @access private
     */
    var $_attribute = '';
    
	
    /**
     * Constructor
     */
    function XUpdateParser()
    {
        $this->init();
    }
    

    /**
     * Initializes the Expat parser object.
     *
     * @access public
     */
    function init()
    {
        $this->_parser = xml_parser_create();
        
        xml_set_object( $this->_parser, &$this );
        xml_set_element_handler( $this->_parser, '_handleStartElement', '_handleStopElement' );
        xml_set_character_data_handler( $this->_parser, '_handleCharData' );
        xml_parser_set_option( $this->_parser, XML_OPTION_CASE_FOLDING, 0 );
    }
    
    /**
     * Frees the Expat parser object.
     *
     * @access public
     * @return boolean success
     */
    function free()
    {
        return xml_parser_free( $this->_parser );
    }
    
    /**
     * Parses given XML code (see http://www.php.net/manual/en/function.xml-parse.php).
     * 
     * @access public
     * @param string $xml XML code
     * @param boolean $final whether given XML code is final or not
     * @return mixed true on success, error message otherwise
     */
    function parse( $xml, $final = true )
    {
        if ( !xml_parse( $this->_parser, $xml, $final ) )
			return PEAR::raiseError( sprintf( "XML error: %s at line %d", xml_error_string( xml_get_error_code( $this->_parser ) ), xml_get_current_line_number( $this->_parser ) ) );
        else
            return true;
    }

    /**
     * Parses given file (utilizing XUpdateParser::parse() function).
     * 
     * @access public
     * @param string $file filename
     * @return mixed false on read error, true on success, error message on parse error
     */
    function parseFile( $file )
    {
        if ( !( $fp = fopen( $file, 'r' ) ) )
            return PEAR::raiseError( "Cannot open file $file." );
        
        while ( $xml = fread( $fp, 4096 ) ) 
		{
			$result = $this->parse( $xml, feof( $fp ) );
			
            if ( PEAR::isError( $result ) )
                return $result;
            
            if ( !$this->_isValid )
                return PEAR::raiseError( "Invalid query." );
        }
        
        return true;
    }

    /**
     * Returns internal query array generated by XUpdateParser::parse().
     * 
     * @access public
     * @param boolean $free whether to free the internal array afterwards or not
     * @return array query data
     */
    function get( $free = true )
    {
        $query = $this->_query;
        
        if ( $free ) 
		{
            $this->_index       = 0;
            $this->_attribute   = '';
            $this->_isValid     = false;
            $this->_inElement   = false;
            $this->_inStatement = false;
            $this->_query       = array();
            $this->_variables   = array();
        }
        
        return $query;
    }
	
	
	// private methods
	
    /**
     * Handles the encounter of a start element.
     *
     * @access private
     */
    function _handleStartElement( $parser, $name, $attr )
    {
        if ( ( !$this->_isValid ) && ( ( $name == 'xupdate:modifications' ) && ( $attr['version'] == $this->version ) ) ) 
		{
            $this->_isValid = true;
        }
        else if ( $this->_isValid ) 
		{
            array_push( $this->_stack, $name );
            $this->_level++;
            $this->_parseElement( $name, $attr );
        }
    }

    /**
     * Handles the encounter of a stop element.
     *
     * @access private
     */
    function _handleStopElement( $parser, $name )
    {
        array_pop( $this->_stack );
        $this->_level--;
        
        if ( $this->_level == 0 )
            $this->_inStatement = false;
        
        if ( $name == XUPDATE_ELEMENT )
            $this->_inElement = false;
    }
    
    /**
     * Handles the encounter of char data.
     *
     * @access private
     */
    function _handleCharData( $parser, $data )
    {
        $this->_parseData( $data );
    }

    /**
     * Helper function for XUpdateParser::_handleStartElement().
     *
     * @access private
     */
    function _parseElement( &$name, &$attr )
    {
        // New statement?
        if ( !$this->_inStatement ) 
		{
            // Valid statement?s
            if ( !$this->_isValidStatement( $name ) )
                return;
            
            // Add new statement to the query
            $this->_index = array_push( $this->_query, array( 'statement' => $name ) ) - 1;
            
            // Add attributes, if any
            if ( !empty( $attr ) )
                $this->_query[$this->_index] = array_merge( $this->_query[$this->_index], $attr );
            
            // Check whether we have an xupdate:variable
			if ( $name == XUPDATE_VARIABLE ) 
			{
			    // If so, add it to the stack
				$this->_variables[$attr['name']] = $attr['select'];
			}
            
            // Yes, we are
            $this->_inStatement = true;
        }
        else 
		{
            // Within statement, do something!    
            switch ( $this->_query[$this->_index]['statement'] ) 
			{
                // Insert statement? Then expect xupdate:element!
                case XUPDATE_INSERT_BEFORE:
                
				case XUPDATE_INSERT_AFTER:
                
				case XUPDATE_APPEND:
                    // If we are not inside xupdate:element yet, check whether the current element is an xupdate:element
                    if ( ( !$this->_inElement ) && ( $name == XUPDATE_ELEMENT ) ) 
					{
                        // set the name
                        $this->_query[$this->_index]['name'] = $attr['name'];
                        $this->_query[$this->_index]['element'] = array();
                        $this->_inElement = true;
                    }
                    // If we are inside an element already, parse the values
                    else if ( $this->_inElement ) 
					{
                        switch ( $name ) 
						{
                            // xupdate:attribute?
                            case XUPDATE_ATTRIBUTE:
                                $this->_attribute = $attr['name'];
                                break;
                            
							case XUPDATE_COMMENT:
                                // initialize comment-array
                                if ( !isset( $this->_query[$this->_index]['element']['comments'] ) )
                                    $this->_query[$this->_index]['element']['comments'] = array();
                                
                                break;

                            case XUPDATE_TEXT:
                                break;

                            case XUPDATE_VALUE_OF:
							    // Substitute variable's value
                                $this->_query[$this->_index]['value'] = $this->_variables[substr( $attr['select'], 1 )];
							    break;

                            case XUPDATE_PROCESSING_INSTRUCTION:
                                // initialize pi-array
                                if ( !isset( $this->_query[$this->_index]['element']['pi'] ) )
                                    $this->_query[$this->_index]['element']['pi'] = array();
                                
                                // add name
                                $this->_query[$this->_index]['element']['pi'][] = array( 'name' => $attr['name'] );
                                break;

                            // assume it is a normal value field
                            default:
                                // initialize value-array
                                if ( !isset( $this->_query[$this->_index]['element']['values'] ) )
                                    $this->_query[$this->_index]['element']['values'] = array();
                                
                                // add value
                                $this->_query[$this->_index]['element']['values'][$name] = array();
                                
                                // add attributes, if any
                                if ( !empty( $attr ) )
                                    $this->_query[$this->_index]['element']['values'][$name]['attributes'] = $attr;
                                
                                break;
                        }
                    }
					
                    break;
            }
        }
    }

    /**
     * Helper function for XUpdateParser::_handleCharData().
     *
     * @access private
     */
    function _parseData( &$data )
    {
        if ( !$this->_inStatement )
            return;
        
        switch ( $this->_query[$this->_index]['statement'] )
		{
            // Check current statement
            case XUPDATE_UPDATE:
        
		    case XUPDATE_RENAME:
                $this->_query[$this->_index]['value'] = $data;
                break;
            
            // On insert statement do ...
            case XUPDATE_INSERT_BEFORE:
        
		    case XUPDATE_INSERT_AFTER:
        
		    case XUPDATE_APPEND:
                // are we inside the xupdate:element already?
                if ( !$this->_inElement )
                    break;
                
                switch ( $this->_stack[$this->_level - 1] ) 
				{
                    // inside these elements do nothing
                    case XUPDATE_COMMENT:
                        $this->_query[$this->_index]['element']['comments'][] = $data;
                        break;
                
				    case XUPDATE_TEXT:
                        break;
                
				    // are we inside an xupdate:attribute element?
                    case XUPDATE_ATTRIBUTE:
                        $this->_query[$this->_index]['element']['attributes'][$this->_attribute] = $data;
                        break;
                
				    // are we inside a processing instruction?
                    case XUPDATE_PROCESSING_INSTRUCTION:
                        $this->_query[$this->_index]['element']['pi'][count( $this->_query[$this->_index]['element']['pi'] ) - 1]['attributes'] = $data;
                        break;

                    // otherwise assume that we are inside of a normal value field
                    default:
                        // maybe we have a misinterpreted statement here?
                        if ( $this->_isStatement( $this->_stack[$this->_level - 1] ) )
                            break;
                        
                        // otherwise update the corresponding field
                        $this->_query[$this->_index]['element']['values'][$this->_stack[$this->_level - 1]]['value'] = $data;
                        break;
                }
                
                break;
        }
    }

    /**
     * Checks whether given element name has valid XUpdate statement format.
     * 
     * @access private
     * @param string $str element name
     * @return boolean
     */
    function _isStatement( $str )
    {
        return ( substr( $str, 0, 8 ) == 'xupdate:' );
    }
	
    /**
     * Checks whether given element name is a valid XUpdate statement (see constants section).
     * 
     * @access private
     * @param string $str element name
     * @return boolean
     */
    function _isValidStatement( $str )
    {
        // check whether the form is valid anyway - if not, return false
        if ( !$this->_isStatement( $str ) )
            return false;
        
        switch ( $str ) 
		{
            case XUPDATE_INSERT_BEFORE:
        
		    case XUPDATE_INSERT_AFTER:
        
		    case XUPDATE_APPEND:
        
		    case XUPDATE_UPDATE:
        
		    case XUPDATE_REMOVE:
        
		    case XUPDATE_RENAME:
        
		    case XUPDATE_VARIABLE:
        
		    case XUPDATE_VALUE_OF:
        
		    case XUPDATE_IF:
                return true;
                break;
        
		    default: 
                return false;
                break;
        }
    }
} // END OF XUpdateParser

?>
