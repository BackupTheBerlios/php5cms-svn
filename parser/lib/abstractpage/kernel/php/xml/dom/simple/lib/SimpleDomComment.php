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
 * @package xml_dom_simple_lib
 */
 
class SimpleDomComment extends PEAR
{
	/**
	 * @access public
	 */
	var $comments = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SimpleDomComment() 
	{
		$arga = func_num_args();
		$args = func_get_args();

		if ( $arga > 0 ) 
		{
			if ( $arga == 1 && is_array( $args[0] ) )
				$this->comments = $args[0];
 			else
				$this->comments = $args;
		}  
	}


	/**
	 * @access public
	 */
	function setComments( $comments ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( is_array( $comments ) ) 
		{
			if ( ! empty( $comments ) ) 
			{
				if ( isset( $comments[0] ) ) 
				{
					$this->comments = $comments;
					$result = true;
				} 
				else 
				{
					$GLOBALS["AP_DOM_ERRORS"] = 2;
				}  
			} 
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
		} 

		return $result;
	} 

	/**
	 * @access public
	 */
	function addComment() 
	{
		$arga = func_num_args();
		$args = func_get_args();

		if ( $arga > 0 ) 
		{
			for ( $j = 0; $j < $arga; $j++ ) 
			{
				$i = count( $this->comments );
				$this->comments[$i] = $args[$j];
			}  
		}

		return true;
	} 
	
	/**
	 * @access public
	 */
	function getComments() 
	{
		return $this->comments;
	} 

	/**
	 * @access public
	 */
	function getComment( $index ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;

		if ( is_long( $index ) ) 
		{
			if ( isset( $this->comments[$index] ) ) 
			{
				$result = $this->comments[$index];
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 4; // index out of bounds
				$result = false;
			} 
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
			$result = false;
		}  
		
		return $result;
	}

	/**
	 * @access public
	 */
  	function toString() 
	{
		$result = "Comments: <br><br>\n\n";

		for ( $i = 0; $i< count( $this->comments ); $i++ ) 
			$result .= "&nbsp;&nbsp;&nbsp;" . $this->comments[$i] . "<br>\n";

		return $result;
	} 
} // END OF SimpleDomComment

?>
