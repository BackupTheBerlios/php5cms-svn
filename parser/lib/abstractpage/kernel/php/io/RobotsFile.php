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


/**
 * Class to write simple robots.txt files.
 *
 * Usage:
 *
 * $rf = new RobotsFile();
 * $rf->disallow( "/themen/thema_header.php3" );
 * $rf->disallow( "/music/" );
 * 
 * $rf->setUserAgent( "Googlebot-Image" );
 * $rf->disallow( "/basis/" );
 * $rf->disallow( "/cgi-bin/" );
 * 
 * echo( "<pre>" );
 * print_r( $rf->getRobotsString() );
 * echo( "<pre>" );
 * 
 * $rf->writeFile();
 *
 * @todo disallow by filter
 * @package io
 */
 
class RobotsFile extends PEAR
{
	/**
	 * @access public
	 */
	var $_actual_agent = "*";
	
	/**
	 * @access public
	 */
	var $_disallowed   = array( 
		"*" => array()
	);
	
	
	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function RobotsFile( $agent = "" )
	{
		if ( !empty( $agent ) )
			$this->setUserAgent( $agent );
	}
	

	/**
	 * @access public
	 */	
	function setUserAgent( $agent = "*" )
	{
		$this->_actual_agent = $agent;
	}
	
	/**
	 * @access public
	 */
	function disallow( $path = "", $check_existence = false )
	{
		if ( empty( $path ) )
			return false;
			
		if ( $check_existence && is_resource( $path ) )
		{
			if ( is_dir( $path ) )
			{
				// TODO: format correctly
			}
			else if ( is_file( $path ) )
			{
				// TODO: format correctly
			}
			else
			{
				return false;
			}
			
			$this->_disallowed[$this->_actual_agent][] = $path;
			return true;
		}
		else
		{
			$this->_disallowed[$this->_actual_agent][] = $path;
			return true;
		}
		
		return false;
	}
	
	/**
	 * @access public
	 */
	function getRobotsString()
	{
		$out = "";
		
		foreach ( $this->_disallowed as $agent => $disallowed )
		{
			$out .= "User-agent: " . $agent . "\n";
			
			for ( $i = 0; $i < count( $disallowed ); $i++ )
				$out .= "Disallow: " . $disallowed[$i] . "\n";
				
			$out .= "\n";
		}
		
		return $out;
	}
	
	/**
	 * @access public
	 */
	function writeFile()
	{
		$robot_path   = Util::docRoot(); // okay for us?
		$robot_file   = "robots.txt";
		$file_content = $this->getRobotsString();
		
		$fh = fopen( $robot_path . $robot_file, "wb" );
		
		if ( !$fh )
			return PEAR::raiseError( "Cannot write 'robots.txt' file." );
		
		fwrite( $fh, $file_content );
		fclose( $fh );
	}
} // END OF RobotsFile

?>
