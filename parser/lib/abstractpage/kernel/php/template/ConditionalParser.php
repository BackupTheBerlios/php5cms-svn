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


using( 'util.Util' );


define( "CONDITIONALPARSER_PARSER_ON",  true  );
define( "CONDITIONALPARSER_PARSER_OFF", false );
define( "CONDITIONALPARSER_SUCCESS",    true  );
define( "CONDITIONALPARSER_FAILED",     false );
define( "CONDITIONALPARSER_EOF",        -1    );
		

/**
 * @package template
 */
 
class ConditionalParser extends PEAR
{
	/**
     * array of defined values
	 * @var			array		$defined
	 * @access		private
	 */
    var $defined = array();

	/**
     * array of templates
	 * @var			array		$templates
	 * @access		private
	 */
    var $templates = array();

	/**
     * path of template dir
     * @var			string		$path
 	 * @access		private
	 */
    var $path;

	/**
     * flag if parse is on/off
	 * @var			array		$parseflag
	 * @access		private
	 */
    var $parseflag = array();

	/**
     * flag if parser has been on/off
	 * @var			array		$flagset
	 * @access		private
	 */
	var $flagset = array();

	/**
     * current nesting level of ifs
 	 * @var			int			$level
	 * @access		private
	 */
    var $level = 0;

	/**
     * current line in template
 	 * @var			int			$line
	 * @access		private
	 */
    var $line = 0;

	/**
     * current line offset
	 * @var			int			$line_offset
	 * @access		private
	 */
    var $line_offset = 0;

	/**
     * buffer for template
	 * @var			array		$template_buff
	 * @access		private
	 */
    var $template_buff = array();

	/**
     * output buffer
	 * @var			string		$output_buff
	 * @access		private
	 */
    var $output_buff = "";

	/**
     * opening tag
	 * @var			string		$opening_tag
	 * @access		private
	 */
    var $opening_tag = "";

	/**
     * closing tag
	 * @var			string		$closing_tag
	 * @access		private
	 */
    var $closing_tag = "";

	/**
     * holder for last char fetched
	 * @var			string		$last_char
	 * @access		private
	 */
    var $last_char = "";

	/**
     * holder for last token fetched
	 * @var			string		$last_token
	 * @access		private
	 */
    var $last_token = "";

	/**
     * a character that separates tokens such as "|"
	 * @var			string		$tok_sep_char
	 * @access		private
	 */
    var $tok_sep_char = " ";

	/**
     * holds current templatename
	 * @var			string		$template_name
	 * @access		private
	 */
    var $template_name = "";
 
	/**
	 * linefeed char(s)
	 * 
	 * @var     	string		$linefeed
	 * @access  	private
	 */
	var $linefeed;
	
  
	/** 
	 * Constructor
	 *
	 * @param		string		$path
	 */
    function ConditionalParser( $path )
    {
        $this->path        = $path;
		$this->linefeed    = Util::getLinefeedForOS();
		
		$this->opening_tag = "[%";
		$this->closing_tag = "%]";
    }


	/** 
	 * Adds a template to the catalog, accepts either an array of single template name.
	 *
	 * @param		string		$name
	 * @param		string		$file
	 * @return		int			$ret
	 * @access		public
	 */
    function addtemplate( $name, $file = "" )
    {
       // handle being passed an array
       if ( is_array( $name ) )
        {
			$ret = CONDITIONALPARSER_SUCCESS;
            while ( ( list( $tempname, $filename ) = each( $name ) ) && ( $ret == CONDITIONALPARSER_SUCCESS ) )
            {
				// call wrapping function
                $ret = $this->templateadd( $tempname, $filename );
            }
        }
        else
		{
			// handle being passed a single template
            $ret = $this->templateadd( $name, $file );
		}
       
		return $ret;
	}
		  
	/** 
     * Defines a constant to be used in the parsing of the script.
	 * Can be passed an array or a single value.
	 *
	 * @param		string		$name
	 * @param		string		$contents
	 * @param		boolean		$flag
	 * @access		public
	 */
	function define( $name, $contents = "", $flag = 0 )
	{
		// handle being passed an array
        if ( is_array( $name ) )
        {
			while( list( $constname, $constvalue ) = each( $name ) )
            {
                // if final argument is 1 then append to previous constant that was defined
                if ( $flag )
                {
                    // append value to defined array
                    $this->defined[strtolower( $constname )] .= $constvalue;
                }
                else
				{
                    // write value to defined array
                    $this->defined[strtolower( $constname )] = $constvalue;
                }
            }
        }
        else
		{
			// if final argument is 1 then append to previous constant that was defined
        	if ( $flag )
            {
				// append value to defined array
                $this->defined[strtolower( $name )] .= $contents;
            }
            else
			{
                // write value to defined array
                $this->defined[strtolower($name)] = $contents;
            }
        }
    }
   
	/** 
     * Starts the parsing process of a template.
	 *
	 * @param		string		$reference
	 * @param		string		$template
	 * @param		boolean		$flag
	 * @return		boolean		$success
	 * @access		public
	 */
    function parse( $reference, $template, $flag = 0 )
    {
        // clear all vars used and reset to original values
        $this->template_buff = array();
        $this->line          = 0;
        $this->line_offset   = 0;
        $this->parseflag     = array( CONDITIONALPARSER_PARSER_ON  );
		$this->flagset       = array( CONDITIONALPARSER_PARSER_OFF );
        $this->level         = 0;
        $this->output_buff   = null;
        $this->template_name = $template;
           
        // get contents of template and assign to needed vars
        // put contents of template into template_buff
        $this->template_buff = explode( $this->linefeed, $this->templates[strtolower( $template )] );
        
        // start parser: check for special cases where template starts with an instruction
        if ( ( $this->template_buff[$this->line][$this->line_offset] == $this->opening_tag[0] )
        	 && ( $this->template_buff[$this->line][( $this->line_offset + 1 )] == $this->opening_tag[1] ) )
        {
            // if it does then move to the begining of the instruction and call handler
            $this->line_offset += 2;
            $this->handle_instruction();
        }
        else
		{
			// the template starts with text so lets send it to the correct handler
			$this->handle_text();
        }
        
		if ( $flag )
			$this->defined[strtolower($reference)] .= $this->output_buff;
		else
			$this->defined[strtolower($reference)]  = $this->output_buff;

        return CONDITIONALPARSER_SUCCESS;
    } 
    
	/** 
     * Returns the value of reference to the caller.
	 *
	 * @param		string		$reference
	 * @return		string		$output
	 * @access		public
	 */
    function output( $reference )
    {
        return $this->defined[strtolower( $reference )];
    }
    
	
	/** 
     * Internal array wrapper to add templates.
	 *
	 * @param		string		$reference
	 * @param		string		$templatefile
	 * @return		boolean
	 * @access		private
	 */
    function templateadd( $templatename, $templatefile )
    {
        // check to see if file exists
        if ( file_exists( $this->path.$templatefile ) )
        {
            if ( $fp = fopen( $this->path.$templatefile, "r" ) )
            {
                // lock the file
                flock( $fp, LOCK_SH );
				
                // read the file
                $contents = fread( $fp, filesize( $this->path . $templatefile ) );
				
                // unlock the file
                flock( $fp, LOCK_UN );
				
                // clode the file
                fclose( $fp );
				
                // add to template catalog
                $this->templates[strtolower( $templatename )] = $contents;
				
                return true;
            }
            else
			{
				return PEAR::raiseError( "Could not open file " . $this->path . $templatefile );
            }
        }
        else
		{
			return PEAR::raiseError( "Could not find file " . $this->path . $templatefile );		
        }
    }

	/** 
     * Evaluates an expression passed in three parts,
	 * the defined value is the first operand, the operator
	 * is the logical statement and the senond operand is
	 * the value for the first to be compared with.
	 *
	 * @param		string		$defined
	 * @param		string		$rval
	 * @param		string		$operator
	 * @return		boolean
	 * @access		private
	 */
    function evaluate( $defined, $rval, $operator )
    {
        $lval = $this->defined[strtolower( $defined )];

        switch( $operator )
		{
			case ">" : 
				$retval = ( ( $lval  > $rval )? true : false );
				break;
				
			case "<" : 
				$retval = ( ( $lval  < $rval )? true : false );
				break;
				
			case ">=" : 
				$retval = ( ( $lval >= $rval )? true : false );
				break;
			 
			case "<=" : 
				$retval = ( ( $lval <= $rval )? true : false );
				break;
				
			case "==" : 
				$retval = ( ( $lval == $rval )? true : false );
				break;
				
			case "!=" :
				$retval = ( ( $lval != $rval )? true : false );
				break;
				
			default:
				return PEAR::raiseError( "You are using an operator I dont understand: " . $lval );
        }

        return $retval;
    }
	
	/** 
     * Rewinds the line pointers.
	 *
	 * @access		private
	 */
    function rewind()
    {
    	if ( $this->line_offset != 0 )
        {
            $this->line_offset--;
            $this->last_char = " ";
        }
        else
		{
            $this->line--;
            $this->line_offset = strlen( $this->template_buff[$this->line] );
            $this->last_char   = " ";
        }
        
        return;
    }
 
	/** 
     * Skips all spaces/tabs until next char.
	 *
 	 * @access		private
	 */
    function skip_blanks()
    {
	    $this->get_char();
     
	    // loop until we get a non whitespace char
        while ( preg_match( "/\s/i", $this->last_char ) )
			$this->get_char();

        // rewind the pointers
		$this->rewind();
		
        return;          
    }
 
	/** 
     * Jumps past the end tags.
	 *
  	 * @access		private
	 */
	function jump_past_end_tags()
	{
        $this->get_char();
	    
		while ( 1 )
		{
	        while ( $this->last_char != $this->closing_tag[0] )
				$this->get_char();
            
			$this->get_char();
			
			if ( $this->last_char == $this->closing_tag[1] )
				break;
        }
		
		return;
	}

	/** 
     * Returns the next token in the file.
	 *
	 * @return		string		$token
  	 * @access		private
	 */
    function get_token()
    {
        // reset variables
        $this->last_token = null;
        
        // get characters
        while ( $this->get_char() )
		{
            if ( (!( ereg( "[A-Za-z0-9_!=><]", $this->last_char ) ) ) || ( ( $this->last_char == $this->closing_tag[0] ) && ( $this->template_buff[$this->line][$this->line_offset] == $this->closing_tag[1] ) ) )
            {
				if ( $this->last_char == $this->closing_tag[0] )
					$this->rewind();

                return;
            }
            else
			{
                $this->last_token .= $this->last_char;
            }
        }
    }
   
	/** 
     * Gets the next char in the buffer.
	 *
	 * @return		string		$char
  	 * @access		private
	 */
    function get_char()
    {
        // check to see if we are at the end of the file
        if ( $this->line == sizeof( $this->template_buff ) )
			return CONDITIONALPARSER_EOF;

		// so what's wrong here?
		$this->last_char = $this->template_buff[$this->line][$this->line_offset];
		
        // replace a linefeed and check if at the end of a line
        if ( $this->line_offset == strlen( $this->template_buff[$this->line] ) )
        {
		    if ( $this->parseflag[$this->level] == CONDITIONALPARSER_PARSER_ON )
				$this->output_buff .= $this->linefeed;
			
            $this->line++;
            $this->line_offset = 0;
        }
        else
		{
            $this->line_offset++;
        }
        
        return $this->last_char;
    } 
 
	/** 
     * Handles the parsing of text/html.
	 *
  	 * @access		private
	 */
    function handle_text()
    {
        // offset should not be at the first char of text
        while ( $this->get_char() != CONDITIONALPARSER_EOF )
        {
            if ( ( $this->last_char == $this->opening_tag[0] )
				 && ( $this->template_buff[$this->line][$this->line_offset] == $this->opening_tag[1] ) )
            {
                // get the final tag opening char
                $this->get_char();
				
                // pass to handler
                $this->handle_instruction();
                
                return;
            }
            else
			{
                if ( $this->parseflag[$this->level] == CONDITIONALPARSER_PARSER_ON )
					$this->output_buff .= $this->last_char;
            }
        }
    
	 	// occurs when get char returns flase
    	return;
    }

	/** 
     * Handles the processing of instructions.
	 *
  	 * @access		private
	 */
    function handle_instruction()
    {
        // offset is just after tag		
        $this->skip_blanks();
        $this->get_token();
        $token = $this->last_token;

        // find which token it is
        switch ( strtoupper( $token ) )
        {
			case "IF" :
				++$this->level;
                
				if ( ( $this->parseflag[( ( $this->level ) - 1 )] != CONDITIONALPARSER_PARSER_ON ) ) 
                {
					$this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_OFF;
					
					// ignore and other logic statements at this level of nesting
                    $this->flagset[$this->level] = CONDITIONALPARSER_PARSER_ON;
                    $this->jump_past_end_tags();
					
                    break;
                }
    
            case "ELSEIF" :
				$this->skip_blanks();			// skip blanks before next token
				$this->get_token();				// get token
				$var = $this->last_token;		// assign token
				
				$this->skip_blanks();			// skip blanks before next token
				$this->get_token();				// get token
				$operator = $this->last_token;	// assign token
				
				$this->skip_blanks();			// skip blanks before next token
				$this->get_token();				// get token
				$operand = $this->last_token;	// assign token
				
				$this->jump_past_end_tags();	// jump past end tags

				if( strtoupper( $token ) == "ELSEIF" )
				{
					if ( $this->flagset[$this->level] == CONDITIONALPARSER_PARSER_ON )
					{				
						$this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_OFF;
						break;
					}
				}
                    
				if ( $operator && $operand )
				{
					// evaluate expression
					$res = $this->evaluate( $var, $operand, $operator );
					
					if ( $res && !PEAR::isError( $res ) )
					{
						$this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_ON;
						$this->flagset[$this->level]   = CONDITIONALPARSER_PARSER_ON;
						
						break;
					}
					else
					{
						$this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_OFF;
						$this->flagset[$this->level]   = CONDITIONALPARSER_PARSER_OFF;
                        
						break;
					}
				}
				else
				{
					if ( $this->defined[strtolower( $var )] )
					{
						$this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_ON;
						$this->flagset[$this->level]   = CONDITIONALPARSER_PARSER_ON;

						break;
					}
					else
					{
						$this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_OFF;
						$this->flagset[$this->level]   = CONDITIONALPARSER_PARSER_OFF;
							
						break;
					}
				}                
                  
                break;
				
            case "ELSE" :
                if ( !( $this->flagset[$this->level] == CONDITIONALPARSER_PARSER_ON ) )
                {
                    $this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_ON;                    
                }
				else
				{
				    $this->parseflag[$this->level] = CONDITIONALPARSER_PARSER_OFF;
			    }
				
                $this->jump_past_end_tags();
                break;
            
            case "ENDIF":
                $this->level--;
                $this->jump_past_end_tags(); // jump past end tags
				break;
				
                
            default:
                if ( $this->parseflag[$this->level] == CONDITIONALPARSER_PARSER_ON )
      				$this->output_buff .= $this->defined[strtolower( $token )];
				
                $this->jump_past_end_tags();
				break;
        }
		    
        $this->handle_text();
        return;
    }
} // END OF ConditionalParser

?>
