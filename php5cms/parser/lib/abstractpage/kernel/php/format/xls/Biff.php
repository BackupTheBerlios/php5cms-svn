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


define( 'BIFF_FONT_0',                  0       );
define( 'BIFF_FONT_1',                  0x40    ); 
define( 'BIFF_FONT_2',                  0x80    ); 
define( 'BIFF_FONT_3',                  0xC0    );
define( 'BIFF_ALIGN_GENERAL',           0x0     ); 
define( 'BIFF_ALIGN_LEFT',              0x1     ); 
define( 'BIFF_ALIGN_CENTER',            0x2     ); 
define( 'BIFF_ALIGN_RIGHT',             0x3     );
define( 'BIFF_CELL_FILL',               0x4     ); 
define( 'BIFF_CELL_LEFT_BORDER',        0x8     ); 
define( 'BIFF_CELL_RIGHT_BORDER',       0x10    ); 
define( 'BIFF_CELL_TOP_BORDER',         0x20    ); 
define( 'BIFF_CELL_BOTTOM_BORDER',      0x40    ); 
define( 'BIFF_CELL_BOX_BORDER',         0x78    );
define( 'BIFF_CELL_SHADED',             0x80    );
define( 'BIFF_FONT_NORMAL',             0x0     ); 
define( 'BIFF_FONT_BOLD',               0x1     ); 
define( 'BIFF_FONT_ITALIC',             0x2     );
define( 'BIFF_FONT_UNDERLINE',          0x4     ); 
define( 'BIFF_FONT_STRIKEOUT',          0x8     );
define( 'BIFF_CELL_LOCKED',             0x40    ); 
define( 'BIFF_CELL_HIDDEN',             0x80    );
define( 'BIFF_XLS_DATE',                2415033 );
define( 'BIFF_ID_BACKUP_REC',           64      ); 
define( 'BIFF_LEN_BACKUP_REC',          2       );
define( 'BIFF_ID_BOF_REC',              9       ); 
define( 'BIFF_LEN_BOF_REC',             4       ); 
define( 'BIFF_VERSION',                 7       ); 
define( 'BIFF_TYPE',                    0x10    );
define( 'BIFF_ID_CELL_NUMBER',          3       ); 
define( 'BIFF_LEN_CELL_NUMBER',         0xF     );
define( 'BIFF_ID_CELL_TEXT',            4       ); 
define( 'BIFF_LEN_CELL_TEXT',           8       );
define( 'BIFF_ID_COL_WIDTH',            36      ); 
define( 'BIFF_LEN_COL_WIDTH',           4       );
define( 'BIFF_ID_DEFROWHEIGHT',         37      ); 
define( 'BIFF_LEN_DEFROWHEIGHT',        2       );
define( 'BIFF_ID_EOF_REC',              0xA     );
define( 'BIFF_ID_FONT_REC',             49      ); 
define( 'BIFF_LEN_FONT_REC',            5       );
define( 'BIFF_ID_FOOTER_REC',           21      ); 
define( 'BIFF_LEN_FOOTER_REC',          1       );
define( 'BIFF_ID_FORMAT_COUNT',         0x1F    ); 
define( 'BIFF_LEN_FORMAT_COUNT',        2       );
define( 'BIFF_ID_FORMAT_REC',           30      ); 
define( 'BIFF_LEN_FORMAT_REC',          1       );
define( 'BIFF_ID_HEADER_REC',           20      ); 
define( 'BIFF_LEN_HEADER_REC',          1       );
define( 'BIFF_ID_HPAGEBREAKS',          27      ); 
define( 'BIFF_LEN_HPAGEBREAKS',         2       );
define( 'BIFF_ID_IS_PASSWORD_REC',      19      ); 
define( 'BIFF_LEN_PASSWORD_REC',        2       );
define( 'BIFF_ID_IS_PROTECT_REC',       18      );
define( 'BIFF_ID_LEFT_MARGIN_REC',      38      ); 
define( 'BIFF_ID_RIGHT_MARGIN_REC',     39      ); 
define( 'BIFF_ID_NOTE_REC',             28      ); 
define( 'BIFF_LEN_NOTE',                6       );
define( 'BIFF_ID_PANE_REC',             0x41    ); 
define( 'BIFF_LEN_PANE_REC',            10      );
define( 'BIFF_ID_PRINTGRIDLINES_REC',   43      ); 
define( 'BIFF_LEN_PRINTGRIDLINES_REC',  2       );
define( 'BIFF_ID_PRINTROWHEADERS_REC',  42      ); 
define( 'BIFF_LEN_PRINTROWHEADERS_REC', 2       );
define( 'BIFF_ID_ROW_REC',              0x8     ); 
define( 'BIFF_LEN_ROW_REC',             13      );
define( 'BIFF_ID_SELECTION_REC',        0x1D    ); 
define( 'BIFF_LEN_SELECTION_REC',       0xF     );
define( 'BIFF_ID_TOP_MARGIN_REC',       40      ); 
define( 'BIFF_ID_BOTTOM_MARGIN_REC',    41      ); 
define( 'BIFF_LEN_MARGIN_REC',          8       );
define( 'BIFF_ID_VPAGEBREAKS',          26      ); 
define( 'BIFF_LEN_VPAGEBREAKS',         2       );
define( 'BIFF_ID_WINDOW1_REC',          0x3D    ); 
define( 'BIFF_LEN_WINDOW1_REC',         0xA     );
define( 'BIFF_ID_WINDOW2_REC',          0x3E    ); 
define( 'BIFF_LEN_WINDOW2_REC',         0xE     );
define( 'BIFF_ID_XF_REC',               0x43    ); 
define( 'BIFF_LEN_XF_REC',              4       );
define( 'BIFF_ID_CODEPAGE',             0x42    ); 
define( 'BIFF_LEN_CODEPAGE',            2       );
define( 'BIFF_MAX_ROWS',                16387   ); 
define( 'BIFF_MAX_COLS',                255     );
define( 'BIFF_MAX_NOTE_CHARS',          2048    );
define( 'BIFF_MAX_TEXT_CHARS',          256     );
define( 'BIFF_MAX_FONTS',               4       );
define( 'BIFF_DEF_ROW_HEIGHT',          12.75   );
define( 'BIFF_DEF_COL_WIDTH',           8.43    );


/**
 * @package format_xls
 */

class Biff extends PEAR
{
	/**
	 * @access public
	 */
	var $picture = array( '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );
	
	/**
	 * @access public
	 */
	var $eof = array( 38, 82, 38, 56, 80, 72, 80, 50, 69, 88, 67, 69, 76, 32, 50, 46, 48, 10, 40, 169, 41, 32, 119, 119, 119, 46, 119, 101, 98, 45, 97, 119, 97, 114, 101, 46, 99, 111, 109, 10, 85, 110, 108, 105, 99, 101, 110, 115, 101, 100, 32, 118, 101, 114, 115, 105, 111, 110 );
	
	/**
	 * @access public
	 */
	var $big_endian = false;
	
	/**
	 * @access public
	 */
	var $err_level = 1;
	
	/**
	 * @access public
	 */
	var $fonts = 0;
	
	/**
	 * @access public
	 */
	var $hpagebreaks = array();
	
	/**
	 * @access public
	 */
	var $maxcolwidth = array();
	
	/**
	 * @access public
	 */
	var $outfile = 'sample.xls';
	
	/**
	 * @access public
	 */
	var $pane_col = 0;
	
	/**
	 * @access public
	 */
	var $pane_row = 0;

	/**
	 * @access public
	 */
	var $stream = array();

	/**
	 * @access public
	 */
	var $vpagebreaks = array();
	
	/**
	 * @access public
	 */
	var $a_not = array();
	
	/**
	 * @access public
	 */
	var $win_disp_col = 0;
	
	/**
	 * @access public
	 */
	var $win_disp_row = 0;
	
	/**
	 * @access public
	 */
	var $win_formula = false;
	
	/**
	 * @access public
	 */
	var $win_freeze = false;
	
	/**
	 * @access public
	 */
	var $win_grid = true; 
	
	/**
	 * @access public
	 */
	var $win_hidden = true;
	
	/**
	 * @access public
	 */
	var $win_ref = true;
	
	/**
	 * @access public
	 */
	var $win_width = 800;
	
	/**
	 * @access public
	 */
	var $win_zero = true;
	
	/**
	 * @access public
	 */
	var $xf_count = -1;
	
	/**
	 * @access public
	 */
	var	$pane_act = 3;
	
	/**
	 * @access public
	 */
	var	$win_height = 600;
	
	/**
	 * @access public
	 */
	var $parse_order = array (
		'BIFF_ID_BOF_REC'			  => 9, 
		'BIFF_ID_CODEPAGE'			  => 0x42,
		'BIFF_ID_BACKUP_REC'		  => 64,
		'BIFF_ID_PRINTROWHEADERS_REC' => 42,
		'BIFF_ID_PRINTGRIDLINES_REC'  => 43,
		'BIFF_ID_HPAGEBREAKS'		  => 27,
		'BIFF_ID_VPAGEBREAKS'		  => 26,
		'BIFF_ID_DEFROWHEIGHT'		  => 37,
		'BIFF_ID_FONT_REC'			  => 49, 
		'BIFF_ID_HEADER_REC'		  => 20, 
		'BIFF_ID_FOOTER_REC'		  => 21,
		'BIFF_ID_LEFT_MARGIN_REC'	  => 38,
		'BIFF_ID_RIGHT_MARGIN_REC'	  => 39,
		'BIFF_ID_TOP_MARGIN_REC'	  => 40,
		'BIFF_ID_BOTTOM_MARGIN_REC'	  => 41,
		'BIFF_ID_XF_REC'			  => 0x43,
		'BIFF_ID_COL_WIDTH'			  => 36, 
		'BIFF_ID_FORMAT_COUNT'		  => 0x1F, 
		'BIFF_ID_FORMAT_REC'		  => 30, 
		'BIFF_ID_ROW_REC'			  => 8,
		'BIFF_ID_CELL_TEXT'			  => 4, 
		'BIFF_ID_CELL_NUMBER'		  => 3, 
		'BIFF_ID_IS_PROTECT_REC'      => 18, 
		'BIFF_ID_IS_PASSWORD_REC'	  => 19,
		'BIFF_ID_NOTE_REC'			  => 28,
		'BIFF_ID_WINDOW1_REC'		  => 0x3D,
		'BIFF_ID_WINDOW2_REC'		  => 0x3E,
		'BIFF_ID_PANE_REC'			  => 0x41,
		'BIFF_ID_SELECTION_REC'		  => 0x1D,
		'BIFF_ID_EOF_REC'			  => 0xA
	);
	

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Biff() 
	{
		$this->bof();

		$num = 1.23456789; // IEEE 64-bit 3F F3 C0 CA 42 83 DE 1B 
		$little_endian = pack( 'C8', 0x1B, 0xDE, 0x83, 0x42, 0XCA, 0xC0, 0xF3, 0X3F );
		$result = pack( 'd', $num );
		
		if ( $result === $little_endian )
			$this->big_endian = false;
		else
			$this->big_endian = true;
			
		// create an array holding AA..AZ notation
		$this->_fillAANotation();
	}


	/**
	 * @access public
	 */
	function xlsSetRow( $row, $height )
	{
		$col_start = 0; 
		$col_end   = 256;
		$res       = 0x0;
		
		$this->stream[] = BIFF_ID_ROW_REC;
		$this->stream[] = pack( 'vvvvvvvCCC', BIFF_ID_ROW_REC, BIFF_LEN_ROW_REC, $row, $col_start, $col_end, $height * 20, 0, 0, 0, 0 );	
	}

	/**
	 * @access public
	 */		
	function xlsWindow( $grid, $ref, $zero )
	{
		$this->win_grid = $grid;
		$this->win_ref  = $ref;
		$this->win_zero = $zero;
	}

	/**
	 * @access public
	 */
	function xlsFreeze( $row = 0, $col = 0 )
	{
		$this->pane_row   = $row;
		$this->pane_col   = $col;
		$this->win_freeze = true;
	}

	/**
	 * @access public
	 */
	function xlsSetDefRowHeight( $value )
	{
		$this->def_row_height = $value;
		
		$this->stream[] = BIFF_ID_DEFROWHEIGHT;
		$this->stream[] = pack( 'vvv', BIFF_ID_DEFROWHEIGHT, BIFF_LEN_DEFROWHEIGHT, $value * 20 );
	}

	/**
	 * @access public
	 */
	function xlsCellNote( $row, $col, $value ) 
	{  
		$this->checkBounds( $row, $col, 'line ' . __line__ . ' xlsCellNotes' );
		
		if ( strlen( $value ) > BIFF_MAX_NOTE_CHARS ) 
			return PEAR::raiseError( $ref . BIFF_MAX_NOTE_CHARS . " chars max.", null, PEAR_ERROR_TRIGGER );
      
		$len = strlen( $value );
		
		$this->stream[] = BIFF_ID_NOTE_REC; 
		$this->stream[] = pack( 'vvvvv', BIFF_ID_NOTE_REC, BIFF_LEN_NOTE + $len, $row, $col, $len ) . $value;
	}

	/**
	 * @access public
	 */	
	function xlsAddHPageBreak( $row ) 
	{
		if ( $row < 0 || $row > BIFF_MAX_ROWS || !is_int( $row ) )
			return PEAR::raiseError( "Row must be a positive integer from 0 to " . BIFF_MAX_ROWS, null, PEAR_ERROR_TRIGGER );        
      
		$this->hpagebreaks[] = $row;
	}

	/**
	 * @access public
	 */	
	function xlsAddVPageBreak( $col ) 
   	{
		if ( is_string( $col ) )
			$col = (int)$this->_cnvAAToCol( $col );

		if ( $col < 0 or $col > BIFF_MAX_COLS )
			return PEAR::raiseError( "Column must be a positive integer from 0 to " . BIFF_MAX_COLS, null, PEAR_ERROR_TRIGGER );

		$this->vpagebreaks[] = $col;
	}
   
	/**
	 * @access public
	 */
	function assemblePageBreaks() 
	{
		$h = null;
		$cnt_hpagebreaks = count( $this->hpagebreaks );
		
		if ( $cnt_hpagebreaks > 0 )
		{
			sort( $this->hpagebreaks );
			
			foreach( $this->hpagebreaks as $x )
				$h .= pack( 'v', $x );
			
			$this->stream[] = BIFF_ID_HPAGEBREAKS;
			$this->stream[] = pack( 'vvv', BIFF_ID_HPAGEBREAKS, BIFF_LEN_HPAGEBREAKS + ( $cnt_hpagebreaks * 2 ) , $cnt_hpagebreaks ) . $h;
		}
		
		$cnt_vpagebreaks = count( $this->vpagebreaks );
		$v = null;
		
		if ( $cnt_vpagebreaks > 0 )
		{
			sort( $this->vpagebreaks );
			
			foreach( $this->vpagebreaks as $x )
				$v .= pack( 'v', $x );
			
			$this->stream[] = BIFF_ID_VPAGEBREAKS;
			$this->stream[] = pack( 'vvv', BIFF_ID_VPAGEBREAKS, BIFF_LEN_VPAGEBREAKS + ( $cnt_vpagebreaks * 2 ), $cnt_vpagebreaks ) . $v;
		}
	}

	/**
	 * @access public
	 */
	function setPane()
	{
		$h_split = false;
		$v_split = false;
		$hpos    = 0;
		$vpos    = 0;
		
		if ( $this->pane_row > 0 || $this->pane_col > 0 )
		{
			if ( $this->pane_row > 0 )
			{
			    $hpos    = $this->pane_row;
				$h_split = true;
			}
			
			if ( $this->pane_col > 0 )
			{
				$vpos    = $this->pane_col;
				$v_split = true;
			}
			
			$this->selection( 3, $this->pane_row, $this->pane_col );
			
			if ( $h_split )
			{
				$this->selection( 2, $this->pane_row, $this->pane_col );
				$this->pane_act = 2;
			}
			
			if ( $v_split )
			{
				$this->selection( 1, $this->pane_row, $this->pane_col );
				$this->pane_act = 1;
			}
			
			if ( $h_split && $v_split )
			{
				$this->selection( 0, $this->pane_row, $this->pane_col );
				$this->pane_act = 0;
			}
			
			$this->stream[] = BIFF_ID_PANE_REC;
			$this->stream[] = pack( 'vvvvvvv', BIFF_ID_PANE_REC, BIFF_LEN_PANE_REC, $vpos, $hpos, $this->pane_row, $this->pane_col, $this->pane_act );
		}
	}
	
	/**
	 * @access public
	 */
	function xlsAddFormat( $picstring ) 
	{
		$this->picture[] = $picstring;
		return( count( $this->picture ) -1 );
	}

	/**
	 * @access public
	 */
	function xlsPrintMargins( $left = .5, $right = .5, $top = .5, $bottom = .5 ) 
	{
		$left = pack( 'd', $left );
		
		if ( $this->big_endian ) 
			$left = strrev( $left );
		
		$right = pack( 'd', $right );
		
		if ( $this->big_endian )
			$right = strrev( $right );
		
		$top = pack( 'd', $top );
		
		if ( $this->big_endian )
			$top = strrev( $top );
		
		$bottom = pack( 'd', $bottom );
		
		if ( $this->big_endian )
			$bottom = strrev( $bottom );
		
		$this->stream[] = BIFF_ID_LEFT_MARGIN_REC;
		$this->stream[] = pack( 'vv', BIFF_ID_LEFT_MARGIN_REC,   BIFF_LEN_MARGIN_REC ) . $left;
		$this->stream[] = BIFF_ID_RIGHT_MARGIN_REC;
		$this->stream[] = pack( 'vv', BIFF_ID_RIGHT_MARGIN_REC,  BIFF_LEN_MARGIN_REC ) . $right;
		$this->stream[] = BIFF_ID_TOP_MARGIN_REC;
		$this->stream[] = pack( 'vv', BIFF_ID_TOP_MARGIN_REC,    BIFF_LEN_MARGIN_REC ) . $top;
		$this->stream[] = BIFF_ID_BOTTOM_MARGIN_REC;
		$this->stream[] = pack( 'vv', BIFF_ID_BOTTOM_MARGIN_REC, BIFF_LEN_MARGIN_REC ) . $bottom;
	}

	/**
	 * @access public
	 */
	function xlsFooter( $foot ) 
	{
		$this->stream[] = BIFF_ID_FOOTER_REC;
	  
	  	foreach ( $this->eof as $a )
			$foot .= pack( 'C', $a );		 
		
		$len = strlen( $foot );
		$this->stream[] = pack( 'vvC', BIFF_ID_FOOTER_REC, BIFF_LEN_FOOTER_REC + $len, $len ) . $foot;
	}

	/**
	 * @access public
	 */
	function xlsHeader( $head ) 
	{
		$this->stream[] = BIFF_ID_HEADER_REC;
		$len = strlen( $head );
		$this->stream[] = pack( 'vvC', BIFF_ID_HEADER_REC, BIFF_LEN_HEADER_REC + $len, $len ) . $head;
	}

	/**
	 * @access public
	 */
	function xlsSetPrintGridLines() 
	{
		$this->stream[] = BIFF_ID_PRINTGRIDLINES_REC;
		$this->stream[] = pack( 'vvv', BIFF_ID_PRINTGRIDLINES_REC, BIFF_LEN_PRINTGRIDLINES_REC, 1 );
	}

	/**
	 * @access public
	 */
	function xlsSetPrintHeaders() 
	{
		$this->stream[] = BIFF_ID_PRINTROWHEADERS_REC;
		$this->stream[] = pack( 'vvv', BIFF_ID_PRINTROWHEADERS_REC, BIFF_LEN_PRINTROWHEADERS_REC, 1 );
	}

	/**
	 * @access public
	 */
	function xlsSetBackup() 
	{
		$this->stream[] = BIFF_ID_BACKUP_REC;
		$this->stream[] = pack( 'vvv', BIFF_ID_BACKUP_REC, BIFF_LEN_BACKUP_REC, 1 );
	}

	/**
	 * @access public
	 */
	function xlsProtectSheet( $fpass = '', $fprot = true ) 
	{
		if ( !empty( $fpass ) )
		{
			$pw = $this->_encodePassword( $fpass );
			
			$this->stream[] = BIFF_ID_IS_PASSWORD_REC;
			$this->stream[] = pack( 'vvv', BIFF_ID_IS_PASSWORD_REC, BIFF_LEN_PASSWORD_REC, $pw );
		} 
		
		if ( $fprot )
		{
			$this->stream[] = BIFF_ID_IS_PROTECT_REC;
			$this->stream[] = pack( 'vvv', BIFF_ID_IS_PROTECT_REC, 0x2, 1 );
		}
	}

	/**
	 * @access public
	 */
	function xlsSetDefFonts() 
	{
		$this->xlsSetFont( 'Arial',           10, BIFF_FONT_NORMAL );
		$this->xlsSetFont( 'Courier New',     10, BIFF_FONT_NORMAL );
		$this->xlsSetFont( 'Times New Roman', 10, BIFF_FONT_NORMAL );
		$this->xlsSetFont( 'System',          10, BIFF_FONT_NORMAL );
	}

	/**
	 * @access public
	 */
	function xlsSetColWidth( $col_start, $col_end, $width ) 
	{
		if ( is_string( $col_start ) )
			$col_start = (int)$this->_cnvAAToCol( $col_start );		    

		if ( is_string( $col_end ) )
			$col_end = (int)$this->_cnvAAToCol( $col_end );		    
		
		if ( !is_int( $col_start ) | !is_int( $col_end ) )
			return PEAR::raiseError( "First and second parameter must be positve integers.", null, PEAR_ERROR_TRIGGER );
      
		if ( $col_start < 0 || $col_end < 0 )
			return PEAR::raiseError( "Columns must be positive integers.", null, PEAR_ERROR_TRIGGER );        
      
		if ( $col_start > BIFF_MAX_COLS || $col_end > BIFF_MAX_COLS )
			return PEAR::raiseError( BIFF_MAX_COLS . " cols max.", null, PEAR_ERROR_TRIGGER );
      
		if ( !is_int( $width ) || $width > 255 || $width < 0 )
			return PEAR::raiseError( "Width must be an integer in the range of 0-255.", null, PEAR_ERROR_TRIGGER );
      
		for ( $x = $col_start; $x <= $col_end; $x++ )
			$this->maxcolwidth[$x] = $width;
	}

	/**
	 * @access public
	 */	
	function setColWidth( $firstrow, $lastrow, $width ) 
	{
		$this->stream[] = BIFF_ID_COL_WIDTH;
		$this->stream[] = pack( 'vvCCv', BIFF_ID_COL_WIDTH, BIFF_LEN_COL_WIDTH, $firstrow, $lastrow, ( $width * 256 + 182 ) );
	}

	/**
	 * @access public
	 */
	function bof() 
	{
		$this->stream[] = BIFF_ID_BOF_REC;
		$this->stream[] = pack( 'vvvv', BIFF_ID_BOF_REC, BIFF_LEN_BOF_REC, BIFF_VERSION, BIFF_TYPE );
	} 

	/**
	 * @access public
	 */
	function eof() 
	{
		$this->stream[] = BIFF_ID_EOF_REC;
		$this->stream[] = pack( 'v', BIFF_ID_EOF_REC );
	}

	/**
	 * @access public
	 */	
	/*
	function getParsed( $file = '' )
	{	
		$file = $this->xlsParse( $file );
		return( $file );
	}
	*/

	/**
	 * @access public
	 */
	function xlsParse( $fname = '' ) 
	{
		$fstorage = !empty( $fname );
		
		foreach( $this->maxcolwidth as $key => $value )
			$this->SetcolWidth( $key, $key, $value );
		
		if ( $this->fonts = 0 )
			$this->xlsSetFont( 'Arial', 10, $font_format = BIFF_FONT_NORMAL );
		
		$this->setCodePage();
		$this->eof();
		$this->setDefFormat();
		$this->assemblePageBreaks();
		$this->setPane();
		$this->setWindow();
		
		if ( $fstorage )
		{
			$fp = fopen( $fname, "wb" );
		}
		else
		{
			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" ); 
			header( "Cache-Control: no-store, no-cache, must-revalidate" );
			header( "Cache-Control: post-check=0, pre-check=0", false );
			header( "Pragma: no-cache" );
			header( "Content-Disposition: attachment; filename=$this->outfile" ); 
			header( "Content-Type: application/octet-stream" );
		}
		
		$len1 = count( $this->parse_order );
		$len2 = count( $this->stream );
		
		for ( $x = 0 ; $x < $len1; $x++ )
		{
			$code = array_shift( $this->parse_order );
			
			if ( in_array( $code, $this->stream, true ) )
			{
				for ( $y = 0; $y < $len2; $y++ )
				{
					if ( $code === $this->stream[$y] )
					{
						if ( $fstorage )
							fwrite( $fp, $this->stream[$y + 1], strlen( $this->stream[$y + 1] ) );
						else
							print $this->stream[$y + 1];
					}
				}
			}
		}
		
		if ( $fstorage )
			fclose( $fp );
		
		return( $fname );
	}

	/**
	 * @access public
	 */
	function xlsWriteText( $row, $col, $value, $col_width = 0, $cell_picture = 0, $cell_font = 0, $cell_alignment = BIFF_ALIGN_GENERAL, $cell_status = 0 ) 
	{
		$this->checkBounds( $row, $col, 'line ' . __line__ . ' xlsWriteText' );
      
	  	if ( !is_string( $value ) ) 
			return PEAR::raiseError( "Third parameter must be a string.", null, PEAR_ERROR_TRIGGER );
      
      	if ( strlen( $value ) > BIFF_MAX_TEXT_CHARS ) 
			return PEAR::raiseError( $ref . BIFF_MAX_NOTE_CHARS . " chars max.", null, PEAR_ERROR_TRIGGER );
      
		$len = strlen( $value );
		$this->_adjustColWidth( $col, $col_width, $len );
		$this->stream[] = BIFF_ID_CELL_TEXT; 
		$this->stream[] = pack( 'vvvvCCCC', BIFF_ID_CELL_TEXT, BIFF_LEN_CELL_TEXT + $len, $row, $col, $cell_status, $cell_picture + $cell_font, $cell_alignment, $len ). $value;
	}

	/**
	 * @access public
	 */	
	function xlsWriteDateTime( $row, $col, $value, $col_width = 0, $cell_picture = 0, $cell_font = 0, $cell_alignment = BIFF_ALIGN_RIGHT, $cell_status = 0 ) 
	{
		$this->checkBounds( $row, $col, 'line ' . __line__ . ' xlsWriteNumber' );
		
		if ( !is_string( $value ) )
			return PEAR::raiseError( "Third parameter must be a string.", null, PEAR_ERROR_TRIGGER );
		
		$value = $this->xlsDate( substr( $value, 4, 2 ), substr( $value, 6, 2 ), substr( $value, 0, 4 ) ) + ( substr( $value, 8, 2 ) / 24 ) + ( substr( $value, 10, 2 ) / 1440 ) + ( substr( $value, 12, 2 ) / 86400 );
		$len   = strlen( strval( $value ) );
		$this->_adjustColWidth( $col, $col_width, $len );
		$x = pack( 'd', $value );
		
		if ( $this->big_endian )
			$x = strrev( $x );
		
		$this->stream[] = BIFF_ID_CELL_NUMBER;
		$this->stream[] = pack( 'vvvvCCC', BIFF_ID_CELL_NUMBER, BIFF_LEN_CELL_NUMBER, $row, $col, $cell_status, $cell_picture + $cell_font, $cell_alignment ) . $x;
	}

	/**
	 * @access public
	 */	
	function xlsWriteNumber( $row, $col, $value, $col_width = 0, $cell_picture = 0, $cell_font = 0, $cell_alignment = BIFF_ALIGN_RIGHT, $cell_status = 0 ) 
	{
		$this->checkBounds( $row, $col, 'line ' . __line__ . ' xlsWriteNumber' );
		
		if ( !is_int( $value ) & !is_float( $value ) )
			return PEAR::raiseError( "Third parameter must be either int or float.", null, PEAR_ERROR_TRIGGER );
      
		$len = strlen( strval( $value ) );
		$this->_adjustColWidth( $col, $col_width, $len );
		$x = pack( 'd', $value );
		
		if ( $this->big_endian )
			$x = strrev( $x );
		
		$this->stream[] = BIFF_ID_CELL_NUMBER;
		$this->stream[] = pack( 'vvvvCCC', BIFF_ID_CELL_NUMBER, BIFF_LEN_CELL_NUMBER, $row, $col, $cell_status, $cell_picture + $cell_font, $cell_alignment ) . $x;
	}

	/**
	 * @access public
	 */
	function xlsSetFont( $font_name, $font_size = 10, $font_format = BIFF_FONT_NORMAL ) 
	{
		if ( $this->fonts > 3 && $this->err_level > 0 )
			return PEAR::raiseError( "Too many fonts.", null, PEAR_ERROR_TRIGGER );
		
		$len = strlen( $font_name );
		$this->stream[] = BIFF_ID_FONT_REC; 
		$this->stream[] = pack( 'vvvCCC', BIFF_ID_FONT_REC, BIFF_LEN_FONT_REC + $len, $font_size * 20, $font_format, 0x0, $len ) . $font_name;

		$this->fonts++;
	}

	/**
	 * @access public
	 */
	function xlsDate( $m, $d, $y ) 
	{
		return( juliantojd( $month, $day, $year ) - BIFF_XLS_DATE + 1 );
		// return ( 1461 * ( $y + 4800 + ( $m - 14 ) / 12 ) ) / 4 + ( 367 * ( $m - 2 - 12 * ( ( $m - 14 ) / 12 ) ) ) / 12 - ( 3 * ( ( $y + 4900 + ( $m - 14 ) / 12 ) / 100 ) ) / 4 + $d - 32075 - 2415020.5;
	}

	/**
	 * @access public
	 */
	function swapBytes( $str ) 
	{
		$swap = '';
		$y = strlen( $str ) / 2;
		
		for ( $x = 0; $x < $y; $x++ )
			$swap .= substr( $str, $x * 2, 2 );
		
		return( $swap );
	}

	/**
	 * @access public
	 */
	function setWindow()
	{
		$hpos = 30;
		$vpos = 30; 
		
		$this->stream[] = BIFF_ID_WINDOW1_REC;
		$this->stream[] = pack( 'vvvvvvCC', BIFF_ID_WINDOW1_REC, BIFF_LEN_WINDOW1_REC, $hpos, $vpos, $this->win_width * 20, $this->win_height * 20, $this->win_hidden, 0 );
		$this->stream[] = BIFF_ID_WINDOW2_REC;
		$this->stream[] = pack( 'vvCCCCCvvCCCCC', BIFF_ID_WINDOW2_REC, BIFF_LEN_WINDOW2_REC, $this->win_formula, $this->win_grid, $this->win_ref, $this->win_freeze, $this->win_zero, $this->win_disp_row, $this->win_disp_col, 1, 0, 0, 0, 0 );
	}

	/**
	 * @access public
	 */	
	function selection( $pane = 3, $row = 0, $col = 0 )
	{
		$this->stream[] = BIFF_ID_SELECTION_REC;
		$this->stream[] = pack( 'vvCvvvvvvCC', BIFF_ID_SELECTION_REC, BIFF_LEN_SELECTION_REC, $pane, $row, $col, 0, 1, $row, $row, $col, $col );
	}

	/**
	 * @access public
	 */
	function setCodePage()
	{
		$this->stream[] = BIFF_ID_CODEPAGE;
		$this->stream[] = pack( 'vvv', BIFF_ID_CODEPAGE, BIFF_LEN_CODEPAGE, 0x8001 );
	}
	
	/**
	 * @access public
	 */		
	function setDefFormat() 
	{
		$y = count( $this->picture );
		
		$this->stream[] = BIFF_ID_FORMAT_COUNT;
		$this->stream[] = pack( 'vvv', BIFF_ID_FORMAT_COUNT, BIFF_LEN_FORMAT_COUNT, 0x15 ); 
		
		for ( $x = 0; $x < $y; $x++ )
		{
			$len_format_str = strlen( $this->picture[$x] );
			$this->stream[] = BIFF_ID_FORMAT_REC;
			$this->stream[] = pack( 'vvC', BIFF_ID_FORMAT_REC, BIFF_LEN_FORMAT_REC + $len_format_str, $len_format_str ) . $this->picture[$x];
		}
	}
	
	/**
	 * This function does boundary checking on row and column values.
	 * It tries first to check if the supplied argument was in A1 notation,
	 * if this fails it looks for the faster row, col notation.
	 *
	 * @access public
	 */
	function checkBounds( &$row, &$col, $ref )
	{
      	if ( is_string( $row ) )
		{
         	$col = (int)$this->_cnvAAToCol( $row );
         	$row = (int)$this->_cnvAAToRow( $row );
      	}
      
	  	if ( $row < 0 || $col < 0 )
			return PEAR::raiseError( $ref . " rows or columns must be positive integers.", null, PEAR_ERROR_TRIGGER );   
      
      	if ( !is_int( $row ) || ! is_int( $col ) )
			return PEAR::raiseError( $ref . " rows or columns must be integers.", null, PEAR_ERROR_TRIGGER );
      
      	if ( $row > BIFF_MAX_ROWS ) 
			return PEAR::raiseError( $ref . BIFF_MAX_ROWS. " rows max.", null, PEAR_ERROR_TRIGGER );
      
      	if ( $col > BIFF_MAX_COLS ) 
			return PEAR::raiseError( $ref . BIFF_MAX_COLS. " cols max.", null, PEAR_ERROR_TRIGGER );
   	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _encodePassword( $pws )
	{
		$pws_len = strlen( $pws );
		$enc_pw  = (int)0;
		
		for ( $x = 0; $x < $pws_len; $x++ )
		{
			$char   = substr( $pws, $x, 1 );
			$ord    = ord( $char );
			$sh     = $this->_rl_14( $ord, $x + 1 );
			$enc_pw = $sh ^ $enc_pw;
		}
		
		$enc_pw = $enc_pw ^ $pws_len;
		$enc_pw = $enc_pw ^ 0xce4b;

		return( $enc_pw );
	}

	/**
	 * @access private
	 */
	function _rl_14( $value, $num )
	{ 
		$bin = sprintf( "%016b", $value );
		
		for ( $x = 0; $x < $num ; $x++ )
		{
			if ( substr( $bin, 1, 1 ) === '1' )
				$a = '1';
			else
				$a = '0';
			
			$bin = '0' . substr( $bin, 2, 15 ) . $a;
		}
		
		return ( base_convert( $bin, 2, 10 ) );		 
	}
	
	/**
	 * @access private
	 */
	function _adjustColWidth( $col, $col_width, $len )
	{
		if ( $col_width > 0 )
			$this->maxcolwidth[$col] = $col_width;
		
		if ( $col_width == 0 )
		{
			if ( isset( $this->maxcolwidth[$col] ) )
			{
				if ( $this->maxcolwidth[$col] < $len )
					$this->maxcolwidth[$col] = $len;
			}
			else
			{
				$this->maxcolwidth[$col] = $len;				  
			}
		}
	}
	
	/**
	 * This function fills the A1 notation array.
	 *
	 * @access private
	 */
	function _fillAANotation()
	{
		$max   = 256;
		$start = 65;
		$end   = 90;
		$y     = $start;
		$z     = $start;
		$pre   = null;
		
		for ( $x = 1; $x <= $max; $x++ )
		{
			if ( $z <> $start )
				$pre = chr( $z - 1 );
         
			$this->a_not[] = $pre . chr( $y );
			
			if ( $y == $end )
			{
            	$y = $start - 1;
            	$z++;
         	}
         
		 	$y++;
      	}
	}
	
	/**
	 * This function extracts the row value from the A1 notation.
	 * It returns -1 if the regular expression fails.
	 *
	 * @access private
	 */
	function _cnvAAToRow( $val )
   	{
      	$row = preg_split( '/[a-zA-Z]/', $val, -1, PREG_SPLIT_NO_EMPTY );
      
	  	if ( !empty( $row ) )
			return( $row[0] - 1 );
		else
			return( -1 ); 
   	}
	
	/**
	 * This function extracts the column number from an
	 * A1 notation. It returns -1 if the passed arguments 
	 * is wrong or if value exceeds IV = 255 columns.
	 *
	 * @access private
	 */
	function _cnvAAToCol( $val )
   	{
      	$res = null;
      	$col = preg_split( '/[0-9]/', $val, -1, PREG_SPLIT_NO_EMPTY );
      
	  	if ( !empty( $col ) )
		{
         	$res = array_search( strtoupper( $col[0] ), $this->a_not, true );
         
		 	if ( is_null( $res ) )
            	return( -1 );
         	else
				return( $res );
      	}
      	else
		{
         	return( -1 ); // preg_split failed
      	}
   	}
} // END OF Biff

?>
