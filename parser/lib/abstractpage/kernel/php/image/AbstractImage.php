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


using( 'image.svg.lib.SvgDocument' );
using( 'image.svg.lib.SvgGroup' );
using( 'image.svg.lib.SvgLine' );
using( 'image.svg.lib.SvgRect' );
using( 'image.svg.lib.SvgPath' );


/**
 * Generate images using number of output types
 * Now supports jpg, png, svg
 * Can produce MORE images directly in one script.
 * Uses GD2.
 *
 *
 * Usage:
 *
 * echo "<hr>This it SVG image";
 * $svg   = new AbstractImage( "svg", 320, 200 );
 * $black = $svg->colorAllocate( 0, 0, 0 );
 * $white = $svg->colorAllocate( 255, 255, 255 );
 * $green = $svg->colorAllocate( 0, 255, 0 );
 * $red   = $svg->colorAllocate( 255, 0, 0 );
 * $svg->filledRectangle( 0, 0, 320, 200, $white );
 * $svg->rectangle( 20, 20, 80, 80, $green );
 * $svg->filledRectangle( 80, 80, 150, 200, $black );
 * $svg->line( 0, 0, 320, 200, $green );
 * $svg->dashedLine( 320, 0, 0, 200, $green );
 * 
 * for ( $i = 1; $i < 20; $i++ )
 * 		$svg->line( 0, $i, 320, $i * 3, $green );
 * 
 * $svg->arc( 100, 100, 200, 100, 0, 90, $red );
 * $svg->filledArc( 0, 0, 200, 40, 0, 90, $red, 0 );
 * $svg->imageHTML();
 * 
 * 
 * echo "<hr>This is png image";
 * $png   = new AbstractImage( "png", 320, 200 );
 * $black = $png->colorAllocate( 0, 0, 0 );
 * $white = $png->colorAllocate( 255, 255, 255 );
 * $green = $png->colorAllocate( 0, 255, 0 );
 * $red   = $png->colorAllocate( 255, 0, 0 );
 * $png->filledRectangle( 0, 0, 320, 200, $white );
 * $png->rectangle( 20, 20, 80, 80, $green );
 * $png->filledRectangle( 80, 80, 150, 200, $black );
 * $png->line( 0, 0, 320, 200, $green );
 * $png->dashedLine( 320, 0, 0, 200, $green );
 * 
 * for ( $i = 1; $i < 20; $i++ )
 * 		$png->line( 0, $i, 320, $i * 3, $green );
 * 
 * $png->arc( 100, 100, 200, 100, 0, 90, $red );
 * $png->filledArc( 0, 0, 200, 40, 0, 90, $red, 0 );
 * $png->imageHTML();
 * 
 * 
 * echo "<hr>This is jpg image";
 * $jpg   = new AbstractImage( "jpg", 320, 200 );
 * $black = $jpg->colorAllocate( 0, 0, 0 );
 * $white = $jpg->colorAllocate( 255, 255, 255 );
 * $green = $jpg->colorAllocate( 0, 255, 0 );
 * $red   = $jpg->colorAllocate( 255, 0, 0 );
 * $jpg->filledRectangle( 0, 0, 320, 200, $white );
 * $jpg->rectangle( 20, 20, 80, 80, $green );
 * $jpg->filledRectangle( 80, 80, 150, 200, $black );
 * $jpg->line( 0, 0, 320, 200, $green );
 * $jpg->dashedLine( 320, 0, 0, 200, $green );
 * 
 * for ( $i = 1; $i < 20; $i++ )
 * 		$jpg->line( 0, $i, 320, $i * 3, $green );
 * 
 * $jpg->arc( 100, 100, 200, 100, 0, 90, $red );
 * $jpg->filledArc( 0, 0, 200, 40, 0, 90, $red, 0 );
 * $jpg->imageHTML();
 *
 * @package image
 */

class AbstractImage extends PEAR
{
	/**
	 * Default ouptut type to none, we must call SetOutputType()
	 * @access public
	 */
	var $output_type;
	
	/**
	 * Default viewport is all image
	 * @access public
	 */
	var $viewport;
	
	/**
	 * xsize of img
	 * @access public
	 */
	var $xsize;
	
	/**
	 * ysize of img
	 * @access public
	 */
	var $ysize;
	
	/**
	 * GD image resource
	 * @access public
	 */
	var $gdim;
	
	/**
	 * SVG image resource
	 * @access public
	 */
	var $svgdoc;
	
	/**
	 * @access public
	 */
	var $svggrp;
	
	/**
	 * ID of the image, randomly generated, belongs to only this instance of class
	 * @access public
	 */
	var $imageid;
	
	/**
	 * Colours array
	 * @access public
	 */
	var $colors;
	
	/**
	 * @access public
	 */
	var $numcolors;

	
	/**
	 * Constructor
	 *
	 * @param  $type     Type of the output ('svg','jpg','png')
	 * @param  $width    Image width
	 * @param  $height   Image height
	 * @param  $viewport Viewport string (if outpus supports it) "x1 x2 y1 y2"
	 * @access public
	 */
	function AbstractImage( $type, $width, $height, $viewport = "" )
	{
		switch ( $type ) 
		{
			case "jpg":
		
			case "png":
		
			case "svg":
		
			case "php":
				$this->output_type = $type;
				break;
		
			default:
				$this = new PEAR_Error( "Output $type not supported." );
				return;
		}
			
		switch ( $this->output_type ) 
		{
			case "png":
		
			case "jpg":
				$this->viewport = $viewport;
				$this->xsize    = $width;
				$this->ysize    = $height;
				$this->gdim     = ImageCreate( $width, $height );
				$ret = $this->gdim;
			
				break;
		
			case "svg":
				$this->viewport = $viewport;
				$this->xsize    = $width;
				$this->ysize    = $height;
				$this->svgdoc   = new SvgDocument( $width, $height );
				$this->svggrp   = new SvgGroup( "stroke:black; fill:white", "" );
				$this->svgdoc->AddChild( $this->svggrp );
				$ret = $this->svgdoc;
			
				break;
		}
			
		$this->imageid   = $this->output_type . $this->xsize . $this->ysize . rand( 0, 100 );
		$this->numcolors = 0;
		$this->colors    = array();
	}

	
	/**
	 * Return html format of color.
	 *
	 * @param $r R
	 * @param $g G
	 * @param $b B
	 * @return #rrggbb
	 */
	function rgbToHTMLColor( $r, $g, $b ) 
	{
		return sprintf( "#%02X%02X%02X", $r, $g, $b );
	}

	/**
	 * Return RGB format of color (not supported yet).
	 *
	 * @param $hcolor #rrggbb
	 * @return array(r,g,b)
	 */
	function htmlToRGBColor( $hcolor ) 
	{
		return ( "ble" );
	}

	/**
	 * Returns x in bounds (if somebody wants to draw over margins).
	 *
	 * @param $x input x
	 * @return bounded x
	 */
	function xBounds( $x ) 
	{
		if ( $x < 0 )
	  		return $this->xsize;
		else
			return $x;
	}

	/**
	 * Returns y in bounds (if somebody wants to draw over margins).
	 *
	 * @param $y  input y
	 * @return bounded y
	 */
	function yBounds( $y ) 
	{
		if ( $y < 0 )
			return $this->ysize;
		else
			return $y;
	}

	/**
	 * Returns rounded coordinate.
	 *
	 * @param $x - input x
	 * @return rounded x
	 */
	function roundCoord( $x ) 
	{
		return ( sprintf( "%.2f", $x ) );
	}

	/**
	 * Return image data here into document.
	 *
	 * @return true
	 */
	function imageData()
	{
		switch ( $this->output_type ) 
		{
			case "png":
				return ( ImagePng( $this->gdim ) );
			
			case "jpg":
				return ( ImageJpeg( $this->gdim ) );
			
			case "svg":
				return ( $this->svgdoc->printElement();
		}
	}

	/**
	 * Return image data here into document.
	 * Put header content-type too.
	 *
	 * @return true
	 */
	function imageDataHeader()
	{
		switch ( $this->output_type ) 
		{
			case "png":
				header( "content-type: image/png" );
				return ( ImagePng( $this->gdim ) );
			
			case "jpg":
				header( "content-type: image/jpeg" );
				return ( ImageJpeg( $this->gdim ) );
			
			case "svg":
				header( "content-type: image/svg+xml" );
				return( $this->svgdoc->printElement() );
		}
	}

	/**
	 * Return image data as string.
	 *
	 * @return string
	 */
	function imageDataStr()
	{
		ob_start();
		AbstractImage::imageData( $this->gdim );
		$image_data = ob_get_contents();
		ob_end_clean();
		
		if ( $this->output_type == "svg" )
			$image_data = gzencode( $image_data );
		
		return $image_dat);
	}

	/**
	 *  Put html string to embed image here.
	 *
	 * @param $url URL of the image.php
	 * @return string
	 */
	function imageHTML( $url = "image.php" ) 
	{
		global $phpimage_data;
		global $phpimage_header;

		session_register( "phpimage_data"   );
		session_register( "phpimage_header" );
		
		$phpimage_data[$this->imageid] = AbstractImage::imageDataStr();

		switch ( $this->output_type ) 
		{
			case "png":
				$phpimage_header[$this->imageid] = "content-type: image/png";
				echo "<img width='$this->xsize' height='$this->ysize'" ." src='$url?imageid=" . urlencode( $this->imageid ) . "'>";
			
				break;
		
			case "jpg":
				$phpimage_header[$this->imageid] = "content-type: image/jpeg";
				echo "<img width='$this->xsize' height='$this->ysize'" . " src='$url?imageid=" . urlencode( $this->imageid ) . "'>";

				break;
	
			case "svg":
				$phpimage_header[$this->imageid] = "content-type: image/svg+xml";
				echo "<embed width='$this->xsize' height='$this->ysize'" . " src='$url?imageid=" . urlencode( $this->imageid ) . "'>";
			
				break;
		}
	}

	/**
	 * Allocate color for drawing.
	 *
	 * @param $r R
	 * @param $g G
	 * @param $b B
	 * @return true or false
	 */
	function colorAllocate( $r, $g, $b ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
		
			case "jpg":
				$color = ImageColorAllocate( $this->gdim, $r, $g, $b );
				break;
		
			case "svg":
				$color = ++$this->numcolors;
		};
		
		$this->colors[$color] = array(
			"r"    => $r,
			"g"    => $g,
			"b"    => $b,
			"html" => AbstractImage::rgbToHTMLColor( $r, $g, $b )
		);
		return($color);
	}

	/**
	 * Draw a line.
	 *
	 * @param $x1 from x
	 * @param $y1 from y
	 * @param $x2 to x
	 * @param $y2 to y
	 * @param $color color
	 * @return true or false
	 */
	function line( $x1, $y1, $x2, $y2, $color ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
			
			case "jpg":
				$ret = ImageLine( $this->gdim, $x1, $y1, $x2, $y2, $color );
				break;
				
			case "svg":
				list( $x1, $x2, $y1, $y2 ) = array( 
					AbstractImage::xBounds( $x1 ),
					AbstractImage::xBounds( $x2 ),
					AbstractImage::yBounds( $y1 ),
					AbstractImage::yBounds( $y2 ) 
				);
				
				$ret = new SvgLine( $x1, $y1, $x2, $y2, "stroke: " . $this->colors[$color]["html"], "" );
				$this->svggrp->addChild( $ret );

				break;
		}
		
		return $ret;
	}

	/**
	 * Draw a dashed line.
	 *
	 * @param $x1 from x
	 * @param $y1 from y
	 * @param $x2 to x
	 * @param $y2 to y
	 * @param $color color
	 * @return true or false
	 */
	function dashedLine( $x1, $y1, $x2, $y2, $color ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
			
			case "jpg":
				$ret = ImageDashedLine( $this->gdim, $x1, $y1, $x2, $y2, $color );
				break;
				
			case "svg":
				list( $x1, $x2, $y1, $y2 ) = array( 
					AbstractImage::xBounds( $x1 ),
					AbstractImage::xBounds( $x2 ),
					AbstractImage::yBounds( $y1 ),
					AbstractImage::yBounds( $y2 ) 
				);
				
				$ret = new SvgLine( $x1, $y1, $x2, $y2, "stroke-dasharray: 5,5; stroke: " . $this->colors[$color]["html"], "" );
				$this->svggrp->addChild( $ret );

				break;
		}
		
		return $ret;
	}

	/**
	 * Draw a filled rectangle.
	 *
	 * @param $x1 from x
	 * @param $y1 from y
	 * @param $x2 to x
	 * @param $y2 to y
	 * @param $color color
	 * @return true or false
	 */
	function filledRectangle( $x1, $y1, $x2, $y2, $color ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
		
			case "jpg":
				$ret = ImageFilledRectangle( $this->gdim, $x1, $y1, $x2, $y2, $color );
				break;

			case "svg":
				list( $x1, $x2, $y1, $y2 ) = array( 
					AbstractImage::xBounds( $x1 ),
					AbstractImage::xBounds( $x2 ),
					AbstractImage::yBounds( $y1 ),
					AbstractImage::yBounds( $y2 )
				);
				
				$ret = new SvgRect( $x1, $y1, $x2 - $x1, $y2 - $y1, "fill: " . $this->colors[$color]["html"] . "; stroke: " . $this->colors[$color]["html"], "" );
				$this->svggrp->addChild( $ret );
			
				break;
		}
	}

	/**
	 * Draw a rectangle.
	 *
	 * @param $x1 from x
	 * @param $y1 from y
	 * @param $x2 to x
	 * @param $y2 to y
	 * @param $color color
	 * @return true or false
	 */
	function rectangle( $x1, $y1, $x2, $y2, $color ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
		
			case "jpg":
				$ret = ImageRectangle( $this->gdim, $x1, $y1, $x2, $y2, $color );
				break;

			case "svg":
				list( $x1, $x2, $y1, $y2 ) = array( 
					AbstractImage::xBounds( $x1 ),
					AbstractImage::xBounds( $x2 ),
					AbstractImage::yBounds( $y1 ),
					AbstractImage::yBounds( $y2 )
				);
				
				$ret = new SvgRect( $x1, $y1, $x2 - $x1, $y2 - $y1, "fill: none; stroke: " . $this->colors[$color]["html"], "" );
				$this->svggrp->addChild( $ret );
		
				break;
		}
	}

	/**
	 * Draw a arc.
	 *
	 * @param $x center x
	 * @param $y center y
	 * @param $w x size
	 * @param $h y size
	 * @param $s start angle
	 * @param $e end angle
	 * @param $color color
	 * @return true or false
	 */
	function arc( $x, $y, $w, $h, $s, $e, $color ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
		
			case "jpg":
				$ret = ImageArc( $this->gdim, $x, $y, $w, $h, $s, $e, $color );
				break;
	
			case "svg":
				list( $x, $y ) = array( 
					AbstractImage::xBounds( $x ),
					AbstractImage::yBounds( $y ) 
				);

				$xs  = $x + 0.5 * $w * cos( deg2rad( $s ) );
				$ys  = $y + 0.5 * $h * sin( deg2rad( $s ) );
				$xe  = $x + 0.5 * $w * cos( deg2rad( $e ) );
				$ye  = $y + 0.5 * $h * sin( deg2rad( $e ) );
				$ret = new SvgPath( "M$xs,$ys A" . ( $w / 2 ) . "," . ( $h / 2 ) ." 0 0,1 $xe,$ye", "fill: none; stroke: " . $this->colors[$color]["html"], "" );
				$this->svggrp->addChild( $ret );
	
				break;
		}
	}

	/**
	 * Draw a filled arc.
	 *
	 * @param $x center x
	 * @param $y center y
	 * @param $w x size
	 * @param $h y size
	 * @param $s start angle
	 * @param $e end angle
	 * @param $color color
	 * @return true or false
	 */
	function filledArc( $x, $y, $w, $h, $s, $e, $color, $style ) 
	{
		switch ( $this->output_type ) 
		{
			case "png":
		
			case "jpg":
				$ret = ImageFilledArc( $this->gdim, $x, $y, $w, $h, $s, $e, $color, $style );
				break;

			case "svg":
				list( $x, $y ) = array( 
					AbstractImage::xBounds( $x ),
					AbstractImage::yBounds( $y )
				);
				
				$xs = AbstractImage::roundCoord( $x + 0.5 * $w * cos( deg2rad( $s ) ) );
				$ys = AbstractImage::roundCoord( $y + 0.5 * $h * sin( deg2rad( $s ) ) );
				$xe = AbstractImage::roundCoord( $x + 0.5 * $w * cos( deg2rad( $e ) ) );
				$ye = AbstractImage::roundCoord( $y + 0.5 * $h * sin( deg2rad( $e ) ) );
	
				if ($ style & IMG_ARC_NOFILL ) 
					$fill = "none";
				else 
					$fill = $this->colors[$color]["html"];
				
				if ( $style & IMG_ARC_EDGED ) 
				{
					$ret = new SvgPath( "M$x,$y L$xs,$ys A"  .
						AbstractImage::roundCoord( $w / 2 ) . "," .
						AbstractImage::roundCoord( $h / 2 ) . " 0 0,1 $xe,$ye z",
						"fill: $fill; stroke: " . $this->colors[$color]["html"], ""
					);
				} 
				else 
				{
					$ret = new SvgPath( "M$x,$y L$xs,$ys A" .
						AbstractImage::roundCoord( $w / 2 ) . "," .
						AbstractImage::roundCoord( $h / 2 ) . " 0 0,1 $xe,$ye z",
						"fill: $fill; stroke: " . $this->colors[$color]["html"], ""
					);
				}
			
				$this->svggrp->addChild( $ret );
				break;
		}
	}
} // END OF AbstractImage

?>
