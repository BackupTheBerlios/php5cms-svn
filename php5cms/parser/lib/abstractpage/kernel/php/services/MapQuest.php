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
 * @package services
 */
 
class MapQuest extends PEAR
{
	/**
	 * if this ever changes, you will need to edit
	 * @access public
	 */
	var $mq_url = "http://mapquest.com/maps/map.adp";

	/**
	 * @access public
	 */
	var $mq_qstring = array(
		// you will have to study the results from the country select box here:
		// http://mapquest.com/maps/main.adp to find the abbreviation for the country
		// you are requesting a map from.  
		// for example, Germany is "DE"
		"country=" => "US",
		"address=" => "",
		"city="    => "chicago",
		"state="   => "IL",
		"zipcode=" => "",
		"zoom="    => "8", // default for zoom -- the range is 0 - 9
		"dtype="   => "s", // s = streetmap, a= aerial view
	);

	/**
	 * no css class by default, otherwise make the var = "name_of_class"
	 * @access public
	 */
	var $a_css = "";
	
	/**
	 * if you don't like new windows, make it "_top"
	 * @access public
	 */
	var $a_target = "_blank";
	
	/**
	 * what to show as the link text
	 * @access public
	 */
	var $a_text = "check mapquest.com";

	/**
	 * use this for javascript tags .. 
	 * @access public
	 */
	var $a_extra = "";

	/**
	 * result
	 * @access public
	 */
	var $mq = "";
	
	
	/**
	 * @access public
	 */	
	function addQvar( $Qvar, $Qvar_val )
	{
		$Qvar = ( !eregi( "=", $Qvar ) )? $Qvar . "=" : $Qvar;
		$this->mq_qstring[$Qvar] = $Qvar_val;

		return true;
	}

	/**
	 * @access public
	 */	 
	function makeA()
	{
		$this->mq .= "\n<a ";
		
		if ( !empty( $this->a_css ) ) 
			$this->mq .= "class=" . $this->a_css . " ";
			 
		$this->mq .= "href=\"";
		$this->makeHREF();
		$this->mq .= "\" target=".$this->a_target." ";
		
		if ( !empty( $this->a_extra ) ) 
			$this->mq .= $this->a_extra . " ";
			 
		$this->mq .= ">" . $this->a_text . "</a>\n";
		return true;
	}
	
	/**
	 * @access public
	 */
	function makeHREF()
	{
		$this->mq .= $this->mq_url; 
		
		// make query string:
		if ( !is_array( $this->mq_qstring ) )
		{
			return false;
		} 
		else 
		{
			$c = 1;
			foreach ( $this->mq_qstring as $key => $val )
			{	
				if ( $c > 1 )
					$this->mq .= "&";
				else
					$this->mq .= "?";
				
				$this->mq .= $key;
				$this->mq .= urlencode( $val );
				$c++;
			}
			
			return true;
		}
	}
	
	/**
	 * @access public
	 */
	function zoom( $val )
	{
		// if out of range, back to default
		if ( $val > 9 || $val < 0 ) 
			$val = 8;
		$this->mq_qstring['zoom='] = $val;
		return true;
	}

	/**
	 * @access public
	 */	
	function printA()
	{
		$this->makeA();
		print $this->mq;
		
		return true;
	}
	
	/**
	 * @access public
	 */
	function printHREF()
	{
		$this->makeHREF();
		print $this->mq;
		
		return true;
	}
} // END OF MapQuest

?>
