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
 * @package format_ps
 */
 
class Postscript extends PEAR
{
	/**
	 * @access private
	 */
    var $fp;
	
	/**
	 * @access private
	 */
    var $filename;
	
	/**
	 * @access private
	 */
    var $string = "";
	
	/**
	 * @access private
	 */
    var $page = 1;
	
	/**
	 * @access private
	 */
    var $acentos = "";

	
    /**
	 * Constructor
	 *
	 * @access public
	 */ 
    function Postscript( $fname = "", $author = "PSLib", $title = "Generated with Abstractpage", $orientation = "Portrait" )
    {
        // a text string was requested: file name to create
        if ( $fname )
        {
            if( !$this->fp = fopen( $fname, "w" ) )
				return false;
        }
        
        $this->string .= "%!PS-Adobe-3.0 \n";
        $this->string .= '%%Creator: '				. $author . "\n";
        $this->string .= '%%CreationDate: '			. date( "d/m/Y, H:i" ) . "\n";
        $this->string .= '%%Title: '				. $title . "\n";
        $this->string .= "%%PageOrder: Ascend \n";
        $this->string .= '%%Orientation: '			. $orientation . "\n";
        $this->string .= "%%EndComments \n";
        $this->string .= "%%BeginProlog \n";
        $this->string .= "%%BeginResource: definicoes \n";
		
        // Comment this to disable support for international character encoding.
        if ( file_exists( 'accents.ps' ) )
        {
             if ( $f = join( '', file( 'accents.ps' ) ) )
			 	$this->string .= $f;
        }

        $this->string .= "%%EndResource \n";
        $this->string .= "%%EndProlog \n";

        return true;
    }


    /**
	 * Begin new page.
	 *
	 * @access public
	 */
    function begin_page( $page )
    {
        $this->string.= "%%Page: " . $page . ' ' . $page . "\n";
        return true;
    }

    /**
	 * End page.
	 *
	 * @access public
	 */
    function end_page()
    {
        $this->string .= "showpage \n";
        return true;
    }

    /**
	 * Close the postscript file.
	 *
	 * @access public
	 */
    function close()
    {
        $this->string .= "showpage \n";
        
		if ( $this->fp )
		{
           fwrite( $this->fp, $this->string );
           fclose( $this->fp );
		}

        return ( $this->string );
	}

    /**
	 * Draw a line.
	 *
	 * @access public
	 */
    function line( $xcoord_from = 0, $ycoord_from = 0, $xcoord_to = 0, $ycoord_to = 0, $linewidth = 0 )
    {
        if ( !$xcoord_from || !$ycoord_to || !$xcoord_to || !$ycoord_to || !$linewidth )
			return false;
        
        $this->string .= $linewidth   . " setlinewidth  \n";
        $this->string .= $xcoord_from . ' ' . $ycoord_from . " moveto \n";
        $this->string .= $xcoord_to   . ' ' . $ycoord_to   . " lineto \n";
        $this->string .= "stroke \n";
        
        return true;
	}

	/**
	 * Move to coordinates.
	 *
	 * @access public
	 */
    function moveto( $xcoord, $ycoord )
    {
        if ( empty( $xcoord ) || empty( $ycoord ) )
			return false;
        
        $this->string .= $xcoord . ' ' . $ycoord . " moveto \n";
        return true;
    }

	/**
	 * Move to coordinates and change the font.
	 *
	 * @access public
	 */
    function moveto_font( $xcoord, $ycoord, $font_name, $font_size )
    {
        if ( !$xcoord || !$ycoord || !$font_name || !$font_size )
			return false;
        
        $this->string .= $xcoord . ' ' . $ycoord . " moveto \n";
        $this->string .= '/' . $font_name . ' findfont ' . $font_size . " scalefont setfont \n";
        
        return true;
    }

	/**
	 * Insert a PS file/image (remember to delete the information in the top of the file (source)).
	 *
	 * @access public
	 */
    function open_ps( $ps_file = "" )
    {
        if ( !$ps_file )
			return false;

        if ( $f = join( '', file( $ps_file ) ) )
        	$this->string .= $f;
        else
        	return false;

        return true;
    }

	/**
	 * Draw a rectangle.
	 *
	 * @access public
	 */
    function rect( $xcoord_from, $ycoord_from, $xcoord_to, $ycoord_to, $linewidth )
    {
		if ( !$xcoord_from || !$ycoord_from || !$xcoord_to || !$ycoord_to || !$linewidth )
			return false;

		$this->string .= $linewidth . " setlinewidth  \n";
		$this->string .= "newpath \n";
		$this->string .= $xcoord_from . ' ' . $ycoord_from  . " moveto \n";
		$this->string .= $xcoord_to   . ' ' . $ycoord_from  . " lineto \n";
		$this->string .= $xcoord_to   . ' ' . $ycoord_to    . " lineto \n";
		$this->string .= $xcoord_from . " " . $ycoord_to    . " lineto \n";
		$this->string .= "closepath \n";
		$this->string .= "stroke \n";

		return true;
	}

	/**
	 * Draw and shade a rectangle.
	 *
	 * @access public
	 */
    function rect_fill( $xcoord_from, $ycoord_from, $xcoord_to, $ycoord_to, $linewidth, $darkness )
    {
		if ( !$xcoord_from || !$ycoord_from || !$xcoord_to || !$ycoord_to || !$linewidth || !$darkness )
			return false;

		$this->string .= "newpath \n";
		$this->string .= $linewidth . " setlinewidth  \n";
		$this->string .= $xcoord_from . ' ' . $ycoord_from  . " moveto \n";
		$this->string .= $xcoord_to   . ' ' . $ycoord_from  . " lineto \n";
		$this->string .= $xcoord_to   . ' ' . $ycoord_to    . " lineto \n";
		$this->string .= $xcoord_from . ' ' . $ycoord_to    . " lineto \n";
		$this->string .= "closepath \n";
		$this->string .= "gsave \n";
		$this->string .= $darkness . " setgray  \n";
		$this->string .= "fill \n";
		$this->string .= "grestore \n";
		$this->string .= "stroke \n";

		return true;
	}

	/**
	 * Set rotation, use 0 or 360 to end rotation.
	 *
	 * @access public
	 */
	function rotate( $degrees )
	{
		if ( !$degrees )
			return false;

		if ( ( $degrees == '0' ) || ( $degrees == '360' ) )
			$this->string .= "grestore \n";
		else
		{
			$this->string .= "gsave \n";
			$this->string .= $degrees . " rotate \n";
		}

		return true;
	}

	/**
	 * Set the font to show.
	 *
	 * @access public
	 */
	function set_font( $font_name, $font_size )
	{
		if ( !$font_name || !$font_size )
			return false;

		$this->string .= '/' . $font_name . ' findfont ' . $font_size . " scalefont setfont \n";
		return true;
	}
	
	/**
	 * Show some text at the current coordinates (use 'moveto' to set coordinates).
	 *
	 * @access public
	 */
	function show( $text )
	{
		if ( !$text )
			return false;

		$this->string .=  '(' . $text . ") show \n";
		return true;
	}

	/**
	 * Evaluate the text and show it at the current coordinates.
	 *
	 * @access public
	 */
	function show_eval( $text )
	{
		if ( !$text )
			return false;
       
		eval( "\$text = \"$text\";" );
		$this->string .= '(' . $text . ") show \n";
       
		return true;
	}

	/**
	 * Show some text at specific coordinates.
	 *
	 * @access public
	 */
	function show_xy( $text, $xcoord, $ycoord )
	{
		if ( !$text || !$xcoord || !$ycoord )
			return false;
       
		$this->moveto( $xcoord, $ycoord );
		$this->show( $text );

		return true;
	}

	/**
	 * Show some text at specific coordinates with font settings.
	 *
	 * @access public
	 */
	function show_xy_font( $text, $xcoord, $ycoord, $font_name, $font_size )
	{
		if ( !$text || !$xcoord || !$ycoord || !$font_name || !$font_size )
			return false;

		$this->set_font( $font_name, $font_size );
		$this->show_xy( $text, $xcoord, $ycoord );

		return true;
	}
} // END OF Postscript

?>
