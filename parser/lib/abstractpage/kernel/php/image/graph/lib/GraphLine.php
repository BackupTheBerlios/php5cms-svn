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


using( 'image.graph.lib.GraphObject' );


/**
 * @package image_graph_lib
 */
 
class GraphLine extends GraphObject
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function GraphLine()
	{
		$this->GraphObject();
	}
	

	/**
	 * @access public
	 */
	function prep()
	{
    	// if $data_x is NULL, default allocate the increase data to it
    	if ( !$this->data_x )
		{
			for ( $i = 0; $i < count( $this->data ); $i++ )
			{	
				for ( $ii = 0; $ii < count( $this->data[$i] ); $ii++ )
					$this->data_x[$i][$ii] = $ii + 1;
			}
    	}

    	// check the $data_x   
    	for ( $i = 0; $i < count( $this->data_x ); $i++ )
		{
			if ( max( $this->data_x[$i] ) > $max_x_value )
				$max_x_value = max( $this->data_x[$i] );
		
			if ( count( $this->data[$i] ) != count( $this->data_x[$i] ) )
			{
				// The X axis element can not match the Y axis element!
				exit;
			}
    	}

    	// get each value of $data_x array
    	for ( $i = 0; $i < count( $this->data_x ); $i++ )
		{
			for ( $ii = 0; $ii < count( $this->data_x[$i] ); $ii++ )
			{
				$data_x_entry = current( $this->data_x[$i] );

				if ( $data_x_entry == "" )
					$data_x_entry = 0;

				$x = $this->width - $this->plotarea_width  + ( ( $this->plotarea_width - $this->average_space ) * ( $data_x_entry / $max_x_value ) );
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
				next( $this->data_x[$i] );
			}
    	}
	}

	/**
	 * @access public
	 */
	function plot()
	{
    	for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			$count = count( $this->plotted_data[$i] );
			reset( $this->plotted_data[$i] );
			$y_last = $this->x_axis;
			list( $x_last, $junk ) = current( $this->plotted_data[$i] );
			reset( $this->plotted_data[$i] );
	
			for ( $ii = 0; $ii < $count; $ii++ )
			{
				$x_value = $this->data_x[$i][$ii];
				$y_value = $this->data[$i][$ii];
				list( $x_next, $y_next ) = current( $this->plotted_data[$i] );
		
				imageline(
					$this->image,
					$x_next,
					$y_next,
					$x_last,
					$y_last,
					$this->graph_color[$i]
				);
		
				imagestring(
					$this->image,
					$this->font,
					$x_next - $this->font,
					$y_next - $this->font * $this->font,
					".",
					$this->graph_color[$i]
				);
		
				$x_last = $x_next;
				$y_last = $y_next;

		 		// whether to show detail of each dot on the line chart
				if ( $this->show_x_detail || $this->show_y_detail )
				{
					if ( $this->show_x_detail )
						$show_detail = "X=$x_value";
			
					if ( $this->show_y_detail )
						$show_detail = "Y=$y_value";
			
					if ( $this->show_x_detail && $this->show_y_detail )
						$show_detail = "($x_value,$y_value)";
			
					imagestring(
						$this->image,
						$this->font,
						$x_next + 1,
						$y_next + 1,
						"$show_detail",
						$this->graph_color[$i]
					);
				}
		
				next( $this->plotted_data[$i] );
			}

			imageline(
				$this->image,
				$x_last,
				$y_last,
				$x_next,
				$this->x_axis,
				$this->graph_color[$i]
			);

    		// if legend is defined,show it
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

				$str_space = $str_space + $this->plotarea_width / count( $this->data );
			}
    	}
	}
} // END OF GraphLine

?>
