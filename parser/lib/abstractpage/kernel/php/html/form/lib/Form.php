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
 
class Form extends PEAR
{
	/**
	 * @access public
	 */
	var $elements;
	
	/**
	 * @access public
	 */
  	var $hidden;
	
	/**
	 * @access public
	 */
  	var $jvs_name;
	
	/**
	 * @access public
	 */
  	var $isfile;
	
	/**
	 * @access public
	 */
  	var $n;

	
	/**
	 * Create an element.
	 *
	 * @access   public
	 * @param    string  element name
	 * @param    array   attributes
	 * @param    string  character data, mainly used for description element
	 * @return   object  CBLElement
	 */
    function &createElement( $name, $attributes = array() )
    {
		$class = "FormElement_" . strtolower( $name );

		using( 'html.form.lib.' . $class );
		
		if ( class_registered( $class ) )
		{
	        $el = &new $class( $attributes );
	        return $el;
		}
		else
		{
			return PEAR::raiseError( 'Cannot create element.' );
		}
    }
	
	/**
	 * @access public
	 */
	function getStart( $jvs_name = "", $method = "", $action = "", $target = "", $form_name = "" )
	{
    	$str = "";
    
    	$this->jvs_name = "";
    	$this->n = 0;
    
		if ( !$method )
			$method = "POST";
    
		if ( !$action )
			$action = $_SERVER["PHP_SELF"];
    
		if ( !$target )
			$target = "_self";

    	$str .= "<form name='$form_name' ";
    
		if ( $this->isfile )
		{
			$str .= " enctype='multipart/form-data'";
			$method = "POST";
		}
		
		$str .= " method='$method'";
    	$str .= " action='$action'";
    	$str .= " target='$target'";
    
		if ( $jvs_name )
		{
      		$this->jvs_name = $jvs_name;
      		$str .= " onsubmit=\"return ${jvs_name}_Validator(this)\"";
    	}
    
    	$str .= ">";
    	return $str;
	}

	/**
	 * @access public
	 */
	function start( $jvs_name = "", $method = "", $action = "", $target = "", $form_name = "" )
	{
    	echo( $this->getStart( $jvs_name, $method, $action, $target, $form_name ) );
  	}

	/**
	 * @access public
	 */
	function getFinish( $after = "", $before = "" )
	{
    	global $sess;
    
		$str = "";
    
    	if ( $this->hidden )
		{
      		reset( $this->hidden );
      		while ( list( $k, $elname ) = each( $this->hidden ) ) 
        		$str .= $this->getElement( $elname );
    	}
    
		if ( is_object( $sess ) && ( $sess->mode == "get" ) )
			$str .= sprintf( "<input type=\"hidden\" name=\"%s\" value=\"%s\">\n", $sess->name, $sess->id );
    
    	$str .= "</form>";

    	if ( $this->jvs_name )
		{
      		$jvs_name = $this->jvs_name;
      		
			$str .= "<script language='javascript'>\n<!--\n";
      		$str .= "function ${jvs_name}_Validator(f) {\n";

      		if ( strlen( $before ) )
        		$str .= "$before\n";
      
	  		reset( $this->elements );
      		while ( list( $k, $elrec ) = each( $this->elements ) )
			{
        		$el   = $elrec["ob"];
        		$str .= $el->selfGetJS( $elrec["ndx_array"] );
      		}
      
	  		if ( strlen( $after ) )
        		$str .= "$after\n";
      
	  		$str .= "}\n//-->\n</script>";
    	}
    
		return $str;
	}

	/**
	 * @access public
	 */  
  	function finish( $after = "", $before = "" )
	{
    	echo( $this->getFinish( $after, $before ) );
  	}

	/**
	 * @access public
	 */  
	function addElement( $el )
	{
    	if ( !is_array( $el ) ) 
      		return false;
    
    	$cv_tab = array(
			"select multiple" => "select",
			"image" => "submit"
		);
    
		if ( $t = $cv_tab[$el["type"]] ) 
      		$t = ( ucfirst( $t ) . "Element" );
    	else
      		$t = ( ucfirst( $el["type"] ) . "Element" );
    
    	// translate names like $foo[int] to $foo{int} so that they can cause no
    	// harm in $this->elements
    	// Original match: if (preg_match("/(\w+)\[(d+)\]/i", $el[name], $regs)) { 
		if ( ereg( "([a-zA-Z_]+)\[([0-9]+)\]", $el["name"], $regs ) )
		{
       		$el["name"]     = sprintf( "%s{%s}", $regs[1], $regs[2] );
       		$el["multiple"] = true;
    	}
    
		$el = new $t( $el );
    	$el->type = $t;
    
		if ( $el->isfile ) 
      		$this->isfile = true;
    
		$this->elements[$el->name]["ob"] = $el;
    
		if ( $el->hidden ) 
      		$this->hidden[] = $el->name;
  	}

	/**
	 * @access public
	 */
	function getElement( $name, $value = false )
	{
    	$str = "";
    	$x   = 0;
    	$flag_nametranslation = false;
    
    	// see addElement: translate $foo[int] to $foo{int}
		// Original pattern: if (preg_match("/(w+)\[(\d+)\]/i", $name, $regs) {
		if ( ereg( "([a-zA-Z_]+)\[([0-9]+)\]", $name, $regs ) )
		{
       		$org_name = $name;
       		$name = sprintf( "%s{%s}", $regs[1], $regs[2] );
       		$flag_nametranslation = true;
    	}
    
    	if ( !isset( $this->elements[$name] ) ) 
      		return false; 

    	if ( !isset( $this->elements[$name]["which"] ) )
      		$this->elements[$name]["which"] = 0;
   
    	$el = $this->elements[$name]["ob"];
    
		if ( $falg_nametranslation == true )
      		$el->name = $org_name; 

    	if ( $value == false ) 
       		$value = $el->value; 

    	if ( $this->elements[$name]["frozen"] )
      		$str .= $el->selfGetFrozen( $value, $this->elements[$name]["which"]++, $x );
    	else
      		$str .= $el->selfGet( $value, $this->elements[$name]["which"]++, $x );
    
		$this->elements[$name]["ndx_array"][] = $this->n;
    	$this->n += $x;
    
    	return $str;
  	}

	/**
	 * @access public
	 */
	function showElement( $name, $value = "" )
	{
    	echo( $this->getElement( $name, $value ) );
  	}

	/**
	 * @access public
	 */
  	function validate( $default = false, $vallist = "" )
	{
    	if ( $vallist )
		{
      		reset( $vallist );
      		$elrec = $this->elements[current( $vallist )];
    	}
		else
		{
      		reset( $this->elements );
      		$elrec = current( $this->elements );
		}
    
		while ( $elrec )
		{
      		$el = $elrec["ob"];
      
	  		if ( $res = $el->marshalDispatch( $this->method, "selfValidate" ) )
        		return $res; 
      
	  		if ( $vallist )
			{
        		next( $vallist );
        		$elrec = $this->elements[current( $vallist )];
      		}
			else
			{
        		next( $this->elements );
        		$elrec = current( $this->elements );
      		}
    	}
    
		return $default;
  	}

	/**
	 * @access public
	 */
  	function loadDefaults( $deflist = "" )
	{
    	if ( $deflist )
		{
      		reset( $deflist );
      		$elrec = $this->elements[current( $deflist )];
		}
		else
		{
      		reset( $this->elements );
      		$elrec = current( $this->elements );
    	} 
    
		while ( $elrec )
		{
      		$el = $elrec["ob"];
      		$el->marshalDispatch( $this->method, "selfLoadDefaults" );
      		$this->elements[$el->name]["ob"] = $el;  // no refs -> must copy back
      
	  		if ( $deflist )
			{
        		next( $deflist );
        		$elrec = $this->elements[current( $deflist )];
      		}
			else
			{
        		next( $this->elements );
       	 		$elrec = current( $this->elements );
      		}
    	}
  	}

	/**
	 * @access public
	 */
	function freeze( $flist = "" )
	{
    	if ( $flist )
		{
      		reset( $flist );
      		$elrec = $this->elements[current( $flist )];
    	}
		else
		{
      		reset( $this->elements );
      		$elrec = current( $this->elements );
    	}
    
		while ( $elrec )
		{
      		$el = $elrec["ob"];
      		$this->elements[$el->name]["frozen"] = 1;
      
	  		if ( $flist )
			{
        		next( $flist );
        		$elrec = $this->elements[current( $flist )];
      		}
			else
			{
        		next( $this->elements );
        		$elrec = current( $this->elements );
      		}
    	}
  	}
} // END OF Form

?>
