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
 * Realtime Apache Bandwidth-Based Redirection.
 * Required Apache with Extended Status and /server-status enabled.
 *
 * Method 1:
 * Send them directly to the file. Good for files.
 * header( "Location: $location" );
 *
 * Method 2:
 * Embed the path as text (in an IMG tag, etc).
 * To use, where you'd stick the source, call PHP with the following code:
 * readfile( "http://path.com/to/load.php?sn=file" );
 * echo $location;
 *
 * @package org_apache
 */

class ApacheServerStatus extends PEAR
{
	/**
	 * @access public
	 */
	var $server;
	
	/**
	 * @access public
	 */
	var $mirror;
	
	/**
	 * Maximum Bandwidth before rollover in BYTES (500000 is 500k).
	 * @access public
	 */
	var $maxband;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ApacheServerStatus( $server, $mirror, $max_bandwidth = 500000 )
	{
		$this->server  = $server;
		$this->mirror  = $mirror;
		$this->maxband = $max_bandwidth;
		
		$this->_populate();
	}
	
	
	/**
	 * Load balancing helper.
	 *
	 * @access public
	 */
	function getLocation( $file )
	{
		if ( $this->bs > $this->maxband )
			$location = "http://$this->mirror/$file";
		else
			$location = "http://$this->server/$file";
			
		return $location;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populate()
	{
		$handle = fopen( "http://$this->server/server-status?auto", "r" );

		// Check if handle is valid.
		while ( !feof( $handle ) )
			$buffer = fread( $handle, 4096 );

		fclose( $handle );
		
		/*
		Returns the following stuff...

		Total Accesses: 22403
		Total kBytes: 434115
		CPULoad: .341125
		Uptime: 612319
		ReqPerSec: .0365871
		BytesPerSec: 725.984
		BytesPerReq: 19842.6
		BusyServers: 3
		IdleServers: 7
		...
		*/

		list( $th, $tb, $cpu, $uptime, $rs, $bs, $br, $busy, $idle )= split( "\n", $buffer );
		
		$this->th     = ereg_replace( ".*Total Accesses: ", "", $th     );
		$this->tb     = ereg_replace( ".*Total kBytes: ",   "", $tb     );
		$this->cpu    = ereg_replace( ".*CPULoad: ",        "", $cpu    );
		$this->uptime = ereg_replace( ".*Uptime: ",         "", $uptime );
		$this->rs     = ereg_replace( ".*ReqPerSec: ",      "", $rs     );
		$this->bs     = ereg_replace( ".*BytesPerSec: ",    "", $bs     );
		$this->br     = ereg_replace( ".*BytesPerReq: ",    "", $br     );
		$this->busy   = ereg_replace( ".*BusyServers: ",    "", $busy   );
		$this->idle   = ereg_replace( ".*IdleServers: ",    "", $idle   );
	}
} // END OF ApacheServerStatus

?>
