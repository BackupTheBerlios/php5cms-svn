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


using( 'format.xls.workbook.XLSBiffWriter' );
using( 'format.xls.workbook.XLSParser' );


/**
 * Class for generating Excel Spreadsheets.
 *
 * @package format_xls_workbook
 */

class XLSWorksheet extends XLSBiffWriter
{
	/**
	 * Constructor
	 *
	 * @param $name       The name of the new worksheet
	 * @param $index      The index of the new worksheet
	 * @param $activeshee The current activesheet of the workbook we belong to (is this necesary?)
	 * @param $firstsheet The first worksheet in the workbook we belong to (is this necesary?)
	 * @param $url_format The default format for hyperlinks
	 */
	function XLSWorksheet( $name, $index, &$activesheet, &$firstsheet, &$url_format )
    {
		$this->XLSBiffWriter(); 

		$rowmax = 65536; // 16384 in Excel 5
		$colmax = 256;
		$strmax = 255;

		$this->name            = $name;
		$this->index           = $index;
		$this->activesheet     = &$activesheet;
		$this->firstsheet      = &$firstsheet;
		$this->url_format      = &$url_format;
 
		$this->ext_sheets      = array();
		$this->using_tmpfile   = 1;
		$this->filehandle      = "";
		$this->fileclosed      = 0;
		$this->offset          = 0;
		$this->xls_rowmax      = $rowmax;
		$this->xls_colmax      = $colmax;
		$this->xls_strmax      = $strmax;
		$this->dim_rowmin      = $rowmax + 1;
		$this->dim_rowmax      = 0;
		$this->dim_colmin      = $colmax + 1;
		$this->dim_colmax      = 0;
		$this->colinfo         = array();
		$this->selection       = array( 0, 0, 0, 0 );
		$this->panes           = array();
		$this->active_pane     = 3;
		$this->frozen          = 0;
		$this->selected        = 0;

		$this->_paper_size     = 0x0;
		$this->_orientation    = 0x1;
		$this->_header         = '';
		$this->_footer         = '';
		$this->_hcenter        = 0;
		$this->_vcenter        = 0;
		$this->_margin_head    = 0.50;
		$this->_margin_foot    = 0.50;
		$this->_margin_left    = 0.75;
		$this->_margin_right   = 0.75;
		$this->_margin_top     = 1.00;
		$this->_margin_bottom  = 1.00;

		$this->_title_rowmin   = null;
		$this->_title_rowmax   = null;
		$this->_title_colmin   = null;
		$this->_title_colmax   = null;

		$this->print_gridlines = 1;
		$this->print_headers   = 0;

		$this->fit_page        = 0;
		$this->fit_width       = 0;
		$this->fit_height      = 0;

		$this->hbreaks         = array();
		$this->vbreaks         = array();

		$this->protect         = 0;

		$this->col_sizes       = array();
		$this->row_sizes       = array();

		$this->zoom            = 100;
		$this->print_scale     = 100;

		$this->initialize();
	}

	/**
	 * Open a tmp file to store the majority of the Worksheet data. If this fails,
	 * for example due to write permissions, store the data in memory. This can be
	 * slow for large files.
	 */
  	function initialize()
    {
    	// open tmp file for storing Worksheet data
    	$fh = tmpfile();
    
		if ( $fh )
		{
        	// binmode file whether platform requires it or not
        	// binmode($fh);
        	// store filehandle
        	$this->filehandle = $fh;
        }
    	else
		{
        	// if tmpfile() fails store data in memory
        	$this->using_tmpfile = 0;
        }
    }

	/**
	 * Add data to the beginning of the workbook (note the reverse order)
	 * and to the end of the workbook.
	 */
  	function close( $sheetnames )
    {
    	$num_sheets = count( $sheetnames );

		// prepend in reverse order!!

		// Prepend the sheet dimensions
		$this->store_dimensions();

		// Prepend the sheet password
		// $this->store_password();

		// Prepend the sheet protection
		// $this->store_protect();

		// Prepend the page setup
		$this->_store_setup();

		// Prepend the bottom margin
		$this->_store_margin_bottom();

		// Prepend the top margin
		$this->_store_margin_top();

		// Prepend the right margin
		$this->_store_margin_right();

		// Prepend the left margin
		$this->_store_margin_left();

		// Prepend the page vertical centering
		$this->store_vcenter();

		// Prepend the page horizontal centering
		$this->store_hcenter();

		// Prepend the page footer
		$this->store_footer();

		// Prepend the page header
		$this->store_header();

		// Prepend the vertical page breaks
		// $this->store_vbreak();

		// Prepend the horizontal page breaks
		// $this->store_hbreak();

		// Prepend WSBOOL
		$this->store_wsbool();

		// Prepend GRIDSET
		$this->store_gridset();

		// Prepend PRINTGRIDLINES
		$this->store_print_gridlines();

		// Prepend PRINTHEADERS
		$this->store_print_headers();

		// Prepend EXTERNSHEET references
		for ( $i = $num_sheets; $i > 0; $i-- )
		{
        	$sheetname = $sheetnames[$i - 1];
        	$this->store_externsheet( $sheetname );
        }

    	// Prepend the EXTERNCOUNT of external references.
    	$this->store_externcount( $num_sheets );

    	// Prepend the COLINFO records if they exist
    	if ( !empty( $this->colinfo ) )
		{
        	for ( $i = 0; $i < count( $this->colinfo ); $i++ )
				$this->store_colinfo( $this->colinfo[$i] );
         
        	$this->store_defcol();
        }

    	// Prepend the BOF record
    	$this->_store_bof( 0x0010 );

		// End of prepend. Read upwards from here.

    	// Append
    	$this->store_window2();
    	$this->store_zoom();
    
		if ( !empty( $this->panes ) )
      		$this->store_panes( $this->panes );
    
		$this->store_selection( $this->selection );
    	$this->_store_eof();
	}

	/**
	 * Retrieve the worksheet name. This is usefull when creating worksheets
	 * without a name.
	 */
  	function get_name()
    {
    	return $this->name;
    }

	/**
	 * Retrieves data from memory in one chunk, or from disk in $buffer
	 * sized chunks.
	 */
  	function get_data()
    {
    	$buffer = 4096;

    	// Return data stored in memory
    	if ( isset( $this->data ) )
		{
        	$tmp = $this->data;
        	unset($this->data);
        	$fh = $this->filehandle;
        
			if ( $this->using_tmpfile )
            	fseek( $fh, 0 );
            
        	return ( $tmp );
        }
    
		// Return data stored on disk
    	if ( $this->using_tmpfile )
		{
        	if ( $tmp = fread( $this->filehandle, $buffer ) )
            	return ( $tmp );
        }

    	// No data to return
    	return ( '' );
    }

	/**
	 * Set this worksheet as a selected worksheet, i.e. the worksheet has its tab
	 * highlighted.
	 */
  	function select()
    {
    	$this->selected = 1;
    }

	/**
	 * Set this worksheet as the active worksheet, i.e. the worksheet that is
	 * displayed when the workbook is opened. Also set it as selected.
	 */
  	function activate()
    {
    	$this->selected = 1;
    	$this->activesheet =& $this->index;
    }

	/**
	 * Set this worksheet as the first visible sheet. This is necessary
	 * when there are a large number of worksheets and the activated
	 * worksheet is not visible on the screen.
	 */
  	function set_first_sheet()
    {
    	$this->firstsheet = $this->index;
    }

	/**
	 * Set the width of a single column or a range of column.
	 * See also: _store_colinfo
	 */
  	function set_column( $firstcol, $lastcol, $width, $format = 0, $hidden = 0 )
    {
    	$this->colinfo[] = array( $firstcol, $lastcol, $width, &$format, $hidden );

    	// Set width to zero if column is hidden
    	$width = ( $hidden )? 0 : $width;

    	for ( $col = $firstcol; $col <= $lastcol; $col++ )
        	$this->col_sizes[$col] = $width;
	}

	/**
	 * Set which cell or cells are selected in a worksheet
	 *
	 * @param integer $first_row    first row in the selected quadrant
	 * @param integer $first_column first column in the selected quadrant
	 * @param integer $last_row     last row in the selected quadrant
	 * @param integer $last_column  last column in the selected quadrant
	 * @see store_selection
	 */
  	function set_selection( $first_row, $first_column, $last_row, $last_column )
    {
    	$this->selection = array( $first_row, $last_row, $first_column, $last_column );
    }

	/**
	 * Set the page orientation as portrait.
	 */
  	function set_portrait()
    {
    	$this->_orientation = 1;
    }

	/**
	 * Set the page orientation as landscape.
	 */
  	function set_landscape()
    {
    	$this->_orientation = 0;
    }

	/**
	 * Set the paper type. Ex. 1 = US Letter, 9 = A4
	 */
  	function set_paper( $size = 0 )
    {
    	$this->_paper_size = $size;
    }

	/**
	 * Set the page header caption and optional margin.
	 *
	 * @param string $string The header text
	 * @param $margin        optional head margin in inches.
	 */
  	function set_header( $string, $margin = 0.50 )
    {
		// Header string must be less than 255 characters
    	if ( strlen( $string ) >= 255 )
        	return;
        
    	$this->_header = $string;
    	$this->_margin_head = $margin;
    }

	/**
	 * Set the page footer caption and optional margin.
	 *
	 * @param string $string The footer text
	 * @param $margin        optional foot margin in inches.
	 */
  	function set_footer( $string, $margin = 0.50 )
    {
		// Footer string must be less than 255 characters
    	if ( strlen( $string ) >= 255 )
			return;
        
		$this->_footer = $string;
		$this->_margin_foot = $margin;
	}

	/**
	 * Center the page horinzontally.
	 */
  	function center_horizontally( $center = 1 )
    {
		$this->_hcenter = $center;
    }

	/**
	 * Center the page horinzontally.
	 */
  	function center_vertically( $center = 1 )
    {
		$this->{_vcenter} = $center;
    }

	/**
	 * Set all the page margins to the same value in inches.
	 */
  	function set_margins( $margin )
    {
		$this->set_margin_left( $margin );
		$this->set_margin_right( $margin );
		$this->set_margin_top( $margin );
		$this->set_margin_bottom( $margin );
    }

	/**
	 * Set the left and right margins to the same value in inches.
	 */
  	function set_margins_LR( $margin )
    {
    	$this->set_margin_left( $margin );
    	$this->set_margin_right( $margin );
    }

	/**
	 * Set the top and bottom margins to the same value in inches.
	 */
  	function set_margins_TB( $margin )
    {
    	$this->set_margin_top( $margin );
    	$this->set_margin_bottom( $margin );
    }

	/**
	 * Set the left margin in inches.
	 */
  	function set_margin_left( $margin = 0.75 )
    {
    	$this->_margin_left = $margin;
    }

	/**
	 * Set the right margin in inches.
	 */
  	function set_margin_right( $margin = 0.75 )
    {
    	$this->_margin_right = $margin;
    }

	/**
	 * Set the top margin in inches.
	 */
  	function set_margin_top( $margin = 1.00 )
    {
    	$this->_margin_top = $margin;
    }

	/**
 	 * Set the bottom margin in inches.
	 */
  	function set_margin_bottom( $margin = 1.00 )
    {
    	$this->_margin_bottom = $margin;
    }

	/**
	 * Set the worksheet zoom factor.
	 *
	 * @param integer $scale The zoom factor
	 */
  	function set_zoom( $scale = 100 )
    {
    	// Zoom factor $scale outside range: 10 <= zoom <= 400
    	if ( $scale < 10 || $scale > 400 )
			$scale = 100;

    	$this->zoom = (int)$scale;
    }

	/**
	 * Map to the appropriate write method acording to the token recieved.
	 *
	 * @param $row    The row of the cell we are writing to
	 * @param $col    The column of the cell we are writing to
	 * @param $token  What we are writing
	 * @param $format The optional format to apply to the cell
	 */
  	function write( $row, $col, $token, $format = 0 )
    {
    	// Match number
    	if ( preg_match( "/^([+-]?)(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?$/", $token ) )
        	return $this->write_number( $row, $col, $token, $format );
    	// Match formula
    	else if ( preg_match( "/^=/", $token ) )
        	return $this->write_formula( $row, $col, $token, $format );
    	// Match blank
    	else if ( $token == '' )
        	return $this->write_blank( $row, $col, $format );
    	// Default: match string
    	else
        	return $this->write_string( $row, $col, $token, $format );
	}

	/**
	 * Store Worksheet data in memory using the parent's class append() or to a
	 * temporary file, the default.
	 */
  	function append( $data )
    {
    	if ( $this->using_tmpfile )
        {
        	// Add CONTINUE records if necessary
			fwrite( $this->filehandle, $data );
			$this->_datasize += strlen( $data );
        }
    	else
		{
        	parent::append( $data );
        }
    }

	/**
	 * Write a double to the specified row and column (zero indexed).
	 * An integer can be written as a double. Excel will display an
	 * integer. $format is optional.
	 *
	 * Returns  0 : normal termination
	 *         -2 : row or column out of range
	 *
	 * @param $row Zero indexed row
	 * @param $col Zero indexed column
	 * @param $num The number to write
	 */
  	function write_number( $row, $col, $num, $format = 0 )
    {
    	$record    = 0x0203; // Record identifier
    	$length    = 0x000E; // Number of bytes to follow

		$xf = $this->_XF( &$format ); // The cell format

		// Check that row and col are valid and store max and min values
		if ( $row >= $this->xls_rowmax )
			return ( -2 );
        
    	if ( $col >= $this->xls_colmax )
        	return ( -2 );
        
    	if ( $row < $this->dim_rowmin ) 
        	$this->dim_rowmin = $row;
        
    	if ( $row > $this->dim_rowmax ) 
        	$this->dim_rowmax = $row;
        
    	if ( $col < $this->dim_colmin ) 
        	$this->dim_colmin = $col;
        
    	if ( $col > $this->dim_colmax ) 
        	$this->dim_colmax = $col;

    	$header    = pack( "vv",  $record, $length );
    	$data      = pack( "vvv", $row, $col, $xf  );
    	$xl_double = pack( "d",   $num );
    
		// if it's Big Endian
		if ( $this->_byte_order )
			$xl_double = strrev( $xl_double );

    	$this->append( $header . $data . $xl_double );
    	return ( 0 );
    }

	/**
	 * Write a string to the specified row and column (zero indexed).
	 * NOTE: there is an Excel 5 defined limit of 255 characters.
	 * $format is optional.
	 * Returns  0 : normal termination
	 *         -1 : insufficient number of arguments
	 *         -2 : row or column out of range
	 *         -3 : long string truncated to 255 chars
	 *
	 * @param integer $row Zero indexed row
	 * @param integer $col Zero indexed column
	 * @param string $str  The string to write
	 * @param $format      The XF format for the cell
	 */
  	function write_string( $row, $col, $str, $format = 0 )
    {
    	$strlen    = strlen( $str );
    	$record    = 0x0204;					// Record identifier
    	$length    = 0x0008 + $strlen;			// Bytes to follow
    	$xf        = $this->_XF( &$format );	// The cell format
    
		$str_error = 0;

		// Check that row and col are valid and store max and min values
		if ( $row >= $this->xls_rowmax ) 
			return ( -2 );
        
		if ( $col >= $this->xls_colmax ) 
			return ( -2 );
        
		if ( $row < $this->dim_rowmin ) 
        	$this->dim_rowmin = $row;
        
    	if ( $row > $this->dim_rowmax ) 
       		$this->dim_rowmax = $row;
        
    	if ( $col < $this->dim_colmin ) 
        	$this->dim_colmin = $col;
        
    	if ( $col > $this->dim_colmax ) 
        	$this->dim_colmax = $col;

		// LABEL must be < 255 chars
    	if ( $strlen > $this->xls_strmax )
        {
	        $str       = substr( $str, 0, $this->xls_strmax );
   	     	$length    = 0x0008 + $this->xls_strmax;
        	$strlen    = $this->xls_strmax;
        	$str_error = -3;
        }

    	$header = pack( "vv",   $record, $length );
    	$data   = pack( "vvvv", $row, $col, $xf, $strlen );
		
    	$this->append( $header . $data . $str );
		return ( $str_error );
	}

	/**
	 * Write a blank cell to the specified row and column (zero indexed).
	 * A blank cell is used to specify formatting without adding a string
	 * or a number.
	 *
	 * A blank cell without a format serves no purpose. Therefore, we don't write
	 * a BLANK record unless a format is specified. This is mainly an optimisation
	 * for the write_row() and write_col() methods.
	 *
	 * Returns  0 : normal termination (including no format)
	 *         -1 : insufficient number of arguments
	 *         -2 : row or column out of range
	 *
	 * @param $row    Zero indexed row
	 * @param $col    Zero indexed column
	 * @param $format The XF format
	 */
  	function write_blank( $row, $col, $format )
    {
    	// Don't write a blank cell unless it has a format
    	if ( $format == 0 )
        	return ( 0 );

    	$record = 0x0201;					// Record identifier
    	$length = 0x0006;					// Number of bytes to follow
    	$xf     = $this->_XF( &$format );	// The cell format

		// Check that row and col are valid and store max and min values
		if ( $row >= $this->xls_rowmax ) 
      		return ( -2 );
        
    	if ( $col >= $this->xls_colmax ) 
        	return ( -2 );
        
    	if ( $row < $this->dim_rowmin ) 
        	$this->dim_rowmin = $row;
        
    	if ( $row > $this->dim_rowmax ) 
        	$this->dim_rowmax = $row;
        
    	if ( $col < $this->dim_colmin ) 
        	$this->dim_colmin = $col;
        
    	if ( $col > $this->dim_colmax ) 
        	$this->dim_colmax = $col;

    	$header = pack( "vv",  $record, $length );
    	$data   = pack( "vvv", $row, $col, $xf  );
    
		$this->append( $header . $data );
    	return false;
	}

	/**
	 * Write a formula to the specified row and column (zero indexed).
	 * The textual representation of the formula is passed to the parser in
	 * Formula.pm which returns a packed binary string.
	 *
	 * Returns  0 : normal termination
	 *         -1 : insufficient number of arguments
	 *         -2 : row or column out of range
	 *
	 * @param integer $row    Zero indexed row
	 * @param integer $col    Zero indexed column
	 * @param string $formula The formula text string
	 * @param $format         The optional XF format
	 */
  	function write_formula( $row, $col, $formula, $format = 0 )
    {
    	$record = 0x0006; // Record identifier

    	// Excel normally stores the last calculated value of the formula in $num.
    	// Clearly we are not in a position to calculate this a priori. Instead
    	// we set $num to zero and set the option flags in $grbit to ensure
    	// automatic calculation of the formula when the file is opened.
		$xf    = $this->_XF( $format );	// The cell format
		$num   = 0x00;					// Current value of formula
		$grbit = 0x03;					// Option flags
		$chn   = 0x0000;				// Must be zero

    	// Check that row and col are valid and store max and min values
    	if ( $row >= $this->xls_rowmax )
			return ( -2 );
        
    	if ( $col >= $this->xls_colmax )
        	return ( -2 );
        
    	if ( $row < $this->dim_rowmin ) 
        	$this->dim_rowmin = $row;
        
    	if ( $row > $this->dim_rowmax ) 
        	$this->dim_rowmax = $row;
        
    	if ( $col < $this->dim_colmin ) 
        	$this->dim_colmin = $col;
        
    	if ( $col >  $this->dim_colmax ) 
       		$this->dim_colmax = $col;

    	// Strip the = sign at the beginning of the formula string
    	$formula = preg_replace( "/(^=)/", "", $formula );

    	$tree = new XLSParser( $this->_byte_order );
    	$tree->parse( $formula );
    	$formula = $tree->to_reverse_polish();

		$formlen = strlen( $formula );	// Length of the binary string
		$length  = 0x16 + $formlen;		// Length of the record data

		$header  = pack( "vv", $record, $length );
		$data    = pack( "vvvdvVv", $row, $col, $xf, $num, $grbit, $chn, $formlen );

		$this->append( $header . $data . $formula );
		return false;
    }

	/**
	 * This is the more general form of write_url(). It allows a hyperlink to be
	 * written to a range of cells. This function also decides the type of hyperlink
	 * to be written. These are either, Web (http, ftp, mailto), Internal
	 * (Sheet1!A1) or external ('c:\temp\foo.xls#Sheet1!A1').
	 *
	 * See also write_url() above for a general description and return values.
	 *
	 * @param $row1   Start row
	 * @param $col1   Start column
	 * @param $row2   End row
	 * @param $col2   End column
	 * @param $url    URL string
	 * @param $str    Alternative label
	 * @param $format The cell format
	 */
	function write_url_range( $row1, $col1, $row2, $col2, $url, $string = '', $format = 0 )
    {
    	// Check for internal/external sheet links or default to web link
    	if ( preg_match( '[^internal:]', $url ) )
        	return ( $this->_write_url_internal( $row1, $col1, $row2, $col2, $url, $string, $format ) );
        
    	if ( preg_match( '[^external:]', $url ) )
        	return ( $this->_write_url_external( $row1, $col1, $row2, $col2, $url, $string, $format ) );
        
    	return ( $this->_write_url_web( $row1, $col1, $row2, $col2, $url, $string, $format ) );
	}

	/**
	 * This method is used to set the height and XF format for a row.
	 * Writes the  BIFF record ROW.
	 *
	 * @param $row    The row to set
	 * @param $height Height we are giving to the row
	 * @param $XF     XF format we are giving to the row
	 */
  	function set_row( $row, $height, $format = 0 )
    {
    	$record   = 0x0208;					// Record identifier
    	$length   = 0x0010;					// Number of bytes to follow

    	$colMic   = 0x0000;					// First defined column
    	$colMac   = 0x0000;					// Last defined column
    	$miyRw;								// Row height
    	$irwMac   = 0x0000;					// Used by Excel to optimise loading
    	$reserved = 0x0000;					// Reserved
    	$grbit    = 0x01C0;					// Option flags. (monkey) see $1 do
    	$ixfe     = $this->_XF( &$format );	// XF index

		$miyRw = $height *20;

		$header = pack( "vv", $record, $length );
		$data   = pack( "vvvvvvvv", $row, $colMic, $colMac, $miyRw, $irwMac,$reserved, $grbit, $ixfe );
		
		$this->append( $header . $data );
    }

	/**
	 * Writes Excel DIMENSIONS to define the area in which there is data.
	 */
  	function store_dimensions()
    {
    	$record    = 0x0000;			// Record identifier
    	$length    = 0x000A;			// Number of bytes to follow
    	$row_min   = $this->dim_rowmin;	// First row
    	$row_max   = $this->dim_rowmax;	// Last row plus 1
    	$col_min   = $this->dim_colmin;	// First column
    	$col_max   = $this->dim_colmax;	// Last column plus 1
    	$reserved  = 0x0000;			// Reserved by Excel

    	$header    = pack( "vv", $record, $length );
    	$data      = pack( "vvvvv", $row_min, $row_max, $col_min, $col_max, $reserved );
		
    	$this->prepend( $header . $data );
    }

	/**
	 * Write BIFF record Window2.
	 */
  	function store_window2()
    {
    	$record  = 0x023E;		// Record identifier
    	$length  = 0x000A;		// Number of bytes to follow

    	$grbit   = 0x00B6;		// Option flags
    	$rwTop   = 0x0000;		// Top row visible in window
    	$colLeft = 0x0000;		// Leftmost column visible in window
    	$rgbHdr  = 0x00000000;	// Row/column heading and gridline color

    	// The options flags that comprise $grbit
    	$fDspFmla       = 0;				// 0 - bit
    	$fDspGrid       = 1;				// 1
    	$fDspRwCol      = 1;				// 2
    	$fFrozen        = $this->frozen;	// 3
    	$fDspZeros      = 1;				// 4
    	$fDefaultHdr    = 1;				// 5
    	$fArabic        = 0;				// 6
    	$fDspGuts       = 1;				// 7
    	$fFrozenNoSplit = 0;				// 0 - bit
    	$fSelected      = $this->selected;	// 1
    	$fPaged         = 1;				// 2

    	$grbit  = $fDspFmla;
    	$grbit |= $fDspGrid       <<  1;
    	$grbit |= $fDspRwCol      <<  2;
    	$grbit |= $fFrozen        <<  3;
    	$grbit |= $fDspZeros      <<  4;
    	$grbit |= $fDefaultHdr    <<  5;
    	$grbit |= $fArabic        <<  6;
    	$grbit |= $fDspGuts       <<  7;
    	$grbit |= $fFrozenNoSplit <<  8;
    	$grbit |= $fSelected      <<  9;
    	$grbit |= $fPaged         << 10;

    	$header = pack( "vv",   $record, $length );
    	$data   = pack( "vvvV", $grbit, $rwTop, $colLeft, $rgbHdr );
    
		$this->append( $header . $data );
	}

	/**
	 * Write BIFF record DEFCOLWIDTH if COLINFO records are in use.
	 */
  	function store_defcol()
    {
    	$record   = 0x0055; // Record identifier
    	$length   = 0x0002; // Number of bytes to follow
    	$colwidth = 0x0008; // Default column width

    	$header   = pack( "vv", $record, $length );
    	$data     = pack( "v",  $colwidth );
    
		$this->prepend( $header . $data );
    }

	/**
	 * Write BIFF record COLINFO to define column widths
	 *
	 * Note: The SDK says the record length is 0x0B but Excel writes a 0x0C
	 * length record.
	 *
	 * @param array $col_array This is the only parameter received and is composed of the following:
	 * @param $colFirst First formatted column
	 * @param $colLast  Last formatted column
	 * @param $coldx    Col width, 8.43 is Excel default
	 * @param $format   The optional XF format of the column
	 * @param $grbit    Option flags
	 */
  	function store_colinfo( $col_array )
    {
    	if ( isset( $col_array[0] ) )
			$colFirst = $col_array[0];
    
		if ( isset( $col_array[1] ) )
			$colLast = $col_array[1];
		
		if ( isset( $col_array[2] ) )
			$coldx = $col_array[2];
		else
			$coldx = 8.43;
		
		if ( isset( $col_array[3] ) )
			$format = $col_array[3];
		else
			$format = 0;
		
		if ( isset( $col_array[4] ) )
			$grbit = $col_array[4];
		else
			$grbit = 0;
			
		$record   = 0x007D; // Record identifier
		$length   = 0x000B; // Number of bytes to follow

		$coldx   += 0.72;   // Fudge. Excel subtracts 0.72 !?
		$coldx   *= 256;    // Convert to units of 1/256 of a char

		$ixfe     = $this->_XF( &$format );
		$reserved = 0x00; // Reserved

		$header   = pack( "vv", $record, $length );
		$data     = pack( "vvvvvC", $colFirst, $colLast, $coldx, $ixfe, $grbit, $reserved );
		
		$this->prepend( $header . $data );
    }

	/**
	 * Write BIFF record SELECTION.
	 *
	 * @param array $array array containing ($rwFirst,$colFirst,$rwLast,$colLast)
	 * @see set_selection
	 */
  	function store_selection( $array )
    {
    	list( $rwFirst,$colFirst,$rwLast,$colLast ) = $array;
    
		$record   = 0x001D; // Record identifier
    	$length   = 0x000F; // Number of bytes to follow

    	$pnn      = $this->active_pane;	// Pane position
    	$rwAct    = $rwFirst;			// Active row
    	$colAct   = $colFirst;			// Active column
    	$irefAct  = 0;					// Active cell ref
    	$cref     = 1;					// Number of refs

		if ( !isset( $rwLast ) )
			$rwLast = $rwFirst;   // Last row in reference
		
		if ( !isset( $colLast ) )
			$colLast = $colFirst; // Last  col in reference

		// Swap last row/col for first row/col as necessary
		if ( $rwFirst > $rwLast )
		{
        	$aux      = $rwFirst;
        	$rwFirst  = $rwLast;
        	$rwLast   = $aux;
        }

    	if ( $colFirst > $colLast )
		{
        	$aux      = $colFirst;
        	$colFirst = $colLast;
        	$colLast  = $aux;
        }

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "CvvvvvvCC",  $pnn, $rwAct, $colAct, $irefAct, $cref, $rwFirst, $rwLast, $colFirst, $colLast );
    
		$this->append( $header . $data );
    }

	/**
	 * Write BIFF record EXTERNCOUNT to indicate the number of external sheet
	 * references in a worksheet.
	 *
	 * Excel only stores references to external sheets that are used in formulas.
	 * For simplicity we store references to all the sheets in the workbook
	 * regardless of whether they are used or not. This reduces the overall
	 * complexity and eliminates the need for a two way dialogue between the formula
	 * parser the worksheet objects.
	 *
	 * @param $count The number of external sheet references in this worksheet
	 */
  	function store_externcount( $count )
    {
    	$record = 0x0016; // Record identifier
    	$length = 0x0002; // Number of bytes to follow

		$header = pack( "vv", $record, $length );
		$data   = pack( "v",  $count );
		
		$this->prepend( $header . $data );
    }

	/**
	 * Writes the Excel BIFF EXTERNSHEET record. These references are used by
	 * formulas. A formula references a sheet name via an index. Since we store a
	 * reference to all of the external worksheets the EXTERNSHEET index is the same
	 * as the worksheet index.
	 *
	 * @param string $sheetname The name of a external worksheet
	 */
  	function store_externsheet( $sheetname )
    {
    	$record = 0x0017; // Record identifier

		// References to the current sheet are encoded differently to references to
		// external sheets.
    	if ( $this->name == $sheetname )
		{
        	$sheetname = '';
        	$length    = 0x02;  // The following 2 bytes
        	$cch       = 1;     // The following byte
        	$rgch      = 0x02;  // Self reference
        }
    	else
		{
        	$length    = 0x02 + strlen( $sheetname );
        	$cch       = strlen( $sheetname );
        	$rgch      = 0x03; // Reference to a sheet in the current workbook
        }

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "CC", $cch, $rgch );
    
		$this->prepend( $header . $data . $sheetname );
    }
	
	/**
	 * Writes the Excel BIFF PANE record.
	 * The panes can either be frozen or thawed (unfrozen).
	 * Frozen panes are specified in terms of a integer number of rows and columns.
	 * Thawed panes are specified in terms of Excel's units for rows and columns.
	 *
	 * @param $y       Vertical split position
	 * @param $x       Horizontal split position
	 * @param $rwTop   Top row visible
	 * @param $colLeft Leftmost column visible
	 * @param $pnnAct  Active pane
	 */
  	function store_panes( $y, $x, $rwTop, $colLeft, $pnnAct )
    {
    	$record = 0x0041; // Record identifier
    	$length = 0x000A; // Number of bytes to follow

		// Code specific to frozen or thawed panes.
		if ( $this->frozen )
		{
        	// Set default values for $rwTop and $colLeft
        	if ( !isset( $rwTop ) )
            	$rwTop = $y;
            
        	if ( !isset( $colLeft ) )
            	$colLeft = $x;
     	}
    	else
		{
        	// Set default values for $rwTop and $colLeft
        	if ( !isset( $rwTop ) )
            	$rwTop = 0;
            
        	if ( !isset( $colLeft ) )
            	$colLeft = 0;

        	// Convert Excel's row and column units to the internal units.
        	// The default row height is 12.75
        	// The default column width is 8.43
        	// The following slope and intersection values were interpolated.
        	$y = 20 * $y      + 255;
        	$x = 113.879 * $x + 390;
        }

    	// Determine which pane should be active. There is also the undocumented
    	// option to override this should it be necessary: may be removed later.
    	if ( !isset( $pnnAct ) )
        {
        	if ( $x != 0 && $y != 0 )
            	$pnnAct = 0; // Bottom right
        
			if ( $x != 0 && $y == 0 )
            	$pnnAct = 1; // Top right
        
			if ( $x == 0 && $y != 0 )
            	$pnnAct = 2; // Bottom left
        
			if ( $x == 0 && $y == 0 )
            	$pnnAct = 3; // Top left
        }

    	$this->active_pane = $pnnAct; // Used in _store_selection

    	$header     = pack( "vv",    $record, $length );
    	$data       = pack( "vvvvv", $x, $y, $rwTop, $colLeft, $pnnAct );
    
		$this->append( $header . $data );
    }

	/**
	 * Store the header caption BIFF record.
	 */
  	function store_header()
    {
    	$record = 0x0014;			// Record identifier

    	$str    = $this->_header;	// header string
    	$cch    = strlen( $str );	// Length of header string
    	$length = 1 + $cch;			// Bytes to follow

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "C",  $cch );

    	$this->append( $header . $data . $str );
    }

	/**
	 * Store the footer caption BIFF record.
	 */
  	function store_footer()
    {
    	$record = 0x0015;			// Record identifier

    	$str    = $this->_footer;	// Footer string
    	$cch    = strlen( $str );	// Length of footer string
    	$length = 1 + $cch;			// Bytes to follow

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "C",  $cch );

    	$this->append( $header . $data . $str );
    }

	/**
	 * Store the horizontal centering HCENTER BIFF record.
	 */
  	function store_hcenter()
    {
    	$record   = 0x0083;	// Record identifier
    	$length   = 0x0002;	// Bytes to follow

    	$fHCenter = $this->_hcenter; // Horizontal centering

    	$header   = pack( "vv", $record, $length );
    	$data     = pack( "v",  $fHCenter );

    	$this->append( $header . $data );
    }

	/**
	 * Store the vertical centering VCENTER BIFF record.
	 */
  	function store_vcenter()
    {
    	$record   = 0x0084; // Record identifier
    	$length   = 0x0002; // Bytes to follow

    	$fVCenter = $this->_vcenter; // Horizontal centering

    	$header   = pack( "vv", $record, $length );
    	$data     = pack( "v",  $fVCenter );
    
		$this->append( $header . $data );
    }
	
	/**
	 * Write the PRINTHEADERS BIFF record.
	 */
  	function store_print_headers()
    {
    	$record = 0x002a; // Record identifier
    	$length = 0x0002; // Bytes to follow

    	$fPrintRwCol = $this->print_headers; // Boolean flag

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "v",  $fPrintRwCol );
    
		$this->prepend( $header . $data );
    }

	/**
	 * Write the PRINTGRIDLINES BIFF record. Must be used in conjunction with the
	 * GRIDSET record.
	 */
  	function store_print_gridlines()
    {
    	$record = 0x002b; // Record identifier
    	$length = 0x0002; // Bytes to follow

    	$fPrintGrid = $this->print_gridlines; // Boolean flag

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "v",  $fPrintGrid );
    
		$this->prepend( $header . $data );
    }

	/**
	 * Write the GRIDSET BIFF record. Must be used in conjunction with the
	 * PRINTGRIDLINES record.
	 */
  	function store_gridset()
    {
    	$record = 0x0082; // Record identifier
    	$length = 0x0002; // Bytes to follow

    	$fGridSet = !( $this->print_gridlines ); // Boolean flag

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "v",  $fGridSet );
    
		$this->prepend( $header . $data );
    }

	/**
	 * Write the WSBOOL BIFF record, mainly for fit-to-page. Used in conjunction
	 * with the SETUP record.
	 */
  	function store_wsbool()
    {
    	$record = 0x0081; // Record identifier
    	$length = 0x0002; // Bytes to follow

		// The only option that is of interest is the flag for fit to page. So we
		// set all the options in one go.
    	if ( $this->fit_page )
        	$grbit = 0x05c1;
        else
        	$grbit = 0x04c1;

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "v",  $grbit );
    
		$this->prepend( $header . $data );
    }

	/**
	 * Insert a 24bit bitmap image in a worksheet. The main record required is
	 * IMDATA but it must be proceeded by a OBJ record to define its position.
	 *
	 * @param $row     The row we are going to insert the bitmap into
	 * @param $col     The column we are going to insert the bitmap into
	 * @param $bitmap  The bitmap filename
	 * @param $scale_x The horizontal scale
	 * @param $scale_y The vertical scale
	 */
  	function insert_bitmap( $row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1 )
    {
		$bas     = $this->process_bitmap( $bitmap );
    	$width   = $bas[0];
    	$height  = $bas[1];
    	$size    = $bas[2];
    	$data    = $bas[3];

    	// Scale the frame of the image.
    	$width  *= $scale_x;
    	$height *= $scale_y;

    	// Calculate the vertices of the image and write the OBJ record
    	$this->position_image( $col, $row, $x, $y, $width, $height );

    	// Write the IMDATA record to store the bitmap data
    	$record  = 0x007f;
    	$length  = 8 + $size;
    	$cf      = 0x09;
    	$env     = 0x01;
    	$lcb     = $size;
		$header  = pack( "vvvvV", $record, $length, $cf, $env, $lcb );

    	$this->append( $header . $data );
    }

	/**
	 * Calculate the vertices that define the position of the image as required by
	 * the OBJ record.
	 *
	 *         +------------+------------+
	 *         |     A      |      B     |
	 *   +-----+------------+------------+
	 *   |     |(x1,y1)     |            |
	 *   |  1  |(A1)._______|______      |
	 *   |     |    |              |     |
	 *   |     |    |              |     |
	 *   +-----+----|    BITMAP    |-----+
	 *   |     |    |              |     |
	 *   |  2  |    |______________.     |
	 *   |     |            |        (B2)|
	 *   |     |            |     (x2,y2)|
	 *   +---- +------------+------------+
	 *
	 * Example of a bitmap that covers some of the area from cell A1 to cell B2.
	 *
	 * Based on the width and height of the bitmap we need to calculate 8 vars:
	 *     $col_start, $row_start, $col_end, $row_end, $x1, $y1, $x2, $y2.
	 * The width and height of the cells are also variable and have to be taken into
	 * account.
	 * The values of $col_start and $row_start are passed in from the calling
	 * function. The values of $col_end and $row_end are calculated by subtracting
	 * the width and height of the bitmap from the width and height of the
	 * underlying cells.
	 * The vertices are expressed as a percentage of the underlying cell width as
	 * follows (rhs values are in pixels):
	 *
	 *       x1 = X / W *1024
	 *       y1 = Y / H *256
	 *       x2 = (X-1) / W *1024
	 *       y2 = (Y-1) / H *256
	 *
	 *       Where:  X is distance from the left side of the underlying cell
	 *               Y is distance from the top of the underlying cell
	 *               W is the width of the cell
	 *               H is the height of the cell
	 *
	 * Note:  the SDK incorrectly states that the height should be expressed as a
	 *        percentage of 1024.
	 *
	 * @param $col_start Col containing upper left corner of object
	 * @param $row_start Row containing top left corner of object
	 * @param $x1        Distance to left side of object
	 * @param $y1        Distance to top of object
	 * @param $width     Width of image frame
	 * @param $height    Height of image frame
	 */
  	function position_image( $col_start, $row_start, $x1, $y1, $width, $height )
    {
    	// Initialise end cell to the same as the start cell
    	$col_end = $col_start; // Col containing lower right corner of object
    	$row_end = $row_start; // Row containing bottom right corner of object

		// Zero the specified offset if greater than the cell dimensions
		if ( $x1 >= $this->size_col( $col_start ) )
			$x1 = 0;
      
    	if ( $y1 >= $this->size_row( $row_start ) )
			$y1 = 0;

		$width  = $width  + $x1 -1;
		$height = $height + $y1 -1;

		// Subtract the underlying cell widths to find the end cell of the image
		while ( $width >= $this->size_col( $col_end ) )
		{
        	$width -= $this->size_col( $col_end );
        	$col_end++;
        }

    	// Subtract the underlying cell heights to find the end cell of the image
    	while ( $height >= $this->size_row( $row_end ) )
		{
        	$height -= $this->size_row( $row_end );
        	$row_end++;
        }

		// Bitmap isn't allowed to start or finish in a hidden cell, i.e. a cell
		// with zero eight or width.
		if ( $this->size_col( $col_start ) == 0 )
        	return;
    
		if ( $this->size_col( $col_end ) == 0 )
        	return;
    
		if ( $this->size_row( $row_start ) == 0 )
        	return;
    
		if ( $this->size_row( $row_end ) == 0 )
        	return;

    	// Convert the pixel values to the percentage value expected by Excel
    	$x1 = $x1     / $this->size_col( $col_start ) * 1024;
    	$y1 = $y1     / $this->size_row( $row_start ) *  256;
    	$x2 = $width  / $this->size_col( $col_end )   * 1024; // Distance to right side of object
    	$y2 = $height / $this->size_row( $row_end )   *  256; // Distance to bottom of object

		$this->store_obj_picture( $col_start, $x1, $row_start, $y1, $col_end, $x2, $row_end, $y2 );
    }

	/**
	 * Convert the width of a cell from user's units to pixels. By interpolation
	 * the relationship is: y = 7x +5. If the width hasn't been set by the user we
	 * use the default value. If the col is hidden we use a value of zero.
	 */
  	function size_col( $col )
    {
    	// Look up the cell value to see if it has been changed
    	if ( isset( $this->col_sizes[$col] ) )
		{
        	if ( $this->col_sizes[$col] == 0 )
            	return false;
           	else
            	return (int)( 7 * $this->col_sizes[$col] + 5 );
        }
    	else
		{
        	return 64;
        }
    }

	/**
	 * Convert the height of a cell from user's units to pixels. By interpolation
	 * the relationship is: y = 4/3x. If the height hasn't been set by the user we
	 * use the default value. If the row is hidden we use a value of zero. (Not
	 * possible to hide row yet).
	 */
  	function size_row( $row )
    {
    	// Look up the cell value to see if it has been changed
    	if ( isset( $this->row_sizes[$row] ) )
		{
        	if ( $this->row_sizes[$row] == 0 )
            	return false;
        	else
            	return (int)( 4 / 3 * $this->row_sizes[$row] );
        }
    	else
		{
        	return 17;
        }
    }

	/**
	 * Store the OBJ record that precedes an IMDATA record. This could be generalise
	 * to support other Excel objects.
	 *
	 * @param $colL Col containing upper left corner of object
	 * @param $dxL  Distance from left side of cell
	 * @param $rwT  Row containing top left corner of object
	 * @param $dyT  Distance from top of cell
	 * @param $colR Col containing lower right corner of object
	 * @param $dxR  Distance from right of cell
	 * @param $rwB  Row containing bottom right corner of object
	 * @param $dyB  Distance from bottom of cell
	 */
  	function store_obj_picture( $colL, $dxL, $rwT, $dyT, $colR, $dxR, $rwB, $dyB )
    {
    	$record      = 0x005d; // Record identifier
    	$length      = 0x003c; // Bytes to follow

	    $cObj        = 0x0001; // Count of objects in file (set to 1)
   	 	$OT          = 0x0008; // Object type. 8 = Picture
    	$id          = 0x0001; // Object ID
    	$grbit       = 0x0614; // Option flags

    	$cbMacro     = 0x0000; // Length of FMLA structure
    	$Reserved1   = 0x0000; // Reserved
    	$Reserved2   = 0x0000; // Reserved

    	$icvBack     = 0x09;   // Background colour
    	$icvFore     = 0x09;   // Foreground colour
    	$fls         = 0x00;   // Fill pattern
    	$fAuto       = 0x00;   // Automatic fill
    	$icv         = 0x08;   // Line colour
    	$lns         = 0xff;   // Line style
    	$lnw         = 0x01;   // Line weight
    	$fAutoB      = 0x00;   // Automatic border
    	$frs         = 0x0000; // Frame style
    	$cf          = 0x0009; // Image format, 9 = bitmap
    	$Reserved3   = 0x0000; // Reserved
    	$cbPictFmla  = 0x0000; // Length of FMLA structure
    	$Reserved4   = 0x0000; // Reserved
    	$grbit2      = 0x0001; // Option flags
    	$Reserved5   = 0x0000; // Reserved

		$header  = pack( "vv", $record, $length );
		$data    = pack( "V",  $cObj );
		$data   .= pack( "v",  $OT );
		$data   .= pack( "v",  $id );
		$data   .= pack( "v",  $grbit );
		$data   .= pack( "v",  $colL );
		$data   .= pack( "v",  $dxL );
		$data   .= pack( "v",  $rwT );
		$data   .= pack( "v",  $dyT );
		$data   .= pack( "v",  $colR );
		$data   .= pack( "v",  $dxR );
		$data   .= pack( "v",  $rwB );
		$data   .= pack( "v",  $dyB );
		$data   .= pack( "v",  $cbMacro );
		$data   .= pack( "V",  $Reserved1 );
		$data   .= pack( "v",  $Reserved2 );
		$data   .= pack( "C",  $icvBack );
		$data   .= pack( "C",  $icvFore );
		$data   .= pack( "C",  $fls );
		$data   .= pack( "C",  $fAuto );
		$data   .= pack( "C",  $icv );
		$data   .= pack( "C",  $lns );
		$data   .= pack( "C",  $lnw );
		$data   .= pack( "C",  $fAutoB );
		$data   .= pack( "v",  $frs );
		$data   .= pack( "V",  $cf );
		$data   .= pack( "v",  $Reserved3 );
		$data   .= pack( "v",  $cbPictFmla );
		$data   .= pack( "v",  $Reserved4 );
		$data   .= pack( "v",  $grbit2 );
		$data   .= pack( "V",  $Reserved5 );

		$this->append( $header . $data );
    }

	/**
	 * Convert a 24 bit bitmap into the modified internal format used by Windows.
	 * This is described in BITMAPCOREHEADER and BITMAPCOREINFO structures in the
	 * MSDN library.
	 */
  	function process_bitmap( $bitmap )
    {
    	// Open file.
    	$bmp_fd = @fopen( $bitmap, "r" );
    
		if ( !$bmp_fd )
        	return PEAR::raiseError( "Couldn't import bitmap." );
            
    	// Slurp the file into a string.
    	$data = fread( $bmp_fd, filesize( $bitmap ) );

    	// Check that the file is big enough to be a bitmap.
    	if ( strlen( $data ) <= 0x36 )
        	return PEAR::raiseError( "Bitmap doesn't contain enough data." );
        
    	// The first 2 bytes are used to identify the bitmap.
    	$identity = unpack( "A2", $data );
    
		if ( $identity[''] != "BM" )
        	return PEAR::raiseError( "File doesn't appear to be a valid bitmap image." );

    	// Remove bitmap data: ID.
    	$data = substr( $data, 2 );

		// Read and remove the bitmap size. This is more reliable than reading
		// the data size at offset 0x22.
 		$size_array = unpack( "V", substr( $data, 0, 4 ) );
		$size   = $size_array[''];
		$data   = substr( $data, 4 );
		$size  -= 54; // Subtract size of bitmap header.
		$size  += 12; // Add size of BIFF header.

    	// Remove bitmap data: reserved, offset, header length.
    	$data = substr( $data, 12 );

    	// Read and remove the bitmap width and height. Verify the sizes.
    	$width_and_height = unpack( "V2", substr( $data, 0, 8 ) );
    	$width  = $width_and_height[1];
    	$height = $width_and_height[2];
    	$data   = substr( $data, 8 );
    
		if ( $width > 0xFFFF ) 
        	return PEAR::raiseError( "Largest image width supported is 65k." );
        
    	if ( $height > 0xFFFF ) 
        	return PEAR::raiseError( "Largest image height supported is 65k." );
        
    	// Read and remove the bitmap planes and bpp data. Verify them.
    	$planes_and_bitcount = unpack( "v2", substr( $data, 0, 4 ) );
		$data = substr( $data, 4 );
    
		// Bitcount
		if ( $planes_and_bitcount[2] != 24 )
        	return PEAR::raiseError( "Bitmap isn't a 24bit true color bitmap." );
        
    	if ( $planes_and_bitcount[1] != 1 )
        	return PEAR::raiseError( "Only 1 plane supported in bitmap image." );
        
    	// Read and remove the bitmap compression. Verify compression.
    	$compression = unpack( "V", substr( $data, 0, 4 ) );
    	$data = substr( $data, 4 );
		
    	// Remove bitmap data: data size, hres, vres, colours, imp. colours.
    	$data = substr( $data, 24 );

    	// Add the BITMAPCOREHEADER data
    	$header = pack( "Vvvvv", 0x000c, $width, $height, 0x01, 0x18 );
    	$data   = $header . $data;

    	return ( array( $width, $height, $size, $data ) );
    }

	/**
	 * Store the window zoom factor. This should be a reduced fraction but for
	 * simplicity we will store all fractions with a numerator of 100.
	 */
  	function store_zoom()
    {
    	// If scale is 100 we don't need to write a record
    	if ( $this->zoom == 100 )
        	return ( 0 );
        
    	$record = 0x00A0; // Record identifier
    	$length = 0x0004; // Bytes to follow

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "vv", $this->zoom, 100 );
    
		$this->append( $header . $data );
    }
	
	
	// private methods
	
	/**
	 * Returns an index to the XF record in the workbook
	 */
  	function _XF( $format = 0 )
    {
    	if ( $format != 0 )
			return ( $format->get_xf_index() );
		else
			return ( 0x0F );
	}
	
	/**
	 * Substitute an Excel cell reference in A1 notation for  zero based row and
	 * column values in an argument list.
	 *
	 * Ex: ("A4", "Hello") is converted to (3, 0, "Hello").
	 */
  	function _substitute_cellref( $cell )
    {
    	$cell = strtoupper( $cell );

    	// Convert a column range: 'A:A' or 'B:G'
    	if ( preg_match( "/([A-I]?[A-Z]):([A-I]?[A-Z])/", $cell, $match ) )
		{
        	list( $no_use, $col1 ) = $this->_cell_to_rowcol( $match[1] . '1' ); // Add a dummy row
        	list( $no_use, $col2 ) = $this->_cell_to_rowcol( $match[2] . '1' ); // Add a dummy row
        
			return ( array( $col1, $col2 ) );
        }

    	// Convert a cell range: 'A1:B7'
    	if ( preg_match( "/\$?([A-I]?[A-Z]\$?\d+):\$?([A-I]?[A-Z]\$?\d+)/", $cell, $match ) )
		{
        	list( $row1, $col1 ) = $this->_cell_to_rowcol( $match[1] );
        	list( $row2, $col2 ) = $this->_cell_to_rowcol( $match[2] );
        
			return ( array( $row1, $col1, $row2, $col2 ) );
        }

    	// Convert a cell reference: 'A1' or 'AD2000'
    	if ( preg_match( "/\$?([A-I]?[A-Z]\$?\d+)/", $cell ) )
		{
        	list( $row1, $col1 ) = $this->_cell_to_rowcol( $match[1] );
        	return( array( $row1, $col1 ) );
        }

		return PEAR::raiseError( "Unknown cell reference." );
	}
	
	/**
	 * Convert an Excel cell reference in A1 notation to a zero based row and column
	 * reference; converts C1 to (0, 2).
	 *
	 * Returns: row, column
	 */
  	function _cell_to_rowcol( $cell )
    {
		preg_match( "/\$?([A-I]?[A-Z])\$?(\d+)/", $cell, $match );
		$col = $match[1];
		$row = $match[2];

    	// Convert base26 column string to number
    	$chars = split( '', $col );
    	$expn  = 0;
    	$col   = 0;

    	while ( $chars )
		{
        	$char  = array_pop( $chars ); // LS char first
        	$col  += ( ord( $char ) - ord( 'A' ) + 1 ) * pow( 26, $expn );
        
			$expn++;
        }

    	// Convert 1-index to zero-index
    	$row--;
    	$col--;

    	return ( array( $row, $col ) );
    }
	
	/**
	 * Used to write http, ftp and mailto hyperlinks.
	 * The link type ($options) is 0x03 is the same as absolute dir ref without
	 * sheet. However it is differentiated by the $unknown2 data stream.
	 *
	 * See also write_url() above for a general description and return values.
	 *
	 * @param $row1   Start row
	 * @param $col1   Start column
	 * @param $row2   End row
	 * @param $col2   End column
	 * @param $url    URL string
	 * @param $str    Alternative label
	 * @param $format The cell format
	 */
  	function _write_url_web( $row1, $col1, $row2, $col2, $url, $str, $format = 0 )
    {
		$record = 0x01B8;	// Record identifier
		$length = 0x00000;	// Bytes to follow

		if ( $format == 0 )
        	$format = $this->_url_format;
        
    	// Write the visible label using the write_string() method.
    	if ( $str == '' )
        	$str = $url;
        
    	$str_error = $this->write_string( $row1, $col1, $str, $format );
    
		if ( $str_error == -2 )
        	return ( $str_error );

    	// Pack the undocumented parts of the hyperlink stream
    	$unknown1 = pack( "H*", "D0C9EA79F9BACE118C8200AA004BA90B02000000" );
    	$unknown2 = pack( "H*", "E0C9EA79F9BACE118C8200AA004BA90B" );

		// Pack the option flags
		$options = pack( "V", 0x03 );

		// Convert URL to a null terminated wchar string
		$url = join( "\0", preg_split( "''", $url, -1, PREG_SPLIT_NO_EMPTY ) );
		$url = $url . "\0\0\0";

    	// Pack the length of the URL
    	$url_len = pack( "V", strlen( $url ) );

    	// Calculate the data length
    	$length = 0x34 + strlen( $url );

    	// Pack the header data
    	$header = pack( "vv",   $record, $length );
    	$data   = pack( "vvvv", $row1, $row2, $col1, $col2 );

    	// Write the packed data
    	$this->_append( $header, $data, $unknown1, $options, $unknown2, $url_len, $url );
		return ( $str_error );
    }
	
	/**
	 * Used to write internal reference hyperlinks such as "Sheet1!A1".
	 *
	 * See also write_url() above for a general description and return values.
	 *
	 * @param $row1   Start row
	 * @param $col1   Start column
	 * @param $row2   End row
	 * @param $col2   End column
	 * @param $url    URL string
	 * @param $str    Alternative label
	 * @param $format The cell format
	 */
  	function _write_url_internal( $row1, $col1, $row2, $col2, $url, $str, $format = 0 )
    {
		$record = 0x01B8;	// Record identifier
		$length = 0x00000;	// Bytes to follow

		if ( $format == 0 )
        	$format = $this->_url_format;

    	// Strip URL type
    	$url = preg_replace( 's[^internal:]', '', $url );

    	// Write the visible label
    	if ( $str == '' )
        	$str = $url;
        
    	$str_error = $this->write_string( $row1, $col1, $str, $format );
    
		if ( $str_error == -2 )
        	return ( $str_error );

		// Pack the undocumented parts of the hyperlink stream
		$unknown1 = pack( "H*", "D0C9EA79F9BACE118C8200AA004BA90B02000000" );

		// Pack the option flags
		$options = pack( "V", 0x08 );

		// Convert the URL type and to a null terminated wchar string
		$url = join( "\0", preg_split( "''", $url, -1, PREG_SPLIT_NO_EMPTY ) );
		$url = $url . "\0\0\0";

		// Pack the length of the URL as chars (not wchars)
		$url_len = pack( "V", (int)( strlen( $url ) / 2 ) );

		// Calculate the data length
		$length = 0x24 + strlen( $url );

		// Pack the header data
		$header = pack( "vv",   $record, $length );
		$data   = pack( "vvvv", $row1, $row2, $col1, $col2 );

		$this->_append( $header, $data, $unknown1, $options, $url_len, $url );
		return ( $str_error );
    }
	
	/**
	 * Write links to external directory names such as 'c:\foo.xls',
	 * c:\foo.xls#Sheet1!A1', '../../foo.xls'. and '../../foo.xls#Sheet1!A1'.
	 *
	 * Note: Excel writes some relative links with the $dir_long string. We ignore
	 * these cases for the sake of simpler code.
	 *
	 * See also write_url() above for a general description and return values.
	 * @param $row1   Start row
	 * @param $col1   Start column
	 * @param $row2   End row
	 * @param $col2   End column
	 * @param $url    URL string
	 * @param $str    Alternative label
	 * @param $format The cell format
	 */
  	function _write_url_external( $row1, $col1, $row2, $col2, $url, $str, $format = 0 )
    {
    	// Network drives are different. We will handle them separately
    	// MS/Novell network drives and shares start with \\
    	if ( preg_match( '[^external:\\\\]', $url ) )
        	return ( $this->_write_url_external_net( $row1, $col1, $row2, $col2, $url, $str, $format ) );

		$record = 0x01B8;	// Record identifier
		$length = 0x00000;	// Bytes to follow

		if ( $format == 0 )
        	$format = $this->_url_format;

    	// Strip URL type and change Unix dir separator to Dos style (if needed)
		$url = preg_replace( '[^external:]', '', $url );
		$url = preg_replace( '[/]', "\\", $url );

    	// Write the visible label
    	if ( $str == '' )
        	$str = preg_replace( '[\#]', ' - ', $url );
        
    	$str_error = $this->write_string( $row1, $col1, $str, $format );
    
		if ( $str_error == -2 )
        	return ( $str_error );
        
    	// Determine if the link is relative or absolute:
    	//   relative if link contains no dir separator, "somefile.xls"
    	//   relative if link starts with up-dir, "..\..\somefile.xls"
    	//   otherwise, absolute
    	$absolute = 0x02; // Bit mask
    
		if ( !preg_match( '[\\]', $url ) )
        	$absolute = 0x00;
        
    	if ( preg_match( '[^\.\.\\]', $url ) )
        	$absolute = 0x00;

    	// Determine if the link contains a sheet reference and change some of the
    	// parameters accordingly.
    	// Split the dir name and sheet name (if it exists)
   	 	list( $dir_long , $sheet ) = split( '/\#/', $url );
    	$link_type = 0x01 | $absolute;

		if ( isset( $sheet ) )
		{
        	$link_type |= 0x08;
        	$sheet_len  = pack( "V",  strlen( $sheet ) + 0x01 );
        	$sheet      = join( "\0", split( '', $sheet ) );
        	$sheet     .= "\0\0\0";
        }
    	else
		{
        	$sheet_len  = '';
        	$sheet      = '';
        }

		// Pack the link type
		$link_type = pack( "V", $link_type );

		// Calculate the up-level dir count e.g.. (..\..\..\ == 3)
		$up_count = preg_match_all( "/\.\.\\/", $dir_long, $useless );
		$up_count = pack( "v", $up_count );

    	// Store the short dos dir name (null terminated)
    	$dir_short = preg_replace( '/\.\.\\/', '', $dir_long ) . "\0";

    	// Store the long dir name as a wchar string (non-null terminated)
    	$dir_long = join( "\0", split( '', $dir_long ) );
    	$dir_long = $dir_long . "\0";

    	// Pack the lengths of the dir strings
    	$dir_short_len = pack( "V", strlen( $dir_short ) );
    	$dir_long_len  = pack( "V", strlen( $dir_long  ) );
    	$stream_len    = pack( "V", strlen( $dir_long  ) + 0x06 );

		// Pack the undocumented parts of the hyperlink stream
		$unknown1 = pack( "H*", 'D0C9EA79F9BACE118C8200AA004BA90B02000000' );
		$unknown2 = pack( "H*", '0303000000000000C000000000000046' );
		$unknown3 = pack( "H*", 'FFFFADDE000000000000000000000000000000000000000' );
		$unknown4 = pack( "v",  0x03 );

		// Pack the main data stream
		$data = pack( "vvvv", $row1, $row2, $col1, $col2 ) .
			$unknown1      .
			$link_type     .
			$unknown2      .
			$up_count      .
			$dir_short_len .
			$dir_short     .
			$unknown3      .
			$stream_len    .
			$dir_long_len  .
			$unknown4      .
			$dir_long      .
			$sheet_len     .
			$sheet;

		// Pack the header data
    	$length = strlen( $data );
    	$header = pack( "vv", $record, $length );

    	// Write the packed data
    	$this->_append( $header, $data);
    	return ( $str_error );
    }
	
	/**
	 * Store the page setup SETUP BIFF record.
	 */
  	function _store_setup()
    {
    	$record       = 0x00A1;					// Record identifier
    	$length       = 0x0022;					// Number of bytes to follow

    	$iPaperSize   = $this->_paper_size;		// Paper size
    	$iScale       = $this->print_scale;		// Print scaling factor
    	$iPageStart   = 0x01;					// Starting page number
    	$iFitWidth    = $this->fit_width;		// Fit to number of pages wide
    	$iFitHeight   = $this->fit_height;		// Fit to number of pages high
    	$grbit        = 0x00;					// Option flags
    	$iRes         = 0x0258;					// Print resolution
    	$iVRes        = 0x0258;					// Vertical print resolution
    	$numHdr       = $this->_margin_head;	// Header Margin
    	$numFtr       = $this->_margin_foot;	// Footer Margin
    	$iCopies      = 0x01;					// Number of copies

    	$fLeftToRight = 0x0;					// Print over then down
    	$fLandscape   = $this->_orientation;	// Page orientation
    	$fNoPls       = 0x0;					// Setup not read from printer
    	$fNoColor     = 0x0;					// Print black and white
    	$fDraft       = 0x0;					// Print draft quality
    	$fNotes       = 0x0;					// Print notes
    	$fNoOrient    = 0x0;					// Orientation not set
    	$fUsePage     = 0x0;					// Use custom starting page

    	$grbit  = $fLeftToRight;
    	$grbit |= $fLandscape    << 1;
    	$grbit |= $fNoPls        << 2;
    	$grbit |= $fNoColor      << 3;
    	$grbit |= $fDraft        << 4;
    	$grbit |= $fNotes        << 5;
    	$grbit |= $fNoOrient     << 6;
    	$grbit |= $fUsePage      << 7;

    	$numHdr = pack( "d", $numHdr );
    	$numFtr = pack( "d", $numFtr );
    
		if ( $this->_byte_order ) // if it's Big Endian
        {
        	$numHdr = strrev( $numHdr );
        	$numFtr = strrev( $numFtr );
        }

    	$header = pack( "vv", $record, $length );
    	$data1  = pack( "vvvvvvvv", $iPaperSize, $iScale, $iPageStart, $iFitWidth, $iFitHeight, $grbit, $iRes, $iVRes );
		
    	$data2  = $numHdr .$numFtr;
    	$data3  = pack( "v", $iCopies );
		
    	$this->prepend( $header . $data1 . $data2 . $data3 );
    }
	
	/**
	 * Store the LEFTMARGIN BIFF record.
	 */
  	function _store_margin_left()
    {
    	$record = 0x0026; // Record identifier
    	$length = 0x0008; // Bytes to follow

    	$margin = $this->_margin_left; // Margin in inches

    	$header = pack("vv",  $record, $length);
    	$data   = pack("d",   $margin);
    
		if ( $this->_byte_order ) // if it's Big Endian
        	$data = strrev( $data );

    	$this->append( $header . $data );
	}

	/**
	 * Store the RIGHTMARGIN BIFF record.
	 */
	function _store_margin_right()
    {
    	$record = 0x0027; // Record identifier
    	$length = 0x0008; // Bytes to follow

    	$margin = $this->_margin_right; // Margin in inches

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "d",  $margin );
    
		if ( $this->_byte_order ) // if it's Big Endian
        	$data = strrev( $data );

    	$this->append( $header . $data );
    }

	/**
	 * Store the TOPMARGIN BIFF record.
	 */
  	function _store_margin_top()
    {
    	$record = 0x0028; // Record identifier
    	$length = 0x0008; // Bytes to follow

    	$margin = $this->_margin_top; // Margin in inches

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "d",  $margin);
    
		if ($this->_byte_order) // if it's Big Endian
			$data = strrev( $data );

    	$this->append( $header . $data );
    }

	/**
	 * Store the BOTTOMMARGIN BIFF record.
	 */
  	function _store_margin_bottom()
    {
    	$record = 0x0029; // Record identifier
    	$length = 0x0008; // Bytes to follow

    	$margin = $this->_margin_bottom; // Margin in inches

    	$header = pack( "vv", $record, $length );
    	$data   = pack( "d",  $margin );
    
		if ( $this->_byte_order ) // if it's Big Endian
			$data = strrev( $data );

    	$this->append( $header . $data );
    }
} // END OF XLSWorksheet

?>
