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
 * Class for phone number formatting
 *
 * @package util_telephony
 */
 
class PhoneNumber extends PEAR
{
	/**
	 * @access public
	 */
	var $format;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PhoneNumber( $format )
	{
		$this->setFormat( $format );
	}
	
	
	/**
	 * @access public
	 */
	function setFormat( $format = "unformatted" )
	{
		$this->format = $format;
	}
	
	/**
	 * @access public
	 */
	function phone_assemble( $phonevar, $array_index = -1 )
	{
		global ${$phonevar . "_1"};
		global ${$phonevar . "_2"};
		global ${$phonevar . "_3"};
		global ${$phonevar . "_4"};
		global ${$phonevar . "_5"};

		if ( $array_index == -1 )
		{
    		$p1 = ${$phonevar . "_1"};
    		$p2 = ${$phonevar . "_2"};
    		$p3 = ${$phonevar . "_3"};
    		$p4 = ${$phonevar . "_4"};
    		$p5 = ${$phonevar . "_5"};
		}
		else
		{
    		$p1 = ${$phonevar . "_1"}[$array_index];
    		$p2 = ${$phonevar . "_2"}[$array_index];
    		$p3 = ${$phonevar . "_3"}[$array_index];
    		$p4 = ${$phonevar . "_4"}[$array_index];
    		$p5 = ${$phonevar . "_5"}[$array_index];
  		}
  
		// return the composited string
		switch ( $this->format )
		{
    		case "unformatted" :
     			return $p1 . $p2 . $p3 . $p4 . $p5;
     			break;

			case "usa" :
     			return $p1 . $p2 . $p3 . $p4;
     			break;

			case "europe" :
				return $p1 . $p2 . $p3 . $p4 . $p5;
				break;

			default :
				break;
		}
	}

	/**
	 * @access public
	 */
	function phone_entry( $phonevar, $array_index = -1 )
	{
		global $$phonevar;
		global ${$phonevar . "_1"};
		global ${$phonevar . "_2"};
		global ${$phonevar . "_3"};
		global ${$phonevar . "_4"};
		global ${$phonevar . "_5"};

		// clear buffer
		$buffer = "";

		// move into local vars
		if ( ( $array_index + 0 ) == -1 )
		{
    		$w  = $$phonevar;
    		$p1 = ${$phonevar . "_1"};
    		$p2 = ${$phonevar . "_2"};
    		$p3 = ${$phonevar . "_3"};
    		$p4 = ${$phonevar . "_4"};
    		$p5 = ${$phonevar . "_5"};
    		$suffix = "";
  		}
		else
		{
    		$w  = ${$phonevar}[$array_index];
    		$p1 = ${$phonevar . "_1"}[$array_index];
    		$p2 = ${$phonevar . "_2"}[$array_index];
    		$p3 = ${$phonevar . "_3"}[$array_index];
    		$p4 = ${$phonevar . "_4"}[$array_index];
    		$p5 = ${$phonevar . "_5"}[$array_index];
    		$suffix = "[]";
  		}

		if ( !empty( $w ) && empty( $p1 ) )
		{
    		// if the whole thing is there, split into parts
    		switch ( $this->format )
			{
      			case "europe":
			       	$buffer .=

         "<INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_1\" 
	  SIZE=3 MAXLENGTH=2 VALUE=\"".prepare($p1)."\">
         &nbsp; <B>-</B> &nbsp;
         <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_2\" 
	  SIZE=3 MAXLENGTH=2 VALUE=\"".prepare($p2)."\">
         &nbsp; <B>-</B> &nbsp;
         <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_3\"
	  SIZE=3 MAXLENGTH=2 VALUE=\"".prepare($p3)."\">
         &nbsp; <B>-</B> &nbsp;
         <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_4\" 
	  SIZE=3 MAXLENGTH=2 VALUE=\"".prepare($p4)."\">
         &nbsp; <B>-</B> &nbsp;
         <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_5\" 
	  SIZE=3 MAXLENGTH=2 VALUE=\"".prepare($p5)."\">\n";

					break;

	      		case "usa":
		       		$buffer .=

         "<B>(</B> &nbsp;
	  <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_1\" 
	  SIZE=4 MAXLENGTH=3 VALUE=\"".prepare($p1)."\">
         &nbsp; <B>)</B> &nbsp;
	  <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_2\" 
	  SIZE=4 MAXLENGTH=3 VALUE=\"".prepare($p2)."\">
         &nbsp; <B>-</B> &nbsp;
	  <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_3\" 
	  SIZE=5 MAXLENGTH=4 VALUE=\"".prepare($p3)."\">
	 &nbsp; <B>x</B> &nbsp; 
	  <INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."_4\" 
	  SIZE=5 MAXLENGTH=4 VALUE=\"".prepare($p4)."\">\n";

					break;
    
    	  		case "unformatted" :

				default :
    	   			$buffer .=
 
         "<INPUT TYPE=TEXT NAME=\"".prepare($phonevar)."\" ". 
	 "SIZE=16 MAXLENGTH=16 VALUE=\"".prepare($w)."\">\n";

	       			break;
			}
		}

		// return proper part
		return $buffer;
	}

	/**
	 * @access public
	 */
	function phone_vars( $varname )
	{
		return array(
			$varname,
			$varname . "_1",
			$varname . "_2",
			$varname . "_3",
			$varname . "_4",
			$varname . "_5"
		);
	}
} // END OF PhoneNumber

?>
