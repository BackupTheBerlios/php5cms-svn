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
 * This class is used to generate very simple MS Excel file (xls) via PHP.
 * The generated xls file can be obtained by web as a stream
 * file or can be written under $default_dir path. This package
 * is also included mysql, pgsql, oci8 database interaction to
 * generate xls files. Limitations:
 * - Max character size of a text(label) cell is 255
 * ( due to MS Excel 5.0 Binary File Format definition )
 *
 * @package format_xls
 */
 
class XLSGenerator extends PEAR
{
	/**
	 * where generated xls be stored
	 * @access public
	 */
	var $xls_data = "";
	
	/**
	 * default directory to be saved file
	 * @access public
	 */
	var $default_dir = "";
	
	/**
	 * save filename
	 * @access public
	 */
	var $filename = "psxlsgen";
	
	/**
	 * filename with full path
	 * @access public
	 */
	var $fname = "";
	
	/**
	 * current row number
	 * @access public
	 */
	var $crow = 0;
	
	/**
	 * current column number
	 * @access public
	 */
	var $ccol = 0;
	
	/**
	 * total number of columns
	 * @access public
	 */
	var $totalcol = 0;
	
	/**
	 * 0=stream, 1=file
	 * @access public
	 */
	var $get_type = 0;
	
	/**
	 * 0=no error
	 * @access public
	 */
	var $errno = 0;
	
	/**
	 * error string
	 * @access public
	 */
	var $error = "";
	
	/**
	 * 0=no header, 1=header line for xls table
	 * @access public
	 */
	var $header = 1;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function XLSGenerator()
	{
		$this->default_dir = ap_ini_get( "path_tmp_os", "path" );
		
		// begin of the excel file header
		$this->xls_data = pack( "ssssss", 0x809, 0x08, 0x00,0x10, 0x0, 0x0 );
		
		// check header text
		if ( $this->header )
			$this->Header();
	}


	/**
	 * @access public
	 */
	function Header( $text = "" )
	{
		if ( $text == "" )
			$text = "This file was generated at " . date( "D, d M Y H:i:s T" );
		
        if ( $this->totalcol < 1 )
          $this->totalcol = 1;
        
        $this->InsertText( $text );
        $this->crow += 2;
		$this->ccol  = 0;
	}

	/**
	 * End of the excel file.
	 *
	 * @access public
	 */
	function End()
	{
		$this->xls_data .= pack( "sssssssC", 0x7D, 11, 3, 4, 25600, 0, 0, 0 );
		$this->xls_data .= pack( "ss", 0x0A, 0x00 );
		
		return;
	}

	/**
	 * Write a Number (double) into row, col.
	 *
	 * @access public
	 */
	function WriteNumber_pos( $row, $col, $value )
	{
		$this->xls_data .= pack( "d", $value );
		return;
	}

	/**
	 * Write a label (text) into Row, Col.
	 *
	 * @access public
	 */
	function WriteText_pos( $row, $col, $value )
	{
		$len = strlen( $value );
		$this->xls_data .= $value;
		
		return;
	}

	/**
	 * Insert a number, increment row,col automatically.
	 *
	 * @access public
	 */
	function InsertNumber( $value )
	{
		if ( $this->ccol == $this->totalcol )
		{
			$this->ccol = 0;
			$this->crow++;
		}
		
		$this->WriteNumber_pos( $this->crow, $this->ccol, &$value );
		$this->ccol++;
		
		return;
	}

	/**
	 * Insert a number, increment row, col automatically.
	 *
	 * @access public
	 */
	function InsertText( $value )
	{
		if ( $this->ccol == $this->totalcol )
		{
           $this->ccol = 0;
           $this->crow++;
        }
		
		$this->WriteText_pos( $this->crow, $this->ccol, &$value );
		$this->ccol++;
		
		return;
	}

	/**
	 * Change position of row, col.
	 *
	 * @access public
	 */
	function ChangePos( $newrow, $newcol )
	{
		$this->crow = $newrow;
		$this->ccol = $newcol;
		
		return;
	}

	/**
	 * New line.
	 *
	 * @access public
	 */
	function NewLine()
	{
		$this->ccol = 0;
		$this->crow++;
		
		return;
	}

	/**
	 * Send generated xls as stream file.
	 *
	 * @access public
	 */
	function SendFile( $filename )
	{
		$this->filename = $filename;
		$this->SendFile();
	}
	
	/**
	 * Send generated xls as stream file.
	 *
	 * @access public
	 */
	function SendFile()
	{
		$this->End();
		
		header( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
		header( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
		header( "Pragma: no-cache" );
        header( "Content-type: application/x-msexcel" );
        header( "Content-Disposition: attachment; filename=$this->filename.xls" );
        header( "Content-Description: PHP Generated XLS Data" );
		
		print $this->xls_data;
	}

	/**
	 * Change the default saving directory.
	 *
	 * @access public
	 */
	function ChangeDefaultDir( $newdir )
	{
		$this->default_dir = $newdir;
		return;
	}

	/**
	 * Save generated xls file.
	 *
	 * @access public
	 */
	function SaveFile( $filename )
	{
		$this->filename = $filename;
		$this->SaveFile();
	}

	/**
	 * Save generated xls file.
	 *
	 * @access public
	 */
	function SaveFile()
	{
		$this->End();
		$this->fname = $this->default_dir . $this->filename;
		
		if ( !stristr( $this->fname, ".xls" ) )
			$this->fname .= ".xls";
        
		$fp = fopen( $this->fname, "wb" );
		fwrite( $fp, $this->xls_data );
        fclose( $fp );
		
		return;
	}

	/**
	 * Get generated xls as specified type.
	 *
	 * @access public
	 */
	function GetXls( $type = 0 )
	{
		if ( !$type && !$this->get_type )
			$this->SendFile();
        else
			$this->SaveFile();
	}
} // END OF XLSGenerator

?>
