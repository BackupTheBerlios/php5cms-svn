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
 * Class to create simultaneous tabbed text for each column. 
 * Text should be formated with tabs and breaks like this:
 *	
 * Field 1 \t Field 2 \t Field 3 \n
 * Item 11\t Item 12 \ Item 13\n
 * Item 21\t Item 22 \t Item 23\n
 *
 * @package util_text
 */

class TabText extends PEAR
{
	/**
	 * @access public
	 */
	var $full_text;
	
	/**
	 * @access public
	 */
	var $fields_maxlen;
	
	/**
	 * @access public
	 */
	var $text_fields;
	
	/**
	 * @access public
	 */
	var $line_fields;
	
	/**
	 * @access public
	 */
	var $tabsize;
	
	/**
	 * @access public
	 */
	var $right;
	
	/**
	 * @access public
	 */
	var $tab = "\t";
	
	/**
	 * @access public
	 */
	var $nl = "\n";
	
	/**
	 * @access public
	 */
	var $w = " ";
	
	
	/**
	 * Expects one var, possible are two more vars for the tabsize and rightbound field (array).
	 *
	 * @param  string $text		should be with tabs and breaks
	 * @param  int	  $tabsize	size of a tab, depending on editor or email client	 
	 * @param  array  $right	array with field numbers, you want to rightbound (count from 0)
	 * @access public
	 */
	function text_format( $text, $tabsize = 6, $right )
	{
		$this->tabsize = $tabsize;
		$this->right   = $right;
		
		$text_lines = explode( "\n", $text );
		
		for ( $i = 0; $i < count( $text_lines ); $i++ )
			$this->text_fields[] = explode( "\t", trim( $text_lines[$i] ) ); 
		
		$this->max_length();
	}
	
	/**
	 * @access public
	 */
	function max_length()
	{
		for ( $i = 0; $i < count( $this->text_fields ); $i++ )
		{
			for ( $e = 0; $e < count( $this->text_fields[$i] ); $e++ )
			{
				if ( strlen( $this->text_fields[$i][$e] ) > $this->fields_maxlen[$e] )
					$this->fields_maxlen[$e] = strlen( $this->text_fields[$i][$e] );
			}
		}
		
		for ( $e = 0; $e < count( $this->fields_maxlen ); $e++ )
			$this->fields_maxlen[$e] = ceil( $this->fields_maxlen[$e] / $this->tabsize ) * $this->tabsize;
	}
	
	/**
	 * Adds a line after given line number. You can choose the line art as second parameter.
	 *
	 * @param  int		the line number, you wanna add the line after
	 * @param  string	just a char, you wanna have the line of
	 * @access public
	 */
	function text_line( $line_number, $line_art = "-" )
	{	
		$len = 0;
		reset( $this->fields_maxlen );
		
    	while ( list( $k, $v ) = each( $this->fields_maxlen ) )
			$len =  $len + $v;
    	
		reset ( $this->fields_maxlen );
		
		for ( $i = 0; $i < $len; $i++ )
			$this->line_fields[$line_number-1] .= $line_art;
		
		$this->line_fields[$line_number-1] .= "\n";
	}
	
	/**
	 * Just call to output the whole stuff.
	 *
	 * @access public
	 */
	function text_output()
	{
		$this->full_text = $this->line_fields[-1];
	
		for ( $i = 0; $i < count( $this->text_fields ); $i++ )
		{	
			for ( $e = 0; $e < count( $this->fields_maxlen ); $e++ )
			{
				if ( in_array( $e, $this->right ) )
				{
					$tempstring = $this->text_fields[$i][$e];
					
					for ( $q = 0; $q < ( $this->fields_maxlen[$e] - strlen( $tempstring ) - 1 ); $q++ )
						$this->text_fields[$i][$e] = " " . $this->text_fields[$i][$e];
					
					$this->text_fields[$i][$e] .= " ";
				}
				
				$new_fields[$i][$e] = $this->text_fields[$i][$e];
			
				for ( $r = 0; $r < ceil( ( $this->fields_maxlen[$e] - strlen( $this->text_fields[$i][$e] ) ) / $this->tabsize ); $r++ )
					$new_fields[$i][$e] .= "\t";
				
				$this->full_text .= $new_fields[$i][$e];
			}
			
			$this->full_text .= "\n".$this->line_fields[$i];
		}
		
		return $this->full_text;
	}
} // END OF TabText

?>
