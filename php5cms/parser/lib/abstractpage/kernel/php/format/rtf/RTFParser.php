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
 * Rich Text Format - Parsing Class
 *
 * General Notes:
 * Unknown or unspupported control symbols are silently ignored
 * Group stacking is still not supported.
 *
 * @link http://msdn.microsoft.com/library/default.asp?URL=/library/specs/rtfspec.htm
 * @package format_rtf
 */

class RTFParser extends PEAR
{
	/**
	 * output data stream (depends on which $wantXXXXX is set to true)
	 * @access public
	 */
	var $out;
	
	/**
	 * htmlified styles (generated after parsing if wantHTML)
	 * @access public
	 */
	var $outstyles;
	
	/**
	 * if wantHTML, stylesheet definitions are put in here
	 * @access public
	 */
	var $styles;
	
	/**
	 * rtf core stream
	 * @access private
	 */
	var $rtf;
	
	/**
	 * length in characters of the stream (get performace due avoiding calling strlen everytime)
	 * @access private
	 */
	var $len;

	/**
	 * convert to XML
	 * @access private
	 */
	var $wantXML;
	
	/**
	 * convert to HTML
	 * @access private
	 */
	var $wantHTML;

	/**
	 * holds the current (or last) control word, depending on $cw
	 * @access private
	 */
	var $cword;
	
	/**
	 * are we currently parsing a control word ?
	 * @access private
	 */
	var $cw;
	
	/**
	 * could this be the first character ? so watch out for control symbols
	 * @access private
	 */
	var $cfirst;

	/**
	 * parser flags
	 * @access private
	 */
	var $flags = array();
	
	/**
	 * every character which is no sepcial char, not belongs to a control word/symbol; is generally considered being 'plain'
	 * @access private
	 */
	var $queue;
	
	/**
	 * group stack
	 * @access private
	 */
	var $stack = array();

	/**
	 * keywords which don't follw the specification (used by Word '97 - 2000) - not yet used
	 * @access private
	 */
	var $control_exception = array(
		"clFitText",
		"clftsWidth(-?[0-9]+)?",
		"clNoWrap(-?[0-9]+)?",
		"clwWidth(-?[0-9]+)?",
		"tdfrmtxtBottom(-?[0-9]+)?",
		"tdfrmtxtLeft(-?[0-9]+)?",
		"tdfrmtxtRight(-?[0-9]+)?",
		"tdfrmtxtTop(-?[0-9]+)?",
		"trftsWidthA(-?[0-9]+)?",
		"trftsWidthB(-?[0-9]+)?",
		"trftsWidth(-?[0-9]+)?",
		"trwWithA(-?[0-9]+)?",
		"trwWithB(-?[0-9]+)?",
		"trwWith(-?[0-9]+)?",
		"spectspecifygen(-?[0-9]+)?"
	);

	/**
	 * @access private
	 */
	var $charset_table = array(
		"0"		=> "ANSI",
 		"1"		=> "Default",
		"2"		=> "Symbol",
		"77"	=> "Mac",
		"128"	=> "Shift Jis",
		"129"	=> "Hangul",
		"130"	=> "Johab",
		"134"	=> "GB2312",
		"136"	=> "Big5",
		"161"	=> "Greek",
		"162"	=> "Turkish",
		"163"	=> "Vietnamese",
		"177"	=> "Hebrew",
		"178"	=> "Arabic",
		"179"	=> "Arabic Traditional",
		"180"	=> "Arabic user",
		"181"	=> "Hebrew user",
		"186"	=> "Baltic",
		"204"	=> "Russion",
 		"222"	=> "Thai",
		"238"	=> "Eastern European",
		"255"	=> "PC 437",
		"255"	=> "OEM"
	);

	/**
	 * note: the only conversion table used
	 * @access private
	 */
	var $fontmodifier_table = array(
		"bold"			=> "b",
		"italic"		=> "i",
		"underlined"	=> "u",
		"strikethru"	=> "strike"
	);

	
	/**
	 * Constructor
	 * Takes as argument the raw RTF stream
 	 * (Note under certain circumstances the stream has to be stripslash'ed before handling over)
	 *
	 * @access public
	 */
	function RTFParser( $data )
	{
		$this->len       = strlen( $data );
		$this->rtf       = $data;
		$this->wantXML   = false;
		$this->wantHTML  = false;
		$this->out       = "";
		$this->outstyles = "";
		$this->styles    = array();
		$this->text      = "";

		if ( $this->len == 0 )
		{
			$this = new PEAR_Error( "No data in stream found." );
			return;
		}
	}

	
	/**
	 * @access public
	 */
	function parserInit()
	{
		// default values according to the specs
		$this->flags = array(
			"fontsize"       => 24,
			"beginparagraph" => true
		);
	}
        
	/**
	 * Sets the output type.
	 *
	 * @access public
	 */
	function output( $typ )
	{
		switch( $typ )
		{
			case "xml" :
				$this->wantXML = true;
				break;
				
			case "html" :
				
			default :
				$this->wantHTML = true;
				break;
		}
	}

	/**
	 * @access public
	 */
	function parseControl( $control, $parameter )
	{
		switch( $control )
		{
			// font table definition start
			case "fonttbl" :
				$this->flags["fonttbl"] = true;
				break;
				
			// define or set font
			case "f" :
				 // if its set, the fonttable definition is written to; else its read from
				if ( $this->flags["fonttbl"] )
					$this->flags["fonttbl_current_write"] = $parameter;
				else
					$this->flags["fonttbl_current_read"] = $parameter;
				
				break;
                
			case "fcharset" :
				// this is for preparing flushQueue; it then moves the Queue to $this->fonttable .. instead to formatted output
				$this->flags["fonttbl_want_fcharset"] = $parameter;
				break;
				
			case "fs" :
 				// sets the current fontsize; is used by stylesheets (which are therefore generated on the fly
				$this->flags["fontsize"] = $parameter;
				break;
				
			// handle alignment
			case "qc" :
				$this->flags["alignment"] = "center";
				break;
				
			case "qr" :
				$this->flags["alignment"] = "right";
				break;

			// reset paragraph settings ( only alignment)
			case "pard" :
				$this->flags["alignment"] = "";
				break;

			// define new paragraph (for now, thats a simple break in html)
			case "par" :
				// begin new line
				$this->flags["beginparagraph"] = true;

				if ( $this->wantHTML )
					$this->out .= "</div>";
                  
				break;

			// bold
			case "bnone":
				$parameter = "0";
				
			case "b":
				// haven'y yet figured out WHY I need a (string)-cast here ... hm
				if ( (string)$parameter == "0" )
					$this->flags["bold"] = false;
				else
					$this->flags["bold"] = true;
				
				break;

			// underlined
			case "ulnone" :
				$parameter = "0";
				
			case "ul" :
				if ( (string)$parameter == "0" )
					$this->flags["underlined"] = false;
				else
					$this->flags["underlined"] = true;

				break;
                
			// italic
			case "inone" :
				$parameter = "0";
				
			case "i" :
				if ( (string)$parameter == "0" )
  					$this->flags["italic"] = false;
				else
					$this->flags["italic"] = true;
				
				break;

			// strikethru
			case "strikenone" :
				$parameter = "0";
				
			case "strike" :
				if ( (string)$parameter == "0" )
					$this->flags["strikethru"] = false;
				else
					$this->flags["strikethru"] = true;
				
				break;

			// reset all font modifiers and set fontsize to 12
			case "plain" :
				$this->flags["bold"]           = false;
				$this->flags["italic"]         = false;
				$this->flags["underlined"]     = false;
				$this->flags["strikethru"]     = false;
				$this->flags["fontsize"]       = 12;
				$this->flags["subscription"]   = false;
				$this->flags["superscription"] = false;
				
				break;

			// sub and superscription
			case "subnone" :
				$parameter = "0";
				
			case "sub" :
				if ( (string)$parameter == "0" )
					$this->flags["subscription"] = false;
				else
					$this->flags["subscription"] = true;
				
				break;

			case "supernone" :
				$parameter = "0";
				
			case "super" :
				if ( (string)$parameter == "0" )
					$this->flags["superscription"] = false;
				else
 					$this->flags["superscription"] = true;
 				
				break;
		}
	}

	/**
	 * Dispatch the control word to the output stream.
	 *
	 * @access public
	 */
	function flushControl()
	{
		if ( ereg( "^([A-Za-z]+)(-?[0-9]*) ?$", $this->cword, $match ) )
		{
			$this->parseControl( $match[1], $match[2] );

			if ( $this->wantXML )
			{
				$this->out .= "<control word=\"".$match[1]."\"";

				if ( strlen( $match[2] ) > 0 )
					$this->out .= " param=\"".$match[2]."\"";
                   
				$this->out .= "/>";
			}
		}
	}

	/**
	 * If output stream supports comments, dispatch it.
	 *
	 * @access public
	 */
	function flushComment( $comment)
	{
		if ( $this->wantXML || $this->wantHTML )
			$this->out.="<!-- " . $comment . " -->";
	}

	/**
	 * Dispatch start/end of logical rtf groups (not every output type needs it; merely debugging purpose).
	 *
	 * @access public
	 */
	function flushGroup( $state )
	{
		if ( $state == "open" )
		{
			// push onto the stack
			array_push( $this->stack, $this->flags );
            
			if( $this->wantXML )
				$this->out .= "<group>";
		}

		if ( $state == "close" )
		{
			// pop from the stack
 			$this->last_flags = $this->flags;
			$this->flags = array_pop( $this->stack );

			$this->flags["fonttbl_current_write"] = "";	// on group close, no more fontdefinition will be written to this id
                                                     	// this is not really the right way to do it !
                                                     	// of course a '}' not necessarily donates a fonttable end; a fonttable
                                                    	// group at least *can* contain sub-groups
                                                     	// therefore an stacked approach is heavily needed

			$this->flags["fonttbl"] = false; // no matter what you do, if a group closes, its fonttbl definition is closed too

			if ( $this->wantXML )
				$this->out.="</group>";
		}
	}

	/**
	 * @access public
	 */
	function flushHead()
	{
		if ( $this->wantXML )
			$this->out .= "<rtf>";
	}

	/**
	 * @access public
	 */
	function flushBottom()
	{
		if ( $this->wantXML )
			$this->out .= "</rtf>";
	}

	/**
	 * @access public
	 */
	function checkHtmlSpanContent( $command )
	{
		reset( $this->fontmodifier_table );
		
		while( list( $rtf, $html ) = each( $this->fontmodifier_table ) )
		{
			if ( $this->flags[$rtf] == true )
			{
				if ( $command == "start" )
					$this->out .= "<".$html.">";
				else
					$this->out .= "</".$html.">";
			}
		}
	}

	/**
	 * Flush text in queue.
	 *
	 * @access public
	 */
	function flushQueue()
	{
		if ( strlen( $this->queue ) )
		{
			// processing logic
			if ( ereg( "^[0-9]+$", $this->flags["fonttbl_want_fcharset"] ) )
			{
				$this->fonttable[$this->flags["fonttbl_want_fcharset"]]["charset"] = $this->queue;
				$this->flags["fonttbl_want_fcharset"] = "";
				$this->queue = "";
			}
                
			// output logic
			if ( strlen( $this->queue ) )
			{
				// Everything which passes this is (or, at leat, *should*) be only outputted as plaintext.
  				// Thats why we can safely add the css-stylesheet when using wantHTML.
				if ( $this->wantXML )
					$this->out .= "<plain>" . $this->queue . "</plain>";

				if ( $this->wantHTML )
				{
					// only output html if a valid (for now, just numeric;) fonttable is given
					if( ereg( "^[0-9]+$", $this->flags["fonttbl_current_read"] ) )
					{
						if ( $this->flags["beginparagraph"] == true )
						{
							$this->flags["beginparagraph"] = false;
							$this->out .= "<div align=\"";
							
							switch( $this->flags["alignment"] )
							{
								case "right" :
									$this->out .= "right";
									break;
									
								case "center" :
									$this->out .= "center";
									break;

								case "left" :

								default :
									$this->out .= "left";
							}
							
							$this->out .= "\">";
						}
                            
						// define new style for that span
						$this->styles["f".$this->flags["fonttbl_current_read"]."s".$this->flags["fontsize"]] = "font-family:".$this->fonttable[$this->flags["fonttbl_current_read"]]["charset"]." font-size:".$this->flags["fontsize"].";";
 
 						// write span start
						$this->out .= "<span class=\"f".$this->flags["fonttbl_current_read"]."s".$this->flags["fontsize"]."\">";

						// check if the span content has a modifier
						$this->checkHtmlSpanContent( "start" );

						// write span content
						$this->out .= $this->queue;

						// close modifiers
						$this->checkHtmlSpanContent( "stop" );

						// close span (??)
                        $this->out .= "</span>";
					}
				}
				
				$this->queue = "";
			}
		}
	}

	/**
	 * Handle special charactes like \'ef.
	 *
	 * @access public
	 */
	function flushSpecial( $special )
	{
		if ( strlen( $special ) == 2 )
		{
			if ( $this->wantXML )
 				$this->out .= "<special value=\"".$special."\"/>";
		}
	}

	/**
	 * @access public
	 */
	function makeStyles()
	{
		$this->outstyles = "<style type=\"text/css\"><!--\n";
		reset( $this->styles );
		
		while( list( $stylename, $styleattrib ) = each( $this->styles ) )
			$this->outstyles .= "." . $stylename . " { " . $styleattrib . " }\n";
        
		$this->outstyles .= "--></style>\n";
	}

	/**
	 * This parse simply starts at the beginning of the rtf core stream, catches every
	 * controlling character {,} and \, automatically builds control words and control
	 * symbols during his livetime, trashes every other character into the plain text queue.
	 *
	 * @access public
	 */
	function parse()
	{
		$this->parserInit();

		$i = 0;
		
		$this->cw     = false;	// flag if control word is currently parsed
		$this->cfirst = false;	// first control character ?
		$this->cword  = "";		// last or current control word ( depends on $this->cw
		$this->queue  = "";		// plain text data found during parsing
		
		$this->flushHead();

		while( $i < $this->len )
		{
			switch( $this->rtf[$i] )
			{
				case "{" :
					if ( $this->cw )
					{
						$this->flushControl();
						$this->cw = false;
						$this->cfirst = false;
					}
					else
					{
						$this->flushQueue();
					}

					$this->flushGroup( "open" );
					break;
					
				case "}" :
					if ( $this->cw )
					{
						$this->flushControl();
						$this->cw = false;
						$this->cfirst = false;
					}
					else
					{
						$this->flushQueue();
					}

					$this->flushGroup( "close" );
					break;
					
				case "\\" :
					if ( $this->cfirst )
					{
						$this->queue .= '\\';
						$this->cfirst = false;
						$this->cw = false;
						
						break;
					}
					
					if ( $this->cw )
						$this->flushControl();
					else
						$this->flushQueue();
					
					$this->cw = true;
					$this->cfirst = true;
					$this->cword  = "";
					
					break;
					
				default :    
					if ( ( ord( $this->rtf[$i] ) == 10 ) || ( ord( $this->rtf[$i] ) == 13 ) )
						break; // eat line breaks
					
					// active control word ?
					if( $this->cw )
					{
						// Watch the RE: there's an optional space at the end which IS part of
						// the control word (but actually its ignored by flushControl).
						
						// continue parsing
						if ( ereg( "^[a-zA-Z0-9-]?$", $this->rtf[$i] ) )
						{
							$this->cword .= $this->rtf[$i];
							$this->cfirst = false;
						}
						else
						{
							// Control word could be a 'control symbol', like \~ or \* etc.
							$specialmatch = false;
							
							if ( $this->cfirst )
							{
								// expect to get some special chars
								if ( $this->rtf[$i] == '\'' ) //'
								{
									$this->flushQueue();
									$this->flushSpecial( $this->rtf[$i+1].$this->rtf[$i+2] );
									$i += 2;
									$specialmatch = true;
									$this->cw = false;
									$this->cfirst = false;
									$this->cword = "";
								}
								else if ( ereg( "^[{}\*]$", $this->rtf[$i] ) )
								{
									$this->flushComment( "control symbols not yet handled" );
									$specialmatch = true;
								}
									
								$this->cfirst = false;
 							}
							else
							{
								// space delimtes control words, so just discard it and flush the controlword
								if ( $this->rtf[$i] == ' ' )
								{
									$this->cw = false;
									$this->flushControl();
									
									break;
								}
							}
							
							if ( !$specialmatch )
							{
								$this->flushControl();
								$this->cw = false;
								$this->cfirst = false;
    							
								// The current character is a delimeter, but is NOT
								// part of the control word so we hop one step back
								// in the stream and process it again.
								
								$i--;
							}
						}
					}
					else
					{
						// < and > need translation before putting into queue when XML or HTML is wanted
  						if ( ( $this->wantHTML ) || ( $this->wantXML ) )
						{
							switch ( $this->rtf[$i] )
							{
								case "<" :
									$this->queue .= "&lt;";
									break;
									
								case ">" :
									$this->queue .= "&gt;";
									break;
									
								default :
									$this->queue .= $this->rtf[$i];
									break;
							}
						}
						else
						{
							$this->queue .= $this->rtf[$i];
						}
					}
			}
				
			$i++;
		}
			
		$this->flushQueue();
		$this->flushBottom();

		if ( $this->wantHTML )
			$this->makeStyles();
	}
} // END OF RTFParser
	
?>
