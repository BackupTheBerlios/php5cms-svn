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
 * @package search_dig
 */
 
class HTDig extends PEAR
{
	/**
	 * @access public
	 */
	var $htdig_path;
	
	/**
	 * @access public
	 */
	var $configuration;
	
	/**
	 * @access public
	 */
	var $database_directory;
	
	/**
	 * @access public
	 */
	var $version = "";

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function HTDig()
	{
		$this->htdig_path         = '/usr/local/htdig/bin/';
		$this->configuration      = '/usr/local/htdig/conf/htdig.conf';
		$this->database_directory = '/usr/local/htdig/db/';
	}
	

	/**
	 * @access public
	 */	
	function GenerateConfiguration( $options )
 	{
  		$options["database_dir"] = $this->database_directory;
  		closedir( $directory );

  		if ( !isset( $options["start_url"] ) || $options["start_url"] == "" )
   			return PEAR::raiseError( "It was not specified a valid start url." );
		
  		$defaults = array(
   			"bad_extensions"		=> ".wav .gz .z .sit .au .zip .tar .hqx .exe .com .gif .jpg .jpeg .aiff .class .map .ram .tgz .bin .rpm .mpg .mov .avi",
   			"max_head_length"		=> "10000",
   			"max_doc_size"			=> "200000",
   			"no_excerpt_show_top"	=> "true",
   			"valid_punctuation"		=> ": .-_/!#$%^&*«»"
  		);

  		for ( $option = 0, reset( $defaults ); $option < count( $defaults ); next( $defaults ), $option++ )
  		{
   			$option_name = key( $defaults );
   
   			if ( !isset( $options[$option_name] ) )
    			$options[$option_name] = $defaults[$option_name];
  		}

  		if ( isset( $options["template_path"] ) )
  		{
   			$template_path = $options["template_path"];
   
   			if ( !( $directory = @opendir( $template_path ) ) )
    			return PEAR::raiseError( "It was not specified an existing template path directory." );
   
   			closedir( $directory );
   			unset( $options["template_path"] );
   			$template_path .= DIRECTORY_SEPARATOR;
  		}
  		else
		{
			$template_path = "";
		}

  		if ( !file_exists( $template_path . "htdig_template.htm" ) )
   			return PEAR::raiseError( "Missing required HTDig file in template directory." );
  
  		$options["template_map"] = "htdig htdig " . $template_path . "htdig_template.htm";

  		if ( !file_exists( $template_path . "htdig_header.htm" ) )
   			return PEAR::raiseError( "Missing required HTDig file in template directory." );
  
  		$options["search_results_header"] = $template_path . "htdig_header.htm";
		$options["search_results_footer"] = "";

  		if ( !file_exists( $template_path . "htdig_nomatch.htm" ) )
   			return PEAR::raiseError( "Missing required HTDig file in template directory." );
  
  		$options["nothing_found_file"] = $template_path . "htdig_nomatch.htm";

  		if ( !file_exists( $template_path . "htdig_syntaxerror.htm" ) )
   			return PEAR::raiseError( "Missing required HTDig file in template directory." );
  
  		$options["syntax_error_file"] = $template_path . "htdig_syntaxerror.htm";

  		for ( $configuration = "", $option = 0, reset( $options ); $option < count( $options ); next( $options ), $option++ )
   			$configuration .= key( $options ) . ": " . $options[key( $options )] . "\n";
  
  		if ( !( $file = fopen( $this->configuration, "w" ) ) )
   			return PEAR::raiseError( "Could not open configuration file for writing." );
  
  		if ( strcmp( $configuration, "" ) && ( !fwrite( $file, $configuration ) || !fclose( $file ) ) )
   			return PEAR::raiseError( "Could not write to the configuration file." );
  
  		return ( "" );
 	}

	/**
	 * @access public
	 */
	function Dig( $fuzzy_algorithm = "", $log = "" )
 	{
  		$log = array();

  		if ( !strcmp( $this->version, "" ) )
  		{
   			$command = $this->htdig_path . "htdig 2>/dev/null --help";
   			$log[]   = strftime( "%Y-%m-%d %H:%M:%S") . " Figuring htdig version... ($command)";
   			$version = array();
   			
			exec( $command, $version, $result );
   
   			if ( $result ) // !result ?
   			{
    			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htdig failed with result code $result";
				return PEAR::raiseError( "Execution of the htdig program failed: " . $command );
   			}
   
   			if ( !( $version_start = strrpos( $version[1], " " ) ) )
   			{
    			for ( $line = 0; $line < count( $version ); $line++ )
     				$log[] = $version[$line];
    
				$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " could not figure what is the htdig program version";
				return PEAR::raiseError( "Could not figure what is the htdig program version." );
   			}
   
   			$this->version = substr( $version[1], $version_start + 1 );
   			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htdig version is " . $this->version;
  		}

  		$command = $this->htdig_path . "htdig -v -s -a " . ( ( $this->configuration == "" )? "" : " -c " . $this->configuration );
  		$log[]   = strftime( "%Y-%m-%d %H:%M:%S" ) . " Starting htdig... ($command)";
  		
		exec( $command, $log, $result );
  
  		if ( $result )
  		{
   			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htdig failed with result code $result";
   			return PEAR::raiseError( "Execution of the htdig program failed: " . $command );
  		}
  
  		$log[]   = strftime( "%Y-%m-%d %H:%M:%S" ) . " htdig done...";
		$command = $this->htdig_path . "htmerge -v -s -a " . ( ( $this->configuration == "" )? "" : " -c " . $this->configuration );
  		$log[]   = strftime( "%Y-%m-%d %H:%M:%S" ) . " Starting htmerge... ($command)";
  		
		exec( $command, $log, $result );
  
  		if ( $result )
  		{
   			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htmerge failed with result code $result";
  	 		return PEAR::raiseError( "Execution of the htmerge program failed: ". $command );
  		}
  
  		$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htmerge done...";

  		if ( strcmp( $fuzzy_algorithm, "" ) )
  		{
   			$command = $this->htdig_path . "htfuzzy" . ( ( $this->configuration == "" )? "" : " -c " . $this->configuration . " $fuzzy_algorithm" );
   			$log[]   = strftime( "%Y-%m-%d %H:%M:%S" ) . " Starting htfuzzy... ($command)";
  	 		
			exec( $command, $log, $result );
   
   			if ( $result )
   			{
    			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htfuzzy failed with result code $result";
				return PEAR::raiseError( "Execution of the htfuzzy program failed: " . $command );
   			}
   
   			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " htfuzzy done...";
  		}

  		$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " Updating htdig database files";
  
  		if ( strcmp( $this->version, "3.2" ) < 0 )
  		{
   			$files = array(
    			"db.wordlist.work"			=> "db.wordlist",
    			"db.docdb.work"				=> "db.docdb",
    			"db.docs.index.work"		=> "db.docs.index",
    			"db.words.db.work"			=> "db.words.db"
   			);
  		}
  		else
  		{
   			$files = array(
    			"db.docdb.work"				=> "db.docdb",
    			"db.docs.index.work"		=> "db.docs.index",
    			"db.excerpts.work"			=> "db.excerpts",
    			"db.words.db.work"			=> "db.words.db",
    			"db.words.db.work_weakcmpr"	=> "db.words.db_weakcmpr"
   			);
  		}
  
  		for ( reset( $files ), $file = 0; $file < count( $files ); next( $files ), $file++ )
  		{
   			$from_file = $this->database_directory . key( $files );
   			$to_file   = $this->database_directory . $files[key( $files )];
   
   			if ( !file_exists( $from_file ) )
   			{
    			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " failed while checking htdig database file $from_file possibly because htdig program version is yet not supported";
    			return PEAR::raiseError( "Could not check htdig database file possibly because htdig program version is yet not supported." );
   			}
   
   			if ( !copy( $from_file, $to_file ) )
   			{
    			$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " failed while updating htdig database file $to_file";
    			return PEAR::raiseError( "Could not update htdig database file." );
   			}
  		}
  
  		$log[] = strftime( "%Y-%m-%d %H:%M:%S" ) . " Updated htdig database files";

  		for ( reset( $files ), $file = 0; $file < count( $files ); next( $files ), $file++ )
   			unlink( $this->database_directory . key( $files ) );
  
  		return ( "" );
	}

	/**
	 * @access public
	 */
	function Search( $words, $options, &$results )
 	{
  		$query_string = "words=" . urlencode( $words ) . "&format=htdig";
  
  		$option_names = array(
   			"config",
   			"exclude",
   			"keywords",
   			"matchesperpage",
   			"method",
   			"page",
   			"restrict",
   			"sort"
  		);
  
  		for ( $option = 0; $option < count( $option_names ); $option++ )
  		{
   			$option_name = $option_names[$option];
   
   			if ( isset( $options[$option_name] ) )
    			$query_string .= "&$option_name=" . urlencode( $options[$option_name] );
  		}
  
  		$path = $this->htdig_path . "htsearch";
  
  		if ( !file_exists( $path ) )
  		{
   			$cgi_path = $this->htdig_path . "../cgi-bin/htsearch";
   
   			if ( !file_exists( $cgi_path ) )
				return PEAR::raiseError( "The htsearch program executable could not be found." );
   
   			$path = $cgi_path;
  		}
  
  		$command = "REQUEST_METHOD=GET QUERY_STRING=\"$query_string\" " . $path . ( ( $this->configuration == "" )? "" : " -c " . $this->configuration );
  		exec( $command, $output, $result );
  
  		if ( $result )
   			return PEAR::raiseError( "Execution of the htsearch program failed: " . implode( "\n", &$output ) );
  
  		if ( count( $output ) < 3 )
   			return PEAR::raiseError( "Unexpected htsearch program output: " . implode( "\n", &$output ) );
  
  		switch ( $output[2] )
  		{
   			case "NOMATCH" :
    			$results = array( "MatchCount" => 0 );
				break;
   
   			case "SYNTAXERROR" :
				return PEAR::raiseError( "Unexpected htsearch program syntax error: " . implode( "\n", &$output ) );
   
   			default :
    			if ( count( $output ) < 6 )
     				return PEAR::raiseError( "Unexpected htsearch program output: " . implode( "\n", &$output ) );
    
				$first = intval( $output[3] );
    			$last  = intval( $output[4] );
    
				$results = array(
     				"MatchCount"	=> intval( $output[2] ),
     				"FirstMatch"	=> $first,
     				"LastMatch"		=> $last,
     				"Words"			=> $output[5]
    			);
    
				for ( $match = $first; $match <= $last; $match++ )
    			{
     				$line = 6 + ( $match - $first ) * 4;
     
	 				$results["Matches"][$match] = array(
      					"Title"		=> $output[$line],
      					"URL"		=> $output[$line+1],
      					"Percent"	=> intval( $output[$line+2] ),
      					"Excerpt"	=> $output[$line+3]
     				);
    			}
    
				break;
  		}

		return ( "" );
	}
} // END OF HTDig

?>
