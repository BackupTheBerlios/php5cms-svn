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
 * @package io
 */
 
class JargonFile extends PEAR 
{
	/**
	 * jargon file (you know where to get it eh?)
	 * @access public
	 */
	var $JARG_FILE = "jarg433.txt";
	
	/**
	 * the index file (must be chmod 777 when building)
	 * @access public
	 */
	var $JARG_IDX = "jargon.idx";
	
	/**
	 * set this to "yes" when index is rebuilt
	 * @access public
	 */
	var $lock = "no";
	
	/**
	 * show random word in search Form
	 * @access public
	 */
	var $showrand = true;
	
	/**
	 * show list if letters in search Form
	 * @access public
	 */
	var $showindex = true;
	
	/**
	 * show internal hyperlinks for {definitions}
	 * @access public
	 */
	var $showlinks = true;

	/**
	 * @access public
	 */
 	var $self = "";
	
	/**
	 * @access public
	 */
 	var $rebuild = false;
	
	/**
	 * @access public
	 */
 	var $index = false;
	
	/**
	 * @access public
	 */
 	var $jargon = false;
	
	/**
	 * @access public
	 */
 	var $footer = "";
	
	/**
	 * @access public
	 */
 	var $output = "";
	
	/**
	 * @access public
	 */
 	var $result = array();
	
	/**
	 * @access public
	 */
 	var $op = "";
	
	/**
	 * @access public
	 */
 	var $l = "";
	
	/**
	 * @access public
	 */
 	var $strict = "exact";
	
	/**
	 * @access public
	 */
 	var $definition = "";
 
 	/**
	 * @access public
	 */
  	var $word = array(
		"Keyword" => "", 			// the keyword itself
		"Def"     => "", 			// the full text definition
		"Html"    => "", 			// the full html definition
		"Links"   => array( "" ) 	// the embedded {keywords}
	);


	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function JargonFile() 
	{
    	// name of this document
    	if ( $this->self == "" )
      		$this->self = end( explode( "/", $_SERVER["PHP_SELF"] ) );

    	// clean keyword
    	if ( $_GET["op"] )
      		$this->op = ltrim( chop( stripslashes( $_GET["op"] ) ) );

    	// clean letter
    	if ( $_GET["l"] ) 
      		$this->l = substr( ltrim( chop( strtolower( $_GET["l"] ) ) ), 0, 1 );

    	// search method
    	if ( !$_GET["strict"] || $_GET["strict"] == "no" )
      		$this->strict = "no";

    	// file checking for Jargon file
    	if ( !file_exists( $this->JARG_FILE ) ) 
		{
			$this = new PEAR_Error( "File $this->JARG_FILE does not exist." );
			return;
      	}

    	// file checking for Index file
    	if ( !file_exists( $this->JARG_IDX ) && ( $this->op != "RebuildIndex" ) ) 
		{
			$this = new PEAR_Error( "Index File $this->JARG_IDX does not exist." );
			return;
      	}

    	// footer and link to search page
    	if ( $this->showlinks )
      		$this->footer = "\n\n<a href='$this->self'>Search page</a>";

    	// if $op has only one letter, display index list for that letter
    	if ( strlen( $this->op ) == 1 && !$this->isInTheIndex( $this->op ) ) 
		{
      		$this->l  = strtolower( $this->op );
      		$this->op = "List";
      
	  		return;
      	}

    	// $op = "List" are we listing keywords for a specific letter ?
    	if ( ( $this->op == "List" ) && ( $this->l != "" ) )
      		return;

    	// $op = rebuild index ?
    	if ( ( $this->op == "RebuildIndex" ) && ( $this->lock == "no" ) )
      		$this->rebuild = true;
    	else
      		$this->index = $this->getIndex();
  	}


	/**
	 * This function extracts one or several words from the array $this->jargon.
	 *
	 * @access public
	 */
	function extractWord() 
	{
    	$op = $this->op;
    	$strict = $this->strict;

    	if ( $op == "" )
      		return false;

    	$ary = $this->getJargon();

		// passing intro
    	while ( ( $bfr = next( $ary ) ) && !ereg( "The Jargon Lexicon", $bfr ) );

		while ( ( $bfr = next( $ary ) ) /*&& !ereg( "(Lexicon Entries End Here)", $bfr )*/ ) 
		{
      		if ( substr( $bfr, 0, 1 ) == ":" ) 
			{
        		$match   = no;
        		$i       = 1;
        		$keyword = "";
        		$links   = 0;

        		while ( substr( $bfr, $i, 1 ) != ":" && $i <= strlen( $bfr ) ) 
				{
          			$keyword .= substr( $bfr, $i, 1 );
          			$i++;
          		}

        		// build words array
        		$this->word[strtolower( $keyword )][Keyword] = $keyword;

        		if ( $this->rebuild && ( $keyword != "(Lexicon Entries End Here)" ) ) 
				{
          			// only terms are stored in the index when rebuilt
          			$this->index .= addslashes( $keyword ) . "\n";
          		}
				// definition matches searched keyword, activate index boolean
        		else if ( ( $strict == "exact" && strtolower( $op ) == strtolower( $keyword ) ) || ( $strict != "exact" && ( ereg( strtolower( $op ), strtolower( $keyword ) ) || ereg( $op, $keyword ) ) ) ) 
				{
          			$this->result[$rs++] = strtolower( $keyword );
          			$match = "yes"; // inside the definition itself AND match keyword
          		}
        	}
      
	  		// build words array
      		$this->word[strtolower( $keyword )][Def] .=$bfr;

			// buffer is on the searched data, start processing definition data
      		if ( $match == "yes" ) 
			{
				// insert html links
        		if ( $this->showlinks ) 
          			$bfr = str_replace( ":$keyword:", ":<a href='$this->self?op=$keyword&strict=no' title='Termes similaires'>$keyword</a>:", $bfr );
          		
        		// get buffer length (variable)
        		$txtlen = strlen( $bfr );

        		$mylogicalpointer = 0;
        		$def = "";

        		// process all data in the buffer (80 chars max)
        		while ( ($mylogicalpointer <= strlen( $bfr ) ) ) 
				{
          			// found a link to another keyword ?
          			if ( (substr( $bfr, $mylogicalpointer, 1 ) == "{" ) || $inword == "yes" ) 
					{
						// we're inside a linked keyword...
						// it might be wrapped in the text... let's check if we're
						// not already inside one that started on the previous line
            
						// start of a new keyword, set the start pointers
						if ( $inword != "yes" ) 
						{
              				$subword = "";
              
			  				// insert html links
			  				if ( $this->showlinks ) 
							{
                				// build words array
                				$this->word[strtolower( $keyword )][Html] .= "{<a href=\"$this->self?op=";
                				$def .= "{<a href=\"$this->self?op=";
                				$sim .= " <a href=\"$this->self?op=";
                			}
							// restore the bracket at the beginning of the keyword
              				else 
							{
                				$def .= "{"; // (this can be improved)
                			}
              
			  				$inword = "yes"; // set pointer (we're inside a {linked keyword} )
              				$mylogicalpointer++;
              			}

            			// get the content of the {keyword} (this can be improved)
            			while ( ( $mylogicalpointer <= strlen( $bfr ) ) && ( substr( $bfr, $mylogicalpointer, 1 ) != "}" ) ) 
						{
              				$subword .= substr( $bfr, $mylogicalpointer, 1 );
              				$mylogicalpointer++;
              			}

            			if ( $mylogicalpointer < strlen( $bfr ) ) 
						{
              				$subword = str_replace( "{", "", $subword ); // this can be improved
              				$sublink = $subword;

              				// build the links

							// insert html links
              				if ( $this->showlinks ) 
							{
								// remove double spaces
								while ( ereg( "  ", $sublink ) ) 
									$sublink = str_replace( "  ", " ", $sublink );

                				// remove <CR> and <LF>
                				$sublink = str_replace( "\r\n", "", $sublink );

                				$def .= urlencode( $sublink ); // insert keyword inside url
                				$this->word[strtolower( $keyword )][Html] .= urlencode( $sublink );
                				$sim .= urlencode( $sublink ); // insert keyword inside url

                				// complete html link for "more like this"
                				$sim .= "&strict=no";
                				$sim .= "\" title=\"Search similar term\">";
                				$sim .= "<font size=-1><u><sup>[?]</sup></u></a></font>";

								// complete html link for "exact word"
                				$def .= "&strict=exact";
                				$def .= "\" title=\"Search exact term\">";
                				$def .= $subword;
                				$def .= "</a>$sim";
                
								// build words array
								$this->word[strtolower( $keyword )][Html] .= "&strict=exact" .
									"\" title=\"Search exact term\">" .
									$subword .
									"</a>$sim";
                			}
              				else 
							{
                				// just print the word
                				$def .= $sublink;
                			}

			              	// build words array
              				$this->word[strtolower( $keyword )][Links][$links++] = $sublink;
              				$sim = "";

              				$inword = "no"; // end of the word, ready to process definition data
              			}
            		}

          			$def .= substr( $bfr, $mylogicalpointer, 1 );
          
		  			// build words array
          			$this->word[strtolower( $keyword )][Html] .= substr( $bfr, $mylogicalpointer, 1 );
          			$mylogicalpointer++;
          		}

        		$this->definition .= $def;
        	}
      	}

    	if ( $this->rebuild )
      		return $this->rebuildIndex();
    	else
      		return $this->definition;
   	}

	/**
	 * Just builds the form and the index links.
	 *
	 * @access public
	 */
  	function getForm() 
	{
    	$this->output .= "<form action='$this->self' method='get'>" .
            "<table border='1' cellspacing='1' cellpadding='5' align='center'><tr>" .
            "<td align='center'>Search: <input type='text' name='op' size=5>" .
            "<input type='submit' value='go!'> \n<br />Exact term:     " .
            "<input type='checkbox' name='strict' CHECKED value='exact'>" .
            "</td></tr><tr><td align=center>";

    	// display list of existing letters in index
    	if ( $this->showindex )
      		$this->showLettersFromIndex( $this->index );
      
    	$this->output .= "</font></td></tr></table></form>";

    	// display random term
    	if ( $this->showrand ) 
		{
      		$ary = $this->getIndex();
      		$max = count( $ary );
      		srand( (double)microtime() * 1000000 );
      		$r = rand( 0, $max );
			
      		$this->output .= "<table border='0' cellspacing='0' cellpadding='0' align='center'>" .
          		"<tr><td><pre><BR>\nThere are $max terms in the jargon\n\n";
      
	  		$this->op = $ary[$r];
      		$this->strict  = "exact";
      		$this->output .= $this->extractWord();
      		$this->output .= "</pre></td></tr></table>";
      	}
		
    	return $this->output;
    }

    /**
	 * List words for a specific letter - returns false if bad entry or if nothing is found.
	 *
	 * @access public
     */
  	function getWordsFrom( $l ) 
	{
    	$l = substr( ltrim( chop( $l ) ), 0, 1 );
    
		if ( $l == "" )
      		return false;
      
    	$ary = $this->getIndex();
    	$w   = 0;
    
		while ( $w <= count( $ary ) ) 
		{
      		if ( strtolower( $l ) == strtolower( substr( $ary[$w], 0, 1 ) ) ) 
			{
        		$this->output .= "<a href=\"$this->self?op=" .
					urlencode( $ary[$w] ) .
					"&strict=exact\">" .
					htmlentities( $ary[$w] ) .
					"</a>\n";
				
				$let++;
        	}
      
	  		$w++;
      	}
    
		if ( $let == 0 ) 
		{
      		$this->output .= "Sorry, None of the words in the file $this->JARG_IDX starts with the character '$l'";
      		return false;
      	}
    	else 
		{
      		return $this->output;
      	}
  	}

	/**
	 * Returns all first letters from words in the index file.
	 *
	 * @access public
	 */
  	function showLettersFromIndex() 
	{
    	$ary = $this->getIndex();
    	$w   = 0;
    
		while ( $w <= count( $ary ) ) 
		{
      		$tmp = strtolower( substr( $ary[$w], 0, 1 ) );
      		$link[$tmp] = "<a href=?op=List&l=" . urlencode( $tmp ) . ">$tmp</a>";
      		$w++;
      	}
    
		sort( $link );
    	
		while ( list( $v, $n ) = each( $link ) ) 
			$output .= $n;
      
    	$this->output .= $output;
    	return $this->output;
  	}

	/**
	 * Returns the link to the jargon index file.
	 *
	 * @access public
	 */
	function loadJargon() 
	{
    	$fp = @fopen( $this->JARG_IDX, "r" );
		
		if ( !$fp )
			return PEAR::raiseError( "Unable to open $this->JARG_IDX file." );

		return $fp;
  	}

	/**
	 * Checks in index file for matching string $op.
     * Returns the string or false if nothing found.
	 *
	 * @access public
	 */
  	function isInTheIndex( $op ) 
	{
    	$op = ltrim( chop( strtolower( $op ) ) );
    
		if ( !is_array( $this->index ) )
      		$ary = $this->getIndex();
    	else
      		$ary = $this->index;
      
    	while ( $bfr = next( $ary ) ) 
		{
      		$tmp = ltrim( chop( $bfr ) );
      
	  		if ( $op == strtolower( $tmp ) )
        		return $tmp;
      	}
    
		return false;
  	}

	/**
	 * @access public
	 */
  	function matchesSimilarTerms( $op ) 
	{
    	$op = ltrim( chop( strtolower( $op ) ) );
		
    	if ( !is_array( $this->index ) )
      		$ary = $this->getIndex();
    	else
      		$ary = $this->index;
      
    	while ( $bfr = next( $ary ) ) 
		{
      		$tmp = ltrim( chop( $bfr ) );
			
      		if ( ereg( $op, strtolower( $tmp ) ) )
        		$result[$i++] = $tmp;
      	}
    
		if ( !empty( $result ) )
      		return true;
      
    	return false;
  	}

    /**
	 * Reads the index file from filesystem or from $this->index (if exists) and
     * return content into an array.
	 *
	 * @access public
	 */
	function getIndex() 
	{
    	if ( !is_array( $this->index ) ) 
		{
      		$fp = $this->loadJargon();
			
			if ( PEAR::isError( $fp ) )
			{
				$this->index = array();
				return $this->index;
      		}
		
	  		while ( !@feof( $fp ) && $bfr = @fgets( $fp, 255 ) ) 
			{
        		$ary[$i] = stripslashes( ltrim( chop( $bfr ) ) );
        
				if ( $ary[$i] != "" )
          			$i++;
        	}
      
	  		$this->index = $ary;
    	}
    
		return $this->index;
  	}

	/**
	 * Just rebuilds the index file.
	 *
	 * @access public
	 */
  	function rebuildIndex() 
	{
    	$q = @fopen( $this->JARG_IDX, "w" );
	
		if ( !$q )
			return PEAR::raiseError( "Unable to create $this->JARG_IDX file." );	

		@fputs( $q, $this->index );
    	@fclose( $q );
		
    	return "Index file rebuilt on file $this->JARG_IDX";
  	}

	/**
	 * Get the content of the jargon and store it into an array of 80 chars width.
	 *
	 * @access public
	 */
  	function getJargon() 
	{
    	if ( $this->jargon == false ) 
		{
      		$fp = @fopen( $this->JARG_FILE, "r" ) || die ( "Unable to open jargon file." );
      
	  		while ( !@feof( $fp ) )
        		$index[$i++] = @fgets( $fp, 80 );
        
      		$this->jargon = $index;
      	}
    
		return $this->jargon;
  	}

	/**
	 * Returns the results or writes to stdout.
	 *
	 * @access public
	 */
  	function out( $mode ) 
	{
    	switch ( $this->op ) 
		{
      		case "List":
        		if ( $this->l != "" )
          			$this->getWordsFrom( $this->l );
          
        		break;

      		case "RebuildIndex":
        		$this->output .= $this->extractWord() . $this->footer;
        		break;

      		default:
        		if ( $this->op != "" ) 
				{
          			// $op = "some term"
          			// process this only if not rebuilding the index
          			$this->output .= "\nSearching for '$this->op' on $this->JARG_FILE...\n\n";
          			$op = $this->op;

					// empty results?
          			if ( !$this->isInTheIndex( $this->op ) ) 
					{ 
            			if ( $this->matchesSimilarTerms( $this->op ) ) 
						{
              				if ( $this->strict == "exact" ) 
							{
                				$this->output .= "\n  ... <a href='$this->self?op=$op&strict=no'>Similar terms</a>.\n\n";
                				$this->op = false;
                			}
              				else 
							{
                				$this->output .= "\n ... Search similar terms automatically ...\n\n";
                				$this->strict  = "no";
                			}
              			}
            		}

          			$this->extractWord();
          			$ar = $this->result;

					// multiple results
          			if ( count( $ar ) > 1 )
					{
            			// $this->output .= "\n\n Found multiple results... displaying \n\n";
            			while ( $a++ < count( $ar ) ) 
						{
              				$ary = $this->word[$ar[$a]];

              				if ( $this->showlinks ) 
								$out .= $ary[Html];
              				else 
								$out .= $ary[Def];
              
			  				if ( $this->relatedlinks ) 
							{
                				if ( is_array( $ary[Links] ) ) 
								{
                  					while ( list( $n, $v ) = each( $ary[Links] ) ) 
										$out .= $v . "\n<br>";
                  				}
                			}
              			}
            
						$this->output .= $out . $this->footer;
            		}
					// single result
          			else 
					{ 
            			// $this->output .= "\n Found single result... displaying \n\n";
            			$ary = $this->word[strtolower( $this->op )];

            			if ( $this->showlinks ) 
							$out .= $ary[Html];
            			else 
							$out .= $ary[Def];
            
						if ( $this->relatedlinks ) 
						{
              				if ( is_array( $ary[Links] ) ) 
							{
                				while ( list( $n, $v ) = each( $ary[Links] ) ) 
									$out .= $v . "\n<br>";
                			}
              			}
            
						$this->output .= $out . $this->footer;
            		}
          		}
        		else 
				{
          			$this->getForm();
          		}
        }

    	if ( $mode == "out" )
      		echo $this->output;
    	else
      		return $this->output;
    }
} // END OF JargonFile

?>
