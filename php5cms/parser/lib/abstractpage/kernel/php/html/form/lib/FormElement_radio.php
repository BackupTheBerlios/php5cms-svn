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
 
class FormElement_radio extends FormElement
{
	/**
	 * @access public
	 */
	var $valid_e;


	/**
	 * Constructor
	 *
	 * @access public
	 */ 
  	function FormElement_radio( $a )
	{
		$this->setupElement( $a );
	}

	
	/**
	 * @access public
	 */
	function selfGet( $val, $which, &$count )
	{
		$str  = "";
    	$str .= "<input type='radio' name='$this->name' value='$val'";
    
		if ( $this->extrahtml ) 
      		$str .= " $this->extrahtml";
    
		if ( $this->value == $val ) 
      		$str .= " checked";
    
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
    	$x   = 0;
    
		if ( $this->value == $val )
		{
      		$x = 1;
      
	  		$str .= "<input type='hidden' name='$this->name' value='$val'>\n";
      		$str .= "<table border=1 bgcolor=#333333>";
    	}
		else
		{
      		$str .= "<table border=1>";
    	}
    
		$str .= "<tr><td>&nbsp</tr></td></table>\n";
    
    	$count = $x;
    	return $str;
  	}

	/**
	 * @access public
	 */
  	function selfGetJS( $ndx_array )
	{
    	$str = "";
    
    	if ( $this->valid_e )
		{
      		$n = $this->name;
      
	  		$str .= "var l = f.${n}.length;\n";
      		$str .= "var radioOK = false;\n";
      		$str .= "for (i=0; i<l; i++)\n";
      		$str .= "  if (f.${n}[i].checked) {\n";
      		$str .= "    radioOK = true;\n";
      		$str .= "    break;\n";
      		$str .= "  }\n";
      		$str .= "if (!radioOK) {\n";
      		$str .= "  alert(\"$this->valid_e\");\n";
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
    	if ( $this->valid_e && !isset( $val ) )
			return $this->valid_e;
    
		return false;
	}
} // END OF FormElement_radio

?>
