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
 * @package xml
 */
 
class CSV2XML extends PEAR
{
	/**
	 * @access public
	 */
	var $encoding;
	
	/**
	 * @access public
	 */
	var $separator = ";";
	
	/**
	 * @access public
	 */
	var $rootname = "data";
	
	/**
	 * @access public
	 */
	var $rowname = "row";
	
	/**
	 * @access public
	 */
	var $maketabs = true;
	
	/**
	 * @access public
	 */
	var $buffersize = 4096;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function CSV2XML( $separator, $rootname, $rowname )
	{
		if ( isset( $separator ) )
			$this->setSeperator( $separator );
			
		if ( isset( $rootname ) )
			$this->setRootName( $rootname );
			
		if ( isset( $rowname ) )
			$this->setRowName( $rowname );
	}
	

	/**
	 * @access public
	 */	
	function setSeperator( $sep )
	{
		if ( isset( $sep ) )
			$this->separator = $sep;
	}
	
	/**
	 * @access public
	 */
	function setRootName( $name )
	{
		if ( isset( $name ) )
			$this->rootname = $name;
	}
	
	/**
	 * @access public
	 */
	function setRowName( $name )
	{
		if ( isset( $name ) )
			$this->rowname = $name;
	}

	/**
	 * @access public
	 */
	function convert( $in, $out = null, $description = array(), $skipfirst = true, $convertspecial = true )
	{
		$mode   = ( $out != null )? "file" : "print";
		$str    = "<?xml version=\"1.0\"" . ( is_string( $this->encoding )? " encoding=\"" . $this->encoding . "\"" : "" ) . "?>\n\n";
		$str   .= "<" . $this->rootname . ">\n\n";
		
		// no csv file given
		if ( isset( $in ) )
		{
			$fe = file_exists( $in );
			
			if ( $fe )
			{
				// open csv file
				$csv   = fopen( $in, "r" );
				$count = 0;
				
				while ( $actline = fgets( $csv, $this->buffersize ) )
				{	
					$chunks = explode( $this->separator, trim( $actline ) );
					
					if ( $count == 0 )
					{
						if ( is_array( $description) && ( count( $description ) > 0 ) )
						{
							if ( count( $description ) != count( $chunks ) )
								return PEAR::raiseError( "Number of description arguments doesn't match." );
							
							$tagnames = $description;
							
							if ( !$skipfirst )
								$count--;
						}
						else
						{
							$tagnames = $chunks;
						}
					}
					else
					{
						$str .= "<" . $this->rowname . ">\n";
						
						for ( $i = 0; $i < count( $chunks ); $i++ )
						{
							$val  = $convertspecial? htmlspecialchars( $chunks[$i] ) : $chunks[$i];
							$str .= ( $this->maketabs? "\t" : "" ) . "<" . $tagnames[$i] . ">" . htmlspecialchars( $chunks[$i] ) . "</" . $tagnames[$i] . ">\n";
						}
							
						$str .= "</" . $this->rowname . ">\n\n";
					}
					
					$count++;
				}
				
				$str .= "</" . $this->rootname . ">\n";
				fclose( $csv );
				
				// save to file
				if ( $mode == "file" )
				{
					$xml = fopen( $out, 'w' );
					
					if ( $xml )
					{
						fwrite( $xml, $str );
						fclose( $xml );
					}
					else
					{
						return PEAR::raiseError( "XML File is not writeable." );
					}
				}
				// fire xml
				else
				{
					ob_start();
					echo $str;
					
					header( "Content-Type: text/xml" );
					header( "Content-Length: " . strlen( $str ) );
					
					ob_end_flush();
				}
			}
			else
			{
				return PEAR::raiseError( "Cannot find CSV file." );
			}
		}
		else
		{
			return PEAR::raiseError( "No CSV file specified." );
		}
		
		return true;
	}
} // END OF CSV2XML

?>
