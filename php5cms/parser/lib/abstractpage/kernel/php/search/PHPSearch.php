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
 * @package search
 */
 
class PHPSearch extends PEAR
{
	/**
	 * Retrieves a file list and determines whether meta tags are to be used then indexes that page.
	 *
	 * @access public
	 */
	function indexSite( $dr, $sr, $if, $nw, $meta )
	{
		$file_arr = $this->getFiles( $dr );
		$index    = @fopen( $if, "a" );

		if ( !$index )
		{
			return PEAR::raiseError( "Could not open index file." );
 		}
		else
		{
  			foreach ( $file_arr as $file )
			{
    			if ( $meta == "off" )
				{
     				set_time_limit( 90 ); 

		 			if ( !false/*connection_timeout() */)
						$line = $this->fullIndexPage( $file, $if, $dr, $sr, $nw );
					else
						return PEAR::raiseError( "Too many files. Indexer Halted." );
    			}
    			else
				{
     				$meta_tags = get_meta_tags( $file );
     
	 				if ( $meta_tags[index] == "no" )
					{
						continue;
     				}
					else
					{
    	  				$ext = substr( $file, -4 ); 
      
	  					if ( $ext == ".txt" )
							$line = $this->fullIndexPage( $file, $if, $dr, $sr, $nw );
      					else
							$line = $this->metaIndexPage( $dr, $sr, $in, $no, $meta_tags, $file );
     				}
    			}
    
				fputs( $index, $line );
   			}
  
  			fclose( $index );
 		}
		
		return true;
	}

	/**
	 * Indexes the pages by meta tags.
	 *
	 * @access public
	 */
	function metaIndexPage( $dr, $sr, $if, $nw, $mt, $file )
	{
		$web_path = str_replace( $dr, $sr , $file );
		$web_path = str_replace( "\\", "/", $web_path );
		$line     = "$web_path|";
		$title    = $mt[title];
		$line     = "$line$title|";
		$keywords = $mt[keywords];
		$keywords = explode( ",", $keywords );

		foreach ( $keywords as $word )
		{
   			$word = trim( $word );
   			$word = strtolower( $word );
   			$word = $this->removeChars( $word );
   			$line = "$line$word|";
 		}
 
 		$line = "$line\n";
 		return $line;
	}

	/**
	 * Reads the page, strips out any unwanted stuff (HTML etc...)
	 *
	 * @access public
	 */
	function fullIndexPage( $page, $index_file, $dc, $sr, $wf )
	{
		// displays URL, instead of file system path
		$web_path = str_replace( $dc, $sr , $page );  

		$web_path      = str_replace( "\\", "/", $web_path );
 		$line_complete = "$web_path|";
 		$cur_page      = file( $page );
 
 		foreach ( $cur_page as $html_line )
		{
  			$line     = strip_tags( $html_line );		// removes HTML tags
  			$line     = strtolower( $line );			// converts to lower case
  			$line     = trim( $line );					// removes some white space
  			$line     = $this->removeChars( $line );	// removes some special characters
  			$line_arr = explode( " ", $line );			// puts remaining words into an array
  
  			foreach ( $line_arr as $word )
			{
   				if ( $word == "" )
				{
					// ignores any remaining white space
					continue;
   				}
				else
				{
   					$section = "$word|";    
   					$line_complete = "$line_complete$section";
   				}
  			}
 		}
 	
		$line_complete = $this->removeWords( $line_complete, $wf );	// removes unwanted words (and, or, etc.)
	 	$line_arr      = explode( "|", $line_complete );
 		$line_arr      = array_unique( $line_arr );						// removes duplicate words 
 		$line_complete = implode( "|", $line_arr ); 
 		$line_complete = "$line_complete\n";
	 
 		return $line_complete;
	}

	/**
	 * Navigates through the directories recurrsively and retrieves a listing in an array.
	 *
	 * @access public
	 */
	function getFiles( $dirname )
	{	 
		if ( $dirname[strlen($dirname)-1] != DIRECTORY_SEPARATOR ) 
  			$dirname .= DIRECTORY_SEPARATOR; 
  
 	 	static $result_array = array(); 
  		$mode = fileperms( $dirname );
	
  		if ( ( $mode & 0x4000 ) == 0x4000 && ( $mode & 0x00004 ) == 0x00004 )
		{
   			chdir( $dirname );
   			$handle = opendir( $dirname ); 
  		}
  
  		if ( $handle )
		{
   			while ( $file = readdir( $handle ) )
			{
    			if ( $file == '.' || $file == '..' )
     				continue;
     
		 		if ( is_dir( $dirname . $file ) )
				{
    	  			$this->getFiles( $dirname . $file . DIRECTORY_SEPARATOR ); 
     			}
				else
				{
      				$ext  = substr( $file, -4 ); 
      				$ext2 = substr( $file, -5 );
      				$ext  = strtolower( $ext );
      				$ext2 = strtolower( $ext2 );
     
	 				if ( ( $ext == ".htm" ) || ( $ext == ".txt" ) || ( $ext == ".php" ) || ( $ext2 == ".html" ) )
						$result_array[] = $dirname . $file;
    			}
   			}
   
   			closedir( $handle );  
 		}

		return $result_array; 
	}

	/**
	 * Removes any unwanted characters.
	 *
	 * @access public
	 */
	function removeChars( $line )
	{
		$line = str_replace( ".",     "", $line );
 		$line = str_replace( ",",     "", $line );
 		$line = str_replace( "\"",    "", $line	);
 		$line = str_replace( "'",     "", $line );
 		$line = str_replace( "+",     "", $line );
 		$line = str_replace( "-",     "", $line );
 		$line = str_replace( "*",     "", $line );
 		$line = str_replace( "/",     "", $line );
 		$line = str_replace( "!",     "", $line );
 		$line = str_replace( "%",     "", $line );
 		$line = str_replace( ">",     "", $line );
 		$line = str_replace( "<",     "", $line );
 		$line = str_replace( "^",     "", $line );
 		$line = str_replace( "(",     "", $line );
 		$line = str_replace( ")",     "", $line );
 		$line = str_replace( "[",     "", $line );
 		$line = str_replace( "]",     "", $line );
 		$line = str_replace( "\\",    "", $line	);
 		$line = str_replace( "=",     "", $line );
 		$line = str_replace( "$",     "", $line );
 		$line = str_replace( "#",     "", $line );
 		$line = str_replace( "?",     "", $line );
 		$line = str_replace( "~",     "", $line );
 		$line = str_replace( "@",     "", $line );
 		$line = str_replace( ":",     "", $line );
 		$line = str_replace( ";",     "", $line );
 		$line = str_replace( "_",     "", $line );
 		$line = str_replace( "0",     "", $line );
 		$line = str_replace( "1",     "", $line );
 		$line = str_replace( "2",     "", $line );
 		$line = str_replace( "3",     "", $line );
 		$line = str_replace( "4",     "", $line );
 		$line = str_replace( "5",     "", $line );
 		$line = str_replace( "6",     "", $line );
 		$line = str_replace( "7",     "", $line );
 		$line = str_replace( "8",     "", $line );
 		$line = str_replace( "9",     "", $line );
 		$line = str_replace( "  ",   " ", $line	);
 		$line = str_replace( "&amp",  "", $line	);
 		$line = str_replace( "&copy", "", $line	);
 		$line = str_replace( "&nbsp", "", $line	);
 		$line = str_replace( "&nbsp", "", $line	);
 		$line = str_replace( "&quot", "", $line	);
 		$line = str_replace( "&",     "", $line );

		return $line;
	}

	/**
	 * Removes any unwanted words that are found in the file.
	 *
	 * @access public
	 */
	function removeWords( $line, $file_name )
	{
		$nonwords = @file( $file_name );

		foreach( $nonwords as $word )
		{
 			$word = trim( $word );
  			$word = "|$word|";
  			$line = str_replace( "$word", "|", $line );  
 		}

		return $line;
	}

	/**
	 * @access public
	 */
	function clearIndex( $if )
	{
		$fd = @fopen( $if, "a" );
 
 		if ( !$fd )
		{
			return PEAR::raiseError( "Could not open index file." );
 		}
		else
		{
  			ftruncate( $fd, 0 );
  			fclose( $fd );
			
			return true;
 		}
	}
	
	/**
	 * @access public
	 */
	function search( $query, $if )
	{
		$search_data = @file( $if );

		if ( !$search_data )
		{
			return PEAR::raiseError( "Could not find search database." );
 		}
 		else
		{
  			foreach ( $search_data as $search_page )
			{
   				$page_arr = explode( "|", $search_page );
   
   				foreach ( $page_arr as $keyword )
				{
    				if ( $keyword == $query )
						$result_arr[] = $page_arr[0];
   				}
  			}
 		}

		return $result_arr;
	}
} // END OF PHPSearch

?>
