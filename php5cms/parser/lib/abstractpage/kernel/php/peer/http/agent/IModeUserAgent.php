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
|Authors: Mika Tuupola <tuupola@appelsiini.net>                        |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/* As you can see specifications are missing from the following
   models: ER209i and KD209i. */ 
define( 'IMODEUSERAGENT_COLOUR_BW',          0 );
define( 'IMODEUSERAGENT_COLOUR_GREYSCALE',   1 );
define( 'IMODEUSERAGENT_COLOUR_256',         2 );
define( 'IMODEUSERAGENT_COLOUR_4096',        3 );
define( 'IMODEUSERAGENT_COLOUR_65536',       4 );

define( 'IMODEUSERAGENT_DEFAULT_CACHE',      5 );


/**
 * @package peer_http_agent
 */
 
class IModeUserAgent extends PEAR
{
	/**
	 * @access private
	 */
	var $_data = array( 
		"D209i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 90, 
			"textwidth"		=> 8, 
			"textheight"	=> 7, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "F209i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 91, 
			"textwidth"		=> 8,
			"textheight"	=> 7, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "N209i" => array(
			"imagewidth"	=> 108,
			"imageheight"	=> 82, 
			"textwidth"		=> 9, 
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
        "P209i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 87, 
			"textwidth"		=> 8, 
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
		"P209is" => array( 
			"imagewidth"	=> 96,
			"imageheight"	=> 87, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "R209i" => array( 
			"imagewidth"	=> 96,
			"imageheight"	=> 72, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
		"ER209i" => array ( 
			"imagewidth"    => 120, 
			"imageheight"   => 72, 
           	"textwidth"     => 10,  
			"textheight"    => 6, 
           	"colour" 		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"  => array( "gif" )
        ),
        "KO209i" => array ( 
			"imagewidth"    => 96, 
			"imageheight"   => 96, 
			"textwidth"     => 8,  
			"textheight"    => 8, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
           	"imageformats"  => array( "gif" )
        ),
        "D210i" => array ( 
           	"imagewidth"    => 96, 
			"imageheight"   => 91, 
			"textwidth"     => 8,  
			"textheight"    => 7, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
           	"imageformats"  => array( "gif" )
        ),
		"F210i" => array ( 
           	"imagewidth"    => 96, 
			"imageheight"   => 113, 
			"textwidth"     => 8,  
			"textheight"    => 8, 
           	"colour"        => IMODEUSERAGENT_COLOUR_256,
           	"imageformats"  => array( "gif" )
        ),
		"KO210i" => array ( 
           	"imagewidth"    => 96, 
			"imageheight"   => 96, 
			"textwidth"     => 8,
			"textheight"    => 8, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
			"imageformats"  => array( "gif" )
        ),
		"N210i" => array ( 
           	"imagewidth"    => 118, 
			"imageheight"   => 113, 
			"textwidth"     => 10,  
			"textheight"    => 8, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
           	"imageformats"  => array( "gif" )
        ),
		"P210i" => array ( 
           	"imagewidth"    => 96, 
			"imageheight"   => 91, 
           	"textwidth"     => 8,  
			"textheight"    => 6, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
			"imageformats"  => array( "gif" )
        ),
		"SO210i" => array( 
           	"imagewidth"    => 120, 
			"imageheight"   => 113, 
			"textwidth"     => 8,  
			"textheight"    => 7, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
			"imageformats"  => array( "gif" )
        ),
        "D501i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 72, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_BW,
			"imageformats"	=> array( "gif" )
        ),
        "F501i" => array(
			"imagewidth"	=> 112,
			"imageheight"	=> 84, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_BW,
			"imageformats"	=> array( "gif" )
        ),
        "N501i" => array(
			"imagewidth"	=> 118,
			"imageheight"	=> 128, 
			"textwidth"		=> 10,
			"textheight"	=> 10, 
			"colour"		=> IMODEUSERAGENT_COLOUR_BW,
			"imageformats"	=> array( "gif" )
        ),
        "P501i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 120, 
			"textwidth"		=> 8,
			"textheight"	=> 8, 
			"colour"		=> IMODEUSERAGENT_COLOUR_BW,
			"imageformats"	=> array( "gif" )
        ),
        "D502i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 90, 
			"textwidth"		=> 8,
			"textheight"	=> 7, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "F502i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 91, 
			"textwidth"		=> 8,
			"textheight"	=> 7, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "F502it" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 91, 
			"textwidth"		=> 8,
			"textheight"	=> 7, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "N502i" => array(
			"imagewidth"	=> 118,
			"imageheight"	=> 128, 
			"textwidth"		=> 10,
			"textheight"	=> 10, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
        "N502it" => array(
			"imagewidth"	=> 118,
			"imageheight"	=> 128, 
			"textwidth"		=> 10,
			"textheight"	=> 10, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "P502i" => array(
			"imagewidth"	=> 96,
			"imageheighth"	=> 117, 
			"textwidth"		=> 8,
			"textheight"	=> 8, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
        "NM502i" => array(
			"imagewidth"	=> 111,
			"imageheight"	=> 77, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_BW,
			"imageformats"	=> array( "gif" )
        ),
        "SO502i" => array(
			"imagewidth"	=> 120,
			"imageheight"	=> 120, 
			"textwidth"		=> 8,
			"textheight"	=> 8, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
		"SO502iwm" => array(
           	"imagewidth"    => 120, 
			"imageheight"   => 113, 
			"textwidth"     => 8,  
			"textheight"    => 7, 
			"colour"        => IMODEUSERAGENT_COLOUR_256,
			"imageformats"  => array( "gif" )
        ),
		"F503i" => array(
           	"imagewidth"    => 120, 
			"imageheight"   => 130, 
           	"textwidth"     => 10,  
			"textheight"    => 10, 
           	"colour"        => IMODEUSERAGENT_COLOUR_256,
           	"imageformats"  => array( "gif" )
        ),
        "F503iS" => array(
           	"imagewidth"    => 120, 
			"imageheight"   => 130, 
           	"textwidth"     => 12,  
			"textheight"    => 12, 
           	"colour"        => IMODEUSERAGENT_COLOUR_4096,
           	"imageformats"  => array( "gif" )
        ),
        "P503i" => array(
           	"imagewidth"    => 120, 
			"imageheight"   => 130, 
           	"textwidth"     => 12,  
			"textheight"    => 10, 
           	"colour"        => IMODEUSERAGENT_COLOUR_256,
          	"imageformats"  => array( "gif" )
        ),
        "P503iS" => array (
			"imagewidth"    => 120, 
			"imageheight"   => 130,
			"textwidth"     => 12,  
			"textheight"    => 10,
			"colour"        => IMODEUSERAGENT_COLOUR_256,
			"imageformats"  => array( "gif" )
        ),
        "SO503i" => array (
			"imagewidth"    => 120, 
			"imageheight"   => 113, 
           	"textwidth"     => 8.5,  
			"textheight"    => 7, 
           	"colour"        => IMODEUSERAGENT_COLOUR_65536,
           	"imageformats"  => array( "gif" )
        ),
        "D503i" => array (
           	"imagewidth"    => 132, 
			"imageheight"   => 126, 
           	"textwidth"     => 8,  
			"textheight"    => 7, 
           	"colour"        => IMODEUSERAGENT_COLOUR_4096,
           	"imageformats"  => array( "gif" )
        ),
        "N503i" => array (
           	"imagewidth"    => 118, 
			"imageheight"   => 128, 
           	"textwidth"     => 10,  
			"textheight"    => 10, 
           	"colour"        => IMODEUSERAGENT_COLOUR_4096,
           	"imageformats"  => array( "gif", "jpg" )
        ),
        "N503iS" => array (
           	"imagewidth"    => 118, 
			"imageheight"   => 128, 
           	"textwidth"     => 10,  
			"textheight"    => 10, 
           	"colour"        => IMODEUSERAGENT_COLOUR_4096,
           	"imageformats"  => array( "gif", "jpg" )
        ),
        "N691i" => array (
           	"imagewidth"    => 96, 
			"imageheight"   => 72, 
           	"textwidth"     => 8,  
			"textheight"    => 6, 
           	"colour"        => IMODEUSERAGENT_COLOUR_GREYSCALE,
           	"imageformats"  => array( "gif" )
        ),
        "SH821i" => array(
			"imagewidth"	=> 96,
			"imageheight"	=> 78, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_256,
			"imageformats"	=> array( "gif" )
        ),
        "N821i" => array(
			"imagewidth"	=> 118,
			"imageheight"	=> 128, 
			"textwidth"		=> 10,
			"textheight"	=> 10, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
        "P821i" => array(
			"imagewidth"	=> 118,
			"imageheight"	=> 128, 
			"textwidth"		=> 10,
			"textheight"	=> 10, 
			"colour"		=> IMODEUSERAGENT_COLOUR_GREYSCALE,
			"imageformats"	=> array( "gif" )
        ),
        "safe" => array(
			"imagewidth"	=> 94,
			"imageheight"	=> 72, 
			"textwidth"		=> 8,
			"textheight"	=> 6, 
			"colour"		=> IMODEUSERAGENT_COLOUR_BW,
			"imageformats"	=> array( "gif" )
		)
	);

	/**
	 * @access private
	 */
	var $_manufacturerlist = array(
		"D"  => "Mitsubishi",
        "P"  => "Panasonic (Matsushita)",
        "NM" => "Nokia",
        "SO" => "Sony",
        "F"  => "Fujitsu",
        "N"  => "Nec",
        "SH" => "Sharp",
        "ER" => "Ericsson",
        "KD" => "Kenwood"
	);

	/**
	 * @access private
	 */
	var $_extra = array(
        "t"  => "Transport layer",
        "e"  => "English language",
        "s"  => "Second version",
	);

	/**
	 * @access private
	 */
	var $_user_agent;     
	
	/**
	 * @access private
	 */
	var $_model;
	
	/**
	 * @access private
	 */
	var $_manufacturer;
	
	/**
	 * @access private
	 */
	var $_httpversion;
	
	/**
	 * @access private
	 */
	var $_cache;
	
	/**
	 * @access private
	 */
	var $_extra;

	
	/**
	 * Constructor
	 *
	 * Parameters: String describing the user_agent.
	 * Returns: Object
	 * Example usage: $ua = new ImodeUserAgent( $HTTP_USER_AGENT );
	 *
	 * @access public
	 */
	function IModeUserAgent( $input )
	{
    	// DoCoMo/1.0/SO502i
    	// DoCoMo/1.0/N502it/c10
		
    	$temp  = explode( "/", $input );

		$this->_user_agent  = $input;
		$this->_httpversion = $temp[1];
    	$this->_model       = $temp[2];
    
		if ( $temp[3] )
			$this->_cache = substr( $temp[3], 1 );
 		else
			$this->_cache = IMODEUSERAGENT_DEFAULT_CACHE;

		preg_match( "/(^[a-zA-Z]+)([0-9]+i)(.*)\/?(.*)/", $this->_model, $matches );
 
		// TODO: Fix situation of unknown manufacturer. Implement extrainfo properly
		$this->_manufacturer = $this->_manufacturerlist[$matches[1]];
		$this->_extra = $matches[3]; 

		if ( !( $this->_data[$this->_model] ) )
		{
			$this = new PEAR_Error( "Unkown device." );
			return;
		}
	} 


	/**
	 * Returns: Array containing maximum imagewidth and imageheight to fit on the handset screen without scrolling.
	 *
	 * Example usage:
	 * $imagedim    = $ua->getImageDimensions();
	 * $imagewidth  = $imagedim[0];
	 * $imageheight = $imagedim[1];
	 *
	 * @access public
	 */
	function getImageDimensions()
	{
		$data	= $this->_data["$this->_model"];
    	$width	= $data["imagewidth"];
    	$height	= $data["imageheight"];
    	$retval	= array( $width, $height );
		
		return( $retval );
	}

	/**
	 * Returns: Array containing maximum textwidth and textheight to fit on the handset screen without scrolling.
  	 *
	 * Example usage:
	 * $textdim    = $ua->getTextDimensions();
	 * $textwidth  = $textdim[0];
	 * $textheight = $textdim[1];
	 *
	 * @access public
	 */
	function getTextDimensions()
	{
		$data   = $this->_data[$this->_model];
		$width  = $data[textwidth];
		$height = $data[textheight];
 		$retval = array( $width, $height );

		return( $retval );
	}

	/**
	 * Returns: Integer containing the amount of handset cache in kilobytes.
	 * Example usage: $cache = $ua->getCache();
	 *
	 * @access public
	 */
	function getCache()
	{
    	return ( ( int )$this->_cache );
	}

	/**
	 * @access public
	 */
	function getManufacturer()
	{
    	return ( $this->_manufacturer );
	}

	/**
	 * @access public
	 */
	function getExtra()
	{
		return( $this->_extra );
	}

	/**
	 * @access public
	 */
	function getImageFormats()
	{
		$data   = $this->_data[$this->_model];
		$retval = $data[imageformats];
		
		return ( $retval );
	}

	/**
	 * Returns: Integer describing what colour model the handset supports.
	 * Values have the following meaning:
	 *   0 -> black and white
	 *   1 -> 4 tone greyscale
	 *   2 -> 256 colour
	 *
	 * @access public
	 */
	function getColour()
	{
		$data   = $this->_data[$this->_model];
		$retval = $data[colour];

		return ( $retval );
	}

	/**
	 * @access public
	 */
	function getHTTPVersion()
	{
		return ( $this->_httpversion );
	}

	/**
	 * @access public
	 */
	function isColour()
	{
		$data   = $this->_data[$this->_model];
		$colour = $data[colour];
		$retval = 0;

		if ( $colour = IMODEUSERAGENT_COLOUR_256 )
			$retval = 1;
			
		return ( $retval );
	}

	/**
	 * @access public
	 */
	function isGreyScale()
	{
		$data   = $this->_data[$this->_model];
		$colour = $data[colour];
		$retval = 0;

		if ( $colour = IMODEUSERAGENT_COLOUR_GREYSCALE )
			$retval = 1;

		return ( $retval );
	}

	/**
	 * @access public
	 */
	function isBlackAndWhite()
	{
		$data   = $this->_data[$this->_model];
		$colour = $data[colour];
		$retval = 0;

		if ( $colour = IMODEUSERAGENT_COLOUR_BW )
			$retval = 1;
			
		return ( $retval );
	}

	/**
	 * @access public
	 */
	function supportsGIF()
	{
	}

	/**
	 * @access public
	 */
	function supportsJPG()
	{
	}

	/**
	 * @access public
	 */
	function supportsPNG()
	{
	}

	/**
	 * @access public
	 */ 
	function getAllInfo()
	{
	}
} // END OF IModeUserAgent

?>
