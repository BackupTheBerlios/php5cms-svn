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
 * @package image_graph_lib
 */
 
class GraphObject extends PEAR
{
	/**
	 * @access public
	 */
	var $filename;
	
	/**
	 * @access public
	 */
	var $width;
	
	/**
	 * @access public
	 */
	var $height;

	/**
	 * Top margin for write title
	 * @access public
	 */
	var $top_margin;

	/**
	 * Text labels describing the x and y axis
	 * @access public
	 */
	var $x_axis_label;
	var $y_axis_label;

	/**
	 * Image title description
	 * @access public
	 */
	var $title_label;

	/**
	 * The array of integers data to be plotted, $data_x is only for draw line chart
	 * @access public
	 */
	var $data   = array();
	var $data_x = array();

	/**
	 * Text labels on the x axis corresponding to each element in $data
	 * @access public
	 */
	var $x_labels = array();

	/**
	 * Text labels on the y axis describing the range of the data
	 * @access public
	 */
	var $y_labels = array();

	/**
	 * Legend for the graph
	 * @access public
	 */
	var $legend = array();

	/**
	 * Default font to use when printing
	 * @access public
	 */
	var $font_title = 5;
	
	/**
	 * @access public
	 */
	var $font = 3;

	/**
	 * @access public
	 */
	var $title_color = 0;
	
	/**
	 * @access public
	 */
	var $background_color = 0;
	
	/**
	 * @access public
	 */
	var $border_color = 0;
	
	/**
	 * @access public
	 */
	var $graph_color = array();

	/**
	 * Default not show dashed line
	 * @access public
	 */
	var $x_dashed_line = 0;
	var $y_dashed_line = 0;

	/**
	 * Default not show the detail for each element in the graph
	 * @access public
	 */
	var $show_x_detail = 0;
	var $show_y_detail = 0;

	/**
	 * Distance from the circle's top
	 * @access public
	 */
	var $dd = 0;

	/**
	 * The diameter of a circle
	 * @access public
	 */
	var $diameter = 150;

	/**
	 * The (x,y) coordinate for the centre of a circle
	 * @access public
	 */
	var $x_centre = 120;
	var $y_centre = 160;

	/**
	 * Image handle
	 * @access private
	 */
	var $image;

	/**
	 * The virtual range within that the data is displayed on the graph
	 * @access private
	 */
	var $y_range_pos;
	var $y_range_neg;

	/**
	 * Cooridnate the x axis is graphed
	 * @access private
	 */
	var $x_axis;

	/**
	 * width and Height in pixels of the plotting area
	 * @access private
	 */
	var $plotarea_width;
	var $plotarea_height;

	/**
	 * Average space from one grid to another on the x axis
	 * @access private
	 */
	var $average_space;

	/**
	 * Array of plotted data  in (x,y) format
	 * @access private
	 */
	var $plotted_data = array();

	/**
	 * Text labels on the x axis corresponding to each element in $data
	 * @access private
	 */
	var $x_labels_default = array();

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function GraphObject( $width = 600, $height = 300 )
	{
		$this->filename     = "graph.gif";
		$this->title_label  = "Abstractpage Analysis";
		$this->x_axis_label = "x_axis";
		$this->y_axis_label = "y_axis";
		$this->top_margin   = 50;
		
		// create image handle
		$this->image  = imagecreate( $width, $height );	
		$this->width  = $width;
		$this->height = $height;

		// allocate standard colors
		$this->background_color = imagecolorallocate( $this->image, 255, 255, 255 );
		$this->border_color 	= imagecolorallocate( $this->image,   0,   0,   0 );
		$this->title_color 		= imagecolorallocate( $this->image, 255,   0,   0 );
		
		$this->graph_color = array(
			imagecolorallocate( $this->image, 255,   0,   0 ),
			imagecolorallocate( $this->image,   0,   0, 255 ),
			imagecolorallocate( $this->image,   0, 255,   0 ),
			imagecolorallocate( $this->image, 255,   0, 255 ),
			imagecolorallocate( $this->image, 150, 100, 100 ),
			imagecolorallocate( $this->image, 100, 150, 100 ),
			imagecolorallocate( $this->image, 155,   0,   0 ),
			imagecolorallocate( $this->image,   0,   0, 155 ),
			imagecolorallocate( $this->image,   0, 219, 219 ),
			imagecolorallocate( $this->image, 255, 199,  60 ),
			imagecolorallocate( $this->image,   0, 155,   0 ),
			imagecolorallocate( $this->image,   0, 255, 255 ) 
		 );
	}

	
	/**
	 * Change the default allocate color function.
	 *
	 * @access public
	 */
	function change_color( $num, $red, $green, $blue )
	{
		$this->graph_color[$num] = imagecolorallocate( $this->image, $red, $green, $blue );
	
		if ( $this->graph_color[bg] )
			$this->background_color = $this->graph_color[bg];
	
		if ( $this->graph_color[bd] )
			$this->border_color = $this->graph_color[bd];
	
		if ( $this->graph_color[tt] )
			$this->title_color = $this->graph_color[tt];
	}

	/**
	 * $y_grid is the grids you want to separate the y axis $d is the width for each edrectangle
	 *
	 * @access public
	 */
	function draw( $y_grid = 8, $d = 10 )
	{
		$this->init( $y_grid );
		$this->prep();
		$this->plot( $d );
		$this->close();
	}
	
	/**
	 * Defacto constructor.
	 *
	 * @access public
	 */
	function init( $y_grid = 8 )
	{	
		// find the max value and the longest count of the $data array
		for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			if ( max( $this->data[$i] ) > $max_value )
				$max_value = max( $this->data[$i] );
			
			if ( count( $this->data[$i] ) > $count_num )
				$count_num = count( $this->data[$i] );
		}

		// if the grids is too much, cut it down
		if ( $y_grid > ceil( $this->height / 35 ) )
			$y_grid = floor( $this->height / 35 );
		
		if ( !$this->y_labels )
		{
			$y_grid = ( $max_value * 11 ) / ( 10 * $y_grid );
			$this->y_labels = $this->xrange( 0, $max_value * 11 / 10, $y_grid );
		}
	
		// keep the default labels
		for ( $i = 1; $i <= $count_num; $i++ )
			$this->x_labels_default[] = $i;
	
		if ( $this->x_labels && count( $this->x_labels_default ) != count( $this->x_labels ) )
			return PEAR::raiseError( "The X labels elements can not match the max count of element in data array." );

		// find longest string of the y_labels
		$longest = strlen( max( $this->y_labels ) );
		$this->plotarea_width  = $this->width  - 35 - ( $longest * 7 );
		$this->plotarea_height = $this->height - 40;

		// set the display range
		if ( !isset( $this->y_range_pos ) )
			$this->y_range_pos = max( $this->y_labels );
	
		if ( !isset( $this->y_range_neg ) )
			$this->y_range_neg = abs( min( $this->y_labels ) );
	
		// calculate where to graph the x axis
		$this->x_axis = ( $this->y_range_pos / ( $this->y_range_pos + $this->y_range_neg ) ) * $this->plotarea_height;
	
		// draw Background
		imagefilledrectangle(
			$this->image,
			0,
			0,
			$this->width, 
			$this->height,
			$this->background_color
		);

		// draw y axis label
		imagestringup(
			$this->image, 
			$this->font,
			10, 
			( ( $this->plotarea_height + $this->top_margin ) / 2 ) + ( strlen( $this->y_axis_label ) * 3.5 ), 
			$this->y_axis_label,
			$this->border_color
		);

		// draw title label
		imagestring(
			$this->image, 
			$this->font_title,
			( ( $this->width - $this->plotarea_width ) + $this->plotarea_width / 2 ) - ( strlen( $this->title_label ) * $this->font_title ),
			$this->font_title,
			$this->title_label, 
			$this->title_color
		);

		// draw x axis label	
		imagestring(
			$this->image, 
			$this->font,
			( ( $this->width - $this->plotarea_width ) + $this->plotarea_width / 2 ) - ( strlen( $this->x_axis_label ) * 3.5 ),
			$this->plotarea_height + 25,
			$this->x_axis_label, 
			$this->border_color
		);

		// draw plot borders
		imageline(
			$this->image,
			$this->width - $this->plotarea_width, 
			$this->top_margin,
			$this->width - $this->plotarea_width, 
			$this->plotarea_height, 
			$this->border_color
		);

		imageline(
			$this->image,
			$this->width - $this->plotarea_width, 
			$this->plotarea_height,
			$this->width, 
			$this->plotarea_height, 
			$this->border_color
		);

		// draw x axis
		imageline(
			$this->image,
			$this->width - $this->plotarea_width, 
			$this->x_axis,
			$this->width, 
			$this->x_axis, 
			$this->border_color
		);
	
		// draw y labels (if present)
		if ( $num_lable = count( $this->y_labels ) )
		{
			reset( $this->y_labels );
		
			// calculate the space between each label
			$label_space = ( $this->plotarea_height - $this->top_margin ) / ( $num_lable - 1 );
		
			for ( $y = $this->plotarea_height; $y > -1 + $this->top_margin; $y = $y - $label_space )
			{
				$label = current( $this->y_labels );
				$left_offset = strlen( $label ) * 7;

				// write label string
				imagestring(
					$this->image, 
					$this->font,
					$this->width - $this->plotarea_width - $left_offset - 4, 
					$y - 1,
					$label, 
					$this->border_color
				);

				// draw data marker
				imageline(
					$this->image,
					$this->width - $this->plotarea_width - 2, 
					$y,
					$this->width - $this->plotarea_width, 
					$y,
					$this->border_color
				);
			
				if ( $this->x_dashed_line )
					ImageDashedLine(
						$this->image,
						$this->width - $this->plotarea_width,
						$y,
						$this->width,
						$y,
						$this->border_color
					);
			
				next( $this->y_labels );
			}
		}
	
		// draw x labels (if present)
		if ( $num_lable = count( $this->x_labels_default ) )
		{
			current( $this->x_labels_default );

			// calculate the space between each label
			$label_space = $this->plotarea_width / ( $num_lable + 1 );
			$this->average_space = $label_space;
		
			for ( $x = ( $this->width - $this->plotarea_width ) + $label_space; $x < $this->width; $x = $x + $label_space )
			{
				$label = current( $this->x_labels_default );
				$left_offset = ( strlen( $label ) * 6 ) / 2;

				// write label marker
				imageline(
					$this->image,
					$x,
					$this->plotarea_height,
					$x,
					$this->plotarea_height + 2,
					$this->border_color
				);

				if ( $this->y_dashed_line )
					imageDashedLine(
						$this->image,
						$x,
						$this->plotarea_height,
						$x,
						$this->top_margin,
						$this->border_color
					);

				// draw data string	
				if ( !$this->x_labels )
					imagestring(
						$this->image, 
						$this->font,
						$x - $left_offset, 
						$this->plotarea_height + 4,
						$label, 
						$this->border_color
					);
			
				next( $this->x_labels_default );
			}
		
			if ( $num_label = count( $this->x_labels ) )
			{
				$num_lable = count( $this->x_labels );
				current($this->x_labels);

				// calculate the space between each label
				$label_space = $this->plotarea_width / ( $num_lable + 1 );
		
				for ( $x = ( $this->width - $this->plotarea_width ) + $label_space; $x < $this->width; $x = $x + $label_space )
				{
					$label = current( $this->x_labels );
					$left_offset = ( strlen( $label ) * 6 ) / 2;
				
					imagestring(
						$this->image, 
						$this->font,
						$x - $left_offset, 
						$this->plotarea_height + 4,
						$label,
						$this->border_color
					);

					// write label marker
					imageline(
						$this->image,
						$x,
						$this->plotarea_height,
						$x,
						$this->plotarea_height + 4,
						$this->border_color
					);
				
					next( $this->x_labels );
				}
			}
		}
	}

	/**
	 * Plot data to plotted_data.
	 *
	 * @access public
	 */
	function prep()
	{
    	for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			// if x_labels spresent get spacing from x_labels
			if ( $num_lable = count( $this->x_labels_default ) )
			{
				$label_space = $this->plotarea_width / ( $num_lable + 1 );
			}
			else if ( $num_lable = count( $this->data[$i] ) )
			{
				$label_space = $this->plotarea_width / ( $num_lable + 1 );
			}
			else
			{
				return PEAR::raiseError( "No data to prepare." );
				// exit;
			}

			reset( $this->data[$i] );
			$plotted_index = 0;
			
			for ( $x = ( $this->width - $this->plotarea_width ) + $label_space; $x < $this->width; $x = $x + $label_space )
			{
				$data_entry = current( $this->data[$i] );

				if ( $data_entry == "" )
					$data_entry = 0;

				// if data_entry off the top of the chart, clip it
				if ( $data_entry > $this->y_range_pos )
					$data_entry = $this->y_range_pos;

				// if data_entry off the bottom of the chart, clip it
				if ( $data_entry < ( $this->y_range_neg * -1 ) )
					$data_entry = $this->y_range_neg * -1;
		
				if ( $data_entry >= 0 )
					$y = $this->x_axis - ( ( $this->x_axis - $this->top_margin ) * ( $data_entry / $this->y_range_pos ) );
				else
					$y = $this->x_axis + ( ( $this->plotarea_height - $this->x_axis ) * ( ( $data_entry  * -1 ) / $this->y_range_neg ) );

				// write plotting data to plotted_data variable
				$this->plotted_data[$i][$plotted_index] = array( $x, $y );

				$plotted_index++;
				next( $this->data[$i] );
			}
    	}
	}

 	/**
	 * Plot data to image as solid bars.
	 *
	 * @access public
	 */
	function plot( $bar_width_1 = 10 )
	{
		if ( $this->average_space / ( count( $this->data ) + 2 ) < $bar_width_1 )
			$bar_width_1 = round( $this->average_space / ( count( $this->data ) + 2 ) );

		$n = 0;	// if only draw one bar ,$n is response to change the color
		for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			reset( $this->plotted_data[$i] );
			$bar_width = $bar_width_1 / 2;

			while( list( $x, $y ) = current( $this->plotted_data[$i] ) )
			{	
				// if only draw one bar, allocate different color to each rectangle
				if ( count( $this->data ) == 1 )
				{
					$color=$this->graph_color[$n];
					$n++;
				}
				else
				{
					$color = $this->graph_color[$i];
				}

				if ( $y < $this->x_axis )
				{
					imagefilledrectangle(
						$this->image,
						$x - $bar_width + $d, $y,
						$x + $bar_width + $d - 2,
						$this->x_axis,
						$color
					);
				}
				else
				{
					imagefilledrectangle(
						$this->image,
						$x - $bar_width + $d,
						$this->x_axis,
						$x + $bar_width + $d -2,
						$y,
						$color
					);			
				}

				next( $this->plotted_data[$i] );
			}
	
			$d = $d + $bar_width_1; 

			// if legend is defined, show it
			if ( $this->legend )
			{
				// write the description for each bar
				imagefilledrectangle(
					$this->image,
					$this->width - $this->plotarea_width + $str_space, 
					$this->top_margin - 15, 
					$this->width - $this->plotarea_width + $str_space + 10, 
					$this->top_margin - 5, 
					$this->graph_color[$i]
				);

				imagestring(
					$this->image, 
					$this->font, 
					$this->width - $this->plotarea_width  + $str_space + 20, 
					$this->top_margin -15, 
					$this->legend[$i], 
					$this->graph_color[$i]
				);

				$str_space=$str_space + $this->plotarea_width / count( $this->data );
			}
		}

		if ( $this->show_x_detail || $this->show_y_detail )
		{
			$d = 0;
	
			for ( $i = 0; $i < count( $this->data ); $i++ )
			{
				reset( $this->plotted_data[$i] );
				$bar_width = $bar_width_1 / 2;
				$ii = 0;	
				$n  = 0;	// if only draw one bar ,$n is response to change the color
		
				while ( list( $x, $y ) = current( $this->plotted_data[$i] ) )
				{	
					$y_value = $this->data[$i][$ii];
					$x_value = $this->x_labels[$ii];

					if ( $this->show_x_detail )
						$show_detail = "$x_value";
			
					if ( $this->show_y_detail )
						$show_detail = "$y_value";
			
					if ( $this->show_x_detail && $this->show_y_detail )
						$show_detail = "($x_value,$y_value)";

					// if only draw one bar, allocate different color to each rectangle
					if ( count( $this->data ) == 1 )
					{
						$color = $this->graph_color[$n];
						$n++;
					}
					else
					{
						$color = $this->graph_color[$i];
					}

					imagestring(
						$this->image,
						$this->font,
						$x - $bar_width + $d, 
						$y - $this->font * 5,
						"$show_detail",
						$color
					);
			
					$ii++;
					next( $this->plotted_data[$i] );
				}
		
				$d = $d + $bar_width_1;
			}
		}
	}

	/**
	 * Save file and free memory.
	 *
	 * @access public
	 */
	function close()
	{
		imagegif( $this->image, $this->filename );
		imagedestroy( $this->image );
	}
	
	/**
	 * Little helper
	 *
	 * @access public
	 */
	function xrange( $begin, $end, $step = 1 )
	{
		if ( $begin > $end )
		{
			if ( $step >= 0 )
			{
				return PEAR::raiseError( "'step' argument is positive or zero." );
				// exit;
			}

			for ( $i = $begin; $i >= $end; $i = $i + $step )
				$range[] = $i;
		}
		else
		{
			if ( $step <= 0 )
			{
				return PEAR::raiseError( "'step' argument is negative or zero." );
				// exit;
			}

			for ( $i = $begin; $i <= $end; $i = $i + $step )
				$range[] = round( $i );
		}

		return $range;
	}
} // END OF GraphObject

?>
