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
 *  This is a class allows you to do a man page lookup and view
 * the results in a parsed format, including bold, italics and
 * coloured text.  A search form is included to allow you to list
 * man pages (all of them, all by letter, number, etc.) and to
 * offer an easy way to search on different man sections.
 *
 * Contributions
 *
 * Caching idea and code contribution by James Richardson
 * Unicode soft-hyphen fix (as used by RedHat) by Dan Edwards
 * Some optimisations by Eli Argon
 * Based on a C man page viewer by Vadim Pavlov
 *
 * Example of use
 *
 * $mp = new ManPageLookup();
 * $mp->DisplayManPage();
 *
 * Example of using cache
 *
 * $mp = new ManPageLookUp();
 * $mp->UseCaching( true );
 * $mp->SetCacheDir( dirname( __FILE__ ) . "/cache/" );
 * $mp->DisplayManPage();
 *
 * @package sys
 */
 
class ManPageLookup extends PEAR
{
	/**
	 * the raw command passed
	 * @access public
	 */
	var $command;
	
	/**
	 * the man page section
	 * @access public
	 */
	var $section;
	
	/**
	 * the raw data
	 * @access public
	 */
	var $raw_data;
	
	/**
	 * the html formatted data
	 * @access public
	 */
	var $output;
	
	/**
	 * what groups of man pages to display
	 * @access public
	 */
	var $display;
	
	/**
	 * how many columns to display
	 * @access public
	 */
	var $width;
	
	/**
	 * convert emails to mailto: addresses
	 * @access public
	 */
	var $doemails;
	
	/**
	 * show search box above output
	 * @access public
	 */
	var $showsearch;
	
	/**
	 * directory where outputs are cached
	 * @access public
	 */
	var $cachedir;
	
	/**
	 * do we get the raw data or not?
	 * @access public
	 */
	var $use_rawdata;
	
	/**
	 * do we use caching or not?
	 * @access public
	 */
	var $use_caching;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ManPageLookup()
	{
		$this->command     = ( $_POST['command']? $_POST['command'] : $_GET['command'] );
		$this->section     = $_POST['section'];
		$this->display     = $_GET['display'];
		
		$this->output      = "";
		$this->rawdata     = "";
		$this->width       = 4;
		$this->doemails    = 1;
		$this->showsearch  = 1;
		$this->use_rawdata = 0;
		$this->use_caching = 0;
		
		$this->SetCacheDir();
	}
	
	
	/**
	 * @access public
	 */
	function UseCaching( $do )
	{
		$this->use_caching = ( ( $do == true )? true : false );
	}

	/**
	 * @access public
	 */
	function SetCacheDir( $dir = "/var/tmp/man-cache-html/" )
	{
		$this->cachedir = $dir . ( ( $dir[strlen( $dir ) - 1] != "/" )? "/" : "" );
	}

	/**
	 * @access public
	 */	
	function GetCacheName( $name = "" )
	{
		if ( $name == "" )
			$name = $this->command;
		
		return ( $this->cachedir . md5( $name ) . ".html" );
	}

	/**
	 * @access public
	 */
	function CheckCache( $name = "" )
	{
		if ( $name == "" )
			$name = $this->command;
		
		$fn = $this->GetCacheName( $name );
		$fs = @filesize( $fn );
		
		if ( $fs )
		{
			$fp = @fopen( $fn, "r" );
			
			if ( $fp )
			{
				$this->output = fread( $fp, $fs );
				fclose( $fp );
				
				return true;
			}
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function SaveCache( $name = "" )
	{
		if ( $name == "" )
			$name = $this->command;
		
		$fp = fopen( $this->GetCacheName( $name ), "w" );
		
		if ( $fp )
		{
			fwrite( $fp, $this->output );
			fclose( $fp );
			
			return true;
		}
		
		return false;
	}

	/**
	 * @access public
	 */
	function GetRawData()
	{
		$this->raw_data = "";
		$this->raw_data = @strip_tags( $this->output );
		
		$trans = @get_html_translation_table( HTML_ENTITIES );
		$trans = @array_flip( $trans );
		
		$this->raw_data = @strtr( $this->raw_data, $trans );
	}

	/**
	 * @access public
	 */
	function SearchManPage()
	{
		$sections = array(
			1 => "Executable programs or shell commands",
			2 => "System calls (functions provided by the kernel)",
			3 => "Library calls (functions within system libraries)",
			4 => "Special files (usually found in /dev)",
			5 => "File formats and conventions eg /etc/passwd",
			6 => "Games",
			7 => "Macro packages and conventions eg man(7), groff(7)",
			8 => "System administration commands (usually only for root)",
			9 => "Kernel routines [Non standard]"
		);
		$alphas = array( "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" );

		// manual search table
		echo "\n<form action=\"" . $_SERVER["PHP_SELF"] . "\" method=\"POST\">\n";
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#DDDDDD\">\n";
		echo "<tr><td><b>Man page search options</b></td></tr>\n";
		echo "<tr><td><select name=\"section\"><option value=\"\">Any section</option>";
		
		foreach ( $sections as $num => $type )
			echo "<option value=\"$num\">(S$num) $type</option>\n";
		
		echo "</select> <input type=\"text\" name=\"command\" size=\"25\"> ";
		echo "<input type=\"submit\" value=\"search\"></td></tr>\n";
		
		// a-z listing table
		echo "<tr><td><b>List man pages starting with</b></td></tr>\n";
		echo "<tr><td>";
		
		for ( $i = 0; $i < 26; $i++ )
			echo "<a href=\"$_SERVER["PHP_SELF"]?command=&display={$alphas[$i]}\">{$alphas[$i]}</a> &nbsp; ";
		
		echo "<a href=\"$_SERVER["PHP_SELF"]?command=&display=ALP\">ALPHA</a> &nbsp; ";
		echo "<a href=\"$_SERVER["PHP_SELF"]?command=&display=NUM\">NUM</a> &nbsp; ";
		echo "<a href=\"$_SERVER["PHP_SELF"]?command=&display=OTH\">OTHER</a> &nbsp; ";
		echo "<a href=\"$_SERVER["PHP_SELF"]?command=&display=ALL\">ALL</a>\n";
		echo "</td></tr>\n</table>\n</form>\n\n";
	}

	/**
	 * @access public
	 */
	function GetBigList( $name = "" )
	{
		$pipe = popen( "man -k a", "r" );
		
		if ( !$pipe )
		{
			echo "<p>Cannot open a pipe to a list of all man pages.</p>";
			return;
		}
		
		$build = array();
		
		while ( !feof( $pipe ) )
		{
			$s = fgets( $pipe, 1024 );
			$s = trim( $s );
			
			preg_match( "/(.*?) \((.*?)\)(.*)/i", $s, $matches );
			
			switch ( $this->display )
			{
				case "ALL":
					if ( $matches[1] )
						$build[] = $matches[1];
					
					break;
				
				case "NUM":
					if ( preg_match( "/^[0-9]/", $matches[1] ) )
						$build[] = $matches[1];
					
					break;
				
				case "ALP":
					if ( preg_match( "/^[a-zA-Z]/", $matches[1] ) )
						$build[] = $matches[1];
					
					break;
				
				case "OTH":
					if ( preg_match( "/^[^a-zA-Z0-9]/", $matches[1] ) )
						$build[] = $matches[1];
					
					break;
				
				default:
					if ( preg_match( "/^{$this->display}/i", $matches[1] ) )
						$build[] = $matches[1];
					
					break;
			}
		}
		
		pclose( $pipe );
		sort( $build );
		
		// create output
		$cnt = 0;
		$this->output = "<table border=\"0\" border=\"0\" width=\"100%\" cellpadding=\"5\">\n<tr>\n";
		
		for ( $i = 0; $i < count( $build ); $i++ )
		{
			if ( $cnt++ == $this->width )
			{
				$this->output .= "</tr>\n<tr>\n";
				$cnt = 1;
			}
			
			$this->output .= "	<td><a href=\"$_SERVER["PHP_SELF"]?command=" . urlencode($build[$i]) . "\">" . htmlentities( $build[$i] ) . "</a></td>\n";
		}
		
		$this->output .= "</tr>\n</table>\n";
		$this->output .= "<p>" . count( $build ) . " man page" . ( ( count( $build ) == 1 )? "" : "s" ) . "</p>\n";
		
		// create plain text version
		if ( $this->use_rawdata )
			$this->GetRawData();
		
		// cache
		if ($this->use_caching)
			$this->SaveCache( $name );
	}

	/**
	 * @access public
	 */
	function BuildManPage()
	{
		// make sure not to include any /, <, ;, etc
		$cmd  = ( ( $this->command == "" )? "man" : eregi_replace( "[^a-zA-Z0-9:\[_ -.+\]]", "", $this->command ) );
		$exe  = "man " . ( $this->section? "-S{$this->section} ":"" ) . EscapeShellCmd( $cmd );
		$pipe = popen( $exe, "r" );

		if ( !$pipe )
		{
			echo "<p>Cannot open a pipe to the man page.</p>";
			return;
		}
	
		$build = "";
		
		while ( !feof( $pipe ) )
		{
			$s = fgets( $pipe, 1024 );
			
			for ( $i = 0 ; $i < strlen( $s ); $i++ )
			{ 
				switch ( ord( $s[$i] ) )
				{
					case 8:
						break;
				
					case 0xAD:
						// Unicode soft hyphen
						$build .= "-";
						break;
				
					default:
						if ( ord( $s[$i+1] ) == 8 )
							break;
					
						if ( ord( $s[$i-1] ) == 8 )
						{
							if ( $s[$i-2] == $s[$i] )
							{
								if ($italic)
								{
									$build  .= "</i></font>";
									$italic  = 0;
								}
									
								if ( $bold )
								{
									$build .= htmlentities( $s[$i] );
								}
								else
								{
									$build .= "<b>" . htmlentities( $s[$i] );
									$bold = 1;
								}
							}
							else if ( $s[$i-2] == '_' )
							{
								if ( $bold )
								{
									$build .= "</b>";
									$bold   = 0;
								}
						
								if ( $italic )
								{
									$build .= htmlentities($s[$i]);
								}
								else
								{
									$build  .= "<font color=\"#0000FF\"><i>" . htmlentities($s[$i]);
									$italic  = 1;
								}
							}
						}
						else
						{
							if ( $italic )
							{
								$build  .= "</i></font>";
								$italic  = 0;
							}
					
							if ( $bold )
							{
								$build .= "</b>";
								$bold   = 0;
							}
					
							$build .= htmlentities( $s[$i] );
						}
				
						break;				  
				}
			}
		}
		
		pclose( $pipe );
		
		// create formatted version
		$this->output = "";
		$this->output = ereg_replace( "\n\n\n+", "\n\n", $build );

		if ( $this->doemails )
			$this->output = eregi_replace( "[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}", "<a href=\"mailto:\\0\">\\0</a>", $this->output );
	
		// create plain text version
		if ( $this->use_rawdata )
			$this->GetRawData();
		
		// cache
		if ( $this->use_caching )
			$this->SaveCache();
	}

	/**
	 * @access public
	 */
	function DisplayManPage()
	{
		// setup the man page/search form
		if ( ( $this->command == "" ) && ( $this->display == "" ) )
		{
			$this->SearchManPage();
			return;
		}
		else
		{
			if ( $this->display != "" )
			{
				switch ( $this->display )
				{
					case "ALL":
						$tmpCacheName = "cacheALL";
						break;
						
					case "NUM":
						$tmpCacheName = "cacheNUM";
						break;
						
					case "ALP":
						$tmpCacheName = "cacheALP";
						break;
						
					case "OTH":
						$tmpCacheName = "cacheOTH";
						break;
						
					default:
						$tmpCacheName = "cache" . $this->display;
						break;
				}
				
				if ( $this->use_caching && $this->CheckCache( $tmpCacheName ) );
				else $this->GetBigList($tmpCacheName);
			}
			else if ( $this->use_caching && $this->CheckCache() )
			{
			}
			else 
			{
				$this->BuildManPage();
			}
		}

		// show the man page/list results
		if ( $this->showsearch )
			$this->SearchManPage();
		
		if ( $this->command )
		{
			if ( $this->output == "" )
			{
				echo "<p>Could not display man page for <b>{$this->command}</b>";
				
				if ( $this->section )
					echo " (using -S{$this->section})";
				
				echo "</p>\n";
			}
			else
			{
				echo "<pre>{$this->output}</pre>\n";
			}
		}
		else
		{
			if ($this->output)
			{
				echo $this->output;
			}
			else
			{
				switch ( $this->display )
				{
					case "ALL":
						echo "<p>No man pages were found or could be listed.</p>\n";
						break;
						
					case "NUM":
						echo "<p>No man pages starting with a digit were found or could be listed.</p>\n";
						break;
						
					case "ALP":
						echo "<p>No man pages starting with an alpha character were found or could be listed.</p>\n";
						break;
						
					case "OTH":
						echo "<p>No man pages starting with a character other than alphanumeric were found or could be listed.</p>\n";
						break;
						
					default:
						echo "<p>No man pages starting with {$this->display} were found or could be listed.</p>\n";
						break;
				}
			}
		}
	}
} // END OF ManPageLookup

?>
