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
 * @package peer
 */
 
class Ping extends PEAR
{
	/**
	 * @access public
	 */	
	function chkuri( $link )
	{
		$churl = @fopen( "http://" . $link, 'r' );

		if ( !$churl )
			return false;
		else
			return true;

		return $msg;
	}

	/**
	 * @access public
	 */
	function performping( $link )
	{
		$packs = 5;
	
		for ( $tt = 0; $tt <= $packs; $tt++ )
		{
			$a     = $this->getmstime();
			$churl = @fsockopen( $this->server( $link ), 80, &$errno, &$errstr, 20 );
			$b     = $this->getmstime();
		
			if (!$churl)
			{
				$time = "down!!";
				break;
       		}
      
	  		$time = $time + round( ( $b - $a ) * 1000 );
       		@fclose( $churl );
  		}
  
  		if ( $time == "down!!" )
		{
		}
		else
		{
			if ( ( $time / $packs ) < 3 )
				$time = "<3 ms";
			else
				$time = ( $time / $packs ) . " ms";
		}

		return $time;
	}

	/**
	 * @access public
	 */
	function server( $link )
	{
		if ( strstr( $link, "/" ) )
			$link = substr( $link, 0, strpos( $link, "/" ) );

		return $link;
	}

	/**
	 * @access public
	 */
	function getmstime()
	{
		return ( substr( microtime(), 11, 9 ) + substr( microtime(), 0, 10 ) );
	}

	/**
	 * @access public
	 */
	function correcturl( $link )
	{
		return str_replace( "http://", "", strtolower( $link ) );
	}
} // END OF Ping
	
?>
