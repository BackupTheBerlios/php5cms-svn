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
 
class RTF2HTML extends PEAR
{
	/**
	 * @access public
	 */  
    var $htmlOut;
	
	/**
	 * @access private
	 */  
	var $_current = -1;
	
	/**
	 * @access private
	 */  
    var $_fileContent = "";
	
	/**
	 * @access private
	 */  
    var $_stack = array();
	
	/**
	 * @access private
	 */  
    var $_align = array();

	/**
	 * @access private
	 */ 
    var $_group = 0;
	
	/**
	 * @access private
	 */  
    var $_skipGroup = 0;
	
	/**
	 * @access private
	 */  
    var $_firstControl = 0;
    
	/**
	 * @access private
	 */  
    var $_dflFont = true;
	
	/**
	 * @access private
	 */  
    var $_dflSize = true;
	
	/**
	 * @access private
	 */  
    var $_dflColor = true;

	/**
	 * @access private
	 */  
    var $_dflFontIndex = 0;

	/**
	 * @access private
	 */  
    var $_rtfCons = array();
	
	/**
	 * @access private
	 */  
    var $_fontTable = array();
	
	/**
	 * @access private
	 */  
    var $_colorTable = array();
    
	/**
	 * @access private
	 */  
    var $_previousFont = array();
	
	/**
	 * @access private
	 */  
    var $_previousSize = array();
	
	/**
	 * @access private
	 */  
    var $_previousColor = array();

	/**
	 * @access private
	 */  
    var $_applyNames = true;
	
	/**
	 * @access private
	 */  
    var $_applySize = true;
	
	/**
	 * @access private
	 */  
    var $_applyColor = true;

	/**
	 * @access private
	 */  
    var $_bulletPara = false;
    

	/**
	 * Constructor
	 *
	 * @access public
	 */  
    function RTF2HTML( $applyFontNames = true, $applyFontSize = true, $applyFontColor = true )
	{
      	$this->_applyNames = $applyFontNames;
      	$this->_applySize  = $applyFontSize;
      	$this->_applyColor = $applyFontColor;
 	}

	
	/**
	 * @access public
	 */
	function getHTML( $fileName = "" )
	{
		$this->htmlOut = "";
		
      	$fp = fopen( $fileName, "rb" );
      
	  	if ( !$fp )
       		return PEAR::raiseError( "Cannot open file $fileName." );
			
		$this->_fileContent = fread( $fp, filesize( $fileName ) );
      	fclose( $fp );
      
		$this->_initControlTable();
		$this->_collectInfo();

		$this->_current = -1;

      	if ( substr( $this->_fileContent, 0, 5 ) == "{\\rtf" ) // is it okay?
		{
      		$this->_parseRTF();
      		$this->_correctLists();
      	}
		else
		{
		   $this->htmlOut = eregi_replace( '<\?[^>]+>', '', $this->_fileContent );
      	}
		
		return $this->htmlOut;
	}

	
	// private methods
	
	/**
	 * @access private
	 */
	function _correctLists()
	{
		$this->htmlOut = eregi_replace( "</li><br>(\r?\n)<li>", '</li>\1<li>', $this->htmlOut );
		$this->htmlOut = eregi_replace( "<br>(\r?\n)<li>", '<ul>\1<li>', $this->htmlOut );
		$this->htmlOut = str_replace( '</li><br>', '</li></ul>', $this->htmlOut );
	}
 
 	/**
	 * @access private
	 */
    function _initControlTable()
	{ 
     	$this->_rtfCons[0]   = array( "b",         "property",    "<b>","</b>" );
     	$this->_rtfCons[1]   = array( "ul",        "property",    "<u>","</u>" );
     	$this->_rtfCons[2]   = array( "i",         "property",    "<i>","</i>" );
     	$this->_rtfCons[3]   = array( "fs",        "property",    "<font style=\"{font-size=%spt}\">","</font>" );
     	$this->_rtfCons[4]   = array( "tab",       "property",    "	","" );
     	$this->_rtfCons[6]   = array( "cols",      "property",    "","" );
     	$this->_rtfCons[7]   = array( "sbknone",   "property",    "","" );
     	$this->_rtfCons[8]   = array( "sbkcol",    "property",    "","" );
     	$this->_rtfCons[9]   = array( "sbkeven",   "property",    "","" );
     	$this->_rtfCons[10]  = array( "sbkodd",    "property",    "","" );
     	$this->_rtfCons[11]  = array( "sbkpage",   "property",    "","" );
     	$this->_rtfCons[12]  = array( "pgnx",      "property",    "","" );
     	$this->_rtfCons[13]  = array( "pgny",      "property",    "","" );
     	$this->_rtfCons[14]  = array( "pgndec",    "property",    "","" );
     	$this->_rtfCons[15]  = array( "pgnucrm",   "property",    "","" );
     	$this->_rtfCons[16]  = array( "pgnlcrm",   "property",    "","" );
     	$this->_rtfCons[17]  = array( "pgnucltr",  "property",    "","" );
     	$this->_rtfCons[18]  = array( "pgnlcltr",  "property",    "","" );
     	$this->_rtfCons[19]  = array( "qc",        "property",    '<div align="center">',"</div>" );
     	$this->_rtfCons[20]  = array( "ql",        "property",    '<div align="left">',"</div>" );
     	$this->_rtfCons[21]  = array( "qr",        "property",    '<div align="right">',"</div>" );
     	$this->_rtfCons[22]  = array( "qj",        "property",    '<div align="justify">',"</div>" );
     	$this->_rtfCons[23]  = array( "paperw",    "property",    "","" );
     	$this->_rtfCons[24]  = array( "paperh",    "property",    "","" );
     	$this->_rtfCons[25]  = array( "margl",     "property",    "","" );
     	$this->_rtfCons[26]  = array( "margr",     "property",    "","" );
     	$this->_rtfCons[27]  = array( "margt",     "property",    "","" );
     	$this->_rtfCons[28]  = array( "margb",     "property",    "","" );
     	$this->_rtfCons[29]  = array( "pgnstart",  "property",    "","" );
     	$this->_rtfCons[30]  = array( "facingp",   "property",    "","" );
     	$this->_rtfCons[41]  = array( "landscape", "property",    "","" );
     	$this->_rtfCons[42]  = array( "par",       "property",    "<br>\n" );
     	$this->_rtfCons[43]  = array( "\0x0a",     "spec_char",   "\n" );
     	$this->_rtfCons[44]  = array( "\0x0d",     "spec_char",   "\r" );
     	$this->_rtfCons[45]  = array( "tab",       "spec_char",   "\t" );
     	$this->_rtfCons[46]  = array( "ldblquote", "spec_char",   '"' );
     	$this->_rtfCons[47]  = array( "rdblquote", "spec_char",   '"' );
     	$this->_rtfCons[48]  = array( "bin",       "special",     "","" );
     	$this->_rtfCons[49]  = array( "*",         "special",     "","" );
     	$this->_rtfCons[50]  = array( "'",         "special",     "","" );
     	$this->_rtfCons[51]  = array( "author",    "jump",        "","" );
     	$this->_rtfCons[52]  = array( "buptim",    "jump",        "","" );
     	$this->_rtfCons[53]  = array( "colortbl",  "jump",        "","" );
     	$this->_rtfCons[54]  = array( "comment",   "jump",        "","" );
     	$this->_rtfCons[55]  = array( "creatim",   "jump",        "","" );
     	$this->_rtfCons[56]  = array( "doccomm",   "jump",        "","" );
     	$this->_rtfCons[57]  = array( "fonttbl",   "jump",        "","" );
     	$this->_rtfCons[58]  = array( "footer",    "jump",        "","" );
     	$this->_rtfCons[59]  = array( "footerf",   "jump",        "","" );
     	$this->_rtfCons[60]  = array( "footerl",   "jump",        "","" );
     	$this->_rtfCons[61]  = array( "footerr",   "jump",        "","" );
     	$this->_rtfCons[62]  = array( "footnote",  "jump",        "","" );
     	$this->_rtfCons[63]  = array( "ftncn",     "jump",        "","" );
     	$this->_rtfCons[64]  = array( "ftnsep",    "jump",        "","" );
     	$this->_rtfCons[65]  = array( "ftnsepc",   "jump",        "","" );
     	$this->_rtfCons[66]  = array( "header",    "jump",        "","" );
     	$this->_rtfCons[67]  = array( "headerf",   "jump",        "","" );
     	$this->_rtfCons[68]  = array( "headerl",   "jump",        "","" );
     	$this->_rtfCons[69]  = array( "headerr",   "jump",        "","" );
     	$this->_rtfCons[70]  = array( "info",      "jump",        "","" );
     	$this->_rtfCons[71]  = array( "keywords",  "jump",        "","" );
     	$this->_rtfCons[72]  = array( "operator",  "jump",        "","" );
     	$this->_rtfCons[73]  = array( "pict",      "jump",        "","" );
     	$this->_rtfCons[74]  = array( "printim",   "jump",        "","" );
     	$this->_rtfCons[75]  = array( "private1",  "jump",        "","" );
     	$this->_rtfCons[76]  = array( "revtim",    "jump",        "","" );
     	$this->_rtfCons[77]  = array( "rxe",       "jump",        "","" );
     	$this->_rtfCons[78]  = array( "stylesheet","jump",        "","" );
     	$this->_rtfCons[79]  = array( "subject",   "jump",        "","" );
     	$this->_rtfCons[80]  = array( "tc",        "jump",        "","" );
     	$this->_rtfCons[81]  = array( "title",     "jump",        "","" );
     	$this->_rtfCons[82]  = array( "txe",       "jump",        "","" );
     	$this->_rtfCons[83]  = array( "xe",        "jump",        "","" );
     	$this->_rtfCons[84]  = array( "{",         "spec_char",   '{' ); 
     	$this->_rtfCons[85]  = array( "}",         "spec_char",   '}' ); 
     	$this->_rtfCons[86]  = array( "\\",        "spec_char",   '\\' );
     	$this->_rtfCons[87]  = array( "f",         "property",    "<font face=\"%s\">","</font>" );
     	$this->_rtfCons[88]  = array( "pntext",    "property",    "<li>","</li>" );
     	$this->_rtfCons[89]  = array( "line",      "property",    "<br>","" );
     	$this->_rtfCons[90]  = array( "pict",      "jump",        "","" );
     	$this->_rtfCons[91]  = array( "ulnone",    "property",    "</u>","" );
     	$this->_rtfCons[92]  = array( "pntxta",    "jump",        "","" );
     	$this->_rtfCons[93]  = array( "pntxtb",    "jump",        "","" );
     	$this->_rtfCons[94]  = array( "cf",        "property",    "<font color=\"%s\">","</font>" );
     	$this->_rtfCons[95]  = array( "pard",      "property",    "","" );
     	$this->_rtfCons[96]  = array( "<",		   "spec_char",   '&lt;' );
     	$this->_rtfCons[97]  = array( ">",         "spec_char",   '&gt;' );
     	$this->_rtfCons[98]  = array( "'c",        "special",     "Ä","" );
     	$this->_rtfCons[99]  = array( "'d",        "special",     "","" );
     	$this->_rtfCons[100] = array( "'e",        "special",     "","" );
    	$this->_rtfCons[101] = array( "'f",        "special",     "","" );
     	$this->_rtfCons[104] = array( "u",         "special",     "","" );
     	$this->_rtfCons[105] = array( "\r",        "spec_char",   "<br>\n","" );
     	$this->_rtfCons[106] = array( "\n",        "spec_char",   "<br>\n","" );
	 	$this->_rtfCons[107] = array( "panose",    "special",     "","" );
    }

	/**
	 * @access private
	 */
    function _collectInfo()
	{  
     	$ch;
     	$infocon;
		
     	$kind      = 0; // 1-font table; 2-color table;
     	$fonttbl   = "";
     	$colortbl  = "";
     	$tableproc = false; 
     	$g         = 1;
     	$font_fin  = false;
     	$color_fin = false;
     
     	$ch = $this->_getNextChar();   
     	
		while ( $ch != EOF )
		{   
       		switch ( $ch )
           	{
           		case '{':
                 	if ( substr( $infocon, 0, 7 ) == "fonttbl" )
					{ 
						$kind      = 1; 
						$g         = 1; 
						$tableproc = true;
						$infocon   = "";
					}
					
                    if ( substr( $infocon, 0, 8 ) == "colortbl" )
					{ 
						$kind      = 2; 
						$g         = 1; 
						$tableproc = true;
						$infocon   = "";
					}
					 
                    if ( $tableproc ) 
						$g++; 
           			
					break;
           		
				case '}':               
                   	if ( $tableproc ) 
					{                 
                     	$g--; 
                     	
						if ( $g == 0 )
						{
                      		$g = 1;                  
                      		$tableproc = false;
							
							if ( $kind == 1 ) 
							{
								$fonttbl  = $infocon;
								$kind     = 0;
								$infocon  = "";
								$font_fin = true;
							}
							
							if ( $kind == 2 ) 
							{
								$colortbl  = $infocon;
								$kind      = 0;
								$infocon   = "";
								$color_fin = true;
							}                  
						} 
					}  
               
					break;
       
	   			case '\\':
               		if ( substr( $infocon, 0, 7 ) == "fonttbl" )
					{ 
						$kind      = 1; 
						$g         = 1; 
						$tableproc = true;
						$infocon   = "";
					}
					
               		if ( substr( $infocon, 0, 8 ) == "colortbl" )
					{ 
						$kind      = 2; 
						$g         = 1; 
						$tableproc = true;
						$infocon   = "";
					}
					               
               		if ( $kind == 0 ) 
						$infocon = ""; 
					else if ( $g < 3 ) 
						$infocon .= "\\"; 
					else 
						$infocon .= " ";
               
       				break;
				
				case "\r":
				
				case "\n":
					break;
				
				default:          
					if ( $g < 3 ) 
						$infocon .= $ch;
       				
					break;
   			} 
   			
			$ch = $this->_getNextChar();  
       		
			if ( $font_fin && $color_fin ) 
				break;
   		}
     
   		$this->_parseFontTable( $fonttbl );
   		$this->_parseColorTable( $colortbl );
   	}
 
 	/**
	 * @access private
	 */
   	function _hexColor( $red, $green, $blue )
	{
     	$hex = "0123456789abcdef";
     	$h1  = substr( $hex, floor( $red / 16 ), 1 );     
     	$h2  = substr( $hex, $red % 16, 1 );     
     	$h3  = substr( $hex, floor( $green / 16 ), 1 );    
     	$h4  = substr( $hex, $green % 16, 1 );
     	$h5  = substr( $hex, floor( $blue / 16 ), 1 );
     	$h6  = substr( $hex, $blue % 16, 1 );    
     	
		return "#" . $h1 . $h2 . $h3 . $h4 . $h5 . $h6;
   }

   	/**
	 * @access private
	 */
  	function _parseFontTable( $fonttbl )
	{
    	$a = array();
    	$b = array();
	 	$ident = 0;

    	$a = explode( ";", $fonttbl );
    	foreach ( $a as $k => $v )
		{
      		$b    = explode( " ", $v );       
			$name = "";
      		$m    = array();
			
			if ( preg_match( "/f([0-9]+)/", $b[0], $m ) )        			
				$ident = $m[1];
		
			if ( $k == 0 ) 
				$this->_dflFontIndex = $ident;
      
	  		for ( $i = 1; $i < count( $b ); $i++ ) 
				$name .= $b[$i] . " ";
      		
			$name = trim( $name );
      		
			if ( $name != "" ) 
				$this->_fontTable[$ident] = $name;
			
			$ident++;
    	}
  	}

  	/**
	 * @access private
	 */
  	function _parseColorTable( $colortbl )
	{	
    	$a = array();
    	$b = array();
	 	
		array_push( $this->_colorTable, $this->_hexColor( 0, 0, 0 ) );
    	
		$a = explode( ";", $colortbl );
    	foreach ( $a as $k => $v )
		{       		
			if ( $v != "" )
			{
				$m = array();
				
				if ( preg_match( "/red([0-9]+)/", $v, $m ) ) 
					$red = $m[1];		
				
				$m = array();
				
				if ( preg_match( "/green([0-9]+)/", $v, $m ) ) 
					$green = $m[1];	
				
				$m = array();
				
				if ( preg_match( "/blue([0-9]+)/", $v, $m ) ) 
					$blue = $m[1];		
				
				array_push( $this->_colorTable, $this->_hexColor( $red, $green, $blue ) );
			}
	 	}
 	} 

	/**
	 * @access private
	 */  
  	function _parseRTF()
  	{
    	$ch;
    	$cNibble = 2;
    	$b = 0;
        
    	$ch = $this->_getNextChar();   
    	while ( $ch != EOF )
     	{        
        	switch ( $ch )
            {
            	case '{': 
					$this->_pushGroup();
            		break;
            	
				case '}': 
					$this->_popGroup();
            		break;
            	
				case '\\': 
					if ( ( $this->_skipGroup > $this->_group ) || ( $this->_skipGroup == 0 ) ) 
						$this->_parseControl();                    
            		
					break;
            	
				case "\r":
            	
				case "\n":
            		break;
            	
				default:
					if ( ( $this->_skipGroup > $this->_group ) || ( $this->_skipGroup == 0 ) ) 
						$this->_appendChars( $ch );
            		
					break;
        	} 
			
			$ch = $this->_getNextChar();  
     	}
     
     	if ( $this->_group < 0 )
			return false;
     
     	return true;
  	}

  	/**
	 * @access private
	 */
  	function _pushGroup()
	{
    	if ( $this->_firstControl == 0 ) 
			$this->_firstControl = 1;
    	
		$this->_group++;
    
	 	$this->_stack[$this->_group]         = array();
    	$this->_previousFont[$this->_group]  = array();
    	$this->_previousSize[$this->_group]  = array();
    	$this->_previousColor[$this->_group] = array();
  	}

	/**
	 * @access private
	 */  
  	function _popGroup()
	{
    	while ( count( $this->_stack[$this->_group] ) != 0 )
		{
     		$key = $this->_popStack();
     		
			if ( $key == -1 ) 
				break;
     		
			if ( $this->_rtfCons[$key][3] != "" ) 
				$this->_appendChars( $this->_rtfCons[$key][3] );
     		
			if ( $key == 87 )
				$this->_previousFont[$this->_group]["index"] = $this->_dflFontIndex;
     
	 		if ( $key == 3 )
				$this->_previousSize[$this->_group]["index"] = 0;
     		
			if ( $key == 94 )
				$this->_previousColor[$this->_group]["index"] = 0;
    	}   
    	
		if ( $this->_group == $this->_skipGroup )
			$this->_skipGroup = 0;
    	
		$this->_group--;
  	}
 
 	/**
	 * @access private
	 */
  	function _getCurrentChar()
	{
    	return $this->_fileContent[$this->_current];
  	}

	/**
	 * @access private
	 */  
  	function _getPreviousChar()
	{
    	if ( $this->_current > 0 )
		{
     		$this->_current--;     
     		return $this->_getCurrentChar();
    	}
		else
		{
			return false;
		}
  	}

	/**
	 * @access private
	 */    
  	function _getNextChar()
	{
    	if ( $this->_current < strlen( $this->_fileContent ) )
    	{
      		$this->_current++;
      		return $this->_getCurrentChar();
    	}
    	else
		{
      		return EOF;
    	}
  	}

	/**
	 * @access private
	 */ 
  	function _parseControl()
	{
    	$ch      = "";
    	$neg     = false;
    	$control = "";
    	$para    = "";
    	$ch      = $this->_getNextChar();
    
    	if ( $ch == "\r" || $ch == "\n" )
    		return $this->_analyzeControl( $ch, $para );

    	if ( ( !$this->_isLetter( $ch ) ) && ( $ch != "'" ) )
    	{
        	$control = $ch;
        	return $this->_analyzeControl( $control, $para );
    	}

    	if ( $ch == "'" ) 
			$sfch = true;
    	else 
			$sfch = false;

    	while ( ( $this->_isLetter( $ch ) ) || ( $ch == "'" ) || ( $ch == "B" ) ) 
		{
        	$control .= $ch;
        	$ch = $this->_getNextChar();
			
			if ( $sfch )
			{ 
				$control .= $ch; 
				$ch = $this->_getNextChar(); 
				
				break;
			}
    	}

    	$neg = false;

    	if ( $ch == '-' ) 
		{
        	$neg = true;
        	$ch  = $this->_getNextChar();
    	}

    	if ( $this->_isNumber( $ch ) )
    	{
        	while ( $this->_isNumber( $ch ) ) 
			{
         		$para .= $ch;
         		$ch = $this->_getNextChar();
        	}
    	}
    	else if ( $this->_isLetter( $ch ) )
		{
			$para = $ch;
			$ch   = $this->_getNextChar();
    	}

    	if ( $ch == "?" ) 
			$para .= $ch; // exceptions for the unicode chars which are coded like "\uN?"
    
		if ( $neg ) 
			$para = "-" . $para;
    	
		if ( $ch != ' ' && $ch != "?" ) 
			$ch = $this->_getPreviousChar();
    	
		if ( $sfch && $ch == ' ' )
		 	$ch = $this->_getPreviousChar();
	 
	 	if ( substr( $control, 0, 1 ) == "'" )
		{				 
		 	$para = substr( $control, 1 ) . $para;
		 	$control = "'";
	 	}
    
		return $this->_analyzeControl( $control, $para );
  	}

  	/**
	 * @access private
	 */
  	function _isLetter( $ch )
	{
   		if ( ( ord( $ch ) > 96 ) && ( ord( $ch ) < 123 ) ) 
			return true;
   		else 
			return false;
  	}

  	/**
	 * @access private
	 */
  	function _isNumber( $ch )
	{
   		if ( ( ord( $ch ) > 47 ) && ( ord( $ch ) < 58 ) ) 
			return true;
   		else 
			return false;
  	}

  	/**
	 * @access private
	 */
  	function _analyzeControl( $control, $para = "" )
	{
    	$last = 0;

    	foreach ( $this->_rtfCons as $key => $value )
		{
      		if ( $value[0] == $control ) 
				break;
      		
			$last++;
    	}

    	if ( $last == count( $this->_rtfCons ) )
        	return false;
		
    	switch ( $value[1] )
    	{
     		case "property": 
				$this->_parseProp( $key, $para ); 
				break;
     		
			case "spec_char": 
				$this->_appendChars( $this->_rtfCons[$key][2] );
				break;
     		
			case "jump":
     		
			case "special":
     		
			default:
				$this->_parseSpecial( $key, $para );
				break;
    	}

    	$this->_firstControl++;
    	
		if ( $this->_firstControl > 1 ) 
			$this->_firstControl = 0;
    
    	return true;
  	}
 
 	/**
	 * @access private
	 */
 	function _parseProp( $key, $para = "" ) 
	{  
	   	if ( ( $key == 19 ) || ( $key == 20 ) || ( $key == 21 ) )
       		array_push( $this->_align, $key );
      
      	if ( $key == 95 )
		{
       		$this->_appendChars( $this->_rtfCons[array_pop( $this->_align )][3] );
       		
			$ret = $this->_popStack( 0 ); 
			
			while ( $ret > -1 )
			{ 
				$this->_appendChars( $this->_rtfCons[$ret][3] );
				$ret = $this->_popStack( 0 );
			}
		 	
			$ret = $this->_popStack( 1 ); 
			
			while ( $ret > -1 )
			{ 
				$this->_appendChars( $this->_rtfCons[$ret][3] );
				$ret = $this->_popStack( 1 );
			}
		 	
			$ret = $this->_popStack( 2 ); 
			
			while ( $ret > -1 )
			{ 
				$this->_appendChars( $this->_rtfCons[$ret][3] );
				$ret = $this->_popStack( 2 );
			}
      	}
      
      	if ( $key == 42 && $this->_bulletPara )
	  	{                    
       		$this->_appendChars( $this->_rtfCons[88][3] );
       		$this->_bulletPara = false;
      	}

      	switch ( $key )
		{          
      		case 87:
         		if ( $this->_applyNames )
				{
          			if ( $this->_dflFont ) 
					{
				 		$this->_appendChars( sprintf( $this->_rtfCons[$key][2], $this->_fontTable[$para] ) );
				 		$this->_pushStack( $key );
						
						$this->_dflFont = false;
			 		}
          			else 
					{
             			$this->_appendChars( sprintf( $this->_rtfCons[$key][2], $this->_fontTable[$para] ) );
             			$this->_pushStack( $key ); 
             			
						$this->_previousFont[$this->_group]["index"] = $para;             
          			}
         		}             
      			
				break;
      		
			case 94:          
            	if ( $this->_applyColor )
				{
             		if ( $this->_dflColor )
					{ 
                     	$this->_appendChars( sprintf( $this->_rtfCons[$key][2], $this->_colorTable[$para] ) );
                     	$this->_pushStack( $key );
						
                     	$this->_dflColor = false;
                     	$this->_previousColor[$this->_group]["index"] = $para;
             		}
             		else 
					{
               			$this->_appendChars( sprintf( $this->_rtfCons[$key][2], $this->_colorTable[$para] ) );
               			$this->_pushStack( $key ); 
               			
						$this->_previousColor[$this->_group]["index"] = $para;                           
             		}
          		}
                    
      			break;  
      		
			case 4:
         		$this->_appendChars( $this->_rtfCons[$key][2] );
      			break;
      		
			case 88:
         		$this->_appendChars( $this->_rtfCons[$key][2] );
         		
				$this->_bulletPara = true;
         		$this->_skipGroup  = $this->_group;
      			
				break;        
      		
			case 91:
          		if ( $this->htmlOut != "" ) 
					$this->_appendChars( $this->_rtfCons[$key][2] );
      			
				break;     
      		
			case 42:
      		
			case 89:
          		$this->_appendChars( $this->_rtfCons[$key][2] );
      			break;  
      		
			case 3:         
         		if ( $this->_applySize )
				{
                	if ( $this->_dflSize )
					{ 
                     	$this->_appendChars( sprintf( $this->_rtfCons[$key][2], $para / 2 ) );
                        $this->_pushStack( $key );
                        
						$this->_dflSize = false;
                        $this->_previousSize[$this->_group]["index"] = $para;                           
                	}
                	else if ( $para < $this->_previousSize[$this->_group]["index"] )
					{                                    
                  		$this->_appendChars( $this->_rtfCons[$key][3] );
                  		$this->_popStack( $key );
                  		
						$this->_previousSize[$this->_group]["index"] = $para;
                	}
                	else
					{
                  		$this->_appendChars( sprintf( $this->_rtfCons[$key][2], $para / 2 ) );                                                                           
                  		$this->_pushStack( $key );
						
                  		$this->_previousSize[$this->_group]["index"] = $para;
                	}
          		}
      			
				break;           
         	
			default:
            	if ( $para == "0" )
				{
             		if ( $this->_rtfCons[$key][3] != "" )
					{     
              			$this->_appendChars( $this->_rtfCons[$key][3] );        
              			$this->_popStack( $key );
             		}
            	}  
            	else
				{               
             		if ( $this->_rtfCons[$key][2] != "" )
					{
              			$this->_appendChars( $this->_rtfCons[$key][2] );                         
              			$this->_pushStack( $key );
             		}
            	}
         		
				break;        
      	}
 	}

 	/**
	 * @access private
	 */
  	function _parseSpecial( $key, $para )
  	{
		$go = true;
		
		switch ( $key )
		{
			case 98: 
				if ( $para == "4" ) 
					$this->_appendChars( "Ä" ); // AE
			
				$go = false;
				break;
			
			case 99: 
				if ( $para == "6" ) 
					$this->_appendChars( "Ö" );
					 
				if ( $para == "c" ) 
					$this->_appendChars( "Ü" );
					 
				if ( $para == "f" ) 
					$this->_appendChars( "ß" ); // OE and UE
				
				$go = false;
      			break;
			
			case 100: 
				if ( $para == "4" ) 
					$this->_appendChars( "ä" ); // ae
				
				$go = false;
				break;
			
			case 101: 
				if ( $para == "6" ) 
					$this->_appendChars( "ö" ); 
				
				if ( $para == "c" ) 
					$this->_appendChars( "ü" ); //  oe and ue
				
				$go = false;
				break;
			
			case 104: 
              	if ( $para == "196?" || $para == "196" ) 
					$this->_appendChars( "Ä" ); // AE
                
				if ( $para == "214?" || $para == "214" ) 
					$this->_appendChars( "Ö" ); // OE
                
				if ( $para == "220?" || $para == "220" ) 
					$this->_appendChars( "Ü" ); // UE
                
				if ( $para == "228?" || $para == "228" ) 
					$this->_appendChars( "ä" ); // ae                       
                
				if ( $para == "246?" || $para == "246" )
					$this->_appendChars( "ö" ); // oe                       
                
				if ( $para == "252?" || $para == "252" ) 
					$this->_appendChars( "ü" ); // ue                                       
				
				$go = false;
				break;
			
			case 50:
				if ( $para == "b7" ) 
					$this->_appendChars( chr( hexdec( $para ) ) );
				
				if ( $para == "f6" ) 
					$this->_parseSpecial( "101", "6" );
				
				if ( $para == "fc" ) 
					$this->_parseSpecial( "101", "c" );
				
				if ( $para == "e4" ) 
					$this->_parseSpecial( "100", "4" );
				
				if ( $para == "c4" ) 
					$this->_parseSpecial( "98", "4" );
				
				if ( $para == "d6" ) 
					$this->_parseSpecial( "99", "6" );
				
				if ( $para == "dc" ) 
					$this->_parseSpecial( "99", "c" );
				
				if ( $para == "df" ) 
					$this->_parseSpecial( "99", "f" );
				
				if ( $para == "80" ) 
					$this->_appendChars( "&euro;" );
				
				$go = false;			
				break;
   		} 
    
   		if ( $go )
		{
    		if ( ( ( $this->_skipGroup > $this->_group ) || ( $this->_skipGroup == 0 ) ) && ( $this->_firstControl == 1 ) ) 
        		$this->_skipGroup = $this->_group;
		}                 
 	} 
   
   	/**
	 * @access private
	 */
  	function _appendChars( $chars ) 
	{	 
    	$this->htmlOut .= $chars;
  	}

  	/**
	 * @access private
	 */
  	function _pushStack( $key )
	{
   		$this->_stack[$this->_group][count( $this->_stack[$this->_group] )] = $key;	  
  	}
   
   	/**
	 * @access private
	 */
 	function _popStack( $key = -1 )
	{
  		$ret = -1;
  		
		if ( is_array( $this->_stack[$this->_group] ) )
		{
   			if ( $key == -1 )
			{       
       			$ret = array_pop( $this->_stack[$this->_group] );  
       			return $ret;     
    		}
   			else
			{      
      			$count = count( $this->_stack[$this->_group] ) - 1;
      			
				for ( $i = $count; $i > -1; $i-- )
				{   
       				if ( ( $key != -1 ) && ( $this->_stack[$this->_group][$i] == $key ) )
					{                     
        				$ret = $this->_stack[$this->_group][$i];
        				array_splice( $this->_stack[$this->_group], $i, 1 );
        				
						return $ret;
       				}
      			}
   			}
  		}
  		
		return $ret;
	}
} // END OF RTF2HTML

?>
