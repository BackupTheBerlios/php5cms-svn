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
 * This class will calculate the total number of lines of all
 * files in a directory and it's subdirectory. Limit file checking
 * by adding the desired file extensions to the array below.
 *
 * Usage:
 *
 * $lineCount = new LineCount;
 *
 * // If no directory is given, it will use the directory of the script.
 * $lineCount->dir = "/path/to/files";
 *
 * // Use this method to output the summary and list of files to the page
 * // You can customize the HTML from within the class.
 * $lineCount->summary( 1 );
 *
 * // Use this method to get the totals as an associative array:
 * $totals = $lineCount->summary( 0 );
 * // echo $totals["folders"] / $totals["files"] / $totals["lines"]
 *
 * @package util
 */
 
class LineCount extends PEAR
{
	/**
	 * @access public
	 */
	var $x = 0;
	
	/**
	 * @access public
	 */
	var $cnt = array();

	/**
	 * Files to include in the count.
	 * @access public
	 */
	var $ext = array(
		"php",
		"phtml",
		"php3",
		"inc",
		"js",
		"html",
		"htm"
	);

	/**
	 * @access public
	 */	
	var $dir = "";
	
	
	/**
	 * @access public
	 */
	function summary( $output = 1 ) 
	{
		// $output
		//    1 to generate a summary and file list
		//    0 to get an associative array with the totals

		if ( !( is_dir( $this->dir ) ) ) 
		{ 
			// No directory given, use document root.
			$this->dir = $_SERVER['DOCUMENT_ROOT'];
		}

		$this->_countLines( $this->dir );

		$listOutput = "";
		$totalLines = 0;
		$usedDir    = array();

		for  ( $i = 0; $i < count( $this->cnt ); $i++ ) 
		{
			$totalLines += $this->cnt[$i]['count'];

			if ( !( in_array( $this->cnt[$i]['dir'], $usedDir ) ) ) 
			{
				if ( $output == 1 ) 
				{
					$listOutput .= "	<TR>\n";
					$listOutput .= "		<TD COLSPAN=\"2\" BGCOLOR=\"#EEEEEE\" WIDTH=\"100%\" ALIGN=\"left\" STYLE=\"border-bottom: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC\"><FONT STYLE=\"font-family: arial; font-size: 9pt\"><B>".$this->cnt[$i]['dir']."</B></FONT></TD>\n";
					$listOutput .= "	</TR>\n";
				}
				
				$usedDir[] = $this->cnt[$i]['dir'];
			}
			
			if ( $output == 1 ) 
			{
				$listOutput .= "	<TR>\n";
				$listOutput .= "		<TD WIDTH=\"80%\" ALIGN=\"left\" STYLE=\"border-bottom: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC\"><FONT STYLE=\"font-family: arial; font-size: 9pt\">".$this->cnt[$i]['file']."</FONT></TD>\n";
				$listOutput .= "		<TD WIDTH=\"20%\" ALIGN=\"center\" STYLE=\"border-bottom: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC\"><FONT STYLE=\"font-family: arial; font-size: 9pt\">".number_format($this->cnt[$i]['count'])."</FONT></TD>\n";
				$listOutput .= "	</TR>\n";
			}
		}

		$totalFiles   = number_format( count( $this->cnt ) );
		$totalLines   = number_format( $totalLines );
		$totalFolders = number_format( count( $usedDir ) );

		if ( $output == 1 ) 
		{
			print "<CENTER>\n";
			print "<B><FONT STYLE=\"font-family: arial; font-size: 13pt\">" . $this->dir . "</B></FONT><BR><BR>\n\n";
			print "<TABLE WIDTH=\"85%\" BORDER=\"0\" CELLPADDING=\"10\" CELLSPACING=\"0\">\n";
			print "	<TR>\n";
			print "		<TD WIDTH=\"10%\" ALIGN=\"left\"><FONT STYLE=\"font-family: arial; font-size: 11pt\"><B>Summary:</B> ".$totalFolders." folder(s), ".$totalFiles." file(s), ".$totalLines." lines of code</FONT></TD>\n";
			print "	</TR>\n";
			print "</TABLE>\n";
			print "<TABLE WIDTH=\"85%\" CELLPADDING=\"6\" CELLSPACING=\"1\" STYLE=\"border-top: 1px solid #CCCCCC; border-left: 1px solid #CCCCCC\">\n";
			print "	<TR>\n";
			print "		<TD WIDTH=\"80%\" ALIGN=\"left\" BGCOLOR=\"#4C6177\" STYLE=\"border-bottom: 1px solid #182C41; border-right: 1px solid #182C41\"><FONT STYLE=\"font-family: arial; font-size: 11pt; color: #FFFFFF\"><B>Filename</B></FONT></TD>\n";
			print "		<TD WIDTH=\"20%\" ALIGN=\"center\" BGCOLOR=\"#4C6177\" STYLE=\"border-bottom: 1px solid #182C41; border-right: 1px solid #182C41\"><FONT STYLE=\"font-family: arial; font-size: 11pt; color: #FFFFFF\"><B>Lines</B></FONT></TD>\n";
			print "	</TR>\n";

			print $listOutput;

			print "</TABLE>\n";
			print "<TABLE WIDTH=\"85%\" BORDER=\"0\" CELLPADDING=\"10\" CELLSPACING=\"0\">\n";
			print "	<TR>\n";
			print "		<TD WIDTH=\"10%\" ALIGN=\"left\"><FONT STYLE=\"font-family: arial; font-size: 11pt\"><B>Summary:</B> ".$totalFolders." folder(s), ".$totalFiles." file(s), ".$totalLines." lines of code</FONT></TD>\n";
			print "	</TR>\n";
			print "</TABLE>\n";
			print "</CENTER>\n";
		} 
		else 
		{
			return array(
				"files"   => $totalFiles,
				"lines"   => $lines,
				"folders" => $totalFolders
			); 
		}
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _countLines( $dir ) 
	{
		if ( is_dir( $dir ) ) 
		{
			if ( $handle = opendir( $dir ) ) 
			{
				// Loop through files.
				while ( ( $file = readdir( $handle ) ) !== false ) 
				{ 
					if ( $file != "." && $file != ".." ) 
					{ 
						$filePath = $dir . "/" . $file;

						if ( is_dir( $filePath ) ) 
						{
							// Item is another folder, call the function again.
							$this->_countLines( $filePath );
						} 
						else 
						{
							// Item is a file, get some info about it.
							$fileName = explode( "/", $filePath );
							$fileDir  = $fileName[( count( $fileName ) - 2 )];
							$fileName = $fileName[( count( $fileName ) - 1 )];
							$fileExt  = explode( ".", $fileName );
							$fileExt  = $fileExt[( count( $fileExt ) - 1 )];
	
							if ( in_array( $fileExt, $this->ext ) ) 
							{
								// Open the file, get line count.
								$fp       = fopen( $filePath, "r" );
								$buffer   = rawurlencode( fread( $fp, filesize( $filePath ) ) );
								$buffer   = explode( "%0A", $buffer );
								$numLines = count( $buffer );
								
								fclose( $fp );

								// Add the information to our count array.
								$this->cnt[$this->x]['dir']   = $dir;
								$this->cnt[$this->x]['file']  = $fileName;
								$this->cnt[$this->x]['count'] = $numLines;
								
								$this->x++;
							}
						}
					} 
				}

				closedir( $handle );
			} 
			else 
			{
				return false;
			}
		} 
		else 
		{
			return false;
		}
	}
} // END OF LineCount

?>
