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
 
class FormElement_textarea extends FormElement
{
	/**
	 * @access public
	 */
	var $rows;
	
	/**
	 * @access public
	 */
	var $cols;
	
	/**
	 * @access public
	 */
	var $wrap;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function FormElement_textarea( $a )
	{
		$this->setupElement( $a );
	}


	/**
	 * @access public
	 */
	function selfGet( $val, $which, &$count )
	{
		$str  = "";
		$str .= "<textarea name='$this->name'";
		$str .= " rows='$this->rows' cols='$this->cols'";
		
		if ( $this->wrap ) 
			$str .= " wrap='$this->wrap'";
		
		if ( $this->extrahtml ) 
			$str .= " $this->extrahtml";
		
		$str .= ">" . htmlspecialchars( $this->value ) ."</textarea>";
    
		$count = 1;
		return $str;
	}

	/**
	 * @access public
	 */
	function selfGetFrozen( $val, $which, &$count )
	{
		$str  = "";
		$str .= "<input type='hidden' name='$this->name'";
		$str .= " value='$this->value'>\n";
		$str .= "<table border=1><tr><td>\n";
		$str .= nl2br( $this->value );
		$str .= "\n</td></tr></table>\n";
    
		$count = 1;
		return $str;
	}
} // END OF FormElement_textarea

?>
