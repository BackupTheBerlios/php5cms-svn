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
|         Olivier Plathey                                              |
+----------------------------------------------------------------------+
*/


define( 'PDF_VERSION', '1.0' );

if ( !defined( 'PDF_FONT_DEFAULT' ) )
	define( 'PDF_FONT_DEFAULT', 'Arial' );


/**
 * PDF Class
 *
 * @todo lowercase properties and method names
 * @package format_pdf
 */
 
class PDF
{
	/**
	 * current page number
	 *
	 * @access  public
	 */
	var $page;
	
	/**
	 * current object number
	 *
	 * @access  public
	 */
	var $n;

	/**
	 * array of object offsets
	 *
	 * @access  public
	 */
	var $offsets;

	/**
	 * buffer holding in-memory PDF
	 *
	 * @access  public
	 */
	var $buffer;
	
	/**
	 * array containing pages
	 *
	 * @access  public
	 */
	var $pages;
	
	/**
	 * current document state
	 *
	 * @access  public
	 */
	var $state;
	
	/**
	 * compression flag
	 *
	 * @access  public
	 */
	var $compress;
	
	/**
	 * default orientation
	 *
	 * @access  public
	 */
	var $defOrientation;
	
	/**
	 * current orientation
	 *
	 * @access  public
	 */
	var $curOrientation;
	
	/**
	 * array indicating orientation changes
	 *
	 * @access  public
	 */
	var $orientationChanges;
	
	/**
	 * dimensions of page format in points (width)
	 *
	 * @access  public
	 */
	var $fwPt;
	
	/**
	 * dimensions of page format in points (height)
	 *
	 * @access  public
	 */
	var $fhPt;
	
	/**
	 * dimensions of page format in user unit (width)
	 *
	 * @access  public
	 */
	var $fw;
	
	/**
	 * dimensions of page format in user unit (height)
	 *
	 * @access  public
	 */
	var $fh;
	
	/**
	 * current dimensions of page in points (width)
	 *
	 * @access  public
	 */
	var $wPt;
	
	/**
	 * current dimensions of page in points (height)
	 *
	 * @access  public
	 */
	var $hPt;
	
	/**
	 * scale factor (number of points in user unit)
	 *
	 * @access  public
	 */
	var $k;
	
	/**
	 * current dimensions of page in user unit (width)
	 *
	 * @access  public
	 */
	var $w;
	
	/**
	 * current dimensions of page in user unit (height)
	 *
	 * @access  public
	 */
	var $h;
	
	/**
	 * left margin
	 *
	 * @access  public
	 */
	var $lMargin;
	
	/**
	 * top margin
	 *
	 * @access  public
	 */
	var $tMargin;
	
	/**
	 * right margin
	 *
	 * @access  public
	 */
	var $rMargin;
	
	/**
	 * page break margin
	 *
	 * @access  public
	 */
	var $bMargin;
	
	/**
	 * cell margin
	 *
	 * @access  public
	 */
	var $cMargin;
	
	/**
	 * current position in user unit for cell positionning
	 *
	 * @access  public
	 */
	var $x, $y;
	
	/**
	 * height of last cell printed
	 *
	 * @access  public
	 */
	var $lasth;
	
	/**
	 * line width in user unit
	 *
	 * @access  public
	 */
	var $lineWidth;
	
	/**
	 * array of standard font names
	 *
	 * @access  public
	 */
	var $coreFonts;
	
	/**
	 * array of used fonts
	 *
	 * @access  public
	 */
	var $fonts;
	
	/**
	 * array of font files
	 *
	 * @access  public
	 */
	var $fontFiles;
	
	/**
	 * array of encoding differences
	 *
	 * @access  public
	 */
	var $diffs;
	
	/**
	 * array of used images
	 *
	 * @access  public
	 */
	var $images;
	
	/**
	 * array of links in pages
	 *
	 * @access  public
	 */
	var $pageLinks;
	
	/**
	 * array of internal links
	 *
	 * @access  public
	 */
	var $links;
	
	/**
	 * current font family
	 *
	 * @access  public
	 */
	var $fontFamily;
	
	/**
	 * current font style
	 *
	 * @access  public
	 */
	var $fontStyle;
	
	/**
	 * underlining flag
	 *
	 * @access  public
	 */	
	var $underline;
	
	/**
	 * current font info
	 *
	 * @access  public
	 */
	var $currentFont;
	
	/**
	 * current font size in points
	 *
	 * @access  public
	 */
	var $fontSizePt;
	
	/**
	 * current font size in user unit
	 *
	 * @access  public
	 */
	var $fontSize;
	
	/**
	 * commands for drawing color
	 *
	 * @access  public
	 */
	var $drawColor;
	
	/**
	 * commands for filling color
	 *
	 * @access  public
	 */
	var $fillColor;
	
	/**
	 * commands for text color
	 *
	 * @access  public
	 */
	var $textColor;
	
	/**
	 * indicates whether fill and text colors are different
	 *
	 * @access  public
	 */
	var $colorFlag;
	
	/**
	 * word spacing
	 *
	 * @access  public
	 */
	var $ws;
	
	/**
	 * automatic page breaking
	 *
	 * @access  public
	 */
	var $autoPageBreak;
	
	/**
	 * threshold used to trigger page breaks
	 *
	 * @access  public
	 */
	var $pageBreakTrigger;
	
	/**
	 * flag set when processing footer
	 *
	 * @access  public
	 */
	var $inFooter;
	
	/**
	 * zoom display mode
	 *
	 * @access  public
	 */
	var $zoomMode;
	
	/**
	 * layout display mode
	 *
	 * @access  public
	 */
	var $layoutMode;
	
	/**
	 * title
	 *
	 * @access  public
	 */
	var $title;
	
	/**
	 * subject
	 *
	 * @access  public
	 */
	var $subject;
	
	/**
	 * author
	 *
	 * @access  public
	 */
	var $author;
	
	/**
	 * keywords
	 *
	 * @access  public
	 */
	var $keywords;
	
	/**
	 * creator
	 *
	 * @access  public
	 */
	var $creator;
	
	/**
	 * alias for total number of pages
	 *
	 * @access  public
	 */
	var $aliasNumPages;

	/**
	 * whether document is protected
	 *
	 * @access  public
	 */	
	var $encrypted;
	
	/**
	 * U entry in pdf document
	 *
	 * @access  public
	 */
	var $uvalue;
	
	/**
	 * O entry in pdf document
	 *
	 * @access  public
	 */
	var $ovalue;
	
	/**
	 * P entry in pdf document
	 *
	 * @access  public
	 */
	var $pvalue;
	
	/**
	 * encryption object id
	 *
	 * @access  public
	 */
	var $enc_obj_id;
	
	/**
	 * last RC4 key encrypted (cached for optimisation)
	 *
	 * @access  public
	 */
	var $last_rc4_key;
	
	/**
	 * last RC4 computed key
	 *
	 * @access  public
	 */
	var $last_rc4_key_c;
	
	/**
	 * @access  public
	 */
	var $javascript;
	
	/**
	 * @access  public
	 */
	var $n_js;
	
	/**
	 * @access  public
	 */
	var $legends;
	
	/**
	 * @access  public
	 */
	var $wLegend;
	
	/**
	 * @access  public
	 */
	var $sum;
	
	/**
	 * @access  public
	 */
	var $nbval;

	/**
	 * @access  public
	 */	
	var $oldFontSize;
	
	/**
	 * @access  public
	 */
	var $outlineRoot;

	/**
	 * @access  public
	 */
	var $tags_ol  = array();
	
	/**
	 * @access  public
	 */
	var $tags_ul  = array();
	
	/**
	 * @access  public
	 */
	var $classes  = array();
	
	/**
	 * @access  public
	 */
	var $footerH  = 15;
	
	/**
	 * @access  public
	 */
	var $tags     = array();
	
	/**
	 * @access  public
	 */
	var $outlines = array();
	
	/**
	 * @access  public
	 */
	var $angle    = 0;
	

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function PDF( $orientation = 'P', $unit = 'mm', $format = 'A4' )
	{
		$this->page               = 0;
		$this->n                  = 2;
		$this->buffer             = '';
		$this->pages              = array();
		$this->orientationChanges = array();
		$this->state              = 0;
		$this->fonts              = array();
		$this->fontFiles          = array();
		$this->diffs              = array();
		$this->images             = array();
		$this->links              = array();
		$this->inFooter           = false;
		$this->fontFamily         = '';
		$this->fontStyle          = '';
		$this->fontSizePt         = 12;
		$this->underline          = false;
		$this->drawColor          = '0 G';
		$this->fillColor          = '0 g';
		$this->textColor          = '0 g';
		$this->colorFlag          = false;
		$this->ws                 = 0;
		
		// Standard fonts
		$this->coreFonts['courier']      = 'Courier';
		$this->coreFonts['courierB']     = 'Courier-Bold';
		$this->coreFonts['courierI']     = 'Courier-Oblique';
		$this->coreFonts['courierBI']    = 'Courier-BoldOblique';
		$this->coreFonts['helvetica']    = 'Helvetica';
		$this->coreFonts['helveticaB']   = 'Helvetica-Bold';
		$this->coreFonts['helveticaI']   = 'Helvetica-Oblique';
		$this->coreFonts['helveticaBI']  = 'Helvetica-BoldOblique';
		$this->coreFonts['times']        = 'Times-Roman';
		$this->coreFonts['timesB']       = 'Times-Bold';
		$this->coreFonts['timesI']       = 'Times-Italic';
		$this->coreFonts['timesBI']      = 'Times-BoldItalic';
		$this->coreFonts['symbol']       = 'Symbol';
		$this->coreFonts['zapfdingbats'] = 'ZapfDingbats';
	
		// Scale factor
		if ( $unit == 'pt' )
		{
			$this->k = 1;
		}
		else if ( $unit == 'mm' )
		{
			$this->k = 72 / 25.4;
		}
		else if ( $unit == 'cm' )
		{
			$this->k = 72 / 2.54;
		}
		else if ( $unit == 'in' )
		{
			$this->k = 72;
		}
		else
		{
			$this = new PEAR_Error( "Incorrect unit: " . $unit );
			return;
		}
		
		// Page format
		if ( is_string( $format ) )
		{
			$format = strtolower( $format );
		
			if ( $format == 'a3' )
			{
				$format = array( 841.89, 1190.55 );
			}
			else if ( $format == 'a4' )
			{
				$format = array( 595.28, 841.89 );
			}
			else if ( $format == 'a5' )
			{
				$format = array( 420.94, 595.28 );
			}
			else if ( $format == 'letter' )
			{
				$format = array( 612, 792 );
			}
			else if ( $format == 'legal' )
			{
				$format = array( 612, 1008 );
			}
			else
			{
				$this = new PEAR_Error( "Unknown page format: " . $format );
				return;
			}

			$this->fwPt = $format[0];
			$this->fhPt = $format[1];
		}
		else
		{
			$this->fwPt = $format[0] * $this->k;
			$this->fhPt = $format[1] * $this->k;
		}
	
		$this->fw = $this->fwPt / $this->k;
		$this->fh = $this->fhPt / $this->k;
	
		// Page orientation
		$orientation = strtolower( $orientation );
	
		if ( $orientation == 'p' || $orientation == 'portrait' )
		{
			$this->defOrientation = 'P';
			
			$this->wPt = $this->fwPt;
			$this->hPt = $this->fhPt;
		}
		else if ( $orientation == 'l' || $orientation == 'landscape' )
		{
			$this->defOrientation = 'L';
			
			$this->wPt = $this->fhPt;
			$this->hPt = $this->fwPt;
		}
		else
		{
			$this = new PEAR_Error( "Incorrect orientation: " . $orientation );
			return;
		}
		
		$this->curOrientation = $this->defOrientation;
		$this->w = $this->wPt / $this->k;
		$this->h = $this->hPt / $this->k;
	
		// Page margins (1 cm)
		$margin = 28.35 / $this->k;
		$this->setMargins( $margin, $margin );
	
		// Interior cell margin (1 mm)
		$this->cMargin = $margin / 10;
	
		// Line width (0.2 mm)
		$this->lineWidth = .567 / $this->k;
	
		// Automatic page break
		$this->setAutoPageBreak( true, 2 * $margin );
	
		// Full width display mode
		$res = $this->setDisplayMode( 'fullwidth' );
		
		if ( PEAR::isError( $res ) )
		{
			$this = $res;
			return;
		}
	
		// Compression
		$this->setCompression( true );
		
		// Encryption
		$this->encrypted = false;
		$this->last_rc4_key = '';
		
		$this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08" . 
			"\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
	}


	/**
	 * @access  public
	 */
	function setMargins( $left, $top, $right = -1 )
	{
		$this->lMargin = $left;
		$this->tMargin = $top;

		if ( $right == -1 )
			$right = $left;
			
		$this->rMargin = $right;
	}
	
	/**
	 * @access  public
	 */
	function setLeftMargin( $margin )
	{
		$this->lMargin = $margin;
		
		if ( $this->page > 0 && $this->x < $margin )
			$this->x = $margin;
	}

	/**
	 * @access  public
	 */	
	function setTopMargin( $margin )
	{
		$this->tMargin = $margin;
	}
	
	/**
	 * @access  public
	 */
	function setRightMargin( $margin )
	{
		$this->rMargin = $margin;
	}
	
	/**
	 * @access  public
	 */
	function setAutoPageBreak( $auto, $margin = 0 )
	{
		$this->autoPageBreak    = $auto;
		$this->bMargin          = $margin;
		$this->pageBreakTrigger = $this->h - $margin;
	}

	/**
	 * @access  public
	 */	
	function setDisplayMode( $zoom, $layout = 'continuous' )
	{
		if ( $zoom == 'fullpage' || $zoom == 'fullwidth' || $zoom == 'real' || $zoom == 'default' || !is_string( $zoom ) )
			$this->zoomMode = $zoom;
		else if ( $zoom == 'zoom' )
			$this->zoomMode = $layout;
		else
			return PEAR::raiseError( 'Incorrect zoom display mode: ' . $zoom );
			
		if ( $layout == 'single' || $layout == 'continuous' || $layout == 'two' || $layout == 'default' )
			$this->layoutMode = $layout;
		else if ( $zoom != 'zoom' )
			return PEAR::raiseError( 'Incorrect layout display mode: ' . $layout );
	}
	
	/**
	 * @access  public
	 */
	function setCompression( $compress )
	{
		if ( function_exists( 'gzcompress' ) )
			$this->compress = $compress;
		else
			$this->compress = false;
	}
	
	/**
	 * @access  public
	 */
	function setTitle( $title )
	{
		$this->title = $title;
	}
	
	/**
	 * @access  public
	 */
	function setSubject( $subject )
	{
		$this->subject = $subject;
	}
	
	/**
	 * @access  public
	 */
	function setAuthor( $author )
	{
		$this->author = $author;
	}

	/**
	 * @access  public
	 */	
	function setKeywords( $keywords )
	{
		$this->keywords = $keywords;
	}
	
	/**
	 * @access  public
	 */
	function setCreator( $creator )
	{
		$this->creator = $creator;
	}

	/**
	 * @access  public
	 */	
	function aliasNbPages( $alias = '{nb}' )
	{
		$this->aliasNumPages = $alias;
	}
	
	/**
	 * @access  public
	 */
	function open()
	{
		$this->_begindoc();
	}

	/**
	 * @access  public
	 */	
	function close()
	{
		if ( $this->page == 0 )
			$this->addPage();
			
		// Page footer
		$this->inFooter = true;
		$this->footer();
		$this->inFooter = false;

		// Close page
		$this->_endpage();
		
		//Close document
		return $this->_enddoc();
	}
	
	/**
	 * @access  public
	 */
	function addPage( $orientation = '' )
	{
		$family = $this->fontFamily;
		$style  = $this->fontStyle . ( $this->underline? 'U' : '' );
		$size   = $this->fontSizePt;
		$lw     = $this->lineWidth;
		$dc     = $this->drawColor;
		$fc     = $this->fillColor;
		$tc     = $this->textColor;
		$cf     = $this->colorFlag;

		if ( $this->page > 0 )
		{
			// Page footer
			$this->inFooter = true;
			$this->footer();
			$this->inFooter = false;

			// Close page
			$this->_endpage();
		}
		
		// Start new page
		$this->_beginpage( $orientation );
		
		// Set line cap style to square
		$this->_out( '2 J' );
		
		// Set line width
		$this->lineWidth = $lw;
		$this->_out( sprintf( '%.2f w', $lw * $this->k ) );
		
		// Set font
		if ( $family )
		{
			$res = $this->setFont( $family, $style, $size );
			
			if ( PEAR::isError( $res ) )
				return $res;
		}
		
		// Set colors
		$this->drawColor = $dc;
		
		if ( $dc != '0 G' )
			$this->_out( $dc );
			
		$this->fillColor = $fc;
		
		if ( $fc != '0 g' )
			$this->_out( $fc );
			
		$this->textColor = $tc;
		$this->colorFlag = $cf;
		
		// Page header
		$this->header();
		
		// Restore line width
		if ( $this->lineWidth != $lw )
		{
			$this->lineWidth = $lw;
			$this->_out( sprintf( '%.2f w', $lw * $this->k ) );
		}
		
		// Restore font
		if ( $family )
		{
			$res = $this->setFont( $family, $style, $size );
				
			if ( PEAR::isError( $res ) )
				return $res;
		}
		
		// Restore colors
		if ( $this->drawColor != $dc )
		{
			$this->drawColor = $dc;
			$this->_out( $dc );
		}
		
		if ( $this->fillColor != $fc )
		{
			$this->fillColor = $fc;
			$this->_out( $fc );
		}
		
		$this->textColor = $tc;
		$this->colorFlag = $cf;
	}
	
	/**
	 * @abstract
	 */
	function header()
	{
	}

	/**
	 * @abstract
	 */	
	function footer()
	{
	}
	
	/**
	 * @access  public
	 */
	function pageNum()
	{
		return $this->page;
	}

	/**
	 * Set color for all stroking operations.
	 *
	 * @access  public
	 */
	function setDrawColor( $r, $g = -1, $b = -1 )
	{
		if ( ( $r == 0 && $g == 0 && $b == 0 ) || $g == -1 )
			$this->drawColor = sprintf( '%.3f G', $r / 255 );
		else
			$this->drawColor = sprintf( '%.3f %.3f %.3f RG', $r / 255, $g / 255, $b / 255 );
			
		if ( $this->page > 0 )
			$this->_out( $this->drawColor );
	}
	
	/**
	 * @access  public
	 */
	function setDrawColorRGB( $rgb )
	{
		$r = hexdec( substr( $rgb, 0, 2 ) );
		$g = hexdec( substr( $rgb, 2, 2 ) );
		$b = hexdec( substr( $rgb, 4, 2 ) );
		
		$this->setDrawColor( $r, $g, $b );
	}
		
	/**
	 * Set color for all filling operations.
	 *
	 * @access  public
	 */
	function setFillColor( $r, $g = -1, $b = -1 )
	{
		if ( ( $r == 0 && $g == 0 && $b == 0 ) || $g == -1 )
			$this->fillColor = sprintf( '%.3f g', $r / 255 );
		else
			$this->fillColor = sprintf( '%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255 );
			
		$this->colorFlag = ( $this->fillColor != $this->textColor );

		if ( $this->page > 0 )
			$this->_out( $this->fillColor );
	}
	
	/**
	 * @access  public
	 */
	function setTextColor( $r, $g = -1, $b = -1 )
	{
		if ( ( $r == 0 && $g == 0 && $b == 0 ) || $g == -1 )
			$this->textColor = sprintf( '%.3f g', $r / 255 );
		else
			$this->textColor = sprintf( '%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255 );
			
		$this->colorFlag = ( $this->fillColor != $this->textColor );
	}
	
	/**
	 * Get width of a string in the current font.
	 *
	 * @access  public
	 */
	function getStringWidth( $s )
	{
		$s  =  (string)$s;
		$cw =& $this->currentFont['cw'];
		$w  =  0;
		$l  =  strlen( $s );
		
		for ( $i = 0; $i < $l; $i++ )
			$w += $cw[$s{$i}];
			
		return $w * $this->fontSize / 1000;
	}
	
	/**
	 * Function to set permissions as well as user and owner passwords
	 *
	 * - permissions is an array with values taken from the following list:
	 *   copy, print, modify, annot-forms
	 *   If a value is present it means that the permission is granted
	 * - If a user password is set, user will be prompted before document is opened
	 * - If an owner password is set, document can be opened in privilege mode with no
	 *   restriction if that password is entered
	 *
	 * @access  public
	 */
	function setProtection( $permissions = array(), $user_pass = '', $owner_pass = '' )
	{
		$options = array(
			'print'       => 4, 
			'modify'      => 8, 
			'copy'        => 16, 
			'annot-forms' => 32 
		);
		
		$protection = 192;
		
		foreach ( $permissions as $permission )
		{
			if ( !isset( $options[$permission] ) )
				return PEAR::raiseError( 'Incorrect permission: ' . $permission );
				
			$protection += $options[$permission];
		}
		
		if ( $owner_pass == '' )
			$owner_pass = $user_pass;
			
		$this->encrypted = true;
		$this->_generateencryptionkey( $user_pass, $owner_pass, $protection );
	}
	
	/**
	 * @access  public
	 */
	function setLineWidth( $width )
	{
		$this->lineWidth = $width;

		if ( $this->page > 0 )
			$this->_out( sprintf( '%.2f w', $width * $this->k ) );
	}

	/**
	 * @access  public
	 */	
	function line( $x1, $y1, $x2, $y2 )
	{
		$this->_out( sprintf( '%.2f %.2f m %.2f %.2f l S', $x1 * $this->k, ( $this->h - $y1 ) * $this->k, $x2 * $this->k, ( $this->h - $y2 ) * $this->k ) );
	}

	/**
	 * @access  public
	 */	
	function rect( $x, $y, $w, $h, $style = '' )
	{
		if ( $style == 'F' )
			$op = 'f';
		else if ( $style == 'FD' || $style == 'DF' )
			$op = 'B';
		else
			$op = 'S';
			
		$this->_out( sprintf( '%.2f %.2f %.2f %.2f re %s', $x * $this->k, ( $this->h - $y ) * $this->k, $w * $this->k, -$h * $this->k, $op ) );
	}
	
	/**
	 * @access  public
	 */	
	function roundedRect( $x, $y, $w, $h, $r, $style = '' )
	{
		$k  = $this->k;
		$hp = $this->h;
		
		if ( $style == 'F' )
			$op = 'f';
		else if ( $style == 'FD' || $style == 'DF' )
			$op = 'B';
		else
			$op = 'S';
			
		$MyArc = 4 / 3 * ( sqrt( 2 ) - 1 );
		$this->_out( sprintf( '%.2f %.2f m', ( $x + $r ) * $k, ( $hp - $y ) * $k ) );
		$xc = $x + $w - $r ;
		$yc = $y + $r;
		$this->_out( sprintf( '%.2f %.2f l', $xc * $k, ( $hp - $y ) * $k ) );
		$this->_arc( $xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc );
		$xc = $x + $w - $r ;
		$yc = $y + $h - $r;
		$this->_out( sprintf( '%.2f %.2f l', ( $x + $w ) * $k, ( $hp - $yc ) * $k ) );
		$this->_arc( $xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r );
		$xc = $x + $r ;
		$yc = $y + $h - $r;
		$this->_out( sprintf( '%.2f %.2f l', $xc * $k, ( $hp - ( $y + $h ) ) * $k ) );
		$this->_arc( $xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc );
		$xc = $x + $r ;
		$yc = $y + $r;
		$this->_out( sprintf( '%.2f %.2f l', ( $x ) * $k, ( $hp - $yc ) * $k ) );
		$this->_arc( $xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r );
		$this->_out( $op );
	}
	
	/**
	 * Add a TrueType or Type1 font.
	 *
	 * @access  public
	 */
	function addFont( $family, $style = '', $file = '' )
	{
		$family = strtolower( $family );
		
		if ( $family == 'arial' )
			$family = 'helvetica';
			
		$style = strtoupper( $style );
		
		if ( $style == 'IB' )
			$style = 'BI';
			
		if ( isset( $this->fonts[$family . $style] ) )
			return PEAR::raiseError( 'Font already added: ' . $family . ' ' . $style );
		
		if ( $file == '' )
			$file = str_replace( ' ', '', $family ) . strtolower( $style ) . '.php';
		
		if ( defined( 'PDF_FONTPATH' ) )
			$file = PDF_FONTPATH . $file;
			
		include( $file );
		
		if ( !isset( $name ) )
			return PEAR::raiseError( 'Could not include font definition file.' );
		
		$i = count( $this->fonts ) + 1;
		
		$this->fonts[$family . $style] = array(
			'i'    => $i,
			'type' => $type,
			'name' => $name,
			'desc' => $desc,
			'up'   => $up,
			'ut'   => $ut,
			'cw'   => $cw,
			'enc'  => $enc,
			'file' => $file
		);
		
		if ( $diff )
		{
			// Search existing encodings
			$d  = 0;
			$nb = count( $this->diffs );
			
			for ( $i = 1; $i <= $nb; $i++ )
			{
				if ( $this->diffs[$i] == $diff )
				{
					$d = $i;
					break;
				}
			}
				
			if ( $d == 0 )
			{
				$d = $nb + 1;
				$this->diffs[$d] = $diff;
			}
			
			$this->fonts[$family . $style]['diff'] = $d;
		}
		
		if ( $file )
		{
			if ( $type == 'TrueType' )
				$this->fontFiles[$file] = array( 'length1' => $originalsize );
			else
				$this->fontFiles[$file] = array( 'length1' => $size1, 'length2' => $size2 );
		}
	}
	
	/**
	 * Select a font; size given in points.
	 *
	 * @access  public
	 */
	function setFont( $family, $style = '', $size = 0 )
	{
		global $pdf_charwidths;
	
		$family = strtolower( $family );
		
		if ( $family == '' )
			$family = $this->fontFamily;
		
		if ( $family == 'arial' )
			$family = 'helvetica';
		else if ( $family == 'symbol' || $family == 'zapfdingbats' )
			$style = '';
			
		$style = strtoupper( $style );
		
		if ( is_int( strpos( $style, 'U' ) ) )
		{
			$this->underline = true;
			$style = str_replace( 'U', '', $style );
		}
		else
		{
			$this->underline = false;
		}
		
		if ( $style == 'IB' )
			$style = 'BI';
		
		if ( $size == 0 )
			$size = $this->fontSizePt;
		
		// Test if font is already selected
		if ( $this->fontFamily == $family && $this->fontStyle == $style && $this->fontSizePt == $size )
			return;
		
		// Test if used for the first time
		$fontkey = $family . $style;
		
		if ( !isset( $this->fonts[$fontkey] ) )
		{
			// Check if one of the standard fonts
			if ( isset( $this->coreFonts[$fontkey] ) )
			{
				if ( !isset( $pdf_charwidths[$fontkey] ) )
				{
					// Load metric file
					$file = $family;
					
					if ( $family == 'times' || $family == 'helvetica' )
						$file .= strtolower( $style );
						
					$file .= '.php';
					
					if ( defined( 'PDF_FONTPATH' ) )
						$file = PDF_FONTPATH . $file;
					
					include( $file );
					
					if ( !isset( $pdf_charwidths[$fontkey] ) )
						return PEAR::raiseError( 'Could not include font metric file.' );
				}
				
				$i = count( $this->fonts ) + 1;

				$this->fonts[$fontkey] = array(
					'i'    => $i,
					'type' => 'core',
					'name' => $this->coreFonts[$fontkey],
					'up'   => -100,
					'ut'   => 50,
					'cw'   => $pdf_charwidths[$fontkey]
				);
			}
			else
			{
				return PEAR::raiseError( 'Undefined font: ' . $family . ' ' . $style );
			}
		}
		
		// Select it
		$this->fontFamily  =  $family;
		$this->fontStyle   =  $style;
		$this->fontSizePt  =  $size;
		$this->fontSize    =  $size / $this->k;
		$this->currentFont = &$this->fonts[$fontkey];

		if ( $this->page > 0 )
			$this->_out( sprintf( 'BT /F%d %.2f Tf ET', $this->currentFont['i'], $this->fontSizePt ) );
	}
	
	/**
	 * Set font size in points.
	 *
	 * @access  public
	 */
	function setFontSize( $size )
	{
		if ( $this->fontSizePt == $size )
			return;
			
		$this->fontSizePt = $size;
		$this->fontSize   = $size / $this->k;

		if ( $this->page > 0 )
			$this->_out( sprintf( 'BT /F%d %.2f Tf ET', $this->currentFont['i'], $this->fontSizePt ) );
	}
	
	/**
	 * Create a new internal link.
	 *
	 * @access  public
	 */
	function addLink()
	{
		$n = count( $this->links ) + 1;
		$this->links[$n] = array( 0, 0 );

		return $n;
	}
	
	/**
	 * Set destination of internal link.
	 *
	 * @access  public
	 */
	function setLink( $link, $y = 0, $page = -1 )
	{
		if ( $y == -1 )
			$y = $this->y;
		
		if ( $page == -1 )
			$page = $this->page;
		
		$this->links[$link] = array( $page, $y );
	}
	
	/**
	 * Put a link on the page.
	 *
	 * @access  public
	 */
	function link( $x, $y, $w, $h, $link )
	{
		$this->pageLinks[$this->page][] = array( $x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h * $this->k, $link );
	}
	
	/**
	 * @access  public
	 */
	function setDash( $black = false, $white = false )
	{
		if ( $black && $white )
			$s = sprintf( '[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k );
		else
			$s = '[] 0 d';
			
		$this->_out( $s );
	}
	
	/**
	 * @access  public
	 */
	function dashedRect( $x1, $y1, $x2, $y2, $width = 1, $nb = 15 )
	{
		$this->setLineWidth( $width );
		
		$width  = abs( $x1 - $x2 );
		$height = abs( $y1 - $y2 );
		
		if ( $width > $height ) 
		{
			$points = ( $width / $nb ) / 2; // length of dashes
		}
		else 
		{
			$points = ( $height / $nb ) / 2;
		}
		
		for ( $i = $x1; $i <= $x2; $i += $points + $points ) 
		{
			for ( $j = $i; $j <= ( $i + $points ); $j++ ) 
			{
				if ( $j <= ( $x2 - 1 ) ) 
				{
					$this->line( $j, $y1, $j + 1, $y1 ); // upper dashes
					$this->line( $j, $y2, $j + 1, $y2 ); // lower dashes
				}
			}
		}
		
		for ( $i = $y1; $i <= $y2; $i += $points + $points ) 
		{
			for ( $j = $i; $j <= ( $i + $points ); $j++ ) 
			{
				if ( $j <= ( $y2 - 1 ) ) 
				{
					$this->line( $x1, $j, $x1, $j + 1 ); // left dashes
					$this->line( $x2, $j, $x2, $j + 1 ); // right dashes
				}
			}
		}
	}

	/**
	 * @access  public
	 */	
	function rotate( $angle, $x = -1, $y = -1 )
	{
		if ( $x == -1 )
			$x = $this->x;
	
		if ( $y == -1 )
			$y = $this->y;
	
		if ( $this->angle != 0 )
			$this->_out( 'Q' );

		$this->angle = $angle;
	
		if ( $angle != 0 )
		{
			$angle *= M_PI / 180;
			$c  = cos( $angle );
			$s  = sin( $angle );
			$cx = $x * $this->k;
			$cy = ( $this->h - $y ) * $this->k;
	
			$this->_out( sprintf( 'q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy ) );
		}
	}

	/**
	 * Text rotated around its origin.
	 *
	 * @access  public
	 */	
	function rotatedText( $x, $y, $txt, $angle )
	{
		$this->rotate( $angle, $x, $y );
		$this->text( $x, $y, $txt );
		$this->rotate( 0 );
	}

	/**
	 * Image rotated around its upper-left corner.
	 *
	 * @access  public
	 */	
	function rotatedImage( $file, $x, $y, $w, $h, $angle )
	{
		$this->rotate( $angle, $x, $y );
		$this->image( $file, $x, $y, $w, $h );
		$this->rotate( 0 );
	}

	/**
	 * @access  public
	 */	
	function includeJavaScript( $script ) 
	{
		$this->javascript = $script;
	}
	
	/**
	 * @access  public
	 */
	function text( $x, $y, $txt )
	{
		$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
		$s   = sprintf( 'BT %.2f %.2f Td (%s) Tj ET', $x * $this->k, ( $this->h - $y ) * $this->k, $txt );

		if ( $this->underline && $txt != '' )
			$s .= ' ' . $this->_dounderline( $x, $y, $txt );
			
		if ( $this->colorFlag )
			$s = 'q ' . $this->textColor . ' ' . $s . ' Q';

		$this->_out( $s );
	}
	
	/**
	 * @access  public
	 */
	function textWithDirection( $x, $y, $txt, $direction = 'R' )
	{
		$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
		
		if ( $direction == 'R' )
			$s = sprintf( 'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET', 1, 0, 0, 1, $x * $this->k, ( $this->h - $y ) * $this->k, $txt );
		else if ( $direction == 'L' )
			$s = sprintf( 'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET', -1, 0, 0, -1, $x * $this->k, ( $this->h - $y ) * $this->k, $txt );
		else if ( $direction == 'U' )
			$s = sprintf( 'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET', 0, 1, -1, 0, $x * $this->k, ( $this->h - $y ) * $this->k, $txt );
		else if ( $direction == 'D' )
			$s = sprintf( 'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET', 0, -1, 1, 0, $x * $this->k, ( $this->h - $y ) * $this->k, $txt );
		else
			$s = sprintf( 'BT %.2f %.2f Td (%s) Tj ET', $x * $this->k, ( $this->h - $y ) * $this->k, $txt );
	
		$this->_out( $s );
	}

	/**
	 * @access  public
	 */
	function textWithRotation( $x, $y, $txt, $txt_angle, $font_angle = 0 )
	{
		$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );

		$font_angle += 90 + $txt_angle;
		$txt_angle  *= M_PI / 180;
		$font_angle *= M_PI / 180;

		$txt_dx  = cos( $txt_angle  );
		$txt_dy  = sin( $txt_angle  );
		$font_dx = cos( $font_angle );
		$font_dy = sin( $font_angle );

		$s = sprintf( 'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
			 $txt_dx,
			 $txt_dy,
			 $font_dx,
			 $font_dy,
			 $x * $this->k,
			 ( $this->h - $y ) * $this->k,
			 $txt
		);
	
		$this->_out( $s );
	}

	/**
	 * Accept automatic page break or not.
	 *
	 * @access  public
	 */
	function acceptPageBreak()
	{
		return $this->autoPageBreak;
	}

	/**
	 * Output a cell.
	 *
	 * @access  public
	 */
	function cell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '' )
	{
		$k = $this->k;
		
		if ( $this->y + $h > $this->pageBreakTrigger && !$this->inFooter && $this->acceptPageBreak() )
		{
			$x  = $this->x;
			$ws = $this->ws;
			
			if ( $ws > 0 )
			{
				$this->ws = 0;
				$this->_out( '0 Tw' );
			}
			
			$this->addPage( $this->curOrientation );
			$this->x = $x;
		
			if ( $ws > 0 )
			{
				$this->ws = $ws;
				$this->_out( sprintf( '%.3f Tw', $ws * $k ) );
			}
		}
	
		if ( $w == 0 )
    	    $w = $this->w - $this->rMargin - $this->x;

		$s = '';
	
		if ( $fill == 1 || $border == 1 )
		{
			if ( $fill == 1 )
				$op = ( $border == 1 )? 'B' : 'f';
			else
				$op = 'S';
		
			$s = sprintf( '%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ( $this->h - $this->y ) * $k, $w * $k, -$h * $k, $op );
		}
	
		if ( is_string( $border ) )
		{
			$x = $this->x;
			$y = $this->y;
		
			if ( is_int( strpos( $border, 'L' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, $x * $k, ( $this->h - ( $y + $h ) ) * $k );
		
			if ( is_int( strpos( $border, 'T' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - $y ) * $k );
			
			if ( is_int( strpos( $border, 'R' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', ( $x + $w ) * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
			
			if ( is_int( strpos( $border, 'B' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - ( $y + $h ) ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
		}
			
		if ( $txt != '' )
		{
			if ( $align == 'R' )
			{
				$dx = $w - $this->cMargin - $this->getStringWidth( $txt );
			}
			else if ( $align == 'C' )
			{
				$dx = ( $w - $this->getStringWidth( $txt ) ) / 2;
			}
			else if ( $align == 'FJ' )
			{
				// Set word spacing
				$wmax = ( $w - 2 * $this->cMargin );
				$this->ws = ( $wmax - $this->getStringWidth( $txt ) ) / substr_count( $txt, ' ' );
				$this->_out( sprintf( '%.3f Tw', $this->ws * $this->k ) );
				$dx = $this->cMargin;
			}
			else
			{
				$dx = $this->cMargin;
			}
			
			$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
		
			if ( $this->colorFlag )
				$s .= 'q ' . $this->textColor . ' ';
			
			$s .= sprintf( 'BT %.2f %.2f Td (%s) Tj ET', ( $this->x + $dx ) * $k, ( $this->h - ( $this->y + .5 * $h + .3 * $this->fontSize ) ) * $k, $txt );
			
			if ( $this->underline )
				$s .= ' ' . $this->_dounderline( $this->x + $dx, $this->y + .5 * $h + .3 * $this->fontSize, $txt );
				
			if ( $this->colorFlag )
				$s .= ' Q';
		
			if ( $link )
			{
				if ( $align == 'FJ' )
					$wlink = $wmax;
				else
					$wlink = $this->getStringWidth( $txt );
				
				$this->link( $this->x + $dx, $this->y + .5 * $h - .5 * $this->fontSize, $wlink, $this->fontSize, $link );
			}
		}
		
		if ( $s )
			$this->_out( $s );
	
		if ( $align == 'FJ' )
		{
			// Remove word spacing
			$this->_out( '0 Tw' );
			$this->ws = 0;
		}
	
		$this->lasth = $h;
	
		if ( $ln > 0 )
		{
			$this->y += $h;
		
			if ( $ln == 1 )
				$this->x = $this->lMargin;
		}
		else
		{
			$this->x += $w;
		}
	}
	
	/**
	 * Extended versions of the cell method which prints vertical text.
	 * If the cell contains a single line and its length exceeds the size 
	 * of the cell, the text will be compressed to fit.
     *
	 * @access public
	 */
	function vCell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0 )
	{
		// output a cell
		$k = $this->k;
		
		if ( $this->y + $h > $this->pageBreakTrigger && !$this->inFooter && $this->acceptPageBreak() )
		{
			$x  = $this->x;
			$ws = $this->ws;
			
			if ( $ws > 0 )
			{
				$this->ws = 0;
				$this->_out( '0 Tw' );
			}
			
			$this->addPage( $this->curOrientation );
			$this->x = $x;
			
			if ( $ws > 0 )
			{
				$this->ws = $ws;
				$this->_out( sprintf( '%.3f Tw', $ws * $k ) );
			}
		}
	
		if ( $w == 0 )
			$w = $this->w - $this->rMargin - $this->x;
		
		$s = '';

		if ( $fill == 1 || $border > 0 )
		{
			if ( $fill == 1 )
				$op = ( $border > 0 )? 'B' : 'f';
			else
				$op = 'S';
		
			if ( $border > 1 )
				$s = sprintf( ' q %.2f w %.2f %.2f %.2f %.2f re %s Q ',$border, $this->x * $k, ( $this->h - $this->y ) * $k, $w * $k, -$h * $k, $op );
			else
				$s = sprintf( '%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ( $this->h - $this->y ) * $k, $w * $k, -$h * $k, $op );
		}
		
		if ( is_string( $border ) )
		{
			$x = $this->x;
			$y = $this->y;
		
			if ( is_int( strpos( $border, 'L' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, $x * $k, ( $this->h - ( $y + $h ) ) * $k );
			else if ( is_int( strpos( $border, 'l' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', $x * $k, ( $this->h - $y ) * $k, $x * $k, ( $this->h - ( $y + $h ) ) * $k );
			
			if ( is_int( strpos( $border, 'T' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - $y ) * $k );
			else if ( is_int( strpos( $border, 't' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', $x * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - $y ) * $k );
		
			if ( is_int( strpos( $border, 'R' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', ( $x + $w ) * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
			else if ( is_int( strpos( $border, 'r' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', ( $x + $w ) * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
		
			if ( is_int( strpos( $border, 'B' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - ( $y + $h ) ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
			else if ( is_int( strpos( $border, 'b' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', $x * $k, ( $this->h - ( $y + $h ) ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
		}
	
		if ( trim( $txt ) != '' )
		{
			$cr = substr_count( $txt, "\n" );
			
			// Multi line
			if ( $cr > 0 ) 
			{
				$txts  = explode( "\n", $txt );
				$lines = count( $txts );
				
				for ( $l = 0; $l < $lines; $l++ ) 
				{
					$txt   = $txts[$l];
					$w_txt = $this->getStringWidth( $txt );
				
					if ( $align == 'U' )
						$dy = $this->cMargin + $w_txt;
					else if ( $align == 'D' )
						$dy = $h - $this->cMargin;
					else
						$dy = ( $h + $w_txt ) / 2;
					
					$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
				
					if ( $this->colorFlag )
						$s .= 'q ' . $this->textColor . ' ';
				
					$s .= sprintf( 'BT 0 1 -1 0 %.2f %.2f Tm (%s) Tj ET ',
						( $this->x + .5 * $w + ( .7 + $l - $lines / 2 ) * $this->fontSize ) * $k,
						( $this->h - ( $this->y + $dy ) ) * $k, $txt );
				
					if ( $this->colorFlag )
						$s .= 'Q ';
				}
			}
			// Single line
			else 
			{
				$w_txt = $this->getStringWidth( $txt );
				$Tz    = 100;
			
				if ( $w_txt > $h - 2 * $this->cMargin ) 
				{
					$Tz    = ( $h - 2 * $this->cMargin ) / $w_txt * 100;
					$w_txt = $h - 2 * $this->cMargin;
				}
			
				if ( $align == 'U' )
					$dy = $this->cMargin + $w_txt;
				else if ( $align == 'D' )
					$dy = $h - $this->cMargin;
				else
					$dy = ( $h + $w_txt ) / 2;
			
				$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
			
				if ( $this->colorFlag )
					$s .= 'q ' . $this->textColor . ' ';
			
				$s .= sprintf( 'q BT 0 1 -1 0 %.2f %.2f Tm %.2f Tz (%s) Tj ET Q ',
					( $this->x + .5 * $w + .3 * $this->fontSize ) * $k,
					( $this->h - ( $this->y + $dy ) ) * $k, $Tz, $txt );
			
				if ( $this->colorFlag )
					$s .= 'Q ';
			}
		}

		if ( $s )
			$this->_out( $s );
		
		$this->lasth = $h;
		
		if ( $ln > 0 )
		{
			// Go to next line
			$this->y += $h;
		
			if ( $ln == 1 )
				$this->x = $this->lMargin;
		}
		else
		{
			$this->x += $w;
		}
	}

	/**
	 * Extended versions of the cell method which prints horizontal text.
	 * If the cell contains a single line and its length exceeds the size 
	 * of the cell, the text will be compressed to fit.
	 *
	 * @access public
	 */
	function hCell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '' )
	{
		// Output a cell
		$k = $this->k;
		
		if ( $this->y + $h > $this->pageBreakTrigger && !$this->inFooter && $this->acceptPageBreak() )
		{
			$x  = $this->x;
			$ws = $this->ws;
			
			if ( $ws > 0 )
			{
				$this->ws = 0;
				$this->_out( '0 Tw' );
			}
			
			$this->addPage( $this->curOrientation );
			$this->x = $x;
		
			if ( $ws > 0 )
			{
				$this->ws = $ws;
				$this->_out( sprintf( '%.3f Tw', $ws * $k ) );
			}
		}
	
		if ( $w == 0 )
			$w = $this->w - $this->rMargin - $this->x;
	
		$s = '';

		if ( $fill == 1 || $border > 0 )
		{
			if ( $fill == 1 )
				$op = ( $border > 0 )? 'B' : 'f';
			else
				$op = 'S';
		
			if ( $border > 1 )
				$s = sprintf( ' q %.2f w %.2f %.2f %.2f %.2f re %s Q ', $border, $this->x * $k, ( $this->h - $this->y ) * $k, $w * $k, -$h * $k, $op );
			else
				$s = sprintf( '%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ( $this->h - $this->y ) * $k, $w * $k, -$h * $k, $op );
		}
	
		if ( is_string( $border ) )
		{
			$x = $this->x;
			$y = $this->y;
		
			if ( is_int( strpos( $border, 'L' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, $x * $k, ( $this->h - ( $y + $h ) ) * $k );
			else if ( is_int( strpos( $border, 'l' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', $x * $k, ( $this->h - $y ) * $k, $x * $k, ( $this->h - ( $y + $h ) ) * $k );
			
			if ( is_int( strpos( $border, 'T' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - $y ) * $k );
			else if ( is_int( strpos( $border, 't' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', $x * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - $y ) * $k );
		
			if ( is_int( strpos( $border, 'R' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', ( $x + $w ) * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
			else if ( is_int( strpos( $border, 'r' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', ( $x + $w ) * $k, ( $this->h - $y ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k);
		
			if ( is_int( strpos( $border, 'B' ) ) )
				$s .= sprintf( '%.2f %.2f m %.2f %.2f l S ', $x * $k, ( $this->h - ( $y + $h ) ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
			else if ( is_int( strpos( $border, 'b' ) ) )
				$s .= sprintf( 'q 2 w %.2f %.2f m %.2f %.2f l S Q ', $x * $k, ( $this->h - ( $y + $h ) ) * $k, ( $x + $w ) * $k, ( $this->h - ( $y + $h ) ) * $k );
		}
		
		if ( trim( $txt ) != '' ) 
		{
			$cr = substr_count( $txt, "\n" );
		
			// Multi line
			if ( $cr > 0 ) 
			{
				$txts  = explode( "\n", $txt );
				$lines = count( $txts );
				
				// $dy = ( $h - 2 * $this->cMargin ) / $lines;
		
				for ( $l = 0; $l < $lines; $l++ ) 
				{
					$txt   = $txts[$l];
					$w_txt = $this->getStringWidth( $txt );
				
					if ( $align == 'R' )
						$dx = $w - $w_txt - $this->cMargin;
					else if ( $align == 'C' )
						$dx = ( $w - $w_txt ) / 2;
					else
						$dx = $this->cMargin;

					$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
				
					if ( $this->colorFlag )
						$s .= 'q ' . $this->textColor . ' ';
					
					$s .= sprintf( 'BT %.2f %.2f Td (%s) Tj ET ',
						( $this->x + $dx ) * $k,
						( $this->h - ( $this->y + .5 * $h + ( .7 + $l - $lines / 2 ) * $this->fontSize ) ) * $k, $txt );
					
					if ( $this->underline )
						$s .= ' ' . $this->_dounderline( $this->x + $dx, $this->y + .5 * $h + .3 * $this->fontSize, $txt );
				
					if ( $this->colorFlag )
						$s .= 'Q ';
				
					if ( $link )
						$this->link( $this->x + $dx, $this->y + .5 * $h - .5 * $this->fontSize, $w_txt, $this->fontSize, $link );
				}
			}
			// Single line
			else 
			{
				$w_txt = $this->getStringWidth( $txt );
				$Tz = 100;
				
				// Need compression
				if ( $w_txt > $w - 2 * $this->cMargin ) 
				{
					$Tz = ( $w - 2 * $this->cMargin ) / $w_txt * 100;
					$w_txt = $w - 2 * $this->cMargin;
				}
	
				if ( $align == 'R' )
					$dx = $w - $w_txt - $this->cMargin;
				else if ( $align == 'C' )
					$dx = ( $w - $w_txt ) / 2;
				else
					$dx = $this->cMargin;
	
				$txt = str_replace( ')', '\\)', str_replace( '(', '\\(', str_replace( '\\', '\\\\', $txt ) ) );
			
				if ( $this->colorFlag )
					$s .= 'q ' . $this->textColor . ' ';
			
				$s .= sprintf( 'q BT %.2f %.2f Td %.2f Tz (%s) Tj ET Q ',
					( $this->x + $dx ) * $k,
					( $this->h - ( $this->y + .5 * $h + .3 * $this->fontSize ) ) * $k, $Tz, $txt );
					
				if ( $this->underline )
					$s .= ' ' . $this->_dounderline( $this->x + $dx, $this->y + .5 * $h + .3 * $this->fontSize, $txt );
		
				if ( $this->colorFlag )
					$s .= 'Q ';
					
				if ( $link )
					$this->link( $this->x + $dx, $this->y + .5 * $h - .5 * $this->fontSize, $w_txt, $this->fontSize, $link );
			}
		}

		if ( $s )
			$this->_out( $s );
	
		$this->lasth = $h;
	
		if ( $ln > 0 )
		{
			// Go to next line
			$this->y += $h;
			
			if ( $ln == 1 )
				$this->x = $this->lMargin;
		}
		else
		{
			$this->x += $w;
		}
	}

	/**
	 * @access public
	 */
	function multicell( $w, $h, $txt, $border = 0, $align = 'J', $fill = 0, $maxline = 0 )
	{
		// Output text with automatic or explicit line breaks, maximum of $maxlines
		$cw = &$this->currentFont['cw'];
		
		if ( $w == 0 )
			$w = $this->w - $this->rMargin - $this->x;
		
		$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->fontSize;
		$s    = str_replace( "\r", '', $txt );
		$nb   = strlen( $s );
		
		if ( $nb > 0 && $s[$nb-1] == "\n" )
			$nb--;
		
		$b = 0;
		
		if ( $border )
		{
			if ( $border == 1 )
			{
				$border = 'LTRB';
				$b  = 'LRT';
				$b2 = 'LR';
			}
			else
			{
				$b2 = '';
				
				if ( is_int( strpos( $border, 'L' ) ) )
					$b2 .= 'L';
				
				if ( is_int( strpos( $border, 'R' ) ) )
					$b2 .= 'R';
					
				$b = is_int( strpos( $border, 'T' ) )? $b2 . 'T' : $b2;
			}
		}
		
		$sep = -1;
		$i   = 0;
		$j   = 0;
		$l   = 0;
		$ns  = 0;
		$nl  = 1;
		
		while ( $i < $nb )
		{
			// Get next character
			$c = $s[$i];
			
			if ( $c == "\n" )
			{
				// Explicit line break
				if ( $this->ws > 0 )
				{
					$this->ws = 0;
					$this->_out( '0 Tw' );
				}
				
				$this->cell( $w, $h, substr( $s, $j, $i - $j ), $b, 2, $align, $fill );
				$i++;
				
				$sep = -1;
				$j   = $i;
				$l   = 0;
				$ns  = 0;
				$nl++;
				
				
				if ( $border && $nl == 2 )
					$b = $b2;
				
				if ( $maxline && $nl > $maxline ) 
					return substr( $s, $i );
				
				continue;
			}
			
			if ( $c == ' ' )
			{
				$sep = $i;
				$ls  = $l;
				
				$ns++;
			}
			
			$l += $cw[$c];
			
			if ( $l > $wmax)
			{
				// Automatic line break
				if ( $sep == -1 )
				{
					if ( $i == $j )
						$i++;
						
					if ( $this->ws > 0 )
					{
						$this->ws = 0;
						$this->_out( '0 Tw' );
					}
					
					$this->cell( $w, $h, substr( $s, $j, $i - $j ), $b, 2, $align, $fill );
				}
				else
				{
					if ( $align == 'J' )
					{
						$this->ws = ( $ns > 1 )? ( $wmax - $ls ) / 1000 * $this->fontSize / ( $ns - 1 ) : 0;
						$this->_out( sprintf( '%.3f Tw', $this->ws * $this->k ) );
					}
					
					$this->cell( $w, $h, substr( $s, $j, $sep - $j ), $b, 2, $align, $fill );
					$i = $sep + 1;
				}
				
				$sep = -1;
				$j   = $i;
				$l   = 0;
				$ns  = 0;

				$nl++;

				if ( $border && $nl == 2 )
					$b = $b2;
					
				if ( $maxline && $nl > $maxline ) 
					return substr( $s, $i );
			}
			else
			{
				$i++;
			}
		}
		
		// Last chunk
		if ( $this->ws > 0 )
		{
			$this->ws = 0;
			$this->_out( '0 Tw' );
		}
		
		if ( $border && is_int( strpos( $border, 'B' ) ) )
			$b .= 'B';
			
		$this->cell( $w, $h, substr( $s, $j, $i - $j ), $b, 2, $align, $fill );
		$this->x = $this->lMargin;

		return '';
	}
	
	/**
	 * @access  public
	 */
	function createIndex()
	{
		//Index title
		$this->setFontSize( 20 );
		$this->cell( 0, 5, 'Index', 0, 1, 'C' );
		$this->setFontSize( 15 );
		$this->ln( 10 );

		$size = sizeof( $this->outlines );
		$PageCellSize = $this->getStringWidth( 'p. ' . $this->outlines[$size - 1]['p'] ) + 2;
	
		for ( $i = 0; $i < $size; $i++ )
		{
			// Offset
			$level = $this->outlines[$i]['l'];
		
			if ( $level > 0 )
				$this->cell( $level * 8 );

			// Caption
			$str = $this->outlines[$i]['t'];
			$strsize = $this->getStringWidth( $str );
			$avail_size = $this->w - $this->lMargin - $this->rMargin - $PageCellSize - ( $level * 8 ) - 4;
			
			while ( $strsize >= $avail_size )
			{
				$str = substr( $str, 0, -1 );
				$strsize = $this->getStringWidth( $str );
			}
			
			$this->cell( $strsize + 2, $this->fontSize + 2, $str );

			// Filling dots
			$w    = $this->w - $this->lMargin - $this->rMargin - $PageCellSize - ( $level * 8 ) - ( $strsize + 2 );
			$nb   = $w / $this->getStringWidth( '.' );
			$dots = str_repeat( '.', $nb );
			
			$this->cell( $w, $this->fontSize + 2, $dots, 0, 0, 'R' );

			// Page number
			$this->cell( $PageCellSize, $this->fontSize + 2, 'p. ' . $this->outlines[$i]['p'], 0, 1, 'R' );
		}
	}

	/**
	 * Output text in flowing mode.
	 *
	 * @access  public
	 */
	function write( $h, $txt, $link = '' )
	{
		$cw   =& $this->currentFont['cw'];
		$w    =  $this->w - $this->rMargin - $this->x;
		$wmax =  ( $w - 2 * $this->cMargin ) * 1000 / $this->fontSize;
		$s    =  str_replace( "\r", '', $txt );
		$nb   =  strlen( $s );
		$sep  =  -1;
		$i    =  0;
		$j    =  0;
		$l    =  0;
		$nl   =  1;
		
		while ( $i < $nb )
		{
			// Get next character
			$c = $s{$i};
			
			if ( $c == "\n" )
			{
				// Explicit line break
				$this->cell( $w, $h, substr( $s, $j, $i - $j ), 0, 2, '', 0, $link );
				$i++;
				
				$sep = -1;
				$j   = $i;
				$l   = 0;
				
				if ( $nl == 1 )
				{
					$this->x = $this->lMargin;
					$w    = $this->w - $this->rMargin - $this->x;
					$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->fontSize;
				}
				
				$nl++;
				continue;
			}
			
			if ( $c == ' ' )
			{
				$sep = $i;
				$ls  = $l;
			}
			
			$l += $cw[$c];
			
			if ( $l > $wmax )
			{
				// Automatic line break
				if ( $sep == -1 )
				{
					if ( $this->x > $this->lMargin )
					{
						// Move to next line
						$this->x  = $this->lMargin;
						$this->y += $h;
						
						$w    = $this->w - $this->rMargin - $this->x;
						$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->fontSize;

						$i++;
						$nl++;

						continue;
					}
					
					if ( $i == $j )
						$i++;

					$this->cell( $w, $h, substr( $s, $j, $i - $j ), 0, 2, '', 0, $link );
				}
				else
				{
					$this->cell( $w, $h, substr( $s, $j, $sep - $j ), 0, 2, '', 0, $link );
					$i = $sep + 1;
				}
				
				$sep = -1;
				$j   = $i;
				$l   = 0;
				
				if ( $nl == 1 )
				{
					$this->x = $this->lMargin;
					$w    = $this->w - $this->rMargin - $this->x;
					$wmax = ( $w - 2 * $this->cMargin ) * 1000 / $this->fontSize;
				}
				
				$nl++;
			}
			else
			{
				$i++;
			}
		}
		
		// Last chunk
		if ( $i != $j )
			$this->cell( $l / 1000 * $this->fontSize, $h, substr( $s, $j, $i ), 0, 0, '', 0, $link );
	}
	
	/**
	 * @access  public
	 */
	function justify( $text, $w, $h )
	{
		$tab_paragraph = explode( "\n", $text );
		$nb_paragraph  = count( $tab_paragraph );
		$j = 0;

		while ( $j < $nb_paragraph ) 
		{
			$paragraph = $tab_paragraph[$j];
			$tab_word  = explode( ' ', $paragraph );
			$nb_word   = count( $tab_word );

			// Handle strings longer than paragraph width
			$k = 0;
			$l = 0;
		
			while ( $k < $nb_word ) 
			{
				$len_word = strlen( $tab_word[$k] );
				
				if ( $len_word < ( $w - 5 ) )
				{
					$tab_word2[$l] = $tab_word[$k];
					$l++;	
				} 
				else 
				{
					$m = 0;
					$chaine_letter = '';
				
					while ( $m < $len_word ) 
					{
						$letter = substr( $tab_word[$k], $m, 1 );
						$len_chaine_letter = $this->getStringWidth( $chaine_letter . $letter );

						if ( $len_chaine_letter > ( $w - 7 ) ) 
						{
							$tab_word2[$l] = $chaine_letter . '-';
							$chaine_letter = $letter;
						
							$l++;
						} 
						else 
						{
							$chaine_letter .= $letter;
						}
					
						$m++;
					}
				
					if ( $chaine_letter ) 
					{
						$tab_word2[$l] = $chaine_letter;
						$l++;
					}
				}
			
				$k++;
			}

			// Justified lines
			$nb_word = count( $tab_word2 );
			$i      = 0;
			$line  = '';
			
			while ( $i < $nb_word ) 
			{
				$word = $tab_word2[$i];
				$len_line = $this->getStringWidth( $line . ' ' . $word );

				if ( $len_line > ( $w - 5 ) ) 
				{
					$len_line = $this->getStringWidth( $line );
					$nb_carac = strlen( $line );
					$ecart    = ( ( $w - 2 ) - $len_line ) / $nb_carac;
					
					$this->_out( sprintf( 'BT %.3f Tc ET', $ecart * $this->k ) );
					$this->multicell( $w, $h, $line );
					
					$line = $word;
				} 
				else 
				{
					if ( $line )
						$line .= ' ' . $word;
					else
						$line = $word;
				}
			
				$i++;
			}

			// Last line
			$this->_out( 'BT 0 Tc ET' );
			$this->multicell( $w, $h, $line );
			
			$tab_word  = '';
			$tab_word2 = '';
			
			$j++;
		}
	}

	/**
	 * Put an image on the page.
	 *
	 * @access  public
	 */
	function image( $file, $x, $y, $w, $h = 0, $type = '', $link = '' )
	{
		if ( !isset( $this->images[$file] ) )
		{
			// First use of image, get info
			if ( $type == '' )
			{
				$pos = strrpos( $file, '.' );
				
				if ( !$pos )
					return PEAR::raiseError( 'Image file has no extension and no type was specified: ' . $file );
					
				$type = substr( $file, $pos + 1 );
			}
			
			$type = strtolower( $type );
			$mqr  = get_magic_quotes_runtime();

			set_magic_quotes_runtime( 0 );

			if ( $type == 'jpg' || $type == 'jpeg' )
			{
				$info = $this->_parsejpg( $file );
				
				if ( PEAR::isError( $info ) )
					return $info;
			}
			else if ( $type=='png' )
			{
				$info = $this->_parsepng( $file );
				
				if ( PEAR::isError( $info ) )
					return $info;
			}
			else
			{
				return PEAR::raiseError( 'Unsupported image file type: ' . $type );
			}
			
			set_magic_quotes_runtime( $mqr );
			$info['i'] = count( $this->images ) + 1;
			$this->images[$file] = $info;
		}
		else
		{
			$info = $this->images[$file];
		}
		
		// Automatic width || height calculation
		if ( $w == 0 )
			$w = $h * $info['w'] / $info['h'];
		
		if ( $h == 0 )
			$h = $w * $info['h'] / $info['w'];
		
		$this->_out( sprintf( 'q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ( $this->h - ( $y + $h ) ) * $this->k, $info['i'] ) );
		
		if ( $link )
			$this->link( $x, $y, $w, $h, $link );
	}
	
	/**
	 * @access  public
	 */
	function circle( $x, $y, $r, $style = '' )
	{
		$this->ellipse( $x, $y, $r, $r, $style );
	}

	/**
	 * @access  public
	 */
	function ellipse( $x, $y, $rx, $ry, $style = 'D' )
	{
		if ( $style == 'F' )
			$op = 'f';
		else if ( $style == 'FD' || $style == 'DF' )
			$op = 'B';
		else
			$op = 'S';
	
		$lx = 4 / 3 * ( M_SQRT2 - 1 ) * $rx;
		$ly = 4 / 3 * ( M_SQRT2 - 1 ) * $ry;
		$k  = $this->k;
		$h  = $this->h;
	
		$this->_out( sprintf( '%.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c',
			( $x + $rx ) * $k, ( $h - $y ) * $k,
			( $x + $rx ) * $k, ( $h - ( $y - $ly ) ) * $k,
			( $x + $lx ) * $k, ( $h - ( $y - $ry ) ) * $k,
			$x * $k, ( $h - ( $y - $ry ) ) * $k )
		);

		$this->_out( sprintf( '%.2f %.2f %.2f %.2f %.2f %.2f c',
			( $x - $lx ) * $k, ( $h - ( $y - $ry ) ) * $k,
			( $x - $rx ) * $k, ( $h - ( $y - $ly ) ) * $k,
			( $x - $rx ) * $k, ( $h - $y ) * $k )
		);
	
		$this->_out( sprintf( '%.2f %.2f %.2f %.2f %.2f %.2f c',
			( $x - $rx ) * $k, ( $h - ( $y + $ly ) ) * $k,
			( $x - $lx ) * $k, ( $h - ( $y + $ry ) ) * $k,
			$x * $k, ( $h - ( $y + $ry ) ) * $k )
		);

		$this->_out( sprintf( '%.2f %.2f %.2f %.2f %.2f %.2f c %s',
			( $x + $lx ) * $k, ( $h - ( $y + $ry ) ) * $k,
			( $x + $rx ) * $k, ( $h - ( $y + $ly ) ) * $k,
			( $x + $rx ) * $k, ( $h - $y ) * $k,
			$op )
		);
	}

	/**
	 * @access  public
	 */
	function sector( $xc, $yc, $r, $a, $b, $style = 'FD', $cw = true, $o = 90 )
	{
		if ( $cw )
		{
			$d = $b;
			$b = $o - $a;
			$a = $o - $d;
		}
		else
		{
			$b += $o;
			$a += $o;
		}
		
		$a = ( $a % 360 ) + 360;
		$b = ( $b % 360 ) + 360;

		if ( $a > $b )
			$b += 360;
			
		$b = $b / 360 * 2 * M_PI;
		$a = $a / 360 * 2 * M_PI;
		$d = $b - $a;
		
		if ( $d == 0 )
			$d = 2 * M_PI;
		
		$k  = $this->k;
		$hp = $this->h;
		
		if ( $style == 'F' )
			$op = 'f';
		else if ( $style == 'FD' || $style == 'DF' )
			$op = 'b';
		else
			$op = 's';
			
		if ( sin( $d / 2 ) )
			$MyArc = 4 / 3 * ( 1 - cos( $d / 2 ) ) / sin( $d / 2 ) * $r;
		
		// first put the center
		$this->_out( sprintf( '%.2f %.2f m',( $xc ) * $k,( $hp - $yc ) * $k ) );
		
		// put the first point
		$this->_out( sprintf( '%.2f %.2f l', ( $xc + $r * cos( $a ) ) * $k, ( ( $hp - ( $yc - $r * sin( $a ) ) ) * $k ) ) );
		
		// draw the arc
		if ( $d < M_PI/2 )
		{
			$this->_arc( $xc + $r * cos( $a ) + $MyArc * cos( M_PI / 2 + $a ),
						 $yc - $r * sin( $a ) - $MyArc * sin( M_PI / 2 + $a ),
						 $xc + $r * cos( $b ) + $MyArc * cos( $b - M_PI / 2 ),
						 $yc - $r * sin( $b ) - $MyArc * sin( $b - M_PI / 2 ),
						 $xc + $r * cos( $b ),
						 $yc - $r * sin( $b ) );
		}
		else
		{
			$b = $a + $d / 4;
			$MyArc = 4 / 3 * ( 1 - cos( $d / 8 ) ) / sin( $d / 8 ) * $r;
			
			$this->_arc( $xc + $r * cos( $a ) + $MyArc * cos( M_PI / 2 + $a ),
						 $yc - $r * sin( $a ) - $MyArc * sin( M_PI / 2 + $a ),
						 $xc + $r * cos( $b ) + $MyArc * cos( $b - M_PI / 2 ),
						 $yc - $r * sin( $b ) - $MyArc * sin( $b - M_PI / 2 ),
						 $xc + $r * cos( $b ),
						 $yc - $r * sin( $b ) );

			$a = $b;
			$b = $a + $d / 4;
			
			$this->_arc( $xc + $r * cos( $a ) + $MyArc * cos( M_PI / 2 + $a ),
						 $yc - $r * sin( $a ) - $MyArc * sin( M_PI / 2 + $a ),
						 $xc + $r * cos( $b ) + $MyArc * cos( $b - M_PI / 2 ),
						 $yc - $r * sin( $b ) - $MyArc * sin( $b - M_PI / 2 ),
						 $xc + $r * cos( $b ),
						 $yc - $r * sin( $b ) );
						 
			$a = $b;
			$b = $a + $d / 4;
			
			$this->_arc( $xc + $r * cos( $a ) + $MyArc * cos( M_PI / 2 + $a ),
						 $yc - $r * sin( $a ) - $MyArc * sin( M_PI / 2 + $a ),
						 $xc + $r * cos( $b ) + $MyArc * cos( $b - M_PI / 2 ),
						 $yc - $r * sin( $b ) - $MyArc * sin( $b - M_PI / 2 ),
						 $xc + $r * cos( $b ),
						 $yc - $r * sin( $b ) );
						 
			$a = $b;
			$b = $a + $d / 4;
			
			$this->_arc( $xc + $r * cos( $a ) + $MyArc * cos( M_PI / 2 + $a ),
						 $yc - $r * sin( $a ) - $MyArc * sin( M_PI / 2 + $a ),
						 $xc + $r * cos( $b ) + $MyArc * cos( $b - M_PI / 2 ),
						 $yc - $r * sin( $b ) - $MyArc * sin( $b - M_PI / 2 ),
						 $xc + $r * cos( $b ),
						 $yc - $r * sin( $b ) );
		}
		
		// terminate drawing
		$this->_out( $op );
	}	
	
	/**
	 * @access  public
	 */
	function pieChart( $w, $h, $data, $format, $colors = null )
	{
		$this->setFont( 'Courier', '', 10 );
		$this->setLegends( $data, $format );

		$XPage   = $this->getX();
		$YPage   = $this->getY();
		$margin  = 2;
		$hLegend = 5;
		$radius  = min( $w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2 );
		$radius  = floor( $radius / 2 );
		$XDiag   = $XPage + $margin + $radius;
		$YDiag   = $YPage + $margin + $radius;

		if ( $colors == null ) 
		{
			for ( $i = 0; $i < $this->nbval; $i++ ) 
			{
				$gray = $i * intval( 255 / $this->nbval );
				$colors[$i] = array( $gray, $gray, $gray );
			}
		}

		// Sectors
		$this->setLineWidth( 0.2 );
		$angleStart = 0;
		$angleEnd   = 0;
		$i = 0;

		foreach ( $data as $val ) 
		{
			$angle = floor( ( $val * 360 ) / doubleval( $this->sum ) );
			
			if ( $angle != 0 ) 
			{
				$angleEnd = $angleStart + $angle;
				$this->setFillColor( $colors[$i][0], $colors[$i][1], $colors[$i][2] );
				$this->sector( $XDiag, $YDiag, $radius, $angleStart, $angleEnd );
				$angleStart += $angle;
			}
			
			$i++;
		}
		
		if ( $angleEnd != 360 )
			$this->sector( $XDiag, $YDiag, $radius, $angleStart - $angle, 360 );

		// Legends
		$this->setFont( 'Courier', '', 10 );
		
		$x1 = $XPage + 2 * $radius + 4 * $margin;
		$x2 = $x1 + $hLegend + $margin;
		$y1 = $YDiag - $radius + ( 2 * $radius - $this->nbval * ( $hLegend + $margin ) ) / 2;
		
		for ( $i = 0; $i < $this->nbval; $i++ ) 
		{
			$this->setFillColor( $colors[$i][0], $colors[$i][1], $colors[$i][2] );
			$this->rect( $x1, $y1, $hLegend, $hLegend, 'DF' );
			$this->setXY( $x2, $y1 );
			$this->cell( 0, $hLegend, $this->legends[$i] );
			
			$y1 += $hLegend + $margin;
		}
	}

	/**
	 * @access  public
	 */
	function barDiagram( $w, $h, $data, $format, $color = null, $maxVal = 0, $nbDiv = 4 )
	{
		$this->setFont( 'Courier', '', 10 );
		$this->setLegends( $data, $format );

		$XPage  = $this->getX();
		$YPage  = $this->getY();
		$margin = 2;
		$YDiag  = $YPage + $margin;
		$hDiag  = floor( $h - $margin * 2 );
		$XDiag  = $XPage + $margin * 2 + $this->wLegend;
		$lDiag  = floor( $w - $margin * 3 - $this->wLegend );
		
		if ( $color == null )
			$color = array( 155, 155, 155 );
			
		if ( $maxVal == 0 )
			$maxVal = max( $data );
		
		$valIndRepere = ceil( $maxVal / $nbDiv );
		$maxVal       = $valIndRepere * $nbDiv;
		$lRepere      = floor( $lDiag / $nbDiv );
		$lDiag        = $lRepere * $nbDiv;
		$unit         = $lDiag / $maxVal;
		$hBar         = floor( $hDiag / ( $this->nbval + 1 ) );
		$hDiag        = $hBar * ( $this->nbval + 1 );
		$eBaton       = floor( $hBar * 80 / 100 );

		$this->setLineWidth( 0.2 );
		$this->rect( $XDiag, $YDiag, $lDiag, $hDiag );

		$this->setFont( 'Courier', '', 10 );
		$this->setFillColor( $color[0], $color[1], $color[2] );
		
		$i = 0;
		foreach ( $data as $val ) 
		{
			// Bar
			$xval = $XDiag;
			$lval = (int)( $val * $unit );
			$yval = $YDiag + ( $i + 1 ) * $hBar - $eBaton / 2;
			$hval = $eBaton;

			$this->rect( $xval, $yval, $lval, $hval, 'DF' );

			// Legend
			$this->setXY( 0, $yval );
			$this->cell( $xval - $margin, $hval, $this->legends[$i], 0, 0, 'R' );
			$i++;
		}

		// Scales
		for ( $i = 0; $i <= $nbDiv; $i++ ) 
		{
			$xpos = $XDiag + $lRepere * $i;
			$this->line( $xpos, $YDiag, $xpos, $YDiag + $hDiag );
			$val  = $i * $valIndRepere;
			$xpos = $XDiag + $lRepere * $i - $this->getStringWidth( $val ) / 2;
			$ypos = $YDiag + $hDiag - $margin;

			$this->text( $xpos, $ypos, $val );
		}
	}

	/**
	 * @access  public
	 */
	function setLegends( $data, $format )
	{
		$this->legends = array();
		$this->wLegend = 0;
		$this->sum     = array_sum( $data );
		$this->nbval   = count( $data );
		
		foreach ( $data as $l => $val )
		{
			$p = sprintf( '%.2f', $val / $this->sum * 100 ) . '%';
			$legend = str_replace( array( '%l', '%v', '%p' ), array( $l, $val, $p ), $format );
			$this->legends[] = $legend;
			$this->wLegend   = max( $this->getStringWidth( $legend ), $this->wLegend );
		}
	}
	
	/**
	 * @access  public
	 */
	function parseHTML( $html )
	{
		$html = $this->_prepareHTML( $html );
		$html = $this->_explodeHTML( $html );
		
		for ( $i = 0; $i < count( $html ); $i++ )
		{
			if ( ereg( "<[^>]*>", $html[$i] ) )
			{
				if ( eregi( "<table[^>]*>", $html[$i] ) )
				{
					$t = '';
					
					while ( !eregi( "</table[^>]*>", $html[$i] ) )
					{
						$t .= $html[$i];
						$i++;
					}
					
					$this->_parseHTMLTable( $t );
				}
				else if ( eregi( '<a[^>]* href="[^"]+"[^>]*>', $html[$i] ) )
				{
					$this->_writeHTMLLink( $html[$i], $html[$i + 1] );
					$i += 2;
				}
				else if ( eregi( '<span[^>]* class="[^"]+"[^>]*>', $html[$i] ) )
				{
					$this->_writeHTMLSpan( $html[$i], $html[$i + 1] );
					$i += 2;
				}
				else if ( eregi( '<img src="[^"]+"[^>]*>', $html[$i] ) )
				{
					$res = $this->_addHTMLImage( $html[$i] );
					
					if ( PEAR::isError( $res ) )
						return $res;
				}
				else
				{					
					$tags    = array();
					$type    = array();
					$classes = array();
					
					eregi( '<(/?[a-zA-Z]*)[^>]*>',        $html[$i], $tags    );
					eregi( '<[^>]*type="([^"]*)"[^>]*>',  $html[$i], $type    );
					eregi( '<[^>]*class="([^"]*)"[^>]*>', $html[$i], $classes );
					
					$this->_changeTags( $tags[1], $classes[1], $type[1] );
				}
			}
			else
			{
				$this->write( $this->fontSizePt + 2, $html[$i] );
			}
		}
	}

	/**
	 * @access  public
	 */	
	function bookmark( $txt, $level = 0, $y = 0 )
	{
		if ( $y == -1 )
			$y = $this->getY();
	
		$this->outlines[] = array(
			't' => $txt,
			'l' => $level,
			'y' => $y,
			'p' => $this->pageNum()
		);
	}
	
	/**
	 * @access  public
	 */	
	function ean13( $x, $y, $barcode, $h = 16, $w = .35 )
	{
		return $this->_barcode( $x, $y, $barcode, $h, $w, 13 );
	}

	/**
	 * @access  public
	 */	
	function upc_a( $x, $y, $barcode, $h = 16, $w = .35 )
	{
		return $this->_barcode( $x, $y, $barcode, $h, $w, 12 );
	}

	/**
	 * Line feed; default value is last cell height.
	 *
	 * @access  public
	 */
	function ln( $h = '' )
	{
		$this->x = $this->lMargin;
		
		if ( is_string( $h ) )
			$this->y += $this->lasth;
		else
			$this->y += $h;
	}
	
	/**
	 * @access  public
	 */
	function getX()
	{
		return $this->x;
	}

	/**
	 * @access  public
	 */	
	function setX( $x )
	{
		if ( $x >= 0 )
			$this->x = $x;
		else
			$this->x = $this->w + $x;
	}
	
	/**
	 * @access  public
	 */
	function getY()
	{
		return $this->y;
	}

	/**
	 * @access  public
	 */	
	function setY( $y )
	{
		$this->x = $this->lMargin;
		
		if ( $y >= 0 )
			$this->y = $y;
		else
			$this->y = $this->h + $y;
	}
	
	/**
	 * @access  public
	 */
	function setXY( $x, $y )
	{
		$this->setY( $y );
		$this->setX( $x );
	}
	
	/**
	 * Output PDF to file or browser.
	 *
	 * @access  public
	 */
	function output( $file = '', $download = false )
	{
		if ( $this->state < 3 )
			$this->close();
			
		if ( $file == '' )
		{
			// Send to browser
			header( 'Content-Type: application/pdf' );
			
			if ( headers_sent() )
				return PEAR::raiseError( 'Some data has already been output to browser, cannnot send PDF file.' );
				
			header( 'Content-Length: ' . strlen( $this->buffer ) );
			header( 'Content-disposition: inline; filename=doc.pdf' );

			echo $this->buffer;
		}
		else
		{
			if ( $download )
			{
				// Download file
				if ( isset( $_ENV['HTTP_USER_AGENT'] ) && strpos( $_ENV['HTTP_USER_AGENT'], 'MSIE 5.5' ) )
					header( 'Content-Type: application/dummy' );
				else
					header( 'Content-Type: application/octet-stream' );
					
				if ( headers_sent() )
					return PEAR::raiseError( 'Some data has already been output to browser, cannot send PDF file.' );
					
				header( 'Content-Length: ' . strlen( $this->buffer ) );
				header( 'Content-disposition: attachment; filename=' . $file );
				
				echo $this->buffer;
			}
			else
			{
				// Save file locally
				$f = fopen( $file, 'wb' );
				
				if ( !$f )
					return PEAR::raiseError( 'Unable to create output file: ' . $file );
					
				fwrite( $f, $this->buffer, strlen( $this->buffer ) );
				fclose( $f );
			}
		}
	}
	
	
	// private methods
		
	/**
	 * @access  private
	 */
	function _begindoc()
	{
		$this->state = 1;
		$this->_out( '%PDF-1.3' );
	}
	
	/**
	 * @access  private
	 */
	function _putpages()
	{
		$nb = $this->page;
		
		if ( !empty( $this->aliasNumPages ) )
		{
			// Replace number of pages
			for ( $n = 1; $n <= $nb; $n++ )
				$this->pages[$n] = str_replace( $this->aliasNumPages, $nb, $this->pages[$n] );
		}
		
		if ( $this->defOrientation == 'P' )
		{
			$wPt = $this->fwPt;
			$hPt = $this->fhPt;
		}
		else
		{
			$wPt = $this->fhPt;
			$hPt = $this->fwPt;
		}
		
		$filter = ( $this->compress )? '/Filter /FlateDecode ' : '';

		for ( $n = 1; $n <= $nb; $n++ )
		{
			// Page
			$this->_newobj();
			$this->_out( '<</Type /Page' );
			$this->_out( '/Parent 1 0 R' );
			
			if ( isset( $this->orientationChanges[$n] ) )
				$this->_out( sprintf( '/MediaBox [0 0 %.2f %.2f]', $hPt, $wPt ) );
				
			$this->_out( '/Resources 2 0 R' );
			
			if ( isset( $this->pageLinks[$n] ) )
			{
				// Links
				$annots = '/Annots [';
				
				foreach ( $this->pageLinks[$n] as $pl )
				{
					$rect    = sprintf( '%.2f %.2f %.2f %.2f', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3] );
					$annots .= '<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
					
					if ( is_string( $pl[4] ) )
					{
						$annots .= '/A <</S /URI /URI ' . $this->_textstring( $pl[4] ) . '>>>>';
					}
					else
					{
						$l = $this->links[$pl[4]];
						$h = isset( $this->orientationChanges[$l[0]] )? $wPt : $hPt;
						
						$annots .= sprintf( '/Dest [%d 0 R /XYZ 0 %.2f null]>>', 1 + 2 * $l[0], $h - $l[1] * $this->k );
					}
				}
				
				$this->_out( $annots . ']' );
			}
			
			$this->_out( '/Contents ' . ( $this->n + 1 ) . ' 0 R>>' );
			$this->_out( 'endobj' );
			
			// Page content
			$p = ( $this->compress )? gzcompress( $this->pages[$n] ) : $this->pages[$n];
			
			$this->_newobj();
			$this->_out( '<<' . $filter . '/Length ' . strlen( $p ) . '>>' );
			$this->_putstream( $p );
			$this->_out( 'endobj' );
		}
		
		// Pages root
		$this->offsets[1] = strlen( $this->buffer );
		$this->_out( '1 0 obj' );
		$this->_out( '<</Type /Pages' );
		
		$kids = '/Kids [';
		
		for ( $i = 0; $i < $nb; $i++ )
			$kids .= ( 3 + 2 * $i ) . ' 0 R ';

		$this->_out( $kids.']' );
		$this->_out( '/Count ' . $nb );
		$this->_out( sprintf( '/MediaBox [0 0 %.2f %.2f]', $wPt, $hPt ) );
		$this->_out( '>>' );
		$this->_out( 'endobj' );
	}
	
	/**
	 * @access  private
	 */
	function _putfonts()
	{
		$nf = $this->n;
		
		foreach ( $this->diffs as $diff )
		{
			// Encodings
			$this->_newobj();
			$this->_out( '<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $diff . ']>>' );
			$this->_out( 'endobj' );
		}
		
		$mqr = get_magic_quotes_runtime();
		set_magic_quotes_runtime( 0 );
		
		foreach ( $this->fontFiles as $file => $info )
		{
			// Font file embedding
			$this->_newobj();
			$this->fontFiles[$file]['n'] = $this->n;

			if ( defined( 'PDF_FONTPATH' ) )
				$file = PDF_FONTPATH . $file;
				
			$size = filesize( $file );
			
			if ( !$size )
				return PEAR::raiseError( 'Font file not found.' );
			
			$this->_out( '<</Length ' . $size );
			
			if ( substr( $file, -2 ) == '.z' )
				$this->_out( '/Filter /FlateDecode' );
				
			$this->_out( '/Length1 ' . $info['length1'] );
			
			if ( isset( $info['length2'] ) )
				$this->_out( '/Length2 ' . $info['length2'] . ' /Length3 0' );
				
			$this->_out( '>>' );
			$f = fopen( $file, 'rb' );
			$this->_putstream( fread( $f, $size ) );
			fclose( $f );
			$this->_out( 'endobj' );
		}
		
		set_magic_quotes_runtime( $mqr );
		
		foreach ( $this->fonts as $k => $font )
		{
			// Font objects
			$this->_newobj();
			$this->fonts[$k]['n'] = $this->n;
			$name = $font['name'];
			$this->_out( '<</Type /Font' );
			$this->_out( '/BaseFont /' . $name );
			
			if ( $font['type'] == 'core' )
			{
				// Standard font
				$this->_out( '/Subtype /Type1' );
				
				if ( $name != 'Symbol' && $name != 'ZapfDingbats' )
					$this->_out( '/Encoding /WinAnsiEncoding' );
			}
			else
			{
				// Additional font
				$this->_out( '/Subtype /' . $font['type'] );
				$this->_out( '/FirstChar 32' );
				$this->_out( '/LastChar 255' );
				$this->_out( '/Widths ' . ( $this->n + 1 ) . ' 0 R' );
				$this->_out( '/FontDescriptor ' . ( $this->n + 2 ) . ' 0 R' );

				if ( $font['enc'] )
				{
					if ( isset( $font['diff'] ) )
						$this->_out( '/Encoding ' . ( $nf + $font['diff'] ) . ' 0 R' );
					else
						$this->_out( '/Encoding /WinAnsiEncoding' );
				}
			}
			
			$this->_out( '>>' );
			$this->_out( 'endobj' );
			
			if ( $font['type']!='core' )
			{
				// Widths
				$this->_newobj();
				
				$cw = &$font['cw'];
				$s  =  '[';
				
				for ( $i = 32; $i <= 255; $i++ )
					$s .= $cw[chr( $i )] . ' ';

				$this->_out( $s . ']' );
				$this->_out( 'endobj' );
				
				// Descriptor
				$this->_newobj();
				$s = '<</Type /FontDescriptor /FontName /' . $name;
				
				foreach ( $font['desc'] as $k => $v )
					$s .= ' /' . $k . ' ' . $v;
					
				$file = $font['file'];
				
				if ( $file )
					$s .= ' /FontFile' . ( $font['type'] == 'Type1'? '' : '2' ) . ' ' . $this->fontFiles[$file]['n'] . ' 0 R';

				$this->_out( $s . '>>' );
				$this->_out( 'endobj'  );
			}
		}
	}
	
	/**
	 * @access  private
	 */
	function _putjavascript() 
	{
		$this->_newobj();
		$this->n_js = $this->n;
		
		$this->_out( '<<' );
		$this->_out( '/Names [(EmbeddedJS) ' . ( $this->n + 1 ) . ' 0 R ]' );
		$this->_out( '>>' );
		$this->_out( 'endobj' );
		
		$this->_newobj();
		
		$this->_out( '<<' );
		$this->_out( '/S /JavaScript' );
		$this->_out( '/JS ' . $this->_textstring( $this->javascript ) );
		$this->_out( '>>' );
		$this->_out( 'endobj' );
	}
	
	/**
	 * @access  private
	 */
	function _putimages()
	{
		$filter = $this->compress? '/Filter /FlateDecode ' : '';

		foreach ( $this->images as $file => $info )
		{
			$this->_newobj();
			$this->images[$file]['n'] = $this->n;
			
			$this->_out( '<</Type /XObject' );
			$this->_out( '/Subtype /Image'  );
			$this->_out( '/Width '  . $info['w'] );
			$this->_out( '/Height ' . $info['h'] );
			
			if ( $info['cs'] == 'Indexed' )
			{
				$this->_out( '/ColorSpace [/Indexed /DeviceRGB ' . ( strlen( $info['pal'] ) / 3 - 1 ) . ' ' . ( $this->n + 1 ) . ' 0 R]' );
			}
			else
			{
				$this->_out( '/ColorSpace /' . $info['cs'] );
				
				if ( $info['cs'] == 'DeviceCMYK' )
					$this->_out( '/Decode [1 0 1 0 1 0 1 0]' );
			}
			
			$this->_out( '/BitsPerComponent ' . $info['bpc'] );
			$this->_out( '/Filter /' . $info['f'] );
			
			if ( isset( $info['parms'] ) )
				$this->_out( $info['parms'] );
				
			if ( isset( $info['trns'] ) && is_array( $info['trns'] ) )
			{
				$trns = '';
				
				for ( $i = 0; $i < count( $info['trns'] ); $i++ )
					$trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
				
				$this->_out( '/Mask [' . $trns . ']' );
			}
			
			$this->_out( '/Length ' . strlen( $info['data'] ) . '>>' );
			$this->_putstream( $info['data'] );
			$this->_out( 'endobj' );
			
			// Palette
			if ( $info['cs'] == 'Indexed' )
			{
				$this->_newobj();
				$pal = $this->compress? gzcompress( $info['pal'] ) : $info['pal'];
				$this->_out( '<<' . $filter . '/Length ' . strlen( $pal ) . '>>' );
				$this->_putstream( $pal );
				$this->_out( 'endobj' );
			}
		}
	}
	
	/**
	 * @access  private
	 */
	function _putresources()
	{
		$res = $this->_putfonts();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		$this->_putimages();
		
		// Resource dictionary
		$this->offsets[2] = strlen( $this->buffer );
		$this->_out( '2 0 obj' );
		$this->_out( '<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]' );
		$this->_out( '/Font <<' );
		
		foreach ( $this->fonts as $font )
			$this->_out( '/F' . $font['i'] . ' ' . $font['n'] . ' 0 R' );
			
		$this->_out( '>>' );
		
		if ( count( $this->images ) )
		{
			$this->_out( '/XObject <<' );
			
			foreach ( $this->images as $image )
				$this->_out( '/I' . $image['i'] . ' ' . $image['n'] . ' 0 R' );
				
			$this->_out( '>>' );
		}
		
		$this->_out( '>>' );
		$this->_out( 'endobj' );
		
		if ( !empty( $this->javascript ) )
			$this->_putjavascript();
			
		$this->_putbookmarks();
		
		if ( $this->encrypted ) 
		{
			$this->_newobj();
			$this->enc_obj_id = $this->n;
			$this->_out( '<<' );
			$this->_putencryption();
			$this->_out( '>>' );
		}
	}

	/**
	 * @access  private
	 */	
	function _putencryption()
	{
		$this->_out( '/Filter /Standard' );
		$this->_out( '/V 1' );
		$this->_out( '/R 2' );
		$this->_out( '/O (' . $this->_escape( $this->ovalue ) . ')' );
		$this->_out( '/U (' . $this->_escape( $this->uvalue ) . ')' );
		$this->_out( '/P '  . $this->pvalue );
	}
	
	/**
	 * @access  private
	 */	
	function _putinfo()
	{
		$this->_out( '/Producer ' . $this->_textstring( 'Abstractpage PDFProducer ' . PDF_VERSION ) );

		if ( !empty( $this->title ) )
			$this->_out( '/Title ' . $this->_textstring( $this->title ) );
			
		if ( !empty( $this->subject ) )
			$this->_out( '/Subject ' . $this->_textstring( $this->subject ) );
			
		if ( !empty( $this->author ) )
			$this->_out( '/Author ' . $this->_textstring( $this->author ) );
			
		if ( !empty( $this->keywords ) )
			$this->_out( '/Keywords ' . $this->_textstring( $this->keywords ) );
			
		if ( !empty( $this->creator ) )
			$this->_out( '/Creator ' . $this->_textstring( $this->creator ) );
			
		$this->_out( '/CreationDate ' . $this->_textstring( 'D:' . date( 'YmdHis' ) ) );
	}
	
	/**
	 * @access  private
	 */	
	function _putbookmarks()
	{
		$nb = count( $this->outlines );
	
		if ( $nb == 0 )
			return;
	
		$lru = array();
		$level = 0;
	
		foreach ( $this->outlines as $i => $o )
		{
			if ( $o['l'] > 0 )
			{
				$parent = $lru[$o['l'] - 1];
			
				// Set parent and last pointers
				$this->outlines[$i]['parent'] = $parent;
				$this->outlines[$parent]['last'] = $i;
			
				if ( $o['l'] > $level )
				{
					// Level increasing: set first pointer
					$this->outlines[$parent]['first'] = $i;
				}
			}
			else
			{
				$this->outlines[$i]['parent'] = $nb;
			}
			
			if ( $o['l'] <= $level && $i > 0 )
			{
				// Set prev and next pointers
				$prev = $lru[$o['l']];
				$this->outlines[$prev]['next'] = $i;
				$this->outlines[$i]['prev'] = $prev;
			}
	
			$lru[$o['l']] = $i;
			$level=$o['l'];
		}

		// Outline items
		$n = $this->n + 1;

		foreach ( $this->outlines as $i => $o )
		{
			$this->_newobj();
			$this->_out( '<</Title ' . $this->_textstring( $o['t'] ) );
			$this->_out( '/Parent '  . ( $n + $o['parent'] ) . ' 0 R' );
	
			if ( isset( $o['prev'] ) )
				$this->_out( '/Prev ' . ( $n + $o['prev'] ) . ' 0 R' );
	
			if ( isset( $o['next'] ) )
				$this->_out( '/Next ' . ( $n + $o['next'] ) . ' 0 R' );
	
			if ( isset( $o['first'] ) )
				$this->_out( '/First ' . ( $n + $o['first'] ) . ' 0 R' );
	
			if ( isset( $o['last'] ) )
				$this->_out( '/Last ' . ( $n + $o['last'] ) . ' 0 R' );
	
			$this->_out( sprintf( '/Dest [%d 0 R /XYZ 0 %.2f null]', 1 + 2 * $o['p'], ( $this->h - $o['y'] ) * $this->k ) );
			$this->_out( '/Count 0>>' );
			$this->_out( 'endobj' );
		}

		// Outline root
		$this->_newobj();
		$this->outlineRoot = $this->n;
		$this->_out( '<</Type /Outlines /First ' . $n . ' 0 R' );
		$this->_out( '/Last ' . ( $n + $lru[0] ) . ' 0 R>>' );
		$this->_out( 'endobj' );
	}

	/**
	 * @access  private
	 */
	function _putcatalog()
	{
		$this->_out( '/Type /Catalog' );
		$this->_out( '/Pages 1 0 R' );
		
		if ( $this->zoomMode == 'fullpage' )
			$this->_out( '/OpenAction [3 0 R /Fit]' );
		else if ( $this->zoomMode == 'fullwidth' )
			$this->_out( '/OpenAction [3 0 R /FitH null]' );
		else if ( $this->zoomMode == 'real' )
			$this->_out( '/OpenAction [3 0 R /XYZ null null 1]' );
		else if ( !is_string( $this->zoomMode ) )
			$this->_out( '/OpenAction [3 0 R /XYZ null null ' . ( $this->zoomMode / 100 ) . ']' );
			
		if ( $this->layoutMode == 'single' )
			$this->_out( '/PageLayout /SinglePage' );
		else if ( $this->layoutMode == 'continuous' )
			$this->_out( '/PageLayout /OneColumn' );
		else if ( $this->layoutMode == 'two' )
			$this->_out( '/PageLayout /TwoColumnLeft' );
			
		if ( isset( $this->javascript ) )
			$this->_out( '/Names <</JavaScript ' . ( $this->n_js ) . ' 0 R>>' );
			
		if ( count( $this->outlines ) > 0 )
		{
			$this->_out( '/Outlines ' . $this->outlineRoot . ' 0 R' );
			$this->_out( '/PageMode /UseOutlines' );
		}
	}

	/**
	 * @access  private
	 */	
	function _puttrailer()
	{
		$this->_out( '/Size ' . ( $this->n + 1 ) );
		$this->_out( '/Root ' . $this->n . ' 0 R' );
		$this->_out( '/Info ' . ( $this->n - 1 ) . ' 0 R' );
		
		if ( $this->encrypted )
			$this->_out( '/Encrypt ' . $this->enc_obj_id . ' 0 R' );
	}
	
	/**
	 * @access  private
	 */
	function _enddoc()
	{
		$this->_putpages();
		
		$res = $this->_putresources();
		
		if ( PEAR::isError( $res ) )
			return $res;
			
		// Info
		$this->_newobj();
		$this->_out( '<<' );
		$this->_putinfo();
		$this->_out( '>>' );
		$this->_out( 'endobj' );
		
		// Catalog
		$this->_newobj();
		$this->_out( '<<' );
		$this->_putcatalog();
		$this->_out( '>>' );
		$this->_out( 'endobj' );
		
		// Cross-ref
		$o = strlen( $this->buffer );
		$this->_out( 'xref' );
		$this->_out( '0 ' . ( $this->n + 1 ) );
		$this->_out( '0000000000 65535 f ' );
		
		for ( $i = 1; $i <= $this->n; $i++ )
			$this->_out( sprintf( '%010d 00000 n ', $this->offsets[$i] ) );
			
		// Trailer
		$this->_out( 'trailer' );
		$this->_out( '<<' );
		$this->_puttrailer();
		$this->_out( '>>' );
		$this->_out( 'startxref' );
		$this->_out( $o );
		$this->_out( '%%EOF' );
		
		$this->state = 3;
		
		return true;
	}
	
	/**
	 * @access  private
	 */
	function _beginpage( $orientation )
	{
		$this->page++;
		$this->pages[$this->page] ='';
		$this->state = 2; 
		$this->x = $this->lMargin;
		$this->y = $this->tMargin;
		$this->lasth = 0;
		$this->fontFamily = '';
		
		// Page orientation
		if ( !$orientation )
		{
			$orientation = $this->defOrientation;
		}
		else
		{
			$orientation = strtoupper( $orientation{0} );
			
			if ( $orientation != $this->defOrientation )
				$this->orientationChanges[$this->page] = true;
		}
		
		if ( $orientation != $this->curOrientation )
		{
			// Change orientation
			if ( $orientation == 'P' )
			{
				$this->wPt = $this->fwPt;
				$this->hPt = $this->fhPt;
				$this->w   = $this->fw;
				$this->h   = $this->fh;
			}
			else
			{
				$this->wPt = $this->fhPt;
				$this->hPt = $this->fwPt;
				$this->w   = $this->fh;
				$this->h   = $this->fw;
			}
			
			$this->pageBreakTrigger = $this->h - $this->bMargin;
			$this->curOrientation   = $orientation;
		}
	}

	/**
	 * @access  private
	 */	
	function _endpage()
	{
		if ( $this->angle != 0 )
		{
			$this->angle = 0;
			$this->_out( 'Q' );
		}
		
		$this->state = 1;
	}
	
	/**
	 * Begin a new object.
	 *
	 * @access  private
	 */
	function _newobj()
	{
		$this->n++;
		$this->offsets[$this->n] = strlen( $this->buffer );
		$this->_out( $this->n . ' 0 obj' );
	}
	
	/**
	 * @access  private
	 */
	function _dounderline( $x, $y, $txt )
	{
		$up = $this->currentFont['up'];
		$ut = $this->currentFont['ut'];
		$w  = $this->getStringWidth( $txt ) + $this->ws * substr_count( $txt, ' ' );
		
		return sprintf( '%.2f %.2f %.2f %.2f re f', $x * $this->k, ( $this->h - ( $y - $up / 1000 * $this->fontSize ) ) * $this->k, $w * $this->k, -$ut / 1000 * $this->fontSizePt );
	}
	
	/**
	 * Extract info from a JPEG file.
	 *
	 * @access  private
	 */
	function _parsejpg( $file )
	{
		$a = getimagesize( $file );
		
		if ( !$a )
			return PEAR::raiseError( 'Missing or incorrect image file: ' . $file );
			
		if ( $a[2] != 2 )
			return PEAR::raiseError( 'Not a JPEG file: ' . $file );
			
		if ( !isset( $a['channels'] ) || $a['channels'] == 3 )
			$colspace = 'DeviceRGB';
		else if ( $a['channels'] == 4 )
			$colspace = 'DeviceCMYK';
		else
			$colspace = 'DeviceGray';

		$bpc = isset( $a['bits'] )? $a['bits'] : 8;

		// Read whole file
		$f = fopen( $file, 'rb' );
		$data = fread( $f, filesize( $file ) );
		fclose( $f );
		
		return array( 
			'w'    => $a[0], 
			'h'    => $a[1], 
			'cs'   => $colspace, 
			'bpc'  => $bpc, 
			'f'    => 'DCTDecode', 
			'data' => $data
		);
	}
	
	/**
	 * Extract info from a PNG file.
	 *
	 * @access  private
	 */
	function _parsepng( $file )
	{
		$f = fopen( $file, 'rb' );
		
		if ( !$f )
			return PEAR::raiseError( 'Cannot open image file: ' . $file );
			
		// Check signature
		if ( fread( $f, 8 ) != chr( 137 ) . 'PNG' . chr( 13 ) . chr( 10 ) . chr( 26 ) . chr( 10 ) )
			return PEAR::raiseError( 'Not a PNG file: ' . $file );
			
		// Read header chunk
		fread( $f, 4 );
		
		if ( fread( $f, 4 ) != 'IHDR' )
			return PEAR::raiseError( 'Incorrect PNG file: ' . $file );
			
		$w   = $this->_freadint( $f );
		$h   = $this->_freadint( $f );
		$bpc = ord( fread( $f, 1 ) );

		if ( $bpc > 8 )
			return PEAR::raiseError( '16-bit depth not supported: ' . $file );
			
		$ct = ord( fread( $f, 1 ) );
		
		if ( $ct == 0 )
			$colspace = 'DeviceGray';
		else if ( $ct == 2 )
			$colspace = 'DeviceRGB';
		else if ( $ct == 3 )
			$colspace = 'Indexed';
		else
			return PEAR::raiseError( 'Alpha channel not supported: ' . $file );
			
		if ( ord( fread( $f, 1 ) ) != 0 )
			return PEAR::raiseError( 'Unknown compression method: ' . $file );
			
		if ( ord( fread( $f, 1 ) ) != 0 )
			return PEAR::raiseError( 'Unknown filter method: ' . $file );
			
		if ( ord( fread( $f, 1 ) ) != 0 )
			return PEAR::raiseError( 'Interlacing not supported: ' . $file );
			
		fread( $f, 4 );
		$parms = '/DecodeParms <</Predictor 15 /Colors ' . ( $ct == 2? 3 : 1 ) . ' /BitsPerComponent ' . $bpc . ' /Columns ' . $w . '>>';
		
		// Scan chunks looking for palette, transparency and image data
		$pal  = '';
		$trns = '';
		$data = '';
		
		do
		{
			$n = $this->_freadint( $f );
			$type = fread( $f, 4 );
			
			if ( $type == 'PLTE' )
			{
				// Read palette
				$pal = fread( $f, $n );
				fread( $f, 4 );
			}
			else if ( $type == 'tRNS' )
			{
				// Read transparency info
				$t = fread( $f, $n );
				
				if ( $ct == 0 )
				{
					$trns = array( ord( substr( $t, 1, 1 ) ) );
				}
				else if ( $ct == 2 )
				{
					$trns = array( ord( substr( $t, 1, 1 ) ), ord( substr( $t, 3, 1 ) ), ord( substr( $t, 5, 1 ) ) );
				}
				else
				{
					$pos = strpos( $t, chr( 0 ) );
					
					if ( is_int( $pos ) )
						$trns = array( $pos );
				}
				
				fread( $f, 4 );
			}
			else if ( $type == 'IDAT' )
			{
				// Read image data block
				$data .= fread( $f, $n );
				fread( $f, 4 );
			}
			else if ( $type=='IEND' )
			{
				break;
			}
			else
			{
				fread( $f, $n + 4 );
			}
		} while ( $n );
		
		if ( $colspace == 'Indexed' && empty( $pal ) )
			return PEAR::raiseError( 'Missing palette in ' . $file );
		
		fclose( $f );
		
		return array(	
			'w'     => $w,
			'h'     => $h,
			'cs'    => $colspace,
			'bpc'   => $bpc,
			'f'     => 'FlateDecode',
			'parms' => $parms,
			'pal'   => $pal,
			'trns'  => $trns,
			'data'  => $data
		);
	}
	
	/**
	 * Compute the check digit.
	 *
	 * @access  private
	 */
	function _getCheckDigit( $barcode )
	{
		$sum = 0;
		
		for ( $i = 1; $i <= 11; $i += 2 )
			$sum += 3 * $barcode{$i};
	
		for ( $i = 0; $i <= 10; $i += 2 )
			$sum += $barcode{$i};
	
		$r = $sum % 10;

		if ( $r > 0 )
			$r = 10 - $r;
	
		return $r;
	}

	/**
	 * Test validity of check digit.
	 *
	 * @access  private
	 */
	function _testCheckDigit( $barcode )
	{
		$sum = 0;
	
		for ( $i = 1; $i <= 11; $i += 2 )
			$sum += 3 * $barcode{$i};
	
		for ( $i = 0; $i <= 10; $i += 2 )
			$sum += $barcode{$i};
	
		return ( $sum + $barcode{12} ) % 10 == 0;
	}

	/**
	 * @access  private
	 */
	function _barcode( $x, $y, $barcode, $h, $w, $len )
	{
		// Padding
		$barcode = str_pad( $barcode, $len - 1, '0', STR_PAD_LEFT );
		
		if ( $len == 12 )
			$barcode = '0' . $barcode;
	
		// Add or control the check digit
		if ( strlen( $barcode ) == 12 )
			$barcode .= $this->_getCheckDigit( $barcode );
		else if ( !$this->_testCheckDigit( $barcode ) )
			return PEAR::raiseError( 'Incorrect check digit.' );
	
		// Convert digits to bars
		$codes = array(
			'A' => array(
				'0' => '0001101', 
				'1' => '0011001',
				'2' => '0010011',
				'3' => '0111101',
				'4' => '0100011',
				'5' => '0110001', 
				'6' => '0101111',
				'7' => '0111011',
				'8' => '0110111',
				'9' => '0001011'
			),
			'B' => array(
				'0' => '0100111',
				'1' => '0110011',
				'2' => '0011011',
				'3' => '0100001',
				'4' => '0011101',
				'5' => '0111001', 
				'6' => '0000101',
				'7' => '0010001',
				'8' => '0001001',
				'9' => '0010111'
			),
			'C' => array(
				'0' => '1110010',
				'1' => '1100110',
				'2' => '1101100',
				'3' => '1000010',
				'4' => '1011100',
				'5' => '1001110',
				'6' => '1010000',
				'7' => '1000100',
				'8' => '1001000',
				'9' => '1110100'
			)
		);
	
		$parities = array(
			'0' => array( 'A', 'A', 'A', 'A', 'A', 'A' ),
			'1' => array( 'A', 'A', 'B', 'A', 'B', 'B' ),
			'2' => array( 'A', 'A', 'B', 'B', 'A', 'B' ),
			'3' => array( 'A', 'A', 'B', 'B', 'B', 'A' ),
			'4' => array( 'A', 'B', 'A', 'A', 'B', 'B' ),
			'5' => array( 'A', 'B', 'B', 'A', 'A', 'B' ),
			'6' => array( 'A', 'B', 'B', 'B', 'A', 'A' ),
			'7' => array( 'A', 'B', 'A', 'B', 'A', 'B' ),
			'8' => array( 'A', 'B', 'A', 'B', 'B', 'A' ),
			'9' => array( 'A', 'B', 'B', 'A', 'B', 'A' )
		);
	
		$code = '101';
		$p = $parities[$barcode{0}];
	
		for ( $i = 1; $i <= 6; $i++ )
			$code .= $codes[$p[$i - 1]][$barcode{$i}];
	
		$code .= '01010';
	
		for($i=7;$i<=12;$i++)
			$code .= $codes['C'][$barcode{$i}];
	
		$code .= '101';
	
		// Draw bars
		for ( $i = 0; $i < strlen( $code ); $i++ )
		{
			if ( $code{$i} == '1' )
				$this->rect( $x + $i * $w, $y, $w, $h, 'F' );
		}

		// Print text under barcode
		$this->setFont( 'Arial', '', 12 );
		$this->text( $x, $y + $h + 11 / $this->k, substr( $barcode, -$len ) );
		
		return true;
	}

	/**
	 * @access  private
	 */
	function _convertGreek( $t )
	{
		$grec = array(
			"&alpha;"   => 97, 
			"&Alpha;"   => 65, 
			"&beta;"    => 98, 
			"&Beta;"    => 66, 
			"&gamma;"   => 103, 
			"&Gamma;"   => 71, 
			"&delta;"   => 100, 
			"&Delta;"   => 68, 
			"&epsilon;" => 101, 
			"&Epsilon;" => 69, 
			"&zeta;"    => 122, 
			"&Zeta;"    => 90, 
			"&eta;"     => 104, 
			"&Eta;"     => 72, 
			"&theta;"   => 113, 
			"&Theta;"   => 81, 
			"&iota;"    => 105, 
			"&Iota;"    => 73, 
			"&kappa;"   => 107, 
			"&Kappa;"   => 75, 
			"&lambda;"  => 108, 
			"&Lambda;"  => 76, 
			"&mu;"      => 109, 
			"&Mu;"      => 77, 
			"&nu;"      => 110, 
			"&Nu;"      => 78, 
			"&xi;"      => 120, 
			"&Xi;"      => 88, 
			"&omicron;" => 111, 
			"&Omicron;" => 79, 
			"&pi;"      => 112, 
			"&Pi;"      => 80, 
			"&rho;"     => 114, 
			"&Rho;"     => 82, 
			"&sigma;"   => 115, 
			"&Sigma;"   => 83, 
			"&tau;"     => 116, 
			"&Tau;"     => 84, 
			"&upsilon;" => 117, 
			"&Upsilon;" => 85, 
			"&phi;"     => 106, 
			"&Phi;"     => 74, 
			"&chi;"     => 99, 
			"&Chi;"     => 67, 
			"&psi;"     => 121, 
			"&Psi;"     => 89, 
			"&omega;"   => 119, 
			"&Omega;"   => 87
		);
		
		while ( list( $k, $v ) = each( $grec ) )
			$t = str_replace( $k, "<span class=\"symbol\">" . chr( $v ) . "</span>", $t );
			
		return $t;
	}
		
	/**
	 * @access  private
	 */
	function _prepareHTML( $t )
	{
		// remove comments
		$t = preg_replace( "/<!--(.*)-->/U", "", $t );
		$t = preg_replace( "/{[A-Z]+}(.*){\/[A-Z]+}/U", "", $t );
		$t = $this->_convertGreek( $t );
		
		$trans_tbl = get_html_translation_table( HTML_ENTITIES );
		$trans_tbl = array_merge( array_flip( $trans_tbl ), array( 
			"&#946;"  => "ß", 
			"&rsquo;" => "'", 
			"&oelig;" => "œ", 
			"&ndash;" => "-", 
			"\t"      => '', 
			"\n"      => '', 
			"\r"      => ''
		) );
		
		$t = stripslashes( strtr( $t, $trans_tbl ) );
		return $t;
	}

	/**
	 * @access  private
	 */	
	function _explodeHTML( $t )
	{
		$html = preg_split( '/(<.*>)/U', $t, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		return $html;
	}
	
	/**
	 * @access  private
	 */
	function _changeType( $t )
	{
		switch ( $t )
		{
			case "I":
				$this->rangOl = array( 1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI" );
				break;
			
			case "i":
				$this->rangOl = array( 1 => "i", "ii", "iii", "iv", "v", "vi", "vii", "viii", "ix", "x", "xi", "xii", "xiii", "xiv", "xv", "xvi", "xvii", "xviii", "xix", "xx", "xxi", "xxii", "xxiii", "xxiv", "xxv", "xxvi" );
				break;
			
			case "1":
				$this->rangOl = array( 1 => "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26" );
				break;
			
			case "A":
				$this->rangOl = array( 1 => "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" );
				break;
			
			case "a":
				$this->rangOl = array( 1 => "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z" );
				break;
			
			default:
				$this->rangOl = array( 1 => "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26" );
				break;
		}
	}

	/**
	 * @access  private
	 */	
	function _changeClass( $c )
	{
		switch ( $c )
		{
			case "headline":
				$this->setTextColor( 121, 44, 4 );
				$this->setFont( $this->fontFamily, '', 12 );
				
				break;
				
			case "biography":
				$this->setTextColor( 192, 192, 192 );
				$this->setFont( $this->fontFamily, 'I', 8 );
				
				break;
				
			case "author":
				$this->setFont( $this->fontFamily, '', 8 );
				
				break;
				
			case "note":
				$this->setFont( $this->fontFamily, '', 6 );
				
				break;
				
			case "codeexample":
				$this->setFont( 'Courier', '', 11 );
				
				break;
				
			case "inlineexample":
				$this->setFont( 'Courier', '', $this->fontSizePt );
				
				break;
				
			case "cite":
				$this->setTextColor( 192, 192, 192 );
				$this->setFont( $this->fontFamily, 'I', 11 );
				
				break;
				
			case "cite-author":
				$this->setTextColor( 192, 192, 192 );
				$this->setFont( $this->fontFamily, 'I', 7 );
				
				break;
				
			case "link":
				$this->setTextColor( 121, 44, 4 );
				
				break;
			
			case "text":
				$this->setTextColor( 0, 0, 0 );
				$this->setFont( $this->fontFamily, '', 11 );
				
				break;
			
			case "small":
				$this->setTextColor( 0, 0, 0 );
				$this->setFont( $this->fontFamily, '', 9 );
				
				break;
			
			case "intro":
				$this->setTextColor( 0, 0, 0 );
				$this->setFont( $this->fontFamily, 'I', 13 );
				
				break;
			
			case "title":
				$this->setTextColor( 59, 55, 107 );
				$this->setFont( $this->fontFamily, 'B', 16 );
				
				break;
			
			case "t_art":
				$this->setTextColor( 59, 55, 107 );
				$this->setFont( $this->fontFamily, 'B', 13 );
				
				break;
			
			case "tb":
				$this->setTextColor( 59, 55, 107 );
				$this->setFont( $this->fontFamily, 'B', 11 );
				
				break;
			
			case "tc":
				$this->setTextColor( 59, 55, 107 );
				$this->setFont( $this->fontFamily, 'I', 11 );
				
				break;
			
			case "NoPage":
				$this->setTextColor( 255, 255, 255 );
				$this->setFont( $this->fontFamily, 'B', 18 );
				
				break;
			
			case "mention":
				$this->setTextColor( 255, 255, 255 );
				$this->setFont( 'Times', 'I', 10 );
				
				break;
			
			case "symbol":
				$this->setFont( 'Symbol', $this->fontStyle, $this->fontSize );
				
				break;
			
			case "listing":
				$this->setTextColor( 0, 0, 0 );
				$this->setFont( $this->fontFamily, 'B', 12 );
				
				break;
		}
	}
	
	/**
	 * @access  private
	 */
	function _changeTags( $b, $c, $t )
	{
		$this->lineheight = $this->fontSize + 2;
		$pos = count( $this->tags );
		
		if ( empty( $c ) && !strpos( $b, "/" ) )
			$c = $this->classes[$pos - 1];

		switch ( $b )
		{
			case 'u':
				if ( !strpos( $this->fontStyle, "U" ) )
				{
					$this->tags[$pos] = 'i';
					$style = $this->fontStyle . 'U';
					$this->setFont( $this->fontFamily, $style );
				}
		
				break;
		
			case 'strong':
			
			case 'b':
				if ( !strpos( $this->fontStyle, "B" ) )
				{
					$this->tags[$pos] = 'b';
					$style = $this->fontStyle . 'B';
					$this->setFont( $this->fontFamily, $style );
				}
			
				break;
			
			case 'em':
			
			case 'i':
				if ( !strpos( $this->fontStyle, "I" ) )
				{
					$this->tags[$pos] = 'i';
					$style = $this->fontStyle . 'I';
					$this->setFont( $this->fontFamily, $style );
				}
				
				break;

			case '/strong':
			
			case '/b':
				unset( $this->tags[$pos - 1] );
				$style = str_replace( 'B', '', $this->fontStyle );
				$this->setFont( $this->fontFamily, $style );
				
				break;
				
			case '/em';

			case '/i':
				unset( $this->tags[$pos - 1] );
				$style = str_replace( 'I', '', $this->fontStyle );
				$this->setFont( $this->fontFamily, $style );
				
				break;

			case '/u':
				unset( $this->tags[$pos - 1] );
				$style = str_replace( 'U', '', $this->fontStyle );
				$this->setFont( $this->fontFamily, $style );
				
				break;
			
			case "p":
				$this->tags[$pos] = 'p';
				$this->classes[$pos] = $c;
				$this->ln();
				
				break;
			
			case "/p":
				unset( $this->tags[$pos - 1] );
				unset( $this->classes[$pos - 1] );
				$c = $this->classes[$pos - 2];
				$this->ln();
	
				break;
		
			case "br":
				$this->ln();
				break;
			
			case "ul":
				$this->tags[$pos] = 'ul';
				$this->classes[$pos] = $c;
				$this->ln();
				$this->setLeftMargin( $this->lMargin + 10 );
				$this->setRightMargin( $this->rMargin + 10 );
				
				break;
			
			case "/ul":
				unset( $this->tags[$pos - 1] );
				unset( $this->classes[$pos - 1] );
				$c = $this->classes[$pos - 2];
				$this->setLeftMargin( $this->lMargin - 10 );
				$this->setRightMargin( $this->rMargin - 10 );
				$this->ln();
				
				break;
			
			case "ol":
				$this->tags[$pos] = 'ol';
				$this->classes[$pos] = $c;
				$this->setLeftMargin( $this->lMargin + 10 );
				$this->setRightMargin( $this->rMargin + 10 );
				
				if ( !is_array( $this->tags_ol[count( $this->tags_ol )] ) )
					$this->tags_ol[count( $this->tags_ol )] = array( $t, 1 );
					
				$this->_changeType( $this->tags_ol[count( $this->tags_ol ) - 1][0] );
				break;
			
			case "/ol":
				unset( $this->tags[$pos - 1] );
				unset( $this->classes[$pos - 1] );
				$c = $this->classes[$pos - 2];
				$this->setLeftMargin( $this->lMargin - 10 );
				$this->setRightMargin( $this->rMargin - 10 );
				unset( $this->tags_ol[count( $this->tags_ol ) - 1] );
				$this->_changeType( $this->tags_ol[count( $this->tags_ol ) - 1][0] );
				$this->ln();
				
				break;
			
			case "li":
				$this->tags[$pos] = 'li';
				$this->classes[$pos] = $c;
				$this->lineheight = $this->fontSize + 8;
				$this->ln();
				$oldX = $this->getX();
				$oldY = $this->getY();
				
				if ( $this->tags[$pos - 1] == "ol" )
				{
					$this->setX( $oldX - $this->getStringWidth( "      " ) );
					$this->_changeType( $this->tags_ol[count( $this->tags_ol ) - 1][0] );
					$this->write( $this->lineheight, $this->rangOl[$this->tags_ol[count( $this->tags_ol ) - 1][1]] . ".  " );
					$this->tags_ol[count( $this->tags_ol ) - 1][1]++;
				}
				
				if ( $this->tags[$pos - 1] == "ul" )
				{
					$this->setX( $oldX - $this->getStringWidth( "   " ) );
					$this->write( $this->lineheight, chr( 149 ) . "  " );
				}
				
				$this->setXY( $oldX, $oldY );
				break;
			
			case "/li":
				unset( $this->tags[$pos - 1] );
				unset( $this->classes[$pos - 1] );
				$c = $this->classes[$pos - 2];
			
				break;
			
			case 'sup':
				$this->tags[$pos] = 'sup';
				$this->oldFontSize = $this->fontSizePt;
				$this->setFontSize($this->oldFontSize - 2);
				$this->setXY($this->getX(),$this->getY() - 2);
			break;
			
			case '/sup':
				unset( $this->tags[$pos - 1] );
				$this->setXY( $this->getX(), $this->getY() + 2 );
				$this->setFontSize( $this->oldFontSize );
				
				break;
			
			case 'sub':
				$this->tags[$pos]  = 'sub';
				$this->oldFontSize = $this->fontSizePt;
				$this->OldY = $this->getY();
				$this->setFontSize( $this->fontSizePt - 2 );
				$this->setXY( $this->getX(), $this->getY() + $this->oldFontSize / 3 );
		
				break;
			
			case '/sub':
				unset( $this->tags[$pos - 1] );
				$this->setXY( $this->getX(), $this->OldY );
				$this->setFontSize( $this->oldFontSize );
				
				break;
			
			case "newPage":
				$this->addPage();
				break;
		}
		
		$this->_changeClass( $c );
	}
	
	/**
	 * @access  private
	 */
	function _parseHTMLTable( $t )
	{
		$this->ln();
		$pdfTest = $this;
		$tab = new PDF_HTMLTable( &$pdfTest, $t );
		$tab->widthCol( &$pdfTest );
		$addPage = $tab->testWriteTable( &$pdfTest );
		
		$tab = new PDF_HTMLTable( &$this, $t );
		$tab->widthCol( &$this );
		$tab->writeTable( &$this, $addPage );
		$tab->drawCell( &$this );
	}

	/**
	 * @access  private
	 */	
	function _writeHTMLLink( $href, $t )
	{
		/*
		$pos = count( $this->tags );
		$OldClass = $this->classes[$pos - 1];
		$this->_changeClass( "link" );
		$href = eregi_replace( '<a[^>]* href="([^"]+)"[^>]*>', "\\1", $href );
		$this->write( $this->fontSizePt + 2, $t, $href );
		$this->_changeClass( $OldClass );
		*/
		
		$href = eregi_replace( '<a[^>]* href="([^"]+)"[^>]*>', "\\1", $href );
		
        $this->setTextColor( 0, 0, 255 );
		$this->setFont( $this->fontFamily, 'U' );
        $this->fontStyle = 'U'; 
        $this->write( $this->fontSizePt + 2, $t, $href ); 
        $this->setFont( $this->fontFamily, '' );
        $this->setTextColor( 0, 0, 0 );
	}

	/**
	 * @access private
	 */
	function _writeHTMLSpan( $span, $t )
	{	
		// store current values
		$family = $this->fontFamily;
		$style  = $this->fontStyle;
		$size   = $this->fontSizePt;
		$color  = $this->textColor;
				
		$class = eregi_replace( '<span[^>]* class="([^"]+)"[^>]*>', "\\1", $span );
        $this->_changeClass( $class );
        $this->write( $this->fontSizePt, $t );
		
		$this->setFont( $family, $style, $size );
		$this->textColor = $color;
	}
	
	/**
	 * @access  private
	 */	
	function _addHTMLImage( $t )
	{
		eregi( '<img src="([^".]+)\.([^"]{3})"[^>]*>', $t, $i );
		$ext = strtolower( $i[2] );
		$f   = PDF_IMAGEPATH . $i[1] . "." . $ext;

		if ( $ext == "jpg" || $ext == "png" )
		{
			eregi( 'alt="([^"]+)"', $t, $z );
			$info = getimagesize( $f );
			$this->ln();
			
			if ( $info[0] > $this->wPt )
				$info[0] = $this->wPt - $this->lMargin - $this->rMargin;
			
			$x = $this->getX();
			$this->setX( ( $this->wPt - $info[0] ) / 2 );
			
			if ( $this->getY() + $info[1] > $this->hPt )
				$this->addPage();
				
			if ( strlen( $z[1]) > 0 )
			{
				$this->write( $this->fontSizePt + 2, $z[1] );
				$this->ln( $this->fontSizePt + 2 );
			}
			
			$y = $this->getY();
			$this->image( $f, ( $this->wPt - $info[0] ) / 2, $y, $info[0], $info[1] );
			$this->setXY( $x, $y + $info[1] + $this->fontSizePt + 2 );
			
			return true;
		}
		else
		{
			return PEAR::raiseError( 'Image format not supported.'  );
		}
	}
		
	/**
	 * Read a 4-byte integer from file.
	 *
	 * @access  private
	 */
	function _freadint( $f )
	{
		$i  = ord( fread( $f, 1 ) ) << 24;
		$i += ord( fread( $f, 1 ) ) << 16;
		$i += ord( fread( $f, 1 ) ) <<  8;
		$i += ord( fread( $f, 1 ) );

		return $i;
	}
	
	/**
	 * @access  private
	 */
	function _textstring( $s )
	{
		if ( $this->encrypted )
			$s = $this->_rc4( $this->_objectkey( $this->n ), $s );
		
		return '(' . $this->_escape( $s ) . ')';
	}
	
	/**
	 * Draw arc.
	 *
	 * @access  private
	 */
	function _arc( $x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
		
		$this->_out( sprintf( '%.2f %.2f %.2f %.2f %.2f %.2f c',
			$x1 * $this->k,
			( $h - $y1 ) * $this->k,
			$x2 * $this->k,
			( $h - $y2 ) * $this->k,
			$x3 * $this->k,
			( $h - $y3 ) * $this->k
		) );
	}

	/**
	 * Compute key depending on object number where the encrypted data is stored.
	 *
	 * @access  private
	 */
	function _objectkey( $n )
	{
		return substr( $this->_md5_16( $this->encryption_key . pack( 'VXxx', $n ) ), 0, 10 );
	}

	/**
	* Escape special characters.
	*
	* @access  private
	*/
	function _escape( $s )
	{
		$s = str_replace( '\\', '\\\\', $s );
		$s = str_replace( ')',  '\\)',  $s );
		$s = str_replace( '(',  '\\(',  $s );
		$s = str_replace( "\r", '\\r',  $s );
		return $s;
	}
	
	/**
	 * @access  private
	 */	
	function _putstream( $s )
	{
		if ( $this->encrypted )
			$s = $this->_rc4( $this->_objectkey( $this->n ), $s );
		
		$this->_out( 'stream' );
		$this->_out( $s );
		$this->_out( 'endstream' );
	}
	
	/**
	 * @access  private
	 */	
	function _getDimCell( $d, $coordCell )
	{
		$l = split( $d, $coordCell );
		$dimCell = array( "lg" => 1, "ht" => 1 );
		
		for ( $i = 0; $i < count( $l ) - 1; $i++ )
		{
			if ( substr( $l[$i], 0, 1 ) == substr( $l[$i + 1], 0, 1 ) )
				$dimCell["lg"]++;
				
			if ( substr( $l[$i], 1, 1 ) == substr( $l[$i + 1], 1, 1 ) )
				$dimCell["ht"]++;
		}
		
		return $dimCell;
	}

	/**
	 * @access  private
	 */		
	function _traceCell( $x, $y, $w, $h )
	{
		$this->line( $x, $y, $x + $w, $y );
		$this->line( $x + $w, $y, $x + $w, $y + $h );
		$this->line( $x + $w, $y + $h, $x, $y + $h );
		$this->line( $x, $y + $h, $x, $y );
	}
	
	/**
	 * RC4 is the standard encryption algorithm used in PDF format.
	 *
	 * @access  private
	 */
	function _rc4( $key, $text )
	{
		if ( $this->last_rc4_key != $key ) 
		{
			$k   = str_repeat( $key, 256 / strlen( $key ) + 1 );
			$rc4 = range( 0, 255 );
			$j   = 0;
			
			for ( $i = 0; $i < 256; $i++ )
			{
				$t = $rc4[$i];
				$j = ( $j + $t + ord( $k{$i} ) ) % 256;
				$rc4[$i] = $rc4[$j];
				$rc4[$j] = $t;
			}
			
			$this->last_rc4_key   = $key;
			$this->last_rc4_key_c = $rc4;
		} 
		else 
		{
			$rc4 = $this->last_rc4_key_c;
		}

		$len = strlen( $text );
		$a   = 0;
		$b   = 0;
		$out = '';
		
		for ( $i = 0; $i < $len; $i++ )
		{
			$a = ( $a + 1 ) % 256;
			$t = $rc4[$a];
			$b = ( $b + $t ) % 256;
			$rc4[$a] = $rc4[$b];
			$rc4[$b] = $t;
			$k = $rc4[( $rc4[$a] + $rc4[$b] ) % 256];
			$out .= chr( ord( $text{$i} ) ^ $k );
		}

		return $out;
	}

	/**
	 * Get MD5 as binary string.
	 *
	 * @access  private
	 */
	function _md5_16( $string )
	{
		return pack( 'H*', md5( $string ) );
	}

	/**
	 * Compute O value.
	 *
	 * @access  private
	 */
	function _ovalue( $user_pass, $owner_pass )
	{
		$tmp = $this->_md5_16( $owner_pass );
		$owner_RC4_key = substr( $tmp, 0, 5 );
		
		return $this->_rc4( $owner_RC4_key, $user_pass );
	}

	/**
	 * Compute U value.
	 *
	 * @access  private
	 */
	function _uvalue()
	{
		return $this->_rc4( $this->encryption_key, $this->padding );
	}

	/**
	 * Compute encryption key.
	 *
	 * @access  private
	 */
	function _generateencryptionkey( $user_pass, $owner_pass, $protection )
	{
		// Pad passwords
		$user_pass  = substr( $user_pass  . $this->padding, 0, 32 );
		$owner_pass = substr( $owner_pass . $this->padding, 0, 32 );
		
		// Compute O value
		$this->ovalue = $this->_ovalue( $user_pass, $owner_pass );
		
		// Compute encyption key
		$tmp = $this->_md5_16( $user_pass . $this->ovalue . chr( $protection ) . "\xFF\xFF\xFF" );
		$this->encryption_key = substr( $tmp, 0, 5 );
		
		// Compute U value
		$this->uvalue = $this->_uvalue();
		
		// Compute P value
		$this->pvalue = -( ( $protection ^ 255 ) + 1 );
	}
	
	/**
	 * Add a line to the document.
	 *
	 * @access  private
	 */
	function _out( $s )
	{
		if ( $this->state == 2 )
			$this->pages[$this->page] .= $s . "\n";
		else
			$this->buffer .= $s . "\n";
	}
} // END OF PDF


/**
 * Handle silly IE contype request
 */
/*
if ( isset( $HTTP_ENV_VARS['HTTP_USER_AGENT'] ) && $HTTP_ENV_VARS['HTTP_USER_AGENT'] == 'contype' )
{
	header( 'Content-Type: application/pdf' );
	exit;
}
*/

?>
