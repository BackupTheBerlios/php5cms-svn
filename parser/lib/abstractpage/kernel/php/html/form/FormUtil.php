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
 
class FormUtil extends PEAR
{
    /**
     * @var		string
     * @access	private
     */
    var $tokenarray = '__token';

    /**
     * Name of hidden element
     * @var		string
     * @access	public
     */
	var $tokenname = '__token';
	
	
	/**
	 * Methods to avoid form reloads.
	 *
	 * Usage:
	 *
	 * session_start();
	 * $f =& new FormUtil;
     *
	 * if ( isset( $_POST['submit'] ) ) 
	 * {
     * 		if ( $f->easycheck() )
     *   		print "Data can be saved.";
     *		else
     *   		print "This is a reload.";
     * }
     * else 
	 * {
	 *		printf( "<form action='%s' method='post'>%s", $PHP_SELF, $f->getFormToken() );
     *		print "<input type='text' name='name'>";
     *		print "<input type='submit' name='submit' value='Click me'>";
     * 		print "</form>";
	 * }
	 *
	 * @access public
     */
    function getFormToken()
	{
     	$tok = md5( uniqid( "foobarmagic" ) );
   		return sprintf( "<input type='hidden' name='%s' value='%s'>", $this->tokenname, htmlspecialchars( $tok ) );
    }

	/**
	 * @access public
	 */
    function easyCheck() 
	{
        $tok = $_POST[$this->tokenname];
        
		if ( isset( $_SESSION[$this->tokenarray][$tok] ) ) 
		{
            return false;
        } 
		else 
		{
            $_SESSION[$this->tokenarray][$tok] = true;
            return true;
        }
    }
	
	/**
	 * @access public
	 */
	function getCombo( 
		$name = "name", 		// name of select-tag
		$content = array(), 	// options-values and strings
		$multiple = false,	 	// multiple selections allowed?
		$size = 1, 				// vertical size
		$onchange = "", 		// javascript-eventhandler, executed when selecting an option. Do not use with multiple=true!
		$selected = array() )	// selected option-values/key from $content
	{
  		if ( $multiple )
   			$mu = " multiple";
  
  		if ( $onchange != "" )
   			$oc = " onchange=\"$onchange\"";
  
  		$out = "<select name=\"$name\" size=$size$mu$oc>\n";
  
  		if ( count( $content ) > 0 )
  		{
   			while( list( $k, $v ) = each( $content ) )
   			{
    			$out .= "\t<option value=\"$k\"";

				for ( $i = 0; $i < count( $selected ); $i++ )
    			{
     				if ( $selected[$i] == $k )
      					$out .= " selected";
    			}

				$out .= ">$v</option>\n";
   			}
  		}
  		else
		{
			$out .= "\t<option value=\"\">(no options defined)</option>\n";
		}

		$out .= "  </select>\n";
		return $out;
	}
} // END OF FormUtil

?>
