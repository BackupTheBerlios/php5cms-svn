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
 * @package image
 */
 
class ImageInformation extends PEAR
{
	/**
	 * @access public
	 */
	var $source;
	
	/**
	 * @access public
	 */
	var $details;
	
	/**
	 * @access public
	 */
	var $format;
	
	/**
	 * @access public
	 */
	var $ImageInformation;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ImageInformation( $myfile )
	{
		$this->source = fopen( $myfile, "r" );
		$format = $this->GetFileFormat();
		
		if ( $format != "unknown" )
		{
			$this->format = $format;
			$format = "ProcessFile" . $format;
			$this->details = $this->$format( $this->source );
		}
		else
		{
			$this->details = array();
			$this->format = "unknown";
		}

		fclose( $this->source );
	}

	
	/**
	 * @access public
	 */
	function GetFileFormat()
	{
		$head = fread( $this->source, 32 );
			
	    if ( preg_match( "/^\xFF\xD8/", $head ) )
			return "JPG";
		else if ( preg_match( "/^GIF8[79]a/", $head ) )
			return "GIF";
		else if ( preg_match( "/^BM/", $head ) )
			return "BMP";
		else if ( preg_match( "/^\x89PNG\x0d\x0a\x1a\x0a/", $head ) )
			return "PNG";
		else
			return "unknown";
	}

	/**
	 * @access public
	 */	
	function ShowDetails()
	{
		$keys = array_keys( $this->details );
		$out  = '';
		
		foreach ( $keys as $key )
			$out .= $key . ": " . $this->details[$key] . "<br>";
		
		return $out;	
	}
	
	
	// BMP

	/**
	 * @access public
	 */	
	function ProcessFileBMP( $source )
	{	
		$this->ImageInformation = array();
		rewind( $source );
		$buf = fread( $source, 54 );
		$header = unpack( "Szero/Lone/Stwo/Sthree/Lfour/Lfive/Vwidth/Vheight/Ssamplesperpixel/Scolor_type/Lfile_ext/Leleven/Vresa/Vresb/Lcolortablesize/Lcolorsimportant", $buf );
		extract( unpack( "Szero/Lone/Stwo/Sthree/Lfour/Lfive/Vwidth/Vheight/Ssamplesperpixel/Scolor_type/Lfile_ext/Leleven/Vresa/Vresb/Lcolortablesize/Lcolorsimportant", $buf ) );
		$total += strlen( $buf );
		$this->ImageInformation["file_media_type"] = "image/bmp";

		if ( ( $file_ext == 1 ) || ( $file_ext == 2 ) )
			$this->ImageInformation["file_ext"] = "rle";
		else
			$this->ImageInformation["file_ext"] = "bmp";

		$this->ImageInformation["width"]  = abs( $width );
		$this->ImageInformation["height"] = abs( $height );

		if ( $color_type & ( $color_type < 24 ) )
			$this->ImageInformation["color_type"] = "Indexed-RGB";
		else
			$this->ImageInformation["color_type"] = "RGB";
	
		$this->ImageInformation["SamplesPerPixel"] = $samplesperpixel;
		$this->ImageInformation["BitsPerSample"] = $color_type;
		$this->ImageInformation["resolution"] = "$resa/$resb";
		$this->ImageInformation["BMP_ColorsImportant"] = $colorsimportant;
		$this->ImageInformation["ColorTableSize"] = $colortablesize;

		if ( $height > 1 )
			$this->ImageInformation["BMP_Origin"] = 1; // dunno if this is correct
		else
			$this->ImageInformation["BMP_Origin"] = 0; // dunno if this is correct

		$compression  = $file_ext;
		$compressions = array( 'none', 'RLE8', 'RLE4', 'BITFIELDS', 'JPEG', 'PNG' );
	
		if ( isset( $compressions[$compression] ) )
			$compression = $compressions[$compression];
	
		$this->ImageInformation["Compression"] = $compression; 
		return $this->ImageInformation;
	}
	
	
	// GIF

	/**
	 * @access public
	 */
	function ProcessFileGIF( $source )
	{
		rewind( $source );
		$head = fread( $source, 13 );
	
		if ( !eregi( "^GIF(8[79]a)", $head ) )
			return PEAR::raiseError( "Bad GIF signature." );
	
		preg_match( "/^GIF(8[79]a)/", $head, $matches );
		$version = $matches[0];
		$head    = str_replace( $matches[0], "",$head );
		$version = str_replace( "GIF", "", $version );
		$this->ImageInformation["GIF_Version"] = $version;
		$vars = unpack( "vsw/vsh/Cpacked/Cbg/Caspect", $head );

		$this->ImageInformation["ScreenWidth"] = $vars["sw"];
		$this->ImageInformation["ScreenHeight"] = $vars["sh"];
	        
	    $color_table_size = ( 1 << ( ( $vars["packed"] & 7 ) + 1 ) );
    	$this->ImageInformation["ColorTableSize"] = $color_table_size;

		// not sure below stuff is return the correct values
		if ( ( $vars["packed"] & 8 ) > 0 )
			$sorted_colors = ($vars["packed"] & 8);
		else
			$sorted_colors = 0;

		$this->ImageInformation["SortedColors"] = $sorted_colors;
		
		if ( $version == "89a" )
		{
			$this->ImageInformation["ColorResolution"] = ( ( ( $vars["packed"] & 112 ) >> 4 ) + 1 );
    		$global_color_table = ( $vars["packed"] & 128 );

	    	if ( $global_color_table )
			{
    			$this->ImageInformation["GlobalColorTableFlag"] = 1;
    			$this->ImageInformation["BackgroundColor"] = $vars["bg"];
  	  		}
			else
			{
    			$this->ImageInformation["GlobalColorTableFlag"] = 0;
    		}

			if ( $vars["aspect"] )
			{
				$aspect = ($vars["aspect"] + 15) / 64;		
				$this->ImageInformation["PixelAspectRatio"] = $aspect;
				$this->ImageInformation["resolution"] = "1/" . $aspect;
			}
			else
			{
				$this->ImageInformation["resolution"] = "1/1";
			}
    	}
	
		$this->ImageInformation["file_media_type"] = "image/gif";
		$this->ImageInformation["file_ext"] = "gif";  

		return $this->ImageInformation;
	}
	
	
	// PNG

	/**
	 * @access public
	 */	
	function ProcessFilePNG( $source )
	{
		$this->ImageInformation = array();
		rewind( $source );
		$signature = fread( $source, 8 );
	
		if ( $signature != "\x89PNG\x0d\x0a\x1a\x0a" )
			return PEAR::raiseError( "Bad PNG signature." );
	
		$this->ImageInformation["file_media_type"] = "image/png";
		$this->ImageInformation["file_ext"] = "png";

		while( 1 )
		{
			extract( unpack("Nlen/a4type", fread( $source, 8 ) ) );
		
			if ( $type == "IEND" )
				break;

			$data = fread($source, $len + 4);

			if ( ( $type == "IHDR" ) && ( $len == 13 ) )
			{	
				extract(unpack( "Nwidth/Nheight/Cdepth/Cctype/Ccompression/Cfilter/Cinterlace", $data ) );
			
				$this->ImageInformation["width"]  = $width;
				$this->ImageInformation["height"] = $height;
				$this->ImageInformation["SampleFormat"] = "U$depth";
		    
				$ctypes = array(
					0 => "Gray",
					2 => "RGB",
					3 => "Indexed-RGB",
					4 => "GrayA",
					6 => "RGBA"
				);
			
				if ( isset( $ctypes[$ctype] ) )
					$this->ImageInformation["color_type"] = $ctypes[$ctype];
				else
					$this->ImageInformation["color_type"] = "PNG-$ctype";

				if ( $compression == 0 )
					$compression = "Deflate";

				$this->ImageInformation["Compression"] = $compression;
						
				if ( $filter == 0 )
					$filter = "Adaptive";

				$this->ImageInformation["PNG_Filter"] = $filter;
			
				if ( $interlace == 1 )
					$interlace = "Adam7";

				$this->ImageInformation["Interlace"] = $interlace;	
			}
			else if ( $type == "PLTE" )
			{  
				$paltable = array();
				$no = 0;
			
				while ( $no < strlen( $data ) )
				{
					extract( unpack( "Cchar1/Cchar2/Cchar3", substr( $data, 0, 3 ) ) );
					array_push( $paltable, sprintf("#%02x%02x%02x", $char1, $char2, $char3 ) );
					$no++;	
				}
			
	    		$this->ImageInformation["ColorPalette"] = $paltable;  //dunno if this is correct
			}
			else if ( ( $type == "gAMA" ) && ( $len == 4 ) )
			{
				extract( unpack( "Ngamma", $data ) );
				$this->ImageInformation["Gamma"] = $gamma/100000;
			}
			else if ( ( $type == "pHYs" ) && ( $len == 9 ) )
			{
				extract( unpack( "Nres_x/Nres_y/Cunit", $data ) );
			
				if ( ( 0 && $unit ) == 1 )
				{
					// convert to dpi
					$unit  = "dpi";
					$res_x = $res_x * 0.0254;
					$res_y = $res_y * 0.0254;
				}
			
				if ( $res_x == $res_y )
					$res = $res_x;
				else
					$res = "$res_x/$res_y";
			
				if ( $unit )
				{
					if ( $unit == 1 )
						$res .= " dpm";
					else
						$res .= " png-unit-$unit";
				}
			
				$this->ImageInformation["resolution"] = $res;
			}
			else if ( ( $type == "tIME" ) && ( $len == 7 ) )
			{
				$mt = unpack( "nyear/Cmonth/Cday/Chour/Cminute/Csecond", $data );
				$this->ImageInformation["LastModificationTime"] = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $mt["year"], $mt["month"], $mt["day"], $mt["hour"], $mt["minute"], $mt["second"] );
			}
			else if ( $type == "IDAT" )
			{
				// ignore image data
			}
			else
			{
				$this->ImageInformation["Chunk-$type"] = $data; //unknown chunk type
			}
		}
	
		if ( !isset( $this->ImageInformation["resolution"] ) )
			$this->ImageInformation["resolution"] = "1/1";
	
		return $this->ImageInformation;	
	}
	
	
	// JPG

	/**
	 * @access public
	 */
	function ProcessFileJPG( $source )
	{	
		$this->ImageInformation = array();
		rewind( $source );
		$soi = fread( $source, 2 );
	
		if ( !$soi == "\xFF\xD8" )
			return PEAR::raiseError( "SOI missing." );

		$this->ImageInformation["file_media_type"] = "image/jpeg";
    	$this->ImageInformation["file_ext"] = "jpg";
    
		while ( 1 )
		{
        	extract( unpack( "Cff/Cmark/nlen", fread( $source, 4 ) ) );
        
			if ( $ff != 255 )
				break;

	        if ( ( $mark == 218 ) | ( $mark == 217 ) )
				break;
		
     	   	if ( $len < 2 )
				break;
		
        	$this->_chunk( $mark, fread( $source, ( $len - 2 ) ) );
    	}
	
    	return $this->ImageInformation;
	}


	// private methods
	
	/**
	 * @access private
	 */
	function _app0_jfxx( $data )
	{
		$code = ord( substr( $data, 0, 1 ) );

		if ( $code == 16 )
			$type = "JPEG thumbnail";
		else if ( $code == 17 )
			$type = "Bitmap thumbnail";
		else if ( $code == 19 )
			$type = "RGB thumbnail";
		else
			$type = "Unknown extention code $code";
	
		$this->ImageInformation["JFXX_ImageType"] = $type;
	}

	/**
	 * @access private
	 */
	function _app14_adobe( $data )
	{	 
		extract( unpack( "nversion/nflaga/nflagb/Ctransform", $data ) );
    	$this->ImageInformation["AdobeTransformVersion"] = $version;
    	$this->ImageInformation["AdobeTransformFlags"] = array( $flags0, $flags1 );
    	$this->ImageInformation["AdobeTransform"] = $transform;
	}

	/**
	 * @access private
	 */
	function _app0_jfif( $data )
	{	
    	if ( strlen( $data ) < 9 )
		{
			$this->ImageInformation["Debug"] = "Short JFIF chunk";
			return;
    	}
	
		extract( unpack( "Cver_hi/Cver_lo/Cunit/nx_density/ny_density/Cx_thumb/Cy_thumb", substr( $data, 0, 9 ) ) );
    	$this->ImageInformation["JFIF_Version"] = sprintf( "%d.%02d", $ver_hi, $ver_lo );

		if ( ( $x_density != $y_density ) | !$unit )
			$res = $x_density . "/" . $y_density;
		else
			$res = $x_density;
	
    	if ( $unit )
		{
			$units = array( "pixels", "dpi", "dpcm" );
		
			if ( isset( $units[$unit] ) )
				$res .= " ".$units[$unit];
			else
				$res .= " jfif-unit-".$unit;
    	}
    
		$this->ImageInformation["resolution"] = $res;
    
		if ( $x_thumb | $y_thumb )
		{
    		$this->ImageInformation["width"]     = $x_thumb;
    		$this->ImageInformation["height"]    = $y_thumb;
    		$this->ImageInformation["ByteCount"] = strlen($data);
 		}
	}

	/**
	 * @access private
	 */
	function _app( $mark, $data )
	{    
		$app = $mark - 224;
    	$id  = substr( $data, 0, 5 );
        
    	$id = "$app-$id";
    
		if ( $id == "0-JFIF\0" )
			$this->_app0_jfif( substr( $data, 5 ) ); 
		else if ( $id == "0-JFXX\0" )
			$this->_app0_jfxx( substr( $data, 5 ) ); // not tested
		/*
		else if ( $id == "1-Exif\0" )
			$this->_app1_exif( substr( $data, 5 ) ); // not converted yet
		*/
		else if ( $id == "14-Adobe" )
			$this->_app14_adobe( substr( $data, 5 ) );
		else {}
	}

	/**
	 * @access private
	 */
	function _chunk( $mark, $data )
	{
		$JpegTypes = array(
			'192' => "Baseline",
		   	'193' => "Extended sequential",
	   		'194' => "Progressive",
		   	'195' => "Lossless",
		   	'197' => "Differential sequential",
	   		'198' => "Differential progressive",
		   	'199' => "Differential lossless",
		   	'201' => "Extended sequential, arithmetic coding",
	   		'202' => "Progressive, arithmetic coding",
		   	'203' => "Lossless, arithmetic coding",
		   	'205' => "Differential sequential, arithmetic coding",
	   		'206' => "Differential progressive, arithmetic coding",
	   		'207' => "Differential lossless, arithmetic coding"
		);

   	 	if ( $mark == 254 )
		{
    		$this->ImageInformation["Comment"] = $data;
    	}
		else if ( ( $mark >= 224 ) & ( $mark <= 239 ) )
		{
    		$this->_app( $mark, $data );
    	}
		else if ( $JpegTypes[$mark] )
		{	
	    	extract( unpack( "Cprecision/nheight/nwidth/Cnum_comp", substr( $data, 0, 6 ) ) );
			
			$this->ImageInformation["JPEG_Type"]       = $JpegTypes[$mark];
			$this->ImageInformation["width"]           = $width;
			$this->ImageInformation["height"]          = $height;
			$this->ImageInformation["SamplesPerPixel"] = $num_comp;

			// XXX need to consider JFIF/Adobe markers to determine this...
			if ( $num_comp == 1 )
				$this->ImageInformation["color_type"] = "Gray";
			else if ( $num_comp == 3 )
				$this->ImageInformation["color_type"] = "YCbCr"; # or RGB ?
			else if ( $num_comp == 4 )
				$this->ImageInformation["color_type"] = "CMYK"; # or YCCK ?
    	}
	}
} // END OF ImageInformation

?>
