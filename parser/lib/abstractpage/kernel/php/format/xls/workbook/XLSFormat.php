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
 * @package format_xls_workbook
 */
 
class XLSFormat extends PEAR
{
	/**
	 * Constructor
	 *
	 * @param array $properties array with properties to be set on initialization.
	 */
	function XLSFormat( $index = 0,$properties = array() )
    {
		$this->xf_index       = $index;
		$this->font_index     = 0;
		$this->font           = 'Arial';
		$this->size           = 10;
		$this->bold           = 0x0190;
		$this->italic         = 0;
		$this->color          = 0x7FFF;
		$this->underline      = 0;
		$this->font_strikeout = 0;
		$this->font_outline   = 0;
		$this->font_shadow    = 0;
		$this->font_script    = 0;
		$this->font_family    = 0;
		$this->font_charset   = 0;
		$this->num_format     = 0;
		$this->hidden         = 0;
		$this->locked         = 1;
		$this->text_h_align   = 0;
		$this->text_wrap      = 0;
		$this->text_v_align   = 2;
		$this->text_justlast  = 0;
		$this->rotation       = 0;
		$this->fg_color       = 0x40;
		$this->bg_color       = 0x41;
		$this->pattern        = 0;

		$this->bottom         = 0;
		$this->top            = 0;
		$this->left           = 0;
		$this->right          = 0;

		$this->bottom_color   = 0x40;
		$this->top_color      = 0x40;
		$this->left_color     = 0x40;
		$this->right_color    = 0x40;

		// Set properties passed to XLSWorkbook::addformat()
		foreach ( $properties as $property => $value )
        {
        	if ( method_exists( $this, "set_$property" ) )
			{
				$aux = 'set_'.$property;
				$this->$aux( $value );
			}
		}
	}

	
	/**
	 * Generate an Excel BIFF XF record.
	 *
	 * @param $style The type of the XF record.
	 */
  	function get_xf( $style )
    {
    	// Set the type of the XF record and some of the attributes.
    	if ( $style == "style" )
		{
        	$style = 0xFFF5;
        }
    	else
		{
        	$style  = $this->locked;
        	$style |= $this->hidden << 1;
		}

		// Flags to indicate if attributes have been set.
		$atr_num = ( $this->num_format != 0 )? 1 : 0;
		$atr_fnt = ( $this->font_index != 0 )? 1 : 0;
		$atr_alc = ( $this->text_wrap )? 1 : 0;
		
		$atr_bdr = ( $this->bottom   ||
					 $this->top      ||
					 $this->left     ||
					 $this->right )? 1 : 0;
		
		$atr_pat = ( ( $this->fg_color != 0x40 ) ||
					 ( $this->bg_color != 0x41 ) ||
					   $this->pattern )? 1 : 0;
		
		$atr_prot = 0;

		// Zero the default border colour if the border has not been set.
		if ( $this->bottom == 0 )
        	$this->bottom_color = 0;
    
		if ( $this->top  == 0 )
        	$this->top_color = 0;
    
		if ( $this->right == 0 )
        	$this->right_color = 0;
    
		if ( $this->left == 0 )
        	$this->left_color = 0;

		$record   = 0x00E0; 					// Record identifier
		$length   = 0x0010; 					// Number of bytes to follow
                                           
		$ifnt     = $this->font_index; 			// Index to FONT record
		$ifmt     = $this->num_format; 			// Index to FORMAT record

		$align    = $this->text_h_align;		// Alignment
		$align   |= $this->text_wrap     << 3;
		$align   |= $this->text_v_align  << 4;
		$align   |= $this->text_justlast << 7;
		$align   |= $this->rotation      << 8;
		$align   |= $atr_num  << 10;
		$align   |= $atr_fnt  << 11;
		$align   |= $atr_alc  << 12;
		$align   |= $atr_bdr  << 13;
		$align   |= $atr_pat  << 14;
		$align   |= $atr_prot << 15;

		$icv      = $this->bg_color;			// fg and bg pattern colors
		$icv     |= $this->fg_color << 7;

		$fill     = $this->pattern;				// Fill and border line style
		$fill    |= $this->bottom        << 6;
		$fill    |= $this->bottom_color  << 9;

		$border1  = $this->top;					// Border line style and color
		$border1 |= $this->left      << 3;
		$border1 |= $this->right     << 6;
		$border1 |= $this->top_color << 9;

		$border2  = $this->left_color;			// Border color
		$border2 |= $this->right_color << 7;

		$header   = pack("vv", $record, $length );
		$data     = pack("vvvvvvvv", $ifnt, $ifmt, $style, $align, $icv, $fill, $border1, $border2 );
    
		return( $header . $data );
	}

	/**
	 * Generate an Excel BIFF FONT record.
	 */
  	function get_font()
    {
		$dyHeight   = $this->size * 20;		// Height of font (1/20 of a point)
		$icv        = $this->color;			// Index to color palette
		$bls        = $this->bold;			// Bold style
		$sss        = $this->font_script;	// Superscript/subscript
		$uls        = $this->underline;		// Underline
		$bFamily    = $this->font_family;	// Font family
		$bCharSet   = $this->font_charset;	// Character set
		$rgch       = $this->font;			// Font name

		$cch        = strlen( $rgch );		// Length of font name
		$record     = 0x31;					// Record identifier
		$length     = 0x0F + $cch;			// Record length
		$reserved   = 0x00;					// Reserved
		$grbit      = 0x00;					// Font attributes
    
		if ( $this->italic )
        	$grbit |= 0x02;
    
		if ( $this->font_strikeout )
        	$grbit |= 0x08;
    
		if ( $this->font_outline )
        	$grbit |= 0x10;
    
		if ( $this->font_shadow )
        	$grbit |= 0x20;

		$header = pack( "vv", $record, $length );
		$data   = pack( "vvvvvCCCCC", $dyHeight, $grbit, $icv, $bls, $sss, $uls, $bFamily, $bCharSet, $reserved, $cch );
    
		return ( $header . $data. $this->font );
	}

	/**
	 * Returns a unique hash key for a font. Used by Workbook->_store_all_fonts()
	 *
	 * The elements that form the key are arranged to increase the probability of
	 * generating a unique key. Elements that hold a large range of numbers
	 * (eg. _color) are placed between two binary elements such as _italic
	 */
  	function get_font_key()
    {
		$key  = "$this->font$this->size";
		$key .= "$this->font_script$this->underline";
		$key .= "$this->font_strikeout$this->bold$this->font_outline";
		$key .= "$this->font_family$this->font_charset";
		$key .= "$this->font_shadow$this->color$this->italic";
		$key  = str_replace( " ", "_", $key );
		
		return ( $key );
	}

	/**
	 * Returns the used by Worksheet->XF()
	 */
  	function get_xf_index()
    {
    	return ( $this->xf_index );
    }

	/**
	 * Used in conjunction with the set_xxx_color methods to convert a color
	 * string into a number. Color range is 0..63 but we will restrict it
	 * to 8..63 to comply with Gnumeric. Colors 0..7 are repeated in 8..15.
	 */
  	function _get_color( $name_color = '' )
    {
    	$colors = array(
			'aqua'    => 0x0F,
			'cyan'    => 0x0F,
			'black'   => 0x08,
			'blue'    => 0x0C,
			'brown'   => 0x10,
			'magenta' => 0x0E,
			'fuchsia' => 0x0E,
			'gray'    => 0x17,
			'grey'    => 0x17,
			'green'   => 0x11,
			'lime'    => 0x0B,
			'navy'    => 0x12,
			'orange'  => 0x35,
			'purple'  => 0x14,
			'red'     => 0x0A,
			'silver'  => 0x16,
			'white'   => 0x09,
			'yellow'  => 0x0D
		);

		// Return the default color, 0x7FFF, if undef,
    	if ( $name_color == '' )
        	return ( 0x7FFF );

    	// or the color string converted to an integer,
    	if ( isset( $colors[$name_color] ) )
        	return ( $colors[$name_color] );

    	// or the default color if string is unrecognised,
    	if ( preg_match( "/\D/", $name_color ) )
        	return ( 0x7FFF );

    	// or an index < 8 mapped into the correct range,
    	if ( $name_color < 8 )
        	return ( $name_color + 8 );

    	// or the default color if arg is outside range,
    	if ( $name_color > 63 )
        	return ( 0x7FFF );

    	// or an integer in the valid range.
    	return ( $name_color );
	}

	/**
	 * Set cell alignment.
	 */
  	function set_align( $location )
    {
    	//return if not defined $location;
    	if ( preg_match( "/\d/", $location ) )
        	return false;

    	$location = strtolower( $location );

    	if ( $location == 'left' )
        	$this->text_h_align = 1; 
    
		if ( $location == 'centre' )
        	$this->text_h_align = 2; 
    
		if ( $location == 'center' )
        	$this->text_h_align = 2; 
    
		if ( $location == 'right' )
        	$this->text_h_align = 3; 
    
		if ( $location == 'fill' )
        	$this->text_h_align = 4; 
    
		if ( $location == 'justify' )
        	$this->text_h_align = 5;
    
		if ( $location == 'merge' )
        	$this->text_h_align = 6;
    
		if ( $location == 'equal_space' )
        	$this->text_h_align = 7; 
    
		if ( $location == 'top' )
        	$this->text_v_align = 0; 
    
		if ( $location == 'vcentre' )
        	$this->text_v_align = 1; 
    
		if ( $location == 'vcenter' )
        	$this->text_v_align = 1; 
    
		if ( $location == 'bottom' )
        	$this->text_v_align = 2; 
    
		if ( $location == 'vjustify' )
        	$this->text_v_align = 3; 
    
		if ( $location == 'vequal_space' )
        	$this->text_v_align = 4; 
	}

	/**
	 * This is an alias for the unintuitive set_align('merge')
	 */
  	function set_merge()
    {
    	$this->set_align ('merge' );
    }

	/**
	 * Bold has a range 0x64..0x3E8.
	 * 0x190 is normal. 0x2BC is bold.
	 */
  	function set_bold( $weight = 1 )
    {
    	if ( !isset( $weight ) )
        	$weight = 0x2BC;  // Bold text
    
		if ( $weight == 1 )
        	$weight = 0x2BC;  // Bold text
    
		if ( $weight == 0 )
        	$weight = 0x190;  // Normal text
    
		if ( $weight <  0x064 )
        	$weight = 0x190;  // Lower bound
    
		if ( $weight >  0x3E8 )
        	$weight = 0x190;  // Upper bound
    
		$this->bold = $weight;
	}

	
	// methods for setting cell borders

	/**
	 * Sets the bottom border of the cell
	 *
	 * @param $style style of the cell border
	 */
  	function set_bottom( $style )
    {
    	$this->bottom = $style;
    }

	/**
	 * Sets the top border of the cell
	 *
	 * @param $style style of the cell border
	 */
  	function set_top( $style )
    {
    	$this->top = $style;
    }

	/**
	 * Sets the left border of the cell
	 *
	 * @param $style style of the cell border
	 */
  	function set_left( $style )
    {
    	$this->left = $style;
    }

	/**
	 * Sets the right border of the cell
	 *
	 * @param $style style of the cell border
	 */
  	function set_right( $style )
    {
    	$this->right = $style;
    }

	/**
	 * Set cells borders to the same style
	 *
	 * @param $style style of the cell border
	 */
  	function set_border( $style )
    {
    	$this->set_bottom( $style );
    	$this->set_top( $style );
    	$this->set_left( $style );
    	$this->set_right( $style );
    }


	// methods for setting cells border colors

	function set_bottom_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->bottom_color = $value;
    }

  	function set_top_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->top_color = $value;
    }

  	function set_left_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->left_color = $value;
    }

  	function set_right_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->right_color = $value;
    }

	/**
	 * Set cells border to the same color
	 *
	 * @param $color The color we are setting
	 */
  	function set_border_color( $color )
    {
    	$this->set_bottom_color( $color );
    	$this->set_top_color( $color );
    	$this->set_left_color( $color );
    	$this->set_right_color( $color );
    }

	function set_fg_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->fg_color = $value;
    }
  
  	function set_bg_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->bg_color = $value;
    }

  	function set_color( $arg )
    {
    	$value = $this->_get_color( $arg );
    	$this->color = $value;
    }

  	function set_pattern( $arg = 1 )
    {
    	$this->pattern = $arg;
    }

  	function set_underline( $underline )
    {
    	$this->underline = $underline;
    }

  	function set_size( $size )
    {
    	$this->size = $size;
    }
} // END OF XLSFormat

?>
