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
 
class GraphDot extends GraphObject
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function GraphDot()
	{
		$this->GraphObject();
	}
	

	/**
	 * @access public
	 */
	function init()
	{
		for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			if ( strlen( $this->data[$i] ) > $longest_value )
				$longest_value = strlen( $this->data[$i] );
		
			if ( strlen( $this->x_labels[$i] ) > $longest_label )
				$longest_label = strlen( $this->x_labels[$i] );
		}

		$str_range = 100 + ( 6 + $longest_label + $longest_value ) * $this->font;
	
		if ( $this->diameter > ( $this->width - $str_range ) * 4 / 3 || !$this->diameter )
		{
			if ( ( $this->width - $str_range ) > $this->height - $this->font_title )
				$smaller = $this->height - $this->font_title;
			else
				$smaller = $this->width - $str_range;
		
			$this->diameter = $smaller * 2 / 3;
		}

		if ( $this->x_centre > $this->width - $str_range - $this->diameter * 3 / 4 || $this->x_centre < $this->diameter * 3 / 4 )
			$this->x_centre = ( $this->width - $str_range ) / 2;
	
		if ( $this->y_centre > $this->height - $this->diameter * 3 / 4 || $this->y_centre < $this->diameter * 3 / 4 + $this->font_title )
			$this->y_centre = ( $this->height - $this->font_title ) / 2;

		// draw background
		imagefilledrectangle(
			$this->image,
			0,
			0,
			$this->width - 1,
			$this->height - 1,
			$this->background_color
		);

		// draw the roundness border_color
		imagearc(
			$this->image, 
			$this->x_centre, 
			$this->y_centre, 
			$this->diameter, 
			$this->diameter, 
			0, 
			360,  
			$this->border_color
		);

		imagestring(
			$this->image, 
			$this->font_title,
			$this->width / 2 - strlen( $this->title_label ) * $this->font_title,
			$this->font_title,	
			$this->title_label, 
			$this->title_color
		);

		// sum the array data
		for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			$total = current( $this->data ) + $total;
			next( $this->data );
		}

		reset( $this->data );
		$round = 2 * pi();
	
		for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			$cornu  = current( $this->data ) / $total * $round;
			$cornu2 = $cornu2 + $cornu;
			$cornu  = $cornu2;
			$half_cornu  = current( $this->data ) / $total * $round / 2;
			$color_cornu = $cornu2 - $half_cornu;
			
			$dx 		= abs( $this->diameter * cos( $cornu ) / 2 );
			$dy 		= abs( $this->diameter * sin( $cornu ) / 2 );
			$color_dx	= abs( $this->diameter * cos( $color_cornu ) / 4 );
			$color_dy	= abs( $this->diameter * sin( $color_cornu ) / 4 );
			$detail_dx	= abs( $this->diameter * cos( $color_cornu ) * 3 / 5 );
			$detail_dy	= abs( $this->diameter * sin( $color_cornu ) * 3 / 5 );

			// first quadrant
			if ( $cornu <= 1 / 4 * $round )
				$dy =-$dy;
		
			if ( $color_cornu <= 1 / 4 * $round )
			{
				$color_dy  =-$color_dy;
				$detail_dy =-$detail_dy;
			}

			// second quadrant
			if ( $cornu <= 1 / 2 * $round && $cornu > 1 / 4 * $round )
			{
				$dy =-$dy;
				$dx =-$dx;
			}
		
			if ( $color_cornu <= 1 / 2 * $round && $color_cornu > 1 / 4 * $round )
			{
				$color_dx  =-$color_dx;
				$color_dy  =-$color_dy;
				$detail_dx =-$detail_dx;
				$detail_dy =-$detail_dy;
			}

			// third quadrant
			if ( $cornu <= 3 / 4 * $round && $cornu > 1 / 2 * $round )
				$dx =-$dx;
		
			if ( $color_cornu <= 3 / 4 * $round && $color_cornu > 1 / 2 * $round )
			{
				$color_dx  =-$color_dx;
				$detail_dx =-$detail_dx;
			}

			// separate the round by lines
			imageline(
				$this->image, 
				$this->x_centre, 
				$this->y_centre, 
				$this->x_centre + $dx, 
				$this->y_centre + $dy, 
				$this->border_color
			);

			// save the color coordinate
			$this->color_coordinate[$i][0] = $this->x_centre + $color_dx;
			$this->color_coordinate[$i][1] = $this->y_centre + $color_dy;
			$detail_dx_array[$i] = $detail_dx;
			$detail_dy_array[$i] = $detail_dy;
		
			next( $this->data );
		}

		// fill in the color and write the description for each partition
		reset( $this->data );
		$dd = $this->dd;
	
		for ( $i = 0; $i < count( $this->data ); $i++ )
		{
			// fill in the color for each partition
			ImageFillToborder(
				$this->image,
				$this->color_coordinate[$i][0],
				$this->color_coordinate[$i][1],
				$this->border_color,
				$this->graph_color[$i]
			);

			// write the description for each partition
			imagefilledrectangle(
				$this->image,
				$this->x_centre + $this->diameter - 20, 
				$this->y_centre - $this->diameter / 2 + $dd, 
				$this->x_centre + $this->diameter - 10, 
				$this->y_centre - $this->diameter / 2 + $dd + 10, 
				$this->graph_color[$i]
			);

			$percent = round( current( $this->data ) / $total * 100 * 100 ) / 100;
			$current_data = current( $this->data );
		
			if ( $this->x_labels )
				$current_data_label = current( $this->x_labels );

			imagestring(
				$this->image, 
				$this->font, 
				$this->x_centre + $this->diameter, 
				$this->y_centre - $this->diameter / 2 + $dd, 
				"$current_data_label $current_data $percent%", 
				$this->graph_color[$i]
			);

			// if needed, show the detail for each partition
			if ( $this->show_x_detail || $this->show_y_detail )
			{
				imageline(
					$this->image, 
					$this->color_coordinate[$i][0],
					$this->color_coordinate[$i][1], 
					$this->x_centre+$detail_dx_array[$i], 
					$this->y_centre+$detail_dy_array[$i], 
					$this->graph_color[$i]
				);

				imagestring(
					$this->image, 
					$this->font, 
					$this->x_centre + $detail_dx_array[$i], 
					$this->y_centre + $detail_dy_array[$i], 
					"$current_data", 
					$this->graph_color[$i]
				);
			}

			$dd = $dd + 20;	
			next( $this->data );

			if ( $this->x_labels )
				next( $this->x_labels );
		}
	}

	/**
	 * @access public
	 */
	function draw()
	{
		$this->init();
		$this->close();
	}
} // END OF GraphDot

?>
