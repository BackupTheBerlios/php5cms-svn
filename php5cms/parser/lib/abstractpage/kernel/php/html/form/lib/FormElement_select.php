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
 
class FormElement_select extends FormElement
{
	/**
	 * @access public
	 */
	var $options;
	
	/**
	 * @access public
	 */
  	var $size;
	
	/**
	 * @access public
	 */
  	var $valid_e;

 
 	/**
	 * Constructor
	 *
	 * @access public
	 */ 
  	function FormElement_select( $a )
	{
		$this->setupElement( $a );
		
    	if ( $a["type"] == "select multiple" )
			$this->multiple = 1;
  	}
  
  
  	/**
  	 * @access public
	 */
  	function selfGet( $val, $which, &$count )
	{
    	$str = "";
    
    	if ( $this->multiple )
		{
      		$n = $this->name . "[]";
      		$t = "select multiple";
    	}
		else
		{
      		$n = $this->name;
      		$t = "select";
    	}
    
		$str .= "<$t name='$n'";
    
		if ( $this->size ) 
      		$str .= " size='$this->size'";
    
		if ( $this->extrahtml ) 
      		$str .= " $this->extrahtml";
    
		$str .= ">";

    	reset( $this->options );
    	while ( list( $k, $o ) = each( $this->options ) )
		{
      		$str .= "<option";
      
	  		if ( is_array( $o ) ) 
        		$str .= " value='" .  $o["value"] . "'";
      
	  		if ( !$this->multiple && ( $this->value == $o["value"] || $this->value == $o ) )
			{
        		$str .= " selected";
      		}
			else if ( $this->multiple && is_array( $this->value ) )
			{
        		reset( $this->value );
        		while ( list( $tk, $v ) = each( $this->value ) )
				{
          			if ( $v == $o["value"] || $v == $o )
					{ 
            			$str .= " selected";
						break; 
          			}
        		}
      		}
      
	  		$str .= ">" . ( is_array( $o )? $o["label"] : $o ) . "\n";
    	}
    
		$str .= "</select>";
    
    	$count = 1;
    	return $str;
  	}

	/**
	 * @access public
	 */
	function selfGetFrozen( $val, $which, &$count )
	{
    	$str     = "";
    	$x       = 0;
    	$n       = $this->name . ( $this->multiple ? "[]" : "" );
    	$v_array = ( is_array( $this->value )? $this->value : array( $this->value ) );
    	
		$str .= "<table border=1>\n";
    
		reset( $v_array );
    	while ( list( $tk, $tv ) = each( $v_array ) )
		{
      		reset( $this->options );
      		while ( list( $k, $v ) = each( $this->options ) )
			{
        		if ( ( is_array( $v ) && ( ( $tmp = $v["value"] ) == $tv || $v["label"] == $tv ) ) || ( $tmp = $v ) == $tv )
				{
					$x++;
					$str .= "<input type='hidden' name='$n' value='$tmp'>\n";
					$str .= "<tr><td>" . ( is_array( $v )? $v["label"] : $v) . "</td></tr>\n";
        		}
      		}
    	}
    
		$str .= "</table>\n";
    
    	$count = $x;
    	return $str;
  	}

	/**
	 * @access public
	 */
	function selfGetJS( $ndx_array )
	{
    	$str = "";
    
    	if ( !$this->multiple && $this->valid_e )
		{
      		$str .= "if (f.$this->name.selectedIndex == 0) {\n";
      		$str .= "  alert(\"$this->valid_e\");\n";
      		$str .= "  f.$this->name.focus();\n";
      		$str .= "  return(false);\n";
      		$str .= "}\n";
    	}
    
    	return $str;
  	}

	/**
	 * @access public
	 */
  	function selfValidate( $val )
	{
    	if ( !$this->multiple && $this->valid_e )
		{
      		reset( $this->options );
      		$o = current( $this->options );
      
	  		if ( $val == $o["value"] || $val == $o )
				return $this->valid_e;
    	}
    
		return false;
	}
} // END OF FormElement_select

?>
