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


using( 'html.form.lib.FormElement' );


/**
 * @package html_form_lib
 */
 
class FormElement_text extends FormElement
{
	/**
	 * @access public
	 */
	var $maxlength;
	
	/**
	 * @access public
	 */
  	var $minlength;
	
	/**
	 * @access public
	 */
  	var $length_e;
	
	/**
	 * @access public
	 */
  	var $valid_regex;
	
	/**
	 * @access public
	 */
  	var $valid_icase;
	
	/**
	 * @access public
	 */
  	var $valid_e;
	
	/**
	 * @access public
	 */
  	var $pass;
	
	/**
	 * @access public
	 */
  	var $size;


  	/**
	 * Constructor
	 *
	 * @access public
	 */ 
  	function FormElement_text( $a )
	{
		$this->setupElement( $a );
		
		if ( $a["type"] == "password" )
			$this->pass = 1;
	}


	/**
	 * @access public
	 */
	function selfGet( $val, $which, &$count )
	{
    	$str = "";
    
    	if ( is_array( $this->value ) )
      		$v = htmlspecialchars( $this->value[$which] );
    	else 
      		$v = htmlspecialchars( $this->value );
    
		$n = $this->name . ( $this->multiple? "[]" : "" );
    
		$str .= "<input name='$n' value=\"$v\"";
    	$str .= ( $this->pass )? " type='password'" : " type='text'";
    
		if ( $this->maxlength )
      		$str .= " maxlength='$this->maxlength'";
    
		if ( $this->size ) 
      		$str .= " size='$this->size'";
    
		if ( $this->extrahtml ) 
      		$str .= " $this->extrahtml";
    
		$str .= ">";
    
    	$count = 1;
    	return $str;
  	}

	/**
	 * @access public
	 */
  	function selfGetFrozen( $val, $which, &$count )
	{
    	$str = "";
    
    	if ( is_array( $this->value ) )
      		$v = $this->value[$which];
    	else 
      		$v = $this->value;
    
		$n = $this->name . ( $this->multiple? "[]" : "" );
    
		$str .= "<input type='hidden' name='$n' value='$v'>\n";
    	$str .= "<table border=1><tr><td>$v</td></tr></table>\n";
    
    	$count = 1;
    	return $str;
  	}

	/**
	 * @access public
	 */
	function selfGetJS( $ndx_array )
	{
    	$str = "";
    
    	reset( $ndx_array );
    	while ( list( $k, $n ) = each( $ndx_array ) )
		{
      		if ( $this->length_e )
			{
        		$str .= "if (f.elements[${n}].value.length < $this->minlength) {\n";
        		$str .= "  alert(\"$this->length_e\");\n";
        		$str .= "  f.elements[${n}].focus();\n";
        		$str .= "  return(false);\n}\n";
      		}
      
	  		if ( $this->valid_e )
			{
        		$flags = ( $this->icase? "gi" : "g" );
        
				$str .= "if (window.RegExp) {\n";
        		$str .= "  var reg = new RegExp(\"$this->valid_regex\",\"$flags\");\n";
        		$str .= "  if (!reg.test(f.elements[${n}].value)) {\n";
        		$str .= "    alert(\"$this->valid_e\");\n";
        		$str .= "    f.elements[${n}].focus();\n";
        		$str .= "    return(false);\n";
        		$str .= "  }\n}\n";
      		}
    	}
    
    	return $str;
  	}

	/**
	 * @access public
	 */
	function selfValidate( $val )
	{
    	if ( !is_array( $val ) )
			$val = array( $val );
    
		reset( $val );
    	while ( list( $k, $v ) = each( $val ) )
		{
      		if ( $this->length_e && ( strlen( $v ) < $this->minlength ) )
        		return $this->length_e;
      
	  		if ( $this->valid_e && ( ( $this->icase && !eregi( $this->valid_regex, $v ) ) || ( !$this->icase && !ereg( $this->valid_regex, $v ) ) ) )
        		return $this->valid_e;
    	}
    
		return false;
	} 
} // END OF FormElement_text

?>
