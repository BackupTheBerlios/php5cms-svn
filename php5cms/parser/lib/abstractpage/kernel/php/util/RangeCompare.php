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
 * Compare values within a set range.
 *
 * @package util
 */
 
class RangeCompare extends PEAR
{
	/**
	 * @access public
	 */	
	var $nTopRange;
	
	/**
	 * @access public
	 */	
	var $nBotRange;
	
	/**
	 * @access public
	 */	
	var $nRangeValue;
	
	/**
	 * @access public
	 */	
	var $nCompValue;
	
	/**
	 * @access public
	 */	
	var $nStaticValue;
	
	
	/**
	 * @access public
	 */		
	function compare( $nCompValue, $nStaticValue, $nRangeValue )
	{
		// Define top and bottom ranges.
		$this->nTopRange = $nStaticValue + $nRangeValue;
		$this->nBotRange = $nStaticValue - $nRangeValue;

		// The compared value is within the range of the static value.
		if ( ( $nCompValue <= $this->nTopRange ) && ( $nCompValue >= $this->nBotRange ) )
			return 0;

		// The compared value is less than the range of the static value.
		if ( $nCompValue < $this->nBotRange )
			return -1;

		// The compared value is greater than the range of the static value.
		if ( $nCompValue > $this->nTopRange )
			return 1;
		
		return PEAR::raiseError( "No value." );
	}
} // END OF RangeCompare

?>
