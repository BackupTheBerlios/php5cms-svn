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
|         Roy Kaldung                                                  |
+----------------------------------------------------------------------+
*/


using( 'format.pdf.lib.PDFTagNode' );
using( 'util.Util' );


define( 'PDFTAG_VERSION',    '1.0' );
define( 'PDFTAG_PTPERMM',    0.3528 ); // points per mm 1pt = 1/72 inch = 0.3528 mm
define( 'PDFTAG_INDENTCHAR', '&nbsp;' );
define( 'PDFTAG_INDENT',     2 );
define( 'MEASURE_REGEX',     "([0-9]{1,5}(\.[0-9]{1,}){0,1})([ ]{0,1})(cm|mm|in|pt){0,1}" );
define( 'OP_REGEX',          "[+*-/]" );


/**
 * The need to handle a lot of modifiable pdf-documents with PHP and the 
 * PDFlib was the reason for creating PDFTag.
 *
 * In order to combine PDFTag with template engines the decision for an 
 * XML-based format, similar to html, was made.
 *
 * requires pdf extension, dom-support is optional
 *
 * @package format_pdf_lib
 */

class PDFTag extends PEAR
{
    /**
     * @var string where to store the generated PDF (in memory or as a file)
     * @access private
     */
    var $pdfDest;

    /**
     * @var resource the PDFlib resourcehandle
     * @access private
     */
    var $pPDF;

    /**
     * @var string contains the source XML
     * @access private
     */
    var $XML;

    /**
     * @var resource the filehandle, when creating the PDf on the filesystem
     * @access private
     */
    var $fpPDF;

    /**
     * @var string the filename for the created pdf (temporary)
     * @access private
     */
    var $filePDF; // the filename for creating the PDF on the filesystem

    /**
     * @var string contains the generated PDF data
     * @access private
     */
    var $PDFdata;

    /**
     * @var array pre-defined page formats
     * @access private
     */
    var $pageFormat;

    /**
     * @var string the place to look for an errormessage
     * @access public
     */
    var $error = '';

    /**
     * @var boolean if true, the processing time is measured
     * @access public
     */
    var $pdfProfile;
   
    /**
     * @var array contains the data for images in memory
     * @access private
     */
    var $imgDataStack = array();

    /**
     * @var float
     */
    var $processingTime;

    /**
     * @var double the start time of processing
     * @access private
     */
    var $tStart;

    /**
     * @var double the end time of processing
     * @access private
     */
    var $tEnd;

    /**
     * @var array contains all warnings
     */
    var $warnings = array();

    /**
     * @var array for alias mapping of elements
     * @access private
     */
    var $tagAlias  = array();

    /**
     * @access private
     */
    var $fontStack = array();

    /**
     * @access private
     */
    var $outlineStack = array();

    /**
     * @access private
     */
    var $tmplStack;

    /**
     * @var integer pageCount
     * @access public 
     */
    var $pageCount = 0;

    /**
     * @access private
     */
    var $imgStack = array();

    /**
     * hier steht die beschreibung
     * @access public
     * @var array
     */
    var $allowedAttributes = array();

    /**
     * @var boolean
     * @access private
     */
    var $checkOnly = false;
    
    /**
     * @var double
     * @access private
     */
    var $versionPDFlib;

    /**
     * @var string
     * @access private
     */
    var $useParser;

    /**
     * @var boolean
     * @access private
     */
    var $stopOnWarning = false;

    var $userObj;
    var $userFunc;
    var $userExpr;

    
    /**
     * Constructor
	 *
     * @param string $type default 'mem', for creating the pdf in memory, otherwise 'fs' (on filesystem)
     * @param string $src optional, contains the XML source, if $src is a file, the class use it's content
     * @access public
     * @return void
     */
    function PDFTag( $type = 'mem', $src = '' ) 
	{
        /* get the version of the PDFlib */
        $tmpPDF       = pdf_new();
        $majorVersion = pdf_get_value( $tmpPDF, "major" );
        $minorVersion = pdf_get_value( $tmpPDF, "minor" );

        unset( $tmpPDF );
        $this->versionPDFlib = "$majorVersion.$minorVersion";       
        $this->setType( $type );
        
		$this->pageFormat = array(
			'A4'     => array(  210 / PDFTAG_PTPERMM, 297 / PDFTAG_PTPERMM ),
			'DIN A4' => array(  210 / PDFTAG_PTPERMM, 297 / PDFTAG_PTPERMM ),
			'A5'     => array(  148 / PDFTAG_PTPERMM, 210 / 0.3528 ),
			'DIN A5' => array(  148 / PDFTAG_PTPERMM, 210 / PDFTAG_PTPERMM ),
			'A6'     => array(  105 / PDFTAG_PTPERMM, 148 / PDFTAG_PTPERMM ),
			'DIN A6' => array(  105 / PDFTAG_PTPERMM, 148 / PDFTAG_PTPERMM ),
			'letter' => array(  612,  792 ),
			'legal'  => array(  612, 1008 ),
			'ledger' => array( 1224,  792 ),
			'11x17'  => array(  792, 1224 )
		);
		
        $this->tagAlias = array(
			'text' => 'showxy',
			'setlinewidth' => 'linewidth'
		);
        
        if ( $src != '' )
            $this->XML = $src;

        /* check for all required php extensions */
        if ( !Util::extensionExists( 'domxml' ) ) 
		{
            $this->addWarning( "missing php-extension 'domxml'" );
            $this->setParser( 'SAX' );
        } 
		else 
		{
            $this->setParser( 'DOM' );
        }
		
        if ( !Util::extensionExists( 'pdf' ) )
		{
			$this = new PEAR_Error( "Missing pdf extension." );
			return;
		}
    }


    /**
     * Defines a callback function which will be called after each closing page element.
     * 
     * @access public
     * @return boolean
     */
    function setCallback( $obj, $fn, $expr ) 
	{
        $this->userObj  = $obj;
        $this->userFunc = $fn;
        $this->userExpr = $expr;

        return true;
    }

    /**
     * Tells PDFTag which XML parser should be used.
	 *
     * @param string $parser
     * @access public
     * @return boolean
     */
    function setParser( $parser = 'DOM' ) 
	{
        $this->useParser = ( strtoupper( $parser ) == 'DOM' )? 'DOM' : 'SAX';
        return true;
    }

    /**
     * Returns which xml parser should be used.
	 *
     * @return string
     * @access public
     * @return string
     */
    function getParser()
	{
        return $this->useParser;
    }

    /**
     * Decides if PDFTag will stop on errors, without a parameter it returns the current state.
	 *
     * @access public
     * @param $mode boolean optional
     * @return boolean
     */
    function stopOnWarning( $mode = null ) 
	{
        if ( $mode == null ) 
		{
            return $this->stopOnWarning;
        } 
		else 
		{
            $this->stopOnWarning = ( $mode == true )? true : false;
            return true;
        }
    }

    /**
     * Set the attribute checkOnly for PDFTag and enable checking the source without generating the pdf.
	 *
     * @param boolean
     * @access public
     * @return boolean
     */
    function setCheckOnly( $mode ) 
	{
        $this->checkOnly = $mode;
        return true;
    }
    
    /**
     * Defines where to create the PDF, in memory or in the filesystem.
	 *
     * @param string $type   either 'mem' or 'fs'
     * @access public
     * @return boolean
     */
    function setType( $type = "" ) 
	{
        $this->pdfDest = ( strtoupper( $type ) == "FS" )? 'fs' : 'mem';
        return true;
    }

    /**
     * Read the XML source from a string.
	 *
     * @param string $XMLstring
     * @access public
     * @return boolean
     */
    function readFromString( $XMLstring ) 
	{
        $this->XML = $XMLstring;
        return true;
    }

    /**
     * Read the source XML from the specified file.
	 *
     * @param string $file the file containing the XML
     * @access public
     * @return boolean true on success, otherwise false (check PDFTag::error)
     */
    function readFromFile( $file ) 
	{
        $this->error = '';
        $srcExist = file_exists( $file );
        $fp = fopen( $file , "r" );
		
        if ( !$fp ) 
		{
            if ( !$srcExist )
                $this->error = "Can't find source file '$file'";
            else
                $this->error = "Can't open source file '$file'";
            
            return false;
        }
        
		$data = '';
        while ( !feof( $fp ) )
			$data .= fgets( $fp, 8192 );
        
        fclose( $fp );
        $this->XML = $data;

        return true;
    }

    /**
     * Sends the generated pdf to the browser, even when no headers are send.
	 *
     * @param string $desc if not empty this string will be sent with the header 'Content-Description'
     * @return boolean true if all works fine, otherwise false
     * @access public
     */
    function dumpPDF( $desc = '' ) 
	{
        $this->error = '';
        
		if ( !headers_sent() ) 
		{
            header( 'Pragma: ' );
            header( 'Cache-control: cache' );

            if ( $desc != '' )
                header( "Content-Description: $desc" );
            
            header( "Content-Type: application/pdf" );
            $pdf_length = strlen( $this->PDFdata );
            header( "Content-length: $pdf_length" );
            print $this->PDFdata;
			
            return true;
        } 
		else 
		{
            $this->error = "Headers already sent.";
            return false;
        }
    }

    /**
     * Add a warning to the warnings array.
	 *
     * @param string $txt
     * @access private
     * @return boolean
     */
    function addWarning( $txt ) 
	{
        $this->warnings[] = $txt;
        return true;
    }
	
	function hasWarning()
	{
		return count( $this->warnings ) > 0? true: false;
	}

	function getWarnings()
	{
		return $this->warnings;
	}
	
    /**
     * Creates the desired PDF from the source.
	 *
     * @access public
     * @return boolean true on success, otherwise false
     */
    function generatePDF()
	{
        $this->error = '';
        
		if ( strlen( trim( $this->XML ) ) == 0 ) 
		{
            $this->error = "XML source is empty.";
            return false;
        }
		
        $this->tStart = Util::getMicrotime();
        
		if ( $this->useParser == 'DOM' ) 
		{
            $dom = @domxml_open_mem( $this->XML );
			
            if ( !$dom ) 
			{
                $this->error = "Can't create DOM object.";
                return false;
            }
			
            $root = $dom->document_element();
            $ret  = $this->parseXML_DOM( $root );
        } 
		else 
		{
            $ret = $this->parseXML_SAX();
        }
		
        if ( !$ret ) 
		{
            $this->cleanUp();
            return false;
        }
		
        $this->tEnd = Util::getMicrotime();
        $this->processingTime = ( $this->tEnd - $this->tStart );
		
        if ( $this->pdfProfile ) 
		{
            print "processing time "         . substr( $this->processingTime, 0, 5 ) . " sec<br>";
            print "generated pages: "        . number_format( $this->pageCount, 0 ) . "<br>";
            print "Size for source: "        . number_format( strlen( $this->XML ) / 1024, 2, ',', '.' ) . " kb<br>";
            print "Size for generatet PDF: " . number_format( strlen( $this->PDFdata ) / 1024, 2, ',', '.' ) . " kb<br>";
            print "Avg size per page: "      . number_format( strlen( $this->PDFdata ) / 1024 / $this->pageCount, 2, ',', '.' ) . " kb<br>";
        }
		
        return $ret;
    }

    /**
     * Clean the workspace, close file and free resources if called.
	 *
     * @access private
     * @return boolean
     */
    function cleanUp()
	{
        if ( isset( $this->pPDF ) )
            @pdf_close( $this->pPDF );
        
        return true;
    }

    /**
     * Parses the desired xml node via the domxml extension of php.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */  
    function parseXML_DOM( &$node ) 
	{
        $tag = '';
        $ret = true;
        
		if ( $node->type == XML_ELEMENT_NODE ) 
		{
            $tag = $node->tagname;
            
			if ( $node->has_attributes() )
                $attributes = $node->attributes();
            else
                $attributes = null;
        }
		
        if ( $tag != "" ) 
		{
            $fn = "element_$tag";
            
			if ( !method_exists( $this, $fn ) ) 
			{
                if ( isset( $this->tagAlias[$tag] ) )
                    $fn = "element_" . $this->tagAlias[$tag];
                
                if ( !method_exists( $this, $fn ) ) 
				{
                    $this->addWarning( "Non-existing handler for $tag." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                }
            } 
			else 
			{
                $ret = $this->$fn( $node );
                
				if ( !$ret )
                    print "error $tag " . __LINE__ . "<br>";
            }
			
            if ( $node->has_child_nodes() ) 
			{
                $child = $node->child_nodes();
                
				for ( $i = 0; $i < count( $child ); $i++ ) 
				{
                    $ret = $this->parseXML_DOM( $child[$i] );
					
                    if ( $this->error != '' )
                        return false;
                    
                    if ( $this->stopOnWarning == true && $ret == false )
                        return false;
                }
            }
			
            if ( method_exists( $this, $fn ) ) 
			{
                $nullRef = null;
                $ret = $this->$fn( $nullRef );
            }
        }
		
        return $ret;
    }

    /**
     * Parses the xml document with the php builtin sax parser.
	 *
     * @access private
     * @return void
     */
    function parseXML_SAX()
	{
        $parser = xml_parser_create();
        
		if ( !$parser ) 
		{
            $this->error = "Can't create xml parser (sax).";
            return false;
        } 
		else 
		{
            $values = array();
            
			if ( xml_parse_into_struct( $parser, $this->XML, $values ) ) 
			{
                xml_parser_free( $parser );
                $nullRef = null;
				
                for ( $i = 0; $i < count( $values ); $i++ ) 
				{
                    $tag = strtolower( $values[$i]["tag"] );
                    $tag = str_replace( "pdftag:", "", $tag );
                    $fn  =  "element_" . $tag;
					
                    if ( !method_exists( $this, $fn ) ) 
					{
                        if ( isset( $this->tagAlias[$tag] ) )
                            $fn = "element_" . $this->tagAlias[$tag];
                        
                        if ( !method_exists( $this, $fn ) ) 
						{
                            $this->addWarning( "Non-existing handler for $tag." );
							
                            if ( $this->stopOnWarning == true )
                                return false;
                            
                            break;
                        }
                    }
					
                    $node = new PDFTagNode( $tag, $values[$i]["attributes"] );
                    $node->set_content( $values[$i]["value"] );
                    
					switch ( $values[$i]["type"] ) 
					{
                    	case 'open':
                        	$ret = $this->$fn( $node );
                        	break;
							
                    	case 'close':
                        	$ret = $this->$fn( $nullRef );
                        	break;
							
                    	case 'complete':
                        	$ret = $this->$fn( $node );
                        	$ret = $this->$fn( $nullRef ) || $ret;
                        	break;
                    	
						case 'cdata':
                    	
						default:
                    }
					
                    unset( $node );
					
                    if ( $this->error != '' )
                        return false;
                    
                    if ( $this->stopOnWarning == true && $ret == false )
                        return false;
                }
            } 
			else 
			{
                $this->error  = "XML parser error (";
                $this->error .= xml_error_string( xml_get_error_code( $parser ) );
                $this->error .= ") in line " . xml_get_current_line_number( $parser );
                
				xml_parser_free( $parser );
                return false;
            }
        }
		
        return true;
    }

    /**
     * Process element document.
	 *
     * @param object $node 
     * @access private
     * @return boolean
     */  
    function element_document( &$node ) 
	{
        $this->error = '';
        
		if ( $node == null ) 
		{
            if ( $this->pdfDest == 'fs' )
                fclose( $this->fpPDF );
            
            pdf_close( $this->pPDF );
            $this->PDFdata = pdf_get_buffer( $this->pPDF );
			
            if ( strlen( $this->PDFdata ) > 0 )
                pdf_delete( $this->pPDF );
        } 
		else 
		{
            if ( $this->pdfDest == 'fs' ) 
			{
                // generate temporary filename
                if ( $this->filePDF == '' )
                    $this->filePDF = tempnam( '.', 'pdftag_' );
                
                $this->fpPDF = fopen( $this->filePDF, 'wb' );
				
                if ( !$this->fpPDF ) 
				{
                    $this->error = "Can't create outputfile '" . $this->filePDF . "'";
                    return false;
                }
				
                $this->pPDF = pdf_open( $this->fpPDF );
            } 
			else 
			{
                $this->pPDF = pdf_new();
                pdf_open_file( $this->pPDF, "" );
            }
			
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = ( $this->getParser() == 'DOM' )? utf8_decode( $attr[$i]->value ) : $attr[$i]->value;
            
            if ( isset( $author ) )
                pdf_set_info( $this->pPDF, 'Author', $author );
            
            if ( isset( $creator ) )
                pdf_set_info( $this->pPDF, 'Creator', $creator.' / PDFTagClass ' . $this->version() );
            else
                pdf_set_info( $this->pPDF, 'Creator', 'PDFTagClass ' . $this->version() );
            
            if ( isset( $title ) )
                pdf_set_info( $this->pPDF, 'Title', $title );
            
            if ( isset( $subject ) )
                pdf_set_info( $this->pPDF, 'Subject', $subject );
            
            if ( isset( $keywords ) )
                pdf_set_info( $this->pPDF, 'Keywords', $keywords );
            
            $this->commonAttributes( $attr );
        }
		
        return true;
    }

    /**
     * Implements common attributs, will only be called by function _$tag
     * these attributes are:
	 *
     *   {font="" size="" encoding="" {embed="embed"}}|{id="" size=""}	set_font / setfont
     *   {bordercolor="r,g,b"}											set_border_color
     *   {borderdash="b,w"}												set_border_dash
     *   {borderstyle="[solid|dashed]" borderwidth=""}					set_border_style
     *   {charspacing=""}												set_char_spacing
     *   {horiz_scaling=""}												set_horiz_scaling
     *   {leading=""}													set_leading
     *   {text_rendering="[[0|1|2|3|4|5|6|7]|[filled|border|filledborder|hidden|filledclipped|borderclipped|filledborderclipped|clipped]]"}  set_text_rendering
     *   {text_rise=""}													set_text_rise
     *   {word_spacing=""}												set_word_spacing
     *   {dash="b,w"}													set_dash
     *   {flat=""}														set_flat
     *   {gray=""}														set_gray
     *   {grayfill=""}													set_grayfill
     *   {graystroke=""}												set_gray_stroke
     *   {linecap=[0|1|2]}												set_linecap
     *   {linejoin=[0|1|2]}												set_linejoin
     *   {linewidth=""}													set_linewidth
     *   {matrix="a,b,c,d,e,f"}											setmatrix
     *   {color="r,g,b"}												setrgbcolor
     *   {fillcolor="r,g,b"}											setrgbcolor_fill
     *   {strokecolor="r,g,b"}											setrgbcolor_stroke
     *
     * @param    $attr   array which contains attributes
     * @access   private
     */
    function commonAttributes( &$attr ) 
	{
        for ( $i = 0; $i < count( $attr ); $i++ )
            ${$attr[$i]->name} = $attr[$i]->value;
        
        // charspacing
        if ( isset( $charspacing ) ) 
		{
            // pdf_set_char_spacing($this->pPDF, (double)$charspacing);
            pdf_set_value( $this->pPDF, 'charspacing', (double)PDFTag::calcTerm( $charspacing ) );
        }
		
        // gray
        if ( isset( $gray ) )
            pdf_setgray( $this->pPDF, (double)$gray );
        
        // grayfill
        if ( isset( $grayfill ) )
            pdf_setgray_fill( $this->pPDF, (double)$grayfill );
        
        // graystroke
        if ( isset( $graystroke ) )
            pdf_setgray_stroke( $this->pPDF, (double)$graystroke );
        
        // linewidth
        if ( isset( $linewidth ) )
            pdf_setlinewidth( $this->pPDF, (double)PDFTag::calcTerm( $linewidth ) );
        
        if ( isset( $flat ) )
            pdf_setflat( $this->pPDF, (double)$flat );
        
        // color
        if ( isset( $color ) ) 
		{
            $color = explode( ',', $color );
			
            if ( count( $color ) == 3 ) 
			{
                $r = $color[0];
                $g = $color[1];
                $b = $color[2];
                
				pdf_setrgbcolor( $this->pPDF, (double)$r, (double)$g, (double)$b );
            } 
			else 
			{
                $this->addWarning( "Wrong values for color='r,g,b'." );
            }
        }
		
        // strokecolor
        if ( isset( $strokecolor ) ) 
		{
            $strokecolor = explode( ',', $strokecolor );
			
            if ( count( $strokecolor) == 3 ) 
			{
                $r = $strokecolor[0];
                $g = $strokecolor[1];
                $b = $strokecolor[2];
                
				pdf_setrgbcolor_stroke( $this->pPDF, (double)$r, (double)$g, (double)$b );
            } 
			else 
			{ 
                $this->addWarning( "Wrong values for strokecolor='r,g,b'." );
            }
        }
		
        // fillcolor
        if ( isset( $fillcolor ) ) 
		{
            $fillcolor = explode( ',', $fillcolor );
			
            if ( count( $fillcolor) == 3 ) 
			{
                $r = $fillcolor[0];
                $g = $fillcolor[1];
                $b = $fillcolor[2];
                
				pdf_setrgbcolor_fill( $this->pPDF, (double)$r, (double)$g, (double)$b );
            } 
			else 
			{
                $this->addWarning( "Wrong values for fillcolor='r,g,b'." );
            }
        }
		
        // font
        if ( isset( $font ) && isset( $size ) && isset( $encoding ) ) 
		{
            $embed = ( isset( $embed ) && $embed == 'embed' )? 1 : 0;
            $fontHandle = pdf_findfont( $this->pPDF, $font, $encoding, $embed );
			
            if ( !$fontHandle )
				return PEAR::raiseError( "Error while pdf_findfont.", null, PEAR_ERROR_DIE );

            pdf_setfont( $this->pPDF, $fontHandle, (double)PDFTag::calcTerm( $size ) );
        }
		
        if ( isset( $fontid ) ) 
		{
            if ( isset( $this->fontStack[$fontid] ) ) 
			{
                $curFont = $this->fontStack[$fontid];
                
				if ( !isset( $size ) )
                    $size = $curFont["handle"];
                
                pdf_setfont( $this->pPDF, $fontHandle, (double)PDFTag::calcTerm( $size ) );
            } 
			else 
			{
                $this->addWarning( "Usage of prior undefined font fontid='$fontid'." );
            }
        }
		
        if ( isset( $linecap ) ) 
		{
            $linecap = (int)$linecap;
            
			if ( $linecap < 0 || $linecap > 2 )
                $this->addWarning( "Wrong value for attribute 'linecap'." );
            else
                pdf_setlinecap( $this->pPDF, $linecap );
        }
		
        // dash 
        if ( isset( $dash ) ) 
		{
            $dash = explode( ',', $dash );
			
            if ( count( $dash ) == 2 ) 
			{
                $b = $dash[0];
                $w = $dash[1];
                
				pdf_setdash( $this->pPDF, (double)$b, (double)$w );
            } 
			else 
			{
                $this->addWarning( "Wrong values for dash='b,w'." );
            }
        }
		
        return true;
    }

    /**
     * Syntaxcheck for attributes.
	 *
     * @access   private
     * @return boolean
     * EXPERIMENTAL
     */
    function checkAttribute( $var ) 
	{
        // print $$var;
		
        $attr  = var_export( $var );
        $value = $var;
        
		if ( $var == '' ) 
		{
            $this->addWarning( "Empty attribute '$attr' not allowed." );
        } 
		else 
		{
            switch ( $attr ) 
			{
            	case 'border':
                	if ( in_array( $value, array( 'on', 'off', 'filled' ) ) )
                    	return true;
                	else
                    	return false;
                
                	break;
            
				default:
                	$this->addWarning( "Unkown attribute '$attr'." );
                	break;
            }
        }
    }

    /**
     * Process element page.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_page( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
            pdf_end_page( $this->pPDF );
            
			/* if a callback function is defined, just do it */
            if ( $this->userObj != null )
                $this->userObj->{$this->userFunc}( eval( $this->userExpr ) );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $format ) ) 
			{
                if ( isset( $this->pageFormat[$format] ) ) 
				{
                    $width  = $this->pageFormat[$format][0];
                    $height = $this->pageFormat[$format][1];
                } 
				else 
				{
                    $this->addWarning( "Undefined page-format $format." );
                }
            }
			
            if ( isset( $width ) && isset( $height ) ) 
			{
                if ( isset( $orientation ) ) 
				{
                    if ( $orientation == "landscape" ) 
					{
                        $save   = $width;
                        $width  = $height;
                        $height = $save;
                        
						unset( $save );
                    } 
					else 
					{
                        if ( $orientation != "portrait" )
                            $this->addWarning( 'Wrong value for <page orientation="">.' );
                    }
                }
				
                pdf_begin_page( $this->pPDF, (double)PDFTag::calcTerm( $width ), (double)PDFTag::calcTerm( $height ) );
                pdf_save( $this->pPDF );
                $this->commonAttributes( $attr );
                $this->pageCount++;
            } 
			else 
			{ 
                $this->addWarning( "Wrong/missing attributes 'format|(width|height)' for element 'page." );
            }
        }
		
        return true;
    }

    /**
     * Process element outline.
	 *
     * @access private
     * @return boolean
     */
    function element_outline( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( $node->get_content()!='' ) 
			{
                $text = $node->get_content();
                // $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content();
            }
			
            if ( isset( $text ) ) 
			{
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content();
                
				if ( isset( $parent ) ) 
				{
                    if ( isset( $this->outlineStack[$parent] ) ) 
					{
                        if ( isset( $open ) ) 
						{
                            $open = ( $open == 'open' )? 1 : 0;
                            $ret  = pdf_add_bookmark( $this->pPDF, $text, $this->outlineStack[$parent], $open );
                        } 
						else 
						{
                            $ret  = pdf_add_bookmark( $this->pPDF, $text, $this->outlineStack[$parent] );
                        }
                    } 
					else 
					{
                        $this->addWarning( "Undefined parent id '$parent' in element 'outline'." );
						
                        if ( $this->stopOnWarning == true )
                            return false;
                    }
                } 
				else 
				{
                    $ret = pdf_add_bookmark( $this->pPDF, $text );
                }
				
                if ( isset( $id ) ) 
				{
                    if ( isset( $this->outlineStack[$id] ) ) 
					{
                        $this->addWarning( "Overwriting existing outline id '$id'." );
						
                        if ( $this->stopOnWarning == true )
                            return false;
                    } 
					else 
					{
                        $this->outlineStack[$id] = $ret;
                    }
                }
            } 
			else 
			{
                $this->addWarning( "Element 'outline' without content." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element path.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_path( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
			
            if ( $node->get_content() != '' )
                $text = $node->get_content();
            
            if ( isset( $coord ) || isset( $text ) ) 
			{
                if ( isset( $text ) )
                    $coord = $text;
                
                $coord = explode( ',', $coord );
				
                if ( ( count( $coord ) % 2 ) != 0 ) 
				{
                    $this->addWarning( "Wrong parameter count for element 'path'." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                } 
				else 
				{
                    pdf_moveto( $this->pPDF, (double)PDFTag::calcTerm( $coord[0] ), (double)PDFTag::calcTerm( $coord[1] ) );
					
                    for ( $i = 2; $i < count( $coord ); $i = $i + 2 )
                        pdf_lineto( $this->pPDF, (double)PDFTag::calcTerm( $coord[$i] ), (double)PDFTag::calcTerm( $coord[$i + 1] ) );
                    
                    pdf_closepath( $this->pPDF );
					
                    if ( isset( $filled ) && $filled == "filled" ) 
					{
                        if ( isset( $border ) && $border == "border" )
                            pdf_fill_stroke( $this->pPDF );
                        else
                            pdf_fill( $this->pPDF );
                    } 
					else 
					{
                        pdf_stroke( $this->pPDF );
                    }
                }
            } 
			else 
			{
                $this->addWarning( "Wrong/missing attribute(s)/content for element 'path'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element setfont.
	 *
     * @param object $node
     * @return boolean
     * @access private
     */
    function element_setfont( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( !isset( $encoding ) )
                $encoding = "winansi";
            
            if ( isset( $font ) && isset( $size ) && isset( $encoding ) ) 
			{
                $embed = ( isset( $embed ) && $embed == 'embed' )? 1 : 0;
                $fontHandle = pdf_findfont( $this->pPDF, $font, $encoding, $embed );
				
				if ( !$fontHandle )
					return PEAR::raiseError( "Error while pdf_findfont.", null, PEAR_ERROR_DIE );
	
                pdf_setfont( $this->pPDF, $fontHandle, (double)PDFTag::calcTerm( $size ) );
				
                if ( isset( $id ) ) 
				{
                    if ( isset( $this->fontStack[$id] ) ) 
					{
                        $this->addWarning( "Overwriting existing font id '$id'." );
                    } 
					else 
					{
                        $this->fontStack[$id]["font"]     = "$font"; 
                        $this->fontStack[$id]["size"]     = $size;
                        $this->fontStack[$id]["encoding"] = "$encoding";
                        $this->fontStack[$id]["embed"]    = (int)$embed;
                        $this->fontStack[$id]["handle"]   = $fontHandle;
                    }
                }
            } 
			else 
			{
                if ( isset( $fontid ) ) 
				{
                    if ( isset( $this->fontStack[$id] ) ) 
					{
                        if ( !isset( $size ) )
                            $size = $this->fontStack[$id]["size"];
                        
                        pdf_setfont( $this->pPDF, $this->fontStack[$id]["handle"], (double)$size );
                    } 
					else 
					{
                        $this->addWarning( "Usage of prior undefined font with id='$id'." );
						
                        if ( $this->stopOnWarning == true )
                            return false;
                    }
                } 
				else 
				{
                    $this->addWarning( "Missing attributes for element 'setfont'." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                }
            }
        }
		
        return true;
    }

    /**
     * Process element showxy.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_showxy( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            pdf_save( $this->pPDF );
            $attr = $node->attributes();
 
            for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = ( $this->getParser() == 'DOM' )? utf8_decode( $attr[$i]->value ) : $attr[$i]->value;
            
            $this->commonAttributes( $attr );
			
            if ( $node->get_content() != '' )
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content();
            
            if ( isset( $x ) && isset( $y ) ) 
			{
                if ( isset( $text ) ) 
				{
                    if ( !isset( $chop ) )
                        $chop = 'on';
                    
                    if ( $chop == 'on' )
                        $text = PDFTag::strClean( $text );
                    
                    if ( isset( $format ) )
                        $text = sprintf( $format, $text );
                    
                    pdf_show_xy( $this->pPDF, $text, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ) );
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'showxy'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }
    
    /**
     * Remove leading and trailing whitespaces.
	 *
     * @access private
     * @param string $text
     * @return string
     */
    function strClean( $text ) 
	{
        return PDFTag::strReverse( chop( PDFTag::strReverse( $text ) ) );
    }

    /**
     * Returns a given string in reversed order.
	 *
     * @access private
     * @param string $text
     * @return string
     */
    function strReverse( $text ) 
	{
        $str1 = $text;
        $str2 = '';
        
		while ( strlen( $str1 ) > 0 ) 
		{
            $str2 .= substr( $str1, -1 );
            $str1  = substr( $str1, 0, -1 );
        }
		
        return $str2;
    }
    
    /**
     * Process element annotation.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_annotation( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = ( $this->getParser() == 'DOM' )? utf8_decode( $attr[$i]->value ) : $attr[$i]->value;
            
            if ( $node->get_content() != '' )
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content;
            
            if ( isset( $llx ) && isset( $lly ) && isset( $urx ) && isset( $ury ) && isset( $title ) && isset( $text ) ) 
			{
                $text = $text;
                pdf_add_annotation( $this->pPDF, (double)PDFTag::calcTerm( $llx ), (double)PDFTag::calcTerm( $lly ), (double)PDFTag::calcTerm( $urx ), (double)PDFTag::calcTerm( $ury ), $title, $text );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'annotation'." );

                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element note.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_note( &$node ) 
	{
        $iconArray = array(
			'comment', 
			'insert', 
			'note', 
			'paragraph', 
			'newparagraph', 
			'key', 
			'help'
		);
        
		if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = ( $this->getParser() == 'DOM' )? utf8_decode( $attr[$i]->value ) : $attr[$i]->value;
            
            if ( $node->get_content() != '' )
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content();
            
            $open = ( $open == 'open' )? 1 : 0;
            
			if ( !in_array( $icon, $iconArray ) )
                unset( $icon );
            
            if ( isset( $llx ) && isset( $lly ) && isset( $urx ) && isset( $ury ) && isset( $title ) && isset( $text ) && isset( $icon ) && isset( $open ) ) 
			{
                pdf_add_note( $this->pPDF, (double)PDFTag::calcTerm( $llx ), (double)PDFTag::calcTerm( $lly ), (double)PDFTag::calcTerm( $urx ), (double)PDFTag::calcTerm( $ury ), $text, $title, $icon, (int)$open );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'note'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }
    
    /**
     * Process element arc.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_arc( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            pdf_save( $this->pPDF );
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            $this->commonAttributes( $attr );
            
			if ( isset( $x ) && isset( $y ) && isset( $radius ) && isset( $start ) && isset( $end ) ) 
			{
                pdf_arc( $this->pPDF, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ), (double)PDFTag::calcTerm( $radius ), (double)$start, (double)$end );
				
                if ( isset( $filled ) && $filled == "filled" ) 
				{
                    if ( isset( $border )  && $border == "border" )
                        pdf_fill_stroke( $this->pPDF );
                    else
                        pdf_fill( $this->pPDF );
                } 
				else 
				{
                    pdf_stroke( $this->pPDF );
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'arc'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
    * Process element circle.
	*
    * @param object $node
    * @access private
    * @return boolean
    */
    function element_circle( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
			
            if ( isset( $x ) && isset( $y ) && isset( $radius ) ) 
			{
                pdf_circle( $this->pPDF, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ), (double)PDFTag::calcTerm( $radius ) );
				
                if ( isset( $filled ) && $filled == "filled" ) 
				{
                    if ( isset( $border )  && $border == "border" )
                        pdf_fill_stroke( $this->pPDF );
                    else
                        pdf_fill( $this->pPDF );
                } 
				else 
				{
                    pdf_stroke( $this->pPDF );
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'circle'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Processing element clip.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_clip( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            pdf_clip( $this->pPDF );
        }
		
        return true;
    }

    /**
     * Process element continue_text.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_continue_text( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = ( $this->getParser() == 'DOM' )? utf8_decode( $attr[$i]->value ) : $attr[$i]->value;
            
            if ( $node->get_content() != '' )
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content ) : $node->get_content();
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
			
            if ( isset( $text ) ) 
			{
                pdf_continue_text( $this->pPDF, $text );
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'text' for element 'continue_text'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element curveto.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_curveto( &$node ) 
	{
        if ( $node == null ) 
		{
            // pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            // pdf_save( $this->pPDF );
            // $this->commonAttributes( $attr );
			
            if ( $node->get_content() != '' )
                $coord = $node->get_content();
            
            if ( isset( $coord ) && trim( $coord ) != '' )
                $coord = explode( ',', $coord );
            
            if ( count( $coord ) != 6 ) 
			{
                $this->addWarning( "There must be six doublevalues in element 'curveto' present'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            } 
			else 
			{
                $x1 = $coord[0];
                $y1 = $coord[1];
                $x2 = $coord[2];
                $y2 = $coord[3];
                $x3 = $coord[4];
                $y3 = $coord[5];
            }
			
            if ( isset( $x1 ) && isset( $y1 ) && isset( $x2 ) && isset( $y2 ) && isset( $x3 ) && isset( $y3 ) ) 
			{
                pdf_curveto( $this->pPDF, (double)PDFTag::calcTerm( $x1 ), (double)PDFTag::calcTerm( $y1 ),
                                          (double)PDFTag::calcTerm( $x2 ), (double)PDFTag::calcTerm( $y2 ),
                                          (double)PDFTag::calcTerm( $x3 ), (double)PDFTag::calcTerm( $y3 ) );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'curveto'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element fill.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_fill( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
            pdf_fill( $this->pPDF );
        }
		
        return true;
    }

    /**
     * Process element fill_stroke.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_fill_stroke( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
            pdf_fill_stroke( $this->pPDF );
        }
		
        return true;
    }

    /**
     * Process element lineto.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_lineto( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $x ) && isset( $y ) ) 
			{
                pdf_lineto( $this->pPDF, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ) );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'moveto'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element rect.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_rect( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
			
            if ( isset( $x ) && isset( $y ) && isset( $width ) && isset( $height ) ) 
			{
                pdf_rect( $this->pPDF, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ), (double)PDFTag::calcTerm( $width ), (double)PDFTag::calcTerm( $height ) );
				
                if ( isset( $filled ) && $filled == "filled" ) 
				{
                    if ( isset( $border )  && $border == "border" )
                        pdf_fill_stroke( $this->pPDF );
                    else
                        pdf_fill( $this->pPDF );
                } 
				else 
				{
                    pdf_stroke( $this->pPDF );
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'rect'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }
	
    /**
     * Process element save.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_save( &$node ) 
	{
        if ( $node == null )
            pdf_restore( $this->pPDF );
        else
            pdf_save( $this->pPDF );
        
        return true;
    }

    /**
     * Process element rotate.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_rotate( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $angle ) ) 
			{
                pdf_rotate( $this->pPDF, (double)$angle );
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'angle' for element 'rotate'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element stroke.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_stroke( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $filled ) && $filled == "filled" ) 
			{
                if ( isset( $border ) ) 
				{
                    if ( checkAttribute( $border ) )
                         pdf_fill_stroke( $this->pPDF );
                } 
				else 
				{
                    pdf_fill( $this->pPDF );
                }
            } 
			else 
			{
                pdf_stroke( $this->pPDF );
            }
        }
		
        return true;
    }

    /**
     * Process element moveto.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_moveto( &$node ) 
	{
        if ( $node == null ) 
		{
            // pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            // pdf_save( $this->pPDF );
            // $this->commonAttributes( $attr );
			
            if ( isset( $x ) && isset( $y ) ) 
			{
                pdf_moveto( $this->pPDF, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ) );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for 'moveto'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element scale.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_scale( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $sx ) && isset( $sy ) ) 
			{
                pdf_scale( $this->pPDF, (double)$sx, (double)$sy );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for 'scale'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element show.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_show( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = ( $this->getParser() == 'DOM' )? utf8_decode( $attr[$i]->value ) : $attr[$i]->value;
            
            if ( $node->get_content() != '' )
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content();
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
            
			if ( isset( $text ) ) 
			{
                pdf_show( $this->pPDF, $text );
            } 
			else 
			{
                $this->addWarning( "Missing content/attribute 'text' for element show." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element set_leading.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_set_leading( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{ 
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $offset ) ) 
			{
                // pdf_set_leading( $this->pPDF, doubleval( $offset ) );
                pdf_set_value( $this->pPDF, 'leading', (double)$offset );
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'offset' for element 'set_leading'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element initgraphics.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_initgraphics( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            pdf_initgraphics( $this->pPDF );
        }
		
        return true;
    }

    /**
     * Process element border_color.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_border_color( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( $node->get_content() != '' )
                $colors = $node->get_content();
            
            if ( isset( $colors ) || ( isset( $r ) && isset( $g ) && isset( $b ) ) ) 
			{
                if ( isset( $colors ) ) 
				{
                    $colors = explode( ',', $colors );
					
                    if ( count( $colors ) != 3 ) 
					{
                        $this->addWarning( "Wrong parameter count for element 'border_color'." );
                        
						if ( $this->stopOnWarning == true )
                            return false;
                    } 
					else 
					{
                        $r = $colors[0];
                        $g = $colors[1];
                        $b = $colors[2];
                    }
                }
				
                pdf_set_border_color( $this->pPDF, (double)$r, (double)$g, (double)$b );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'border_color'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element translate.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_translate( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $x ) && isset( $y ) ) 
			{
                pdf_translate( $this->pPDF, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ) );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'translate'." );
                
				if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element duration.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_duration( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $value ) ) 
			{
                pdf_set_duration( $this->pPDF, (double)$value );
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'value' for element 'duration'." );
                
				if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element setrgbcolor.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_setrgbcolor( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( $node->get_content() != '' )
                $colors = $node->get_content();
            
            if ( isset( $colors ) || ( isset( $r ) && isset( $g ) && isset( $b ) ) ) 
			{
                if ( isset( $colors ) ) 
				{
                    $colors = explode( ',', $colors );
					
                    if ( count( $colors ) != 3 ) 
					{
                        $this->addWarning( "Wrong colour value for element 'border_color'." );
						
                        if ( $this->stopOnWarning == true )
                            return false;
                    } 
					else 
					{
                        $r = $colors[0];
                        $g = $colors[1];
                        $b = $colors[2];
                    }
                }
				
                pdf_setrgbcolor( $this->pPDF, (double)$r, (double)$g, (double)$b );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'setrgbcolor'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element set.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_set( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            $this->commonAttributes( $attr );
        }
		
        return true;
    }

    /**
     * Process element closepath.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_closepath( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_closepath( $this->pPDF );
        }
		
        return true;
    }

    /**
     * Process element template.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_template( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_end_template( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $width ) && isset( $height ) && isset( $id ) ) 
			{
                $ret = pdf_begin_template( $this->pPDF, (float)PDFTag::calcTerm( $width ), (float)PDFTag::calcTerm( $height ) );
				 
                if ( isset( $this->tmplStack[$id] ) ) 
				{
                    $this->addWarning( "Overwriting existing template id '$id'." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                } 
				else 
				{
                    $this->tmplStack[$id] = $ret;
                }
            } 
			else 
			{
                $this->addWarning( "Wrong/missing parameter for element 'template'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element settemplate.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_settemplate( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            pdf_save( $this->pPDF );
            $attr = $node->attributes();

            for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            $this->commonAttributes( $attr );
			
            if ( isset( $x ) && isset( $y ) && isset( $id ) ) 
			{
                if ( isset( $this->tmplStack[$id] ) ) 
				{
                    if ( !isset( $scale ) )
                        $scale = 1.0;
                    
                    pdf_place_image( $this->pPDF, $this->tmplStack[$id], (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ), (double)$scale );
                } 
				else 
				{
                    $this->addWarning( "id '$id' is not defined beyond this point." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                }
            } 
			else 
			{
                $this->addWarning( "Wrong/missing attributes for 'settemplate'." );
 
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element openimagefile.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_openimagefile( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $type ) ) 
			{
                if ( in_array( $type, array( 'tiff', 'png', 'jpeg', 'gif' ) ) ) 
				{
                    if ( isset( $src ) ) 
					{
                        if ( file_exists( $src ) ) 
						{
                            $fp = @fopen( $src, "rb" );
							
                            if ( !$fp ) 
							{
                                $this->error = "Can't open file '$src' for element 'openimagefile'.";
                                return false;
                            } 
							else 
							{
                                $imgData = '';
                                while ( !feof( $fp ) )
                                    $imgData .= fread( $fp, 8192 );
                                
                                fclose( $fp );
                                $fname = tempnam( '.', 'pdftag_' );
                                $fp = fopen( $fname, "wb" );
                                fwrite( $fp, $imgData );
                                fclose( $fp );
                                unset( $imgData );
                                $src= $fname;
                            }
							
                            $ret = pdf_open_image_file( $this->pPDF, $type, $src );
							
							if ( !$ret )
								return PEAR::raiseError( "Unable to open image.", null, PEAR_ERROR_DIE );

                            if ( isset( $fname ) )
                                unlink( $fname );
                        } 
						else 
						{
                            $this->error = "Can't find/open file '$src' for element 'openimagefile'.";
                            return false;
                        }
                    } 
					else 
					{
                        $this->addWarning( "Missing attribute 'src' for element 'openimagefile'." );
						
                        if ( $this->stopOnWarning == true )
                            return false;
                    }
					
                    if ( $ret ) 
					{
                        if ( isset( $this->imgStack[$id] ) ) 
						{
                            $this->addWarning( "Overwriting existing image id '$id'." );
							
                            if ( $this->stopOnWarning == true )
                                return false;
                        }
						
                        $this->imgStack[$id] = array(
							'handle' => $ret, 
							'type'   => $type, 
							'src'    => $src
						);
                    } 
					else 
					{
                        $this->addWarning( "Error during loading '$src' for element 'openimagefile'." );
						
                        if ( $this->stopOnWarning == true )
                            return false;
                    }
                } 
				else 
				{
                    $this->addWarning( "Wrong value of attribute 'type' for element 'openimagefile'." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'type' for element 'openimagefile'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element addthumbnail.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_addthumbnail( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $id ) ) 
			{
                if ( isset( $this->imgStack[$id] ) ) 
				{
                    pdf_add_thumbnail( $this->pPDF, $this->imgStack[$id]['handle'] );
                } 
				else 
				{
                    $this->addWarning( "Unknown image id='$id' of element 'addthumbnail'" );
                    
					if ( $this->stopOnWarning == true )
                        return false;
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'id' of element 'addthumbnail'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element placeimage.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_placeimage( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $id ) && isset( $x ) && isset( $y ) ) 
			{
                if ( !isset( $scale ) )
                    $scale = 1.0;
                
                if ( isset( $this->imgStack[$id]['handle'] ) ) 
				{
                    pdf_place_image( $this->pPDF, $this->imgStack[$id]['handle'], (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ), (double)$scale );
                } 
				else 
				{
                    $this->addWarning( "No image with id '$id' found for element 'placeimage'." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                }
            } 
			else 
			{
                $this->addWarning( "Missing parameter(s) for element 'placeimage'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element showboxed.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_showboxed( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
            
			if ( $node->get_content() != '' ) 
			{
                // $text = $node->get_content();
                $text = ( $this->getParser() == 'DOM' )? utf8_decode( $node->get_content() ) : $node->get_content();
            }
			
            if ( isset( $x ) && isset( $y ) && isset( $width ) && isset( $height ) ) 
			{
                if ( isset( $text ) ) 
                    pdf_show_boxed( $this->pPDF, $text, (double)PDFTag::calcTerm( $x ), (double)PDFTag::calcTerm( $y ), (double)PDFTag::calcTerm( $width ), (double)PDFTag::calcTerm( $height ), $mode );
            } 
			else 
			{
                $this->addWarning( "Wrong/missing attributes for element 'showboxed'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element dash.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_dash( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $b ) && isset( $w ) ) 
			{
                pdf_setdash( $this->pPDF, (double)$b, (double)$w );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'dash'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process element locallink.
	 *
     * @param object $node
     * @access public
     * @return boolean
     */
    function element_locallink( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $llx ) && isset( $lly ) && isset( $urx ) && isset( $ury ) && isset( $page ) && isset( $dest ) ) 
			{
                if ( in_array( $dest, array( 'retain', 'fitpage', 'fitwidth', 'fitheight', 'fitbox' ) ) ) 
				{
                    pdf_add_locallink( $this->pPDF, (float)PDFTag::calcTerm( $llx ), (float)PDFTag::calcTerm( $lly ), (float)PDFTag::calcTerm( $urx ), (float)PDFTag::calcTerm( $ury ), (int)$page, $dest );
                } 
				else 
				{
                    $this->addWarning( "Wrong value for attribute 'dest' of element 'locallink'." );
					
                    if ( $this->stopOnWarning == true )
                        return false;
                }
            } 
			else 
			{
                $this->addWarning( "Missing attribute for 'locallink'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }
   
    /**
     * Process element weblink.
	 *
     * @param object $node
     * @access public
     * @return boolean
     */
    function element_weblink( &$node ) 
	{
        if ( $node == null ) 
		{
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            if ( isset( $llx ) && isset( $lly ) && isset( $urx ) && isset( $ury ) && isset( $url ) ) 
			{
                pdf_add_weblink( $this->pPDF, (float)PDFTag::calcTerm( $llx ), (float)PDFTag::calcTerm( $lly ), (float)PDFTag::calcTerm( $urx ), (float)PDFTag::calcTerm( $ury ), $url );
            } 
			else 
			{
                $this->addWarning( "Missing attribute for 'weblink'." );
                
				if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process tag linewidth.
	 *
     * @param object $node
     * @access private
     * @return boolean
     */
    function element_linewidth( &$node ) 
	{
        if ( $node == null ) 
		{
            // pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            // pdf_save( $this->pPDF );
            
			if ( isset( $width ) ) 
			{
                pdf_setlinewidth( $this->pPDF, (double)PDFTag::calcTerm( $width ) );
            } 
			else 
			{
                $this->addWarning( "Missing attribute 'width' for element 'linewidth'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }

    /**
     * Process the element line.
	 *
     * @param object $node
     * @return boolean
     */
    function element_line( &$node ) 
	{
        if ( $node == null ) 
		{
            pdf_restore( $this->pPDF );
        } 
		else 
		{
            $attr = $node->attributes();
            
			for ( $i = 0; $i < count( $attr ); $i++ )
                ${$attr[$i]->name} = $attr[$i]->value;
            
            pdf_save( $this->pPDF );
            $this->commonAttributes( $attr );
			
            if ( $node->get_content() != '' )
                $coord = $node->get_content();
            
            if ( isset( $coord ) && trim( $coord ) != '' )
                $coord = explode( ',', $coord );
            
            if ( count( $coord ) != 4 ) 
			{
                $this->addWarning( "There must be four doublevalues in element 'line' present'." );
                
				if ( $this->stopOnWarning == true )
                    return false;
            } 
			else 
			{
                $x1 = $coord[0];
                $y1 = $coord[1];
                $x2 = $coord[2];
                $y2 = $coord[3];
            }
			
            if ( isset( $x1 ) && isset( $y1 ) && isset( $x2 ) && isset( $y2 ) ) 
			{
                pdf_moveto( $this->pPDF, (double)PDFTag::calcTerm( $x1 ), (double)PDFTag::calcTerm( $y1 ) );
                pdf_lineto( $this->pPDF, (double)PDFTag::calcTerm( $x2 ), (double)PDFTag::calcTerm( $y2 ) );
                pdf_stroke( $this->pPDF );
            } 
			else 
			{
                $this->addWarning( "Missing attribute(s) for element 'line'." );
				
                if ( $this->stopOnWarning == true )
                    return false;
            }
        }
		
        return true;
    }
    
    /**
     * Recalculates measures for width, height into pt
     * source measurements: cm, mm, in(ch), pt.
	 *
     * @access private
     * @param string $value
     * @return float
     */
    function measure2Pt( $value ) 
	{
        if ( ereg( MEASURE_REGEX, $value, $regs ) ) 
		{
            if ( in_array( $regs[4], array( "cm", "mm", "in", "pt" ) ) ) 
			{
                switch ( $regs[4] ) 
				{
                    case 'cm':
                        $regs[1] = $regs[1] / 2.54 * 72;
                        break;
                    
					case 'mm':
                        $regs[1] = ( $regs[1] / 10.0 ) / 2.54 * 72;
                        break;
                    
					case 'in':
                        $regs[1] = 72 * $regs[1];
                        break;
                    
					case 'pt':
                        /* nothing to do $regs[1] does already contains the right value */
                        break;
                    
					default:
                        /* unknown type, we add an error message */
                        $this->addWarning( "Wrong measurement unit '$regs[4]'." );
                        break;
                }
				
                // print "$value = $regs[1] <br>";
                return (float)$regs[1];
            } 
			else 
			{
                return (float)$regs[1];
            }
        } 
		else 
		{
            return 0;
        }
    }

    function calcTerm( $term ) 
	{
        $term = preg_replace( "/\s/", "", $term );
		
        if ( ereg( "^" . MEASURE_REGEX . "$", $term ) )
            return (float)PDFTag::measure2Pt( $term );
        
        if ( ereg( "^(" . MEASURE_REGEX . ")(" . OP_REGEX . ")(" . MEASURE_REGEX . "(.*)?)?$", $term, $reg ) ) 
		{
            switch ( $reg[6] ) 
			{
                case '+':
                    return ( PDFTag::calcTerm( $reg[1] ) + PDFTag::calcTerm( $reg[7] ) );
                    break;
					
                case '-':
                    return ( PDFTag::calcTerm( $reg[1] ) - PDFTag::calcTerm( $reg[7] ) );
                    break;
					
                case '*':
                    return ( PDFTag::calcTerm( $reg[1] ) * PDFTag::calcTerm( $reg[7] ) );
                    break;
					
                case '/':
                    return ( PDFTag::calcTerm( $reg[1] ) / PDFTag::calcTerm( $reg[7] ) );
                    break;
					
                default:
                    /* unknown type of calculation request, we add an error message */
                    $this->addWarning( "Wrong type of operation requested '$regs[5]'." );
                    break;
            }
        }
		
        /* unknown type of calculation request, we add an error message */
        $this->error = "Unable to compute term '$term'.";
        return false;
	}
} // END OF PDFTag

?>
