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
 * @package html_form
 */
 
class FormBuilder extends PEAR
{
	/**
	 * This function builds form element input type HTML.
	 *
	 * @access  public
	 * @param	string $type  type of form elements
	 * @param	array $input_ary  associative array of form element info.
	 * @return	string  form element input type HTML
	 */
	function print_input_element( $type, $input_ary )
	{
		$elm = "<input type=\"".$type. "\" ";
  
		foreach ( $input_ary as $key => $value )
		{
  			if ( $input_ary[$key]["value"] )
			{
   				if ( @$input_ary[$key]["type"] == "bool" )
    				$elm .= $key." ";
   				else
    				$elm .= $key."=\"".$input_ary[$key]["value"]."\" ";
  			}
		}

		$elm .= ">";
		return $elm;
	}

	/**
	 * This function builds form element textarea HTML.
	 *
	 * @access  public
	 * @param	string $type  type of form elements
	 * @param	array $input_ary  associative array of form element info.
	 * @return	string  form element textarea HTML
	 */
	function print_textarea_element( $type, $input_ary )
	{
		$elm  = "<".$type." "; 
		$text = $input_ary["value"]["value"];
		$input_ary["value"]["value"] = "";
 
		foreach ( $input_ary as $key => $value )
		{
  			if ( $input_ary[$key]["value"] )
			{
   				if ( $input_ary[$key]["type"] == "bool" )
    				$elm .= $key." ";
   				else
    				$elm .= $key."=\"".$input_ary[$key]["value"]."\" ";
  			}
 		}

		$elm .= ">".$text."</$type>";
		return $elm;
	}

	/**
	 * This function builds form element selectbox HTML.
 	 *
	 * @access  public
 	 * @param	string $type  type of form elements
	 * @param	array $input_ary  associative array of form element info.
	 * @param	array $opt_ary  associative array of form element options
	 * @param	array $options  options
	 * @return	string  form element selectbox HTML
	 */
	function print_select_element( $type, $input_ary, $opt_ary, $options )
	{
		$elm = "<".$type." "; 
 
		foreach ( $input_ary as $key => $value )
		{
  			if ( $input_ary[$key]["value"] )
			{
   				if ( $input_ary[$key]["type"] == "bool" )
    				$elm .= $key." ";
   				else
    				$elm .= $key."=\"".$input_ary[$key]["value"]."\" ";
  			}
 		}

		$elm .= ">";
		$elm .= $this->print_options( $opt_ary, $options );
		$elm .= "</$type>";

		return $elm;
	}

	/**	
	 * This function builds form element options HTML.
	 *
	 * @access  public
	 * @param	array $input_ary  associative array of form element options
	 * @param	array $options  options
	 * @return	string  form element options HTML
	 */
	function print_options( $input_ary, $options )
	{
		$elm = "";

		foreach ( $options as $key => $value )
		{
  			$this->set_values( $options[$key], $input_ary );
  			$elm .= "<option "; 
  
			$text = $input_ary["text"]["value"];
			$input_ary["text"]["value"] = "";
 
			foreach ( $input_ary as $key => $value )
			{
   				if ( $input_ary[$key]["value"] )
				{
    				if ( $input_ary[$key]["type"] == "bool" )
     					$elm .= $key." ";
    				else
     					$elm .= $key."=\"".$input_ary[$key]["value"]."\" ";
   				}
  			}
  
  			$elm .= ">";
  			$elm .= $text;
  			$elm .= "</option>\n";
		}

		return $elm;
	}

	/**
 	 * This function sets the value field of the array holding form element info.
 	 *
	 * @access  public
	 * @param	array $atr_ary  array containing form element values
	 * @param	array $input_ary  associative array to be filled with form
	 * 			element values
	 */
	function set_values( $atr_ary, &$input_ary )
	{
		reset( $atr_ary );

		foreach ( $atr_ary as $key => $value )
  			$input_ary[$key]["value"] = $value;
	}

	/**
	 * This function builds HTML for text, file-upload, radio, checkbox, submit,
	 * hidden, image, reset, password, button, textarea, or select form element
	 * type to be built.
	 *
	 * @access  public
	 * @param	array $atr_ary  array containing attributes for form element
	 * 			to be built
	 * @return	string  form element HTML
	 */
	function form_element( $atr_ary )
	{
		if ( !isset( $atr_ary["type"] ) || $atr_ary["type"] == "" )
  			return "form_element called without a type specified. error!";
 
		$type = $atr_ary["type"];
		unset( $atr_ary["type"] );
 
		if ( !isset( $atr_ary["name"] ) || $atr_ary["name"] == "" )
  			return "form_element $type called without a name specified. error!";
 
		switch ( $type )
		{
			case "text" :
   				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"size"      => array( "type" => "int",  "value" => "" ),
					"maxlength" => array( "type" => "int",  "value" => "" ),
					"readonly"  => array( "type" => "bool", "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" ),
					"onblur"    => array( "type" => "js",   "value" => "" ),
					"onfocus"   => array( "type" => "js",   "value" => "" ),
					"onchange"  => array( "type" => "js",   "value" => "" ),
					"onselect"  => array( "type" => "js",   "value" => "" )
				);
                     
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "file" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"size"      => array( "type" => "int",  "value" => "" ),
					"maxlength" => array( "type" => "int",  "value" => "" ),
					"accept"    => array( "type" => "str",  "value" => "" ),
					"readonly"  => array( "type" => "bool", "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" ),
					"onblur"    => array( "type" => "js",   "value" => "" ),
					"onfocus"   => array( "type" => "js",   "value" => "" ),
					"onchange"  => array( "type" => "js",   "value" => "" )
				);
	
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "radio" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"checked"   => array( "type" => "bool", "value" => "" ),
					"readonly"  => array( "type" => "bool", "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" )
				);
   	
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "checkbox" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"checked"   => array( "type" => "bool", "value" => "" ),
					"readonly"  => array( "type" => "bool", "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" )
				);
        	             
   				$this->set_values( $atr_ary, $elem_ary );
  	 			$element = $this->print_input_element( $type, $elem_ary );
   
  	 			break;
   
			case "submit" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" )
				);
    	                 
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "hidden" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" )
				);
   
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "image" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"align"     => array( "type" => "str",  "value" => "" ),
					"alt"       => array( "type" => "str",  "value" => "" ),
					"border"    => array( "type" => "int",  "value" => "" ),
					"usemap"    => array( "type" => "str",  "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" )
				);
    	                 
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "reset" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" )
				);
                     
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "password" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"size"      => array( "type" => "int",  "value" => "" ),
					"maxlength" => array( "type" => "int",  "value" => "" ),
					"readonly"  => array( "type" => "bool", "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" ),
					"onblur"    => array( "type" => "js",   "value" => "" ),
					"onfocus"   => array( "type" => "js",   "value" => "" ),
					"onchange"  => array( "type" => "js",   "value" => "" ),
					"onselect"  => array( "type" => "js",   "value" => "" )
				);
        	             
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
				
				break;
   
			case "button" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"notab"     => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"taborder"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" ),
					"onblur"    => array( "type" => "js",   "value" => "" ),
					"onfocus"   => array( "type" => "js",   "value" => "" )
				);
   	
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_input_element( $type, $elem_ary );
			
				break;
   
			case "textarea" :
				$elem_ary = array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"value"     => array( "type" => "str",  "value" => "" ),
					"cols"      => array( "type" => "int",  "value" => "" ),
					"rows"      => array( "type" => "int",  "value" => "" ),
					"wrap"      => array( "type" => "str",  "value" => "" ),
					"readonly"  => array( "type" => "bool", "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"accesskey" => array( "type" => "str",  "value" => "" ),
					"onblur"    => array( "type" => "js",   "value" => "" ),
					"onfocus"   => array( "type" => "js",   "value" => "" ),
					"onchange"  => array( "type" => "js",   "value" => "" ),
					"onselect"  => array( "type" => "js",   "value" => "" )
				);
   	
				$this->set_values( $atr_ary, $elem_ary );
				$element = $this->print_textarea_element( $type, $elem_ary );
			
				break;
   
			case "select" :
				$opt_ary =  array(
					"value"     => array( "type" => "str",  "value" => "" ),
					"text"      => array( "type" => "str",  "value" => "" ),
					"selected"  => array( "type" => "bool", "value" => "" ),
					"label"     => array( "type" => "str",  "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" )
				);
				
				$sel_ary =  array(
					"name"      => array( "type" => "str",  "value" => "" ),
					"multiple"  => array( "type" => "bool", "value" => "" ),
					"options"   => array( "type" => "ary",  "value" => "" ),
					"disabled"  => array( "type" => "bool", "value" => "" ),
					"tabindex"  => array( "type" => "int",  "value" => "" ),
					"onblur"    => array( "type" => "js",   "value" => "" ),
					"onfocus"   => array( "type" => "js",   "value" => "" ),
					"onchange"  => array( "type" => "js",   "value" => "" ),
				);
			
				$this->set_values( $atr_ary, $sel_ary );
				$options = $sel_ary["options"]["value"];
				unset( $sel_ary["options"] );
				$element = $this->print_select_element( $type, $sel_ary, $opt_ary, $options );
			
				break;
  
			default :
				return "make_form_element type: $type is unknown";
		}
 
		return $element;
	}
} // END OF FormBuilder

?>
