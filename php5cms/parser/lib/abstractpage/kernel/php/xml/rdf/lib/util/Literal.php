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
|Authors: Chris Bizer <chris@bizer.de>                                 |
|         Daniel Westphal <dawe@gmx.de>                                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'xml.rdf.lib.util.RdfNode' );


define( "LITERAL_DATATYPE_SHORTCUT_PREFIX", "datatype:" );
define( "LITERAL_DATATYPE_SCHEMA_URI",      "http://www.w3.org/TR/xmlschema-2" );


/**
 * An RDF literal.
 * The literal supports the xml:lang and rdf:datatype property.
 * For XML datatypes see: http://www.w3.org/TR/xmlschema-2/
 *
 * @package xml_rdf_lib_util
 */

class Literal extends RdfNode
{
	/**
	 * Label of the literal
	 *
	 * @var		string
	 * @access	private
	 */	
    var $label;
    
	/**
	 * Language of the literal
	 *
	 * @var		string
	 * @access	private
	 */	
    var $lang; 

    /**
	 * Datatype of the literal
	 *
	 * @var		string
	 * @access	private
	 */	
    var $dtype; 


    /**
     * Constructor
     *
     * @param	string	$str			label of the literal
     * @param 	string $language		optional language identifier
     *
     */
	function Literal( $str, $language = null )
	{
		$this->dtype = null;
	    $this->label = $str;
    
		if ( $language != null )
			$this->lang = $language;
		else
			$this->lang = null;
	}
  
  
  	/**
   	 * Returns the string value of the literal.
   	 *
   	 * @access	public
   	 * @return	string value of the literal
   	 */
  	function getLabel()
	{
   	 	return $this->label;
  	}

  	/**
   	 * Returns the language of the literal.
   	 *
   	 * @access	public 
   	 * @return	string language of the literal
   	 */
  	function getLanguage()
	{
    	return $this->lang;
  	}

  	/**
   	 * Returns the datatype of the literal.
   	 *
   	 * @access	public 
   	 * @return	string datatype of the literal
   	 */
  	function getDatatype()
	{
    	return $this->dtype;
  	}

  	/**
   	 * Sets the datatype of the literal.
   	 * Instead of datatype URI, you can also use an datatype shortcuts like STRING or INTEGER.
   	 * The array $short_datatype with the possible shortcuts is definded in ../constants.php
   	 *
   	 * @access	public 
   	 * @param  string URI of XML datatype or datatype shortcut 
   	 */
  	function setDatatype( $datatype )
	{
		// RDF DATATYPE SHORTCUTS (extends datatype shortcuts to the full XML datatype URIs)
		$short_datatype = array(
			'STRING'    => LITERAL_DATATYPE_SCHEMA_URI . "#string",
			'DECIMAL'   => LITERAL_DATATYPE_SCHEMA_URI . "#decimal",
			'INTEGER'   => LITERAL_DATATYPE_SCHEMA_URI . "#integer",
			'INT'       => LITERAL_DATATYPE_SCHEMA_URI . "#int",
			'SHORT'     => LITERAL_DATATYPE_SCHEMA_URI . "#short",
			'BYTE'      => LITERAL_DATATYPE_SCHEMA_URI . "#byte",
			'LONG'      => LITERAL_DATATYPE_SCHEMA_URI . "#long",
			'LANGUAGE'  => LITERAL_DATATYPE_SCHEMA_URI . "#language",
			'NAME'      => LITERAL_DATATYPE_SCHEMA_URI . "#name"
		);

		if  ( stristr( $datatype, LITERAL_DATATYPE_SHORTCUT_PREFIX ) )
			$this->dtype = $short_datatype[substr( $datatype, strlen( LITERAL_DATATYPE_SHORTCUT_PREFIX ) )]; 
		else	
	    	$this->dtype = $datatype;
  	}

  	/**
  	 * Checks if ihe literal equals another literal.
  	 * Two literals are equal, if they have the same label and they
  	 * have the same language/datatype or both have no language/datatype property set. 
  	 *
  	 * @access	public 
  	 * @param   object	literal $that
  	 * @return	boolean 
  	 */  
  	function equals( $that )
	{
		if ( ( $that == null ) || !( is_a( $that, "Literal" ) ) )
	  		return false;

		if ( ( $this->label == $that->getLabel() ) && ( ( ( $this->lang == $that->getLanguage() ) || ( $this->lang == null && $that->getLanguage() == null ) ) && ( ( $this->dtype == $that->getDatatype() || ( $this->dtype == null && $that->getDatatype() == null ) ) ) ) ) 
			return true;

    	return false;
  	}

  	/**
   	 * Dumps literal.
   	 *
   	 * @access	public 
   	 * @return	string 
   	 */  
  	function toString()
	{
		$dump = "Literal(\"" . $this->label . "\"";
		
		if ( $this->lang != null )
			$dump .= ", lang=\"" . $this->lang . "\"";
		
		if ( $this->dtype != null )
			$dump .= ", datatype=\"" . $this->dtype . "\"";
		
		$dump .= ")";    
    	return $dump;
  	}
} // END OF Literal

?>
