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
 * Javascript remote scripting server include (outputs for ie only).
 *
 * @package peer_js
 */

class JSRemoteScripting extends PEAR
{
	/**
	 * @access public
	 */
	function dispatch( $validFuncs )
	{
  		$func = $this->buildFunc( $validFuncs );
  
		if ( $func != "" )
		{
			$retval;
			eval( "\$retval =  " . $func . ";" );
    
			if ( strlen( $retval ) > 0 )
				$this->returnMessage( $retval . "" );
			else
				$this->returnMessage( "" );
		}
		else
		{
			$this->returnError( "Function builds as empty string." );
		}
	}

	/**
	 * @access public
	 */
	function returnMessage( $payload )
	{
		global $C;
  
		echo(
			"<html><head></head><body onload=\"window.parent.JSRS.Loaded('" . $C . 
			"');\">jsrsPayload:<br>" .
			"<form name=\"jsrs_Form\"><textarea name=\"jsrs_Payload\">" .
			$this->escape( $payload ) .
			"</textarea></form></body></html>"
		);

		exit();
	}

	/**
	 * @access public
	 */
	function escape( $str )
	{
		// escape ampersands so special chars aren't interpreted
		$tmp = ereg_replace( "&", "&amp;", $str );

		// escape slashes  with whacks so end tags don't interfere with return html
		return ereg_replace( "\/" , "\\/", $tmp ); 
	}


	// user methods

	/**
	 * @access public
	 */
	function returnError( $str )
	{
		global $C;
  
		// escape quotes
		$cleanStr = ereg_replace( "\'", "\\'", $str );
  
		// Warning!
		$cleanStr = "jsrsError: " . ereg_replace( "\"", "\\\"", $cleanStr ); 

		echo(
			"<html><head></head><body " .
			"onload=\"window.parent.JSRS.Error('" . $C . "','" . urlencode($str) . "');\">" . $cleanStr . 
			"</body></html>"
		);

		exit();
	}

	/**
	 * User function to flatten 1-dim array to string for return to client.
	 *
	 * @access public
	 */
	function arrayToString( $a, $delim )
	{
		$d = "~";

		if ( !isset( $delim ) )
			$d = $delim;

		return implode( $a, $d ); 
	}

	/**
	 * @access public
	 */
	function buildFunc( $validFuncs )
	{
 		global $F;
 
		$func = ""; 
 
		if ( $F != "" )
		{
			$func = $F;
      
			// make sure it's in the dispatch list
			if ( strpos( strtoupper( $validFuncs ), strtoupper( $func ) ) === false )
				$this->returnError( $func . " is not a valid function." );
   
			$func .= "(";
			$i = 0;
    
			// to optimize...
			eval( "global \$P$i;" );
			$Ptmp = "P" . $i;
     
			while ( $$Ptmp != "" )
			{
				$parm  = $$Ptmp;
				$parm  = substr( $parm, 1, strlen( $parm ) - 2 );
				$func .= "\"" . $parm . "\",";
				
				$i++;
				
				eval( "global \$P$i;" );
				$Ptmp = "P" . $i;
			}
   
			if ( substr( $func, strlen( $func ) - 1, 1 ) == "," )  
				$func = substr( $func, 0, strlen( $func ) - 1 );

			$func .= ")";
		} 
 
		return $func;
	}

	/**
	 * @access public
	 */
	function evalEscape( $thing )
	{
		$tmp = ereg_replace( $thing, "\r\n", "\n" );
		return $tmp;
	}
} // END OF JSRemoteScripting

?>
