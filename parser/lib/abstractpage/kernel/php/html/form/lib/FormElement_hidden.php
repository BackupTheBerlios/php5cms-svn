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
 
class FormElement_hidden extends FormElement
{
	/**
	 * @access public
	 */
	var $hidden = 1;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function FormElement_hidden( $a )
	{
		$this->setupElement( $a );
	}


	/**
	 * @access public
	 */
	function selfGet( $val, $which, &$count )
	{
    	$str = "";
    	$v   = ( is_array( $this->value )? $this->value : array( $this->value ) );
    	$n   = $this->name . ( $this->multiple? "[]" : "" );
    
		reset( $v );
    	while ( list( $k, $tv ) = each( $v ) )
		{
      		$str .= "<input type='hidden' name='$n' value='$tv'";
      
	  		if ( $this->extrahtml )
        		$str .=" $this->extrahtml";
      
	  		$str .= ">";
    	}
    
    	return $str;
  	}
} // END OF FormElement_hidden

?>
