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
 * XML Parser.
 *
 * @package xml
 */

class XMLParser extends XML
{
	/**
	 * @access public
	 */
    var $parser = null;
	
	/**
	 * @access public
	 */
	var $dataSource = null;

	/**
	 * @access public
	 */
	var $callback = null;


    /**
     * Constructor
     *
     * @access  public
     * @param   array params default null
     */      
    function XMLParser( $params = null ) 
	{
      	$this->XML();
		
      	$this->parser = $this->dataSource = null;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct()
	{
      	$this->_free();
      	parent::__destruct();
    }

    
    /**
     * Parse.
     *
     * @access  public
     * @param   string data
     * @return  bool
     * @throws  Error
     */
    function parse( $data ) 
	{
      	if ( $this->parser == null ) 
			$this->_create();
      
	  	if ( !isset( $this->callback ) || !is_object( $this->callback ) ) 
			return PEAR::raiseError( 'Callback is not an object.' );
      
      	xml_set_object( $this->parser, $this->callback );
      	xml_set_element_handler( $this->parser, 'onStartElement', 'onEndElement' );
      	xml_set_character_data_handler( $this->parser, 'onCData' );
      	xml_set_default_handler( $this->parser, 'onDefault' );

      	if ( !xml_parse( $this->parser, $data ) ) 
		{
        	$type = xml_get_error_code( $this->parser );
        	return PEAR::raiseError( xml_error_string( $type ) );
      	}
         
      	return true;
    }
    

	// private methods
	
    /**
     * Create this parser.
     *
     * @access  private
     * @return  &resource parser handles
     */
    function &_create()
	{
      	$this->parser = xml_parser_create();
      	xml_parser_set_option( $this->parser, XML_OPTION_CASE_FOLDING, false );
      
	  	return $this->parser;
    }
    
    /**
     * Free this parser.
     *
     * @access  private
     */
    function _free()
	{
      	if ( is_resource( $this->parser ) ) 
			return xml_parser_free( $this->parser );
    }
} // END OF XMLParser

?>
