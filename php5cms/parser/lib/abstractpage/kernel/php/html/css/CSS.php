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
 * Simple CSS Parser
 *
 * Usage:
 *
 * $css = new CSS();
 * $css->parseStr( "b {font-weight: bold; color: #777777;} b.test{text-decoration: underline;}");
 * echo $css->get( "b", "color" );     		// returns #777777
 * echo $css->get( "b.test", "color" );		// returns #777777
 * echo $css->get( ".test",  "color" ); 	// returns an empty string
 *
 * @package html_css
 */
 
class CSS extends PEAR
{
	/**
	 * @access public
	 */
  	var $css;
	
	/**
	 * @access public
	 */
  	var $html;
  
  	
	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function CSS()
	{
		$this->html = true;
		$this->clear();
  	}
  
  
  	/**
	 * Clears the current content. If the html property of the class is set to true 
	 * then the propertylist is filled with standard html information.
	 *
	 * @access public
	 */
  	function clear()
	{
    	unset( $this->css );
		$this->css = array();
	
		if ( $this->html ) 
		{
	  		$this->add( "ADDRESS", "" );
	  		$this->add( "APPLET", "" );
	  		$this->add( "AREA", "" );
	  		$this->add( "A", "text-decoration : underline; color : Blue;" );
	  		$this->add( "A:visited", "color : Purple;" );
	  		$this->add( "BASE", "" );
	  		$this->add( "BASEFONT", "" );
	  		$this->add( "BIG", "" );
	  		$this->add( "BLOCKQUOTE", "" );
	  		$this->add( "BODY", "" );
	  		$this->add( "BR", "" );
	  		$this->add( "B", "font-weight: bold;" );
	  		$this->add( "CAPTION", "" );
	  		$this->add( "CENTER", "" );
	  		$this->add( "CITE", "" );
	  		$this->add( "CODE", "" );
	  		$this->add( "DD", "" );
	  		$this->add( "DFN", "" );
	  		$this->add( "DIR", "" );
	  		$this->add( "DIV", "" );
	  		$this->add( "DL", "" );
	  		$this->add( "DT", "" );
	  		$this->add( "EM", "" );
	  		$this->add( "FONT", "" );
	  		$this->add( "FORM", "" );
	  		$this->add( "H1", "" );
	  		$this->add( "H2", "" );
	  		$this->add( "H3", "" );
	  		$this->add( "H4", "" );
	  		$this->add( "H5", "" );
	  		$this->add( "H6", "" );
	  		$this->add( "HEAD", "" );
	  		$this->add( "HR", "" );
	  		$this->add( "HTML", "" );
	  		$this->add( "IMG", "" );
	  		$this->add( "INPUT", "" );
	  		$this->add( "ISINDEX", "" );
	  		$this->add( "I", "font-style: italic;" );
	  		$this->add( "KBD", "" );
	  		$this->add( "LINK", "" );
	  		$this->add( "LI", "" );
	  		$this->add( "MAP", "" );
	  		$this->add( "MENU", "" );
	  		$this->add( "META", "" );
	  		$this->add( "OL", "" );
	  		$this->add( "OPTION", "" );
	  		$this->add( "PARAM", "" );
	  		$this->add( "PRE", "" );
	  		$this->add( "P", "" );
	  		$this->add( "SAMP", "" );
	  		$this->add( "SCRIPT", "" );
	  		$this->add( "SELECT", "" );
	  		$this->add( "SMALL", "" );
	  		$this->add( "STRIKE", "" );
	  		$this->add( "STRONG", "" );
	  		$this->add( "STYLE", "" );
	  		$this->add( "SUB", "" );
	  		$this->add( "SUP", "" );
	  		$this->add( "TABLE", "" );
	  		$this->add( "TD", "" );
	  		$this->add( "TEXTAREA", "" );
	  		$this->add( "TH", "" );
	  		$this->add( "TITLE", "" );
	  		$this->add( "TR", "" );
	  		$this->add( "TT", "" );
	  		$this->add( "UL", "" );
	  		$this->add( "U", "text-decoration : underline;" );
	  		$this->add( "VAR", "" );
		}
  	}
  
  	/**
	 * Set how to handle standard html information with clear. 
	 * Set to true to include html properties and false to exclude it.
	 *
	 * @access public
	 */
  	function setHTML( $html ) 
	{
    	$this->html = ( $html != false );
  	}
  
  	/**
	 * Add a new propertystring to th list. The key represents under 
	 * which tag/id/class/subclass to store the information.
	 *
	 * The codestr is a string of css properties. Each property should 
	 * be separated by a ;. Values should be separated from the 
	 * propertynames by a :.
	 *
	 * @access public
	 */
  	function add( $key, $codestr ) 
	{
    	$key = strtolower( $key );
    	$codestr = strtolower( $codestr );
		
    	if ( !isset( $this->css[$key] ) ) 
	  		$this->css[$key] = array();
	
		$codes = explode( ";", $codestr );
	
		if ( count( $codes ) > 0 ) 
		{
	  		foreach ( $codes as $code ) 
			{
	    		$code = trim( $code );
		
				list( $codekey, $codevalue ) = explode( ":", $code );
		
				if ( strlen( $codekey ) > 0 )
		  			$this->css[$key][trim( $codekey )] = trim( $codevalue );
	  		}
		}
  	}
  
  	/**
	 * Retrieve the value of a property.
	 *
	 * @access public
	 */
  	function get( $key, $property ) 
	{
    	$key = strtolower( $key );
    	$property = strtolower( $property );
	
    	list( $tag, $subtag ) = explode( ":", $key );
		list( $tag, $class  ) = explode( ".", $tag );
		list( $tag, $id     ) = explode( "#", $tag );
	
		$result = "";
		foreach ( $this->css as $_tag => $value ) 
		{
      		list( $_tag, $_subtag ) = explode( ":", $_tag );
	  		list( $_tag, $_class  ) = explode( ".", $_tag );
	  		list( $_tag, $_id     ) = explode( "#", $_tag );

      		$tagmatch    = ( strcmp( $tag,    $_tag    ) == 0 ) | ( strlen( $_tag    ) == 0 );
      		$subtagmatch = ( strcmp( $subtag, $_subtag ) == 0 ) | ( strlen( $_subtag ) == 0 );
      		$classmatch  = ( strcmp( $class,  $_class  ) == 0 ) | ( strlen( $_class  ) == 0 );
      		$idmatch     = ( strcmp( $id,     $_id     ) == 0 );

	  		if ( $tagmatch & $subtagmatch & $classmatch & $idmatch ) 
			{
	    		$temp = $_tag;
		
				if ( ( strlen( $temp ) > 0 ) & ( strlen( $_class ) > 0 ) ) 
		  			$temp .= "." . $_class; 
				else if ( strlen( $temp ) == 0 )
		  			$temp = "." . $_class;

				if ( ( strlen( $temp ) > 0 ) & ( strlen( $_subtag ) > 0 ) ) 
		  			$temp .= ":" . $_subtag; 
				else if ( strlen( $temp ) == 0 ) 
		  			$temp = ":" . $_subtag;
	    
				if ( isset( $this->css[$temp][$property] ) )
	      			$result = $this->css[$temp][$property];
	  		}
		}
	
		return $result;
  	}
  
  	/**
	 * Retrieve all properties associated with the given key.
	 *
	 * @access public
	 */
  	function getSection( $key ) 
	{
    	$key = strtolower( $key );
	
    	list( $tag, $subtag ) = explode( ":", $key );
		list( $tag, $class  ) = explode( ".", $tag );
		list( $tag, $id     ) = explode( "#", $tag );
	
		$result = array();
		foreach ( $this->css as $_tag => $value ) 
		{
      		list( $_tag, $_subtag ) = explode( ":", $_tag );
	  		list( $_tag, $_class  ) = explode( ".", $_tag );
	  		list( $_tag, $_id     ) = explode( "#", $_tag );

      		$tagmatch    = ( strcmp( $tag,    $_tag    ) == 0 ) | ( strlen( $_tag    ) == 0 );
      		$subtagmatch = ( strcmp( $subtag, $_subtag ) == 0 ) | ( strlen( $_subtag ) == 0 );
      		$classmatch  = ( strcmp( $class,  $_class  ) == 0 ) | ( strlen( $_class  ) == 0 );
      		$idmatch     = ( strcmp( $id,     $_id     ) == 0 );

	  		if ( $tagmatch & $subtagmatch & $classmatch & $idmatch ) 
			{
	    		$temp = $_tag;
		
				if ( ( strlen( $temp ) > 0 ) & ( strlen( $_class ) > 0 ) ) 
		  			$temp .= "." . $_class; 
				else if ( strlen( $temp ) == 0 ) 
		  			$temp = "." . $_class;
				
				if ( ( strlen( $temp ) > 0 ) & ( strlen( $_subtag ) > 0 ) ) 
		  			$temp .= ":" . $_subtag; 
				else if ( strlen( $temp ) == 0 ) 
		  			$temp = ":" . $_subtag;
		
				foreach ( $this->css[$temp] as $property => $value )
	      			$result[$property] = $value;
	  		}
		}
	
		return $result;
  	}
  
  	/**
	 * Parse a textstring that contains css information.
	 *
	 * @access public
	 */
  	function parseStr( $str ) 
	{
    	$this->clear();
	
		// parse this csscode
		$parts = explode( "}", $str );
	
		if ( count( $parts ) > 0 ) 
		{
	  		foreach ( $parts as $part ) 
			{
	    		list( $keystr, $codestr ) = explode( "{", $part );
				$keys = explode( ",", trim( $keystr ) );
		
				if ( count( $keys ) > 0 ) 
				{
		  			foreach ( $keys as $key ) 
					{
		    			if ( strlen( $key ) > 0 )
		      				$this->add( $key, trim( $codestr ) );
		  			}
				}
	  		}
		}
	
		return ( count( $this->css ) > 0 );
  	}
  
  	/**
	 * Parse a file that contains css information.
	 *
	 * @access public
	 */
  	function parse( $filename ) 
	{
    	$this->clear();
    
		if ( file_exists( $filename ) )
	  		return $this->parseStr( file_get_contents( $filename ) );
		else
	  		return false;
  	}

	/**
	 * Returns a brute style css text compiled of the different properties.
	 *
	 * @access public
	 */
  	function getCSS()
	{
    	$result = "";
    
		foreach ( $this->css as $key => $values ) 
		{
	  		$result .= $key . "\n{\n";
	  
	  		foreach ( $values as $key => $value )
	    		$result .= "    $key: $value;\n";
	  
	  		$result .= "}\n\n";
		}
	
		return $result;
  	}
} // END OF CSS

?>
