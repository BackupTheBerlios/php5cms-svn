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
 * @package html_table
 */
 
class TableBuilder extends PEAR 
{
	/**
	 * @access public
	 */
	var $attr = array();
	
	/**
	 * @access public
	 */
	var $rows = array();

	/**
	 * @access public
	 */
	var $corner_char = '+';
	
	/**
	 * @access public
	 */
	var $col_char = '|';
	
	/**
	 * @access public
	 */
	var $row_char = '-';

	/**
	 * @access private
	 */
	var $_plain_text_rows = array();
	
	/**
	 * @access private
	 */
	var $_current_row = 0;
	
	/**
	 * @access private
	 */
	var $_cell_open = false;
	
	/**
	 * @access private
	 */
	var $_current_cell = 0;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function TableBuilder( $attr = array() ) 
	{
		$this->attr = $attr;
	}
	

	/**
	 * Clears the tables contents.
	 *
	 * @access public
	 */
	function clear() 
	{
		$this->attr = array();
		$this->rows = array();
		
		$this->_plain_text_rows = array();
	}

	/**
	 * Open a table row.
	 *
	 * @access public
	 */
	function openRow( $plain_text_border = false, $attr = array() ) 
	{
		if ( $this->_row_open ) 
			$this->closeRow();

		$this->_current_row = count( $this->rows );
		
		$this->rows[$this->_current_row] = array(
			'attr'              => $attr, 
			'plain_text_border' => $plain_text_border, 
			'cells'             => array()
		);
		
		$this->_row_open = true;
	}

	/**
	 * Open a table cell.
	 *
	 * @access public
	 */
	function closeRow() 
	{
		if ( $this->_cell_open ) 
			$this->closeCell();
		
		$this->_row_open = false;
	}

	/**
	 * Open a table cell.
	 *
	 * @access public
	 */
	function openCell( $attr = array() ) 
	{
		if ( $this->_cell_open ) 
			$this->closeCell();

		$this->_current_cell = count( $this->rows[$this->_current_row]['cells'] );
		$this->rows[$this->_current_row]['cells'][$this->_current_cell] = array( 'attr' => $attr, 'contents' => '' );
		$this->_cell_open = true;
		
		ob_start();	
	}

	/**
	 * Close a table cell.
	 *
	 * @access public
	 */
	function closeCell() 
	{
		$this->rows[$this->_current_row]['cells'][$this->_current_cell]['contents'] = ob_get_contents();
		ob_end_clean();
		$this->_cell_open = false;
	}

	/**
	 * @access public
	 */
	function closeTable() 
	{
		if ( $this->_row_open ) 
			$this->closeRow();
	}

	/**
	 * Print the table.
	 *
	 * @access public
	 */
	function paint( $html = true, $echo_pre_tags = true ) 
	{
		if ( $this->_row_open ) 
			$this->closeRow();

		if ( $html ) 
		{
			$this->_paintHTML();
		} 
		else 
		{
			if ( $echo_pre_tags ) 
				echo "<pre>\n";
			
			$this->_paintPlainText();
			
			if ( $echo_pre_tags ) 
				echo "</pre>\n";
		}
	}

	/**
	 * Returns a strings of the table contents.
	 *
	 * @access public
	 */
	function toString( $html = true ) 
	{
		if ( $this->_row_open ) 
			$this->closeRow();

		ob_start();	
		$this->paint( $html, false );
		$str = ob_get_contents();
		ob_end_clean();
		
		return $str;
	}

	
	// private methods

	/**
	 * Paint the html version of the tables.
	 *
	 * @access private
	 */
	function _paintHTML()
	{
		echo "<table" . $this->_attributeString( $this->attr ) . ">\n";
		
		for ( $r = 0; $r < count( $this->rows ); $r++ ) 
		{
			$row = &$this->rows[$r];
			echo "\t<tr" . $this->_attributeString( $row['attr'] ) . ">\n";
			
			for ( $c = 0; $c < count( $row['cells'] ); $c++ ) 
			{
				$cell = &$row['cells'][$c];
				echo "\t\t<td" . $this->_attributeString( $cell['attr'] ) . ">" . $cell['contents'] . "</td>\n";
			}
			
			echo "\t</tr>\n";
		}
		
		echo "</table>\n";
	}
	
	/**
	 * @access private
	 */
	function _attributeString( &$attr ) 
	{
		$attributes = '';
		
		if ( count( $attr ) ) 
		{
			for ( reset( $attr ); ( $k = key( $attr ) ) !== null; next( $attr ) )
				$attributes .= ' ' . $k . '="' . htmlspecialchars( $attr[$k] ) . '"';
		}
		
		return $attributes;
	}

	/**
	 * Paint the plain text version of the tables.
	 *
	 * @access private
	 */
	function _paintPlainText()
	{
		list( $col_widths, $row_heights ) = $this->_paintTextPrepRows();
		$border_on = ( isset( $this->attr['border'] ) && (int)$this->attr['border'] > 0 );
		$this->_paintPlainTextRowLine( $col_widths, 0 );
		$row_base_row_line_printed = true;
		$row_num = 0;
		
		for ( $r = 0; $r < count( $this->_plain_text_rows ); $r++ ) 
		{
			$row = &$this->_plain_text_rows[$r];
			
			// if this is the table has a border or the row wants a border then print the row line
			// assuming the row above hasn't just print a line
			if ( ( $border_on || $row['plain_text_border'] ) && !$row_base_row_line_printed ) 
			{
				$this->_paintPlainTextRowLine( $col_widths, $r );
				$row_base_row_line_printed = true;
			} 
			else 
			{
				$row_base_row_line_printed = false;
			}
			
			// cycle through each line of the row
			for ( $line_num = 0; $line_num < $row_heights[$row_num]; $line_num++ )
			{
				echo $this->col_char;
				$col_num = 0;
				
				for ( $c = 0; $c < count( $row['cells'] ); $c++ ) 
				{
					$cell = &$row['cells'][$c];
					
					switch ( strtolower( $cell['attr']['align'] ) )
					{
						case 'right' :
							$align = STR_PAD_LEFT;
							break;

						case 'center' :
							$align = STR_PAD_BOTH;
							break;

						default :
							$align = STR_PAD_RIGHT;
					}
			
					if ( $cell['attr']['colspan'] <= 1 ) 
					{
						$num_chars = $col_widths[$col_num];
					} 
					else 
					{
						$num_chars = 0;
						
						for ( $j = 0; $j < $cell['attr']['colspan']; $j++ )
							$num_chars += $col_widths[$col_num + $j];
						
						$num_chars += ( $cell['attr']['colspan'] - 1 ) * ( 2 + strlen( $this->col_char ) );
					}
		
					echo ' ' . str_pad( array_shift( $cell['contents'] ), $num_chars, ' ', $align ) . ' ' . $this->col_char;
					$col_num += $cell['attr']['colspan'];
				}

				// print out any other cols
				for ( $j = $col_num; $j < count( $col_widths ); $j++ )
					echo str_repeat( ' ', $col_widths[$j] + 2 ) . $this->col_char;
				
				echo "\n";
			}
			
			$row_num++;
			
			// if this is the table has a border or the row wants a border then print the row line
			if ( $border_on || $row['plain_text_border'] ) 
			{
				$this->_paintPlainTextRowLine( $col_widths, $r );
				$row_base_row_line_printed = true;
			} 
			else 
			{
				$row_base_row_line_printed = false;
			}
		}

		// print the bottom of the table if the last row didn't do it for us
		if ( !$row_base_row_line_printed )
			$this->_paintPlainTextRowLine( $col_widths, ( count( $this->_plain_text_rows ) - 1 ) );
	}

	/**
	 * Prepare the rows for plain text printing.
	 *
	 * @access private
	 */
	function _paintTextPrepRows()
	{
		// we'll play with a copy of the rows, to allow painting multiple times
		$this->_plain_text_rows = $this->rows;

		$col_widths  = array();
		$row_heights = array();
		
		for ( $r = 0; $r < count( $this->_plain_text_rows ); $r++ ) 
		{
			$row = &$this->_plain_text_rows[$r];
			$col_widths[$r]  = array();
			$row_heights[$r] = array();
			
			for ( $c = 0; $c < count( $row['cells'] ); $c++ ) 
			{
				$cell = &$row['cells'][$c];
				
				// if we have no start row then that means that we aren't a reference, so set one up
				if ( !isset( $cell['start_row'] ) )
					$cell['start_row'] = $r;
				
				if ( isset( $cell['attr']['colspan'] ) ) 
				{
					$colspan = (int)$cell['attr']['colspan'];
					
					if ( $colspan <= 0 ) 
						$colspan = 1;
				} 
				else 
				{
					$colspan = 1;
				}
				
				if ( isset( $cell['attr']['rowspan'] ) ) 
				{
					$rowspan = (int)$cell['attr']['rowspan'];
					
					if ( $rowspan <= 0 ) 
						$rowspan = 1;
					
					// OK now the tricky part of moving the cells below us so that we actually span the row
					if ( $rowspan > 1 && $cell['start_row'] == $r ) 
					{	
						for ( $i = 1; $i < $rowspan; $i++ ) 
						{
							if ( !isset( $this->_plain_text_rows[$r + $i] ) ) 
								break;
							
							$next_row = &$this->_plain_text_rows[$r + $i];
							
							// move all the cells to the right one space
							for ( $j = count( $next_row['cells'] ); $j > $c; $j-- )
								$next_row['cells'][$j] = &$next_row['cells'][$j - 1];
							
							$next_row['cells'][$c] = &$cell;
						}
					}
				} 
				else 
				{
					$rowspan = 1;
				}
				
				$cell['attr']['colspan'] = $colspan;
				$cell['attr']['rowspan'] = $rowspan;
				$cell['contents'] = $this->_plainTextArray( $cell['contents'] );

				// we only want to be setting this on our first run through
				if ( $cell['start_row'] == $r ) 
				{
					$row_heights[$c][$r] = array(
						'rowspan' => $rowspan, 
						'height'  => count( $cell['contents'] )
					);
				}

				$col_width = 0;
				
				foreach ( $cell['contents'] as $line ) 
					$col_width = max( $col_width, strlen( $line ) );
					
				$col_widths[$r][$c] = array(
					'colspan' => $colspan, 
					'len'     => $col_width
				);
			}
		}

		// firstly we will get the max size for each col of the single col cells
		$max_col_widths = array();

		for ( reset( $col_widths ); ( $r = key( $col_widths ) ) !== null; next( $col_widths ) ) 
		{
			$i = 0;
			
			for ( reset( $col_widths[$r] ); ( $c = key( $col_widths[$r] ) ) !== null; next( $col_widths[$r] ) ) 
			{
				$cell = &$col_widths[$r][$c];
				
				if ( !$max_col_widths[$i] ) 
					$max_col_widths[$i] = 0;
				
				if ( $cell['colspan'] == 1 )
					$max_col_widths[$i] = max( $max_col_widths[$i], $cell['len'] );
				
				$i += $cell['colspan'];
			}
		}

		// OK now we need to check the cells that span cols to make sure that they 
		// don't use more chars than the sum of the max of the single cells they span
		for ( reset( $col_widths ); ( $r = key( $col_widths ) ) !== null; next( $col_widths ) ) 
		{
			$i = 0;
			
			for ( reset( $col_widths[$r] ); ( $c = key( $col_widths[$r] ) ) !== null; next( $col_widths[$r] ) ) 
			{
				$cell = &$col_widths[$r][$c];
				
				if ( $cell['colspan'] > 1 ) 
				{
					do 
					{
						$sum_max = 0;
						
						for ( $j = 0; $j < $cell['colspan']; $j++ ) 
						{
							// if we are not the first col then include space after the col char
							$sum_max += $max_col_widths[$i + $j];
						}
						
						$sum_max    += ( $cell['colspan'] - 1 ) * ( 2 + strlen( $this->col_char ) );
						$test_again  = false;	
						
						// bugger, we have to adjust the max values so that this cell fits
						if ( $sum_max < $cell['len'] ) 
						{
							for ( $j = 0; $j < $cell['colspan']; $j++ ) 
								$max_col_widths[$i + $j]++;
							
							$test_again = true;	
						}
					} while ( $test_again );
				}
				
				$i += $cell['colspan'];
			}
		}

		// get the max number of lines for each of the single rowspan cells
		$max_row_heights = array();
		
		for ( reset( $row_heights ); ( $c = key( $row_heights ) ) !== null; next( $row_heights ) ) 
		{
			$i = 0;

			for ( reset( $row_heights[$c] ); ( $r = key( $row_heights[$c] ) ) !== null; next( $row_heights[$c] ) ) 
			{
				$cell = &$row_heights[$c][$r];
				
				if ( !$max_row_heights[$i] ) 
					$max_row_heights[$i] = 0;
				
				if ( $cell['rowspan'] == 1 )
					$max_row_heights[$i] = max( $max_row_heights[$i], $cell['height'] );

				$i += $cell['rowspan'];
			}
		}
		
		ksort( $max_row_heights );

		// OK now we need to check the cells that span rows to make sure that they 
		// don't use more lines than the sum of the max of the single cells they span
		for ( reset( $row_heights ); ( $c = key( $row_heights ) ) !== null; next( $row_heights ) ) 
		{
			$i = 0;
			
			for ( reset( $row_heights[$c] ); ( $r = key( $row_heights[$c] ) ) !== null; next( $row_heights[$c] ) ) 
			{
				$cell = &$row_heights[$c][$r];
				
				if ( $cell['rowspan'] > 1 ) 
				{
					do 
					{
						$sum_max = 0;
						
						for ( $j = 0; $j < $cell['rowspan']; $j++ )
							$sum_max += $max_row_heights[$i + $j];
						
						$sum_max    += ( $cell['rowspan'] - 1 ) * strlen( $this->row_char );
						$test_again  = false;	
						
						// bugger, we have to adjust the max values so that this cell fits
						if ( $sum_max < $cell['height'] ) 
						{
							for ( $j = 0; $j < $cell['rowspan']; $j++ ) 
								$max_row_heights[$i + $j]++;
							
							$test_again = true;	
						}
					} while ( $test_again );
				}
				
				$i += $cell['rowspan'];
			}
		}

		ksort( $max_col_widths  );
		ksort( $max_row_heights );

		return array(
			$max_col_widths, 
			$max_row_heights
		);
	}

	/**
	 * Convert a bunch of html to plain text, trying to keep the line breaks in the right spot.
	 *
	 * @access private
	 */
	function _plainTextArray( $html ) 
	{
		// remove all the new lines as we are going to make our own
		$html = ereg_replace( "[\r\n]",	'', $html );
		
		$html = str_replace( '<p>',		"\n\n", $html );
		$html = str_replace( '<div>',	"\n\n", $html );
		$html = str_replace( '<br>',	"\n",   $html );
		$html = str_replace( '<br />',	"\n",   $html );
		$html = str_replace( '<li>',	"\n -", $html );
		
		$html = str_replace(
			array( '&amp;', '&nbsp;', '&quot;'),
			array( '&',     ' ',      '"'     ), 
			$html
		);
		
		$html = strip_tags( $html );
		return explode( "\n", $html );
	}

	/**
	 * @access private
	 */
	function _paintPlainTextRowLine( &$col_widths, $rowid ) 
	{
		echo $this->corner_char;
		
		foreach ( $col_widths as $i => $width )
			echo str_repeat( $this->row_char, $width + 2 ) . $this->corner_char;
		
		echo "\n";
	}
} // END OF TableBuilder

?>
