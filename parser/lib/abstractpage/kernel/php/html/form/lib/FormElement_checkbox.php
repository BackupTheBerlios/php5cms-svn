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
 
class FormElement_checkbox extends FormElement
{
	/**
	 * @access public
	 */
	var $checked;

  
  	/**
	 * Constructor
	 */ 
  	function FormElement_checkbox( $a )
	{
		$this->setupElement( $a );
	}


	/**
	 * @access public
	 */
	function selfGet( $val, $which, &$count )
	{
    	$str = "";
    
    	if ( $this->multiple )
		{
      		$n    = $this->name . "[]";
      		$str .= "<input type='checkbox' name='$n' value='$val'";
      
	  		if ( is_array( $this->value ) )
			{
        		reset( $this->value );
        		while ( list( $k, $v ) = each( $this->value ) )
				{
         	 		if ( $v == $val )
					{
            			$str .= " checked"; 
            			break; 
          			}
        		}
      		}
    	}
		else
		{
			$str .= "<input type='checkbox' name='$this->name'";
      		$str .= " value='$this->value'";
      
	  		if ( $this->checked ) 
        		$str .= " checked";
    	}
    
		if ( $this->extrahtml ) 
      		$str .= " $this->extrahtml";
    
		$str .= ">\n";
    
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
    	$t   = "";
    
		if ( $this->multiple )
		{
      		$n = $this->name . "[]";
      
	  		if ( is_array( $this->value ) )
			{
        		reset( $this->value );
				while ( list( $k, $v ) = each( $this->value ) )
				{
          			if ( $v == $val )
					{
	          			$x    = 1;
            			$str .= "<input type='hidden' name='$this->name' value='$v'>\n";
            			$t    =" bgcolor=#333333";
            
						break;
          			}
        		}
      		}
    	}
		else
		{
      		if ( $this->checked )
			{
        		$x = 1;
        		$t = " bgcolor=#333333";
        		
				$str .= "<input type='hidden' name='$this->name'";
        		$str .= " value='$this->value'>";
      		}
    	}
    
		$str .= "<table$t border=1><tr><td>&nbsp</td></tr></table>\n";

   	 	$count = $x;
    	return $str;
	}

	/**
	 * @access public
	 */  
	function selfLoadDefaults( $val )
	{
    	if ( $this->multiple )
     	 	$this->value = $val;
    	else if ( isset( $val ) && ( !$this->value || $val == $this->value ) ) 
      		$this->checked = 1;
    	else 
      		$this->checked = 0;
  	}
} // END OF FormElement_checkbox

?>
