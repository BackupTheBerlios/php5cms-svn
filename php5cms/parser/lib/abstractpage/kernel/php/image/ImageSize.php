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
 * This is a port of perls Image::Size module.
 * It supports JPEG, GIF, PNG, TIF, BMP, WBMP, XPM, XBM, 
 * and PPM out of the box, without the need to compile
 * GD or any other lib into PHP.
 *
 * @package image
 */

class ImageSize extends PEAR
{
	// Currently unused, but exported so the user can do his own detection
	var $type_map = array(	
		'^GIF8[7,9]a'              => "gif",
		"^\xFF\xD8"                => "jpeg",
		"^\x89PNG\x0d\x0a\x1a\x0a" => "png",
		"^P[1-7]"                  => "ppm", // also XVpics
		'\#define\s+\S+\s+\d+'     => "xbm",
		'\/\* XPM \*\/'            => "xpm",
		'^MM\x00\x2a'              => "tiff",
		'^II\x2a\x00'              => "tiff",
		'^BM'                      => "bmp"
	);

	// This contains the picture width after initialization, or -1 if there was an error.
	var $x = -1;

	// This contains the picture height after initialization, or -1 if there was an error.
	var $y = -1;

	// This containse the error message or, if successful, the filetype such as "TIF" or "GIF" etc.
	var $id = "";

	// private vars
	var $suffix   = "";
	var $last_pos = 0;
	var $fp       = 0;

	
	/**
	 * Constructor
	 */
	function ImageSize( $filename, $forced_suffix = "" )
	{
		if ( !file_exists( $filename ) ) 
		{
			$this = new PEAR_Error( "File does not exist." );
			return;
		}

		if ( !is_readable( $filename ) )
		{
			$this = new PEAR_Error( "File is not readable." );
			return;
		}

		// suffix crap
		if ( $forced_suffix == "" )
		{
			if ( !ereg( ".*\.(.*)$", $filename, $regs ) )
			{
				$this = new PEAR_Error( "Could not determine filetype." );
				return;
			}
			else
			{
				$this->suffix = $regs[1];
			}
		}
		else if ( $forced_suffix == "auto" )
		{
			// Need to implement autodetection.
		} 
		else
		{
			$this->suffix = $forced_suffix;
		}
		
		$this->fp = fopen( $filename, "r" );
		$this->suffix = strtolower( $this->suffix );
		
		// print "Suffix is: ".$this->suffix;
		if ( $this->suffix == "jpg" || $this->suffix == "jpeg" )
			$ra = $this->GetJPEGSize( $this->fp );
		else if ( $this->suffix == "bmp" )
			$ra = $this->GetBMPSize( $this->fp );
		else if ( $this->suffix == "xbm" )
			$ra = $this->GetXBMSize( $this->fp );
		else if ( $this->suffix == "xpm" )
			$ra = $this->GetXPMSize( $this->fp );
		else if ( $this->suffix == "png" )
			$ra = $this->GetPNGSize( $this->fp );
		else if ( $this->suffix == "tif" )
			$ra = $this->GetTIFFSize( $this->fp );
		else if ( $this->suffix == "gif" )
			$ra = $this->GetGIFSize( $this->fp );
		else if ( $this->suffix == "ppm" )
			$ra = $this->GetPPMSize( $this->fp );
		else if ( $this->suffix == "wbmp" )
			$ra = $this->GetWBMPSize( $this->fp );

		list( $this->x, $this->y, $this->id ) = $ra;
	}

	
	function read_io( $handle, $length, $offset = -1 )
	{
		if ( ( $offset >= 0 ) && ( $offest != $this->last_pos ) )
		{
			$this->last_pos = $offset;
			
			if ( fseek($handle, $offset, SEEK_SET ) != 0 )
				return "ugh";
		}
		
		$data = ""; 
		$data = fread( $handle, $length );
		$this->last_pos = ftell( $handle );
		
		return $data;
	}

	function detect_type()
	{
		while ( list( $pat, $type ) = each( $type_map ) )
		{
			fseek( $this->fp, 0 );
			$header = $this->read_io( $this->fp, 16 );
			
			if ( preg_match( "/" . $pat . "/", $header ) )
				return $type;
		}
		
		return false;		
	}

	function GetBMPSize( $stream )
	{
		$x  = -1;
		$y  = -1;
		$id = "Unable to determine size of BMP data.";

		$buffer = $this->read_io( $stream, 26 );
		$parray = unpack( "x18nix/Vblah1/Vblah2", $buffer );
		
		$x = $parray["blah1"];
		$y = $parray["blah2"];
		
		if ( $x != -1 && $y != -1 )
			$id = "BMP";
			
		return array( $x, $y, $id );
	}
	
	function GetJPEGSize( $stream )
	{
		$D_MARKER   = "\xFF";
		$SIZE_FIRST = 0xC0;
		$SIZE_LAST  = 0xC3;
		
		$x  = -1;
		$y  = -1;
		$id = "Could not determine JPEG size.";

		// dummy read to skip header ID
		$this->read_io( $stream, 2 );
		
		while (1)
		{
			$length = 4;
			$segheader = $this->read_io( $stream, $length );

			// Extract the segment header.
			$sarray = unpack( "amarker/acode/nlength", $segheader );
			$marker = $sarray["marker"];
			$code   = $sarray["code"];
			$length = $sarray["length"];
			
			// verify that it's a valid segment
			if ( $marker != $D_MARKER )
			{
				// Was it there?
				$id = "JPEG marker not found.";
				break;
			}
			else if ( ( ord( $code ) >= $SIZE_FIRST ) && ( ord( $code ) <= $SIZE_LAST ) )
			{
				// Segments that contain size info.
				$length = 5;
				$sizearray = unpack( "xumm/nblah1/nblah2", $this->read_io( $stream, $length ) );
				$y  = $sizearray["blah1"];
				$x  = $sizearray["blah2"];
				$id = "JPG";
				break;
			}
			else
			{
				// skip over data
				$this->read_io( $stream, ( $length - 2 ) );
			}
		}
		
		return array($x, $y, $id);
	}

	function GetXBMSize( $stream )
	{
		$x     = -1;
		$y     = -1;
		$id    = "Could not determine XBM size.";
		$input = $this->read_io( $stream, 1024 );
		
		if ( preg_match( "/^\#define\s*\S*\s*(\d+)\s*\n\#define\s*\S*\s*(\d+)/si", $input, $regs ) )
		{
			$x  = $regs[1];
			$y  = $regs[2];
			$id = "XBM";
		}
		
		return array( $x, $y, $id );
	}

	function GetXPMSize( $stream )
	{
		$x  = -1;
		$y  = -1;
		$id = "Could not determine XPM size.";
		
		while ( $line = $this->read_io( $stream, 1024 ) )
		{
			if ( !preg_match( "/\"\s*(\d+)\s+(\d+)(\s+\d+\s+\d+){1,2}\s*\"/s", $line, $regs ) )
			{
				continue;
			}
			else
			{
				$x  = $regs[1];
				$y  = $regs[2];
				$id = "XPM";
				
				break;
			}
		}
		
		return array( $x, $y, $id );
	}

	function GetPNGSize( $stream )
	{
		$x  = -1;
		$y  = -1;
		$id = "Could not determine PNG size.";
		
		// Offset to first Chunk Type code = 8-byte ident + 4-byte chunk length + 1.
	    $offset = 12;
		$length = 4;
		
		// Skip $offset bytes to header.
		$this->read_io( $stream, $offset );
		
		if ( $this->read_io( $stream, $length ) == "IHDR" )
		{
			// IHDR = Image Header
			$length = 8;
			$sizearray = unpack( "Nnum1/Nnum2", $this->read_io( $stream, $length ) );
			$x  = $sizearray["num1"];
			$y  = $sizearray["num2"];
			$id = "PNG";
		}

		return array( $x, $y, $id );
	}

	// Seems working, might need fixing.
	function GetPPMSize( $stream )
	{
		$x  = -1;
		$y  = -1;
		$id = "Unable to determine size of PPM/PGM/PBM data.";

		$header = $this->read_io( $stream, 1024 );
		
		// PPM file of some sort
		// preg_replace("s/^\#.*//mg", $header); //!!! Needs fixing !!!
		preg_match( "/^(P[1-7])\s+(\d+)\s+(\d+)/s", $header, $regs );
		$n = $regs[1];
		$x = $regs[2];
		$y = $regs[3];
		
		if ( $n == "P1" || $n == "P4" )
			$id = "PBM";

		if ( $n == "P2" || $n == "P5" )
			$id = "PGM";

		if ( $n == "P3" || $n == "P6" )
			$id = "PPM";

		if ( $n == "P7" )
		{
			$id = "XV";
			preg_match( "/IMGINFO:(\d+)x(\d+)/s", $header, $regs );
			$x = $regs[1];
			$y = $regs[2];
		}
		
		return array( $x, $y, $id );
	}
	
	// Working, could need more verification.
	function GetTIFFSize( $stream )
	{
		$x      = -1;
		$y      = -1;
		$id     = "Unable to determine size of TIFF data.";
		$endian = "n"; // Default to big-endian; I like it better
		$header = $this->read_io( $stream, 4 );
		
		// if (preg_match("/II\x2a\x00/o", $header)) $endian = "v"; // little-endian
		
		$packspec = array( 
			0,						// nothing (shouldn't happen)
			"C",					// BYTE (8-bit unsigned integer)
			0,						// ASCII
			$endian,				// SHORT (16-bit unsigned integer)
			strtoupper( $endian ),	// LONG (32-bit unsigned integer)
			0,						// RATIONAL
			"c",					// SBYTE (8-bit signed integer)
			0,						// UNDEFINED
			$endian,				// SSHORT (16-bit unsigned integer)
			strtoupper( $endian )	// SLONG (32-bit unsigned integer)
		);
		
		fseek( $stream, 4 );
		$offset = $this->read_io( $stream, 4 ); // Get offset to IFD.

		$arr = unpack( strtolower( $endian ) . "blah", $offset ); // Fix it so we can use it
		$offset = $arr["blah"];
		
		fseek( $stream, $offset );
		$ifd = $this->read_io( $stream, 4 ); // Get number of directory entries.

		$arr = unpack( $endian . "blahha", $ifd ); // Make it useful.
		$num_dirent = $arr["blahha"];
		$offset    += 2;
		$num_dirent = $offset + ( $num_dirent * 12 ); // Calc. maximum offset of IFD.

		// Do all the work.
		while ( !isset( $nx ) || !isset( $ny ) ) 
		{	
			fseek( $stream, $offset );
			$ifd = $this->read_io( $stream, 12 ); // Get first directory entry.

			if ( ( $ifd == "" ) || ( $offset > $num_dirent ) )
				break;

			$offset += 12;
			$tar     = unpack( $endian . "blah", $ifd ); // ...and decode its tag
			$tag     = $tar["blah"];
			$tyar    = unpack( $endian . "blah", substr( $ifd, 2, 2 ) ); // .. and the data type
			$type    = $tyar["blah"];
			
			// Check the type for sanity.
			if ( ( $type > count( $packspec ) ) /*||($packspec[$type] == 0)*/ )
				continue;

			if ( $tag == 0x0100 ) // ImageWidth (x)
			{
				$xa = unpack( $packspec[$type] . "blah", substr( $ifd, 8, 4 ) );
				$nx = $xa["blah"];
			}
			else if ( $tag == 0x0101 ) // ImageLength (y)
			{
				$ya = unpack( $packspec[$type] . "blah", substr( $ifd, 8, 4 ) );
				$ny = $ya["blah"];
			}
		}
		
		if ( isset( $nx ) && isset( $ny ) )
		{
			$x = $nx;
			$y = $ny;
		}
		
		// Decide if we were successful or not.
		if ( $x != -1 && $y != -1 )
		{
			$id = "TIF";
		}
		else
		{
			$id = "";
			
			if ( $x == -1 )
				$id = "ImageWidth ";
			
			if ( $y == -1 )
			{
				if ( $id != "" )
					$id .= "& ";
					
				$id .= "ImageLength";
			}
			
			$id .= " tag(s) could not be found.";
		}
		
		return( array ( $x, $y, $id ) );
	}

	function GIF_blockskip( $stream, $skip, $type )
	{
		$this->read_io( $stream, $skip ); // Skip Header (if any).
		
		while ( 1 )
		{
			if ( /*$this->img_eof($stream)*/ feof( $stream ) )
				return array( -1, -1, "Invalid/Corrupted GIF (at EOF in GIF " . $type . ")" );
			
			$lbuf = $this->read_io( $stream, 1 ); // Block size
			
			if ( ord( $lbuf ) == 0 )
				break;
				
			$this->read_io( $stream, ord( $lbuf ) );
		}
	}

	// Working, could need more verifications
	function GetGIFSize( $stream )
	{
		$type = $this->read_io( $stream, 6 );
		
		if ( strlen( $buf = $this->read_io( $stream, 7 ) ) != 7 )
			return array( -1, -1, "Invalid/Corrupted GIF (bad header)" );

		$xar = unpack( "x4nix/Cblah", $buf );
		$x   = $xar["blah"];
		
		if ( $x & 0x80 )
		{
			$cmapsize = 3 * ( pow( 2, ( ( $x & 0x07 ) + 1 ) ) ); // $cmapsize = 3 * (2**(($x & 0x07) + 1));
			
			if ( !$this->read_io( $stream, $cmapsize ) )
				return array( -1, -1, "Invalid/Corrupted GIF (global color map too small?)" );
		}

		while ( 1 )
		{
			if ( feof( $stream ) )
				return array( -1, -1, "Invalid/Corrupted GIF (at EOF w/o Image Descriptors)" );
				
			$buf    = $this->read_io( $stream, 1 );
			$xarray = unpack( "Cblah", $buf );
			$x      = $xarray["blah"];
			
			if ( $x == 0x2c )
			{
				// Image Descriptor (GIF87a, GIF89a 20.c.i).
				if ( strlen( $buf = $this->read_io( $stream, 8 ) ) != 8 )
					return array( -1, -1, "Invalid/Corrupted GIF (missing image header?)" );

				$bar = unpack( "x4nix/Cnum1/Cnum2/Cnum3/Cnum4", $buf );
				$x   = $bar["num1"];
				$w   = $bar["num2"];
				$y   = $bar["num3"];
				$h   = $bar["num4"];
				$x  += $w * 256;
				$y  += $h * 256;
				
				// if ($x > 10000 || $y > 10000) continue; // somethings wrong..
				return array( $x, $y, "GIF" );
			}
			
			if ( $x == 0x21 )
			{
				// Extension Introducer (GIF89a 23.c.i, could also be in GIF87a).
				$buf  = $this->read_io( $stream, 1 );
				$xarr = unpack( "Cblah", $buf );
				$x    = $xarr["blah"];
				
				if ( $x == 0x21 )
					continue;
				
				if ( $x == 0xF9 )
				{
					// Graphic Control Extension (GIF89a 23.c.ii).
					// print "passed control once";
					$this->read_io($stream, 6);
					continue;
				}
				else if ( $x == 0xFE )
				{
					// Comment Extension (GIF89a 24.c.ii).
					// print "passed comment once";
					$this->GIF_blockskip( $stream, 0, "Comment" );
					continue;
				}
				else if ( $x == 0x01 )
				{
					// Plain Text Label (GIF89a 25.c.ii).
					// print "passed plain text once";
					$this->GIF_blockskip( $stream, 13, "text data" );
					continue;
				}
				else if ( $x == 0xFF )
				{
					// Application Extension Label (GIF89a 26.c.ii).
					// print "passed app once";
					$this->GIF_blockskip( $stream, 12, "application data" );
					continue;
				}
				else
				{
					return array( -1, -1, "Invalid/Corrupted GIF (Unknown extension: " . $x . ")" );
				}
			}
			else
			{
				continue; // ?
				return array( -1, -1, "Invalid/Corrupted GIF (Unknown code: " . $x . ")" );
			}
		}
	}

	
	// WBMP STUFF EXPERIMENTAL!

	// Skip over ExtHeaders
	function wbmp_skipheader( $stream )
	{
		$i = 128;
		
		while ( $i & 0x80 )
		{
			$i = $this->read_io( $stream, 1 );
			
			if ( $i < 0 )
				return( -1 );
		}
		
		return false;
	}

	function wbmp_getmbi( $stream )
	{
		$i   = 128;
		$mbi = 0;

		while ( $i & 0x80 )
		{
			$i   = $this->read_io( $stream, 1 );
			$arr = unpack( "Cblah", $i );
			$i   = $arr["blah"];
			
			if ( $i < 0 )
				return -1;
				
			$mbi = $mbi << 7 | ( $i & 0x7f );
		}
		
		return ( $mbi );
	}

	// Seems working, could need verification.
	function GetWBMPSize( $stream )
	{
		$x  = -1;
		$y  = -1;
		$id = "Unable to determine size of WBMP data.";

		fseek( $stream, 0 );
		$wtype = $this->read_io( $stream, 1 );
		
		if ( $wtype != 0 )
			return array( $x, $y, $id );
		
		if ( $this->wbmp_skipheader( $stream ) )
			return array( $x, $y, $id );

		$x = $this->wbmp_getmbi( $stream );
		
		if ( $x == 0 )
			$x = -1;
			
		$y = $this->wbmp_getmbi( $stream );

		if ( $y == 0 )
			$y = -1;

		if ( $x != -1 && $y != -1 )
			$id = "WBMP";
		
		return array( $x, $y, $id );
	}
} // END OF ImageSize

?>
