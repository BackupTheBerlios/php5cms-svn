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
 * @package util_color
 */
 
class ColorCodes extends PEAR 
{
	/**
	 * @access private
	 */
	var $_webSafe;
	
	/**
	 * @access private
	 */
	var $_namedColors;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ColorCodes() 
	{
		$this->_createWebSafe();
		$this->_createNamedColors();
	}
	

	/**
	 * @access public
	 */	
	function hexToRgb( $hex ) 
	{
		$hex = $this->_cleanCode( $hex );
		
		return array( 
			hexdec( substr( $hex, 0, 2 ) ), 
			hexdec( substr( $hex, 2, 2 ) ), 
			hexdec( substr( $hex, 4, 2 ) )
		);
	}
	
	/**
	 * @access public
	 */
	function rgbToHex( $rgb ) 
	{
		return dechex( $rgb[0] ) . dechex( $rgb[1] ) . dechex( $rgb[2] );
	}
	
	/**
	 * @access public
	 */
	function nameToHex( $colorName ) 
	{
		$colorName = strtolower( $colorName );
		
		if ( isset( $this->_namedColors[$colorName] ) ) 
			return $this->_namedColors[$colorName];
			
		return false;
	}
	
	/**
	 * @access public
	 */
	function isDark( $colorCode ) 
	{
		$colorCode = $this->_cleanCode( $colorCode );
		$char = substr( $colorCode, 2, 1 );
		
		return ( is_numeric( $char ) && ( $char < 8 ) );
	}
	
	/**
	 * @access public
	 */
	function getWebSafe() 
	{
		return $this->_webSafe;
	}
	
	/**
	 * @access public
	 */
	function makeWebSafe( $colorCode ) 
	{
		$colorCode = $this->_cleanCode( $colorCode );
		$ret       = '';
		
		for ( $i = 0; $i < 3; $i++ ) 
		{
			$char = $colorCode[( $i * 2 )];
			
			switch ( $char ) 
			{
				case '0': 
				
				case '1':
					$newChar = 0;
					break;
				
				case '2': 
				
				case '3': 
				
				case '4':
					$newChar = 3;
					break;
					
				case '5': 
				
				case '6': 
				
				case '7':
					$newChar = 6;
					break;
				
				case '8': 
				
				case '9': 
				
				case 'A':
					$newChar = 9;
					break;
				
				case 'B': 
				
				case 'C': 
				
				case 'D':
					$newChar = 'C';
					break;
				
				default:
					$newChar = 'F';
					break;
			}

			$ret .= $newChar . $newChar;
		}
		
		return $ret;
	}
	
	/**
	 * @access public
	 */
	function isGray( $colorCode ) 
	{
		$colorCode = $this->_cleanCode( $colorCode );
		
		$r = substr( $colorCode, 0, 2 );
		$g = substr( $colorCode, 2, 2 );
		$b = substr( $colorCode, 4, 2 );
		
		return ( ( $r == $g ) && ( $r == $b ) );
	}
	
	/**
	 * @access public
	 */
	function toGray( $colorCode ) 
	{
		$colorCode = $this->_cleanCode( $colorCode );
		$rgb = $this->hexToRgb( $colorCode );
		$av  = ( $rgb[0] + $rgb[1] + $rgb[2] ) /3;
		
		return $this->rgbToHex( array( $av, $av, $av ) );
	}
	
	/**
	 * @access public
	 */
	function dumpWebSafe() 
	{
		$ret = '<table><tr>';
		$i   = 0;
		
		foreach ( $this->_webSafe as $dev0 => $color ) 
		{
			if ( $i == 6 ) 
			{
				$i    = 0;
				$ret .= '</tr><tr>';
			}
			
			$fontColor = ( $this->isDark( $color ) )? 'white' : 'black';
			$ret .= "<td bgcolor='{$color}'><font color='{$fontColor}' face='arial' size='2'>{$color}</font></td>";
			
			$i++;
		}
		
		$ret .= '</tr></table>';
		return $ret;
	}
	
	/**
	 * @access public
	 */
	function isWebSafe( $colorCode ) 
	{
		$colorCode = $this->_cleanCode( $colorCode );
		return in_array( $colorCode, $this->_webSafe );
	}


	// private methods

	/**
	 * @access private
	 */	
	function _cleanCode( $colorCode ) 
	{
		$colorCode = strtoupper( $colorCode );
		
		if ( $colorCode[0] == '#' ) 
			$colorCode = substr( $colorCode, 1 );
			
		return $colorCode;
	}
	
	/**
	 * @access private
	 */	
	function _createWebSafe() 
	{
		if ( !is_array( $this->_webSafe ) ) 
		{
			$x = array( '00', '33', '66', '99', 'CC', 'FF' );
			static $colors = array();
			
			for ( $i = 0; $i < 6; $i++ ) 
			{
				$colLeft = $x[$i];
				
				for ( $j = 0; $j < 6; $j++ ) 
				{
					$colMid = $x[$j];
					
					for ( $k = 0; $k < 6; $k++ ) 
						$colors[] = $colLeft . $colMid . $x[$k];
				}
			}

			$this->_webSafe = &$colors;
		}
	}

	/**
	 * @access private
	 */	
	function _createNamedColors() 
	{
		if ( !is_array( $this->_namedColors ) ) 
		{
			static $c = array();
			$c['aliceblue'] 			= 'F0F8FF';
			$c['antiquewhite'] 			= 'FAEBD7';
			$c['aqua'] 					= '00FFFF';
			$c['aquamarine'] 			= '7FFFD4';
			$c['azure'] 				= 'F0FFFF';
			$c['beige'] 				= 'F5F5DC';
			$c['bisque'] 				= 'FFE4C4';
			$c['black'] 				= '000000';
			$c['blanchedalmond'] 		= 'FFEBCD';
			$c['blue'] 					= '0000FF';
			$c['blueviolet'] 			= '8A2BE2';
			$c['brown'] 				= 'A52A2A';
			$c['burlywood'] 			= 'DEB887';
			$c['cadetblue'] 			= '5F9EA0';
			$c['chartreuse'] 			= '7FFF00';
			$c['chocolate'] 			= 'D2691E';
			$c['coral'] 				= 'FF7F50';
			$c['cornflowerblue'] 		= '6495ED';
			$c['cornsilk'] 				= 'FFF8DC';
			$c['crimson'] 				= 'DC143C';
			$c['cyan'] 					= '00FFFF';
			$c['darkblue'] 				= '00008B';
			$c['darkcyan'] 				= '008B8B';
			$c['darkgoldenrod'] 		= 'B8860B';
			$c['darkgray'] 				= 'A9A9A9';
			$c['darkgreen'] 			= '006400';
			$c['darkkhaki'] 			= 'BDB76B';
			$c['darkmagenta'] 			= '8B008B';
			$c['darkolivegreen'] 		= '556B2F';
			$c['darkorange'] 			= 'FF8C00';
			$c['darkorchid'] 			= '9932CC';
			$c['darkred'] 				= '8B0000';
			$c['darksalmon'] 			= 'E9967A';
			$c['darkseagreen'] 			= '8FBC8F';
			$c['darkslateblue'] 		= '483D8B';
			$c['darkslategray'] 		= '2F4F4F';
			$c['darkturquoise'] 		= '00CED1';
			$c['darkviolet'] 			= '9400D3';
			$c['deeppink'] 				= 'FF1493';
			$c['deepskyblue'] 			= '00BFFF';
			$c['dimgray'] 				= '696969';
			$c['dodgerblue'] 			= '1E90FF';
			$c['feldspar'] 				= 'D19275';
			$c['firebrick'] 			= 'B22222';
			$c['floralwhite'] 			= 'FFFAF0';
			$c['forestgreen'] 			= '228B22';
			$c['fuchsia'] 				= 'FF00FF';
			$c['gainsboro'] 			= 'DCDCDC';
			$c['ghostwhite'] 			= 'F8F8FF';
			$c['gold'] 					= 'FFD700';
			$c['goldenrod'] 			= 'DAA520';
			$c['gray'] 					= '808080';
			$c['green'] 				= '008000';
			$c['greenyellow'] 			= 'ADFF2F';
			$c['honeydew'] 				= 'F0FFF0';
			$c['hotpink'] 				= 'FF69B4';
			$c['indianred'] 			= 'CD5C5C';
			$c['indigo'] 				= '4B0082';
			$c['ivory'] 				= 'FFFFF0';
			$c['khaki'] 				= 'F0E68C';
			$c['lavender'] 				= 'E6E6FA';
			$c['lavenderblush'] 		= 'FFF0F5';
			$c['lawngreen'] 			= '7CFC00';
			$c['lemonchiffon'] 			= 'FFFACD';
			$c['lightblue'] 			= 'ADD8E6';
			$c['lightcoral'] 			= 'F08080';
			$c['lightcyan'] 			= 'E0FFFF';
			$c['lightgoldenrodyellow'] 	= 'FAFAD2';
			$c['lightgrey'] 			= 'D3D3D3';
			$c['lightgreen'] 			= '90EE90';
			$c['lightpink'] 			= 'FFB6C1';
			$c['lightsalmon'] 			= 'FFA07A';
			$c['lightseagreen'] 		= '20B2AA';
			$c['lightskyblue'] 			= '87CEFA';
			$c['lightslateblue'] 		= '8470FF';
			$c['lightslategray'] 		= '778899';
			$c['lightsteelblue'] 		= 'B0C4DE';
			$c['lightyellow'] 			= 'FFFFE0';
			$c['lime'] 					= '00FF00';
			$c['limegreen'] 			= '32CD32';
			$c['linen'] 				= 'FAF0E6';
			$c['magenta'] 				= 'FF00FF';
			$c['maroon'] 				= '800000';
			$c['mediumaquamarine'] 		= '66CDAA';
			$c['mediumblue'] 			= '0000CD';
			$c['mediumorchid'] 			= 'BA55D3';
			$c['mediumpurple'] 			= '9370D8';
			$c['mediumseagreen'] 		= '3CB371';
			$c['mediumslateblue'] 		= '7B68EE';
			$c['mediumspringgreen'] 	= '00FA9A';
			$c['mediumturquoise'] 		= '48D1CC';
			$c['mediumvioletred'] 		= 'C71585';
			$c['midnightblue'] 			= '191970';
			$c['mintcream'] 			= 'F5FFFA';
			$c['mistyrose'] 			= 'FFE4E1';
			$c['moccasin'] 				= 'FFE4B5';
			$c['navajowhite'] 			= 'FFDEAD';
			$c['navy'] 					= '000080';
			$c['oldlace'] 				= 'FDF5E6';
			$c['olive'] 				= '808000';
			$c['olivedrab'] 			= '6B8E23';
			$c['orange'] 				= 'FFA500';
			$c['orangered'] 			= 'FF4500';
			$c['orchid'] 				= 'DA70D6';
			$c['palegoldenrod'] 		= 'EEE8AA';
			$c['palegreen'] 			= '98FB98';
			$c['paleturquoise'] 		= 'AFEEEE';
			$c['palevioletred'] 		= 'D87093';
			$c['papayawhip'] 			= 'FFEFD5';
			$c['peachpuff'] 			= 'FFDAB9';
			$c['peru'] 					= 'CD853F';
			$c['pink'] 					= 'FFC0CB';
			$c['plum'] 					= 'DDA0DD';
			$c['powderblue'] 			= 'B0E0E6';
			$c['purple'] 				= '800080';
			$c['red'] 					= 'FF0000';
			$c['rosybrown'] 			= 'BC8F8F';
			$c['royalblue'] 			= '4169E1';
			$c['saddlebrown'] 			= '8B4513';
			$c['salmon'] 				= 'FA8072';
			$c['sandybrown'] 			= 'F4A460';
			$c['seagreen'] 				= '2E8B57';
			$c['seashell'] 				= 'FFF5EE';
			$c['sienna'] 				= 'A0522D';
			$c['silver'] 				= 'C0C0C0';
			$c['skyblue'] 				= '87CEEB';
			$c['slateblue'] 			= '6A5ACD';
			$c['slategray'] 			= '708090';
			$c['snow'] 					= 'FFFAFA';
			$c['springgreen'] 			= '00FF7F';
			$c['steelblue'] 			= '4682B4';
			$c['tan'] 					= 'D2B48C';
			$c['teal'] 					= '008080';
			$c['thistle'] 				= 'D8BFD8';
			$c['tomato'] 				= 'FF6347';
			$c['turquoise'] 			= '40E0D0';
			$c['violet'] 				= 'EE82EE';
			$c['violetred'] 			= 'D02090';
			$c['wheat'] 				= 'F5DEB3';
			$c['white'] 				= 'FFFFFF';
			$c['whitesmoke'] 			= 'F5F5F5';
			$c['yellow'] 				= 'FFFF00';
			$c['yellowgreen'] 			= '9ACD32';
			
			$this->_namedColors = &$c;
		}
	}
} // END OF ColorCodes

?>
