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
 * Evaluate Class
 * 
 * The Evaluate class is built on the capabilities of output buffering introduced in PHP4.
 * This makes it possible to capture content sent to the client and use it in the processing. 
 * 
 * streval( $code )
 * The ob_start function initializes the buffering and buffers all output to the client 
 * (except for headers). When the processing is done the ob_get_clean() collects the buffer 
 * and shuts it down. The result is then returned as a result from the streval function.
 *
 * The argument $code contains pure php and will (most likely) fail if an attempt is made to 
 * process non-php code.
 *
 * mixeval( $str )
 * The argument $str contains a mix of enclosed php and non-php code. An attempt to pass pure 
 * php code to the function will fail since it require all php code to be enclosed with <?php ?> 
 * or <? ?>
 *
 * $eval = new Evaluate();
 * echo $eval->mixeval( "<B><?php include(\"obi.php\"); ?></B> <I><? echo \"Hello\";?></I> <U>there</U>" );
 *
 * @package util
 */
 
class Evaluate extends PEAR
{ 
	/**
     * Constructor
	 *
	 * @access public
     */
	function Evaluate() 
	{
   		$this->banPattern = array();
 	}

 
 	/** 
 	 * Evaluate a string by capturing the output.
	 *
	 * @access public
 	 */
 	function streval( $code ) 
	{
   		ob_start();
   		eval( $code );
   		$temp = ob_get_contents();
   		ob_end_clean();
   
   		return $temp;
 	}
 
 	/**
  	 * Extract and evaluate php code.
	 *
	 * @access public
  	 */
 	function mixeval( $str ) 
	{
   		$result = "";
   		
		while ( ( $n = strpos( $str, "<?" ) ) !== false ) 
		{
     		$result .= substr( $str, 0, $n );
	 
	 		if ( substr( $str, $n + 2, 3 ) == "php" ) 
	   			$str = substr( $str, $n + 5 );
	 		else
	   			$str = substr( $str, $n + 2 );
	 
	 		if ( ( $n = strpos( $str, "?>" ) ) !== false ) 
			{
	   			$result .= $this->streval( substr( $str, 0, $n ) );
	   			$str = substr( $str, $n + 2 );
	 		} 
			else 
			{
	   			return $result;
	 		}
   		}
   
   		return $result . $str;
 	}
 
 	/**
 	 * Extract and evaluate php code using regular expressions.
	 *
	 * @access public
 	 */
 	function mixpeval( $str ) 
	{ 
   		if ( preg_match_all( "/(?:(?:<\?php)|(?:<\?))(.*)?(?:\?>)/Usi", $str, $matches, PREG_PATTERN_ORDER ) ) 
		{ 
     		for ( $i = 0; $i < count( $matches[1] ); $i++ ) 
			{ 
       			$replace = $this->streval( $matches[1][$i] ); 
       			$str = preg_replace( "/" . preg_quote( $matches[0][$i] ) . "/U", $replace, $str, 1 ); 
     		} 
   		} 
   
   		return $str; 
 	} 
 
 	/**
 	 * Evaluate a whole file.
	 *
	 * @access public
 	 */
 	function mixevalfile( $filename ) 
	{
   		if ( file_exists( $filename ) )
     		return $this->mixeval( file_get_contents( $filename ) );
   		else
     		return false;
 	}
 
 	/**
 	 * Evaluate a whole file using regular expressions.
	 *
	 * @access public
 	 */
 	function mixpevalfile( $filename ) 
	{
   		if ( file_exists( $filename ) )
     		return $this->mixpeval( file_get_contents( $filename ) );
   		else
     		return false;
 	}
} // END OF Evaluate

?>
