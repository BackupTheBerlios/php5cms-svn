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
 
class PrintServer extends PEAR
{
	/**
	 * @access public
	 */
	var $username;
	
	/**
	 * @access public
	 */
	var $port;
	
	/**
	 * @access public
	 */
	var $host;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PrintServer( $user, $host = 'localhost', $port = 515 )
	{
		$this->username = $user;
		$this->host     = $host;
		$this->port     = $port;
	}
	
	
	/**
	 * @access public
	 */
	function setPrintServer( $server = "localhost" )
	{
		$this->host = $server;
	}

	/**
	 * @access public
	 */	
	function setPassword( $pass )
	{
		if ( empty( $pass ) )
			return false;
			
		$this->username = $pass;
	}
	
	/**
	 * Send the lpr request string to the specified host.
	 * Then get the response and stuff it into an array.
	 * Each line is trimmed, removing white space from
	 * both ends.
	 *
	 * @access public
	 */
	function fetch( $lpstr )
	{
		unset( $retarray );
		$linelen = 1024;

		// Check the dns entry for the hostname.
		// This is simply to give better feed back
		// as to any errors.
		if ( !checkdnsrr( $this->host, "A" ) )
			return false;

		// Open a socket to the print server.
		$sock = fsockopen( $this->host, $this->port, $errno, $errstr );

		if ( !$sock )
			return;

		fwrite( $sock, $lpstr );

		// Slurp up the whole response.
		for ( $string = fgets( $sock, $linelen ); $string; $string = fgets( $sock, $linelen ) )
			$retarray[] = trim( $string );

		fclose( $sock );
		return $retarray;
	}

	/**
	 * @access public
	 */
	function lpq( $printer = "all", $options = "" )
	{
		// @unset( $retarray );
		unset( $retarray );

		// Check out our arguments.
		if ( !is_array( $options ) )
			$options = array( $options );

		$outstr = "\4" . $printer . " " . implode( $options, " " ) . "\n";

		// Get the server's response.
		$rawarray = $this->fetch( $outstr );

		if ( !is_array( $rawarray ) )
			return $retarray;

		// Some strings we will use.
		$rankstring    = "Rank";
		$printerstring = "Printer:";
		$errorstring   = "ERROR:";
		$serverstring  = "Server";
		$statusstring  = "Status:";

		$injobs = 0;

		// Go through the array.
		reset( $rawarray );
		while ( list( $key, $string ) = each( $rawarray ) )
    	{
    		if ( strlen( $string ) == 0 )
				continue;

			// What is the first word of the current line?
			$firsttoken = strtok( $string, " " );

			// If the first token is Printer or Server, then
			// we have the printer info line. Form:
			// 		[Server ]Printer:
			// 		Q@PRINTSERVER
			//		(dest Q@PRINTSERVER)
			//		[(serving Q1)|
			//		(subservers Q1, ...)]
			//		... 'COMMENT'
			
			if ( $firsttoken == $printerstring || $firsttoken == $serverstring )
			{
				$injobs = 0;

				if ( $firsttoken == $serverstring )
					$secondtoken = strtok( " " );

				// Get the queuename, print server and option comment.

				if ( ereg( ":[ ]+([^@]+)@([^ ]+)[^']+('([^']+)')?", $string, $regexarray ) )
				{
					$printer = $regexarray[1];
					$pserver = $regexarray[2];
					$comment = $regexarray[4];

					$retarray[$pserver][$printer]["comment"] = $comment;
				}

				// Does it have a destination listed?
				if ( ereg( "\(dest([^\)]+)\)", $string, $regexarray ) )
				{
					$destination = $regexarray[1];
					$retarray[$pserver][$printer]["destination"] = $destination;
				}

				// Is is a server queue?
				if ( ereg( "\(subservers([^\)]+)\)", $string, $regexarray ) )
				{
					$subserver = $regexarray[1];
					$subserverarray = split( "[ ]+|[,]|[)]", $subserver );

					@reset( $subserverarray );
					while ( list( $index, $subserv ) = @each( $subserverarray ) )
					{
						if ( strlen( $subserv ) == 0 )
							continue;

						$retarray[$pserver][$printer]["subservers"][] = $subserv;
					}
				}

				// Is it a subserver queue?
				if ( ereg( "\(serving([^\)]+)\)", $string, $regexarray ) )
				{
					$serving = $regexarray[1];
					$retarray[$pserver][$printer]["server"] = trim( $serving );
				}

				continue;
			}

			// '^Rank' indicates we are getting to the job list.
			if ( $firsttoken == $rankstring )
			{
				$injobs = 1;
				continue;
			}

			// Finally the jobs listing.
			switch ( $injobs )
			{
				case 1:
					{
						if ( !eregi( "([^ ]+)[ ]+" .							// Rank
									 "([^@]+)@([^\+]+)\+([0-9]+)[ ]+" .			// Owner
									 "([a-z])[ ]+" .							// Class
									 "[0-9]*[ ]*" .								// Jobid (not always complete)
									 "(.*)[ ]+" .								// Filename (not always there)
									 "([0-9]+)[ ]+" .							// Size
									 "(([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}-)?" .	// Time
									 "[0-9]+:[0-9]+:[0-9]+)",					// Date
									 $string, $regexarray ) )
						{
							continue;
						}

						$rank     = $regexarray[1];
						$username = $regexarray[2];
						$source   = $regexarray[3];
						$jobid    = $regexarray[4];
						$owner    = "$username@$pserver+$jobid";
						$class    = $regexarray[5];
						$filename = $regexarray[6];
						$size     = $regexarray[7];
						$time     = $regexarray[8];

						if ( strlen( $filename ) == 0 )
							$filename = "unknown";

						$lpqline = array(
							"printer"  => $printer,
							"pserver"  => $pserver,
							"source"   => $source,
							"rank"     => $rank,
							"owner"    => $owner,
							"username" => $username,
							"source"   => $source,
							"class"    => $class,
							"jobid"    => $jobid,
							"file"     => $filename,
							"size"     => $size,
							"time"     => $time
						);

						$retarray[$pserver][$printer]["jobs"][] = $lpqline;
						break;
					}

				// Some other token starts the line, like Error, Status, etc.
				default:
					{
						$lctoken = strtolower( strtok( $string, ":" ) );
						$value   = strtok( "" );

						if ( strlen( strstr( $value, "no permission" ) ) != 0 )
						{
							$value   = $string;
							$lctoken = "error";
						}

						if ( $lctoken == "status" )
							$retarray[$pserver][$printer][$lctoken][] = $value;
						else
							$retarray[$pserver][$printer][$lctoken]   = $value;

						break;
					}
			}
		}

		@reset( $retarray );
		return $retarray;
	}

	/**
	 * Removing jobs.
	 *
	 * @access public
	 */
	function lprm( $printer, $args = "", $raw = 0 )
	{
		global $errorarray;
		
		unset( $errorarray );
		unset( $retarray );
		
		if ( !is_array( $args ) )
			$args = array( $args );
			
		if ( strlen( @trim( $printer ) ) == 0 )
			$printer = "all";

		$outstr    = "\5$printer $this->username " . implode( $args, " " ) . "\n";
		$lprmarray = $this->fetch( $outstr );

		// If they want it raw, then don't process the output.
		if ( $raw )
			return $lprmarray;

		for ( $count = 0; $count < @count( $lprmarray ); $count++ )
    	{
    		$string = strtok( $lprmarray[$count], " " );

			// What queue are we talking about?
			if ( $string == "Printer" )
			{
				$printer = strtok( "@" );
				$pserver = strtok( ":" );

				continue;
			}

			// We only understand two types of lines.
			if ( $string != "dequeued" )
			{
				$errorarray[] = $lprmarray[$count];
				continue;
			}

			// Break apart a 'dequeued' line.
			// It is of the form: dequeued user@printhost+id
			$retval = ereg( "'([^@]*)@([^+]*)\+([^']*)'", $lprmarray[$count], $array );

			if ( $retval == 0 )
			{
				$errorarray[] = $lprmarray[$count];
				continue;
			}

			$retarray[] = array(
				"username" => $array[1],
				"source"   => $array[2],
				"jobid"    => $array[3],
				"pserver"  => $pserver,
				"printer"  => $printer
			);
		}

		return $retarray;
	}

	/**
	 * Execute any lpc command.
	 *
	 * @access public
	 */
	function lpc( $printer, $args = "" )
	{
		// @unset( $retarray );
		unset( $retarray );
		
		if ( !is_array( $args ) )
			$args = array( $args );

		if ( strlen( @trim( $printer ) )== 0 )
			$printer = "all";

		$outstr   = "\6$printer $this->username " . implode( $args, " " ) . "\n";
		$retarray = $this->fetch( $outstr );

		return $retarray;
	}

	/**
	 * Execute and process lpc's status command.
	 *
	 * @access public
	 */
	function status( $printer = "all" )
	{
		global $errorarray;
		
		unset( $errorarray );
		unset( $retarray );
		
		// Get the status of all printers if the printer string is empty.
		if ( strlen( @trim( $printer ) ) == 0 )
			$printer = "all";

		// Where do we find what we need?
		$printingindex = 1;
		$spoolingindex = 2;
		$jobsindex     = 3;
		$serverindex   = 4;
		$slaveindex    = 5;
		$redirectindex = 6;
		$statusindex   = 7;

		// Issue the status command of lpc.
		$rawarray = $this->lpc( $printer, array( "status" ) );

		// Skip the first line, it is simply column headers.
		@reset( $rawarray );
		@next( $rawarray  );

		// Go through the raw lpc array.
		while ( list( $index, $line ) = @each( $rawarray ) )
    	{
    		if ( strlen( strstr( $line, "not in printcap" ) ) != 0 )
        	{
				$errorarray[] = $line;
				continue;
			}

			// Break on white space.
			$array = split( " +", $line );
			list( $printer, $pserver ) = explode( "@", $array[0] );

			// Sanity check! Make sure we recognize the printing
			// and spooling columns.
			if ( $array[$printingindex] != "enabled"  &&
				 $array[$printingindex] != "disabled" &&
				 $array[$printingindex] != "aborted"  &&
				 $array[$spoolingindex] != "enabled"  &&
				 $array[$spoolingindex] != "disabled" )
			{
				$errorarray[] = $line;
				continue;
        	}

			// If there is a status column and no redirection, then
			// we will wrongly interpret the status as the redirect.
			if ( strstr( $array[$redirectindex], '(' ) )
			{
				$array[$statusindex]   = $array[$redirectindex];
				$array[$redirectindex] = "";
        	}

			// Add an entry to the return array.
			$retarray[$printer] = array(
				"printer"  => $printer,
				"pserver"  => $pserver,
				"printing" => $array[$printingindex],
				"spooling" => $array[$spoolingindex],
				"jobs"     => $array[$jobsindex],
				"server"   => $array[$serverindex],
				"slave"    => $array[$slaveindex],
				"redirect" => $array[$redirectindex],
				"status"   => $array[$statusindex]
			);
		}

		return $retarray;
	}

	/**
	 * Get a list of printers.
	 *
	 * @access public
	 */
	function getprinters()
	{
		$statarray = $this->status( "all" );
		@reset( $statarray );

		while ( list( $index, $statline ) = @each( $statarray ) )
			$retarray[] = $statline["printer"];

		@reset( $retarray );
		return $retarray;
	}
	
	/**
	 * Get a list of subservers. 
	 * Return array is like:
	 * $retarray[server-queue] =  array(sub1, sub2, ...);
	 *
	 * @access public
	 */
	function getsubservers( $printer = "all" )
	{
		$retarray = array();

		// Get the short lpq format.
		$rawarray = $this->fetch( "\3$printer $this->username\n" );

		if ( !is_array( $rawarray ) )
			return $retarray;

		$pservershort = strtok( $this->host, "." );

		// Go through the lpq array.
		while ( list( $index, $lpqline ) = each( $rawarray ) )
		{
			if ( strlen( strstr( $lpqline, "(serving") ) != 0 )
				continue;

			if ( !ereg( "^(.+)@$pservershort", $lpqline, $regexarray ) )
				continue;

			$printer = $regexarray[1];

			// Does the current queue have subservers?
			if ( !ereg( "\(subservers(.+)\)", $lpqline, $regexarray ) )
			{
				$retarray[$printer] = array();
				continue;
			}

			$subserverstr   = $regexarray[1];
			$subserverarray = split( "[ ]+|[,]|[)]", $subserverstr );

			while ( list( $index, $subserver ) = @each( $subserverarray ) )
			{
				if ( strlen( $subserver ) == 0 )
					continue;

				$retarray[$printer][] = $subserver;
			}
		}

		@reset( $retarray );
		return $retarray;
	}

	/**
	 * Parse LPRng's different time formats.
	 *
	 * @access public
	 */
	function parsetime( $lprngtimestr )
	{
		$datearray = getdate( time() );
		$array     = explode( "-", $lprngtimestr );

		if ( count( $array ) == 4 )
		{
			$datearray["year"] = $array[0];
			$datearray["mon"]  = $array[1];
			$datearray["mday"] = $array[2];
			
			$timestr = $array[3];
		}
		else
		{
			$timestr = $array[0];
		}

		$array = explode( ":", $timestr );

		$datearray["hours"]   = $array[0];
		$datearray["minutes"] = $array[1];
		$datearray["seconds"] = $array[2];

		// Convert it to the number of seconds since the epoch.
		$time = mktime(
			$datearray["hours"],
			$datearray["minutes"],
			$datearray["seconds"],
			$datearray["mon"],
			$datearray["mday"],
			$datearray["year"]
		);

		return $time;
	}
} // END OF PrintServer

?>
