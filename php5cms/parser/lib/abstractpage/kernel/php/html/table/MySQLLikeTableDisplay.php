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
 * Class to render php structs as mysql tables or HTML tables
 *
 * eg this struct: 
 *
 * $array = array(
 *		array(
 *			"id"   => 1,
 *			"name" =>"mark"
 *		), 
 *		array(
 *			"id"   => 2,
 *			"name" => "jayne"
 *		)
 * );
 *
 * would display as:
 *
 * +-------+---------+
 * | id    | name    |
 * +-------+---------+
 * | 1     | Mark    | 
 * | 2     | Jayne   | 
 * +-------+---------+
 *
 * Also added (somewhat bolted on and nasty) is ability to render
 * html tables (full stylesheet support) 
 *
 * The only thing to be aware of is the use of "-" and "+" signs - if these
 * are in the field values then they need to be replaced with &minus; or &plus; depending	
 * - this will make sure the class doesn't get confused as to where cells end etc. 
 *
 * Example usage using above array: 
 *
 * $array = array(
 *		array(
 *			"id"   => 1,
 *			"name" =>"mark"
 *		), 
 *		array(
 *			"id"   => 2,
 *			"name" => "jayne"
 *		)
 * );
 *
 * $ms = new MySQLLikeTableDisplay($array );
 * $ms->makeLayout( 1 );
 *
 * // HTML TABLE DISPLAY
 * $htmltable = new display( $array );
 *
 * // pass 0 here so that mysql style table isnt rendered
 * $htmltable->makeLayout( 0 );
 *
 * $htmltable->setBorderWidth( 0 );  
 * $htmltable->setTableWidth( "100%" );  
 * $htmltable->setCellSpacing( 1 );
 * $htmltable->setCellPadding( 2 );
 *
 * // style values for various settings
 * $htmltable->createStyleSheet( "cccccc", "000000", "x-small", "000066", "ffffff", "medium" );
 *
 * // creates the table
 * $htmltable->makeHTMLTable();
 *
 * // renders the table to the screen
 * $htmltable->parseHTML();
 *
 * @package html_table
 */

class MySQLLikeTableDisplay extends PEAR
{
	/**
	 * @access public
	 */
	var $dis;
	
	/**
	 * @access public
	 */
	var $divider;
	
	/**
	 * @access public
	 */
	var $rows;
	
	/**
	 * @access public
	 */
	var $write;
	
	/**
	 * @access public
	 */
	var $ascii_output;
	
	/**
	 * @access public
	 */
	var $finalhtmltable;
	
	/**
	 * @access public
	 */
	var $stylesheet;
	
	/**
	 * @access public
	 */
	var $tdwidth;
	
	/**
	 * @access public
	 */
	var $use_tdwidths;
	
	/**
	 * @access public
	 */
	var $num_cells;	
	
	/**
	 * @access public
	 */
	var $widths = array();
	
	/**
	 * @access public
	 */
	var $biggest = array();
	
	/**
	 * @access public
	 */
	var $data = array();
	
	/**
	 * @access public
	 */
	var $emptyset = false;
	
	/**
	 * @access public
	 */
	var $borderwidth = 0;
	
	/**
	 * @access public
	 */
	var $tablewidth = 600;
	
	/**
	 * @access public
	 */
	var $bordercolor = "#000000";
	
	/**
	 * @access public
	 */
	var $cellpadding = 2;
	
	/**
	 * @access public
	 */
	var $cellspacing = 1;
	

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MySQLLikeTableDisplay( $stuff, $widths = 0 )
	{
		$this->use_tdwidths = $widths;
		
		foreach ( $stuff as $s )
		{
			$this->num_cells = count( $s );	
			continue; 
		}
		
		$this->num_cells = $this->num_cells? $this->num_cells : count( $stuff );
		$this->widths = $this->calculateWidths( $stuff );
	}
	
	
	/**
	 * @access public
	 */
	function calculateWidths( $array )
	{
		if ( empty( $array ) )
		{
			// check that there is some data to display
			$this->emptyset = true;
			return false;
		}
		
		// loop through each row
		$this->data = $array;
		$x = 0;
		
		if ( is_array( $array ) )
		{
			foreach ( $array as $a )
			{
				if ( is_array( $a ) )
				{
				 	while ( list( $key, $val ) = each( $a ) ) 
						$this->widths[$x][$key] = strlen( $val );
				}
				
				++$x;
			}
		}
		
		$this->biggest = $this->getLongestOnly();
		return ( $this->widths );
	}

	/**
	 * @access public
	 */
	function getLongestOnly()
	{
		$x = 0;
		$array = $this->widths;
		
		foreach ( $array as $a )
		{
			while ( list( $key, $val ) = each( $a ) )
			{ 
				if ( $val > $this->biggest[$key] || empty( $this->biggest[$key] ) )	
					$this->biggest[$key] = $val;
					
				if ( strlen( $key ) > $this->biggest[$key] )
					$this->biggest[$key] = strlen( $key );
			}
			
			++$x;
		}
		
		return ( $this->biggest );
	}	

	/**
	 * @access public
	 */
	function makeLayout( $write = 1 )
	{
		$this->write = $write;
		
		if ( $this->emptyset )
			return( "Empty set\n" );
		
		$first = "+";	
		
		while ( list( $key, $val ) = each( $this->biggest ) )
		{ 
			$dis .= "+";
			
			for ( $x = 0; $x < $this->biggest[$key]; $x++ )
				$first .= "-";

			$first .= "+";
			$s = "|" . ucwords( str_replace( "_", " ", $key ) );
			
			if ( strlen( $s ) <= $this->biggest[$key] )
			{
				for ( $x = strlen( $s ); $x <= $this->biggest[$key]; $x++ )
					$s.=" ";
			}
			
			$second .= $s;
		}
		
		$this->divider = $first;
		$re  = $first . "\n" . $second . "|\n" . $first . "\n";
		$re .= $rows;	

		$this->rows = $this->makeBody();
		$re .= $this->rows;
		
		if ( $this->write )
			echo "" . $re . "";
		
		$this->ascii_out = $re;
		return ( $re );
	}

	/**
	 * @access public
	 */
	function createStyleSheet( $bg = "ededed", $fontcol = "000000", $fontsize = "small", $bg2 = "444444", $fontcol2 = "ffffff", $fontsize2 = "medium" )
	{
		$this->stylesheet = "
			<STYLE type='text/css'> 
			<!--
				.column-data  { background-color:$bg; color:$fontcol; font-size:$fontsize; }
				.table-header { background-color:$bg2; color:$fontcol2; font-weight:bold; text-align:center; font-size:$fontsize2 }
			//-->
			</style>";
	}

	/**
	 * @access public
	 */
	function makeBody()
	{
		if ( is_array( $this->data ) )
		{
			foreach ( $this->data as $row )
			{	
				if ( is_array( $row ) )
				{
				 	while ( list( $key, $val ) = each( $row ) )
					{
						if ( is_array( $val ) )
						{
							$out[0] = $val;
							$tr = new MySQLLikeTableDisplay( $out );
							$tr->makeLayout( 0 );
							$tr->setBorderWidth( $this->borderwidth );
							$tr->setCellPadding( $this->cellpadding );
							$tr->setCellSpacing( $this->cellspacing );
							$tr->setBorderColor( $this->bordercolor );		
							$val = "<Table><TR><TD> " . $tr->makeHTMLTable() . "</TD></tR></tAble>";
						}
					
						$r .= "|" . $val;
					
						if ( strlen( $val ) <= $this->biggest[$key] )
						{
							for ( $x = strlen( $val ); $x < $this->biggest[$key]; $x++ )	
							$r .= " ";
						}
					}
				}
				
				$r.="|\n";
			}	
		}	
		
		$r .= $this->divider . "\n";
		return ( $r );	
	}

	/**
	 * @access public
	 */
	function getDivider()
	{
		return ( $this->divider );
	}

	/**
	 * @access public
	 */
	function getNumCells()
	{
		return ( $this->num_cells );
	}

	/**
	 * @access public
	 */
	function setBorderWidth( $wid )
	{
		$this->borderwidth = $wid;
	}

	/**
	 * @access public
	 */
	function setCellPadding( $pad )
	{
		$this->cellpadding = $pad;
	}

	/**
	 * @access public
	 */	
	function setCellSpacing( $spac )
	{
		$this->cellspacing = $spac;
	}

	/**
	 * @access public
	 */
	function setTableWidth( $tbwidth )
	{
		$this->tablewidth = $tbwidth;
	}

	/**
	 * @access public
	 */
	function setBorderColor( $col )
	{
		$this->bordercolor = $col;
	}

	/**
	 * Converts ascii display into a proper html table.
	 *
	 * @access public
	 */	
	function makeHTMLTable()
	{
		$text = $this->ascii_out;
		$this->tdwidth = floor( 100 / $this->num_cells ) . "%";

		if ( !$this->use_tdwidths )
			$this->tdwidth = "";

		$rows = explode( "\n", $text );
		$x = 0;
		
		foreach ( $rows as $row )
		{
			$last  = strlen( $row );
			$class = "column-data";	
			
			if ( $x == 1 )
				$class = "table-header";
			
			if ( !ereg( "^\+\-*", $row ) && strlen( $row ) > 0 )
			{
				$row  = "<TR>\n <td class='$class' align='center' valign='middle' width=$this->tdwidth>" . $row;
				$row .= "</td>\n</TR>\n";
				$row  = str_replace( "+", "</td><td class='$class' align='center' width=$this->tdwidth>", $row );
				$row  = str_replace( "|", "</td><td class='$class' align='center' width=$this->tdwidth>", $row );
				$row  = str_replace( "<td class='$class' align='center' width=$this->tdwidth></td>", "",  $row ); //remove any blanks
				$row  = str_replace( "<td class='$class' align='center' valign='middle' width=$this->tdwidth></td>", "", $row ); //remove any blanks
				
				$htmloutput .= $row;
			}
			
			$x++;
		}

		$style = $this->stylesheet;
		$htmloutput = $style . "\n<TABLE border='" .
			$this->borderwidth . "' bordercolor='" . 
			$this->bordercolor . "' cellpadding='" . 
			$this->cellpadding . "' cellspacing='" . 
			$this->cellspacing . "' width='" . 
			$this->tablewidth  . "'>\n " .
			$htmloutput . "\n</TABLE>";

		$this->finalhtmltable = $htmloutput;
		return ( $htmloutput );	
	}

	/**
	 * @access public
	 */
	function returnHTML()
	{
		$this->finalhtmltable = str_replace( "&plus;",  "+", $this->finalhtmltable );
		$this->finalhtmltable = str_replace( "&minus;", "-", $this->finalhtmltable );
		
		return ( $this->finalhtmltable );
	}

	/**
	 * @access public
	 */	
	function parseHTML()
	{
		$this->finalhtmltable = str_replace( "&plus;",  "+", $this->finalhtmltable );
		$this->finalhtmltable = str_replace( "&minus;", "-", $this->finalhtmltable );
		
		echo $this->finalhtmltable;
	}
} // END OF MySQLLikeTableDisplay

?>
