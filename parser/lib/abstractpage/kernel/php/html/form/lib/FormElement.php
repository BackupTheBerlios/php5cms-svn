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
 * @package html_form_lib
 */
 
class FormElement extends PEAR
{
	/**
	 * @access public
	 */
	var $name;
	
	/**
	 * @access public
	 */
  	var $value;
	
	/**
	 * @access public
	 */
  	var $multiple;
	
	/**
	 * @access public
	 */
  	var $extrahtml;
	
	
	/**
	 * @access public
	 */
	function marshalDispatch( $m, $func )
	{   
    	$vname = $this->name;
    	global $$vname;
    
		return $this->$func( $$vname );
	}

	/**
	 * @access public
	 */  
	function selfGet( $val, $which, &$count )
	{
  	}

	/**
	 * @access public
	 */  
  	function selfShow( $val, $which )
	{
    	$count = 0;
    	echo( $this->selfGet( $val, $which, $count ) );
    
		return $count;
  	}

	/**
	 * @access public
	 */
  	function selfGetFrozen( $val, $which, &$count )
	{
    	return $this->selfGet( $val, $which, $count );
  	}

	/**
	 * @access public
	 */
  	function selfShowFrozen( $val, $which )
	{
    	$count = 0;
    	echo( $this->selfGetFrozen( $val, $which, $count ) );
    
		return $count;
  	}

	/**
	 * @access public
	 */
  	function selfValidate( $val )
	{
    	return false;
  	}

	/**
	 * @access public
	 */
  	function selfGetJS( $ndx_array )
	{
  	}

	/**
	 * @access public
	 */  
  	function selfPrintJS( $ndx_array )
	{
    	echo( $this->selfGetJS( $ndx_array ) );
  	}

	/**
	 * Note that this function is generally quite simple since
  	 * most of the work of dealing with different types of values
  	 * is now done in show_self.  It still needs to be overidable,
  	 * however, for elements like checkbox that deal with state
  	 * differently
	 *
	 * @access public
	 */
  	function selfLoadDefaults( $val )
	{
    	$this->value = $val;
  	}

	/**
	 * Helper function for compatibility.
	 *
	 * @access public
	 */
  	function setupElement( $a )
	{
    	$cv_tab = array(
			"type"		 => "ignore",
        	"min_l"		 => "minlength",
        	"max_l"	 	 => "maxlength",
        	"extra_html" => "extrahtml"
		);
    
		reset( $a );
    	while ( list( $k, $v ) = each( $a ) )
		{
      		if ( $cv_tab[$k] == "ignore" )
				continue;
      		else
				$k = ( $cv_tab[$k]? $cv_tab[$k] : $k );
      
	  		$this->$k = $v;
    	}
  	}
} // END OF FormElement

?>
