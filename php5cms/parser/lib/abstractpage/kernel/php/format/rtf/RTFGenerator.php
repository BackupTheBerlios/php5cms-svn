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
 * @package format_rtf
 */
 
class RTFGenerator extends PEAR
{
	/**
	 * @access public
	 */
	var $pg_width;
	
	/**
	 * @access public
	 */
	var $pg_height;
	
	/**
	 * @access public
	 */
	var $mar_left;
	
	/**
	 * @access public
	 */
	var $mar_right;
	
	/**
	 * @access public
	 */
	var $mar_top;
	
	/**
	 * @access public
	 */
	var $mar_bott;

	/**
	 * @access public
	 */
	var $image_size;
	
	/**
	 * @access public
	 */
	var $header_align;
	
	/**
	 * @access public
	 */
	var $footer_align;
	
	/**
	 * @access public
	 */
	var $head_y;
	
	/**
	 * @access public
	 */
	var $foot_y;
	
	/**
	 * @access public
	 */
	var $page_numbers;
	
	/**
	 * @access public
	 */
	var $page_numbers_valign;
	
	/**
	 * @access public
	 */
	var $page_numbers_align;
	
	/**
	 * @access public
	 */
	var $font_face;
	
	/**
	 * @access public
	 */
	var $font_size;
	
	/**
	 * @access public
	 */
	var $def_par_before;
	
	/**
	 * @access public
	 */
	var $def_par_after;
	
	/**
	 * @access public
	 */
	var $def_par_align;
	
	/**
	 * @access public
	 */
	var $def_par_lines;
	
	/**
	 * @access public
	 */
	var $def_par_lindent;
	
	/**
	 * @access public
	 */
	var $def_par_rindent;
	
	/**
	 * @access public
	 */
	var $def_par_findent;
	
	/**
	 * @access public
	 */
	var $tbl_def_border;
	
	/**
	 * @access public
	 */
	var $tbl_def_width;
	
	/**
	 * @access public
	 */
	var $tbl_def_align;
	
	/**
	 * @access public
	 */
	var $tbl_def_valign;
	
	/**
	 * @access public
	 */
	var $tbl_def_bgcolor;
	
	/**
	 * @access public
	 */
	var $row_def_align;
	
	/**
	 * @access public
	 */
	var $img_def_border;
	
	/**
	 * @access public
	 */
	var $img_def_src;
	
	/**
	 * @access public
	 */
	var $img_def_width;
	
	/**
	 * @access public
	 */
	var $img_def_height;
	
	/**
	 * @access public
	 */
	var $img_def_left;
	
	/**
	 * @access public
	 */
	var $img_def_top;
	
	/**
	 * @access public
	 */
	var $img_def_space;
	
	/**
	 * @access public
	 */
	var $img_def_align;
	
	/**
	 * @access public
	 */
	var $img_def_wrap;
	
	/**
	 * @access public
	 */
	var $img_def_anchor;

	/**
	 * @access public
	 */
	var $h_link_fontf;
	
	/**
	 * @access public
	 */
	var $h_link_fonts;
	
	/**
	 * @access public
	 */
	var $h_link_fontd;
	
	/**
	 * @access public
	 */
	var $tr_hd_mass;
	
	/**
	 * @access public
	 */
	var $tb_wdth = 0;

	/**
	 * @access public
	 */
	var $header = "";
	
	/**
	 * @access public
	 */
	var $text = "";
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function RTFGenerator()
	{
		$slash = "--%345pag1223%--";

		$this->pg_width  = $this->twips( 210 ); // page width (mm)
		$this->pg_height = $this->twips( 297 ); // page height (mm)
		$this->mar_left  = $this->twips( 16  ); // left margin width (mm)
		$this->mar_right = $this->twips( 16  ); // right margin width (mm)
		$this->mar_top   = $this->twips( 19  ); // top margin height (mm)
		$this->mar_bott  = $this->twips( 19  );	// bottom margin height (mm)

		$this->header_align = "right";			// default header align - "left", "right", "center"
		$this->footer_align = "left";			// default footer align - "left", "right", "center"

		$this->image_size = 500;				// maximum allowed image size in kb

		$this->head_y = $this->twips( 5  );		// space between the top edge of the page and the top header (mm)
		$this->foot_y = $this->twips( 10 );		// space between the bottom edge of the page and the bottom footer (mm)
		
		$this->page_numbers        = 1;			// page numbers, if  < 0 - no page numbers; if >= 0 page numbers start from the specified number
		$this->page_numbers_valign = "bottom";	// vertical alignment of the page numbers ("top" or "bottom")
		$this->page_numbers_align  = "center";	// horisontal alignment of the page numbers ("left", "center", "right")
	 
		$this->font_face           = "arial";	// default font face [arial,roman,sym,courier,seriff,garamond]
		$this->font_size           = "12";		// font size in pt
		$this->def_par_before      = "0";		// space before paragraph (mm) (better set to 0, when using tables and set by tag)
		$this->def_par_after       = "0";		// space after paragraph (mm) (better set to 0, when using tables and set by tag)
		$this->def_par_align       = "left";	// default paragraph align ("left", "right", "center", "justify")
		$this->def_par_lines       = 0;			// space between lines (mm). if 0 - minimal is taken
		$this->def_par_lindent     = "0";		// paragraph left indent (mm) (better set by tag)
		$this->def_par_rindent     = "0";		// paragraph right indent (mm) (better set by tag)
		$this->def_par_findent     = "0";		// first line indent (mm) (better set by tag)

		$this->tbl_def_border      = 1;			// table border (1 - yes; 0 - no) or string value:
												// "t,b,r,l" - means: top,bottom,right,left borders
												// you can vary these letters to get the result you need
		$this->tbl_def_width       = "100%";	// table width (in mm or in % from page width)
		$this->tbl_def_cellpadding = 2;			// default cellpadding (mm)
		$this->tbl_def_align       = "center";	// default table align on the page (center, right, left)
		$this->tbl_def_valign      = "middle";	// default vertical text align for all the cells (top, middle, bottom)
		$this->tbl_def_bgcolor     = "0";		// table background (0 - no, or % from black)
		$this->row_def_align       = "center";	// default horizontal text align for all the cells (center, right, left)
					 
		$this->img_def_border      = 0;			// image border (1 - yes; 0 - no)
		$this->img_def_src         = "";		// default image src (used when no, or a bad source specified)
		$this->img_def_width       = 20;		// default image width (mm)
		$this->img_def_height      = 20;		// default image height (mm)
		$this->img_def_left        = 0;			// space between the anchor and image left edge (mm)
		$this->img_def_top         = 0;			// space between the anchor and image top edge (mm)
		$this->img_def_space       = 1;			// space between the image and the text (mm)
		$this->img_def_align       = "left";	// image align on the page (center, right, left)
		$this->img_def_wrap        = "around";	// type of text wrapping for image (no, updown, around)
		$this->img_def_anchor      = "par";		// linking anchor for image
												// para   = image is linked to the current paragraph
												// page   = image is linked to the current page (top left page corner)
												// margin = image is linked to margin (top left page corner including margins)

		$this->h_link_fontf = "garamond";		// default link font face [arial,roman,sym,courier,seriff,garamond]
		$this->h_link_fonts = "12";				// default link font size in pt
		$this->h_link_fontd = "i";				// default link decoration [ul - underline, i - italic, b - old]
		
		$hlink = $this->get_rtf_color( preg_replace( "/\#/","", "#009900" ) ); // default color for hyper links

		// sets default character set 
		$default_charset = 0;
		/*
		ANSI				= 0
		Default				= 1
		Symbol				= 2
		Invalid				= 3
		Mac					= 77
		Shift Jis			= 128
		Hangul				= 129
		Johab				= 130
		GB2312				= 134
		Big5				= 136
		Greek				= 161
		Turkish				= 162
		Vietnamese			= 163
		Hebrew				= 177
		Arabic				= 178
		Arabic Traditional	= 179
		Arabic user			= 180
		Hebrew user			= 181
		Baltic				= 186
		Russian				= 204
		Thai				= 222
		238Eastern European	= 238
		PC 437				= 254
		OEM					= 255
		*/
		
		
		$this->$header = "{\\rtf1\\ansi\\deff0\\deftab720

{\\fonttbl
{\\f0\\fnil MS Sans Serif;}
{\\f1\\froman\\fcharset2 Symbol;}
{\\f2\\fswiss\\fprq2\\fcharset".$default_charset."{\\*\\fname Arial;}Arial;}
{\\f3\\froman\\fprq2\\fcharset".$default_charset."{\\*\\fname Times New Roman;}Times New Roman;}
{\f4\fmodern\fcharset".$default_charset."\fprq1{\*\panose 02070309020205020404}Courier New;}
{\f5\fswiss\fcharset".$default_charset."\fprq2{\*\panose 020b0604020202020204}Microsoft Sans Serif;}
{\f6\froman\fcharset".$default_charset."\fprq2{\*\panose 02020404030301010803}Garamond;}
{\f7\froman\fcharset".$default_charset."\fprq2{\*\panose 02020404030301010999}Verdana;}
{\f8\froman\fcharset".$default_charset."\fprq2{\*\panose 02020404030301010888}Courier;}
}

{\\colortbl;
\\red0\\green0\\blue0;
\\red0\\green0\\blue255;
\\red0\\green255\\blue255;
\\red0\\green255\\blue0;
\\red255\\green0\\blue255;
\\red255\\green0\\blue0;
\\red255\\green255\\blue0;
\\red255\\green255\\blue255;
\\red0\\green0\\blue128;
$hlink
goesuserscolors
}


{\\info
{\\title Abstractpage}
{\\author Abstractpage}
{\\operator info@docuverse.de}
}\r\n";

		$this->$header .= "\\" . $this->font( $this->font_face ) . "\\fs" . ( $this->font_size * 2 ) . "\r\n";
		$this->$header .= "\\paperw" . $this->pg_width . "\\paperh" . $this->pg_height . "\\margl" . $this->mar_left . "\\margr" . $this->mar_right . "\\margt" . $this->mar_top . "\\margb" . $this->mar_bott . "\\headery" . $this->head_y . "\\footery" . $this->foot_y . "\r\n";

		if ( $this->page_numbers >= 0 )
		{
			$pgn_y = ( $this->page_numbers_valign == "top" )? $this->head_y : $this->pg_height - $this->foot_y;

			switch ( $this->page_numbers_align )
			{
				case "center":
					$pgn_x = round( $this->pg_width / 2 );
					break;
				
				case "right":
					$pgn_x = $this->mar_left; 
					break;
				
				case "left": 
					$pgn_x = $this->pg_width - $this->mar_right; 
					break;
			}
			
			$this->$header .= "\\pgncont\\pgnx" . $pgn_x . "\\pgny" . $pgn_y . "\\pgndec\\pgnstarts" . $this->page_numbers . "\\pgnrestart \r\n";
		}
	} 
	

	/**
	 * @access public
	 */
	function def_par()
	{
		$before  = "\\sb" . $this->twips( $this->def_par_before  );
		$after   = "\\sa" . $this->twips( $this->def_par_after   );
		$align   = "\\q"  . $this->def_par_align;
		$lines   = "\\sl" . $this->twips( $this->def_par_lines   );
		$lindent = "\\li" . $this->twips( $this->def_par_lindent );
		$rindent = "\\ri" . $this->twips( $this->def_par_rindent );
		$findent = "\\fi" . $this->twips( $this->def_par_findent );
		
		return $before . $after . $align . $lines . $lindent . $rindent . $findent;
	}

	/**
	 * @access public
	 */	
	function font( $font )
	{
		switch ( strtolower( $font ) )
		{
			case "sym":
				$perm = "f1 "; 
				break;
				
			case "symbol": 
				$perm = "f1 "; 
				break;
			
			case "arial": 
				$perm = "f2 "; 
				break;
				
			case "roman":
				$perm = "f3 "; 
				break;
			
			case "courier": 
				$perm = "f4 "; 
				break;
				
			case "seriff": 
				$perm = "f5 "; 
				break;
				
			case "garamond": 
				$perm = "f6 "; 
				break;
				
			case "verdana": 
				$perm = "f7 ";
				break;
			
			case "cur": 
				$perm = "f8 "; 
				break;
			
			case "roman_czech": 
				$perm = "f28 "; 
				break;
		}
		
		return $perm;
	}
	
	/**
	 * @access public
	 */
	function get_align( $variable )
	{
		switch ( strtolower( $variable ) )
		{
			case "center": 
				$variable = "c"; 
				break;
			
			case "left": 
				$variable = "l"; 
				break;
			
			case "right": 
				$variable = "r"; 
				break;
			
			case "justify": 
				$variable = "j"; 
				break;
		}
		
		return $variable;
	}
	
	/**
	 * @access public
	 */
	function twips( $num )
	{
		$twp = 564;
		$num = $num / 10;
		$sum = round( $twp * $num );
		
		return $sum;
	} 
	
	/**
	 * @access public
	 */
	function get_rtf()
	{
		$rtf = $this->$header . "\r\n" . $this->text . "\r\n}";
		// $rtf = preg_replace( "/[\r\n]+/", " ", $rtf );
		
		return $rtf;
	}
	
	/**
	 * @access public
	 */
	function r_encode( $string )
	{
		return rawurlencode( $string );
	}

	/**
	 * @access public
	 */
	function toCyr( $string )
	{
		$fig_l  = "pag456pag";
		$fig_r  = "pag654pag";
		$slash  = "pp345pag1223pp";
		$star   = "pp346pag1224pp"; // *
		$quote  = "pp375pag1225pp"; // "
		$prgrf  = $slash."";
		$string = preg_replace( "/[\r\n\t]+/", "", $string );

		if ( preg_match_all( "/(<font)(.*?)(>)/msi", $string, $fonts ) )
		{
			$text   = preg_split( "/(<font)(.*?)(>)/msi", $string );
			$fonts  = $fonts[2];
			$string = "";
			
			for ( $i = 0; $i < sizeof( $text ); $i++ )
			{
				parse_str( strtolower( ereg_replace( " +", "&", trim( $fonts[$i] ) ) ) );
				$perms = "";
					
				if ( isset( $face ) )
				{
					$perms .= $slash . $this->font( $face );
					unset( $face );
				}
				
				if ( isset( $size ) )
				{
					$size   = $size * 2;
					$perms .= $slash . "fs" . $size . " ";
					unset( $size );
				}
				
				if ( isset( $color ) )
				{
					$perms .= $slash . "cf" . $color . " "; 
					unset( $color );
				}
				
				if ( $perms == "" )
					$rer = "";
				else
					$rer = $fig_l . $perms;
					
				$string .= $text[$i] . $rer;
			}
			
			$string = eregi_replace( "</font>", $fig_r, $string );
		}

		if ( preg_match_all( "/(<a)(.*?)(>)/msi", $string, $links ) )
		{
			$text   = preg_split( "/(<a)(.*?)(>)/msi", $string );
			$links  = $links[2];
			$string = "";
			
			for ( $i = 0; $i < sizeof( $text ); $i++ )
			{
				unset( $local, $alt );
				$tmp = preg_match( "/(alt=\")([^\"]*)(\")/", trim( $links[$i] ), $all );
				parse_str( strtolower( ereg_replace( " +", "&", trim( $links[$i] ) ) ) );
				
				$local  = preg_replace( "/\"/", "", $local );
				$alt    = "";
				$alt    = $all[2];
				$perms  = "";
				$perms .= $fig_l . $slash . "field" . $fig_l . $slash . $star . $slash . "fldinst  ";
				
				if ( $local )
					$perms .= "HYPERLINK  " . $slash . $slash . "l " . $quote . $local . $quote . " ";
				else if ($file)
					$perms .= "HYPERLINK  " . $quote . $file  . $quote . " ";
					
				// $perms .= $slash . $slash . "o " . $quote . $alt . $quote;
				$perms .= $fig_r;
				$perms .= $fig_l . $slash . "fldrslt ";
				
				if ( isset( $def ) )
				{
					$perms .= $slash . $this->font( $this->h_link_fontf ) . $slash . "fs" . ( $this->h_link_fonts * 2 ) . $slash . "cf10 " . $slash . $this->h_link_fontd . " ";
					unset( $def );
				}
				
				$slash . $this->font( $this->h_link_fontf ) . $slash . "fs" . ( $this->h_link_fonts * 2 ) . $slash . "cf10 " . $slash . $this->h_link_fontd . " ";
					
				$string .= ( $i < sizeof( $text ) - 1 )? $text[$i] . $perms : $text[$i];
				unset( $perms );
			}
				
			$string = eregi_replace( "</a>", $slash . "cf0" . $fig_r . $fig_r, $string );
		}
			
		$target_s  = $fig_l . $slash . $star . $slash . "bkmkstart ";
		$target_e  = $fig_l . $slash . $star . $slash . "bkmkend ";
		$string    = preg_replace( "/(<id )([^>]*)(>)/msi", $target_s . "\\2" . $fig_r . $target_e . "\\2" . $fig_r, $string );

		$d_before  = $slash  . "sb" . $this->twips( $this->def_par_before );
		$d_after   = $slash  . "sa" . $this->twips( $this->def_par_after  );
		$d_align   = $slash  . "q"  . $this->get_align( $this->def_par_align );
		$d_lines   = $slash  . "sl" . $this->twips($this->def_par_lines   );
		$d_lindent = $slash  . "li" . $this->twips($this->def_par_lindent );
		$d_rindent = $slash  . "ri" . $this->twips($this->def_par_rindent );
		$d_findent = $slash  . "fi" . $this->twips($this->def_par_findent );
		$d_def_par = $before . $after . $align . $lines . $lindent . $rindent . $findent;

		if ( preg_match_all( "/(<p)(.*?)(>)/msi", $string, $pars ) )
		{
			$text   = preg_split( "/(<p)(.*?)(>)/msi", $string );
			$pars   = $pars[2];
			$string = "";
				
			for ( $i = 0; $i < sizeof( $text ); $i++ )
			{
				parse_str( strtolower( ereg_replace( " +", "&", trim( $pars[$i] ) ) ) );
					
				switch ( strtolower( $align ) )
				{
					case "center": 
						$align = "c"; 
						break;
						
					case "left": 
						$align = "l"; 
						break;
						
					case "right": 
						$align = "r"; 
						break;
						
					case "justify": 
						$align = "j"; 
						break;
				}
					
				if ( isset( $before ) )
				{
					$f_before = $slash . "sb" . $this->twips( $before ); 
					unset( $before );
				}
				else 
				{
					$f_before = $d_before;
				}
					
				if ( isset( $after ) )
				{
					$f_after = $slash . "sa" . $this->twips( $after ); 
					unset( $after );
				}
				else 
				{
					$f_after = $d_after;
				}
					
				if ( isset( $align ) ) 
				{ 
					$f_align = $slash . "q" . $align; 
					unset( $align ); 
				}
				else 
				{
					$f_align = $d_align;
				}
					
				if ( isset( $lines ) ) 
				{ 
					$f_lines = $slash . "sl" . $this->twips( $lines ); 
					unset( $lines ); 
				}
				else 
				{
					$f_lines = $d_lines;
				}
					
				if ( isset( $lindent ) ) 
				{
					$f_lindent = $slash . "li" . $this->twips( $lindent ); 
					unset( $lindent );
				}
				else 
				{
					$f_lindent = $d_lindent;
				}
					
				if ( isset( $rindent ) ) 
				{
					$f_rindent = $slash . "ri" . $this->twips( $rindent ); 
					unset( $rindent );
				}
				else 
				{
					$f_rindent = $d_rindent;
				}
					
				if ( isset( $findent ) ) 
				{
					$f_findent = $slash . "fi" . $this->twips( $findent ); 
					unset( $findent );
				}
				else 
				{
					$f_findent = $d_findent;
				}

				if ( isset( $talign ) )
				{
					$f_talign_ar = preg_split( "/[,. ]/", preg_replace( "/['\"]/", "", $talign ) );
					unset( $talign );
				}
					
				if ( isset( $lead ) )
				{
					$f_lead_ar = preg_split( "/[,. ]/", preg_replace( "/['\"]/", "", $lead ) );
					unset( $lead );
				}
				else 
				{
					$f_lead = "";
				}
					
				if ( isset( $tsize ) ) 
				{
					$f_tsize_ar = preg_split( "/[,. ]/", preg_replace( "/['\"]/", "", $tsize ) );
					unset( $tsize );
				}
				else 
				{
					$f_tsize = "";
				}
					
				for ( $ll = 0; $ll < sizeof( $f_tsize_ar ); $ll++ )
				{
					if ( $f_tsize_ar[$ll] != "" )
					{
						$f_tsize_ar[$ll] = ( $f_tsize_ar[$ll] )? $f_tsize_ar[$ll] : 10;

						switch ( $f_talign_ar[$ll] )
						{
							case "right": 
								$talign_tmp = $slash . "tqr"; 
								break;
									
							case "center": 
								$talign_tmp = $slash . "tqc"; 
								break;
									
							case "decimal": 
								$talign_tmp = $slash . "tqdec"; 
								break;
						}
							
						$f_tabs .= $slash . "tl" . $f_lead_ar[$ll] . $talign_tmp . $slash . "tx" . $this->twips( $f_tsize_ar[$ll] );
					}
				}
					
				$f_par = $f_before . $f_after . $f_align . $f_lines . $f_lindent . $f_rindent . $f_findent . $f_tabs;
				unset( $f_tabs, $f_tsize, $f_lead );

				if ( $text[$i] == "" ) 
					$tyu = $slash . "pard";
				else 
					$tyu = $slash . "par" . $slash . "pard";
					
				$string .= $text[$i];
					
				if ( $i < sizeof( $text ) - 1 ) 
					$string .= $tyu . $f_par . " ";
			}
		}

		// section handle
		if ( preg_match_all( "/(<new section)(.*?)(>)/msi", $string, $pars ) )
		{
			$text = preg_split( "/(<new section)(.*?)(>)/msi", $string );
			$pars = $pars[2];
			// $string = "";
				
			for ( $i = 0; $i < sizeof( $text ); $i++ )
			{
				$f_sect = $slash . "sect \r\n ";
				parse_str( strtolower( ereg_replace( " +", "&", trim( $pars[$i] ) ) ) );
					
				if ( $nobreak )
					$f_sect .= $slash . "sbknone ";
						
				if ( $columns )
					$f_sect .= $slash . "cols" . $columns . " ";
				else
					$f_sect .= $slash . "cols1 ";
					
				if ( isset( $landscape ) && !isset( $portrait ) )
				{
					$f_sect .= $slash . "lndscpsxn";
					$f_sect .= $slash . "pghsxn"   . $this->pg_width;
					$f_sect .= $slash . "pgwsxn"   . $this->pg_height;
					$f_sect .= $slash . "marglsxn" . $this->mar_left;
					$f_sect .= $slash . "margrsxn" . $this->mar_right;
					$f_sect .= $slash . "margtsxn" . $this->mar_top;
					$f_sect .= $slash . "margbsxn" . $this->mar_bott;
				}
					
				if ( !isset( $landscape ) && isset( $portrait ) )
				{
					$f_sect .= $slash . "lndscpsxn";
					$f_sect .= $slash . "pgwsxn"   . $this->pg_width;
					$f_sect .= $slash . "pghsxn"   . $this->pg_height;
					$f_sect .= $slash . "marglsxn" . $this->mar_left;
					$f_sect .= $slash . "margrsxn" . $this->mar_right;
					$f_sect .= $slash . "margtsxn" . $this->mar_top;
					$f_sect .= $slash . "margbsxn" . $this->mar_bott;
				}
					
				if ( $pn_start )
				{
					switch ( strtolower( $pn_align ) )
					{
						case "center": 
							$pgn_x = round( $this->pg_width / 2 ); 
							break;
							
						case "right": 
							$pgn_x = $this->mar_left; 
							break;
							
						case "left": 
							$pgn_x = $this->pg_width - $this->mar_right; 
							break;
					}
						
					$pgn_y = ( strtolower( $pn_valign ) == "top" )? $this->head_y : $this->pg_height - $this->foot_y;
					// $f_sect = $slash."sect \r\n ";
					// if ( $nobreak ) {$f_sect .= $slash . "sbknone";}
					$f_sect .= $slash . "pgncont" . $slash . "pgnx" . $pgn_x . $slash . "pgny" . $pgn_y . $slash . "pgndec" . $slash . "pgnstarts" . $pn_start . $slash . "pgnrestart \r\n";
					$string  = preg_replace( "/(<new section)(" . $pars[$i] . ")(>)/msi", $f_sect,$string );
				}
				else
				{
					// $f_sect = $slash."sect \r\n";
					// if ( $nobreak ) {$f_sect .= $slash . "sbknone";}
					$string = preg_replace( "/(<new section)(" . $pars[$i] . ")(>)/msi", $f_sect, $string );
				}
				
				unset( $nobreak, $columns, $landscape, $portrait, $pn_start );
			}
		}

		$string = eregi_replace( "\r", "", $string );
		$string = eregi_replace( "\n", "", $string );
		$string = eregi_replace( "<u>", "<U>", $string );
		$string = eregi_replace( "<U>", $slash . "ul ", $string );
		$string = eregi_replace( "</u>", "</U>", $string );
		$string = eregi_replace( "</U>", $slash . "ul0 ", $string );
		$string = eregi_replace( "<i>", "<I>", $string );
		$string = eregi_replace( "<I>", $slash . "i ", $string );
		$string = eregi_replace( "</i>", "</I>", $string );
		$string = eregi_replace( "</I>", $slash . "i0 ", $string );
		$string = eregi_replace( "<b>", "<B>", $string );
		$string = eregi_replace( "<B>", $slash . "b ", $string );
		$string = eregi_replace( "</b>", "</B>", $string );
		$string = eregi_replace( "</B>", $slash . "b0 ", $string );
		$string = eregi_replace( "<strong>", "<STRONG>", $string );
		$string = eregi_replace( "<STRONG>", $slash . "b ", $string );
		$string = eregi_replace( "</strong>", "</STRONG>", $string );
		$string = eregi_replace( "</STRONG>", $slash . "b0 ", $string );
		$string = eregi_replace( "<br>", "<BR>", $string );
		$string = eregi_replace( "<BR>", " " . $slash . "line ", $string );
		$string = eregi_replace( "<sup>", "<SUP>", $string );
		$string = eregi_replace( "<SUP>", $fig_l . $slash . "super ", $string );
		$string = eregi_replace( "</sup>", "</SUP>", $string );
		$string = eregi_replace( "</SUP>", $fig_r, $string );
		$string = eregi_replace( "<sub>", "<SUB>", $string );
		$string = eregi_replace( "<SUB>", $fig_l . $slash . "sub ", $string );
		$string = eregi_replace( "</sub>", "</SUB>", $string );
		$string = eregi_replace( "</SUB>", $fig_r, $string );
		$string = eregi_replace( "<new page>", " " . $slash . "page ", $string );
		$string = eregi_replace( "<header>", "<HEADER>", $string );
		$string = eregi_replace( "<HEADER>", $fig_l . $slash . "header " . $slash . "pard" . $slash . "plain " . $slash . "q" . $this->get_align( $this->header_align ) . " " . $fig_l, $string );
		$string = eregi_replace( "</header>", "</HEADER>", $string );
		$string = eregi_replace( "</HEADER>", $fig_r . $fig_l . $slash . "par " . $fig_r . $fig_r, $string );
		$string = eregi_replace( "<footer>", "<FOOTER>", $string );
		$string = eregi_replace( "<FOOTER>", $fig_l . $slash . "footer " . $slash . "pard" . $slash . "plain " . $slash . "q" . $this->get_align( $this->footer_align ) . " " . $fig_l, $string );
		$string = eregi_replace( "</footer>", "</FOOTER>", $string );
		$string = eregi_replace( "</FOOTER>", $fig_r . $fig_l . $slash . "par " . $fig_r . $fig_r, $string );
		$string = eregi_replace( "<tab>", "<TAB>", $string );
		$string = eregi_replace( "<TAB>", $slash . "tab ", $string );
		$string = eregi_replace( "<hr>", "<HR>", $string );
		$string = eregi_replace( "<HR>", $slash . "brdrb" . $slash . "brdrs" . $slash . "brdrw15" . $slash . "brsp20  " . $slash . "par" . $slash . "pard ", $string );

		$string = preg_replace( "/&#([0-9]+)/e", "chr('\\1')",      $string );
		$string = preg_replace( "/&#U([0-9]+)/", $slash . "u\\1  ", $string );

		$fin = rawurlencode( $string );
		$fin = ereg_replace( "%20",  " ",  $fin );
		$fin = ereg_replace( "%",    "\'", $fin );
		$fin = ereg_replace( "\'5C", "\\", $fin );
		$fin = ereg_replace( $slash, "\\", $fin );
		$fin = ereg_replace( $fig_l, "{",  $fin );
		$fin = ereg_replace( $fig_r, "}",  $fin );
		$fin = ereg_replace( $star,  "*",  $fin );
		$fin = ereg_replace( $quote, "\"", $fin );
		$fin = $fin;

		return $fin;
	}
	
	/**
	 * @access public
	 */
	function add_text( $string )
	{
		$this->text .= $this->toCyr( $string );
	}

	/**
	 * @access public
	 */
	function par()
	{
		$this->text .= "\\par\r\n";
	}

	/**
	 * @access public
	 */
	function add_tbl( $tar, $flg = 0, $brd = 1, $bld = 1, $hlt = 1 )
	{
		$p   = ( $this->pg_width - ( $this->mar_left + $this->mar_right ) ) / 100;
		$ftb = "";
		
		for ( $i = 1; $i < sizeof( $tar ); $i++ )
		{
			$ttt   = 0;
			$ftb  .="\\trowd\\trqc\\trgaph108\\trrh380\\trleft36\r\n";
			$tmp1  = "\\clvertalt";
			$tmp2  = "";
			
			for ( $r = 0; $r < sizeof( $tar[0] ); $r++ )
			{
				$ttt += round( $tar[0][$r] * $p );

				if ( $hlt == 1 )
				{
					if ( $flg == 1 )
					{
						if ( $i == 1 )
							$tmp1 .= "\\clcbpat8\\clshdng3000";
					}
					
					if ( $flg == 2 ) 
					{ 
						if ( $r == 0 ) 
							$tmp1 .= "\\clcbpat8\\clshdng3000";
					}
				}

				if ( $brd == 1 )
					$tmp1 .= "\\clbrdrt\\brdrs\\brdrw10 \\clbrdrl\\brdrs\\brdrw10 \\clbrdrb\\brdrs\\brdrw10 \\clbrdrr\\brdrs\\brdrw10 ";

				if ( $bld == 1 )
				{
					if ( $i == 1 && $flg == 1 )
					{
						$tmp2 .= "\\b";
					}
					else
					{
						if ( $r == 0 && $flg == 2 ) 
							$tmp2 .= "\\b"; 
						else 
							$tmp2 .= "\\plain";
					}
				}
				else 
				{ 
					$tmp2 .= "\\plain";
				}

				$tmp1 .= "\\cltxlrtb\\cellx" . $ttt;
				$tmp2 .= "\\intbl " . $this->toCyr( $tar[$i][$r] ) . "\\cell \\pard \r\n";
			}
			
			$ftb .= $tmp1 . "\r\n" . $tmp2 . "\\intbl \\row \\pard\r\n";
		}
		
		$this->text .= $ftb;
	}
	
	/**
	 * @access public
	 */
	function get_rtf_color( $color )
	{
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
		
		//\red0\green0\blue0;
		return "\\red" . $r . "\\green" . $g . "\\blue" . $b.";";
	}

	/**
	 * @access public
	 */
	function my_ar_unique( $array )
	{
		sort( $array );
		reset( $array );
		
		$newarray = array();
		$i = 0;
		$element = current( $array );
		
		for ( $n = 0; $n < sizeof( $array ); $n++ )
		{
			if ( next( $array ) != $element )
			{
				$newarray[$i] = $element;
				$element = current( $array );
				$i++;
			}
		}
		
		return $newarray;
	}

	/**
	 * @access public
	 */
	function parse_HTML( $string )
	{
		// colors
		if ( preg_match_all( "/(color=\#)([^ >]*)([ >])/msi", $string, $colors ) )
		{
			// $colors = array_unique( $colors[2] );
			$colors = $this->my_ar_unique( $colors[2] );
			
			for ( $i = 0; $i < sizeof( $colors ); $i++ )
			{
				$c_find[]     = "'color=#" . $colors[$i] . "'si";
				$c_replace[]  = "color=" . ( $i + 11 );
				$c_tbl       .= $this->get_rtf_color( $colors[$i] );
			}
			
			$string = preg_replace( $c_find, $c_replace, $string );
			$this->$header = preg_replace( "/goesuserscolors/", $c_tbl, $this->$header );
		}

		$fig_l = "pag456pag";
		$fig_r = "pag654pag";
		$token = " img12365412img ";
		$final = "";
		$im_fl = 0;
		
		if ( preg_match_all( "/(<img )(.*?)(>)/msi", $string, $imgs ) )
		{
			$text_mass = preg_split( "/(<img )(.*?)(>)/msi", $string );
			$images    = $imgs[2];
			$string    = "";
			
			for ( $i = 0; $i < sizeof( $text_mass ); $i++ )
			{
				if ( strlen( $images[$i] ) > 5 )
				{
					$img_mass[$im_fl] = $this->parse_image( $images[$i] );
					$string .= $text_mass[$i] . $token . $im_fl . "nort";
					$im_fl++;
				}
				else 
				{ 
					$string .= $text_mass[$i];
				}
			}
		}
		
		// tables
		if ( preg_match_all( "/(<table)(.*?)(<\/table>)/msi", $string, $tbls ) )
		{
			$text_mass = preg_split( "/(<table.*?<\/table>)/msi", $string );
			$tables    = $tbls[2];
			
			for ( $i = 0; $i < sizeof( $text_mass ); $i++ )
			{
				$fin_mass[$i] = $this->parse_table_new( $tables[$i] );
				$final .= $this->toCyr( $text_mass[$i] ) . "\\par" . $fin_mass[$i];
			}
		}
		else 
		{ 
			$final = $this->toCyr( $string );
		}
		
		if ( preg_match_all( "/" . $token . "/ms", $final,$count_i ) )
		{
			$count_i = $count_i[0];
			
			for ( $i = 0; $i < sizeof( $count_i ); $i++ )
			{
				$fnd   = $token . $i . "nort";
				$final = ereg_replace( $fnd, $img_mass[$i], $final );
			}
		}
		
		$this->text .= $final;
	}
	
	
	// image methods
	
	/**
	 * @access public
	 */
	function pixtotwips( $pix )
	{
		return $this->twips( $pix * 3.53 );
	}
	
	/**
	 * @access public
	 */
	function openimage( $image )
	{
		$sz = 0;
		$fp = @fopen( $image, "r" );
		
		while ( !feof( $fp ) )
		{
			$cy .= @fread( $fp, 1024 );
			$sz++;
			
			if ( $sz > $this->image_size )
				break;
		}
		
		@fclose( $fp );
		return bin2hex( $cy );
	}
	
	/**
	 * @access public
	 */
	function parse_image( $image )
	{
		$perms = ereg_replace( " +", "&", trim( $image ) );
		$perms = strtolower( $perms );
		parse_str( $perms );

		if ( isset( $top ) )
			$img_top = $top;
		else
			$img_top = $this->img_def_top;
			
		if ( isset( $src ) )
			$img_src = $src;
		else
			$img_src = $this->img_def_src;
			
		if ( isset( $width ) )
			$img_width = $width;
		else
			$img_width = $this->img_def_width;
			
		if ( isset( $height ) )
			$img_height = $height + $img_top;
		else
			$img_height = $this->img_def_height + $img_top;

		if ( isset( $left ) )
			$img_left = $left;
		else
			$img_left = $this->img_def_left;

		if ( isset( $border ) )
			$img_border = $border;
		else
			$img_border = $this->img_def_border;
			
		if ( isset( $align ) )
			$img_align = $align;
		else
			$img_align = $this->img_def_align;
			
		if ( isset( $wrap ) )
			$img_wrap = $wrap;
		else
			$img_wrap = $this->img_def_wrap;
			
		if ( isset( $space ) )
			$img_space = $space;
		else
			$img_space = $this->img_def_space;
			
		if ( isset( $anchor ) )
			$img_anchor = $anchor;
		else
			$img_anchor = $this->img_def_anchor;

		srand( (double)microtime() * 1000000 );
		
		$randval = rand( 1111, 9999 );
		$bliptag = rand();
		$blipuid = bin2hex( rand() );
		$src     = explode( ".", ereg_replace( "\"", "", $img_src ) );
		
		switch ( strtoupper( $src[sizeof( $src ) - 1] ) )
		{
			case "JPG": 
				$img_type = "jpeg"; 
				$im = true; 
				break;
			
			case "JPEG": 
				$img_type = "jpeg"; 
				$im = true; 
				break;
			
			case "PNG": 
				$img_type = "png"; 
				$im = true; 
				break;
		}
		
		switch ( strtoupper( $img_wrap ) )
		{
			case "NO": 
				$img_wrap = 3; 
				break;
			
			case "AROUND": 
				$img_wrap = 2; 
				break;
			
			case "UPDOWN": 
				$img_wrap = 1; 
				break;
		}
		
		// align
		switch ( strtoupper( $img_anchor ) )
		{
			case "PARA": 
				$a_left = true; 
				break;
			
			case "PAGE": 
				$a_left = 0; 
				break;
			
			case "MARGIN": 
				$a_left = true; 
				break;
		}
		
		switch ( strtoupper( $img_align ) )
		{
			case "RIGHT":
				if ( $a_left )
					$a_left = $this->mar_right + $this->mar_left;
					
				$del = $this->pg_width - $a_left - $this->twips( $img_width );
				break;
				
			case "LEFT":
				$del = 0;
				break;
			
			case "CENTER":
				if ( $a_left )
					$a_left = $this->mar_right + $this->mar_left;
					
				$del = round( ( ( $this->pg_width - $a_left ) / 2 ) - ( $this->twips( $img_width ) / 2 ) );
				break;
		}
		
		// picture params
		$sps = $this->twips( $img_space );
		$sps = $img_space * 36004;
		$x1  = $this->twips( $img_left ) + $del;
		$x2  = $x1 + $this->twips( $img_width );
		$y1  = $this->twips( $img_top );
		$y2  = $this->twips( $img_height );
		
		$f_image  = "{\\shp{\\*\\shpinst\\shpleft" . $x1 . "\\shpright" . $x2 . "\\shptop" . $y1 . "\\shpbottom" . $y2;
		$f_image .= "\\shpbx" . strtolower( $img_anchor ) . "\\shpby" . strtolower( $img_anchor ) . "\\shpz0\\shplid" . $randval;
		$f_image .= "\\shpwr" . $img_wrap . "\\shpwrk0";
		$f_image .= "{\\sp{\\sn fLine}{\\sv " . $img_border . "}}";
		$f_image .= "{\\sp{\\sn shapeType}{\\sv 75}}{\\sp{\\sn fBehindDocument}{\\sv 1}}";
		$f_image .= "{\\sp{\\sn dxWrapDistLeft}{\\sv " .$sps . "}}{\\sp{\\sn dxWrapDistRight}{\\sv "  . $sps . "}}";
		$f_image .= "{\\sp{\\sn dyWrapDistTop}{\\sv "  .$sps . "}}{\\sp{\\sn dyWrapDistBottom}{\\sv " . $sps . "}}";
		$f_image .= "{\\sp{\\sn pib}{\\sv {\\pict\\"   .$img_type . "blip\\picw" . $img_width . "\\pich" . $img_height . "\\picscalex100\\picscaley100";
		$f_image .= "\\bliptag" . $bliptag . "{\\*\\blipuid " . $blipuid . "}";

		if ( $im )
			$f_image .= $this->openimage( ereg_replace( "\"", "", $img_src ) );
		/*
		else
			$f_image .= $this->openimage( "logo.png" );
		*/
		
		$f_image .= "}}}}}";
		return $f_image;
	}
	
	
	// table methods

	/**
	 * @access public
	 */
	function parse_table_new( $table )
	{
		$result = "";
		
		unset( $tbl_border    );
		unset( $tbl_width     );
		unset( $tbl_height    );
		unset( $tbl_align     );
		unset( $tbl_valign    );
		unset( $tbl_bgcolor   );
		unset( $all_data_head );
		unset( $all_data_body );
		unset( $all_data_wdth );
		
		$tmp   = split( ">", $table );
		$p_tbl = "";
		$perms = ereg_replace( " +", "&", trim( $tmp[0] ) );
		$perms = strtolower( $perms );
		
		parse_str( $perms );
		
		if ( isset( $cellpadding ) )
		{ 
			$tbl_cellpadding = $this->twips( $cellpadding ); 
			unset( $cellpadding );
		}
		else 
		{ 
			$tbl_cellpadding = $this->twips( $this->tbl_def_cellpadding );
		}
		
		if ( isset( $border ) ) 
		{ 
			$tbl_border = $border; 
			unset( $border ); 
			$p_tbl .= "border=" . $tbl_border . " - "; 
		}
		else 
		{
			$tbl_border = $this->tbl_def_border;
		}
		
		if ( isset( $width ) ) 
		{ 
			$tbl_width = $width; 
			unset( $width ); 
			$p_tbl .= "width=" . $tbl_width . " - "; 
		}
		else 
		{
			$tbl_width = $this->tbl_def_width;
		}
		
		if ( isset( $align ) ) 
		{ 
			$tbl_align = $align; 
			unset( $align ); 
			$p_tbl .= "align=" . $tbl_align . " - "; 
		}
		else 
		{
			$tbl_align = $this->tbl_def_align;
		}
		
		if ( isset( $valign ) ) 
		{ 
			$tbl_valign = $valign; 
			unset( $valign ); 
			$p_tbl .= "valign=" . $tbl_valign . " - "; 
		}
		else 
		{
			$tbl_valign = $this->tbl_def_valign;
		}
		
		if ( isset( $bgcolor ) ) 
		{ 
			$tbl_bgcolor = $bgcolor; 
			unset( $bgcolor ); 
			$p_tbl .= "bgcolor=" . $tbl_bgcolor . " - "; 
		}
		else 
		{
			$tbl_bgcolor = $this->tbl_def_bgcolor;
		}
			
		if ( ereg( "%", $tbl_width ) )
		{
			$yyy = ereg_replace( "%", "", $tbl_width );
			$this->tb_wdth = round( ( ( $this->pg_width - ( $this->mar_left + $this->mar_right ) ) / 100 ) * $yyy );
		}
		else 
		{ 
			$this->tb_wdth = $this->twips( $tbl_width );
		}
		
		$cells_wdth = 0;
		$cells_hght = 0;
		
		$other   = substr( strstr( $table, ">" ), 1 );
		$result .= "таблица <b>$i</b> (" . $p_tbl . ")<br>\n";
		
		if ( preg_match_all( "/(<tr)(.*?)(<\/tr>)/ms", $other, $trs ) )
		{
			$trs = $trs[2];
			$tr_all_f = "";
			
			for ( $r = 0; $r < sizeof( $trs ); $r++ )
			{
				$num_t = 0;
				unset( $tr_border, $tr_width, $tr_height, $tr_align );
				unset( $tr_valign, $tr_bgcolor, $keep_row );
				$tmp2 = split( ">", $trs[$r] );
				$keep_row = ( $rowkeep )? "\\trkeep" : "";
				$p_tr  = "";
				$perms = ereg_replace( " +", "&", trim( $tmp2[0] ) );
				$perms = strtolower( $perms );
				parse_str( $perms );
				
				if ( isset( $cellpadding ) ) 
				{ 
					$tr_cellpadding = $this->twips( $cellpadding ); 
					unset( $cellpadding ); 
					$p_tr .= "cellpadding=" . $tr_cellpadding . " - "; 
				}
				else 
				{
					$tr_cellpadding = $tbl_cellpadding;
				}
				
				if ( isset( $border ) ) 
				{ 
					$tr_border = $border; 
					unset( $border ); 
					$p_tr .= "border=" . $tr_border . " - "; 
				}
				else 
				{
					$tr_border = $tbl_border;
				}
				
				if ( isset( $width ) ) 
				{ 
					$tr_width = $width; 
					unset( $width ); 
					$p_tr .= "width=" . $tr_width . " - "; 
				}
				else 
				{
					$tr_width = $tbl_width;
				}
				
				if ( isset( $height ) ) 
				{ 
					$tr_height = $height; 
					unset( $height ); 
					$p_tr .= "height=" . $tr_height . " - "; 
				}
				
				if ( isset( $align ) ) 
				{ 
					$tr_align = $align; 
					unset( $align ); 
					$p_tr .= "align=" . $tr_align . " - "; 
				}
				else 
				{
					$tr_align = $this->row_def_align;
				}
				
				if ( isset( $valign ) ) 
				{ 
					$tr_valign = $valign; 
					unset( $valign ); 
					$p_tr .= "valign=" . $tr_valign . " - "; 
				}
				else 
				{
					$tr_valign = $tbl_valign;
				}
					
				if ( isset( $bgcolor ) ) 
				{ 
					$tr_bgcolor = $bgcolor; 
					unset( $bgcolor ); 
					$p_tr .= "bgcolor=" . $tr_bgcolor . " - "; 
				}
				else 
				{
					$tr_bgcolor = $tbl_bgcolor;
				}

				$other2 = substr( strstr( $trs[$r], ">" ), 1 );
				
				// -row
				if ( ereg( "%", $tr_width ) )
				{
					$yyy = ereg_replace( "%", "", $tr_width );
					$tr_twips_wdth = round( ( ( $this->pg_width - ( $this->mar_left + $this->mar_right ) ) / 100 ) * $yyy );
				}
				else 
				{ 
					$tr_twips_wdth = $this->twips( $tr_width ); 
				}
				
				if ( $tr_height != 0 )
					$tr_wdth_f = "\\trrh" . $this->twips( $tr_height ); 
				else 
					$tr_wdth_f = ""; 

				switch ( strtoupper( $tbl_align ) ) 
				{
					case "CENTER": 
						$tbl_all_all = "\\trqc "; 
						break;
					
					case "LEFT": 
						$tbl_all_all = "\\trql "; 
						break;
					
					case "RIGHT": 
						$tbl_all_all = "\\trqr "; 
						break;
				}
					
				$tr_padding = "\\trpaddl" . $tr_cellpadding . "\\trpaddt" . $tr_cellpadding . "\\trpaddb" . $tr_cellpadding . "\\trpaddr" . $tr_cellpadding . "\\trpaddfl3\\trpaddft3\\trpaddfb3\\trpaddfr3";
				$tr_res = "\\pard\\trowd" . $keep_row . $tbl_all_all . $tr_padding . "\\trgaph100\\trrh100\\trleft36\r\n";
					
				$this->tr_hd_mass[$r] = $tr_res;
					
				$cells_row_hght = 0;
				$cells_row_wdth = 0;
					
				if ( preg_match_all( "/(<td)(.*?)(<\/td>)/ms", $other2, $tds ) )
				{
					$gen_cell_wdth = 0;
					$cur_cell_wdth = 0;
					$tds = $tds[2];
					$cells_in_row = sizeof( $tds );
					$td_body_res  = "";
					
					unset( $td_head_res  ); 
					unset( $td_wdth_mass );
					
					for ( $d = 0; $d < sizeof( $tds ); $d++ )
					{
						unset( $td_border  ); 
						unset( $td_width   ); 
						unset( $td_height  ); 
						unset( $td_align   ); 
						unset( $td_valign  ); 
						unset( $td_bgcolor ); 
						unset( $td_colspan ); 
						unset( $td_rowspan ); 
						unset( $td_mrg_f   );
						
						$tmp3  = split( ">", $tds[$d] );
						$p_td  = "";
						$perms = ereg_replace( " +", "&", trim( $tmp3[0] ) );
						$perms = strtolower( $perms );
						
						parse_str( $perms );
						
						if ( isset( $colspan ) ) 
						{ 
							$td_colspan = $colspan; 
							unset( $colspan ); 
							$p_td .= "colspan=" . $td_colspan . " - "; 
						}
						else 
						{
							$td_colspan = 1;
						}
						
						if ( isset( $rowspan ) ) 
						{ 
							$td_rowspan = $rowspan; 
							unset( $rowspan ); 
							$p_td .= "rowspan=" . $td_rowspan . " - "; 
						}
						else 
						{
							$td_rowspan = 1;
						}
						
						if ( isset( $border ) ) 
						{ 
							$td_border = $border; 
							unset( $border ); 
							$p_td .= "border=" . $td_border . " - "; 
						}
						else 
						{
							$td_border = $tr_border;
						}
						
						if ( isset( $width ) ) 
						{ 
							$td_width = $width; 
							unset( $width ); 
							$p_td .= "width=" . $td_width . " - "; 
						}
						else 
						{
							$td_width = "no";
						}
						
						if ( isset( $align ) ) 
						{ 
							$td_align = $align; 
							unset( $align ); 
							$p_td .= "align=" . $td_align . " - "; 
						}
						else 
						{
							$td_align = $tr_align;
						}
						
						if ( isset( $valign ) ) 
						{ 
							$td_valign = $valign; 
							unset( $valign ); 
							$p_td .= "valign=" . $td_valign . " - "; 
						}
						else 
						{
							$td_valign = $tr_valign;
						}
						
						if ( isset( $bgcolor ) ) 
						{ 
							$td_bgcolor = $bgcolor; 
							unset( $bgcolor ); 
							$p_td .= "bgcolor=" . $td_bgcolor . " - "; 
						}
						else 
						{
							$td_bgcolor = $tr_bgcolor;
						}
						
						$other3 = substr( strstr( $tds[$d], ">" ), 1 );
						
						// cells
						switch ( strtoupper( $td_valign ) ) 
						{
							case "TOP": 
								$td_val_f = "\\clvertalt"; 
								break;
							
							case "MIDDLE": 
								$td_val_f = "\\clvertalc"; 
								break;
							
							case "BOTTOM": 
								$td_val_f = "\\clvertalb"; 
								break;
						}

						if ( $td_bgcolor == 0 ) 
						{ 
							$td_bg_f = ""; 
						}
						else 
						{ 
							$td_bgcolor = $td_bgcolor * 100; 
							$td_bg_f = "\\clcbpat8\\clshdng" . $td_bgcolor; 
						}
						
						if ( $td_border == 1 ) 
						{ 
							$td_brd_f = "\\clbrdrt\\brdrs\\brdrw10 \\clbrdrl\\brdrs\\brdrw10 \\clbrdrb\\brdrs\\brdrw10 \\clbrdrr\\brdrs\\brdrw10 "; 
						}
						else
						{
							$td_brd_f = "";
							
							if ( preg_match( "/t/", $td_border ) )
								$td_brd_f .= "\\clbrdrt\\brdrs\\brdrw10";
								
							if ( preg_match( "/b/", $td_border ) )
								$td_brd_f .= "\\clbrdrb\\brdrs\\brdrw10";
								
							if ( preg_match( "/r/",$td_border ) )
								$td_brd_f .= "\\clbrdrr\\brdrs\\brdrw10";
								
							if ( preg_match( "/l/", $td_border ) )
								$td_brd_f .= "\\clbrdrl\\brdrs\\brdrw10";
						}
						
						if ( ereg( "%", $td_width ) )
						{
							$ooo = ereg_replace( "%", "", $td_width );
							$td_wdth_mass[] = round( ( $tr_twips_wdth / 100 ) * $ooo );
							$tmp_wdth = round( ( $tr_twips_wdth / 100 ) * $ooo );
						}
						else if ( $td_width == "no" )
						{
							$td_wdth_mass[] = "no"; 
							$tmp_wdth = "no";
						}
						else 
						{ 
							$td_wdth_mass[] = $this->twips( $td_width ); 
							$tmp_wdth = $this->twips( $td_width );
						}
							
						switch ( strtoupper( $td_align ) )
						{
							case "CENTER": 
								$td_text = "\\qc " . $this->toCyr( $other3 ) . ""; 
								break;
							
							case "LEFT": 
								$td_text = "\\ql " . $this->toCyr( $other3 ) . ""; 
								break;
							
							case "RIGHT": 
								$td_text = "\\qr " . $this->toCyr( $other3 ) . ""; 
								break;
							
							case "JUSTIFY": $td_text = "\\qj " . $this->toCyr( $other3 ) . ""; 
							break;
						}

						$td_head_res[]  = $td_mrg_f . $td_val_f . $td_bg_f . $td_brd_f . "\\cltxlrtb";
						$td_body_res   .= "\\intbl " . $td_text . "\\cell \\pard \r\n";

						$tmp_head = $td_mrg_f . $td_val_f . $td_bg_f . $td_brd_f . "\\cltxlrtb";
						$tmp_body = "\\intbl " . $td_text . "\\cell \\pard \r\n";

						for ( $gh = 0; $gh < $td_rowspan; $gh++ )
						{
							for ( $jh = 0; $jh < $td_colspan; $jh++ )
							{
								$all_data[$r][$num_t][$gh][$jh]      = $other3;
								$all_data_head[$r][$num_t][$gh][$jh] = $tmp_head;
								$all_data_body[$r][$num_t][$gh][$jh] = $tmp_body;
								$all_data_wdth[$r][$num_t][$gh][$jh] = $tmp_wdth;
							}
						}
						
						$num_t++;
						$cells_row_wdth++;
						
						if ( $td_colspan > 1 )
						{
							$cells_row_wdth += $td_colspan - 1;
							
							for ( $q = 1; $q < $td_colspan; $q++ )
								$cells_data .= "\r\n\t\t<td>$other3</td>";
						}
						else 
						{
							$cells_data .= "\r\n\t\t<td>$other3</td>";
						}
					}
				}
					
				if ( $cells_wdth < $cells_row_wdth )
					$cells_wdth = $cells_row_wdth;
				
				$cells_hght++;
				$rows_t .= "\r\n\t<tr>\r\n\t\t$cells_data\r\n\t</tr>";
				$cells_data = "";
			}	
		}
		
		return $this->tbl_full( $all_data_head, $all_data_body, $all_data_wdth, $cells_wdth );
	}
	
	/**
	 * Three-dimensional array parse.
	 *
	 * @access public
	 */
	function tbl_full( $mass_head, $mass_body, $mass_wdth, $width )
	{
		$h   = "\\intbl          \\cell \\pard \r\n";
		$hh  = "no";
		$hhh = "\\clvertalc\\clbrdrt\\brdrs\\brdrw10 \\clbrdrl\\brdrs\\brdrw10 \\clbrdrb\\brdrs\\brdrw10 \\clbrdrr\\brdrs\\brdrw10 \\cltxlrtb";

		for ( $i = 0; $i < sizeof( $mass_wdth ); $i++ ) 
		{
			for ( $b = 0; $b < $width; $b++ ) 
			{ 
				$shablon_mass[$i][$b] = "&nbsp;"; 
				$fin_tbl_head[$i][$b] = $hhh;
				$fin_tbl_body[$i][$b] = $h;
				$fin_tbl_wdth[$i][$b] = $hh; 
			}
		}
		
		$num_id = 0;
		
		for ( $a = 0; $a < sizeof( $mass_wdth ); $a++ )
		{
			$id = 0;
			
			for ( $c = 0; $c < $width; $c++ )
			{
				if ( $fin_tbl_body[$a][$c] == $h )
				{
					for ( $lk = 0; $lk < sizeof( $mass_wdth[$a][$id] ); $lk++ )
					{
						for ( $kl = 0; $kl < sizeof( $mass_wdth[$a][$id][$lk] ); $kl++ )
						{
							if ( $mass_wdth[$a][$id][$lk][$kl] != "" )
							{
								$shablon_mass[$a + $lk][$c + $kl] = $num_id + $id + 1;
								$fin_tbl_head[$a + $lk][$c + $kl] = $mass_head[$a][$id][$lk][$kl];
								$fin_tbl_body[$a + $lk][$c + $kl] = $mass_body[$a][$id][$lk][$kl];
								$fin_tbl_wdth[$a + $lk][$c + $kl] = $mass_wdth[$a][$id][$lk][$kl];
							}
						}
					}
					
					$id++; // $num_id += $id; 
				}
			}
			
			$num_id += $id;
		}
		
		$fin_max = $this->row_me( $fin_tbl_wdth, $width, $shablon_mass );
		return $this->final_parse( $fin_tbl_head, $fin_max, $fin_tbl_body, $shablon_mass );
	}

	/**
	 * @access public
	 */	
	function final_parse( $head, $fin_max, $body, $shablon )
	{
		for ( $h = 0; $h < sizeof( $shablon ); $h++ )
		{
			$td_head_f = "";
			$td_body_f = "";
			$tr_res    = $this->tr_hd_mass[$h];
			$iiii      = 0;

			for ( $w = 0; $w < sizeof( $shablon[0] ); $w++ )
			{
				$iiii += $fin_max[$w];
				
				if ( $shablon[$h][$w] != $shablon[$h][$w+1] || $w == sizeof( $shablon[0] ) - 1 )
				{	
					$rspn = "rspn" . $shablon[$h][$w];
					
					if ( !$$rspn )
					{
						if ( $shablon[$h][$w] == $shablon[$h + 1][$w] ) 
						{ 
							$rs    = "\\clvmgf"; 
							$$rspn = true;
						}
						else 
						{
							$rs = "";
						}
					}
					else
					{
						if ( $shablon[$h][$w] != $shablon[$h - 1][$w] ) 
							$rs = ""; 
						else
							$rs = "\\clvmrg";
					}
					
					$td_head_f .= $rs . $head[$h][$w] . "\\cellx" . $iiii . "\r\n"; 
					$td_body_f .= $body[$h][$w];
				}
			}
			
			$tr_all_f .= $tr_res . $td_head_f . "\r\n" . $td_body_f . "\r\n\\intbl \\row \\pard\r\n";
		}
		
		return $tr_all_f;
	}
	
	/**
	 * Object inserted tables searching.
	 *
	 * @access public
	 */
	function obj_srch( $shablon )
	{
		$width  = sizeof( $shablon[0] );
		$height = sizeof( $shablon );
		
		for ( $h = 0; $h < $height; $h++ )
		{
			$g_count = 0;
			
			for ( $w = 0; $w < $width; $w++ )
			{
				if ( $shablon[$h][$w] != $shablon[$h + 1][$w] )
					$g_count++;
			}
			
			$g_mass[$h] = $g_count;
		}
		
		for ( $w = 0; $w < $width; $w++ )
		{
			$v_count = 0;
			
			for ( $h = 0; $h < $height; $h++ )
			{
				if ( $shablon[$h][$w] != $shablon[$h][$w + 1] )
					$v_count++;
			}
			
			$v_mass[$w] = $v_count;
		}
	}
	
	/**
	 * Row widths counting method.
	 *
	 * @access public
	 */
	function row_me( $wdth, $or_wdth, $shablon )
	{
		for ( $h = 0; $h < sizeof( $wdth ); $h++ )
		{
			$count = 0; 
			$sum   = 0; 
			$mstc  = 0;
			
			for ( $w = 0; $w < $or_wdth; $w++ )
			{
				if ( $wdth[$h][$w] == "no" ) 
				{
					$count++;
				}
				else
				{
					if ( $shablon[$h][$w] != $shablon[$h][$w + 1] )
					{
						$sum += $wdth[$h][$w];
						$wdth[$h][$w] = $wdth[$h][$w] . "mst" . $mstc; 
						$mstc = 0;
					}
					else 
					{ 
						$wdth[$h][$w] = $wdth[$h][$w] . "sl" . $mstc; 
						$mstc++;
					}
				}
			}
			
			$opt = round( ( $this->tb_wdth - $sum ) / $count );
			
			for ( $w = 0; $w < $or_wdth; $w++ ) 
			{
				if ( $wdth[$h][$w] == "no" )
					$wdth[$h][$w] = $opt;
			}
		}
		
		for ( $w = 0; $w < $or_wdth; $w++ )
		{
			$fl = false;
			
			for ( $h = 0; $h < sizeof( $wdth ); $h++ )
			{
				if ( ereg( "mst", $wdth[$h][$w] ) || ereg( "sl", $wdth[$h][$w] ) )
					$fl = true;
			}
			
			if ( $fl )
				$yes_no[$w] = "yes";
			else
				$yes_no[$w] = "no";
		}
		
		return $this->mxs( $wdth, $or_wdth, $shablon );
	}
	
	/**
	 * Main borders counting method.
	 *
	 * @access public
	 */
	function mxs( $wdth, $or_wdth, $shablon )
	{
		$t_count = 0;
		
		for ( $h = 0; $h < $or_wdth; $h++ ) 
			$fin_max[$h] = "no";
			
		for ( $w = 0; $w < $or_wdth; $w++ )
		{
			for ( $h = 0; $h < sizeof( $wdth ); $h++ )
			{
				unset( $d_tmp );
				
				if ( ereg( "mst", $wdth[$h][$w] ) )
				{
					$width = preg_replace( "/mst\d+/", "", $wdth[$h][$w] );
					$span  = preg_replace( "/\d+mst/", "", $wdth[$h][$w] );
					
					if ( $span > 0 )
					{
						$tty = $width / ( $span + 1 );
						
						if ( $mst_mass[$w] < $tty ) 
						{ 
							$mst_mass[$w] = $tty; 
							$mst[$w] = $wdth[$h][$w]; 
						}
					}
					else
					{
						$d_tmp = $width;
					}
				}
				
				if ( $fin_max[$w] < $d_tmp || $fin_max[$w] == "no" )
					$fin_max[$w] =$d_tmp;
			}
			
			$t_count++;
		}
		
		for ( $i = 0; $i < $t_count; $i++ ) 
		{ 
			if ( $fin_max[$i] == "" )
				$fin_max[$i] = "no";
		}
		
		return $this->mxs2( $fin_max, $mst );
	}

	/**
	 * @access public
	 */
	function mxs2( $fin_max, $mst )
	{
		for ( $i = 0; $i < sizeof( $fin_max ); $i++ )
		{
			$tmp_sum = 0; 
			$fl = 1;
			
			if ( $mst[$i] != "" )
			{
				if ( $fin_max[$i] == "no" )
				{
					$width = preg_replace( "/mst\d+/", "", $mst[$i] );
					$span  = preg_replace( "/\d+mst/", "", $mst[$i] );
					
					for ( $h = $i - $span; $h < $i; $h++ )
					{
						if ( $fin_max[$h] != "no" )
							$tmp_sum += $fin_max[$h];
						else
							$fl++;
					}
					
					$opt = round( ( $width - $tmp_sum ) / $fl );
					
					for ( $h = $i - $span; $h <= $i; $h++ )
					{
						if ( $fin_max[$h] == "no" )
							$fin_max[$h] = $opt;
					}
				}
				else
				{
					$width = preg_replace( "/mst\d+/", "", $mst[$i] );
					$span  = preg_replace( "/\d+mst/", "", $mst[$i] );
					
					for ( $h = $i - $span; $h <= $i; $h++ )
					{
						if ( $fin_max[$h] != "no" )
							$tmp_sum += $fin_max[$h];
						else
							$fl++;
					}
					
					$opt = round( ( $width - $tmp_sum ) / ( $fl - 1 ) );
					
					if ( $opt >= 0 )
					{
						for ( $h = $i - $span; $h < $i; $h++ )
						{
							if ( $fin_max[$h] == "no" )
								$fin_max[$h] = $opt;
						}
					}
				}
			}
		}
		
		$f_sum = 0;
		$f_fl  = 0;
		
		for ( $i = 0; $i < sizeof( $fin_max ); $i++ )
		{
			if ( $fin_max[$i] != "no" )
				$f_sum += $fin_max[$i];
			else
				$f_fl++;
		}
		
		$f_fl  = ( $f_fl == 0 )? 1 : $f_fl;
		$f_opt = round( ( $this->tb_wdth - $f_sum ) / $f_fl );
		
		if ( $f_opt < 0 )
			$f_opt = 10;
			
		for ( $i = 0; $i < sizeof( $fin_max ); $i++ )
		{
			if ( $fin_max[$i] == "no" )
				$fin_max[$i] = $f_opt;
		}
		
		return $fin_max;
	}
} // END OF RTFGenerator

?>
