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


using( 'format.afm.AFMCharMetric' );


/**
 * Adobe Font Metrics class
 * 
 * This is used to read AFM files and retrieve information from them.
 *
 * @package format_afm
 */
 
class AFMFile extends PEAR 
{
	/**
	 * @access private
	 */
	var $AFM;

	/**
	 * @access private
	 */
	var $byName;

	/**
	 * @access private
	 */
	var $byOrd;

	/**
	 * @access private
	 */
	var $ascender;

	/**
	 * @access private
	 */
	var $descender;

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
	var $urx;

	/**
	 * @access private
	 */
	var $ury;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function AFMFile() 
	{
		$this->clear();
	}


	/**
	 * @access public
	 */
	function clear() 
	{
		$this->AFM    = array();
		$this->byName = array();
		$this->byOrd  = array();
	}

	/**
	 * Load font metrics from file.
	 *
	 * @access public
	 */
	function loadAFMFile( $filename ) 
	{
		$this->AFM = file( $filename, 1 );
		$this->hash();
	}

	/**
	 * Load font metrics from array.
	 *
	 * @access public
	 */
	function loadAFMArray( $A ) 
	{
		$this->AFM = $A;
		$this->hash();
	}

	/**
	 * Retrieve the character represented by given name.
	 *
	 * @access public
	 */
	function getCharByName( $name ) 
	{
		return chr( $this->byName[$name]->getOrdinal() );
	}

	/**
	 * This calculates the width of a string, taking into
	 * account any ligatures that will be used when rendering
	 * the string.
	 *
	 * @access public
	 */
	function getWidth( $string ) 
	{
		$width = 0;

		$A = preg_split( "//", $string );
		$i = 0;
		
		while ( $i < count( $A ) )
		{
			if ( ord( $A[$i] ) == 0 ) 
			{
				$i++;
				continue;
			}

			if ( !is_object( $this->byOrd[ord( $A[$i] )] ) ) 
			{
				$i++;
				continue;
			}

			// ligature check
			if ( $i < count( $A ) - 1 ) 
			{
				$lig = $this->byOrd[ord( $A[$i] )]->getLigature( $A[$i + 1] );
				
				if ( $lig != '' ) 
				{
					$width += $this->byName[$lig]->getWidth();
					$i += 2;
					
					continue;
				}
			}

			// no ligature, just a plain old character
			$width += $this->byOrd[ord( $A[$i] )]->getWidth();
			$i++;
		}

		return $width;
	}

	/**
	 * This returns the greatest ascent among the characters
	 * in the given string.
	 *
	 * @access public
	 */
	function getAscent( $string ) 
	{
		$result = 0;
		$A = preg_split( "//", $string );
		
		for ( $i = 0; $i < count( $A ); $i++ ) 
		{
			if ( ord( $A[$i] ) == 0 ) 
				continue;

			if ( !is_object( $this->byOrd[ord( $A[$i] )] ) )
				continue;

			$ascent = abs( $this->byOrd[ord( $A[$i] )]->getURY() );
			$result = max( $result, $ascent );
		}
		
		return $result;
	}

	/**
	 * This returns the greatest height among the characters
	 * in the given string.
	 *
	 * @access public
	 */
	function getHeight( $string ) 
	{
		$result = 0;
		$A = preg_split( "//", $string );
		
		for ( $i = 0; $i < count( $A ); $i++ ) 
		{
			if ( !is_object( $this->byOrd[ord( $A[$i] )] ) )
				continue;

			$height =
				$this->byOrd[ord( $A[$i] )]->getURY() -
				$this->byOrd[ord( $A[$i] )]->getLLY();

			$result = max( $result, $height );
		}
		
		return $result;
	}

	/**
	 * This returns the greatest descent among the characters
	 * in the given string.
	 *
	 * @access public
	 */
	function getDescent( $string ) 
	{
		$result = 0;
		$A = preg_split( "//", $string );
		
		for ( $i = 0; $i < count( $A ); $i++ ) 
		{
			if ( ord( $A[$i] ) == 0 ) 
				continue;

			if ( !is_object( $this->byOrd[ord( $A[$i] )] ) )
				continue;

			$descent = $this->byOrd[ord( $A[$i] )]->getLLY();
			$result  = min( $result, $descent );
		}
		
		return $result;
	}

	/**
	 * Retrieve the ascender value for the font.
	 *
	 * @access public
	 */
	function getAscender() 
	{
		return $this->ascender;
	}

	/**
	 * Retrieve the descender value for the font.
	 *
	 * @access public
	 */
	function getDescender() 
	{
		return $this->descender;
	}

	/**
	 * Retrieve the lower left bounding box x value.
	 *
	 * @access public
	 */
	function getLLX() 
	{
		return $this->llx;
	}

	/**
	 * Retrieve the lower left bounding box y value.
	 *
	 * @access public
	 */
	function getLLY() 
	{
		return $this->lly;
	}

	/**
	 * Retrieve the upper right bounding box x value.
	 *
	 * @access public
	 */
	function getURX() 
	{
		return $this->urx;
	}

	/**
	 * Retrieve the upper right bounding box y value.
	 *
	 * @access public
	 */
	function getURY() 
	{
		return $this->ury;
	}

	
	// private methods
	
	/**
	 * Parse the AFM information for use by other methods.
	 *
	 * @access private
	 */
	function hash() 
	{
		$inmetrics  = false;
		$inkerndata = false;

		foreach ( $this->AFM AS $line ) 
		{
			$line = chop( $line ); // strip newlines

			// search for end of char metrics
			if ( preg_match( "/^EndCharMetrics/", $line ) )
				$inmetrics = false;

			// search for end of kern data
			if ( preg_match( "/^EndKernData/", $line ) )
				$inkerndata = false;

			// operate on this line if it's a font metric line
			if ( $inmetrics ) 
			{
				$metric = new AFMCharMetric();

				// break it up by semi-colon
				$pairs = preg_split( "/ *; */", $line );
				
				foreach ( $pairs AS $pair ) 
				{
					$values = array();
					$values = preg_split( "/ +/", $pair );

					switch ( $values[0] ) 
					{
						case "C":
							$metric->setOrdinal( $values[1] );
							break;
						
						case "WX":
							$metric->setWidth( $values[1] );
							break;
						
						case "N":
							$metric->setName( $values[1] );
							break;
						
						case "L":
							$metric->addLigature( $values[1], $values[2] );
							break;
						
						case "B":
							$metric->setBBox( $values[1], $values[2], $values[3], $values[4] );
							break;
					}
				}

				// add the metric to our list
				$this->byName[$metric->getName()] = $metric;
				
				if ( $metric->getOrdinal() > -1 )
					$this->byOrd[$metric->getOrdinal()] = $metric;
			} 
			else if ( $inkerndata ) 
			{
				// it's a kerning data line (we don't care about these)
			} 
			else 
			{
				// otherwise, it's just a (key, value) line

				$values = array();
				$values = preg_split( "/ +/", $line );

				switch ( $values[0] ) 
				{
					case "Ascender":
						$this->ascender = $values[1];
						break;
					
					case "Descender":
						$this->descender = $values[1];
						break;
					
					case "FontBBox":
						$this->llx = $values[1];
						$this->lly = $values[2];
						$this->urx = $values[3];
						$this->ury = $values[4];
						break;
				}
			}

			// search for beginning of char metrics
			if ( preg_match( "/^StartCharMetrics/", $line ) )
				$inmetrics = true;

			// search for beginning of char metrics
			if ( preg_match( "/^StartKernData/", $line ) )
				$inkerndata = true;
		}
	}
} // END OF AFMFile

?>
