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
 * Simple class to pick news from Google.com.
 *
 * @package services_google
 */
 
class GoogleNews extends PEAR
{
	/**
	 * Gets the latest headline from Google.
	 *
	 * @access public
	 * @static
	 */
	function getNews( $topic )
    {
		$pattern     = "/ /i";
		$replacement = "+" ;
		$topic       = preg_replace( $pattern, $replacement, $topic );
        $str         = "http://news.google.com/news?hl=en&edition=us&q=" . $topic . "&btnG=Search+News";
        $handle      = fopen( $str, "rb" );
        
		while ( !feof( $handle ) )
             $img = $img . fread( $handle, 1024 );
        
        fclose( $handle );
        
        $start = "<div>";
        $end   = "<b>\.\.\.<\/b>\s*?<\/font>";
        
        preg_match( "/$start(.*?)$end/s", $img, $str ); // go through the page
        $str = preg_replace( "/<br>/", " ", $str[1] );
        $str = preg_replace( "/<b>/", " ", $str );
		
		return $str;
    }
} // END OF GoogleNews

?>
