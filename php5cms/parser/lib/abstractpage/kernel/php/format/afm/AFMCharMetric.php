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
 * Helper class for AdobeFontMetricsFile class. This stores metrics for an
 * individual character.
 *
 * @package format_afm
 */
 
class AFMCharMetric extends PEAR
{
	/**
	 * @access private
	 */
	var $name;

	/**
	 * @access private
	 */
	var $ordinal;

	/**
	 * @access private
	 */
	var $width;

	/**
	 * @access private
	 */
	var $ligatures;

	/**
	 * @access private
	 */
	var $llx;

	/**
	 * @access private
	 */
	var $lly;

	/**
	 * @access private
	 */
	var $ulx;

	/**
	 * @access private
	 */
	var $ury;

	
	/**
	 * Constructor
	 */
	function AFMCharMetric() 
	{
		$this->setName( "" );
		$this->setWidth( 0 );
		$this->setOrdinal( 0 );
		$this->clearLigatures();
	}

	
	/**
	 * @access private
	 */
	function setBBox( $llx, $lly, $urx, $ury ) 
	{
		$this->llx = $llx;
		$this->lly = $lly;
		$this->urx = $urx;
		$this->ury = $ury;
	}

	/**
	 * @access private
	 */
	function getLLX() 
	{
		return $this->llx;
	}

	/**
	 * @access private
	 */
	function getLLY() 
	{
		return $this->lly;
	}

	/**
	 * @access private
	 */
	function getURX() 
	{
		return $this->urx;
	}

	/**
	 * @access private
	 */
	function getURY() 
	{
		return $this->ury;
	}

	/**
	 * @access private
	 */
	function setName( $name ) 
	{
		$this->name = $name;
	}

	/**
	 * @access private
	 */
	function getName() 
	{
		return $this->name;
	}

	/**
	 * @access private
	 */
	function setWidth( $width ) 
	{
		$this->width = $width;
	}

	/**
	 * @access private
	 */
	function getWidth() 
	{
		return $this->width;
	}

	/**
	 * @access private
	 */
	function setOrdinal( $value ) 
	{
		$this->ordinal = $value;
	}

	/**
	 * @access private
	 */
	function getOrdinal() 
	{
		return $this->ordinal;
	}

	/**
	 * @access private
	 */
	function clearLigatures() 
	{
		$this->ligatures = array();
	}

	/**
	 * @access private
	 */
	function addLigature( $succ,$lig ) 
	{
		$this->ligatures[$succ] = $lig;
	}

	/**
	 * @access private
	 */
	function getSuccessors() 
	{
		return array_keys( $this->ligatures );
	}

	/**
	 * @access private
	 */
	function getLigature( $succ ) 
	{
		if ( in_array( $succ, array_keys( $this->ligatures ) ) )
			return $this->ligatures[$succ];
		else
			return '';
	}
} // END OF AFMCharMetric

?>
