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


/**
 * Implements the Phrasebook pattern
 *
 * This class implements the phrasebook pattern, as documented by Yonat Sharon
 * and Rani Pinchuk in their paper, The Phrasebook Pattern available at
 * http://jerry.cs.uiuc.edu/~plop/plop2k/proceedings/Pinchuk/Pinchuk.pdf. It
 * uses similar calls and XML document format to Rani Pinchuk's 
 * Class::Phrasebook module for Perl. The following documentation detailing 
 * the XML format is from the Class::Phrasebook module. (No reason to
 * rewrite it)
 *
 * This class implements the Phrasebook pattern. It lets us create 
 * dictionaries of phrases. Each phrase can be accessed by a unique key. Each
 * phrase may have placeholders. Group of phrases are kept in a dictionary. 
 * ... The phrases are kept in an XML document.
 * 
 * The XML document type definition is as followed:
 *
 * <pre>
 *  &lt;?xml version="1.0"?&gt;
 *  &lt;!DOCTYPE phrasebook [
 * 	       &lt;!ELEMENT phrasebook (dictionary)*&gt;              
 * 	       &lt;!ELEMENT dictionary (phrase)*&gt;
 *                &lt;!ATTLIST dictionary name CDATA #REQUIRED&gt;
 *                &lt;!ELEMENT phrase (#PCDATA)&gt;
 *                &lt;!ATTLIST phrase name CDATA #REQUIRED&gt;
 *  ]&gt;
 * </pre>
 * Example for XML file:
 * <pre>
 *  &lt;?xml version="1.0"?&gt;
 *  &lt;!DOCTYPE phrasebook [
 * 	       &lt;!ELEMENT phrasebook (dictionary)*&gt;              
 * 	       &lt;!ELEMENT dictionary (phrase)*&gt;
 *                &lt;!ATTLIST dictionary name CDATA #REQUIRED&gt;
 *                &lt;!ELEMENT phrase (#PCDATA)&gt;
 *                &lt;!ATTLIST phrase name CDATA #REQUIRED&gt;
 *  ]&gt;
 *  &lt;phrasebook&gt;
 *  &lt;dictionary name="EN"&gt;
 * 
 *  &lt;phrase name="HELLO_WORLD"&gt;
 *             Hello World!!!
 *  &lt;/phrase&gt;
 * 
 *  &lt;phrase name="THE_HOUR"&gt;
 *             The time now is $hour. 
 *  &lt;/phrase&gt;
 * 
 *  &lt;phrase name="ADDITION"&gt;
 *             add $a and $b and you get $c
 *  &lt;/phrase&gt;
 * 
 * 
 *  &lt;!-- my name is the same in English Dutch and French. --&gt;
 *  &lt;phrase name="THE_AUTHOR"&gt;
 *             Markus Nix
 *  &lt;/phrase&gt;
 *  &lt;/dictionary&gt;
 * 
 *  &lt;dictionary name="FR"&gt;
 *  &lt;phrase name="HELLO_WORLD"&gt;
 *             Bonjour le Monde!!!
 *  &lt;/phrase&gt;
 * 
 *  &lt;phrase name="THE_HOUR"&gt;
 *             Il est maintenant $hour. 
 *  &lt;/phrase&gt;
 * 
 *  &lt;phrase name="ADDITION"&gt;
 *             $a + $b = $c
 *  &lt;/phrase&gt;
 * 
 *  &lt;/dictionary&gt;
 * 
 *  &lt;dictionary name="NL"&gt;
 *  &lt;phrase name="HELLO_WORLD"&gt;
 *             Hallo Werld!!!
 *  &lt;/phrase&gt;
 * 
 *  &lt;phrase name="THE_HOUR"&gt;
 *             Het is nu $hour. 
 *  &lt;/phrase&gt;
 * 
 *  &lt;phrase name="ADDITION"&gt;
 *             $a + $b = $c
 *  &lt;/phrase&gt;
 * 
 *  &lt;/dictionary&gt;
 * 
 *  &lt;/phrasebook&gt;
 * </pre>
 * 
 * Each phrase should have a unique name. Within the phrase text we can 
 * place placeholders. When get method is called, those placeholders will be 
 * replaced by their value.
 * 
 * </snip>
 *
 * <b>Example:</b>
 *
 * <pre>
 * $hPhrase = new Phrasebook( "mydict.xml" );
 * $hPhrase->load( "EN" );
 * print $hPhrase->getPhrase( "ADDITION", array( "a" => "1", "b" => "2", "c" => "3" ) );
 * </pre>
 *
 * @package xml_phrasebook
 */
 
class Phrasebook extends PEAR
{
    /**
     * @access private
     */
    var $stFileName;
	
    /**
     * @access private
     */
    var $stDictionary;
	
    /**
     * @access private
     */
    var $astPhrases;

    /**
     * @access private
     */
    var $stCurrentTag;
	
    /**
     * @access private
     */
    var $stPhraseName;
	
    /**
     * @access private
     */
    var $stPhraseValue;
	
    /**
     * @access private
     */
    var $boReadOn;
	
    /**
     * @access private
     */
    var $boRemoveNewLines;


    /**
     * Constructor
     * 
     * @param  string  a_stFileName  The filename of the xml phrasebook document to read.
     * @access public
     */    
    function Phrasebook( $a_stFileName ) 
	{
        $this->setFileName( $a_stFileName );
        
		$this->stPhrases = array();
        $this->boRemoveNewLines = false;
    }
	
	
    /**
     * Registers the name of the XML phrasebook file to use next time the
     * load function is called. Currently, this looks in the working directory
     * for the XML file.
     * 
     * @param  string  a_stFileName  The filename of the xml phrasebook document to read.
	 * @access public
     */
    function setFileName( $a_stFileName ) 
	{
        $this->stFileName = $a_stFileName;
    }
	
    /**
     * Loads the dictionary from the xml file into memory. Calling this 
     * more than once will not reset the previous dictionary but will instead
     * override and add to it.
     * 
     * @param  string  a_stDictionary  The filename of the dictionary to read from the phrasebook
	 * @access public
     */
    function load( $a_stDictionary = "" ) 
	{
        if ( $a_stDictionary != "" ) 
            $this->setDictionaryName( $a_stDictionary );
        
		$l_hXmlParser = xml_parser_create();
        
		xml_set_element_handler( $l_hXmlParser, array( &$this, "_startElement" ), array( &$this, "_endElement" ) );
        xml_set_character_data_handler( $l_hXmlParser, array( &$this, "_characterData" ) );
        
		if ( !( $l_hFile = fopen( $this->stFileName, "r" ) ) )
            return PEAR::raiseError( "Cannot locate XML data file: " . $this->stFileName );
        
        $this->boReadOn      = false;
        $this->stPhraseName  = "";
        $this->stPhraseValue = "";
        
		// read and parse data
        while ( $l_stData = fread( $l_hFile, 4096 ) ) 
		{
            if ( !xml_parse( $l_hXmlParser, $l_stData, feof( $l_hFile ) ) ) 
			{
                $stError = xml_error_string( xml_get_error_code( $l_hXmlParser ) );
                $err = PEAR::raiseError( sprintf( "XML error: %s at line %d", $stError, xml_get_current_line_number( $l_hXmlParser ) ) );
				xml_parser_free( $l_hXmlParser );
				
				return $err;
            }
        }
		
        xml_parser_free( $l_hXmlParser );
		return true;
    }
	
    /**
     * Returns the phrase from the dictionary and will substitute 
     * variables when appropriate.
     * 
     * @param  string  a_stName     The key of the phrase to return
     * @param  array   a_astValues  Array of strings to replace variables in the phrase with
     * @return string  The phrase with variables replaced if appropriate
	 * @access public
     *            
     */
    function getPhrase( $a_stName, $a_astValues = array() ) 
	{
        if ( !array_key_exists( $a_stName, $this->astPhrases ) ) 
            return "";
			
        $stPhrase = $this->astPhrases[$a_stName];
        $stPhrase = preg_replace( '/(\$)([a-zA-Z0-9_]+)/ie',     "\$a_astValues['$2']", $stPhrase );
        $stPhrase = preg_replace( '/(\$\()([a-zA-Z0-9_]+)\)/ie', "\$a_astValues['$2']", $stPhrase );
        
		if ( $this->boRemoveNewLines ) 
            $stPhrase = str_replace( '\n', '', $stPhrase );
			
        return $stPhrase;
    }
	
    /**
     * Returns the name of the dictionary currently in use.
     *
     * @return string  The name of the current dictionary
	 * @access public
     *            
     */
    function getDictionaryName()
	{
        return $this->stDictionary;
    }
	
    /**
     * Registers the name of the dictionary to use next time 
     * the load function is called. 
     *
     * @param  string  a_stDictionary  The name of the dictionary to use
	 * @access public
     *            
     */
    function setDictionaryName( $a_stDictionary ) 
	{
        $this->stDictionary = $a_stDictionary;
    }
	
    /**
     * Returns the value of the RemoveNewLines setting.
     *
     * @return boolean  The current value of the RemoveNewLines setting
	 * @access public
     *            
     */
    function getRemoveNewLines()
	{
        return $this->boRemoveNewLines;
    }
	
    /**
     * Sets the value of the RemoveNewLines setting. When true,
     * new lines will be stripped when calling the getPhrase method.
     * When false, will leave phrases intact. 
     *
     * @param  boolean  a_boRemoveNewLines  The value of the RemoveNewLines setting
	 * @access public
     *            
     */
    function setRemoveNewLines( $a_boRemoveNewLines ) 
	{
        $this->boRemoveNewLines = $a_boRemoveNewLines;
    }
	
	
	// private methods
	
    /**
     * @access private
     */
    function _startElement( $a_hParser, $a_stName, $a_astAttrs )
	{        
        $this->stCurrentTag  = $a_stName;
        $this->stPhraseValue = "";
        
        switch ( $a_stName )
		{
        	case "DICTIONARY":
            	if ( is_array( $a_astAttrs ) )
				{
                	$stDictionary = $a_astAttrs["NAME"]; // attribute is mandatory but we don't perform a check here
                
					if ( $stDictionary == $this->stDictionary )
                    	$this->boReadOn = true;
                	else
                    	$this->boReadOn = false;
            	}
            	else 
				{
                	die( "The dictionary element must have the name attribute." );
            	}
            
				break;
        
			case "PHRASE":
            	if ( $this->boReadOn ) 
				{
                	if ( is_array( $a_astAttrs ) ) 
					{
                    	$stPhraseName = $a_astAttrs["NAME"]; // attribute is mandatory but we don't perform a check here
                    
						$this->stPhraseName  = $stPhraseName;
                    	$this->stPhraseValue = "";
                	}
            	}
            
				break;
    	}
	}
	
    /**
     * @access private
     */
    function _endElement( $a_hParser, $a_stName )
	{
        if ( $this->boReadOn && strlen( $a_stName ) )
            $this->astPhrases[$this->stPhraseName] = $this->stPhraseValue;
			
        $this->stCurrentTag = "";
    }

    /**
     * @access private
     */    
    function _characterData( $a_hParser, $a_stData )
	{    
        $this->stPhraseValue .= $a_stData;            
    }
} // END OF Phrasebook

?>
